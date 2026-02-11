<?php
session_start();
require_once 'config/db.php';

// Sécurité : Uniquement pour l'Admin (ou l'encadreur selon ton choix)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'stagiaire') {
    header('Location: index.php');
    exit();
}

// 1. Récupérer tous les stagiaires
$stagiaires = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'stagiaire'")->fetchAll();

// 2. Récupérer tous les encadreurs
$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();

// 3. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $id_stagiaire = $_POST['id_stagiaire'];
    $id_encadreur = $_POST['id_encadreur'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $sql = "INSERT INTO sessions (titre, date_debut, date_fin, id_stagiaire, id_encadreur, status) 
            VALUES (?, ?, ?, ?, ?, 'en_cours')";

    if ($pdo->prepare($sql)->execute([$titre, $date_debut, $date_fin, $id_stagiaire, $id_encadreur])) {
        $msg = "Session de stage créée avec succès !";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Créer une Nouvelle Session de Stage</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (isset($msg)): ?>
                        <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Intitulé du Stage</label>
                            <input type="text" name="titre" class="form-control" placeholder="ex: Stage d'immersion PHP/MySQL" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stagiaire</label>
                                <select name="id_stagiaire" class="form-select" required>
                                    <option value="">Choisir un stagiaire...</option>
                                    <?php foreach ($stagiaires as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= $s['nom'] ?> <?= $s['prenom'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Encadreur</label>
                                <select name="id_encadreur" class="form-select" required>
                                    <option value="">Choisir un encadreur...</option>
                                    <?php foreach ($encadreurs as $e): ?>
                                        <option value="<?= $e['id'] ?>"><?= $e['nom'] ?> <?= $e['prenom'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date de fin prévue</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Lancer la Session
                            </button>
                            <a href="index.php" class="btn btn-light">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
