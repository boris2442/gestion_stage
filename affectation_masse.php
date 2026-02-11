<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// --- NOUVEAU : Récupérer la session active ---
$stmt = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt->fetch();

if (!$session_active) {
    die("<div class='alert alert-danger m-5'>Erreur : Aucune session n'est activée. <a href='liste_sessions.php'>Activez une session ici</a> avant d'affecter des stagiaires.</div>");
}

// 1. Récupérer les stagiaires qui n'ont pas encore d'ID de session (ou qui ne sont pas dans la session active)
// On ajoute id_session_actuelle dans la requête
$sql_stagiaires = "SELECT id, nom, prenom, niveau_etude FROM users 
                   WHERE role = 'stagiaire' 
                   AND (id_session_actuelle IS NULL OR id_session_actuelle != ?)";
$stagiaires = $pdo->prepare($sql_stagiaires);
$stagiaires->execute([$session_active['id']]);
$liste_stagiaires = $stagiaires->fetchAll();

// 2. Récupérer les encadreurs
$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i> Affectation pour la session active : <strong><?= htmlspecialchars($session_active['titre']) ?></strong></span>
        <span class="badge bg-primary">ID Session: <?= $session_active['id'] ?></span>
    </div>

    <h2 class="mb-4"><i class="fas fa-users-cog me-2"></i> Affectation en Masse</h2>

    <form action="traitement_affectation_masse.php" method="POST">
        <input type="hidden" name="id_session" value="<?= $session_active['id'] ?>">
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold text-primary">
                        Sélectionner les stagiaires (<?= count($liste_stagiaires) ?> en attente)
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center"><input type="checkbox" id="checkAll"></th>
                                    <th>Stagiaire</th>
                                    <th>Niveau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($liste_stagiaires as $s): ?>
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
                        <label class="form-label fw-bold text-danger">Action de l'ingénieur :</label>
                        <p class="small text-muted">Les stagiaires sélectionnés seront liés à l'encadreur ET à la session active.</p>
                        
                        <label class="form-label fw-bold">Assigner à l'encadreur :</label>
                        <select name="id_encadreur" class="form-select mb-3" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($encadreurs as $e): ?>
                                <option value="<?= $e['id'] ?>">M. <?= htmlspecialchars($e['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary w-100 shadow">
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
