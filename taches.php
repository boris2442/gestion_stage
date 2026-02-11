<?php
include 'includes/header.php';
if ($_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

// Action : Marquer comme terminé
if (isset($_GET['action']) && $_GET['action'] == 'done') {
    $pdo->prepare("UPDATE taches SET status = 'termine' WHERE id = ? AND id_stagiaire = ?")
        ->execute([$_GET['id'], $_SESSION['user_id']]);
}

// $taches = $pdo->prepare("SELECT * FROM taches WHERE id_stagiaire = ? ORDER BY date_creation DESC");
// $taches->execute([$_SESSION['user_id']]);


// On récupère d'abord l'ID de la session actuelle du stagiaire
$user_stmt = $pdo->prepare("SELECT id_session_actuelle FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();
$session_actuelle = $user['id_session_actuelle'];

// On ne récupère que les tâches de la session en cours
$sql = "SELECT * FROM taches 
        WHERE id_stagiaire = ? 
        AND id_session = ? 
        ORDER BY status ASC, date_fin ASC"; // On trie par statut puis par date limite

$taches_stmt = $pdo->prepare($sql);
$taches_stmt->execute([$_SESSION['user_id'], $session_actuelle]);
$liste_taches = $taches_stmt->fetchAll();
?>

<div class="container mt-4">
    <h3><i class="fas fa-list-check me-2"></i> Ma To-Do List</h3>
    <div class="row">
        <?php foreach ($liste_taches as $t): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 <?= $t['status'] == 'termine' ? 'border-start border-success border-4' : 'border-start border-primary border-4' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($t['titre']) ?></h5>
                            <?php if ($t['status'] == 'termine'): ?>
                                <i class="fas fa-check-circle text-success fa-lg"></i>
                            <?php endif; ?>
                        </div>

                        <p class="card-text text-muted small mb-3">
                            <?= nl2br(htmlspecialchars($t['description'])) ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-danger fw-bold">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Pour le : <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 pb-3">
                        <?php if ($t['status'] == 'a_faire'): ?>
                            <a href="mes_taches.php?action=done&id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm w-100 shadow-sm">
                                <i class="fas fa-check me-1"></i> Marquer comme terminé
                            </a>
                        <?php elseif ($t['status'] == 'termine' && !empty($t['note'])): ?>
                            <div class="alert alert-success py-1 px-2 mb-0 small">
                                <strong>Note : <?= $t['note'] ?>/20</strong> - <?= htmlspecialchars($t['commentaire_encadreur']) ?>
                            </div>
                        <?php else: ?>
                            <span class="badge w-100 py-2 bg-success-subtle text-success border border-success">
                                En attente de correction
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
