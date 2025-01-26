<?
require "support_file.php";
if(!empty($_POST['order_by'])) $order_by_GET=$_POST['order_by'];
if(isset($order_by_GET))				{$order_by=' order by i.'.$order_by_GET;} else {$order_by = '';}
if(!empty($_POST['order_by']) && !empty($_POST['sort'])) $order_by_GET=$_POST['order_by'];
if(isset($order_by_GET))				{$order_by=' order by i.'.$order_by_GET.' '.$_POST['sort'].'';} else {$order_by='';}
$warehouse_id = @$_POST['warehouse_id'];
$warehouse_name= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$warehouse_id.'"');
$PostFDate = @$_POST['f_date'];
$PostTDate = @$_POST['t_date'];
if(isset($_REQUEST['submit'])&&isset($_REQUEST['report_id'])>0)
{
$to_date=@$_POST['f_date'];
$fr_date=@$_POST['t_date'];
$date_con=' and j.ji_date between \''.$fr_date.'\' and \''.$to_date.'\'';
$do_date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';
$PostProductGroup = @$_POST['product_group'];
$PostItemBrand = @$_POST['item_brand'];
$PostItemId = @$_POST['item_id'];
$PostDealerCode = @$_POST['dealer_code'];
$PostDealerType = @$_POST['dealer_type'];
$PostProductTeam = @$_POST['product_team'];
$PostStatus = @$_POST['status'];
$PostDoNo = @$_POST['do_no'];
$PostAreaId = @$_POST['area_id'];
$PostZoneId = @$_POST['zone_id'];
$PostRegionId = @$_POST['region_id'];
$PostDepotId = @$_POST['depot_id'];
$PostUserId = @$_POST['user_id'];


    if($PostProductGroup!='')       $product_group=$PostProductGroup;
    if($PostItemBrand!='') 	        $item_brand=$PostItemBrand;
    if($PostItemId>0) 		        $item_id=@$_POST['item_id'];
    if($PostDealerCode>0) 	        $dealer_code=@$_POST['dealer_code'];
    if($PostDealerType!='') 	    $dealer_type=@$_POST['dealer_type'];
    if($PostProductTeam!='') 	    $product_team=@$_POST['product_team'];
    if($PostStatus!='') 		    $status=@$_POST['status'];
    if($PostDoNo!='') 		        $do_no=@$_POST['do_no'];
    if($PostAreaId!='') 		    $area_id=@$_POST['area_id'];
    if($PostZoneId!='') 		    $zone_id=@$_POST['zone_id'];
    if($PostRegionId>0) 		    $region_id=@$_POST['region_id'];
    if($PostDepotId!='') 		    $depot_id=@$_POST['depot_id'];
    if(isset($item_brand)) 			{$item_brand_con=' and i.item_brand="'.$item_brand.'"';}
    if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code="'.$dealer_code.'"';}
    if(isset($t_date)) 				{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
    if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';}
    if(isset($dealer_type)) 		{$dtype_con=' and d.dealer_type="'.$dealer_type.'"';}
    if(isset($product_team)) 		{$product_team_con=' and d.team_name="'.$product_team.'"';}
    if(isset($dealer_type)) 		{$dealer_type_con=' and d.dealer_type="'.$dealer_type.'"';}
    if(isset($item_id))				{$item_con=' and i.item_id='.$item_id;}
    if(isset($depot_id)) 			{$depot_con=' and d.depot="'.$depot_id.'"';}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
        #customers {
            font-family: "Gill Sans", sans-serif;
        }
        #customers td {
        }
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
        td{}
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








