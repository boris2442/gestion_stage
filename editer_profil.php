<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// 1. Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 2. Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            // Mise à jour AVEC mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, password = ? WHERE id = ?");
            $update->execute([$nom, $prenom, $email, $hashed_password, $user_id]);
        } else {
            // Mise à jour SANS changer le mot de passe
            $update = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id = ?");
            $update->execute([$nom, $prenom, $email, $user_id]);
        }
        
        $_SESSION['nom'] = $nom; // Mettre à jour la session pour l'affichage header
        $success = "Profil mis à jour avec succès !";
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Modifier mon profil</div>
                <div class="card-body">
                    <?php if($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Professionnel</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 p-3 border border-danger rounded">
                <h6 class="text-danger">Zone de danger</h6>
                <p class="small text-muted">La suppression de votre compte est irréversible.</p>
                <a href="supprimer_mon_compte.php" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')">Supprimer mon compte</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
