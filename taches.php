<?php
include 'includes/header.php';
if ($_SESSION['role'] !== 'stagiaire') { header('Location: index.php'); exit(); }

// Action : Marquer comme terminé
if (isset($_GET['action']) && $_GET['action'] == 'done') {
    $pdo->prepare("UPDATE taches SET status = 'termine' WHERE id = ? AND id_stagiaire = ?")
        ->execute([$_GET['id'], $_SESSION['user_id']]);
}

$taches = $pdo->prepare("SELECT * FROM taches WHERE id_stagiaire = ? ORDER BY date_creation DESC");
$taches->execute([$_SESSION['user_id']]);
?>

<div class="container mt-4">
    <h3><i class="fas fa-list-check me-2"></i> Ma To-Do List</h3>
    <div class="row">
        <?php foreach($taches->fetchAll() as $t): ?>
            <div class="col-md-4 mb-3">
                <div class="card <?= $t['status'] == 'termine' ? 'bg-light' : 'border-primary' ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $t['titre'] ?></h5>
                        <p class="card-text text-muted"><?= $t['description'] ?></p>
                        <?php if($t['status'] == 'a_faire'): ?>
                            <a href="mes_taches.php?action=done&id=<?= $t['id'] ?>" class="btn btn-primary btn-sm">Marquer comme fait</a>
                        <?php else: ?>
                            <span class="badge bg-success">Terminée <i class="fas fa-check"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
