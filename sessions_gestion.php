<?php
// sessions_gestion.php (Inclus ton header en haut)
include 'includes/header.php';

// Vérification de sécurité Admin
if($_SESSION['role'] !== 'administrateur') {
    header("Location: dashboard.php");
    exit();
}

// Traitement de l'ajout d'une session
if(isset($_POST['ajouter_session'])) {
    $titre = $_POST['titre'];
    $debut = $_POST['date_debut'];
    $fin = $_POST['date_fin'];
    $stagiaire = $_POST['id_stagiaire'];
    $encadreur = $_POST['id_encadreur'];
    $status = $_POST['status'];

    $sql = "INSERT INTO sessions (titre, date_debut, date_fin, id_stagiaire, id_encadreur, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titre, $debut, $fin, $stagiaire, $encadreur, $status]);
    echo "<div class='alert alert-success'>Session créée avec succès !</div>";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestion des Sessions de Stage</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">
        <i class="fas fa-plus"></i> Nouvelle Session
    </button>
</div>

<div class="table-responsive bg-white p-3 shadow-sm rounded">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Titre</th>
                <th>Stagiaire</th>
                <th>Encadreur</th>
                <th>Période</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>
</div>

<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un nouveau stage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Titre du stage</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Date début</label>
                        <input type="date" name="date_debut" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Date fin</label>
                        <input type="date" name="date_fin" class="form-control" required>
                    </div>
                </div>
                </div>
            <div class="modal-footer">
                <button type="submit" name="ajouter_session" class="btn btn-primary w-100">Enregistrer le stage</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
