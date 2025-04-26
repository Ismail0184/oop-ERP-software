<?php
require_once 'support_file.php';
$title='DO Details';
$now=time();
$unique='do_no';
$table="sale_do_master";
$table_details="sale_do_details";
$table_sale_do_chalan="sale_do_chalan";
$journal_item="journal_item";


$page='MIS_invoice_view.php';
$pages='MIS_data_delete_invoice_delete.php';

$crud      =new crud($table);
$$unique = $_GET[$unique];
$master=find_all_field(''.$table.'','',''.$unique.'='.$_GET[$unique].'');
$mushak_master = find_all_field('VAT_mushak_6_3','','do_no='.$_GET[$unique]);

$do_date = @$master->do_date;
$challan_date = @$master->challan_date;
$sent_to_warehuse_at = @$master->sent_to_warehuse_at;
$user = @$master->entry_by;
$invoice_by = find_a_field('users','fname','user_id='.$user);
$dealer_master=find_all_field('dealer_info','','dealer_code='.$master->dealer_code);
$challan_no = find_a_field('sale_do_chalan','distinct chalan_no','do_no='.$_GET[$unique]);

$invoiceDataQuqery="Select d.id as iid, SUM(d.total_unit) as total_unit,SUM(d.total_amt) as total_amt,d.unit_price,i.*
                        from
                        ".$table_details." d,
                        item_info i
                        where
                        d.item_id=i.item_id and
                        d.item_id not in ('1096000100010312') and
                        d.".$unique."=".$$unique." group by d.id order by d.id";
$query=mysqli_query($conn, $invoiceDataQuqery);
while($idata=mysqli_fetch_object($query))
{
    $ids =$idata->iid;
    $unit_price = @$_POST['unit_price'.$ids];
    $total_unit = $_POST['total_unit'.$ids];
    $total_amt = $unit_price*$total_unit;

    if(isset($_POST['itemEdit'.$ids]))
    {
        $res=mysqli_query($conn, ("UPDATE ".$table_details." set unit_price='".$unit_price."',total_amt='".$total_amt."' WHERE id=".$ids));
        $res=mysqli_query($conn, ("UPDATE ".$table_sale_do_chalan." set unit_price='".$unit_price."',total_amt='".$total_amt."' WHERE order_no=".$ids));
        unset($_POST);
    }
}

$getInvoiceAmount= find_a_field(''.$table_details.'','SUM(total_amt)','do_no='.$$unique);
$getCustomerLedger = find_a_field('dealer_info','account_code','dealer_code='.$master->dealer_code);

if(isset($_POST['confirmInvoiceUpdate']))
{
    $res=mysqli_query($conn, ("UPDATE journal set dr_amt='".$getInvoiceAmount."'  WHERE ledger_id='".$getCustomerLedger."' and do_no=".$$unique));
    $res=mysqli_query($conn, ("UPDATE journal set cr_amt='".$getInvoiceAmount."'  WHERE ledger_id='3002000100000000' and do_no=".$$unique));

    echo "<script>window.close(); </script>";
}

$results="Select d.*,i.*
from
".$table_details." d,
item_info i

where
d.item_id=i.item_id and
d.".$unique."=".$$unique." order by d.id";

$queryJournal = mysqli_query($conn, "SELECT j.id as jid,j.*,l.* FROM journal j, accounts_ledger l where j.ledger_id=l.ledger_id and j.do_no=".$$unique);

