<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Journal Voucher';
$sectionid = @$_SESSION['sectionid'];
$sectionid_substr = @(substr($_SESSION['sectionid'],4));
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
    $sec_com_connection_wa=' and 1';
} else {
    $sec_com_connection=" and j.company_id='".$_SESSION['companyid']."' and j.section_id in ('400000','".$_SESSION['sectionid']."')";
    $sec_com_connection_wa=" and company_id='".$_SESSION['companyid']."' and section_id in ('400000','".$_SESSION['sectionid']."')";
}
$unique='voucherno';
$unique_field='voucher_date';
$table_journal_master="journal_voucher_master";
$table_journal_info="journal_info";
$journal_info_unique='journal_info_no';
$page="acc_journal_voucher.php";
$crud      =new crud($table_journal_master);
$checkFileUploadMetaAccess = find_a_field('dev_usage_control_metas','meta_value','meta_key="file_upload_access_on_voucher_entry" and status="active" and section_id="'.$_SESSION['sectionid'].'" and company_id='.$_SESSION['companyid']);

//Image Attachment Function
function image_upload_on_id($path,$file,$id='')
{    if($file['name']!=''){
    $path_file = $path.basename($file['name']);
    $imageFileType = pathinfo($path_file,PATHINFO_EXTENSION);
    $root=$path.'/'.$id.'.'.$imageFileType;
    if($imageFileType != "jpg" && $imageFileType != "pdf" )
    {}
    else
        move_uploaded_file($file['tmp_name'],$root);
    return $root;
}}
//Image Attachment Function
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
            if(!empty($_POST['Cheque_Date'])){
                $ckd = $_POST['Cheque_Date'];
                $_POST['Cheque_Date'] = $ckd;
            } else {
                $_POST['Cheque_Date']='';
            }
            $_POST['entry_by'] = $_SESSION['userid'];
            $_POST['entry_at'] = date('Y-m-d H:s:i');
            $_SESSION['initiate_journal_note'] = $_POST[$unique];
            $_POST['journal_type'] = 'Journal_info';
            $_POST['entry_by'] = 'MANUAL';
            $crud->insert();
            unset($_POST);
        }

//for modify PS information ...........................
        if (isset($_POST['modify'])) {
            $d = $_POST['voucher_date'];
            $_POST['voucher_date'] = date('Y-m-d', strtotime($d));
            if(!empty($_POST['Cheque_Date'])){
                $ckd = $_POST['Cheque_Date'];
                $_POST['Cheque_Date'] = $ckd;
            } else {
                $_POST['Cheque_Date']='';
            }
            $_POST['edit_at'] = time();
            $_POST['edit_by'] = $_SESSION['userid'];
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
            $date = $_POST['receipt_date'];
            $_POST_Cheque_Date = @$_POST['Cheque_Date'];
            if(!empty($_POST_Cheque_Date)){
                $c_date = $_POST_Cheque_Date;
            } else {
                $c_date='';
            }

            $tdates = date("Y-m-d");
            $day = date('l', strtotime($tdates));
            $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $timess = $dateTime->format("d-m-y  h:i A");

            $POST_dr_amt = @$_POST['dr_amt'];
            $POST_cr_amt = @$_POST['cr_amt'];
            $cur_bal = 0;
            $manual_payment_no = 0;
            $cc_code = @$_POST['cc_code'];
            $subledger_id = @$_POST['subledger_id'];
            $receive_ledger = 0;

            if (($_POST['dr_amt'] && $_POST['cr_amt']) > 0) {
                echo "<script>alert('Yor are trying to input an invalid transaction!!')</script>";
            } else {
                if ((($POST_dr_amt || $POST_cr_amt) > 0) && ($_SESSION['initiate_journal_note']>0)) {
                    add_to_journal_info($_SESSION['initiate_journal_note'],$date, $proj_id, $_POST['narration'], $_POST['ledger_id'], $POST_dr_amt,
                        $POST_cr_amt, $type,$cur_bal,$_POST['paid_to'],$_POST['Cheque_No'],$c_date,$_POST['Cheque_of_bank'],$manual_payment_no,$cc_code,$subledger_id,'MANUAL',$ip,$_POST['receipt_date'],$_SESSION['sectionid'],$_SESSION['companyid'],$_SESSION['userid'],$create_date,$now,$day
                        ,$thisday,$thismonth,$thisyear,$receive_ledger);
                    $_SESSION['journal_last_narration']=$_POST['narration'];
                }
                if ($_FILES["attachment"]["tmp_name"] != '') {
                    $path = '../assets/images/attachment/vouchers/journal/' . $_SESSION['initiate_journal_note'] . '.jpg';
                    move_uploaded_file($_FILES["attachment"]["tmp_name"], $path);
                }
            }
        } // post add

        if(isset($_POST["Import"])){
            $date = $_POST['receipt_date'];
            $filename=$_FILES["file"]["tmp_name"];
            if($_FILES["file"]["size"] > 0)
            { $file = fopen($filename, "r");
                while (($eData = fgetcsv($file, 10000, ",")) !== FALSE)
                {
                    $entry_at = date('Y-m-d H:i:s');
                    $sql = "INSERT INTO ".$table_journal_info."
   (`journal_info_no`,`j_date`,`proj_id`,`narration`,`ledger_id`,`dr_amt`,`cr_amt`,`type`,`cur_bal`,`received_from`,`cheq_no`,`cheq_date`,`bank`,`manual_journal_info_no`,`cc_code`,`sub_ledger_id`,`entry_status`,`ip`,`section_id`,`company_id`,`entry_by`,`create_date`)
	         VALUES ('".$_SESSION['initiate_journal_note']."','".$date."','".$proj_id."','".$eData[1]."','".$eData[0]."','".$eData[2]."','$eData[3]','".$eData[4]."','0','0','0','0','','','$eData[5]','','MANUAL','".$ip."','".$_SESSION['sectionid']."','".$_SESSION['companyid']."','".$_SESSION['userid']."','".date('Y-m-d')."')";
                    $result = mysqli_query( $conn, $sql);
                    if(! $result )
                    {
                        echo "<script type=\"text/javascript\">
							alert(\"Invalid File:Please Upload CSV File.\");
							window.location = ".$page."
						</script>";
                    }}
                fclose($file);
                echo "<script type=\"text/javascript\">
						alert(\"CSV File has been successfully Imported.\");
						window.location = ".$page."
					</script>";
            }

            header("Location: ".$page."");
        }

    } // end post unique
} // end prevent_multi_submit

