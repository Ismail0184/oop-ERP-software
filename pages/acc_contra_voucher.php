<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Contra Voucher';
$unique='voucherno';
$unique_field='voucher_date';
$table_journal_master="journal_voucher_master";
$table_contra="coutra";
$coutra_no_unique='coutra_no';
$page="acc_contra_voucher.php";
$crud      =new crud($table_journal_master);
$$unique = @$_POST[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";
$create_date=date('Y-m-d');
$jv=next_journal_voucher_id();
if(prevent_multi_submit()) {
    if(isset($_POST[$unique]))
    {
        if (isset($_POST['initiate'])) {
            $_POST['section_id'] = $_SESSION['sectionid'];
            $_POST['company_id'] = $_SESSION['companyid'];
            $_POST['ip'] = $ip;
            if(!empty($_POST['Cheque_Date'])){
                $ckd = $_POST['Cheque_Date'];
                $_POST['Cheque_Date'] =@$_POST['Cheque_Date'];
            } else {
                $_POST['Cheque_Date']='';
            }
            $_POST['entry_by'] = $_SESSION['userid'];
            $_POST['entry_at'] = date('Y-m-d H:s:i');
            $_SESSION['initiate_contra_note'] = @$_POST[$unique];
            $_POST['journal_type'] = 'Contra';
            $_POST['entry_status'] = 'MANUAL';
            $crud->insert();
            unset($_POST);
        }

//for modify PS information ...........................
        if (isset($_POST['modify'])) {
            $d = $_POST['voucher_date'];
            $_POST['voucher_date'] = date('Y-m-d', strtotime($d));
            if(!empty($_POST['Cheque_Date'])){
                $ckd = $_POST['Cheque_Date'];
                $_POST['Cheque_Date'] = @$_POST['Cheque_Date'];
            } else {
                $_POST['Cheque_Date']='';
            }
            $_POST['edit_at'] = time();
            $_POST['edit_by'] = @$_SESSION['userid'];
            $crud->update($unique);
            $type = 1;
            unset($_POST);
        }


//for single FG Add...........................
        if (isset($_POST['add'])) {
            if ($_POST['dr_amt'] > 0) {
                $type = 'Debit';
            } elseif ($_POST['cr_amt'] > 0) {
                $type = 'Credit';
            }

            $tdates = date("Y-m-d");
            $day = date('l', strtotime($idatess));
            $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $timess = $dateTime->format("d-m-y  h:i A");
            if (($_POST['dr_amt'] && $_POST['cr_amt']) > 0) {
                echo "<script>alert('Yor are trying to input an invalid transaction!!')</script>";
                echo $targeturl;
            } else {

                if ((($_POST['dr_amt'] || $_POST['cr_amt']) > 0) && ($_SESSION['initiate_contra_note']>0)) {
                    add_to_coutra($_SESSION['initiate_contra_note'],0, $proj_id, $_POST['narration'], $_POST['ledger_id'], $_POST['dr_amt'],
                        $_POST['cr_amt'], $type,0,0,$_POST['Cheque_No'],$_POST['Cheque_Date'],$_POST['Cheque_of_bank'],0,$_POST['cc_code'],0,'MANUAL',$ip,$_POST['voucher_date'],$_SESSION['sectionid'],$_SESSION['companyid'],$_SESSION['userid'],$create_date,$now,$day
                        ,$thisday,$thismonth,$thisyear,0);
                    $_SESSION['contra_note_last_narration']=@$_POST['narration'];
                }
            }} } // end post unique
} // prevent multi submit
$initiate_contra_note = @$_SESSION['initiate_contra_note'];
$contra_note_last_narration = @$_SESSION['contra_note_last_narration'];
if($initiate_contra_note>0){
$rs="Select 
j.id as jid,
j.coutra_no,
j.coutradate,
j.coutra_date,
j.narration,
j.ledger_id,
j.dr_amt,
j.cr_amt,
j.type,
j.cheq_no,
j.cheq_date,
j.bank,
j.cc_code,
j.sub_ledger_id,
j.day_name,
a.*,c.center_name as cname 
from 
".$table_contra." j,
 accounts_ledger a,cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.entry_status='MANUAL' and 
 j.".$coutra_no_unique."='".$initiate_contra_note."'";
$re_query=mysqli_query($conn, $rs);
while($uncheckrow=mysqli_fetch_array($re_query)){
    if (isset($_POST['confirmsave']) && ($uncheckrow['coutra_no']>0)) {
        add_to_journal_new($uncheckrow['coutradate'],$proj_id, $jv, $uncheckrow['coutra_date'], $uncheckrow['ledger_id'], $uncheckrow['narration'], $uncheckrow['dr_amt'], $uncheckrow['cr_amt'],'Contra', $uncheckrow['coutra_no'],$uncheckrow['jid'],$uncheckrow['cc_code'],$uncheckrow['sub_ledger_id'],$_SESSION['usergroup'],$uncheckrow['cheq_no'],$uncheckrow['cheq_date'],$create_date,$ip,$now,$uncheckrow['day_name'],$thisday,$thismonth,$thisyear,'','','');
    }
    $ids=$uncheckrow['jid'];
    if(isset($_POST['deletedata'.$ids]))
    {  mysqli_query($conn, ("DELETE FROM ".$table_contra." WHERE id='$ids'"));
        unset($_POST);
    }
    if(isset($_POST['editdata'.$ids]))
    {  mysqli_query($conn, ("UPDATE ".$table_contra." SET ledger_id='".$_POST['ledger_id']."', cc_code='".$_POST['cc_code']."',narration='".$_POST['narration']."',dr_amt='".$_POST['dr_amt']."',cr_amt='".$_POST['cr_amt']."' WHERE id=".$ids));
        unset($_POST);
    }
}
if (isset($_POST['confirmsave'])) {
    $up_journal="UPDATE ".$table_contra." SET entry_status='UNCHECKED' where ".$coutra_no_unique."=".$initiate_contra_note."";
    $up_query=mysqli_query($conn, $up_journal);
    $up_master=mysqli_query($conn, "UPDATE journal SET status='UNCHECKED' where jv_no=".$jv);
    $up_master=mysqli_query($conn, "UPDATE ".$table_journal_master." SET entry_status='UNCHECKED' where ".$unique."=".$initiate_contra_note."");
    unset($_SESSION['initiate_contra_note']);
    unset($_SESSION['contra_note_last_narration']);
    unset($_POST);
    unset($$unique);
}


if (isset($_GET['id'])) {
    $edit_value=find_all_field(''.$table_contra.'','','id='.$_GET['id'].'');
}
    $edit_value_ledger_id = @$edit_value->ledger_id;
    $edit_value_cc_code = @$edit_value->cc_code;
    $edit_value_narration = @$edit_value->narration;
    $edit_value_dr_amt = @$edit_value->dr_amt;
    $edit_value_cr_amt = @$edit_value->cr_amt;

$COUNT_details_data=find_a_field(''.$table_contra.'','Count(id)',''.$coutra_no_unique.'='.$initiate_contra_note.'');
//for Delete..................................
if (isset($_POST['cancel'])) {
    $crud = new crud($table_contra);
    $condition =$coutra_no_unique."=".$initiate_contra_note;
    $crud->delete_all($condition);
    $crud = new crud($table_journal_master);
    $condition=$unique."=".$initiate_contra_note;
    $crud->delete($condition);
    unset($_SESSION['contra_note_last_narration']);
    unset($_SESSION['initiate_contra_note']);
    unset($_POST);
    unset($initiate_contra_note);
    unset($$unique);
}
    $initiate_contra_note = @$_SESSION['initiate_contra_note'];
// data query..................................
   $condition=$unique."=".$initiate_contra_note;
    $data=db_fetch_object($table_journal_master,$condition);
    $array = (array)$data;
    foreach ($array as $key => $value)
    { $$key=$value;}
}

$voucher_date = @$voucher_date;
$date = date('Y-m-d');
$paid_to = @$paid_to;
$Cheque_of_bank = @$Cheque_of_bank;
$Cheque_No = @$Cheque_No;
$Cheque_Date = @$Cheque_Date;
$amount = @$amount;
$party_ledger = @$party_ledger;

$sql2="select a.tr_no, a.jvdate as Date,a.jv_no as Voucher_No,SUM(a.dr_amt) as amount
from  journal a where a.tr_from='Contra' and a.user_id='".$_SESSION['userid']."' and a.section_id='".$_SESSION['sectionid']."' and a.company_id='".$_SESSION['companyid']."'  group by a.tr_no  order by a.id desc limit 10";
$rs="Select 
j.id as jid,
concat(a.ledger_id, ' : ' ,a.ledger_name) as Account_Head,c.center_name,j.narration,j.dr_amt,j.cr_amt
from 
".$table_contra." j,
 accounts_ledger a,cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.entry_status='MANUAL' and 
 j.".$coutra_no_unique."='".$initiate_contra_note."'
 ";
?>

<?php require_once 'header_content.php'; ?>
<style>
    input[type=text]:focus {
        background-color: lightblue;
    }
</style>
<script type="text/javascript">
    function OpenPopupCenter(pageURL, title, w, h) {
        var left = (screen.width - w) / 2;
        var top = (screen.height - h) / 4;  // for 25% - devide by 4  |  for 33% - devide by 3
        var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }
</script>
<?php require_once 'body_content_nva_sm.php'; ?>

<div class="col-md-8 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title; ?> <small class="text-danger">field marked with * are mandatory</small></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">

            <form action="<?=$page;?>" enctype="multipart/form-data" method="post" name="addem" id="addem" style="font-size: 11px" ><table align="center" style="width:100%">
                    <tr>
                        <th style="width:15%;">Transaction Date <span class="required text-danger">*</span></th><th style="width: 2%;">:</th>
                        <td><input type="date" id="voucher_date"  required="required" name="voucher_date" value="<?=($voucher_date!='')? $voucher_date : date('Y-m-d') ?>" max="<?=date('Y-m-d');?>" min="<?=date('Y-m-d', strtotime($date .' -'.find_a_field('acc_voucher_config_log','back_date_limit','status="Active"'). 'day'));?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle" ></td>

                        <th style="width:15%;">Transaction No <span class="required text-danger">*</span></th><th style="width: 2%">:</th>
                        <td><input type="text" required="required" name="<?=$unique?>" id="<?=$unique?>"  value="<?=($initiate_contra_note!='')? $initiate_contra_note : automatic_voucher_number_generate($table_contra,$coutra_no_unique,1,4); ?>" class="form-control col-md-7 col-xs-12" readonly style="width: 90%; font-size: 11px;"></td>
                    </tr>

                    <tr>
                        <th style="">Person</th><th>:</th>
                        <td><input type="text" id="paid_to"  value="<?=$paid_to;?>" name="paid_to" class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>

                        <th>Of Bank</th><th>:</th>
                        <td><input type="text" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>" class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;"></td>
                    </tr>
                    <tr>
                        <th style="">Cheque No</th><th>:</th>
                        <td><input type="text" id="Cheque_No"  value="<?=$Cheque_No;?>" name="Cheque_No"  class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>

                        <th>Cheque Date</th><th>:</th>
                        <td><input type="date" id="Cheque_Date"   value="<?=$Cheque_Date;?>" name="Cheque_Date"  class="form-control col-md-7 col-xs-12"  style="width: 90%; margin-top: 5px; font-size: 11px; vertical-align: middle"></td>
                    </tr>
                </table>


                <?php if($initiate_contra_note){
                    if($COUNT_details_data>0) {
                        $ml='40';
                        $display='style="margin-left:40%; margin-top: 22px;"';

                    } else {
                        $ml='40';
                        $display='style="margin-left:40%; margin-top: 15px; display: none"';
                    }
                    ?>
                    <div class="form-group" style="margin-left:<?=$ml;?>%; margin-top: 15px">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" name="modify" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Update?");' style="font-size: 11px">Update Contra Voucher</button>
                        </div></div>

                    <div class="form-group" <?=$display;?>>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <a  href="voucher_print_preview.php?v_type=contra&vo_no=<?=$initiate_contra_note;?>&v_date=<?=$voucher_date;?>" target="_blank" style="color: blue; text-decoration: underline; font-size: 11px; font-weight: bold; vertical-align: middle">View Contra Voucher</a>
                        </div></div>
                <?php   } else {?>
                    <div class="form-group" style="margin-left:40%; margin-top: 15px">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" name="initiate" class="btn btn-primary" style="font-size: 11px">Initiate Contra Voucher</button>
                        </div></div>
                <?php } ?>

            </form>
        </div>
    </div>
