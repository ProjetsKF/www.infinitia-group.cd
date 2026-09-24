<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
infinitia_admin_require_login();

$catalog = infinitia_training_catalog();
$active_catalog = array();
foreach (infinitia_get_training_courses($conn, true) as $active_course) {
    $active_catalog[$active_course['code']] = $active_course['name'];
}
$statuses = infinitia_session_statuses();
$errors = array();
$editing = array('id' => 0, 'formation_code' => '', 'start_date' => '', 'end_date' => '', 'schedule' => '', 'location' => '', 'capacity' => '', 'prerequisites' => '', 'status' => 'draft');
$show_form = isset($_GET['action']) && $_GET['action'] === 'create';

if (!infinitia_admin_database_ready($conn)) {
    infinitia_admin_header('Sessions', 'sessions');
    echo '<div class="alert alert-danger">Le module de base de données n’est pas initialisé.</div>';
    infinitia_admin_footer();
    exit;
}

if (isset($_GET['edit']) && infinitia_valid_id($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $statement = mysqli_prepare($conn, 'SELECT id, formation_code, start_date, end_date, schedule, location, capacity, prerequisites, status FROM training_sessions WHERE id = ? LIMIT 1');
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $edit_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $id, $formation_code, $start_date, $end_date, $schedule, $location, $capacity, $prerequisites, $status);
        if (mysqli_stmt_fetch($statement)) {
            $editing = array('id' => $id, 'formation_code' => $formation_code, 'start_date' => $start_date, 'end_date' => $end_date, 'schedule' => $schedule, 'location' => $location, 'capacity' => $capacity, 'prerequisites' => $prerequisites, 'status' => $status);
            $show_form = true;
        }
        mysqli_stmt_close($statement);
    }
}

