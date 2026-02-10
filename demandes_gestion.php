<?php
include 'includes/header.php';
// Sécurité : Uniquement pour l'Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php'); exit();
}

// Récupération des demandes en attente
$stmt = $pdo->query("SELECT * FROM demandes WHERE status = 'en_attente' ORDER BY date_demande DESC");
$demandes = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-user-clock me-2"></i> Gestion des Candidatures</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Candidat</th>
                        <th>Type</th>
                        <th>Documents</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($demandes as $d): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($d['nom']) ?> <?= htmlspecialchars($d['prenom']) ?></strong><br>
                            <small class="text-muted"><?= $d['email'] ?> | CNI: <?= $d['cni'] ?></small>
                        </td>
                        <td><span class="badge bg-info"><?= ucfirst($d['type_stage']) ?></span></td>
                        <td>
                            <a href="uploads/cv/<?= $d['cv_path'] ?>" class="btn btn-sm btn-outline-danger" target="_blank">CV</a>
                            <a href="uploads/cv/<?= $d['lettre_motivation_path'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">Lettre</a>
                        </td>
                        <td>
                            <a href="valider_stagiaire.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-success">Valider</a>
                            <button class="btn btn-sm btn-danger">Rejeter</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
