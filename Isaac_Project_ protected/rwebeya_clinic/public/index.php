<?php
require_once __DIR__.'/../app/bootstrap.php';
require_once __DIR__.'/../app/layout.php';
require_login();
$role = current_user()['role'];
if ($role === 'Admin') {
  $staff = (int)db()->query("SELECT COUNT(*) FROM users")->fetchColumn();
  $active = (int)db()->query("SELECT COUNT(*) FROM users WHERE is_active=1 AND is_verified=1")->fetchColumn();
  $pending = (int)db()->query("SELECT COUNT(*) FROM users WHERE is_verified=0")->fetchColumn();
  $recent = db()->query("SELECT full_name,email,role,is_verified,is_active,created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
} elseif ($role === 'Receptionist') {
  $patients = (int)db()->query("SELECT COUNT(*) FROM patients")->fetchColumn();
  $today = (int)db()->query("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_at)=CURDATE()")->fetchColumn();
  $staff = $pending = 0;
} else {
  $queue = (int)db()->query("SELECT COUNT(*) FROM consultations WHERE status IN ('Waiting','In Progress')")->fetchColumn();
  $done = (int)db()->query("SELECT COUNT(*) FROM consultations WHERE status='Completed' AND DATE(completed_at)=CURDATE()")->fetchColumn();
}
page_start('Admin dashboard','index.php');
?>
<?php if ($role==='Admin'): ?>
<div class="welcome-row"><div><p class="eyebrow">ADMINISTRATIVE WORKSPACE</p><h2>Welcome back, <?=e(current_user()['full_name'])?></h2><p class="muted">Manage staff, review clinic reports and configure the system.</p></div><span class="date-chip"><?=date('D, d M Y')?></span></div>
<div class="stats-grid admin-stats">
 <article class="stat-card"><span class="stat-icon">♙</span><p>Total staff accounts</p><strong><?=$staff?></strong><small>All registered staff</small></article>
 <article class="stat-card"><span class="stat-icon">✓</span><p>Active verified staff</p><strong><?=$active?></strong><small>Accounts ready to use</small></article>
 <article class="stat-card"><span class="stat-icon">◷</span><p>Pending verification</p><strong><?=$pending?></strong><small>Invitations awaiting verification</small></article>
</div>
<div class="admin-content-grid">
 <section class="panel recent-staff"><div class="section-heading"><div><h3>Recently added staff</h3><p class="muted">Latest accounts created in the clinic system.</p></div><a class="text-link" href="staff.php">View all staff →</a></div>
 <div class="table-wrap"><table><thead><tr><th>Staff member</th><th>Role</th><th>Account status</th></tr></thead><tbody>
 <?php foreach($recent as $r): ?><tr><td><b><?=e($r['full_name'])?></b><small class="table-subtext"><?=e($r['email'])?></small></td><td><?=e($r['role'])?></td><td><span class="status <?=$r['is_verified'] && $r['is_active']?'good':'pending'?>"><?=$r['is_verified']?($r['is_active']?'Active':'Inactive'):'Pending verification'?></span></td></tr><?php endforeach; ?>
 <?php if(!$recent): ?><tr><td colspan="3" class="empty">No staff accounts yet.</td></tr><?php endif; ?></tbody></table></div></section>
 <section class="panel admin-shortcuts"><h3>Administration</h3><p class="muted">Choose an administrative task.</p>
 <a class="admin-action" href="staff.php"><span class="action-symbol">＋</span><span><b>Staff management</b><small>Invite staff and manage account access</small></span><span class="action-arrow">→</span></a>
 <a class="admin-action" href="reports.php"><span class="action-symbol">▥</span><span><b>Clinic reports</b><small>Review high-level clinic activity</small></span><span class="action-arrow">→</span></a>
 <a class="admin-action" href="settings.php"><span class="action-symbol">⚙</span><span><b>System settings</b><small>Manage your system preferences</small></span><span class="action-arrow">→</span></a>
 </section>
</div>
<div class="notice"><b>Administrator access</b><p>You have access to staff administration, system settings and reports. Patient registration, appointment scheduling and clinical work are handled in the Receptionist and Doctor workspaces.</p></div>
<?php elseif ($role==='Receptionist'): ?>
<div class="welcome-row"><div><p class="muted">Welcome back, <?=e(current_user()['full_name'])?>.</p><h2>Your clinic at a glance</h2><p class="muted">Manage patient registration and appointments.</p></div><span class="date-chip"><?=date('D, d M Y')?></span></div>
<div class="stats-grid"><article class="stat-card"><span class="stat-icon">♡</span><p>Registered patients</p><strong><?=$patients?></strong><small>Patient directory</small></article><article class="stat-card"><span class="stat-icon">▦</span><p>Appointments today</p><strong><?=$today?></strong><small>Today's schedule</small></article></div>
<div class="quick-grid"><a class="quick-card" href="patients.php"><span>＋</span><b>Register a patient</b><small>Add a patient to the directory</small></a><a class="quick-card" href="appointments.php"><span>▦</span><b>Appointments</b><small>View and schedule visits</small></a></div>
<?php else: ?>
<div class="welcome-row"><div><p class="muted">Welcome back, <?=e(current_user()['full_name'])?>.</p><h2>Your clinical workspace</h2><p class="muted">Review your consultation work for today.</p></div><span class="date-chip"><?=date('D, d M Y')?></span></div>
<div class="stats-grid"><article class="stat-card"><span class="stat-icon">◷</span><p>Active queue</p><strong><?=$queue?></strong><small>Waiting or in progress</small></article><article class="stat-card"><span class="stat-icon">✓</span><p>Completed today</p><strong><?=$done?></strong><small>Consultations completed</small></article></div>
<div class="quick-grid"><a class="quick-card" href="queue.php"><span>✚</span><b>Open consultation queue</b><small>Review waiting patients</small></a></div>
<?php endif; ?>
<?php page_end(); ?>
