<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
infinitia_admin_require_login();

$counts = array('active_courses' => 0, 'sessions' => 0, 'open_sessions' => 0, 'new_registrations' => 0, 'registrations' => 0);
if (infinitia_admin_database_ready($conn)) {
    $queries = array(
        'active_courses' => 'SELECT COUNT(*) FROM training_courses WHERE is_active = 1',
        'sessions' => 'SELECT COUNT(*) FROM training_sessions',
        'open_sessions' => "SELECT COUNT(*) FROM training_sessions WHERE status = 'open'",
        'new_registrations' => "SELECT COUNT(*) FROM training_registrations WHERE status = 'new'",
        'registrations' => 'SELECT COUNT(*) FROM training_registrations'
    );
    foreach ($queries as $key => $sql) {
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $row = mysqli_fetch_row($result);
            $counts[$key] = (int) $row[0];
        }
    }
}

infinitia_admin_header('Tableau de bord', 'dashboard');
?>
<div class="admin-page-heading"><div><span>Vue d’ensemble</span><h1>Formations professionnelles</h1></div></div>
<div class="row g-4">
    <div class="col-md-6 col-xl"><div class="admin-stat"><i class="bi bi-mortarboard"></i><strong><?php echo $counts['active_courses']; ?></strong><span>Formations actives</span></div></div>
    <div class="col-md-6 col-xl"><div class="admin-stat"><i class="bi bi-calendar3"></i><strong><?php echo $counts['sessions']; ?></strong><span>Sessions</span></div></div>
    <div class="col-md-6 col-xl"><div class="admin-stat"><i class="bi bi-calendar-check"></i><strong><?php echo $counts['open_sessions']; ?></strong><span>Sessions ouvertes</span></div></div>
    <div class="col-md-6 col-xl"><div class="admin-stat"><i class="bi bi-envelope-exclamation"></i><strong><?php echo $counts['new_registrations']; ?></strong><span>Nouvelles demandes</span></div></div>
    <div class="col-md-6 col-xl"><div class="admin-stat"><i class="bi bi-people"></i><strong><?php echo $counts['registrations']; ?></strong><span>Demandes totales</span></div></div>
</div>
<div class="admin-panel mt-4">
    <h2>Actions rapides</h2>
    <div class="d-flex flex-wrap gap-3"><a href="sessions.php?action=create" class="btn admin-primary">Créer une session</a><a href="registrations.php?status=new" class="btn btn-outline-primary">Voir les nouvelles demandes</a></div>
</div>
<?php infinitia_admin_footer(); ?>
