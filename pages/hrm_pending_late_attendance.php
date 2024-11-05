<?php
require_once 'support_file.php';
$title="Pending Late Attendance List";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$dfromM=date('Y-m-1');
$dtoM=date('Y-m-d');
$now=time();
$unique='id';
$unique_field='PBI_ID';
$table="hrm_late_attendance";

$current_status=find_a_field("".$table."","status","".$unique."=".$_GET[$unique]."");
$required_status="RECOMMENDED";
$authorused_status="APPROVED";
$page="hrm_pending_late_attendance.php";
$crud      =new crud($table);
$$unique = $_GET[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";

$leaverequest=find_all_field(''.$table.'','',''.$unique.'='.$_GET[$unique]);

if(prevent_multi_submit()){


//for modify..................................
    if(isset($_POST['confirm']))
    {
        $sd=$_POST['attendance_date'];
        $_POST['attendance_date']=date('Y-m-d' , strtotime($sd));
        $_POST['status']="APPROVED";
        $_POST['dept_head_aprv_at']=date("Y-m-d h:i:sa");
        $crud->update($unique);
        $type=1;
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }

//for Delete..................................
    if(isset($_POST['Deleted']))
    {   $condition=$unique."=".$$unique;
        $crud->delete($condition);
        unset($$unique);
        $type=1;
        $msg='Successfully Deleted.';
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

if(isset($_POST['viewreport'])){
    $res='select r.'.$unique.',r.'.$unique.' as AID,CONCAT(r.attendance_date, " ", r.late_entry_at) AS "Late date & time",
				 (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME," - ", d.DEPT_DESC,")") FROM 
							 
							personnel_basic_info p2,
							department d,
							designation de 
							 where 
							 p2.PBI_ID=r.PBI_ID and
							 p2.PBI_DESIGNATION=de.DESG_ID and  							 
							 p2.PBI_DEPARTMENT=d.DEPT_ID) as Applicant,
							 r.late_reason as late_reason,
							 (SELECT CONCAT(p2.PBI_NAME) 
        FROM personnel_basic_info p2
        JOIN designation de ON p2.PBI_DESIGNATION = de.DESG_ID
        JOIN department d ON p2.PBI_DEPARTMENT = d.DEPT_ID
        WHERE p2.PBI_ID = r.authorised_by
    ) AS "Approving Person",
							 r.status
				  from '.$table.' r
				  WHERE 
				    r.attendance_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"
				   order by r.'.$unique.' DESC';
} else {
    $res='SELECT 
    r.'.$unique.',
    r.'.$unique.' AS AID,
    CONCAT(r.attendance_date, " ", r.late_entry_at) AS "Late date & time",
    (
        SELECT CONCAT(p2.PBI_NAME, " # ","(",de.DESG_SHORT_NAME," - ", d.DEPT_DESC,")") 
        FROM personnel_basic_info p2
        JOIN designation de ON p2.PBI_DESIGNATION = de.DESG_ID
        JOIN department d ON p2.PBI_DEPARTMENT = d.DEPT_ID
        WHERE p2.PBI_ID = r.PBI_ID
    ) AS Applicant,
    r.late_reason AS late_reason,
    (SELECT CONCAT(p2.PBI_NAME) 
        FROM personnel_basic_info p2
        JOIN designation de ON p2.PBI_DESIGNATION = de.DESG_ID
        JOIN department d ON p2.PBI_DEPARTMENT = d.DEPT_ID
        WHERE p2.PBI_ID = r.authorised_by
    ) AS "Approving Person",
    r.status
FROM '.$table.' r
WHERE r.status NOT IN ("APPROVED","REJECTED")
ORDER BY r.'.$unique.' DESC;
';}?>




<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=800,height=500,left = 250,top = -1");}
</script>
<style>
    input[type=text]{
        font-size: 11px;
    }
</style>
<?php require_once 'body_content.php'; ?>

<?php if(!isset($_GET[$unique])){ ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
        <table align="center" style="width: 50%;">
            <tr><td>
                    <input type="date" style="width:150px; font-size: 11px; height: 25px"  value="<?php if(isset($_POST['f_date'])) echo $_POST['f_date']; else echo date('Y-m-01');?>" max="<?=date('Y-m-d');?>" required   name="f_date" >
                <td style="width:10px; text-align:center"> -</td>
                <td><input type="date" style="width:150px;font-size: 11px; height: 25px"  value="<?php if(isset($_POST['t_date'])) { echo $_POST['t_date']; } else { echo date('Y-m-d'); }?>" max="<?=date('Y-m-d')?>" required   name="t_date"></td>
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewreport"  class="btn btn-primary">View Late Attendance</button></td>
            </tr></table>
        <?=$crud->report_templates_with_status_employee_dashboard($res,$title);?>
    </form>
<?php } ?>



<?php if(isset($_GET[$unique])){ ?>
<form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
    <? require_once 'support_html.php';?>
    <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px; margin-top: -22px">
        <thead>
        <tr style="background-color: #4682B4">
            <th colspan="7" style="text-align: center; font-size: 15px; font-weight: bold; color: white">Late Attendance Request</th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style="text-align: center">Types</th>
            <th style="text-align: center">Date</th>
            <th style="text-align: center">Late At</th>
            <th style="text-align: center">Late Days<br>(Current Year)</th>
            <th style="text-align: center">Late Days<br>(Current Month)</th>
            <th style="text-align: center">Late <br>Reason</th>
            <th style="text-align: center">Responsible Person</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align: center; vertical-align: middle">Late Attendance</td>
            <td style="text-align: center;"><input type="text" id="attendance_date" style="width: 100px; text-align: center"  required="required" name="attendance_date" value="<?php if($$unique>0){ echo date('m/d/y' , strtotime($leaverequest->attendance_date)); } else { echo ''; } ?>" class="form-control col-md-7 col-xs-12" ></td>
            <td style="text-align: center"><input type="text" id="departure_time" style="width: 100px; text-align: center"   name="departure_time" value="<?=$leaverequest->late_entry_at;?>" class="form-control col-md-7 col-xs-12" ></td>
            <td style="text-align: center"><input type="text" id="total_days11" style="width: 100px; text-align: center"  name="total_days11" readonly value="<?php $leave_taken=find_a_field("".$table."","COUNT(id)","status not in ('PENDING') and attendance_date between '$dfrom' and '$dto' and  PBI_ID='".$leaverequest->PBI_ID."'"); if($leave_taken>0){ echo $leave_taken,', Days';} else echo ''; ?>" class="form-control col-md-7 col-xs-12" ></td>
            <td style="text-align: center"><input type="text" id="total_days11" style="width: 100px; text-align: center"  name="total_days11" readonly value="<?php $leave_takenM=find_a_field("".$table."","COUNT(id)","status not in ('PENDING') and attendance_date between '$dfromM' and '$dtoM' and  PBI_ID='".$leaverequest->PBI_ID."'"); if($leave_takenM>0){ echo $leave_takenM,', Days';} else echo ''; ?>" class="form-control col-md-7 col-xs-12" ></td>
            <td style="text-align: center; vertical-align: middle"><?=$leaverequest->late_reason;?></td>
            <td style="text-align: center; vertical-align: middle"><?=find_a_field("personnel_basic_info","PBI_NAME","PBI_ID=".$leaverequest->authorised_by."");?></td>
        </tr>
        </tbody>
    </table>


    <?php if($current_status!=$required_status && $current_status!="MANUAL" && $current_status!="RETURNED"){ echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This late attendance application has not yet been approved ! Please wait until approval !!</i></h6>';} else { ?>
        <table align="center" style="width:90%;font-size:12px;">
            <tr>
                <td style="width:20%">
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" onclick='return window.confirm("Are you confirm to Deleted?");' name="Deleted" id="Deleted" class="btn btn-danger">Cancel & Deleted</button>
                        </div>
                    </div>
                </td>

                <td style="width:40%; float:right">
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" onclick='return window.confirm("Are you confirm ?");' name="confirm" id="confirm" class="btn btn-success">Confirm & GRANTED</button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    <?php } ?>
    <?php } ?>
</form>

<?=$html->footer_content();?>
