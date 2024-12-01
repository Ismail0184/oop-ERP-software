<?php
require_once 'support_file.php';
$title="Job Info";


$now=time();
$unique='PBI_ID';
$unique_field='ESSENTIAL_ID';
$table="essential_info";
$table_details='personnel_basic_info';
$page="hrm_employee_job_info.php";
$crud      =new crud($table);


if (isset($_POST['selectEmployee']))
{
    $_SESSION['employee_selected'] = @$_POST['employee_selected'];
}

if (isset($_POST['cancelEmployee']))
{
    unset($_SESSION['employee_selected']);
}
$$unique = $_SESSION['employee_selected'];
$targeturl="<meta http-equiv='refresh' content='0;hrm_employee_report.php'>";




if(prevent_multi_submit()){


    if(isset($_POST['record']))
    {
        $_POST['PBI_DOJ']=@$_POST['ESSENTIAL_JOINING_DATE'];
        $_POST['PBI_DEPARTMENT']=@$_POST['ESS_DEPARTMENT'];
        $_POST['PBI_DESIGNATION']=@$_POST['ESS_DESIGNATION'];
        $crud->insert();
        unset($_POST);
    }




//for modify..................................
        if(isset($_POST['modify']))
        {
            $_POST['edit_at']=time();
            $_POST['edit_by']=$_SESSION['userid'];

            $crud->update($unique);


            $_POST['PBI_DOJ']=$_POST['ESSENTIAL_JOINING_DATE'];
            $_POST['PBI_DEPARTMENT']=$_POST['ESS_DEPARTMENT'];
            $_POST['PBI_DESIGNATION']=$_POST['ESS_DESIGNATION'];
            $crud      =new crud($table_details);
            $crud->update($unique);
            $type=1;
            //echo $targeturl;
            //echo "$targeturl";
            //echo "<script>self.opener.location = '$page'; self.blur(); </script>";
            echo "<script>window.close(); </script>";
        }

//for Delete..................................
        if(isset($_POST['delete']))
        {   $condition=$unique."=".$$unique;
            $crud->delete($condition);
            unset($$unique);
            $type=1;
            $msg='Successfully Updated.';
            echo "<script>window.close(); </script>";
        }}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

$ESSENTIAL_INFO=find_a_field("".$table."","".$unique."","".$unique."=".$$unique."");

?>



<?php require_once 'header_content.php'; ?>
<style>
    input[type=text]{
        font-size: 11px;
    }
</style>
<?php require_once 'body_content.php'; ?>


