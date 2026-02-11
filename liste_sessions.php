<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'stagiaire') {
    header('Location: index.php');
    exit();
}

// --- LOGIQUE DE L'INTERRUPTEUR (BOOLEAN) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if ($_GET['action'] == 'activer') {
        // On désactive tout d'abord pour n'avoir qu'une seule session active à la fois
        $pdo->query("UPDATE sessions SET is_active = 0");
        $pdo->prepare("UPDATE sessions SET is_active = 1 WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] == 'desactiver') {
        $pdo->prepare("UPDATE sessions SET is_active = 0 WHERE id = ?")->execute([$id]);
    }
    
    header("Location: liste_sessions.php");
    exit();
}

// Utilisation de LEFT JOIN pour voir les sessions même si aucun stagiaire n'est lié
$sql = "SELECT s.*, 
               u1.nom AS nom_stagiaire, u1.prenom AS prenom_stagiaire,
               u2.nom AS nom_encadreur, u2.prenom AS prenom_encadreur
        FROM sessions s
        LEFT JOIN users u1 ON s.id_stagiaire = u1.id
        LEFT JOIN users u2 ON s.id_encadreur = u2.id
        ORDER BY s.id DESC";

$sessions = $pdo->query($sql)->fetchAll();
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-layer-group me-2 text-primary"></i> Pilotage des Promotions</h2>
        <a href="creer_session.php" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nouvelle Session
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start">Titre de la Session</th>
                        <th>Période</th>
                        <th>Statut (Interrupteur)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sessions)): ?>
                        <tr><td colspan="4" class="py-4 text-muted">Aucune session créée pour le moment.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sessions as $s): ?>
                            <tr>
                                <td class="text-start">
                                    <span class="fw-bold text-uppercase"><?= htmlspecialchars($s['titre']) ?></span>
                                    <?php if ($s['is_active']): ?>
                                        <span class="badge rounded-pill bg-primary ms-2 small">SESSION COURANTE</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?= date('d/m/y', strtotime($s['date_debut'])) ?> au <?= date('d/m/y', strtotime($s['date_fin'])) ?>
                                    </small>
                                </td>

                                <td style="width: 200px;">
                                    <?php if ($s['is_active'] == 1): ?>
                                        <a href="?action=desactiver&id=<?= $s['id'] ?>" class="btn btn-success btn-sm w-100 shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i> ACTIVÉE
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=activer&id=<?= $s['id'] ?>" class="btn btn-outline-danger btn-sm w-100">
                                            <i class="fas fa-power-off me-1"></i> DÉSACTIVÉE
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="btn-group">
                                        <a href="modifier_session.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-light border" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="supprimer_session.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Supprimer cette session ?')" title="Supprimer">
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

<?php include 'includes/footer.php'; ?>
