<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// 1. Récupérer les infos avec une JOINTURE pour afficher la session
$sql = "SELECT u.*, s.titre as nom_session 
        FROM users u 
        LEFT JOIN sessions s ON u.id_session_actuelle = s.id 
        WHERE u.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 2. Traitement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, password = ? WHERE id = ?");
            $update->execute([$nom, $prenom, $email, $hashed_password, $user_id]);
        } else {
            $update = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id = ?");
            $update->execute([$nom, $prenom, $email, $user_id]);
        }

        $_SESSION['nom'] = $nom;
        $success = "Profil mis à jour avec succès !";
        // On rafraîchit les infos locales
        $user['nom'] = $nom;
        $user['prenom'] = $prenom;
        $user['email'] = $email;
    } catch (PDOException $e) {
        $error = "Une erreur est survenue (l'email est peut-être déjà utilisé).";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <?php if ($_SESSION['role'] === 'stagiaire'): ?>
                <div class="alert alert-light border shadow-sm mb-4">
                    <small class="text-muted d-block">Session de stage actuelle :</small>
                    <strong><i class="fas fa-graduation-cap me-2"></i><?= $user['nom_session'] ?? 'Aucune session assignée' ?></strong>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-user-edit me-2"></i>Modifier mon profil
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?> <div class="alert alert-success border-0 shadow-sm"><?= $success ?></div> <?php endif; ?>
                    <?php if ($error): ?> <div class="alert alert-danger border-0 shadow-sm"><?= $error ?></div> <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email Professionnel</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-primary">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="Laissez vide pour conserver l'actuel">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 border border-danger rounded bg-light">
                <h6 class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Zone de danger</h6>
                <p class="small text-muted mb-2">La suppression de votre compte effacera toutes vos données de stage.</p>
                <a href="supprimer_mon_compte.php" class="btn btn-sm btn-outline-danger" onclick="return confirm('La suppression est irréversible. Continuer ?')">Supprimer mon compte</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
