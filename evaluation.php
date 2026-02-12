<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'stagiaire') {
    header('Location: index.php');
    exit();
}

$id_encadreur = $_SESSION['user_id'];

// 1. Récupérer la session active
$stmt_sess = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt_sess->fetch();
$id_session_active = $session_active ? $session_active['id'] : 0;

// 2. Récupérer les stagiaires avec leurs statistiques de tâches
$sql = "SELECT u.id, u.nom, u.prenom, u.note_final, u.observations,
        (SELECT COUNT(*) FROM taches WHERE id_stagiaire = u.id AND id_session = ?) as total_taches,
        (SELECT COUNT(*) FROM taches WHERE id_stagiaire = u.id AND id_session = ? AND status = 'termine') as taches_faites
        FROM users u 
        WHERE u.encadreur_id = ? 
        AND u.role = 'stagiaire' 
        AND u.id_session_actuelle = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_session_active, $id_session_active, $id_encadreur, $id_session_active]);
$stagiaires = $stmt->fetchAll();

// 3. Traitement de la notation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['evaluer'])) {
    if (isset($_POST['notes']) && is_array($_POST['notes'])) {
        foreach ($_POST['notes'] as $id_stagiaire => $note) {
            // On ne met à jour que si une note a été saisie
            if ($note !== "") {
                $obs = isset($_POST['observations'][$id_stagiaire]) ? htmlspecialchars($_POST['observations'][$id_stagiaire]) : '';

                $sql_update = "UPDATE users SET note_final = ?, observations = ? WHERE id = ?";
                $pdo->prepare($sql_update)->execute([$note, $obs, $id_stagiaire]);
            }
        }
        echo "<script>alert('Évaluations enregistrées avec succès !'); window.location='evaluation.php';</script>";
        exit();
    }
}

include 'includes/header.php';
?>
<div class="container mt-4">
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-edit me-2"></i> Évaluation pour la session : <strong><?= htmlspecialchars($session_active['titre'] ?? 'Aucune') ?></strong>
    </div>

    <form method="POST">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Stagiaire</th>
                            <th>Progression Session</th>
                            <th style="width: 150px;">Note /20</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stagiaires)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <p>Aucun stagiaire n'est actuellement sous votre responsabilité pour cette session.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stagiaires as $s):
                                $ratio = ($s['total_taches'] > 0) ? round(($s['taches_faites'] / $s['total_taches']) * 100) : 0;
                                // Couleur dynamique de la barre
                                $bar_class = ($ratio == 100) ? 'bg-success' : ($ratio > 50 ? 'bg-info' : 'bg-warning');
                            ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                        <?= htmlspecialchars(strtoupper($s['nom']) . ' ' . $s['prenom']) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 10px; border-radius: 5px;">
                                                <div class="progress-bar <?= $bar_class ?> progress-bar-striped progress-bar-animated"
                                                    style="width: <?= $ratio ?>%"></div>
                                            </div>
                                            <span class="badge bg-light text-dark border"><?= $ratio ?>%</span>
                                        </div>
                                        <small class="text-muted"><?= $s['taches_faites'] ?> tâches terminées sur <?= $s['total_taches'] ?></small>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="notes[<?= $s['id'] ?>]"
                                                class="form-control fw-bold text-primary"
                                                value="<?= $s['note_final'] ?>"
                                                placeholder="00" min="0" max="20" step="0.5">
                                            <span class="input-group-text">/20</span>
                                        </div>
                                    </td>
                                    <td>
                                  <textarea name="observations[<?= $s['id'] ?>]" class="form-control form-control-sm" placeholder="Appréciation globale..." rows="1"><?= htmlspecialchars($s['observations'] ?? '') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<div class="text-end mt-3 mb-5">
    <button type="submit" name="evaluer" class="btn btn-primary shadow">
        <i class="fas fa-save me-2"></i> Enregistrer les notes
    </button>
</div>
</form>
</div>
<?php include 'includes/footer.php'; ?>
