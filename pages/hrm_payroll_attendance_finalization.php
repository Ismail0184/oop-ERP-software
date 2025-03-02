<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Attendance Finalization';
$table='hrm_attendance_info';
$unique='id';
$page="hrm_payroll_attendance_finalization.php";

if (isset($_POST['clearManualData'])){
    mysqli_query($conn, "DELETE from ".$table." where status='MANUAL'");
}

if (isset($_POST['confirmAttendance'])){
    mysqli_query($conn, "UPDATE ".$table." SET status='UNCHECKED' where status='MANUAL' and month='".$_SESSION['selectedMonth']."' and year='".$_SESSION['selectedYear']."'");
    unset($_SESSION['selectedYear']);
    unset($_SESSION['selectedMonth']);
    //unset($_POST);
}

if (isset($_POST['initiate'])){
    unset($_SESSION['selectedMonth']);
    $_SESSION['selectedYear'] = @$_POST['selectedYear'];
    $_SESSION['selectedMonth'] = @$_POST['selectedMonth'];
}
if (isset($_POST['updateMonth'])){
    unset($_SESSION['selectedYear']);
    unset($_SESSION['selectedMonth']);
    $_SESSION['selectedYear'] = @$_POST['selectedYear'];
    $_SESSION['selectedMonth'] = @$_POST['selectedMonth'];
}

if (isset($_POST['cancel'])){
    unset($_SESSION['selectedYear']);
    unset($_SESSION['selectedMonth']);
}

$selectedYear =  @$_SESSION['selectedYear'];
$selectedMonth =  @$_SESSION['selectedMonth'];
$year = date('Y');

