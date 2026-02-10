<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Créer un compte</h4>
                </div>
                <div class="card-body p-4">
                    <form action="register_process.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Professionnel</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select">
                                <option value="stagiaire">Stagiaire</option>
                                <option value="encadreur">Encadreur</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">S'inscrire</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <a href="login.php" class="text-decoration-none">Déjà inscrit ? Connectez-vous</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
