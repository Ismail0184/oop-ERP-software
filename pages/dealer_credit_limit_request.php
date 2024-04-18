<?php
require_once 'support_file.php';
$title='Request for Credit Limit';
$unique='id';
$unique_field='fname';
$table="dealer_credit_limit_request";
$page="dealer_credit_limit_request.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$GetDealerCode = @$_GET['dealer_code'];
$dealer_master = find_all_field('dealer_info','account_code','dealer_code='.$GetDealerCode);
$dealer_master_account_code = @$dealer_master->account_code;
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$now=$dateTime->format("Y-m-d, h:i:s A");

$companyid = @$_SESSION['companyid'];
$sectionid = @$_SESSION['sectionid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
    $sec_com_connection_wa=' and 1';
} else {
    $sec_com_connection=" and d.company_id='".$companyid."' and d.section_id in ('400000','".$sectionid."')";
    $sec_com_connection_wa=" and company_id='".$companyid."' and section_id in ('400000','".$sectionid."')";
}

if(prevent_multi_submit()){
    if(isset($_POST['add']))
    {   
        $_POST['entry_by'] = $_SESSION['userid'];
        $_POST['entry_at'] = $now;
        $_POST['section_id'] = $_SESSION['sectionid'];
        $_POST['company_id'] = $_SESSION['companyid'];
        $_POST['requested_date'] = date('Y-m-d');
        $crud->insert();
    }
}

if(isset($_POST['deleteRequest'])){
    $condition=$unique."=".$$unique;
    $crud->delete($condition);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['updateRequest'])){
    $up= mysqli_query($conn, "Update ".$table." SET 
    dealer_code     ='".$_POST['dealer_code']."',
    current_balance ='".$_POST['current_balance']."',
    current_credit_limit ='".$_POST['current_credit_limit']."',
    requested_limit ='".$_POST['requested_limit']."',
    remarks ='".$_POST['remarks']."',
    limit_duration ='".$_POST['limit_duration']."'
    WHERE id=".$$unique."");
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}




if(isset($$unique)>0) {
    $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}
}
if ($GetDealerCode>0) {
    $current_balance = find_a_field('journal', 'SUM(cr_amt-dr_amt)', 'ledger_id=' . $dealer_master_account_code);
    $current_credit_limit = @$dealer_master->credit_limit;
} else {
    $current_balance = @$current_balance;
    $current_credit_limit = @$current_credit_limit;
}

$requested_limit = @$requested_limit;
$limit_duration  = @$limit_duration;
$remarks  = @$remarks;
$status = @$status;
$dealer_code = @$dealer_code;
if($status=='APPROVEDS')
{
    $statusIs = 'APPROVED';
} elseif($status=='REJECTED') {
    $statusIs = 'REJECTED';
}


if(isset($_POST['viewReport']))
{
    $dateConn=' and r.requested_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
    $res="Select 
r.id,    
r.requested_date as date,
concat(d.dealer_code,' : ',d.dealer_name_e) as dealer_name,
r.remarks,
r.approved_remarks as 'approved/rejected note',
r.current_balance as ledger_balance,
r.current_credit_limit as current_credit_limit,
r.requested_limit,
IF (r.limit_duration = 'Longtime', 'Unlimited', 'Once Only') as type,
r.status as status
from 
dealer_info d ,
accounts_ledger a,
dealer_credit_limit_request r,
users u
 where 
     r.entry_by = u.user_id and 
 d.account_code=a.ledger_id and
 d.canceled in ('Yes') and
 r.dealer_code=d.dealer_code".$dateConn.$sec_com_connection." 
 order by r.id desc";
} else {
    $dateConn='';
    $res="Select 
r.id,
r.requested_date as date,
concat(d.dealer_code,' : ',d.dealer_name_e) as dealer_name,
r.remarks,
r.approved_remarks as 'approved/rejected note',
r.current_balance as ledger_balance,
r.current_credit_limit as current_credit_limit,
r.requested_limit,
IF (r.limit_duration = 'Longtime', 'Unlimited', 'Once Only') as type,
r.status as status
from 
dealer_info d ,
accounts_ledger a,
dealer_credit_limit_request r,
users u
 where 
     r.entry_by = u.user_id and 
 d.account_code=a.ledger_id and
 d.canceled in ('Yes') and
 r.dealer_code=d.dealer_code ".$sec_com_connection."
 order by r.id desc limit 50";
}


