<?php require_once 'support_file.php';

$po_no = $_REQUEST['po_no'];
if(isset($_POST['cash_discount']))
{
    $po_no = $_POST['po_no'];
    $cash_discount = $_POST['cash_discount'];
    $ssql='update purchase_master set cash_discount="'.$_POST['cash_discount'].'" where po_no="'.$po_no.'"';
    mysqli_query($conn, $ssql);

} else {
    $cash_discount = 0;
}



$sql1="select * from purchase_master where   po_no='$po_no'";
$data=mysqli_fetch_object(mysqli_query($conn, $sql1));
$vendor=find_all_field('vendor','','vendor_id='.$data->vendor_id );
$whouse=find_all_field('warehouse','address','warehouse_id='.$data->warehouse_id);
$sql_proj = "select * from project_info where 1";
$datasks = mysqli_fetch_object(mysqli_query($conn, $sql_proj));

//$proj_info = find_all_field('project_info','proj_name','proj_id='.$data->proj_id);
$bd_style=$data->po_date;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            line-height: 1.5;
        }

        /* Container */
        .work-order {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0073e6;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            color: #0073e6;
            margin: 0;
        }

        .order-meta {
            text-align: right;
            font-size: 14px;
        }

        /* Sections */
        section {
            margin-bottom: 20px;
        }

        section h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        /* Customer Details */
        .customer-details p {
            margin: 5px 0;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background: #f4f4f4;
            font-weight: bold;
            color: #333;
        }

        tfoot td {
            font-weight: bold;
            text-align: right;
        }

        tfoot .text-right {
            text-align: left;
        }

        /* Notes */
        .notes {
            font-size: 14px;
            color: #555;
            background: #f9f9f9;
            padding: 10px;
            border-left: 4px solid #0073e6;
            border-radius: 4px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }

    </style>
</head>
<body>
<div class="work-order">
    <!-- Header -->
    <header class="header">
        <div class="company-info">
            <h1>Work Order</h1>
            <p style="text-transform: uppercase; font-weight: bold"><?=$_SESSION['company_name']?></p>
            <p><?=$_SESSION['company_address']?></p>
            <p>Phone: +88 022-22260178 | Email: info@icpbd.com</p>
        </div>
        <div class="order-meta">
            <p><strong>Order Number:</strong> WO-<?=$po_no?></p>
            <p><strong>Issue Date:</strong> 2024-12-10</p>
            <p><strong>Due Date:</strong> 2024-12-17</p>
            <p><strong>Note:</strong> <?=$data->po_details?></p>
        </div>
    </header>

    <!-- Customer Information -->
    <section class="customer-section">
        <h2>Customer Information</h2>
        <div class="customer-details">
            <?php if (!empty($vendor->vendor_name)): ?>
                <p><strong>Name:</strong> <?=$vendor->vendor_name;?></p>
            <?php endif; ?>
            <?php if (!empty($vendor->address)): ?>
                <p><strong>Address:</strong> <?=$vendor->address;?></p>
            <?php endif; ?>
            <?php if (!empty($vendor->contact_person_name)): ?>
                <p><strong>Contact Person:</strong> <?=$vendor->contact_person_name;?>, <?=$vendor->contact_person_designation;?></p>
            <?php endif; ?>
            <?php if (!empty($vendor->email)): ?>
                <p><strong>Email:</strong> <?=$vendor->email;?></p>
            <?php endif; ?>
            <?php if (!empty($vendor->contact_no)): ?>
                <p><strong>Phone:</strong> <?=$vendor->contact_no;?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Work Details -->
    <section class="work-details">
        <h2>Work Order Details</h2>
        <table class="work-items" style="font-size: 12px">
            <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=0;
            $final_amt=0;
            $pi=0;
            $total=0;
            $sql2="select p.*,i.*,i.unit_name from purchase_invoice p, item_info i where p.item_id not in ('1096000100010313') and p.po_no='$po_no' and 
p.item_id=i.item_id
";
            $data2=mysqli_query($conn, $sql2);
            $total_item_value = 0;
            while($info=mysqli_fetch_object($data2)){
            $pi++;
            $amount=$info->qty*$info->rate;
            $total=$total+($info->amount);
            $total_item_value=$total_item_value+($info->qty*$info->rate);

            $sl=$pi;

            $item=find_a_field('item_info','concat(item_name,item_description)','item_id='.$info->item_id);
            $item_details=find_a_field('purchase_invoice','item_details','id='.$info->id);
            $fg_code = find_a_field('item_info','finish_goods_code','item_id='.$info->item_id);
            $qty=$info->qty;

            $unit_name=$info->unit_name;

            $rate=$info->rate;
            $item_del_date=$info->item_del_date;
            $disc=$info->disc;

            ?>
            <tr>
                <td><?=$i=$i+1;?></td>
                <td><?=$fg_code?> - <?=$item?> # <?=$item_details?></td>
                <td><?=$unit_name?></td>
                <td style="text-align: center"><?=$qty?></td>
                <td style="text-align: right"><?=number_format($rate,2)?></td>
                <td style="text-align: right"><?=number_format($amount,2)?></td>
            </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5" class="text-right">Subtotal</td>
                <td><?=number_format($total,2);?></td>
            </tr>
            <?php if ($data->tax_ait>0): ?>
            <tr>
                <td colspan="5" class="text-right">Tax (<?=$data->tax_ait?>%)</td>
                <td><?=number_format((($total*15)/100),2)?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="5" class="text-right"><strong>Total</strong></td>
                <td><strong>$2,420.00</strong></td>
            </tr>
            </tfoot>
        </table>
    </section>

    <!-- Additional Notes -->
    <section class="notes">
        <h2>Terms & Conditions</h2>
        <p><strong>Delivery Destination: </strong> <?=$whouse->warehouse_name.', '.$whouse->address?></p>
        <p><strong>Contact Person: </strong> <?=$whouse->warehouse_company?>. Mobile No: <?=$whouse->contact_no?></p>
        <p><strong>Delivery Instruction: </strong> Delivery Should be Reached at Destination Point within 5PM.</p>
        <p><strong>Payment: </strong> <?=$cehckstatus=find_a_field('purchase_master','payment_terms','po_no="'.$_GET['po_no'].'"');?></p>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>Prepared by: [Your Name]</p>
        <p>Signature: ________________________________</p>
    </footer>
</div>
</body>
</html>
