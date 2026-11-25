<?php
require_once __DIR__.'/../app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Use the logout button to end your session.'); }
verify_csrf();
if (current_user()) audit('logout','user',(int)current_user()['id']);
$_SESSION=[];
if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>'Lax']); }
session_destroy();
header('Location: '.rtrim(envv('APP_URL',''), '/').'/login.php'); exit;
