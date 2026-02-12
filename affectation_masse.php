<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if ($_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

// 1. Récupérer la session active
$stmt = $pdo->query("SELECT id, titre FROM sessions WHERE is_active = 1 LIMIT 1");
$session_active = $stmt->fetch();

if (!$session_active) {
    die("<div class='alert alert-danger m-5'>Erreur : Aucune session n'est activée. <a href='liste_sessions.php'>Activez une session ici</a> avant d'affecter des stagiaires.</div>");
}

// 2. LA REQUÊTE CORRIGÉE :
// On prend les stagiaires qui :
// - N'ont pas la bonne session (id_session_actuelle IS NULL OR != ?)
// - OU qui ont la bonne session mais PAS d'encadreur (encadreur_id IS NULL OR 0)
$sql_stagiaires = "SELECT id, nom, prenom, niveau_etude FROM users 
                   WHERE role = 'stagiaire' 
                   AND (
                       id_session_actuelle IS NULL 
                       OR id_session_actuelle != ? 
                       OR encadreur_id IS NULL 
                       OR encadreur_id = 0
                   )";

$stagiaires = $pdo->prepare($sql_stagiaires);
$stagiaires->execute([$session_active['id']]);
$liste_stagiaires = $stagiaires->fetchAll();

// 3. Récupérer les encadreurs
//$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();
$encadreurs = $pdo->query("SELECT id, nom, prenom FROM users WHERE role = 'encadreur'")->fetchAll();
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i> Session cible : <strong><?= htmlspecialchars($session_active['titre']) ?></strong></span>
    </div>

    <h2 class="mb-4"><i class="fas fa-users-cog me-2"></i> Affectation en Masse</h2>

    <form action="traitement_affectation_masse.php" method="POST">
        <input type="hidden" name="id_session" value="<?= $session_active['id'] ?>">
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold text-primary">
                        Stagiaires en attente d'encadreur (<?= count($liste_stagiaires) ?>)
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
    document.getElementById('checkAll').onclick = function() {
        let checkboxes = document.getElementsByClassName('checkItem');
        for (let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