</div>




<?=recentvoucherview($sql2,'voucher_view_popup_ismail.php','Contra','166px','');?>
<?php if($initiate_contra_note):  ?>
    <form action="<?=$page;?>" enctype="multipart/form-data" name="addem" id="addem" style="font-size: 11px" class="form-horizontal form-label-left" method="post">
        <input type="hidden" name="payment_no" id="payment_no" value="<?=$initiate_contra_note;?>">
        <input type="hidden" name="voucher_date" id="voucher_date" value="<?=$voucher_date;?>">
        <input type="hidden" name="<?=$unique?>" id="<?=$unique?>"  value="<?=$initiate_contra_note;?>">
        <input type="hidden" name="Cheque_No" id="Cheque_No" value="<?=$Cheque_No;?>">
        <input type="hidden" name="paid_to" id="paid_to" value="<?=$paid_to;?>">
        <input type="hidden" name="Cheque_Date" id="Cheque_Date" value="<?=$Cheque_Date;?>">
        <input type="hidden" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>">
        <table align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
            <tbody>
            <tr style="background-color: bisque">
                <th style="text-align: center">Cash & Bank Account</th>
                <th style="text-align: center">Cost Center</th>
                <th style="text-align: center">Narration</th>
                <th style="text-align: center">Attachment</th>
                <th style="width:5%; text-align:center">Amount</th>
                <th style="text-align:center">Action</th>
            </tr>
            <tbody>
            <tr>
                <td style="width: 25%; vertical-align: middle" align="center">
                    <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="-1" required="required"  name="ledger_id">
                        <option></option>
                        <?php foreign_relation('accounts_ledger', 'ledger_id', 'CONCAT(ledger_id," : ", ledger_name)',  $edit_value_ledger_id, 'ledger_group_id in ("1002") and status=1'); ?>
                    </select></td>
                <td align="center" style="width: 10%;vertical-align: middle">
                    <select class="select2_single form-control" style="width:100%" tabindex="-1"   name="cc_code" id="cc_code">
                        <option></option>
                        <?php foreign_relation('cost_center', 'id', 'CONCAT(id," : ", center_name)', $edit_value_cc_code, 'status=1'); ?>
                    </select></td>
                <td style="width:15%;vertical-align: middle" align="center">

                    <textarea id="narration" style="width:100%; height:37px; font-size: 11px; text-align:center"  name="narration" class="form-control col-md-7 col-xs-12" autocomplete="off"><?=($edit_value_narration!='')? $edit_value_narration : $contra_note_last_narration;?></textarea>

                <td style="width:10%;vertical-align: middle" align="center">
                    <input type="file" id="attachment" style="width:100%; height:37px; font-size: 11px; text-align:center"    name="attachment" class="form-control col-md-7 col-xs-12" autocomplete="off" ></td>
                <td align="center" style="width:10%"><?php if (isset($_GET['id'])) { ?>
                        <input type="number" id="dr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center"  value="<?=$edit_value_dr_amt;?>" <?php if($edit_value_dr_amt>0)  echo ''; else echo 'readonly'; ?>  name="dr_amt" placeholder="Debit" class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" />
                        <input type="number" id="cr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center; margin-top: 5px"  value="<?=$edit_value_cr_amt;?>" <?php if($edit_value_cr_amt>0)  echo ''; else echo 'readonly'; ?>  name="cr_amt" placeholder="Credit"class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" /><?php } else {  ?>
                        <input type="number" id="dr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center"  name="dr_amt" placeholder="Debit" class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" />
                        <input type="number" id="cr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center; margin-top: 5px" name="cr_amt" placeholder="Credit" class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" />
                    <?php } ?></td>

                <td align="center" style="width:5%; vertical-align: middle "><?php if (isset($_GET['id'])) : ?><button type="submit" class="btn btn-primary" name="editdata<?=$_GET['id'];?>" id="editdata<?=$_GET['id'];?>" style="font-size: 11px">Update</button><br><a href="<?=$page;?>" style="font-size: 11px"  onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you sure you want to Delete the Voucher?");' class="btn btn-danger">Cancel</a>
                    <?php else: ?><button type="submit" class="btn btn-primary" name="add" id="add" style="font-size: 11px">Add</button> <?php endif; ?></td></tr>
            </tbody>
        </table>
        <input name="count" id="count" type="hidden" value="" />
    </form>
<?=voucher_delete_edit($rs,$unique,$initiate_contra_note,$COUNT_details_data,$page);?><br><br>
<?php endif;?>
<?=$html->footer_content();mysqli_close($conn);?>