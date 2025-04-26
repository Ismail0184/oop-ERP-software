 <?php
require_once 'support_file.php';
$title="Apply for Work from Home";

$now=time();
$unique='id';
$unique_field='attendance_date';
$table="emp_access_work_from_home_application";
$page="emp_access_work_from_home_application.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];


if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))
{
    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
        $_POST['entry_at'] = date('Y-m-d H:i:s');
        $crud->insert();
        unset($_POST);
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

$sql_recommended_by="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' : ',d.DEPT_SHORT_NAME) FROM
							personnel_basic_info p,
							department d,
							essential_info e
							 where
							 p.PBI_JOB_STATUS in ('In Service') and
							 p.PBI_DEPARTMENT=d.DEPT_ID	and
							 p.PBI_ID=e.PBI_ID and
							 e.ESS_JOB_LOCATION=1 group by p.PBI_ID
							  order by p.PBI_NAME";


$sql="select a.id,a.attendance_date as date,a.reason,a.status from ".$table." a where a.user_id=".$_SESSION['PBI_ID']." order by a.".$unique." desc limit 7";
?>



<?php require_once 'header_content.php'; ?>
 <style>
     input[type=text]{
         font-size: 11px;
     }
 </style>
<script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=600,height=600,left = 383,top = -1");}
    </script>
<?php require_once 'body_content.php'; ?>

 <div class="col-md-8 col-sm-12 col-xs-12">
     <div class="x_panel">
         <div class="x_title">
             <h2><?=$title;?></h2>
             <div class="clearfix"></div>
         </div>
         <div class="x_content">
             <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" style="font-size: 11px">
                 <? require_once 'support_html.php';?>
                 <table style="width:100%; font-size: 11px"  cellpadding="0" cellspacing="0">
                     <tr>
                         <th style="width:15%">Date <span class="required text-danger">*</span></th>
                         <th style="width:2%">:</th>
                         <td>
                             <input type="date" id="attendance_date"  required="required" name="attendance_date"  class="form-control col-md-7 col-xs-12" style="font-size:11px;width:97%" value="<?=$attendance_date;?>">
                             <input type="hidden" id="<?=$unique?>" style="width:97%"     name="<?=$unique?>" value="<?=$$unique?>" class="form-control col-md-7 col-xs-12" >
                             <input type="hidden" name="user_id" value="<?=$_SESSION['PBI_ID']?>" class="form-control col-md-7 col-xs-12" >
                         </td>
                         <th style="width:15%">Home Address <span class="required text-danger">*</span></th>
                         <th style="width:2%">:</th>
                         <td>
                             <input type="text" id="place" style="width:97%"  required   name="place"  value="<?=$place;?>"  class="form-control col-md-7 col-xs-12" ></td>
                     </tr>
                     <tr><td style="height:5px"></td></tr>
                     <tr>
                         <th style="width:15%">Reasons <span class="required text-danger">*</span></th>
                         <th style="width:2%">:</th>
                         <td><input type="text" style="width:97%"  required   name="reason" value="<?=$reason;?>"   class="form-control col-md-7 col-xs-12" ></td>
                         <th style="width:15%">Authorised Person <span class="required text-danger">*</span></th>
                         <th style="width:2%">:</th>
                         <td><select class="select2_single form-control" style="width: 97%;" tabindex="-1" required="required" name="approved_by">
                                 <option></option>
                                 <?=advance_foreign_relation(find_active_user_HO($approved_by));?>
                             </select>
                         </td>
                     </tr>
                 </table>
                 <br>
                 <?php if($_GET[$unique]){  ?>
                     <div class="form-group" style="margin-left:40%">
                         <div class="col-md-6 col-sm-6 col-xs-12">
                             <button type="submit" name="modify" id="modify" class="btn btn-success" style="font-size: 12px">Modify</button>
                         </div>
                     </div>
                     <? if($_SESSION['userid']=="10019"){?>
                         <div class="form-group" style="margin-left:40%;">
                             <div class="col-md-6 col-sm-6 col-xs-12">
                                 <input  name="delete" type="submit" class="btn btn-danger" style="font-size: 12px" id="delete" value="Delete"/>
                             </div>
                         </div>
                     <? }?>
                 <?php } else {?>
                     <div class="form-group" style="margin-left:40%">
                         <div class="col-md-6 col-sm-6 col-xs-12">
                             <button type="submit" name="record" id="record" style="font-size: 12px" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary">Submit the Application</button>
                         </div>
                     </div>
                 <?php } ?>
             </form>
         </div>
     </div>
 </div>
 <?=recentdataviewAttendance($sql,'','emp_access_work_from_home_application','282px','Recent Applications','emp_access_work_from_home_application.php','4');?>
<?=$html->footer_content();?>
