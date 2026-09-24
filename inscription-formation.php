<?php
$currentPage = 'formations';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'training_functions.php';

infinitia_start_session();
infinitia_initialize_training_catalog($conn);

$errors = array();
$success_message = '';
$session_record = null;
$session_id = 0;
$form = array(
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'current_level' => '',
    'availability' => '',
    'message' => ''
);

if (isset($_SESSION['training_registration_success'])) {
    $success_message = $_SESSION['training_registration_success'];
    unset($_SESSION['training_registration_success']);
}

if (infinitia_request_method() === 'POST') {
    $session_id = infinitia_valid_id(infinitia_post_value('session_id')) ? (int) infinitia_post_value('session_id') : 0;
    foreach ($form as $key => $value) {
        $form[$key] = infinitia_post_value($key);
    }

    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token'))) {
        $errors[] = 'La session du formulaire a expiré. Veuillez réessayer.';
    }
    if (infinitia_post_value('website') !== '') {
        $errors[] = 'La demande n’a pas pu être enregistrée.';
    }
    if ($session_id <= 0) {
        $errors[] = 'La session sélectionnée est invalide.';
    }
    if ($form['full_name'] === '' || infinitia_strlen($form['full_name']) < 2 || infinitia_strlen($form['full_name']) > 150) {
        $errors[] = 'Le nom complet doit contenir entre 2 et 150 caractères.';
    }
    if ($form['phone'] === '' || infinitia_strlen($form['phone']) < 7 || infinitia_strlen($form['phone']) > 50 || !preg_match('/^[0-9+() .\-]+$/', $form['phone'])) {
        $errors[] = 'Le numéro de téléphone ou WhatsApp est invalide.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) || infinitia_strlen($form['email']) > 190) {
        $errors[] = 'L’adresse e-mail est invalide.';
    }
    if ($form['current_level'] === '' || infinitia_strlen($form['current_level']) > 190) {
        $errors[] = 'Veuillez préciser votre niveau actuel (190 caractères maximum).';
    }
    if (infinitia_strlen($form['availability']) > 255) {
        $errors[] = 'La disponibilité ne peut pas dépasser 255 caractères.';
    }
    if (infinitia_strlen($form['message']) > 2000) {
        $errors[] = 'Le message ne peut pas dépasser 2 000 caractères.';
    }

    if (!infinitia_database_ready($conn) || !infinitia_table_exists($conn, 'training_sessions') || !infinitia_table_exists($conn, 'training_registrations')) {
        $errors[] = 'Le service d’inscription est momentanément indisponible.';
    }

    if (count($errors) === 0) {
        mysqli_begin_transaction($conn);
        $lookup_sql = "SELECT s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status,
                             (SELECT COUNT(*) FROM training_registrations r WHERE r.session_id = s.id AND r.status = 'accepted') AS accepted_count
                      FROM training_sessions s WHERE s.id = ? LIMIT 1 FOR UPDATE";
        $lookup = mysqli_prepare($conn, $lookup_sql);
        if ($lookup) {
            mysqli_stmt_bind_param($lookup, 'i', $session_id);
            mysqli_stmt_execute($lookup);
            mysqli_stmt_bind_result($lookup, $db_id, $formation_code, $start_date, $end_date, $schedule, $location, $capacity, $session_status, $accepted_count);
            if (mysqli_stmt_fetch($lookup)) {
                $session_record = array(
                    'id' => $db_id,
                    'formation_code' => $formation_code,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'schedule' => $schedule,
                    'location' => $location,
                    'capacity' => $capacity,
                    'status' => $session_status,
                    'accepted_count' => (int) $accepted_count
                );
            }
            mysqli_stmt_close($lookup);
        }

        if (!$session_record || !infinitia_training_is_active($session_record['formation_code']) || !infinitia_session_is_available($session_record['status'], $session_record['capacity'], $session_record['accepted_count'], $session_record['start_date'], $session_record['end_date'])) {
            mysqli_rollback($conn);
            $errors[] = 'Cette session n’est plus ouverte aux demandes d’inscription.';
        } else {
            $now = date('Y-m-d H:i:s');
            $insert_sql = "INSERT INTO training_registrations
                (session_id, formation_code, full_name, phone, email, current_level, availability, message, status, submitted_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', ?, ?)";
            $insert = mysqli_prepare($conn, $insert_sql);
            if ($insert) {
                mysqli_stmt_bind_param(
                    $insert,
                    'isssssssss',
                    $session_id,
                    $session_record['formation_code'],
                    $form['full_name'],
                    $form['phone'],
                    $form['email'],
                    $form['current_level'],
                    $form['availability'],
                    $form['message'],
                    $now,
                    $now
                );
                $saved = mysqli_stmt_execute($insert);
                $registration_id = $saved ? mysqli_insert_id($conn) : 0;
                mysqli_stmt_close($insert);
            } else {
                $saved = false;
                $registration_id = 0;
            }

            if ($saved) {
                mysqli_commit($conn);
                $_SESSION['training_registration_success'] = 'Votre demande d’inscription a bien été enregistrée sous la référence #' . $registration_id . '. Notre équipe vous contactera après traitement de votre demande.';
                header('Location: inscription-formation.php?success=1');
                exit;
            }

            mysqli_rollback($conn);
            $errors[] = 'Une erreur technique a empêché l’enregistrement. Veuillez réessayer.';
        }
    }
}

