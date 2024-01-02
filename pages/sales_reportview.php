<?php
require_once 'support_file.php';
$title='Report';
$from_date=date('Y-m-d' , strtotime(@$_POST['f_date']));
$to_date=date('Y-m-d' , strtotime(@$_POST['t_date']));




$warehouseid=@$_POST['warehouse_id'];
$_SESSION['company_name']=getSVALUE('company','company_name','where company_id="'.$_SESSION['companyid'].'"');
$sectionid=$_SESSION['sectionid'];
$companyid=$_SESSION['companyid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
} else {
    $sec_com_connection=" and j.section_id='".$sectionid."' and j.company_id='".$companyid."'";
}

if(!empty($_POST['order_by'])) $order_by_GET=$_POST['order_by'];
if(isset($order_by_GET))				{$order_by=' order by '.$order_by_GET;} else {$order_by='';}
if(!empty($_POST['order_by']) && !empty($_POST['sort'])) $order_by_GET=$_POST['order_by'];
if(isset($order_by_GET))				{$order_by=' order by '.$order_by_GET.' '.$_POST['sort'].'';} else {$order_by='';}
$report_id = @$_POST['report_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript">
        function hide()
        {
            document.getElementById("pr").style.display = "none";
        }
    </script>
    <style>
        #customers {font-family: "Gill Sans", sans-serif;}
        #customers td {}
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
    </style>
</head>
<body style="font-family: "Gill Sans", sans-serif;">


<div id="pr" style="margin-left:48%">
    <div align="left">
        <form id="form1" name="form1" method="post" action="">
            <p><input name="button" type="button" onclick="hide();window.print();" value="Print" /></p>
        </form>
    </div>
</div>







<?php if ($report_id=='9001001'):?>
    <title>Sales Invoice List</title>
    <h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
    <h4 align="center" style="margin-top:-15px">Sales Invoice List</h4>
    <?php if($_POST['dealer_code']){?>
        <h5 align="center" style="margin-top:-15px">Dealer : <?=find_a_field('dealer_info','dealer_name_e','dealer_code='.$_POST['dealer_code'].'')?></h5>
    <?php } ?>
    <?php if($_POST['warehouse_id']){?>
        <h5 align="center" style="margin-top:-15px">Warehouse : <?=find_a_field('warehouse','warehouse_name','warehouse_id='.$_POST['warehouse_id'].'')?></h5>
    <?php } ?>
    <h5 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h5>
    <?php
    $datecon=' and m.do_date between  "'.$from_date.'" and "'.$to_date.'"';
    if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
    if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;}
    if($_POST['dealer_code']>0) 			 $dealer_code=$_POST['dealer_code'];
    if(isset($dealer_code))				{$dealer_code_CON=' and m.dealer_code='.$dealer_code;} else {$dealer_code_CON='';}
    $sql="select
distinct c.chalan_no,

