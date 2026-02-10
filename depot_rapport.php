<?php
include 'includes/header.php';
// Code simplifié pour l'upload du rapport (similaire à postuler_process.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['rapport'])) {
    $filename = "rapport_" . $_SESSION['nom'] . "_" . time() . ".pdf";
    if (move_uploaded_file($_FILES['rapport']['tmp_name'], "uploads/rapports/" . $filename)) {
        // Optionnel : Enregistrer le chemin du rapport dans la table sessions ou une table rapports
        echo "<script>alert('Rapport déposé avec succès !');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-sm col-md-6 mx-auto">
        <div class="card-header bg-dark text-white text-center"><h5>Dépôt du Rapport Final</h5></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Sélectionnez votre rapport (PDF)</label>
                    <input type="file" name="rapport" class="form-control" accept=".pdf" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">Envoyer le rapport</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
