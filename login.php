<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once 'config/db.php';
// 1. TRAITEMENT DU FORMULAIRE (Si on a cliqué sur Connexion)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ATTENTION : Vérifie si ta table est 'users' ou 'utilisateurs' !
    // Je mets 'users' car c'est ce que tu m'as montré dans ta base.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        header('Location: dashboard.php');
        exit();
    } else {
        // Redirection vers soi-même avec une erreur
        header('Location: login.php?error=1');
        exit();
    }
}

// 2. AFFICHAGE DE LA PAGE (Header)
include 'includes/header.php';
?>

<div class="container">

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'account_deleted'): ?>
        <div class="alert alert-info">Votre compte a été supprimé avec succès.</div>
    <?php endif; ?>
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-lock me-2"></i> Connexion</h4>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Email ou mot de passe incorrect.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Professionnel</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small><a href="postuler.php">Pas encore stagiaire ? Postulez ici</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
