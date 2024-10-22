<?php

 require_once 'support_file.php';
 $page = 'account_settings.php';
 $title='Change Password';
 $table='users';
 $getOldPassword = find_a_field('users','password','user_id='.$_SESSION['userid']);
 $enat=date('Y-m-d h:i:s');
 if(isset($_POST['changePASS'])){
  $valid = true;
 	if ($getOldPassword!==$_POST['old_password'])
  {echo "<script> alert('Invalid Old Password!!') </script>";
         $valid = false;}
if ($valid){
 unset($_SESSION['PASSCODE']);
 $passHash = password_hash("".$_POST['new_password']."", PASSWORD_DEFAULT);

 $previousPasswordInactive = mysqli_query($conn, "UPDATE users_password_logs SET status='inactive' WHERE user_id='".$_SESSION['userid']."'");
 $insertPasswordLogs = mysqli_query($conn, "INSERT INTO users_password_logs (user_id,password,password_encryption,status,ip_address,created_at,section_id,company_id) 
VALUES ('".$_SESSION['userid']."','".$_POST['new_password']."','".$passHash."','active','".$ip."','".$enat."','".$_SESSION['sectionid']."','".$_SESSION['companyid']."')");
 $insert=mysqli_query($conn, "UPDATE  users SET password='".$_POST['new_password']."',passwords='".$passHash."' where user_id='".$_SESSION['userid']."' ");
  $_SESSION["PASSCODE"]	=$_POST['new_password'];
    session_destroy();
    unset($_POST);
    header('Location: ../pages/');
}}?>

<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content.php';?>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?=$title;?></h2>
                    <a style="float: right" class="btn btn-sm btn-default"  href="account_settings_warehouse.php">
                        <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000; font-size: 11px">Change Default Warehouse</span>
                    </a>
                      <a style="float: right" class="btn btn-sm btn-default"  href="account_settings_change_default_branch.php">
                          <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000; font-size: 11px">Change Default Branch</span>
                      </a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
<form  name="addem" id="addem" style="font-size:11px" class="form-horizontal form-label-left" method="post">
<div class="form-group">
  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Old Password<span class="required">*</span></label>
  <div class="col-md-6 col-sm-6 col-xs-12">
	<input type="text" id="old_password" style="width:400px"  required  name="old_password"  class="form-control col-md-7 col-xs-12" >
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">New Password<span class="required">*</span></label>
  <div class="col-md-6 col-sm-6 col-xs-12">
	<input type="text" id="new_password" style="width:400px"  required  name="new_password"  class="form-control col-md-7 col-xs-12" >
  </div>
</div>
<div class="form-group" style="margin-left:40%">
               <div class="col-md-6 col-sm-6 col-xs-12">
               <button type="submit" name="changePASS" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary">Change Password</button>
               </div></div>
             </form>
         </div>
     </div>
 </div>
<?=$html->footer_content();?>
