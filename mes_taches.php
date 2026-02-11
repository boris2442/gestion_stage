<?php
// mes_taches.php
session_start();
require_once 'config/db.php';

// Sécurité : Uniquement pour les stagiaires
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

include 'includes/header.php';

$id_stagiaire = $_SESSION['user_id'];

// Récupération des tâches avec les infos de l'encadreur
$sql = "SELECT t.*, u.nom AS nom_encadreur, u.prenom AS prenom_encadreur 
        FROM taches t 
        JOIN users u ON t.id_encadreur = u.id 
        WHERE t.id_stagiaire = ? 
        ORDER BY t.status ASC, t.date_fin ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_stagiaire]);
$taches = $stmt->fetchAll();




// echo $_SESSION['user_id'];


// Initialisation des compteurs
$total = count($taches);
$realisees = 0;
$en_attente = 0;

foreach ($taches as $t) {
    if ($t['status'] == 'termine') {
        $realisees++;
    } else {
        $en_attente++;
    }
}

// Calcul du pourcentage de progression pour le graphe
$pourcentage = ($total > 0) ? round(($realisees / $total) * 100) : 0;
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tasks me-2 text-primary"></i> Mon Plan de Travail</h2>
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-tasks me-2 text-primary"></i> Mon Plan de Travail</h2>
                <p class="text-muted">
                    Vous avez <strong><?= $total ?></strong> missions au total :
                    <span class="badge bg-success"><?= $realisees ?> terminées</span>
                    <span class="badge bg-warning text-dark"><?= $en_attente ?> en cours</span>
                </p>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold">Progression globale</span>
                        <span class="small fw-bold"><?= $pourcentage ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width: <?= $pourcentage ?>%"
                            aria-valuenow="<?= $pourcentage ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'done'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> Super ! Tâche marquée comme terminée. Votre encadreur recevra une notification pour l'évaluer.
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($taches)): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm text-center py-5">
                    <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                    Aucune tâche ne vous a été assignée pour le moment. Profitez-en pour avancer sur votre rapport !
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($taches as $t): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 <?= $t['status'] == 'termine' ? 'bg-light text-muted' : 'border-start border-primary border-4' ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($t['titre']) ?></h5>
                                <?php if ($t['status'] == 'a_faire'): ?>
                                    <span class="badge bg-secondary">À faire</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Terminé</span>
                                <?php endif; ?>
                            </div>

                            <p class="card-text"><?= nl2br(htmlspecialchars($t['description'])) ?></p>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> Échéance :
                                    <span class="fw-bold <?= (strtotime($t['date_fin']) < time() && $t['status'] != 'termine') ? 'text-danger' : '' ?>">
                                        <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                                    </span>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i> M. <?= htmlspecialchars($t['nom_encadreur']) ?>
                                </small>
                            </div>

                            <?php if ($t['status'] == 'termine'): ?>
                                <div class="p-2 rounded bg-white border border-success text-center">
                                    <small class="text-success fw-bold">
                                        Note : <?= ($t['note'] !== null) ? $t['note'] . "/20" : "En attente d'évaluation" ?>
                                    </small>
                                    <?php if ($t['commentaire_encadreur']): ?>
                                        <p class="small text-muted mt-1 mb-0 italic">"<?= htmlspecialchars($t['commentaire_encadreur']) ?>"</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="d-grid mt-3">
                                    <a href="terminer_tache.php?id=<?= $t['id'] ?>"
                                        class="btn btn-primary"
                                        onclick="return confirm('Confirmez-vous avoir terminé ce travail ?')">
                                        <i class="fas fa-check-circle me-2"></i> Marquer comme terminé
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