c.chalan_date,
m.do_no,
m.do_date,
d.dealer_code as dealercode,
d.region,
d.area_code,
d.territory,
d.town_code,
p.PBI_NAME as tsm ,
concat(d.dealer_name_e) as dealer_name,
a.AREA_NAME as area,
a.ZONE_ID as Zonecode,
a.PBI_ID,
d.team_name as team,
w.warehouse_name as depot,
d.product_group as grp,
c.driver_name,
m.cash_discount commission,
m.commission_amount as comissionamount,
SUM(c.total_amt)as invoice_amount,
(SELECT SUM(total_amt) from sale_do_details where do_no=c.do_no and item_id=1096000100010312) as discount
from
sale_do_master m,
sale_do_chalan c,
dealer_info d ,
warehouse w,
area a,
personnel_basic_info p
where
a.AREA_CODE=d.area_code
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_type  in ('sales','') and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
c.item_id not in ('1096000100010312') and
a.PBI_ID=p.PBI_ID".$warehouse_id_CON.$datecon.$dealer_code_CON."
group by c.do_no
order by c.do_no";
    $query = mysqli_query($conn, $sql); ?>
    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse;">
        <thead>
        <p style="width:95%; text-align:right; font-size:10px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; background-color:#FFCCFF; font-size:11px">
            <th style="border: solid 1px #999; padding:2px">S/L</th>
            <th style="border: solid 1px #999; padding:2px">Chalan No</th>
            <th style="border: solid 1px #999; padding:2px">Chalan Date</th>
            <th style="border: solid 1px #999; padding:2px">Do No</th>
            <th style="border: solid 1px #999; padding:2px">Do Date</th>
            <th style="border: solid 1px #999; padding:2px">Dealer Name</th>
            <th style="border: solid 1px #999; padding:2px">Territory</th>
            <th style="border: solid 1px #999; padding:2px">Incharge Person</th>
            <th style="border: solid 1px #999; padding:2px">Depot</th>
            <th style="border: solid 1px #999; padding:2px">Invoice Amount</th>
            <th style="border: solid 1px #999; padding:2px">Discount</th>
            <th style="border: solid 1px #999; padding:2px">Commission</th>
            <th style="border: solid 1px #999; padding:2px">Receivable Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $s=0;
        $discounttotal=0;
        $total_invoice_amount=0;
        $totalsaleafterdiscounts=0;
        $totalcomissionamount = 0;
        while($data=mysqli_fetch_object($query)){$s++; ?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
                <td style="border: solid 1px #999; text-align:center"><?=$s?></td>
                <td style="border: solid 1px #999; text-align:center"><a href="chalan_view.php?v_no=<?=$data->chalan_no?>" target="_blank"><?=$data->chalan_no?></a></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->chalan_date?></td>
                <td style="border: solid 1px #999; text-align:center"><a href="chalan_bill_distributors.php?do_no=<?=$data->do_no?>" target="_blank"><?=$data->do_no;?></a></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->do_date?></td>
                <td style="border: solid 1px #999; text-align:left"><?=$data->dealer_name;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->area;?></td>
                <td style="border: solid 1px #999; text-align:left"><?=$data->tsm;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->depot;?></td>
                <td style="border: solid 1px #999; text-align:right"><?=number_format($data->invoice_amount,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><? if(substr($data->discount,1)>0) echo  number_format(substr($data->discount,1),2); else echo'-';?></td>
                <td style="border: solid 1px #999; text-align:right"><? if($data->comissionamount>0) echo  number_format($data->comissionamount,2); else echo'-';?></td>
                <td style="border: solid 1px #999; text-align:right"><?=number_format(($data->invoice_amount+$data->comissionamount)+$data->discount,2)?></td>
            </tr>

            <?php
            $discounts=substr($data->discount,1);
            $discounttotal=$discounttotal+$discounts;
            $total_invoice_amount=$total_invoice_amount+$data->invoice_amount;
            $totalsaleafterdiscount=($total_invoice_amount-($discounttotal+$data->comissionamount));

            $totalsaleafterdiscounts=$totalsaleafterdiscounts+$totalsaleafterdiscount;
            $totalcomissionamount=$totalcomissionamount+$data->comissionamount;

        } ?>
        <tr style="font-size:11px; font-weight:bold">
            <td colspan="9" style="border: solid 1px #999; text-align:right;  padding:2px">Total</td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($discounttotal,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalcomissionamount,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount-($discounttotal+$totalcomissionamount),2);?></td>
        </tr></tbody>
    </table>

<?php elseif ($report_id=='9001002'):?>
    <h2 align="center"><?=$_SESSION['company_name']?></h2>
    <h4 align="center" style="margin-top:-10px">Sales Summery</h4>
    <?php if($_POST['item_id']){?>
        <h5 align="center" style="margin-top:-10px">Item Name:  <?=find_a_field('item_info','item_name','item_id='.$_POST['item_id'].'');?></h5>
    <?php } ?>
    <h5 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h5>
    <table id="customers" align="center"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
        <thead>
        <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color:#FFCCFF">
            <th style="border: solid 1px #999; padding:2px">SL</th>
            <th style="border: solid 1px #999; padding:2px; %">T.ID</th>
            <th style="border: solid 1px #999; padding:2px; ">Depot</th>
            <th style="border: solid 1px #999; padding:2px; ">Code</th>
            <th style="border: solid 1px #999; padding:2px; ">Dealder Name</th>
            <th style="border: solid 1px #999; padding:2px">D.Type</th>
            <th style="border: solid 1px #999; padding:2px; ">DO</th>
            <th style="border: solid 1px #999; padding:2px;">DO Date</th>
            <th style="border: solid 1px #999; padding:2px">DO.Type</th>
            <th style="border: solid 1px #999; padding:2px">Territory</th>
            <th style="border: solid 1px #999; padding:2px">Region</th>
            <th style="border: solid 1px #999; padding:2px">FG Code</th>
            <th style="border: solid 1px #999; padding:2px">FG Description</th>
            <th style="border: solid 1px #999; padding:2px">UOM</th>
            <th style="border: solid 1px #999; padding:2px">Pack Size</th>
            <th style="border: solid 1px #999; padding:2px">Unit Price</th>
            <th style="border: solid 1px #999; padding:2px">Qty</th>
            <th style="border: solid 1px #999; padding:2px">Amount</th>
            <th style="border: solid 1px #999; padding:2px">Item For</th>
        </tr></thead>
        <tbody>
        <?php
        if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
        if(isset($item_id))				{$item_con=' and sd.item_id='.$item_id;}
        if($_POST['do_no']>0) 					$do_no=$_POST['do_no'];
        if(isset($do_no))				{$do_no_con=' and sd.do_no='.$do_no;}
        $datecon=' and sd.do_date between  "'.$from_date.'" and "'.$to_date.'"';
        $result='Select
				sd.*,
				d.dealer_custom_code,
				d.dealer_name_e,
				d.dealer_type,
				w.warehouse_name,
				a.AREA_NAME,
				b.BRANCH_NAME,
				i.item_id as itemid,
				i.finish_goods_code as FGCODE,
				i.item_name as FGdescription,
				i.pack_unit as UOM,
				i.pack_size as psize
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i
				where
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				d.area_code=a.AREA_CODE  '.$datecon.$item_con.$do_no_con.'
				order by sd.id DESC';
        $query2 = mysqli_query($conn, $result);
        while($data=mysqli_fetch_object($query2)){
            $i=$i+1; ?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                <td style="border: solid 1px #999; text-align:center"><?php echo $i; ?></td>
                <td style="border: solid 1px #999; text-align:center"><?php echo $data->id; ?></td>
                <td style="border: solid 1px #999; text-align:left"><?php echo $data->warehouse_name; ?></td>
                <td style="border: solid 1px #999; text-align:left"><?php echo $data->dealer_custom_code; ?></td>
                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->dealer_name_e; ?></td>
                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->dealer_type; ?></td>
                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->do_no; ?></td>
                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->do_date; ?></td>
                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->do_type; ?></td>
                <td style="border: solid 1px #999; padding:5px"><?=$data->AREA_NAME;?></td>
                <td style="border: solid 1px #999; text-align:left; padding:2px"><?=$data->BRANCH_NAME;?></td>
                <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->FGCODE;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->FGdescription;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->psize;?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($data->unit_price,2);?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->total_unit;?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=($data->total_amt!=0)? number_format($data->total_amt,2) : '-';?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?php if($data->total_amt=="0.00") {echo 'Free';} else echo 'Sales';;?></td>
            </tr>
            <?php
            $total_sales_amount=$total_sales_amount+$data->total_amt;
            $total_do=$data->total_do;
        }
        $toatl_sales_reguler=find_a_field('sale_do_details','SUM(total_amt)','do_type in ("","sales") and do_date between "'.$from_date.'" and "'.$to_date.'" and dealer_type not in ("export") ');
        $toatl_sales_export=find_a_field('sale_do_details','SUM(total_amt)','do_type in ("","sales") and do_date between "'.$from_date.'" and "'.$to_date.'" and dealer_type in ("export")')
        ?>
        <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
            <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Local Sales in Amount  = </td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales_reguler,2);?></td>
            <td style="border: solid 1px #999; padding:2px; "></td>
        </tr>
        <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
            <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Export Sales in Amount  = </td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales_export,2);?></td>
            <td style="border: solid 1px #999; padding:2px; "></td>
        </tr>
        <tr style="border: solid 1px #999; font-size:12px; font-weight:normal">
            <th style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Total Sales in Amount  = </th>
            <th style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales_reguler+$toatl_sales_export,2);?></th>
            <th style="border: solid 1px #999; padding:2px; "></th>
        </tr>
        <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
            <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Total (sample, gift and others) = </td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($total_sales_amount-$toatl_sales,2);?></td>
            <td style="border: solid 1px #999; padding:2px; "></td>
        </tr>
        <tr style="border: solid 1px #999; font-size:12px; font-weight:normal">
            <th style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Grand Total  = </th>
            <th style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($total_sales_amount,2);?></th>
            <th style="border: solid 1px #999; padding:2px; "></th>
        </tr>
        </tbody>
    </table>

