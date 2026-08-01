<?php
$currentPage = 'equipe';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Notre équipe - INFINITIA GROUP SARLU</title>
    <meta name="description" content="Découvrez l'équipe d'INFINITIA GROUP SARLU, ses fonctions et ses missions principales.">
    <meta name="keywords" content="équipe, INFINITIA GROUP SARLU, direction, informatique, communication, commercial">
    <link href="assets/img/ico.ico" rel="icon">
    <link href="assets/img/ico.ico" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .team-page-title {
            padding: 70px 0 35px;
            background: color-mix(in srgb, var(--accent-color), transparent 94%);
        }

        .team-page-title h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .team-breadcrumb {
            margin: 0;
            padding: 0;
            list-style: none;
            color: color-mix(in srgb, var(--default-color), transparent 35%);
        }

        .team-breadcrumb li {
            display: inline-flex;
            align-items: center;
            font-size: 15px;
        }

        .team-breadcrumb li + li::before {
            content: ">";
            padding: 0 10px;
            color: color-mix(in srgb, var(--default-color), transparent 55%);
        }

        .team-breadcrumb a {
            color: var(--accent-color);
        }

        .team-intro {
            padding-bottom: 20px;
        }

        .team-intro .intro-box {
            max-width: 920px;
            margin: 0 auto;
        }

        .team-intro h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .team-intro p {
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 0;
            color: color-mix(in srgb, var(--default-color), transparent 22%);
        }

        .team-member-card {
            height: 100%;
            overflow: hidden;
            background: var(--surface-color);
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 92%);
            border-radius: 8px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .team-member-card:hover {
            transform: translateY(-6px);
            border-color: color-mix(in srgb, var(--accent-color), transparent 70%);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.1);
        }

        .team-member-card .member-photo {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: color-mix(in srgb, var(--default-color), transparent 94%);
        }

        .team-member-card .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .team-member-card:hover .member-photo img {
            transform: scale(1.04);
        }

        .team-member-card .member-content {
            padding: 26px;
        }

        .team-member-card h3,
        .team-member-card h4 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .team-member-card .member-role {
            display: block;
            margin-bottom: 14px;
            color: color-mix(in srgb, var(--default-color), transparent 28%);
            font-weight: 500;
            line-height: 1.5;
        }

        .team-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            margin-bottom: 16px;
            border-radius: 50px;
            color: var(--accent-color);
            background: color-mix(in srgb, var(--accent-color), transparent 90%);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .team-member-card .member-description {
            color: color-mix(in srgb, var(--default-color), transparent 18%);
            line-height: 1.75;
            margin-bottom: 20px;
        }

        .team-member-card .member-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            font-size: 16px;
            font-weight: 700;
            color: var(--heading-color);
        }

        .team-member-card .missions-list {
            padding-left: 18px;
            margin-bottom: 22px;
        }

        .team-member-card .missions-list li {
            margin-bottom: 8px;
            line-height: 1.55;
            color: color-mix(in srgb, var(--default-color), transparent 22%);
        }

        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .skills-list li {
            padding: 7px 11px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: color-mix(in srgb, var(--default-color), transparent 12%);
            background: color-mix(in srgb, var(--default-color), transparent 94%);
        }

        .featured-member {
            display: grid;
            grid-template-columns: minmax(280px, 0.9fr) minmax(0, 1.1fr);
        }

        .featured-member .member-photo {
            aspect-ratio: auto;
            min-height: 100%;
        }

        .featured-member .member-content {
            padding: 34px;
        }

        .featured-member h3 {
            font-size: 30px;
        }

        .team-cta {
            padding: 60px 0 80px;
        }

        .team-cta .cta-box {
            padding: 42px 28px;
            border-radius: 8px;
            text-align: center;
            background: color-mix(in srgb, var(--accent-color), transparent 92%);
            border: 1px solid color-mix(in srgb, var(--accent-color), transparent 82%);
        }

        .team-cta h2 {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        @media (max-width: 991px) {
            .featured-member {
                grid-template-columns: 1fr;
            }

            .featured-member .member-photo {
                aspect-ratio: 4 / 3;
            }
        }

        @media (max-width: 575px) {
            .team-page-title h1 {
                font-size: 34px;
            }

            .team-intro h2,
            .team-cta h2 {
                font-size: 26px;
            }

            .team-member-card .member-content,
            .featured-member .member-content {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body class="team-page">
    <?php include("menu.php"); ?>
    <br><br><br>

    <main class="main">
        <div class="team-page-title">
            <div class="container" data-aos="fade-up">
                <h1>Notre équipe</h1>
                <ul class="team-breadcrumb">
                    <li><a href="index.php">Accueil</a></li>
                    <li>Équipe</li>
                </ul>
            </div>
        </div>

        <section id="team-intro" class="team-intro section">
            <div class="container" data-aos="fade-up">
                <div class="intro-box text-center">
                    <h2>Une équipe jeune, engagée et orientée innovation</h2>
                    <p>
                        INFINITIA GROUP SARLU s’appuie sur une équipe dynamique et multidisciplinaire pour concevoir, développer et déployer des solutions numériques utiles, fiables et adaptées aux besoins des entreprises, des institutions et des communautés.
                    </p>
                </div>
            </div>
        </section>

        <section id="team" class="team section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Notre équipe</h2>
                <p>Des profils complémentaires mobilisés autour de la qualité, de l’innovation et du service client.</p>
            </div>

            <div class="container">
                <div class="row gy-4">
                    <div class="col-12" data-aos="fade-up" data-aos-delay="100">
                        <article class="team-member-card featured-member">
                            <div class="member-photo">
                                <img src="assets/img/laeti.jpeg" class="img-fluid" alt="LAETITIA MPALABA">
                            </div>
                            <div class="member-content">
                                <span class="team-badge"><i class="bi bi-award"></i> CEO</span>
                                <h3>LAETITIA MPALABA</h3>
                                <span class="member-role">Directrice Générale (CEO) et Responsable des Opérations</span>
                                <p class="member-description">
                                    Directrice Générale d’INFINITIA GROUP SARLU, elle assure la direction générale de l’entreprise, définit la vision stratégique, supervise l’ensemble des activités, représente l’entreprise auprès des partenaires et institutions, valide les décisions importantes et organise les réunions stratégiques et opérationnelles.
                                </p>

                                <h5 class="member-section-title"><i class="bi bi-check2-circle"></i> Missions principales</h5>
                                <ul class="missions-list">
                                    <li>Assurer la direction générale de l’entreprise.</li>
                                    <li>Définir la vision stratégique et les objectifs de l’organisation.</li>
                                    <li>Superviser l’ensemble des activités de l’entreprise.</li>
                                    <li>Représenter l’entreprise auprès des partenaires et institutions.</li>
                                    <li>Valider les décisions importantes et les plans d’action.</li>
                                    <li>Superviser le recrutement des assistantes ménagères.</li>
                                    <li>Vérifier les profils et les références des candidates.</li>
                                    <li>Participer à l’évaluation des compétences des candidates.</li>
                                    <li>Assurer le suivi de la qualité des services fournis aux clients.</li>
                                    <li>Organiser les réunions stratégiques et opérationnelles.</li>
                                </ul>

                                <h5 class="member-section-title"><i class="bi bi-stars"></i> Compétences</h5>
                                <ul class="skills-list">
                                    <li>Direction stratégique</li>
                                    <li>Gestion des opérations</li>
                                    <li>Supervision qualité</li>
                                </ul>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <article class="team-member-card">
                            <div class="member-photo">
                                <img src="assets/img/Francky.jpeg" class="img-fluid" alt="FRANCKY SABITI">
                            </div>
                            <div class="member-content">
                                <span class="team-badge"><i class="bi bi-cpu"></i> IT Manager</span>
                                <h4>FRANCKY SABITI</h4>
                                <span class="member-role">Responsable Informatique (IT Manager) et Chargé des Programmes</span>
                                <p class="member-description">
                                    Responsable informatique d’INFINITIA GROUP SARLU, il conçoit et développe les plateformes numériques de l’entreprise, assure la maintenance technique du site web et des applications, gère les bases de données, veille à la sécurité des systèmes et participe à la planification des activités stratégiques.
                                </p>

                                <h5 class="member-section-title"><i class="bi bi-check2-circle"></i> Missions principales</h5>
                                <ul class="missions-list">
                                    <li>Concevoir et développer la plateforme INFINITIA Care Services.</li>
                                    <li>Assurer la maintenance technique du site web et des applications.</li>
                                    <li>Gérer les bases de données de l’entreprise.</li>
                                    <li>Garantir la sécurité et la disponibilité des systèmes numériques.</li>
                                    <li>Élaborer et suivre les programmes et projets de l’entreprise.</li>
                                    <li>Préparer les rapports techniques et opérationnels.</li>
                                    <li>Concevoir les supports numériques et outils de communication.</li>
                                    <li>Assurer l’assistance technique aux utilisateurs de la plateforme.</li>
                                    <li>Participer à la planification et au suivi des activités stratégiques.</li>
                                </ul>

                                <h5 class="member-section-title"><i class="bi bi-stars"></i> Compétences</h5>
                                <ul class="skills-list">
                                    <li>Développement logiciel</li>
                                    <li>Administration systèmes</li>
                                    <li>Gestion de projets numériques</li>
                                </ul>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <article class="team-member-card">
                            <div class="member-photo">
                                <img src="assets/img/queen.jpeg" class="img-fluid" alt="QUEEN EJIBA">
                            </div>
                            <div class="member-content">
                                <span class="team-badge"><i class="bi bi-megaphone"></i> Communication</span>
                                <h4>QUEEN EJIBA</h4>
                                <span class="member-role">Responsable Communication Digitale et Partenariats</span>
                                <p class="member-description">
                                    Responsable communication digitale et partenariats, elle développe la visibilité d’INFINITIA GROUP SARLU sur les réseaux sociaux, crée les supports de communication et marketing, gère les pages officielles de l’entreprise et participe à la recherche d’opportunités de collaboration et de financement.
                                </p>

                                <h5 class="member-section-title"><i class="bi bi-check2-circle"></i> Missions principales</h5>
                                <ul class="missions-list">
                                    <li>Développer la visibilité de l’entreprise sur les réseaux sociaux.</li>
                                    <li>Créer les supports de communication et de marketing.</li>
                                    <li>Gérer les pages et comptes officiels de l’entreprise.</li>
                                    <li>Élaborer les campagnes de sensibilisation et de promotion.</li>
                                    <li>Rechercher des partenaires stratégiques en ligne.</li>
                                    <li>Identifier les opportunités de collaboration et de financement.</li>
                                    <li>Préparer les présentations institutionnelles de l’entreprise.</li>
                                    <li>Participer à l’organisation des événements promotionnels.</li>
                                    <li>Assurer la veille sur les opportunités de partenariats.</li>
                                </ul>

                                <h5 class="member-section-title"><i class="bi bi-stars"></i> Compétences</h5>
                                <ul class="skills-list">
                                    <li>Communication digitale</li>
                                    <li>Marketing et branding</li>
                                    <li>Partenariats stratégiques</li>
                                </ul>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <article class="team-member-card">
                            <div class="member-photo">
                                <img src="assets/img/sarah.jpeg" class="img-fluid" alt="SARAH BESA">
                            </div>
                            <div class="member-content">
                                <span class="team-badge"><i class="bi bi-briefcase"></i> Commercial</span>
                                <h4>SARAH BESA</h4>
                                <span class="member-role">Responsable Développement Commercial et Relations Clients</span>
                                <p class="member-description">
                                    Responsable du développement commercial et des relations clients, elle assure la prospection des ménages, entreprises et clients potentiels, développe le portefeuille clients, suit les prospects, présente les services de l’entreprise aux partenaires et contribue aux campagnes de promotion de la plateforme.
                                </p>

                                <h5 class="member-section-title"><i class="bi bi-check2-circle"></i> Missions principales</h5>
                                <ul class="missions-list">
                                    <li>Prospecter les ménages, entreprises et clients potentiels.</li>
                                    <li>Développer le portefeuille clients de l’entreprise.</li>
                                    <li>Assurer le suivi des prospects et des clients.</li>
                                    <li>Participer aux incubateurs, concours et programmes entrepreneuriaux.</li>
                                    <li>Présenter les services de l’entreprise aux partenaires potentiels.</li>
                                    <li>Contribuer à la collecte des besoins des clients.</li>
                                    <li>Participer aux campagnes de promotion de la plateforme.</li>
                                    <li>Produire les rapports de prospection et de développement commercial.</li>
                                    <li>Assurer la satisfaction et la fidélisation des clients.</li>
                                </ul>

                                <h5 class="member-section-title"><i class="bi bi-stars"></i> Compétences</h5>
                                <ul class="skills-list">
                                    <li>Prospection commerciale</li>
                                    <li>Relations clients</li>
                                    <li>Développement des affaires</li>
                                </ul>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="team-cta">
            <div class="container" data-aos="fade-up">
                <div class="cta-box">
                    <h2>Vous souhaitez collaborer avec notre équipe ?</h2>
                    <a href="contact.php" class="btn btn-primary">Contactez-nous</a>
                </div>
            </div>
        </section>
    </main>

    <?php include("pied.php"); ?>
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
