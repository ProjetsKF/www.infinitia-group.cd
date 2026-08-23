<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

function respond_contact($message)
{
  header('Content-Type: text/plain; charset=UTF-8');
  echo $message;
  exit;
}

function has_header_injection($value)
{
  return preg_match("/[\r\n]/", $value);
}

function get_post_value($key)
{
  if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
    return '';
  }

  return trim($_POST[$key]);
}

function log_mail_error($message)
{
  $log_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs';
  $log_file = $log_dir . DIRECTORY_SEPARATOR . 'mail_errors.log';
  $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

  if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
  }

  @error_log($line, 3, $log_file);
}

function load_phpmailer()
{
  $autoload = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
  $phpmailer = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
  $smtp = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';
  $exception = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';

  if (file_exists($autoload)) {
    require_once $autoload;
  } elseif (file_exists($phpmailer) && file_exists($smtp) && file_exists($exception)) {
    require_once $exception;
    require_once $phpmailer;
    require_once $smtp;
  }

  return class_exists('PHPMailer\\PHPMailer\\PHPMailer');
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  respond_contact('Veuillez utiliser le formulaire de contact.');
}

$turnstile_config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'turnstile.php';

if (!file_exists($turnstile_config_file)) {
  log_mail_error('Fichier de configuration Turnstile introuvable.');
  respond_contact('Impossible de vérifier la sécurité du formulaire pour le moment. Veuillez réessayer plus tard.');
}

require_once $turnstile_config_file;

$turnstile_response = get_post_value('cf-turnstile-response');

if ($turnstile_response == '') {
  respond_contact('Veuillez valider la vérification de sécurité.');
}

if (!defined('TURNSTILE_SECRET_KEY') || TURNSTILE_SECRET_KEY == '' || TURNSTILE_SECRET_KEY == 'VOTRE_SECRET_KEY') {
  log_mail_error('Cle secrete Turnstile non configuree.');
  respond_contact('Impossible de vérifier la sécurité du formulaire pour le moment. Veuillez réessayer plus tard.');
}

if (!function_exists('curl_init')) {
  log_mail_error('Extension cURL indisponible pour la verification Turnstile.');
  respond_contact('Impossible de vérifier la sécurité du formulaire pour le moment. Veuillez réessayer plus tard.');
}

$turnstile_data = array(
  'secret' => TURNSTILE_SECRET_KEY,
  'response' => $turnstile_response
);