?>

<?php require_once 'header_content.php'; ?>

    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?id="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 250,top = -1");}
    </script>
<script type="text/javascript">
function reload(form){
var val=form.dealer_code.options[form.dealer_code.options.selectedIndex].value;
self.location='<?=$page;?>?<?php if($$unique>0){?>id=<?=$$unique?>&<?php } ?>dealer_code=' + val ;}</script>

<?php if(isset($_GET[$unique])):
        require_once 'body_content_without_menu.php';
    elseif(isset($_GET['dealer_code'])):
        require_once 'body_content_nva_sm.php';
    else :
    require_once 'body_content.php';
    endif;
    ?>


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
            <form action="" enctype="multipart/form-data" method="post" name="addem" id="addem" style="font-size: 11px" >
                <table  class="table table-striped table-bordered" style="width:100%">
                    <tr style="background-color: #3caae4; color:white">
                        <th class="text-center">Select Dealer</th>
                        <th class="text-center">Ledger Balance</th>
                        <th class="text-center">Current Limit</th>
                        <th class="text-center">Request Limit</th>
                        <th class="text-center">Remarks</th>
                        <th class="text-center">Type</th>
                        <?php if(!isset($_GET[$unique])){ ?>
                        <th class="text-center">Option</th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td style="">
                            <select class="select2_single form-control" style="width:90%; font-size: 11px" tabindex="-1" required="required"  name="dealer_code" id="dealer_code" onchange="javascript:reload(this.form)">
                                <option></option>
                                <?=foreign_relation('dealer_info', 'dealer_code', 'CONCAT(dealer_code," : ", dealer_name_e)', ($GetDealerCode>0)? $GetDealerCode : $dealer_code, 'canceled="YES"'.$sec_com_connection_wa.''); ?>
                            </select>
                        </td>
                        <td style="width:10%"><input type="text" name="current_balance" value="<?=$current_balance;?>" class="form-control col-md-7 col-xs-12" readonly style="width: 90%; font-size: 11px;"></td>
                        <td style="width:10%"><input type="text" name="current_credit_limit" value="<?=$current_credit_limit?>" class="form-control col-md-7 col-xs-12" readonly style="width: 90%; font-size: 11px;"></td>
                        <td style="width:10%"><input type="number" name="requested_limit" value="<?=$requested_limit?>" class="form-control col-md-7 col-xs-12" required style="width: 90%; font-size: 11px;" placeholder="request limit"></td>
                        <td style="width:15%"><input type="text" name="remarks" value="<?=$remarks?>" class="form-control col-md-7 col-xs-12" required style="width: 90%; font-size: 11px;" placeholder="remarks"></td>
                        <td style="width:15%">
                            <select class="select form-control" required style="width:90%; font-size: 11px" name="limit_duration">
                                <option value="">-- limit type --</option>
                                <option value="Longtime" <?php if ($limit_duration=='Longtime') {echo 'selected';} ?>>Unlimited</option>
                                <option value="For one time DO" <?php if ($limit_duration=='For one time DO') {echo 'selected';} ?>>Once Only</option>
                            </select>
                        </td>
                        <?php if(!isset($_GET[$unique])){ ?>
                        <td style="width:5%">
                            <?php
                            if($GetDealerCode>0){?>
                                <button type="submit" class="btn btn-primary" name="add" id="add" style="font-size: 12px">Add <i class="fa fa-plus"></i></button>
                            <?php } ?>
                        </td>
                        <?php } ?>
                    </tr>
                </table>
                <?php if($$unique>0){
                    if($status=='PENDINGS' || $status=='REJECTED'){
                    ?>
                <div class="text-center">
                    <button type="submit" class="btn btn-danger" name="deleteRequest" style="font-size: 12px"><i class="fa fa-eraser"></i> Delete</button>
                    <button type="submit" class="btn btn-primary" name="updateRequest" style="font-size: 12px">Update  <i class="fa fa-edit"></i></button>
                </div>
                <?php } else { echo '<h6 style="text-align: center; color: red; font-weight: bold">This credit limit request has been '.$statusIs.' !!</h6>';}} ?>
            </form>
        </div>
    </div>
</div>

<?php if(!isset($_GET[$unique])){ ?>
    <?=$crud->report_templates_with_status($res,$title='Requested Logs');?>
<?php } ?>
<?=$html->footer_content();?>