<?php
session_start();
require_once 'config/db.php';


// Sécurité : Seul l'admin accède à cette page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// ACTION : Mise à jour du statut d'un incident
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_id = $_GET['id'];
    $nouveau_statut = ($_GET['action'] == 'resoudre') ? 'resolu' : 'en_cours';

    $update = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
    $update->execute([$nouveau_statut, $id_id]);
    header('Location: admin_incidents.php?msg=statut_mis_a_jour');
}

// RÉCUPÉRATION : Tous les incidents avec les infos du stagiaire
$sql = "SELECT i.*, u.nom, u.prenom 
        FROM incidents i 
        JOIN users u ON i.id_stagiaire = u.id 
        ORDER BY i.status ASC, i.date_signalement DESC";
$incidents = $pdo->query($sql)->fetchAll();
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-headset text-danger me-2"></i> Gestion des Signalements</h2>
        <span class="badge bg-dark"><?= count($incidents) ?> Ticket(s) au total</span>
    </div>

    <?php if (empty($incidents)): ?>
        <div class="alert alert-info text-center py-5 shadow-sm">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <p class="mb-0">Aucun problème signalé par les stagiaires pour le moment. Tout va bien !</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm rounded overflow-hidden">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Stagiaire</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody style="vertical-align: middle;">
                    <?php foreach ($incidents as $inc): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y H:i', strtotime($inc['date_signalement'])) ?></td>
                            <td><strong><?= htmlspecialchars($inc['prenom'] . ' ' . $inc['nom']) ?></strong></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($inc['sujet']) ?></span></td>
                            <td style="max-width: 300px;"><small><?= nl2br(htmlspecialchars($inc['message'])) ?></small></td>
                            <td>
                                <?php
                                $color = ($inc['status'] == 'resolu') ? 'success' : (($inc['status'] == 'en_cours') ? 'warning' : 'danger');
                                $label = ($inc['status'] == 'resolu') ? 'Résolu' : (($inc['status'] == 'en_cours') ? 'En cours' : 'Ouvert');
                                ?>
                                <span class="badge bg-<?= $color ?> px-3 py-2"><?= $label ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($inc['status'] !== 'en_cours' && $inc['status'] !== 'resolu'): ?>
                                        <a href="admin_incidents.php?action=traiter&id=<?= $inc['id'] ?>" class="btn btn-sm btn-warning">Traiter</a>
                                    <?php endif; ?>

                                    <?php if ($inc['status'] !== 'resolu'): ?>
                                        <a href="admin_incidents.php?action=resoudre&id=<?= $inc['id'] ?>" class="btn btn-sm btn-success">Régler</a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled><i class="fas fa-check-double"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
