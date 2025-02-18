<?php require_once 'support_file.php'; ?>
<?=$html->header_content('Dashboard');?>
<?php require_once 'body_content.php'; ?>
<?php
if (empty($passwordHashCheck)) {?>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2 class="text-danger">Your password has expired!! Please reset your password to get the module access.</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form  name="addem" id="addem" style="font-size:11px" class="form-horizontal form-label-left" action="account_settings.php" method="post">
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

<?php } else { ?>
<div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="x_panel">
                  <div class="x_title">
                    <h2><?php if($_SESSION['language']=='Bangla') : ?>আপনার মডিউল এবং মেনু পছন্দ করুন <?php else: ?>   Choose your Module & Menu <?php endif;?></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                  <?=$crud->dashboard_modules($module_get,$url_current,$link);?> 
                  </div>
                </div>
              </div>
<?php if (isset($_SESSION['module_id']) && $_SESSION['module_id'] != 11) {?>
              <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="x_panel">
                  <div class="x_content">
                  <?=$crud->dashboard_quick_access_menu($main_manu_get,$url_current,$link);?> 
                  </div>
                </div>
              </div>
              <?php } else {} ?>
<?php if(@$_SESSION['module_id']>0):
require_once("toptitle_".@$_SESSION['module_name'].".php"); else :
endif; ?>


<?php } ?>
<?=$html->footer_content();?>