<?php require_once 'support_file.php';
$title='Inter-Company Journal Voucher';

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
$url.= $_SERVER['HTTP_HOST'];
$url.= $_SERVER['REQUEST_URI'];

$unique='voucherno';
$unique_field='voucher_date';
$table_journal_master="journal_voucher_master";
$table_journal_info="journal_info";
$journal_info_unique='journal_info_no';
$page="acc_intercompany_journal_voucher.php";
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
            $d = $_POST['voucher_date'];
            $_POST['voucher_date'] = date('Y-m-d', strtotime($d));
            if($_POST['Cheque_Date']>0){
                $_POST['Cheque_Date'] = @$_POST['Cheque_Date'];
            } else {
                $_POST['Cheque_Date']='';
            }
            $PostDrAmt = @$_POST['drAmt'];
            $PostCrAmt = @$_POST['crAmt'];

            if($PostDrAmt>0)
            {
                $_POST['amount'] = $PostDrAmt;
            } elseif ($PostCrAmt>0)
            {
                $_POST['amount'] = $PostCrAmt;
            }

            $_POST['entry_by'] = $_SESSION['userid'];
            $_POST['entry_at'] = date('Y-m-d H:s:i');

            $_POST['journal_type'] = 'Journal_info';
            $_POST['status'] = 'MANUAL';
            $_POST['party_ledger'] = $_POST['ledger_id_1'];
            if (($PostDrAmt && $PostCrAmt) > 0) {
                echo "<script>alert('Yor are trying to input an invalid transaction!!')</script>";
                echo $targeturl;
            } else {
                $crud->insert();
                $_SESSION['initiate_journal_note_inter_company'] = $_POST[$unique];
                $_SESSION['API_client_id']=find_a_field('acc_intercompany','client_id','ledger_id='.$_POST['ledger_id_1']);
                unset($_POST);
            }
        }

//for modify PS information ...........................
        if (isset($_POST['modify'])) {
            $d = $_POST['voucher_date'];
            $_POST['voucher_date'] = date('Y-m-d', strtotime($d));
            if($_POST['Cheque_Date']>0){
                $ckd = $_POST['Cheque_Date'];
                $_POST['Cheque_Date'] = $ckd;
            } else {
                $_POST['Cheque_Date']='';
            }
            $_POST['edit_at'] = time();
            $_POST['edit_by'] = $_SESSION['userid'];
            $_POST['party_ledger'] = $_POST['ledger_id_1'];

            $PostDrAmt = @$_POST['drAmt'];
            $PostCrAmt = @$_POST['crAmt'];

            if($PostDrAmt>0)
            {
                $_POST['amount'] = $PostDrAmt;
            } elseif ($PostCrAmt>0)
            {
                $_POST['amount'] = $PostCrAmt;
            }

            if (($PostDrAmt && $PostCrAmt) > 0) {
                echo "<script>alert('Yor are trying to input an invalid transaction!!')</script>";
                echo $targeturl;
            } else {
                $crud->update($unique);
                $_SESSION['API_client_id'] = find_a_field('acc_intercompany', 'client_id', 'ledger_id=' . $_POST['ledger_id_1']);
                unset($_POST);
            }
        }


