<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// 1. Récupérer les stagiaires NON AFFECTÉS (ou tous)
$stagiaires = $pdo->query("SELECT id, nom, prenom, niveau_etude FROM users WHERE role = 'stagiaire' AND encadreur_id IS NULL")->fetchAll();

// 2. Récupérer les encadreurs
$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-users-cog me-2"></i> Affectation en Masse</h2>

    <form action="traitement_affectation_masse.php" method="POST">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold text-primary">
                        Sélectionner les stagiaires (<?= count($stagiaires) ?> en attente)
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50 text-center"><input type="checkbox" id="checkAll"></th>
                                    <th>Stagiaire</th>
                                    <th>Niveau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stagiaires as $s): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="ids_stagiaires[]" value="<?= $s['id'] ?>" class="checkItem">
                                        </td>
                                        <td><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></td>
                                        <td><span class="badge bg-light text-dark"><?= $s['niveau_etude'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <label class="form-label fw-bold">Assigner à l'encadreur :</label>
                        <select name="id_encadreur" class="form-select mb-3" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($encadreurs as $e): ?>
                                <option value="<?= $e['id'] ?>">M. <?= htmlspecialchars($e['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-link me-2"></i> Confirmer l'affectation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Script pour tout cocher d'un coup
    document.getElementById('checkAll').onclick = function() {
        let checkboxes = document.getElementsByClassName('checkItem');
        for (let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
</script>
