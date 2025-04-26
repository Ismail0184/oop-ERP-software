<?php
require_once 'support_file.php';
$title="Work from Home Applications";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$unique='id';
$$unique = @$_GET[$unique];
$unique_field='approved_by';
$table="emp_access_work_from_home_application";
$now = date("Y-m-d h:i:sa");

$current_status=find_a_field("".$table."","approved_status","".$unique."=".$$unique."");
$required_status="PENDING";
$authorused_status="APPROVED";
$page="emp_access_work_from_home_application_unauthorised.php";
$crud      =new crud($table);

$LeaveMasterData=find_all_field(''.$table.'','',''.$unique.'='.$$unique);

if (empty($LeaveMasterData->approved_viewed_at))
{
    mysqli_query($conn, "UPDATE ".$table." SET approved_viewed_at='".$now."' WHERE ".$unique."=".$$unique);
}


if(isset($_POST['confirm']))
{
    $_POST['status']="APPROVED";
    $_POST['approved_status']="APPROVED";
    $_POST['approved_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['reject']))
{
    $_POST['status']="REJECTED";
    $_POST['approved_status']="REJECTED";
    $_POST['approved_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}




// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}


if(isset($_POST['viewReport'])){
    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.user_id and p2.PBI_DESIGNATION=de.DESG_ID and 
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Application_By,r.attendance_date,r.reason,r.place,r.approved_status as status
     from '.$table.' r
     WHERE
     r.approved_by="'.$_SESSION['PBI_ID'].'"	and 
     r.attendance_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" 
     order by r.'.$unique.' DESC';
} else {
    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.user_id and p2.PBI_DESIGNATION=de.DESG_ID and 
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Application_By,r.attendance_date,r.reason,r.place,r.approved_status as status
     from '.$table.' r
     WHERE
     r.status = "PENDING" and
     r.approved_by="'.$_SESSION['PBI_ID'].'"	and 
     r.approved_status="'.$required_status.'" 
     order by r.'.$unique.' DESC';
} ?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {
            myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=600,left = 250,top = -1");
        }
    </script>

<?php if(isset($_GET[$unique])):
    require_once 'body_content_without_menu.php';
else :
    require_once 'body_content.php'; endif;
?>

<?php if(!isset($_GET[$unique])){ ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
        <table align="center" style="width: 50%;">
            <tr><td>
                    <input type="date" style="width:150px; font-size: 11px; height: 25px"  value="<?php if(isset($_POST['f_date'])) echo $_POST['f_date']; else echo date('Y-m-01');?>" max="<?=date('Y-m-d');?>" required   name="f_date" ></td>
                <td style="width:10px; text-align:center"> -</td>
                <td><input type="date" style="width:150px;font-size: 11px; height: 25px"  value="<?php if(isset($_POST['t_date'])) { echo $_POST['t_date']; } else { echo date('Y-m-d'); }?>" max="<?=date('Y-m-d')?>" required   name="t_date"></td>
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary">View Leave</button></td>
            </tr>
        </table>

        <?=$crud->report_templates_with_status_employee_dashboard($res,$link)?>
    </form>
<?php } ?>




<?php if(isset($_GET[$unique])){ ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
        <? require_once 'support_html.php';?>

        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px; margin-top: 5px">
            <thead>
            <tr class="bg-danger text-white">
                <th colspan="7" style="text-align: center; font-size: 13px; font-weight: bold;">Work from Home Application</th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th style="text-align: center; vertical-align:middle; width: 10%">Attendance Date</th>
                <th style="text-align: center; vertical-align:middle; width: 15%">Work Place</th>
                <th style="text-align: center; vertical-align:middle; width: 15%">Reason</th>
                <th style="text-align: center; vertical-align:middle">Remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="text-align: center; vertical-align: middle"><?=$LeaveMasterData->attendance_date;?></td>
                <td style="text-align: center; vertical-align: middle"><?=$LeaveMasterData->place;?></td>
                <td style="text-align: center; vertical-align:middle"><?=$LeaveMasterData->reason;?></td>
                <td style="text-align: center; vertical-align:middle"><input type="text" class="form-control col-md-7 col-xs-12" placeholder="Enter a note for the application, if necessary" name="remarks_while_approved" style="width: 99%; font-size: 11px"></td>
            </tr>
            </tbody>
        </table>
        <?php if($current_status!=$required_status && $current_status!="MANUAL" && $current_status!="RETURNED"){ echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been Approved!!</i></h6>';} else { ?>
            <table align="center" style="width:90%;font-size:12px;">
                <tr>
                    <td>
                        <button type="submit" style="font-size:12px; float:left" onclick='return window.confirm("Are you confirm to reject the Application?");' name="reject" class="btn btn-danger"><i class="fa fa-ban"></i> Reject & Back</button>
                        <button type="submit" style="font-size:12px; float:right" onclick='return window.confirm("Are you confirm to Approve the Application?");' name="confirm" id="confirm" class="btn btn-success">I Approved <i class="fa fa-check"></i></button>
                    </td>
                </tr>
            </table>
        <?php } ?>
        <?php } ?>
    </form>
<?=$html->footer_content();?>