<?php $RequestReport = @$_REQUEST['report'];
switch ($RequestReport) {
case 1:
$report="BIN CARD (Details)";
	$s=1;
$sql="select j.* from
journal_item j  , warehouse w
where w.warehouse_id=j.warehouse_id and j.warehouse_id='".$_SESSION['warehouse']."' ".$date_con.$item_con." order by j.id";
	break;


	case 2:
$report="BIN CARD (Summary)";
		$s=1;
$sql="select j.* from
journal_item j  , warehouse w
where w.warehouse_id=j.warehouse_id and j.warehouse_id='".$_SESSION['warehouse']."' ".$date_con.$item_con." order by j.id";
	break;

    case 10:
$report="Delivery Challan Report";
        $s=1;
        if(isset($_POST['t_date'])) {$to_date=date('Y-m-d' , strtotime($_POST['t_date']));; $fr_date=date('Y-m-d' , strtotime($_POST['f_date']));; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

        if($_POST['depot_id']>0) 			 $depot_id=$_POST['depot_id'];
        if(isset($depot_id))				{$depot_con=' and m.depot_id='.$depot_id;}
        $sql=mysqli_query($conn, "select
distinct c.chalan_no,
c.chalan_date,
c.driver_name_real as driver_name,
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
sum(c.total_amt) as total_amt,
(select SUM(total_amt) from sale_do_chalan where do_no=m.do_no and total_amt<0) as discount

from

sale_do_master m
,sale_do_chalan c,
dealer_info d ,
warehouse w,
area a,
personnel_basic_info p
where
a.AREA_CODE=d.area_code and
c.total_amt>0 and
m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_type  in ('sales','') and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
a.PBI_ID=p.PBI_ID".$depot_con.$date_con.$pg_con.$dealer_con.$dtype_con.$product_team_con." group by c.do_no order by c.do_no");
        break;

        case 11:
        $report="Undelivered DO Report";
        $s=1;
        if(isset($_POST['t_date'])) {$to_date=date('Y-m-d' , strtotime($_POST['t_date']));; $fr_date=date('Y-m-d' , strtotime($_POST['f_date']));; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
        if($_POST['depot_id']>0) 			 $depot_id=$_POST['depot_id'];
        if(isset($depot_id))				{$depot_con=' and m.depot_id='.$depot_id;}
            $sql=mysqli_query($conn, "select
distinct c.do_no,

c.do_date, m.do_no,m.do_date,d.dealer_code as dealercode,p.PBI_NAME as tsm ,concat(d.dealer_name_e) as dealer_name,a.AREA_NAME as area, a.ZONE_ID as Zonecode, d.team_name as team,w.warehouse_name as depot,d.product_group as grp,m.cash_discount commission from
sale_do_master m,sale_do_details c,dealer_info d  , warehouse w, area a, personnel_basic_info p
where a.AREA_CODE=d.area_code and m.status in ('CHECKED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code  and w.warehouse_id=m.depot_id and d.tsm=p.PBI_ID".$depot_con.$date_con.$pg_con.$dealer_con.$dtype_con.$product_team_con." order by c.do_no");
        break;

	case 3:
$report="Product BIN CARD (Date Wise)";
$sql="select j.* from
journal_item j  , warehouse w
where w.warehouse_id=j.warehouse_id and j.warehouse_id='".$_SESSION['warehouse']."' ".$date_con.$item_con." order by j.ji_date";
    break;

   case 4:
$report="BIN CARD";
$sql="select j.* from
journal_item j  , warehouse w
where w.warehouse_id=j.warehouse_id and j.warehouse_id='".$_SESSION['warehouse']."' ".$date_con.$item_con." order by j.ji_date";
	break;



    case 5:
$report="BIN CARD (Finish Goods)";
$sql="select j.* from
journal_item j  , warehouse w
where w.warehouse_id=j.warehouse_id and j.warehouse_id='".$_SESSION['warehouse']."' ".$date_con.$item_con." order by j.ji_date";
	break;

}}

$str = '';
// Retrieve from POST and set defaults if not provided
$PostItemId = isset($_POST['PostItemId']) ? (int)$_POST['PostItemId'] : 0;
$PostUserId = isset($_POST['PostUserId']) ? (int)$_POST['PostUserId'] : 0;
$to_date = isset($_POST['t_date']) ? (int)$_POST['t_date'] : 0;

$str 	.= '<div class="header">';
if(isset($_SESSION['company_name']))
$str 	.= '<h1>'.$_SESSION['company_name'].'</h1>';
if(isset($report))
$str 	.= '<h2>'.$report.'</h2>';
if(($PostItemId>0))
$str 	.= '<h2>Item Name: '.find_a_field('item_info','item_name','item_id='.$PostItemId).'</h2>';
if(($PostUserId>0))
$str 	.= '<h2>Entry By: '.find_a_field('users','fname','user_id='.$_POST['user_id']).'</h2>';



if (isset($fr_date) && !empty($fr_date)) {
    $dateParts = preg_split("/[\/\.\-]+/", $fr_date);
    if (count($dateParts) === 3) {
        list($year1, $month, $day) = $dateParts;
    } else {
        $year1 = $month = $day = null; // Default values or error handling
        error_log("Invalid date format for \$to_date: $fr_date");
    }} else {
    $year1 = $month = $day = null; // Default values
    error_log("Empty or undefined \$to_date");
}


if (isset($to_date) && !empty($to_date)) {
    $dateParts = preg_split("/[\/\.\-]+/", $to_date);
    if (count($dateParts) === 3) {
        list($year2, $month2, $day2) = $dateParts;
    } else {
        $year2 = $month2 = $day2 = null; // Default values or error handling
        error_log("Invalid date format for \$to_date: $to_date");
    }} else {
    $year2 = $month2 = $day2 = null; // Default values
    error_log("Empty or undefined \$to_date");
}

$str 	.= '<h3>Date Interval : '.$day.'-'.$month.'-'.$year1.' To '.$day2.'-'.$month2.'-'.$year2.'</h3>';
if(isset($product_group))
$str 	.= '<h2>Product Group : '.$product_group.'</h2>';
$str 	.= '</div>';
$str 	.= '<div class="left" style="width:100%">';
$report_id = @$_POST['report_id'];
?>


<? if($report_id==7001001) { ?>
  <style>
      #customers {
          font-family: "Gill Sans", sans-serif;
      }
      #customers td {
      }
      #customers tr:ntd-child(even)
      {background-color: #f0f0f0;}
      #customers tr:hover {background-color: #f5f5f5;}
      td{
          text-align: center;

      }
  </style>
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
$datecon=' and m.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
        if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;} else {$warehouse_id_CON='';}
		if($_POST['dealer_code']>0) 			 $dealer_code=$_POST['dealer_code'];
        if(isset($dealer_code))				{$dealer_code_CON=' and m.dealer_code='.$dealer_code;} else {$dealer_code_CON='';}

    if(!empty($_POST['do_type'])) 					$do_type=@$_POST['do_type'];
    if(($do_type=='sales' || $do_type=='sample' || $do_type=='gift'|| $do_type=='free'|| $do_type=='display')) {$do_type_conn=' and m.do_type="'.$do_type.'"';} else {$do_type_conn='';}


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
m.do_type,
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
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
c.item_id not in ('1096000100010312') and
a.PBI_ID=p.PBI_ID".$warehouse_id_CON.$datecon.$dealer_code_CON.$do_type_conn."
group by c.do_no
order by c.do_no";
$query = mysqli_query($conn, $sql); ?>


<table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse;">
<thead>
        <p style="width:95%; text-align:right; font-size:10px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
<tr style="border: solid 1px #999;font-weight:bold; background-color:#f5f5f5; font-size:11px">
<th style="border: solid 1px #999; padding:2px">S/L</th>
<th style="border: solid 1px #999; padding:2px">Chalan No</th>
<th style="border: solid 1px #999; padding:2px">Chalan Date</th>
<th style="border: solid 1px #999; padding:2px">Do No</th>
<th style="border: solid 1px #999; padding:2px">Do Date</th>
<th style="border: solid 1px #999; padding:2px">Do Type</th>
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
<?php  $discounttotal = 0;$total_invoice_amount=0;$s=0;$totalcomissionamount = 0; while($data=mysqli_fetch_object($query)){$s++;
list( $year1, $month, $day) = preg_split("/[\/\.\-]+/", $data->do_date); ?>
<tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
<td style="border: solid 1px #999; text-align:center"><?=$s?></td>
<td style="border: solid 1px #999; text-align:center"><a href="chalan_view.php?v_no=<?=$data->chalan_no?>" target="_blank"><?=$data->chalan_no?></a></td>
<td style="border: solid 1px #999; text-align:center"><?=$data->chalan_date?></td>
<td style="border: solid 1px #999; text-align:center"><a href="chalan_bill_distributors.php?do_no=<?=$data->do_no?>" target="_blank"><?=$data->do_no;?></a></td>
<td style="border: solid 1px #999; text-align:center"><?=$day.'-'.$month.'-'.$year1;?></td>
<td style="border: solid 1px #999; text-align:center"><?=$data->do_type;?></td>
<td style="border: solid 1px #999; text-align:left"><?=$data->dealer_name;?></td>
<td style="border: solid 1px #999; text-align:center"><?=$data->area;?></td>
<td style="border: solid 1px #999; text-align:left"><?=$data->tsm;?></td>
<td style="border: solid 1px #999; text-align:center"><?=$data->depot;?></td>
<td style="border: solid 1px #999; text-align:right"><?=number_format($data->invoice_amount,2);?></td>
<td style="border: solid 1px #999; text-align:right"><? if(substr($data->discount,1)>0) echo  number_format(substr($data->discount,1),2); else echo'-';?></td>
<td style="border: solid 1px #999; text-align:right"><? if($data->comissionamount>0) echo  number_format($data->comissionamount,2); else echo'-';?></td>
<td style="border: solid 1px #999; text-align:right"><?=number_format(($data->invoice_amount-(substr($data->discount,1)+$data->comissionamount)),2)?></td>
</tr>

<?php
$discounts=substr($data->discount,1);
$discounttotal=$discounttotal+$discounts;
$total_invoice_amount=$total_invoice_amount+$data->invoice_amount;
$totalsaleafterdiscount=($total_invoice_amount-($discounttotal+$data->comissionamount));
$totalcomissionamount=$totalcomissionamount+$data->comissionamount;

} ?>
<tr style="font-size:11px; font-weight:bold">
<td colspan="10" style="border: solid 1px #999; text-align:right;  padding:2px">Total</td>
<td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount,2);?></td>
<td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($discounttotal,2);?></td>
<td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalcomissionamount,2);?></td>
<td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount-($discounttotal+$totalcomissionamount),2);?></td>
</tr></tbody>
</table>


