<?php
// Load application helpers and redirect already-authenticated users.
require_once __DIR__.'/../app/bootstrap.php';
if (current_user()) redirect('index.php');
$error = ''; $notice = take_flash();

// Process the submitted sign-in form.
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verify_csrf();
  $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
  // Find the account using its unique email address.
  $stmt = db()->prepare('SELECT id,full_name,email,password_hash,role,is_verified,is_active,avatar_path,created_at FROM users WHERE email=? LIMIT 1');
  $stmt->execute([$email]); $u = $stmt->fetch();
  // Check credentials and account status before starting a session.
  if (!$u || !$u['password_hash'] || !password_verify($password,$u['password_hash'])) $error='Email or password is incorrect.';
  elseif (!(int)$u['is_verified']) $error='Please verify your email address before signing in.';
  elseif (!(int)$u['is_active']) $error='This account is inactive. Contact the administrator.';
  else {
    session_regenerate_id(true);
    $_SESSION['user']=['id'=>(int)$u['id'],'full_name'=>$u['full_name'],'email'=>$u['email'],'role'=>$u['role'],'avatar_path'=>$u['avatar_path'],'created_at'=>$u['created_at']];
    audit('login','user',(int)$u['id']); redirect('index.php');
  }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in · Rwebeya Clinic</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-body"><main class="auth-card"><a class="brand auth-brand" href="login.php"><span class="brand-mark">R</span><span>Rwebeya<small>CLINIC PORTAL</small></span></a><span class="eyebrow">STAFF ACCESS</span><h1>Welcome back</h1><p class="muted">Sign in to continue to your workspace.</p>
<?php if ($notice): ?><div class="alert <?=e($notice['type'])?>"><?=e($notice['message'])?></div><?php endif; ?><?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Email address<input type="email" name="email" required autocomplete="username" placeholder="you@clinic.com"></label><label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"></label><button class="btn primary full" type="submit">Sign in <span>→</span></button></form><p class="auth-foot">Staff accounts are created by your clinic administrator.</p></main><script src="assets/app.js"></script></body></html>