if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '') {
  $turnstile_data['remoteip'] = $_SERVER['REMOTE_ADDR'];
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($turnstile_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$turnstile_result = curl_exec($ch);
$turnstile_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$turnstile_curl_error = curl_error($ch);
curl_close($ch);

if ($turnstile_result === false || $turnstile_http_code < 200 || $turnstile_http_code >= 300) {
  log_mail_error('Echec de connexion a Turnstile Siteverify : ' . $turnstile_curl_error);
  respond_contact('Impossible de vérifier la sécurité du formulaire pour le moment. Veuillez réessayer plus tard.');
}

$turnstile_verification = json_decode($turnstile_result, true);

if (!is_array($turnstile_verification)) {
  log_mail_error('Reponse Turnstile Siteverify invalide.');
  respond_contact('Impossible de vérifier la sécurité du formulaire pour le moment. Veuillez réessayer plus tard.');
}

if (!isset($turnstile_verification['success']) || $turnstile_verification['success'] !== true) {
  respond_contact('Vérification de sécurité invalide. Veuillez réessayer.');
}

$request_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
$request_host = preg_replace('/:\\d+$/', '', $request_host);
$remote_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$is_local_host = $request_host == 'localhost'
  || $request_host == '127.0.0.1'
  || $request_host == '::1';
$is_local_address = $remote_address == '127.0.0.1' || $remote_address == '::1';
$is_local_request = $is_local_host && $is_local_address;

if (!$is_local_request && isset($turnstile_verification['hostname'])) {
  $verified_hostname = strtolower(trim($turnstile_verification['hostname']));
  $allowed_hostnames = array('infinitia-group.com', 'www.infinitia-group.com');

  if (!in_array($verified_hostname, $allowed_hostnames, true)) {
    respond_contact('Vérification de sécurité invalide. Veuillez réessayer.');
  }
}

$config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mail_config.php';

if (!file_exists($config_file)) {
  log_mail_error('Fichier de configuration SMTP introuvable.');
  respond_contact('Une erreur est survenue lors de l envoi du message. Veuillez reessayer.');
}

require_once $config_file;

if (!load_phpmailer()) {
  log_mail_error('PHPMailer n a pas pu etre charge.');
  respond_contact('Une erreur est survenue lors de l envoi du message. Veuillez reessayer.');
}

if (SMTP_PASSWORD == '') {
  log_mail_error('Mot de passe SMTP professionnel non configure.');
  respond_contact('Une erreur est survenue lors de l envoi du message. Veuillez reessayer.');
}

$name = get_post_value('name');
$email = get_post_value('email');
$subject = get_post_value('subject');
$message = get_post_value('message');

if ($name == '' || $email == '' || $subject == '' || $message == '') {
  respond_contact('Veuillez remplir tous les champs du formulaire.');
}

if (has_header_injection($name) || has_header_injection($email) || has_header_injection($subject)) {
  respond_contact('Votre message contient des donnees non autorisees.');
}

$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  respond_contact('Veuillez saisir une adresse e-mail valide.');
}

$clean_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$clean_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$clean_subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$clean_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$clean_message_html = nl2br($clean_message);
$sent_date = date('d/m/Y H:i:s');
$mail_subject = 'Nouveau message INFINITIA GROUP SARLU - ' . $subject;

$email_body = '
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Nouveau message INFINITIA GROUP SARLU</title>
</head>
<body style="margin:0; padding:0; background:#F7F7F7; color:#2F2F2F; font-family:Arial, Helvetica, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F7F7F7; padding:30px 0;">
    <tr>
      <td align="center">
        <table width="640" cellpadding="0" cellspacing="0" style="max-width:640px; width:100%; background:#FFFFFF; border:1px solid #eeeeee; border-collapse:collapse;">
          <tr>
            <td style="background:#232F73; color:#FFFFFF; padding:26px 30px; border-bottom:4px solid #D84B8A;">
              <h1 style="margin:0; font-size:22px; line-height:1.4; font-weight:700;">Nouveau message depuis le site web INFINITIA GROUP</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #F7F7F7; color:#2F2F2F;"><strong>Nom de l expediteur :</strong><br>' . $clean_name . '</td>
                </tr>
                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #F7F7F7; color:#2F2F2F;"><strong>Adresse email :</strong><br>' . $clean_email . '</td>
                </tr>
                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #F7F7F7; color:#2F2F2F;"><strong>Sujet :</strong><br>' . $clean_subject . '</td>
                </tr>
                <tr>
                  <td style="padding:12px 0; border-bottom:1px solid #F7F7F7; color:#2F2F2F;"><strong>Date d envoi :</strong><br>' . $sent_date . '</td>
                </tr>
                <tr>
                  <td style="padding:18px 0 0; color:#2F2F2F;"><strong>Message :</strong><br><div style="margin-top:10px; padding:18px; background:#F7F7F7; border-left:4px solid #D84B8A; line-height:1.7;">' . $clean_message_html . '</div></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="background:#232F73; color:#FFFFFF; padding:16px 30px; text-align:center; font-size:13px;">
              INFINITIA GROUP SARLU
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';

$alt_body = "Nouveau message depuis le site web INFINITIA GROUP\n\n";
$alt_body .= "Nom de l expediteur : " . $name . "\n";
$alt_body .= "Adresse email : " . $email . "\n";
$alt_body .= "Sujet : " . $subject . "\n";
$alt_body .= "Date d envoi : " . $sent_date . "\n\n";
$alt_body .= "Message :\n" . $message . "\n\n";
$alt_body .= "INFINITIA GROUP SARLU";

try {
  $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
  $mailer->isSMTP();
  $mailer->Host = SMTP_HOST;
  $mailer->Port = SMTP_PORT;
  $mailer->SMTPAuth = true;
  $mailer->Username = SMTP_USERNAME;
  $mailer->Password = SMTP_PASSWORD;
  $mailer->SMTPSecure = defined('PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_SMTPS') ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : 'ssl';
  $mailer->CharSet = 'UTF-8';
  $mailer->isHTML(true);
  $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
  $mailer->addAddress(SMTP_TO_EMAIL, SMTP_TO_NAME);
  $mailer->addReplyTo($email, $name);
  $mailer->Subject = $mail_subject;
  $mailer->Body = $email_body;
  $mailer->AltBody = $alt_body;
  $mailer->send();

  respond_contact('OK');
} catch (Exception $e) {
  log_mail_error('Erreur PHPMailer : ' . $e->getMessage());
  respond_contact('Une erreur est survenue lors de l envoi du message. Veuillez reessayer.');
}
?>
