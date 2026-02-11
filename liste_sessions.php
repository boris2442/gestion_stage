<?php
session_start();
require_once 'config/db.php';

// Sécurité : Uniquement Admin ou Encadreur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'stagiaire') {
    header('Location: index.php');
    exit();
}

// Récupération de toutes les sessions avec les noms des stagiaires et encadreurs
$sql = "SELECT s.*, 
               u1.nom AS nom_stagiaire, u1.prenom AS prenom_stagiaire,
               u2.nom AS nom_encadreur, u2.prenom AS prenom_encadreur
        FROM sessions s
        JOIN users u1 ON s.id_stagiaire = u1.id
        JOIN users u2 ON s.id_encadreur = u2.id
        ORDER BY s.date_debut DESC";

$sessions = $pdo->query($sql)->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-handshake me-2 text-primary"></i> Gestion des Sessions de Stage</h2>
        <a href="creer_session.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle Session
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre du Stage</th>
                            <th>Stagiaire</th>
                            <th>Encadreur</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Note finale</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Aucune session enregistrée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sessions as $s): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($s['titre']) ?></span>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-graduate me-1 text-muted"></i>
                                        <?= htmlspecialchars($s['nom_stagiaire']) ?> <?= htmlspecialchars($s['prenom_stagiaire']) ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-tie me-1 text-muted"></i>
                                        M. <?= htmlspecialchars($s['nom_encadreur']) ?>
                                    </td>
                                    <td>
                                        <small>
                                            Du <?= date('d/m/y', strtotime($s['date_debut'])) ?><br>
                                            Au <?= date('d/m/y', strtotime($s['date_fin'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($s['status'] === 'en_cours'): ?>
                                            <span class="badge bg-warning text-dark">En cours</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Terminé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            <?= ($s['note'] !== null) ? $s['note'] . "/20" : "--" ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="modifier_session.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="supprimer_session.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Supprimer cette session ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