if (infinitia_request_method() === 'POST') {
    $show_form = true;
    $editing = array(
        'id' => infinitia_valid_id(infinitia_post_value('id')) ? (int) infinitia_post_value('id') : 0,
        'formation_code' => infinitia_post_value('formation_code'),
        'start_date' => infinitia_post_value('start_date'),
        'end_date' => infinitia_post_value('end_date'),
        'schedule' => infinitia_post_value('schedule'),
        'location' => infinitia_post_value('location'),
        'capacity' => infinitia_post_value('capacity'),
        'prerequisites' => infinitia_post_value('prerequisites'),
        'status' => infinitia_post_value('status')
    );

    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token'))) {
        $errors[] = 'La session du formulaire a expiré.';
    }
    $selected_course = infinitia_training_course($editing['formation_code']);
    $existing_formation_code = '';
    if ((int) $editing['id'] > 0) {
        $existing_statement = mysqli_prepare($conn, 'SELECT formation_code FROM training_sessions WHERE id = ? LIMIT 1');
        if ($existing_statement) {
            mysqli_stmt_bind_param($existing_statement, 'i', $editing['id']);
            mysqli_stmt_execute($existing_statement);
            mysqli_stmt_bind_result($existing_statement, $existing_formation_code);
            mysqli_stmt_fetch($existing_statement);
            mysqli_stmt_close($existing_statement);
        }
    }
    if ($selected_course === null || ((int) $selected_course['is_active'] !== 1 && $existing_formation_code !== $editing['formation_code'])) {
        $errors[] = 'La formation sélectionnée est invalide.';
    }
    if ($selected_course !== null && (int) $selected_course['is_active'] !== 1 && $editing['status'] === 'open') {
        $errors[] = 'Une formation inactive ne peut pas recevoir une nouvelle session ouverte.';
    }
    if (!isset($statuses[$editing['status']])) {
        $errors[] = 'Le statut est invalide.';
    }
    if (!infinitia_valid_date($editing['start_date']) || !infinitia_valid_date($editing['end_date'])) {
        $errors[] = 'Une date est invalide.';
    }
    if ($editing['start_date'] !== '' && $editing['end_date'] !== '' && $editing['end_date'] < $editing['start_date']) {
        $errors[] = 'La date de fin ne peut pas précéder la date de début.';
    }
    if ($editing['status'] === 'open' && $editing['start_date'] === '') {
        $errors[] = 'Une date de début est obligatoire pour ouvrir une session.';
    }
    if ($editing['capacity'] !== '' && (!ctype_digit($editing['capacity']) || (int) $editing['capacity'] <= 0 || (int) $editing['capacity'] > 100000)) {
        $errors[] = 'Le nombre de places doit être un entier positif.';
    }
    if (infinitia_strlen($editing['schedule']) > 190 || infinitia_strlen($editing['location']) > 190 || infinitia_strlen($editing['prerequisites']) > 3000) {
        $errors[] = 'Un champ dépasse la longueur autorisée.';
    }
    if ((int) $editing['id'] > 0 && $editing['capacity'] !== '') {
        $count_statement = mysqli_prepare($conn, "SELECT COUNT(*) FROM training_registrations WHERE session_id = ? AND status = 'accepted'");
        if ($count_statement) {
            mysqli_stmt_bind_param($count_statement, 'i', $editing['id']);
            mysqli_stmt_execute($count_statement);
            mysqli_stmt_bind_result($count_statement, $accepted_count);
            mysqli_stmt_fetch($count_statement);
            mysqli_stmt_close($count_statement);
            if ((int) $accepted_count > (int) $editing['capacity']) {
                $errors[] = 'Le nombre de places ne peut pas être inférieur au nombre de demandes déjà acceptées.';
            } elseif ($editing['status'] === 'open' && (int) $accepted_count >= (int) $editing['capacity']) {
                $errors[] = 'Une session dont la capacité est atteinte doit avoir le statut « Complète ».';
            }
        }
    }

    if (count($errors) === 0) {
        $start_date = $editing['start_date'] !== '' ? $editing['start_date'] : null;
        $end_date = $editing['end_date'] !== '' ? $editing['end_date'] : null;
        $schedule = $editing['schedule'] !== '' ? $editing['schedule'] : null;
        $location = $editing['location'] !== '' ? $editing['location'] : null;
        $capacity = $editing['capacity'] !== '' ? (int) $editing['capacity'] : null;
        $prerequisites = $editing['prerequisites'] !== '' ? $editing['prerequisites'] : null;
        $now = date('Y-m-d H:i:s');

        if ((int) $editing['id'] > 0) {
            $sql = 'UPDATE training_sessions SET formation_code = ?, start_date = ?, end_date = ?, schedule = ?, location = ?, capacity = ?, prerequisites = ?, status = ?, updated_at = ? WHERE id = ?';
            $statement = mysqli_prepare($conn, $sql);
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'sssssisssi', $editing['formation_code'], $start_date, $end_date, $schedule, $location, $capacity, $prerequisites, $editing['status'], $now, $editing['id']);
                $saved = mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);
            } else {
                $saved = false;
            }
        } else {
            $sql = 'INSERT INTO training_sessions (formation_code, start_date, end_date, schedule, location, capacity, prerequisites, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $statement = mysqli_prepare($conn, $sql);
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'sssssissss', $editing['formation_code'], $start_date, $end_date, $schedule, $location, $capacity, $prerequisites, $editing['status'], $now, $now);
                $saved = mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);
            } else {
                $saved = false;
            }
        }

        if ($saved) {
            infinitia_admin_flash('success', (int) $editing['id'] > 0 ? 'La session a été mise à jour.' : 'La session a été créée.');
            header('Location: sessions.php');
            exit;
        }
        $errors[] = 'La session n’a pas pu être enregistrée.';
    }
}

$sessions = array();
$result = mysqli_query($conn, "SELECT s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status,
    COUNT(r.id) AS request_count, SUM(CASE WHEN r.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_count
    FROM training_sessions s LEFT JOIN training_registrations r ON r.session_id = s.id
    GROUP BY s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status
    ORDER BY s.created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = $row;
    }
}

infinitia_admin_header('Sessions', 'sessions');
$session_catalog = $active_catalog;
if ($editing['formation_code'] !== '' && isset($catalog[$editing['formation_code']]) && !isset($session_catalog[$editing['formation_code']])) {
    $session_catalog[$editing['formation_code']] = $catalog[$editing['formation_code']] . ' (inactive)';
}
?>
<div class="admin-page-heading"><div><span>Gestion</span><h1>Sessions de formation</h1></div><?php if (!$show_form) { ?><a href="sessions.php?action=create" class="btn admin-primary"><i class="bi bi-plus-lg"></i> Nouvelle session</a><?php } ?></div>

