<?php require_once 'support_file.php'; ?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$now=time();
$unique='BRANCH_ID';
$unique_field='BRANCH_NAME';
$table="branch";
$page="sales_market_setup_region.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$title='Region Setup';

if(prevent_multi_submit()){
    if(isset($_POST[$unique_field]))
    {    $$unique = $_POST[$unique];
        if(isset($_POST['record']))
        {
            $_POST['status']=1;
            $crud->insert();
            $type=1;
            $msg='New Entry Successfully Inserted.';
            //unset($_POST);
        }

//for modify..................................
        if(isset($_POST['modify']))
        {
            $_POST['edit_at']=time();
            $_POST['edit_by']=$_SESSION['userid'];
            $crud->update($unique);
            $type=1;
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

if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}
$BRANCH_NAME = @$BRANCH_NAME;
$BRANCH_ID = @$BRANCH_ID;
$branch_rsm_name = @$branch_rsm_name;
$sql = "SELECT typeshorname, typedetails from distributor_type
where 1 order by typedetails";
$sql_item = "SELECT item_id, concat(item_id,' : ',finish_goods_code,' : ', item_name) from item_info
                        where product_nature in ('Salable','Both') order by finish_goods_code";
$res="SELECT r.BRANCH_ID,r.BRANCH_NAME as Region_name,(select PBI_NAME from personnel_basic_info where PBI_ID=r.branch_rsm_name) as Regional_Manager,if(r.status>0, 'Active','Inactive') as status from ".$table." r where 1";
$result=mysqli_query($conn, $res);
while($data=mysqli_fetch_object($result)){
    $id=$data->BRANCH_ID;

    if(isset($_POST['deletedata'.$id]))
    {
        //$del=mysqli_query($conn, "Delete from ".$table." where ".$unique."=".$id."");
    }
}

$sql_user_id="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') FROM 						 
personnel_basic_info p,
department d,
designation des
 where p.PBI_JOB_STATUS='In Service' and 							 
 p.PBI_DEPARTMENT=d.DEPT_ID and 
 p.PBI_DESIGNATION=des.DESG_ID	 
  order by p.PBI_NAME";
?>



<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=320,left = 230,top = 5");}
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
                            <form  name="addem" id="addem" class="form-horizontal form-label-left" style="font-size: 11px" method="post">
                                <? require_once 'support_html.php';?>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Region Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <input type="hidden" id="<?=$unique;?>" style="width:100%; font-size: 12px"  required   name="<?=$unique;?>" value="<?=$$unique;?>" class="form-control col-md-7 col-xs-12" >
                                        <input type="text" id="BRANCH_NAME" style="width:100%; font-size: 12px"  required   name="BRANCH_NAME" value="<?=$BRANCH_NAME;?>" class="form-control col-md-7 col-xs-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Regional Manager<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                                        <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="branch_rsm_name" id="branch_rsm_name">
                                            <option></option>
                                            <?=advance_foreign_relation($sql_user_id,$branch_rsm_name);?>
                                        </select>
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

                                <?php if(@$_GET[$unique]):  ?>
                                    <div class="form-group" style="margin-left:40%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-danger" onclick="self.close()">Close</button>
                                            <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-primary">Modify</button>
                                        </div></div>
                                <?php else : ?>
                                    <div class="form-group" style="margin-left:40%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <a name="modify"  style="font-size:12px" class="btn btn-danger" data-dismiss="modal">Close</a>
                                            <button type="submit" name="record" id="record"  style="font-size:12px" class="btn btn-primary">Add New</button></div></div> <?php endif; ?>
                            </form>
                        </div></div></div><?php if(!isset($_GET[$unique])): ?></div><?php endif; ?>
            <?php if(!isset($_GET[$unique])):?>
                <?=$crud->report_templates_with_add_new($res,$title,12,$action=$_SESSION["userlevel"],$create=1,'');?>
            <?php endif; ?>
            <?=$html->footer_content();mysqli_close($conn);?>