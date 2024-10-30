<style>
.c--anim-btn span {
  text-decoration: none;
  text-align: left;
  display: block;
  font-size:30px;
}
.c--anim-btn, .c-anim-btn {
  transition: 0.3s;     
}

.c--anim-btn {
  height: 50px;
  font: normal normal 700 1em/4em Arial,sans-serif;
  overflow: hidden;
  width: 200px;
  
}

.c-anim-btn{
  margin-top: 0em;   
}

.c--anim-btn:hover .c-anim-btn{
  margin-top: -1.2em;
}

</style>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("admin_action_print_view.php?action_id="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=500,left = 200,top = -1");}
</script>
<?php


require_once 'support_file.php';
require_once 'dashboard_data.php';
$dyear=date('Y');
$dmon='12';
$dday='31';
$cday=date('d');

$dfrom=date('Y-1-1');
$dto=date('Y-m-d');
 
 // if session is not set this will redirect to login page
 if( !isset($_SESSION['login_email']) ) {
  header("Location: index.php");
  exit;
 }


$PostMonth = @$_POST['mon'];
if($PostMonth!=''){
    $mon=$PostMonth;}
else{
    $mon=date('m');
}
$PostYear = @$_POST['year'];
if($PostYear!=''){
    $year=$PostYear;}
else{
    $year=date('Y');
}
$startTime = $days1=mktime(0,0,0,($mon-1),26,$year);
$endTime = $days2=mktime(0,0,0,$mon,25,$year);
$days_in_month = date('t',$endTime);
$startTime1 = $days1=mktime(0,0,0,($mon),01,$year);
$endTime1 = $days2=mktime(0,0,0,$mon,$days_in_month,$year);
$startday = date('Y-m-d',$startTime);
$endday = date('Y-m-d',$endTime);
$start_date = $year.'-'.($mon-1).'-26';
$end_date = $year.'-'.$mon.'-25';


$dashboardpermission=find_a_field('user_permissions_dashboard','COUNT(module_id)','user_id='.$_SESSION['userid'].' and module_id='.$_SESSION['module_id'].'');
?>


<?php if($_SESSION['module_id']=='11') { ?>

    <div class="col-md-12 col-xs-12">
        <div class="x_panel" >
            <div class="x_content">Under build.</div>
        </div>
    </div>


    <div class="col-md-6 col-xs-12">
        <div class="x_panel fixed_height_230" >
            <div class="x_title">
                <h2 style="color: #FF6347"><i class="fa fa-calendar"></i> Attendance Status</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
                    <thead>
                    <tr class="bg-success">
                        <th colspan="10" style="text-align: center; font-size: 15px; font-weight: bold">Current Month Attendance Status</th>
                    </tr>
                    <th style="text-align: center">Total Day</th>
                    <th style="text-align: center">Off Day</th>
                    <th style="text-align: center">Holiday</th>
                    <th style="text-align: center">Present</th>
                    <th style="text-align: center">Late Present</th>
                    <th style="text-align: center">Leave</th>
                    <th style="text-align: center">Early Leave</th>
                    <th style="text-align: center">Absent</th>
                    <th style="text-align: center">Outdoor Duty</th>
                    <th style="text-align: center">Overtime</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: center"><?=$days_in_month;?></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
    </div>


    <div class="col-md-6 col-xs-12">
        <div class="x_panel fixed_height_230" >
            <div class="x_title">
                <h2 style="color: #FF6347"><i class="fa fa-calendar"></i> Leave Status</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                    <thead>
                    <tr class="bg-primary">
                        <th colspan="8" style="text-align: center; font-size: 15px; font-weight: bold">Current Year <?=date('Y')?></th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 2%; vertical-align:middle">Leave</th>
                        <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                        while($leave_row=mysqli_fetch_object($res)){
                            ?>
                            <th style="text-align: center; vertical-align:middle"><?=$leave_row->leave_type_name;?></th>
                        <?php } ?>
                        <th style="text-align: center; vertical-align:middle">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Policy</td>
                        <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                        $totalPolicy = 0;
                        while($leave_row=mysqli_fetch_object($res)){ ?>
                            <td style="text-align: center"><?=$leave_row->yearly_leave_days;?></td>
                            <?php
                            $totalPolicy=$totalPolicy+$leave_row->yearly_leave_days;
                        } ?>
                        <td style="text-align: center"><?=$totalPolicy;?></td>
                    </tr>

                    <tr>
                        <td>Taken</td>
                        <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                        $total_taken = 0;
                        while($leave_row=mysqli_fetch_object($res)){ ?>
                            <td style="text-align: center"><?php $leave_taken=find_a_field("hrm_leave_info","SUM(total_days)","type='".$leave_row->id."' and s_date between '".$dfrom."' and '".$dto."' and PBI_ID='".$_SESSION['PBI_ID']."'"); if($leave_taken>0){ echo number_format($leave_taken);} else echo ''; ?></td>
                            <?php
                            $total_taken=$total_taken+$leave_taken;
                        } ?>
                        <td style="text-align: center"><?=$total_taken;?></td>
                    </tr>
                    </tbody>

                    <tr>
                        <th>Balance</th>
                        <?php
                        $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                        while($leave_row=mysqli_fetch_object($res)){
                            $balance=$leave_row->yearly_leave_days - find_a_field("hrm_leave_info","SUM(total_days)","type='".$leave_row->id."' and s_date between '$dfrom' and '$dto' and PBI_ID='".$_SESSION['PBI_ID']."'");?>
                            <th class="<?php if($balance==0){?> bg-danger <?php } ?>" style="text-align: center"><?=$balance?></th>
                        <?php } ?>
                        <th style="text-align: center"><?=$totalPolicy-$total_taken;?></th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
    <div class="col-md-6 col-xs-12">
        <div class="x_panel fixed_height_250">
            <div class="x_title">
                <h2 style="color: hotpink"><i class="fa fa-bullhorn"></i> Announcement</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="legend list-unstyled">
                    <?php
                    $res=mysqli_query($conn, "SELECT * FROM hrm_announcement WHERE STATUS in ('ACTIVE') order by ADMIN_ANN_DID desc limit 1");
                    while($row=mysqli_fetch_object($res)){
                        ?>

                        <li  style="vertical-align: middle; cursor: pointer" onclick="DoNavPOPUP('<?=$row->ADMIN_ANN_DID;?>', 'TEST!?', 600, 700)">
                            <p style="vertical-align: middle">
                                <span class="badge badge-primary h-50"><?=$row->ADMIN_ANN_DATE;?></span> <span class="name" style="vertical-align: middle"><?=$row->ADMIN_ANN_TYPE;?> - <?=$row->ADMIN_ANN_SUBJECT;?><br><br><font style="font-size: 10px;"><?=$row->ADMIN_ANN_DETAILS;?></font></span>
                            </p>
                        </li>
                    <?php } ?></ul>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xs-12">
        <div class="x_panel fixed_height_250" >
            <div class="x_title">
                <h2 class="text-danger"><i class="fa fa-bell"></i> Admin Action</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="legend list-unstyled">
                    <?php
                    $result=mysqli_query($conn, "SELECT  a.*,p.*,d.* FROM 
							admin_action_detail a,
							personnel_basic_info p,
							department d						
							 where 
							 a.PBI_ID=p.PBI_ID and 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID	and 
							 a.PBI_ID=".$_SESSION['PBI_ID']."				 
							  order by p.PBI_NAME");
                    while($action=mysqli_fetch_object($result)){
                        ?>
                        <li style="vertical-align: middle; cursor: pointer" onclick="DoNavPOPUP('<?=$action->ADMIN_ACTION_DID;?>', 'TEST!?', 600, 700)">
                            <p style="vertical-align: middle">
                                <span class="icon" ><i class="fa fa-square blue"></i></span> <span class="name" style="vertical-align: middle"><br><font style="font-size: 10px;"><?=$row->ADMIN_ANN_SUBJECT;?></font></span>
                            </p>
                        </li>
                    <?php } ?></ul>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xs-12">
        <div class="x_panel fixed_height_390" >
            <div class="x_title">
                <h2 class="text-success"><i class="fa fa-birthday-cake"></i> Upcoming Birthday</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="overflow: auto;height: 320px">
                <ul class="legend list-unstyled">
                    <?php $res=mysqli_query($conn, 'select p2.*,d.*,de.* FROM 
							personnel_basic_info p2,
							department d,
							designation de 
							 where 
							 p2.PBI_JOB_STATUS in ("In Service") and 
							 p2.PBI_DESIGNATION=de.DESG_ID and  							 
							 p2.PBI_DEPARTMENT=d.DEPT_ID order by p2.PBI_DOB asc ');
				   while($birthday=mysqli_fetch_object($res)){
                       $bday=$birthday->PBI_DOB;
                       $dateArray = explode("-", $bday);
                       if (count($dateArray) === 3) {
                           list($year, $month, $day) = $dateArray;
                       } else {
                           echo "Invalid date format";
                       }
                       if($month==$mon){
                        ?>
                        <li style="vertical-align: middle; cursor: pointer">
                            <p style="vertical-align: middle">
                                <span class="icon" ><i class="fa fa-square grey"></i></span> <span class="name" style="vertical-align: middle"><?=$birthday->PBI_NAME;?></span>
                            </p>
                            <p style="font-size: 10px; margin-top: -10px"><?=$birthday->DESG_DESC;?></p>
                            <p style="font-size: 10px;margin-top: -10px; color: red"><?=date("d M", strtotime($birthday->PBI_DOB));?> (<strong><?=date("D", strtotime($birthday->PBI_DOB));?></strong>)</p>
                        </li>
                    <?php }} ?></ul>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xs-12 widget widget_tally_box">
        <div class="x_panel fixed_height_390" >
            <div class="x_title">
                <h2 class="text-light"><i class="fa fa-calendar"></i> Upcoming Holiday</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="overflow: auto;height: 320px">
                <ul class="legend list-unstyled">
                    <?php
                    $res=mysqli_query($conn, "SELECT * FROM salary_holy_day WHERE holy_day between '$year-$mon-$cday' and '$dyear-$dmon-$dday' order by id asc limit 7");
                    while($holiday=mysqli_fetch_object($res)){
                                               ?>
                    <li style="vertical-align: middle; cursor: pointer">
                        <p style="vertical-align: middle">
                            <span class="icon" ><i class="fa fa-square dark"></i></span> <span class="name" style="vertical-align: middle"><?=$holiday->reason;?><br><font style="font-size: 10px;"><?=date("d M Y", strtotime($holiday->holy_day));?> (<strong><?=date("D", strtotime($holiday->holy_day));?></strong>)</font></span>
                        </p>
                    </li>
                    <?php } ?></ul>

            </div>
        </div>
    </div>
    <?php } else { ?>
             <h1 style="text-align:center; margin-top:200px">Welcome to <?php if($_SESSION['module_id']>0) { ?> <?=find_a_field('module_department', 'modulename','id='.$_SESSION['module_id']);?> Module <?php } else { echo 'ERP Software. <br><font style="font-size: 15px">Please See the above menu</font>'; }?></h1>
       <?php } ?><?php ob_end_flush(); ?>