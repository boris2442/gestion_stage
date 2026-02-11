<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// Requête pour voir TOUTES les tâches avec les noms des acteurs
$sql = "SELECT t.*, 
               s.nom AS stagiaire_nom, s.prenom AS stagiaire_prenom,
               e.nom AS encadreur_nom, e.prenom AS encadreur_prenom
        FROM taches t
        JOIN users s ON t.id_stagiaire = s.id
        JOIN users e ON t.id_encadreur = e.id
        ORDER BY t.date_creation DESC";
$stmt = $pdo->query($sql);
$taches = $stmt->fetchAll();
include 'includes/header.php';
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list me-2"></i> Suivi Global des Travaux</h2>
        <div class="">
            <a href="ajouter_tache.php" class="btn btn-success btn-sm fw-bold">Ajouter une tache</a>

        </div>

        <div class="text-muted">Total : <?= count($taches) ?> missions en cours</div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tâche</th>
                        <th>Stagiaire</th>
                        <th>Assigné par</th>
                        <th>Deadline</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $t): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?= htmlspecialchars($t['titre']) ?></span><br>
                                <small class="text-muted"><?= substr(htmlspecialchars($t['description']), 0, 50) ?>...</small>
                            </td>
                            <td><?= htmlspecialchars($t['stagiaire_nom']) ?></td>
                            <td><small>M. <?= htmlspecialchars($t['encadreur_nom']) ?></small></td>
                            <td>
                                <?php
                                $date_fin = new DateTime($t['date_fin']);
                                echo $date_fin->format('d/m/Y');
                                ?>
                            </td>
                            <td>
                                <?php if ($t['status'] == 'a_faire'): ?>
                                    <span class="badge bg-secondary">À faire</span>
                                <?php elseif ($t['status'] == 'en_cours'): ?>
                                    <span class="badge bg-primary">En cours</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Terminé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light" title="Voir les détails">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