<?php if ($show_form) { ?>
<div class="admin-panel mb-4">
    <h2><?php echo (int) $editing['id'] > 0 ? 'Modifier la session' : 'Créer une session'; ?></h2>
    <?php if (count($errors) > 0) { ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error) { ?><li><?php echo infinitia_h($error); ?></li><?php } ?></ul></div><?php } ?>
    <form method="post" action="sessions.php<?php echo (int) $editing['id'] > 0 ? '?edit=' . (int) $editing['id'] : '?action=create'; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
        <div class="row g-3">
            <div class="col-lg-8"><label class="form-label" for="formation_code">Formation *</label><select class="form-select" id="formation_code" name="formation_code" required><option value="">Sélectionner</option><?php foreach ($session_catalog as $code => $name) { ?><option value="<?php echo infinitia_h($code); ?>"<?php echo $editing['formation_code'] === $code ? ' selected' : ''; ?>><?php echo infinitia_h($name); ?></option><?php } ?></select></div>
            <div class="col-lg-4"><label class="form-label" for="status">Statut *</label><select class="form-select" id="status" name="status" required><?php foreach ($statuses as $code => $label) { ?><option value="<?php echo infinitia_h($code); ?>"<?php echo $editing['status'] === $code ? ' selected' : ''; ?>><?php echo infinitia_h($label); ?></option><?php } ?></select></div>
            <div class="col-md-6"><label class="form-label" for="start_date">Date de début</label><input class="form-control" type="date" id="start_date" name="start_date" value="<?php echo infinitia_h($editing['start_date']); ?>"></div>
            <div class="col-md-6"><label class="form-label" for="end_date">Date de fin</label><input class="form-control" type="date" id="end_date" name="end_date" value="<?php echo infinitia_h($editing['end_date']); ?>"></div>
            <div class="col-md-6"><label class="form-label" for="schedule">Horaires</label><input class="form-control" type="text" id="schedule" name="schedule" maxlength="190" value="<?php echo infinitia_h($editing['schedule']); ?>"></div>
            <div class="col-md-6"><label class="form-label" for="location">Lieu</label><input class="form-control" type="text" id="location" name="location" maxlength="190" value="<?php echo infinitia_h($editing['location']); ?>"></div>
            <div class="col-md-6"><label class="form-label" for="capacity">Nombre maximum de places</label><input class="form-control" type="number" id="capacity" name="capacity" min="1" max="100000" value="<?php echo infinitia_h($editing['capacity']); ?>"></div>
            <div class="col-12"><label class="form-label" for="prerequisites">Prérequis / informations utiles</label><textarea class="form-control" id="prerequisites" name="prerequisites" rows="4" maxlength="3000"><?php echo infinitia_h($editing['prerequisites']); ?></textarea></div>
            <div class="col-12 d-flex gap-2"><button type="submit" class="btn admin-primary">Enregistrer</button><a href="sessions.php" class="btn btn-outline-secondary">Annuler</a></div>
        </div>
    </form>
</div>
<?php } ?>

<div class="admin-panel">
    <div class="table-responsive"><table class="table align-middle admin-table"><thead><tr><th>ID</th><th>Formation</th><th>Période</th><th>Lieu</th><th>Places</th><th>Demandes</th><th>Statut</th><th></th></tr></thead><tbody>
    <?php if (count($sessions) === 0) { ?><tr><td colspan="8" class="text-center py-4">Aucune session enregistrée.</td></tr><?php } ?>
    <?php foreach ($sessions as $session) { ?><tr><td>#<?php echo (int) $session['id']; ?></td><td><?php echo infinitia_h(infinitia_training_name($session['formation_code'])); ?></td><td><?php echo infinitia_h(infinitia_format_date_range($session['start_date'], $session['end_date'])); ?></td><td><?php echo infinitia_h($session['location'] !== null && $session['location'] !== '' ? $session['location'] : 'À préciser'); ?></td><td><?php echo $session['capacity'] === null ? 'Non limité' : (int) $session['accepted_count'] . ' / ' . (int) $session['capacity']; ?></td><td><?php echo (int) $session['request_count']; ?></td><td><span class="admin-badge"><?php echo infinitia_h(isset($statuses[$session['status']]) ? $statuses[$session['status']] : $session['status']); ?></span></td><td><a href="sessions.php?edit=<?php echo (int) $session['id']; ?>" class="btn btn-sm btn-outline-primary">Modifier</a></td></tr><?php } ?>
    </tbody></table></div>
</div>
<?php infinitia_admin_footer(); ?>
