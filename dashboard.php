<?php
include 'includes/header.php';
include 'config/db.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- LOGIQUE D'EXTRACTION DES DONNÉES (SQL) ---
// On ne récupère ces chiffres que pour l'admin pour ne pas alourdir le serveur
if ($_SESSION['role'] == 'administrateur') {
    $total_stagiaires = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'stagiaire'")->fetchColumn();
    $total_demandes = $pdo->query("SELECT COUNT(*) FROM demandes WHERE status = 'en_attente'")->fetchColumn();
    $taches_finies = $pdo->query("SELECT COUNT(*) FROM taches WHERE status = 'termine'")->fetchColumn();

    // Données pour le graphique : Type de stage
    $acad = $pdo->query("SELECT COUNT(*) FROM demandes WHERE type_stage = 'academique'")->fetchColumn();
    $pro = $pdo->query("SELECT COUNT(*) FROM demandes WHERE type_stage = 'professionnel'")->fetchColumn();
}
?>

<div class="container-fluid">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); // Important 
        ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tableau de bord - RESOTEL SARL</h1>
        <div class="badge bg-dark p-2">Session : <?php echo ucfirst($_SESSION['role']); ?></div>
    </div>

    <div class="alert alert-light border-0 shadow-sm mb-4">
        <h4>Ravi de vous revoir, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</h4>
        <p class="mb-0 text-muted">Voici l'état actuel de la plateforme de gestion.</p>
    </div>

    <?php if ($_SESSION['role'] == 'administrateur'): ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Stagiaires Actifs</h6>
                                <h2 class="mb-0"><?= $total_stagiaires ?></h2>
                            </div>
                            <i class="fas fa-user-graduate fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Demandes en Attente</h6>
                                <h2 class="mb-0"><?= $total_demandes ?></h2>
                            </div>
                            <i class="fas fa-file-import fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Objectifs Atteints</h6>
                                <h2 class="mb-0"><?= $taches_finies ?></h2>
                            </div>
                            <i class="fas fa-check-double fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white"><strong>Répartition des Candidatures</strong></div>
                    <div class="card-body">
                        <canvas id="stageChart" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white"><strong>Actions Rapides</strong></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="demandes_gestion.php" class="btn btn-outline-primary text-start"><i class="fas fa-user-plus me-2"></i> Valider les nouveaux</a>
                            <a href="affecter_stagiaire.php" class="btn btn-outline-dark text-start"><i class="fas fa-link me-2"></i> Créer une affectation</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-tasks fa-4x text-primary mb-3"></i>
                <h3>Prêt pour vos tâches du jour ?</h3>
                <p>Consultez votre espace personnel pour mettre à jour vos travaux.</p>
                <a href="mes_taches.php" class="btn btn-primary">Voir mes tâches</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/chart.min.js"></script>
<script>
    const ctx = document.getElementById('stageChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Format "Donut" très moderne
        data: {
            labels: ['Académique', 'Professionnel'],
            datasets: [{
                data: [<?= $acad ?>, <?= $pro ?>],
                backgroundColor: ['#0d6efd', '#20c997'],
                hoverOffset: 4
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
