<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title="Vendor Type";

$now=time();
$unique='id';
$unique_field='vendor_type';
$table="vendor_type";
$page="vendor_type.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))

//for insert..................................
{    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        unset($_POST);
        unset($$unique);
    }
    
    
//for modify..................................
if(isset($_POST['modify']))
{
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
    $crud->update($unique);
    $type=1;
    //echo $targeturl;
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

//for Delete..................................
if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}
$vendor_type = @$vendor_type;
?>



<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content.php'; ?>









 <?php if(!isset($_GET['id'])){ ?>
     <!-------------------list view ------------------------->
     <div class="col-md-6 col-sm-12 col-xs-12">
         <div class="x_panel">
             <div class="x_title">
                 <h2>List of <?=$title;?></h2>
                 <div class="clearfix"></div>
             </div>

             <div class="x_content">
                 <? 	$res='select '.$unique.','.$unique.' as Code,'.$unique_field.' from '.$table.' order by '.$unique;
                 echo $crud->link_report_popup($res,$link);?>
                 <?=paging(10);?>
             </div>

         </div></div>
     <!-------------------End of  List View --------------------->
 <?php } ?>
 <!---page content----->


                    <!-- input section-->
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?=$title;?></h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <div class="input-group pull-right">
                                        <!--a target="_new" class="btn btn-sm btn-default"  href="user_permission2.php">
                                            <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000">Uer Permission (SUB)</span>
                                        </a-->
                                    </div>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br />

                                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                                    <?require_once 'support_html.php';?>


                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Name<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="vendor_type" style="width:100%"  required   name="vendor_type" value="<?=$vendor_type;?>" class="form-control col-md-7 col-xs-12" >
                                    </div></div>
                                    
                                    <br><br><br>

                                        <br>
                                        <?php if(@$_GET['id']){  ?>
                                            <? if($_SESSION['userlevel']==5){?>                                            
                                             <div class="form-group" style="margin-left:40%; display: none">
                                             <div class="col-md-6 col-sm-6 col-xs-12">
                                             <input  name="delete" type="submit" class="btn btn-success" id="delete" value="Delete"/>
                                             </div></div>                                             
                                             <? }?>
                                            <div class="form-group" style="margin-left:40%">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="modify" id="modify" class="btn btn-success">Modify</button>
                                            </div></div>                                            
                                            <?php } else {?>                                           
                                            <div class="form-group" style="margin-left:40%">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="record" id="record"  class="btn btn-success">Add New </button>
                                            </div></div>                                                                                        
                                            <?php } ?> 
                                </form>
                            </div>
                        </div>
                    </div>
<?=$html->footer_content();mysqli_close($conn);?>        