<?php elseif ($report_id=='9004001'):
    ?>
    <style>
        #customers {
            font-family: "Gill Sans", sans-serif;
        }
        #customers td {
        }
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #FFCCFF;}
        td{
            text-align: center;

        }
    </style>
    <title>Sales Info Master | <?=$_SESSION['company_name'];?></title>
    <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
    <p align="center" style="margin-top:-18px; font-size: 15px">Sales Info Master</p>
    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
        <thead>
        <p style="width:95%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #FFCCFF">
            <th style="border: solid 1px #999; padding:2px">SL</th>
            <th style="border: solid 1px #999; padding:2px; width:5%">Region</th>
            <th style="border: solid 1px #999; padding:2px; width:10%">Territory</th>
            <th style="border: solid 1px #999; padding:2px; width:8%">Name of TSM</th>
            <th style="border: solid 1px #999; padding:2px; width: 15%">Super Distributor</th>
            <th style="border: solid 1px #999; padding:2px">Sub Distributor</th>
            <th style="border: solid 1px #999; padding:2px">SO Code</th>
            <th style="border: solid 1px #999; padding:2px; width: 10%">Name of SO</th>
            <th style="border: solid 1px #999; padding:2px; width: 10%">NID</th>
            <th style="border: solid 1px #999; padding:2px; width: 10%">Type</th>
        </tr></thead>
        <tbody>
        <? $i=0;	$res=mysqli_query($conn, 'select p.PBI_ID,p.so_type,p.PBI_ID_UNIQUE as SO_code,p.ESSENTIAL_NATIONAL_ID as nid,p.PBI_NAME, p.PBI_JOB_STATUS as status,e.*,
								(SELECT PBI_NAME from personnel_basic_info where PBI_ID=p.tsm) as tsm,
								(select sub_dealer_name_e from sub_db_info where sub_db_code=p.sub_db_code)	as sub_dealer,
								  a.AREA_NAME as territory,b.BRANCH_NAME as region,
								 (select dealer_name_e from dealer_info where dealer_code=(select super_dealer_code from sub_db_info where sub_db_code=p.sub_db_code)) as dealer
								from
								personnel_basic_info p ,
								essential_info e,
								area a,
								branch b
								where
								p.PBI_ID=e.PBI_ID and
								p.PBI_JOB_STATUS in ("In Service") and
								a.PBI_ID=p.tsm and
								a.Region_code=b.BRANCH_ID and
								p.PBI_DESIGNATION like "60" group by p.PBI_ID order by p.tsm,dealer,sub_dealer,p.PBI_ID');
        while($PBI_ROW=mysqli_fetch_object($res)){?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$i=$i+1;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->region;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->territory;?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->tsm?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->dealer?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->sub_dealer?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->SO_code;?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->PBI_NAME;?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->nid;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$PBI_ROW->so_type;?></td>
            </tr>
        <?php } ?>
        </tbody></table>


<?php elseif ($report_id=='9004002'):
  $sql="SELECT d.dealer_code,d.dealer_code,d.dealer_custom_code,d.account_code as ledger_id,d.dealer_name_e as customer_name,t.town_name as town,a.AREA_NAME as territory,b.BRANCH_NAME as region,d.propritor_name_e as propritor_name,d.contact_person,d.contact_number,d.address_e as address,d.national_id,d.TIN_BIN as 'TIN / BIN'  from dealer_info d, town t, area a, branch b WHERE
d.town_code=t.town_code and a.AREA_CODE=d.area_code and b.BRANCH_ID=d.region and
   d.canceled in ('".$_POST['canceled']."') ".$order_by.""; echo reportview($sql,'Customer Report','98','','','');
  ?>


<?php elseif ($report_id=='9003002'):?>
<title>Daily IMS Report</title>
    <?php
    $datecon=' and d.ims_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
    if($_POST['tsm']>0) 				$tsm=$_POST['tsm'];
    if(isset($tsm)) 				{$tsm_con=' and d.TSM_PBI_ID='.$tsm;}
    $query='Select d.uid,d.ims_no,d.ims_date,p.PBI_NAME as tsm_name,a.AREA_NAME as territory,(select dealer_name_e from dealer_info where dealer_code=(select super_dealer_code from sub_db_info where sub_db_code=p2.sub_db_code)) as "DB / Super DB",
       p2.PBI_ID_UNIQUE as so_code,p2.PBI_NAME as so_name,
       i.finish_goods_code as FG_code,i.item_name as FG_Description,i.unit_name as unit,d.total_unit_today as IMS_Qty,d.unit_price as Effect_TP,d.total_amt_ims as amount,m.region as Workday_Count,m.total_call as "call",m.productive_call,m.total_line as TLS
from
ims_master m,
ims_details d,
item_info i,
personnel_basic_info p,
personnel_basic_info p2,
area a

  where
  m.UID=d.UID and
  m.UID>0 and
  d.item_id=i.item_id and
  d.total_unit_today>0 and
  d.PBI_ID=p2.PBI_ID and
  d.TSM_PBI_ID=p.PBI_ID and
  d.TSM_PBI_ID=a.PBI_ID'.$datecon.$tsm_con.' group by d.id order by d.ims_date,p.PBI_NAME,p2.PBI_NAME,d.ims_no,d.item_id asc';
    echo reportview($query,'Daily IMS Report','99','','','');
    ?>




<?php elseif ($report_id=='9003001'):?>

    <style>
        #customers {
            font-family: "Gill Sans", sans-serif;
        }
        #customers td {
        }
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #ddd;}
        td{text-align: center; }
    </style>


