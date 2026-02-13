<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOTEL SARL - Gestion des Stagiaires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding-top: 70px;
        }

        .sidebar {
            min-height: calc(100vh - 70px);
            background: white;
            transition: all 0.3s;
            z-index: 1000;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 250px;
                height: 100%;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        .nav-link {
            color: #333;
            padding: 10px 20px;
        }

        .nav-link:hover {
            background: #f0f7ff;
            color: #0d6efd;
        }

        .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
            background: #e7f1ff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <button class="btn btn-primary d-md-none me-2" id="sidebarCollapse"><i class="fas fa-bars"></i></button>
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="assets/img/logo_resotel.png" alt="Logo" style="width: 30px;" class="me-2"> RESOTEL SARL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (!isset($_SESSION['user_id'])): ?>

                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-light btn-sm fw-bold" href="login.php">Connexion</a></li>
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-light btn-sm fw-bold" href="register.php">Inscription</a></li>
                    <?php else: ?>
                        <li class="nav-item"><span class="nav-link text-white-50 me-3">Salut, <?= $_SESSION['nom'] ?? 'Utilisateur'; ?></span></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm fw-bold" href="dashboard.php"><i class="fas fa-sign-out-alt"></i> dashboard</a></li>
                        <li class="nav-item"><a class="btn btn-danger btn-sm fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Quitter</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar-overlay" id="overlay"></div>

    <div class="container-fluid">
        <div class="row">
            <?php
            $isDashboard = isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'index.php';
            if ($isDashboard):
            ?>
                <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar shadow-sm bg-white overflow-y-scroll">
                    <div class="position-sticky">
                        <ul class="nav flex-column p-3">



                            <li class="nav-item">
                                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>" href="index.php">
                                    <i class="fas fa-home me-2"></i> Accueil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>

                            <?php if ($_SESSION['role'] === 'stagiaire'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'mes_taches.php') ? 'active' : '' ?>" href="mes_taches.php">
                                        <i class="fas fa-tasks me-2"></i> Mes Tâches
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Si le role est stagiaire on affiche pas -->
                            <?php if ($_SESSION['role'] !== 'stagiaire'): ?>

                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'evaluation.php') ? 'active' : '' ?>" href="evaluation.php">
                                        <i class="fas fa-star me-2"></i> Evaluations
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'stagiaires.php') ? 'active' : '' ?>" href="stagiaires.php">
                                        <i class="fas fa-users me-2"></i> Stagiaires
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'demandes_gestion.php') ? 'active' : '' ?>" href="demandes_gestion.php">
                                        <i class="fas fa-envelope me-2"></i> Demandes
                                    </a>
                                </li>

                                <?php if ($_SESSION['role'] === 'administrateur'): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'affectation_masse.php') ? 'active' : '' ?>" href="affectation_masse.php">
                                            <i class="fas fa-users-cog me-2"></i> Affectations
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'recapitulatif_encadrement.php') ? 'active' : '' ?>" href="recapitulatif_encadrement.php">
                                        <i class="fas fa-sitemap me-2"></i> Vue d'ensemble
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'gestion_taches_admin.php') ? 'active' : '' ?>" href="gestion_taches_admin.php">
                                        <i class="fas fa-tasks me-2"></i> Tâches
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'liste_sessions.php') ? 'active' : '' ?>" href="liste_sessions.php">
                                        <i class="fas fa-list me-2"></i> Sessions
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : '' ?>" href="users.php">
                                        <i class="fas fa-users me-2"></i> Users
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'gestion_rapports.php') ? 'active' : '' ?>" href="gestion_rapports.php">
                                        <i class="fas fa-file-signature me-2"></i> Rapports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'admin_incidents.php') ? 'active' : '' ?>" href="admin_incidents.php">
                                        <i class="fas fa-headset me-2"></i> Signalements
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item  border-top pt-2">
                                <a class="nav-link text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
                <?php else: ?>
                    <main class="col-12 px-4 pt-3">
                    <?php endif; ?>
