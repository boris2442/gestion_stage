<?php
// mes_taches.php
session_start();
require_once 'config/db.php';

// 1. Sécurité : Uniquement pour les stagiaires
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
    header('Location: index.php');
    exit();
}

include 'includes/header.php';

$id_stagiaire = $_SESSION['user_id'];

// 2. Récupération des tâches
$sql = "SELECT t.*, u.nom AS nom_encadreur, u.prenom AS prenom_encadreur 
        FROM taches t 
        JOIN users u ON t.id_encadreur = u.id 
        WHERE t.id_stagiaire = ? 
        ORDER BY t.status ASC, t.date_fin ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_stagiaire]);
$taches = $stmt->fetchAll();

// 3. Calcul des statistiques pour le Dashboard
$total = count($taches);
$realisees = 0;
foreach ($taches as $t) {
    if ($t['status'] == 'termine') {
        $realisees++;
    }
}
$en_attente = $total - $realisees;
$pourcentage = ($total > 0) ? round(($realisees / $total) * 100) : 0;
?>

<div class="container mt-4">
    <div class="row align-items-center mb-5">
        <div class="col-md-7">
            <h2><i class="fas fa-tasks me-2 text-primary"></i> Mon Plan de Travail</h2>
            <p class="text-muted mb-4">
                Bonjour ! Voici l'état d'avancement de vos missions.
            </p>
            
            <div class="row text-center">
                <div class="col-6 col-sm-4 mb-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h4 class="mb-0 text-success"><?= $realisees ?></h4>
                        <small class="text-muted text-uppercase">Réalisées</small>
                    </div>
                </div>
                <div class="col-6 col-sm-4 mb-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h4 class="mb-0 text-warning"><?= $en_attente ?></h4>
                        <small class="text-muted text-uppercase">Restantes</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 text-center">
            <div style="max-width: 180px; margin: 0 auto;">
                <canvas id="evolutionChart"></canvas>
            </div>
            <div class="mt-2 fw-bold text-primary"><?= $pourcentage ?>% Accomplis</div>
        </div>
    </div>

    <hr>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'done'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> Super ! Tâche marquée comme terminée.
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <?php if (empty($taches)): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm text-center py-5">
                    <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                    Aucune tâche pour le moment.
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

                            <p class="card-text small"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> Fin : <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i> M. <?= htmlspecialchars($t['nom_encadreur']) ?>
                                </small>
                            </div>

                            <?php if ($t['status'] == 'termine'): ?>
                                <div class="mt-3 p-2 rounded bg-white border border-success text-center">
                                    <small class="text-success fw-bold">Note : <?= ($t['note'] !== null) ? $t['note'] . "/20" : "Attente évaluation" ?></small>
                                </div>
                            <?php else: ?>
                                <div class="d-grid mt-3">
                                    <a href="terminer_tache.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('Confirmez-vous ?')">
                                        Marquer comme terminé
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Réalisées', 'Restantes'],
            datasets: [{
                data: [<?= $realisees ?>, <?= $en_attente ?>],
                backgroundColor: ['#198754', '#e9ecef'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
