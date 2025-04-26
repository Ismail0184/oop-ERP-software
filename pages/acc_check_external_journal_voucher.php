<?php
require_once 'support_file.php';
$title = "External Journal Vouchers";
$page = "acc_check_external_journal_voucher.php";
$uniqueICPBL = "journal_info_no";
$uniqueLBC = "journal_info_no";


if(isset($_GET[$uniqueICPBL])) {
    $jsonUrl = "http://icpbd-erp.com/51816/cmu_mod/page/api/api_journal_single_voucher.php?journal_info_no=".$_GET[$uniqueICPBL]; // Replace with your API URL
    $jsonData = file_get_contents($jsonUrl);
    $data = json_decode($jsonData, true);
} else {
    // icpbl software
    $jsonUrl = "http://icpbd-erp.com/51816/cmu_mod/page/api/api_journal_vouchers.php"; // Replace with your API URL
    $jsonData = file_get_contents($jsonUrl);
    $data = json_decode($jsonData, true);
}

if(isset($_GET[$uniqueLBC])) {
    $jsonUrl = "http://lbcme.icpbd-erp.com/news/api/api_receipt_single_voucher.php?journal_info_no=".$_GET[$uniqueICPBL]; // Replace with your API URL
    $jsonData = file_get_contents($jsonUrl);
    $dataLBC = json_decode($jsonData, true);
} else {
    // icpbl software
    $jsonUrl = "http://lbcme.icpbd-erp.com/news/api/api_receipt_vouchers.php"; // Replace with your API URL
    $jsonData = file_get_contents($jsonUrl);
    $dataLBC = json_decode($jsonData, true);
}



$sectionid = @$_SESSION['sectionid'];
$sectionid_substr = @(substr($_SESSION['sectionid'],4));

$receiptVoucherNo = automatic_voucher_number_generate('receipt','journal_info_no',1,'1'.$sectionid_substr);
$jv=next_journal_voucher_id();

if(prevent_multi_submit()) {

    if (isset($_POST['confirmSaveReceiptICPBL'])) {
        foreach ($data["data"] as $item) {
            $receiptdate = $item['receiptdate'];
            $id = $item['id'];
            $ledgerId = $_POST['ledger_id'.$id];
            $narration = $_POST['narration'.$id];
            $drAmt = $_POST['dr_amt'.$id];
            $crAmt = $_POST['cr_amt'.$id];
            // Your functions to add to receipt and journal
            add_to_receipt($receiptVoucherNo, $receiptdate, $proj_id, $narration, $ledgerId, $drAmt, $crAmt, $item['type'], 0, $item['type'], $item['cheq_no'], $item['cheq_date'], $item['bank'], $item['manual_payment_no'], $item['cc_code'], $item['sub_ledger_id'], 'UNCHECKED', $ip, $item['receiptdate'], $_SESSION['sectionid'], $_SESSION['companyid'], $_SESSION['userid'], date('Y-m-d'), $now, $day, $thisday, $thismonth, $thisyear, '');
            add_to_journal_new($receiptdate, $proj_id, $jv, $item['receipt_date'], $ledgerId, $narration, $drAmt, $crAmt, 'Receipt', $receiptVoucherNo, $item['id'], 0, 0, $_SESSION['usergroup'], $item['cheq_no'], $item['cheq_date'], date('Y-m-d'), $ip, $now, $day, $thisday, $thismonth, $thisyear);
            // Construct the URL
            $deleteVoucherUrl = "http://icpbd-erp.com/51816/cmu_mod/page/api/api_receipt_single_voucher_deleted.php?journal_info_no=" . $_GET[$uniqueICPBL];
            // Send the request using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $deleteVoucherUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Optional timeout setting
            $response = curl_exec($ch);
            // Check for errors
            if(curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        }
        // Closing the window and refreshing the opener window
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }




    if(isset($_POST['delete']))
    {
        mysqli_query($conn, "DELETE from receipt where entry_status in ('UNCHECKED') and journal_info_no=".$_GET[$uniqueICPBL]." and received_from in ('External')");
        echo "<script>window.close(); </script>";
    }
}


?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUPICPBL(lk)
        {myWindow = window.open("<?=$page?>?<?=$uniqueICPBL?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 230,top = -1");}

        function DoNavPOPUPLBC(lk)
        {myWindow = window.open("<?=$page?>?<?=$uniqueICPBL?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 230,top = -1");}
    </script>
<?php require_once 'body_content.php'; ?>




<?php if(isset($_GET[$uniqueICPBL])){ ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                    <? require_once 'support_html.php';?>
                    <table align="center" class="table table-striped table-bordered" style="width:98%;font-size:11px">
                        <thead>
                        <tr style="background-color: bisque">
                            <th style="width:1%">#</th>
                            <th style="">Accounts Description</th>
                            <th style="text-align:center; width: 30%">Narration</th>
                            <th style="text-align:center; width: 15%">Debit</th>
                            <th style="text-align:center; width: 15%">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data["data"] as $item) { $sl = 0; ?>
                            <tr>
                                <td style="vertical-align: middle"><?=$sl=$sl+1?></td>
                                <td style="vertical-align: middle">
                                    <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="-1" required="required"  name="ledger_id<?=$item['id']?>">
                                        <option></option>
                                        <?php foreign_relation('accounts_ledger', 'ledger_id', 'ledger_name', $item['ledger_id'], 'status=1'); ?>
                                    </select>
                                </td>
                                <td style="vertical-align: middle"><textarea  id="narration" style="width:100%; height:50px; font-size: 11px; text-align:center"  name="narration<?=$item['id']?>"  class="form-control col-md-7 col-xs-12" autocomplete="off" >External Receipt Voucher from ICPBL Software, <?=$item['narration']?></textarea></td>
                                <td style="vertical-align: middle; text-align: right"><input type="text" value="<?=($item['dr_amt']>0)? $item['dr_amt'] : '0' ?>" class="form-control col-md-7 col-xs-12" style="font-size: 11px" name="dr_amt<?=$item['id']?>"></td>
                                <td style="vertical-align: middle; text-align: right"><input type="text" value="<?=($item['cr_amt']>0)? $item['cr_amt'] : '0' ?>" class="form-control col-md-7 col-xs-12" style="font-size: 11px" name="cr_amt<?=$item['id']?>"></td>
                            </tr>
                            <?php
                            if ($item["entry_status"] == "UNCHECKED") {
                                $entryStatus = $item["entry_status"];
                            }
                            $total_dr_amt=$total_dr_amt+$item['dr_amt'];$total_cr_amt=$total_cr_amt+$item['cr_amt']; } ?>
                        <tr>
                            <th style="vertical-align: middle" colspan="3">Total</th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_dr_amt,2)?></th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_cr_amt,2)?></th>
                        </tr>
                        </tbody>
                    </table>
                    <?php
                    $GET_status=$entryStatus;
                    if($GET_status=='UNCHECKED'){  ?>
                        <p>
                            <button style="float: left; margin-left:1%;  font-size: 11px" type="submit" name="deleteJournal" id="delete" class="btn btn-danger" onclick='return window.confirm("Are you confirm to returned?");'>Delete</button>
                            <button style="float: right; margin-right:1%; font-size: 11px" type="submit" name="confirmSaveReceiptICPBL" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Completed?");'>Check and Confirm</button>
                        </p>
                    <? } else {echo '<h6 style="text-align: center;color: red;  font-weight: bold"><i>This Voucher has been Checked & Confirmed !!</i></h6>'; ?>
                    <?php  }?>
                </form>
            </div>
        </div>
    </div>
