<?php
require_once 'support_file.php';
$title='Report';
$page='hrm_select_report.php';
$report_id = @$_REQUEST['report_id'];



$sql_user_id="SELECT  p.PBI_ID,concat(p.PBI_ID,' : ',p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') FROM 						 
							personnel_basic_info p,
							department d,
							designation des
							 where p.PBI_JOB_STATUS='In Service' and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID and 
							 p.PBI_DESIGNATION=des.DESG_ID	 
							  order by p.serial";
?>


<?php require_once 'header_content.php'; ?>
<SCRIPT language=JavaScript>
    function reload(form)
    {
        var val=form.report_id.options[form.report_id.options.selectedIndex].value;
        self.location='<?=$page;?>?report_id=' + val ;
    }
    function reload1(form)
    {
        var val=form.report_id.options[form.report_id.options.selectedIndex].value;
        var val2=form.ledgercode.options[form.ledgercode.options.selectedIndex].value;
        self.location='<?=$page;?>?report_id=' + val +'&ledgercode=' + val2 ;
    }

</script>
    <style>
        input[type=text]{
            font-size: 11px;
        }
        input[type=date]{
            font-size: 11px;
        }


    </style>

<?php require_once 'body_content_nva_sm.php'; ?>

    <form class="form-horizontal form-label-left" method="POST" action="<?=($report_id=='1000303') ? 'emp_access_pay_slip.php' : 'hrm_reportview.php';?>" style="font-size: 11px" target="_blank">
        <div class="col-md-5 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <?=$crud->select_a_report(10);?>
                </div>
            </div>
        </div>

        <div class="col-md-7 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><small class="text-danger">field marked with * are mandatory</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <?php if ($report_id=='1000101'): ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Designation</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%" tabindex="-1"   name="PBI_DESIGNATION" >
                                    <option></option>
                                    <?=foreign_relation('designation', 'DESG_ID', 'CONCAT(DESG_ID," : ", DESG_DESC)','', '1'); ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Department</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%" tabindex="-1"   name="department" >
                                    <option></option>
                                    <?=foreign_relation('department', 'DEPT_ID', 'CONCAT(DEPT_ID," : ", DEPT_DESC)','', '1'); ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Service Status <span class="required text-danger">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%" tabindex="-1"  name="PBI_JOB_STATUS"  id="PBI_JOB_STATUS">
                                    <option></option>
                                    <option value="In Service" selected>In Service</option>
                                    <option value="Not In Service">Not In Service</option>
                                </select>
                            </div>
                        </div>



                    <?php elseif ($report_id=='1000201' || $report_id=='1000202' || $report_id=='1000203' || $report_id=='1000204' || $report_id=='1000205'): ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Department</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%" tabindex="-1"   name="department" >
                                    <option></option>
                                    <?=foreign_relation('department', 'DEPT_ID', 'CONCAT(DEPT_ID," : ", DEPT_DESC)','', '1'); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Name</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width: 100%; flot:left" tabindex="-1" required="required" name="PBI_ID" id="PBI_ID">
                                    <option></option>
                                    <?=advance_foreign_relation($sql_user_id,$_SESSION['HRM_payroll_employee']);?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Date From <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="date"  required="required" name="f_date" value="<?=date('Y-m-01')?>" class="form-control col-md-7 col-xs-12" placeholder="From Date" autocomplete="off"></td>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Date to <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="date"  required="required" name="t_date" value="<?=date('Y-m-d')?>" class="form-control col-md-7 col-xs-12"  placeholder="to Date" autocomplete="off"></td>
                            </div>
                        </div>

                    <?php elseif ( $report_id=='1000301' || $report_id=='1000302'): ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Department</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%" tabindex="-1"   name="department" >
                                    <option></option>
                                    <?=foreign_relation('department', 'DEPT_ID', 'CONCAT(DEPT_ID," : ", DEPT_DESC)','', '1'); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Employee Name</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width: 100%; flot:left" tabindex="-1" required="required" name="PBI_ID" id="PBI_ID">
                                    <option></option>
                                    <?=advance_foreign_relation($sql_user_id,$_SESSION['HRM_payroll_employee']);?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Month <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="6" required="required"  name="month">
                                    <option></option>
                                    <?php foreign_relation("monthname", "month", "CONCAT(month,' : ', monthfullName)", '', "1"); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> Year <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <?php
                                $start_year = 2020; // Starting year
                                $end_year = date('Y'); // Current year or any ending year
                                ?>
                                <select class="select2_single form-control" style="width:100%; font-size: 11px" name="year">
                                    <?php
                                    for ($year = $end_year; $year >= $start_year; $year--) { ?>
                                        <option value='<?=$year?>'><?=$year?></option>";
                                    <?php } ?>
                                </select>                            </div>
                        </div>

                    <?php elseif ($report_id=='1000303'): ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Slip for <span class="required text-danger">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width: 100%; flot:left" tabindex="-1" required="required" name="PBI_ID" id="PBI_ID">
                                    <option></option>
                                    <?=advance_foreign_relation($sql_user_id,$_SESSION['HRM_payroll_employee']);?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Month <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="6" required="required"  name="month">
                                    <option></option>
                                    <?php foreign_relation("monthname", "month", "CONCAT(month,' : ', monthfullName)", '', "1"); ?>
                                </select>                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"> Year <span class="required text-danger">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <?php
                                $start_year = 2020; // Starting year
                                $end_year = date('Y'); // Current year or any ending year
                                ?>
                                <select class="select2_single form-control" style="width:100%; font-size: 11px" name="year">
                                    <?php
                                    for ($year = $end_year; $year >= $start_year; $year--) { ?>
                                        <option value='<?=$year?>'><?=$year?></option>";
                                    <?php } ?>
                                </select>                            </div>
                        </div>


                    <?php  else:  ?>
                        <h5 class="text-danger" style="text-align: center">Please select a report from left</h5>
                    <?php endif; ?>

                    <?php if ($report_id>0): ?>

                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            <a href="<?=$page;?>"  class="btn btn-danger" style="font-size: 12px">Cancel</a>
                            <button type="submit" class="btn btn-primary" name="getstarted" style="font-size: 12px"><i class="fa fa-file"></i> Generate Report</button>
                        </div>
                    </div>
                    <?php  else:  ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>

<?=$html->footer_content();?>