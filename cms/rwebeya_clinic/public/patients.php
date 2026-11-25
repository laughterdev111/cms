<?php
// Load shared helpers and restrict patient directory access.
require_once __DIR__.'/../app/bootstrap.php'; require_once __DIR__.'/../app/layout.php';
require_role(['Receptionist','Doctor']);
// Receptionists register submitted patient details.
if ($_SERVER['REQUEST_METHOD']==='POST') {
  require_role(['Receptionist']); verify_csrf();
  $name=trim($_POST['full_name']??''); $phone=trim($_POST['phone']??''); $dob=$_POST['date_of_birth']??''; $gender=$_POST['gender']??''; $insurance=$_POST['insurance_provider']??'None'; $insno=trim($_POST['insurance_number']??'');
  if($name==='' || $phone==='' || !in_array($gender,['Female','Male','Other','Prefer not to say'],true) || !in_array($insurance,['None','RSSB','RAMA','Other'],true)) flash('error','Complete the required patient details.');
  else {
    try {
      $number='RWC-'.date('ym').'-'.strtoupper(bin2hex(random_bytes(3)));
      $stmt=db()->prepare('INSERT INTO patients(patient_number,full_name,phone,date_of_birth,gender,insurance_provider,insurance_number,registered_by) VALUES(?,?,?,?,?,?,?,?)');
      $stmt->execute([$number,$name,$phone,$dob!==''?$dob:null,$gender,$insurance,$insno!==''?$insno:null,current_user()['id']]);
      audit('patient_registered','patient',(int)db()->lastInsertId()); flash('success','Patient registered successfully.');
    } catch(Throwable $ex) { flash('error','Patient could not be saved. Check the information and try again.'); }
  }
  redirect('patients.php');
}
// Search patient records by name, patient number, or phone number.
$q=trim($_GET['q']??''); $stmt=db()->prepare('SELECT p.*,u.full_name AS registered_by_name FROM patients p JOIN users u ON u.id=p.registered_by WHERE p.full_name LIKE ? OR p.patient_number LIKE ? OR p.phone LIKE ? ORDER BY p.created_at DESC LIMIT 200');
$like='%'.$q.'%'; $stmt->execute([$like,$like,$like]); $rows=$stmt->fetchAll();
page_start('Patients','patients.php');
?>
<div class="section-heading"><div><h2>Patient directory</h2><p class="muted">Register patients and find existing records.</p></div></div>
<?php if(in_array(current_user()['role'],['Admin','Receptionist'],true)): ?><div class="panel"><h3>Register a new patient</h3><form method="post" class="form-grid" data-track-changes><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Full name<input name="full_name" required maxlength="150"></label><label>Phone number<input name="phone" required maxlength="40"></label><label>Date of birth<input type="date" name="date_of_birth"></label><label>Gender<select name="gender" required><option value="">Choose...</option><option>Female</option><option>Male</option><option>Other</option><option>Prefer not to say</option></select></label><label>Insurance provider<select name="insurance_provider"><option>None</option><option>RSSB</option><option>RAMA</option><option>Other</option></select></label><label>Insurance number (optional)<input name="insurance_number" maxlength="80"></label><div class="form-action"><button class="btn primary">Save patient</button></div></form></div><?php endif; ?>
<div class="panel"><div class="table-heading"><div><h3>Registered patients</h3><p class="muted"><?=count($rows)?> result(s)</p></div><form method="get"><input class="search-input" name="q" value="<?=e($q)?>" placeholder="Search name, number or phone"></form></div><div class="table-wrap"><table><thead><tr><th>Patient number</th><th>Full name</th><th>Phone</th><th>Insurance</th><th>Registered</th><th>Record</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><b><?=e($r['patient_number'])?></b></td><td><?=e($r['full_name'])?></td><td><?=e($r['phone'])?></td><td><span class="insurance"><?=e($r['insurance_provider'])?></span></td><td><?=e(date('d M Y',strtotime($r['created_at'])))?></td><td><a class="text-link" href="patient.php?id=<?=(int)$r['id']?>">View profile →</a></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="6" class="empty">No patient records found.</td></tr><?php endif; ?></tbody></table></div></div>
<?php page_end(); ?>
