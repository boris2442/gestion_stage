<?php 
session_start();
// Si déjà connecté, redirection immédiate
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit(); }

include 'includes/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Connexion</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger small py-2">
                            <i class="fas fa-exclamation-triangle me-2"></i> Email ou mot de passe incorrect.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'account_deleted'): ?>
                        <div class="alert alert-info small py-2">Votre compte a été supprimé.</div>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">Se connecter</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-light small">
                    <a href="postuler.php" class="text-decoration-none">Pas encore de compte ? Postulez !</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
