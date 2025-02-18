<?php
require_once 'support_file.php';
$title="SALARY SLIP";

$data = find_all_field('salary_info','','PBI_ID='.$_POST['PBI_ID']);
$employeeMasterData = find_all_field('personnel_basic_info','','PBI_ID='.$_POST['PBI_ID']);
$employeeEssentialData = find_all_field('essential_info','','PBI_ID='.$_POST['PBI_ID']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>.: Print Preview :.</title>
    <link href="../../css/report.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">

        function hide()

        {

            document.getElementById("pr").style.display="none";

        }

    </script>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pay-slip {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 600px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
        }
        header h1 {
            margin: 0;
            color: #007BFF;
        }
        .employee-details {
            margin: 20px 0;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .details p {
            margin: 5px 0;
        }
        .salary-details table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .salary-details th,
        .salary-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .salary-details th {
            background-color: #007BFF;
            color: #fff;
        }
        .salary-details tfoot td {
            font-weight: bold;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }
        .signature {
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body style="font-family:Tahoma, Geneva, sans-serif">
<br />
<br />
<br />
<table class="pay-slip" style="width: 80%" align="center">
    <tr>
        <td><div class="header">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
                                                        <tr>
                                                            <td rowspan="2" style="text-align:center; font-size:25px; font-weight:bold; width: 25%; background-color: #DCDCDC">
                                                                <img src="http://icpd.icpbd-erp.com/assets/images/icon/400001.png" width="50" height="50">
                                                            </td>
                                                            <td style="text-align:center; font-size:25px; font-weight:bold;">SALARY SLIP</td>
                                                            <td rowspan="2" style="text-align:center; font-size:25px; font-weight:bold; width: 25%; background-color: #DCDCDC">CONFIDENTIAL</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center; font-size:13px; font-weight:bold;"><span class="style1"><?=date('F', mktime(0, 0, 0, $_POST['month'], 1))?>, <?=$_POST['year']?></span></td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <br>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="3"  style="font-size:11px">
                                            <tr>
                                                <th  align="left" valign="middle">Name of Employee</th>
                                                <th  align="center" valign="middle" style="width: 2%">: </th>
                                                <td ><?=$employeeMasterData->PBI_NAME?></td>
                                            </tr>
                                            <tr>
                                                <th align="left" valign="middle">Employee ID</th>
                                                <th  align="center" valign="middle" style="width: 2%">: </th>
                                                <td><?=$employeeMasterData->PBI_ID_UNIQUE?></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="40%">
                                        <br>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="3"  style="font-size:11px">
                                            <tr>
                                                <th width="48%" align="left" valign="middle">Title</th>
                                                <th  align="center" valign="middle" style="width: 2%">: </th>
                                                <td width="52%"><?=find_a_field('designation','DESG_DESC','DESG_ID='.$employeeMasterData->PBI_DESIGNATION)?></td>
                                            </tr>
                                            <tr>
                                                <th align="left" valign="middle">Department</th>
                                                <th  align="center" valign="middle" style="width: 2%">: </th>
                                                <td><?=find_a_field('department','DEPT_DESC','DEPT_ID='.$employeeMasterData->PBI_DEPARTMENT)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td><div id="pr">
                <div align="left">
                    <form id="form1" name="form1" method="post" action="">
                        <table width="50%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>

            <table width="100%" class="tabledesign" border="1" bordercolor="#000000" cellspacing="0" cellpadding="3" style="font-size:11px; border-collapse: collapse">
                <tr style="background-color: #DCDCDC">
                    <td align="center"><strong>Description</strong></td>
                    <td align="center"><strong>Earning</strong></td>
                    <td align="center"><strong>Deductions</strong></td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Basic Salary</th>
                    <td align="right" valign="middle"><?=($data->basic_salary>0)? number_format($data->basic_salary,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">HRA</th>
                    <td align="right" valign="middle"><?=($data->house_rent>0)? number_format($data->house_rent,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Medical Allowance</th>
                    <td align="right" valign="middle"><?=($data->medical_allowance>0)? number_format($data->medical_allowance,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Conveyance Allowance</th>
                    <td align="right" valign="middle"><?=($data->convenience>0)? number_format($data->convenience,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Others Allowance</th>
                    <td align="right" valign="middle"><?=($data->extra_allowance>0)? number_format($data->extra_allowance,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Provident Fund</th>
                    <td align="right" valign="middle"><?=($data->pf>0)? number_format($data->pf,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Salary Advance (Products & Cash)</th>
                    <td align="right" valign="middle"><?=($data->pf>0)? number_format($data->pf,2) : '-';?></td>
                    <td align="right" valign="middle">-</td>
                </tr>
                <tr>
                    <th align="left" valign="middle">Tax Deducted at Source</th>
                    <td align="right" valign="middle">-</td>
                    <td align="right" valign="middle"><?=($data->income_tax>0)? number_format($data->income_tax,2) : '-';?></td>
                </tr>
                <tr>
                    <th rowspan="3" align="center" valign="middle" style="background-color: #DCDCDC">Total</th>
                    <td align="right" valign="middle" style="font-weight: bold">BDT <?=number_format($totalIncome=$data->basic_salary+$data->house_rent+$data->medical_allowance+$data->convenience+$data->special_allowance+$data->pf,2)?></td>
                    <td align="right" valign="middle" style="font-weight: bold">BDT <?=number_format($totalDeduction=$data->income_tax,2)?></td>
                </tr>

                <tr>
                    <td colspan="2" align="center" valign="middle" style="font-weight: bold; background-color: #DCDCDC">NET PAY</td>
                </tr>

                <tr>
                    <td colspan="2" align="center" valign="middle" style="font-weight: bold; background-color: #DCDCDC">BDT <?=number_format($totalIncome-$totalDeduction,2);?></td>
                </tr>

                <tr>
                    <td align="center" valign="middle">
                        <table border="1" cellspacing="0" cellpadding="3" style="border: 1px solid; width: 100%; font-size:11px; border-collapse: collapse">
                            <tr>
                                <td align="left">Payment Mode</td>
                                <td align="center">:</td>
                                <td align="left"><?= ($data->cash_bank=='cash') ? 'Cash' : (($data->cash_bank =='bank') ? 'Bank' : 'Cash & Bank'); ?></td>
                            </tr>
                            <tr>
                                <td align="left">Cash Payment Amount</td>
                                <td align="center">:</td>
                                <td align="left"><?=($data->cashAmount>0) ? number_format($data->cashAmount,2) : '-';?></td>
                            </tr>
                            <tr>
                                <td align="left">Bank Payment Amount</td>
                                <td align="center">:</td>
                                <td align="left"><?=($data->bankAmount>0) ? number_format($data->bankAmount,2) : '-';?></td>
                            </tr>

                            <tr>
                                <td align="left">Bank Name</td>
                                <td align="center">:</td>
                                <td align="left"><?=$employeeEssentialData->ESS_BANK?></td>
                            </tr>

                            <tr>
                                <td align="left">Bank Account Name</td>
                                <td align="center">:</td>
                                <td align="left"><?=$employeeEssentialData->ESS_BANK_ACC_NAME?></td>
                            </tr>
                            <tr>
                                <td align="left">Bank Account No</td>
                                <td align="center">:</td>
                                <td align="left"><?=$employeeEssentialData->ESS_BANK_ACC_NO?></td>
                            </tr>
                        </table>
                        <table style="font-size:11px; margin-top:40px" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" >
                                    <span class="oe_form_group_cell oe_form_group_cell_label">
                                        <?=$employeeMasterData->PBI_NAME?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="text-decoration:overline"><strong>Receiver Signature </strong></td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="2" rowspan="2" align="left" valign="middle">Signed for & on behalf of - </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
