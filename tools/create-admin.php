<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'training_functions.php';

if (!infinitia_database_ready($conn) || !infinitia_table_exists($conn, 'administrators')) {
    fwrite(STDERR, "La base ou la table administrators n'est pas disponible.\n");
    exit(1);
}

if ($argc < 3) {
    fwrite(STDERR, "Usage: php tools/create-admin.php adresse@email nom-complet\n");
    exit(1);
}

$email = strtolower(trim($argv[1]));
$full_name = trim($argv[2]);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $full_name === '' || infinitia_strlen($full_name) > 120) {
    fwrite(STDERR, "Adresse e-mail ou nom invalide.\n");
    exit(1);
}

$duplicate_statement = mysqli_prepare($conn, 'SELECT id FROM administrators WHERE email = ? LIMIT 1');
if (!$duplicate_statement) {
    fwrite(STDERR, "Impossible de vérifier l'adresse e-mail.\n");
    exit(1);
}
mysqli_stmt_bind_param($duplicate_statement, 's', $email);
mysqli_stmt_execute($duplicate_statement);
mysqli_stmt_store_result($duplicate_statement);
$email_exists = mysqli_stmt_num_rows($duplicate_statement) > 0;
mysqli_stmt_close($duplicate_statement);

if ($email_exists) {
    fwrite(STDERR, "Un administrateur utilise déjà cette adresse e-mail.\n");
    exit(1);
}

fwrite(STDOUT, "Mot de passe (saisie visible selon le terminal) : ");
$password = trim(fgets(STDIN));
if (strlen($password) < 12) {
    fwrite(STDERR, "Le mot de passe doit contenir au moins 12 caractères.\n");
    exit(1);
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');
$statement = mysqli_prepare($conn, 'INSERT INTO administrators (full_name, email, password_hash, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)');
if (!$statement) {
    fwrite(STDERR, "Impossible de préparer la création.\n");
    exit(1);
}
mysqli_stmt_bind_param($statement, 'sssss', $full_name, $email, $password_hash, $now, $now);
$created = mysqli_stmt_execute($statement);
$error = mysqli_stmt_error($statement);
mysqli_stmt_close($statement);

if (!$created) {
    fwrite(STDERR, "Création impossible : " . $error . "\n");
    exit(1);
}

fwrite(STDOUT, "Administrateur créé.\n");
