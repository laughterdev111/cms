<?php
// Load shared helpers and allow this page only for administrators.
require_once __DIR__.'/../app/bootstrap.php'; require_once __DIR__.'/../app/layout.php'; require_role(['Admin']);
page_start('System settings','settings.php');
?>
<div class="welcome-row"><div><p class="eyebrow">ADMINISTRATION</p><h2>System settings</h2><p class="muted">Manage the appearance and basic information of this workspace.</p></div></div>
<div class="settings-grid"><section class="panel"><div class="settings-title"><span class="settings-icon">◐</span><div><h3>Appearance</h3><p class="muted">Choose how the clinic system looks on this device.</p></div></div><div class="theme-choice-row"><div><b>Light theme</b><small>Bright background with forest-green accents.</small></div><button class="btn secondary small" type="button" data-set-theme="light">Use light</button></div><div class="theme-choice-row"><div><b>Dark theme</b><small>Dark green workspace, similar to the dark prototype.</small></div><button class="btn secondary small" type="button" data-set-theme="dark">Use dark</button></div><p class="muted settings-footnote">Your theme preference is saved in this browser and applies to all staff pages on this device.</p></section>
<section class="panel"><div class="settings-title"><span class="settings-icon">▣</span><div><h3>Clinic information</h3><p class="muted">Current application identity.</p></div></div><div class="settings-info"><small>APPLICATION NAME</small><b>Rwebeya Clinic Management System</b></div><div class="settings-info"><small>PORTAL TYPE</small><b>Role-based clinic workspace</b></div><div class="settings-info"><small>YOUR ACCESS</small><b>Administrator</b></div></section></div>
<?php page_end(); ?>
