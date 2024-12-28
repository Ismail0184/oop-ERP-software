<?php
require_once 'support_file.php';
$title='Others Received / Deduction Entry';
$page="hrm_payroll_others_deduction_entry.php";

$table='payroll_others_receive_deduction';
$unique='id';
$PBI_unique='PBI_ID';
$shown='gross_salary';
$crud      =new crud($table);

if (isset($_POST['initiate'])){
    $_SESSION['selectedYearForOtherDeduction'] = @$_POST['selectedYearForOtherDeduction'];
    $_SESSION['selectedMonthForOtherDeduction'] = @$_POST['selectedMonthForOtherDeduction'];
    $_SESSION['HRM_payroll_others_deduction_employee'] = @$_POST['HRM_payroll_others_deduction_employee'];
}
if (isset($_POST['updateMonth'])){
    unset($_SESSION['selectedYearForOtherDeduction']);
    unset($_SESSION['selectedMonthForOtherDeduction']);
    unset($_SESSION['HRM_payroll_others_deduction_employee']);
    $_SESSION['selectedYearForOtherDeduction'] = @$_POST['selectedYearForOtherDeduction'];
    $_SESSION['selectedMonthForOtherDeduction'] = @$_POST['selectedMonthForOtherDeduction'];
    $_SESSION['HRM_payroll_others_deduction_employee'] = @$_POST['HRM_payroll_others_deduction_employee'];
}

if (isset($_POST['cancel'])){
    unset($_SESSION['selectedYearForOtherDeduction']);
    unset($_SESSION['selectedMonthForOtherDeduction']);
    unset($_SESSION['HRM_payroll_others_deduction_employee']);
}

$selectedYearForOtherDeduction =  @$_SESSION['selectedYearForOtherDeduction'];
$selectedMonthForOtherDeduction =  @$_SESSION['selectedMonthForOtherDeduction'];
$HRM_payroll_others_deduction_employee =  @$_SESSION['HRM_payroll_others_deduction_employee'];

if(prevent_multi_submit()){
    if(isset($_POST['record']))
    {
        $crud->insert();
        unset($_POST);
    }

    if(isset($_POST['update']))
    {
        $crud->update($unique);
        $type=1;
    }

    if(isset($_POST['delete']))
    {
        $condition=$unique."=".$$unique;
        $crud->delete($condition);
        unset($$unique);
    }
}



$data = find_all_field('salary_info','','PBI_ID='.$_SESSION['HRM_payroll_others_deduction_employee']);
$sql_user_id="SELECT  p.PBI_ID,concat(p.PBI_ID,' : ',p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') FROM 						 
							personnel_basic_info p,
							department d,
							designation des
							 where p.PBI_JOB_STATUS='In Service' and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID and 
							 p.PBI_DESIGNATION=des.DESG_ID	 
							  order by p.serial";

$sqlQuery = "SELECT  p.PBI_ID,concat(p.PBI_ID,' : ',p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') as Employee,si.advance as 'Advance / Loan',si.product_purchase,si.mobile_use,si.month,si.year FROM 						 
							personnel_basic_info p,
							department d,
							designation des,
							payroll_others_receive_deduction si
							 where p.PBI_JOB_STATUS='In Service' and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID and 
							 p.PBI_DESIGNATION=des.DESG_ID and 
							 p.PBI_ID=si.PBI_ID
							  order by p.serial";
?>

<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content_nva_sm.php'; ?>

