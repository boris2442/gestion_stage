<?php
session_start();
require_once 'config/db.php';

// Sécurité
if ($_SESSION['role'] !== 'encadreur' && $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

$id_encadreur = $_SESSION['user_id'];

// --- LOGIQUE INGÉNIEUR : Récupérer la session active ---
$stmt_session = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt_session->fetch();

if (!$session_active) {
    die("<div class='alert alert-danger m-5'>Erreur : Aucune session de stage n'est active actuellement.</div>");
}

// 1. Récupérer uniquement les stagiaires de cet encadreur QUI sont dans la session active
// $sql = "SELECT id, nom, prenom FROM users 
//         WHERE encadreur_id = ? 
//         AND role = 'stagiaire' 
//         AND id_session_actuelle = ?"; // Filtre crucial

// $stmt = $pdo->prepare($sql);
// $stmt->execute([$id_encadreur, $session_active['id']]);
// $mes_stagiaires = $stmt->fetchAll();




// On récupère TOUS les stagiaires de cet encadreur, 
// en faisant une jointure pour afficher le nom de leur session
$sql = "SELECT u.id, u.nom, u.prenom, s.titre as nom_session 
        FROM users u 
        LEFT JOIN sessions s ON u.id_session_actuelle = s.id 
        WHERE u.encadreur_id = ? 
        AND u.role = 'stagiaire'";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_encadreur]);
$mes_stagiaires = $stmt->fetchAll();
echo "Mon ID encadreur est : " . $id_encadreur . "<br>";
echo "Nombre de stagiaires trouvés : " . count($mes_stagiaires);
//die(); // Décommente ça pour arrêter l'affichage ici et lire le résultat






// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_stagiaire = $_POST['id_stagiaire'];
    $titre = htmlspecialchars($_POST['titre']);
    $desc = htmlspecialchars($_POST['description']);
    $date_fin = $_POST['date_fin'];

    // On ajoute id_session dans la table tâches pour pouvoir filtrer l'historique plus tard
    $ins = $pdo->prepare("INSERT INTO taches (id_stagiaire, id_encadreur, id_session, titre, description, date_fin, status) 
                          VALUES (?, ?, ?, ?, ?, ?, 'a_faire')");

    if ($ins->execute([$id_stagiaire, $id_encadreur, $session_active['id'], $titre, $desc, $date_fin])) {
        header('Location: gestion_taches_admin.php?msg=Tâche assignée');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center mb-3">
        <div class="col-md-8 text-center">
            <span class="badge bg-info text-dark p-2">
                <i class="fas fa-calendar-check me-2"></i> Session de travail : <?= htmlspecialchars($session_active['titre']) ?>
            </span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Assigner une mission</h5>
                </div>
                <div class="card-body p-4">

                    <?php if (empty($mes_stagiaires)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Vous n'avez aucun stagiaire affecté pour cette session active.
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Stagiaire concerné</label>
                                <select name="id_stagiaire" class="form-select" required>
                                    <option value="">--- Sélectionnez un stagiaire ---</option>
                                    <?php foreach ($mes_stagiaires as $s): ?>
                                        <option value="<?= $s['id'] ?>">
                                            <?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?>
                                            (<?= htmlspecialchars($s['nom_session'] ?? 'Sans session') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Titre de la tâche</label>
                                <input type="text" name="titre" class="form-control" placeholder="Ex: Rapport de maintenance hebdomadaire" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Description / Instructions</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Détaillez les étapes à suivre..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Date limite</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer la mission
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