<form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
    <? require_once 'support_html.php';?>

    <!-- input section-->
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>:: Job Information :: <?php if($_SESSION['employee_selected']>0) { ?><?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$_SESSION['employee_selected'])?><?php } ?></h2>
                    <table align="center" style="width: 80%; font-size: 11px">
                    <tr>
                        <td>
                            <select class="select2_single form-control" name="employee_selected" style="width:80%; height: 30px;margin-top: 5px; margin-bottom: 5px; font-size: 11px" class="form-control col-md-7 col-xs-12">
                                <option></option>
                                <?=foreign_relation('personnel_basic_info','PBI_ID','concat(PBI_ID," : ",PBI_NAME)',$_SESSION['employee_selected'],'PBI_JOB_STATUS="In Service" order by serial');?>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="selectEmployee" id="record" class="btn btn-primary" style="font-size: 12px">Select & Proceed <i class="fa fa-forward"></i></button>
                            <?php if($_SESSION['employee_selected']>0) { ?>
                            <button type="submit" name="cancelEmployee" id="record" class="btn btn-danger" style="font-size: 12px">Cancel <i class="fa fa-close"></i></button>
    <?php } ?>
                        </td>
                    </tr>
                </table>




                <ul class="nav navbar-right panel_toolbox">
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <table style="width: 100%; font-size: 11px">
                    <tr>
                        <td>Employment Type :</td><td><input type="hidden" id="<?=$unique?>" style="width:80%; height: 30px"  readonly   name="<?=$unique?>" value="<?=$_SESSION['employee_selected'];?>" class="form-control col-md-7 col-xs-12" >
                            <select name="EMPLOYMENT_TYPE" id="EMPLOYMENT_TYPE" style="width:80%; height: 30px;margin-top: 5px; margin-bottom: 5px; font-size: 11px" class="form-control col-md-7 col-xs-12">
                                <option></option>
                                <? foreign_relation('employment_type','id','employment_type_name',$EMPLOYMENT_TYPE,'1');?>
                            </select>
                        </td>
                        <td>Job Location:</td><td>
                            <select name="ESS_JOB_LOCATION" id="ESS_JOB_LOCATION" style="width:80%; height: 30px;margin-top: 5px;font-size: 11px" class="form-control col-md-7 col-xs-12">
                                <option></option>
                                <? foreign_relation('job_location_type','id','job_location_name',$ESS_JOB_LOCATION,'1 order by job_location_name');?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Reporting Authority 1:</td><td>
                            <select name="ESSENTIAL_REPORTING_1" id="ESSENTIAL_REPORTING_1" style="width:80%; height: 30px;font-size: 11px" class="select2_single form-control">
                                <option></option>
                                <? foreign_relation('personnel_basic_info p, designation d','p.PBI_ID','concat(p.PBI_NAME," - ",d.DESG_DESC)',$ESSENTIAL_REPORTING_1,'p.PBI_DESIGNATION=d.DESG_ID order by p.PBI_NAME');?>
                            </select></td>
                        <td>Reporting Authority 2:</td><td>
                            <select name="ESSENTIAL_REPORTING" id="ESSENTIAL_REPORTING" style="width:80%; height: 30px;font-size: 11px" class="select2_single form-control">
                                <option></option>
                                <? foreign_relation('personnel_basic_info p, designation d','p.PBI_ID','concat(p.PBI_NAME," - ",d.DESG_DESC)',$ESSENTIAL_REPORTING,'p.PBI_DESIGNATION=d.DESG_ID order by p.PBI_NAME');?>
                            </select></td>
                    </tr>
                    <tr><td style="height: 5px"></td></tr>
                    <tr>
                        <td>Department :</td>
                        <td>
                            <select name="ESS_DEPARTMENT" id="ESS_DEPARTMENT" style="width:80%; height: 30px;margin-top: 5px;font-size: 11px" class="select2_single form-control">
                                <option></option>
                                <? foreign_relation('department','DEPT_ID','DEPT_DESC',$ESS_DEPARTMENT,' 1 order by DEPT_ID asc');?>
                            </select>
                        </td>

                        <td>Designation :</td><td><select name="ESS_DESIGNATION" id="ESS_DESIGNATION" style="width:80%; height: 30px;margin-top: 5px;font-size: 11px" class="select2_single form-control">
                                <option></option>
                                <? foreign_relation('designation','DESG_ID','DESG_DESC',$ESS_DESIGNATION,'1 order by DESG_DESC');?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Appointment Ref. No :</td><td><input type="text" id="ESSENTIAL_APPOINT_REF_NO" style="width:80%; height: 30px;margin-top: 5px"   name="ESSENTIAL_APPOINT_REF_NO" value="<?=$ESSENTIAL_APPOINT_REF_NO;?>" class="form-control col-md-7 col-xs-12" ></td>
                        <td>Appointment Date :</td><td><input type="date" id="ESSENTIAL_APPOINT_DATE" style="width:80%; height: 30px;margin-top: 5px; font-size: 11px"   name="ESSENTIAL_APPOINT_DATE" value="<?=$ESSENTIAL_APPOINT_REF_NO;?>" class="form-control col-md-7 col-xs-12" ></td>

                    </tr>
                    <tr>
                        <td>Corporate Email ID :</td><td>
                            <input type="text" id="ESS_CORPORATE_EMAIL" style="width:80%; height: 30px;margin-top: 5px" name="ESS_CORPORATE_EMAIL" value="<?=$ESS_CORPORATE_EMAIL;?>" class="form-control col-md-7 col-xs-12" >
                        </td>
                        <td>Corporate Phone No:</td><td><input type="text" id="ESS_CORPORATE_PHONE" style="width:80%; height: 30px;margin-top: 5px" name="ESS_CORPORATE_PHONE" value="<?=$ESS_CORPORATE_PHONE;?>" class="form-control col-md-7 col-xs-12" ></td>
                    </tr>
                    <tr>
                        <td>Joining Date :</td><td>
                            <input type="date" id="ESSENTIAL_JOINING_DATE" style="width:80%; height: 30px;margin-top: 5px; font-size: 11px" name="ESSENTIAL_JOINING_DATE" value="<?=$ESSENTIAL_JOINING_DATE;?>" class="form-control col-md-7 col-xs-12" ></td>

                        <td> Confirmation Date :</td><td>
                            <input type="text" id="ESSENTIAL_CONFIRM_DATE" style="width:80%; height: 30px;margin-top: 5px" name="ESSENTIAL_CONFIRM_DATE" value="<?=$ESSENTIAL_CONFIRM_DATE;?>" class="form-control col-md-7 col-xs-12" ></td>
                    </tr>

                    <tr>
                        <td>Job Status :</td><td>
                            <select name="PBI_JOB_STATUS" id="PBI_JOB_STATUS" style="width:80%; height: 30px;margin-top: 5px; margin-bottom: 5px; font-size: 11px" class="form-control col-md-7 col-xs-12">
                                <option <?php if($PBI_JOB_STATUS=='In Service') echo 'Selected';?>>In Service</option>
                                <option <?php if($PBI_JOB_STATUS=='Not In Service') echo 'Selected';?>>Not In Service</option>
                            </select>
                        <td> </td><td></td>
                    </tr>
                </table>

            </div>
        </div>
    </div>


    <!-------------------list view ------------------------->
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>:: Bank Information ::</h2>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <table style="width: 100%;">

                    <tr>
                        <td>Bank Name :</td><td><input type="text" id="ESS_BANK" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_BANK" value="<?=$ESS_BANK;?>" class="form-control col-md-7 col-xs-12" ></td>
                        <td>Branch :</td><td><input type="text" id="ESS_BANK_BRANCH" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_BANK_BRANCH" value="<?=$ESS_BANK_BRANCH;?>" class="form-control col-md-7 col-xs-12" ></td>
                    </tr>

                    <tr>
                        <td>Swift Code:</td><td><input type="text" id="ESS_BANK_SWIFT" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_BANK_SWIFT" value="<?=$ESS_BANK_SWIFT;?>" class="form-control col-md-7 col-xs-12" ></td>
                        <td>Account Name:</td><td><input type="text" id="ESS_BANK_ACC_NAME" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_BANK_ACC_NAME" value="<?=$ESS_BANK_ACC_NAME;?>" class="form-control col-md-7 col-xs-12" ></td>
                    </tr>
                    <tr>
                        <td>Account Number :</td><td>
                            <input type="text" id="ESS_BANK_ACC_NO" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_BANK_ACC_NO" value="<?=$ESS_BANK_ACC_NO;?>" class="form-control col-md-7 col-xs-12" ></td>
                           <td>Card No:</td><td><input type="text" id="ESS_DEBIT_CARD_NO" style="width:80%; height: 30px;margin-top: 5px"   name="ESS_DEBIT_CARD_NO" value="<?=$ESS_DEBIT_CARD_NO;?>" class="form-control col-md-7 col-xs-12" ></td>

                    </tr>

                    <tr>
                        <td style="height: 50px"></td></tr>
                </table>


            </div>

        </div></div>
    <!-------------------End of  List View --------------------->
    <table align="center" style="width: 100%">
        <tr>
            <td align="center">
                <?php if($ESSENTIAL_INFO>0){ ?>
                    <button type="submit" name="modify" id="modify" class="btn btn-primary" style="font-size: 12px"><i class="fa fa-edit"></i> Update Job Information</button>
                <?php } else { ?>
                    <button type="submit" name="record" id="record" class="btn btn-primary" style="font-size: 12px"><i class="fa fa-plus"></i> Add Job Information</button>

                <?php } ?>

            </td></tr>
    </table>
</form>



<?=$html->footer_content();mysqli_close($conn);?>