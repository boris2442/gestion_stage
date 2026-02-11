<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// Requête pour voir TOUTES les tâches avec les noms des acteurs
// 1. Récupérer la session active pour le filtre par défaut
$stmt_sess = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt_sess->fetch();
$id_session_filtre = $_GET['session_id'] ?? ($session_active['id'] ?? null);

// 2. Requête filtrée (ou non)
$sql = "SELECT t.*, 
               s.nom AS stagiaire_nom, s.prenom AS stagiaire_prenom,
               e.nom AS encadreur_nom, e.prenom AS encadreur_prenom
        FROM taches t
        JOIN users s ON t.id_stagiaire = s.id
        JOIN users e ON t.id_encadreur = e.id";

if ($id_session_filtre) {
    $sql .= " WHERE t.id_session = :id_session"; // On filtre par la session choisie
}

$sql .= " ORDER BY t.date_creation DESC";

$stmt = $pdo->prepare($sql);
if ($id_session_filtre) {
    $stmt->execute(['id_session' => $id_session_filtre]);
} else {
    $stmt->execute();
}
$taches = $stmt->fetchAll();
include 'includes/header.php';
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list me-2"></i> Suivi Global des Travaux</h2>
        <div class="col-md-4">
            <form method="GET" action="" class="d-flex align-items-center justify-content-center">
                <label class="me-2 fw-bold text-muted small">Session :</label>
                <select name="session_id" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                    <option value="">Toutes les sessions</option>
                    <?php
                    // On récupère toutes les sessions pour le menu déroulant
                    $all_sess = $pdo->query("SELECT id, titre FROM sessions ORDER BY date_debut DESC")->fetchAll();
                    foreach ($all_sess as $s):
                        $selected = (isset($_GET['session_id']) && $_GET['session_id'] == $s['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $s['id'] ?>" <?= $selected ?>><?= htmlspecialchars($s['titre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>




        <div class="">
            <a href="ajouter_tache.php" class="btn btn-success btn-sm fw-bold">Ajouter une tache</a>

        </div>

        <div class="text-muted">Total : <?= count($taches) ?> missions en cours</div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tâche</th>
                        <th>Stagiaire</th>
                        <th>Assigné par</th>
                        <th>Deadline</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $t): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?= htmlspecialchars($t['titre']) ?></span><br>
                                <small class="text-muted"><?= substr(htmlspecialchars($t['description']), 0, 50) ?>...</small>
                            </td>
                            <td><?= htmlspecialchars($t['stagiaire_nom']) ?></td>
                            <td><small>M. <?= htmlspecialchars($t['encadreur_nom']) ?></small></td>
                            <td>
                                <?php
                                $date_fin = new DateTime($t['date_fin']);
                                echo $date_fin->format('d/m/Y');
                                ?>
                            </td>
                            <td>
                                <?php if ($t['status'] == 'a_faire'): ?>
                                    <span class="badge bg-secondary">À faire</span>
                                <?php elseif ($t['status'] == 'en_cours'): ?>
                                    <span class="badge bg-primary">En cours</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Terminé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light" title="Voir les détails">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
