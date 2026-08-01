<?php
require_once 'config/database.php';

$currentPage = 'desabonnement';
$message = '';
$message_type = 'danger';
$token = '';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
}

if ($token === '' || strlen($token) !== 64 || !ctype_xdigit($token)) {
    $message = 'Lien de désabonnement invalide.';
} elseif (!$conn) {
    $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
} else {
    $select_sql = 'SELECT id, email, statut FROM abonnes_newsletter WHERE token_desabonnement = ? LIMIT 1';
    $select_stmt = mysqli_prepare($conn, $select_sql);

    if (!$select_stmt) {
        $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
    } else {
        mysqli_stmt_bind_param($select_stmt, 's', $token);

        if (!mysqli_stmt_execute($select_stmt)) {
            $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
        } else {
            mysqli_stmt_bind_result($select_stmt, $id, $email, $statut);
            $subscriber_found = mysqli_stmt_fetch($select_stmt);
            mysqli_stmt_close($select_stmt);
            $select_stmt = false;

            if (!$subscriber_found) {
                $message = 'Lien de désabonnement invalide ou expiré.';
            } elseif ($statut === 'desabonne') {
                $message = 'Vous êtes déjà désabonné de notre newsletter.';
                $message_type = 'info';
            } else {
                $update_sql = "UPDATE abonnes_newsletter SET statut = 'desabonne' WHERE token_desabonnement = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);

                if (!$update_stmt) {
                    $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
                } else {
                    mysqli_stmt_bind_param($update_stmt, 's', $token);

                    if (!mysqli_stmt_execute($update_stmt)) {
                        $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
                    } else {
                        $message = 'Vous avez été désabonné avec succès de la newsletter d’INFINITIA GROUP SARLU.';
                        $message_type = 'success';
                    }

                    mysqli_stmt_close($update_stmt);
                }
            }
        }

        if ($select_stmt) {
            mysqli_stmt_close($select_stmt);
        }
    }
}

/*
 * Chaque newsletter doit contenir un lien de désabonnement de ce type :
 * https://www.infinitia-group.com/desabonnement.php?token=TOKEN_DE_L_ABONNE
 *
 * Requête à utiliser plus tard pour envoyer uniquement aux abonnés actifs :
 * SELECT email, token_desabonnement
 * FROM abonnes_newsletter
 * WHERE statut = 'actif'
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Désabonnement Newsletter | INFINITIA GROUP SARLU</title>
    <meta name="description" content="Désabonnement à la newsletter d'INFINITIA GROUP SARLU.">
    <meta name="keywords" content="désabonnement, newsletter, INFINITIA GROUP">

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
    <br><br><br>

    <main class="main">
        <section class="section">
            <div class="container" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center p-4 p-md-5 shadow-sm rounded bg-white">
                            <div class="mb-4">
                                <i class="bi bi-envelope-check fs-1 text-primary"></i>
                            </div>
                            <h1 class="mb-3">Désabonnement newsletter</h1>
                            <div class="alert alert-<?php echo htmlspecialchars($message_type, ENT_QUOTES, 'UTF-8'); ?>" role="alert">
                                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                                <a href="index.php" class="btn btn-primary">Retour à l’accueil</a>
                                <a href="index.php" class="btn btn-outline-primary">Se réinscrire</a>
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
