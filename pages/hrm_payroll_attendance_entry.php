<?php require_once 'support_file.php';require_once 'report.class.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Attendance Upload';
$unique='id';
$table='ZKTeco_attendance';
$crud      =new crud($table);

$page="hrm_payroll_attendance_entry.php";

if(prevent_multi_submit()){

    if (isset($_POST['submit']) && isset($_FILES['file'])) {
        mysqli_query($conn, "DELETE from ZKTeco_basic_data where record_status = 'PENDING' and dataFor='HeadOffice'");
        $filename = $_FILES["file"]["tmp_name"];
        if ($_FILES["file"]["size"] > 0) {
            $file = fopen($filename, "r");
            while (($eData = fgetcsv($file, 10000, ",")) !== FALSE) {
                $entry_at = date('Y-m-d H:i:s');
                $entry_by = $_SESSION['userid'];
                $employee_id = find_a_field('personnel_basic_info', 'PBI_ID', 'PBI_ID_UNIQUE in ("'.$eData[1].'") and PBI_JOB_STATUS="In Service"');
                $input_date = $eData[3];
                $formatted_date = date("Y-m-d H:i:s", strtotime($input_date));
                $attendanceDate = date("Y-m-d", strtotime($input_date));

                // Check for duplicates
                $check_query = "SELECT COUNT(*) AS count FROM ZKTeco_basic_data WHERE dataFor='HeadOffice' and employee_id = '".$employee_id."' AND clock_time = '".$formatted_date."'";
                $check_result = mysqli_query($conn, $check_query);
                $check_row = mysqli_fetch_assoc($check_result);

                if ($attendanceDate==date('Y-m-d'))
                {
                    $recordStatus = "PENDING";
                } else {
                    $recordStatus = "FINISHED";
                }

                if ($check_row['count'] == 0 && $employee_id>0) {

                    $sql = "INSERT INTO ZKTeco_basic_data (employee_id, attendance_date, clock_time, upload_at, upload_by, status,record_status,dataFor)
                        VALUES ('".$employee_id."', '".$attendanceDate."','".$formatted_date."', '".$entry_at."', '".$entry_by."', 'MANUAL','".$recordStatus."','HeadOffice')";
                    $result = mysqli_query($conn, $sql);

                    if (!$result) {
                        echo "<script type=\"text/javascript\">
                            alert(\"Invalid File: Please Upload CSV File.\");
                            window.location = '".$page."';
                        </script>";
                        exit;
                    }
                } else {
                    // Handle duplicate record (optional)
                    echo "<script type=\"text/javascript\">
                        console.log(\"Duplicate entry found for Employee ID: ".$employee_id." on Date: ".$attendanceDate."\");
                    </script>";
                }
            }
            fclose($file);

            // Success message
            echo "<script type=\"text/javascript\">
                alert(\"CSV File has been successfully Imported.\");
                window.location = '".$page."';
            </script>";
        }
        if (!headers_sent()) {
            header("Location: " . $page);
            exit;
        } else {
            die("Headers already sent. Cannot redirect.");
        }
    }

}


if (isset($_POST['confirmAllData'])){
    mysqli_query($conn, "DELETE from ZKTeco_attendance where dataFor='HeadOffice' and record_status = 'PENDING'");
    $sqlQuery = "
    SELECT 
    a.employee_id AS Employee,
    p.PBI_NAME as EmployeeName,
    DATE(a.clock_time) AS Date,
    MIN(a.clock_time) AS Clock_In,
    MAX(a.clock_time) AS Clock_Out,
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 'Late'
        ELSE 'On Time'
    END AS Check_In_Status,
    CASE
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 'Early'
        ELSE 'On Time'
    END AS Check_Out_Status,
    
    TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) AS Work_Time,
    CASE
        WHEN TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) > '09:00:00' THEN 
            SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time))) - TIME_TO_SEC('08:00:00'))
        ELSE '00:00:00'
    END AS OT_Time,
    -- Late Time Calculation
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 
            TIMEDIFF(MIN(TIME(a.clock_time)), '09:45:00')
        ELSE '00:00:00'
    END AS Late_Time,
    -- Early Time Calculation
    CASE 
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 
            TIMEDIFF('18:15:00', MAX(TIME(a.clock_time)))
        ELSE '00:00:00'
    END AS Early_Time,a.status
