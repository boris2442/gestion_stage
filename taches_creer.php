<?php
include 'includes/header.php';
// Sécurité : Uniquement Encadreur ou Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'stagiaire') {
    header('Location: index.php');
    exit();
}


// Récupérer uniquement les stagiaires ACTIFS de cet encadreur
$stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.prenom, s.nom_session 
    FROM users u 
    JOIN sessions s ON u.id_session_actuelle = s.id 
    WHERE u.encadreur_id = ? 
    AND u.role = 'stagiaire'
    AND s.is_active = 1
");
$stmt->execute([$_SESSION['user_id']]);
$mes_stagiaires = $stmt->fetchAll();



// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $id_s = $_POST['id_stagiaire'];
//     $titre = htmlspecialchars($_POST['titre']);
//     $desc = htmlspecialchars($_POST['description']);

//     $sql = "INSERT INTO taches (id_stagiaire, titre, description, status) VALUES (?, ?, ?, 'a_faire')";
//     if ($pdo->prepare($sql)->execute([$id_s, $titre, $desc])) {
//         echo "<script>alert('Tâche assignée !');</script>";
//     }
// }


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_s = $_POST['id_stagiaire'];
    $titre = htmlspecialchars($_POST['titre']);
    $desc = htmlspecialchars($_POST['description']);
    $deadline = $_POST['date_fin']; // On utilise ta colonne date_fin

    // 1. On va chercher la session actuelle du stagiaire
    $stmt_sess = $pdo->prepare("SELECT id_session_actuelle FROM users WHERE id = ?");
    $stmt_sess->execute([$id_s]);
    $user_info = $stmt_sess->fetch();
    $id_session = $user_info['id_session_actuelle'];

    // 2. On insère la tâche avec l'ID de session
    $sql = "INSERT INTO taches (id_stagiaire, id_session, titre, description, date_fin, status) 
            VALUES (?, ?, ?, ?, ?, 'a_faire')";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$id_s, $id_session, $titre, $desc, $deadline])) {
        echo "<script>alert('Tâche assignée et liée à la session !');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h4><i class="fas fa-plus-circle me-2"></i> Assigner une nouvelle tâche</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Sélectionner le Stagiaire</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                        <select name="id_stagiaire" class="form-select" required>
                            <option value="">-- Choisir un stagiaire --</option>
                            <?php foreach ($mes_stagiaires as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?>
                                    (<?= htmlspecialchars($s['nom_session']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Titre de la tâche</label>
                    <input type="text" name="titre" class="form-control" placeholder="Ex: Rédaction du rapport hebdomadaire" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Date limite (Deadline)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="deadline" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description / Instructions</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Détaillez les objectifs ici..."></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Envoyer la tâche</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