<title><?=$_SESSION['company_name'];?> | Monthly Target Report</title>
    <h2 align="center"><?=$_SESSION['company_name'];?></h2>
    <h4 align="center" style="margin-top:-13px">Monthly Target Report</h4>
<h6 align="center" style="margin-top:-13px">For the month of <?=find_a_field("monthname","monthfullName","month=".$_POST['month']."");?>, <?=$_POST[year];?></h6>

<table align="center" id="customers" style="width:90%; border: solid 1px #999; border-collapse:collapse; ">
    <thead>
    <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
        echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
    <thead>
    <tr bgcolor="#FFCCFF" style="border: solid 1px #999;font-weight:bold; font-size:11px">
        <th style="border: solid 1px #999; padding:2px;">Sl</th>
        <th style="border: solid 1px #999; padding:2px;">TSM Name</th>
        <th style="border: solid 1px #999; padding:2px;">Tarritory</th>
        <th style="border: solid 1px #999; padding:2px;">DB / Super DB</th>
        <th style="border: solid 1px #999; padding:2px;">SO Code</th>
        <th style="border: solid 1px #999; padding:2px;">SO Name</th>
        <th style="border: solid 1px #999; padding:2px;">FG Code</th>
        <th style="border: solid 1px #999; padding:2px;">FG Description</th>
        <th style="border: solid 1px #999; padding:2px;">Unit</th>
        <th style="border: solid 1px #999; padding:2px;">Target Qty</th>
        <th style="border: solid 1px #999; padding:2px;">Target Amount</th>
       </tr>
    </thead>

    <tbody>

 <?php
 $query=mysqli_query($conn, "Select d.*,i.*,p.PBI_NAME as tsm_name,p2.PBI_NAME as so_name,p2.PBI_ID_UNIQUE as socode,a.AREA_NAME as territory,
 (select dealer_name_e from dealer_info where dealer_code=(select super_dealer_code from sub_db_info where sub_db_code=p2.sub_db_code)) as dealer
 from
ims_monthly_target_details d,
item_info i,
personnel_basic_info p,
personnel_basic_info p2,
area a

  where
  d.item_id=i.item_id and
  d.amount not in ('0') and
  d.PBI_ID=p2.PBI_ID and
  d.TSM_PBI_ID=p.PBI_ID and
  d.TSM_PBI_ID=a.PBI_ID and
  d.month=".$_POST['month']." and d.year=".$_POST['year']." group by d.id order by p.PBI_ID,p2.PBI_ID asc,d.item_id");
 while($target_row=mysqli_fetch_object($query)){


 ?>

    <tr style="border: solid 1px #999; font-size:11px">
        <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$i=$i+1;?></td>
        <td style="border: solid 1px #999; padding:2px 10px 2px 2px; text-align: left"><?=$target_row->tsm_name;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=$target_row->territory;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=$target_row->dealer;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: left"><?=$target_row->socode;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: left"><?=$target_row->so_name;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$target_row->finish_goods_code;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: left"><?=$target_row->item_name;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$target_row->unit_name;?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($target_row->target_revised/$target_row->pack_size,2);?></td>
        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($target_row->amount,2);?></td>
    </tr>
    <?php
 $total_target_amount=$total_target_amount+$target_row->amount;
 } ?>
    <tr style="font-size: 12px; font-weight: bold">
        <td colspan="10" style="text-align: right;border: solid 1px #999; padding:2px;">Total Target in amount = </td>
        <td style="text-align: right;border: solid 1px #999; padding:2px;"><?=number_format($total_target_amount,2);?></td>
    </tr>
    </tbody>
    </div></div></div>


    <?php elseif ($report_id=='9005001'):?>
        <title>Cash Collection (Country)</title>
        <?php
        $datecon=' and j.jvdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        $query='Select d.dealer_code,d.account_code,d.dealer_name_e as dealer_name,t.town_name as town,a.AREA_NAME as territory,b.BRANCH_NAME as region,
				SUM(j.dr_amt) adjustment,
				SUM(j.cr_amt) collection,
				SUM(j.cr_amt-j.dr_amt) actual_collection
				from dealer_info d,town t, area a, branch b, journal j
				where
				d.customer_type not in ("display","gift") and d.dealer_category not in ("Rice") and d.town_code=t.town_code and a.AREA_CODE=d.area_code and b.BRANCH_ID=d.region and
				j.ledger_id=d.account_code and j.tr_from not in ("Sales","SalesReturn","Journal_info") '.$datecon.'
				group by d.dealer_code order by b.sl,a.AREA_NAME,t.town_name'; echo reportview($query,'Cash Collection (Country)','99');?>

    <?php elseif ($report_id=='9005002'):?>
        <title>Cash Collection (Region)</title>
        <?php
        $datecon=' and d.region="'.$_POST['BRANCH_ID'].'" and j.jvdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        $query='Select
				d.dealer_code,
				d.account_code,
				d.dealer_name_e,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.dr_amt) adjustment,
				SUM(j.cr_amt) collection,
				SUM(j.cr_amt-j.dr_amt) actual_collection
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				d.canceled!="No" and
				d.customer_type not in ("display","gift") and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				d.region=b.BRANCH_ID and
				d.account_code=j.ledger_id and j.tr_from not in ("Sales","SalesReturn","Journal_info") '.$datecon.'
				group by d.dealer_code'; echo reportview($query,'Cash Collection (Region)','99');?>

    <?php elseif ($report_id=='9005003'):?>
        <title>Cash Collection (Territory)</title>
        <?php
        $datecon=' and d.area_code="'.$_POST[AREA_CODE].'" and j.jvdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        $query='Select
				d.dealer_code,
				d.account_code,
				d.dealer_name_e,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.dr_amt) adjustment,
				SUM(j.cr_amt) collection,
				SUM(j.cr_amt-j.dr_amt) actual_collection
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				d.canceled!="No" and
				d.customer_type not in ("display","gift") and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				d.region=b.BRANCH_ID and
				j.ledger_id=d.account_code and j.tr_from not in ("Sales","SalesReturn","Journal_info") '.$datecon.'
				group by d.dealer_code'; echo reportview($query,'Cash Collection (Territory)','99');?>


