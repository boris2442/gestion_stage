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

// 2. SQL Amélioré : On récupère les STAGIAIRES de la session active liés à cet encadreur
$sql = "SELECT u.id, u.nom, u.prenom,
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
            if ($note !== "") {
                $obs = isset($_POST['observations'][$id_stagiaire]) ? htmlspecialchars($_POST['observations'][$id_stagiaire]) : '';

                // On met à jour les notes dans la table users (ou une table dédiée)
                // Ici, on considère que tu as des colonnes note et observations dans 'users'
                $sql_update = "UPDATE users SET note_final = ?, observations = ? WHERE id = ?";
                $pdo->prepare($sql_update)->execute([$note, $obs, $id_stagiaire]);
            }
        }
        echo "<script>alert('Évaluations enregistrées !'); window.location='evaluation.php';</script>";
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
                        <?php foreach ($stagiaires as $s):
                            $ratio = ($s['total_taches'] > 0) ? round(($s['taches_faites'] / $s['total_taches']) * 100) : 0;
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?= $ratio ?>%"></div>
                                        </div>
                                        <small><?= $s['taches_faites'] ?>/<?= $s['total_taches'] ?></small>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" name="notes[<?= $s['id'] ?>]" class="form-control form-control-sm" min="0" max="20">
                                </td>
                                <td>
                                    <textarea name="observations[<?= $s['id'] ?>]" class="form-control form-control-sm" rows="1"></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
