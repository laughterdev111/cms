<?php
declare(strict_types=1);
// Load database and environment helpers used by the mail service.
require_once __DIR__.'/bootstrap.php';

function send_verification_email(string $to, string $name, string $url): bool {
    // Use SMTP when MAIL_HOST is configured.
    $host = envv('MAIL_HOST');
    if ($host !== '') {
        require_once BASE_PATH.'/vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = envv('MAIL_USERNAME') !== '';
            $mail->Username = envv('MAIL_USERNAME');
            $mail->Password = envv('MAIL_PASSWORD');
            $mail->Port = (int)envv('MAIL_PORT','587');
            $enc = strtolower(envv('MAIL_ENCRYPTION','tls'));
            if ($enc === 'tls') $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            if ($enc === 'ssl') $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->setFrom(envv('MAIL_FROM_ADDRESS','no-reply@example.com'), envv('MAIL_FROM_NAME','Rwebeya Clinic'));
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Verify your Rwebeya Clinic staff account';
            $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $mail->Body = "<p>Hello {$safeName},</p><p>An administrator invited you to Rwebeya Clinic. Verify your email and set your password:</p><p><a href=\"{$safeUrl}\">Verify email and create password</a></p><p>If you did not expect this invitation, ignore this email.</p>";
            $mail->AltBody = "Verify your email and set your password: ".$url;
            $mail->send();
            return true;
        } catch (Throwable $ex) {
            error_log('Mail error: '.$ex->getMessage());
            return false;
        }
    }
    if (envv('APP_ENV','production') === 'local') {
        $line = date('c')." | TO: {$to} | VERIFY: {$url}\n";
        file_put_contents(BASE_PATH.'/storage/mail.log', $line, FILE_APPEND | LOCK_EX);
        return true;
    }
    return false;
}
