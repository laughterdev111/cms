<?php
require_once __DIR__.'/../app/bootstrap.php'; require_once __DIR__.'/../app/layout.php'; require_role(['Doctor']);
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=(int)($_POST['consultation_id']??0);$sym=trim($_POST['symptoms']??'');$diag=trim($_POST['diagnosis']??'');$notes=trim($_POST['treatment_notes']??'');$status=$_POST['status']??'In Progress';
 if(!in_array($status,['In Progress','Completed'],true))$status='In Progress';
 $s=db()->prepare("UPDATE consultations SET symptoms=?,diagnosis=?,treatment_notes=?,status=?,started_at=COALESCE(started_at,NOW()),completed_at=IF(?='Completed',NOW(),NULL) WHERE id=? AND doctor_id=?");
 $s->execute([$sym,$diag,$notes,$status,$status,$id,current_user()['id']]);
 if($s->rowCount()>=0){audit('consultation_updated','consultation',$id);flash('success','Consultation notes saved.');}else flash('error','Could not update this consultation.');
 redirect('queue.php');
}
$s=db()->prepare("SELECT c.*,p.patient_number,p.full_name,p.phone FROM consultations c JOIN patients p ON p.id=c.patient_id WHERE c.doctor_id=? ORDER BY FIELD(c.status,'Waiting','In Progress','Completed'),c.created_at ASC");
$s->execute([current_user()['id']]);$rows=$s->fetchAll();
page_start('Consultation queue','queue.php');
?>
<div class="section-heading"><div><h2>Today's care workspace</h2><p class="muted">Review your assigned patients and record consultation notes.</p></div></div>
<?php foreach($rows as $r): ?><article class="panel consultation-card"><div class="consult-head"><div><span class="eyebrow"><?=e($r['patient_number'])?></span><h3><?=e($r['full_name'])?></h3><p class="muted"><?=e($r['phone'])?> · Added <?=e(date('d M, H:i',strtotime($r['created_at'])))?></p></div><span class="status <?=$r['status']==='Completed'?'good':'pending'?>"><?=e($r['status'])?></span></div><form method="post" class="form-stack"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="consultation_id" value="<?=(int)$r['id']?>"><label>Symptoms / observations<textarea name="symptoms" rows="2"><?=e($r['symptoms'])?></textarea></label><label>Diagnosis<textarea name="diagnosis" rows="2"><?=e($r['diagnosis'])?></textarea></label><label>Treatment / follow-up notes<textarea name="treatment_notes" rows="2"><?=e($r['treatment_notes'])?></textarea></label><div class="inline-actions"><select name="status"><option <?=$r['status']==='Waiting'?'selected':''?> value="In Progress">In progress</option><option <?=$r['status']==='Completed'?'selected':''?> value="Completed">Completed</option></select><button class="btn primary">Save consultation</button><a class="text-link" href="patient.php?id=<?=(int)$r['patient_id']?>">Patient history →</a></div></form></article><?php endforeach; ?>
<?php if(!$rows): ?><div class="panel empty">Your queue is empty. New appointments assigned to you will appear here.</div><?php endif; ?>
<?php page_end(); ?>