<?php elseif ($report_id=='9002005'):?>
            <?php
            $datecon=' and d.area_code="'.$_POST[AREA_CODE].'" and sdc.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
            $query='Select
    				d.dealer_code,
    				d.account_code,
    				d.dealer_name_e as dealer_name,
    				t.town_name as town,
    				a.AREA_NAME as territory,
    				b.BRANCH_NAME as region,
    				FORMAT(SUM(sdc.total_amt),2) as shipment
    				from
    				dealer_info d,
    				town t,
    				area a,
    				branch b,
    				sale_do_chalan sdc
    				where
    				d.canceled!="No" and
    				d.customer_type not in ("display","gift") and
    				d.town_code=t.town_code and
    				a.AREA_CODE=d.area_code and
            d.dealer_code=sdc.dealer_code AND
    				d.region=b.BRANCH_ID '.$datecon.'
    				group by d.dealer_code'; echo reportview($query,'Shipment (Territory-Wise)','99');?>

    <?php elseif ($report_id=='9005004'):?>
        <title>Cash Collection (Town)</title>
        <?php
        $datecon=' and d.town_code="'.$_POST[town_code].'" and j.jvdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        $query='Select
				d.dealer_code,
				d.account_code,
				d.dealer_name_e,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.dr_amt) adjustment,
				SUM(j.cr_amt) collection,
				SUM(j.cr_amt-j.dr_amt) actual_collection
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				d.canceled!="No" and
				d.customer_type not in ("display","gift") and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				 d.region=b.BRANCH_ID and
				j.ledger_id=d.account_code and j.tr_from not in ("Sales","SalesReturn","Journal_info") '.$datecon.'
				group by d.dealer_code'; echo reportview($query,'Cash Collection (Town)','99');?>
    <?php elseif ($report_id=='9005005'):?>
        <title>Cash Collection (Dealer)</title>
        <?php
        $datecon=' and d.dealer_code="'.$_POST[dealer_code].'" and j.jvdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        $query='Select
				d.dealer_code,
				d.account_code,
				d.dealer_name_e,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.dr_amt) adjustment,
				SUM(j.cr_amt) collection,
				SUM(j.cr_amt-j.dr_amt) actual_collection
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				d.canceled!="No" and
				d.customer_type not in ("display","gift") and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				 d.region=b.BRANCH_ID and
				j.ledger_id=d.account_code and j.tr_from not in ("Sales","SalesReturn","Journal_info") '.$datecon.'
				group by d.dealer_code'; echo reportview($query,'Cash Collection (Dealer)','99');?>


        <?php elseif ($report_id=='9002009'):?>
            <title>Shipment Helper | Sales Report</title>
            <?php
            if($_POST['dealer_code']>0) 					$dealer_code=$_POST['dealer_code'];
            if(isset($dealer_code))				{$dealer_con=' and c.dealer_code='.$dealer_code;} else {$dealer_con='';}
            if($_POST['dealer_type_con']>0) 					$dealer_type_con=$_POST['dealer_type_con'];
            if(isset($dealer_type_con))				{$dealer_type_con=' and c.dealer_type_con='.$dealer_type_con;} else {$dealer_type_con='';}
            $query="SELECT i.brand_id,i.finish_goods_code as FG_code,i.item_name as 'FG Description',i.unit_name,i.pack_size as 'Pcs (Per Ctn)',
            (select SUM(c.total_unit)  from dealer_info d,sale_do_details c
            where c.item_id=i.item_id and c.total_amt!='0.00' and c.do_type in ('sales','') and status not in ('MANUAL','UNCHECKED') and c.dealer_code=d.dealer_code  and c.do_date between  '".$_POST['f_date']."' and '".$_POST['t_date']."' ".$dealer_type_con.$dealer_con.") as total_unit
            					 FROM  item_info i,
            							item_sub_group sg,
            							item_group g WHERE  i.sub_group_id=sg.sub_group_id and sg.group_id=g.group_id and
            							 g.group_id	in ('500000000') and i.status in ('Active') and i.item_id not in ('1096000100010312','1096000100010313','700020001')
            							  order by i.".$_POST['order_by'].""; echo reportview($query,'Shipment Helper','99','','','');?>


