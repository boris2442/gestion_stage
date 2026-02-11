<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';

// 1. Sécurité : Seul l'admin accède à cette page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: index.php");
    exit();
}

// 2. Récupération des rôles (ENUM)
$rolesStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
$roleInfo = $rolesStmt->fetch();
preg_match("/^enum\((.*)\)$/", $roleInfo['Type'], $matches);
$roles = str_getcsv($matches[1], ',', "'");

// 3. Gestion de la Session Active (Correction de la colonne 'titre')
$stmt_sess = $pdo->query("SELECT * FROM sessions WHERE is_active = 1 LIMIT 1");
$active_session = $stmt_sess->fetch();

if ($active_session) {
    $id_session_active = $active_session['id'];
    $nom_session_affiche = $active_session['titre']; // Colonne correcte : titre
} else {
    $id_session_active = 0;
    $nom_session_affiche = "Aucune session active";
}

// 4. Construction de la requête filtrée
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

/**
 * LOGIQUE DE GÉNIE LOGICIEL :
 * On affiche tous les Admins et Encadreurs.
 * On n'affiche que les stagiaires de la session ACTIVE.
 */
$sql = "SELECT id, nom, email, role, date_inscription FROM users 
        WHERE (role IN ('administrateur', 'encadreur') 
           OR (role = 'stagiaire' AND id_session_actuelle = :active_sess))";

$params = ['active_sess' => $id_session_active];

if ($search) {
    $sql .= " AND (nom LIKE :search_nom OR email LIKE :search_email)";
    $params['search_nom'] = "%$search%";
    $params['search_email'] = "%$search%";
}

if ($role_filter) {
    $sql .= " AND role = :role";
    $params['role'] = $role_filter;
}

$sql .= " ORDER BY role ASC, nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// 5. Calcul des KPI (Statistiques)
$count_total = count($users);
$count_admin = 0;
$count_encadreurs = 0;
$count_stagiaires = 0;

foreach ($users as $user) {
    if ($user['role'] == 'administrateur') $count_admin++;
    elseif ($user['role'] == 'encadreur') $count_encadreurs++;
    elseif ($user['role'] == 'stagiaire') $count_stagiaires++;
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog me-2"></i>Gestion des utilisateurs</h2>
        <div class="alert alert-info mb-0 py-2 shadow-sm border-0">
            <i class="fas fa-calendar-check me-2"></i>
            Session active : <strong><?= htmlspecialchars($nom_session_affiche) ?></strong>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-dark text-white text-center p-2">
                <small class="text-uppercase opacity-75">Total (Session Actuelle)</small>
                <h3 class="mb-0"><?= $count_total ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 p-2 text-center">
                <small class="text-uppercase text-success fw-bold">Stagiaires Actifs</small>
                <h3 class="mb-0"><?= $count_stagiaires ?></h3>
            </div>
        </div>
    </div>

    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Rechercher nom ou email..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Tous les rôles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r ?>" <?= $role_filter === $r ? 'selected' : '' ?>>
                        <?= ucfirst(htmlspecialchars($r)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            <a href="users.php" class="btn btn-outline-secondary w-100 mt-2">Réinitialiser</a>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($u['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <form action="update_role.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="role" class="form-select form-select-sm bg-light border-0" onchange="this.form.submit()">
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= $r ?>" <?= ($u['role'] === $r) ? 'selected' : '' ?>>
                                                    <?= ucfirst($r) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-dark">Vous</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                            <td class="text-center">
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
