<?php require_once 'support_file.php'; ?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Pending Customer to Creation';
$page="sales_app_management_pending_customer_list.php";		// PHP File Name
$table='app_get_customer_data';		// Database Table Name Mainly related to this page
$dealerTable='dealer_info';		// Database Table Name Mainly related to this page
$unique='id';			// Primary Key of this Database table
$shown='customer_name';
$dealer_custom_codess='dealer_custom_code';				// For a New or Edit Data a must have data field
$crud      =new crud($table);
$$unique = @$_GET[$unique];



if(prevent_multi_submit()) {

    if(isset($_POST['acceptNewCustomer']))
    {
        $now				= time();
        $entry_by = @$_SESSION['user'];
        $crud      =new crud($dealerTable);
        $crud->insert();

        mysqli_query($conn, "UPDATE app_get_customer_data SET status='ACCEPTED' WHERE id='" . $_POST['requestID'] . "'");
        $id = @$_POST['dealer_code'];
        unset($_POST);
        unset($$unique);
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
}

if(isset($_POST['rejectTheCustomer']))
{
    $_POST['status']='REJECTED';
    $crud->update($unique);
		$type=1;
		$msg='Successfully Updated.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['delete'])) {
    $condition = $unique . "=" . $$unique;
    $crud->delete($condition);
    unset($$unique);
    $type = 1;
    $msg = 'Successfully Deleted.';
}

}



if(isset($$unique))
{
$condition=$unique."=".$$unique;
$data=db_fetch_object($table,$condition);
    $array = (array)$data;
    foreach ($array as $key => $value)
    { $$key=$value;}
}
if(!isset($$unique)) $$unique=db_last_insert_id($table,$unique);

$dealer_name_e = @$customer_name;
$propritor_name_e = @$contact_person_name;
$town_code = @$town_code;
$mobile_no = @$mobile_no;
$contact_person = @$contact_person_name;
$depot = @$depot;
$contact_number = @$contact_number;
$dealer_type = @find_a_field('distributor_type','typeshorname','id='.$customer_type);
$contact_person_desig = @$contact_person_designation;
$address_e = @$address;
$commission = @$commission;
$national_id = @$nid;
$canceled = @$canceled;
$TIN_BIN = @$tin;
$bank_account = @$bank_account;
$account_code = @$account_code;
$select_dealer_do_regular = @$select_dealer_do_regular;
$region = @$region;
$customer_type = @find_a_field('distributor_type','typeshorname','id='.$customer_type);
$tsm = @$tsm;
$dealer_category = @$dealer_category;
$GetDealerCode = @$_GET['dealer_code'];
$area_code = @$territory;
?>
<?php

$sql_area = 'select a.AREA_CODE,concat(AREA_CODE," : ",a.AREA_NAME) from area a  where 1 order by a.AREA_NAME';

$res='select c.id,c.customer_name,c.address,c.mobile_no,ct.typedetails as type,t.AREA_NAME as territory_name 
from 
    '.$table.' c, 
    distributor_type ct,
    area t
 WHERE 
 
 ct.id=c.customer_type and
 c.territory=t.AREA_CODE   and 
 c.status="PENDING"
 order by c.'.$unique;
$sql_TOWN="Select town_code,concat(town_code,' : ',town_name) from town order by town_name";
$res_daeler_type="Select typeshorname,typedetails from distributor_type order by id";
$dealer_code_GET = @$_GET['dealer_code'];
?>

