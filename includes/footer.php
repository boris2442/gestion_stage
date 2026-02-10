</main>
</div>
</div>

<footer class="footer py-3 bg-white border-top mt-auto">
    <div class="container text-center">
        <span class="text-muted">© 2026 RESOTEL SARL - Système de Gestion des Stagiaires.</span>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
    let lastScrollTop = 0;
    const navbar = document.getElementById('smartNavbar');

    if (navbar) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // On descend : on cache la barre
                navbar.classList.add('navbar-hidden');
            } else {
                // On monte : on montre la barre
                navbar.classList.remove('navbar-hidden');
            }
            lastScrollTop = scrollTop;
        });
    }
</script>
</body>

</html>