//for single FG Add...........................
        if (isset($_POST['add'])) {
            $dd = $_POST['journal_info_date'];

            if(@$_POST['Cheque_Date']) {
                $c_date = @$_POST['Cheque_Date'];
            } else {
                $c_date='';
            }
            $tdates = date("Y-m-d");
            $day = date('l', strtotime($idatess));
            $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $timess = $dateTime->format("d-m-y  h:i A");
            $manual_payment_no = 0;
            $cr_amt1 = @$_POST['cr_amt1'];
            if ((($_POST['dr_amt1'] && $_POST['party_ledger']) > 0) && ($_SESSION['initiate_journal_note_inter_company']>0)) {
                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'], '', $proj_id, $_POST['internal_narration'], $_POST['party_ledger'], $_POST['dr_amt1'],
                    0, 'Debit', '', $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                        , $thisday, $thismonth, $thisyear, $_POST['crLedger_id_2']);
                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'],0, $proj_id, $_POST['internal_narration'], $_POST['crLedger_id_2'], 0,
                        $_POST['cr_amt2'], 'Credit','', $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                        , $thisday, $thismonth, $thisyear, $_POST['party_ledger']);
            }

            if ((($cr_amt1 && $_POST['party_ledger']) > 0) && ($_SESSION['initiate_journal_note_inter_company']>0)) {

                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'],0, $proj_id, $_POST['internal_narration'], $_POST['party_ledger'], 0,
                    $_POST['cr_amt1'], 'Debit','', $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                    , $thisday, $thismonth, $thisyear, $_POST['drLedger_id_2']);

                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'], '', $proj_id, $_POST['internal_narration'], $_POST['drLedger_id_2'], $_POST['dr_amt2'],
                    0, 'Credit', '', $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                    , $thisday, $thismonth, $thisyear, $_POST['party_ledger']);
            }

            if (($_POST['ex_amount']> 0) && (($_POST['Ex_debit_ledger'] && $_POST['Ex_credit_ledger']) > 0) && ($_SESSION['initiate_journal_note_inter_company']>0)) {
                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'], 1845854380, $proj_id, $_POST['internal_narration'].', '.$_POST['ex_narration'], $_POST['Ex_debit_ledger'], $_POST['ex_amount'],
                        0, 'Debit', 1, $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                        , $thisday, $thismonth, $thisyear, $_POST['Ex_credit_ledger']);
                add_to_journal_info($_SESSION['initiate_journal_note_inter_company'], 1845854380, $proj_id, $_POST['internal_narration'].', '.$_POST['ex_narration'], $_POST['Ex_credit_ledger'], 0,
                        $_POST['ex_amount'], 'Credit', 2, $_POST['paid_to'], $_POST['Cheque_No'], $c_date, $_POST['Cheque_of_bank'], $manual_payment_no, 0, 0, 'MANUAL', $ip, $_POST['journal_info_date'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], $create_date, $now, $day
                        , $thisday, $thismonth, $thisyear, $_POST['Ex_debit_ledger']);
            }
        } // add
    } // end post unique
} // end prevent_multi_submit

