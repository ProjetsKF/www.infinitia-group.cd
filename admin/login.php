<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

if (infinitia_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';
$now = time();
if (!isset($_SESSION['login_window_started']) || $now - (int) $_SESSION['login_window_started'] > 900) {
    $_SESSION['login_window_started'] = $now;
    $_SESSION['login_attempts'] = 0;
}

if (infinitia_request_method() === 'POST') {
    $email = strtolower(infinitia_post_value('email'));
    $password = infinitia_post_value('password');

    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token'))) {
        $error = 'La session du formulaire a expiré.';
    } elseif ((int) $_SESSION['login_attempts'] >= 5) {
        $error = 'Trop de tentatives. Veuillez patienter 15 minutes.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Identifiants invalides.';
    } elseif (!infinitia_admin_database_ready($conn)) {
        $error = 'L’administration n’est pas encore initialisée.';
    } else {
        $_SESSION['login_attempts'] = (int) $_SESSION['login_attempts'] + 1;
        $statement = mysqli_prepare($conn, 'SELECT id, full_name, password_hash, is_active FROM administrators WHERE email = ? LIMIT 1');
        if ($statement) {
            mysqli_stmt_bind_param($statement, 's', $email);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $admin_id, $full_name, $password_hash, $is_active);
            $found = mysqli_stmt_fetch($statement);
            mysqli_stmt_close($statement);
            if ($found && (int) $is_active === 1 && password_verify($password, $password_hash)) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int) $admin_id;
                $_SESSION['admin_name'] = $full_name;
                $_SESSION['admin_last_activity'] = time();
                $_SESSION['login_attempts'] = 0;
                $login_at = date('Y-m-d H:i:s');
                $update = mysqli_prepare($conn, 'UPDATE administrators SET last_login_at = ?, updated_at = ? WHERE id = ?');
                if ($update) {
                    mysqli_stmt_bind_param($update, 'ssi', $login_at, $login_at, $admin_id);
                    mysqli_stmt_execute($update);
                    mysqli_stmt_close($update);
                }
                header('Location: index.php');
                exit;
            }
        }
        $error = 'Identifiants invalides.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Administration INFINITIA</title>
    <link href="../assets/img/ico.ico" rel="icon">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body class="admin-login-body">
    <main class="admin-login-card">
        <img src="../assets/img/brand.png" alt="INFINITIA GROUP SARLU" class="admin-login-logo">
        <h1>Administration</h1>
        <p>Gestion des sessions et demandes de formation.</p>
        <?php if ($error !== '') { ?><div class="alert alert-danger" role="alert"><?php echo infinitia_h($error); ?></div><?php } ?>
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>">
            <div class="mb-3"><label for="email" class="form-label">Adresse e-mail</label><input type="email" id="email" name="email" class="form-control" maxlength="190" required autocomplete="username" value="<?php echo infinitia_h($email); ?>"></div>
            <div class="mb-4"><label for="password" class="form-label">Mot de passe</label><input type="password" id="password" name="password" class="form-control" required autocomplete="current-password"></div>
            <button type="submit" class="btn admin-primary w-100">Se connecter</button>
        </form>
        <a href="../" class="admin-back-link"><i class="bi bi-arrow-left"></i> Retour au site</a>
    </main>
</body>
</html>