<?php elseif ($report_id=='9002010'):?>
            <title>Shipment Helper | Sales Report</title>
            <?php
            if($_POST['dealer_code']>0) 					$dealer_code=$_POST['dealer_code'];
            if(isset($dealer_code))				{$dealer_con=' and c.dealer_code='.$dealer_code;}
            if($_POST['brand_id']>0) 					$brand_id=$_POST['brand_id'];
            if(isset($brand_id))				{$brand_id_con=' and i.brand_id='.$brand_id;}
            if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
    if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;}
            $query="SELECT d.dealer_code,d.dealer_name_e as dealer_name,m.do_no,m.do_date,c.chalan_no,i.item_id,i.finish_goods_code,i.item_name,i.unit_name,b.brand_name,w.warehouse_name,a.AREA_NAME as Territory,c.unit_price as invoice_rate,c.total_unit as Invoice_qty,c.total_amt as invoice_value,(select SUM(total_amt) from sale_do_details where do_no=c.do_no and item_id='1096000100010312' and gift_on_item=c.item_id) as cash_discount 
            from dealer_info d,sale_do_master m,sale_do_chalan c,item_info i,item_brand b,warehouse w,area a where 
            m.dealer_code=d.dealer_code and 
            m.depot_id=w.warehouse_id and
            m.do_no=c.do_no and 
            m.area_code=a.AREA_CODE and
            d.dealer_code=c.dealer_code and 
            c.item_id=i.item_id and
            i.brand_id=b.brand_id and  
            m.do_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and c.total_amt>0 ".$warehouse_id_CON."".$brand_id_con."
             order by m.do_date,m.do_no,d.dealer_code,c.item_id"; echo reportview($query,'Invoice wise sales summery','99');?>


    <?php elseif ($report_id=='9006001'): $cc_code = @$_POST['cc_code']; $tr_from = @$_POST['tr_from'];?>
       <title><?=$ledger_name=getSVALUE('accounts_ledger','ledger_name','where ledger_id='.$_REQUEST['ledger_id']);?> | Transaction Statement</title>
        <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
        <p align="center" style="margin-top:-18px; font-size: 15px">Transaction Statement</p>
        <p align="center" style="margin-top:-10px; font-size: 12px; font-weight: bold"><?=($_REQUEST['ledger_id']>0)? 'Customer: '.$_REQUEST['ledger_id'].' - '.$ledger_name.'' : '' ?></p>
        <?php if($cc_code){ ?>
        <p align="center" style="margin-top:-10px; font-size: 12px"><strong>Cost Center:</strong> <?=find_a_field('cost_center','center_name','id='.$_REQUEST['cc_code']);?> (<?=$_REQUEST['cc_code'];?>)</p>
        <?php } ?>
        <?php if($tr_from){ ?>
        <p align="center" style="margin-top:-10px; font-size: 12px"><strong>Transaction Type:</strong> <?=$_REQUEST['tr_from'];?></p>
        <?php } ?>
        <p align="center" style="margin-top:-10px; font-size: 11px"><strong>Period From :</strong> <?=$_POST['f_date']?> to <?=$_POST['t_date']?></p>
        <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:95%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
                <th style="border: solid 1px #999; padding:2px">SL</th>
                <th style="border: solid 1px #999; padding:2px; width:5%">Date</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Transaction No</th>
                <th style="border: solid 1px #999; padding:2px">Particulars</th>
                <th style="border: solid 1px #999; padding:2px">Source</th>
                <th style="border: solid 1px #999; padding:2px; width: 10%">Entry By</th>
                <th style="border: solid 1px #999; padding:2px">Dr Amt</th>
                <th style="border: solid 1px #999; padding:2px">Cr Amt</th>
                <th style="border: solid 1px #999; padding:2px;">Balance</th>
            </tr></thead>
            <tbody>
        <?php

        if($tr_from!=''){
            $emp_id.=" and a.tr_from='".$tr_from."'";}
            $total_sql = "select sum(a.dr_amt),sum(a.cr_amt) from journal a,accounts_ledger b where a.ledger_id=b.ledger_id and a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and a.ledger_id like '".$_POST['ledger_id']."'";
            $total=mysqli_fetch_array(mysqli_query($conn, $total_sql));

            $c="select sum(a.dr_amt)-sum(a.cr_amt) from
            journal a,
            accounts_ledger b
            where a.ledger_id=b.ledger_id and a.jvdate<'".$_POST['f_date']."' and a.ledger_id like '".$_POST['ledger_id']."'";
            $p="select