$initiate_journal_note_inter_company = @$_SESSION['initiate_journal_note_inter_company'];
if($initiate_journal_note_inter_company>0){
    $rs="Select 
j.id as jid,
j.journal_info_no,
j.j_date,
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
a.*,c.center_name as cname,j.day_name 
from 
".$table_journal_info." j,
accounts_ledger a,
cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.journal_info_date not in ('1845854380') and 
 j.entry_status='MANUAL' and 
 j.journal_info_no='".$initiate_journal_note_inter_company."'";
    $re_query=mysqli_query($conn, $rs);
    while($uncheckrow=mysqli_fetch_array($re_query)){
        $ids=$uncheckrow['jid'];
        if (isset($_POST['confirmsave']) && ($uncheckrow['journal_info_no']>0)) {
            add_to_journal_new($uncheckrow['j_date'], $proj_id, $jv,0, $uncheckrow['ledger_id'], $uncheckrow['narration'], $uncheckrow['dr_amt'], $uncheckrow['cr_amt'], 'Journal_info', $uncheckrow['journal_info_no'], $uncheckrow['jid'], $uncheckrow['cc_code'], $uncheckrow['sub_ledger_id'], $_SESSION['usergroup'], $uncheckrow['cheq_no'], $uncheckrow['cheq_date'], $create_date, $ip, $now, $uncheckrow['day_name'], $thisday, $thismonth, $thisyear,'','','');
        }

        if(isset($_POST['deletedata'.$ids]))
        {  mysqli_query($conn, ("DELETE FROM ".$table_journal_info." WHERE id=".$ids));
            $_SESSION['initiate_journal_note_inter_company']=$_SESSION['initiate_journal_note_inter_company'];
            unset($_POST);
        }
        if(isset($_POST['editdata'.$ids]))
        {  mysqli_query($conn, ("UPDATE ".$table_journal_info." SET ledger_id='".$_POST['ledger_id']."', pc_code='".$_POST['pc_code']."',narration='".$_POST['narration']."',dr_amt='".$_POST['dr_amt']."',cr_amt='".$_POST['cr_amt']."' WHERE id=".$ids));
            unset($_POST);
        }
    }
    if (isset($_GET['id'])) {
        $edit_value=find_all_field(''.$table_journal_info.'','','id='.$_GET['id'].'');
    }
    $edit_value_ledger_id = @$edit_value->ledger_id;
    $edit_value_pc_code = @$edit_value->pc_code;
    $edit_value_narration = @$edit_value->narration;
    $edit_value_dr_amt = @$edit_value->dr_amt;
    $API_client_id = @$_SESSION['API_client_id'];
    $find_API_journal_voucher=find_all_field('dev_API_received','','API_name="API_journal_voucher" and status=1 and client_id='.$API_client_id);
    $find_API_journal_voucher_API_endpoint = @$find_API_journal_voucher->API_endpoint;
    $initiate_journal_note_inter_company = @$_SESSION['initiate_journal_note_inter_company'];
    if (isset($_POST['confirmsave'])) {
        $up_master=mysqli_query($conn, "UPDATE ".$table_journal_info." SET entry_status='UNCHECKED' where ".$journal_info_unique."=".$_SESSION['initiate_journal_note_inter_company']."");
        $up_master=mysqli_query($conn, "UPDATE journal SET status='UNCHECKED' where jv_no=".$jv);
        $up_master=mysqli_query($conn, "UPDATE ".$table_journal_master." SET entry_status='UNCHECKED' where ".$unique."=".$_SESSION['initiate_journal_note_inter_company']."");
        $up_query=mysqli_query($conn, $up_master);
        $external_dr_voucher_data=find_all_field(''.$table_journal_info.'','','journal_info_date in ("1845854380") and cur_bal=1 and journal_info_no='.$initiate_journal_note_inter_company);
        $external_dr_voucher_data_2=find_all_field(''.$table_journal_info.'','','journal_info_date in ("1845854380") and cur_bal=3 and journal_info_no='.$initiate_journal_note_inter_company);
        $external_cr_voucher_data=find_all_field(''.$table_journal_info.'','','journal_info_date in ("1845854380") and cur_bal=2 and journal_info_no='.$initiate_journal_note_inter_company);
        $external_cr_voucher_data_2=find_all_field(''.$table_journal_info.'','','journal_info_date in ("1845854380") and cur_bal=4 and journal_info_no='.$initiate_journal_note_inter_company);
        $targeturl=$find_API_journal_voucher_API_endpoint.'?jv_ref='.$initiate_journal_note_inter_company.'&create_order=1&ledger_id_dr_2='.$external_dr_voucher_data_2->ledger_id.'&ledger_id_cr_2='.$external_cr_voucher_data_2->ledger_id.'&dr_amt_2='.$external_dr_voucher_data_2->dr_amt.'&cr_amt_2='.$external_cr_voucher_data_2->cr_amt.'&dr_amt='.$external_dr_voucher_data->dr_amt.'&cr_amt='.$external_cr_voucher_data->cr_amt.'&j_date='.$external_dr_voucher_data->j_date.'&narration='.$external_dr_voucher_data->narration.'&ledger_id_dr='.$external_dr_voucher_data->ledger_id.'&ledger_id_cr='.$external_cr_voucher_data->ledger_id.'&entry_by='.$_SESSION['userid'].'&sectionid='.$_SESSION['sectionid'].'&companyid='.$_SESSION['companyid'].'&return_back_URL='.$url.'';
        unset($_SESSION['initiate_journal_note_inter_company']);
        unset($_POST);
        unset($$unique);
        unset($_SESSION['API_client_id']);
        header("Location: ".$targeturl."");
    }




//for Delete..................................
    if (isset($_POST['cancel'])) {
        $crud = new crud($table_journal_info);
        $condition =$journal_info_unique."=".$_SESSION['initiate_journal_note_inter_company'];
        $crud->delete_all($condition);
        $crud = new crud($table_journal_master);
        $condition=$unique."=".$_SESSION['initiate_journal_note_inter_company'];
        $crud->delete($condition);
        unset($_SESSION['initiate_journal_note_inter_company']);
        unset($_POST);
        unset($$unique);
        unset($_SESSION['API_client_id']);
        header("Location: ".$page."");
    }

    $COUNT_details_data=find_a_field(''.$table_journal_info.'','Count(id)',''.$journal_info_unique.'='.$initiate_journal_note_inter_company.' and journal_info_date not in ("1845854380")');

// data query..................................
    $condition=$unique."=".$initiate_journal_note_inter_company;
    $data=db_fetch_object($table_journal_master,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}
    $inputted_amount=find_a_field('journal_info','SUM(dr_amt)','journal_info_no="'.$initiate_journal_note_inter_company.'"');
}
$drAmt = @$drAmt;
$crAmt = @$crAmt;
$voucher_date = @$voucher_date;
$date = date('Y-m-d');
$paid_to = @$paid_to;
$Cheque_of_bank = @$Cheque_of_bank;
$Cheque_No = @$Cheque_No;
$Cheque_Date = @$Cheque_Date;
$amount = @$amount;
$party_ledger = @$party_ledger;
$initiate_journal_note_inter_company = @$_SESSION['initiate_journal_note_inter_company'];

