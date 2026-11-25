<?php
require_once __DIR__.'/../app/bootstrap.php'; require_role(['Admin']);
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit; }
verify_csrf(); $id=(int)($_POST['id']??0);
if ($id>0 && $id!==(int)current_user()['id']) {
 $s=db()->prepare('UPDATE users SET is_active=IF(is_active=1,0,1) WHERE id=?'); $s->execute([$id]);
 if($s->rowCount()) audit('staff_access_toggled','user',$id);
}
flash('success','Staff access status updated.'); redirect('staff.php');
