<?php
// Load shared application services and the common page layout.
require_once __DIR__.'/../app/bootstrap.php';
require_once __DIR__.'/../app/layout.php';

// Only logged-in users may view or update this profile.
require_login();

// Get the current account ID and refresh its details from the database.
$userId=(int)current_user()['id'];
$refresh=db()->prepare('SELECT id,full_name,email,role,is_active,is_verified,avatar_path,created_at FROM users WHERE id=?');
$refresh->execute([$userId]);
$_SESSION['user']=$refresh->fetch() ?: current_user();

// Handle profile form submissions.
if($_SERVER['REQUEST_METHOD']==='POST'){
  // Reject the request if it does not contain a valid CSRF token.
  verify_csrf();
  $name=trim($_POST['full_name']??'');
  if($name==='') flash('error','Enter your full name.');
  else {
    try {
      // Keep the existing photo unless a valid replacement is uploaded.
      $photoPath=current_user()['avatar_path']??null;
      if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error']!==UPLOAD_ERR_NO_FILE){
        $file=$_FILES['profile_photo'];
        // Validate the uploaded file's status and size.
        if($file['error']!==UPLOAD_ERR_OK || $file['size']>2*1024*1024) throw new RuntimeException('Choose an image smaller than 2 MB.');
        // Confirm the actual MIME type, rather than trusting the filename.
        $finfo=new finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($file['tmp_name']);
        $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if(!isset($extensions[$mime])) throw new RuntimeException('Profile photos must be JPG, PNG or WEBP images.');
        // Create a unique, safely named file inside the avatars folder.
        $dir=__DIR__.'/uploads/avatars'; if(!is_dir($dir) && !mkdir($dir,0755,true)) throw new RuntimeException('Could not create the photo folder.');
        $filename='user_'.$userId.'_'.bin2hex(random_bytes(12)).'.'.$extensions[$mime];
        if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$filename)) throw new RuntimeException('The profile photo could not be saved.');
        $photoPath='uploads/avatars/'.$filename;
      }
      // Save the updated name and photo path to the database.
      $stmt=db()->prepare('UPDATE users SET full_name=?, avatar_path=? WHERE id=?');
      $stmt->execute([$name,$photoPath,$userId]);
      // Reload the account and store its latest values in the session.
      $fresh=db()->prepare('SELECT id,full_name,email,role,is_active,is_verified,avatar_path,created_at FROM users WHERE id=?'); $fresh->execute([$userId]); $_SESSION['user']=$fresh->fetch();
      // Record the update in the audit log and show a success message.
      audit('profile_updated','user',$userId); flash('success','Your profile has been updated.');
    } catch(PDOException $ex){ flash('error','Your profile could not be updated.'); }
      catch(Throwable $ex){ flash('error',$ex->getMessage() ?: 'Your profile could not be updated.'); }
  }
  // Redirect after POST to prevent the form from being submitted again.
  redirect('profile.php');
}

// Prepare page values and the two-character fallback profile initials.
$u=current_user(); $initials=strtoupper(implode('',array_map(fn($p)=>substr($p,0,1),array_slice(preg_split('/\s+/',trim($u['full_name']))?:['U'],0,2))));
page_start('My profile','');
?>
<!-- Profile heading and navigation -->
<div class="profile-page-heading"><div><p class="eyebrow">ACCOUNT INFORMATION</p><h2>My profile</h2><p class="muted">Update the personal details associated with your staff account.</p></div><a class="btn secondary" href="index.php">← Back to dashboard</a></div>
<!-- Profile photo preview and editable account details -->
<div class="profile-page-grid"><section class="panel profile-photo-panel"><h3>Profile photo</h3><div class="profile-photo-preview"><?php if(!empty($u['avatar_path'])): ?><img src="<?=e($u['avatar_path'])?>" alt="Your profile photo"><?php else: ?><span><?=$initials?></span><?php endif; ?></div><p class="muted">Use a clear JPG, PNG or WEBP image. Maximum size: 2 MB.</p></section>
<section class="panel"><h3>Personal details</h3><form method="post" enctype="multipart/form-data" class="form-stack" data-track-changes><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Full name<input name="full_name" value="<?=e($u['full_name'])?>" maxlength="120" required></label><label>Email address<input type="email" value="<?=e($u['email'])?>" readonly><small class="field-help">Email changes require a separate verification process.</small></label><label>Upload profile photo<input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"></label><div class="account-readonly"><span><small>ACCOUNT ROLE</small><b><?=e($u['role'])?></b></span><span><small>MEMBER SINCE</small><b><?=e(date('d M Y',strtotime($u['created_at'])))?></b></span></div><button class="btn primary" type="submit">Save profile changes</button></form></section></div>
<?php page_end(); ?>
