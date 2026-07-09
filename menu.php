<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="container position-relative d-flex align-items-center justify-content-between">

    <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">

    <img src="assets/img/brand.png"
         alt="INFINITIA GROUP SARLU"
         class="img-fluid">
</a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Accueil</a></li>
        <li><a href="a-propos.php" class="<?php echo ($current_page == 'a-propos.php') ? 'active' : ''; ?>">A propos</a></li>
        <li><a href="services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a></li>
        <li><a href="solutions.php" class="<?php echo ($current_page == 'solutions.php') ? 'active' : ''; ?>">Solutions</a></li>
        <li><a href="projets.php" class="<?php echo ($current_page == 'projets.php') ? 'active' : ''; ?>">Projets</a></li>
        <li><a href="equipe.php" class="<?php echo ($current_page == 'equipe.php') ? 'active' : ''; ?>">Equipe</a></li>
        <li><a href="faq.php" class="<?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>">FAQ</a></li>
        <li><a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
      </ul>

      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <div class="header-social-links">
      <a href="contact.php" class="twitter"><i class="bi bi-twitter-x"></i></a>
      <a href="contact.php" class="facebook"><i class="bi bi-facebook"></i></a>
      <a href="contact.php" class="instagram"><i class="bi bi-instagram"></i></a>
      <a href="contact.php" class="linkedin"><i class="bi bi-linkedin"></i></a>
    </div>

  </div>
</header>