<?php
$currentPage = 'projet-care-services';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>INFINITIA Care Services - INFINITIA GROUP SARLU</title>
    <meta name="description" content="INFINITIA Care Services, plateforme numérique de gestion des services à domicile.">
    <meta name="keywords" content="INFINITIA Care Services, services à domicile, plateforme numérique, intervenants">

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

<body class="portfolio-details-page">
    <?php include("menu.php"); ?>

    <main class="main">
        <!-- Project Hero -->
        <section class="page-title light-background">
            <div class="container">
                <div class="heading" data-aos="fade-up">
                    <h1>INFINITIA Care Services</h1>
                    <p>Plateforme numérique de gestion des services à domicile</p>
                </div>
            </div>
        </section>

        <!-- Project Details -->
        <section class="starter-section section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-8">
                        <div class="p-4 border rounded-3 h-100">
                            <h2>Présentation du projet</h2>
                            <p>INFINITIA Care Services est une plateforme numérique accessible via le web et les appareils mobiles. Elle permet aux clients de formuler leurs besoins en services à domicile et à l’entreprise de sélectionner les intervenants les plus adaptés pour y répondre.</p>

                            <h3>Problématique</h3>
                            <p>Le recrutement des intervenants à domicile se fait encore souvent de manière informelle, sans mécanisme fiable de contrôle, de vérification ou de suivi. Cette situation expose les clients à des risques liés à la sécurité, à la qualité du service et à la fiabilité des personnes recrutées.</p>

                            <h3>Objectif général</h3>
                            <p>Créer une plateforme numérique permettant la gestion complète des services à domicile et la mise en relation sécurisée entre les clients et les intervenants.</p>

                            <h3>Impact attendu</h3>
                            <p>Le projet contribue à la professionnalisation du secteur des services à domicile, à la création d’opportunités d’emploi et à l’amélioration de la qualité des prestations fournies aux clients.</p>

                            <h3>Statut du projet</h3>
                            <p><strong>En développement</strong></p>

                            <a href="projets.php" class="btn btn-primary mt-3">
                                <i class="bi bi-arrow-left"></i> Retour aux projets
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="p-4 border rounded-3 mb-4">
                            <h3>Services couverts</h3>
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-check2-circle"></i> Femme de ménage</li>
                                <li><i class="bi bi-check2-circle"></i> Nounou</li>
                                <li><i class="bi bi-check2-circle"></i> Cuisinier</li>
                                <li><i class="bi bi-check2-circle"></i> Gardien</li>
                                <li><i class="bi bi-check2-circle"></i> Jardinier</li>
                                <li><i class="bi bi-check2-circle"></i> Assistant aux personnes âgées</li>
                                <li><i class="bi bi-check2-circle"></i> Personnel d’entretien</li>
                                <li><i class="bi bi-check2-circle"></i> Aide domestique</li>
                            </ul>
                        </div>

                        <div class="p-4 border rounded-3">
                            <h3>Modules principaux</h3>
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-grid"></i> Gestion des utilisateurs</li>
                                <li><i class="bi bi-grid"></i> Gestion des intervenants</li>
                                <li><i class="bi bi-grid"></i> Gestion des clients</li>
                                <li><i class="bi bi-grid"></i> Gestion des demandes de service</li>
                                <li><i class="bi bi-grid"></i> Gestion des missions</li>
                                <li><i class="bi bi-grid"></i> Gestion documentaire</li>
                                <li><i class="bi bi-grid"></i> Gestion des formations</li>
                                <li><i class="bi bi-grid"></i> Gestion des évaluations</li>
                                <li><i class="bi bi-grid"></i> Tableau de bord et statistiques</li>
                            </ul>
                        </div>
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
