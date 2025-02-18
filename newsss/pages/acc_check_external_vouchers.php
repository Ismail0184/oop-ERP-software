<?php
require_once 'support_file.php';
$title='External Receipt Voucher';


$unique='receipt_no';
$unique_journal='journal_info_no';
$unique_field='voucher_date';
$table_journal_master="secondary_payment";
$table="receipt";
$table_journal="journal_info";
$page="acc_check_external_receipt_voucher.php";
$crud      =new crud($table_journal_master);
$$unique = $_GET[$unique];
$create_date=date('Y-m-d');
$jv=next_journal_voucher_id();









if(prevent_multi_submit()) {
    if (isset($_POST['confirmsave'])) {
        $ress="SELECT er.id as jid,er.receipt_no,er.receipt_no,er.ledger_id,er.receiptdate as date,a.ledger_name,er.narration,er.cr_amt,er.dr_amt,er.cr_amt from receipt er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.entry_status in ('UNCHECKED') and er.receipt_no=".$_GET[$unique]." and er.received_from in ('External')";
        $re_query = mysqli_query($conn, $ress);
        while ($data = mysqli_fetch_object($re_query)) {
            $receiptdate=$data->date;
            add_to_journal_new($receiptdate, $proj_id, $jv, $data->receipt_date, $data->ledger_id, $data->narration, $data->dr_amt, $data->cr_amt, Receipt, $data->receipt_no, $data->jid, 0, 0, $_SESSION[usergroup], $data->cheq_no, $data->cheq_date, $create_date, $ip, $now, $day, $thisday, $thismonth, $thisyear);
        }
        $up_payment="UPDATE ".$table." SET entry_status='CHECKED' where ".$unique."=".$$unique."";
        $up_query=mysqli_query($conn, $up_payment);
        unset($_POST);
        unset($$unique);
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }// if insert confirm

    if(isset($_POST['delete']))
    {
        mysqli_query($conn, "DELETE from receipt where entry_status in ('UNCHECKED') and receipt_no=".$_GET[$unique]." and received_from in ('External')");
        echo "<script>window.close(); </script>";
    }
    if(isset($_POST['deleteJournal']))
    {
        mysqli_query($conn, "DELETE from journal_info where entry_status in ('UNCHECKED') and journal_info_no=".$_GET[$unique]." and received_from in ('External')");
        echo "<script>window.close(); </script>";
    }


    if (isset($_POST['confirmsaveJournal'])) {
        $ress="SELECT er.id as jid,er.journal_info_no,er.journal_info_no,er.ledger_id,er.j_date as date,a.ledger_name,er.narration,er.cr_amt,er.dr_amt,er.cr_amt from journal_info er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.entry_status in ('UNCHECKED') and er.journal_info_no=".$_GET[$unique]." and er.received_from in ('External')";
        $re_query = mysqli_query($conn, $ress);
        while ($data = mysqli_fetch_object($re_query)) {
            $receiptdate=$data->date;
            add_to_journal_new($receiptdate, $proj_id, $jv, $data->j_date, $data->ledger_id, $data->narration, $data->dr_amt, $data->cr_amt, 'Journal_info', $data->journal_info_no, $data->jid, 0, 0, $_SESSION['usergroup'], $data->cheq_no, $data->cheq_date, $create_date, $ip, $now, $day, $thisday, $thismonth, $thisyear);
        }
        $up_payment="UPDATE ".$table_journal." SET entry_status='CHECKED' where journal_info_no=".$$unique."";
        $up_query=mysqli_query($conn, $up_payment);
        unset($_POST);
        unset($$unique);
        echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }
}

$ress="SELECT er.receipt_no,er.receipt_no,er.ledger_id,er.receiptdate as date,a.ledger_name,er.narration,er.cr_amt,er.dr_amt,er.cr_amt from receipt er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.receipt_no=".$_GET[$unique]." and er.received_from in ('External')";

$ress_journal="SELECT er.journal_info_no,er.journal_info_no,er.ledger_id,er.j_date as date,a.ledger_name,er.narration,er.cr_amt,er.dr_amt,er.cr_amt from journal_info er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.journal_info_no=".$_GET[$unique_journal]." and er.received_from in ('External')";
?>


