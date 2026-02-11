<?php
session_start();
require_once 'config/db.php';


// Sécurité : Uniquement pour l'encadreur (ou l'admin)
if ($_SESSION['role'] !== 'encadreur' && $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

$id_encadreur = $_SESSION['user_id'];

// 1. Récupérer uniquement les stagiaires de cet encadreur
$stmt = $pdo->prepare("SELECT id, nom, prenom FROM users WHERE encadreur_id = ? AND role = 'stagiaire'");
$stmt->execute([$id_encadreur]);
$mes_stagiaires = $stmt->fetchAll();

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_stagiaire = $_POST['id_stagiaire'];
    $titre = $_POST['titre'];
    $desc = $_POST['description'];
    $date_fin = $_POST['date_fin'];

    $ins = $pdo->prepare("INSERT INTO taches (id_stagiaire, id_encadreur, titre, description, date_fin, status) VALUES (?, ?, ?, ?, ?, 'a_faire')");
    if ($ins->execute([$id_stagiaire, $id_encadreur, $titre, $desc, $date_fin])) {
        echo "<div class='alert alert-success'>Tâche assignée avec succès !</div>";
        header('location:gestion_taches_admin.php');
    }
}
?>
<?php
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Assigner une nouvelle mission</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Choisir le stagiaire</label>
                            <select name="id_stagiaire" class="form-select" required>
                                <option value="">--- Sélectionnez un stagiaire ---</option>
                                <?php foreach ($mes_stagiaires as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titre de la tâche</label>
                            <input type="text" name="titre" class="form-control" placeholder="Ex: Configuration du routeur Cisco" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description / Instructions</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date limite (Deadline)</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer la tâche
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
