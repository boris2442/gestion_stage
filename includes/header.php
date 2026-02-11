<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOTEL SARL - Gestion des Stagiaires</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="assets/js/chart.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 70px;
        }

        .navbar-smart {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 9999 !important;
            transition: transform 0.3s ease-in-out;
        }

        .navbar-hidden {
            transform: translateY(-100%);
        }

        .sidebar {
            min-height: 100vh;
            z-index: 100;
            position: sticky;
            top: 70px;
        }

        .nav-link {
            color: #333;
        }

        .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .collapse.navbar-collapse.show {
            display: block !important;
        }
    </style>
</head>

<body class="<?php echo isset($_SESSION['user_id']) ? 'logged-in' : 'not-logged'; ?>">

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
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="postuler.php">Postuler</a></li>
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-light btn-sm fw-bold" href="login.php">Connexion</a></li>
                        <li class="nav-item ms-lg-2"><a class="btn btn-warning btn-sm fw-bold text-dark" href="register.php">S'inscrire</a></li>
                    <?php else: ?>

                        <li class="nav-item"><span class="nav-link text-white-50 me-3">Salut, <?php echo $_SESSION['nom'] ?? 'Utilisateur'; ?></span></li>


                        <?php
                        if ($_SESSION['role'] === 'stagiaire'):
                            //Afficher le role pour debugger
                            echo "Role de l'utilisateur : " . $_SESSION['role'];
                        ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'mes_taches.php') ? 'active' : '' ?>" href="mes_taches.php">
                                    <i class="fas fa-tasks me-2 text-primary"></i> <strong>Mes Tâches</strong>
                                </a>
                            </li>
                        <?php
                        endif;
                        ?>




                        <li class="nav-item"><a class="dropdown-item" href="editer_profil.php">
                                <i class="fas fa-user-edit me-2"></i>Mon Profil
                            </a></li>
                        <li class="nav-item"><a class="btn btn-light btn-sm fw-bold me-2" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="btn btn-danger btn-sm fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Quitter</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php
                if ($_GET['success'] == 'registered') echo "Inscription réussie ! Connectez-vous.";
                if ($_GET['success'] == 'loggedout') echo "Déconnexion réussie.";
                if ($_GET['success'] == 'session_added') echo "Session créée.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $isDashboard = isset($_SESSION['user_id'])
        && basename($_SERVER['PHP_SELF']) !== 'index.php';
    ?>

    <div class="container-fluid">
        <div class="row">
            <?php
            $isDashboard = isset($_SESSION['user_id'])
                && basename($_SERVER['PHP_SELF']) !== 'index.php';
            ?>

            <?php if ($isDashboard): ?>
                <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
                    <!-- CONTENU DU SIDEBAR DIRECTEMENT ICI -->
                    <ul class="nav flex-column p-3">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i> Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>



                        <li class="nav-item">
                            <a class="nav-link" href="stagiaires.php">
                                <i class="fas fa-users me-2"></i> Stagiaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="demandes_gestion.php">
                                <i class="fas fa-users me-2"></i> Demandes
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users-cog me-2"></i> Utilisateurs
                            </a>
                        </li>


                        <?php if ($_SESSION['role'] === 'administrateur'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'affectation_masse.php') ? 'active' : '' ?>"
                                    href="affectation_masse.php">
                                    <i class="fas fa-users-cog me-2"></i>
                                    <span>Affectation en Masse</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="recapitulatif_encadrement.php">
                                <i class="fas fa-sitemap me-2"></i>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_taches_admin.php">
                                <i class="fas fa-tasks me-2"></i>
                                <span>Taches</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                        </li>
                    </ul>
                </nav>

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
                <?php else: ?>
                    <main class="col-12 px-0">
                    <?php endif; ?>
