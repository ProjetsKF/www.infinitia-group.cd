<?php
require_once __DIR__ . '/config/app.php';

if (!isset($currentPage)) {
  $currentPage = '';
}

function infinitiaMenuClass($page, $currentPage)
{
  return $page === $currentPage ? ' class="active" aria-current="page"' : '';
}

function infinitiaMenuParentClass($pages, $currentPage)
{
  return in_array($currentPage, $pages, true) ? ' class="active"' : '';
}

$entreprisePages = array(
  'a-propos',
  'equipe',
  'faq',
  'privacy'
);

$servicesPages = array(
  'services',
  'applications-web',
  'applications-mobiles',
  'intelligence-artificielle',
  'logistique-digitale',
  'agriculture-digitale'
);

$projetsPages = array(
  'projets',
  'projet-care-services',
  'projet-ruralher-impact',
  'projet-agrichain',
  'portfolio-details'
);
?>

<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="container position-relative d-flex align-items-center justify-content-between">

    <a href="<?php echo infinitia_url_html('/'); ?>" class="logo d-flex align-items-center me-auto me-xl-0">

    <img src="assets/img/brand.png"
         alt="INFINITIA GROUP SARLU"
         class="img-fluid">
</a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="<?php echo infinitia_url_html('/'); ?>"<?php echo infinitiaMenuClass('accueil', $currentPage); ?>>Accueil</a></li>
        <li class="dropdown">
          <a href="<?php echo infinitia_url_html('/a-propos'); ?>"<?php echo infinitiaMenuParentClass($entreprisePages, $currentPage); ?>><span>Entreprise</span> <i class="bi bi-chevron-down toggle-dropdown" role="button" tabindex="0" aria-label="Ouvrir le sous-menu Entreprise"></i></a>
          <ul>
            <li><a href="<?php echo infinitia_url_html('/a-propos'); ?>"<?php echo infinitiaMenuClass('a-propos', $currentPage); ?>>A propos</a></li>
            <li><a href="<?php echo infinitia_url_html('/equipe'); ?>"<?php echo infinitiaMenuClass('equipe', $currentPage); ?>>Equipe</a></li>
            <li><a href="<?php echo infinitia_url_html('/faq'); ?>"<?php echo infinitiaMenuClass('faq', $currentPage); ?>>FAQ</a></li>
            <li><a href="<?php echo infinitia_url_html('/privacy'); ?>"<?php echo infinitiaMenuClass('privacy', $currentPage); ?>>Politique de confidentialite</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="<?php echo infinitia_url_html('/services'); ?>"<?php echo infinitiaMenuParentClass($servicesPages, $currentPage); ?>><span>Services</span> <i class="bi bi-chevron-down toggle-dropdown" role="button" tabindex="0" aria-label="Ouvrir le sous-menu Services"></i></a>
          <ul>
            <li><a href="<?php echo infinitia_url_html('/services'); ?>"<?php echo infinitiaMenuClass('services', $currentPage); ?>>Services</a></li>
            <li><a href="<?php echo infinitia_url_html('/applications-web'); ?>"<?php echo infinitiaMenuClass('applications-web', $currentPage); ?>>Applications web</a></li>
            <li><a href="<?php echo infinitia_url_html('/applications-mobiles'); ?>"<?php echo infinitiaMenuClass('applications-mobiles', $currentPage); ?>>Applications mobiles</a></li>
            <li><a href="<?php echo infinitia_url_html('/intelligence-artificielle'); ?>"<?php echo infinitiaMenuClass('intelligence-artificielle', $currentPage); ?>>Intelligence artificielle</a></li>
            <li><a href="<?php echo infinitia_url_html('/logistique-digitale'); ?>"<?php echo infinitiaMenuClass('logistique-digitale', $currentPage); ?>>Logistique digitale</a></li>
            <li><a href="<?php echo infinitia_url_html('/agriculture-digitale'); ?>"<?php echo infinitiaMenuClass('agriculture-digitale', $currentPage); ?>>Agriculture digitale</a></li>
          </ul>
        </li>
        <li><a href="<?php echo infinitia_url_html('/formations'); ?>"<?php echo infinitiaMenuClass('formations', $currentPage); ?>>Formations</a></li>
        <li><a href="<?php echo infinitia_url_html('/solutions'); ?>"<?php echo infinitiaMenuClass('solutions', $currentPage); ?>>Solutions</a></li>
        <li class="dropdown">
          <a href="<?php echo infinitia_url_html('/projets'); ?>"<?php echo infinitiaMenuParentClass($projetsPages, $currentPage); ?>><span>Projets</span> <i class="bi bi-chevron-down toggle-dropdown" role="button" tabindex="0" aria-label="Ouvrir le sous-menu Projets"></i></a>
          <ul>
            <li><a href="<?php echo infinitia_url_html('/projets'); ?>"<?php echo infinitiaMenuClass('projets', $currentPage); ?>>Projets</a></li>
            <li><a href="<?php echo infinitia_url_html('/projet-care-services'); ?>"<?php echo infinitiaMenuClass('projet-care-services', $currentPage); ?>>INFINITIA Care Services</a></li>
            <li><a href="<?php echo infinitia_url_html('/projet-ruralher-impact'); ?>"<?php echo infinitiaMenuClass('projet-ruralher-impact', $currentPage); ?>>RuralHer Impact</a></li>
            <li><a href="<?php echo infinitia_url_html('/projet-agrichain'); ?>"<?php echo infinitiaMenuClass('projet-agrichain', $currentPage); ?>>INFINITIA AgriChain</a></li>
          </ul>
        </li>
        <li><a href="<?php echo infinitia_url_html('/contact'); ?>"<?php echo infinitiaMenuClass('contact', $currentPage); ?>>Contact</a></li>
      </ul>

      <i class="mobile-nav-toggle d-xl-none bi bi-list" role="button" tabindex="0" aria-label="Ouvrir le menu"></i>
    </nav>

    <div class="header-social-links">
      <a href="<?php echo infinitia_url_html('/contact'); ?>" class="twitter"><i class="bi bi-twitter-x"></i></a>
      <a href="<?php echo infinitia_url_html('/contact'); ?>" class="facebook"><i class="bi bi-facebook"></i></a>
      <a href="<?php echo infinitia_url_html('/contact'); ?>" class="instagram"><i class="bi bi-instagram"></i></a>
      <a href="<?php echo infinitia_url_html('/contact'); ?>" class="linkedin"><i class="bi bi-linkedin"></i></a>
    </div>

  </div>
</header>
