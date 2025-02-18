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

$firstDayOfCurrentMonth = date("Y-m-01");
$lastDayOfPreviousMonth = date("Y-m-t", strtotime($firstDayOfCurrentMonth . " -1 month"));
$totalDaysInLastMonth = date("d", strtotime($lastDayOfPreviousMonth));


$lastMonthGet = date('m')-1;
$lastMonthStartDay = date('Y-'.$lastMonthGet.'-01');
$lastMonthEndDay = date('Y-'.$lastMonthGet.'-'.$totalDaysInLastMonth.'');

$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate = date('Y-m-31');

$lastMonthLeaveCount = find_a_field('hrm_leave_info','SUM(total_days)','status="GRANTED" and s_date between "'.$lastMonthStartDay.'" and "'.$lastMonthEndDay.'" and PBI_ID='.$_SESSION['PBI_ID']);
$lastMonthODCount = find_a_field('hrm_od_attendance','COUNT(id)','PBI_ID='.$_SESSION['PBI_ID']);
$currentMonthOffDayCount = find_a_field('salary_holy_day','COUNT(id)','holy_day between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');
$currentMonthPresentCount = find_a_field('ZKTeco_attendance','COUNT(id)','employee_id='.$_SESSION['PBI_ID'].' and date between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');
$currentMonthLateCount = find_a_field('ZKTeco_attendance','COUNT(id)','clock_in_status="Late" and employee_id='.$_SESSION['PBI_ID'].' and date between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');
$currentMonthEarlyLeaveCount = find_a_field('ZKTeco_attendance','COUNT(id)','clock_out_status="Early" and employee_id='.$_SESSION['PBI_ID'].' and date between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');

$query = "SELECT OT_time FROM ZKTeco_attendance WHERE date between '".date('Y-m-01')."' and '".date('Y-m-t')."' and employee_id = ".$_SESSION['PBI_ID'];
$result = mysqli_query($conn, $query);

$totalSeconds = 0;

// Loop through results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Split duration into hours, minutes, and seconds
        list($hours, $minutes, $seconds) = explode(':', $row['OT_time']);
        $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
    }
}

// Convert total seconds to hours, minutes, and seconds
$totalHours = floor($totalSeconds / 3600);
$totalMinutes = floor(($totalSeconds % 3600) / 60);
$totalSeconds = $totalSeconds % 60;

$currentMonthOverTimeCount = $totalHours.':'.$totalMinutes.':'.$totalSeconds;

$currentMonthLeaveCount = find_a_field('hrm_leave_info','COUNT(id)','clock_in_status="Late" and PBI_ID='.$_SESSION['PBI_ID'].' and s_date between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');
$currentMonthOSDCount = find_a_field('hrm_od_attendance','COUNT(id)','PBI_ID='.$_SESSION['PBI_ID'].' and attendance_date between "'.date('Y-m-01').'" and "'.date('Y-m-t').'"');
$currentMonthAbsentCount = 0;

$lastMonthTotalAbsent = $totalDaysInLastMonth-($lastMonthLeaveCount);

