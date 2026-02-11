<?php
session_start();

include 'includes/header.php'; ?>

<section class="bg-gradient-primary text-white py-5 shadow-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #00bfff 100%);">


    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php
            if ($_GET['error'] == 1) echo "Identifiants incorrects ou compte inexistant.";
            else echo "Une erreur est survenue lors de la connexion.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>




    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 animate__animated animate__fadeInLeft">
                <h1 class="display-4 fw-bold lh-1 mb-3">
                    <span class="d-block">Optimisez la Gestion de vos Talents</span>
                    <span class="d-block text-warning">avec RESOTEL SARL</span>
                </h1>
                <p class="lead fw-light mb-4 opacity-75">
                    Notre plateforme centralisée révolutionne le processus de stage, de la candidature à l'évaluation finale. Un outil indispensable pour les entreprises ambitieuses et les jeunes professionnels.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="postuler.php" class="btn btn-warning btn-lg px-4 me-md-2 shadow-lg text-dark fw-bold border-0">
                        <i class="fas fa-paper-plane me-2"></i>Postuler
                    </a>
                    <a href="login.php" class="btn btn-primary btn-lg px-4 me-md-2 shadow-lg fw-bold border-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4 shadow-sm border-2">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block animate__animated animate__fadeInRight">
                <img src="assets/img/hero-illustration.png" alt="Illustration de gestion de stage" class="img-fluid floating-element" width="500">
            </div>
        </div>
    </div>
    <div class="position-absolute w-100 h-100 top-0 start-0" style="z-index: -1;">
        <div class="shape shape-1 bg-white opacity-10 animate__animated animate__zoomIn"></div>
        <div class="shape shape-2 bg-white opacity-10 animate__animated animate__zoomIn animate__delay-1s"></div>
    </div>
</section>

<section class="bg-light py-5">
    <div class="container py-5">
        <h2 class="text-center mb-5 display-6 fw-bold text-dark animate__animated animate__fadeInUp">Pourquoi Choisir Notre Plateforme ?</h2>
        <div class="row g-4 justify-content-center">

            <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp animate__delay-0-5s">
                <div class="card h-100 shadow-sm border-0 transform-on-hover">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 p-3 shadow">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <h3 class="card-title h5 mb-3">Candidatures Simplifiées</h3>
                        <p class="card-text text-muted">Un processus clair et rapide pour attirer les meilleurs profils. Dépôt facile de CV et lettres de motivation.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card h-100 shadow-sm border-0 transform-on-hover">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 p-3 shadow">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                        <h3 class="card-title h5 mb-3">Suivi et Gestion des Tâches</h3>
                        <p class="card-text text-muted">Attribuez et supervisez les projets des stagiaires avec une visibilité complète sur leur progression.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp animate__delay-1-5s">
                <div class="card h-100 shadow-sm border-0 transform-on-hover">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 p-3 shadow">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3 class="card-title h5 mb-3">Évaluations et Rapports</h3>
                        <p class="card-text text-muted">Des outils d'évaluation performants et des rapports détaillés pour une mesure objective des compétences.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="bg-dark text-white py-5">
    <div class="container text-center py-4 animate__animated animate__fadeInUp">
        <h2 class="display-6 fw-bold mb-3">Prêt à Transformer la Gestion de vos Stagiaires ?</h2>
        <p class="lead mb-4 opacity-75">Rejoignez RESOTEL SARL et découvrez une nouvelle ère de l'encadrement professionnel.</p>
        <a href="postuler.php" class="btn btn-warning btn-lg px-5 shadow-lg text-dark fw-bold border-0 animate__animated animate__pulse animate__infinite">
            <i class="fas fa-hand-point-right me-2"></i>Lancez votre candidature !
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Ajoute la classe 'active' au lien Dashboard dans le sidebar si l'utilisateur est connecté
        // Tu peux adapter cette logique selon tes pages si tu veux que d'autres liens soient actifs
        if (window.location.pathname.includes("dashboard.php")) {
            $('#sidebar .nav-link[href="dashboard.php"]').addClass('active');
        } else if (window.location.pathname.includes("demandes_gestion.php")) {
            $('#sidebar .nav-link[href="demandes_gestion.php"]').addClass('active');
        } else if (window.location.pathname.includes("taches.php")) {
            $('#sidebar .nav-link[href="taches.php"]').addClass('active');
        } else if (window.location.pathname.includes("evaluation.php")) {
            $('#sidebar .nav-link[href="evaluation.php"]').addClass('active');
        }
        // Ajoute ici d'autres logiques d'activation de liens si nécessaire pour ton sidebar
    });
</script>

<style>
    /* Styles pour les animations subtiles */
    .feature-icon {
        transition: all 0.3s ease;
    }

    .card:hover .feature-icon {
        transform: scale(1.1);
    }

    .transform-on-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .transform-on-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Styles pour les formes décoratives de la section Hero */
    .shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        filter: blur(50px);
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        top: -50px;
        left: -50px;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .shape-2 {
        width: 400px;
        height: 400px;
        bottom: -100px;
        right: -100px;
        background-color: rgba(255, 255, 255, 0.15);
    }

    /* Animation pour l'image flottante */
    .floating-element {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-15px);
        }

        100% {
            transform: translateY(0);
        }
    }
</style>
