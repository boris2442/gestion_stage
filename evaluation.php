<?php
include 'includes/header.php';
// Sécurité : Uniquement Encadreur ou Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'stagiaire') {
    header('Location: index.php'); exit();
}

// Récupérer les sessions actives pour cet encadreur
$stmt = $pdo->prepare("SELECT s.*, u.nom, u.prenom FROM sessions s 
                       JOIN users u ON s.id_stagiaire = u.id 
                       WHERE s.id_encadreur = ? AND s.status = 'en_cours'");
$stmt->execute([$_SESSION['user_id']]);
$sessions = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_session = $_POST['id_session'];
    $note = $_POST['note'];
    $obs = htmlspecialchars($_POST['observations']);

    $sql = "UPDATE sessions SET note = ?, observations = ?, status = 'termine' WHERE id = ?";
    if($pdo->prepare($sql)->execute([$note, $obs, $id_session])) {
        echo "<script>alert('Évaluation enregistrée. Stage clôturé !');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h4><i class="fas fa-star me-2"></i> Évaluation de Fin de Stage</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Sélectionner le Stagiaire à noter</label>
                    <select name="id_session" class="form-select" required>
                        <?php foreach($sessions as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['nom'] ?> <?= $s['prenom'] ?> (<?= $s['titre'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Note Finale (/20)</label>
                        <input type="number" name="note" class="form-control" min="0" max="20" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Observations générales</label>
                        <textarea name="observations" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning w-100">Valider l'Évaluation</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