<?php require_once 'header_content.php'; ?>
        <script type="text/javascript">
            function DoNavPOPUP(lk)
            {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=600,left = 250,top = -1");}
        </script>
        <SCRIPT language=JavaScript>
function reload(form)
{
	var val=form.area_code.options[form.area_code.options.selectedIndex].value;
	self.location='dealer_info.php?dealer_code=<?=$dealer_code_GET?>&area_codeGET=' + val ;
}

function reload2(form)
{
	var val=form.dealersearchid.options[form.dealersearchid.options.selectedIndex].value;
	self.location='dealer_info.php?dealer_code=' + val ;
}

</script>
        <style>
            input[type=text] {
                width: 100%;
                margin-top: 5px;
                margin-bottom: 5px;
				font-size:11px
            }
            select {

                margin-top: 5px;
                margin-bottom: 5px;
            }
        </style>
    </head>
<?php require_once 'body_content.php'; ?>
<?php if(isset($_GET[$unique])){ ?>
<div class="col-md-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title?> <small class="text-danger">First Select Territory from the dropdown list</small></h2>
            <ul class="nav navbar-right panel_toolbox">
                <div class="input-group pull-right"> </div>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?=$page?>" enctype="multipart/form-data" style="font-size:11px" method="post" name="addem" id="addem" >
                <table style="width:100%; font-size: 11px">
                    <tr>
                        <th style="width:10%;">Territory <span class="required text-danger">*</span></th><th style="width: 2%;">:</th>
                        <input name="requestID" value="<?=$_GET['id']?>" type="hidden" />
                        <input name="section_id" id="section_id" value="<?=$sectionid?>" type="hidden" />
                        <input name="company_id" id="company_id" value="<?=$companyid?>" type="hidden" />
                        <input name="<?=$unique?>" id="<?=$unique?>" value="<?=$$unique?>" type="hidden" />
                        <input name="dealer_code" type="hidden" id="dealer_code" tabindex="1" value="<?=$dealer_code?>" readonly>
                        <td style="width: 21.5%">
                            <select class="select2_single form-control" name="area_code" id="area_code" tabindex="11" style="width: 90%">
                                <option></option>
                                <?=advance_foreign_relation($sql_area,$area_code);?>
                            </select>
                        </td>

                        <th style="width:10%;">Country</th><th style="width: 2%;">:</th>
                        <td style="width: 21.5%">
                            <select class="select2_single form-control" name="country" id="country" style="width: 90%"  tabindex="11">
                                <option value="1" selected>Bangladesh</option>
                                <? while($Cnrow = @mysqli_fetch_array($countryquery)){
                                    if($country==$Cnrow['BRANCH_ID']){ ?>
                                        <option value="<?=$Cnrow['id'];?>" selected><?=$Cnrow['country_name'];?></option>
                                    <?php } else { ?>
                                        <option value="<?=$Cnrow['id'];?>"><?=$Cnrow['country_name'];?></option>
                                    <?php }}?>
                            </select>
                        </td>

                        <th style="width:10%;">Serial</th><th style="width: 2%;">:</th>
                        <td style="width: 21%">
                            <input name="serial" type="text" required id="serial" tabindex="10" style="width: 30%;" placeholder="serial" value="<?=$serial?>" class="form-control col-md-7 col-xs-12" />
                            <input type="text" id="dealer_custom_code"  value="<?=$dealer_custom_code?>" placeholder="custom code" style="width: 60%; margin-left: 1px" name="dealer_custom_code" class="form-control col-md-7 col-xs-12">
                        </td>
                    </tr>
                    <tr>
                        <th style="">Region</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="region" tabindex="11" style="width: 90%">
                                <option></option>
                                <?=foreign_relation('branch','BRANCH_ID','BRANCH_NAME',$region,' status=1');?>
                            </select>
                        </td>

                        <th style="">Dealer Name</th><th>:</th>
                        <td>
                            <input type="text" id="dealer_name_e"  value="<?=$dealer_name_e?>" name="dealer_name_e" style="width: 90%" class="form-control col-md-7 col-xs-12">
                        </td>
                        <th style="">Area</th><th>:</th>
                        <td style="vertical-align: middle">
                            <input type="text" id="territory"  value="10" name="territory" style="width: 90%; height: " readonly  class="form-control col-md-7 col-xs-12">
                        </td>
                    </tr>

                    <tr>
                        <th style="">Proprietor's Name</th><th>:</th>
                        <td>
                            <input type="text" id="propritor_name_e"  value="<?=$propritor_name_e?>" name="propritor_name_e" style="width: 90%" class="form-control col-md-7 col-xs-12">
                        </td>
                        <th style="">Town</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="town_code" required id="town_code"  style="width: 90%" tabindex="3">
                                <option></option>
                                <?=advance_foreign_relation($sql_TOWN,$town_code);?>
                            </select>
                        </td>
                        <th style="">Propritor's Mobile No</th><th>:</th>
                        <td>
                            <input type="text" id="mobile_no"  value="<?=$mobile_no?>" name="mobile_no" style="width: 90%" class="form-control col-md-7 col-xs-12">
                        </td>
                    </tr>
                    <tr>
                        <th style="">In Charge person</th><th>:</th>
                        <td>
                            <?php if(@$_GET['area_codeGET']){ ?>
                                <input name="tsm" type="hidden" id="tsm" class="form-control col-md-7 col-xs-12" tabindex="2" value="<?=$PID= find_a_field('area','PBI_ID','AREA_CODE='.$_GET['area_codeGET']);?>" style="width: 90%" />
                                <?php } else { ?>
                                <input name="tsm" type="hidden" id="tsm" class="form-control col-md-7 col-xs-12" tabindex="2" value="<?=$tsm?>" style="width: 90%" />
                            <?php } ?>
                            <?php if(@$_GET['area_codeGET']){ ?>
                                <input name="tsmNAME" type="text" class="form-control col-md-7 col-xs-12" id="tsmNAME" tabindex="2" value="<?=$PBI_ID_GET = find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$PID);?>" readonly="readonly" style="width: 90%" />
                            <?php } else { ?>
                                <input name="tsmNAME" type="text" class="form-control col-md-7 col-xs-12" id="tsmNAME" tabindex="2" value="<?=$PBI_ID_GET = find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$tsm);?>" readonly="readonly" style="width: 90%" />
                            <?php } ?>
                        </td>
                        <th style="">Contact Person</th><th>:</th>
                        <td>
                            <input type="text" id="contact_person"  value="<?=$contact_person?>" name="contact_person" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        </td>
                        <th style="">Depot Name</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="depot" required id="depot" tabindex="7" style="width: 90%">
                                <?=foreign_relation('warehouse','warehouse_id','warehouse_name',$depot,' warehouse_type != "Purchase"');?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th style="">Contact Person Mobile</th><th>:</th>
                        <td>
                            <input type="text" id="contact_number"  value="<?=$contact_number?>" name="contact_number" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        </td>
                        <th style="">Trade Scheme Type</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="dealer_type" required id="dealer_type" tabindex="3" style="width: 90%">
                                <option></option>
                                <?=advance_foreign_relation($res_daeler_type,$dealer_type);?>
                            </select>
                        </td>
                        <th style="">Designation</th><th>:</th>
                        <td>
                            <input type="text" id="contact_person_desig"  value="<?=$contact_person_desig?>" name="contact_person_desig" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        </td>
                    </tr>
                    <tr>
                        <th style="">Customer Type</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="customer_type" required id="customer_type" tabindex="3" style="width: 90%">
                                <option></option>
                                <?=advance_foreign_relation($res_daeler_type,$customer_type);?>
                            </select>
                        </td>
                        <th style="">Address</th><th>:</th>
                        <td>
                            <textarea id="address_e" name="address_e" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px"><?=$address_e?></textarea>
                        </td>
                        <th style="">Commission</th><th>:</th>
                        <td><?php $userid=$_SESSION['userid']; if($userid=='10019'){?>
                                <input type="text" id="commission"  value="<?=$commission?>" name="commission" class="form-control col-md-7 col-xs-12" style="width: 90%"><?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <th style="">National ID</th><th>:</th>
                        <td>
                            <input type="text" id="national_id"  value="<?=$national_id?>" name="national_id" class="form-control col-md-7 col-xs-12" style="width: 90%">
                        </td>
                        <th style="">Status</th><th>:</th>
                        <td>
                            <select class="select_single form-control" style="font-size: 11px; width: 90%" name="canceled" id="canceled" tabindex="12">
                                <option <?=($canceled=='Yes')?'Selected':'';?>>Yes</option>
                                <option <?=($canceled=='No')?'Selected':'';?> >No</option>
                            </select>
                        </td>
                        <th style="">TIN / BIN</th><th>:</th>
                        <td>
                            <input type="text" id="TIN_BIN"  value="<?=$TIN_BIN?>" name="TIN_BIN" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        </td>
                    </tr>

                    <tr>
                        <th style="">Bank</th><th>:</th>
                        <td>
                            <select class="select2_single form-control" name="bank_account" id="bank_account" tabindex="3" style="width: 90%">
                                <option></option>
                                <? foreign_relation('bank_account_name','id','concat(account_name)',$bank_account,'1');?>
                            </select>
                        </td>
                        <th style="">Accounts Code</th><th>:</th>
                        <td>
                        <?php if(@$_SESSION['userid']=='10019' || $_SESSION['userid']=='9764' || $_SESSION['userid']=='10044'): ?>
                            <input type="text" id="account_code"  value="<?=$account_code?>" name="account_code" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        <?php endif; ?>
                        </td>

                        <th style="">Dealer Category</th><th>:</th>
                        <td>
                            <input type="text" value="<?=$dealer_category?>" name="dealer_category" class="form-control col-md-7 col-xs-12" style="width: 90%" />
                        </td>
                    </tr>
                </table>
                <hr>
                <button type="submit" name="acceptNewCustomer"  style="font-size: 12px" class="btn btn-primary"><i class="fa fa-plus"></i> Accept the Customer</button>
            </form>
            <form method="post">
                <button type="submit" name="rejectTheCustomer" style="float: left; font-size: 12px" class="btn btn-danger"><i class="fa fa-close"></i> Reject The Customer</button>
            </form>
        </div>
    </div>
</div>
    <?php } ?>
<?php if(!isset($_GET[$unique])){ ?>
<?=$crud->report_templates_with_title_and_class($res,'Pending Customer to Creation','12');?>
<?php } ?>
<?=$html->footer_content();mysqli_close($conn);?>