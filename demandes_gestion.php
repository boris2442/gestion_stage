<?php
session_start();
include 'includes/header.php';
include 'config/db.php';
// Sécurité : Uniquement pour l'Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// Récupération des demandes en attente
$stmt = $pdo->query("SELECT * FROM demandes WHERE status = 'en_attente' ORDER BY date_demande DESC");
$demandes = $stmt->fetchAll();



//Gestion des KPI 
// Calcul des KPI pour les demandes en attente
$total_demandes = count($demandes);
$academiques = 0;
$professionnels = 0;

foreach ($demandes as $d) {
    if ($d['type_stage'] == 'academique') $academiques++;
    else $professionnels++;
}
?>

<div class="container-fluid">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'rejected'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            La candidature a été rejetée et déplacée dans les archives.

            <a href="demandes_gestion.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Actualiser</a>
        </div>
    <?php endif; ?>
    <h2 class="mb-4"><i class="fas fa-user-clock me-2"></i> Gestion des Candidatures</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Total en attente</h6>
                            <h2 class="mb-0"><?= $total_demandes ?></h2>
                        </div>
                        <i class="fas fa-file-import fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.8rem;">Stages Académiques</h6>
                    <h2 class="mb-0 text-info"><?= $academiques ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.8rem;">Stages Professionnels</h6>
                    <h2 class="mb-0 text-success"><?= $professionnels ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Candidat</th>
                        <th>Type && Niveau etude</th>
                        <th>Documents</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $d): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($d['nom']) ?> <?= htmlspecialchars($d['prenom']) ?></strong><br>
                                <small class="text-muted"><?= $d['email'] ?> | CNI: <?= $d['cni'] ?></small><br>
                                <small class="text-muted">Soumis le <?= date('d/m/Y', strtotime($d['date_demande'])) ?></small>
                            </td>
                            <td><span class="badge bg-info"><?= ucfirst($d['type_stage']) ?></span><span class="badge bg-secondary"><?= $d['niveau_etude'] ?></span></td>
                            <td>
                                <a href="/uploads/cv/<?= $d['cv_path'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>CV
                                </a>

                                <a href="/uploads/cv/<?= $d['lettre_motivation_path'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    <i class="fas fa-file-alt me-1"></i>Lettre
                                </a>
                            </td>
                            <td>
                                <a href="valider_stagiaire.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-success">Valider</a>
                                <a href="rejeter_demande.php?id=<?= $d['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette candidature ?')">
                                    <i class="fas fa-times me-1"></i> Rejeter
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
