<?php
include 'includes/header.php';
// Sécurité : Uniquement Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php'); exit();
}

// 1. On récupère les stagiaires et les encadreurs pour les menus déroulants
$stagiaires = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'stagiaire'")->fetchAll();
$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_s = $_POST['id_stagiaire'];
    $id_e = $_POST['id_encadreur'];
    $debut = $_POST['date_debut'];
    $fin = $_POST['date_fin'];

    // Insertion dans la table SESSIONS pour lier les deux
    $sql = "INSERT INTO sessions (titre, date_debut, date_fin, id_stagiaire, id_encadreur, status) 
            VALUES ('Session de Stage', ?, ?, ?, ?, 'en_cours')";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$debut, $fin, $id_s, $id_e])) {
        echo "<script>alert('Affectation réussie !'); window.location.href='dashboard.php';</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-link me-2"></i> Affectation Stagiaire - Encadreur</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Choisir le Stagiaire</label>
                    <select name="id_stagiaire" class="form-select" required>
                        <?php foreach($stagiaires as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['nom'] ?> <?= $s['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Attribuer un Encadreur</label>
                    <select name="id_encadreur" class="form-select" required>
                        <?php foreach($encadreurs as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= $e['nom'] ?> <?= $e['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Début</label>
                        <input type="date" name="date_debut" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Fin</label>
                        <input type="date" name="date_fin" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Finaliser l'Affectation</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