<div class="col-md-12 col-xs-12">

    <div class="<?php if(isset($_POST['viewReport'])){ ?> row <?php } else { echo 'row collapse';} ?>" id="experience2">
        <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
            <table align="center" style="width: 50%;">
                <tr><td>
                        <input type="date"  style="width:150px; font-size: 11px; height: 25px"  value="<?=(@$_POST['f_date']!='')? $_POST['f_date'] : date('Y-m-01') ?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                    <td style="width:10px; text-align:center"> -</td>
                    <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?=(@$_POST['t_date']!='')? $_POST['t_date'] : date('Y-m-d') ?>" required   name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                    <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary"> <i class="fa fa-eye"></i> View Salary Info</button></td>
                </tr></table>
        </form>
    </div>

    <div class="x_panel">
    <div class="x_title">
        <h2><?=$title;?></h2> <span class="text-right h5" style="float: right" data-toggle="collapse" data-target="#experience2">Filter <i class="fa fa-filter"></i></span>
        <div class="clearfix"></div>
    </div>
    <form id="demo-form2" method="post" data-parsley-validate class="form-horizontal form-label-left" style="font-size: 11px">
        <table align="center" style="width:70%; font-size: 11px" class="table table-striped table-bordered">
            <thead>
            <tr class="bg-primary text-white">
                <th style="text-align: center">Employee</th>
                <th style="text-align: center">Month</th>
                <th style="text-align: center">Year</th>
                <th style="text-align: center">Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td align="center" style="vertical-align: middle">
                    <select class="select2_single form-control" style="width: 99%; flot:left" tabindex="-1" required="required" name="HRM_payroll_others_deduction_employee" id="PBI_ID">
                        <option></option>
                        <?=advance_foreign_relation($sql_user_id,$HRM_payroll_others_deduction_employee);?>
                    </select>
                </td>
                <td align="center" style="vertical-align: middle">
                    <select class="select2_single form-control" style="width:98%; font-size: 11px" tabindex="6" required="required"  name="selectedMonthForOtherDeduction">
                        <option></option>
                        <?php foreign_relation("monthname", "month", "CONCAT(month,' : ', monthfullName)", $selectedMonthForOtherDeduction, "1"); ?>
                    </select>
                </td>
                <td style="vertical-align: middle">
                    <?php
                    $start_year = 2020; // Starting year
                    $end_year = date('Y'); // Current year or any ending year
                    ?>
                    <select class="select2_single form-control" style="width:98%; font-size: 11px" name="selectedYearForOtherDeduction">
                        <?php
                        for ($year = $end_year; $year >= $start_year; $year--) { ?>
                            <option value='<?=$year?>'><?=$year?></option>";
                        <?php } ?>
                    </select>
                </td>
                <td align="center" style="width:15%; vertical-align:middle">
                    <?php if (isset($selectedMonthForOtherDeduction)) {?>
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

<?php if(isset($HRM_payroll_others_deduction_employee)): ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form action="" method="post" enctype="multipart/form-data" style="font-size: 11px">
                    <strong><h5>OTHERS DEDUCTION</h5></strong><hr>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="oe_form_group ">
                        <tr>
                            <th style="width: 9%">Advance / Loan</th>
                            <th style="width: 1%">:</th>
                            <td style="width: 20%">
                                <input name="<?=$unique?>" id="<?=$unique?>"  type="hidden" />
                                <input name="PBI_ID" value="<?=$HRM_payroll_others_deduction_employee?>" type="hidden" />
                                <input name="month"  value="<?=$selectedMonthForOtherDeduction?>" type="hidden" />
                                <input name="year" value="<?=$selectedYearForOtherDeduction?>" type="hidden" />
                                <input name="advance" type="number" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle" value="<?=$data->advance?>" />
                            </td>
                            <th style="width: 9%"><span >Product Purchase</span></th>
                            <th style="width: 1%">:</th>
                            <td style="width: 20%">
                                <input name="product_purchase" type="number"  value="<?=$data->product_purchase?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-left: 2%" />
                            </td>
                            <th style="width: 9%"><span >Mobile Use</span></th>
                            <th style="width: 1%">:</th>
                            <td style="width: 20%">
                                <input name="mobile_use" type="number" value="<?=$data->mobile_use?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-left: 2%" />
                            </td>
                        </tr>
                    </table>

                    <br><br>

                        <strong><h5>OTHERS RECEIVED</h5></strong><hr>

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="oe_form_group ">
                            <tr>
                                <th style="width: 9%">Extra Allowance</th>
                                <th style="width: 1%">:</th>
                                <td style="width: 20%">
                                    <input class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px;" name="extra_allowance" type="number" value="<?=$data->extra_allowance?>" style="width:100px" />
                                </td>
                                <th style="width: 9%"></th>
                                <th style="width: 1%"></th>
                                <td style="width: 20%">
                                </td>

                                <th style="width: 9%"></th>
                                <th style="width: 1%"></th>
                                <td style="width: 20%">
                                    <button type="submit" name="record" class="btn btn-primary"><i class="fa fa-plus"></i> Submit Receive / Deduction </button>
                                </td>
                            </tr>
                        </table>
                    <br>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php if(isset($_POST['viewReport'])): ?>
<?=$crud->report_templates_with_status($sqlQuery,$title='Other Received / Deduction');?>
<?php endif; ?>
<?=$html->footer_content();?>
