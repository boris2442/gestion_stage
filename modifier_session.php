<?php
session_start();
require_once 'config/db.php';

// Sécurité : Uniquement Admin ou Encadreur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'stagiaire') {
    header('Location: index.php');
    exit();
}

// 1. Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    header('Location: liste_sessions.php');
    exit();
}

$id = $_GET['id'];

// 2. Récupérer les infos actuelles de la session
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
$stmt->execute([$id]);
$session = $stmt->fetch();

if (!$session) {
    die("Session introuvable.");
}

// 3. Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $sql = "UPDATE sessions SET titre = ?, date_debut = ?, date_fin = ? WHERE id = ?";

    if ($pdo->prepare($sql)->execute([$titre, $date_debut, $date_fin, $id])) {
        $success = "La session a été mise à jour avec succès !";
        // On rafraîchit les données pour l'affichage
        $session['titre'] = $titre;
        $session['date_debut'] = $date_debut;
        $session['date_fin'] = $date_fin;
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="liste_sessions.php">Sessions</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Modifier la Session</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success border-0 shadow-sm text-center">
                            <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Titre de la Promotion</label>
                            <input type="text" name="titre" class="form-control"
                                value="<?= htmlspecialchars($session['titre']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="<?= $session['date_debut'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="<?= $session['date_fin'] ?>" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning btn-lg shadow-sm fw-bold">
                                <i class="fas fa-sync-alt me-2"></i> Enregistrer les modifications
                            </button>
                            <a href="liste_sessions.php" class="btn btn-light border">Retour à la liste</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
