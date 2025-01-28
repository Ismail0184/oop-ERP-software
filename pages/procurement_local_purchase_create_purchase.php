<?php require_once 'support_file.php'; ?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Purchase Entry';
$now=time();
$unique='po_no';
$unique_field='po_id';
$table="purchase_master";
$table_details="purchase_invoice";
$page="procurement_local_purchase_create_purchase.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];

if(prevent_multi_submit()){

    if(isset($_POST['initiate']))
    {
        $_POST['section_id'] = @$_SESSION['sectionid'];
        $_POST['company_id'] = @$_SESSION['companyid'];
        $_POST['entry_by'] = @$_SESSION['userid'];
        $_POST['entry_at'] = date('Y-m-d H:s:i');
        $_SESSION['local_purchase_unique_id'] =@$_POST[$unique_field];
        $_POST['create_date']=date('Y-m-d');
        $crud->insert();
        $_SESSION['local_purchase_po_no'] =find_a_field(''.$table.'',''.$unique.'',''.$unique_field.'='.$_SESSION['local_purchase_unique_id']);
        unset($_POST);
    }

    $local_purchase_po_no = @$_SESSION['local_purchase_po_no'];

    if(isset($_POST['modify']))
    {
        $crud->update($unique);
        unset($_POST);
    }

    if(isset($_POST['add']))
    {  if($_POST['total_qty']>0) {
        $_POST['pkt_size']=find_a_field("item_info", "pack_size", "item_id='".$_POST['item_id']);
        $_POST['pkt_unit']=$_POST['total_unit'];
        $_POST['status']="UNCHECKED";
        $_POST['entry_by'] = $_SESSION['userid'];
        $_POST['entry_at'] = date('Y-m-d H:s:i');
        $crud = new crud($table_details);
        $crud->insert();
    }   unset($_POST);
    }



//for single FG Delete..................................
    $results="Select srd.*,i.* from ".$table_details." srd, item_info i  where
 srd.item_id=i.item_id and
 srd.po_no='".$local_purchase_po_no."' order by srd.id";
    $query=mysqli_query($conn, $results);
    while($row=mysqli_fetch_array($query)){
        $ids=$row['id'];
        if(isset($_POST['deletedata'.$ids]))
        {
            $del="DELETE FROM ".$table_details." WHERE id='$ids' and ".$unique."=".$local_purchase_po_no."";
            $del_item=mysqli_query($conn, $del);
            unset($_POST);
        }}

//for Delete..................................
    if(isset($_POST['cancel']))
    {   $crud = new crud($table_details);
        $condition =$unique."=".$local_purchase_po_no;
        $crud->delete_all($condition);
        $crud = new crud($table);
        $condition=$unique."=".$local_purchase_po_no;
        $crud->delete($condition);
        unset($_SESSION['local_purchase_po_no']);
        unset($local_purchase_po_no);
        unset($_SESSION['local_purchase_unique_id']);
        unset($_POST);
    }

    if(isset($_POST['confirmsave']))
    {
        $up_master="UPDATE ".$table." SET status='UNCHECKED' where ".$unique."='".$local_purchase_po_no."'";
        $update_table_master=mysqli_query($conn, $up_master);
        $up_details="UPDATE ".$table_details." SET status='UNCHECKED' where ".$unique."='".$local_purchase_po_no."'";
        $update_table_details=mysqli_query($conn, $up_details);
        unset($_SESSION['local_purchase_po_no']);
        unset($_SESSION['initiate_sr_documents']);
        unset($_POST);
    } // if insert posting
}
$local_purchase_po_no = @$_SESSION['local_purchase_po_no'];
$COUNT_details_data=find_a_field(''.$table_details.'','Count(id)',''.$unique.'='.$local_purchase_po_no.'');


$results="Select srd.*,i.* from purchase_invoice srd, item_info i  where
srd.item_id=i.item_id and
srd.po_no='".$local_purchase_po_no."' order by srd.id";

