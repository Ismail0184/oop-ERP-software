<?php
require_once 'support_file.php';
$title='View Work Order';
$table = 'purchase_master';
$table_details="purchase_invoice";
$unique = 'po_no';
$status = 'UNCHECKED';
$page="po_status.php";
$print_page="po_print_view.php";
$crud      =new crud($table);
$$unique=@$_GET[$unique];
if (isset($_POST['reprocess'])) {

        $_POST['status'] = 'MANUAL';
        $crud->update($table);
        $_SESSION['initiate_po_no'] = $_GET[$unique];
        $type = 1;
        echo "<script>self.opener.location = 'po_create_item.php'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }
$po_master=find_all_field(''.$table.'','',''.$unique.'='.$$unique.'');	
$GET_status=find_a_field(''.$table.'','status',''.$unique.'='.$$unique);

if(isset($_POST['viewReport'])){
    $res='select  
    a.po_no,
    a.vendor_id, 
    a.po_no, 
    a.return_comments,
    a.exim_status as type, 
    a.po_date as Work_order_Date, 
    v.vendor_name, 
    b.warehouse_name as final_Destination,
    a.work_order_for_department as Created_By_Department,
    c.fname,
    p.PBI_NAME as Check_By,
    a.delivery_within,
    a.status 
    from 
    purchase_master a,
    warehouse b,
    users c,
    vendor v,
    personnel_basic_info p 
    where  
    a.warehouse_id=b.warehouse_id and 
    a.entry_by=c.user_id and 
    a.checkby=p.PBI_ID and 
    a.vendor_id=v.vendor_id and
    a.po_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"
    order by a.po_no desc';
    } else { 
    $res='select  
    a.po_no,
    a.vendor_id, 
    a.po_no, 
    a.return_comments,
    a.exim_status as type, 
    a.po_date as Work_order_Date, 
    v.vendor_name, 
    b.warehouse_name as final_Destination,
    a.work_order_for_department as Created_By_Department,
    c.fname,
    p.PBI_NAME as Check_By,
    a.delivery_within,a.status 
    from 
    purchase_master a,
    warehouse b,
    users c,
    vendor v,
    personnel_basic_info p 
    where  
    a.warehouse_id=b.warehouse_id and 
    a.entry_by=c.user_id and 
    a.checkby=p.PBI_ID and 
    a.vendor_id=v.vendor_id and
    a.status in ("MANUAL","CANCELED","UNCHECKED") 
    order by a.po_no desc';
    }

$PostFDate = @$_POST['f_date'];
$PostTDate = @$_POST['t_date'];

$cash_discount = @$cash_discount;
$tax_ait = @$tax_ait;
$asf = @$asf;
$transport_bill = @$transport_bill;
$labor_bill = @$labor_bill;
$tax = @$tax;
	?>


<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=600,left = 250,top = -1");}
    </script>
</head>


<?php require_once 'body_content.php'; ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <table class="table table-striped table-bordered" style="width:100%;font-size:11px">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th style="width: 2%; vertical-align: middle">#</th>
                            <th style="vertical-align: middle">PO</th>
                            <th style="vertical-align: middle">Final Destination</th>
                            <th style="vertical-align: middle">Item Code</th>
                            <th style="vertical-align: middle">Description of the Goods</th>
                            <th style="text-align: center; vertical-align: middle">UOM</th>
                            <th style="text-align: center; vertical-align: middle">Pre. Rate</th>
                            <th style="text-align: center; vertical-align: middle">Rate</th>
                            <th style="text-align: center; vertical-align: middle">Qty</th>
                            <th style="text-align: center; vertical-align: middle">Amount</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $res=mysqli_query($conn, 'Select td.*,i.*,w.warehouse_name,(select rate from '.$table_details.' where '.$unique.'!='.$_GET[$unique].' and item_id=i.item_id order by id DESC limit 1) as pre_rate from '.$table_details.' td,item_info i,warehouse w where td.item_id=i.item_id and td.warehouse_id=w.warehouse_id and td.'.$unique.'='.$_GET[$unique].'');$i = 0;$total=0;
                        while($req_data=mysqli_fetch_object($res)){?>
                            <tr>
                                <td><?=$i=$i+1;?></td>
                                <td><?=$req_data->po_no;?></td>
                                <td><?=$req_data->warehouse_name;?></td>
                                <td><?=$req_data->finish_goods_code;?></td>
                                <td><?=$req_data->item_name;?></td>
                                <td style="text-align:center"><?=$req_data->unit_name;?></td>
                                <td style="text-align:center"><?=$req_data->pre_rate;?></td>
                                <td style="text-align:center"><?=number_format($req_data->rate,2);?></td>
                                <td style="text-align:center"><?=number_format($req_data->qty,2);?></td>
                                <td style="text-align: right"><?=number_format($req_data->amount,2);?></td>
                            </tr>
                            <?php $total=$total+$req_data->amount;  } ?>
                        <tr style="font-weight: bold">
                            <td colspan="9" align="right">TOTAL:</td>
                            <td align="right"><strong>
                                    <?  echo number_format(($total),2);?>
                                </strong>
                            </td>
                        </tr>

                        <? if($cash_discount>0){?>
                            <tr style="font-weight: bold">
                                <td colspan="9" align="right">Discount:</td>
                                <td align="right"><strong>
                                        <? if($cash_discount>0) echo number_format($cash_discount,2); else echo '0.00';?>
                                    </strong>
                                </td>
                            </tr>
                        <? }?>

                        <? if($tax_ait>0){?>
                            <tr style="font-weight: bold">
                                <td colspan="9" align="right">AIT/Tax (<?=$tax_ait?>%): </td>
                                <td align="right"><strong> <? echo number_format((($total-$cash_discount*$tax_ait)/100),2);?> </strong></td>
                            </tr>

                        <? } $totaltaxait=($total*$tax_ait)/100; ?>

                        <tr style="font-weight: bold">
                            <td colspan="9" align="right">SUB TOTAL:</td>
                            <td align="right"><strong>
                                    <?  echo number_format(($subtotal=$total+$asf+$totaltaxait-$cash_discount),2) ?>
                                </strong>
                            </td>
                          </tr>

                        <? if($tax>0){?>
                            <tr style="font-weight: bold">
                                <td colspan="9" align="right">VAT(<?=$tax;?> %):</td>
                                <td align="right"><strong><?  echo number_format((($subtotal*$tax)/100),2);?></strong></td>
                            </tr>
                        <? } $tax_totals=($subtotal*$tax)/100; ?>

                        <? if($transport_bill>0){?>
                            <tr style="font-weight: bold">
                                <td colspan="9" align="right">Transport Bill: </td>
                                <td align="right"><strong> <? echo number_format(($transport_bill),2);?> </strong></td>
                            </tr>
                        <? }?>

                        <? if($labor_bill>0){?>
                            <tr style="font-weight: bold">
                                <td colspan="9" align="right">Labor Bill: </td>
                                <td align="right"><strong> <? echo number_format(($labor_bill),2);?> </strong></td>
                            </tr>
                        <? }?>

                        <tr style="font-weight: bold">
                            <td colspan="9" align="right">Grand Total:</td>
                            <td align="right"><strong> <? echo number_format(($subtotal+$tax_totals+$transport_bill+$labor_bill),2);?> </strong></td>
                        </tr>
                        </tbody>
                    </table>
                                
                    <?php if($GET_status=='UNCHECKED' || $GET_status=='MANUAL' || $GET_status=='CANCELED'){
                        if($po_master->entry_by==$_SESSION['userid']){ ?>
                            <p align="center">
                                <button style="font-size:12px" type="submit" name="reprocess" id="reprocess" class="btn btn-danger" onclick='return window.confirm("Are you confirm to Re-process?");'>Re-process the Work Order</button>
                                <!--button style="float: right; margin-right: 1%; font-size:12px" type="submit" name="checked" id="checked" class="btn btn-primary" onclick='return window.confirm("Are you confirm?");'>Checked & Completed</button-->
                            </p>
                        <? } else { echo '<h6 style="text-align: center;color: red;  font-weight: bold"><i>This work order was created by another person. So you are not able to do anything here!!</i></h6>';
                        }} else {echo '<h6 style="text-align: center;color: red;  font-weight: bold"><i>This purchase has been checked !!</i></h6>';}?>
                </div>
            </div>
        </div>
    </form>
<?=$html->footer_content();mysqli_close($conn);?>