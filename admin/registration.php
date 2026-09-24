<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
infinitia_admin_require_login();

if (!infinitia_admin_database_ready($conn) || !isset($_GET['id']) || !infinitia_valid_id($_GET['id'])) {
    header('Location: registrations.php');
    exit;
}

$registration_id = (int) $_GET['id'];
$statuses = infinitia_registration_statuses();
$transitions = infinitia_registration_transitions();
$errors = array();

function infinitia_load_registration($conn, $registration_id)
{
    $sql = 'SELECT r.id, r.session_id, r.formation_code, r.full_name, r.phone, r.email, r.current_level, r.availability, r.message, r.status, r.submitted_at, r.updated_at, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status FROM training_registrations r INNER JOIN training_sessions s ON s.id = r.session_id WHERE r.id = ? LIMIT 1';
    $statement = mysqli_prepare($conn, $sql);
    if (!$statement) {
        return null;
    }
    mysqli_stmt_bind_param($statement, 'i', $registration_id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $id, $session_id, $formation_code, $full_name, $phone, $email, $current_level, $availability, $message, $status, $submitted_at, $updated_at, $start_date, $end_date, $schedule, $location, $capacity, $session_status);
    $record = null;
    if (mysqli_stmt_fetch($statement)) {
        $record = array('id' => $id, 'session_id' => $session_id, 'formation_code' => $formation_code, 'full_name' => $full_name, 'phone' => $phone, 'email' => $email, 'current_level' => $current_level, 'availability' => $availability, 'message' => $message, 'status' => $status, 'submitted_at' => $submitted_at, 'updated_at' => $updated_at, 'start_date' => $start_date, 'end_date' => $end_date, 'schedule' => $schedule, 'location' => $location, 'capacity' => $capacity, 'session_status' => $session_status);
    }
    mysqli_stmt_close($statement);
    return $record;
}

$registration = infinitia_load_registration($conn, $registration_id);
if (!$registration) {
    header('Location: registrations.php');
    exit;
}

if (infinitia_request_method() === 'POST') {
    $new_status = infinitia_post_value('status');
    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token'))) {
        $errors[] = 'La session du formulaire a expiré.';
    } elseif (!isset($statuses[$new_status])) {
        $errors[] = 'Le statut demandé est invalide.';
    } elseif (!isset($transitions[$registration['status']]) || !in_array($new_status, $transitions[$registration['status']], true)) {
        $errors[] = 'Ce changement de statut n’est pas autorisé.';
    } else {
        mysqli_begin_transaction($conn);
        $lock = mysqli_prepare($conn, 'SELECT capacity, status FROM training_sessions WHERE id = ? LIMIT 1 FOR UPDATE');
        if ($lock) {
            mysqli_stmt_bind_param($lock, 'i', $registration['session_id']);
            mysqli_stmt_execute($lock);
            mysqli_stmt_bind_result($lock, $capacity, $session_status);
            $session_found = mysqli_stmt_fetch($lock);
            mysqli_stmt_close($lock);
        } else {
            $session_found = false;
        }

        if (!$session_found) {
            $errors[] = 'La session associée est introuvable.';
        } elseif ($new_status === 'accepted') {
            $count_statement = mysqli_prepare($conn, "SELECT COUNT(*) FROM training_registrations WHERE session_id = ? AND status = 'accepted' AND id <> ?");
            mysqli_stmt_bind_param($count_statement, 'ii', $registration['session_id'], $registration_id);
            mysqli_stmt_execute($count_statement);
            mysqli_stmt_bind_result($count_statement, $accepted_count);
            mysqli_stmt_fetch($count_statement);
            mysqli_stmt_close($count_statement);
            if ($capacity !== null && (int) $capacity > 0 && (int) $accepted_count >= (int) $capacity) {
                $errors[] = 'La capacité de cette session est déjà atteinte.';
            }
        }

        if (count($errors) === 0) {
            $now = date('Y-m-d H:i:s');
            $update = mysqli_prepare($conn, 'UPDATE training_registrations SET status = ?, updated_at = ? WHERE id = ? AND status = ?');
            mysqli_stmt_bind_param($update, 'ssis', $new_status, $now, $registration_id, $registration['status']);
            $saved = mysqli_stmt_execute($update) && mysqli_stmt_affected_rows($update) === 1;
            mysqli_stmt_close($update);

            if ($saved && $new_status === 'accepted' && $capacity !== null && (int) $capacity > 0 && ((int) $accepted_count + 1) >= (int) $capacity) {
                $full_status = 'full';
                $session_update = mysqli_prepare($conn, "UPDATE training_sessions SET status = ?, updated_at = ? WHERE id = ? AND status = 'open'");
                mysqli_stmt_bind_param($session_update, 'ssi', $full_status, $now, $registration['session_id']);
                mysqli_stmt_execute($session_update);
                mysqli_stmt_close($session_update);
            }

            if ($saved) {
                mysqli_commit($conn);
                infinitia_admin_flash('success', 'Le statut de la demande a été mis à jour.');
                header('Location: registration.php?id=' . $registration_id);
                exit;
            }
            $errors[] = 'La demande a été modifiée par une autre opération. Rechargez la page.';
        }
        mysqli_rollback($conn);
    }
}

