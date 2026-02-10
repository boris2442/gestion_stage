<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';

// 1. Sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Récupération des rôles (ENUM)
$rolesStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
$roleInfo = $rolesStmt->fetch();
preg_match("/^enum\((.*)\)$/", $roleInfo['Type'], $matches);
$roles = str_getcsv($matches[1], ',', "'");
//	enum('administrateur', 'encadreur', 'stagiaire')
// 3. Construction de la requête avec paramètres uniques
$search = $_GET['search'] ?? '';
$role   = $_GET['role'] ?? '';

// Ajoute bien date_inscription ici !
$sql = "SELECT id, nom, email, role, date_inscription FROM users WHERE 1=1";
$params = [];

if ($search) {
    // CORRECTION : On utilise deux clés différentes pour les deux marqueurs
    $sql .= " AND (nom LIKE :search_nom OR email LIKE :search_email)";
    $params['search_nom'] = "%$search%";
    $params['search_email'] = "%$search%";
}

if ($role) {
    $sql .= " AND role = :role";
    $params['role'] = $role;
}

// 4. Exécution
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();


//gestion des KPI

// Calcul des KPI
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

<h2 class="mb-4">Gestion des utilisateurs</h2>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-body py-2 px-3">
                <small class="text-uppercase fw-bold opacity-75">Total</small>
                <h4 class="mb-0"><?= $count_total ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body py-2 px-3">
                <small class="text-uppercase fw-bold text-primary">Admins</small>
                <h4 class="mb-0"><?= $count_admin ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body py-2 px-3">
                <small class="text-uppercase fw-bold text-info">Encadreurs</small>
                <h4 class="mb-0"><?= $count_encadreurs ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body py-2 px-3">
                <small class="text-uppercase fw-bold text-success">Stagiaires</small>
                <h4 class="mb-0"><?= $count_stagiaires ?></h4>
            </div>
        </div>
    </div>
</div>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
            placeholder="Nom ou email" value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="">Tous les rôles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r ?>" <?= $role === $r ? 'selected' : '' ?>>
                    <?= ucfirst(htmlspecialchars($r)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <div class="btn-group w-100">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <?php if ($search || $role): ?>
                <a href="users.php" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Creer la</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <form action="update_role.php" method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <select name="role" class="form-select form-select-sm d-inline-block w-auto border-0 bg-light" onchange="this.form.submit()">
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r ?>" <?= ($u['role'] === $r) ? 'selected' : '' ?>>
                                        <?= ucfirst($r) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php else: ?>
                        <span class="badge bg-dark">Vous (<?= ucfirst($u['role']) ?>)</span>
                    <?php endif; ?>
                </td>
                <td class="text-sm"><i> <?= date('d/m/Y', strtotime($u['date_inscription'])) ?></i></td>

                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="delete_user.php?id=<?= $u['id'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Supprimer cet utilisateur ?')">
                            Supprimer
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Vous</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include 'includes/footer.php'; ?>
