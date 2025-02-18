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


$results="Select d.*,i.*
from
".$table_details." d,
item_info i

where
d.item_id=i.item_id and
d.".$unique."=".$$unique." order by d.id";

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
                        <tr style="background-color: bisque; vertical-align: middle">
                            <th style="vertical-align: middle">SL</th>
                            <th style="vertical-align: middle">Code</th>
                            <th style="vertical-align: middle">Finish Goods</th>
                            <th style="width:5%; text-align:center;vertical-align: middle">UOM</th>
                            <th style="text-align:center; vertical-align: middle">Order Qty</th>
                            <th style="text-align:center; vertical-align: middle">Unit Price</th>
                            <th style="text-align:center; vertical-align: middle">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $results="Select SUM(d.total_unit) as total_unit,SUM(d.total_amt) as total_amt,d.unit_price,i.*
                        from
                        ".$table_details." d,
                        item_info i
                        where
                        d.item_id=i.item_id and
                        d.item_id not in ('1096000100010312') and
                        d.".$unique."=".$$unique." group by d.item_id order by d.id";
                        $query=mysqli_query($conn, $results);
                        $i=0;
                        $ttotalamount = 0;
                        while($data=mysqli_fetch_object($query)){
                            $present_stock_sql=mysqli_query($conn, "Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size
    from
    item_info i,
    journal_item j,
    lc_lc_received_batch_split bsp
    where
    j.item_id=i.item_id and
    j.warehouse_id='".$master->depot_id."' and
    bsp.batch=j.batch and 
    bsp.status='PROCESSING' and 
    j.item_id='".$data->item_id."'
    group by j.item_id order by i.item_id");
    $ps_data=mysqli_fetch_object($present_stock_sql);
                         
                            $available_stock=@$ps_data->Available_stock_balance;
                          $unrec_qty=$available_stock-$data->total_unit;?>
                            <tr>
                                <td style="width:3%; vertical-align:middle"><?=$i=$i+1; ?></td>
                                <td style="vertical-align:middle"><?=$data->item_id;?> - <?=$data->finish_goods_code;?></td>
                                <td style="vertical-align:middle;"><?=$data->item_name; if($data->total_amt==0){ echo '<font style="color: red; margin-left: 5px">[Free]</font>'; } elseif($data->total_amt<0){echo '<font style="color: red; margin-left: 5px">[Discounted]</font>'; }?></td>
                                <td style="vertical-align:middle; text-align:center"><?=$data->unit_name;?></td>
                                <td align="center" style=" text-align:center;vertical-align: middle"><?=$data->total_unit;?></td>
                                <td align="center" style=" text-align:right;vertical-align: middle"><?=($data->unit_price==0)? '-' : $data->unit_price?></td>
                                <td align="center" style=" text-align:right;vertical-align: middle"><?=($data->total_amt==0)? '-' : $data->total_amt?></td>
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