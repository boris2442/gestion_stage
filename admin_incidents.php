<?php
session_start();
require_once 'config/db.php';

// Sécurité : Uniquement l'Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// Action : Marquer comme résolu
if (isset($_GET['action']) && $_GET['action'] == 'resoudre') {
    $id_inc = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE incidents SET status = 'resolu' WHERE id = ?");
    $stmt->execute([$id_inc]);
    header('Location: admin_incidents.php?msg=Incidents résolu');
    exit();
}

// Récupérer les incidents avec les noms des stagiaires
$sql = "SELECT i.*, u.nom, u.prenom 
        FROM incidents i 
        JOIN users u ON i.id_stagiaire = u.id 
        ORDER BY i.date_signalement DESC";
$incidents = $pdo->query($sql)->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-exclamation-triangle text-danger me-2"></i> Gestion des Incidents</h2>
        <span class="badge bg-dark"><?= count($incidents) ?> signalement(s) au total</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Stagiaire</th>
                            <th>Sujet</th>
                            <th>Message</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incidents)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Aucun incident signalé pour le moment.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($incidents as $inc): ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($inc['date_signalement'])) ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($inc['nom'] . ' ' . $inc['prenom']) ?></span>
                                </td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($inc['sujet']) ?></span></td>
                                <td class="text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($inc['message']) ?>
                                </td>
                                <td>
                                    <?php if ($inc['status'] == 'ouvert'): ?>
                                        <span class="badge bg-danger">En attente</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Résolu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($inc['status'] == 'ouvert'): ?>
                                        <a href="admin_incidents.php?action=resoudre&id=<?= $inc['id'] ?>"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="return confirm('Marquer ce problème comme réglé ?')">
                                            <i class="fas fa-check"></i> Résoudre
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-light" disabled><i class="fas fa-check-double"></i> Terminé</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