if(prevent_multi_submit()) {
    if (isset($_POST['reprocess'])) {
        if ($master->do_section == 'Special_invoice') {
            $_SESSION['select_dealer_do_SP'] = $master->dealer_code;
            $_SESSION['unique_master_for_SP'] = $$unique;
        } else {
            $_SESSION['select_dealer_do_regular'] = $master->dealer_code;
            $_SESSION['unique_master_for_regular'] = $$unique;
        }
        echo "<script>self.opener.location = '$target_page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }

    if(isset($_POST['delete_forever'])){
        mysqli_query($conn, "DELETE FROM `VAT_mushak_6_3` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `VAT_mushak_6_3_details` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `journal_item` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `journal` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `sale_do_chalan` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `sale_do_details` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `sale_do_master` WHERE do_no=".$_GET[$unique]."");
        echo "<script>window.close(); </script>";
    }

    if (isset($_POST['back_to_manual_with_delete_all']))
    {
        mysqli_query($conn, "DELETE FROM `VAT_mushak_6_3` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `VAT_mushak_6_3_details` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `journal_item` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `journal` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "DELETE FROM `sale_do_chalan` WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "UPDATE `sale_do_details` SET status='MANUAL' WHERE do_no=".$_GET[$unique]."");
        mysqli_query($conn, "UPDATE `sale_do_master` SET status='MANUAL' WHERE do_no=".$_GET[$unique]."");
    }
}
?>


<?php require_once 'header_content.php'; ?>
<style>
    #customers {}
    #customers td {}
    #customers tr:ntd-child(even)
    {background-color: #f0f0f0;}
    #customers tr:hover {background-color: #f5f5f5;}
    td{}
</style>
<?php if(isset($_GET[$unique])){
 require_once 'body_content_without_menu.php'; } else {
 require_once 'body_content.php'; } ?>

<?php if(isset($_GET[$unique])){ ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                    <? require_once 'support_html.php';?>
                    <table id="customers" align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                        <tr>
                            <th style="width: 10%;vertical-align: middle">Invoice No</th>
                            <th style="width: 1%;vertical-align: middle">:</th>
                            <td style="width: 22%;vertical-align: middle"><?=$_GET['do_no']?></td>

                            <th style="width: 10%;vertical-align: middle">Invoice Date</th>
                            <th style="width: 1%;vertical-align: middle">:</th>
                            <td style="width: 23%;vertical-align: middle"><?=$do_date?></td>

                            <th style="width:10%; vertical-align: middle">Sent to warehouse</th>
                            <th style="width:1%; vertical-align: middle">:</th>
                            <td style="width:22%; vertical-align: middle"><?=$sent_to_warehuse_at?></td>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle">Challan Status</th>
                            <th style="vertical-align: middle">:</th>
                            <td style="vertical-align: middle">
                                <?php if(!empty($challan_no)) { ?>
                                    <span class="label label-success" style="font-size:10px">Created</span>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>

                            <th style="vertical-align: middle">Challan No</th>
                            <th style="vertical-align: middle">:</th>
                            <td style="vertical-align: middle">
                                <?php if(!empty($challan_no)) { ?>
                                    <?=$challan_no?>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>

                            <th style="vertical-align: middle">Challan Date</th>
                            <th style="vertical-align: middle ">:</th>
                            <td style="vertical-align: middle">
                                <?php if(!empty($challan_no)) { ?>
                                    <?=$challan_date?>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>
                        </tr>

                        <tr>
                            <th style="vertical-align: middle">Mushak Status</th>
                            <th style="vertical-align: middle">:</th>
                            <td style="vertical-align: middle">
                                <?php if($master->mushak_challan_status=='RECORDED') { ?>
                                    <span class="label label-success" style="font-size:10px">Created</span>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>

                            <th style="vertical-align: middle">Mushak No</th>
                            <th style="vertical-align: middle">:</th>
                            <td style="vertical-align: middle">
                                <?php if($master->mushak_challan_status=='RECORDED') { ?>
                                    <?=$mushak_master->mushak_no;?>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>

                            <th style="vertical-align: middle">Mushak By</th>
                            <th style="vertical-align: middle ">:</th>
                            <td style="vertical-align: middle">
                                <?php if($master->mushak_challan_status=='RECORDED') { ?>
                                    <?=find_a_field('users','fname','user_id='.$mushak_master->entry_by);?><br>
                                    At: <?=$mushak_master->entry_at?>
                                <?php } else { ?>
                                    <span class="label label-danger" style="font-size:10px">Pending</span>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    <table id="customers" align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                        <thead>
                        <tr class="bg-primary">
                            <th style="vertical-align: middle">SL</th>
                            <th style="vertical-align: middle">Code</th>
                            <th style="vertical-align: middle">Finish Goods</th>
                            <th style="width:5%; text-align:center;vertical-align: middle">UOM</th>
                            <th style="text-align:center; vertical-align: middle">Order Qty</th>
                            <th style="text-align:center; vertical-align: middle">Unit Price</th>
                            <th style="text-align:center; vertical-align: middle">Amount</th>
                            <th style="text-align:center; vertical-align: middle">Option</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=0;
                        $ttotalamount = 0;
                        $query=mysqli_query($conn, $invoiceDataQuqery);
                        while($data=mysqli_fetch_object($query)){?>
                            <tr>
                                <td style="width:3%; vertical-align:middle"><?=$i=$i+1; ?></td>
                                <td style="vertical-align:middle"><?=$data->item_id;?> - <?=$data->finish_goods_code;?></td>
                                <td style="vertical-align:middle;"><?=$data->item_name; if($data->total_amt==0){ echo '<font style="color: red; margin-left: 5px">[Free]</font>'; } elseif($data->total_amt<0){echo '<font style="color: red; margin-left: 5px">[Discounted]</font>'; }?></td>
                                <td style="vertical-align:middle; text-align:center"><?=$data->unit_name;?></td>
                                <td align="center" style=" text-align:center;vertical-align: middle">
                                    <input type="text" value="<?=$data->total_unit;?>" name="total_unit<?=$data->iid;?>">
                                </td>
                                <td align="center" style=" text-align:right;vertical-align: middle">
                                    <input type="text" value="<?=($data->unit_price==0)? '-' : $data->unit_price?>" name="unit_price<?=$data->iid;?>">
                                </td>
                                <td align="center" style=" text-align:right;vertical-align: middle"><?=($data->total_amt==0)? '-' : $data->total_amt?></td>
                                <td align="center" style=" text-align:right;vertical-align: middle">
                                    <button type="submit" name="itemEdit<?=$data->iid;?>" style="background-color:transparent;color:; border:none; margin:0px; font-size:13px; padding:0px" onclick="return window.confirm('Are you sure you want to edit this?');" title="Edit Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></button>
                                    <button type="submit" name="dataDelete<?=$data->iid;?>" style="background-color:transparent;color:red; border:none; margin:0px; font-size:13px; padding:0px" onclick="return window.confirm('Are you sure you want to delete this?');" title="Delete Record" data-toggle="tooltip"><span class="glyphicon glyphicon-trash"></span></button>
                                </td>
                            </tr>
                        <?php $ttotalamount=$ttotalamount+$data->total_amt;
                        }
                              $cash_discount=find_a_field(''.$table_details.'','SUM(total_amt)','item_id="1096000100010312" and do_no="'.$_GET['do_no'].'"');
                              $cash_discounts=substr($cash_discount,1)
                         ?>
                        </tbody>
                        <?php if($cash_discounts>0):?>
                        <tr style="font-weight: bold">
                            <td colspan="6" style="font-weight:bold; font-size:11px" align="right">Less: Cash Discount</td>
                            <td align="right" ><?=number_format($cash_discounts,2);?></td>
                        </tr><?php endif;?>
                        <tr style="font-weight: bold">
                            <td colspan="6" style="font-weight:bold; font-size:11px" align="right">Total Order in Amount</td>
                            <td align="right" ><?=number_format($ttotalamount+$cash_discount,2);?></td>
                        </tr>
                        <?php
                        if($master->commission>0) { ?>
                        <tr style="font-weight: bold">
                            <td colspan="6" style="font-weight:bold; font-size:11px" align="right">Less: Commission</td>
                            <td style="text-align:right"><?=number_format($master->commission_amount,2);?></td>
                        </tr>
                        <tr style="font-weight: bold">
                            <td colspan="6" style="font-weight:bold; font-size:11px" align="right">Total Receivable Amount</td>
                            <td style="text-align:right"><?=number_format(($ttotalamount+$cash_discount)-$master->commission_amount,2);?></td>
                        </tr>
                      <?php } ?>
                        <tr>
                            <th colspan="8">
                                <button style="float: right;font-size: 12px;margin-left: 1%;" type="submit" name="confirmInvoiceUpdate" class="btn btn-success" onclick='return window.confirm("Are you confirm?");'>Confirm Update the Invoice</button>
                            </th>
                        </tr>
                    </table>

                    <table id="customers" align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                        <thead>
                        <tr class="bg-primary" style="vertical-align: middle">
                            <th style="vertical-align: middle">SL</th>
                            <th style="vertical-align: middle">Ledger</th>
                            <th style="vertical-align: middle">Narrations</th>
                            <th style="width:5%; text-align:center;vertical-align: middle">Dr Amt</th>
                            <th style="text-align:center; vertical-align: middle">Cr Amt</th>
                            <th style="text-align:center; vertical-align: middle">Option</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=0; while($data=mysqli_fetch_object($queryJournal)){?>
                        <tr>
                            <td><?=$i=$i+1;?></td>
                            <td><?=$data->ledger_name;?></td>
                            <td><?=$data->narration;?></td>
                            <td>
                                <input type="text" value="<?=$data->dr_amt;?>" name="drAmt<?=$data->jid;?>">
                            </td>
                            <td>
                                <input type="text" value="<?=$data->cr_amt;?>" name="crAmt<?=$data->jid;?>">
                            </td>
                            <td align="center" style=" text-align:right;vertical-align: middle">
                                <button type="submit" name="journalEditData<?=$data->jid;?>" style="background-color:transparent;color:; border:none; margin:0px; font-size:13px; padding:0px" onclick="return window.confirm('Are you sure you want to Edit this?');" title="Edit Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></button>
                                <button type="submit" name="journalDeleteData<?=$data->jid;?>" style="background-color:transparent;color:red; border:none; margin:0px; font-size:13px; padding:0px" onclick="return window.confirm('Are you sure you want to delete this?');" title="Delete Record" data-toggle="tooltip"><span class="glyphicon glyphicon-trash"></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <p>
                        <?php
                        $access_days=30;
                        $datetime1 = date_create($master->do_date);
                        $datetime2 = date_create(date('Y-m-d'));
                        $interval = date_diff($datetime1, $datetime2);
                        $v_d=$interval->format('%a');
                        if($v_d<=$access_days){?>
                            <button style="float: left; margin-left: 1%; font-size: 12px" type="submit" name="delete_forever" class="btn btn-danger" onclick='return window.confirm("Are you confirm?");'>Delete Forever</button>
                            <button style="float: left; margin-left: 1%; font-size: 12px" type="submit" name="back_to_manual" id="reprocess" class="btn btn-success" onclick='return window.confirm("Are you confirm?");'>Back to Manual</button>
                        <?php if(!empty($challan_no)) { ?>
                            <button style="float: right;font-size: 12px;margin-left: 1%;" type="submit" name="back_to_manual_with_delete_all" id="checked" class="btn btn-danger" onclick='return window.confirm("Are you confirm?");'>Back to Manual with Delete (Challan & Mushak)  </button>
                        <?php } } else { ?>
                            <h6 style="font-weight: bold; color: red; text-align: center">You do not have permission to modify this invoice.</h6>
                        <?php } ?>
                    </p>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<?=$html->footer_content();mysqli_close($conn);?>