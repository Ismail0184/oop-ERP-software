<?php require_once 'support_file.php';
$now=time();
$unique='id';
$unique_field='purpose';
$table="emp_access_IOU_request";
$page="emp_access_requisition_IOU.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$GetUnique = @$_GET[$unique];
$title='IOU Requests';

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))

//for insert..................................
{    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        //unset($_POST);
        unset($$unique);
    }

//for modify..................................
if(isset($_POST['modify']))
{
    $crud->update($unique);
    $type=1;
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

//for Delete..................................
if(isset($_POST['deleteRequest']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}

} // prevent multi submit

// data query..................................
if(isset($$unique)) {
    $condition = $unique . "=" . $$unique;
    $data = db_fetch_object($table, $condition);
    $array = (array)$data;
    foreach ($array as $key => $value) {
        $$key = $value;
    }
}

$purpose        = @$purpose;
$date           = @$date;
$time           = @$time;
$amount         = @$amount;
$recommended_by = @$recommended_by;
$approved_by    = @$approved_by;

$res="SELECT id,purpose,concat(date,', ',time) as when_need,FORMAT(amount,2) as amount,status from ".$table." where user_id=".$_SESSION['PBI_ID'];
?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?id="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=700,left = 250,top = -1");}
    </script>
<?php if(isset($_GET[$unique])):
    require_once 'body_content_without_menu.php'; else :
    require_once 'body_content.php'; endif;  ?>


<?php if(isset($_GET[$unique])): ?>
    <div class="col-md-5 col-sm-12 col-xs-12">
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
        <h5 class="modal-title">Create IOU
            <button class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
        </h5>
    </div>
    <div class="modal-body">
<?php endif; ?>
    <form  name="addem" class="form-horizontal form-label-left" style="font-size: 11px" method="post">
        <? require_once 'support_html.php';?>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">IOU Purpose <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="hidden" style="width:100%; font-size: 12px" value="<?=$_SESSION['PBI_ID']?>" name="user_id" class="form-control col-md-7 col-xs-12" >
                <input type="text" style="width:100%; font-size: 12px"  required value="<?=$purpose?>"  name="purpose" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">When IOU Need <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="date" style="width:60%; font-size: 11px; float: left"  required value="<?=$date?>"  name="date" class="form-control col-md-7 col-xs-12" >
                <input type="time" style="width:35%; font-size: 11px; float: right" required value="<?=$time?>"  name="time" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Amount <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="number" style="width:100%; font-size: 11px;" required value="<?=$amount?>"  name="amount" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Recommend Person <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="recommended_by">
                    <option></option>
                    <?=advance_foreign_relation(find_active_user_HO($recommended_by),'');?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Approve Person <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="approved_by">
                    <option></option>
                    <?=advance_foreign_relation(find_active_user_HO($approved_by),'');?>
                </select>
            </div>
        </div>
        <?php if($GetUnique):  ?>
            <div class="form-group" style="margin-left:40%">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php  $status = find_a_field(''.$table.'','status','id='.$GetUnique); if($status=='PENDING'){ ?>
                        <button type="submit" class="btn btn-danger" onclick='return window.confirm("Are you confirm to delete?");' name="deleteRequest" style="font-size: 12px"><i class="fa fa-eraser"></i> Delete</button>
                        <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-primary">Update <i class="fa fa-edit"></i></button>
                    <?php } else { ?>
                        <h2 class="text-danger text-center">This application has been <?=$status?>!!</h2>
                    <?php } ?>
                </div>
            </div>
        <?php else : ?>
            <div class="form-group" style="margin-left:40%">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button type="submit" name="record" id="record"  style="font-size:12px" class="btn btn-primary"><i class="fa fa-plus"></i> Add IOU</button>
                </div>
            </div>
        <?php endif; ?>
    </form>
    </div>
    </div>
    </div>
        <?php if(!isset($GetUnique)): ?>
    </div>
    <?php endif; ?>
<?php if(!isset($GetUnique)):?>
<?=$crud->report_templates_with_add_newView($res,$title,'Create IOU');?>
<?php endif; ?>
<?=$html->footer_content();
mysqli_close($conn);?>