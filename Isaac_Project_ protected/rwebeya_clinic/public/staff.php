<?php
require_once __DIR__.'/../app/bootstrap.php'; require_once __DIR__.'/../app/mailer.php'; require_once __DIR__.'/../app/layout.php';
require_role(['Admin']);
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verify_csrf(); $name=trim($_POST['full_name']??''); $email=strtolower(trim($_POST['email']??'')); $role=$_POST['role']??'';
  if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || !in_array($role,['Admin','Receptionist','Doctor'],true)) flash('error','Enter a valid name, email and role.');
  else {
    $token=bin2hex(random_bytes(32)); $hash=hash('sha256',$token);
    try {
      $stmt=db()->prepare('INSERT INTO users(full_name,email,role,verification_token_hash,verification_expires_at) VALUES(?,?,?,?,DATE_ADD(NOW(),INTERVAL 24 HOUR))');
      $stmt->execute([$name,$email,$role,$hash]);
      $url=app_url('verify.php?token='.$token);
      if (send_verification_email($email,$name,$url)) { audit('staff_invited','user',(int)db()->lastInsertId()); flash('success','Invitation created. Verification instructions were sent (or written to the local mail log).'); }
      else { db()->prepare('DELETE FROM users WHERE email=?')->execute([$email]); flash('error','The invitation email could not be sent. Check SMTP settings and try again.'); }
    } catch (PDOException $ex) { flash('error',$ex->getCode()==='23000'?'That email address already has an account.':'Could not create the staff invitation.'); }
  }
  redirect('staff.php');
}
$rows=db()->query('SELECT id,full_name,email,role,is_verified,is_active,created_at FROM users ORDER BY created_at DESC')->fetchAll();
page_start('Staff management','staff.php');
?>
<div class="section-heading"><div><h2>Staff directory</h2><p class="muted">Invite staff and manage account access.</p></div></div>
<div class="panel"><h3>Invite a staff member</h3><p class="muted">The invited person must verify their email and create a password before signing in.</p><form method="post" class="form-grid" data-track-changes><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Full name<input name="full_name" required maxlength="120" placeholder="Staff member name"></label><label>Email address<input type="email" name="email" required placeholder="staff@example.com"></label><label>Role<select name="role" required><option value="Receptionist">Receptionist</option><option value="Doctor">Doctor</option><option value="Admin">Admin</option></select></label><div class="form-action"><button class="btn primary">Send invitation</button></div></form></div>
<div class="panel"><div class="table-heading"><div><h3>All staff</h3><p class="muted"><?=count($rows)?> account(s)</p></div><input class="search-input" id="tableSearch" placeholder="Search staff..."></div><div class="table-wrap"><table data-search-table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Verification</th><th>Access</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><b><?=e($r['full_name'])?></b></td><td><?=e($r['email'])?></td><td><?=e($r['role'])?></td><td><span class="status <?=$r['is_verified']?'good':'pending'?>"><?=$r['is_verified']?'Verified':'Pending'?></span></td><td><span class="status <?=$r['is_active']?'good':'off'?>"><?=$r['is_active']?'Active':'Inactive'?></span></td><td><?php if((int)$r['id']!==(int)current_user()['id']): ?><form method="post" action="toggle_staff.php"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn small secondary"><?=$r['is_active']?'Deactivate':'Activate'?></button></form><?php else: ?><small>Current user</small><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php page_end(); ?>
