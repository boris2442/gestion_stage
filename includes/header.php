<!-- <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOTEL SARL - Gestion des Stagiaires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .sidebar { min-height: 100vh; z-index: 100; }
        .nav-link { color: #333; }
        .nav-link.active { color: #0d6efd; font-weight: bold; }
        footer { margin-top: auto; }
    </style>
  
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php if(isset($_SESSION['user_id'])): ?>
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <img src="assets/img/logo_resotel.png" alt="Logo" style="width: 80px;">
                    <h6 class="mt-2">RESOTEL SARL</h6>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
                    </li>
                    <?php if($_SESSION['role'] == 'administrateur'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="demandes_gestion.php"><i class="fas fa-user-plus me-2"></i> Candidatures</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="taches.php"><i class="fas fa-tasks me-2"></i> Mes Tâches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="evaluation.php"><i class="fas fa-file-alt me-2"></i> Évaluations</a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
        <?php else: ?>
        <main class="col-12 px-md-4 pt-3">
        <?php endif; ?> -->


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOTEL SARL - Gestion des Stagiaires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <style>
   body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
.sidebar { min-height: 100vh; z-index: 100; position: sticky; top: 0; }
.nav-link { color: #333; }
.nav-link.active { color: #0d6efd; font-weight: bold; }

/* Styles pour le Header Intelligent */
.navbar-smart {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1050;
    transition: transform 0.3s ease-in-out; /* Animation fluide */
}

/* Cache la barre quand on descend */
.navbar-hidden {
    transform: translateY(-100%);
}

/* Ajustement pour que le contenu ne soit pas caché sous la barre fixe au chargement */
body.not-logged {
    padding-top: 60px;
}
  </style>
</head>
<body>

        <body>

<?php if(!isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm navbar-smart" id="smartNavbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="assets/img/logo_resotel.png" alt="Logo" style="width: 30px;" class="me-2">
            RESOTEL SARL
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="postuler.php">Postuler</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light btn-sm" href="login.php fw-bold">Connexion</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-warning btn-sm fw-bold text-dark" href="register.php">S'inscrire</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <?php if(isset($_SESSION['user_id'])): ?>
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <img src="assets/img/logo_resotel.png" alt="Logo" style="width: 80px;">
                    <h6 class="mt-2 fw-bold text-primary">RESOTEL SARL</h6>
                    <small class="text-muted text-uppercase"><?php echo $_SESSION['role']; ?></small>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
                    </li>
                    <?php if($_SESSION['role'] == 'administrateur'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="demandes_gestion.php"><i class="fas fa-user-plus me-2"></i> Candidatures</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="taches.php"><i class="fas fa-tasks me-2"></i> Mes Tâches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="evaluation.php"><i class="fas fa-file-alt me-2"></i> Évaluations</a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
        <?php else: ?>
        <main class="col-12 px-0"> <?php endif; ?>
