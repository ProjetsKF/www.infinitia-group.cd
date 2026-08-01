<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$currentPage = 'contact';
$contact_success = '';
$contact_error = '';

if (isset($_SESSION['contact_success'])) {
  $contact_success = $_SESSION['contact_success'];
  unset($_SESSION['contact_success']);
}

if (isset($_SESSION['contact_error'])) {
  $contact_error = $_SESSION['contact_error'];
  unset($_SESSION['contact_error']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Contact - INFINITIA GROUP SARLU</title>
  <meta name="description" content="Contactez INFINITIA GROUP SARLU pour votre projet numerique.">
  <meta name="keywords" content="contact, devis, projet numerique">
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
<body class="contact-page">
  <?php include("menu.php"); ?>
  <br>  <br>  <br>

  <main class="main">
    <section id="contact" class="contact section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Parlez-nous de votre projet de transformation numerique.</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-5">
          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="150">
            <div class="main-contact-wrapper">
              <div class="row gy-4">
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                  <div class="info-box">
                    <div class="icon-wrap"><i class="bi bi-envelope-heart"></i></div>
                    <h5>Ecrivez-nous</h5>
                    <p>info@infinitia-group.cd</p>
                    <span class="availability">Reponse selon la priorite du projet</span>
                  </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="250">
                  <div class="info-box">
                    <div class="icon-wrap"><i class="bi bi-phone-vibrate"></i></div>
                    <h5>Planifions un appel</h5>
                    <p>Disponible sur demande</p>
                    <span class="availability">Analyse initiale et cadrage</span>
                  </div>
                </div>

                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                  <div class="info-box">
                    <div class="icon-wrap"><i class="bi bi-pin-map"></i></div>
                    <h5>Localisation</h5>
                    <p>Kolwezi, RDC</p>
                    <span class="availability">Interventions locales et a distance</span>
                  </div>
                </div>
              </div>

              <div class="form-section" data-aos="fade-up" data-aos-delay="350">
                <div class="form-intro">
                  <h3>Decrivez votre besoin</h3>
                  <p>Expliquez votre activite, vos objectifs et les operations que vous souhaitez digitaliser.</p>
                </div>

                <?php if ($contact_success != '') { ?>
                  <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($contact_success, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php } ?>

                <?php if ($contact_error != '') { ?>
                  <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($contact_error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php } ?>

                <form action="mail.php" method="post" class="contact-form">
                  <div class="row gy-3">
                    <div class="col-md-4">
                      <div class="input-group-custom">
                        <i class="bi bi-person"></i>
                        <input type="text" name="name" class="form-control" placeholder="Nom complet" required="" autocomplete="name">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="input-group-custom">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Adresse e-mail" required="" autocomplete="email">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="input-group-custom">
                        <i class="bi bi-tag"></i>
                        <input type="text" name="subject" class="form-control" placeholder="Sujet" required="">
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="input-group-custom textarea-group">
                        <i class="bi bi-chat-text"></i>
                        <textarea name="message" class="form-control" rows="5" placeholder="Decrivez votre projet ou votre demande..." required=""></textarea>
                      </div>
                    </div>
                  </div>

                  <div class="form-footer">
                    <button type="submit" class="btn-submit">
                      <i class="bi bi-rocket-takeoff"></i>
                      <span>Envoyer le message</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-4" data-aos="fade-left" data-aos-delay="200">
            <div class="sidebar-panel">
              <div class="panel-header">
                <div class="header-badge"><i class="bi bi-lightning-charge-fill"></i></div>
                <h4>Pourquoi nous contacter ?</h4>
              </div>
              <div class="metrics-grid">
                <div class="metric-item"><div class="metric-value">01</div><div class="metric-desc">Cadrage clair</div></div>
                <div class="metric-item"><div class="metric-value">02</div><div class="metric-desc">Solution adaptee</div></div>
                <div class="metric-item"><div class="metric-value">03</div><div class="metric-desc">Execution suivie</div></div>
                <div class="metric-item"><div class="metric-value">04</div><div class="metric-desc">Impact mesurable</div></div>
              </div>
              <div class="testimonial-mini" data-aos="fade-up" data-aos-delay="450">
                <div class="quote-icon"><i class="bi bi-quote"></i></div>
                <p>Une bonne solution numerique commence par une comprehension precise du terrain et des objectifs.</p>
                <div class="client-info">
                  <img src="assets/img/laeti.jpeg"alt="LAETITIA MPALABA - Directrice Générale (CEO)" class="client-avatar">
                  <div class="client-details">
                    <span class="client-name">INFINITIA GROUP SARLU</span>
                    <span class="client-role">Equipe conseil</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
