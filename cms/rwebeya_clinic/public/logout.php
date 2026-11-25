<?php
// Load the shared session and application helpers.
require_once __DIR__.'/../app/bootstrap.php';

// Logout is only accepted through a POST request to prevent accidental session termination.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Use the logout button to end your session.'); }

// Validate the logout form token before ending the session.
verify_csrf();

// Record who logged out before clearing the session data.
if (current_user()) audit('logout','user',(int)current_user()['id']);

// Clear session data and delete the browser's session cookie.
$_SESSION=[];
if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>'Lax']); }
session_destroy();

// Send the user back to the sign-in page.
header('Location: '.rtrim(envv('APP_URL',''), '/').'/login.php'); exit;