FROM 
    ZKTeco_basic_data a
    JOIN personnel_basic_info p ON a.employee_id = p.PBI_ID where a.status='MANUAL' and a.dataFor='HeadOffice'
GROUP BY 
    a.employee_id, DATE(a.clock_time)";
    $result= mysqli_query($conn, $sqlQuery);
    while($data=mysqli_fetch_object($result))
    {
        $_POST['on_duty'] = '09:30:00';
        $_POST['off_duty'] = '18:30:00';
        $_POST['dataFor'] = 'HeadOffice';
        $_POST['employee_id'] = $data->Employee;
        $_POST['date'] = $data->Date;
        $_POST['clock_in'] = $data->Clock_In;
        $_POST['clock_out'] = $data->Clock_Out;
        $_POST['clock_in_status'] = $data->Check_In_Status;
        $_POST['clock_out_status'] = $data->Check_Out_Status;
        $_POST['late'] = $data->Late_Time;
        $_POST['early'] = $data->Early_Time;
        $_POST['absent'] = 0;
        $_POST['OT_time'] = $data->OT_Time;
        $_POST['work_time'] = $data->Work_Time;
        if ($data->Date==date('Y-m-d'))
        {
            $_POST['record_status'] = "PENDING";
        } else {
            $_POST['record_status'] = "FINISHED";
        }
        $crud->insert();
        unset($_POST);
    }
    mysqli_query($conn, "UPDATE ZKTeco_basic_data SET status='CHECKED' where dataFor='HeadOffice' and status='MANUAL'");
} // if isset submit


if (isset($_POST['clearAllData'])){
    mysqli_query($conn, "DELETE from ZKTeco_basic_data where dataFor='HeadOffice' and status='MANUAL'");
}

if(isset($_POST['viewReport'])) {
    $dateConn=' and a.attendance_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
    $sqlQuery = "
    SELECT 
    a.employee_id AS Employee,
    p.PBI_NAME as EmployeeName,
    DATE(a.clock_time) AS Date,
    MIN(a.clock_time) AS Clock_In,
    MAX(a.clock_time) AS Clock_Out,
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 'Late'
        ELSE 'On Time'
    END AS Check_In_Status,
    CASE
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 'Early'
        ELSE 'On Time'
    END AS Check_Out_Status,
    
    TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) AS Work_Time,
    CASE
        WHEN TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) > '09:00:00' THEN 
            SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time))) - TIME_TO_SEC('08:00:00'))
        ELSE '00:00:00'
    END AS OT_Time,
    -- Late Time Calculation
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 
            TIMEDIFF(MIN(TIME(a.clock_time)), '09:15:00')
        ELSE '00:00:00'
    END AS Late_Time,
    -- Early Time Calculation
    CASE 
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 
            TIMEDIFF('18:15:00', MAX(TIME(a.clock_time)))
        ELSE '00:00:00'
    END AS Early_Time,a.status
FROM 
    ZKTeco_basic_data a
    JOIN personnel_basic_info p ON a.employee_id = p.PBI_ID where a.dataFor='HeadOffice'".$dateConn."