a.jvdate,
b.ledger_name,
a.dr_amt,
a.cr_amt,
a.tr_from,
a.narration,
a.jv_no,
a.tr_no,
a.jv_no,
a.cheq_no,
a.cheq_date,
a.user_id,
a.PBI_ID,
a.cc_code,
a.ledger_id as lid ,
u.fname as approvedby,
c.center_name
from
journal a,
accounts_ledger b,
users u,
cost_center c
where
a.cc_code=c.id and
a.ledger_id=b.ledger_id and
a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and
a.ledger_id like '".$_POST['ledger_id']."' and
a.user_id=u.user_id
order by a.jvdate,a.id";
        if($total[0]>$total[1])
        {
            $t_type="(Dr)";
            $t_total=$total[0]-$total[1];
        }	else	{
            $t_type="(Cr)";
            $t_total=$total[1]-$total[0];	}
        /* ===== Opening Balance =======*/

        $psql=mysqli_query($conn, $c);
        $pl = mysqli_fetch_array($psql);
        $blance=$pl[0];
        ?>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
            <td align="center" bgcolor="#FFCCFF">#</td>
            <td colspan="2" align="center" bgcolor="#FFCCFF"><?=$from_date;?></td>
            <td align="left" bgcolor="#FFCCFF">Opening Balance </td>
            <td align="center" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="center" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="right" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="right" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="right" bgcolor="#FFCCFF"><?php if($blance>0) echo '(Dr)'.number_format($blance,2); elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),0,'.','');else echo "0.00"; ?></td>
        </tr>

        <?php
        $sql=mysqli_query($conn, $p);
        while($data=mysqli_fetch_row($sql)){?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
            <td align="center" style="border: solid 1px #999; padding:2px"><?=$i=$i+1;?></td>
            <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[0];?></td>
            <td align="center" style="border: solid 1px #999; padding:2px">
                <?php
                if($data[4]=='Receipt'||$data[4]=='Payment'||$data[4]=='Journal_info'||$data[4]=='Contra')
                {
                    $link="voucher_print1.php?v_type=".$data[4]."&v_date=".$data[0]."&view=1&vo_no=".$data[8];
                    echo "<a href='$link' target='_blank'>".$data[7]."</a>";
                }else {
                    $link="voucher_print1.php?v_type=".$data[4]."&v_date=".$data[0]."&view=1&vo_no=".$data[8];
                    echo "<a href='$link' target='_blank'>".$data[6]."</a>";}?>
            </td>
            <td align="left" style="border: solid 1px #999; padding:2px"><?=$data[5];?><?=(($data[9]!='')?'-Cq#'.$data[9]:'');?><?=(($data[10]>943898400)?'-Cq-Date#'.date('d-m-Y',$data[10]):'');?></td>
            <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[4];?></td>
            <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[15];?></td>
            <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data[2],2,'.',',');?></td>
            <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data[3],2,'.',',');?></td>
            <td align="right" bgcolor="#FFCCFF" style="border: solid 1px #999; padding:2px"><?php $blance = $blance+($data[2]-$data[3]);
                if($blance>0) echo '(Dr)'.number_format($blance,2,'.',',');
                elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00"; ?></td>
        </tr>
        <?php } ?>
        <tr style="font-size: 11px">
            <th colspan="6"  style="border: solid 1px #999; padding:2px; text-align: right"><strong>Total : </strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?php echo number_format($total[0],2);?></strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?php echo number_format($total[1],2);?></strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; width: 10%; text-align: right"><?php if($blance>0) echo '(Dr)'.number_format($blance,2,'.',',');
                elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00";
                ?></div>
            </th>
        </tr>
    </tbody>
    </table>

            <?php elseif ($report_id=='9006002'):?>
                <title>Customer Outstanding Balance</title>
                <?php
                $datecon=' and j.jvdate<"'.$_POST['t_date'].'"';
                $query='Select
				d.dealer_code,
				d.dealer_custom_code,
				d.account_code,
				d.dealer_name_e as dealername,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.cr_amt-j.dr_amt) as Outstanding_balance
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				d.canceled!="No" and
				d.customer_type not in ("display","gift") and
				d.dealer_category not in ("Rice") and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				 d.region=b.BRANCH_ID and
				j.ledger_id=d.account_code  '.$datecon.'
				group by d.dealer_code order by b.sl,a.AREA_NAME,t.town_name'; echo reportview($query,'Customer Outstanding Balance','99');?>

            <?php elseif ($report_id=='9005008'): if ($_POST['commission_status']=='0'): ?>
                <title>Collection & Shipment</title>
                <h2 align="center"><?=$_SESSION['company_name']?></h2>
                <h4 align="center" style="margin-top:-10px">Cash Collection and Shipment in Value (Total Country)</h4>
                <h5 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h5>
                <table align="center"  style="width:90%; border: solid 1px #999; border-collapse:collapse; ">
                    <thead>
                    <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                        echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                    <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                        <th style="border: solid 1px #999; padding:2px">SL</th>
                        <th style="border: solid 1px #999; padding:2px; width:5%">Code</th>
                        <th style="border: solid 1px #999; padding:2px; width:10%">Accounts Code</th>
                        <th style="border: solid 1px #999; padding:2px">Dealder Name</th>
                        <th style="border: solid 1px #999; padding:2px">Town</th>
                        <th style="border: solid 1px #999; padding:2px">Territory</th>
                        <!---th style="border: solid 1px #999; padding:2px">Area</th--->
                        <th style="border: solid 1px #999; padding:2px">Region</th>
                        <!--th style="border: solid 1px #999; padding:2px">Adjustment</th>
                        <th style="border: solid 1px #999; padding:2px">Collection</th--->
                        <th style="border: solid 1px #999; padding:2px">Actual Collection</th>
                        <th style="border: solid 1px #999; padding:2px">Shipment</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $result='Select
				d.dealer_code,
				d.dealer_custom_code as Customcode,
				d.account_code,
				d.dealer_name_e as dealername,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				sale_do_master m
				where
				d.customer_type not in ("display","gift") and
				d.dealer_category not in ("Rice") AND
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				b.BRANCH_ID=d.region
				group by d.dealer_code order by b.sl,a.AREA_NAME,t.town_name';
                    $query2 = mysqli_query($conn, $result);
                    while($data=mysqli_fetch_object($query2)){
                        $collection = find_a_field('journal','SUM(cr_amt-dr_amt)','jvdate between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and tr_from not in ("Sales","SalesReturn","Journal_info","Imported") and ledger_id='.$data->account_code);
                        $shipment=find_a_field('sale_do_details','SUM(total_amt)','do_type in ("sales","") and do_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"  and dealer_code='.$data->dealer_code);
                        if($collection>0 || $shipment>0) {
                            $i=$i+1; ?>
                            <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                                <td style="border: solid 1px #999; text-align:center"><?php echo $i; ?></td>
                                <td style="border: solid 1px #999; text-align:center"><?php echo $data->Customcode; ?></td>
                                <td style="border: solid 1px #999; text-align:center"><?php echo $data->account_code; ?></td>
                                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->dealername; ?></td>
                                <td style="border: solid 1px #999; text-align:left; padding:5px; width:10%"><?=$data->town;?></td>
                                <td style="border: solid 1px #999; padding:5px"><?=$data->territory;?></td>
                                <!---td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->area;?></td--->
                                <td style="border: solid 1px #999; text-align:left; padding:2px"><?=$data->region;?></td>
                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?php  echo number_format($collection,2);?></td>
                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?php echo number_format($shipment,2) ?></td>
                            </tr>
                            <?php
                            $totaladjustment=$totaladjustment+$adjustment;
                            $totalcollection=$totalcollection+$collection;
                            $totalactualcollection=$totalactualcollection+$actualcollection;
                            $totalcollectionreport=$totalcollectionreport+$collection;
                            $totalshipment=$totalshipment+$shipment;
                        }} ?>
                    <tr><td colspan="5" style="text-align:right;border: solid 1px #999;">Total</td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totaladjustment,2)?></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totalcollectionreport,2)?></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totalshipment,2)?></strong></td>
                    </tr>
                    </tbody>
                </table>
