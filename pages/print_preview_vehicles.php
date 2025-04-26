<?php
require_once 'support_file.php';

$table = 'vehicle_registration';

$query = "
    SELECT * 
    FROM ".$table."
    WHERE section_id = {$_SESSION['sectionid']}
      AND company_id = {$_SESSION['companyid']}
    ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$vdata = mysqli_fetch_object($result);

// Get request and fetch data
$request_id = $_REQUEST['id'];
$issueData = find_all_field('warehouse_other_issue', '', 'oi_no=' . $request_id);
$employeeData = find_all_field('personnel_basic_info', '', 'PBI_ID=' . $vdata->employee_id);
$designation = find_a_field('designation', 'DESG_DESC', 'DESG_ID=' . $employeeData->PBI_DESIGNATION);
$department = find_a_field('department', 'DEPT_DESC', 'DEPT_ID=' . $employeeData->PBI_DEPARTMENT);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Preview</title>
    <link href="../css/invoice.css" rel="stylesheet" type="text/css">
    <style>
        body {
            font-family: Tahoma, Geneva, sans-serif;
        }
        .container {
            width: 800px;
            margin: 0 auto;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .tabledesign {
            font-size: 11px;
            border-collapse: collapse;
            width: 100%;
        }
        .tabledesign td {
            border: 1px solid #000;
            padding: 5px;
        }
        .label {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        #printBtn {
            margin: 20px 0;
        }
        .signatures {
            margin-top: 40px;
            text-align: center;
        }
        .signatures td {
            width: 50%;
        }
        .signature-line {
            text-decoration: overline;
        }
    </style>
    <script>
        function printPage() {
            document.getElementById("printBtn").style.display = "none";
            window.print();
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-title"><?= $_SESSION['company_name'] ?></div>
        <div class="header-title">Vehicle Information</div>
    </div>

    <br>

    <table width="100%" cellpadding="3" style="font-size:13px;">
        <tr>
            <td><strong>Employee Name:</strong> <?= $vdata->PBI_NAME ?></td>
            <td><strong>Joining Date:</strong> <?= $vdata->PBI_DOJ ?></td>
        </tr>
        <tr>
            <td><strong>Designation:</strong> <?= $vdata->DESG_DESC ?></td>
            <td><strong>From:</strong> <?= $vdata->employee_date_from ?></td>
        </tr>
        <tr>
            <td><strong>Department:</strong> <?= $vdata->DEPT_DESC ?></td>
            <td><strong>To:</strong> <?= $vdata->employee_date_to ?></td>
        </tr>
    </table>

    <div id="printBtn">
        <button onclick="printPage()">Print</button>
    </div>

    <table class="tabledesign">
        <tr><td class="label">Reg. No</td><td><?= $vdata->registration_no ?></td></tr>
        <tr><td class="label">Description</td><td><?= $vdata->description ?></td></tr>
        <tr><td class="label">Chassis No</td><td><?= $vdata->chassis ?></td></tr>
        <tr><td class="label">Engine No</td><td><?= $vdata->engine_no ?></td></tr>
        <tr><td class="label">CC</td><td><?= $vdata->cc ?></td></tr>
        <tr><td class="label">Color</td><td><?= $vdata->vehicle_color ?></td></tr>
        <tr><td class="label">Owner Name</td><td><?= $vdata->owner_name ?></td></tr>
        <tr><td class="label">Address</td><td><?= $vdata->address ?></td></tr>
        <tr><td class="label">Registration Certificate</td><td><?= $vdata->certificate ?></td></tr>
        <tr><td class="label">Digital Number Plate</td><td><?= $vdata->number_plate ?></td></tr>

        <tr>
            <td class="label">Fitness</td>
            <td>
                <?= $vdata->fitness ?><br>
                From: <?= $vdata->fitness_date_from ?><br>
                To: <?= $vdata->fitness_date_to ?><br>
                Amount: <?= $vdata->fitness_amount ?>
            </td>
        </tr>

        <tr>
            <td class="label">Tax Token</td>
            <td>
                <?= $vdata->tax_token ?><br>
                From: <?= $vdata->tax_token_date_from ?><br>
                To: <?= $vdata->tax_token_date_to ?><br>
                Amount: <?= $vdata->tax_token_amount ?>
            </td>
        </tr>

        <tr>
            <td class="label">Insurance</td>
            <td>
                <?= $vdata->insurance ?><br>
                From: <?= $vdata->insurance_date_from ?><br>
                To: <?= $vdata->insurance_date_to ?><br>
                Amount: <?= $vdata->insurance_amount ?>
            </td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <?= find_a_field('personnel_basic_info', 'PBI_NAME', 'PBI_ID=' . $issueData->recommended_by) ?>
            </td>
            <td>
                <?= find_a_field('personnel_basic_info', 'PBI_NAME', 'PBI_ID=' . $issueData->authorised_person) ?>
            </td>
        </tr>
        <tr><td colspan="2" style="height:40px;"></td></tr>
        <tr>
            <td class="signature-line"><strong>Approved By</strong></td>
            <td class="signature-line"><strong>Authorised By</strong></td>
        </tr>
    </table>
</div>
</body>
</html>
