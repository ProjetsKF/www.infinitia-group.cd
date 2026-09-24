<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
infinitia_admin_require_login();

$catalog = infinitia_training_catalog();
$statuses = infinitia_registration_statuses();
$filter_formation = isset($_GET['formation']) && isset($catalog[$_GET['formation']]) ? $_GET['formation'] : '';
$filter_status = isset($_GET['status']) && isset($statuses[$_GET['status']]) ? $_GET['status'] : '';
$filter_session = isset($_GET['session']) && infinitia_valid_id($_GET['session']) ? (int) $_GET['session'] : 0;

if (!infinitia_admin_database_ready($conn)) {
    infinitia_admin_header('Inscriptions', 'registrations');
    echo '<div class="alert alert-danger">Le module de base de données n’est pas initialisé.</div>';
    infinitia_admin_footer();
    exit;
}

$sessions = array();
$session_result = mysqli_query($conn, 'SELECT id, formation_code, start_date FROM training_sessions ORDER BY start_date DESC, id DESC');
if ($session_result) {
    while ($row = mysqli_fetch_assoc($session_result)) {
        $sessions[] = $row;
    }
}

$registrations = array();
$sql = "SELECT r.id, r.session_id, r.formation_code, r.full_name, r.phone, r.email, r.status, r.submitted_at,
               s.start_date, s.end_date
        FROM training_registrations r
        INNER JOIN training_sessions s ON s.id = r.session_id
        ORDER BY r.submitted_at DESC, r.id DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($filter_formation !== '' && $row['formation_code'] !== $filter_formation) {
            continue;
        }
        if ($filter_status !== '' && $row['status'] !== $filter_status) {
            continue;
        }
        if ($filter_session > 0 && (int) $row['session_id'] !== $filter_session) {
            continue;
        }
        $registrations[] = $row;
    }
}

infinitia_admin_header('Inscriptions', 'registrations');
?>
<div class="admin-page-heading"><div><span>Traitement</span><h1>Demandes d’inscription</h1></div></div>
<div class="admin-panel mb-4">
    <form method="get" action="registrations.php" class="row g-3 align-items-end">
        <div class="col-lg-4"><label class="form-label" for="formation">Formation</label><select class="form-select" id="formation" name="formation"><option value="">Toutes</option><?php foreach ($catalog as $code => $name) { ?><option value="<?php echo infinitia_h($code); ?>"<?php echo $filter_formation === $code ? ' selected' : ''; ?>><?php echo infinitia_h($name); ?></option><?php } ?></select></div>
        <div class="col-lg-3"><label class="form-label" for="session">Session</label><select class="form-select" id="session" name="session"><option value="">Toutes</option><?php foreach ($sessions as $session) { ?><option value="<?php echo (int) $session['id']; ?>"<?php echo $filter_session === (int) $session['id'] ? ' selected' : ''; ?>>#<?php echo (int) $session['id']; ?> — <?php echo infinitia_h(infinitia_training_name($session['formation_code'])); ?></option><?php } ?></select></div>
        <div class="col-lg-3"><label class="form-label" for="status">Statut</label><select class="form-select" id="status" name="status"><option value="">Tous</option><?php foreach ($statuses as $code => $label) { ?><option value="<?php echo infinitia_h($code); ?>"<?php echo $filter_status === $code ? ' selected' : ''; ?>><?php echo infinitia_h($label); ?></option><?php } ?></select></div>
        <div class="col-lg-2 d-grid"><button type="submit" class="btn admin-primary">Filtrer</button></div>
    </form>
</div>
<div class="admin-panel">
    <div class="table-responsive"><table class="table align-middle admin-table"><thead><tr><th>Référence</th><th>Candidat</th><th>Contact</th><th>Formation</th><th>Session</th><th>Date</th><th>Statut</th><th></th></tr></thead><tbody>
    <?php if (count($registrations) === 0) { ?><tr><td colspan="8" class="text-center py-4">Aucune demande ne correspond aux filtres.</td></tr><?php } ?>
    <?php foreach ($registrations as $registration) { ?><tr>
        <td>#<?php echo (int) $registration['id']; ?></td>
        <td><?php echo infinitia_h($registration['full_name']); ?></td>
        <td><div><?php echo infinitia_h($registration['phone']); ?></div><small><?php echo infinitia_h($registration['email']); ?></small></td>
        <td><?php echo infinitia_h(infinitia_training_name($registration['formation_code'])); ?></td>
        <td>#<?php echo (int) $registration['session_id']; ?> — <?php echo infinitia_h(infinitia_format_date_range($registration['start_date'], $registration['end_date'])); ?></td>
        <td><?php echo infinitia_h(date('d/m/Y H:i', strtotime($registration['submitted_at']))); ?></td>
        <td><span class="admin-badge"><?php echo infinitia_h(isset($statuses[$registration['status']]) ? $statuses[$registration['status']] : $registration['status']); ?></span></td>
        <td><a href="registration.php?id=<?php echo (int) $registration['id']; ?>" class="btn btn-sm btn-outline-primary">Consulter</a></td>
    </tr><?php } ?>
    </tbody></table></div>
</div>
<?php infinitia_admin_footer(); ?>