$dashboardpermission=find_a_field('user_permissions_dashboard','COUNT(module_id)','user_id='.$_SESSION['userid'].' and module_id='.$_SESSION['module_id'].'');
$lateAttendanceApplicationURL = 'emp_acess_apply_for_late_attendance.php';
?>


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
                    <th style="text-align: center; vertical-align: middle">Total Day</th>
                    <th style="text-align: center; vertical-align: middle">Off Day</th>
                    <th style="text-align: center; vertical-align: middle">Holiday</th>
                    <th style="text-align: center; vertical-align: middle">Present</th>
                    <th style="text-align: center; vertical-align: middle">Late Present</th>
                    <th style="text-align: center; vertical-align: middle">Leave</th>
                    <th style="text-align: center; vertical-align: middle">Early Leave</th>
                    <th style="text-align: center; vertical-align: middle">Absent</th>
                    <th style="text-align: center; vertical-align: middle">Outdoor Duty</th>
                    <th style="text-align: center; vertical-align: middle">Overtime</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: center"><?=$days_in_month;?></td>
                        <td style="text-align: center"><?=($currentMonthOffDayCount>0)? $currentMonthOffDayCount : '-' ?></td>
                        <td style="text-align: center"><?=countFridaysInMonth(date('Y'),date('m'));?></td>
                        <td style="text-align: center"><?=($currentMonthPresentCount>0)? $currentMonthPresentCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthLateCount>0)? $currentMonthLateCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthLeaveCount>0)? $currentMonthLeaveCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthEarlyLeaveCount>0)? $currentMonthEarlyLeaveCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthAbsentCount>0)? $currentMonthAbsentCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthOSDCount>0)? $currentMonthOSDCount : '-' ?></td>
                        <td style="text-align: center"><?=($currentMonthOverTimeCount>0)? $currentMonthOverTimeCount : '-' ?></td>
                    </tr>
                    </tbody>
                </table>

                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
                    <thead>
                    <tr class="bg-success">
                        <th colspan="10" style="text-align: center; font-size: 15px; font-weight: bold">Last Month Attendance Status</th>
                    </tr>
                    <th style="text-align: center; vertical-align: middle">Total Day</th>
                    <th style="text-align: center; vertical-align: middle">Off Day</th>
                    <th style="text-align: center; vertical-align: middle">Holiday</th>
                    <th style="text-align: center; vertical-align: middle">Present</th>
                    <th style="text-align: center; vertical-align: middle">Late Present</th>
                    <th style="text-align: center; vertical-align: middle">Leave</th>
                    <th style="text-align: center; vertical-align: middle">Early Leave</th>
                    <th style="text-align: center; vertical-align: middle">Absent</th>
                    <th style="text-align: center; vertical-align: middle">Outdoor Duty</th>
                    <th style="text-align: center; vertical-align: middle">Overtime</th>
                    </thead>
                    <tbody>
                    <?php
                    $lastMonthAttendance = find_all_field('hrm_attendance_info','','PBI_ID='.$_SESSION['PBI_ID']);
                    ?>
                    <tr>
                        <td style="text-align: center"><?=$totalDaysInLastMonth;?></td>
                        <td style="text-align: center"><?=$lastMonthAttendance->offDay;?></td>
                        <td style="text-align: center"><?=$lastMonthAttendance->holiday;?></td>
                        <td style="text-align: center"><?=$lastMonthAttendance->present;?></td>
                        <td style="text-align: center"><?=$lastMonthAttendance->latePresent;?></td>
                        <td style="text-align: center"><?=number_format($lastMonthLeaveCount,0)?></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"><?=$lastMonthTotalAbsent;?></td>
                        <td style="text-align: center"><?=number_format($lastMonthODCount,0)?></td>
                        <td style="text-align: center"></td>
                    </tr>
                    </tbody></table>
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
        <div class="x_panel" style="height: 568px; overflow: auto">
            <div class="x_title">
                <h2 style="color: #FF6347"><i class="fa fa-calendar"></i> Finger Punch Logs</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                    <thead>
                    <tr class="bg-info">
                        <th colspan="8" style="text-align: center; font-size: 15px; font-weight: bold">Current Month <?=date('M Y')?></th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <th class="bg-green">Clock In</th>
                        <th>Clock In Status</th>
                        <th>Late Time</th>
                        <th class="bg-red">Clock Out</th>
                        <th>Clock Out Status</th>
                        <th>Early Time</th>
                        <th>Work Time</th>
                    </tr>
                    </thead>

                    <?php $res=mysqli_query($conn, "select * from ZKTeco_attendance where employee_id=".$_SESSION['PBI_ID']." and date between '".$currentMonthStartDate."' and '".$currentMonthEndDate."' order by date desc");
                    while($data=mysqli_fetch_object($res)){ $yesterday = date("Y-m-d", strtotime("-1 day")); ?>
                        <tr>
                            <td class="text-center">
                                <?= ($data->date===date("Y-m-d")) ? 'Today' : (($data->date === $yesterday) ? 'Yesterday' : date("d M Y", strtotime($data->date))); ?>
                            </td>
                            <td><?=$data->clock_in?></td>
                            <?php if($data->clock_in_status=='Late'){?>
                            <td class="bg-danger">
                                <a <?php if($data->apply_status=='PENDING') { ?> href="<?=$lateAttendanceApplicationURL?>?rid=<?=$data->id?>" <?php } else { ?> href="#" <?php } ?>><?=$data->clock_in_status?></a> <?php if($data->apply_status=='APPLIED') { ?><i class="fa fa-check text-danger"></i> <?php } ?> <?php if($data->apply_status=='APPROVED') { ?> <i class="fa fa-check text-danger"></i> <i class="fa fa-check text-success"></i> <?php } ?>
                                <?php if($data->apply_status=='REJECTED') { ?> <i class="fa fa-check text-danger"></i> <i class="fa fa-close text-danger"></i> <?php } ?>
                            </td>
                            <?php } else { ?>
                            <td><?=$data->clock_in_status?></td>
                            <?php } ?>
                            <td><?=($data->clock_in_status=='Late')? $data->late : '-';?> </td>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out?><?php } ?></td>
                            <?php if($data->clock_out_status=='Early'){?>
                            <td <?php if ($data->date===date("Y-m-d")) { echo '';} else { ?> class="bg-danger" <?php } ?>><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out_status?><?php } ?></td>
                            <?php } else {?>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out_status?><?php } ?></td>
                            <?php } ?>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->early?><?php } ?></td>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->work_time?><?php } ?></td>
                        </tr>
                    <?php } ?>


                </table>

                <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                    <thead>
                    <tr class="bg-info">
                        <th colspan="8" style="text-align: center; font-size: 15px; font-weight: bold">Last Month Attendance</th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <th class="bg-green">Clock In</th>
                        <th>Clock In Status</th>
                        <th>Late Time</th>
                        <th class="bg-red">Clock Out</th>
                        <th>Clock Out Status</th>
                        <th>Early Time</th>
                        <th>Work Time</th>
                    </tr>
                    </thead>

                    <?php $res=mysqli_query($conn, "select * from ZKTeco_attendance where employee_id=".$_SESSION['PBI_ID']." and date between '".$lastMonthStartDay."' and '".$lastMonthEndDay."' order by date desc");
                    while($data=mysqli_fetch_object($res)){ $yesterday = date("Y-m-d", strtotime("-1 day")); ?>
                        <tr>
                            <td class="text-center">
                                <?= ($data->date===date("Y-m-d")) ? 'Today' : (($data->date === $yesterday) ? 'Yesterday' : date("d M Y", strtotime($data->date))); ?>
                            </td>
                            <td><?=$data->clock_in?></td>
                            <?php if($data->clock_in_status=='Late'){?>
                                <td class="bg-danger">
                                    <a <?php if($data->apply_status=='PENDING') { ?> href="<?=$lateAttendanceApplicationURL?>?rid=<?=$data->id?>" <?php } else { ?> href="#" <?php } ?>><?=$data->clock_in_status?></a> <?php if($data->apply_status=='APPLIED') { ?><i class="fa fa-check text-danger"></i> <?php } ?> <?php if($data->apply_status=='APPROVED') { ?> <i class="fa fa-check text-danger"></i> <i class="fa fa-check text-success"></i> <?php } ?>
                                    <?php if($data->apply_status=='REJECTED') { ?> <i class="fa fa-check text-danger"></i> <i class="fa fa-close text-danger"></i> <?php } ?>
                                </td>
                            <?php } else { ?>
                                <td><?=$data->clock_in_status?></td>
                            <?php } ?>
                            <td><?=($data->clock_in_status=='Late')? $data->late : '-';?> </td>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out?><?php } ?></td>
                            <?php if($data->clock_out_status=='Early'){?>
                                <td <?php if ($data->date===date("Y-m-d")) { echo '';} else { ?> class="bg-danger" <?php } ?>><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out_status?><?php } ?></td>
                            <?php } else {?>
                                <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->clock_out_status?><?php } ?></td>
                            <?php } ?>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->early?><?php } ?></td>
                            <td><?php if ($data->date===date("Y-m-d")) { echo '-';} else { ?> <?=$data->work_time?><?php } ?></td>
                        </tr>
                    <?php } ?>


                </table>

                <?php
                // Database connection
                $conn = mysqli_connect("host", "username", "password", "database");

                // Variables
                $lastMonthStartDay = '2023-11-01'; // Example start date
                $lastMonthEndDay = '2023-11-30';   // Example end date
                $employee_id = $_SESSION['PBI_ID'];

                // Generate a full list of dates in the range
                $allDates = [];
                $start = new DateTime($lastMonthStartDay);
                $end = new DateTime($lastMonthEndDay);

                while ($start <= $end) {
                    $allDates[] = $start->format("Y-m-d");
                    $start->modify('+1 day');
                }

                // Fetch existing attendance data
                $query = "SELECT * FROM ZKTeco_attendance 
          WHERE employee_id = $employee_id 
          AND date BETWEEN '$lastMonthStartDay' AND '$lastMonthEndDay' 
          ORDER BY date DESC";
                $result = mysqli_query($conn, $query);

                $presentDates = [];
                $attendanceData = [];

                // Store attendance data and dates
                while ($row = mysqli_fetch_assoc($result)) {
                    $presentDates[] = $row['date'];
                    $attendanceData[$row['date']] = $row; // Save entire row with key as date
                }

                // Add missing dates
                foreach ($allDates as $date) {
                    if (!in_array($date, $presentDates)) {
                        // Add missing date with placeholder data
                        $attendanceData[$date] = [
                            'date' => $date,
                            'status' => 'Missing',
                            'other_columns' => 'N/A' // Placeholder for other columns
                        ];
                    }
                }

                // Sort the list by date in descending order
                uksort($attendanceData, function ($a, $b) {
                    return strcmp($b, $a); // Reverse order (descending)
                });

                // Display the data
                echo "<table border='1'>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Other Details</th>
        </tr>";

                foreach ($attendanceData as $data) {
                    echo "<tr>
            <td>{$data['date']}</td>
            <td>" . (isset($data['status']) ? $data['status'] : 'Present') . "</td>
            <td>{$data['other_columns']}</td>
          </tr>";
                }

                echo "</table>";
                ?>


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
        <div class="x_panel" style="height: 300px; overflow: auto">
            <div class="x_title">
                <h2 class="text-danger"><i class="fa fa-bell"></i> Login Logs</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                    <thead>
                    <tr class="bg-info">
                        <th>#</th>
                        <th>Login Time</th>
                        <th>Browser</th>
                        <th>Operating System</th>
                        <th>LogOut</th>
                        <th>IP</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    $queryLog = mysqli_query($conn, "SELECT * from user_activity_log where user_id=".$_SESSION['userid']." order by id desc limit 10");
                    while( $logData = mysqli_fetch_object($queryLog)){ ?>
                    <tr>
                        <td><?=$i=$i+1?></td>
                        <td><?=$logData->access_time?></td>
                        <td><?=$logData->browser?></td>
                        <td><?=$logData->os?></td>
                        <td><?=$logData->access_time_out?></td>
                        <td><?=$logData->ip?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



<div class="col-md-4 col-xs-12">
    <div class="x_panel" style="height: 300px; overflow: auto">
        <div class="x_title">
            <h2 class="text-danger"><i class="fa fa-bell"></i> Upcoming Holiday</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                <thead>
                <tr class="bg-info">
                    <th>#</th>
                    <th>Date</th>
                    <th>Holiday For</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $ho = 0;
                $res=mysqli_query($conn, "SELECT * FROM salary_holy_day WHERE holy_day between '".date('Y-01-01')."' and '".date('Y-12-31')."' order by holy_day asc limit 7");
                while($holiday=mysqli_fetch_object($res)){?>
                    <tr>
                        <td><?=$ho=$ho+1?></td>
                        <td><?=date('l', strtotime($holiday->holy_day)); ?>, <?=date("M d Y", strtotime($holiday->holy_day)); ?></td>
                        <td><?=$holiday->reason?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-md-4 col-xs-12">
    <div class="x_panel fixed_height_390" >
        <div class="x_title">
            <h2 class="text-success"><i class="fa fa-birthday-cake"></i> Upcoming Birthday</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="height: 300px">
            <table align="center" class="table table-striped table-bordered" style="font-size:10px;">
                <thead>
                <tr class="bg-info">
                    <th>#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Date of Birth</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $ho = 0;
                $res=mysqli_query($conn, "SELECT 
    p.PBI_ID_UNIQUE, 
    p.PBI_NAME AS Name, 
    d.DESG_SHORT_NAME AS designation,
    DATE_FORMAT(p.PBI_DOB, '%Y-%m-%d') AS DOB,
    CASE 
        WHEN p.PBI_DOB IS NOT NULL AND p.PBI_DOB != '0000-00-00' THEN 
            CASE 
                WHEN DATE_FORMAT(p.PBI_DOB, CONCAT(YEAR(CURDATE()), '-%m-%d')) >= CURDATE() 
                THEN DATE_FORMAT(p.PBI_DOB, CONCAT(YEAR(CURDATE()), '-%m-%d')) 
                ELSE DATE_FORMAT(p.PBI_DOB, CONCAT(YEAR(CURDATE()) + 1, '-%m-%d')) 
            END
        ELSE 'Date not available'
    END AS next_birthday
FROM 
    personnel_basic_info p
JOIN 
    designation d 
    ON d.DESG_ID = p.PBI_DESIGNATION
WHERE 
    p.PBI_DOB IS NOT NULL AND p.PBI_DOB != '0000-00-00' and 
    p.PBI_JOB_STATUS in ('In Service')
ORDER BY 
    next_birthday 
LIMIT 8;
");
                while($empData=mysqli_fetch_object($res)){?>
                    <tr>
                        <td><?=$ho=$ho+1?></td>
                        <td><?=$empData->Name?></td>
                        <td><?=$empData->designation?></td>
                        <td><?=date('l', strtotime($empData->DOB)); ?>, <?= !empty($empData->DOB) ? date("d M", strtotime($empData->DOB)) : "Date not available"; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-md-4 col-xs-12">
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
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

<?php ob_end_flush(); ?>