if ($session_record === null && $session_id === 0 && isset($_GET['session']) && infinitia_valid_id($_GET['session'])) {
    $session_id = (int) $_GET['session'];
}

if ($success_message === '' && $session_record === null && $session_id > 0 && infinitia_database_ready($conn) && infinitia_table_exists($conn, 'training_sessions') && infinitia_table_exists($conn, 'training_registrations')) {
    $sql = "SELECT s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status,
                   (SELECT COUNT(*) FROM training_registrations r WHERE r.session_id = s.id AND r.status = 'accepted') AS accepted_count
            FROM training_sessions s WHERE s.id = ? LIMIT 1";
    $statement = mysqli_prepare($conn, $sql);
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $session_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $db_id, $formation_code, $start_date, $end_date, $schedule, $location, $capacity, $session_status, $accepted_count);
        if (mysqli_stmt_fetch($statement)) {
            $candidate = array('id' => $db_id, 'formation_code' => $formation_code, 'start_date' => $start_date, 'end_date' => $end_date, 'schedule' => $schedule, 'location' => $location, 'capacity' => $capacity, 'status' => $session_status, 'accepted_count' => (int) $accepted_count);
            if (infinitia_training_is_active($formation_code) && infinitia_session_is_available($session_status, $capacity, $accepted_count, $start_date, $end_date)) {
                $session_record = $candidate;
            }
        }
        mysqli_stmt_close($statement);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Demande d’inscription à une formation - INFINITIA GROUP SARLU</title>
    <meta name="description" content="Soumettez une demande d’inscription à une session de formation professionnelle INFINITIA GROUP SARLU.">
    <link href="assets/img/ico.ico" rel="icon">
    <link href="assets/img/ico.ico" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Lato:wght@300;400;700;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="formations-page training-registration-page">
    <?php include("menu.php"); ?>
    <main class="main">
        <section class="training-hero dark-background">
            <div class="container text-center">
                <span class="training-kicker"><i class="bi bi-person-check"></i> Demande d’inscription</span>
                <h1>Inscription à une formation</h1>
                <p>Envoyez votre demande pour la session choisie. Elle sera examinée par notre équipe avant toute admission définitive.</p>
            </div>
        </section>

        <section class="section light-background">
            <div class="container">
                <?php if ($success_message !== '') { ?>
                    <div class="training-form-panel text-center">
                        <div class="training-confirmation-icon"><i class="bi bi-check2-circle"></i></div>
                        <h2>Demande enregistrée</h2>
                        <div class="alert alert-success" role="status"><?php echo infinitia_h($success_message); ?></div>
                        <p>Cette confirmation concerne la réception de votre demande et ne constitue pas encore une admission définitive.</p>
                        <a href="formations.php" class="training-register-link">Retour aux formations</a>
                    </div>
                <?php } elseif (!$session_record) { ?>
                    <div class="training-form-panel text-center">
                        <div class="training-confirmation-icon"><i class="bi bi-calendar-x"></i></div>
                        <h2>Session indisponible</h2>
                        <p>La session demandée n’existe pas, n’est pas ouverte ou ne dispose plus de places disponibles.</p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="formations.php#nos-formations" class="training-register-link">Voir les formations</a>
                            <a href="contact.php" class="btn btn-outline-primary">Nous contacter</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <aside class="training-session-summary">
                                <span class="training-category">Session sélectionnée</span>
                                <h2><?php echo infinitia_h(infinitia_training_name($session_record['formation_code'])); ?></h2>
                                <ul>
                                    <li><i class="bi bi-calendar3"></i><span><?php echo infinitia_h(infinitia_format_date_range($session_record['start_date'], $session_record['end_date'])); ?></span></li>
                                    <?php if ($session_record['schedule'] !== null && $session_record['schedule'] !== '') { ?><li><i class="bi bi-clock"></i><span><?php echo infinitia_h($session_record['schedule']); ?></span></li><?php } ?>
                                    <?php if ($session_record['location'] !== null && $session_record['location'] !== '') { ?><li><i class="bi bi-geo-alt"></i><span><?php echo infinitia_h($session_record['location']); ?></span></li><?php } ?>
                                </ul>
                            </aside>
                        </div>
                        <div class="col-lg-8">
                            <div class="training-form-panel">
                                <h2>Vos informations</h2>
                                <p>Les champs marqués d’un astérisque sont obligatoires.</p>
                                <?php if (count($errors) > 0) { ?>
                                    <div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ($errors as $error) { ?><li><?php echo infinitia_h($error); ?></li><?php } ?></ul></div>
                                <?php } ?>
                                <form method="post" action="inscription-formation.php?session=<?php echo (int) $session_record['id']; ?>" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>">
                                    <input type="hidden" name="session_id" value="<?php echo (int) $session_record['id']; ?>">
                                    <div class="training-honeypot" aria-hidden="true"><label for="website">Site web</label><input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></div>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label for="full_name" class="form-label">Nom complet *</label><input type="text" class="form-control" id="full_name" name="full_name" maxlength="150" required value="<?php echo infinitia_h($form['full_name']); ?>" autocomplete="name"></div>
                                        <div class="col-md-6"><label for="phone" class="form-label">Téléphone / WhatsApp *</label><input type="tel" class="form-control" id="phone" name="phone" maxlength="50" required value="<?php echo infinitia_h($form['phone']); ?>" autocomplete="tel"></div>
                                        <div class="col-md-6"><label for="email" class="form-label">Adresse e-mail *</label><input type="email" class="form-control" id="email" name="email" maxlength="190" required value="<?php echo infinitia_h($form['email']); ?>" autocomplete="email"></div>
                                        <div class="col-md-6"><label for="current_level" class="form-label">Niveau actuel *</label><input type="text" class="form-control" id="current_level" name="current_level" maxlength="190" required value="<?php echo infinitia_h($form['current_level']); ?>"></div>
                                        <div class="col-12"><label for="availability" class="form-label">Disponibilité</label><input type="text" class="form-control" id="availability" name="availability" maxlength="255" value="<?php echo infinitia_h($form['availability']); ?>"></div>
                                        <div class="col-12"><label for="message" class="form-label">Message ou informations complémentaires</label><textarea class="form-control" id="message" name="message" rows="5" maxlength="2000"><?php echo infinitia_h($form['message']); ?></textarea></div>
                                        <div class="col-12"><button type="submit" class="training-register-link border-0">Envoyer ma demande <i class="bi bi-send"></i></button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>
    <?php include("pied.php"); ?>
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Retour en haut"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