// data query..................................
if(isset($local_purchase_po_no))
{   $condition=$unique."=".$local_purchase_po_no;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

$warehouse_id = @$warehouse_id;
$po_date = @$po_date;
$po_id = @$po_id;
$po_subject = @$po_subject;
$po_details = @$po_details;
$depot_id = @$depot_id;
$remarks = @$remarks;
?>
<?php require_once 'header_content.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
    <style>
        input[type=text]{
            font-size: 11px;
        }
    </style>
<?php require_once 'body_content.php'; ?>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?=$title?></h2>
                <a target="_new" style="float: right" class="btn btn-sm btn-default"  href="procurement_local_purchase_view_purchase.php">
                    <i class="fa fa-plus-circle"></i> <span class="language" style="color:#000; font-size: 11px">Purchase View</span>
                </a>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form  name="addem" id="addem" style="font-size: 11px" class="form-horizontal form-label-left" method="post">
                    <table style="width:100%">
                        <tr>
                            <th style="width:10%">Purchase ID <span class="required text-danger">*</span></th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%; text-align:center">
                                <input type="text" id="po_no" style="width:20%" name="po_no" value="<?=$local_purchase_po_no;?>" readonly class="form-control col-md-7 col-xs-12" >
                                <input type="text" id="po_id" style="width:70%" name="po_id" value="<?=($po_id!='')? $po_id : automatic_number_generate("","purchase_master","po_id","create_date='".date('Y-m-d')."'",""); ?>" class="form-control col-md-7 col-xs-12"  readonly >
                            </td>

                            <th style="width:10%">Purchase Date <span class="required text-danger">*</span></th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%; text-align:center">
                                <input type="date" required="required" style="width:90%;font-size:11px" MAX="<?=date('Y-m-d')?>" name="do_date" value="<?=($po_date>0)?  $po_date : date('Y-m-d') ;?>" class="form-control col-md-7 col-xs-12" ></td>

                            <th style="width:10%">Received Destination <span class="required text-danger">*</span></th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%;">
                                <select class="select2_single form-control"  required style="width: 90%;" name="warehouse_id">
                                    <option></option>
                                    <?=advance_foreign_relation(check_plant_permission($_SESSION['userid']),$warehouse_id);?>
                                </select>
                            </td>
                        </tr>
                        <tr><td style="height:5px"></td></tr>
                        <tr>
                            <th style="width:10%"> Purchase From <span class="required text-danger">*</span></th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%;">
                                <input type="hidden" id="last-name" name="vendor_id" style="width:90%" value="<?=$remarks;?>" class="form-control col-md-7 col-xs-12">
                                <input type="text" id="last-name" name="po_subject" style="width:90%" value="<?=$po_subject;?>" class="form-control col-md-7 col-xs-12">
                            </td>
                            <th>Challan Number</th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%;">
                                <input type="text" id="last-name" name="po_details" style="width:90%" value="<?=$po_details;?>" class="form-control col-md-7 col-xs-12">
                            </td>
                            <th>Remarks</th>
                            <th style="width:1%; text-align:center">:</th>
                            <td style="width:20%;">
                                <input type="text" id="last-name" name="remarks" style="width:90%" value="<?=$remarks;?>" class="form-control col-md-7 col-xs-12">
                            </td>
                        </tr>

                        <tr><td colspan="9"> <div class="form-group" style="margin-left:40%">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <?php if($local_purchase_po_no){  ?>
                                            <button type="submit" style="font-size: 12px; margin-top:10px" name="modify" id="modify" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary"><i class="fa fa-edit"></i> Update Purchase Info</button>
                                        <?php   } else {?>
                                            <button type="submit" style="font-size: 12px; margin-top:10px" name="initiate" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary">Initiate & Proceed <i class="fa fa-step-forward"></i> </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>


<?php if($local_purchase_po_no){  ?>

    <form action="" name="addem" id="addem" class="form-horizontal form-label-left" method="post">
        <? require_once 'support_html.php';?>
        <input type="hidden" name="<?=$unique;?>" id="<?=$unique;?>" value="<?=$$unique;?>" >
        <input type="hidden" name="po_date"  value="<?=$po_date;?>">
        <input type="hidden" name="vendor_id"  value="<?=$vendor_id?>">
        <input type="hidden" name="warehouse_id" id="depot_id" value="<?=$warehouse_id?>">

        <table align="center" style="width:98%; font-size: 11px" class="table table-striped table-bordered">
            <thead>
            <tr class="bg-primary text-white">
                <th style="text-align: center; vertical-align:middle">Item / Goods</th>
                <th style="text-align: center; vertical-align:middle">Batch</th>
                <th style="text-align: center; vertical-align:middle">Expiry Date</th>
                <th style="text-align: center; vertical-align:middle">Sales Qty</th>
                <th style="text-align: center; vertical-align:middle">Unit Price</th>
                <th style="text-align: center; vertical-align:middle">Total Qty</th>
                <th style="text-align: center; vertical-align:middle">Unit Amount</th>
                <th style="text-align: center; vertical-align:middle">Action</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td style="width:20%" align="center">
                    <select class="select2_single form-control" required name="item_id" id="item_id" style="width:99%;font-size: 11px">
                        <option></option>
                        <?=advance_foreign_relation(find_all_item($product_nature="'Salable','Both'"),'');?>
                    </select>
                </td>
                <td style="width:8%" align="center">
                    <input type="text"  value=""    name="cogs_rate"  class="form-control col-md-7 col-xs-12" >
                </td>
                <td style="width:8%" align="center">
                    <input align="center" type="date" id="expiry_date" style="width:100%; height:37px;   text-align:center;font-size:11px" value="" name="expiry_date"  class="form-control col-md-7 col-xs-12" >
                </td>

                <td style="width:11%" align="center">
                    <input type="text" id="total_unit" style="width:100%; height:37px; font-weight:bold; text-align:center"  required="required"  name="total_unit" class="form-control col-md-7 col-xs-12" class='total_unit' autocomplete="off" >
                </td>
                <td style="width:8%" align="center">
                    <input align="center" type="text" id="unit_price" style="width:100%; height:37px;   text-align:center"  required   name="unit_price"  class="form-control col-md-7 col-xs-12" class='unit_price' >
                </td>
                <td style="width:8%" align="center">
                    <input align="center" type="text" id="total_qty" style="width:100%; height:37px;   text-align:center"  readonly   name="total_qty" class="form-control col-md-7 col-xs-12"  class='total_qty' >
                </td>
                <td style="width:10%" align="center">
                    <input type="text" id="total_amt" style="width:100%; height:37px; font-weight:bold; text-align:center" readonly  name="total_amt" class="form-control col-md-7 col-xs-12" autocomplete="off" class='total_amt' ></td>
                <td align="center" style="width:5%">
                    <button type="submit" class="btn btn-primary" style="font-size: 12px;" name="add" id="add">Add</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    <form id="ismail" name="ismail"  method="post" style="font-size: 11px"  class="form-horizontal form-label-left">
        <? require_once 'support_html.php';?>
        <input type="hidden" name="<?=$unique;?>" id="<?=$unique;?>" value="<?=$$unique;?>" >
        <input type="hidden" name="po_date" id="pr_date" value="<?=$po_date;?>">
        <input type="hidden" name="warehouse_id" value="<?=$warehouse_id?>">
        <input type="hidden" name="vendor_id" value="<?=$vendor_id?>">
        <?php if($COUNT_details_data>0) { ?>
            <table align="center" class="table table-striped table-bordered" style="width:98%">
                <thead>
                <tr style="background-color: bisque">
                    <th style="vertical-align:middle">SL</th>
                    <th style="vertical-align:middle">Code</th>
                    <th style="vertical-align:middle">Finish Goods</th>
                    <th style="width:5%; text-align:center; vertical-align:middle">UOM</th>
                    <th style="text-align:center; vertical-align:middle">Discount</th>
                    <th style="text-align:center; vertical-align:middle">Unit Price</th>
                    <th style="text-align:center; vertical-align:middle">Total Qty</th>
                    <th style="text-align:center; vertical-align:middle">Unit Amount</th>
                    <th style="text-align:center; vertical-align:middle">Batch</th>
                    <th style="text-align:center; vertical-align:middle">Expiry Date</th>
                    <th style="text-align:center; vertical-align:middle">COGS Rate</th>
                    <th style="text-align:center; vertical-align:middle">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0; $ttotal_unit =0;$tfree_qty=0;$ttotal_qty=0;$tdiscount=0;$ttotal_amt=0;
                $query=mysqli_query($conn, $results); $i = 0;
                while($row=mysqli_fetch_array($query)){
                    $ids=$row['id'];
                    ?>
                    <tr>
                        <td style="width:3%; vertical-align:middle"><?=$i=$i+1?></td>
                        <td style="vertical-align:middle"><?=$row['finish_goods_code'];?></td>
                        <td style="vertical-align:middle; width: 25%"><?=$row['item_name'];?></td>
                        <td style="vertical-align:middle; text-align:center"><?=$row['unit_name'];?></td>
                        <td align="center" style=" text-align:center; vertical-align:middle"><?=$row['total_unit'];?></td>
                        <td align="center" style=" text-align:center; vertical-align:middle"><?=$row['free_qty'];?></td>
                        <td align="center" style=" text-align:right; vertical-align:middle"><?=$row['discount'];?></td>
                        <td align="center" style=" text-align:right; vertical-align:middle"><?=$row['unit_price']; ?></td>
                        <td align="center" style=" text-align:center; vertical-align:middle"><?=$row['total_qty']; ?></td>
                        <td align="center" style="text-align:right; vertical-align:middle"><?=number_format($row['total_amt'],2);?></td>
                        <td align="center" style=" text-align:center;vertical-align:middle"><?=$row['batch']; ?></td>
                        <td align="center" style=" text-align:center;vertical-align:middle"><?=$row['expiry_date']; ?></td>
                        <td align="center" style=" text-align:center;vertical-align:middle"><?=$row['cogs_rate']; ?></td>
                        <td align="center" style="vertical-align:middle; vertical-align:middle">
                            <button type="submit" name="deletedata<?=$ids;?>" id="deletedata<?=$ids;?>" style="background-color:transparent; border:none" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you sure you want to Delete?");'><img src="/../assets/images/delete.png" style="width:15px;  height:15px"></button>
                        </td>
                    </tr>
                    <?php  $ttotal_unit=$ttotal_unit+$row['total_unit'];
                    $tfree_qty=$tfree_qty+$row['free_qty'];
                    $ttotal_qty=$ttotal_qty+$row['total_qty'];
                    $tdiscount=$tdiscount+$row['discount'];
                    $ttotal_amt=$ttotal_amt+$row['total_amt'];  } ?>
                </tbody>
                <tr style="font-weight: bold">
                    <td colspan="4" style="font-weight:bold; font-size:11px" align="right">Total Sales Return</td>
                    <td style="text-align:center"><?=$ttotal_unit;?></td>
                    <td style="text-align:center"><?=$tfree_qty;?></td>
                    <td style="text-align:right"><?=number_format($tdiscount,2);?></td>
                    <td align="center" ></td>
                    <td align="center" ><?=$ttotal_qty;?></td>
                    <td align="right" ><?=number_format($ttotal_amt,2);?></td>
                    <td align="center" ></td>
                </tr>
            </table>
        <?php } ?>
        <button type="submit" style="float: left; font-size: 12px; margin-left: 1%" name="cancel" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you confirm to the Production Deleted?");' class="btn btn-danger">Delete Sales Return </button>
        <?php if($COUNT_details_data>0) { ?>
            <button type="submit" style="float: right; margin-right: 1%; font-size: 12px" onclick='return window.confirm("Are you want to Finished?");' name="confirmsave" class="btn btn-success">Confirm and Finish Sales Return </button>
        <?php } ?>
    </form>
<?php } ?>
    <script>
        $(function(){
            $('#total_unit, #free_qty').keyup(function(){
                var total_unit = parseFloat($('#total_unit').val()) || 0;
                var free_qty = parseFloat($('#free_qty').val()) || 0;
                $('#total_qty').val((total_unit + free_qty).toFixed(2));
            });
        });
    </script>
    <script>
        $(function(){
            $('#total_unit, #unit_price').keyup(function(){
                var total_unit = parseFloat($('#total_unit').val()) || 0;
                var unit_price = parseFloat($('#unit_price').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;
                $('#total_amt').val((total_unit * unit_price) - (discount).toFixed(2));
            });
        });
    </script>
<?=$html->footer_content();?>