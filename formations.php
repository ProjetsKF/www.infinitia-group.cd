<?php
$currentPage = 'formations';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'training_functions.php';

infinitia_initialize_training_catalog($conn);
$training_courses = infinitia_get_training_courses($conn, true);
$open_training_sessions = infinitia_get_open_training_sessions($conn);
$available_training_sessions = infinitia_filter_available_sessions($open_training_sessions);
$sessions_by_training = infinitia_group_sessions_by_training($open_training_sessions);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Formations professionnelles - INFINITIA GROUP SARLU</title>
    <meta name="description" content="Découvrez les formations professionnelles en informatique, réseaux, cybersécurité, bases de données, systèmes et vidéosurveillance proposées par INFINITIA GROUP SARLU.">
    <meta name="keywords" content="formations professionnelles, informatique, bureautique, réseaux, CCNA, cybersécurité, bases de données, Windows Server, vidéosurveillance, Kolwezi">

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
</head>

<body class="formations-page">
    <?php include("menu.php"); ?>

    <main class="main">
        <section class="training-hero dark-background" aria-labelledby="training-page-title">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-10">
                        <span class="training-kicker"><i class="bi bi-mortarboard"></i> Compétences numériques et techniques</span>
                        <h1 id="training-page-title">Formations professionnelles</h1>
                        <p>Développez vos compétences numériques et techniques avec des formations pratiques adaptées aux réalités professionnelles.</p>
                        <a href="#nos-formations" class="training-primary-link">Découvrir les formations <i class="bi bi-arrow-down"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section training-about light-background" aria-labelledby="centre-formation-title">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5" data-aos="fade-up">
                        <div class="training-about-icon" aria-hidden="true"><i class="bi bi-person-workspace"></i></div>
                        <h2 id="centre-formation-title">Notre centre de formation</h2>
                    </div>
                    <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
                        <p class="lead">Notre centre propose des formations professionnelles en informatique ainsi que des services de consultance informatique, afin d’accompagner le développement des compétences des particuliers, des professionnels et des organisations.</p>
                        <p>Les parcours sont adaptés aux besoins des apprenants et des structures. Ils couvrent des domaines allant de l’initiation informatique aux réseaux, aux systèmes, aux bases de données et à la cybersécurité, avec une approche pratique orientée vers les compétences professionnelles.</p>
                        <div class="row g-3 mt-2">
                            <div class="col-sm-6"><div class="training-feature"><i class="bi bi-check2-circle"></i><span>Accompagnement des compétences</span></div></div>
                            <div class="col-sm-6"><div class="training-feature"><i class="bi bi-check2-circle"></i><span>Parcours adaptés aux besoins</span></div></div>
                            <div class="col-sm-6"><div class="training-feature"><i class="bi bi-check2-circle"></i><span>Apprentissage pratique</span></div></div>
                            <div class="col-sm-6"><div class="training-feature"><i class="bi bi-check2-circle"></i><span>Consultance informatique</span></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="nos-formations" class="section training-catalog" aria-labelledby="formations-title">
            <div class="container section-title" data-aos="fade-up">
                <h2 id="formations-title">Nos formations</h2>
                <p>Choisissez un parcours adapté à votre objectif professionnel.</p>
            </div>

            <div class="container">
                <div class="row g-4">
                    <?php foreach ($training_courses as $index => $course) { ?>
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 50; ?>">
                            <article class="training-card">
                                <div class="training-card-top"><span class="training-category"><?php echo infinitia_h($course['category']); ?></span><i class="bi <?php echo infinitia_h(infinitia_training_category_icon($course['category'])); ?>"></i></div>
                                <h3><?php echo infinitia_h($course['name']); ?></h3>
                                <p><?php echo infinitia_h($course['short_description']); ?></p>
                                <div class="training-meta"><span><i class="bi bi-clock"></i><strong>Durée</strong> <?php echo infinitia_h($course['duration']); ?></span><span><i class="bi bi-cash-stack"></i><strong>Tarif</strong> <?php echo infinitia_h(infinitia_format_training_price($course['price'], $course['price_unit'])); ?></span></div>
                                <details class="training-details"><summary>Voir les détails <i class="bi bi-chevron-down"></i></summary><p><?php echo nl2br(infinitia_h($course['detailed_content'])); ?></p></details>
                                <?php echo infinitia_training_action_html($course['code'], $sessions_by_training); ?>
                            </article>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <section id="prochaines-sessions" class="section light-background training-upcoming" aria-labelledby="upcoming-title">
            <div class="container section-title" data-aos="fade-up">
                <h2 id="upcoming-title">Prochaines sessions</h2>
                <p>Les sessions ouvertes et actuellement disponibles.</p>
            </div>
            <div class="container">
                <?php if (count($available_training_sessions) === 0) { ?>
                    <div class="training-empty-state" data-aos="fade-up">
                        <i class="bi bi-calendar2-event" aria-hidden="true"></i>
                        <p>Les prochaines sessions seront annoncées prochainement.</p>
                    </div>
                <?php } else { ?>
                    <div class="row g-4">
                        <?php foreach ($available_training_sessions as $session) { ?>
                            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                                <article class="training-session-card">
                                    <span class="training-category">Session ouverte</span>
                                    <h3><?php echo infinitia_h(infinitia_training_name($session['formation_code'])); ?></h3>
                                    <ul>
                                        <li><i class="bi bi-calendar3"></i><span><?php echo infinitia_h(infinitia_format_date_range($session['start_date'], $session['end_date'])); ?></span></li>
                                        <?php if ($session['schedule'] !== null && $session['schedule'] !== '') { ?><li><i class="bi bi-clock"></i><span><?php echo infinitia_h($session['schedule']); ?></span></li><?php } ?>
                                        <?php if ($session['location'] !== null && $session['location'] !== '') { ?><li><i class="bi bi-geo-alt"></i><span><?php echo infinitia_h($session['location']); ?></span></li><?php } ?>
                                        <li><i class="bi bi-people"></i><span><?php $remaining_places = infinitia_remaining_places($session['capacity'], $session['accepted_count']); echo $remaining_places === null ? 'Places disponibles' : infinitia_h(infinitia_remaining_places_label($remaining_places)); ?></span></li>
                                    </ul>
                                    <a class="training-register-link" href="inscription-formation.php?session=<?php echo (int) $session['id']; ?>">S’inscrire <i class="bi bi-arrow-right"></i></a>
                                </article>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section class="section training-organizations" aria-labelledby="organizations-title">
            <div class="container" data-aos="fade-up">
                <div class="training-organization-panel">
                    <div>
                        <span class="training-kicker"><i class="bi bi-buildings"></i> Solutions sur mesure</span>
                        <h2 id="organizations-title">Formations pour entreprises et organisations</h2>
                        <p>Nous accompagnons également les entreprises, institutions et organisations dans le renforcement des compétences de leurs équipes à travers des programmes de formation adaptés à leurs besoins.</p>
                    </div>
                    <a href="contact.php" class="training-primary-link flex-shrink-0">Nous contacter <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </section>

        <section class="section training-final-cta dark-background" aria-labelledby="training-cta-title">
            <div class="container text-center" data-aos="fade-up">
                <h2 id="training-cta-title">Prêt à développer vos compétences&nbsp;?</h2>
                <p>Découvrez le parcours qui répond à vos besoins et contactez-nous pour obtenir davantage d’informations.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="#nos-formations" class="training-primary-link">Voir les formations <i class="bi bi-arrow-up"></i></a>
                    <a href="contact.php" class="training-secondary-link">Demander des informations <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </section>
    </main>

    <?php include("pied.php"); ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Retour en haut"><i class="bi bi-arrow-up-short"></i></a>
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