GROUP BY 
    a.employee_id, DATE(a.clock_time)";
} else {
    $sqlQuery = "
    SELECT 
    a.employee_id AS Employee,
    p.PBI_NAME as EmployeeName,
    DATE(a.clock_time) AS Date,
    MIN(a.clock_time) AS Clock_In,
    MAX(a.clock_time) AS Clock_Out,
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 'Late'
        ELSE 'On Time'
    END AS Check_In_Status,
    CASE
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 'Early'
        ELSE 'On Time'
    END AS Check_Out_Status,
    
    TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) AS Work_Time,
    CASE
        WHEN TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time)) > '09:00:00' THEN 
            SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(MAX(a.clock_time), MIN(a.clock_time))) - TIME_TO_SEC('08:00:00'))
        ELSE '00:00:00'
    END AS OT_Time,
    -- Late Time Calculation
    CASE 
        WHEN MIN(TIME(a.clock_time)) > '09:45:00' THEN 
            TIMEDIFF(MIN(TIME(a.clock_time)), '09:45:00')
        ELSE '00:00:00'
    END AS Late_Time,
    -- Early Time Calculation
    CASE 
        WHEN MAX(TIME(a.clock_time)) < '18:15:00' THEN 
            TIMEDIFF('18:15:00', MAX(TIME(a.clock_time)))
        ELSE '00:00:00'
    END AS Early_Time,a.status
FROM 
    ZKTeco_basic_data a
    JOIN personnel_basic_info p ON a.employee_id = p.PBI_ID where a.dataFor='HeadOffice' and a.status='MANUAL'
GROUP BY 
    a.employee_id, DATE(a.clock_time)";
    $result = mysqli_query($conn, $sqlQuery);
}
?>

<?php require_once 'header_content.php'; ?>
<style>
    input[type=text],input[type=file] {
        width: 80%;
    }
    input[type=text]{
        font-size: 11px;
        width: 80%;}

    input[type=file]{
        font-size: 11px;
        width: 80%;}
    .col-xs-12{
        font-size: 11px;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>

<?php require_once 'body_content_nva_sm.php'; ?>
<div class="col-md-12 col-xs-12">

    <div class="<?php if(isset($_POST['viewReport'])){ ?> row <?php } else { echo 'row collapse';} ?>" id="experience2">
        <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
            <table align="center" style="width: 50%;">
                <tr><td>
                        <input type="date"  style="width:150px; font-size: 11px; height: 25px"  value="<?=(@$_POST['f_date']!='')? $_POST['f_date'] : date('Y-m-01') ?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                    <td style="width:10px; text-align:center"> -</td>
                    <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?=(@$_POST['t_date']!='')? $_POST['t_date'] : date('Y-m-d') ?>" required   name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                    <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary">View Data</button></td>
                </tr></table>
        </form>
    </div>

    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2> <span class="text-right h5" style="float: right" data-toggle="collapse" data-target="#experience2">Filter <i class="fa fa-filter"></i></span>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?=$page;?>" method="post" enctype="multipart/form-data" name="cloud" id="cloud" class="form-horizontal form-label-left">

                <? require_once 'support_html.php';?>

                <table align="center" style="width:98%; font-size: 11px" class="table table-striped table-bordered">
                    <thead>
                    <tr style="background-color: #3caae4; color:white">
                        <th style="text-align: center">Select Your File</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td align="center">
                            <input style="font-size:11px" type="file" id="file" name="file" required class="form-control col-md-7 col-xs-12" >
                        </td>
                        <td align="center" style="width:5%; vertical-align:middle">
                            <button type="submit" name="submit" onclick='return window.confirm("Are you confirm to Upload?");' class="btn btn-primary" style="font-size: 11px"> <i class="fa fa-upload"></i> Upload</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>


            <form action="<?=$page;?>" method="post" enctype="multipart/form-data" class="form-horizontal form-label-left">
                <div class="col text-center">
                    <?php if (mysqli_num_rows($result) > 0) {?>
                    <button type="submit" name="clearAllData" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-danger text-center" style="font-size: 11px"> <i class="fa fa-eraser"></i> Clear Manual Data</button>
                    <button type="submit" name="confirmAllData" onclick='return window.confirm("Are you confirm to clear all data?");' class="btn btn-success text-center" style="font-size: 11px"> <i class="fa fa-check"></i> Confirm Uploaded Data</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?=$crud->report_templates_with_status($sqlQuery,$title='Requested Logs');?>
<?=$html->footer_content();mysqli_close($conn);?>
