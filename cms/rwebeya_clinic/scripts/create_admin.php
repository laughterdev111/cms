<?php
// Load application helpers and mail support for CLI administration.
require_once __DIR__.'/../app/bootstrap.php';
require_once __DIR__.'/../app/mailer.php';
// This script must run from the command line and requires name and email arguments.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only.\n"); }
if ($argc < 3) { echo "Usage: php scripts/create_admin.php \"Full Name\" admin@example.com\n"; exit(1); }
$name=trim($argv[1]);$email=strtolower(trim($argv[2]));
if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)){echo "Provide a valid name and email.\n";exit(1);}
$token=bin2hex(random_bytes(32));
try {
 $s=db()->prepare("INSERT INTO users(full_name,email,role,verification_token_hash,verification_expires_at) VALUES(?,?,'Admin',?,DATE_ADD(NOW(),INTERVAL 24 HOUR))");
 $s->execute([$name,$email,hash('sha256',$token)]);
 $url=app_url('verify.php?token='.$token);
 if(send_verification_email($email,$name,$url)){echo "Admin invitation created.\n";if(envv('APP_ENV')==='local' && envv('MAIL_HOST')==='')echo "Local verification link is in storage/mail.log\n";}
 else {db()->prepare('DELETE FROM users WHERE email=?')->execute([$email]);echo "Could not send email. Check SMTP configuration.\n";exit(1);}
} catch(Throwable $e){echo "Could not create administrator: ".$e->getMessage()."\n";exit(1);}