$sql2="select a.tr_no, a.jvdate as Date,a.jv_no as Voucher_No,SUM(a.dr_amt) as amount
from  journal a where a.tr_from='Journal_info' and a.user_id='".$_SESSION['userid']."' and a.section_id='".$_SESSION['sectionid']."' and a.company_id='".$_SESSION['companyid']."'  group by a.tr_no  order by a.id desc limit 10";

$rs = "Select 
j.id as jid,
concat(a.ledger_id, ' : ' ,a.ledger_name) as Account_Head,
c.center_name as 'Profit_Center',
j.narration,
    j.dr_amt,
    j.cr_amt 
from 
".$table_journal_info." j,
accounts_ledger a,
cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.entry_status='MANUAL' and 
 j.journal_info_date not in ('1845854380') and 
 j.journal_info_no='" . $initiate_journal_note_inter_company . "'";

$intercompany_ledger="Select a.ledger_id,concat(i.client_id, ' : ' ,a.ledger_name) as ledger_name from accounts_ledger a,acc_intercompany i where a.ledger_id=i.ledger_id";
$account_ledger="SELECT ledger_id, concat(ledger_id, ' : ', ledger_name) as ledger_name from accounts_ledger where status='1' and show_in_transaction='1'";

$dealer_info="Select a.ledger_id,a.ledger_name from accounts_ledger a,dealer_info d where a.ledger_id=d.account_code and d.canceled in ('Yes')";

$delete_commend = @$_GET['delete_commend'];