<?php } elseif ($report_id=='7004001'){?>
   <title><?=$warehouse_name= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> : Transaction Statement</title>
        <?php
        $datecon=' and sdd.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
        if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.depot_id='.$warehouse_id;}
        if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
        if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else { $item_con=''; }

        $sql='select
		i.item_id,
		i.finish_goods_code as fg_code,
		i.item_name,
		i.unit_name as UOM,
		s.sub_group_name as Category,
		i.pack_size as packsize,
		w.warehouse_name as warehouse,
		sdd.do_no,
		sdd.do_date,
		sdd.status,
		SUM(sdd.total_unit) as Order_Qty_in_Pcs,
		(select SUM(total_unit) from sale_do_chalan where item_id=sdd.item_id and do_no=sdd.do_no) as Challan_Qty_in_Pcs
		from
				sale_do_details sdd,
				item_info i,
				item_sub_group s,
				warehouse w
				where
				s.sub_group_id=i.sub_group_id and
				sdd.depot_id=w.warehouse_id and
				sdd.item_id=i.item_id and i.item_id not in ("1096000100010312")'.$datecon.$warehouse_con.$item_con.' group by sdd.do_no,sdd.item_id order by sdd.do_no,sdd.item_id asc';
        ?>
    <?=reportview($sql,'Order vs Challan',100,'','','')?>



<?php } elseif ($report_id=='7004002'){?>
   <title><?=$warehouse_name= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> : Transaction Statement</title>
        <?php
        $datecon=' and sdd.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
        if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.depot_id='.$warehouse_id;}
        if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
        if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else {$item_con='';}

        $sql='select
		i.item_id,
		i.item_id,
		i.finish_goods_code as fg_code,
		i.item_name,
		i.unit_name as UOM,
		s.sub_group_name as Category,
		i.pack_size as packsize,
		w.warehouse_name as warehouse,
		sdd.do_no,
		sdd.do_date,
		sdd.status,
		SUM(sdd.total_unit) as Order_Qty_in_Pcs,
		FORMAT((select SUM(item_ex) from journal_item where item_id=sdd.item_id and do_no=sdd.do_no and tr_from="Sales"),0) as Stock_Qty_in_Pcs
		from
				sale_do_details sdd,
				item_info i,
				item_sub_group s,
				warehouse w
				where
				s.sub_group_id=i.sub_group_id and
				sdd.depot_id=w.warehouse_id and
				sdd.item_id=i.item_id and i.item_id not in ("1096000100010312")'.$datecon.$warehouse_con.$item_con.' group by sdd.do_no,sdd.item_id order by sdd.do_no,sdd.item_id asc';
        ?>
           <?=reportview($sql,'Order vs Stock',100,'','','')?>

<?php } elseif ($report_id=='7004003'){?>
   <title><?=$warehouse_name= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> : Transaction Statement</title>
        <?php
        $datecon=' and sdd.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
        if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.depot_id='.$warehouse_id;}
        if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
        if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else {$item_con='';}

        $sql='select
		i.item_id,
		i.item_id,
		i.finish_goods_code as fg_code,
		i.item_name,
		i.unit_name as UOM,
		s.sub_group_name as Category,
		i.pack_size as packsize,
		w.warehouse_name as warehouse,
		sdd.do_no,
		sdd.do_date,
		sdd.status,
		SUM(sdd.total_unit) as Order_Qty_in_Pcs,
	    (select SUM(total_unit) from sale_do_chalan where item_id=sdd.item_id and do_no=sdd.do_no)  Challan_Qty_in_Pcs,
		FORMAT((select SUM(item_ex) from journal_item where item_id=sdd.item_id and do_no=sdd.do_no and tr_from="Sales"),0)  Stock_Qty_in_Pcs
		from
				sale_do_details sdd,
				item_info i,
				item_sub_group s,
				warehouse w
				where
				s.sub_group_id=i.sub_group_id and
				sdd.depot_id=w.warehouse_id and
				sdd.item_id=i.item_id and i.item_id not in ("1096000100010312") '.$datecon.$warehouse_con.$item_con.' group by sdd.do_no,sdd.item_id order by sdd.do_no,sdd.item_id asc';
        ?>
           <?=reportview($sql,'Order vs Challan vs Stock',100,'','','')?>


         <?php } elseif ($report_id=='7004004'){?>
            <title>STO vs Stock Exit</title>
                 <?php
                 $datecon=' and sdd.pi_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
                 if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
                 if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.warehouse_from='.$warehouse_id;} else {$warehouse_con='';}
                 if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
                 if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else {$item_con='';}

                 $sql='select
         		i.item_id,
         		i.item_id,
         		i.finish_goods_code as fg_code,
         		i.item_name,
         		i.unit_name as UOM,
         		s.sub_group_name as Category,
         		i.pack_size as packsize,
         		w.warehouse_name as warehouse,
         		sdd.pi_no,
         		sdd.pi_date,
         		SUM(sdd.total_unit) as Order_Qty_in_Pcs,
         		(select SUM(item_ex) from journal_item where item_id=sdd.item_id and tr_no=sdd.pi_no and tr_from="ProductionTransfer" and warehouse_id=sdd.warehouse_from) as Stock_Qty_in_Pcs
         		from
         				production_issue_detail sdd,
         				item_info i,
         				item_sub_group s,
         				warehouse w
         				where
         				s.sub_group_id=i.sub_group_id and
         				sdd.warehouse_from=w.warehouse_id and
         				sdd.item_id=i.item_id '.$datecon.$warehouse_con.$item_con.' group by sdd.pi_no,sdd.item_id order by sdd.pi_no,sdd.item_id asc';
                $result=mysqli_query($conn, $sql);
                 ?>
                 <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
                 <h5 align="center" style="margin-top:-12px">STO vs Stock Exit</h5>
                 <h6 align="center" style="margin-top:-12px">Warehouse / CMU / Factory: <?=$warehouse_name;?></h6>
                 <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
                 <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
                     <thead>
                     <p style="width:98%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                         echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                     <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #f5f5f5">
                         <th style="border: solid 1px #999; padding:2px">#</th>
                         <th style="border: solid 1px #999; padding:2px; %">Item Id	</th>
                         <th style="border: solid 1px #999; padding:2px; %">Fg Code</th>
                         <th style="border: solid 1px #999; padding:2px">Item Name</th>
                         <th style="border: solid 1px #999; padding:2px">UOM</th>
                         <th style="border: solid 1px #999; padding:2px">Category</th>
                         <th style="border: solid 1px #999; padding:2px">Pack<br>Size</th>
                         <th style="border: solid 1px #999; padding:2px; ">Warehoues Name</th>
                         <th style="border: solid 1px #999; padding:2px; ">Pi No</th>
                         <th style="border: solid 1px #999; padding:2px; ">Pi Date</th>
                         <th style="border: solid 1px #999; padding:2px; ">STO Qty</th>
                         <th style="border: solid 1px #999; padding:2px; ">Stock Exit Qty</th>
                         <th style="border: solid 1px #999; padding:2px; ">Difference</th>
                     </tr></thead><tbody>
                     <?php
                     $STO_total=0;
                     $i=0;
                     $stock_exit_total = 0;
                     while($data=mysqli_fetch_object($result)){ ?>
                         <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                             <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                             <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                             <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->fg_code;?></td>
                             <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->item_name;?></td>
                             <td style="border: solid 1px #999; text-align:left"><?=$data->Category; ?></td>
                             <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                             <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->packsize;?></td>
                             <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->warehouse;?></td>
                             <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_no;?></td>
                             <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_date;?></td>
                             <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Order_Qty_in_Pcs;?></td>
                             <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Stock_Qty_in_Pcs;?></td>
                             <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=($data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs>0)? $data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs : $data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs;?></td>
                        </tr>
                         <?php
                         $STO_total=$STO_total+$data->Order_Qty_in_Pcs;
                         $stock_exit_total=$stock_exit_total+$data->Stock_Qty_in_Pcs;
                         $diff_tottal=$STO_total-$stock_exit_total;} ?>
                     <tr style="font-size:12px"><td colspan="10" style="text-align:right; "><strong>Total</strong></td>
                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($STO_total,2)?></strong></td>
                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($stock_exit_total,2)?></strong></td>
                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($diff_tottal,2)?></strong></td>
                     </tr>
                     </tbody>
                 </table>

               <?php } elseif ($report_id=='7004005'){?>
                  <title>STO vs Stock Received</title>
                       <?php
                       $datecon=' and sdd.pi_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
                       if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
                       if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.warehouse_from='.$warehouse_id;} else {$warehouse_con='';}
                       if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
                       if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else {$item_con='';}

                       $sql='select
                  i.item_id,
                  i.item_id,
                  i.finish_goods_code as fg_code,
                  i.item_name,
                  i.unit_name as UOM,
                  s.sub_group_name as Category,
                  i.pack_size as packsize,
                  w.warehouse_name as warehouse,
                  sdd.pi_no,
                  sdd.pi_date,
                  SUM(sdd.total_unit) as Order_Qty_in_Pcs,
                  (select SUM(item_in) from journal_item where item_id=sdd.item_id and tr_no=sdd.pi_no and tr_from="ProductionReceived" and warehouse_id=sdd.warehouse_to) as Stock_Qty_in_Pcs
                  from
                      production_issue_detail sdd,
                      item_info i,
                      item_sub_group s,
                      warehouse w
                      where
                      s.sub_group_id=i.sub_group_id and
                      sdd.warehouse_to=w.warehouse_id and
                      sdd.item_id=i.item_id '.$datecon.$warehouse_con.$item_con.' group by sdd.pi_no,sdd.item_id order by sdd.pi_no,sdd.item_id asc';
                      $result=mysqli_query($conn, $sql);
                       ?>
                       <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
                       <h5 align="center" style="margin-top:-12px">STO vs Stock Exit</h5>
                       <h6 align="center" style="margin-top:-12px">Warehouse / CMU / Factory: <?=$warehouse_name;?></h6>
                       <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
                       <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
                           <thead>
                           <p style="width:98%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                               echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                           <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #f5f5f5">
                               <th style="border: solid 1px #999; padding:2px">#</th>
                               <th style="border: solid 1px #999; padding:2px; %">Item Id	</th>
                               <th style="border: solid 1px #999; padding:2px; %">Fg Code</th>
                               <th style="border: solid 1px #999; padding:2px">Item Name</th>
                               <th style="border: solid 1px #999; padding:2px">UOM</th>
                               <th style="border: solid 1px #999; padding:2px">Category</th>
                               <th style="border: solid 1px #999; padding:2px">Pack<br>Size</th>
                               <th style="border: solid 1px #999; padding:2px; ">Transfer to</th>
                               <th style="border: solid 1px #999; padding:2px; ">Pi No</th>
                               <th style="border: solid 1px #999; padding:2px; ">Pi Date</th>
                               <th style="border: solid 1px #999; padding:2px; ">STO Qty</th>
                               <th style="border: solid 1px #999; padding:2px; ">Stock Exit Qty</th>
                               <th style="border: solid 1px #999; padding:2px; ">Difference</th>
                           </tr></thead><tbody>
                           <?php
                           $i = 0;
                           $STO_total = 0;
                           $stock_exit_total = 0;
                           while($data=mysqli_fetch_object($result)){ ?>
                               <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                                   <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                                   <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                                   <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->fg_code;?></td>
                                   <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->item_name;?></td>
                                   <td style="border: solid 1px #999; text-align:left"><?=$data->Category; ?></td>
                                   <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                                   <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->packsize;?></td>
                                   <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->warehouse;?></td>
                                   <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_no;?></td>
                                   <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_date;?></td>
                                   <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Order_Qty_in_Pcs;?></td>
                                   <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Stock_Qty_in_Pcs;?></td>
                                   <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=($data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs>0)? $data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs : '-';?></td>
                              </tr>
                               <?php
                               $STO_total=$STO_total+$data->Order_Qty_in_Pcs;
                               $stock_exit_total=$stock_exit_total+$data->Stock_Qty_in_Pcs;
                               $diff_tottal=$STO_total-$stock_exit_total;} ?>
                           <tr style="font-size:12px"><td colspan="10" style="text-align:right; "><strong>Total</strong></td>
                               <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($STO_total,2)?></strong></td>
                               <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($stock_exit_total,2)?></strong></td>
                               <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($diff_tottal,2)?></strong></td>
                           </tr>
                           </tbody>
                       </table>

                     <?php } elseif ($report_id=='7004006'){?>
                        <title>STO vs Stock Exit vs Stock Received</title>
                             <?php
                             $datecon=' and sdd.pi_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
                             if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
                             if(isset($warehouse_id)) 				{$warehouse_con=' and sdd.warehouse_from='.$warehouse_id;} else {$warehouse_con='';}
                             if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
                             if(isset($item_id))				{$item_con=' and sdd.item_id='.$item_id;} else {$item_con='';}

                             $sql='select
                        i.item_id,
                        i.item_id,
                        i.finish_goods_code as fg_code,
                        i.item_name,
                        i.unit_name as UOM,
                        s.sub_group_name as Category,
                        i.pack_size as packsize,
                        w.warehouse_name as warehouse,
                        sdd.pi_no,
                        sdd.pi_date,
                        sdd.warehouse_to,
                        SUM(sdd.total_unit) as Order_Qty_in_Pcs,
                        (select SUM(item_ex) from journal_item where item_id=sdd.item_id and tr_no=sdd.pi_no and tr_from="ProductionTransfer" and warehouse_id=sdd.warehouse_from) as Stock_Qty_in_Pcs,
                        (select SUM(item_in) from journal_item where item_id=sdd.item_id and tr_no=sdd.pi_no and tr_from="ProductionReceived" and warehouse_id=sdd.warehouse_to) as stock_received

                        from
                            production_issue_detail sdd,
                            item_info i,
                            item_sub_group s,
                            warehouse w
                            where
                            s.sub_group_id=i.sub_group_id and
                            sdd.warehouse_from=w.warehouse_id and
                            sdd.item_id=i.item_id '.$datecon.$warehouse_con.$item_con.' group by sdd.pi_no,sdd.item_id order by sdd.pi_no,sdd.item_id asc';
                            $result=mysqli_query($conn, $sql);
                             ?>
                             <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
                             <h5 align="center" style="margin-top:-12px">STO vs Stock Exit</h5>
                             <h6 align="center" style="margin-top:-12px">Warehouse / CMU / Factory: <?=$warehouse_name;?></h6>
                             <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
                             <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
                                 <thead>
                                 <p style="width:98%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                                     echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                                 <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #f5f5f5">
                                     <th style="border: solid 1px #999; padding:2px">#</th>
                                     <th style="border: solid 1px #999; padding:2px; %">Item Id	</th>
                                     <th style="border: solid 1px #999; padding:2px; %">Fg Code</th>
                                     <th style="border: solid 1px #999; padding:2px">Item Name</th>
                                     <th style="border: solid 1px #999; padding:2px">UOM</th>
                                     <th style="border: solid 1px #999; padding:2px">Category</th>
                                     <th style="border: solid 1px #999; padding:2px">Pack<br>Size</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Warehouse Name</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Pi No</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Pi Date</th>
                                     <th style="border: solid 1px #999; padding:2px; ">STO Qty</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Stock Exit Qty</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Difference (STO vs Exit)</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Stock Received Qty</th>
                                     <th style="border: solid 1px #999; padding:2px; ">Difference (Exit vs Received)</th>
                                 </tr></thead><tbody>
                                 <?php $i=0;$STO_total=0;$stock_exit_total=0;$stock_received_total=0;
                                 while($data=mysqli_fetch_object($result)){ ?>
                                     <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                                         <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                                         <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                                         <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->fg_code;?></td>
                                         <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->item_name;?></td>
                                         <td style="border: solid 1px #999; text-align:left"><?=$data->Category; ?></td>
                                         <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                                         <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->packsize;?></td>
                                         <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->warehouse;?></td>
                                         <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_no;?></td>
                                         <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->pi_date;?></td>
                                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Order_Qty_in_Pcs;?></td>
                                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Stock_Qty_in_Pcs;?></td>
                                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=($data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs>0)? $data->Order_Qty_in_Pcs-$data->Stock_Qty_in_Pcs : '-';?></td>
                                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->stock_received;?></td>
                                         <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=$data->Stock_Qty_in_Pcs-$data->stock_received;?></td>

                                    </tr>
                                     <?php
                                     $STO_total=$STO_total+$data->Order_Qty_in_Pcs;
                                     $stock_exit_total=$stock_exit_total+$data->Stock_Qty_in_Pcs;
                                     $diff_tottal=$STO_total-$stock_exit_total;
                                     $stock_received_total=$stock_received_total+$data->stock_received;
                                     $diff_exit_vs_received=$stock_exit_total-$stock_received_total;
                                   } ?>
                                 <tr style="font-size:12px"><td colspan="10" style="text-align:right; "><strong>Total</strong></td>
                                     <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($STO_total,2)?></strong></td>
                                     <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($stock_exit_total,2)?></strong></td>
                                     <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($diff_tottal,2)?></strong></td>
                                     <th style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($stock_received_total,2)?></th>
                                     <th style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($diff_exit_vs_received,2)?></th>
                                 </tr>
                                 </tbody>
                             </table>



<?php } elseif ($report_id=='7003001'){

    $PostStatus = @$_POST['status'];
    $PostWarehouse_id = @$_POST['warehouse_id'];
    ?>
<title><?=$warehouse_name= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> : Inventory Register Book</title>

    <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
    <h5 align="center" style="margin-top:-12px">Transaction Statement</h5>
    <h6 align="center" style="margin-top:-12px">Warehouse / CMU / Factory: <?=$warehouse_name;?></h6>
    <?php if($PostStatus=='Received'){?>
    <h4 align="center" style="margin-top:-10px">Status : Received</h4>
<?php } elseif ($PostStatus=='Issue'){?>
    <h4 align="center" style="margin-top:-10px">Status : Issue</h4>
<?php } ?>
    <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
    <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
        <thead>
        <p style="width:98%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #f5f5f5">
            <th style="border: solid 1px #999; padding:2px">SL</th>
            <th style="border: solid 1px #999; padding:2px; %">T.ID</th>
            <th style="border: solid 1px #999; padding:2px; %">Trns. Date</th>
            <th style="border: solid 1px #999; padding:2px">FG Code</th>
            <th style="border: solid 1px #999; padding:2px">FG Description</th>
            <th style="border: solid 1px #999; padding:2px">Category</th>
            <th style="border: solid 1px #999; padding:2px">UOM</th>
            <th style="border: solid 1px #999; padding:2px">Pack<br>Size</th>
            <th style="border: solid 1px #999; padding:2px">Source</th>
            <th style="border: solid 1px #999; padding:2px; ">Warehoues Name</th>
            <?php if($PostStatus=='Received'){?>
                <th style="border: solid 1px #999; padding:2px; ">PO NO</th>
            <?php } elseif ($PostStatus=='Issue'){?>
                <th style="border: solid 1px #999; padding:2px; ">DO NO</th>
            <?php } ?>
            <th style="border: solid 1px #999; padding:2px; ">Tr No</th>
            <th style="border: solid 1px #999; padding:2px; ">C.No</th>
            <th style="border: solid 1px #999; padding:2px; ">Entry At</th>
            <th style="border: solid 1px #999; padding:2px; ">User</th>
            <th style="border: solid 1px #999; padding:2px; ">Batch</th>
            <th style="border: solid 1px #999; padding:2px">IN (Pcs)</th>
            <th style="border: solid 1px #999; padding:2px">OUT (Pcs)</th>
        </tr></thead>

        <tbody>
        <?php
        $datecon=' and a.ji_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
        if($PostWarehouse_id>0) 				$warehouse_id=$PostWarehouse_id;
        if(isset($warehouse_id)) 				{$warehouse_con=' and a.warehouse_id='.$warehouse_id;} else {$warehouse_con='';}

        $GetItemId = $_POST['item_id'];
        if($GetItemId>0) 					$item_id=$GetItemId;
        if(isset($item_id))				{$item_con=' and a.item_id='.$item_id;} else { $item_con = '';}

        $GetBatch = @$_POST['batch'];
        if($GetBatch>0) 					$batch=$GetBatch;
        if(isset($batch))				{$batch_con=' and a.batch='.$batch;} else { $batch_con=''; }

        if($PostStatus=='Received')
        {$status_con=' and a.item_in>0';}
        elseif($PostStatus=='Issue')
        {$status_con=' and a.item_ex>0';} else {$status_con='';}
        $i=0;
        $intotal = 0;
        $outtotal = 0;
        $result=mysqli_query($conn, 'select
        a.id as ID,
		a.ji_date as `Trnsdate`,
		i.finish_goods_code as fg_code,
		i.item_name,
		i.unit_name as UOM,
		s.sub_group_name as Category,
		i.pack_size as packsize,
		a.item_in as `INPcs`,
		a.item_ex as `OUTPcs`,
		a.item_price as rate,
		((a.item_in+a.item_ex)*a.item_price) as amount,
		a.tr_from as Source,
		w.warehouse_name as warehouse,
		a.tr_no,
		a.custom_no,
		a.entry_at,
		a.do_no,
		a.po_no,
		c.fname as User,
		a.item_price,
		a.total_amt,
		a.batch
from
    journal_item a,
    item_info i,
    users c,
    item_sub_group s,
    warehouse w

where 
    c.user_id=a.entry_by and 
	s.sub_group_id=i.sub_group_id and
	a.warehouse_id=w.warehouse_id and
	a.item_id=i.item_id '.$datecon.$warehouse_con.$item_con.$status_con.$batch_con.' order by a.ji_date,a.id asc');
        while($data=mysqli_fetch_object($result)){
            $i=$i+1; ?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                <td style="border: solid 1px #999; text-align:center"><?=$i;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->ID;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->Trnsdate;?></td>
                <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->fg_code;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->item_name;?></td>
                <td style="border: solid 1px #999; text-align:left"><?=$data->Category?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->packsize;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->Source;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->warehouse;?></td>
                <?php if($PostStatus=='Received'){?>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><? if ($data->po_no>0) echo $data->po_no;?></td>
                <?php } elseif ($PostStatus=='Issue'){?>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><? if ($data->do_no>0) echo $data->do_no;?></td>
                <?php } ?>
                <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->tr_no;?></td>
                <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->custom_no;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->entry_at;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->User;?></td>
                <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->batch;?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->INPcs>0) echo number_format($data->INPcs); else echo '-';?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->OUTPcs>0) echo number_format($data->OUTPcs); else echo '-';?></td>
            </tr>
            <?php
            $intotal=$intotal+$data->INPcs;
            $outtotal=$outtotal+$data->OUTPcs;
        } ?>
        <tr style="font-size:12px"><td colspan="<?php if($PostStatus=='Received'){ echo 15; } elseif ($PostStatus=='Issue'){ echo '15'; } else {echo '15';}?> " style="text-align:right; "><strong>Total</strong></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($intotal,2)?></strong></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($outtotal,2)?></strong></td>
        </tr>
        </tbody>
    </table>
    </div>
    </div>
    </div>
