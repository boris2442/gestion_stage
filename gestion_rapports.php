<?php
session_start();
require_once 'config/db.php';


// Sécurité : Seul l'encadreur ou l'admin peut voir cette page
if (!in_array($_SESSION['role'], ['encadreur', 'administrateur'])) {
    header('Location: index.php');
    exit();
}

$id_encadreur = $_SESSION['user_id'];

// --- ACTION : Validation ou Correction ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_rapport'])) {
    $id_rapport = $_POST['id_rapport'];
    $nouveau_status = $_POST['status'];
    $commentaire = htmlspecialchars($_POST['commentaire']);

    $update = $pdo->prepare("UPDATE rapports SET status = ?, commentaire_encadreur = ? WHERE id = ?");
    if ($update->execute([$nouveau_status, $commentaire, $id_rapport])) {
        echo "<div class='alert alert-success mt-2'>Statut du rapport mis à jour !</div>";
    }
}

// --- RÉCUPÉRATION DES RAPPORTS ---
// On récupère les rapports des stagiaires liés à cet encadreur
// Modifie le WHERE pour utiliser le bon nom de colonne
// Remplace temporairement ta ligne WHERE par celle-ci pour tester :
$sql = "SELECT r.*, u.nom as nom_stagiaire, u.prenom as prenom_stagiaire, s.titre as promo 
        FROM rapports r
        JOIN users u ON r.id_stagiaire = u.id
        JOIN sessions s ON r.id_session = s.id
        ORDER BY r.date_depot DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(); // OK : 0 point d'interrogation = 0 paramètre
$rapports = $stmt->fetchAll();





// Initialisation des compteurs
$total_recus = count($rapports);
$attente = 0;
$valides = 0;
$corrections = 0;

foreach ($rapports as $rap) {
    if ($rap['status'] == 'en_attente') $attente++;
    elseif ($rap['status'] == 'valide') $valides++;
    elseif ($rap['status'] == 'a_corriger') $corrections++;
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-signature text-primary me-2"></i> Rapports à valider</h2>
        <span class="badge bg-dark"><?= count($rapports) ?> Rapport(s) reçu(s)</span>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase opacity-75">Total reçus</small>
                        <h3 class="mb-0"><?= $total_recus ?></h3>
                    </div>
                    <i class="fas fa-file-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4 p-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">À traiter</small>
                <h3 class="mb-0 text-warning"><?= $attente ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 p-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Validés</small>
                <h3 class="mb-0 text-success"><?= $valides ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4 p-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Retours envoyés</small>
                <h3 class="mb-0 text-danger"><?= $corrections ?></h3>
            </div>
        </div>
    </div>
    <div class="row">
        <?php if (empty($rapports)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun rapport n'a été déposé par vos stagiaires pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rapports as $r): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title"><?= htmlspecialchars($r['titre_rapport']) ?></h5>
                                <?php
                                $badgeColor = ($r['status'] == 'valide') ? 'success' : (($r['status'] == 'a_corriger') ? 'danger' : 'warning');
                                ?>
                                <span class="badge bg-<?= $badgeColor ?>"><?= ucfirst(str_replace('_', ' ', $r['status'])) ?></span>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">
                                Par : <?= htmlspecialchars($r['prenom_stagiaire'] . ' ' . $r['nom_stagiaire']) ?>
                            </h6>
                            <p class="small text-muted"><i class="fas fa-calendar-alt me-1"></i> Déposé le : <?= date('d/m/Y H:i', strtotime($r['date_depot'])) ?></p>

                            <hr>

                            <div class="d-grid gap-2 mb-3">
                                <a href="<?= $r['fichier_path'] ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-download me-2"></i> Télécharger / Lire le rapport
                                </a>
                            </div>

                            <form method="POST" class="bg-light p-3 rounded">
                                <input type="hidden" name="id_rapport" value="<?= $r['id'] ?>">
                                <input type="hidden" name="action_rapport" value="1">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Commentaire / Feedback</label>
                                    <textarea name="commentaire" class="form-control form-control-sm" rows="2" placeholder="Ex: Très bon travail ou Précisez les corrections..."><?= htmlspecialchars($r['commentaire_encadreur'] ?? '') ?></textarea>
                                </div>

                                <div class="btn-group w-100">
                                    <button type="submit" name="status" value="valide" class="btn btn-sm btn-success">Valider</button>
                                    <button type="submit" name="status" value="a_corriger" class="btn btn-sm btn-danger">À corriger</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