if(prevent_multi_submit()) {
    if (isset($_POST['submitAttendance'])) {
        $sql = "SELECT p.* From personnel_basic_info p where p.PBI_JOB_STATUS='In Service' order by p.serial";
        $result = mysqli_query($conn, $sql);
        while ($data = mysqli_fetch_object($result)) {
            $id = $data->PBI_ID;
            $PBI_ID = @$data->PBI_ID;
            $present = @$_POST['present' . $id];
            $latePresent = @$_POST['latePresent' . $id];
            $OSD = @$_POST['OSD' . $id];
            $leave = @$_POST['leave' . $id];
            $earlyLeave = @$_POST['earlyLeave' . $id];
            $offDay = @$_POST['offDay' . $id];
            $holiday = @$_POST['holiday' . $id];
            $absent = @$_POST['absent' . $id];
            $deductionDays = @$_POST['deductionDays' . $id];
            $payDay = @$_POST['payDay' . $id];
            $totalDaysInTheMonth = @$_POST['totalDaysInTheMonth' . $id];
            $month = @$_SESSION['selectedMonth'];
            $year = @$_SESSION['selectedYear'];
            $entry_by = @$_SESSION['userid'];
            $entry_at = date('Y-m-d H:i:s');
            $sectionid = @$_SESSION['sectionid'];
            $companyid = @$_SESSION['companyid'];

            if($present>0 || $latePresent) {
                mysqli_query($conn, "INSERT INTO " . $table . " 
            (`PBI_ID`,`present`,`latePresent`,`OSD`,`leave`,`earlyLeave`,`offDay`,`holiday`,`absent`,`deductionDays`,`payDay`,`totalDaysInTheMonth`,`month`,`year`,`entry_by`,`entry_at`,`section_id`,`company_id`,`status`) VALUES 
            ($id,'" . $present . "','" . $latePresent . "','" . $OSD . "','" . $leave . "','" . $earlyLeave . "','" . $offDay . "','" . $holiday . "','" . $absent . "','" . $deductionDays . "','" . $payDay . "','" . $totalDaysInTheMonth . "','" . $month . "','" . $year . "','" . $entry_by . "','" . $entry_at . "','" . $sectionid . "','" . $companyid . "','MANUAL')");
                }
            } // submitAttendance
        unset($_POST);
    }


    $sql = "SELECT p.* From personnel_basic_info p where p.PBI_JOB_STATUS='In Service' order by p.serial";
    $result = mysqli_query($conn, $sql);
    while ($data = mysqli_fetch_object($result)) {
        $id = $data->PBI_ID;
        $PBI_ID = @$data->PBI_ID;
        $present = @$_POST['present' . $id];
        $latePresent = @$_POST['latePresent' . $id];
        $OSD = @$_POST['OSD' . $id];
        $leave = @$_POST['leave' . $id];
        $earlyLeave = @$_POST['earlyLeave' . $id];
        $offDay = @$_POST['offDay' . $id];
        $holiday = @$_POST['holiday' . $id];
        $absent = @$_POST['absent' . $id];
        $deductionDays = @$_POST['deductionDays' . $id];
        $payDay = @$_POST['payDay' . $id];
        $totalDaysInTheMonth = @$_POST['totalDaysInTheMonth' . $id];
        $month = @$_SESSION['selectedMonth'];
        $year = @$_SESSION['selectedYear'];
        $entry_by = @$_SESSION['userid'];
        $entry_at = date('Y-m-d H:i:s');
        $sectionid = @$_SESSION['sectionid'];
        $companyid = @$_SESSION['companyid'];

        if (isset($_POST['addAttendance'.$id])) {
            mysqli_query($conn, "INSERT INTO " . $table . " 
            (`PBI_ID`,`present`,`latePresent`,`OSD`,`leave`,`earlyLeave`,`offDay`,`holiday`,`absent`,`deductionDays`,`payDay`,`totalDaysInTheMonth`,`month`,`year`,`entry_by`,`entry_at`,`section_id`,`company_id`,`status`) VALUES 
            ($id,'" . $present . "','" . $latePresent . "','" . $OSD . "','" . $leave . "','" . $earlyLeave . "','" . $offDay . "','" . $holiday . "','" . $absent . "','" . $deductionDays . "','" . $payDay . "','" . $totalDaysInTheMonth . "','" . $month . "','" . $year . "','" . $entry_by . "','" . $entry_at . "','" . $sectionid . "','" . $companyid . "','MANUAL')");
            unset($_POST);
        }
    }
}



$selectedMonthStartDay = date(''.$selectedYear.'-'.$selectedMonth.'-01');
$selectedMonthEndDay = date(''.$selectedYear.'-'.$selectedMonth.'-31');
$getOffDay = find_a_field('salary_holy_day','COUNT(id)','holy_day between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
$sqlQueryMANUAL = "SELECT p.*,d.DESG_DESC,a.id as jid,a.* From personnel_basic_info p, designation d, hrm_attendance_info a where p.PBI_ID=a.PBI_ID and p.PBI_DESIGNATION=d.DESG_ID and p.PBI_JOB_STATUS='In Service' order by p.serial";
$resultMANUAL = mysqli_query($conn, $sqlQueryMANUAL);
while ($data = mysqli_fetch_object($resultMANUAL)) {
    $id=$data->jid;
    if(isset($_POST['deleteData'.$id]))
    {  $res=mysqli_query($conn, ("DELETE FROM hrm_attendance_info WHERE id=".$id));
        unset($_POST);
    }
}

$sqlQuery = mysqli_query($conn, "SELECT p.*,d.DESG_DESC From personnel_basic_info p, designation d where PBI_ID not in (select PBI_ID from hrm_attendance_info where month='".$selectedMonth."' and year='".$selectedYear."') and p.PBI_DESIGNATION=d.DESG_ID and p.PBI_JOB_STATUS='In Service' order by p.serial");
$countManualData = find_a_field(''.$table.'','COUNT(id)','status ="MANUAL" and month="'.$selectedMonth.'" and year="'.$selectedYear.'"');
?>
<?php require_once 'header_content.php'; ?>
<style>
    #customers {}
    #customers td {}
    #customers tr:ntd-child(even)
    {background-color: #white;}
    #customers tr:hover {background-color: #F0F0F0;}
    td{}
    input[type="number"] {
        font-size: 11px; /* Set your desired font size */
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<?php require_once 'body_content_nva_sm.php'; ?>
<div class="col-md-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php if (isset($_POST['confirmAttendance'])){ ?><h5 class="text-success text-center">Attendance recorded successfully!!</h5><?php } ?>
            <form action="<?=$page;?>" method="post" enctype="multipart/form-data" name="cloud" id="cloud" class="form-horizontal form-label-left">
                <? require_once 'support_html.php';?>
                <table align="center" style="width:60%; font-size: 11px" class="table table-striped table-bordered">
                    <thead>
                    <tr class="bg-primary text-white">
                        <th style="text-align: center">Month</th>
                        <th style="text-align: center">Year</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td align="center" style="vertical-align: middle">
                            <select class="select2_single form-control" style="width:98%; font-size: 11px" tabindex="6" required="required"  name="selectedMonth">
                                <option></option>
                                <?php foreign_relation("monthname", "month", "CONCAT(month,' : ', monthfullName)", $selectedMonth, "1"); ?>
                            </select>
                        </td>
                        <td style="vertical-align: middle">
                            <?php
                            $start_year = 2020; // Starting year
                            $end_year = date('Y'); // Current year or any ending year
                            ?>
                            <select class="select2_single form-control" style="width:98%; font-size: 11px" name="selectedYear">
                                <?php
                                for ($year = $end_year; $year >= $start_year; $year--) { ?>
                                <option value='<?=$year?>'><?=$year?></option>";
                            <?php } ?>
                            </select>
                        </td>
                        <td align="center" style="width:15%; vertical-align:middle">
                            <?php if (isset($selectedMonth)) {?>
                                <button type="submit" name="updateMonth" onclick='return window.confirm("Are you confirm to Update month?");' class="btn btn-primary" style="font-size: 11px"> <i class="fa fa-edit"></i> Update</button>
                                <button type="submit" name="cancel" onclick='return window.confirm("Are you confirm to Cancel Month?");' class="btn btn-danger" style="font-size: 11px"> <i class="fa fa-close"></i> Cancel</button>
                            <?php } else { ?>
                                <button type="submit" name="initiate" onclick='return window.confirm("Are you confirm to Initiate?");' class="btn btn-primary" style="font-size: 11px"> <i class="fa fa-hourglass-start"></i> Initiate</button>
                            <?php } ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>



<?php if (isset($selectedMonth)) {?>
<div class="col-md-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <?php if (isset($_POST['submitAttendance'])){ ?><h5 class="text-primary text-center">Attendance submit successfully!!</h5> <?php } ?>
            <?php if (isset($_POST['clearManualData'])){ ?><h5 class="text-danger text-center">Manual attendance removed successfully!!</h5> <?php } ?>

            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?=$page;?>" method="post" enctype="multipart/form-data" name="cloud" id="cloud" style="font-size: 11px" class="form-horizontal form-label-left">
                <? require_once 'support_html.php';?>
                <table id="customers" align="center" style="width:100%; font-size: 11px" class="table table-striped table-bordered">
                    <thead>
                    <tr class="bg-primary text-white">
                        <th style="text-align: center; vertical-align: middle">#</th>
                        <th style="text-align: center; vertical-align: middle">ID</th>
                        <th style="text-align: center; vertical-align: middle">Name</th>
                        <th style="text-align: center; vertical-align: middle">Designation</th>
                        <th style="text-align: center; vertical-align: middle">Present</th>
                        <th style="text-align: center; vertical-align: middle">Late Present</th>
                        <th style="text-align: center; vertical-align: middle">OSD</th>
                        <th style="text-align: center; vertical-align: middle">Leave</th>
                        <th style="text-align: center; vertical-align: middle">Early Leave</th>
                        <th style="text-align: center; vertical-align: middle">Off Day</th>
                        <th style="text-align: center; vertical-align: middle">Holiday</th>
                        <th style="text-align: center; vertical-align: middle">Absent</th>
                        <th style="text-align: center; vertical-align: middle">Deduction Day <br>(Late + Absent)</th>
                        <th style="text-align: center; vertical-align: middle">Pay Day</th>
                        <th style="text-align: center; vertical-align: middle">Total Days</th>
                        <th style="text-align: center; vertical-align: middle">Option</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $totalDaysInTheMonth = getTotalDaysInMonth($year, $selectedMonth);
                    $getTotalFridays = countFridaysInMonth(date('Y'), $selectedMonth);
                    $i = 0;
                    while($data=mysqli_fetch_object($sqlQuery)){
                        $id = $data->PBI_ID;
                        $totalPresent = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_in_status="On Time" and employee_id='.$id);
                        $totalLatePresent = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_in_status="Late" and employee_id='.$id);
                        $totalLatePresentApproved = find_a_field('hrm_late_attendance','COUNT(id)','status in ("RECOMMENDED","APPROVED") and attendance_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and PBI_ID='.$id);
                        $deductionForLateDays = $totalLatePresent-$totalLatePresentApproved;

                        $totalOSD = find_a_field('hrm_od_attendance','COUNT(id)','approved_status="APPROVED" and attendance_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and PBI_ID='.$id);
                        $totalLeave = find_a_field('hrm_leave_info','SUM(total_days)','approved_status="APPROVED" and half_or_full="Full" and PBI_ID='.$id.' and s_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');

                        $totalEarlyLeave = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_out_status="Early" and employee_id='.$id);

                        $totalOffDay = find_a_field('salary_holy_day','COUNT(id)','holy_day between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
                        $totalFriday = countFridaysInMonth(date('Y', strtotime($selectedMonthStartDay)),date('m', strtotime($selectedMonthStartDay)));
                        //$totalAbsent =  $totalDaysInTheMonth- ($totalPresent+$totalLatePresent+$totalLeave+$totalOSD+$totalFriday+$totalOffDay);

                        $getEarlyLeaveDays = find_a_field('hrm_leave_info','COUNT(id)','half_or_full="Half" and PBI_ID='.$id.' and s_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
                        $totalAbsent =  $totalDaysInTheMonth- ($totalPresent+$totalLatePresent+$totalLeave+$totalOSD+$totalFriday+$totalOffDay);
                        $totalDeductionDays = floor($deductionForLateDays/3)+$totalAbsent;
                        if($totalDeductionDays>0) {
                            $totalPayDays = ($totalPresent + $totalLatePresent + $totalLeave + $totalOSD + $totalFriday + $totalAbsent + $totalOffDay) - $totalDeductionDays;
                        } else {
                            $totalPayDays = ($totalPresent + $totalLatePresent + $totalLeave + $totalOSD + $totalFriday + $totalAbsent + $totalOffDay);

                        }
                        ?>
                    <tr>
                        <td style="vertical-align: middle;"><?=$i=$i+1;?></td>
                        <td style="vertical-align: middle;"><?=$data->PBI_ID_UNIQUE;?></td>
                        <td style="vertical-align: middle"><?=$data->PBI_NAME;?></td>
                        <td style="vertical-align: middle; text-align: left"><?=$data->DESG_DESC;?></td>
                        <td style="vertical-align: middle"><input type="number" name="present<?=$id?>" id="present<?=$id?>" value="<?=($totalPresent>0)? $totalPresent : '';?>" class="form-control col-md-7 col-xs-12 text-center present<?=$id?>"   tabindex="2" /></td>
                        <td style="vertical-align: middle"><input type="number" name="latePresent<?=$id?>" id="latePresent<?=$id?>" value="<?=($totalLatePresent>0)? $totalLatePresent : '';?>" name="offDay<?=$id?>"    class="form-control col-md-7 col-xs-12 text-center"   tabindex="3" /></td>
                        <td style="vertical-align: middle"><input type="number" name="OSD<?=$id?>"  id="OSD<?=$id?>" value="<?=($totalOSD>0)? $totalOSD : '';?>" class="form-control col-md-7 col-xs-12 text-center OSD<?=$id?>"   tabindex="2" /></td>
                        <td style="vertical-align: middle"><input type="number" name="leave<?=$id?>" id="leave<?=$id?>" value="<?=($totalLeave>0)? number_format($totalLeave,0) : '';?>"            class="form-control col-md-7 col-xs-12 text-center"   tabindex="4" /></td>
                        <td style="vertical-align: middle"><input type="number" name="earlyLeave<?=$id?>" id="earlyLeave<?=$id?>" value="<?=($totalEarlyLeave>0)? $totalEarlyLeave : '';?>" readonly  class="form-control col-md-7 col-xs-12 text-center"   tabindex="0" /></td>
                        <td style="vertical-align: middle"><input type="number" name="offDay<?=$id?>" id="offDay<?=$id?>" value="<?=($totalOffDay>0)? $totalOffDay : '';?>" readonly class="form-control col-md-7 col-xs-12 text-center"   tabindex="1" /></td>
                        <td style="vertical-align: middle"><input type="number" name="holiday<?=$id?>" id="holiday<?=$id?>" value="<?=($totalFriday>0)? number_format($totalFriday,0) : '';?>" readonly class="form-control col-md-7 col-xs-12 text-center"   tabindex="5" /></td>
                        <td style="vertical-align: middle"><input type="number" name="absent<?=$id?>" id="absent<?=$id?>" value="<?=$totalAbsent?>" class="form-control col-md-7 col-xs-12 text-center"   tabindex="6" /></td>
                        <td style="vertical-align: middle"><input type="number" name="deductionDays<?=$id?>" id="deductionDays<?=$id?>" value="<?=($totalDeductionDays>.99)? number_format($totalDeductionDays) : '';?>" readonly  class="form-control col-md-7 col-xs-12 text-center deductionDays<?=$id?>"   tabindex="7" /></td>
                        <td style="vertical-align: middle"><input type="number" name="payDay<?=$id?>" id="payDay<?=$id?>" value="<?=($totalPayDays>0)? number_format($totalPayDays) : '';?>"  readonly class="form-control col-md-7 col-xs-12 text-center payDay<?=$id?>"   tabindex="7" /></td>
                        <td style="vertical-align: middle"><input type="number" name="totalDaysInTheMonth<?=$id?>" id="totalDaysInTheMonth<?=$id?>" value="<?=($totalDaysInTheMonth>0)? $totalDaysInTheMonth : '';?>" readonly  class="form-control col-md-7 col-xs-12 text-center"   tabindex="7" /></td>
                        <td style="vertical-align: middle">
                            <button type="submit" name="addAttendance<?=$id?>" onclick='return window.confirm("Are you confirm to submit data?");' class="btn btn-primary text-center" style="font-size: 11px"> <i class="fa fa-plus"></i> Add</button>
                        </td>

                        <script>
                            $(function(){
                                $('#present<?=$id;?>, #OSD<?=$id;?>, #leave<?=$id;?>, #offDay<?=$id;?>, #holiday<?=$id;?>, #latePresent<?=$id;?>, #deductionDays<?=$id;?>, #absent<?=$id;?>').keyup(function(){
                                    var present = parseFloat($('#present<?=$id;?>').val()) || 0;
                                    var OSD = parseFloat($('#OSD<?=$id;?>').val()) || 0;
                                    var leave = parseFloat($('#leave<?=$id;?>').val()) || 0;
                                    var offDay = parseFloat($('#offDay<?=$id;?>').val()) || 0;
                                    var holiday = parseFloat($('#holiday<?=$id;?>').val()) || 0;
                                    var latePresent = parseFloat($('#latePresent<?=$id;?>').val()) || 0;
                                    var absent = parseFloat($('#absent<?=$id;?>').val()) || 0;
                                    var deductionDays = parseFloat($('#deductionDays<?=$id;?>').val()) || 0;



                                    var latePresent = parseFloat($('#latePresent<?=$id;?>').val()) || 0;
                                    var latePenalty = Math.floor(latePresent / 3)-<?=$totalLatePresentApproved = find_a_field('hrm_late_attendance','COUNT(id)','attendance_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and PBI_ID='.$id)?>; // 1 day deducted for every 3 latePresent days
                                    var deductionDays = absent + latePenalty;
                                    $('#deductionDays<?=$id;?>').val(deductionDays);

                                    var totalDays = present + OSD + leave + offDay + holiday + latePresent;
                                    var payDay = totalDays - deductionDays;
                                    $('#payDay<?=$id;?>').val(payDay);
                                });
                            });
                        </script>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="col text-center">
                    <button type="submit" name="cancel" onclick='return window.confirm("Are you confirm to Cancel Month?");' class="btn btn-danger" style="font-size: 13px"> <i class="fa fa-close"></i> Cancel</button>
                    <button type="submit" name="submitAttendance" onclick='return window.confirm("Are you confirm to submit data?");' class="btn btn-primary text-center" style="font-size: 13px"> <i class="fa fa-plus"></i> Submit Attendance</button>
                </div>


                <?php if ($countManualData>0){ ?>
                <table id="customers" align="center" style="width:100%; font-size: 11px; margin-top: 10px" class="table table-striped table-bordered">
                    <thead>
                    <tr class="bg-primary text-white">
                        <th style="text-align: center; vertical-align: middle">#</th>
                        <th style="text-align: center; vertical-align: middle">ID</th>
                        <th style="text-align: center; vertical-align: middle">Name</th>
                        <th style="text-align: center; vertical-align: middle">Designation</th>
                        <th style="text-align: center; vertical-align: middle">Present</th>
                        <th style="text-align: center; vertical-align: middle">Late Present</th>
                        <th style="text-align: center; vertical-align: middle">OSD</th>
                        <th style="text-align: center; vertical-align: middle">Leave</th>
                        <th style="text-align: center; vertical-align: middle">Early Leave</th>
                        <th style="text-align: center; vertical-align: middle">Off Day</th>
                        <th style="text-align: center; vertical-align: middle">Holiday</th>
                        <th style="text-align: center; vertical-align: middle">Absent</th>
                        <th style="text-align: center; vertical-align: middle">Deduction Day <br>(Late + Absent)</th>
                        <th style="text-align: center; vertical-align: middle">Pay Day</th>
                        <th style="text-align: center; vertical-align: middle">Total Days</th>
                        <th style="text-align: center; vertical-align: middle">Option</th>

                    </tr>
                    </thead>

                    <?php
                    $is = 0;
                    $resultMANUAL = mysqli_query($conn, $sqlQueryMANUAL);
                    while ($data = mysqli_fetch_object($resultMANUAL)){
                    ?>

                    <tbody>
                    <tr>
                        <td style="vertical-align: middle;"><?=$is=$is+1;?></td>
                        <td style="vertical-align: middle;"><?=$data->PBI_ID;?>:<?=$data->PBI_ID_UNIQUE;?></td>
                        <td style="vertical-align: middle"><?=$data->PBI_NAME;?></td>
                        <td style="vertical-align: middle; text-align: left"><?=$data->DESG_DESC;?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->present>0)? $data->present : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->latePresent>0)? $data->latePresent : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->OSD>0)? $data->OSD : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->leave>0)? $data->leave : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->earlyLeave>0)? $data->earlyLeave : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->offDay>0)? $data->offDay : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->holiday>0)? $data->holiday : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->absent>0)? $data->absent : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->deductionDays>0)? $data->deductionDays : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->payDay>0)? $data->payDay : '-'; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?=($data->totalDaysInTheMonth>0)? $data->totalDaysInTheMonth : '-'; ?></td>
                        <td class="text-center">
                            <button type="submit" name="deleteData<?=$data->id;?>" style="background-color:transparent;color:red; border:none; margin:0px; font-size:13px; padding:0px" onclick="return window.confirm('Are you sure you want to delete this?');" title="Delete Record" data-toggle="tooltip"><span class="glyphicon glyphicon-trash"></span></button>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="col text-center">
                    <button type="submit" name="clearManualData" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-danger text-center" style="font-size: 13px"> <i class="fa fa-eraser"></i> Clear Manual Data</button>
                    <button type="submit" name="confirmAttendance" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-success text-center" style="font-size: 13px"> <i class="fa fa-check"></i> Confirm Attendance</button>
                </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<?php } ?>
<?=$html->footer_content();mysqli_close($conn);?>
