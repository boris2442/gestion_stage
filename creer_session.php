<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'stagiaire') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // 1. On désactive TOUTES les sessions (on met is_active à 0)
    $pdo->query("UPDATE sessions SET is_active = 0");

    // 2. On insère la nouvelle session en l'activant (on met 1)
    // Note : on n'envoie plus 'en_cours', mais bien le chiffre 1
    $sql = "INSERT INTO sessions (titre, date_debut, date_fin, is_active) VALUES (?, ?, ?, 1)";

    if ($pdo->prepare($sql)->execute([$titre, $date_debut, $date_fin])) {
        $msg = "La session <strong>$titre</strong> est maintenant l'unique session ACTIVE (Boolean 1).";
    }
}
include 'includes/header.php';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> Nouvelle Promotion / Session</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (isset($msg)): ?>
                        <div class="alert alert-success border-0 shadow-sm text-center">
                            <i class="fas fa-check-circle me-2"></i> <?= $msg ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nom de la Session (ex: Promotion 2026)</label>
                            <input type="text" name="titre" class="form-control form-control-lg" placeholder="Entrez le titre..." required>
                            <small class="text-muted">Cette session sera automatiquement la session active du système.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow">
                                <i class="fas fa-rocket me-2"></i> Lancer cette session
                            </button>
                            <a href="liste_sessions.php" class="btn btn-outline-secondary">Voir toutes les sessions</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
