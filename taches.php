<?php

session_start();
require_once 'config/db.php';
if ($_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['user_id'];

// 1. Action : Marquer comme terminé
if (isset($_GET['action']) && $_GET['action'] == 'done') {
    $pdo->prepare("UPDATE taches SET status = 'termine' WHERE id = ? AND id_stagiaire = ?")
        ->execute([$_GET['id'], $id_user]);
}

// 2. Récupération de la session actuelle
$user_stmt = $pdo->prepare("SELECT id_session_actuelle FROM users WHERE id = ?");
$user_stmt->execute([$id_user]);
$user = $user_stmt->fetch();
$session_actuelle = $user['id_session_actuelle'] ?? 0;

// 3. NOUVEAU : Récupération du statut du rapport pour l'attestation
$rapport_stmt = $pdo->prepare("SELECT status, commentaire_encadreur FROM rapports WHERE id_stagiaire = ? ORDER BY date_depot DESC LIMIT 1");
$rapport_stmt->execute([$id_user]);
$mon_rapport = $rapport_stmt->fetch();

// 4. Récupération des tâches
$sql = "SELECT * FROM taches 
        WHERE id_stagiaire = ? 
        AND id_session = ? 
        ORDER BY status ASC, date_fin ASC";

$taches_stmt = $pdo->prepare($sql);
$taches_stmt->execute([$id_user, $session_actuelle]);
$liste_taches = $taches_stmt->fetchAll();
include 'includes/header.php';
?>

<div class="container mt-4">

    <div class="row mb-5">
        <div class="col-12">
            <?php if (!$mon_rapport): ?>
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4 text-center">
                        <h5 class="text-muted"><i class="fas fa-file-upload me-2"></i> Aucun rapport déposé</h5>
                        <p class="small text-muted">Soumettez votre rapport final pour obtenir votre attestation.</p>
                        <a href="deposer_rapport.php" class="btn btn-primary btn-sm">Aller au dépôt</a>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($mon_rapport['status'] == 'valide'): ?>
                    <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #1a2a6c, #2c3e50);">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="fw-bold mb-1 text-warning"><i class="fas fa-medal me-2"></i> Félicitations !</h3>
                                    <p class="mb-0">Votre stage est validé. Votre attestation est prête à être téléchargée.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="generer_attestation.php" target="_blank" class="btn btn-warning btn-lg shadow fw-bold">
                                        <i class="fas fa-file-pdf me-2"></i> Mon Attestation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($mon_rapport['status'] == 'a_corriger'): ?>
                    <div class="alert alert-danger shadow-sm border-start border-5 border-danger">
                        <h5 class="fw-bold"><i class="fas fa-tools me-2"></i> Corrections demandées</h5>
                        <p>Note de l'encadreur : <em>"<?= htmlspecialchars($mon_rapport['commentaire_encadreur']) ?>"</em></p>
                        <a href="deposer_rapport.php" class="btn btn-danger btn-sm">Renvoyer le rapport</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info shadow-sm border-start border-5 border-info">
                        <h5><i class="fas fa-hourglass-half me-2"></i> Rapport en cours de validation</h5>
                        <p class="mb-0">Dès que votre encadreur aura validé votre rapport, votre attestation apparaîtra ici.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <h3 class="mb-4"><i class="fas fa-list-check me-2"></i> Ma To-Do List</h3>
    <div class="row">
        <?php if (empty($liste_taches)): ?>
            <div class="col-12 text-center text-muted py-5">
                <p>Aucune tâche assignée pour cette session.</p>
            </div>
        <?php endif; ?>

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
                                <strong>Note : <?= $t['note'] ?>/20</strong> - <?= htmlspecialchars($t['commentaire_encadreur'] ?? '') ?>
                            </div>
                        <?php else: ?>
                            <span class="badge w-100 py-2 bg-success-subtle text-success border border-success">
                                En attente de notation
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
