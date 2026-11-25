<?php
// Load layout helpers and restrict reports to administrators.
require_once __DIR__.'/../app/bootstrap.php';require_once __DIR__.'/../app/layout.php';require_role(['Admin']);
// Collect simple operational totals for the summary cards.
$patients=(int)db()->query('SELECT COUNT(*) FROM patients')->fetchColumn();$staff=(int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();$consult=(int)db()->query('SELECT COUNT(*) FROM consultations')->fetchColumn();$appointments=(int)db()->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
page_start('Reports','reports.php');
?><div class="section-heading"><div><h2>System summary</h2><p class="muted">Basic operational counts from the database.</p></div></div><div class="stats-grid"><?php foreach(['Staff accounts'=>$staff,'Patients'=>$patients,'Appointments'=>$appointments,'Consultations'=>$consult] as $label=>$n): ?><article class="stat-card"><p><?=e($label)?></p><strong><?=$n?></strong></article><?php endforeach; ?></div><div class="notice"><b>Reporting limitation</b><p>This starter provides simple totals only. Add approved reporting rules and privacy controls before using operational reports.</p></div><?php page_end(); ?>
