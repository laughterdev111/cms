<?php
declare(strict_types=1);
// Render the shared page header, navigation, top bar, and flash alerts.
function page_start(string $title, string $active = ''): void {
    $u = current_user(); $flash = take_flash();
    $initials = strtoupper(implode('', array_map(fn($part)=>substr($part,0,1), array_slice(preg_split('/\s+/', trim($u['full_name'] ?? 'U')) ?: ['U'],0,2))));
    $avatar = !empty($u['avatar_path']) ? $u['avatar_path'] : '';
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=e($title)?> · Rwebeya Clinic</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <div class="app-shell">
    <?php if ($u): ?><aside class="sidebar"><a class="brand" href="index.php"><span class="brand-mark">R</span><span>Rwebeya<small>CLINIC PORTAL</small></span></a>
      <div class="side-label"><?= $u['role']==='Admin' ? 'ADMINISTRATION' : 'WORKSPACE' ?></div>
      <nav>
      <?php
      $links = [];
      if ($u['role']==='Admin') $links = [['index.php','Dashboard'],['staff.php','Staff management'],['reports.php','Reports'],['settings.php','System settings']];
      if ($u['role']==='Receptionist') $links = [['index.php','Dashboard'],['patients.php','Patient directory'],['appointments.php','Appointments']];
      if ($u['role']==='Doctor') $links = [['index.php','Dashboard'],['queue.php','Consultation queue'],['patients.php','Patient directory'],['appointments.php','Appointments']];
      foreach ($links as [$href,$label]): ?>
        <a class="nav-link <?=$active===$href?'active':''?>" href="<?=e($href)?>"><span class="nav-icon" aria-hidden="true"><?=match($href){'index.php'=>'⌂','staff.php'=>'♙','reports.php'=>'▥','settings.php'=>'⚙','patients.php'=>'♡','appointments.php'=>'▦','queue.php'=>'✚',default=>'•'}?></span><?=e($label)?></a>
      <?php endforeach; ?>
      </nav>
      <div class="sidebar-bottom"><div class="sidebar-note"><span class="security-dot"></span><span>Secure staff workspace</span></div></div>
    </aside><?php endif; ?>
    <main class="main-area">
      <?php if ($u): ?><header class="topbar"><div class="topbar-heading"><span class="eyebrow">RWEBEYA CLINIC</span><h1><?=e($title)?></h1></div><div class="top-actions">
      <button class="theme-toggle" type="button" id="themeToggle" aria-label="Switch to dark theme"><span class="theme-icon">◐</span><span class="theme-label">Dark mode</span></button>
      <details class="profile-menu"><summary class="profile-trigger" aria-label="Open account details"><?php if($avatar): ?><img class="top-avatar" src="<?=e($avatar)?>" alt="Profile photo"><?php else: ?><span class="top-avatar"><?=e($initials)?></span><?php endif; ?><span class="profile-trigger-text"><b><?=e($u['full_name'])?></b><small><?=e($u['role'])?></small></span><span class="profile-chevron">⌄</span></summary>
      <div class="profile-dropdown"><div class="profile-dropdown-head"><?php if($avatar): ?><img class="profile-avatar-large" src="<?=e($avatar)?>" alt="Profile photo"><?php else: ?><span class="profile-avatar-large"><?=e($initials)?></span><?php endif; ?><div><b><?=e($u['full_name'])?></b><small><?=e($u['role'])?> account</small></div></div><div class="profile-detail"><small>EMAIL ADDRESS</small><span><?=e($u['email'])?></span></div><div class="profile-detail"><small>ACCOUNT ROLE</small><span><?=e($u['role'])?></span></div><a class="profile-edit-link" href="profile.php">View / edit profile <span>→</span></a><form method="post" action="logout.php" class="logout-form" data-logout-form><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><button type="submit" class="logout-button">↪ &nbsp; Log out</button></form></div></details>
      </div></header><?php endif; ?>
      <section class="page-content">
      <?php if ($flash): ?><div class="alert <?=e($flash['type'])?>"><?=e($flash['message'])?></div><?php endif; ?>
    <?php
}
function page_end(): void {
    ?></section><footer class="footer">Rwebeya Clinic Management System <span>· Educational starter project</span></footer></main></div><script src="assets/app.js"></script></body></html><?php
}
