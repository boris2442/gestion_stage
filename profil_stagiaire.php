<?php
session_start();
require_once 'config/db.php';

// 1. SÉCURITÉ : Admin ou Encadreur uniquement
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['administrateur', 'encadreur'])) {
    header('Location: index.php');
    exit();
}

// 2. RÉCUPÉRATION DU STAGIAIRE
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: stagiaires.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'stagiaire'");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    die("Stagiaire introuvable.");
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="stagiaires.php">Liste des stagiaires</a></li>
                    <li class="breadcrumb-item active">Profil de <?= htmlspecialchars($s['nom']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-7x text-primary"></i>
                    </div>
                    <h3 class="mb-1"><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></h3>
                    <p class="text-muted text-uppercase small fw-bold mb-3"><?= htmlspecialchars($s['role']) ?></p>
                    <div class="d-grid gap-2">
                        <a href="mailto:<?= $s['email'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>Envoyer un mail
                        </a>
                        <a href="generer_attestation.php?id=<?= $s['id'] ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-file-pdf me-2"></i>Générer l'Attestation
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Type Stage</small>
                            <span class="fw-bold"><?= ucfirst($s['type_stage'] ?? 'N/A') ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Niveau</small>
                            <span class="fw-bold"><?= htmlspecialchars($s['niveau_etude'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Détails du profil
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Adresse Email :</div>
                        <div class="col-sm-8 fw-bold"><?= htmlspecialchars($s['email']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Téléphone :</div>
                        <div class="col-sm-8 fw-bold"><?= htmlspecialchars($s['telephone'] ?? 'Non renseigné') ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Inscrit le :</div>
                        <div class="col-sm-8"><?= date('d/m/Y', strtotime($s['created_at'] ?? 'now')) ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-tasks me-2 text-warning"></i>Projets et Tâches</span>
                    <button class="btn btn-sm btn-primary">Assigner une tâche</button>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-project-diagram fa-3x text-light mb-3"></i>
                    <p class="text-muted">Aucun projet ou tâche n'est rattaché à ce stagiaire pour le moment.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
