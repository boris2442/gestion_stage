<?php
session_start();
require_once 'config/db.php';

// 1. Sécurité : Vérification du rôle
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

$message = ""; // Pour afficher les alertes proprement

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['rapport_file'])) {
    $titre = htmlspecialchars($_POST['titre']);
    $id_stagiaire = $_SESSION['user_id'];

    // --- FIX INGÉNIEUR : Récupération forcée de la session active ---
    // On ne fait pas confiance à $_SESSION qui peut être vide, on demande à la DB
    $stmt_sess = $pdo->query("SELECT id FROM sessions WHERE is_active = 1 LIMIT 1");
    $active_session = $stmt_sess->fetch();
    $id_session = $active_session['id'] ?? null;

    if (!$id_session) {
        $message = "<div class='alert alert-danger'>Erreur : Aucune session de stage n'est active. Contactez l'admin.</div>";
    } else {
        // Gestion du fichier
        $file = $_FILES['rapport_file'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];

        if (in_array($fileExt, $allowed)) {
            if ($file['size'] < 5000000) { // 5 Mo max

                // Nom unique et chemin absolu pour Windows/Laragon
                $fileNameNew = "rapport_" . $id_stagiaire . "_" . time() . "." . $fileExt;
                $baseDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "rapports" . DIRECTORY_SEPARATOR;

                // Création du dossier si manquant
                if (!is_dir($baseDir)) {
                    mkdir($baseDir, 0777, true);
                }

                $fileDestination = $baseDir . $fileNameNew;

                if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
                    // Chemin relatif pour la DB
                    $dbPath = "uploads/rapports/" . $fileNameNew;

                    $sql = "INSERT INTO rapports (id_stagiaire, id_session, titre_rapport, fichier_path, status) 
                            VALUES (?, ?, ?, ?, 'en_attente')";
                    $stmt = $pdo->prepare($sql);

                    if ($stmt->execute([$id_stagiaire, $id_session, $titre, $dbPath])) {
                        $message = "<div class='alert alert-success mt-3 shadow-sm'>
                                        <i class='fas fa-check-circle me-2'></i>
                                        Félicitations ! Votre rapport a été transmis avec succès.
                                    </div>";
                    }
                } else {
                    $message = "<div class='alert alert-danger'>Erreur lors du déplacement du fichier vers le dossier uploads.</div>";
                }
            } else {
                $message = "<div class='alert alert-warning'>Le fichier est trop volumineux (max 5Mo).</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>Format non autorisé. Utilisez PDF, DOC ou DOCX.</div>";
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="mes_taches.php" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Retour à mon plan de travail
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i> Soumission du Rapport Final</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Titre de votre rapport</label>
                            <input type="text" name="titre" class="form-control" placeholder="Ex: Rapport de stage - Administration Réseaux" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sélectionnez le fichier</label>
                            <div class="input-group">
                                <input type="file" name="rapport_file" class="form-control" id="inputGroupFile01" required>
                            </div>
                            <div class="form-text mt-2 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Formats acceptés : <strong>PDF, DOC, DOCX</strong>. Taille max : 5 Mo.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le rapport
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted italic">Note : Une fois envoyé, votre encadreur sera notifié pour la validation.</small>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
