<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

// Sécurité : Uniquement pour l'Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// 1. Récupérer tous les encadreurs
$stmt = $pdo->query("SELECT id, nom, prenom, email FROM users WHERE role = 'encadreur' ORDER BY nom ASC");
$encadreurs = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-sitemap me-2 text-primary"></i> Récapitulatif de l'Encadrement</h2>
        <a href="affectation_masse.php" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nouvelle Affectation
        </a>
    </div>

    <div class="row">
        <?php if (empty($encadreurs)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun encadreur n'est enregistré dans le système.</div>
            </div>
        <?php endif; ?>

        <?php foreach ($encadreurs as $e): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white p-2 me-3">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($e['nom'] . ' ' . $e['prenom']) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-uppercase small fw-bold text-secondary border-bottom pb-2">Stagiaires suivis :</p>

                        <?php
                        // Sous-requête pour récupérer les stagiaires de CET encadreur
                        $stmt_s = $pdo->prepare("SELECT nom, prenom, niveau_etude FROM users WHERE encadreur_id = ? AND role = 'stagiaire'");
                        $stmt_s->execute([$e['id']]);
                        $mes_stagiaires = $stmt_s->fetchAll();

                        if (count($mes_stagiaires) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($mes_stagiaires as $st): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <span><i class="fas fa-user-graduate me-2 text-info small"></i><?= htmlspecialchars($st['nom'] . ' ' . $st['prenom']) ?></span>
                                        <span class="badge rounded-pill bg-light text-dark border"><?= $st['niveau_etude'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <span class="badge bg-light text-muted fw-normal">Aucun stagiaire assigné</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-light border-0 text-center">
                        <small class="fw-bold text-primary"><?= count($mes_stagiaires) ?> Stagiaire(s)</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
