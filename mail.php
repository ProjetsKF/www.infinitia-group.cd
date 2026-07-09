<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

function redirect_contact($type, $message)
{
  if ($type == 'success') {
    $_SESSION['contact_success'] = $message;
  } else {
    $_SESSION['contact_error'] = $message;
  }

  header('Location: contact.php');
  exit;
}

function has_header_injection($value)
{
  return preg_match("/[\r\n]/", $value);
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
  redirect_contact('error', 'Veuillez utiliser le formulaire de contact.');
}

$config_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mail_config.php';

if (!file_exists($config_file)) {
  log_mail_error('Fichier de configuration SMTP introuvable.');
  redirect_contact('error', 'La configuration email n est pas encore terminee.');
}

require_once $config_file;

if (!load_phpmailer()) {
  log_mail_error('PHPMailer n a pas pu etre charge.');
  redirect_contact('error', 'Le service email est temporairement indisponible.');
}

if (SMTP_PASSWORD == 'A_REMPLACER_PAR_LE_MOT_DE_PASSE_APPLICATION_GMAIL') {
  log_mail_error('Mot de passe d application Gmail non configure.');
  redirect_contact('error', 'La configuration email n est pas encore terminee.');
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($name == '' || $email == '' || $subject == '' || $message == '') {
  redirect_contact('error', 'Veuillez remplir tous les champs du formulaire.');
}

if (has_header_injection($name) || has_header_injection($email) || has_header_injection($subject)) {
  redirect_contact('error', 'Votre message contient des donnees non autorisees.');
}

$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  redirect_contact('error', 'Veuillez saisir une adresse e-mail valide.');
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
  $mailer->SMTPAuth = true;
  $mailer->Username = SMTP_USERNAME;
  $mailer->Password = SMTP_PASSWORD;
  $mailer->SMTPSecure = 'tls';
  $mailer->Port = SMTP_PORT;
  $mailer->CharSet = 'UTF-8';
  $mailer->isHTML(true);
  $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
  $mailer->addAddress(SMTP_TO_EMAIL, SMTP_TO_NAME);
  $mailer->addReplyTo($email, $name);
  $mailer->Subject = $mail_subject;
  $mailer->Body = $email_body;
  $mailer->AltBody = $alt_body;
  $mailer->send();

  redirect_contact('success', 'Votre message a ete envoye avec succes. Notre equipe vous repondra rapidement.');
} catch (Exception $e) {
  log_mail_error('Erreur PHPMailer : ' . $e->getMessage());
  redirect_contact('error', 'Votre message n a pas pu etre envoye pour le moment. Veuillez reessayer plus tard.');
}
?>
