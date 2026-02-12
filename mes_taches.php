<?php
session_start();
require_once 'config/db.php';


if ($_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['user_id'];

// 1. Action : Marquer une tâche comme terminée
if (isset($_GET['action']) && $_GET['action'] == 'done') {
    $pdo->prepare("UPDATE taches SET status = 'termine' WHERE id = ? AND id_stagiaire = ?")
        ->execute([$_GET['id'], $id_user]);
}

// 2. Récupérer la session actuelle
$user_stmt = $pdo->prepare("SELECT id_session_actuelle FROM users WHERE id = ?");
$user_stmt->execute([$id_user]);
$user = $user_stmt->fetch();
$session_actuelle = $user['id_session_actuelle'];

// 3. Récupérer l'état du rapport (S'il existe)
// On récupère le dernier rapport déposé par ce stagiaire, peu importe la session
$rapport_stmt = $pdo->prepare("SELECT status, commentaire_encadreur FROM rapports WHERE id_stagiaire = ? ORDER BY date_depot DESC LIMIT 1");
$rapport_stmt->execute([$id_user]);
$mon_rapport = $rapport_stmt->fetch();

// 4. Récupérer les tâches
$sql = "SELECT * FROM taches WHERE id_stagiaire = ? AND id_session = ? ORDER BY status ASC, date_fin ASC";
$taches_stmt = $pdo->prepare($sql);
$taches_stmt->execute([$id_user, $session_actuelle]);
$liste_taches = $taches_stmt->fetchAll();
include 'includes/header.php';
?>

<div class="container mt-4">
    <?php if ($mon_rapport['status'] == 'valide'): ?>
        <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <h6 class="alert-heading fw-bold mb-1"><i class="fas fa-certificate me-2"></i> Rapport Validé !</h6>
                <p class="mb-0 small">Félicitations, vous avez terminé votre stage avec succès.</p>
            </div>
            <a href="attestation.php" class="btn btn-dark btn-sm fw-bold" target="_blank">
                <i class="fas fa-file-pdf me-2"></i> Mon Attestation
            </a>
        </div>
    <?php endif; ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-3"><i class="fas fa-file-alt text-primary me-2"></i> Mon Rapport Final</h4>
                    <?php if (!$mon_rapport): ?>
                        <div class="alert alert-light border d-flex justify-content-between align-items-center">
                            <span>Vous n'avez pas encore déposé de rapport pour cette session.</span>
                            <a href="deposer_rapport.php" class="btn btn-primary btn-sm">Déposer maintenant</a>
                        </div>
                    <?php else: ?>
                        <?php
                        $status_map = [
                            'en_attente' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'En cours d\'examen'],
                            'valide' => ['class' => 'success', 'icon' => 'check-double', 'text' => 'Rapport Validé'],
                            'a_corriger' => ['class' => 'danger', 'icon' => 'exclamation-triangle', 'text' => 'Corrections demandées']
                        ];
                        $cur = $status_map[$mon_rapport['status']];
                        ?>
                        <div class="alert alert-<?= $cur['class'] ?> mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">
                                        <i class="fas fa-<?= $cur['icon'] ?> me-2"></i> <?= $cur['text'] ?>
                                    </h6>
                                    <?php if ($mon_rapport['status'] == 'a_corriger'): ?>
                                        <p class="mb-0 small"><strong>Note de l'encadreur :</strong> <?= htmlspecialchars($mon_rapport['commentaire_encadreur']) ?></p>
                                        <a href="deposer_rapport.php" class="btn btn-sm btn-outline-danger mt-2">Renvoyer une version corrigée</a>
                                    <?php elseif ($mon_rapport['status'] == 'valide'): ?>
                                        <p class="mb-0 small">Félicitations ! Vous pouvez bientôt télécharger votre attestation.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="p-3 bg-light rounded shadow-sm border">
                <span class="text-muted me-2 small">Un problème technique ou besoin d'aide ?</span>
                <a href="signaler_incident.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-exclamation-triangle me-1"></i> Signaler un incident
                </a>
            </div>
        </div>
    </div>
    <h3 class="mb-4"><i class="fas fa-tasks me-2"></i> Ma To-Do List</h3>
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
                        <p class="card-text text-muted small mb-3"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                        <div class="small text-danger fw-bold">
                            <i class="fas fa-hourglass-half me-1"></i> Échéance : <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <?php if ($t['status'] == 'a_faire'): ?>
                            <a href="mes_taches.php?action=done&id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm w-100 shadow-sm">Terminer</a>
                        <?php else: ?>
                            <span class="badge w-100 py-2 bg-success-subtle text-success border border-success">Terminé</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
