<?php
// stagiaires.php
session_start();
include 'includes/header.php';
include 'config/db.php';

// Sécurité : Uniquement Admin et Encadreur peuvent voir la liste
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'administrateur' && $_SESSION['role'] !== 'encadreur')) {
    header('Location: index.php');
    exit();
}

// Récupération des users ayant le rôle 'stagiaire'
// $stmt = $pdo->query("SELECT * FROM users WHERE role = 'stagiaire' ORDER BY nom ASC");
// $stagiaires = $stmt->fetchAll();



// Requête avec jointure pour récupérer le nom de l'encadreur
$sql = "SELECT s.*, e.nom AS nom_encadreur, e.prenom AS prenom_encadreur 
        FROM users s 
        LEFT JOIN users e ON s.encadreur_id = e.id 
        WHERE s.role = 'stagiaire' 
        ORDER BY s.nom ASC";
$stmt = $pdo->query($sql);
$stagiaires = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-graduate me-2"></i> Stagiaires Actifs</h2>
        <span class="badge bg-primary"><?= count($stagiaires) ?> Stagiaires au total</span>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nom & Prénom</th>
                        <th>Email / Tel</th>
                        <!-- <th>Niveau / Type</th> -->
                        <th>Statut Encadrement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stagiaires as $s): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></strong></td>
                            <td><?= htmlspecialchars($s['email']) ?><br><small><?= $s['telephone'] ?></small></td>
                            <!-- <td>
                                <span class="badge bg-info"><?= htmlspecialchars($s['niveau']) ?></span>
                                <span class="badge bg-secondary"><?= ucfirst($s['type_stage']) ?></span>
                            </td> -->
                            <td>
                                <?php if ($s['encadreur_id']): ?>
                                    <span class="text-success small fw-bold">
                                        <i class="fas fa-user-check me-1"></i>
                                        Encadré par M. <?= htmlspecialchars($s['nom_encadreur']) ?>
                                    </span>
                                <?php else: ?>
                                    <a href="affectation_masse.php" class="text-decoration-none">
                                        <span class="badge bg-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Non assigné
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="profil_stagiaire.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary" title="Voir profil">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="generer_attestation.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-success" title="Générer Attestation">
                                    <i class="fas fa-file-pdf"></i>
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