if($delete_commend==1) {
    $delete_external_receipt=mysqli_query($conn, "delete from ".$table_journal_info." where journal_info_date='1845854380'");
    $narra = find_a_field('journal_info','narration','journal_info_no='.$_GET['jv_ref']);
    $update_narration = $narra.', Ref. JV No # '.$_GET['sjn'];
    $update = mysqli_query($conn, "UPDATE journal_info SET narration='".$update_narration."' where journal_info_no=".$_GET['jv_ref']);
    $update = mysqli_query($conn, "UPDATE journal SET narration='".$update_narration."' where tr_no=".$_GET['jv_ref']);
    header("Location: ".$page."");
}
$API_client_id = @$_SESSION['API_client_id'];
$find_API_all_active_ledger=find_all_field('dev_API_received','','API_name="API_all_active_ledger" and status=1 and client_id='.$API_client_id);
$find_API_intercompany_ledger=find_all_field('dev_API_received','','API_name="API_intercompany_ledger" and status=1 and client_id='.$API_client_id);
$find_API_customer_list=find_all_field('dev_API_received','','API_name="API_customer_list" and status=1 and client_id='.$API_client_id);
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
            <h2><?=$title;?></h2>
            <a style="float: right" class="btn btn-sm btn-default"  href="acc_journal_voucher.php">
                <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000; font-size: 11px">Journal Entry</span>
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?=$page;?>" enctype="multipart/form-data" method="post" name="addem" id="addem" style="font-size: 11px" >
                <table align="center" style="width:100%">
                    <tr>
                        <th style="width:15%;">Transaction Date <span class="required text-danger">*</span></th><th style="width: 2%;">:</th>
                        <td><input type="date" id="voucher_date"  required="required" name="voucher_date" value="<?=($voucher_date!='')? $voucher_date : date('Y-m-d') ?>" max="<?=date('Y-m-d');?>" min="<?=date('Y-m-d', strtotime($date .' -'.find_a_field('acc_voucher_config','back_date_limit','1'). 'day'));?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle" ></td>
                        <th style="width:15%;">Transaction No <span class="required text-danger">*</span></th><th style="width: 2%">:</th>
                        <td><input type="text" required="required" name="<?=$unique?>" id="<?=$unique?>"  value="<?php if($initiate_journal_note_inter_company>0){ echo $initiate_journal_note_inter_company;} else { echo
                            automatic_voucher_number_generate($table_journal_info,$journal_info_unique,1,3); } ?>" class="form-control col-md-7 col-xs-12" readonly style="width: 99%; font-size: 11px;"></td>
                    </tr>
                    <tr>
                        <th style="">Person From</th><th>:</th>
                        <td><input type="text" id="paid_to"  value="<?=$paid_to;?>" name="paid_to" class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>
                        <th>Of Bank</th><th>:</th>
                        <td><input type="text" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>" class="form-control col-md-7 col-xs-12" style="width: 99%; margin-top: 5px; font-size: 11px;"></td>
                    </tr>
                    <tr>
                        <th style="">Cheque No</th><th>:</th>
                        <td><input type="text" id="Cheque_No"  value="<?=$Cheque_No;?>" name="Cheque_No"  class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>
                        <th>Cheque Date</th><th>:</th>
                        <td><input type="date" id="Cheque_Date"   value="<?=$Cheque_Date;?>" name="Cheque_Date"  class="form-control col-md-7 col-xs-12"  style="width: 99%; margin-top: 5px; font-size: 11px; vertical-align: middle"></td>
                    </tr>
                    <tr>
                        <th style="">Inter-company <span class="required text-danger">*</span></th><th>:</th>
                        <td colspan="3" style="padding-top: 5px;">
                            <select class="select2_single form-control" style="width:96%; font-size: 11px" tabindex="-1" required="required"  name="ledger_id_1" id="ledger_id_1">
                                <option></option>
                                <?=advance_foreign_relation($intercompany_ledger,$party_ledger);?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="drAmt" <?php if ($drAmt>0) {?> value="<?=$drAmt;?>" <?php } ?> class="form-control col-md-7 col-xs-12" style="width: 49%; margin-top: 5px; float: left; font-size: 11px;" placeholder="Debit Amt">
                            <input type="number" name="crAmt" <?php if ($crAmt>0) {?> value="<?=$crAmt;?>" <?php } ?> class="form-control col-md-7 col-xs-12" style="width: 49%; margin-top: 5px; float: right; font-size: 11px;" placeholder="Credit Amt">
                        </td>
                    </tr>
                </table>

                <?php if($initiate_journal_note_inter_company){
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
                            <button type="submit" name="modify" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Update?");' style="font-size: 11px">Update Journal Voucher</button>
                        </div>
                    </div>

                    <div class="form-group" <?=$display;?>>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <a  href="voucher_print_preview.php?v_type=Receipt&vo_no=<?=$initiate_journal_note_inter_company;?>&v_date=<?=$voucher_date;?>" target="_blank" style="color: blue; text-decoration: underline; font-size: 11px; font-weight: bold; vertical-align: middle">View Receipt Voucher</a>
                        </div>
                    </div>
                <?php   } else {?>
                    <div class="form-group" style="margin-left:40%; margin-top: 15px">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" name="initiate" class="btn btn-primary" style="font-size: 11px">Initiate Journal Voucher</button>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>


<?=recentvoucherview($sql2,'voucher_view_popup_ismail.php','Journal_info','213px','');?>
<?php if($initiate_journal_note_inter_company):  ?>
    <form action="<?=$page;?>" enctype="multipart/form-data" name="addem" id="addem" style="font-size: 11px" class="form-horizontal form-label-left" method="post">
        <input type="hidden" name="journal_info_no" id="journal_info_no" value="<?=$initiate_journal_note_inter_company;?>">
        <input type="hidden" name="<?=$unique?>" id="<?=$unique?>"  value="<?=$initiate_journal_note_inter_company;?>">
        <input type="hidden" name="journal_info_date" id="journal_info_date" value="<?=$voucher_date;?>">
        <input type="hidden"  <?php if($drAmt>0){ ?>name="dr_amt1"<?php } elseif($crAmt>0) { ?> name="cr_amt1" <?php } ?> value="<?=$amount;?>">
        <input type="hidden"  name="party_ledger" value="<?=$party_ledger;?>">
        <input type="hidden" name="Cheque_No" id="Cheque_No" value="<?=$Cheque_No;?>">
        <input type="hidden" name="paid_to" id="paid_to" value="<?=$paid_to;?>">
        <input type="hidden" name="Cheque_No" id="Cheque_No" value="<?=$Cheque_No;?>">
        <?php if($Cheque_Date>0){ ?>
            <input type="hidden" name="Cheque_Date" id="Cheque_Date" value="<?=$Cheque_Date;?>">
        <?php } ?>
        <input type="hidden" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>">
        <table align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
            <tbody>
            <tr class="info">
                <th style="text-align: center; width: 10%">For</th>
                <th style="text-align: center">Ledger</th>
                <th style="text-align: center; width: 25%">Narration</th>
                <th style="width:10%; text-align:center;">Amount</th>
                <th style="text-align:center; width: 5%">Action</th>
            </tr>
            <tbody>
            <tr class="success">
                <th style="vertical-align: middle; text-align: center">Internal Journal</th>
                <td style="vertical-align: middle;">
                    <?php if($drAmt>0){ ?><span class="text-danger">Cr - </span><?php } elseif($crAmt>0) { ?><span class="text-danger">Dr - </span><?php } ?>
                    <select class="select2_single form-control" style="width:96%; font-size: 11px" tabindex="-1" required="required" <?php if($drAmt>0){ ?> name="crLedger_id_2" <?php } elseif($crAmt>0) { ?> name="drLedger_id_2" <?php } ?>>
                        <option></option>
                        <?=advance_foreign_relation($account_ledger,'');?>
                    </select>
                </td>
                <td style="width:15%;vertical-align: middle">
                    <textarea style="width:100%; font-size: 11px; text-align:center"  name="internal_narration"  class="form-control col-md-7 col-xs-12" autocomplete="off" ></textarea>
                </td>
                <td style="width:10%; vertical-align: middle;">
                    <input type="number"  style="width:99%; font-size: 11px; text-align:center"  value="<?=$edit_value_dr_amt;?>"  <?php if($drAmt>0){ ?>name="cr_amt2" <?php } elseif($crAmt>0) { ?> name="dr_amt2" <?php } ?> class="form-control col-md-7 col-xs-12" required placeholder="<?php if($drAmt>0){ ?>credit amount<?php } elseif($crAmt>0) { ?>debit amount<?php } ?>" autocomplete="off" step="any" min="1" />
                </td>
                <td rowspan="3" style="width:5%; vertical-align: middle ">
                    <?php if (isset($_GET['id'])) : ?><button type="submit" class="btn btn-primary" name="editdata<?=$_GET['id'];?>" id="editdata<?=$_GET['id'];?>" style="font-size: 11px">Update</button><br><a href="<?=$page;?>" style="font-size: 11px"  onclick='return window.confirm("Mr. <?=$_SESSION["username"]; ?>, Are you sure you want to Delete the Voucher?");' class="btn btn-danger">Cancel</a>
                    <?php else: ?><button type="submit" class="btn btn-primary" name="add" id="add" style="font-size: 11px">Add</button> <?php endif; ?>
                </td>
            </tr>
            <tr class="danger">
                <th style="vertical-align: middle; text-align: center">External Journal<br>(vai API)</th>
                <td rowspan="2" style="vertical-align: middle;">
                    <span class="text-danger">Dr</span> - <select class="select2_single form-control" style="width:96%" tabindex="-1" required name="Ex_debit_ledger">
                        <option></option>
                        <?php if($crAmt > 0) { ?>
                            <?php
                            $characters = json_decode(file_get_contents($find_API_intercompany_ledger->API_endpoint)); // decode the JSON feed
                            foreach ($characters as $character) :;?>
                                <option value="<?=$character->ledger_id;?>"><?=$character->ledger_name;?></option>
                            <?php endforeach;  ?>
                        <?php } else { ?>
                            <?php $characters = json_decode(file_get_contents($find_API_all_active_ledger->API_endpoint)); // decode the JSON feed
                            foreach ($characters as $character) :;?>
                                <option value="<?=$character->ledger_id;?>"><?=$character->ledger_name;?></option>
                            <?php endforeach;  ?>
                        <?php } ?>
                    </select>
                    <br><br>
                    <span class="text-danger">Cr</span> -
                    <select class="select2_single form-control" style="width:96%" tabindex="-1" required  name="Ex_credit_ledger">
                        <option></option>
                        <?php if($crAmt > 0) { ?>
                            <?php $characters = json_decode(file_get_contents($find_API_all_active_ledger->API_endpoint)); // decode the JSON feed
                            foreach ($characters as $character) :;?>
                                <option value="<?=$character->ledger_id;?>"><?=$character->ledger_name;?></option>
                            <?php endforeach;  ?>
                        <?php } else { ?>
                            <?php
                            $characters = json_decode(file_get_contents($find_API_intercompany_ledger->API_endpoint)); // decode the JSON feed
                            foreach ($characters as $character) :;?>
                                <option value="<?=$character->ledger_id;?>"><?=$character->ledger_name;?></option>
                            <?php endforeach;  ?>
                        <?php } ?>
                    </select>
                </td>
                <td style="width:15%;vertical-align: middle">
                    <textarea  style="width:100%; font-size: 11px; text-align:center"  name="ex_narration"  class="form-control col-md-7 col-xs-12" autocomplete="off" ><?=($edit_value_narration!='')? $edit_value_narration : 'External Journal Entry from '.$_SESSION['company_name'].' Software, Ref: '.$initiate_journal_note_inter_company.',';?></textarea>
                </td>
                <td style="width:10%; vertical-align: middle;" class="bg-danger">
                    <input type="number"  required style="width:99%; font-size: 11px; text-align:center"  value="<?=$edit_value_dr_amt;?>"  name="ex_amount" class="form-control col-md-7 col-xs-12" autocomplete="off" placeholder="amount" step="any" min="1" />
                </td>
            </tr>
            </tbody>
        </table>
        <SCRIPT language=JavaScript>
            function doAlert(form)
            {
                var val=form.dr_amt.value;
                var val2=form.rcved_remining.value;
                if (Number(val)>Number(val2)){
                    alert('oops!! Exceed Received Limit!! Thanks');
                    form.dr_amt.value='';
                }
                form.dr_amt.focus();
            }</script>
    </form>
    <?=voucher_delete_edit($rs,$unique,$_SESSION['initiate_journal_note_inter_company'],$COUNT_details_data,$page);?><br><br>
<?php endif; mysqli_close($conn); ?>
<?=$html->footer_content();?> 