<?php
// Load shared helpers and restrict patient profiles to clinical roles.
require_once __DIR__.'/../app/bootstrap.php'; require_once __DIR__.'/../app/layout.php'; require_role(['Receptionist','Doctor']);
// Read the selected patient and return 404 if the record does not exist.
$id=(int)($_GET['id']??0); $s=db()->prepare('SELECT p.*,u.full_name AS registered_by_name FROM patients p JOIN users u ON u.id=p.registered_by WHERE p.id=?'); $s->execute([$id]); $p=$s->fetch();
if(!$p){http_response_code(404);exit('Patient not found.');}
// Load the patient's consultation and appointment histories.
$c=db()->prepare('SELECT c.*,u.full_name AS doctor_name FROM consultations c JOIN users u ON u.id=c.doctor_id WHERE c.patient_id=? ORDER BY c.created_at DESC');$c->execute([$id]);$consults=$c->fetchAll();
$a=db()->prepare('SELECT * FROM appointments WHERE patient_id=? ORDER BY appointment_at DESC LIMIT 20');$a->execute([$id]);$apps=$a->fetchAll();
page_start('Patient profile','patients.php');
?>
<div class="section-heading"><div><a class="text-link" href="patients.php">← Back to patients</a><h2><?=e($p['full_name'])?></h2><p class="muted"><?=e($p['patient_number'])?></p></div></div>
<div class="stats-grid"><article class="stat-card"><p>Phone</p><strong class="small-strong"><?=e($p['phone'])?></strong></article><article class="stat-card"><p>Insurance</p><strong class="small-strong"><?=e($p['insurance_provider'])?></strong></article><article class="stat-card"><p>Date of birth</p><strong class="small-strong"><?=e($p['date_of_birth']?:'Not provided')?></strong></article><article class="stat-card"><p>Gender</p><strong class="small-strong"><?=e($p['gender'])?></strong></article></div>
<div class="panel"><h3>Consultation history</h3><div class="table-wrap"><table><thead><tr><th>Date</th><th>Doctor</th><th>Symptoms</th><th>Diagnosis</th><th>Status</th></tr></thead><tbody><?php foreach($consults as $r): ?><tr><td><?=e($r['created_at'])?></td><td><?=e($r['doctor_name'])?></td><td><?=e($r['symptoms']??'')?></td><td><?=e($r['diagnosis']??'')?></td><td><?=e($r['status'])?></td></tr><?php endforeach; ?><?php if(!$consults): ?><tr><td colspan="5" class="empty">No consultation history.</td></tr><?php endif; ?></tbody></table></div></div>
<div class="panel"><h3>Appointment history</h3><div class="table-wrap"><table><thead><tr><th>Date and time</th><th>Reason</th><th>Status</th></tr></thead><tbody><?php foreach($apps as $r): ?><tr><td><?=e($r['appointment_at'])?></td><td><?=e($r['reason']??'')?></td><td><?=e($r['status'])?></td></tr><?php endforeach; ?><?php if(!$apps): ?><tr><td colspan="3" class="empty">No appointments.</td></tr><?php endif; ?></tbody></table></div></div>
<?php page_end(); ?>