<?php } elseif ($report_id=='7003002'){
        $sql="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,s.sub_group_name,g.group_name,
REPLACE(FORMAT(SUM(j.item_in-j.item_ex), 0), ',', '') as Available_stock_balance
from
item_info i,
journal_item j,
item_sub_group s,
item_group g,
lc_lc_received_batch_split bsp
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
g.group_id in ('".$_POST['group_id']."') and
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
i.finish_goods_code not in ('2001') and 
bsp.batch=j.batch and 
bsp.status='PROCESSING'
group by j.item_id ".$order_by."";?>
<?=reportview($sql,'Present Stock',100,'','','')?>


<?php } elseif ($report_id=='7003003'){ $query="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,s.sub_group_name,g.group_name,
SUM(j.item_in-j.item_ex) as Available_stock_balance,bsp.batch_no,j.batch,bsp.status as batch_status,bsp.mfg,bsp.create_date
from
item_info i,
journal_item j,
item_sub_group s,
item_group g,
lc_lc_received_batch_split bsp
where
j.item_id=bsp.item_id and
j.batch=bsp.batch and
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
g.group_id in ('".$_POST['group_id']."') and
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
i.finish_goods_code not in ('2001') and bsp.status in ('PROCESSING')
group by bsp.batch,bsp.mfg,j.item_id order by i.item_id,j.batch,j.expiry_date asc";
$sql=mysqli_query($conn, $query);
?>
  <h2 align="center"><?=$_SESSION['company_name'];?></h2>
  <h5 align="center" style="margin-top:-15px">Present Stock (Batch-Wise)</h5>
  <h6 align="center" style="margin-top:-15px">Warehouse Name: <?= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
  <h6 align="center" style="margin-top:-15px">Date Interval from <?=$PostFDate?> to <?=$PostTDate?></h6>
  <table align="center" id="customers" style="width:95%; border: solid 1px #999; border-collapse:collapse; font-size:11px">
      <thead>
      <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
          echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
      <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
          <th style="border: solid 1px #999; padding:2px">S/L</th>
          <th style="border: solid 1px #999; padding:2px">Code</th>
          <th style="border: solid 1px #999; padding:2px">Custom Code</th>
          <th style="border: solid 1px #999; padding:2px">FG Description</th>
          <th style="border: solid 1px #999; padding:2px">FG Sub Group</th>
          <th style="border: solid 1px #999; padding:2px">FG Group</div></th>
          <th style="border: solid 1px #999; padding:2px">UOM</th>
          <th style="border: solid 1px #999; padding:2px">Pk. Size</th>
          <th style="border: solid 1px #999; padding:2px">Batch No</th>
          <th style="border: solid 1px #999; padding:2px">Batch</th>
          <th style="border: solid 1px #999; padding:2px">Batch Date</th>
          <th style="border: solid 1px #999; padding:2px">Status</th>
		  <th style="border: solid 1px #999; padding:2px">Expiry Date</th>
          <th style="border: solid 1px #999; padding:2px">Present Stock</th>
      </tr>
      </thead>
      <tbody>
        <?php $ismail =0; while($data=mysqli_fetch_object($sql)){ ?>
      <tr><td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->finish_goods_code;?></td>
      <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->sub_group_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->group_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->pack_size;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->batch_no;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->batch;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->create_date;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->batch_status;?></td>
	  <td style="border: solid 1px #999; text-align:center"><?=$data->mfg;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->Available_stock_balance,2);?></td></tr>
    <?php } ?>
      </tbody>
    </table>