<?php endif; if ($_POST['commission_status']=='1'): ?>

                <h2 align="center"><?=$_SESSION[company_name]?></h2>
                <h4 align="center" style="margin-top:-10px">Cash Collection and Shipment in Value (Total Country)</h4>
                <h5 align="center" style="margin-top:-10px">Report From <?=$_POST[fdate]?> to <?=$_POST[tdate]?></h5>
                <table align="center"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
                    <thead>
                    <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                        echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                    <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                        <th style="border: solid 1px #999; padding:2px">SL</th>
                        <th style="border: solid 1px #999; padding:2px; width:5%">Code</th>
                        <th style="border: solid 1px #999; padding:2px; width:10%">Accounts Code</th>
                        <th style="border: solid 1px #999; padding:2px">Dealder Name</th>
                        <th style="border: solid 1px #999; padding:2px">Town</th>
                        <th style="border: solid 1px #999; padding:2px">Territory</th>
                        <!---th style="border: solid 1px #999; padding:2px">Area</th--->
                        <th style="border: solid 1px #999; padding:2px">Region</th>
                        <!--th style="border: solid 1px #999; padding:2px">Adjustment</th>
                        <th style="border: solid 1px #999; padding:2px">Collection</th--->
                        <th style="border: solid 1px #999; padding:2px">Actual Collection</th>
                        <th style="border: solid 1px #999; padding:2px">Shipment</th>
                        <th style="border: solid 1px #999; padding:2px">Comission</th>
                        <th style="border: solid 1px #999; padding:2px">Receiable Amount</th>

                    </tr></thead>
                    <tbody>
                    <?php
                    $datecon=' and j.jv_date between  "'.$fdate.'" and "'.$tdate.'"';
                    $result=mysqli_query($conn, 'Select d.dealer_code,
				d.dealer_custom_code as Customcode,
				d.account_code,
				d.dealer_name_e as dealername,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(sdd.total_amt) as shipment,
				(Select SUM(commission_amount)  from sale_do_master  where dealer_code=sdd.dealer_code and do_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'") as commissionGET
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				sale_do_details sdd
				where
				d.customer_type not in ("display","gift") and
				d.dealer_category not in ("Rice") AND
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				b.BRANCH_ID=d.region and
				sdd.do_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and
				sdd.dealer_code=d.dealer_code and
				sdd.do_type in ("sales")
				group by d.account_code ');
                    while($data=mysqli_fetch_object($result)){
                        if( $data->shipment>0) {
                            $i=$i+1; ?>
                            <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                                <td style="border: solid 1px #999; text-align:center"><?php echo $i; ?></td>
                                <td style="border: solid 1px #999; text-align:center"><?php echo $data->Customcode; ?></td>
                                <td style="border: solid 1px #999; text-align:center"><?php echo $data->account_code; ?></td>
                                <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->dealername; ?></td>
                                <td style="border: solid 1px #999; text-align:left; padding:5px; width:10%"><?=$data->town;?></td>
                                <td style="border: solid 1px #999; padding:5px"><?=$data->territory;?></td>
                                <!---td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->area;?></td--->
                                <td style="border: solid 1px #999; text-align:left; padding:2px"><?=$data->region;?></td>

                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?php  echo number_format($data->collection,2);?></td>
                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?php echo number_format($data->shipment,2) ?></td>
                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?php echo number_format($data->commissionGET,2) ?></td>
                                <td style="border: solid 1px #999; text-align:right;padding:2px"><?=number_format($totalcommission=$data->shipment-$data->commissionGET,2); ?></td>
                            </tr>
                            <?php
                            $tcomission=$tcomission+$data->commissionGET;
                            $totalshipment=$totalshipment+$data->shipment;
                            $totalcommissionCal=$totalcommissionCal+$totalcommission;


                        }} ?>
                    <tr><td colspan="5" style="text-align:right;border: solid 1px #999;">Total</td>
                        <td style="text-align:right;border: solid 1px #999;"><strong></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totalshipment,2)?></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($tcomission,2)?></strong></td>
                        <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totalcommissionCal,2)?></strong></td>
                    </tr>
                    </tbody>
                </table><?php endif; ?>








<?php endif; ?>
</body>
</html>
