<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'training_functions.php';

infinitia_start_session();
infinitia_initialize_training_catalog($conn);

function infinitia_admin_logged_in()
{
    return isset($_SESSION['admin_id']) && infinitia_valid_id($_SESSION['admin_id']);
}

function infinitia_admin_require_login()
{
    global $conn;
    $expired = isset($_SESSION['admin_last_activity']) && time() - (int) $_SESSION['admin_last_activity'] > 7200;
    $authorized = infinitia_admin_logged_in() && !$expired && infinitia_database_ready($conn) && infinitia_table_exists($conn, 'administrators');

    if ($authorized) {
        $statement = mysqli_prepare($conn, 'SELECT full_name, is_active FROM administrators WHERE id = ? LIMIT 1');
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'i', $_SESSION['admin_id']);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $full_name, $is_active);
            $authorized = mysqli_stmt_fetch($statement) && (int) $is_active === 1;
            mysqli_stmt_close($statement);
            if ($authorized) {
                $_SESSION['admin_name'] = $full_name;
                $_SESSION['admin_last_activity'] = time();
            }
        } else {
            $authorized = false;
        }
    }

    if (!$authorized) {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_last_activity']);
        header('Location: login.php');
        exit;
    }
}

function infinitia_admin_flash($type, $message)
{
    $_SESSION['admin_flash'] = array('type' => $type, 'message' => $message);
}

function infinitia_admin_take_flash()
{
    if (!isset($_SESSION['admin_flash'])) {
        return null;
    }
    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    return $flash;
}

function infinitia_admin_database_ready($conn)
{
    return infinitia_database_ready($conn)
        && infinitia_table_exists($conn, 'administrators')
        && infinitia_table_exists($conn, 'training_sessions')
        && infinitia_table_exists($conn, 'training_registrations');
}

function infinitia_admin_header($title, $active)
{
    $admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administration';
    $flash = infinitia_admin_take_flash();
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo infinitia_h($title); ?> - Administration INFINITIA</title>
    <link href="../assets/img/ico.ico" rel="icon">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
    <header class="admin-header">
        <a href="index.php" class="admin-brand"><img src="../assets/img/brand.png" alt="INFINITIA GROUP SARLU"></a>
        <button class="admin-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Ouvrir le menu"><i class="bi bi-list"></i></button>
        <nav id="adminNav" class="admin-nav collapse d-lg-flex">
            <a<?php echo $active === 'dashboard' ? ' class="active"' : ''; ?> href="index.php"><i class="bi bi-grid"></i> Tableau de bord</a>
            <a<?php echo $active === 'courses' ? ' class="active"' : ''; ?> href="formations.php"><i class="bi bi-mortarboard"></i> Formations</a>
            <a<?php echo $active === 'sessions' ? ' class="active"' : ''; ?> href="sessions.php"><i class="bi bi-calendar3"></i> Sessions</a>
            <a<?php echo $active === 'registrations' ? ' class="active"' : ''; ?> href="registrations.php"><i class="bi bi-person-lines-fill"></i> Inscriptions</a>
        </nav>
        <div class="admin-user"><span><?php echo infinitia_h($admin_name); ?></span><form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>"><button type="submit">Déconnexion</button></form></div>
    </header>
    <main class="admin-main">
        <?php if ($flash) { ?><div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>" role="status"><?php echo infinitia_h($flash['message']); ?></div><?php } ?>
    <?php
}

function infinitia_admin_footer()
{
    ?>
    </main>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}

function infinitia_valid_date($value)
{
    if ($value === '') {
        return true;
    }
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

function infinitia_registration_transitions()
{
    return array(
        'new' => array('processing', 'refused', 'cancelled'),
        'processing' => array('accepted', 'refused', 'cancelled'),
        'accepted' => array('cancelled'),
        'refused' => array(),
        'cancelled' => array()
    );
}