<?php } ?>



<?php if (!isset($_GET[$uniqueICPBL])) { ?>
    <div class="col-md- col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>External Journal Voucher (ICPBL)</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%; font-size: 11px">
                    <thead>
                    <tr class="bg-primary text-white">
                        <th style="vertical-align: middle">Journal No</th>
                        <th style="vertical-align: middle">Date</th>
                        <th style="vertical-align: middle">Narration</th>
                        <th style="vertical-align: middle">Amount (Debit)</th>
                        <th style="vertical-align: middle">Amount (Credit)</th>
                        <th style="vertical-align: middle">Received From</th>
                        <th style="vertical-align: middle">Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($data["data"])): ?>
                        <?php foreach ($data["data"] as $item): ?>
                            <tr style="cursor:pointer" onclick="DoNavPOPUPLBC(<?=$item['journal_info_no']?>)">
                                <td><?=$item['journal_info_no']?></td>
                                <td><?=$item['j_date']?></td>
                                <td><?=$item['narration']?></td>
                                <td><?=$item['dr_amt']?></td>
                                <td><?=$item['cr_amt']?></td>
                                <td><?=$item['received_from']?></td>
                                <td>
                                    <?php if ($item['entry_status'] == 'UNCHECKED'): ?>
                                        <span class="label label-default" style="font-size:10px">UNCHECKED</span>
                                    <?php elseif ($item['entry_status'] == 'PENDING'): ?>
                                        <span class="label label-warning" style="font-size:10px">Unsettled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No data available</td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php }?>

<?php if (!isset($_GET[$uniqueLBC])) { ?>
    <div class="col-md- col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>External Journal Voucher (LBC)</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%; font-size: 11px">
                    <thead>
                    <tr class="bg-primary text-white">
                        <th style="vertical-align: middle">Journal No</th>
                        <th style="vertical-align: middle">Date</th>
                        <th style="vertical-align: middle">Narration</th>
                        <th style="vertical-align: middle">Amount (Debit)</th>
                        <th style="vertical-align: middle">Amount (Credit)</th>
                        <th style="vertical-align: middle">Received From</th>
                        <th style="vertical-align: middle">Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($dataLBC["data"])): ?>
                        <?php foreach ($dataLBC["data"] as $item): ?>
                            <tr style="cursor:pointer" onclick="DoNavPOPUPLBC(<?=$item['journal_info_no']?>)">
                                <td><?=$item['journal_info_no']?></td>
                                <td><?=$item['j_date']?></td>
                                <td><?=$item['narration']?></td>
                                <td><?=$item['dr_amt']?></td>
                                <td><?=$item['cr_amt']?></td>
                                <td><?=$item['received_from']?></td>
                                <td>
                                    <?php if ($item['entry_status'] == 'UNCHECKED'): ?>
                                        <span class="label label-default" style="font-size:10px">UNCHECKED</span>
                                    <?php elseif ($item['entry_status'] == 'CHECKED'): ?>
                                        <span class="label label-warning" style="font-size:10px">CHECKED</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php }?>
<?=$html->footer_content();?>