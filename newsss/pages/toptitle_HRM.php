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






if (isset($_POST['mon']) && $_POST['mon'] != '') {
    $mon = $_POST['mon'];
} else {
    $mon = date('m');
}

if (isset($_POST['year']) && $_POST['year'] != '') {
    $year = $_POST['year'];
} else {
    $year = date('Y');
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
$days_mon=ceil(($endTime - $startTime)/(3600*24))+1;
$holy_day=find_a_field('salary_holy_day','count(holy_day)','holy_day between "'.$year.'-'.$mon.'-'.'01'.'" and "'.$year.'-'.$mon.'-'.$days_mon.'"');
$late_attendance=find_a_field('hrm_late_attendance','count(id)','attendance_date between "'.$year.'-'.$mon.'-'.'01'.'" and "'.$year.'-'.$mon.'-'.$days_mon.'" and PBI_ID="'.$_SESSION['PBI_ID'].'"');
$sdte=$year.'-'.$mon.'-'."01";
$edte=$year.'-'.$mon.'-'."31";
$current_month_leave=find_a_field('hrm_leave_info','SUM(total_days)','half_or_full="Full" and PBI_ID="'.$_SESSION['PBI_ID'].'" and s_date between "'.$sdte.'" and "'.$edte.'" and e_date between "'.$sdte.'" and "'.$edte.'"');
$current_month_early_leave=find_a_field('hrm_leave_info','COUNT(id)','half_or_full="Half" and PBI_ID="'.$_SESSION['PBI_ID'].'" and s_date between "'.$sdte.'" and "'.$edte.'"');
$current_month_od_attendance=find_a_field('hrm_od_attendance','count(id)','PBI_ID="'.$_SESSION['PBI_ID'].'" and attendance_date between "'.$sdte.'" and "'.$edte.'"');
$dashboardpermission=find_a_field('user_permissions_dashboard','COUNT(module_id)','user_id='.$_SESSION['userid'].' and module_id='.$_SESSION['module_id'].'');
?>

<?php if($dashboardpermission>0){
$totalemployee=find_a_field('personnel_basic_info','COUNT(PBI_ID)','PBI_JOB_STATUS="In Service"'); ?>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel fixed_height_290" >
            <div class="x_title">
                <h2 class="text-danger"><i class="fa fa-calendar"></i> Manpower Statistics</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="left" class="table table-striped table-bordered" style="width:48%;font-size:12px">
                    <thead>
                    <tr class="bg-primary">
                        <th style="text-align: center">Gander</th>
                        <th style="text-align: center">No. of Employee</th>
                        <th style="text-align: center">Percentage(%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $resgender=mysqli_query($conn, "SELECT PBI_SEX,COUNT(PBI_ID) as noofemployee from personnel_basic_info where PBI_JOB_STATUS='In Service' group by PBI_SEX");
                    while($gender=mysqli_fetch_object($resgender)){
                        ?>
                        <tr>
                            <td style="text-align: center"><?=$gender->PBI_SEX;?></td>
                            <td style="text-align: center"><?=$gender->noofemployee;?></td>
                            <td style="text-align: center"><?=number_format($gender->noofemployee/$totalemployee*100,2);?> %</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>


                <table align="right" class="table table-striped table-bordered" style="width:48%;font-size:12px">
                    <thead>
                    <tr class="bg-success">
                        <th style="text-align: center">Marital Status</th>
                        <th style="text-align: center">No. of Employee</th>
                        <th style="text-align: center">Percentage(%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $resmarital=mysqli_query($conn, "SELECT PBI_MARITAL_STA,COUNT(PBI_ID) as noofemployee from personnel_basic_info where PBI_JOB_STATUS='In Service' group by PBI_MARITAL_STA");
                    while($marital=mysqli_fetch_object($resmarital)){?>
                        <tr>
                            <td style="text-align: center"><?=$marital->PBI_MARITAL_STA;?></td>
                            <td style="text-align: center"><?=$marital->noofemployee;?></td>
                            <td style="text-align: center"><?=number_format($marital->noofemployee/$totalemployee*100,2);?> %</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xs-12 widget widget_tally_box">
        <div class="x_panel fixed_height_390" >
            <div class="x_title">
                <h2><i class="fa fa-birthday-cake"></i> Birthday (This month)</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="overflow: auto;height: 320px">
                <ul class="legend list-unstyled">
                    <?php
                    $cday=date('d');
                    $sdate='$year-$mon-$cday';
                    $todate=$dyear-$dmon-$dday;

                    $dateArrays = explode("-", $sdate);
                    if (count($dateArrays) === 3) {
                        list($years, $months, $days) = $dateArrays;
                    } else {
                        echo "Invalid date format";
                    }

                    $dateArraye = explode("-", $todate);
                    if (count($dateArraye) === 3) {
                        list($yeare, $monthe, $daye) = $dateArraye;
                    } else {
                    }




                    $res=mysqli_query($conn, 'select p2.*,d.*,de.* FROM 							 
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
                        if($month==$mon){?>
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
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="x_panel fixed_height_390" >
            <div class="x_title">
                <h2><i class="fa fa-calendar"></i> Holiday Calender</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="legend list-unstyled">
                    <?php
                    $res=mysqli_query($conn, "SELECT * FROM salary_holy_day WHERE holy_day between '$year-$mon-$cday' and '$dyear-$dmon-$dday' order by id asc limit 5");
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
    <?php } ?>
