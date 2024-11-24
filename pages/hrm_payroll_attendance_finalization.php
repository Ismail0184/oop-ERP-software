<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Attendance Finalization';
$table='hrm_attendance_info';
$unique='id';
$page="hrm_payroll_attendance_finalization.php";

if (isset($_POST['clearAllData'])){
    mysqli_query($conn, "DELETE from ".$table." where status='MANUAL'");
}

if (isset($_POST['confirmAllData'])){
    mysqli_query($conn, "UPDATE ".$table." SET status='CHECKED' where status='MANUAL'");
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
    if (isset($_POST['confirmAttendance'])) {
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

            mysqli_query($conn, "INSERT INTO " . $table . " 
            (`PBI_ID`,`present`,`latePresent`,`OSD`,`leave`,`earlyLeave`,`offDay`,`holiday`,`absent`,`deductionDays`,`payDay`,`totalDaysInTheMonth`,`month`,`year`,`entry_by`,`entry_at`,`section_id`,`company_id`) VALUES 
            ($id,'" . $present . "','" . $latePresent . "','" . $OSD . "','" . $leave . "','" . $earlyLeave . "','" . $offDay . "','" . $holiday . "','" . $absent . "','" . $deductionDays . "','" . $payDay . "','" . $totalDaysInTheMonth . "','" . $month . "','" . $year . "','" . $entry_by . "','" . $entry_at . "','" . $sectionid . "','" . $companyid . "')");
        }
        unset($_POST);
    }
}
$selectedMonthStartDay = date(''.$selectedYear.'-'.$selectedMonth.'-01');
$selectedMonthEndDay = date(''.$selectedYear.'-'.$selectedMonth.'-31');
$getOffDay = find_a_field('salary_holy_day','COUNT(id)','holy_day between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
$sqlQuery = mysqli_query($conn, "SELECT p.*,d.DESG_DESC From personnel_basic_info p, designation d where p.PBI_DESIGNATION=d.DESG_ID and p.PBI_JOB_STATUS='In Service' order by p.serial");

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
<?php require_once 'body_content_nva_sm.php'; ?>
<div class="col-md-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
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
                                <?php foreign_relation("monthname", "month", "CONCAT(month,' : ', monthfullName)", $_SESSION['selectedMonth'], "1"); ?>
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
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?=$page;?>" method="post" enctype="multipart/form-data" name="cloud" id="cloud" style="font-size: 11px" class="form-horizontal form-label-left">
                <? require_once 'support_html.php';?>
                <table id="customers" align="center" style="width:100%; font-size: 11px" class="table table-striped table-bordered">
                    <thead>
                    <tr class="bg-primary text-white">
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

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $totalDaysInTheMonth = getTotalDaysInMonth($year, $selectedMonth);
                    $getTotalFridays = countFridaysInMonth(date('Y'), $selectedMonth);
                    while($data=mysqli_fetch_object($sqlQuery)){
                        $id = $data->PBI_ID;
                        $totalPresent = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_in_status="On Time" and employee_id='.$id);
                        $totalLatePresent = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_in_status="Late" and employee_id='.$id);
                        $totalLatePresentApproved = find_a_field('hrm_late_attendance','COUNT(id)','attendance_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and PBI_ID='.$id);
                        $deductionForLateDays = $totalLatePresent-$totalLatePresentApproved;

                        $totalOSD = find_a_field('hrm_od_attendance','COUNT(id)','attendance_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and PBI_ID='.$id);

                        $totalLeave = find_a_field('hrm_leave_info','SUM(total_days)','half_or_full="Full" and PBI_ID='.$id.' and s_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
                        $totalEarlyLeave = find_a_field('ZKTeco_attendance','COUNT(id)','date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'" and clock_out_status="Early" and employee_id='.$id);

                        $totalOffDay = find_a_field('salary_holy_day','COUNT(id)','holy_day between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
                        $totalFriday = countFridaysInMonth(date('Y', strtotime($selectedMonthStartDay)),date('m', strtotime($selectedMonthStartDay)));
                        $totalAbsent =  $totalDaysInTheMonth- ($totalPresent+$totalLatePresent+$totalLeave+$totalOSD+$totalFriday);

                        $getEarlyLeaveDays = find_a_field('hrm_leave_info','COUNT(id)','half_or_full="Half" and PBI_ID='.$id.' and s_date between "'.$selectedMonthStartDay.'" and "'.$selectedMonthEndDay.'"');
                        $totalAbsent =  $totalDaysInTheMonth- ($totalPresent+$totalLatePresent+$totalLeave+$totalOSD+$totalFriday);
                        $totalDeductionDays = ($deductionForLateDays/3)+$totalAbsent;
                        $totalPayDays = ($totalPresent+$totalLatePresent+$totalLeave+$totalOSD+$totalFriday+$totalAbsent)-$totalDeductionDays;
                        ?>
                    <tr>
                        <td style="vertical-align: middle;"><?=$data->PBI_ID;?>:<?=$data->PBI_ID_UNIQUE;?></td>
                        <td style="vertical-align: middle"><?=$data->PBI_NAME;?></td>
                        <td style="vertical-align: middle; text-align: left"><?=$data->DESG_DESC;?></td>
                        <td style="vertical-align: middle"><input type="number"  <?php if($totalPresent>0) { ?> readonly <?php } ?> name="present<?=$id?>"  value="<?=($totalPresent>0)? $totalPresent : '';?>" class="form-control col-md-7 col-xs-12 text-center"   tabindex="2" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="latePresent<?=$id?>"  value="<?=($totalLatePresent>0)? $totalLatePresent : '';?>" name="offDay<?=$id?>"    class="form-control col-md-7 col-xs-12 text-center"   tabindex="3" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="OSD<?=$id?>"  value="<?=($totalOSD>0)? $totalOSD : '';?>" class="form-control col-md-7 col-xs-12 text-center"   tabindex="2" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="leave<?=$id?>" value="<?=($totalLeave>0)? number_format($totalLeave,0) : '';?>"            class="form-control col-md-7 col-xs-12 text-center"   tabindex="4" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="earlyLeave<?=$id?>"  value="<?=($totalEarlyLeave>0)? $totalEarlyLeave : '';?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="0" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="offDay<?=$id?>" value="<?=($totalOffDay>0)? $totalOffDay : '';?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="1" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="holiday<?=$id?>" value="<?=($totalFriday>0)? number_format($totalFriday,0) : '';?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="5" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly name="absent<?=$id?>" value="<?=$totalAbsent?>" class="form-control col-md-7 col-xs-12 text-center"   tabindex="6" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly value="<?=($totalDeductionDays>0)? number_format($totalDeductionDays) : '';?>" name="deductionDays<?=$id?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="7" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly value="<?=($totalPayDays>0)? number_format($totalPayDays) : '';?>" name="payDay<?=$id?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="7" /></td>
                        <td style="vertical-align: middle"><input type="number"  readonly value="<?=($totalDaysInTheMonth>0)? $totalDaysInTheMonth : '';?>" name="totalDaysInTheMonth<?=$id?>"  class="form-control col-md-7 col-xs-12 text-center"   tabindex="7" /></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="col text-center">
                        <button type="submit" name="clearAllData" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-danger text-center" style="font-size: 11px"> <i class="fa fa-eraser"></i> Clear Manual Data</button>
                        <button type="submit" name="confirmAttendance" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-success text-center" style="font-size: 11px"> <i class="fa fa-check"></i> Confirm Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<?=$html->footer_content();mysqli_close($conn);?>