$registration = infinitia_load_registration($conn, $registration_id);
$allowed_next = isset($transitions[$registration['status']]) ? $transitions[$registration['status']] : array();
infinitia_admin_header('Demande #' . $registration_id, 'registrations');
?>
<div class="admin-page-heading"><div><span>Demande #<?php echo $registration_id; ?></span><h1><?php echo infinitia_h($registration['full_name']); ?></h1></div><a href="registrations.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a></div>
<?php if (count($errors) > 0) { ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error) { ?><li><?php echo infinitia_h($error); ?></li><?php } ?></ul></div><?php } ?>
<div class="row g-4">
    <div class="col-lg-8"><div class="admin-panel h-100"><h2>Informations du candidat</h2><div class="row g-4">
        <div class="col-md-6"><strong>Nom complet</strong><p><?php echo infinitia_h($registration['full_name']); ?></p></div>
        <div class="col-md-6"><strong>Date de demande</strong><p><?php echo infinitia_h(date('d/m/Y H:i', strtotime($registration['submitted_at']))); ?></p></div>
        <div class="col-md-6"><strong>Téléphone / WhatsApp</strong><p><?php echo infinitia_h($registration['phone']); ?></p></div>
        <div class="col-md-6"><strong>E-mail</strong><p><?php echo infinitia_h($registration['email']); ?></p></div>
        <div class="col-md-6"><strong>Niveau actuel</strong><p><?php echo infinitia_h($registration['current_level']); ?></p></div>
        <div class="col-md-6"><strong>Disponibilité</strong><p><?php echo infinitia_h($registration['availability'] !== null && $registration['availability'] !== '' ? $registration['availability'] : 'Non précisée'); ?></p></div>
        <div class="col-12"><strong>Message</strong><p class="text-break"><?php echo nl2br(infinitia_h($registration['message'] !== null && $registration['message'] !== '' ? $registration['message'] : 'Aucun message.')); ?></p></div>
    </div></div></div>
    <div class="col-lg-4">
        <div class="admin-panel mb-4"><h2>Formation</h2><p><strong><?php echo infinitia_h(infinitia_training_name($registration['formation_code'])); ?></strong></p><p>Session #<?php echo (int) $registration['session_id']; ?><br><?php echo infinitia_h(infinitia_format_date_range($registration['start_date'], $registration['end_date'])); ?><br><?php echo infinitia_h($registration['location'] !== null && $registration['location'] !== '' ? $registration['location'] : 'Lieu à préciser'); ?></p></div>
        <div class="admin-panel"><h2>Traitement</h2><p>Statut actuel : <span class="admin-badge"><?php echo infinitia_h($statuses[$registration['status']]); ?></span></p>
        <?php if (count($allowed_next) > 0) { ?><form method="post" action="registration.php?id=<?php echo $registration_id; ?>"><input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>"><label for="status" class="form-label">Nouveau statut</label><select id="status" name="status" class="form-select mb-3" required><option value="">Sélectionner</option><?php foreach ($allowed_next as $next_status) { ?><option value="<?php echo infinitia_h($next_status); ?>"><?php echo infinitia_h($statuses[$next_status]); ?></option><?php } ?></select><button type="submit" class="btn admin-primary w-100">Mettre à jour</button></form><?php } else { ?><p class="mb-0 text-muted">Aucune transition supplémentaire n’est autorisée.</p><?php } ?>
        </div>
    </div>
</div>
<?php infinitia_admin_footer(); ?>