<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 230,top = -1");}

    function DoNavPOPUPS(lks)
    {myWindow = window.open("<?=$page?>?<?=$unique_journal?>="+lks, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 230,top = -1");}
</script>



<?php if(isset($_GET[$unique])){ require_once 'body_content_without_menu.php'; } else { require_once 'body_content.php';} ?>

<?php if(isset($_GET[$unique])){ ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                    <? require_once 'support_html.php';?>
                    <table align="center" class="table table-striped table-bordered" style="width:98%;font-size:11px">
                        <thead>
                        <tr style="background-color: bisque">
                            <th style="width:1%">#</th>
                            <th style="width:49%">Accounts Description</th>
                            <th style="text-align:center; width: 30%">Narration</th>
                            <th style="text-align:center; width: 10%">Debit</th>
                            <th style="text-align:center; width: 10%">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query=mysqli_query($conn, $ress);
                        while($data=mysqli_fetch_object($query)): ?>
                        <tr>
                            <td style="vertical-align: middle"><?=$sl=$sl+1?></td>
                            <td style="vertical-align: middle">
                            <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="-1" required="required"  name="ledger_id">
                        <option></option>
                        <?php foreign_relation('accounts_ledger', 'ledger_id', 'ledger_name', $data->ledger_id, 'status=1'); ?>
                        </select>
                    </td>
                            <td style="vertical-align: middle"><textarea  id="narration" style="width:100%; height:50px; font-size: 11px; text-align:center"  name="narration"  class="form-control col-md-7 col-xs-12" autocomplete="off" ><?=$data->narration?></textarea></td>
                            <td style="vertical-align: middle; text-align: right"><?=($data->dr_amt>0)? $data->dr_amt : '-' ?></td>
                            <td style="vertical-align: middle; text-align: right"><?=($data->cr_amt>0)? $data->cr_amt : '-' ?></td>
                        </tr>
                        <?php $total_dr_amt=$total_dr_amt+$data->dr_amt;$total_cr_amt=$total_cr_amt+$data->cr_amt; endwhile; ?>
                        <tr>
                            <th style="vertical-align: middle" colspan="3">Total</th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_dr_amt,2)?></th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_cr_amt,2)?></th>
                        </tr>
                        </tbody>
                    </table>
                    <?php
                    $GET_status=find_a_field(''.$table.'','entry_status',''.$unique.'='.$_GET[$unique]);
                    if($GET_status=='UNCHECKED'){  ?>
                        <p>
                            <button style="float: left; margin-left:1%;  font-size: 11px" type="submit" name="delete" id="delete" class="btn btn-danger" onclick='return window.confirm("Are you confirm to returned?");'>Delete</button>
                            <button style="float: right; margin-right:1%; font-size: 11px" type="submit" name="confirmsave" id="confirmsave" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Completed?");'>Check and Confirm</button>
                        </p>
                    <? } else {echo '<h6 style="text-align: center;color: red;  font-weight: bold"><i>This Voucher has been Checked & Confirmed !!</i></h6>'; ?>
                        <?php  }?>
                </form>
            </div>
        </div>
    </div>
<?php } ?>



<?php if(isset($_GET[$unique_journal])){ ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                    <? require_once 'support_html.php';?>
                    <table align="center" class="table table-striped table-bordered" style="width:98%;font-size:11px">
                        <thead>
                        <tr style="background-color: bisque">
                            <th style="width:1%">#</th>
                            <th style="width:49%">Accounts Description</th>
                            <th style="text-align:center; width: 30%">Narration</th>
                            <th style="text-align:center; width: 10%">Debit</th>
                            <th style="text-align:center; width: 10%">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query=mysqli_query($conn, $ress_journal);
                        while($data=mysqli_fetch_object($query)): ?>
                            <tr>
                                <td style="vertical-align: middle"><?=$sl=$sl+1?></td>
                                <td style="vertical-align: middle">
                                    <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="-1" required="required"  name="ledger_id">
                                        <option></option>
                                        <?php foreign_relation('accounts_ledger', 'ledger_id', 'ledger_name', $data->ledger_id, 'status=1'); ?>
                                    </select>
                                </td>
                                <td style="vertical-align: middle"><textarea  id="narration" style="width:100%; height:50px; font-size: 11px; text-align:center"  name="narration"  class="form-control col-md-7 col-xs-12" autocomplete="off" ><?=$data->narration?></textarea></td>
                                <td style="vertical-align: middle; text-align: right"><?=($data->dr_amt>0)? $data->dr_amt : '-' ?></td>
                                <td style="vertical-align: middle; text-align: right"><?=($data->cr_amt>0)? $data->cr_amt : '-' ?></td>
                            </tr>
                            <?php $total_dr_amt=$total_dr_amt+$data->dr_amt;$total_cr_amt=$total_cr_amt+$data->cr_amt; endwhile; ?>
                        <tr>
                            <th style="vertical-align: middle" colspan="3">Total</th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_dr_amt,2)?></th>
                            <th style="vertical-align: middle; text-align: right"><?=number_format($total_cr_amt,2)?></th>
                        </tr>
                        </tbody>
                    </table>
                    <?php
                    $GET_status=find_a_field(''.$table_journal.'','entry_status','journal_info_no='.$_GET[$unique]);
                    if($GET_status=='UNCHECKED'){  ?>
                        <p>
                            <button style="float: left; margin-left:1%;  font-size: 11px" type="submit" name="deleteJournal" id="delete" class="btn btn-danger" onclick='return window.confirm("Are you confirm to returned?");'>Delete</button>
                            <button style="float: right; margin-right:1%; font-size: 11px" type="submit" name="confirmsaveJournal" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Completed?");'>Check and Confirm</button>
                        </p>
                    <? } else {echo '<h6 style="text-align: center;color: red;  font-weight: bold"><i>This Voucher has been Checked & Confirmed !!</i></h6>'; ?>
                    <?php  }?>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


