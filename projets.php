<?php
$currentPage = 'projets';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Projets - INFINITIA GROUP SARLU</title>
    <meta name="description" content="Découvrez les projets numériques portés par INFINITIA GROUP SARLU.">
    <meta name="keywords" content="INFINITIA GROUP, projets numériques, Care Services, RuralHer Impact, AgriChain">

    <!-- Favicons -->
    <link href="assets/img/ico.ico" rel="icon">
    <link href="assets/img/ico.ico" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="portfolio-page">
    <?php include("menu.php"); ?>
<br>  <br>  <br>
    <main class="main">
        <!-- Projects Section -->
        <section id="portfolio" class="portfolio section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Projets</h2>
                <p>Les initiatives numériques stratégiques développées par INFINITIA GROUP SARLU.</p>
            </div>

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <article class="card h-100 border-0 shadow-sm">
                            <img src="assets/img/portfolio/care.png" class="img-fluid" alt="INFINITIA Care Services">
                            <div class="card-body">
                                <span class="badge rounded-pill mb-2" style="background:#D84B8A; color:#FFFFFF;">Services à domicile</span>
                                <h3 class="h5">INFINITIA Care Services</h3>
                                <p>Plateforme numérique de gestion des services à domicile permettant de recruter, former, affecter et suivre des intervenants qualifiés pour répondre aux besoins des familles, entreprises et organisations.</p>
                                <p><strong>Mots-clés :</strong> Web, Mobile, Gestion des missions, Formation, Suivi qualité</p>
                                <p><strong>Statut :</strong> En développement</p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="projet-care-services.php" class="btn btn-sm btn-primary">Découvrir le projet</a>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <article class="card h-100 border-0 shadow-sm">
                            <img src="assets/img/portfolio/impact.png" class="img-fluid" alt="RuralHer Impact">
                            <div class="card-body">
                                <span class="badge rounded-pill mb-2" style="background:#D84B8A; color:#FFFFFF;">Agriculture durable &amp; inclusion économique</span>
                                <h3 class="h5">RuralHer Impact</h3>
                                <p>Plateforme digitale destinée à renforcer l’autonomisation économique des femmes rurales à travers la valorisation des produits maraîchers, l’accès aux marchés, l’inclusion numérique et la restauration écologique.</p>
                                <p><strong>Mots-clés :</strong> Web, Android, iOS, SMS, USSD, Données agricoles</p>
                                <p><strong>Statut :</strong> Projet de financement</p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="projet-ruralher-impact.php" class="btn btn-sm btn-primary">Découvrir le projet</a>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <article class="card h-100 border-0 shadow-sm">
                            <img src="assets/img/portfolio/agri.png" class="img-fluid" alt="INFINITIA AgriChain">
                            <div class="card-body">
                                <span class="badge rounded-pill mb-2" style="background:#D84B8A; color:#FFFFFF;">Logistique agricole intelligente</span>
                                <h3 class="h5">INFINITIA AgriChain</h3>
                                <p>Plateforme intégrée de gestion des flux agricoles, du transport et de la distribution, connectant producteurs, coopératives, transporteurs, entrepôts, acheteurs et marchés grâce à une logistique numérique intelligente.</p>
                                <p><strong>Mots-clés :</strong> Web, Mobile, Géolocalisation, Traçabilité, Logistique, IA</p>
                                <p><strong>Statut :</strong> Prototype startup</p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="projet-agrichain.php" class="btn btn-sm btn-primary">Découvrir le projet</a>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("pied.php"); ?>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
</body>

</html>