$initiate_journal_note = @$_SESSION['initiate_journal_note'];
$journal_last_narration = @$_SESSION['journal_last_narration'];

if($initiate_journal_note>0){
    $rs="Select 
j.id as jid,
j.journal_info_no,
j.j_date,
j.journal_info_date,
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
journal_info j,
 accounts_ledger a,cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.entry_status='MANUAL' and 
 j.journal_info_no='".$initiate_journal_note."'".$sec_com_connection." order by j.id asc ";
    $re_query=mysqli_query($conn, $rs);
    while($uncheckrow=mysqli_fetch_array($re_query)){
        $ids=$uncheckrow['jid'];
        if (isset($_POST['confirmsave']) && ($uncheckrow['journal_info_no']>0)) {
            add_to_journal_new($uncheckrow['j_date'],$proj_id, $jv, $uncheckrow['journal_info_date'], $uncheckrow['ledger_id'], $uncheckrow['narration'], $uncheckrow['dr_amt'], $uncheckrow['cr_amt'],'Journal_info',$uncheckrow['journal_info_no'],$uncheckrow['jid'],$uncheckrow['cc_code'],$uncheckrow['sub_ledger_id'],$_SESSION['usergroup'],$uncheckrow['cheq_no'],$uncheckrow['cheq_date'],$create_date,$ip,$now,$uncheckrow['day_name'],$thisday,$thismonth,$thisyear,'','','');
        }
        if(isset($_POST['deletedata'.$ids]))
        {  mysqli_query($conn, ("DELETE FROM ".$table_journal_info." WHERE id='".$ids."'".$sec_com_connection_wa.""));
            unset($_POST);
        }
        if(isset($_POST['editdata'.$ids]))
        {  mysqli_query($conn, ("UPDATE ".$table_journal_info." SET ledger_id='".$_POST['ledger_id']."', cc_code='".$_POST['cc_code']."',narration='".$_POST['narration']."',dr_amt='".$_POST['dr_amt']."',cr_amt='".$_POST['cr_amt']."' WHERE id=".$ids."".$sec_com_connection_wa.""));
            unset($_POST);
        }
    }

    if (isset($_POST['confirmsave'])) {
        $up_journal="UPDATE ".$table_journal_info." SET entry_status='UNCHECKED' where ".$journal_info_unique."=".$_SESSION['initiate_journal_note']."".$sec_com_connection_wa."";
        $up_query=mysqli_query($conn, $up_journal);
        $up_master=mysqli_query($conn, "UPDATE journal SET status='UNCHECKED' where jv_no=".$jv."".$sec_com_connection_wa."");
        $up_master=mysqli_query($conn, "UPDATE ".$table_journal_master." SET entry_status='UNCHECKED' where ".$unique."=".$_SESSION['initiate_journal_note']."".$sec_com_connection_wa."");
        unset($_SESSION['initiate_journal_note']);
        unset($_SESSION['journal_last_narration']);
        unset($_POST);
        unset($$unique);
    }


    if (isset($_GET['id'])) {
        $edit_value=find_all_field("".$table_journal_info."","","id=".$_GET['id']."".$sec_com_connection_wa."");
    }
    $edit_value_ledger_id = @$edit_value->ledger_id;
    $edit_value_cc_code = @$edit_value->cc_code;
    $edit_value_narration = @$edit_value->narration;
    $initiate_journal_note = @$_SESSION['initiate_journal_note'];
    $COUNT_details_data=find_a_field("".$table_journal_info."","Count(id)","".$journal_info_unique."=".$initiate_journal_note."".$sec_com_connection_wa."");

//for Delete..................................
    if (isset($_POST['cancel'])) {
        $crud = new crud($table_journal_info);
        $condition =$journal_info_unique."=".$initiate_journal_note;
        $crud->delete_all($condition);
        $crud = new crud($table_journal_master);
        $condition=$unique."=".$initiate_journal_note;
        $crud->delete($condition);
        unset($initiate_journal_note);
        unset($_SESSION['initiate_journal_note']);
        unset($_SESSION['journal_last_narration']);
        unset($_POST);
        unset($$unique);
    }
    $initiate_journal_note = @$_SESSION['initiate_journal_note'];
// data query..................................
    $condition=$unique."=".$initiate_journal_note;
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

$sql2="select a.tr_no, a.jvdate as Date,a.jv_no as Voucher_No,SUM(a.dr_amt) as amount
from  journal a where a.tr_from='journal_info' and a.user_id=".$_SESSION['userid']." and a.section_id=".$_SESSION['sectionid']." and a.company_id=".$_SESSION['companyid']."  group by a.tr_no  order by a.id desc limit 10";
$rs="Select 
j.id as jid,
concat(a.ledger_id, ' : ' ,a.ledger_name) as Account_Head,c.center_name,j.narration,j.dr_amt,j.cr_amt
from 
journal_info j,
 accounts_ledger a,cost_center c
  where 
 j.ledger_id=a.ledger_id and 
 j.cc_code=c.id and
 j.entry_status='MANUAL' and 
 j.journal_info_no='".$initiate_journal_note."'".$sec_com_connection." group by j.id
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
                <h2><?php echo $title; ?> <small class="text-danger">field marked with * are mandatory</small></h2>
                <a style="float: right" class="btn btn-sm btn-default"  href="acc_intercompany_journal_voucher.php">
                    <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000; font-size: 11px">Intercompany Entry</span>
                </a>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form action="<?=$page;?>" enctype="multipart/form-data" method="post" name="addem" id="addem" style="font-size: 11px" ><table align="center" style="width:100%">
                        <tr>
                            <th style="width:15%;">Transaction Date <span class="required text-danger">*</span></th><th style="width: 2%;">:</th>
                            <td><input type="date" id="voucher_date" tabindex="1" required="required" name="voucher_date" value="<?=($voucher_date!='')? $voucher_date : date('Y-m-d') ?>" max="<?=date('Y-m-d');?>" min="<?=date('Y-m-d', strtotime($date .' -'.find_a_field('acc_voucher_config_log','back_date_limit','status="Active"'). 'day'));?>" class="form-control col-md-7 col-xs-12" style="width: 90%; font-size: 11px;vertical-align:middle" ></td>

                            <th style="width:15%;">Transaction No <span class="required text-danger">*</span></th><th style="width: 2%">:</th>
                            <td><input type="text" required="required" readonly tabindex="2" name="<?=$unique?>" id="<?=$unique?>"  value="<?=($initiate_journal_note!='')? $initiate_journal_note : automatic_voucher_number_generate($table_journal_info,$journal_info_unique,1,'3'.$sectionid_substr); ?>" class="form-control col-md-7 col-xs-12"  style="width: 90%; font-size: 11px;"></td>
                        </tr>
                        <tr>
                            <th style="">Person</th><th>:</th>
                            <td><input type="text" id="paid_to" tabindex="3"  value="<?=$paid_to;?>" name="paid_to" class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>

                            <th>Of Bank</th><th>:</th>
                            <td><input type="text" name="Cheque_of_bank" tabindex="4" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>" class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;"></td>
                        </tr>
                        <tr>
                            <th style="">Cheque No</th><th>:</th>
                            <td><input type="text" id="Cheque_No" tabindex="5" value="<?=$Cheque_No;?>" name="Cheque_No"  class="form-control col-md-7 col-xs-12" style="width: 90%; margin-top: 5px; font-size: 11px;" ></td>

                            <th>Cheque Date</th><th>:</th>
                            <td><input type="date" id="Cheque_Date" tabindex="6" value="<?=$Cheque_Date;?>" name="Cheque_Date"  class="form-control col-md-7 col-xs-12"  style="width: 90%; margin-top: 5px; font-size: 11px; vertical-align: middle"></td>
                        </tr>
                    </table>

                    <?php if($initiate_journal_note){
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
                                <button type="submit" name="modify" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Update?");' style="font-size: 11px" tabindex="7"><i class="fa fa-edit"></i> Update Journal Voucher</button>
                            </div>
                        </div>

                        <div class="form-group" <?=$display;?>>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <a  href="voucher_print_preview.php?v_type=journal_info&vo_no=<?=$initiate_journal_note;?>&v_date=<?=$voucher_date;?>" target="_blank" style="color: blue; text-decoration: underline; font-size: 11px; font-weight: bold; vertical-align: middle" tabindex="8">View Journal Voucher</a>
                            </div>
                        </div>

                    <?php   } else {?>

                        <div class="form-group" style="margin-left:40%; margin-top: 15px">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button type="submit" name="initiate" class="btn btn-primary" style="font-size: 11px" tabindex="7"><i class="fa fa-plus"></i> Initiate Journal Voucher</button>
                            </div></div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

<?=recentvoucherview($sql2,'voucher_view_popup_ismail.php','journal_info','166px','');?>
<?php if($initiate_journal_note):  ?>


        <?php if ($checkFileUploadMetaAccess>0){ ?>
        <form action="<?=$page;?>" enctype="multipart/form-data" name="addem" id="addem" style="font-size: 11px" class="form-horizontal form-label-left" method="post">
            <input type="hidden" name="payment_no" id="payment_no" value="<?=$initiate_journal_note;?>">
            <input type="hidden" name="receipt_date" id="receipt_date" value="<?=$voucher_date;?>">
            <input type="hidden" name="<?=$unique?>" id="<?=$unique?>"  value="<?=$initiate_journal_note;?>">
            <input type="hidden" name="Cheque_No" id="Cheque_No" value="<?=$Cheque_No;?>">
            <input type="hidden" name="paid_to" id="paid_to" value="<?=$paid_to;?>">
            <?php if($Cheque_Date>0){ ?>
                <input type="hidden" name="Cheque_Date" id="Cheque_Date" value="<?=$Cheque_Date;?>">
            <?php } ?>
            <input type="hidden" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>">
            <table align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                <thead>
                <tr class="bg-primary text-white">
                    <th style="text-align: center">Accounts Ledger</th>
                    <th style="text-align: center">Cost Center</th>
                    <th style="text-align: center">Narration</th>
                    <th style="text-align: center">Attachment</th>
                    <th style="width:5%; text-align:center">Amount</th>
                    <th style="text-align:center">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td align="center" colspan="5">
                        <input style="font-size:11px" type="file" id="file" name="file" required class="form-control col-md-7 col-xs-12" >
                    </td>
                    <td align="center" style="width:5%; vertical-align:middle">
                        <button type="submit" name="Import" onclick='return window.confirm("Are you confirm to Upload?");' class="btn btn-primary" style="font-size: 11px"><i class="fa fa-upload"></i> Upload the File</button>
                    </td>
                </tr>
                <tr><th colspan="6" style="text-align: center">or</th></tr>
                </tbody>
            </table>
        </form>



            <?php } ?>
        <form action="<?=$page;?>" enctype="multipart/form-data" name="addem" id="addem" style="font-size: 11px" class="form-horizontal form-label-left" method="post">
            <input type="hidden" name="payment_no" id="payment_no" value="<?=$initiate_journal_note;?>">
            <input type="hidden" name="receipt_date" id="receipt_date" value="<?=$voucher_date;?>">
            <input type="hidden" name="<?=$unique?>" id="<?=$unique?>"  value="<?=$initiate_journal_note;?>">
            <input type="hidden" name="Cheque_No" id="Cheque_No" value="<?=$Cheque_No;?>">
            <input type="hidden" name="paid_to" id="paid_to" value="<?=$paid_to;?>">
            <?php if($Cheque_Date>0){ ?>
                <input type="hidden" name="Cheque_Date" id="Cheque_Date" value="<?=$Cheque_Date;?>">
            <?php } ?>
            <input type="hidden" name="Cheque_of_bank" id="Cheque_of_bank" value="<?=$Cheque_of_bank;?>">
            <table align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                <thead>
                <tr class="bg-primary text-white">
                    <th style="text-align: center">Accounts Ledger</th>
                    <th style="text-align: center">Cost Center</th>
                    <th style="text-align: center">Narration</th>
                    <th style="text-align: center">Attachment</th>
                    <th style="width:5%; text-align:center">Amount</th>
                    <th style="text-align:center">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="width: 25%; vertical-align: middle" align="center">
                        <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="9" required="required"  name="ledger_id">
                            <option></option>
                            <?php foreign_relation("accounts_ledger", "ledger_id", "CONCAT(ledger_id,' : ', ledger_name)",  $edit_value_ledger_id, "show_in_transaction=1 and status=1".$sec_com_connection_wa.""); ?>
                        </select>
                    </td>
                    <td align="center" style="width: 10%;vertical-align: middle">
                        <select class="select2_single form-control" style="width:100%" tabindex="10"   name="cc_code" id="cc_code">
                            <option></option>
                            <?php foreign_relation("cost_center", "id", "CONCAT(id,' : ', center_name)", $edit_value_cc_code, "status=1".$sec_com_connection_wa.""); ?>
                        </select>
                    </td>
                    <td style="width:15%;vertical-align: middle" align="center">
                        <textarea id="narration" style="width:100%; height:37px; font-size: 11px; text-align:center" tabindex="11" name="narration" class="form-control col-md-7 col-xs-12" autocomplete="off"><?=($edit_value_narration!='')? $edit_value_narration : $journal_last_narration;?></textarea>
                    </td>
                    <td style="width:10%;vertical-align: middle" align="center">
                        <input type="file" id="attachment" style="width:100%; height:37px; font-size: 11px; text-align:center" tabindex="12" name="attachment" class="form-control col-md-7 col-xs-12" autocomplete="off" ></td>
                    <td align="center" style="width:10%">
                        <?php if (isset($_GET['id'])) { ?>
                            <input type="number" id="dr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center"  value="<?=$edit_value->dr_amt;?>" <?php if($edit_value->dr_amt>0)  echo ''; else echo ''; ?>  name="dr_amt" placeholder="Debit" class="form-control col-md-7 col-xs-12" autocomplete="off"  <?php if($_REQUEST['id']>0):  echo ''; else: ?> step="any" min="1" <?php endif; ?> tabindex="13" />
                            <input type="number" id="cr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center; margin-top: 5px"  value="<?=$edit_value->cr_amt;?>" <?php if($edit_value->cr_amt>0)  echo ''; else echo ''; ?>  name="cr_amt" placeholder="Credit" class="form-control col-md-7 col-xs-12" autocomplete="off" <?php if($_REQUEST['id']>0):  echo ''; else: ?> step="any" min="1" <?php endif; ?> tabindex="14" />
                        <?php } else {  ?>
                            <input type="number" id="dr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center"  name="dr_amt" placeholder="Debit" class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" tabindex="13" />
                            <input type="number" id="cr_amt" style="width:98%; height:25px; font-size: 11px; text-align:center; margin-top: 5px" name="cr_amt" placeholder="Credit" class="form-control col-md-7 col-xs-12" autocomplete="off" step="any" min="1" tabindex="14" />
                        <?php } ?>
                    </td>
                    <td align="center" style="width:5%; vertical-align: middle ">
                        <?php if (isset($_GET['id'])) : ?>
                            <button type="submit" class="btn btn-primary" name="editdata<?=$_GET['id'];?>" id="editdata<?=$_GET['id'];?>" style="font-size: 11px" tabindex="15"><i class="fa fa-edit"></i> Update</button><br><a href="<?=$page;?>" style="font-size: 11px"  onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you sure you want to Delete the Voucher?");' class="btn btn-danger" tabindex="16"><i class="fa fa-close"></i> Cancel</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary" name="add" id="add" style="font-size: 11px" tabindex="15"><i class="fa fa-plus"></i> Add</button>
                        <?php endif; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        <input name="count" id="count" type="hidden" value="" />
        </form>
        </table>
<?=voucher_delete_edit($rs,$unique,$_SESSION['initiate_journal_note'],$COUNT_details_data,$page);?><br><br>
<?php endif;?>
<?=$html->footer_content();mysqli_close($conn);?>