<?php include 'includes/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4><i class="fas fa-lock me-2"></i> Connexion</h4>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Email ou mot de passe incorrect.</div>
                <?php endif; ?>

                <form action="login_process.php" method="POST">
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

<?php include 'includes/footer.php'; ?>
