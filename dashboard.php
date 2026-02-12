<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- INITIALISATION PAR DÉFAUT ---
$total_stagiaires = 0;
$total_demandes = 0;
$taches_finies = 0;
$non_assignes = 0;
$assignes = 0;
$acad = 0;
$pro = 0;
$session_active = null;

// --- 1. SESSION ACTIVE (POUR TOUS LES RÔLES) ---
// On sort cette requête du "if admin" pour que l'encadreur la voit aussi
$stmt_active = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt_active->fetch();
$id_active = $session_active ? $session_active['id'] : 0;

// --- 2. CALCUL DES DONNÉES SI ADMIN ---
if ($_SESSION['role'] == 'administrateur' || $_SESSION['role'] == 'encadreur') {

    // Alerte si aucune session (visible uniquement par l'admin)
    if (!$session_active) {
        echo "<div class='alert alert-warning m-3'><i class='fas fa-exclamation-circle'></i> <strong>Attention :</strong> Aucune session de stage n'est active. <a href='gestion_sessions.php'>Activer une session ici</a></div>";
    }

    if ($id_active > 0) {
        // Stagiaires actifs dans CETTE session
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'stagiaire' AND id_session_actuelle = ?");
        $stmt->execute([$id_active]);
        $total_stagiaires = $stmt->fetchColumn();

        // Stagiaires non assignés
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'stagiaire' AND id_session_actuelle = ? AND encadreur_id IS NULL");
        $stmt->execute([$id_active]);
        $non_assignes = $stmt->fetchColumn();

        // Tâches terminées
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM taches WHERE status = 'termine' AND id_session = ?");
        $stmt->execute([$id_active]);
        $taches_finies = $stmt->fetchColumn();
    }

    $total_demandes = $pdo->query("SELECT COUNT(*) FROM demandes WHERE status = 'en_attente'")->fetchColumn();
    $acad = $pdo->query("SELECT COUNT(*) FROM demandes WHERE type_stage = 'academique'")->fetchColumn();
    $pro = $pdo->query("SELECT COUNT(*) FROM demandes WHERE type_stage = 'professionnel'")->fetchColumn();
    $assignes = $total_stagiaires - $non_assignes;
}

// --- 3. CALCUL DES DONNÉES SI ENCADREUR (Optionnel) ---
if ($_SESSION['role'] == 'encadreur' && $id_active > 0) {
    // Si tu veux que l'encadreur voit ses propres stats
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE encadreur_id = ? AND id_session_actuelle = ?");
    $stmt->execute([$_SESSION['user_id'], $id_active]);
    $mes_stagiaires = $stmt->fetchColumn();
}

include 'includes/header.php';
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
        <div class="d-flex align-items-center">
            <span class="text-muted me-2 small">Promotion active :</span>
            <?php if ($session_active): ?>
                <div class="badge bg-success p-2 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i>
                    <?= htmlspecialchars($session_active['titre']) ?>
                </div>
            <?php else: ?>
                <a href="creer_session.php" class="badge bg-danger p-2 text-decoration-none shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Activer une session
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="alert alert-light border-0 shadow-sm mb-4">
        <h4>Ravi de vous revoir, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</h4>
        <?php if ($_SESSION['role'] == 'administrateur' && $non_assignes > 0): ?>

            <div class="alert alert-danger border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">

                <div>

                    <i class="fas fa-exclamation-triangle me-2"></i>

                    <strong>Attention :</strong> Il y a <strong><?= $non_assignes ?></strong> stagiaire(s) actif(s) sans encadreur assigné.

                </div>

                <a href="affectation_masse.php" class="btn btn-danger btn-sm">Affecter maintenant</a>

            </div>

        <?php endif; ?>
        <p class="mb-0 text-muted">Voici l'état actuel de la plateforme de gestion.</p>
    </div>

    <?php if ($_SESSION['role'] == 'administrateur' || $_SESSION['role'] == 'encadreur'): ?>
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
                    <div class="card-header bg-white"><strong>Statistiques Globales</strong></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <small class="text-muted d-block mb-2">Types de Stage</small>
                                <canvas id="stageChart" height="200"></canvas>
                            </div>
                            <div class="col-6 text-center">
                                <small class="text-muted d-block mb-2">Suivi Encadrement</small>
                                <canvas id="assignationChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white"><strong>Actions Rapides</strong></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="demandes_gestion.php" class="btn btn-outline-primary text-start"><i class="fas fa-user-plus me-2"></i> Valider les nouveaux</a>
                            <!-- <a href="affecter_stagiaire.php" class="btn btn-outline-dark text-start"><i class="fas fa-link me-2"></i> Créer une affectation</a> -->
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


<script>
    const ctx = document.getElementById('stageChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Format "Donut" très moderne
        data: {
            labels: ['Académique', 'Professionnel'],
            datasets: [{
                data: [<?= (int)$acad ?>, <?= (int)$pro ?>],
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




    // Graphique d'Assignation
    // Graphique d'Assignation
    // Graphique d'Assignation
    const ctx2 = document.getElementById('assignationChart').getContext('2d');
    const totalStagiaires = <?= (int)$total_stagiaires ?>;

    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: totalStagiaires > 0 ? ['Assignés', 'Non-Assignés'] : ['Aucun stagiaire'],
            datasets: [{
                // Si le total est 0, on met 1 dans une donnée "grise" pour afficher le cercle
                data: totalStagiaires > 0 ? [<?= (int)$assignes ?>, <?= (int)$non_assignes ?>] : [1],
                backgroundColor: totalStagiaires > 0 ? ['#198754', '#dc3545'] : ['#e9ecef'], // Gris clair si vide
                hoverOffset: 4
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                },
                // Désactive les tooltips si c'est vide pour ne pas afficher "Aucun stagiaire: 1"
                tooltip: {
                    enabled: totalStagiaires > 0
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
