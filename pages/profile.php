<?php require_once 'support_file.php';
$users = find_all_field('users','','user_id='.$_SESSION['userid']);
$title = 'Profile';

$usersFname = @$users->fname;
$usersEmail = @$users->email;
$usersMobile = @$users->mobile;
$usersDesignation = @$users->designation;
$usersStatus = @$users->account_status;
$usersPP = @$users->picture_url;

?>
<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content.php'; ?>


    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>User Details</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br />
                <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name"></label>
                        <div class="col-md-6 col-sm-6 ">
                            <img src="<?=$usersPP;?>" width="100" height="100">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">User ID</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$_SESSION['userid']?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">User Name</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersFname?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Email</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersEmail?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Mobile</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersMobile?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Designation</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersDesignation?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Status</label>
                        <div class="col-md-6 col-sm-6 ">
                            <?php if($usersStatus=='active') { ?>
                                <span class="label label-success" style="font-size:10px">Active</span>
                            <?php } elseif($usersStatus=='inactive') { ?>
                                <span class="label label-danger" style="font-size:10px">Inactive</span>
                            <?php } else { ?>
                                <span class="label label-danger" style="font-size:10px">Banned</span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a class="btn btn-danger" href="dashboard.php">Cancel</a>
                            <button class="btn btn-primary" type="reset">Reset</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?=$html->footer_content();?>