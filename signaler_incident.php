<?php
session_start();
require_once 'config/db.php';

if ($_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['user_id'];
$success_msg = "";

// 1. Récupération de la session (Toujours nécessaire)
$stmt_user = $pdo->prepare("SELECT id_session_actuelle FROM users WHERE id = ?");
$stmt_user->execute([$id_user]);
$user_data = $stmt_user->fetch();
$id_session = $user_data['id_session_actuelle'] ?? NULL;

// 2. TRAITEMENT DU FORMULAIRE (Seulement si on a cliqué sur Envoyer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sujet'])) {
    
    // On récupère les données proprement
    $sujet = htmlspecialchars($_POST['sujet'] ?? ''); 
    $message = htmlspecialchars($_POST['message'] ?? '');

    if (!empty($sujet) && !empty($message)) {
        $sql = "INSERT INTO incidents (id_stagiaire, id_session, sujet, message, status) 
                VALUES (?, ?, ?, ?, 'ouvert')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id_user, $id_session, $sujet, $message])) {
            $success_msg = "Votre signalement a été transmis avec succès.";
        }
    }
}

include 'includes/header.php';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <?php if ($success_msg): ?>
                <div class="alert alert-success shadow-sm border-0 animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle me-2"></i> <?= $success_msg ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white p-3">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Signaler un Incident</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sujet du problème</label>
                            <input type="text" name="sujet" class="form-control" placeholder="Ex: Panne de courant, accès refusé..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description détaillée</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Décrivez le problème le plus précisément possible..." required></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-danger btn-lg shadow">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le signalement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="mes_taches.php" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Retour à mes tâches
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
