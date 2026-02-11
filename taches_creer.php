<?php
include 'includes/header.php';
// Sécurité : Uniquement Encadreur ou Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'stagiaire') {
    header('Location: index.php');
    exit();
}

// Récupérer les stagiaires affectés à cet encadreur
$stmt = $pdo->prepare("SELECT u.id, u.nom, u.prenom FROM users u 
                       JOIN sessions s ON u.id = s.id_stagiaire 
                       WHERE s.id_encadreur = ?");
$stmt->execute([$_SESSION['user_id']]);
$mes_stagiaires = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_s = $_POST['id_stagiaire'];
    $titre = htmlspecialchars($_POST['titre']);
    $desc = htmlspecialchars($_POST['description']);

    $sql = "INSERT INTO taches (id_stagiaire, titre, description, status) VALUES (?, ?, ?, 'a_faire')";
    if ($pdo->prepare($sql)->execute([$id_s, $titre, $desc])) {
        echo "<script>alert('Tâche assignée !');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h4><i class="fas fa-plus-circle me-2"></i> Assigner une nouvelle tâche</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Sélectionner le Stagiaire</label>
                    <select name="id_stagiaire" class="form-select" required>
                        <?php foreach ($mes_stagiaires as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['nom'] ?> <?= $s['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Titre de la tâche</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description / Instructions</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Envoyer la tâche</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
