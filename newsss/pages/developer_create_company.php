<?php require_once 'support_file.php'; ?>
<?=(($_SESSION["userlevel"])==1)? '' : header('Location: page_403.php');
$now=time();
$unique='id';
$unique_field='company_id';
$table="company";
$page="developer_create_company.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$title='Create Company';

if(prevent_multi_submit()){

    if(isset($_POST['record']))
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;}
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }


            $_POST['status']=1;
            $crud->insert();
            $type=1;
            $msg='New Entry Successfully Inserted.';
            unset($_POST);
        }

//for modify..................................
        if(isset($_POST['modify']))
        {
            $_POST['edit_at']=time();
            $_POST['edit_by']=$_SESSION['userid'];
            $crud->update($unique);
            echo "<script>self.opener.location = '$page'; self.blur(); </script>";
            echo "<script>window.close(); </script>";
        }
    }

if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}


$res="SELECT id,section_name,company_name,com_short_name,address,contact_person,contact_number,website,VAT_regno,logo,logo_color,TIN,BIN,Trade_license_no,telephone,IF(status=1, 'Active','Inactive') as status from ".$table." order by id";
$result=mysqli_query($conn, $res);
while($data=mysqli_fetch_object($result)){
    $id=$data->ZONE_CODE;

    if(isset($_POST['deletedata'.$id]))
    { $del=mysqli_query($conn, "Delete from ".$table." where ".$unique."=".$id."");}
}?>



<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=600,height=900,left = 350,top = 5");}
</script>
<?php if(isset($_GET[$unique])):
    require_once 'body_content_without_menu.php'; else :
    require_once 'body_content.php'; endif;  ?>


<?php if(isset($_GET[$unique])): ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2>
            <ul class="nav navbar-right panel_toolbox">
                <div class="input-group pull-right"></div>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php else: ?>

            <div class="modal fade" id="addModal">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add New
                                <button class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </h5>
                        </div>
                        <div class="modal-body">
                            <?php endif; ?>
                            <form name="addem" id="addem" class="form-horizontal form-label-left" style="font-size: 11px" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Section Id <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="hidden" class="form-control" value="<?=$_GET[$unique]?>" style="font-size: 11px" name="<?=$unique?>">
                                        <input type="hidden" class="form-control" value="<?=$_SESSION['companyid']?>" style="font-size: 11px" name="company_id">
                                        <input type="text" readonly class="form-control" value="<?=$section_id?>" style="font-size: 11px" name="section_id">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Section Name <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$section_name?>" name="section_name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Sister Concern Name <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$company_name?>" name="company_name" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Sister Concern Short Name</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$com_short_name?>" name="com_short_name" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Address</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <textarea class="form-control" style="font-size: 11px" name="address"><?=$address?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Contact Person</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$contact_person?>" name="contact_person" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Contact Number</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$contact_number?>" name="contact_number" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Website</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="website" class="form-control" style="font-size: 11px" value="<?=$website?>" name="website" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">VAT Reg. No</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$VAT_regno?>" name="VAT_regno" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">TIN</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$TIN?>" name="TIN" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">BIN</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$BIN?>" name="BIN" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Trade License No</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$Trade_license_no?>" name="Trade_license_no" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Telephone</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="text" class="form-control" style="font-size: 11px" value="<?=$telephone?>" name="telephone" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Logo</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="file" class="form-control" style="font-size: 11px" value="<?=$telephone?>" name="telephone" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Logo Color</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="website" class="form-control" style="font-size: 11px" value="<?=$telephone?>" name="telephone" />
                                    </div>
                                </div>

                                <?php if(isset($_GET[$unique])): ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Status<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                            <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="status">
                                                <option></option>
                                                <?=foreign_relation('status', 'id', 'name', $status, 'status=1'); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <hr>

                                <?php if($_GET[$unique]):  ?>
                                    <div class="form-group" style="margin-left:40%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" style="font-size:12px" class="btn btn-danger" onclick="self.close()">Close</button>
                                            <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-primary">Modify</button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="form-group" style="margin-left:40%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <a style="font-size:12px" class="btn btn-danger" data-dismiss="modal">Close</a>
                                            <button type="submit" name="record" id="record"  style="font-size:12px" class="btn btn-primary">Add New</button>
                                        </div>
                                    </div> <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if(!isset($_GET[$unique])): ?>
            </div>
        <?php endif; ?>
            <?php if(!isset($_GET[$unique])):?>
                <?=$crud->report_templates_with_add_new($res,$title,12,$action=$_SESSION["userlevel"],$create=1);?>
            <?php endif; ?>
            <?=$html->footer_content();mysqli_close($conn);?>
            <?php ob_end_flush();ob_flush(); ?>
