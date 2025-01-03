<?php
require_once 'support_file.php';
$title='Salary Breakdown';
$page="hrm_payroll_salaryandallowance.php";
$input_page="employee_essential_information_input.php";
$root='hrm';
$table='salary_info';
$unique='PBI_ID';
$PBI_unique='PBI_ID';
$shown='gross_salary';

$datas=find_all_field('personnel_basic_info','','PBI_ID='.$_GET[$unique].'');
$crud      =new crud($table);

$salaryPolicy=find_all_field('hrm_salary_policy','','status="1"');

if(isset($_POST['proceed_to_next']))
{
    $_SESSION['HRM_payroll_employee']=@$_POST['PBI_ID'];
}



if(isset($_POST['cancel_proceed_to_next'])){
    unset($_POST);
    unset($$unique);
    unset($_SESSION['HRM_payroll_employee']);
}


if(isset($_POST[$shown]))
{	if(isset($_POST['insert']))
{
    $crud->insert();
    $type=1;
    $msg='New Entry Successfully Inserted.';
    unset($_POST);
    unset($$unique);
    $required_id=find_a_field($table,$unique,'PBI_ID='.$_SESSION['HRM_payroll_employee'],' order by id desc limit 1');
    if($required_id>0)
        $$unique = $_GET[$unique] = $required_id;
}
    if(isset($_POST['reset'])){
        unset($_POST);
        unset($$unique);
        unset($_SESSION['HRM_payroll_employee']);
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

$data = find_all_field('salary_info','','PBI_ID='.$_SESSION['HRM_payroll_employee']);

$sql_user_id="SELECT  p.PBI_ID,concat(p.PBI_ID,' : ',p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') FROM 						 
							personnel_basic_info p,
							department d,
							designation des
							 where p.PBI_JOB_STATUS='In Service' and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID and 
							 p.PBI_DESIGNATION=des.DESG_ID	 
							  order by p.serial";

$sqlQuery = "SELECT  p.PBI_ID,concat(p.PBI_ID,' : ',p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' (',des.DESG_SHORT_NAME,' - ',d.DEPT_SHORT_NAME,')') as Employee,si.gross_salary,si.basic_salary,si.house_rent,si.medical_allowance,si.convenience,si.bonus_applicable as bonus,si.da as TA_DA,si.mobile_allowance mobile_bill FROM 						 
							personnel_basic_info p,
							department d,
							designation des,
							salary_info si
							 where p.PBI_JOB_STATUS='In Service' and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID and 
							 p.PBI_DESIGNATION=des.DESG_ID and 
							 p.PBI_ID=si.PBI_ID
							  order by p.serial";
?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript"> function DoNav(lk){
            return GB_show('ggg', '../pages/<?=$root?>/<?=$input_page?>?<?=$unique?>='+lk,600,940)
        }</script>
<script type="text/javascript"> function DoNav(lk){document.location.href = '<?=$page?>?<?=$unique?>='+lk;}
    function popUp(URL)
    {   day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=800,height=800,left = 383,top = -16');"); }
</script>
<?php require_once 'body_content_nva_sm.php'; ?>

<div class="col-md-12 col-sm-12 col-xs-12">

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
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Employee <span class="required text-danger">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="select2_single form-control" style="width: 70%; flot:left" tabindex="-1" required="required" name="PBI_ID" id="PBI_ID">
                                <option></option>
                                <?=advance_foreign_relation($sql_user_id,$_SESSION['HRM_payroll_employee']);?>
                            </select>
                            <?php if(isset($_SESSION['HRM_payroll_employee'])): ?>
                                <button type="submit" name="cancel_proceed_to_next" class="btn btn-danger"  style="font-size: 12px; margin-left:5%"><i class="fa fa-close"></i> Cancel</button>
                            <?php  else: ?>
                                <button type="submit" name="proceed_to_next"        class="btn btn-primary" style="font-size: 12px; margin-left:5%">Proceed to the next <i class="fa fa-forward"></i> </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    </form>
            </div></div>

<?php if(isset($_SESSION['HRM_payroll_employee'])): ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form action="" method="post" enctype="multipart/form-data" style="font-size: 11px">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="oe_form_group ">
                        <tr>
                            <th width="15%">Gross Salary</th>
                            <th style="width: 2%;">:</th>
                            <td width="33%" ><input name="<?=$unique?>" id="<?=$unique?>" value="<?=$data->PBI_ID?>" type="hidden" />
                                <input name="PBI_ID" id="PBI_ID" value="<?=$_SESSION['HRM_payroll_employee']?>" type="hidden" />
                                <input name="gross_salary" type="text" id="gross_salary" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle" value="<?=$data->gross_salary?>" onkeyup="salaryCal()"/></td>


                            <th width="15%"><span >Bonus Applicable? :</span></th>
                            <th style="width: 2%;">:</th>
                            <td width="33%">
                                <select class="form-control" name="if_bonus_applicable" id="if_bonus_applicable" style="float: left; width: 20%; font-size: 11px" onchange="bonusAppl(), salaryCal()">
                                    <option selected="selected"><?=$data->if_bonus_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input name="bonus_applicable" type="text" id="bonus_applicable" value="<?=$data->bonus_applicable?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-left: 2%" onkeyup="salaryCal()" />
                            </td>
                        </tr>
                        <tr>
                            <th>Basic Salary</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="<?=$salaryPolicy->basic?>% Of Gross Amount" name="basic_salary" type="text" id="basic_salary" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" value="<?=$data->basic_salary?>" readonly="readonly" /></td>
                            <th>Overtime Applicable?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="if_overtime_applicable" id="if_overtime_applicable" onchange="overtimeAllwAppl()">
                                    <option selected="selected"><?=$data->if_overtime_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input name="overtime_applicable" type="text" id="overtime_applicable" class="form-control col-md-7 col-xs-12" style="margin-left:2%; width: 68%; font-size: 11px;vertical-align:middle; margin-top: 5px" value="<?=$data->overtime_applicable?>"  onkeyup="salaryCal()" />
                            </td>
                        </tr>


                        <tr>
                            <th>HRA</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="<?=$salaryPolicy->house_rent_allowance?>% Of Gross Amount" name="house_rent" type="text" id="house_rent" value="<?=$data->house_rent?>" readonly="readonly" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" /></td>
                            <th>PF Applicable?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="pf_applicable" id="pf_applicable" onchange="pfAllwAppl(),salaryCal()">
                                    <option selected="selected"><?=$data->if_overtime_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input name="pf_percentage" type="text" id="pf_percentage" value="<?=$data->pf_percentage?>" class="form-control col-md-7 col-xs-12" style="width: 20%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" style="width:30px" onkeyup="salaryCal()"/>
                                <input name="pf" type="text" id="pf" value="<?=$data->pf?>" class="form-control col-md-7 col-xs-12" style="width: 47%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 1%" />
                            </td>
                        </tr>

                        <tr>
                            <th>Medical Allowance</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="<?=$salaryPolicy->medical_allowance?>% Of Gross Amount" name="medical_allowance" type="text" id="medical_allowance" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" value="<?=$data->medical_allowance?>" readonly="readonly" /></td>
                            <th>Medical Insurance Applicable?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="mi_applicable" id="mi_applicable" onchange="miAllwAppl()">
                                    <option selected="selected"><?=$data->mi_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input onkeyup="salaryCal()" name="mi_percentage" type="text" id="mi_percentage" value="<?=$data->mi_percentage?>" class="form-control col-md-7 col-xs-12" style="width: 20%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" />
                                <input name="medical_insurance" type="text" id="medical_insurance" value="<?=$data->medical_insurance?>" class="form-control col-md-7 col-xs-12" style="width: 47%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 1%" />
                            </td>
                        </tr>

                        <tr>
                            <th>Convenience</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="<?=$salaryPolicy->convenience?>% Of Gross Amount" name="convenience" type="text" id="convenience" value="<?=$data->convenience?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" readonly="readonly" /></td>
                            <th>Food Allowance?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="food_alw_applicable" id="food_alw_applicable" onchange="foodAppl()">
                                    <option selected="selected">
                                        <?=$data->food_alw_applicable?>
                                    </option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input onkeyup="salaryCal()" name="food_allowance" type="text" id="food_allowance" value="<?=$data->food_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" />
                            </td>
                        </tr>

                        <tr>
                            <th>Special Allowance</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="<?=$salaryPolicy->special_allowance?>% Of Gross Amount" name="special_allowance" type="text" id="special_allowance" value="<?=$data->special_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" /></td>
                            <th>Mobile Allowance?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="mobile_alw_applicable" id="mobile_alw_applicable" onchange="mobileAppl()">
                                    <option selected="selected"><?=$data->mobile_alw_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input onkeyup="salaryCal()" name="mobile_allowance" type="text" id="mobile_allowance" value="<?=$data->mobile_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" />
                            </td>
                        </tr>
                        <tr>
                            <th>Extra Allowance</th>
                            <th style="width: 2%;">:</th>
                            <td><input placeholder="Extra Allowance Amount" name="extra_allowance" type="text" id="extra_allowance" value="<?=$data->extra_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px" /></td>
                            <th>TA/DA?</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="ta_da_alw_applicable" id="ta_da_alw_applicable" onchange="taDaAppl()">
                                    <option selected="selected"><?=$data->ta_da_alw_applicable?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input onkeyup="salaryCal()" name="ta_da_allowance" type="text" id="ta_da_allowance" value="<?=$data->ta_da_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" />
                            </td>
                        </tr>
                        

                        <tr>
                            <th>Salary Pay Through</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" name="cash_bank" required style="width: 30%; font-size: 11px;margin-top: 5px">
                                    <option selected="selected">
                                        <?=$data->cash_bank?>
                                    </option>
                                    <option value="cash">Cash</option>
                                    <option value="bank"> Bank</option>
                                    <option value="both">Both</option>
                                </select>
                            </td>
                            <th>Transport Allowance</th>
                            <th style="width: 2%;">:</th>
                            <td><select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="transportAllwAppl" id="transportAllwAppl" onchange="trnsAppl()">
                                    <option selected="selected"><?=$data->transportAllwAppl?></option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                <input name="transport_allowance" type="text" id="transport_allowance" value="<?=$data->transport_allowance?>" class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-left: 2%; margin-top: 5px" />
                            </td>
                        </tr>
                        <tr>
                            <th>Company for</th>
                            <th style="width: 2%;">:</th>
                            <td>
                                <select class="form-control" name="companyInfo" required style="width: 55%; font-size: 11px;margin-top: 5px">
                                    <?php if (isset($data->companyInfo) && !empty($data->companyInfo)) { ?>
                                    <option selected="selected">
                                        <?=$data->companyInfo?>
                                    </option>
                                    <?php } else { ?>
                                        <option>-- select company --</option>
                                    <?php } ?>
                                    <option>ICPBL</option>
                                    <option>ICP Distribution</option>
                                    <option>LBC Media</option>
                                </select>
                            </td>
                            <th>Salary Portion (Bank & Cash)</th>
                            <th style="width: 2%;">:</th>
                            <td>
                                <input name="bankAmount" type="number" value="<?=$data->bankAmount?>" class="form-control col-md-7 col-xs-12" style="width: 49%; font-size: 11px;vertical-align:middle; margin-top: 5px" placeholder="bank" />
                                <input name="cashAmount" type="number" value="<?=$data->cashAmount?>" class="form-control col-md-7 col-xs-12" style="width: 40%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 1%" placeholder="cash" />
                            </td>
                        </tr>
                    </table>
                    <br>

                        <strong><h5>INCOME TAX CALCULATION</h5></strong><hr>

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="oe_form_group ">
                            <tr>
                                <th width="15%">Total Taxable Amount</th>
                                <th style="width: 2%;">:</th>
                                <td width="33%">
                                    <input class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" onkeyup="" name="" type="text" id="" value=""  readonly />
                                    <input class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" onkeyup="salaryCal()" name="total_taxable_amt" type="text" id="total_taxable_amt" value="<?=$data->total_taxable_amt?>" style="width:100px" /></td>

                                <th width="15%"><span >Car Facilities? :</span></th>
                                <th style="width: 2%;">:</th>
                                <td width="33%">
						        <select class="form-control" style="float: left; width: 20%; font-size: 11px;margin-top: 5px" name="carFacilitiesAppl" id="carFacilitiesAppl" onchange="carFacilitiesAppll(), salaryCal()">
                                <option selected="selected"><?=$data->carFacilitiesAppl?></option>
                                <option>YES</option>
                                <option>NO</option>
                                </select>
                                    <input class="form-control col-md-7 col-xs-12" style="width: 68%; font-size: 11px;vertical-align:middle; margin-top: 5px; margin-left: 2%" name="carFacilitiesAmt" type="text" id="carFacilitiesAmt" value="<?=$carFacilitiesAmt?>" style="width:100px"/>
                          </span></td>
                            </tr>
                                                                    <tr>
                                                                        <th>Investment Amount : </th>
                                                                        <th style="width: 2%;">:</th>
                                                                        <td>
                                                                            <input onkeyup="salaryCal()" name="max_invested_amt" type="text" id="max_invested_amt" value="" class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" readonly />
                                                                            <input onkeyup="salaryCal()" name="total_invested_amt" type="text" id="total_invested_amt" value="<?=$data->total_invested_amt?>" class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" />
                                                                        </td>


                                                                        <th>Income Tax Yearly:</th>
                                                                        <th style="width: 2%;">:</th>
                                                                        <td><input onkeyup="salaryCal()" name="income_tax_yearly" type="text" id="income_tax_yearly" value="<?=$data->income_tax_yearly?>" readonly class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" /></td>
                                                                    </tr>



                                                                    <tr>
                                                                        <th>AIT</th>
                                                                        <th style="width: 2%;">:</th>
                                                                        <td>
                                                                            <input onkeyup="" name="" type="text" id="" value="" class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" readonly />
                                                                            <input onkeyup="salaryCal()" name="advance_IT" type="text" id="advance_IT" value="<?=$data->advance_IT?>" class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" /></td>
                                                                        <th>Income Tax Monthly</th>
                                                                        <th style="width: 2%;">:</th>
                                                                        <td><input name="income_tax" type="text" id="income_tax" value="<?=$data->income_tax?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle; margin-top: 5px;" /></td>
                                                                    </tr>

                                                                    <tr>

                                                                        <td colspan="4" ><span id="taxDetails"></span></td>
                                                                    </tr>


                                                                    <tr>
                                                                        <th>Total Payable Amt : </th>
                                                                        <th style="width: 2%;">:</th>
                                                                        <td><input name="total_payable_amount" type="text" id="total_payable_amount" value="<?=$data->total_payable_amount?>" class="form-control col-md-7 col-xs-12" style="width: 45%; font-size: 11px;vertical-align:middle; margin-top: 5px;" readonly /></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr></table>
                        <br>

                        <?php if($data>0){  ?>
                            <div class="form-group" style="float: right">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="submit" name="update" id="update" class="btn btn-primary">Update Salary Info <i class="fa fa-edit"></i></button>
                                </div></div>
                            <? if($_SESSION['userid']=="10019"){?>
                                <div class="form-group" style="float: left">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input  name="delete" type="submit" class="btn btn-danger" id="delete" value="Delete Salary Info"/>
                                    </div></div>
                            <? }?>
                        <?php } else {?>
                            <div class="form-group" style="float: right">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="submit" name="insert" id="insert"  class="btn btn-primary"><i class="fa fa-plus"></i> Submit Salary Info </button>
                                </div></div>



                                <div class="form-group" style="float: left">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input  name="reset" type="submit" class="btn btn-danger" id="reset" value="Reset Salary Info"/>
                                    </div></div>

                        <?php } ?>

    </form></div></div></div>


<?php endif; ?>

<?php if(isset($_POST['viewReport'])): ?>
<?=$crud->report_templates_with_status($sqlQuery,$title='Salary Info');?>
<?php endif; ?>
<?=$html->footer_content();?>

<script>

    function foodAppl()
    {
        var status = document.getElementById('food_alw_applicable').value;
        if(status!="YES"){
            document.getElementById('food_allowance').setAttribute("readonly", "readonly");
            document.getElementById('food_allowance').value='0.00';}
        if(status=="YES"){
            document.getElementById('food_allowance').removeAttribute("readonly", "readonly");}
    }


    function trnsAppl()
    {
        var status = document.getElementById('transportAllwAppl').value;
        if(status!="YES"){
            document.getElementById('transport_allowance').setAttribute("readonly", "readonly");
            document.getElementById('transport_allowance').value='0.00';}
        if(status=="YES"){
            document.getElementById('transport_allowance').removeAttribute("readonly", "readonly");}
    }

    function mobileAppl()
    {
        var status = document.getElementById('mobile_alw_applicable').value;
        if(status!="YES"){
            document.getElementById('mobile_allowance').setAttribute("readonly", "readonly");
            document.getElementById('mobile_allowance').value='0.00';}
        if(status=="YES"){
            document.getElementById('mobile_allowance').removeAttribute("readonly", "readonly");}
    }

    function taDaAppl()
    {
        var status = document.getElementById('ta_da_alw_applicable').value;
        if(status!="YES"){
            document.getElementById('ta_da_allowance').setAttribute("readonly", "readonly");
            document.getElementById('ta_da_allowance').value='0.00';}
        if(status=="YES"){
            document.getElementById('ta_da_allowance').removeAttribute("readonly", "readonly");}
    }



    function bonusAppl()
    {
        var status = document.getElementById('if_bonus_applicable').value;
        if(status!="YES"){
            document.getElementById('bonus_applicable').setAttribute("readonly", "readonly");
            document.getElementById('bonus_applicable').value='0.00';}
        if(status=="YES"){
            //document.getElementById('bonus_applicable').removeAttribute("readonly", "readonly");
            var gross_salary = document.getElementById('gross_salary').value*1;
            document.getElementById('bonus_applicable').value= ((gross_salary*<?=$salaryPolicy->bonus?>)/100);;
        }
    }

    function overtimeAllwAppl()
    {
        var status = document.getElementById('if_overtime_applicable').value;
        if(status!="YES"){
            document.getElementById('overtime_applicable').setAttribute("readonly", "readonly");
            document.getElementById('overtime_applicable').value='0.00';}
        if(status=="YES"){
            document.getElementById('overtime_applicable').removeAttribute("readonly", "readonly");}
    }

    function pfAllwAppl()
    {
        var status = document.getElementById('pf_applicable').value;
        if(status!="YES"){
            document.getElementById('pf_percentage').setAttribute("readonly", "readonly");
            document.getElementById('pf').setAttribute("readonly", "readonly");
            document.getElementById('pf_percentage').value='0.00';
            document.getElementById('pf').value='0.00';}
        if(status=="YES"){
            document.getElementById('pf_percentage').removeAttribute("readonly", "readonly");
            document.getElementById('pf').removeAttribute("readonly", "readonly");}
    }

    function miAllwAppl()
    {
        var status = document.getElementById('mi_applicable').value;
        if(status!="YES"){
            document.getElementById('mi_percentage').setAttribute("readonly", "readonly");
            document.getElementById('medical_insurance').setAttribute("readonly", "readonly");
            document.getElementById('mi_percentage').value='0.00';
            document.getElementById('medical_insurance').value='0.00';}
        if(status=="YES"){
            document.getElementById('mi_percentage').removeAttribute("readonly", "readonly");
            document.getElementById('medical_insurance').removeAttribute("readonly", "readonly");}
    }

    function carFacilitiesAppll()
    {
        var status = document.getElementById('carFacilitiesAppl').value;
        if(status!="YES"){
            document.getElementById('carFacilitiesAmt').setAttribute("readonly", "readonly");
            document.getElementById('carFacilitiesAmt').value='0.00';}
        if(status=="YES"){
            //document.getElementById('carFacilitiesAmt').removeAttribute("readonly", "readonly");
            var basic_salaryy = document.getElementById('basic_salary').value;
            var carFacilitiesTaxAct = ((basic_salaryy*12)*5)/100;
            if(carFacilitiesTaxAct<60000){
                var carFacilitiesTaxabl = 60000;
            }else{
                var carFacilitiesTaxabl = carFacilitiesTaxAct;
            }
            document.getElementById('carFacilitiesAmt').value=carFacilitiesTaxabl.toFixed(2);
        }
    }



    window.onload = function(){
        foodAppl(); mobileAppl(); taDaAppl(); bonusAppl(); overtimeAllwAppl(); pfAllwAppl(); trnsAppl(); miAllwAppl(); carFacilitiesAppll();

    }
    //window.onload= foodAppl, mobileAppl, bonusAppl, overtimeAllwAppl, pfAllwAppl;
    //window.onload= trnsAppl;
</script>


<script>
    function salaryCal(){
        var gross_salary = document.getElementById('gross_salary').value*1;
        var basic_salary = document.getElementById('basic_salary').value= ((gross_salary*<?=$salaryPolicy->basic?>)/100);
        var pf_percentage = document.getElementById('pf_percentage').value*1;
        var pf = document.getElementById('pf').value= ((gross_salary*pf_percentage)/100);
        var mi_percentage = document.getElementById('mi_percentage').value*1;
        var medical_insurance = document.getElementById('medical_insurance').value= ((gross_salary*mi_percentage)/100);
        var house_rent = document.getElementById('house_rent').value= ((gross_salary*<?=$salaryPolicy->house_rent_allowance?>)/100);
        var medical_allowance =document.getElementById('medical_allowance').value= ((gross_salary*<?=$salaryPolicy->medical_allowance?>)/100);
        var convenience = document.getElementById('convenience').value= ((gross_salary*<?=$salaryPolicy->convenience?>)/100);
        var special_allowance= document.getElementById('special_allowance').value= ((gross_salary*<?=$salaryPolicy->special_allowance?>)/100);
        var food_allowance = document.getElementById('food_allowance').value*1;
        var mobile_allowance = document.getElementById('mobile_allowance').value*1;
        var ta_da_allowance = document.getElementById('ta_da_allowance').value*1;
//var medical_insurance = document.getElementById('medical_insurance').value*1;
        var bonus_applicable = document.getElementById('bonus_applicable').value*1;
        var overtime_applicable = document.getElementById('overtime_applicable').value *1;
        var carFacilitiesAmt = document.getElementById('carFacilitiesAmt').value *1;
        var advance_IT = document.getElementById('advance_IT').value *1;
//var total_taxable_amt2 = document.getElementById('total_taxable_amt').value *1;

        var total_invested_amt = document.getElementById('total_invested_amt').value *1;




        var yearly_basic = basic_salary*12;

        var yearly_houseRent = house_rent*12;
        if(yearly_houseRent>300000){
            var taxable_houseRent = yearly_houseRent-300000;
        }else{
            var taxable_houseRent = 0;
        }

        var yearly_convenience = convenience*12;
        if(yearly_convenience>30000){
            var taxable_convenience = yearly_convenience-30000;
        }else{
            var taxable_convenience = 0;
        }

        var yearly_medicalAlw = medical_allowance*12;
        if(yearly_medicalAlw>120000){
            var taxable_medicalAlw = yearly_medicalAlw-120000;
        }else{
            var medicalAlwAct = (yearly_basic*10)/100;
            var taxable_medicalAlw = yearly_medicalAlw-medicalAlwAct;
        }

        var yearly_specialAlw= special_allowance*12;

        if(bonus_applicable>0){
            var yearly_eidBonus = <?=$salaryPolicy->bonus?>;
        }else{
            var yearly_eidBonus = 0;
        }

        if(pf>0){
            var yearly_pf = (yearly_basic*pf_percentage)/100;}
        else{
            var yearly_pf = 0;
        }


        var invest_amt_tax1=0, invest_amt_tax2=0, invest_amt_tax3=0;

        if(total_invested_amt>250000){
            var invest_amt_tax1 = 37500;
        }else{
            var invest_amt_tax1 = (total_invested_amt*15)/100;
        }

        if(total_invested_amt>750000){
            var invest_amt_tax2 = 60000;
        }else if(total_invested_amt>250000 && total_invested_amt<750000){
            var invest_amt_tax2 = ((total_invested_amt-250000)*12)/100;
        }

        if(total_invested_amt>750000){
            var invest_amt_tax3 = ((total_invested_amt-750000)*10)/100;
        }
        var invest_amt_tax = invest_amt_tax1+ invest_amt_tax2+ invest_amt_tax3;



        var total_taxable = yearly_basic+taxable_houseRent+taxable_convenience+taxable_medicalAlw+yearly_specialAlw+yearly_eidBonus+yearly_pf+carFacilitiesAmt;

        var actualTax1=0, actualTax2=0, actualTax3=0, actualTax4=0, actualTax5=0;



        var pbi_gender = '<?=$pbi_info->PBI_SEX?>';
        if(pbi_gender=='Female'){
            var examptAmtGender = 300000;
        }else{
            var examptAmtGender = 250000;
        }


        var firstTaxAmt = total_taxable-examptAmtGender;

        if(firstTaxAmt>0){
            if(firstTaxAmt>400000){
                actualTax1 = 40000;
            }else{
                actualTax1 = (firstTaxAmt*10)/100;
            }

            if(firstTaxAmt>900000){
                actualTax2 = 75000;
            }else if(firstTaxAmt>400000 && firstTaxAmt<900000){
                actualTax2 = ((firstTaxAmt-400000)*15)/100;
            }

            if(firstTaxAmt>1500000){
                actualTax3 = 120000;
            }else if(firstTaxAmt>900000 && firstTaxAmt<1500000){
                actualTax3 = ((firstTaxAmt-900000)*20)/100;
            }

            if(firstTaxAmt>4500000){
                actualTax4 = 750000;
            }else if(firstTaxAmt>1500000 && firstTaxAmt<4500000){
                actualTax4 = ((firstTaxAmt-1500000)*25)/100;
            }

            if((firstTaxAmt-4500000)>0){
                actualTax5 = ((firstTaxAmt-4500000)*30)/100;
            }



            var totalTax = actualTax1+ actualTax2+ actualTax3+ actualTax4+ actualTax5- (invest_amt_tax + advance_IT);
            if(totalTax<5000){
                totalTax=5000;
            }
            var monthlyTax = totalTax/12;
        }

        if(monthlyTax>0){
            document.getElementById('income_tax').value = monthlyTax.toFixed(2);
            document.getElementById('income_tax_yearly').value = totalTax.toFixed(2);
            document.getElementById('total_taxable_amt').value = total_taxable.toFixed(2);
        }else{
            document.getElementById('income_tax').value = "";
        }
        var income_tax = document.getElementById('income_tax').value*1;
        document.getElementById('total_payable_amount').value = ((gross_salary)-(income_tax+pf+medical_insurance)).toFixed(2);


        var total_invested_amt_appl = (total_taxable*25)/100;
        document.getElementById('max_invested_amt').value = 'Max: '+total_invested_amt_appl;
        if(total_invested_amt>total_invested_amt_appl){
            alert('You Crossed the Limit 25% of Total Taxable Income');
            document.getElementById('total_invested_amt').value = 0;
        }else{
            document.getElementById('total_invested_amt').value = total_invested_amt;
        }


//document.getElementById('taxDetails').innerHTML = 'YB-'+yearly_basic + ' TH-'+taxable_houseRent+ ' TC-'+taxable_convenience+ ' TM-'+taxable_medicalAlw+ ' YS-'+yearly_specialAlw+ ' YE-'+yearly_eidBonus+ ' TT-'+total_taxable+ ' T1-'+actualTax1+ ' T2-'+actualTax2+ ' T3-'+actualTax3+ ' T4-'+actualTax4+ ' T5-'+actualTax5+ ' YTT-'+totalTax+ ' IAT-'+invest_amt_tax+ ' AIT-'+advance_IT;
    }
</script>
