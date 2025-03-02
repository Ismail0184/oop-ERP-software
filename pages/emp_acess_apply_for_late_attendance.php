 <?php
require_once 'support_file.php';
$title="Apply for Late Attendance";
$now=time();
$unique='id';
$unique_field='attendance_date';
$table="hrm_late_attendance";
$page="emp_acess_apply_for_late_attendance.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];

$required_status="PENDING";
$current_status=find_a_field(''.$table.'','status',''.$unique.'='.$$unique.'');

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))

//for insert..................................
{    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
		$_POST['attendance_date']=@$_POST['attendance_date'];
		$_POST['PBI_ID']=$_SESSION['PBI_ID'];
		$_POST['entry_by']=$_SESSION['PBI_ID'];
		$_POST['status'] = "PENDING";
        $_POST['entry_at'] = date('Y-m-d H:i:s');
        $at=$_POST['late_entry_at'];
        $_POST['late_entry_at']=$at;
        $crud->insert();
        $type=1;
        $rid = @$_GET['rid'];
        if ($rid>0){
            mysqli_query($conn, "UPDATE ZKTeco_attendance SET apply_status='APPLIED' where id=".$rid);
            header("Location: dashboard.php");
        }
        unset($_POST);
        unset($$unique);
    }


//for modify..................................
if(isset($_POST['modify']))
{
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
    $at=$_POST['late_entry_at'];
    $_POST['late_entry_at']=$at;
    $crud->update($unique);
    $type=1;
    echo "<script>window.close(); </script>";
}

//for Delete..................................
if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>window.close(); </script>";
}}}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

	$sql_authorised_by="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' : ',d.DEPT_SHORT_NAME) FROM

							personnel_basic_info p,
							department d,
							essential_info e
							 where
							 p.PBI_JOB_STATUS in ('In Service') and
							 p.PBI_DEPARTMENT=d.DEPT_ID	and
							 p.PBI_ID=e.PBI_ID and
							 e.ESS_JOB_LOCATION=1 group by p.PBI_ID
							  order by p.PBI_NAME";
$sql2="select a.id,a.attendance_date as date,a.late_reason as reason,concat(a.late_entry_at,',',a.am_pm) as late_entry_at,a.status from ".$table." a where a.PBI_ID=".$_SESSION['PBI_ID']." order by a.".$unique." desc limit 7";
 $attendance_date   = @$attendance_date;
 $late_entry_at     = @$late_entry_at;
 $am_pm             = @$am_pm;
 $late_reason       = @$late_reason;
 $authorised_by     = @$authorised_by;
 $rid = @$_GET['rid'];
 $ridGetData = find_all_field('ZKTeco_attendance','','id='.$rid);
?>
<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=600,height=600,left = 383,top = -1");}
    </script>
<?php require_once 'body_content.php'; ?>

 <div class="col-md-7 col-sm-12 col-xs-12">
     <div class="x_panel">
         <div class="x_title">
             <h2><?=$title;?> <small class="text-danger float-right">field marked with * are mandatory</small></h2>
             <div class="clearfix"></div>
         </div>
         <div class="x_content">
             <form  name="addem" style="font-size:11px" id="addem" class="form-horizontal form-label-left" method="post">
                 <? require_once 'support_html.php';?>
                 <table style="width:100%"  cellpadding="0" cellspacing="0">
                     <tr>
                         <th style="width:19%">Late Entry Date <span class="required text-danger">*</span></td>
                         <th style="width:2%">:</th>
                         <td style="width:30%">
                             <input type="date" id="attendance_date" max="<?=date('Y-m-d');?>" style="font-size:11px; width:90%"  required="required" name="attendance_date" value="<?=($rid>0)? $ridGetData->date : $attendance_date;?>" <?=($rid>0)? 'readonly' : '';?> class="form-control col-md-7 col-xs-12" >
                             <input type="hidden" id="<?=$unique?>" style="width:80%"     name="<?=$unique?>" value="<?=$$unique?>" class="form-control col-md-7 col-xs-12" >
                             <input type="hidden" style="width:80%"     name="rid" value="<?=$rid?>" class="form-control col-md-7 col-xs-12" >
                         </td>

                         <th style="width:19%">Entry Time <span class="required text-danger">*</span></td>
                         <th style="width:2%">:</th>
                         <td style="width:30%">
                             <input type="text" id="late_entry_at" value="<?=($rid>0)? $ridGetData->clock_in : $late_entry_at;?>" style="width:100%; font-size:11px" <?=($rid>0)? 'readonly' : '';?> required   name="late_entry_at"   class="form-control col-md-7 col-xs-12" >
                         </td>
                     </tr>

                     <tr><td style="height:5px"></td></tr>

                     <tr>
                         <th>Reason <span class="required text-danger">*</span></td>
                         <th>:</td>
                         <td>
                             <input type="text" id="late_reason" style="width:90%; font-size:11px"  required value="<?=$late_reason;?>"  name="late_reason"   class="form-control col-md-7 col-xs-12" >
                         </td>

                         <th>Approve By <span class="required text-danger">*</span></td>
                         <th>:</td>
                         <td>
                             <select style="width: 100%;margin-top: 2px;" class="select2_single form-control" name="authorised_by" id="authorised_by">
                                 <option></option>
                                 <?=advance_foreign_relation(find_active_user_HO($authorised_by),'');?>
                             </select>
                         </td>
                     </tr>
                 </table>
                 <br>
                 <?php if($$unique){  ?>
                     <?php if($current_status!=$required_status){ echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This late attendance has been Approved!!</i></h6>';} else { ?>
                         <div class="col-md-12 col-sm-6 col-xs-12">
                             <input  name="delete" type="submit" style="font-size:12px; float:left; margin-left:10%" class="btn btn-danger" id="delete" value="Delete"/>
                             <button type="submit" name="modify" id="modify" style="font-size:12px; float:right; margin-right:10%" class="btn btn-success">Modify</button>
                         </div>
                     <? }?>
                 <?php } else {?>
                     <div class="form-group" style="margin-left:40%">
                         <div class="col-md-6 col-sm-6 col-xs-12">
                             <button type="submit" name="record" id="record"  onclick='return window.confirm("Are you confirm?");' style="font-size:12px" class="btn btn-primary">Submit the Application</button>
                         </div>
                     </div>
                 <?php } ?>
             </form>
         </div>
     </div>
 </div>

<?php if(!isset($_GET[$unique])){ ?>
<?=recentdataview($sql2,'voucher_view_popup_ismail.php','hrm_late_attendance','282px','Recent Late Applications','hrm_requisition_late_attendance_report.php','5');?>
<?php } ?>
<?=$html->footer_content();?>