<?php if(!isset($_GET[$unique]) && !isset($_GET[$unique_journal])): ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
        <table align="center" style="width: 50%;">
            <tr><td>
                    <input type="date"  style="width:150px; font-size: 11px; height: 25px" max="<?=date('Y-m-d');?>"  value="<?php if($_POST['f_date']) echo $_POST['f_date']; else echo date('Y-m-01');?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                <td style="width:10px; text-align:center"> -</td>
                <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?php if($_POST['t_date']) echo $_POST['t_date']; else echo date('Y-m-d');?>" required  max="<?=date('Y-m-d');?>" name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewreport"  class="btn btn-primary">View Record</button></td>
            </tr></table>
    </form>
    <?php
    if(isset($_POST['viewreport'])){
        $res="SELECT er.receipt_no,er.receipt_no,er.receiptdate as date,a.ledger_name,er.narration,SUM(er.dr_amt) as amount,er.entry_status as status from receipt er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.receiptdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and er.dr_amt>0 and er.received_from in ('External') group by er.receipt_no";
        $res_journal="SELECT er.journal_info_no,er.journal_info_no,er.j_date as date,a.ledger_name,er.narration,SUM(er.dr_amt) as amount,er.entry_status as status from journal_info er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.entry_status in ('UNCHECKED') and er.dr_amt>0 and er.received_from in ('External') group by er.journal_info_no";
    } else {
        $res="SELECT er.receipt_no,er.receipt_no,er.receiptdate as date,a.ledger_name,er.narration,SUM(er.dr_amt) as amount,er.entry_status as status from receipt er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.entry_status in ('UNCHECKED') and er.dr_amt>0 and er.received_from in ('External') group by er.receipt_no";

         }
    echo $crud->report_templates_with_title_and_class($res,'External Receive Voucher',''); ?>


    <div class="col-md- col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>External Journal Voucher</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%; font-size: 11px">
                    <thead>
                    <tr style="background-color: bisque"><th>#</th>
                        <th style="vertical-align:middle">Journal Info No</th>
                        <th style="vertical-align:middle">Date</th>
                        <th style="vertical-align:middle">Ledger Name</th>
                        <th style="vertical-align:middle">Narration</th>
                        <th style="vertical-align:middle">Amount</th>
                        <th style="vertical-align:middle">Status</th>
                    </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $res_journal="SELECT er.journal_info_no,er.journal_info_no,er.j_date as date,a.ledger_name,er.narration,SUM(er.dr_amt) as amount,er.entry_status as status from journal_info er,accounts_ledger a where 
  er.ledger_id=a.ledger_id and er.entry_status in ('UNCHECKED') and er.dr_amt>0 and er.received_from in ('External') group by er.journal_info_no";
                    $query_journal = mysqli_query($conn, $res_journal);
                    while($data=mysqli_fetch_object($query_journal)){?>
                    <tr style="cursor:pointer"  onclick="DoNavPOPUPS(<?=$data->journal_info_no?>)">
                        <td><?=$i?></td>
                        <td><?=$data->journal_info_no?></td>
                        <td><?=$data->date?></td>
                        <td><?=$data->ledger_name?></td>
                        <td><?=$data->narration?></td>
                        <td><?=$data->amount?></td>
                        <td><?=$data->status?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
<?php endif;?>
<?=$html->footer_content();mysqli_close($conn);?>