<?php
require_once __DIR__.'/../app/bootstrap.php';
$token = $_GET['token'] ?? ''; $error=''; $user=null;
if (is_string($token) && preg_match('/^[a-f0-9]{64}$/',$token)) {
  $stmt=db()->prepare('SELECT id,full_name,email FROM users WHERE verification_token_hash=? AND verification_expires_at>NOW() AND is_verified=0 LIMIT 1');
  $stmt->execute([hash('sha256',$token)]); $user=$stmt->fetch();
}
if (!$user) $error='This verification link is invalid or has expired. Ask the administrator to send a new invitation.';
if ($_SERVER['REQUEST_METHOD']==='POST' && $user) {
  verify_csrf(); $pass=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
  if (strlen($pass)<12) $error='Use a password with at least 12 characters.';
  elseif ($pass!==$confirm) $error='The passwords do not match.';
  else {
    $stmt=db()->prepare('UPDATE users SET password_hash=?,is_verified=1,verification_token_hash=NULL,verification_expires_at=NULL WHERE id=?');
    $stmt->execute([password_hash($pass,PASSWORD_DEFAULT),$user['id']]);
    flash('success','Email verified. You can now sign in.'); redirect('login.php');
  }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Verify account · Rwebeya Clinic</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-body"><main class="auth-card"><a class="brand auth-brand" href="login.php"><span class="brand-mark">R</span><span>Rwebeya<small>CLINIC PORTAL</small></span></a><span class="eyebrow">ACCOUNT VERIFICATION</span><h1>Set your password</h1>
<?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<?php if ($user): ?><p class="muted">Hello <?=e($user['full_name'])?>. Your email is ready to be verified. Create a strong password to activate your account.</p><form method="post" class="form-stack"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>New password<input type="password" name="password" required minlength="12" autocomplete="new-password"></label><label>Confirm password<input type="password" name="confirm_password" required minlength="12" autocomplete="new-password"></label><button class="btn primary full">Verify email and activate account</button></form><?php endif; ?>
</main></body></html>
