</main>
</div>
</div>

<footer class="footer py-3 bg-white border-top mt-auto">
    <div class="container text-center">
        <span class="text-muted">© 2026 RESOTEL SARL - Système de Gestion des Stagiaires.</span>
    </div>
</footer>



<script src="assets/js/bootstrap.bundle.min.js"></script>



<script>
    let lastScrollTop = 0;
    const navbar = document.getElementById('smartNavbar');
    const navConnexion = document.getElementById('navbarNav');

    if (navbar) {
        window.addEventListener('scroll', function() {
            // SECURITÉ : On vérifie si le menu mobile (burger) est ouvert
            // Si le menu est ouvert (classe 'show'), on NE TOUCHE PAS à la visibilité
            let isMenuOpen = navConnexion ? navConnexion.classList.contains('show') : false;

            if (!isMenuOpen) {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // On descend
                    navbar.classList.add('navbar-hidden');
                } else {
                    // On monte
                    navbar.classList.remove('navbar-hidden');
                }
                lastScrollTop = scrollTop;
            }
        }, {
            passive: true
        }); // Optimisation des performances
    }

    // // Initialisation jQuery sécurisée
    // $(document).ready(function() {
    //     console.log("Système RESOTEL prêt et stable !");
    // });

    $('.navbar-toggler').on('click', function() {
        // console.log("Le bouton burger a été cliqué !");
        // On force l'affichage du menu
        $('#navbarNav').toggleClass('show');
    });
</script>


</body>

</html>