<?php } elseif ($report_id=='7003004'){ $query="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,s.sub_group_name,g.group_name,
SUM(j.item_in-j.item_ex) as Available_stock_balance,j.batch,j.expiry_date
from
item_info i,
journal_item j,
item_sub_group s,
item_group g
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
g.group_id in ('".$_POST['group_id']."') and
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
i.finish_goods_code not in ('2001')
group by j.batch,j.item_id ".$order_by."";
$sql=mysqli_query($conn, $query);?>
  <h2 align="center"><?=$_SESSION['company_name'];?></h2>
  <h5 align="center" style="margin-top:-15px">Stock Expiry Date Report</h5>
  <h6 align="center" style="margin-top:-15px">Warehouse Name: <?= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
  <h6 align="center" style="margin-top:-15px">Products will expire in <?=$_POST['no_of_days']?> days</h6>
  <table align="center" id="customers" style="width:80%; border: solid 1px #999; border-collapse:collapse; font-size:11px">
      <thead>
      <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
          echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
      <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
          <th style="border: solid 1px #999; padding:2px">S/L</th>
          <th style="border: solid 1px #999; padding:2px">Code</th>
		  <th style="border: solid 1px #999; padding:2px">Custom Code</th>
          <th style="border: solid 1px #999; padding:2px">FG Description</th>
          <th style="border: solid 1px #999; padding:2px">FG Sub Group</th>
          <th style="border: solid 1px #999; padding:2px">FG Group</div></th>
          <th style="border: solid 1px #999; padding:2px">UOM</th>
          <th style="border: solid 1px #999; padding:2px">Pk. Size</th>
          <th style="border: solid 1px #999; padding:2px">Batch</th>
		  <th style="border: solid 1px #999; padding:2px">Expiry Date</th>
		  <th style="border: solid 1px #999; padding:2px">Will expire in</th>
          <th style="border: solid 1px #999; padding:2px">Present Stock</th>
      </tr>
      </thead>
      <tbody>
        <?php $ismail = 0;while($data=mysqli_fetch_object($sql)){
			                $expiry_date=$data->expiry_date;
                            $now = time(); // or your date as well
                            $your_date = strtotime("$expiry_date");
                            $days_betweens = $your_date - $now;
                            $days_between = floor($days_betweens / (60 * 60 * 24));
							if($days_between<$_POST['no_of_days']){
								$one_year=365;
								$nine_months='270';
								$six_months='180';
							?>
      <tr style="background-color:<?php 
	  if($days_between>$nine_months && $days_between<=$one_year) echo 'green'; 
	  if($days_between>$six_months && $days_between<=$nine_months) echo 'orange';
	  if($days_between<=$six_months) echo 'red'; 
	  ?>">
	  <td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
	  <td style="border: solid 1px #999; text-align:center"><?=$data->finish_goods_code;?></td>
      <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->sub_group_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->group_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->pack_size;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=$data->batch;?></td>
	  <td style="border: solid 1px #999; text-align:center"><?=$data->expiry_date;?></td>
	  <td style="border: solid 1px #999; text-align:center"><?=$days_between;?></td>
      <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->Available_stock_balance,2);?></td></tr>
    <?php }} ?>
      </tbody>
    </table>


<? } ?>
