<?php
header('Content-Type: text/plain; charset=utf-8');

require_once '../config/database.php';

function generateUnsubscribeToken($conn)
{
    $sql = 'SELECT id FROM abonnes_newsletter WHERE token_desabonnement = ? LIMIT 1';

    for ($i = 0; $i < 10; $i++) {
        $bytes = openssl_random_pseudo_bytes(32);

        if ($bytes === false) {
            continue;
        }

        $token = bin2hex($bytes);

        if (strlen($token) !== 64) {
            continue;
        }

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $token);

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        mysqli_stmt_store_result($stmt);
        $token_exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$token_exists) {
            return $token;
        }
    }

    return false;
}

/*
 * Chaque newsletter envoyée plus tard devra contenir un lien de désabonnement
 * de ce type : https://www.infinitia-group.com/desabonnement.php?token=TOKEN_DE_L_ABONNE
 *
 * Requête à utiliser pour récupérer uniquement les abonnés actifs :
 * SELECT email, token_desabonnement
 * FROM abonnes_newsletter
 * WHERE statut = 'actif'
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Requête non autorisée.';
    exit;
}

$email = '';

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}

if ($email === '') {
    echo 'Veuillez saisir votre adresse e-mail.';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Adresse e-mail invalide.';
    exit;
}

$adresse_ip = '';

if (isset($_SERVER['REMOTE_ADDR'])) {
    $adresse_ip = $_SERVER['REMOTE_ADDR'];
}

if (!$conn) {
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

$select_sql = 'SELECT id, statut, token_desabonnement FROM abonnes_newsletter WHERE email = ? LIMIT 1';
$select_stmt = mysqli_prepare($conn, $select_sql);

if (!$select_stmt) {
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

mysqli_stmt_bind_param($select_stmt, 's', $email);

if (!mysqli_stmt_execute($select_stmt)) {
    mysqli_stmt_close($select_stmt);
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

mysqli_stmt_bind_result($select_stmt, $id, $statut, $token_desabonnement);
$email_existe = mysqli_stmt_fetch($select_stmt);
mysqli_stmt_close($select_stmt);

if ($email_existe) {
    if ($statut === 'actif') {
        echo 'Vous êtes déjà inscrit à notre newsletter.';
        exit;
    }

    if ($statut === 'desabonne') {
        if ($token_desabonnement == '') {
            $token_desabonnement = generateUnsubscribeToken($conn);

            if ($token_desabonnement === false) {
                echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
                exit;
            }
        }

        $update_sql = "UPDATE abonnes_newsletter SET statut = 'actif', adresse_ip = ?, token_desabonnement = ?, date_inscription = NOW() WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        if (!$update_stmt) {
            echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
            exit;
        }

        mysqli_stmt_bind_param($update_stmt, 'ssi', $adresse_ip, $token_desabonnement, $id);

        if (!mysqli_stmt_execute($update_stmt)) {
            mysqli_stmt_close($update_stmt);
            echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
            exit;
        }

        mysqli_stmt_close($update_stmt);
        echo 'OK';
        exit;
    }

    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

$token_desabonnement = generateUnsubscribeToken($conn);

if ($token_desabonnement === false) {
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

$insert_sql = "INSERT INTO abonnes_newsletter (email, statut, adresse_ip, token_desabonnement, date_inscription) VALUES (?, 'actif', ?, ?, NOW())";
$insert_stmt = mysqli_prepare($conn, $insert_sql);

if (!$insert_stmt) {
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

mysqli_stmt_bind_param($insert_stmt, 'sss', $email, $adresse_ip, $token_desabonnement);

if (!mysqli_stmt_execute($insert_stmt)) {
    mysqli_stmt_close($insert_stmt);
    echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
    exit;
}

mysqli_stmt_close($insert_stmt);
echo 'OK';
exit;
