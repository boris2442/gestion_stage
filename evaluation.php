<?php
session_start();
require_once 'config/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'stagiaire') {
    header('Location: index.php');
    exit();
}

// 1. SQL Amélioré : On récupère les sessions ET le compte des tâches
$sql = "SELECT s.*, u.nom, u.prenom,
        (SELECT COUNT(*) FROM taches WHERE id_stagiaire = s.id_stagiaire) as total_taches,
        (SELECT COUNT(*) FROM taches WHERE id_stagiaire = s.id_stagiaire AND status = 'termine') as taches_faites
        FROM sessions s 
        JOIN users u ON s.id_stagiaire = u.id 
        WHERE s.id_encadreur = ? AND s.status = 'en_cours'";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$sessions = $stmt->fetchAll();
var_dump($sessions); // Debug : Affiche les données récupérées
// 2. Traitement de la notation multiple
// 2. Traitement de la notation multiple
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['evaluer'])) {
    // On vérifie si "notes" existe pour éviter l'erreur "Undefined array key"
    if (isset($_POST['notes']) && is_array($_POST['notes'])) {
        foreach ($_POST['notes'] as $id_session => $note) {
            // On ne traite que si une note a été saisie (pas vide)
            if ($note !== "") {
                $obs = isset($_POST['observations'][$id_session]) ? htmlspecialchars($_POST['observations'][$id_session]) : '';

                $sql_update = "UPDATE sessions SET note = ?, observations = ?, status = 'termine' WHERE id = ?";
                $pdo->prepare($sql_update)->execute([$note, $obs, $id_session]);
            }
        }
        echo "<script>alert('Évaluations enregistrées avec succès !'); window.location='evaluation.php';</script>";
        exit();
    }
}
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-star text-warning me-2"></i> Évaluation Groupée des Stagiaires</h4>
        <span class="badge bg-info"><?= count($sessions) ?> stagiaires en cours</span>
    </div>

    <form method="POST">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Stagiaire</th>
                            <th>Performance (Tâches)</th>
                            <th style="width: 150px;">Note /20</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $s):
                            $ratio = ($s['total_taches'] > 0) ? round(($s['taches_faites'] / $s['total_taches']) * 100) : 0;
                        ?>
                            <tr>
                                <td>
                                    <strong><?= $s['nom'] ?> <?= $s['prenom'] ?></strong><br>
                                    <small class="text-muted"><?= $s['titre'] ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?= $ratio ?>%"></div>
                                        </div>
                                        <small class="fw-bold"><?= $s['taches_faites'] ?> / <?= $s['total_taches'] ?></small>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Soit <?= $ratio ?>% de réussite</small>
                                </td>
                                <td>
                                    <input type="number" name="notes[<?= $s['id'] ?>]" class="form-control form-control-sm" min="0" max="20" placeholder="Ex: 15">
                                </td>
                                <td>
                                    <textarea name="observations[<?= $s['id'] ?>]" class="form-control form-control-sm" rows="1" placeholder="Commentaire..."></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3">
                <button type="submit" name="evaluer" class="btn btn-warning fw-bold shadow-sm">
                    <i class="fas fa-save me-2"></i> Enregistrer toutes les notes
                </button>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
