<?php

require_once 'support_file.php';
$title='Report';
$f_date = @$_POST['f_date'];
$t_date = @$_POST['t_date'];
//$f_date=date('Y-m-d' , strtotime($_POST['f_date']));
//$t_date=date('Y-m-d' , strtotime($_POST['t_date']));
$from_date = @$_POST['f_date'];
$pfrom_date = @$_POST['pf_date'];
$pto_date = @$_POST['pt_date'];

$ledger_id=@$_REQUEST["ledger_id"];
$req_datefrom = @$_REQUEST['datefrom'];

$warehouseid=@$_POST['warehouse_id'];

$companyid=@$_SESSION['companyid'];
$sectionid = @$_SESSION['sectionid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
} else {
    $sec_com_connection=" and j.company_id='".$_SESSION['companyid']."' and j.section_id in ('400000','".$_SESSION['sectionid']."')";
}
$date_checking = find_all_field('dev_software_data_locked','','status="LOCKED" and section_id="'.$_SESSION['sectionid'].'" and company_id="'.$_SESSION['companyid'].'"');
if($date_checking>0) {
    $lockedStartInterval = @$date_checking->start_date;
    $lockedEndInterval = @$date_checking->end_date;
} else
{
    $lockedStartInterval = '';
    $lockedEndInterval = '';
}
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
        {document.getElementById("pr").style.display = "none";}
    </script>
    <style>
        #customers {}
        #customers td {}
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
        td{}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
</head>
<body style="font-family: "Gill Sans", sans-serif;">
<div id="pr" style="margin-left:48%">
    <div align="left">
        <form id="form1" name="form1" method="post" action="">
            <p><input name="button" type="button" onclick="hide();window.print();" value="Print" /></p>
        </form>
    </div>
</div>

<?php if ($_POST['report_id']=='1012007'):
    if($sectionid=='400000'){
        $sec_com_connection=' and 1';
    } else {
        $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
    }?>
    <style>
        #customers {
            font-family: "Gill Sans", sans-serif;
        }
        #customers td {
        }
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
    </style>
    <title><?=$ledger_name=getSVALUE('accounts_ledger','ledger_name','where ledger_id='.$_REQUEST['ledger_id']);?> | Transaction Statement</title>
    <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
    <p align="center" style="margin-top:-18px; font-size: 15px">Transaction Statement</p>
    <p align="center" style="margin-top:-10px; font-size: 12px; font-weight: bold"><?=($_REQUEST['ledger_id']>0)? 'Customer: '.$_REQUEST['ledger_id'].' - '.$ledger_name.'' : '' ?></p>
    <?php $PostCcCode = @$_POST['cc_code']; if($PostCcCode){ ?>
    <p align="center" style="margin-top:-10px; font-size: 12px"><strong>Cost Center:</strong> <?=find_a_field('cost_center','center_name','id='.$_REQUEST['cc_code']);?> (<?=$_REQUEST['cc_code'];?>)</p>
<?php } ?>
    <?php $PostTrFrom = @$_POST['tr_from']; if($PostTrFrom){ ?>
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
            <th style="border: solid 1px #999; padding:2px">Dr Amt</th>
            <th style="border: solid 1px #999; padding:2px">Cr Amt</th>
            <th style="border: solid 1px #999; padding:2px;">Balance</th>
        </tr></thead>
        <tbody>
        <?php

        if($PostTrFrom!=''){
            $emp_id.=" and a.tr_from='".$tr_from."'";}
        $total_sql = "select sum(a.dr_amt),sum(a.cr_amt) from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and a.ledger_id like '".$_POST['ledger_id']."'";
        $total=mysqli_fetch_array(mysqli_query($conn, $total_sql));

        $c="select sum(a.dr_amt)-sum(a.cr_amt) from
            journal a,
            accounts_ledger b
            where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jvdate<'".$_POST['f_date']."' and a.ledger_id like '".$_POST['ledger_id']."'";
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
a.visible_status=1 and
a.cc_code=c.id and
a.ledger_id=b.ledger_id and
a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and
a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
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
            <td align="right" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="right" bgcolor="#FFCCFF">&nbsp;</td>
            <td align="right" bgcolor="#FFCCFF"><?php if($blance>0) echo '(Dr)'.number_format($blance,2); elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),0,'.','');else echo "0.00"; ?></td>
        </tr>

        <?php
        $sql=mysqli_query($conn, $p);
        $i= 0;
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
                <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data[2],2,'.',',');?></td>
                <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data[3],2,'.',',');?></td>
                <td align="right" bgcolor="#FFCCFF" style="border: solid 1px #999; padding:2px"><?php $blance = $blance+($data[2]-$data[3]);
                    if($blance>0) echo '(Dr)'.number_format($blance,2,'.',',');
                    elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00"; ?></td>
            </tr>
        <?php } ?>
        <tr style="font-size: 11px">
            <th colspan="5"  style="border: solid 1px #999; padding:2px; text-align: right"><strong>Total : </strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?php echo number_format($total[0],2);?></strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?php echo number_format($total[1],2);?></strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; width: 10%; text-align: right"><?php if($blance>0) echo '(Dr)'.number_format($blance,2,'.',',');
                elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00";
                ?></div>
            </th>
        </tr>
        </tbody>
    </table>


<?php elseif ($_POST['report_id']=='1012009'):

    $query="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,
SUM(j.item_in-j.item_ex) as Available_stock_balance,bsp.batch_no,bsp.rate,j.batch,bsp.status as batch_status,bsp.mfg,bsp.create_date
from
item_info i,
item_brand b,
journal_item j,
lc_lc_received_batch_split bsp
where
j.item_id=bsp.item_id and
j.batch=bsp.batch and
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
b.vendor_id='".$_POST['pc_code']."' and
i.brand_id=b.brand_id and
i.finish_goods_code not in ('2001') and bsp.status in ('PROCESSING')
group by bsp.batch,bsp.mfg,j.item_id order by i.item_id,j.batch,j.expiry_date asc";
$sql=mysqli_query($conn, $query);
?>
<h2 align="center"><?=$_SESSION['company_name'];?></h2>
<h5 align="center" style="margin-top:-15px">Present Stock (Batch-Wise)</h5>
<h6 align="center" style="margin-top:-15px">Warehouse Name: <?= getSVALUE('warehouse','warehouse_name','WHERE warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
<h6 align="center" style="margin-top:-15px">Date Interval from <?=$f_date?> to <?=$t_date?></h6>
<table align="center" id="customers" style="width:95%; border: solid 1px #999; border-collapse:collapse; font-size:11px">
    <thead>
    <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
        echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
    <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
        <th style="border: solid 1px #999; padding:2px">S/L</th>
        <th style="border: solid 1px #999; padding:2px">Custom Code</th>
        <th style="border: solid 1px #999; padding:2px">FG Description</th>
        <th style="border: solid 1px #999; padding:2px">UOM</th>
        <th style="border: solid 1px #999; padding:2px">Pk. Size</th>
        <th style="border: solid 1px #999; padding:2px">Batch No</th>
        <th style="border: solid 1px #999; padding:2px">Batch Status</th>
        <th style="border: solid 1px #999; padding:2px">Expiry Date</th>
        <th style="border: solid 1px #999; padding:2px">Present Stock</th>
        <th style="border: solid 1px #999; padding:2px">Rate</th>
        <th style="border: solid 1px #999; padding:2px">Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php $ismail = 0;
    $totalValues =0;
    $totalStock = 0;
    while($data=mysqli_fetch_object($sql)){ if ($data->Available_stock_balance>0){ ?>
        <tr><td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
            <td style="border: solid 1px #999; text-align:left"><?=$data->finish_goods_code;?></td>
            <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->pack_size;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->batch_no;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->batch_status;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->mfg;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->Available_stock_balance,2);?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->rate;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$totalValue=$data->Available_stock_balance*$data->rate;?></td>
        </tr>
    <?php
        $totalValues = $totalValues+$totalValue;
        $totalStock = $totalStock+$pstock;
    }} ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="8" style="border: solid 1px #999; text-align:right">Total value of stock</th>
        <th style="border: solid 1px #999; text-align:center"><?=number_format($totalStock,2)?></th>
        <th></th>
        <th style="text-align: right; border: solid 1px #999; text-align:center"><?=number_format($totalValues,2)?></th>
    </tr>
    </tfoot>
</table>

<?php elseif ($_POST['report_id']=='1012010'):
    if($sectionid=='400000'){
        $sec_com_connection=' and 1';
    } else {
        $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
    }?>
    <title><?=$ledger_name=getSVALUE('accounts_ledger','ledger_name','where ledger_id='.$_REQUEST['ledger_id']);?> | Transaction Statement</title>
    <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
    <p align="center" style="margin-top:-18px; font-size: 15px">Dealer Commission</p>
    <p align="center" style="margin-top:-10px; font-size: 12px; font-weight: bold"><?=($_REQUEST['dealer_code']>0)? 'Customer: '.$_REQUEST['dealer_code'].' - '.$ledger_name.'' : '' ?></p>
    <p align="center" style="margin-top:-10px; font-size: 11px"><strong>Period From :</strong> <?=$_POST['f_date']?> to <?=$_POST['t_date']?></p>
    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
        <thead>
        <p style="width:95%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
            <th style="border: solid 1px #999; padding:2px">SL</th>
            <th style="border: solid 1px #999; padding:2px; width:5%">Date</th>
            <th style="border: solid 1px #999; padding:2px; width:10%">Transaction No</th>
            <th style="border: solid 1px #999; padding:2px;">Party Name</th>
            <th style="border: solid 1px #999; padding:2px">Particulars</th>
            <th style="border: solid 1px #999; padding:2px">Source</th>
            <th style="border: solid 1px #999; padding:2px">Commission Amount</th>
        </tr></thead>
        <tbody>
        <?php
        $c="select sum(a.dr_amt)-sum(a.cr_amt) from
            journal a,
            accounts_ledger b
            where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jvdate<'".$_POST['f_date']."' and a.ledger_id like '".$_POST['ledger_id']."'";
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
c.center_name,
a.do_no,
(select dealer_code from sale_do_master where do_no=a.do_no) as dealer_code

from
    
journal a,
accounts_ledger b,
users u,
cost_center c

where
a.visible_status=1 and    
a.cc_code=c.id and
a.ledger_id=b.ledger_id and
a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and
a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
a.ledger_id like '".$_POST['ledger_id']."' and
a.user_id=u.user_id
order by a.jvdate,a.id";?>
        <?php
        $sql=mysqli_query($conn, $p);
        $i = 0;
        $total = 0;
        while($data=mysqli_fetch_row($sql)){?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$i=$i+1;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[0];?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[7]?></td>
                <td align="left" style="border: solid 1px #999; padding:2px; "><?=find_a_field('dealer_info','dealer_name_e','dealer_code='.$data[18]);?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$data[5];?><?=(($data[9]!='')?'-Cq#'.$data[9]:'');?><?=(($data[10]>943898400)?'-Cq-Date#'.date('d-m-Y',$data[10]):'');?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$data[4];?></td>
                <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data[2],2);?></td>
               </tr>
        <?php
        $total = $total +$data[2];
        } ?>
        <tr style="font-size: 11px">
            <th colspan="6"  style="border: solid 1px #999; padding:2px; text-align: right">Total</th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?=number_format($total,2);?></strong></th>
        </tr>
        </tbody>
    </table>


<?php elseif ($_POST['report_id']=='1002008'):?>
    <?php
    if($sectionid=='400000'){
        $sec_com_connection=' 1';
    } else {
        $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
    }
    ?>
    <title>Trial Balance (Sub Ledger)</title>
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
    <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
    <p align="center" style="margin-top:-20px">Statement of <?=find_a_field('accounts_ledger','ledger_name','ledger_id='.$_POST['ledger_id'])?></p>
    <p align="center" style="margin-top:-12px; font-size: 11px">As On: <?=$_POST['t_date']?></p>
    <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
        <thead>
        <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #f5f5f5" >
            <th style="border: solid 1px #999; padding:2px; width: 4%"><strong>SL</strong></th>
            <th style="border: solid 1px #999; padding:2px;"><strong>Account Particulars</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Debit Amount</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Credit Amount</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Balance</strong></th>
        </tr></thead>
        <tbody>
        <?php
        $i = 0;
        $dr_total = 0;
        $cr_total = 0;
        $sql = mysqli_query($conn, "SELECT a.* from sub_ledger a where a.ledger_id=".$_POST['ledger_id']."".$sec_com_connection."");
        while($data=mysqli_fetch_object($sql)){
            $sl_sql = "SELECT SUM(a.dr_amt) as dr_amt,SUM(a.cr_amt) as cr_amt from journal a where a.visible_status=1 and a.ledger_id=".$data->sub_ledger_id;
            $sl_result = mysqli_query($conn, $sl_sql);
            $sl_data=mysqli_fetch_object($sl_result);

            $ssl_dr_total = 0;
            $ssl_cr_total = 0;
        $sql_sub_sub_ledger = mysqli_query($conn, "SELECT a.* from sub_sub_ledger a where a.sub_ledger_id=".$data->sub_ledger_id."".$sec_com_connection."");
        while($data2=mysqli_fetch_object($sql_sub_sub_ledger)) {
            $ssl_sql = "SELECT SUM(a.dr_amt) as dr_amt,SUM(a.cr_amt) as cr_amt from journal a where a.visible_status=1 and a.ledger_id=" . $data2->sub_sub_ledger_id;
            $ssl_result = mysqli_query($conn, $ssl_sql);
            $ssl_data = mysqli_fetch_object($ssl_result);

            $ssl_dr_total =$ssl_dr_total+$ssl_data->dr_amt;
            $ssl_cr_total =$ssl_cr_total+$ssl_data->cr_amt;

        }


            $dr = @$sl_data->dr_amt+$ssl_dr_total;
            $cr = @$sl_data->cr_amt+$ssl_cr_total;
        ?>
        <tr style="border: solid 1px #999; font-size:11px">
            <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$i=$i+1;?></td>
            <td style="border: solid 1px #999; padding:2px 10px 2px 2px; text-align: left"><?=$data->sub_ledger_id?> : <?=$data->sub_ledger?></td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format(($dr),2);?></td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format(($cr),2);?></td>
            <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($dr-$cr,2);?></td>
        </tr>
        <?php
        $dr_total = $dr_total+$dr;
        $cr_total = $cr_total+$cr;
        } ?>

        <tr  style="font-size: 12px">
            <th colspan="2" style="border: solid 1px #999;  text-align: right;"><strong>Total Balance : </strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($dr_total,2);?></strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($cr_total,2)?></strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format(($dr_total-$cr_total),2);?></strong></th>
        </tr>
        </tbody>
    </table>


<?php elseif ($_POST['report_id']=='1002009'):?>
    <?php
    if($sectionid=='400000'){
        $sec_com_connection=' 1';
    } else {
        $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
    }
    ?>
    <title>Trial Balance (Sub Ledger)</title>
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
    <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
    <p align="center" style="margin-top:-20px">Statement of <?=find_a_field('sub_ledger','sub_ledger','sub_ledger_id='.$_POST['sub_ledger_id'])?></p>
    <p align="center" style="margin-top:-12px; font-size: 11px">As On: <?=$_POST['t_date']?></p>
    <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
        <thead>
        <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #f5f5f5" >
            <th style="border: solid 1px #999; padding:2px; width: 4%"><strong>SL</strong></th>
            <th style="border: solid 1px #999; padding:2px;"><strong>Account Particulars</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Debit Amount</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Credit Amount</strong></th>
            <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Balance</strong></th>
        </tr></thead>
        <tbody>
        <?php
        $i = 0;
        $dr_total = 0;
        $cr_total = 0;
        $sql = mysqli_query($conn, "SELECT a.* from sub_sub_ledger a where a.sub_ledger_id=".$_POST['sub_ledger_id']."".$sec_com_connection."");
        while($data=mysqli_fetch_object($sql)){
            $sl_sql = "SELECT SUM(a.dr_amt) as dr_amt,SUM(a.cr_amt) as cr_amt from journal a where a.visible_status=1 and a.ledger_id=".$data->sub_sub_ledger_id;
            $sl_result = mysqli_query($conn, $sl_sql);
            $sl_data=mysqli_fetch_object($sl_result);
            $dr = @$sl_data->dr_amt;
            $cr = @$sl_data->cr_amt;
            ?>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$i=$i+1;?></td>
                <td style="border: solid 1px #999; padding:2px 10px 2px 2px; text-align: left"><?=$data->sub_sub_ledger_id?> : <?=$data->sub_sub_ledger?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format(($dr),2);?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format(($cr),2);?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($dr-$cr,2);?></td>
            </tr>
            <?php
            $dr_total = $dr_total+$dr;
            $cr_total = $cr_total+$cr;
        } ?>

        <tr  style="font-size: 12px">
            <th colspan="2" style="border: solid 1px #999;  text-align: right;"><strong>Total Balance : </strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($dr_total,2);?></strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($cr_total,2)?></strong></th>
            <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format(($dr_total-$cr_total),2);?></strong></th>
        </tr>
        </tbody>
    </table>

<?php elseif ($_POST['report_id']=='1001002'):?>
    <?php
    if($sectionid=='400000'){
        $sec_com_connection=' 1';
    } else {
        $sec_com_connection=" i.company_id='".$_SESSION['companyid']."' and i.section_id in ('400000','".$_SESSION['sectionid']."')";
    }
    $sql="SELECT i.item_id,i.item_id,i.finish_goods_code as custom_code,i.item_name,i.consumable_type,i.product_nature,i.unit_name,b.brand_name,i.d_price,i.t_price,i.m_price,FORMAT(i.production_cost,2) as pro_cost,i.material_cost,
FORMAT(i.SD,3) as SD,i.SD_percentage as 'SD (%)',FORMAT(i.VAT,3) as VAT,i.VAT_percentage as 'VAT (%)',(select group_name from VAT_item_group where i.VAT_item_group=group_id) as VAT_item_group,hs.H_S_code,sg.sub_group_name,g.group_name,s.section_name as branch
from item_info i,
item_sub_group sg,
item_group g,
item_tariff_master hs,
company s,
item_brand b
where
i.section_id=s.section_id and 
i.H_S_code=hs.id and
i.brand_id=b.brand_id and
i.sub_group_id=sg.sub_group_id and
sg.group_id=g.group_id and
i.status in ('".$_POST['status']."') and
".$sec_com_connection."

order by i.".$_POST['order_by'].""?>
    <?=reportview($sql,'Item Info Master','99',0,'',0); ?>


<?php elseif ($_POST['report_id']=='1012001'):?>
    <?php
    $sql="SELECT p.po_no,m.po_no,m.po_date,v.vendor_name,i.item_id,i.finish_goods_code as 'FG Code (Custom Code)',item_name as 'Mat. Description',i.unit_name as 'UoM',p.qty,FORMAT(p.rate,2) as rate,FORMAT(p.amount,2) as amount 
from purchase_invoice p,purchase_master m,vendor v,item_info i 
where 
p.po_no=m.po_no and m.vendor_id=v.vendor_id  and
i.item_id=p.item_id and 
v.vendor_id='".$_POST['pc_code']."' and 
m.po_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
p.po_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'
order by m.po_no,v.vendor_id"?>
    <?=reportview($sql,'Purchase Report','99',0,'',0); ?>

<?php elseif ($_POST['report_id']=='1012015'):?>
    <?php
    $sql="SELECT p.po_no,m.po_no,m.po_date,v.vendor_name,i.item_id,i.finish_goods_code as 'FG Code (Custom Code)',item_name as 'Mat. Description',i.unit_name as 'UoM',p.qty,FORMAT((p.rate * (1 + 15 / 100)),2) as rate,FORMAT((p.qty*(p.rate * (1 + 15 / 100))),2) as amount 
from purchase_invoice p,purchase_master m,vendor v,item_info i 
where 
p.po_no=m.po_no and m.vendor_id=v.vendor_id  and
i.item_id=p.item_id and 
v.vendor_id='".$_POST['pc_code']."' and 
m.po_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
p.po_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'
order by m.po_no,v.vendor_id"?>
    <?=reportview($sql,'Purchase Report (Including VAT)','99',0,'',0); ?>


<?php elseif ($_POST['report_id']=='1012013'):?>
    <?php
    $sql="SELECT p.id,m.id,m.return_date,v.vendor_name,i.item_id,i.finish_goods_code as 'FG Code (Custom Code)',item_name as 'Mat. Description',i.unit_name as 'UoM',p.qty,FORMAT(p.rate,2) as rate,FORMAT(p.amount,2) as amount 
from purchase_return_details p,purchase_return_master m,vendor v,item_info i 
where 
p.m_id=m.id and 
m.vendor_id=v.vendor_id  and
i.item_id=p.item_id and 
v.vendor_id='".$_POST['pc_code']."' and 
m.return_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
p.return_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'
order by m.id,v.vendor_id"?>
    <?=reportview($sql,'Purchase Return Report','99',0,'',0); ?>

<?php elseif ($_POST['report_id']=='1012002'):?>
    <?php
    $sql="WITH cash_discount_cte AS (
    SELECT 
        do_no,
        gift_on_item,
        SUM(total_amt) AS cash_discount
    FROM 
        sale_do_details
    WHERE 
        item_id = '1096000100010312'
    GROUP BY 
        do_no, gift_on_item
),
commission_cte AS (
    SELECT 
        do_no,
        SUM(total_unit) AS total_units_for_commission,
        SUM(CASE WHEN total_amt > 0 THEN total_unit ELSE 0 END) AS total_positive_units
    FROM 
        sale_do_details
    GROUP BY 
        do_no
)
SELECT 
    sdd.id,
    sdd.id AS `T.ID`,
    w.warehouse_name AS Depot,
    d.dealer_custom_code AS `DB Code`,
    d.dealer_name_e AS `Dealer Name`,
    d.dealer_type,
    sdd.do_no,
    sdd.do_date,
    sdd.do_type,
    t.AREA_NAME AS Territory,
    r.BRANCH_NAME AS Region,
    i.finish_goods_code AS `FG Code`,
    i.item_name AS `FG Description`,
    i.unit_name AS UoM,
    i.pack_size,
    sdd.unit_price,
    sdd.total_unit AS qty,
    FORMAT(sdd.total_amt, 2) AS amount,
    cdc.cash_discount,
    CASE 
        WHEN sdd.total_amt > 0 THEN 
            (m.commission_amount / cc.total_positive_units) * sdd.total_unit
        ELSE '-' 
    END AS commission,
    CASE 
        WHEN sdd.total_amt > 0 THEN 'sales' 
        ELSE 'free' 
    END AS sales_for
FROM 
    sale_do_details sdd
JOIN 
    warehouse w ON sdd.depot_id = w.warehouse_id
JOIN 
    dealer_info d ON sdd.dealer_code = d.dealer_code
JOIN 
    branch r ON d.region = r.BRANCH_ID
JOIN 
    area t ON d.area_code = t.AREA_CODE
JOIN 
    item_info i ON sdd.item_id = i.item_id
JOIN 
    sale_do_master m ON m.do_no = sdd.do_no
LEFT JOIN 
    cash_discount_cte cdc ON cdc.do_no = sdd.do_no AND cdc.gift_on_item = sdd.item_id
LEFT JOIN 
    commission_cte cc ON cc.do_no = sdd.do_no
WHERE 
    d.dealer_category = '".$_POST['pc_code']."' 
    AND sdd.item_id NOT IN ('1096000100010312') 
    AND sdd.do_date BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' 
    AND sdd.do_date NOT BETWEEN '".$lockedStartInterval."' AND '".$lockedEndInterval."'"?>
    <?=reportview($sql,'Sales Report','99',0,'',0); ?>



<?php elseif ($_POST['report_id']=='1012011'):

    $sql="SELECT sdd.id,sdd.id as 'T.ID',w.warehouse_name as Depot,d.dealer_custom_code as 'DB Code',
d.dealer_name_e as 'Dealer Name',d.dealer_type,sdd.do_no,sdd.do_date,t.AREA_NAME as 'Territory',r.BRANCH_NAME as region,
i.finish_goods_code as 'FG Code',i.item_name as 'FG Description',i.unit_name as UoM,i.pack_size,sdd.unit_price,sdd.total_unit as qty,
sdd.total_amt as amount

from sale_return_details sdd,warehouse w,dealer_info d,branch r,area t,item_info i
where sdd.depot_id=w.warehouse_id and
      sdd.dealer_code=d.dealer_code and
      d.dealer_category='".$_POST['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      sdd.item_id=i.item_id and 
      sdd.item_id not in ('1096000100010312') and
      sdd.do_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
      sdd.do_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' 
      "?>

    <?=reportview($sql,'Sales Return Report','99',0,'',0); ?>


<?php elseif ($_POST['report_id']=='1012012'):

    $sql="SELECT c.id,c.receipt_no as 'Collection Id',c.receiptdate as 'Collection Date',d.dealer_custom_code as 'Coustomer Code',d.account_code as 'Ledger ID',d.dealer_name_e as 'Customer Name',r.BRANCH_NAME as 'Customer Group',t.AREA_NAME as 'Territory',d.address_e as 'address',d.mobile_no as 
'Phone No',c.bank,c.narration as 'Particulars',FORMAT(c.cr_amt,2) as 'Amount'

from receipt c,
     dealer_info d,
     branch r,
     area t
where c.ledger_id=d.account_code and
      d.dealer_category='".$_POST['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      c.receiptdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
      c.receiptdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' order by c.receipt_no desc";

    $totalCollectionS = "SELECT SUM(c.cr_amt) as amount
from receipt c,
     dealer_info d,
     branch r,
     area t
where c.ledger_id=d.account_code and
      d.dealer_category='".$_POST['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      c.receiptdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
      c.receiptdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'
      ";
    $result = mysqli_fetch_object(mysqli_query($conn, $totalCollectionS));
    $totalCollection = $result->amount;
    ?>
    <?=reportview($sql,'Collection Register','99',$totalCollection,'12',0); ?>

<?php elseif ($_POST['report_id']=='1012014'):


    $totalCollectionS = "SELECT 
    SUM(c.cr_amt) AS amount
FROM 
    journal_info c,
    dealer_info d,
    branch r,
    area t
WHERE 
    d.region = r.BRANCH_ID AND 
    d.area_code = t.AREA_CODE AND
    c.ledger_id = d.account_code AND
    c.type = 'Credit' AND 
    c.cr_amt > 0 AND 
    c.j_date BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' AND 
    c.j_date NOT BETWEEN '".$lockedStartInterval."' AND '".$lockedEndInterval."' AND
    c.journal_info_no IN (
        SELECT journal_info_no
        FROM journal_info
        WHERE ledger_id = '2002018700000000' AND dr_amt > 0
    )";
    $result = mysqli_fetch_object(mysqli_query($conn, $totalCollectionS));
    $totalCollection = $result->amount;

    $sql = "SELECT 
    c.id,
    c.journal_info_no as 'Ref No.', 
    c.j_date as 'Entry Date',
    d.dealer_custom_code as 'Customer Code',
    c.ledger_id AS Ledger_id, 
    d.dealer_name_e as 'Customer Name',
    r.BRANCH_NAME as 'Customer Group',
    t.AREA_NAME as 'Territory',    
    d.address_e as 'address',
    d.mobile_no as 'Phone No',
    c.narration as 'Particulars',
    FORMAT(c.cr_amt,2) as 'Amount'
FROM 
    journal_info c,
    dealer_info d,
    branch r,
    area t
    
WHERE 
    d.region=r.BRANCH_ID and 
    d.area_code=t.AREA_CODE and
    c.ledger_id=d.account_code and
    c.type = 'Credit' AND 
    c.cr_amt > 0 AND 
    c.j_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and 
    c.j_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and
    c.journal_info_no IN (
        SELECT journal_info_no
        FROM journal_info
        WHERE ledger_id = '2002018700000000' AND dr_amt > 0
    ) order by c.j_date,c.journal_info_no";
    ?>


    <?=reportview($sql,'Adjustment Register','99',$totalCollection,'11',''); ?>


<?php elseif ($_POST['report_id']=='1012003'):

    if($_POST['pc_code']=='14'){
    $sql="Select i.item_id,i.finish_goods_code,i.item_name,i.productCategory as category,i.unit_name,i.pack_size,i.m_price as 'MRP Price',
REPLACE(FORMAT(SUM(j.item_in-j.item_ex), 0), ',', '') as Available_stock_balance
from
item_info i,
journal_item j,
item_brand b
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
i.brand_id=b.brand_id and
b.vendor_id='".$_POST['pc_code']."'
group by j.item_id"; } else {
        $sql="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,
REPLACE(FORMAT(SUM(j.item_in-j.item_ex), 0), ',', '') as Available_stock_balance
from
item_info i,
journal_item j,
item_brand b
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$_POST['t_date']."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
i.brand_id=b.brand_id and
b.vendor_id='".$_POST['pc_code']."'
group by j.item_id";
    }?>
<?=reportview($sql,'Present Stock',100,0,'',0)?>



<?php elseif ($_POST['report_id']=='1012004'):?>
    <?php
    $sql="SELECT d.dealer_code,d.dealer_custom_code as 'DB Code',d.account_code as ledger_id,
d.dealer_name_e as 'Dealer Name',t.AREA_NAME as 'Territory',r.BRANCH_NAME as region,
d.credit_limit as current_credit_limit,                                               
IF(SUM(j.dr_amt-j.cr_amt)>'0',CONCAT(' (Dr) ', SUM(j.dr_amt-j.cr_amt)),CONCAT('(Cr) ',SUBSTR(SUM(j.dr_amt-j.cr_amt),2))) as balance                                               
from dealer_info d,branch r,area t,journal j
where 
      j.visible_status=1 and
      d.dealer_category='".$_POST['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      j.jvdate<='".$_POST['t_date']."' and 
      j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
      d.account_code=j.ledger_id group by d.account_code
      "?>
    <?=reportview($sql,'Customer Outstanding Report','99',0,'',0); ?>



<?php elseif($_POST['report_id']==1012005 && $_POST['status']==1): ?>
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
    <title>Undelivered Invoice List</title>
    <h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
    <h4 align="center" style="margin-top:-15px">Undelivered Invoice List</h4>
    <?php if($_POST['dealer_code']){?>
        <h5 align="center" style="margin-top:-15px">Dealer : <?=find_a_field('dealer_info','dealer_name_e','dealer_code='.$_POST['dealer_code'].'')?></h5>
    <?php } ?>
    <?php if($_POST['warehouse_id']){?>
        <h5 align="center" style="margin-top:-15px">Warehouse : <?=find_a_field('warehouse','warehouse_name','warehouse_id='.$_POST['warehouse_id'].'')?></h5>
    <?php } ?>
    <div class="col-md-12 head">
        <div style="float: left; margin-left: 2%">
            <?php echo '<a href="export.php?f_date='.$_POST['f_date'].'&t_date='.$_POST['t_date'].'&report_id='.$_POST['report_id'].'&warehouse_id='.$_POST['warehouse_id'].'" target="_blank" class="btn btn-success"><i class="dwn"></i> Export</a>';?>
        </div>
    </div>
    <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
    <?php
    $datecon=' and m.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
    if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
    if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;} else {$warehouse_id_CON='';}
    if($_POST['dealer_code']>0) 			 $dealer_code=$_POST['dealer_code'];
    if(isset($dealer_code))				{$dealer_code_CON=' and m.dealer_code='.$dealer_code;} else {$dealer_code_CON='';}
    $sql="select
distinct 

m.do_no,
m.do_date,
d.dealer_custom_code,
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

m.cash_discount commission,
m.commission_amount as comissionamount,
m.do_type,
SUM(c.total_amt)as invoice_amount,
(SELECT SUM(total_amt) from sale_do_details where do_no=c.do_no and item_id=1096000100010312) as discount
from
sale_do_master m,
sale_do_details c,
dealer_info d ,
warehouse w,
area a,
personnel_basic_info p

where
d.dealer_category='".$_POST['pc_code']."' and 
m.do_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
a.AREA_CODE=d.area_code
and m.status not in ('COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
c.item_id not in ('1096000100010312') and
a.PBI_ID=p.PBI_ID".$warehouse_id_CON.$datecon.$dealer_code_CON."
group by c.do_no
order by c.do_no";
    $query = mysqli_query($conn, $sql); ?>


    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse;">
        <thead>
        <p style="width:95%; text-align:right; font-size:10px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; background-color:#f5f5f5; font-size:11px">
            <th style="border: solid 1px #999; padding:2px">S/L</th>
            <th style="border: solid 1px #999; padding:2px">Do No</th>
            <th style="border: solid 1px #999; padding:2px">Do Date</th>
            <th style="border: solid 1px #999; padding:2px">Do Type</th>
            <th style="border: solid 1px #999; padding:2px">Dealer Code</th>
            <th style="border: solid 1px #999; padding:2px">Dealer Name</th>
            <th style="border: solid 1px #999; padding:2px">Territory</th>
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
        $discounttotal =0;
        $total_invoice_amount =0;
        $totalamts = 0;
        $actualsalestotalamts = 0;
        $totalsaleafterdiscounts =0;
        $totalcomissionamount = 0;
        while($data=mysqli_fetch_object($query)){$s++; ?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
                <td style="border: solid 1px #999; text-align:center"><?=$s?></td>
                <td style="border: solid 1px #999; text-align:center"><a href="invoice_view.php?do_no=<?=$data->do_no?>" target="_blank"><?=$data->do_no;?></a></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->do_date;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->do_type;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->dealer_custom_code;?></td>
                <td style="border: solid 1px #999; text-align:left"><?=$data->dealer_name;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->area;?></td>
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
            $actualsalestotalamts=$actualsalestotalamts+$totalamts;

            $totalsaleafterdiscounts=$totalsaleafterdiscounts+$totalsaleafterdiscount;
            $totalcomissionamount=$totalcomissionamount+$data->comissionamount;

        } ?>
        <tr style="font-size:11px; font-weight:bold">
            <td colspan="8" style="border: solid 1px #999; text-align:right;  padding:2px">Total</td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($discounttotal,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalcomissionamount,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount-($discounttotal+$totalcomissionamount),2);?></td>
        </tr></tbody>
    </table>
<?php elseif($_POST['report_id']==1012005 && $_POST['status']==2): ?>
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
<title>Delivered Invoice List</title>
<h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
<h4 align="center" style="margin-top:-15px">Delivered Invoice List</h4>
<?php if($_POST['dealer_code']){?>
<h5 align="center" style="margin-top:-15px">Dealer : <?=find_a_field('dealer_info','dealer_name_e','dealer_code='.$_POST['dealer_code'].'')?></h5>
<?php } ?>
<?php if($_POST['warehouse_id']){?>
<h5 align="center" style="margin-top:-15px">Warehouse : <?=find_a_field('warehouse','warehouse_name','warehouse_id='.$_POST['warehouse_id'].'')?></h5>
<?php } ?>
    <div class="col-md-12 head">
        <div style="float: left; margin-left: 2%">
           <?php echo '<a href="export.php?f_date='.$_POST['f_date'].'&t_date='.$_POST['t_date'].'&report_id='.$_POST['report_id'].'&warehouse_id='.$_POST['warehouse_id'].'" target="_blank" class="btn btn-success"><i class="dwn"></i> Export</a>';?>
        </div>
    </div>
<h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
<?php
$datecon=' and m.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;} else {$warehouse_id_CON='';}
if($_POST['dealer_code']>0) 			 $dealer_code=$_POST['dealer_code'];
if(isset($dealer_code))				{$dealer_code_CON=' and m.dealer_code='.$dealer_code;} else {$dealer_code_CON='';}
$sql="select
distinct c.chalan_no,

c.chalan_date,
m.do_no,
m.do_date,
d.dealer_custom_code,
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
d.dealer_category='".$_POST['pc_code']."' and 
a.AREA_CODE=d.area_code
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
c.item_id not in ('1096000100010312') and
a.PBI_ID=p.PBI_ID".$warehouse_id_CON.$datecon.$dealer_code_CON."
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
        <th style="border: solid 1px #999; padding:2px">Dealer Code</th>
        <th style="border: solid 1px #999; padding:2px">Dealer Name</th>
        <th style="border: solid 1px #999; padding:2px">Territory</th>
        <th style="border: solid 1px #999; padding:2px">Depot</th>
        <th style="border: solid 1px #999; padding:2px">Invoice Amount</th>
        <th style="border: solid 1px #999; padding:2px">Discount</th>
        <th style="border: solid 1px #999; padding:2px">Commission</th>
        <th style="border: solid 1px #999; padding:2px">Receivable Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php $s=0;
    $discounttotal =0;
    $total_invoice_amount =0;
    $totalamts = 0;
    $actualsalestotalamts = 0;
    $totalsaleafterdiscounts =0;
    $totalcomissionamount = 0;
    while($data=mysqli_fetch_object($query)){$s++; ?>
        <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
            <td style="border: solid 1px #999; text-align:center"><?=$s?></td>
            <td style="border: solid 1px #999; text-align:center"><a href="delivery_chalan_view.php?v_no=<?=$data->chalan_no?>" target="_blank"><?=$data->chalan_no?></a></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->chalan_date?></td>
            <td style="border: solid 1px #999; text-align:center"><a href="invoice_view.php?do_no=<?=$data->do_no?>" target="_blank"><?=$data->do_no;?></a></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->do_date;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->do_type;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->dealer_custom_code;?></td>
            <td style="border: solid 1px #999; text-align:left"><?=$data->dealer_name;?></td>
            <td style="border: solid 1px #999; text-align:center"><?=$data->area;?></td>
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
        $actualsalestotalamts=$actualsalestotalamts+$totalamts;

        $totalsaleafterdiscounts=$totalsaleafterdiscounts+$totalsaleafterdiscount;
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



<?php elseif ($_POST['report_id']=='1012006'):?>
    <?php $title = 'Collection and Shipment Report';
    $sqls="SELECT d.dealer_code as dealer_code,d.dealer_custom_code,
d.dealer_name_e as dealer_name,d.account_code,t.AREA_NAME as 'Territory',r.BRANCH_NAME as region,

(select sum(cr_amt)-sum(dr_amt) from journal  where visible_status=1 and ledger_id=d.account_code and jvdate<'$f_date' and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as opening,
(select SUM(cr_amt) from journal where ledger_id=d.account_code and jvdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and tr_from='receipt' and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as collection,
(select SUM(cr_amt) from journal where ledger_id=d.account_code and jvdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and tr_from in ('SalesReturn') and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as salesReturn,
(select SUM(cr_amt) from journal where ledger_id=d.account_code and jvdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and tr_from in ('Journal_info','Sales') and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as OtherReceived,
(select SUM(total_amt) from sale_do_details where dealer_code=d.dealer_code and item_id not in ('1096000100010312') and do_type in ('sales') and do_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and do_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as shipment,
(select SUM(dr_amt) from journal where visible_status=1 and ledger_id=d.account_code and jvdate between '".$_POST['f_date']."' and '".$_POST['t_date']."' and tr_from in ('Journal_info', 'Payment') and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."') as OtherIssue

                                            
from dealer_info d,branch r,area t
where 
      d.dealer_category='".$_POST['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE  
        group by d.account_code order by d.dealer_code
      ";

$sql = "
WITH journal_data AS (
    SELECT 
        ledger_id, 
        SUM(CASE WHEN jvdate < '$f_date' THEN cr_amt - dr_amt ELSE 0 END) AS opening_balance,
        SUM(CASE WHEN jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' AND tr_from = 'receipt' THEN cr_amt-dr_amt ELSE 0 END) AS collection,
        SUM(CASE WHEN jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' AND tr_from = 'SalesReturn' THEN cr_amt ELSE 0 END) AS salesReturn,
        SUM(CASE WHEN jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' AND tr_from IN ('Journal_info', 'Sales') THEN cr_amt ELSE 0 END) AS OtherReceived,
        SUM(CASE WHEN jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' AND tr_from IN ('Journal_info', 'Payment') THEN dr_amt ELSE 0 END) AS OtherIssue
    FROM journal
    WHERE visible_status = 1
    AND jvdate NOT BETWEEN '".$lockedStartInterval."' AND '".$lockedEndInterval."'
    GROUP BY ledger_id
),
shipment_data AS (
    SELECT 
        dealer_code, 
        SUM(total_amt) AS shipment
    FROM sale_do_details 
    WHERE item_id NOT IN ('1096000100010312') 
    AND do_type = 'sales' 
    AND do_date BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' 
    AND do_date NOT BETWEEN '".$lockedStartInterval."' AND '".$lockedEndInterval."'
    GROUP BY dealer_code
)
SELECT 
    d.dealer_code,
    d.dealer_custom_code,
    d.dealer_name_e AS dealer_name,
    d.account_code,
    t.AREA_NAME AS 'Territory',
    r.BRANCH_NAME AS region,
    j.opening_balance AS opening,
    j.collection,
    j.salesReturn,
    j.OtherReceived,
    s.shipment,
    j.OtherIssue
FROM dealer_info d
JOIN branch r ON d.region = r.BRANCH_ID
JOIN area t ON d.area_code = t.AREA_CODE
LEFT JOIN journal_data j ON d.account_code = j.ledger_id
LEFT JOIN shipment_data s ON d.dealer_code = s.dealer_code
WHERE d.dealer_category = '".$_POST['pc_code']."'
GROUP BY d.account_code
ORDER BY d.dealer_code";
    $result = mysqli_query($conn, $sql);
    ?>
    <title><?=$title;?></title>
    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse;">
        <thead>
        <h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top: -5px">Collection & Shipment Report</h4>
        <h6 align="center" style="margin-top: -5px">From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <p style="width:95%; text-align:right; font-size:10px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; background-color:#f5f5f5; font-size:11px">
            <th style="border: solid 1px #999; padding:2px">#</th>
            <th style="border: solid 1px #999; padding:2px">DB Code</th>
            <th style="border: solid 1px #999; padding:2px">Ledger Id</th>
            <th style="border: solid 1px #999; padding:2px">Dealer Name</th>
            <th style="border: solid 1px #999; padding:2px">Territory</th>
            <th style="border: solid 1px #999; padding:2px">Region</th>
            <th style="border: solid 1px #999; padding:2px">Opening</th>
            <th style="border: solid 1px #999; padding:2px">Sales Return</th>
            <th style="border: solid 1px #999; padding:2px">Other Received</th>
            <th style="border: solid 1px #999; padding:2px">Collection</th>
            <th style="border: solid 1px #999; padding:2px">Shipment</th>
            <th style="border: solid 1px #999; padding:2px">Other Issue</th>
            <th style="border: solid 1px #999; padding:2px">Closing</th>
        </tr>
        </thead>
        <tbody>
        <?php $s=0;
        $total_opening = 0;
        $total_SalesReturn = 0;
        $total_collection = 0;
        $total_shipment = 0;
        $totalOtherIssue = 0;
        $totalOtherReceived =0;
        while($data=mysqli_fetch_object($result)){$s++; ?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
                <td style="border: solid 1px #999; text-align:center"><?=$s?></td>
                <td style="border: solid 1px #999; text-align:;left"><?=$data->dealer_custom_code;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->account_code;?></td>
                <td style="border: solid 1px #999;"><?=$data->dealer_name;?></td>
                <td style="border: solid 1px #999; text-align:left"><?=$data->Territory;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->region;?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->opening==0)? '-' : number_format($data->opening,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->salesReturn==0)? '-' : number_format($data->salesReturn,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->OtherReceived==0)? '-' : number_format($data->OtherReceived,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->collection==0)? '-' : number_format($data->collection,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->shipment==0)? '-' : number_format($data->shipment,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=($data->OtherIssue==0)? '-' : number_format($data->OtherIssue,2);?></td>
                <td style="border: solid 1px #999; text-align:right"><?=((($data->opening+$data->collection+$data->OtherReceived+$data->salesReturn)-($data->shipment+$data->OtherIssue))==0)? '-' : number_format((($data->opening+$data->collection+$data->OtherReceived+$data->salesReturn)-($data->shipment+$data->OtherIssue)),2);?></td>
                </tr>
            <?php
            $total_opening = $total_opening+$data->opening;
            $total_SalesReturn = $total_SalesReturn+$data->salesReturn;
            $total_collection = $total_collection+$data->collection;
            $totalOtherReceived = $totalOtherReceived+$data->OtherReceived;
            $total_shipment = $total_shipment+$data->shipment;
            $totalOtherIssue = $totalOtherIssue+$data->OtherIssue;
        } ?>
        <tr style="font-size:11px; font-weight:bold">
            <td colspan="6" style="border: solid 1px #999; text-align:right;  padding:2px">Total</td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_opening,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_SalesReturn,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalOtherReceived,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_collection,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_shipment,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalOtherIssue,2);?></td>
            <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format((($total_opening+$total_SalesReturn+$total_collection+$totalOtherReceived)-($total_shipment+$totalOtherIssue)),2);?></td>
        </tr></tbody>
    </table>


<?php elseif ($_POST['report_id']=='1012008'):
    $sql="SELECT d.dealer_code,d.dealer_code,d.dealer_custom_code as dealer_custom_code,d.account_code as 'Ledger ID',d.dealer_name_e as customer_name,t.town_name as town,a.AREA_NAME as territory,b.BRANCH_NAME as region,d.propritor_name_e as propritor_name,d.contact_person,d.contact_number,d.address_e as address,d.national_id,d.TIN_BIN as 'TIN / BIN'  from dealer_info d, town t, area a, branch b WHERE
d.town_code=t.town_code and a.AREA_CODE=d.area_code and b.BRANCH_ID=d.region and d.dealer_category in ('".$_POST['pc_code']."')  order by d.dealer_code"; echo reportview($sql,'Customer Report','98',0,'',0); ?>


<?php elseif ($_POST['report_id']=='1002003'): $LC_no=find_a_field('lc_lc_master','lc_no','id='.$_POST['lc_id']);
    if($sectionid=='400000'){
        $sec_com_connection=' and 1';
    } else {
        $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
    }
?>
    <style>
        #customers {
            font-family: "Gill Sans", sans-serif;
        }
        #customers td {
        }
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
    </style>

    <title><?=($LC_no!='')? $LC_no : 'All Transaction' ?> | Transaction Statement</title>
    <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
    <p align="center" style="margin-top:-18px; font-size: 15px">Transaction Statement</p>
    <p align="center" style="margin-top:-10px; font-size: 12px; font-weight: bold"><?=($_REQUEST['lc_id']>0)? 'LC Number: '.$_REQUEST['lc_id'].' - '.$LC_no.'' : 'All Transaction' ?></p>
    <table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
        <thead>
        <p style="width:95%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
        <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
            <th style="border: solid 1px #999; padding:2px">SL</th>
            <th style="border: solid 1px #999; padding:2px; width:5%">Date</th>
            <th style="border: solid 1px #999; padding:2px; width:10%">Transaction No</th>
            <th style="border: solid 1px #999; padding:2px; width: 15%">Expenses Head</th>
            <th style="border: solid 1px #999; padding:2px">Particulars</th>
            <th style="border: solid 1px #999; padding:2px; width: 10%">Entry By</th>
            <th style="border: solid 1px #999; padding:2px">Amount</th>
        </tr></thead>
        <tbody>
        <?php
        $lc_id =@$_REQUEST['lc_id'];
        $subledger_id = '';
        if(@$_POST['subledger_id']>0){
            $subledger_id.=" and a.sub_ledger_id='".$_POST['subledger_id']."'";} else {$subledger_id='';}
        if($lc_id > 0)
        { $p="select
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
c.*

from

journal a,
accounts_ledger b,
users u,
payment c

where
a.visible_status=1 and
a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and
a.tr_no=c.payment_no and
a.sub_ledger_id=b.ledger_id and
a.user_id=u.user_id and
c.lc_id=".$_POST['lc_id']."".$subledger_id." and
c.dr_amt>0
group by c.id
order by a.jvdate,a.id";}
        $sql=mysqli_query($conn, $p);
        $pi =0;
        $total_expense_amount =0;
        while($data=mysqli_fetch_object($sql)){
            $link="voucher_print1.php?v_type=".$data->tr_from."&v_date=".$data->jvdate."&view=1&vo_no=".$data->jv_no;?>
            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$pi++;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$data->jvdate;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?php echo "<a href='$link' target='_blank'>".$data->jv_no."</a>";?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: left"><?=$data->ledger_name;?></td>
                <td align="left" style="border: solid 1px #999; padding:2px"><?=$data->narration;?></td>
                <td align="center" style="border: solid 1px #999; padding:2px"><?=$data->approvedby;?></td>
                <td align="right" style="border: solid 1px #999; padding:2px"><?=number_format($data->dr_amt,2,'.',',');?></td>
            </tr>
            <?php $total_expense_amount=$total_expense_amount+$data->dr_amt;} ?>
        <tr style="font-size: 11px">
            <th colspan="6"  style="border: solid 1px #999; padding:2px; text-align: right"><strong>Total : </strong></th>
            <th align="right" style="border: solid 1px #999; padding:2px; text-align: right"><strong><?=number_format($total_expense_amount,2);?></strong></th>
            </th>
        </tr>
        </tbody>
    </table>
    </div>
    </div>
    </div>

<?php elseif ($_POST['report_id']=='1002007'):?>
<style>
    #customers {}
    #customers td {}
    #customers tr:ntd-child(even)
    {background-color: #f0f0f0;}
    #customers tr:hover {background-color: #FFCCFF;}
    td{}
</style>
<title>Imbalance Voucher</title>
<p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
<p align="center" style="margin-top:-18px; font-size: 15px">Imbalance Voucher</p>

<p align="center" style="margin-top:-10px; font-size: 11px"><strong>Period From :</strong> <?=$_POST['f_date']?> <strong>to</strong> <?=$_POST['t_date']?></p>
<table align="center" id="customers"  style="width:95%; border: solid 1px #999; border-collapse:collapse; ">
    <thead>
    <p style="width:95%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
        echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
    <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: darkgrey">
        <th style="border: solid 1px #999; padding:2px">SL</th>
        <th style="border: solid 1px #999; padding:2px; width:10%">Voucher Date</th>
        <th style="border: solid 1px #999; padding:2px; width:10%">Voucher No</th>
        <th style="border: solid 1px #999; padding:2px; width:10%">Transaction No</th>
        <th style="border: solid 1px #999; padding:2px; width:10%">T Type</th>
        <th style="border: solid 1px #999; padding:2px">Dr Amt</th>
        <th style="border: solid 1px #999; padding:2px">Cr Amt</th>
        <th style="border: solid 1px #999; padding:2px;">Balance</th>
    </tr></thead>
    <tbody>
    <?php
    $i = 0;
    $result=mysqli_query($conn, "Select tr_no,tr_from,jvdate,jv_no,SUM(dr_amt) as dr_amt,SUM(cr_amt) as cr_amt from journal where visible_status=1 and jvdate between '$f_date' AND '$t_date' and dr_amt!=cr_amt group by jv_no,jvdate order by jv_no");
    while($data=mysqli_fetch_object($result)){
        $Difference=$data->dr_amt-$data->cr_amt;
        if($Difference>0 || $Difference<0) {
            ?>
            <tr style="border:solid 1px #999;font-size:10px;font-weight:normal">
                <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->jvdate;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->jv_no;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->tr_no;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->tr_from;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->dr_amt;?></td>
                <td style="border: solid 1px #999; text-align:center"><?=$data->cr_amt;?></td>
                <td style="border: solid 1px #999; text-align:center; <?php if($Difference>0 || $Difference<0) { echo 'background-color:red; color:white; font-weight:bold'; };?>"><?=number_format($data->dr_amt-$data->cr_amt,2);?></td>
            </tr>


        <?php }} ?>



    </div>
    </div>
    </div>


    <?php elseif ($_POST['report_id']=='1010001'):?>
        <style>
            #customers {}
            #customers td {}
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #FFCCFF;}
            td{}
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
        $datecon=' and m.do_date between  "'.$f_date.'" and "'.$t_date.'"';
        if($_POST['warehouse_id']>0) 			 $warehouse_id=$_POST['warehouse_id'];
        if(isset($warehouse_id))				{$warehouse_id_CON=' and m.depot_id='.$warehouse_id;} else { $warehouse_id_CON=''; }
        if($_POST['dealer_code']>0) 			 $dealer_code=$_POST['dealer_code'];
        if(isset($dealer_code))				{$dealer_code_CON=' and m.dealer_code='.$dealer_code;} else { $dealer_code_CON=''; }
        if($_POST['do_type']>0) 			 $do_type=$_POST['do_type'];
        if(isset($do_type))				{$do_type_con=' and m.do_type='.$do_type;} else { $do_type_con=''; }
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
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and w.warehouse_id=m.depot_id and
c.item_id not in ('1096000100010312') and
m.do_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
a.PBI_ID=p.PBI_ID".$warehouse_id_CON.$datecon.$dealer_code_CON.$do_type_con."
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
            $s                          =0;
            $discounttotal              =0;
            $total_invoice_amount       =0;
            $totalamts                  =0;
            $totalsaleafterdiscount     =0;
            $totalsaleafterdiscounts    =0;
            $totalcomissionamount       =0;
            $actualsalestotalamts       =0;
            while($data=mysqli_fetch_object($query)){$s++; ?>
                <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
                    <td style="border: solid 1px #999; text-align:center"><?=$s?></td>
                    <td style="border: solid 1px #999; text-align:center"><a href="chalan_view.php?v_no=<?=$data->chalan_no?>" target="_blank"><?=$data->chalan_no?></a></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->chalan_date?></td>
                    <td style="border: solid 1px #999; text-align:center"><a href="chalan_bill_distributors.php?do_no=<?=$data->do_no?>" target="_blank"><?=$data->do_no;?></a></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->do_date;?></td>
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
                $actualsalestotalamts=$actualsalestotalamts+$totalamts;

                $totalsaleafterdiscounts=$totalsaleafterdiscounts+$totalsaleafterdiscount;
                $totalcomissionamount=$totalcomissionamount+$data->comissionamount;
            } ?>
            <tr style="font-size:11px; font-weight:bold">
                <td colspan="9" style="border: solid 1px #999; text-align:right;  padding:2px">Total</td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount,2);?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($discounttotal,2);?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($totalcomissionamount,2);?></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><?=number_format($total_invoice_amount-($discounttotal+$totalcomissionamount),2);?></td>
            </tr>
            </tbody>
        </table>


    <?php elseif ($_POST['report_id']=='1001001'):?>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #f5f5f5;}
        </style>
        <title>Chart of Accounts</title>
        <h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top:-15px">Chart of Accounts</h4>
        <table align="center" id="customers"  style="width:90%; border: solid 1px #999; border-collapse:collapse;">
            <thead>
            <p style="width:80%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                <th style="border: solid 1px #999; padding:2px; %">ledger Group</th>
                <th style="border: solid 1px #999; padding:2px; %">ledger Name</th>
                <th style="border: solid 1px #999; padding:2px; %">Sub Ledger</th>
                <th style="border: solid 1px #999; padding:2px; %">Sub Sub Ledger</th>
            </tr></thead>
            <?
            if($sectionid=='400000'){
                $sec_com_connection=' 1';
            } else {
                $sec_com_connection=" company_id='".$_SESSION['companyid']."' and section_id in ('400000','".$_SESSION['sectionid']."')";
            }
            $separator = @$separator;
            $sql="select * from ledger_group where ".$sec_com_connection." order by group_id";
            $query=mysqli_query($conn, $sql);
            if(mysqli_num_rows($query)>0){
                while($grp=mysqli_fetch_object($query)){
                    $grp_id=(string)($grp->group_id*100000000);
                    ?>
                    <tr style="border: solid 1px #999; font-size:13px; font-weight:normal; background-color: #FFCCFF">
                        <td colspan="4" style="border: solid 1px #999; text-align:left"><?=ledger_sepe($grp_id,$separator)?><?=' '.$grp->group_name;?></td></tr>
                    <?
                    $sql2="select * from accounts_ledger where ledger_id like '%00000000' and ledger_group_id='".$grp->group_id."' and ".$sec_com_connection."";
                    $query2=mysqli_query($conn, $sql2);
                    $count_group = 0;
                    if(mysqli_num_rows($query2)>0){
                        while($ledger=mysqli_fetch_object($query2)){
                            $count_group=$count_group+1;
                            ?>
                            <tr style="border: solid 1px #999; font-size:12px; font-weight:normal;">
                                <td style="border: solid 1px #999; text-align:left"></td>
                                <td style="border: solid 1px #999; text-align:left" ><?=ledger_sepe(((string)($ledger->ledger_id)),$separator).' '?><?=$ledger->ledger_name;?></td>
                                <td style="border: solid 1px #999; text-align:left"></td>
                                <td style="border: solid 1px #999; text-align:left"></td>
                            </tr>
                            <?
                            $sql3="select * from sub_ledger where ledger_id=".$ledger->ledger_id." and ".$sec_com_connection."";
                            $query3=mysqli_query($conn, $sql3);
                            if(mysqli_num_rows($query3)>0){
                                while($sub_ledger=mysqli_fetch_object($query3)){
                                    ?>
                                    <tr style="border: solid 1px #999; font-size:11px; font-weight:normal;">
                                        <td style="border: solid 1px #999; text-align:left"></td>
                                        <td style="border: solid 1px #999; text-align:left"></td>
                                        <td style="border: solid 1px #999; text-align:left"><?=ledger_sepe(((string)($sub_ledger->sub_ledger_id)),$separator).' '?><?=$sub_ledger->sub_ledger;?></td>
                                        <td style="border: solid 1px #999; text-align:left"></td>
                                    </tr>
                                    <?
                                    $sql4="select * from sub_sub_ledger where sub_ledger_id=".$sub_ledger->sub_ledger_id." and  ".$sec_com_connection."";
                                    $query4=mysqli_query($conn, $sql4);
                                    if(mysqli_num_rows($query4)>0){?>

                                        <? while($sub_sub_ledger=mysqli_fetch_object($query4)){?>
                                            <tr style="border: solid 1px #999; font-size:10px; font-weight:normal;">
                                                <td style="border: solid 1px #999; text-align:left"></td>
                                                <td style="border: solid 1px #999; text-align:left"></td>
                                                <td style="border: solid 1px #999; text-align:left"></td>
                                                <td style="border: solid 1px #999; text-align:left"><a style="font-size:09px "><?=$sub_sub_ledger->sub_sub_ledger_id;?>&nbsp;<?=$sub_sub_ledger->sub_sub_ledger;?></a></td></tr>
                                        <? }?>
                                    <? }?>
                                <? }?>
                            <? }?>
                        <? }?>
                    <? }?>
                <? }?>
            <?php }?>
            </tbody>
        </table>


    <?php elseif ($_POST['report_id']=='1002004'):?>
        <?php
        if($sectionid=='400000'){
            $sec_com_connection=' and 1';
            $sec_com_connection_wa=' and 1';
        } else {
            $sec_com_connection=" and a.company_id='".$_SESSION['companyid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."')";
            $sec_com_connection_wa=" and company_id='".$_SESSION['companyid']."' and section_id in ('400000','".$_SESSION['sectionid']."')";
        }



        $cash_and_bank_balance=find_a_field("ledger_group","group_id","group_id='1002'".$sec_com_connection_wa."");
        $led=mysqli_query($conn, "select ledger_id,ledger_name from accounts_ledger where ledger_group_id='$cash_and_bank_balance'".$sec_com_connection_wa." order by ledger_id");
        $data = '[';
        $data .= '{ name: "All", id: "%" },';
        while($ledg = @mysqli_fetch_row($conn, $led)){
            $data .= '{ name: "'.$ledg[1].'", id: "'.$ledg[0].'" },';
        }
        $data = substr($data, 0, -1);
        $data .= ']';
        $led1=mysqli_query($conn, "SELECT id, center_name FROM cost_center WHERE 1 ".$sec_com_connection_wa." ORDER BY center_name");
        if(mysqli_num_rows($led1) > 0)
        {
            $data1 = '[';
            while($ledg1 = mysqli_fetch_row($led1)){
                $data1 .= '{ name: "'.$ledg1[1].'", id: "'.$ledg1[0].'" },';}
            $data1 = substr($data1, 0, -1);
            $data1 .= ']';
        }  else  {
            $data1 = '[{ name: "empty", id: "" }]';
        } $PostLedgerId = @$_REQUEST['ledger_id'];
        if($PostLedgerId>0)
        {$ledger_con = 'b.ledger_id="'.$_REQUEST['ledger_id'].'"';
            $ledger_conx = 'a.relavent_cash_head="'.$_REQUEST['ledger_id'].'"';
        }else {$ledger_con = 'b.ledger_group_id="'.$cash_and_bank_balance.'"';
            $ledger_conx = '1';}

        $op_b1="select distinct(b.ledger_name), SUM(dr_amt)-SUM(cr_amt) from journal a, accounts_ledger b where a.visible_status=1 and ".$ledger_con." and a.ledger_id<>'$cash[0]' and a.ledger_id=b.ledger_id and jvdate < '$f_date' ".$sec_com_connection." GROUP  BY ledger_name";
        $cl_c="select SUM(dr_amt)-SUM(cr_amt) from journal where 1 ".$sec_com_connection_wa." and visible_status=1 and ledger_id ='$cash[0]' and jvdate<'$t_date'";
        $cl_c=mysqli_fetch_row(mysqli_query($conn, $cl_c));
        $cl_b="select distinct(b.ledger_name), SUM(dr_amt)-SUM(cr_amt) from journal a, accounts_ledger b where ".$ledger_con."".$sec_com_connection." and a.visible_status=1 and a.ledger_id<>'$cash[0]' and a.ledger_id=b.ledger_id and jvdate < '$t_date' and 1 GROUP  BY ledger_name";
        ?>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top:-15px">Receipt & Payment Statement</h4>
        <?php if (@$_POST['cc_code']>0) { ?><h4 align="center" style="margin-top:-15px">Cost Center :  <?=find_a_field('cost_center','center_name','id="'.$_POST['cc_code'].'"');?> </h4><?php } ?>
        <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center"  style="width:70%; border: solid 1px #999; border-collapse:collapse;font-size:12px">
            <thead>
            <tr style="background-color: #f5f5f5"><th height="20" colspan="5" align="left">Opening Cash &amp; Bank Balance</th></tr></thead>


            <?php
            $opb=mysqli_query($conn, $op_b1);
            $op_to=0; $i = 0;
            while($op_b=mysqli_fetch_row($opb)){
                $op_to=$op_to+$op_b[1];?>
                <tr <? $i++; if($i%2==0)$cls=' class="alt"'; else $cls=''; echo $cls;?> style="font-size: 12px">
                    <td style="border: solid 1px #999; padding:2px"><?php echo $op_b[0];?> </td>
                    <td align="right" style="border: solid 1px #999; padding:2px"><?php if($op_b[1]==0) echo "0.00"; else
                        {if($op_b[1]<0) echo "(".number_format($op_b[1]*(-1),2).")"; else echo number_format($op_b[1],2);}?></td>
                </tr>
            <?php }?>


            <tr style="font-size: 12px; background-color: #f5f5f5"><th align="right" style="border: solid 1px #999; padding:2px"><strong>Total : </strong></th>
                <th align="right" style="border: solid 1px #999; padding:2px"><?php if($op_to==0) echo "0.00"; else
                    {if($op_to<0) echo "(".number_format($op_to*(-1),2).")"; else echo number_format($op_to,2);}?></th></tr>
        </table>
        <br /><br />
        <table align="center"  style="width:70%; border: solid 1px #999; border-collapse:collapse;font-size: 12px ">
            <thead>
            <tr >
                <th height="20" colspan="5" align="left" style="border: solid 1px #999; padding:2px">Receipt</th>
            </tr>
            </thead>



            <?php
            $cc_code = (int) @$_REQUEST['cc_code'];
            if($cc_code > 0)
            {$p = "select DISTINCT(group_name),SUM(cr_amt),b.ledger_group_id from journal a,accounts_ledger b,ledger_group c where a.visible_status=1 and a.ledger_id = b.ledger_id and b.ledger_group_id=c.group_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and a.ledger_id!=a.relavent_cash_head and ".$ledger_conx." and a.tr_from='Receipt'".$sec_com_connection." AND a.cc_code=$cc_code GROUP BY group_name";
            } else {
                $p = "select DISTINCT(group_name),SUM(cr_amt),b.ledger_group_id from journal a,accounts_ledger b,ledger_group c where a.visible_status=1 and a.ledger_id = b.ledger_id and b.ledger_group_id=c.group_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and a.tr_from='Receipt' and a.ledger_id!=a.relavent_cash_head and ".$ledger_conx."".$sec_com_connection." GROUP BY group_name order by c.group_id";
            }
            $pi=0;
            $re_to=0;
            $sql=mysqli_query($conn, $p);
            while($data=mysqli_fetch_row($sql))
            {            $pi++;
                $re_to=$re_to+$data[1];
                ?>
                <tr style="font-weight: bold; background-color: #f5f5f5">
                    <td width="19%" align="center" style="border: solid 1px #999; padding:2px"><?php echo $pi;?></td>
                    <td colspan="2" align="left" style="border: solid 1px #999; padding:2px"><?php echo $data[0];?></td>
                    <td colspan="2" align="right" style="border: solid 1px #999; padding:2px"><?php echo number_format($data[1],2);?></td>
                </tr>
                <?php
                $cc_code = (int) @$_REQUEST['cc_code'];
                if($cc_code > 0)
                {
                    $Lg="select DISTINCT(b.ledger_name),SUM(cr_amt),b.ledger_id from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id = b.ledger_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and b.ledger_group_id='$data[2]' and a.tr_from='Receipt'".$sec_com_connection." and a.ledger_id!=a.relavent_cash_head and ".$ledger_conx." AND a.cc_code=$cc_code GROUP BY ledger_name order by b.ledger_id";
                }   else {
                    $Lg="select DISTINCT(b.ledger_name),SUM(cr_amt),b.ledger_id from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id = b.ledger_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and b.ledger_group_id='$data[2]' and a.tr_from='Receipt'".$sec_com_connection." and a.ledger_id!=a.relavent_cash_head and ".$ledger_conx." GROUP BY ledger_name order by b.ledger_id";
                }   $Li=0;
                $Lsql=mysqli_query($conn, $Lg);
                while($Ldata=mysqli_fetch_row($Lsql)){
                    $Li++;?>
                    <tr onclick="DoNav('<?php echo $f_date;?>','<?php echo $t_date;?>','<?php echo $Ldata[2];?>');">
                        <td width="19%" align="center" style="border: solid 1px #999; padding:2px">&nbsp;</td>
                        <td width="14%" align="center" style="border: solid 1px #999; padding:2px"><?php echo $pi.'.'.$Li;?></td>
                        <td align="left" style="border: solid 1px #999; padding:2px"><?php echo $Ldata[0];?></td>
                        <td width="22%" align="right" style="border: solid 1px #999; padding:2px"><?php echo $Ldata[1];?></td>
                        <td width="15%" align="right" style="border: solid 1px #999; padding:2px">&nbsp;</td>
                    </tr>
                <?php }?>
            <?php }?>
            <tr style="background-color: #f5f5f5"><th colspan="3" align="right" style="border: solid 1px #999; padding:2px"><strong>Total : </strong></th>
                <th colspan="2" align="right" style="border: solid 1px #999; padding:2px"><strong><?php if($re_to==0) echo "0.00"; else echo number_format($re_to,2);?></strong></th>
            </tr>
            <tr style="background-color: #f5f5f5"><th colspan="3" align="right" style="border: solid 1px #999; padding:2px">Grand Total : </th>
                <th colspan="2" align="right" style="border: solid 1px #999; padding:2px"><strong>
                        <?php if(($op_to+$re_to)==0) echo "0.00"; else
                        {if(($op_to+$re_to)<0) echo "(".number_format(($op_to+$re_to)*(-1),2).")"; else echo number_format(($op_to+$re_to),2);}?>
                    </strong></th></tr></table>
        <br /><br />
        <table align="center"  style="width:70%; border: solid 1px #999; border-collapse:collapse;font-size: 12px ">
            <thead>
            <tr style="background-color: #f5f5f5"><th height="20" colspan="5" align="left">Payment</th></tr>
            </thead>
            <?php
            $cc_code = (int) @$_REQUEST['cc_code'];
            if($cc_code > 0)
            {
                $p = "select DISTINCT(group_name),SUM(dr_amt), b.ledger_group_id from journal a,accounts_ledger b,ledger_group c where a.visible_status=1 and a.ledger_id = b.ledger_id and b.ledger_group_id=c.group_id and a.jvdate>='$f_date' and a.jvdate<='$t_date'  and a.ledger_id!=a.relavent_cash_head and ".$ledger_conx." and ".$ledger_conx."".$sec_com_connection." AND a.cc_code=$cc_code GROUP BY group_name order by b.ledger_id";
            } else {
                $p ="select DISTINCT(group_name),SUM(dr_amt), b.ledger_group_id from journal a,accounts_ledger b,ledger_group c where a.visible_status=1 and a.ledger_id = b.ledger_id and b.ledger_group_id=c.group_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and ".$ledger_conx."".$sec_com_connection." GROUP BY group_name order by b.ledger_id";
            }
            //echo $p;
            $pi=0;
            $re_to=0;
            $sql=mysqli_query($conn, $p);
            while($data=mysqli_fetch_row($sql))
            {
                $pi++;
                $re_to=$re_to+$data[1];
                ?>
                <tr style="font-weight: bold; background-color: #f5f5f5">
                    <td align="center" style="border: solid 1px #999; padding:2px"><?php echo $pi;?></td>
                    <td colspan="2" align="left" style="border: solid 1px #999; padding:2px"><?php echo $data[0];?></td>
                    <td colspan="2" align="right" style="border: solid 1px #999; padding:2px"><?php echo number_format($data[1],2);?></td>
                </tr>
                <?php
                $cc_code = (int) @$_REQUEST['cc_code'];
                if($cc_code > 0)
                {
                    $Lg="select DISTINCT(b.ledger_name),SUM(dr_amt),b.ledger_id from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id = b.ledger_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and b.ledger_group_id='$data[2]' and a.tr_from='Payment'".$sec_com_connection." AND a.cc_code=$cc_code GROUP BY ledger_name";
                }   else   {
                    $Lg="select DISTINCT(b.ledger_name),SUM(dr_amt),b.ledger_id from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id = b.ledger_id and a.jvdate>='$f_date' and a.jvdate<='$t_date' and b.ledger_group_id='$data[2]' and a.tr_from='Payment'".$sec_com_connection." GROUP BY ledger_name";
                }
                $Li=0;
                $Lsql=mysqli_query($conn, $Lg);
                while($Ldata=mysqli_fetch_row($Lsql)){
                    $Li++;
                    //$re_to=$re_to+$data[1];
                    ?>
                <tr>
                    <td width="19%" align="center" style="border: solid 1px #999; padding:2px">&nbsp;</td>
                    <td width="14%" align="center" style="border: solid 1px #999; padding:2px"><?php echo $pi.'.'.$Li;?></td>
                    <td align="left" style="border: solid 1px #999; padding:2px"><?php echo $Ldata[0];?></td>
                    <td width="22%" align="right" style="border: solid 1px #999; padding:2px"><?php echo $Ldata[1];?></td>
                    <td width="15%" align="right" style="border: solid 1px #999; padding:2px">&nbsp;</td>
                    </tr><?php }?>

                <tr style="background-color: #f5f5f5">
                    <th colspan="3" align="right" style="border: solid 1px #999; padding:2px"><strong>Total : </strong></th>
                    <th colspan="2" align="right" style="border: solid 1px #999; padding:2px"><strong>
                            <?php if($re_to==0) echo "0.00"; else echo number_format($re_to,2);?>
                        </strong></th></tr>
            <?php }?>
        </table><br /><br />

        <table align="center"  style="width:70%; border: solid 1px #999; border-collapse:collapse;font-size: 12px ">
            <thead>
            <tr style="background-color: #f5f5f5"><th colspan="2" align="left" style="border: solid 1px #999; padding:2px">Closing Cash &amp; Bank Balance </th></tr><thead>
            <tr>
                <td width="70%" style="border: solid 1px #999; padding:2px">Cash Closing  : </td>
                <td width="30%" align="right" style="border: solid 1px #999; padding:2px"><?php if($cl_c[0]==0) echo "0.00";
                    else {if($cl_c[0]<0) echo "(".number_format($cl_c[0]*(-1),2).")"; else echo number_format($cl_c[0],2);}?></td></tr>
            <?php
            $clb=mysqli_query($conn, $cl_b);
            $cl_to=$cl_c[0];
            while($cl_b=mysqli_fetch_row($clb)){
                $cl_to=$cl_to+$cl_b[1];
                ?>
                <tr>
                    <td style="border: solid 1px #999; padding:2px"><?=$cl_b[0];?> </td>
                    <td align="right" style="border: solid 1px #999; padding:2px"><?php if($cl_b[1]==0) echo "0.00"; else
                        {if($cl_b[1]<0) echo "(".number_format($cl_b[1]*(-1),2).")"; else echo number_format($cl_b[1],2);}?></td></tr>
            <?php }?>
            <tr style="background-color: #f5f5f5">
                <th align="right" style="border: solid 1px #999; padding:2px">Total :</th>
                <th align="right" style="border: solid 1px #999; padding:2px"><?php if($cl_to==0) echo "0.00"; else
                    {if($cl_to<0) echo "(".number_format($cl_to*(-1)).")"; else echo number_format($cl_to,2);}?></th></tr>
            <tr style="background-color: #f5f5f5">
                <th align="right" style="border: solid 1px #999; padding:2px">Grand Total :</th>
                <th align="right" style="border: solid 1px #999; padding:2px"><strong>
                        <?php if($cl_to==0) echo "0.00"; else
                        {if($cl_to<0) echo "(".number_format($cl_to*(-1)+$re_to,2).")"; else echo number_format($cl_to+$re_to,2);}?>
                    </strong>
                </th>
            </tr>
        </table>


    <?php elseif ($_POST['report_id']=='1010002'): ?>

        <title>Sales Report</title>
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
            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color:#f5f5f5">
                <th style="border: solid 1px #999; padding:2px">SL</th>
                <th style="border: solid 1px #999; padding:2px; %">T.ID</th>
                <th style="border: solid 1px #999; padding:2px; ">Depot</th>
                <th style="border: solid 1px #999; padding:2px; ">Code</th>
                <th style="border: solid 1px #999; padding:2px; ">Dealer Name</th>
                <th style="border: solid 1px #999; padding:2px">D.Type</th>
                <th style="border: solid 1px #999; padding:2px; ">DO</th>
                <th style="border: solid 1px #999; padding:2px;">DO Date</th>
                <th style="border: solid 1px #999; padding:2px">DO Type</th>
                <th style="border: solid 1px #999; padding:2px">DO Remarks</th>
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
            <?php $PostDoNo = @$_POST['do_no'];
            if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
            if(isset($item_id))				{$item_con=' and sd.item_id='.$item_id;} else { $item_con=''; }
            if($PostDoNo>0) 					$do_no=$_POST['do_no'];
            if(isset($do_no))				{$do_no_con=' and sd.do_no='.$do_no;} else { $do_no_con=''; }
            $datecon=' and sd.do_date between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
            $i = 0;
            if ($_POST['do_type'] == 'all') {
                $result = 'Select
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
				i.pack_size as psize,
				m.remarks
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i,
				sale_do_master m
				
				where
				m.do_no=sd.do_no and
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				sd.do_date NOT BETWEEN "' . $lockedStartInterval . '" and "' . $lockedEndInterval . '" and 
				d.area_code=a.AREA_CODE  ' . $datecon . $item_con . $do_no_con . '
				order by sd.id DESC';
            } elseif ($_POST['do_type'] == 'sales') {
                $result = 'Select
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
				i.pack_size as psize,
				m.remarks
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i,
				sale_do_master m
				
				where
				m.do_no=sd.do_no and
				sd.do_type in ("sales") and
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				sd.do_date NOT BETWEEN "' . $lockedStartInterval . '" and "' . $lockedEndInterval . '" and 
				d.area_code=a.AREA_CODE  ' . $datecon . $item_con . $do_no_con . '
				order by sd.id DESC';
            } elseif ($_POST['do_type'] == 'free') {
                $result = 'Select
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
				i.pack_size as psize,
				m.remarks
				
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i,
				sale_do_master m
				
				where
				    m.do_no=sd.do_no and
				    sd.do_type in ("free") and
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				sd.do_date NOT BETWEEN "' . $lockedStartInterval . '" and "' . $lockedEndInterval . '" and 
				d.area_code=a.AREA_CODE  ' . $datecon . $item_con . $do_no_con . '
				order by sd.id DESC';
            } elseif ($_POST['do_type'] == 'sample') {
                $result = 'Select
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
				i.pack_size as psize,
				m.remarks
				
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i,
				sale_do_master m
				
				where
				    m.do_no=sd.do_no and
				    sd.do_type in ("sample") and
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				sd.do_date NOT BETWEEN "' . $lockedStartInterval . '" and "' . $lockedEndInterval . '" and 
				d.area_code=a.AREA_CODE  ' . $datecon . $item_con . $do_no_con . '
				order by sd.id DESC';
            } elseif ($_POST['do_type'] == 'gift') {
                $result = 'Select
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
				i.pack_size as psize,
				m.remarks
				
				from
				sale_do_details sd,
				dealer_info d,
				area a,
				branch b,
				warehouse w,
				item_info i,
				sale_do_master m
				
				where
				    m.do_no=sd.do_no and
				    sd.do_type in ("gift") and
				i.item_id=sd.item_id and
				sd.depot_id=w.warehouse_id and
				sd.dealer_code=d.dealer_code and
				d.region=b.BRANCH_ID and
				sd.do_date NOT BETWEEN "' . $lockedStartInterval . '" and "' . $lockedEndInterval . '" and 
				d.area_code=a.AREA_CODE  ' . $datecon . $item_con . $do_no_con . '
				order by sd.id DESC';
            }
            $query2 = mysqli_query($conn, $result);
            while($data=mysqli_fetch_object($query2)){?>
                <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->id; ?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->warehouse_name; ?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->dealer_custom_code; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->dealer_name_e; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->dealer_type; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->do_no; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->do_date; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->do_type; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->remarks; ?></td>
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
                $total_sales_amount=@$total_sales_amount+$data->total_amt;
            }
            if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
            if(isset($item_id))				{$item_conT=' and item_id='.$item_id;} else { $item_conT=''; }
            $totalQty=find_a_field('sale_do_details','SUM(total_unit)','do_type in ("","sales") and do_date between "'.$f_date.'" and "'.$t_date.'" and dealer_type not in ("export") '.$item_conT.'');
            $toatl_sales_reguler=find_a_field('sale_do_details','SUM(total_amt)','do_type in ("","sales") and do_date between "'.$f_date.'" and "'.$t_date.'" and dealer_type not in ("export") '.$item_conT.'');
            $toatl_sales=find_a_field('sale_do_details','SUM(total_amt)','do_type not in ("","sales") and do_date between "'.$f_date.'" and "'.$t_date.'" and dealer_type in ("export") '.$item_conT.'')
            ?>
            <?php if($_POST['item_id']>0) { ?>
            <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Total Qty  = </td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=$totalQty;?></td>
                <td style="border: solid 1px #999; padding:2px; "></td>
            </tr>
            <?php } ?>
            <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Local Sales in Amount  = </td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales_reguler,2);?></td>
                <td style="border: solid 1px #999; padding:2px; "></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:12px; font-weight:normal">
                <th style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Total Sales in Amount  = </th>
                <th style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales_reguler,2);?></th>
                <th style="border: solid 1px #999; padding:2px; "></th>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                <td style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Total (sample, gift and others) = </td>
                <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($toatl_sales,2);?></td>
                <td style="border: solid 1px #999; padding:2px; "></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:12px; font-weight:normal">
                <th style="border: solid 1px #999; padding:2px; text-align: right" colspan="17">Grand Total  = </th>
                <th style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($total_sales_amount,2);?></th>
                <th style="border: solid 1px #999; padding:2px; "></th>
            </tr>
            </tbody>
        </table>




    <?php elseif ($_POST['report_id']=='1010003'):?>
        <?php
        if(!empty($_POST['do_type'])) 					$do_type=$_POST['do_type'];
        if(($do_type=='sales' || $do_type=='sample' || $do_type=='gift'|| $do_type=='free'|| $do_type=='display')) {$do_type_conn=' and sd.challan_type="'.$do_type.'"';} else {$do_type_conn='';}
        $datecon=' and sd.do_date between  "'.$f_date.'" and "'.$t_date.'"';

        $query = '
WITH 
FilteredSaleDoDetails AS (
    SELECT 
        do_no, 
        item_id, 
        SUM(CASE WHEN item_id = "1096000100010312" THEN total_amt ELSE 0 END) AS total_cash_discount,
        SUM(total_unit) AS total_units
    FROM 
        sale_do_details
    GROUP BY 
        id
),
ComputedJournalItems AS (
    SELECT 
        do_no, 
        item_id, 
        batch, 
        id, 
        gift_type, 
        item_ex,
        item_price,
        total_amt,
        Remarks
    FROM 
        journal_item
    GROUP BY 
        id
)
SELECT 
    ji.id,
    d.dealer_custom_code AS code,
    d.dealer_name_e AS dealer_name,
    sd.do_no,
    sd.do_date,
    sd.challan_type AS do_type,
    ji.gift_type AS gift_type,
    sd.do_section AS invoice_type,
    i.finish_goods_code AS FG_Code,
    i.item_name AS FG_description,
    i.pack_unit AS UOM,
    i.pack_size AS Pack_Size,
    ib.brand_name,
    ji.batch,
    ji.item_price AS COGS_Price,
    ji.item_ex AS Qty,
    ji.total_amt AS COGS_Amount,
    COALESCE(fsd.total_cash_discount, 0) AS "Discount Amount",    
    CASE 
        WHEN ji.Remarks = "buy" THEN sd.unit_price
        ELSE "-"
    END AS invoice_Price,
    CASE 
        WHEN ji.Remarks = "buy" THEN FORMAT(ji.item_ex*sd.unit_price,2)
        ELSE "-"
    END AS Invoice_Amount

FROM 
    sale_do_chalan sd
JOIN 
    dealer_info d ON sd.dealer_code = d.dealer_code
JOIN 
    warehouse w ON sd.depot_id = w.warehouse_id
JOIN 
    item_info i ON sd.item_id = i.item_id
JOIN 
    item_brand ib ON i.brand_id = ib.brand_id
JOIN 
    ComputedJournalItems ji ON sd.do_no = ji.do_no AND sd.item_id = ji.item_id
LEFT JOIN 
    FilteredSaleDoDetails fsd ON sd.do_no = fsd.do_no AND sd.item_id = fsd.item_id
WHERE 
    i.item_id <> "1096000100010312"
    AND sd.do_date NOT BETWEEN "'.$lockedStartInterval.'" AND "'.$lockedEndInterval.'"
    '.$datecon.$do_type_conn.'
GROUP BY 
     ji.id
ORDER BY 
    sd.do_no, ji.id ASC';?>

        <?=reportview($query,'Item wise COGS Sales','99',0,'',0); ?>



    <?php elseif ($_POST['report_id']=='1007001'):?>
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
        <title>LC Summery</title>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <h5 align="center" style="margin-top:-15px">LC Summery</h5>
        <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse; font-size: 11px">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
                <th colspan="8" style="border: solid 1px #999; padding:2px; background-color: bisque">LC Information</th>
                <?php $i =0;
                $lctablew=mysqli_query($conn, "Select * from LC_expenses_head where status in ('1')");
                while($lcrow=mysqli_fetch_array($lctablew)){
                    $i=$i+1;
                    ?>
                <?php } ?>
                <th colspan="<?=$i;?>" style="border: solid 1px #999; padding:2px; background-color: bisque">LC Expenses Details</th>
                <th rowspan="2" style="border: solid 1px #999; padding:2px; background-color: bisque">LC Grand Total</th>
            </tr>
            <tr style="border: solid 1px #999;font-size:11px">
                <th style="border: solid 1px #999; ">SL</th>
                <th style="border: solid 1px #999;">PI NO</th>
                <th style="border: solid 1px #999; ">LC NO</th>
                <th style="border: solid 1px #999;">LC Date</th>
                <th style="border: solid 1px #999;">LC Issue Date</th>
                <th style="border: solid 1px #999;">Buyer Name</th>
                <th style="border: solid 1px #999;">Buyer Origin</th>
                <th style="border: solid 1px #999;">LC Amount</th>
                <?php
                $lctablew=mysqli_query($conn, "Select * from LC_expenses_head where status in ('1')");
                while($lcrow=mysqli_fetch_array($lctablew)){
                    ?><th style="border: solid 1px #999; padding:2px; "><?=$lcrow['LC_expenses_head'];?></th>
                <?php } ?>
            </tr></thead>
            <tbody>
            <?php
            $g =0;
            $datecon=' and llm.lc_create_date between  "'.$f_date.'" and "'.$t_date.'"';
            $result='Select
				llm.id,
				llm.pi_id,
				llm.lc_issue_date,
				llm.party_id,
				llm.lc_no,
				llm.lc_create_date,
				SUM(lld.amount) as lcamount,
				lpm.*,
				b.*
				from
				lc_lc_master llm,
				lc_lc_details lld,
				lc_pi_master lpm,
				lc_buyer b

				where
				llm.id=lld.lc_id and
				llm.pi_id=lpm.id and
				llm.party_id=b.party_id and 
				llm.lc_create_date NOT BETWEEN "'.$lockedStartInterval.'" and "'.$lockedEndInterval.'"
				  '.$datecon.'
group by llm.id
				order by llm.id DESC';
            $query2 = mysqli_query($conn, $result);
            while($data=mysqli_fetch_object($query2)){
                $g=$g+1; ?>

                <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$g;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?php echo $data->pi_no; ?></td>
                    <td style="border: solid 1px #999; text-align:center"><?php echo $data->lc_no; ?></td>
                    <td style="border: solid 1px #999; text-align:left"><?php echo $data->lc_create_date; ?></td>
                    <td style="border: solid 1px #999; text-align:left"><?php echo $data->lc_issue_date; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->buyer_name; ?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?php echo $data->origin; ?></td>
                    <td style="border: solid 1px #999; text-align:right; padding:5px"><?=number_format($data->lcamount,2); ?></td>
                    <?php
                    $lctablew=mysqli_query($conn,"Select lh.* from LC_expenses_head lh where lh.status in ('1')");
                    while($lcrow=mysqli_fetch_array($lctablew)){
                        ?><td style="border: solid 1px #999; text-align:right; padding:2px"><?php $COST=find_a_field('lc_lc_master',''.$lcrow['db_column_name'].'',''.$lcrow['db_column_name'].'='.$lcrow['db_column_name'].' and id='.$data->id.''); if($COST>0) echo $COST; else echo '';?></td>
                        <?php
                        $total_LC_COST=$total_LC_COST+$COST;
                    }
                    $grandtotal=$total_LC_COST;
                    ?>

                    <td style="border: solid 1px #999; text-align:right"><?=number_format($grandtotal,2);?></td>
                </tr>
                <?php
                $totaladjustment=$totaladjustment+$adjustment;
                $totalcollection=$totalcollection+$collection;
                $totalactualcollection=$totalactualcollection+$actualcollection;
            } ?>
            </tbody>
        </table>
        </div>
        </div>
        </div>


    <?php elseif ($_POST['report_id']=='1007002'): ?>
        <title>LC Wise Cost Summery</title>
        <?php
        $result='Select
				llm.id,
				llm.pi_id,
				llm.lc_issue_date,
				llm.party_id,
				llm.lc_no,
				llm.lc_create_date,
				lld.*,
				llr.*,
				i.item_id,
				i.item_name,
				i.unit_name
				from
				lc_lc_master llm,
				LC_costing_breakdown lld,
				lc_lc_received  llr,
				item_info i

				where
				llm.id=lld.lc_id and
				lld.item_id=i.item_id and
				llm.id="'.$_POST["lc_id"].'" and
				llr.lcr_no=lld.lc_id and
				llr.item_id=lld.item_id
group by lld.item_id
				order by llm.id, lld.id';
        $query2 = mysqli_query($conn, $result);
        while($data=mysqli_fetch_object($query2)){
            $_POST['lc_id']=$_POST['lc_id'];
            $_POST['lcr_no']=$data->lcr_no;
            $_POST['item_id']=$data->item_id;
            $_POST['lc_comission'] = @$_POST['lc_comission'.$data->id];
            $_POST['lc_insurance'] = @$_POST['lc_insurance'.$data->id];
            $_POST['lc_bank_bill'] = @$_POST['lc_bank_bill'.$data->id];
            $_POST['freight_charge'] = @$_POST['freight_charge'.$data->id];
            $_POST['lc_port_bill'] = @$_POST['lc_port_bill'.$data->id];
            $_POST['lc_transport'] = @$_POST['lc_transport'.$data->id];
            $_POST['lc_mis_cost'] = @$_POST['lc_mis_cost'.$data->id];
            $_POST['lc_others'] = @$_POST['lc_others'.$data->id];
            $_POST['air_bill'] = @$_POST['air_bill'.$data->id];
            $_POST['duty'] = @$_POST['duty'.$data->id];
            $_POST['shipping_bill'] = @$_POST['shipping_bill'.$data->id];
            $_POST['labor_bill'] = @$_POST['labor_bill'.$data->id];
            $_POST['BSTI_expense'] = @$_POST['BSTI_expense'.$data->id];
            $_POST['total_LC_cost'] = @$_POST['total_LC_cost'.$data->id];

            $_POST['per_unit_cost']=@$_POST['per_unit_cost'.$data->id];
            $_POST['entry_by']=@$_SESSION['userid'];
            $_POST['entry_at']=date("Y-m-d h:i:sa");
            $_POST['section_id']=@$_SESSION['sectionid'];
            $_POST['company_id']=@$_SESSION['companyid'];
            $LC_item_wise_cost_sheet='LC_item_wise_cost_sheet';
            if(isset($_POST['record_lc_cost'])){
                if(prevent_multi_submit()) {
                    $crud = new crud($LC_item_wise_cost_sheet);
                    $crud->insert();}}
        } ?>
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
        <h2 align="center" style="margin-top: -5px"><?=$_SESSION['company_name'];?></h2>
        <h5 align="center" style="margin-top:-15px">LC Wise Cost Summery</h5>
        <h6 align="center" style="margin-top:-15px">LC No: <?=find_a_field('lc_lc_master','lc_no','id='.$_POST['lc_id'].'');?></h6>
        <form action="" method="post">
            <input type="hidden" name="lc_id" value="<?=$_POST['lc_id']?>">
            <input type="hidden" name="report_id" value="1007002">
            <table align="center" id="customers" style="width:98%; border: solid 1px #999; border-collapse:collapse; font-size: 11px; margin-top: -5px">
                <thead>
                <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                    echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
                <tr style="border: solid 1px #999;font-weight:bold; font-size:11px">
                    <th colspan="7" style="border: solid 1px #999; padding:2px; background-color: bisque">LC Information</th>
                    <?php $i = 0;
                    $lctablew=mysqli_query($conn, "Select * from LC_expenses_head where status in ('1')");
                    while($lcrow=mysqli_fetch_array($lctablew)){
                        $i=$i+1;
                        ?>
                    <?php } ?>
                    <th colspan="6" style="border: solid 1px #999; padding:2px; background-color: bisque">Duty</th>
                    <th colspan="<?=$i;?>" style="border: solid 1px #999; padding:2px; background-color: bisque">LC Expenses Details</th>
                    <th rowspan="2" style="border: solid 1px #999; padding:2px; background-color: bisque">Total LC Cost</th>
                    <th rowspan="2" style="border: solid 1px #999; padding:2px; background-color: bisque">Per Unit Cost</th>

                </tr>
                <tr style="border: solid 1px #999;font-size:11px">
                    <th style="border: solid 1px #999; ">SL</th>
                    <!--th style="border: solid 1px #999; ">LC NO</th-->
                    <!--th style="border: solid 1px #999;">LC Date</th-->
                    <th style="border: solid 1px #999;">Item Id</th>
                    <th style="border: solid 1px #999;">Material Description</th>
                    <th style="border: solid 1px #999;">Unit</th>
                    <th style="border: solid 1px #999;">Rate</th>
                    <th style="border: solid 1px #999;">Qty</th>
                    <th style="border: solid 1px #999;">LC Amount</th>
                    <th style="border: solid 1px #999;">CD</th>
                    <th style="border: solid 1px #999;">RD</th>
                    <th style="border: solid 1px #999;">SD</th>
                    <th style="border: solid 1px #999;">VAT</th>
                    <th style="border: solid 1px #999;">AIT</th>
                    <th style="border: solid 1px #999;">ATV</th>
                    <?php
                    $lctablew=mysqli_query($conn, "Select * from LC_expenses_head where status in ('1')");
                    while($lcrow=mysqli_fetch_array($lctablew)){
                        ?><th style="border: solid 1px #999;  width: 10% "><?=$lcrow['LC_expenses_head'];?></th>
                    <?php } ?>
                </tr></thead>
                <tbody>
                <?php
                $g = 0;
                $actualcollection = 0;
                $totalactualcollection =0;
                $grandtotals =0;
                $gtt=0;
                $totallcamount=0;
                $total_CD_amount=0;
                $total_RD_amount=0;
                $total_SD_amount=0;
                $total_VAT_amount=0;
                $total_AIT_amount=0;
                $total_ATV_amount=0;

                $cost_recorded_status=find_a_field('LC_item_wise_cost_sheet','COUNT(id)','lc_id='.$_POST['lc_id']);
                $customization_permmissin=find_a_field('lc_lc_master','cost_customization','id='.$_POST['lc_id']);
                if($customization_permmissin=='1') echo '<h4 style="color:red">Permitted to modify<h4>'; else echo '';
                $result='Select
				llm.id,
				llm.pi_id,
				llm.lc_issue_date,
				llm.party_id,
				llm.lc_no,
				llm.lc_create_date,
				lld.*,
				llr.*,
				i.item_id,
				i.item_name,
				i.unit_name
				from
				lc_lc_master llm,
				LC_costing_breakdown lld,
				lc_lc_received  llr,
				item_info i

				where
				llm.id=lld.lc_id and
				lld.item_id=i.item_id and
				llm.id='.$_POST['lc_id'].' and
				llr.lc_id=lld.lc_id and
				llr.item_id=lld.item_id
group by lld.item_id
				order by llm.id, lld.id';
                $query2 = mysqli_query($conn, $result);
                while($data=mysqli_fetch_object($query2)){
                    $g=$g+1; ?>

                    <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                        <td style="border: solid 1px #999; text-align:center"><?=$g;?></td>
                        <!--td style="border: solid 1px #999; text-align:center"><?=$data->lc_no; ?></td-->
                        <!--td style="border: solid 1px #999; text-align:center"><?=$data->lc_create_date; ?></td-->
                        <td style="border: solid 1px #999; text-align:center"><?=$data->item_id; ?></td>
                        <td style="border: solid 1px #999; text-align:left;padding:2px;"><?=$data->item_name;?></td>
                        <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->unit_name; ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=$data->rate_in_local_currency; ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=$data->total_unit; ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=number_format($data->amount_in_local_currency,2); ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=($data->CD>0)? $data->CD : '-'?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=$data->RD; ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=$data->SD; ?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=($data->VAT>0)? $data->VAT : '-'?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=($data->AIT>0)? $data->AIT : '-'?></td>
                        <td style="border: solid 1px #999; text-align:right; padding:5px"><?=($data->ATV>0)? $data->ATV : '-'?></td>
                        <?php
                        $totalqty=find_a_field('lc_lc_details','SUM(qty)','lc_id='.$_POST['lc_id'].'');
                        $totalactualcollection=$totalactualcollection+$actualcollection;
                        $lctablew=mysqli_query($conn, "Select lh.* from LC_expenses_head lh where lh.status in ('1')");
                        $pwisecosetotal=0;
                        while($lcrow=mysqli_fetch_array($lctablew)){
                        ?><td style="border: solid 1px #999;text-align:center; ">


                            <?php $COST=find_a_field('lc_lc_master',''.$lcrow['db_column_name'].'',''.$lcrow['db_column_name'].'='.$lcrow['db_column_name'].' and id='.$_POST['lc_id'].'');?>
                            <?php
                            $pwisecose=$COST/$totalqty*$data->total_unit;

                            $pwisecosetotal=$pwisecosetotal+$pwisecose;
                            $grandtotal=$pwisecosetotal;?>
                            <?php if ($cost_recorded_status > 0){?>
                                <?php if($pwisecose>0) echo $pwisecose; else echo '-'; ?>
                            <?php } else { ?>
                                <input style="text-align: right; font-size: 10px; width:70px" <?php if($customization_permmissin==0) echo 'readonly' ?> type="text" name="<?=$lcrow['db_column_name'].$data->id;?>" id="<?=$lcrow['db_column_name'].$data->id;?>" value="<?php if($pwisecose>0) echo $pwisecose; else echo '-';?>" class="<?=$lcrow['db_column_name']?>" />
                            <?php } ?>
                            <?php }
                            $total_LC_cost=$data->amount_in_local_currency+$data->TTI;
                            $grandtotals=$grandtotals+$grandtotal+$total_LC_cost;

                            ?>


                        </td>

                        <td style="border: solid 1px #999; text-align:right">
                            <input style="text-align: right; font-size: 10px; width:65px" type="hidden" name="grandtotal<?=$data->id?>" id="grandtotal<?=$data->id?>" value="<?=$total_LC_cost;?>" class="grandtotal<?=$data->id?>" />
                            <?php if ($cost_recorded_status > 0){ echo $grandtotal+$total_LC_cost; } else { ?>
                                <input style="text-align: right; font-size: 10px; width:65px" readonly type="text" name="total_LC_cost<?=$data->id?>" id="total_LC_cost<?=$data->id?>" value="<?=$grandtotal+$total_LC_cost;?>" class="total_LC_cost<?=$data->id?>" />
                            <?php } ?>
                            <input style="text-align: right; font-size: 10px; width:65px"  type="hidden" name="total_others_cost<?=$data->id?>" id="total_others_cost<?=$data->id?>" value="<?=$grandtotal;?>" class="total_others_cost<?=$data->id?>" />
                            <input style="text-align: right; font-size: 10px; width:65px" type="hidden" name="total_unit<?=$data->id?>" id="total_unit<?=$data->id?>" value="<?=$data->total_unit;?>" class="total_unit<?=$data->id?>" /></td>
                        <td style="border: solid 1px #999; text-align:right; font-size: 10px">
                            <?php if ($cost_recorded_status > 0){?><?=number_format((($grandtotal+$total_LC_cost)/$data->total_unit),2);?>
                            <?php } else { ?>
                                <input type="text" style="text-align: right; font-size: 11px; width: auto" readonly name="per_unit_cost<?=$data->id?>" id="per_unit_cost<?=$data->id?>" value="<?=($grandtotal+$total_LC_cost)/$data->total_unit?>" />
                            <?php } ?>
                        </td>

                        <script>
                            $(function(){
                                $('#lc_comission<?=$data->id?>,#lc_insurance<?=$data->id?>,#lc_bank_bill<?=$data->id?>,#freight_charge<?=$data->id?>,#lc_port_bill<?=$data->id?>,#lc_transport<?=$data->id?>,#lc_mis_cost<?=$data->id?>,#lc_others<?=$data->id?>,#air_bill<?=$data->id?>,#duty<?=$data->id?>,#shipping_bill<?=$data->id?>,#labor_bill<?=$data->id?>,#BSTI_expense<?=$data->id?>').keyup(function(){
                                    var grandtotal<?=$data->id?> = parseFloat($('#grandtotal<?=$data->id?>').val()) || 0;
                                    var lc_comission<?=$data->id?> = parseFloat($('#lc_comission<?=$data->id?>').val()) || 0;
                                    var lc_insurance<?=$data->id?> = parseFloat($('#lc_insurance<?=$data->id?>').val()) || 0;
                                    var lc_bank_bill<?=$data->id?> = parseFloat($('#lc_bank_bill<?=$data->id?>').val()) || 0;
                                    var freight_charge<?=$data->id?> = parseFloat($('#freight_charge<?=$data->id?>').val()) || 0;
                                    var lc_port_bill<?=$data->id?> = parseFloat($('#lc_port_bill<?=$data->id?>').val()) || 0;
                                    var lc_transport<?=$data->id?> = parseFloat($('#lc_transport<?=$data->id?>').val()) || 0;
                                    var lc_mis_cost<?=$data->id?> = parseFloat($('#lc_mis_cost<?=$data->id?>').val()) || 0;
                                    var lc_others<?=$data->id?> = parseFloat($('#lc_others<?=$data->id?>').val()) || 0;
                                    var air_bill<?=$data->id?> = parseFloat($('#air_bill<?=$data->id?>').val()) || 0;
                                    var duty<?=$data->id?> = parseFloat($('#duty<?=$data->id?>').val()) || 0;
                                    var shipping_bill<?=$data->id?> = parseFloat($('#shipping_bill<?=$data->id?>').val()) || 0;
                                    var labor_bill<?=$data->id?> = parseFloat($('#labor_bill<?=$data->id?>').val()) || 0;
                                    var BSTI_expense<?=$data->id?> = parseFloat($('#BSTI_expense<?=$data->id?>').val()) || 0;
                                    $('#total_others_cost<?=$data->id?>').val((lc_comission<?=$data->id?> + lc_insurance<?=$data->id?>
                                        + lc_bank_bill<?=$data->id?>+ freight_charge<?=$data->id?>+ lc_port_bill<?=$data->id?>+ lc_transport<?=$data->id?>
                                        + lc_mis_cost<?=$data->id?>+ lc_others<?=$data->id?>+ air_bill<?=$data->id?>+ duty<?=$data->id?>
                                        + shipping_bill<?=$data->id?>+ labor_bill<?=$data->id?>+ BSTI_expense<?=$data->id?>
                                    ));
                                });
                            });
                        </script>

                        <script>
                            $(function(){
                                $('#lc_comission<?=$data->id?>,#lc_insurance<?=$data->id?>,#lc_bank_bill<?=$data->id?>,#freight_charge<?=$data->id?>,#lc_port_bill<?=$data->id?>,#lc_transport<?=$data->id?>,#lc_mis_cost<?=$data->id?>,#lc_others<?=$data->id?>,#air_bill<?=$data->id?>,#duty<?=$data->id?>,#shipping_bill<?=$data->id?>,#labor_bill<?=$data->id?>,#BSTI_expense<?=$data->id?>').keyup(function(){
                                    var grandtotal<?=$data->id?> = parseFloat($('#grandtotal<?=$data->id?>').val()) || 0;
                                    var lc_comission<?=$data->id?> = parseFloat($('#lc_comission<?=$data->id?>').val()) || 0;
                                    var lc_insurance<?=$data->id?> = parseFloat($('#lc_insurance<?=$data->id?>').val()) || 0;
                                    var lc_bank_bill<?=$data->id?> = parseFloat($('#lc_bank_bill<?=$data->id?>').val()) || 0;
                                    var freight_charge<?=$data->id?> = parseFloat($('#freight_charge<?=$data->id?>').val()) || 0;
                                    var lc_port_bill<?=$data->id?> = parseFloat($('#lc_port_bill<?=$data->id?>').val()) || 0;
                                    var lc_transport<?=$data->id?> = parseFloat($('#lc_transport<?=$data->id?>').val()) || 0;
                                    var lc_mis_cost<?=$data->id?> = parseFloat($('#lc_mis_cost<?=$data->id?>').val()) || 0;
                                    var lc_others<?=$data->id?> = parseFloat($('#lc_others<?=$data->id?>').val()) || 0;
                                    var air_bill<?=$data->id?> = parseFloat($('#air_bill<?=$data->id?>').val()) || 0;
                                    var duty<?=$data->id?> = parseFloat($('#duty<?=$data->id?>').val()) || 0;
                                    var shipping_bill<?=$data->id?> = parseFloat($('#shipping_bill<?=$data->id?>').val()) || 0;
                                    var labor_bill<?=$data->id?> = parseFloat($('#labor_bill<?=$data->id?>').val()) || 0;
                                    var BSTI_expense<?=$data->id?> = parseFloat($('#BSTI_expense<?=$data->id?>').val()) || 0;
                                    $('#total_LC_cost<?=$data->id?>').val((lc_comission<?=$data->id?> + lc_insurance<?=$data->id?>
                                        + lc_bank_bill<?=$data->id?>+ freight_charge<?=$data->id?>+ lc_port_bill<?=$data->id?>+ lc_transport<?=$data->id?>
                                        + lc_mis_cost<?=$data->id?>+ lc_others<?=$data->id?>+ air_bill<?=$data->id?>+ duty<?=$data->id?>
                                        + shipping_bill<?=$data->id?>+ labor_bill<?=$data->id?>+ BSTI_expense<?=$data->id?> + grandtotal<?=$data->id?>
                                    ));
                                });
                            });

                            $(function(){
                                $('#lc_comission<?=$data->id?>,#lc_insurance<?=$data->id?>,#lc_bank_bill<?=$data->id?>,#freight_charge<?=$data->id?>,#lc_port_bill<?=$data->id?>,#lc_transport<?=$data->id?>,#lc_mis_cost<?=$data->id?>,#lc_others<?=$data->id?>,#air_bill<?=$data->id?>,#duty<?=$data->id?>,#shipping_bill<?=$data->id?>,#labor_bill<?=$data->id?>,#BSTI_expense<?=$data->id?>').keyup(function(){
                                    var total_others_cost<?=$data->id?> = parseFloat($('#total_others_cost<?=$data->id?>').val()) || 0;
                                    var grandtotal<?=$data->id?> = parseFloat($('#grandtotal<?=$data->id?>').val()) || 0;
                                    var total_unit<?=$data->id?> = parseFloat($('#total_unit<?=$data->id?>').val()) || 0;
                                    $('#per_unit_cost<?=$data->id?>').val(((total_others_cost<?=$data->id?> + grandtotal<?=$data->id?>)/total_unit<?=$data->id?>
                                    ));
                                });
                            });
                        </script>
                    </tr>
                    <?php $gtt=$gtt+$grandtotal;
                    $totallcamount=$totallcamount+$data->amount_in_local_currency;
                    $total_CD_amount=$total_CD_amount+$data->CD;
                    $total_RD_amount=$total_RD_amount+$data->RD;
                    $total_SD_amount=$total_SD_amount+$data->SD;
                    $total_VAT_amount=$total_VAT_amount+$data->VAT;
                    $total_AIT_amount=$total_AIT_amount+$data->AIT;
                    $total_ATV_amount=$total_ATV_amount+$data->ATV;
                } ?>
                <tr><td colspan="5" style="border: solid 1px #999; text-align:right">Total = </td>
                    <td style="border: solid 1px #999; text-align:right"><?=$totalqty;?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($totallcamount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_CD_amount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_RD_amount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_SD_amount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_VAT_amount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_AIT_amount,2);?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=number_format($total_ATV_amount,2);?></td>
                    <?php
                    $lctablew=mysqli_query($conn, "Select lh.* from LC_expenses_head lh where lh.status in ('1')");
                    while($lcrow=mysqli_fetch_array($lctablew)){
                        ?><td style="border: solid 1px #999; text-align:right; padding:2px"><?php $COST=find_a_field('lc_lc_master',''.$lcrow['db_column_name'].'',''.$lcrow['db_column_name'].'='.$lcrow['db_column_name'].' and id='.$_POST['lc_id'].''); if($COST>0) echo $COST; else echo '';?></td>
                    <?php } ?>
                    <td style="border: solid 1px #999; text-align:right">
                        <input style="text-align: right; font-size: 10px; width:65px" type="text" name="grandtotal" id="grandtotal" value="<?=$grandtotals?>"></td>
                    <td>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php
            $LC_received=find_a_field('lc_lc_received','COUNT(id)','lc_id='.$_POST['lc_id']);
            if($LC_received>0){
            if($cost_recorded_status>0){?><h5 align="center" style="color:red; font-weight: italic; font-weight: bold">This LC cost sheet has been recorded!!</h5> <?php } else { ?>
                <h1 align="center">
                <input type="submit" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you confirm to cancel?");' name="record_lc_cost" value="Confirm the sheet & proceed to next">
                </h1>
            <?php } ?>

            <?php } else { ?>
                <h5 align="center" style="color:red; font-weight: italic; font-weight: bold">This LC has not yet been received!!</h5>
            <?php } ?>
        </form>




    <?php elseif ($_POST['report_id']=='1002005'): ?>

        <style>
            #customers {}
            #customers td {}
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #FFCCFF;}
            td{}
        </style>
        <title><?=$_SESSION['company_name'];?> | Account Receivable Status</title>
        <p align="center" style="margin-top:-5px; font-weight: bold; font-size: 22px"><?=$_SESSION['company_name'];?></p>
        <p align="center" style="margin-top:-15px; font-size: 15px">Account Receiable Status</p>
        <p align="center" style="margin-top:-15px; font-size: 15px">As On: <?=$_POST['t_date'];?></p>
        <?php if($_POST['dealer_type']){?>
            <p align="center" style="margin-top:-15px; font-size: 15px">Customer Type: <?=$_POST['dealer_type'];?></p>
        <?php } ?>
        <table align="center" id="customers"  style="width:90%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>

            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color:#FFCCFF">
                <th style="border: solid 1px #999; padding:2px">SL</th>
                <th style="border: solid 1px #999; padding:2px; width:5%">Code</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Accounts Code</th>
                <th style="border: solid 1px #999; padding:2px">Customer Name</th>
                <th style="border: solid 1px #999; padding:2px">Town</th>
                <th style="border: solid 1px #999; padding:2px">Territory</th>
                <th style="border: solid 1px #999; padding:2px">Region</th>
                <th style="border: solid 1px #999; padding:2px">Current Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php

            $datecon=' and j.jvdate<"'.$_POST['t_date'].'"';

            if ($_POST['dealer_type'] != '' && $_POST['dealer_type'] != 'All') {
                $dealer_type_conn=" and d.dealer_type='" . $_POST['dealer_type'] . "'";
            } else {
                $dealer_type_conn=" and 1";
            }
            $totalactualcollection = 0;
            $i                     = 0;
            $result=mysqli_query($conn, "Select
				d.dealer_code,
				d.account_code,
				d.dealer_name_e as dealername,
				t.town_name as town,
				a.AREA_NAME as territory,
				b.BRANCH_NAME as region,
				SUM(j.cr_amt-j.dr_amt) actualcollection
				from
				dealer_info d,
				town t,
				area a,
				branch b,
				journal j
				where
				    j.visible_status=1 and
				d.town_code=t.town_code and
				a.AREA_CODE=d.area_code and
				 d.region=b.BRANCH_ID and
				j.ledger_id=d.account_code and
				d.company_id='".$_SESSION['companyid']."' and d.section_id in ('400000','".$_SESSION['sectionid']."')
				".$datecon.$dealer_type_conn."
				group by d.dealer_code order by b.sl,d.dealer_code");
            $query2 = $result;
            while($data=mysqli_fetch_object($query2)){ ?>
                <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->dealer_code;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->account_code;?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px"><?=$data->dealername;?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:5px; width:10%"><?=$data->town;?></td>
                    <td style="border: solid 1px #999; padding:5px"><?=$data->territory;?></td>
                    <td style="border: solid 1px #999; text-align:left; padding:2px"><?=$data->region;?></td>
                    <td style="border: solid 1px #999; text-align:right; padding:2px"><strong><?=number_format($actualcollection=$data->actualcollection,2);?></strong></td>
                </tr>
                <?php
                $totalactualcollection=$totalactualcollection+$actualcollection;} ?>
            <tr><td colspan="7" style="text-align:right;border: solid 1px #999;">Total</td>
                <td style="text-align:right;border: solid 1px #999;"><strong><?=number_format($totalactualcollection,2)?></strong></td>
            </tr>
            </tbody>
        </table></div>
        </div>
        </div>

    <?php elseif ($_POST['report_id']=='1004001'):?>
        <title>Trial Balance</title>
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
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Trial Balance</p>
        <p align="center" style="margin-top:-12px; font-size: 11px">As On: <?=$_POST['t_date']?></p>
        <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px" >
                <th style="border: solid 1px #999; padding:2px; width: 4%"><strong>SL</strong></th>
                <th style="border: solid 1px #999; padding:2px;"><strong>Account Particulars</strong></th>
                <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Debit Amount</strong></th>
                <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Credit Amount</strong></th>
                <th style="border: solid 1px #999; padding:2px; width:15%"><strong>Balance</strong></th>
            </tr></thead>
            <tbody>
            <?php

            if($sectionid=='400000'){
                $sec_com_connectionT=' and 1';
            } else {
                $sec_com_connectionT=" and b.section_id='".$_SESSION['sectionid']."' and b.company_id='".$_SESSION['companyid']."'";
            }
            $total_dr=0;
            $total_cr=0;
            $cc_code = (int) $_REQUEST['cc_code'];
            if($cc_code > 0)
            { $g="select DISTINCT c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id from accounts_ledger a, journal b,ledger_group c where b.visible_status=1 and a.ledger_id=b.ledger_id and a.ledger_group_id=c.group_id and b.jvdate <= '$t_date' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'  and 1 AND b.cc_code=$cc_code ".$sec_com_connectionT."  group by c.group_id";} else {
                $g="select DISTINCT c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id
		from accounts_ledger a,
		journal b,
		ledger_group c
		where
		b.visible_status=1 and
		a.ledger_id=b.ledger_id and
		a.ledger_group_id=c.group_id and
		b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
		b.jvdate <= '$t_date'".$sec_com_connectionT."
		group by c.group_id";
            }

            $gsql=mysqli_query($conn, $g);
            while($g=mysqli_fetch_row($gsql))

            {   $total_dr=0;
                $total_cr=0;  ?>
                <tr bgcolor="#f5f5f5" style="font-size: 11px; height: 20px"><th colspan="5" align="left"><?php echo $g[3];?> - <?php echo $g[0];?></th></tr>

                <?php
                $cc_code = (int) $_REQUEST['cc_code'];
                if($cc_code > 0)
                { $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate<= '$t_date' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and a.ledger_group_id='$g[3]' and 1 AND b.cc_code=$cc_code ".$sec_com_connectionT."  group by ledger_name order by a.ledger_id";
                }else {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate<= '$t_date' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  a.ledger_group_id='$g[3]' and 1 ".$sec_com_connectionT."  group by ledger_name order by a.ledger_id";
                }

                $pi=0;
                //$t_dr = 0;
                //$t_cr = 0;
                $sql=mysqli_query($conn, $p);
                while($p=mysqli_fetch_row($sql)){
                    $pi++;
                    $dr=$p[1];
                    $cr=$p[2];
                    ?>
                    <tr style="border: solid 1px #999; font-size:11px">
                        <td style="border: solid 1px #999; padding:2px; text-align: center"><?=$pi;?></td>
                        <td style="border: solid 1px #999; padding:2px 10px 2px 2px; text-align: left"><?=$p[3];?> - <?=$p[0];?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($dr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($cr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($dr-$cr,2);?></td>
                    </tr>
                    <?php
                    $total_dr=$total_dr+$dr;
                    $total_cr=$total_cr+$cr;
                    $t_dr=$t_dr+$dr;
                    $t_cr=$t_cr+$cr;
                }?>
                <tr bgcolor="#f5f5f5" style="font-size: 11px">
                    <th colspan="2"  style="border: solid 1px #999;  text-align: right; ">Total <?=$g[0];?>:</th>
                    <th style="border: solid 1px #999; text-align: right;"><?=number_format($total_dr,2);?></th>
                    <th style="border: solid 1px #999; text-align: right;"><?=number_format($total_cr,2)?></th>
                    <th style="border: solid 1px #999; text-align: right;"><?=number_format($total_dr-$total_cr,2)?></th>
                </tr>
            <?php }?>
            <tr  style="font-size: 12px">
                <th colspan="2" style="border: solid 1px #999;  text-align: right;"><strong>Total Balance : </strong></th>
                <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($t_dr,2);?></strong></th>
                <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format($t_cr,2)?></strong></th>
                <th style="border: solid 1px #999; text-align: right;"><strong><?=number_format(($t_dr-$t_cr),2);?></strong></th>
            </tr>
            </tbody>
        </table></div>
        </div>
        </div>

    <?php elseif ($_POST['report_id']=='1004002'):?>
        <title>Trial Balance (Group)</title>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #f5f5f5;}
            td{text-align: center;}
            th{text-align: center;border: solid 1px #999; padding:2px;}

        </style>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Trial Balance (Group)</p>
        <p align="center" style="margin-top:-12px; font-size: 11px">As On: <?=$_POST['t_date']?></p>
        <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #FFCCFF" >
                <th width="5%" height="20" align="center">S/N</th>
                <th align="center">Name of Ledger Group </th>
                <th width="19%" align="center">Debit Amount </th>
                <th width="19%" height="20" align="center">Credit Amount </th>
                <th width="19%" height="20" align="center">Closing Amount </th>
            </tr></thead>
            <tbody>
            <?php
            $total_dr=0;
            $total_cr=0;
            $cc_code = (int) $_REQUEST['cc_code'];
            if($cc_code > 0)
            {
                $p = "select c.group_name,SUM(dr_amt),SUM(cr_amt) from accounts_ledger a, journal j,ledger_group c where j.visible_status=1 and a.ledger_id=j.ledger_id and a.ledger_group_id=c.group_id and j.jvdate <= '".$_POST['t_date']."' AND j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  cc_code=$cc_code ".$sec_com_connection." group by c.group_name order by c.group_id";
            }
            else
            {
                $p = "select c.group_name,SUM(dr_amt),SUM(cr_amt) from accounts_ledger a, journal j,ledger_group c where j.visible_status=1 and a.ledger_id=j.ledger_id and a.ledger_group_id=c.group_id and j.jvdate <= '".$_POST['t_date']."' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'".$sec_com_connection." group by c.group_name order by c.group_id";
            }
            //echo $p;
            $pi=0;
            $sql=mysqli_query($conn, $p);
            while($p=mysqli_fetch_row($sql))
            {?>

                <tr style="border: solid 1px #999; font-size:11px">
                    <td style="border: solid 1px #999; padding:2px; text-align: center" align="center"><?=$i=$i+1;?></td>
                    <td style="border: solid 1px #999; padding:2px; text-align: left"><?=$p[0];?></td>
                    <td style="border: solid 1px #999; padding:2px; text-align: right" align="right"><?=number_format($p[1],2);?></td>
                    <td style="border: solid 1px #999; padding:2px; text-align: right" align="right"><?=number_format($p[2],2);?></td>
                    <td style="border: solid 1px #999; padding:2px; text-align: right" align="right"><?=number_format(($p[1]-$p[2]),2);?></td>
                </tr>
                <?php
                $total_dr=$total_dr+$p[1];
                $total_cr=$total_cr+$p[2];}?>
            </tbody>
            <tfoot>
            <tr style="font-size: 11px">
                <th colspan="2" align="right" style="border: solid 1px #999; text-align: right;">Total Balance</th>
                <th align="right" style="border: solid 1px #999; text-align: right;"><strong><?php echo number_format($total_dr,2);?></strong></th>
                <th align="right" style="border: solid 1px #999; text-align: right;"><strong><?php echo number_format($total_cr,2)?></strong></th>
                <th align="right" style="border: solid 1px #999; text-align: right;"><strong><?=number_format(($total_dr-$total_cr),2);?></strong></th>
            </tr>
            </tfoot>
        </table>




    <?php elseif ($_POST['report_id']=='1004004'):?>
        <title>Periodical Trial Balance</title>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #ddd;}
            td{text-align: center;}
            th{text-align: center;border: solid 1px #999; padding:2px;}

        </style>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Periodical Trial Balance</p>
        <p align="center" style="margin-top:-20px">Group Name: <?=find_a_field('ledger_group','group_name','group_id='.$_REQUEST['group_id']);?></p>
        <p align="center" style="margin-top:-12px; font-size: 11px">From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></p>
        <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #FFCCFF" >
                <th width="4%" height="20" align="center">S/N</th>
                <th width="42%" height="20" align="center">Ledger Group </th>
                <th width="15%" align="center">Opening</th>
                <th width="12%" align="center">Debit </th>
                <th width="12%" align="center">Credit </th>
                <th width="15%" height="20" align="center">Closing</th>
            </tr>
            <?php

            if($_REQUEST['group_id']>0 )
                $grp_con = " and  c.group_id='".$_REQUEST['group_id']."'";
            $total_dr=0;
            $total_cr=0;
            $cc_code = (int) $_REQUEST['cc_code'];
            if($cc_code > 0)
            {
                $g="select c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal j,ledger_group c where j.visible_status=1 and a.ledger_id=j.ledger_id and a.ledger_group_id=c.group_id and j.jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'".$grp_con."".$sec_com_connection."  AND j.cc_code=$cc_code group by  c.group_id";
            }
            else
            {
                $g="select c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal j,ledger_group c where j.visible_status=1 and a.ledger_id=j.ledger_id and a.ledger_group_id=c.group_id and j.jvdate BETWEEN '".$_POST['f_date']."' AND '".$_POST['t_date']."' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'".$grp_con."".$sec_com_connection."  group by  c.group_id";
            }
            $gsql=mysqli_query($conn, $g);
            while($g=mysqli_fetch_row($gsql))
            {
                $total_dr=0;
                $total_cr=0;
                ?>
                <tr style="font-size: 12px">
                    <th colspan="6" align="left" style="text-align: left"><?php echo $g[0];?></th>
                </tr>
                <?php
                $cc_code = (int) $_REQUEST['cc_code'];
                if($cc_code > 0)
                {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal j where j.visible_status=1 and a.ledger_id=j.ledger_id and j.jv_date BETWEEN '$f_date' AND '$t_date' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  a.ledger_group_id='$g[3]' and 1 AND b.cc_code=$cc_code and a.group_for=".$_SESSION['usergroup']."".$sec_com_connection." group by ledger_id order by a.ledger_id";
                }
                else
                {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal j where j.visible_status=1 and a.ledger_id=j.ledger_id and j.jvdate BETWEEN '$f_date' AND '$t_date' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  a.ledger_group_id='$g[3]' and a.group_for=".$_SESSION['usergroup']."".$sec_com_connection." group by ledger_id order by a.ledger_id";

                }

//echo $p;

                $pi=0;
                $sql=mysqli_query($conn, $p);
                while($p=mysqli_fetch_row($sql))
                {
                    $query="select SUM(j.dr_amt),SUM(j.cr_amt) from journal j where j.visible_status=1 and j.jvdate < '$f_date' and j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  ledger_id='$p[3]'".$sec_com_connection."";
                    $ssql=mysqli_query($conn, $query);
                    $open=mysqli_fetch_row($ssql);
                    $opening = $open[0]-$open[1];
                    $pi++;
                    $dr=$p[1];
                    $cr=$p[2];
                    $closing = $opening + $dr - $cr;
                    if($opening>0)
                    { $tag='(Dr)';}
                    elseif($opening<0)
                    { $tag='(Cr)';$opening=$opening*(-1);}
                    if($closing>0)
                    { $tagc='(Dr)';}
                    elseif($closing<0)
                    { $tagc='(Cr)';$closing=$closing*(-1);}
                    ?>
                    <tr style="border: solid 1px #999; font-size:11px" <? $i++; if($i%2==0)$cls=' class="alt"'; else $cls=''; echo $cls;?>>
                        <td style="border: solid 1px #999; padding:2px; text-align: center"><?php echo $pi;?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: left"><a href="transaction_listledger.php?show=show&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>&ledger_id=<?=$p[3]?>" target="_blank"><?php echo $p[0];?></a></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($opening,2).' '.$tag;?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?php echo number_format($dr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?php echo number_format($cr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($closing,2).' '.$tagc;?></td>
                    </tr>
                    <?php
                    $total_dr=$total_dr+$dr;
                    $total_cr=$total_cr+$cr;
                    $t_dr=$t_dr+$dr;
                    $t_cr=$t_cr+$cr;
                }?>
            <?php }?>
            <tr style="border: solid 1px #999; font-size:12px">
                <th colspan="2" align="right" style="text-align:right">Total Balance</th>
                <th align="right" style="text-align:right">&nbsp;</th>
                <th align="right" style="text-align:right"><strong><?php echo number_format($t_dr,2);?></strong></th>
                <th align="right" style="text-align:right"><strong><?php echo number_format($t_cr,2)?></strong></th>
                <th align="right" style="text-align:right"><strong><?=number_format($t_cr-$t_dr,2)?></strong></th>
            </tr>
        </table>



    <?php elseif ($_POST['report_id']=='1004003'):
        if($sectionid=='400000'){
            $sec_com_connectionT=' and 1';
            $sec_com_connection_wa=' and 1';
        } else {
            $sec_com_connectionT=" and b.section_id='".$_SESSION['sectionid']."' and b.company_id='".$_SESSION['companyid']."'";
            $sec_com_connection_wa=" and company_id='".$_SESSION['companyid']."' and section_id in ('400000','".$_SESSION['sectionid']."')";

        }
        ?>
        <title>Periodical Trial Balance (Details)</title>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #f0f0f0;}
            td{border: solid 1px #999; padding:2px;}
            th{text-align: center;border: solid 1px #999; padding:2px;}
        </style>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Periodical Trial Balance (Details)</p>
        <p align="center" style="margin-top:-12px; font-size: 11px">Date Interval: <?=$_POST['f_date']?> to <?=$_POST['t_date']?></p>
        <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px" >
                <th width="4%" height="20" align="center">S/N</th>
                <th width="45%" height="20" align="center">Head Of Accounts </th>
                <th width="19%" align="center">Opening</th>
                <th width="19%" align="center">Debit </th>
                <th width="19%" height="20" align="center">Credit </th>
                <th width="19%" height="20" align="center">Closing </th>
            </tr>
            <?php
            $total_dr=0;
            $total_cr=0;
            $cc_code = (int) $_POST['cc_code'];
            if($cc_code > 0)
            {
                $g="select DISTINCT c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal b,ledger_group c where b.visible_status=1 and a.ledger_id=b.ledger_id and a.ledger_group_id=c.group_id and b.jvdate BETWEEN '".$f_date."' AND '".$t_date."' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'".$sec_com_connectionT." AND b.cc_code=$cc_code group by c.group_id";
            } else {
                $g="select DISTINCT c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal b,ledger_group c where b.visible_status=1 and a.ledger_id=b.ledger_id and a.ledger_group_id=c.group_id and b.jvdate BETWEEN '".$f_date."' AND '".$t_date."' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'".$sec_com_connectionT." group by c.group_id";
            }
            $gsql=mysqli_query($conn, $g);
            while($g=mysqli_fetch_array($gsql))
            {
                $total_dr=0;
                $total_cr=0;
                ?>
                <tr bgcolor="#f0f0f0" style="font-size: 11px; height: 20px">
                    <th colspan="6" style="text-align: left"><?php echo $g[0];?></th>
                </tr>
                <?php
                $cc_code = (int) $_REQUEST['cc_code'];
                if($cc_code > 0)
                {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt) from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate BETWEEN '$f_date' AND '$t_date' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  a.ledger_group_id='$g[3]' and 1 AND b.cc_code=$cc_code".$sec_com_connectionT." group by ledger_name order by a.ledger_id";
                } else {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate BETWEEN '$f_date' AND '$t_date' and b.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  a.ledger_group_id='$g[3]'".$sec_com_connectionT." group by ledger_name order by a.ledger_id";
                }
                $pi=0;
                $sql=mysqli_query($conn, $p);
                while($p=mysqli_fetch_row($sql))
                {
                    $query="select SUM(dr_amt),SUM(cr_amt) from journal where visible_status=1 and jvdate < '$f_date' and jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and  ledger_id='$p[3]'".$sec_com_connection_wa."";
                    $ssql=mysqli_query($conn, $query);
                    $open=mysqli_fetch_array($ssql);
                    $opening = $open[0]-$open[1];
                    if($opening>0)
                    { $tag='(Dr)';}
                    elseif($opening<0)
                    { $tag='(Cr)';$opening=$opening*(-1);}
                    $pi++;
                    $dr=$p[1];
                    $cr=$p[2]; ?>
                    <tr style="border: solid 1px #999; font-size:11px">
                        <td align="center"><?=$pi;?></td>
                        <td align="left" style="text-align: left"><?=$p[0];?></td>
                        <td align="right"><?php if($opening>0) echo number_format($opening,2).' '.$tag; else echo '-';?></td>
                        <td align="right"><?=number_format($dr,2);?></td>
                        <td align="right"><?=number_format($cr,2);?></td>
                        <?php if ($g['group_id'] =='2010'){ ?>
                        <td align="right"><?=number_format(($dr-($opening+$cr)),2);?></td>
                        <?php } else { ?>
                            <td align="right"><?=number_format(($opening+($dr-$cr)),2);?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $total_dr=$total_dr+$dr;
                    $total_cr=$total_cr+$cr;
                    $t_dr=$t_dr+$dr;
                    $t_cr=$t_cr+$cr;
                }?>
                <tr style="border: solid 1px #999; font-size:12px">
                    <th colspan="2" style="text-align: right">Balance : <?php echo number_format(($total_dr-$total_cr),2);?></th>
                    <th style="text-align: right">&nbsp;</th>
                    <th style="text-align: right"><strong><?php echo number_format($total_dr,2);?></strong></th>
                    <th style="text-align: right"><strong><?php echo number_format($total_cr,2)?></strong></th>
                </tr>
            <?php }?>
            <tr style="border: solid 1px #999; font-size:12px">
                <th colspan="2" style="text-align: right">Total Balance : </th>
                <th style="text-align: right">&nbsp;</th>
                <th style="text-align: right"><strong><?php echo number_format($t_dr,2);?></strong></th>
                <th style="text-align: right"><strong><?php echo number_format($t_cr,2)?></strong></th>
            </tr>
        </table>

    <?php elseif ($_POST['report_id']=='1002006'):
        $profit_center=find_a_field('profit_center','profit_center_name','id='.$_POST['pc_code'].'');
        ?>

        <title><?=$profit_center;?></title>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #ddd;}
            td{text-align: center;}
            th{text-align: center;border: solid 1px #999; padding:2px;}

        </style>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Party / Customer Statement</p>
        <p align="center" style="margin-top:-15px; font-size: 13px">Profit Center : <?=$profit_center;?></p>
        <p align="center" style="margin-top:-10px; font-size: 11px">From <?=$_POST['t_date']?> to <?=$_POST['t_date']?></p>
        <table align="center" id="customers" style="width:75%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #FFCCFF" >
                <th width="4%" height="20" align="center">S/N</th>
                <th width="42%" height="20" align="center">Dealer / Customer Name </th>
                <th width="12%" align="center">Debit </th>
                <th width="12%" align="center">Credit </th>
                <th width="15%" height="20" align="center">Closing</th>
            </tr>
            <?php

            if($_REQUEST['group_id']>0 )
                $grp_con = " and  c.group_id='".$_REQUEST['group_id']."'";
            $total_dr=0;
            $total_cr=0;
            $pc_code = (int) $_POST['pc_code'];
            if($pc_code > 0)
            {
                $g="select c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal b,ledger_group c where b.visible_status=1 and a.ledger_id=b.ledger_id and a.ledger_group_id=c.group_id and b.jvdate BETWEEN '$f_date' AND '$t_date' and c.group_for=".$_SESSION['usergroup']." ".$grp_con."  AND b.pc_code=$pc_code group by  c.group_id";
            }
            else
            {
                $g="select c.group_name,SUM(dr_amt),SUM(cr_amt),c.group_id FROM accounts_ledger a, journal b,ledger_group c where b.visible_status=1 and a.ledger_id=b.ledger_id and a.ledger_group_id=c.group_id and b.jvdate BETWEEN '$f_date' AND '$t_date' ".$grp_con." and c.group_for=".$_SESSION['usergroup']."  group by  c.group_id";
            }
            $gsql=mysqli_query($conn, $g);
            while($g=mysqli_fetch_row($gsql))
            {
                $total_dr=0;
                $total_cr=0;
                ?>
                <tr style="font-size: 12px; display: none">
                    <th colspan="6" align="left" style="text-align: left"><?php echo $g[0];?></th>
                </tr>
                <?php
                $pc_code = (int) $_POST['pc_code'];
                if($pc_code > 0)
                {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate BETWEEN '$f_date' AND '$t_date' and a.ledger_group_id='$g[3]' and 1 AND b.pc_code=$pc_code and a.group_for=".$_SESSION['usergroup']." group by ledger_name order by a.ledger_name";
                }
                else
                {
                    $p="select DISTINCT a.ledger_name,SUM(dr_amt),SUM(cr_amt),a.ledger_id from accounts_ledger a, journal b where b.visible_status=1 and a.ledger_id=b.ledger_id and b.jvdate BETWEEN '$f_date' AND '$t_date' and a.ledger_group_id='$g[3]' and a.group_for=".$_SESSION['usergroup']." group by ledger_name order by a.ledger_name";

                }

//echo $p;

                $pi=0;
                $sql=mysqli_query($conn, $p);
                while($p=mysqli_fetch_row($sql))
                {
                    $query="select SUM(dr_amt),SUM(cr_amt) from journal where visible_status=1 and jvdate < '$f_date' and ledger_id='$p[3]' and group_for=".$_SESSION['usergroup'];
                    $ssql=mysqli_query($conn, $query);
                    $open=mysqli_fetch_row($ssql);
                    $opening = $open[0]-$open[1];
                    $pi++;
                    $dr=$p[1];
                    $cr=$p[2];
                    $closing = $opening + $dr - $cr;
                    if($opening>0)
                    { $tag='(Dr)';}
                    elseif($opening<0)
                    { $tag='(Cr)';$opening=$opening*(-1);}
                    if($closing>0)
                    { $tagc='(Dr)';}
                    elseif($closing<0)
                    { $tagc='(Cr)';$closing=$closing*(-1);}
                    ?>
                    <tr style="border: solid 1px #999; font-size:11px" <? $i++; if($i%2==0)$cls=' class="alt"'; else $cls=''; echo $cls;?>>
                        <td style="border: solid 1px #999; padding:2px; text-align: center"><?php echo $pi;?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: left"><a href="transaction_listledger.php?show=show&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>&ledger_id=<?=$p[3]?>" target="_blank"><?php echo $p[0];?></a></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?php echo number_format($dr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?php echo number_format($cr,2);?></td>
                        <td style="border: solid 1px #999; padding:2px; text-align: right"><?=number_format($closing=$dr-$cr,2).' '.$tagc;?></td>
                    </tr>
                    <?php
                    $total_dr=$total_dr+$dr;
                    $total_cr=$total_cr+$cr;
                    $t_dr=$t_dr+$dr;
                    $t_cr=$t_cr+$cr;
                }?>
            <?php }?>
            <tr style="border: solid 1px #999; font-size:12px">
                <th colspan="2" align="right">Total Balance :</th>
                <th align="right"><strong><?php echo number_format($t_dr,2);?></strong></th>
                <th align="right"><strong><?php echo number_format($t_cr,2)?></strong></th>
                <th align="right"><strong><?php echo number_format(($t_dr-$t_cr),2);?></strong></th>

            </tr>
        </table>








    <?php elseif ($_POST['report_id']=='1005001'):
        $fdate=$_POST['f_date'];
        $tdate=$_POST['t_date'];
        $comparisonF=$_POST['pf_date'];
        $comparisonT=$_POST['pt_date'];
        ?>

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


        <title><?=$_SESSION['company_name'];?> | Profit & Loss Statement</title>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top:-13px">Profit & Loss Statement</h4>
        <table align="center" id="customers" style="width:70%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <thead>
            <tr bgcolor="#FFCCFF" style="border: solid 1px #999;font-weight:bold; font-size:11px">
                <th width="40%" style="border: solid 1px #999; padding:2px;"><span class="style1">PARTICULARS</span></th>
                <th width="30%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Current Period<br>( <?=$_REQUEST['f_date'].' - '.$_REQUEST['t_date']?> )</div></th>
                <th width="30%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Previous Period<br>( <?=$_REQUEST['pf_date'].' - '.$_REQUEST['pt_date']?> )</div></th>
            </tr>
            </thead>

            <tr style="background:#FFF0F5; font-weight:bold; color:#FFF; font-size:14px;">
                <td colspan="3" style="color:#000;border: solid 1px #999; padding:2px; text-align: left" >Revenue</td></tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="padding-left:20px; text-align: left;font-size: 11px"><? $headname="Sales"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '1,35'; $amount = sum_com_sub_PL_cr($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $salesNormal = $amount; $total = $amount; $total1 = $amount; echo '<a href="pl_group_details.php?rno=1&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '1,35'; $amount = sum_com_sub_PL_cr($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $salespreNormal = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=1&headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px;font-size: 11px">Less: <? $headname="Sales Return"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '1,35'; $amount = sum_com_sub_PL_dr($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $salesreturn = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=2&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '1,35'; $amount = sum_com_sub_PL_dr($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $salesreturnPRE = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=2&headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px;font-size: 11px"><strong>Gross Sales </strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px">
                    <strong><?php $sales=$salesNormal-$salesreturn; echo number_format($sales,2);?></strong></td>
                <td align="right"   style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px">
                    <strong><? $salespre=$salespreNormal-$salesreturnPRE; echo number_format($salespre,2);?></strong></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Less: <?$headname="VAT"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 3; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalvat = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 3; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalvatpre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Less: <?$headname="Supplementary Duty (SD)"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 2; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalSD = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 2; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalSDpre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr style="border: solid 1px #999; font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><strong>Net Sales </strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px"><strong><? $netSalesCurrent = $sales-($totalvat+$totalSD); echo number_format($netSalesCurrent,2); ?></strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px"><strong><? $netSalesPrevious = $salespre-($totalvatpre+$totalSDpre); echo number_format($netSalesPrevious,2); ?></strong></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><? $headname="Cost of Goods Sales  (COGS)"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px">
                    <? $com_id = '4,36'; $amount_cogs = sum_com($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval);$cc_code = 18;
                    $amount_cc_code = sum_cc_code($conn,$cc_code,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval);
                    $amount=$amount_cogs+$amount_cc_code;
                    $FactoryCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount;
                    echo '<a href="pl_group_details.php?rno=3&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>

                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px">
                    <? $com_id = '4,36'; $amount_cogs_Previous = sum_com($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $cc_code = 18;
                    $amount_cc_code_Previous = sum_cc_code($conn,$cc_code,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval);
                    $amount = $amount_cogs_Previous+$amount_cc_code_Previous; $FactoryPrevious = $amount;
                    $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=3&headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="color:#000; font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>Gross Profit/Loss</strong></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $grossSalesCurrent = ($netSalesCurrent-$FactoryCurrent);
                        if($grossSalesCurrent>0){
                            $grossSalesCurrents=number_format($grossSalesCurrent,2);
                        } else {
                            $grossSalesCurrents=	"(".number_format(substr($grossSalesCurrent,1),2).")";
                        }
                        echo $grossSalesCurrents;?></strong></td>

                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>
                        <? $grossSalesPrevious = ($netSalesPrevious-$FactoryPrevious);
                        if($grossSalesPrevious>0){
                            $grossSalesPreviouss=number_format($grossSalesPrevious,2);
                        } else {
                            $grossSalesPreviouss=	"(".number_format(substr($grossSalesPrevious,1),2).")";
                        }
                        echo $grossSalesPreviouss;?></strong></td>
            </tr>

            <tr style="border: solid 1px #999;text-align: left; background:#FFF0F5; font-weight:bold; color:#000; font-size:14px"><td colspan="3" style="color:#000; text-align: left">Operating Expenses</td></tr>
            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Administrative Expense"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '19,20,23,35,36,37,38,17,39'; $amount = sum_cc_code($conn, $cc_code,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $adminExpCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=4&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '19,20,23,35,36,37,38,17,39'; $amount = sum_cc_code($conn, $cc_code,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $adminExpPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=4&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr style="font-size:11px"><td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Selling and Distribution Expenses"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '21,34,40,41'; $amount = sum_cc_code($conn, $cc_code,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $SandDErowCurrentAmounttotal = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=5&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '21,34,40,41'; $amount = sum_cc_code($conn, $cc_code,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $SandDErowCurrentAmounttotalPre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=5&headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><? $headname="Marketing Expenses"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '22'; $amount = sum_cc_code($conn, $cc_code,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $marketingExpCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=6&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $cc_code = '22'; $amount = sum_cc_code($conn, $cc_code,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $marketingExpPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=6&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Sales Promotional Expenses"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '7'; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalspx = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=7&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '7'; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalspxs = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=7&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr style="border: solid 1px #999;background-color:#FFF; color:#000; font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>Total Operating Expenses </strong></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $opertaingExpCurrent = ($adminExpCurrent+$SandDErowCurrentAmounttotal+$totalspx+$marketingExpCurrent); echo number_format($opertaingExpCurrent,2); ?></strong></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $opertaingExpPrevious = ($adminExpPrevious+$SandDErowCurrentAmounttotalPre+$totalspxs+$marketingExpPrevious); echo number_format($opertaingExpPrevious,2); ?></strong></td>
            </tr>

            <tr style="border-left: solid 1px #999;border-bottom: solid 1px #FFF;border-right: solid 1px #999;background-color:#FFF; color:#FFF; font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>Operating Profit </strong></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $operatingProfitCurrent = ($grossSalesCurrent-$opertaingExpCurrent);
                        if($operatingProfitCurrent>0){
                            $operatingProfitCurrents=number_format($operatingProfitCurrent,2);
                        } else {
                            $operatingProfitCurrents='('.number_format(substr($operatingProfitCurrent,1),2).')';
                        }
                        echo $operatingProfitCurrents; ?></strong></td>

                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $operatingProfitPrevious = ($grossSalesPrevious-$opertaingExpPrevious);
                        if($operatingProfitPrevious>0){
                            $operatingProfitPreviouss=number_format($operatingProfitPrevious,2);
                        } else {
                            $operatingProfitPreviouss='('.number_format(substr($operatingProfitPrevious,1),2).')';
                        }
                        echo $operatingProfitPreviouss; ?></strong></td>
            </tr>

            <tr style="background:#FFF0F5; font-weight:bold; color:#000; font-size:14px"><td colspan="3" style="border: solid 1px #999;text-align: left;  font-weight:bold;  font-size:14px">Other Expenses</td></tr>
            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Financial Expenses"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalfinancialcostpre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Extra Ordinary Loss"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '9'; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totaleol = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '9'; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totaleolpre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Non-Operating Expenses (Royalty) </td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '10'; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalroyality = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '10'; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totalroyalitypre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr style="color:#000; font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>Total Other Expenses </strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $otherExpCurrent = $totalfinancialcost+$totaleol+$totalroyality; echo number_format($otherExpCurrent,2); ?></strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? $otherExpPrevious = $totalfinancialcostpre+$totaleolpre+$totalroyalitypre; echo number_format($otherExpPrevious,2); ?></strong></td>
            </tr>

            <tr style="color:#000; font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong>Net Operating Profit Over Expenses</strong></td>
                <td align="right"  style="border: solid 1px #999;color:#000; font-weight:bold; text-align: right"><strong><? $netOperProfitCurrent = ($operatingProfitCurrent-$otherExpCurrent);
                        if($netOperProfitCurrent>0){
                            echo number_format($netOperProfitCurrent,2);  } else {echo '('.number_format(substr($netOperProfitCurrent,1),2).')'; }  ?></strong></td>
                <td align="right"  style="border: solid 1px #999;color:#000; font-weight:bold; text-align: right"><strong><? $netOperProfitPrevious = ($operatingProfitPrevious-$otherExpPrevious);
                        if($netOperProfitPrevious>0){
                            echo number_format($netOperProfitPrevious,2);  } else {echo '('.number_format(substr($netOperProfitPrevious,1),2).')'; }  ?></strong></td>
            </tr>


            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Other Income </td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '11'; $amount = sum_com_liabilities($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totherincome = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '11'; $amount = sum_com_liabilities($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $totherincomepre = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr  style="font-size:12px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><strong>Net Profit/(Loss) Before Tax </strong></td>
                <td align="right"   style="border: solid 1px #999;text-align: right; padding-right:5px"><strong><? $pbtCurrent = $netOperProfitCurrent+$totherincome;
                        if($pbtCurrent>0) { echo number_format($pbtCurrent,2); } else { echo '('.number_format(substr($pbtCurrent,1),2).')'; } ?></strong></td>
                <td align="right"   style="border: solid 1px #999;text-align: right; padding-right:5px"><strong>
                        <? $pbtPrevious = $netOperProfitPrevious+$totherincomepre;
                        if($pbtPrevious>0) { echo number_format($pbtPrevious,2); } else { echo '('.number_format(substr($pbtPrevious,1),2).')'; }?>
                    </strong></td>
            </tr>


            <tr style="font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Provision for Income Tax </td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '13'; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $incomeTaxCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '13'; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $incomeTaxPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>


            <tr bgcolor="#FFF0F5" style="font-size:13px;">
                <td  style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000; height: 30px"><strong>Net Profit/(Loss) after tax</strong></td>
                <td align="right"   style="border: solid 1px #999;text-align: right; padding-right:5px; color:#000"><strong><? $patCurrent = $pbtCurrent-$incomeTaxCurrent;
                        if($patCurrent>0){
                            echo number_format($patCurrent,2); } else { echo '('.number_format(substr($patCurrent,1),2).')'; } ?></strong></td>
                <td align="right"  style="border: solid 1px #999;text-align: right; padding-right:5px;color:#000"><strong><? //$patPrevious = $pbtPrevious-$incomeTaxPrevious; echo number_format($patPrevious,2); ?>
                        <? $patPrevious = $pbtPrevious-$incomeTaxPrevious;
                        if($patPrevious>0){
                            echo number_format($patPrevious,2); } else { echo '('.number_format(substr($patPrevious,1),2).')'; } ?></strong></td>
            </tr>
            <thead>
        </table>






    <?php elseif ($_POST['report_id']=='1005002'):
        $fdate='0000-00-00';
        $tdate=$_POST['t_date'];
        $comparisonF=date('Y-m-d' , strtotime($fdate));
        $comparisonT=date('Y-m-d' , strtotime($_POST['pt_date']));
        ?>
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
        <title><?=$_SESSION['company_name'];?> | Financial Statement</title>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top:-13px">Financial Statement</h4>
        <table align="center" id="customers" style="width:70%; border: solid 1px #999; border-collapse:collapse; ">
            <thead><p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>

            <tr bgcolor="#FFCCFF" style="border: solid 1px #999;font-weight:bold; font-size:13px">
                <th width="40%" style="border: solid 1px #999; padding:2px;"><span class="style1">PARTICULARS</span></th>
                <th width="30%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Current Period<br>( <?=$_REQUEST['t_date'];?> )</div></th>
                <th width="30%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Previous Period<br>( <?=$_REQUEST['pt_date'];?> )</div></th> </tr></thead>

            <tr style="background:#FFF0F5; font-weight:bold; color:#FFF; font-size:14px;">
                <td colspan="3" style="color:#000;border: solid 1px #999; padding:2px;; text-align: left" ><em>ASSETS</em></td></tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td colspan="3" style="border: solid 1px #999; padding:2px; text-align: left;">Non current Assets :</td></tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Property Plant Equipment"; echo $headname; ?></td>
                <td  style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 14; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPPE = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td  style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 14; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPPEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px">Less: <?$headname="Accumulated Depreciation"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? $com_id = 15; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? $com_id = 15; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $ADSearchRowPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></strong></td>
            </tr>

            <tr style="font-weight:bold; font-size: 12px">
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong></strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $grossAssetsCurrent = ($TotalPPE-$TotalADCurrent);
                    if($grossAssetsCurrent>0){
                        $grossAssetsCurrents=number_format($grossAssetsCurrent,2);
                    } else {
                        $grossAssetsCurrents=	"(".number_format(substr($grossAssetsCurrent,1),2).")";
                    }
                    echo $grossAssetsCurrents;?>
                </td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;">
                    <? $grossAssetsPrevious = ($TotalPPEPrevious);
                    if($grossAssetsPrevious>0){
                        $grossAssetsPreviouss=number_format($grossAssetsPrevious,2);
                    } else {
                        $grossAssetsPreviouss=	"(".number_format(substr($grossAssetsPrevious,1),2).")";
                    }
                    echo $grossAssetsPreviouss;?>
                </td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Inventory"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 16; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalInventoryCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 16; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalInventoryPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Accounts Receivable"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 17; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalARCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 17; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalARPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Advance, Deposit & Prepayment"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 19; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 19; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Long Term Investment"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 23; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLTICurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 23; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLTIPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Deferred Expenses"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 22; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 22; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Advance Income Tax"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 21; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAITCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 21; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAITPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Cash & Cash Equivalents"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$fdate,$tdate,'1002000100000000','1002000101000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalCCECurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$comparisonF,$comparisonT,'1002000100000000','1002000101000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalCCEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Bank Balance"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$fdate,$tdate,'1002000200000000','1002000901000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBBCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$comparisonF,$comparisonT,'1002000200000000','1002000901000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBBPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-weight:bold; font-size: 13px;">
                <td style="text-align:right;"><strong>Total Current Assets</strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $TotalAssetsCurrent = ($TotalInventoryCurrent+$TotalARCurrent+$TotalADPCurrent+$TotalDEPCurrent+$TotalAITCurrent+$TotalCCECurrent+$TotalBBCurrent+$TotalLTICurrent);
                    if($TotalAssetsCurrent>0){
                        $TotalAssetsCurrents=number_format($TotalAssetsCurrent,2);
                    } else {
                        $TotalAssetsCurrents='('.number_format(substr($TotalAssetsCurrent,1),2).')';
                    }
                    echo $TotalAssetsCurrents; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;">
                    <? $TotalAssetsPrevious = ($TotalInventoryPrevious+$TotalARPrevious+$TotalADPPrevious+$TotalDEPrevious+$TotalAITPrevious+$TotalCCEPrevious+$TotalBBPrevious+$TotalLTIPrevious);
                    if($TotalAssetsPrevious>0){
                        $TotalAssetsPreviouss=number_format($TotalAssetsPrevious,2);
                    } else {
                        $TotalAssetsPreviouss='('.number_format(substr($TotalAssetsPrevious,1),2).')';
                    }
                    echo $TotalAssetsPreviouss; ?>
                </td>
            </tr>
            <tr style="font-size: 14px; background:#FFF0F5;">
                <td  style="border: solid 1px #999; padding:2px; text-align: right;"><strong><i>Total Asset</i></strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? $TotalAssetsCurrent = ($grossAssetsCurrent+$TotalAssetsCurrent);
                        if($TotalAssetsCurrent>0){
                            $TotalAssetsCurrents=number_format($TotalAssetsCurrent,2);
                        } else {
                            $TotalAssetsCurrents='('.number_format(substr($TotalAssetsCurrent,1),2).')';
                        }
                        echo $TotalAssetsCurrents; ?></strong></td>

                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? $TotalAssetsPrevious = ($grossAssetsPrevious+$TotalAssetsPrevious);
                        if($TotalAssetsPrevious>0){
                            $TotalAssetsPreviouss=number_format($TotalAssetsPrevious,2);
                        } else {
                            $TotalAssetsPreviouss='('.number_format(substr($TotalAssetsPrevious,1),2).')';
                        }
                        echo $TotalAssetsPreviouss; ?></strong>
                </td>
            </tr>
            <tr style="background:#FFF0F5; font-weight:bold; color:#FFF; font-size:14px;">
                <td colspan="3" style="color:#000;border: solid 1px #999; padding:2px; text-align: left" ><em>EQUITY AND LIABILITIES</em>
                </td>
            </tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td colspan="3" style="border: solid 1px #999; padding:2px; text-align: left;">Shareholder's Equity:</td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Share Capital"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 25; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSCCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 25; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSCPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="padding-left:20px; text-align: left"><?$headname="Reserves & Surplus"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 26; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalRNSCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 26; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalRNSPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px;padding-left:20px; text-align: left;"><?$headname="Profit / Loss"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $amount = sum_com_P_L($conn,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $patCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&income=3000&show=Show&expenses=4000" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $amount = sum_com_P_L($conn,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $patPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; padding:2px; text-align: right;"><td></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;font-size: 12px">
                    <strong><?php $totalSEQUITYCurrent=$TotalSCCurrent+$TotalRNSCurrent+$patCurrent; echo number_format($totalSEQUITYCurrent,2);?></strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;font-size: 12px">
                    <strong><?php $totalSEQUITYPrevious=$TotalSCPrevious+$TotalRNSPrevious+$patPrevious; echo number_format($totalSEQUITYPrevious,2);?></strong></td>
            </tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td colspan="3" style="border: solid 1px #999; padding:2px; text-align: left;"><strong>LONG TERM LOAN:</strong></td></tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Bank Loan(HPSM)"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 27; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBLHPSMCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 27; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBLHPSMPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Unsecured Loan"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 33; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalUNLCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 33; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalUNLPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-weight:bold;"><td></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right; font-size: 12px"><?php $totalLTOCurrent=$TotalBLHPSMCurrent+$TotalUNLCurrent; echo number_format($totalLTOCurrent,2)?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right; font-size: 12px"><?php $totalLTOPrevious=$TotalBLHPSMPrevious+$TotalUNLPrevious; echo number_format($totalLTOPrevious,2);?></td>
            </tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td colspan="3" style="border: solid 1px #999; padding:2px; text-align: left;"><strong>CURRENT LIABILITIES:</strong></td></tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Short Term Loan"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 29; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSTLOANSMCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 29; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSTLOANPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Provision for expenses"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 32; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPFECurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 32; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPFEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Accounts Payable"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 28; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 28; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Statutory Payables"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 30; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 30; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Intercompany Payable"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 18; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalIPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 18; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalIPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Share Money Deposit"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 34; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 34; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Liability for Employee Benefits"; echo $headname; ?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 31; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLEBCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><? $com_id = 31; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLEBPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-weight:bold; font-size: 12px; text-align: right"><td style="text-align: right">Total Current Liabilities</td>
                <td style="border: solid 1px #999; padding:2px; text-align: right; text-decoration: double"><?php $totalCLIABILITIESCurrent=$TotalSTLOANSMCurrent+$TotalPFECurrent+$TotalAPCurrent+$TotalSPCurrent+$TotalIPCurrent+$TotalSMDCurrent+$TotalLEBCurrent; echo number_format($totalCLIABILITIESCurrent,2);?></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><?php $totalCLIABILITIESPrevious=$TotalSTLOANPrevious+$TotalPFEPrevious+$TotalAPPrevious+$TotalSPPrevious+$TotalIPPrevious+$TotalSMDPrevious+$TotalLEBPrevious; echo number_format($totalCLIABILITIESPrevious,2)?></td>
            </tr>
            <tr style="font-size: 14px; background:#FFF0F5;">
                <td  style="border: solid 1px #999; padding:2px; text-align: right;"><strong><i>Total Equity and Liabilities</i></strong></td>
                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? $TOTALENLCurrent =$totalSEQUITYCurrent+ $totalCLIABILITIESCurrent+$totalLTOCurrent;
                        if($TOTALENLCurrent>0){
                            echo number_format($TOTALENLCurrent,2); } else { echo '('.number_format(substr($TOTALENLCurrent,1),2).')'; } ?></strong></td>

                <td style="border: solid 1px #999; padding:2px; text-align: right;"><strong><? //$patPrevious = $pbtPrevious-$incomeTaxPrevious; echo number_format($patPrevious,2); ?>
                        <?
                        $TOTALENLPrevious =$totalSEQUITYPrevious+ $totalCLIABILITIESPrevious+$totalLTOPrevious;
                        if($TOTALENLPrevious>0){
                            echo number_format($TOTALENLPrevious,2); } else { echo '('.number_format(substr($TOTALENLPrevious,1),2).')'; } ?></strong></td>
            </tr>
        </table>





    <?php elseif ($_POST['report_id']=='1005003'):
        $fdate='0000-00-00';
        $tdate=$_POST['t_date'];
        $comparisonF=date('Y-m-d' , strtotime($t));
        $comparisonT=date('Y-m-d' , strtotime($_POST['pt_date']));
        ?>
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
            .double-underline {
                border-bottom: 4px double;
            }
        </style>
        <title><?=$_SESSION['company_name'];?> | Cash Flow Statement</title>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h4 align="center" style="margin-top:-13px">Cash Flow Statement</h4>
        <h6 align="center" style="margin-top:-13px">For the year ended <?=$_POST['t_date']?></h6>


        <table align="center" id="customers" style="width:80%; border: solid 1px #999; border-collapse:collapse; ">
            <thead><p style="width:85%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>

            <tr bgcolor="#FFCCFF" style="border: solid 1px #999;font-weight:bold; font-size:13px">
                <th width="2%" style="border: solid 1px #999; padding:2px;"><span class="style1">SL</span></th>
                <th width="58%" style="border: solid 1px #999; padding:2px;"><span class="style1">PARTICULARS</span></th>
                <th width="20%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Current Period<br>( <?=$_REQUEST['t_date'];?> )</div></th>
                <th width="20%" align="center" style="border: solid 1px #999; padding:2px;"><div align="center">Previous Period<br>( <?=$_REQUEST['pt_date'];?> )</div></th> </tr></thead>

            <tr style="background:#FFF0F5; font-weight:bold; color:#FFF; font-size:14px;">
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td>1</td><td colspan="4" style="padding:2px; text-align: left;">CASH FLOW FROM OPERATING ACTIVITIES:</td></tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px">A.</td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net Profit After Tax"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $amount = sum_com_P_L($conn,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $patCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&income=3000&show=Show&expenses=4000" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $amount = sum_com_P_L($conn,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $patPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <? $com_id = 15; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 15; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $ADSearchRowPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 22; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 22; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px">B.</td>
                <td style="padding:2px; text-align: left; padding-left:20px">Add: <?$headname="Adjustment for non-cash Items"; echo '<u><strong>'.$headname.'</strong></u>'; ?></td>
                <td style="padding:2px; text-align: right;"><strong><?=$B_totalCurrent=number_format($TotalADCurrent+$TotalDEPCurrent)?></strong></td>
                <td style="padding:2px; text-align: right;"><strong><?=$B_totalPrevious=number_format($ADSearchRowPrevious+$TotalDEPrevious)?></strong></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">i)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="Depreciation of Fixed Assets for"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 15; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 15; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $ADSearchRowPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">ii)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="Adjustment of Deferred Expenses"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 22; $amount = sum_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 22; $amount = sum_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalDEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px">C.</td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Movement in Working Capital"; echo '<u><strong>'.$headname.'</strong></u>'; ?></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">(a)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="(increase) /Decrease in Inventory"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 16; $amount = sum_cash_flow_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalInventoryCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 16; $amount = sum_cash_flow_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalInventoryPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">(b)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="(Increase) /Decrease in Adv., Dep. & Pre-Payments"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 19; $amount = sum_cash_flow_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 19; $amount = sum_cash_flow_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalADPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">(c)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="(Increase) /Decrease in Sundry Debtors"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 17; $amount = sum_cash_flow_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalARCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 17; $amount = sum_cash_flow_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalARPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <? $com_id = 29; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSTLOANSMCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 29; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSTLOANPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount;?>
            <? $com_id = 32; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPFECurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 32; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPFEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 28; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 28; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalAPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 30; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 30; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 18; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalIPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 18; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalIPPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 34; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 34; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 31; $amount = sum_cash_flow_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLEBCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 31; $amount = sum_cash_flow_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLEBPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:40px">(d)</td>
                <td style="padding:2px; text-align: left; padding-left:40px"><?$headname="(Increase) /Decrease in Current Liabilities"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right; text-decoration: double"><?php $totalCLIABILITIESCurrent=$TotalSTLOANSMCurrent+$TotalPFECurrent+$TotalAPCurrent+$TotalSPCurrent+$TotalIPCurrent+$TotalSMDCurrent+$TotalLEBCurrent; echo number_format($totalCLIABILITIESCurrent,2);?></td>
                <td style="padding:2px; text-align: right;"><?php $totalCLIABILITIESPrevious=$TotalSTLOANPrevious+$TotalPFEPrevious+$TotalAPPrevious+$TotalSPPrevious+$TotalIPPrevious+$TotalSMDPrevious+$TotalLEBPrevious; echo number_format($totalCLIABILITIESPrevious,2)?></td>
            </tr>
            <?php
            $C_totalCurrent = $TotalInventoryCurrent+$TotalADPCurrent+$TotalARCurrent+$totalCLIABILITIESCurrent;
            $C_totalPrevious =$TotalInventoryPrevious+$TotalADPPrevious+$TotalARPrevious+$totalCLIABILITIESPrevious;
            $D_totalCurrent = $B_totalCurrent + $C_totalCurrent;
            $D_totalPrevious = $B_totalPrevious + $C_totalPrevious;
            $One_totalCurrent = $patCurrent+$D_totalCurrent;
            $One_totalPrevious = $patPrevious+$D_totalPrevious;
            ?>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($C_totalCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($C_totalPrevious,2)?></strong></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px">D.</td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net Cash After Adjustment (B + C)"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($D_totalCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($D_totalPrevious,2)?></strong></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px">E.</td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net Cash generated /(used) from Operating Activities"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($One_totalCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($One_totalPrevious,2)?></strong></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td>2</td><td colspan="4" style="padding:2px; text-align: left;">CASH FLOW FROM INVESTING ACTIVITIES:</td></tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Purchase / Sales of Fixed Assets"; echo $headname; ?></td>
                <td  style="padding:2px; text-align: right;"><? $com_id = 14; $amount = sum_cash_flow_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPPE = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td  style="padding:2px; text-align: right;"><? $com_id = 14; $amount = sum_cash_flow_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalPPEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Other Investment"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 23; $amount = sum_cash_flow_com($conn, $com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLTICurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 23; $amount = sum_cash_flow_com($conn, $com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalLTIPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <?php
            $Two_TotalCurrent = $TotalPPE + $TotalLTICurrent;
            $Two_TotalPrevious = $TotalPPEPrevious + $TotalLTIPrevious;
            ?>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net Cash generated /(used) from Investing Activities"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;"><strong><?=number_format($Two_TotalCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;"><strong><?=number_format($Two_TotalPrevious,2)?></strong></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>
            <tr style="font-weight:bold; color:#000; font-size:13px;"><td>3</td><td colspan="4" style="padding:2px; text-align: left;">CASH FLOW FROM FINANCING ACTIVITIES :</td></tr>

            <? $com_id = 27; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBLHPSMCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 27; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalBLHPSMPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 33; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalUNLCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>
            <? $com_id = 33; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalUNLPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; ?>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Long Term Loan Received"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $TotalBLHPSMCurrent+$TotalUNLCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $TotalBLHPSMPrevious+$TotalUNLPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>



            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Share Deposit A/c"; echo $headname; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 34; $amount = sum_com_liabilities($conn,$com_id,$fdate,$tdate,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 34; $amount = sum_com_liabilities($conn,$com_id,$comparisonF,$comparisonT,$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalSMDPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <?php
            $three_totalCurrent = $TotalBLHPSMCurrent+$TotalUNLCurrent+$TotalSMDCurrent;
            $three_totalPrevious = $TotalBLHPSMCurrent+$TotalUNLCurrent+$TotalSMDPrevious;
            ?>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($three_totalCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;text-decoration: underline"><strong><?=number_format($three_totalPrevious,2)?></strong></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net cash & equivalents increased/(decreased) [1+2+3]"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;"><strong><?php $net_cashCurrent=$One_totalCurrent+$Two_TotalCurrent+$three_totalCurrent; echo number_format($net_cashCurrent,2);?></strong></td>
                <td style="padding:2px; text-align: right;"><strong><? $net_cashPrevious=$One_totalPrevious+$Two_TotalPrevious+$three_totalPrevious; echo number_format($net_cashPrevious,2); ?></strong></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>

            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net cash & equivalents - Opening"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$fdate,$tdate,'1002000100000000','1002000101000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalCCECurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
                <td style="padding:2px; text-align: right;"><? $com_id = 24; $amount = sum_com_sub($conn, $com_id,$comparisonF,$comparisonT,'1002000100000000','1002000101000000',$sec_com_connection,$lockedStartInterval,$lockedEndInterval); $TotalCCEPrevious = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$comparisonF.'&tdate='.$comparisonT.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"><?$headname="Net cash & equivalents - Closing"; echo '<strong>'.$headname.'</strong>'; ?></td>
                <td style="padding:2px; text-align: right;"><strong class="double-underline"><?php $cash_closingCurrent=$net_cashCurrent+$TotalCCECurrent; echo number_format($cash_closingCurrent,2)?></strong></td>
                <td style="padding:2px; text-align: right;"><strong class="double-underline"><?php $cash_closingPrevious=$net_cashPrevious+$TotalCCEPrevious; echo number_format($cash_closingPrevious,2) ?></strong></td>
            </tr>
            <tr style="font-size:11px">
                <td style="padding:2px; text-align: center; padding-left:20px"></td>
                <td style="padding:2px; text-align: left; padding-left:20px"></td>
                <td style="padding:2px; text-align: right;"></td>
                <td style="padding:2px; text-align: right;"></td>
            </tr>
        </table>



    <?php elseif ($_POST['report_id']=='1005004'):
        $year = $_POST['year'];
        ?>
        <title>Ratio Analysis</title>
        <style>
            #customers {
                font-family: "Gill Sans", sans-serif;
            }
            #customers td {
            }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #f5f5f5;}
            td{text-align: center;}
            th{text-align: center;border: solid 1px #999; padding:2px;}

        </style>
        <h2 align="center" style="margin-top: -8px"><?=$_SESSION['company_name'];?></h2>
        <p align="center" style="margin-top:-20px">Ratio Analysis</p>
        <p align="center" style="margin-top:-12px; font-size: 11px">For the year: <?=$_POST['year']?></p>
        <table align="center" id="customers" style="width:50%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px; background-color: #FFCCFF" >
                <th align="center">Particulars</th>
                <th width="19%" height="20" align="center">Amount</th>
            </tr></thead>
            <tbody>
            <tr style="border: solid 1px #999; font-size:11px">
                <td style="padding-left:20px; text-align: left;font-size: 11px"><? $headname="Sales"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '1,35'; $amount = sum_com_sub_RA_cr($conn, $com_id,$year,$sec_com_connection)-sum_com_sub_RA_dr($conn, $com_id,$year,$sec_com_connection); $salesNormal = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=1&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px;font-size: 11px"><? $headname="COGS"; echo $headname; ?></td>
            <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px">
                <? $com_id = '4,36'; $amount_cogs = sum_RA_com($conn,$com_id,$year,$sec_com_connection);$cc_code = 18;
                $amount_cc_code = sum_cc_code_RA($conn,$cc_code,$year,$sec_com_connection);
                $amount=$amount_cogs+$amount_cc_code;
                $FactoryCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount;
                echo '<a href="pl_group_details.php?rno=3&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code='.$cc_code.'&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="padding-left:20px; text-align: left;font-size: 11px"><? $headname="EBIT"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '0'; $amount = sum_com_sub_RA_cr($conn, $com_id,$year,$sec_com_connection); $salesNormal = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=1&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="padding-left:20px; text-align: left;font-size: 11px"><? $headname="EBITDA"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '0'; $amount = sum_com_sub_RA_cr($conn, $com_id,$year,$sec_com_connection); $salesNormal = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?rno=1&headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Interest Expense"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Net Income"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Total Debt"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Total Assets"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Net Fixed Assets"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Total Equity"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Current Assets"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '8'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Current Liabilities"; echo $headname; ?></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = "29,32,28,30,18,34,31"; $amount = sum_com_liabilities_RA($conn,$com_id,$year,$sec_com_connection); $TotalSTLOANSMCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="font-size:11px">
                <td  style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Cash & Cash Equivalents"; echo $headname; ?></td>
                <td align="right" style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = '24'; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $totalfinancialcost = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="pl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Accounts Receivables"; echo $headname; ?></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 17; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $TotalARCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Inventories"; echo $headname; ?></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 16; $amount = sum_RA_com($conn, $com_id,$year,$sec_com_connection); $TotalInventoryCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>

            <tr style="border: solid 1px #999; font-size:11px">
                <td style="border: solid 1px #999; padding:2px; text-align: left; padding-left:20px"><?$headname="Accounts Payable"; echo $headname; ?></td>
                <td style="border: solid 1px #999;text-align: right; padding-right:5px;font-size: 11px"><? $com_id = 28; $amount = sum_com_liabilities_RA($conn,$com_id,$year,$sec_com_connection); $TotalAPCurrent = $amount; $total = $total + $amount; $total1 = $total1 + $amount; echo '<a href="bl_group_details.php?headname='.$headname.'&fdate='.$fdate.'&tdate='.$tdate.'&cc_code=&show=Show&com_id='.$com_id.'" style="text-decoration:none" target="_new">'.number_format($amount,2).'</a>';?></td>
            </tr>
            </tbody>
        </table>



    <?php elseif ($_POST['report_id']=='1008001'):?>
        <title><?=$warehouse_name= find_a_field('warehouse','warehouse_name','warehouse_id="'.$_POST['warehouse_id'].'"');?> : Transaction Statement</title>
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

        <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
        <h5 align="center" style="margin-top:-12px">Transaction Statement</h5>
        <h6 align="center" style="margin-top:-12px">Warehouse / CMU / Factory: <?=$warehouse_name;?></h6>

        <?php if($_POST['status']=='Received'){?>
            <h4 align="center" style="margin-top:-10px">Status : Received</h4>
        <?php } elseif ($_POST['status']=='Issue'){?>
            <h4 align="center" style="margin-top:-10px">Status : Issue</h4>
        <?php } ?>
        <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center" id="customers"  style="width:98%; border: solid 1px #999; border-collapse:collapse;">
            <thead>
            <p style="width:98%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #FFCCFF">
                <th style="border: solid 1px #999; padding:2px">SL</th>
                <th style="border: solid 1px #999; padding:2px; %">T.ID</th>
                <th style="border: solid 1px #999; padding:2px; %">Trns. Date</th>
                <th style="border: solid 1px #999; padding:2px">FG Code</th>
                <th style="border: solid 1px #999; padding:2px">FG Description</th>
                <th style="border: solid 1px #999; padding:2px">Category</th>
                <th style="border: solid 1px #999; padding:2px">UOM</th>
                <th style="border: solid 1px #999; padding:2px">Pack<br>Size</th>
                <th style="border: solid 1px #999; padding:2px">Source</th>
                <th style="border: solid 1px #999; padding:2px">Batch</th>
                <th style="border: solid 1px #999; padding:2px">Expiry Date</th>
                <th style="border: solid 1px #999; padding:2px; ">Warehoues Name</th>
                <?php if($_POST['status']=='Received'){?>
                    <th style="border: solid 1px #999; padding:2px; ">PO NO</th>
                <?php } elseif ($_POST['status']=='Issue'){?>
                    <th style="border: solid 1px #999; padding:2px; ">DO NO</th>
                <?php } ?>
                <th style="border: solid 1px #999; padding:2px; ">Tr No</th>
                <th style="border: solid 1px #999; padding:2px; ">C.No</th>
                <th style="border: solid 1px #999; padding:2px; ">Entry At</th>
                <th style="border: solid 1px #999; padding:2px; ">User</th>
                <th style="border: solid 1px #999; padding:2px">IN (Pcs)</th>
                <th style="border: solid 1px #999; padding:2px">OUT (Pcs)</th>
                <th style="border: solid 1px #999; padding:2px">Rate</th>
                <th style="border: solid 1px #999; padding:2px">Amount</th>
            </tr></thead>

            <tbody>
            <?php
            $datecon=' and a.ji_date between  "'.$f_date.'" and "'.$t_date.'"';
            if($_POST['warehouse_id']>0) 				$warehouse_id=$_POST['warehouse_id'];
            if(isset($warehouse_id)) 				{$warehouse_con=' and a.warehouse_id='.$warehouse_id;} else {$warehouse_id='';}
            if($_POST['item_id']>0) 					$item_id=$_POST['item_id'];
            if(isset($item_id))				{$item_con=' and a.item_id='.$item_id;} else {$item_con='';}
            if($_POST['status']=='Received')
            {$status_con=' and a.item_in>0';}
            elseif($_POST['status']=='Issue')
            {$status_con=' and a.item_ex>0';} else {$status_con='';}

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
		a.tr_from as Source,
		a.batch,
		a.expiry_date,
		w.warehouse_name as warehouse,
		a.tr_no,
		a.custom_no,
		a.entry_at,
		a.do_no,
		a.po_no,
		c.fname as User,
		a.item_price,
		a.total_amt


				from
				journal_item a,
				item_info i,
				users c,
				item_sub_group s,
				warehouse w

				where c.user_id=a.entry_by and s.sub_group_id=i.sub_group_id and
				a.warehouse_id=w.warehouse_id and
                a.ji_date NOT BETWEEN "'.$lockedStartInterval.'" and "'.$lockedEndInterval.'" and
		    a.item_id=i.item_id '.$datecon.$warehouse_con.$item_con.$status_con.' order by a.ji_date,a.id asc');
            $i = 0;$intotal=0;$outtotal=0;
            while($data=mysqli_fetch_object($result)){
                $i=$i+1; ?>
                <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$i;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->ID;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->Trnsdate;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->fg_code;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->item_name;?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->Category; ?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->UOM;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->packsize;?></td>

                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->Source;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->batch;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->expiry_date;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->warehouse;?></td>

                    <?php if($_POST['status']=='Received'){?>
                        <td style="border: solid 1px #999; text-align:center;  padding:2px"><? if ($data->po_no>0) echo $data->po_no;?></td>
                    <?php } elseif ($_POST['status']=='Issue'){?>
                        <td style="border: solid 1px #999; text-align:center;  padding:2px"><? if ($data->do_no>0) echo $data->do_no;?></td>
                    <?php } ?>

                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->tr_no;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->custom_no;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->entry_at;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->User;?></td>
                    <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->INPcs>0) echo $data->INPcs;?></td>
                    <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->OUTPcs>0) echo $data->OUTPcs;?></td>
                    <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->item_price>0) echo $data->item_price;?></td>
                    <td style="border: solid 1px #999; text-align:right;  padding:2px"><? if ($data->INPcs>0) echo number_format(($data->item_price*$data->INPcs),2); else number_format(($data->item_price*$data->OUTPcs),2); ?></td>

                </tr>
                <?php
                $intotal=$intotal+$data->INPcs;
                $outtotal=$outtotal+$data->OUTPcs;
            } ?>
            <tr style="font-size:12px"><td colspan="<?php if($_POST['status']=='Received'){ echo 14; } elseif ($_POST['status']=='Issue'){ echo '14'; } else {echo '14';}?> " style="text-align:right; "><strong>Total</strong></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($intotal,2)?></strong></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($outtotal,2)?></strong></td>
            </tr>
            </tbody>
        </table>
        </div>
        </div>
        </div>


    <?php elseif ($_POST['report_id']=='1006002'):?>
        <title>Closing balace Confirmation Report</title>
        <style>
            #customers { }
            #customers td {      }
            #customers tr:ntd-child(even)
            {background-color: #f0f0f0;}
            #customers tr:hover {background-color: #FFCCFF;}
            td{  text-align: center;}
        </style>

        <h3 align="center" style="margin-top: -12px"><?=$_SESSION['company_name']?></h3>
        <h5 align="center" style="margin-top:-12px">Closing balace Confirmation Report</h5>
        <h6 align="center" style="margin-top:-10px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center" id="customers"  style="width:90%; border: solid 1px #999; border-collapse:collapse;">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:11px; background-color: #FFCCFF">
                <th style="border: solid 1px #999; padding:2px;width:1%">SL</th>
                <th style="border: solid 1px #999; padding:2px;width:10%">Trns. Date</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Code</th>
                <th style="border: solid 1px #999; padding:2px; width:20%">Vendor Name</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Payment(Dr)</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Ref. no.</th>
                <th style="border: solid 1px #999; padding:2px; width:10%">Ref. Date</th>
                <th style="border: solid 1px #999; padding:2px;">Remarks</th>
            </tr></thead>

            <tbody>
            <?php
            $datecon=' and p.paymentdate between  "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';
            if($_POST['ledger_id']>0) 					$ledger_id=$_POST['ledger_id'];
            if(isset($ledger_id))				{$ledger_id_con=' and p.ledger_id='.$ledger_id;}
            $result=mysqli_query($conn, 'select p.*,al.*,v.*
		        from
				payment p,
				accounts_ledger al,
				vendor v
				where p.ledger_id=al.ledger_id and
				al.ledger_group_id in ("2002") and
				al.ledger_id=v.ledger_id'.$ledger_id_con.$datecon.' order by p.paymentdate,p.id asc');
            while($data=mysqli_fetch_object($result)){?>
                <tr style="border: solid 1px #999; font-size:10px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$i=$i+1;;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->paymentdate;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->ledger_id;?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->vendor_name;?></td>
                    <td style="border: solid 1px #999; text-align:right"><?=$data->dr_amt;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?=$data->cheq_no;?></td>
                    <td style="border: solid 1px #999; text-align:center;  padding:2px"><?php if($data->cheq_no>0) echo date("d.m.Y",$data->cheq_date);?></td>
                    <td style="border: solid 1px #999; text-align:left;  padding:2px"><?=$data->narration;?></td>

                </tr>
                <?php
                $drtotal=$drtotal+$data->dr_amt;
            } ?>
            <tr style="font-size:12px"><td colspan="4" style="text-align:right; "><strong>Total</strong></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"><strong><?=number_format($drtotal,2)?></strong></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"></td>
                <td style="border: solid 1px #999; text-align:right;  padding:2px"></td>
            </tr>
            </tbody>
        </table>


    <?php elseif ($_POST['report_id']=='1008002'):?>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h5 align="center" style="margin-top:-15px">Present Stock (Material)</h5>
        <h6 align="center" style="margin-top:-15px">Warehouse Name: <?=find_a_field('warehouse','warehouse_name','warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
        <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center"  style="width:80%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                <th style="border: solid 1px #999; padding:2px">S/L</th>
                <th style="border: solid 1px #999; padding:2px">Code</th>
                <th style="border: solid 1px #999; padding:2px">Material Description</th>
                <th style="border: solid 1px #999; padding:2px">Material Sub Group</th>
                <th style="border: solid 1px #999; padding:2px">Material Group</div></th>
                <th style="border: solid 1px #999; padding:2px">UOM</th>
                <th style="border: solid 1px #999; padding:2px">Pk. Size</th>
                <th style="border: solid 1px #999; padding:2px">Present Stock</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $fgresult="Select  j.item_id, i.item_id,i.item_name,i.finish_goods_code,i.unit_name,i.pack_size,i.serial, s.sub_group_id, s.group_id, g.group_id,s.sub_group_name,g.group_name,
SUM(j.item_in-j.item_ex) as presentstock
from
item_info i,
journal_item j,
item_sub_group s,
item_group g
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$t_date."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
g.group_id not in ('500000000')
group by j.item_id order by g.group_id DESC,i.serial";
            $persentrow = mysqli_query($conn, $fgresult);
            while($data=mysqli_fetch_object($persentrow)){ ?>
                <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->sub_group_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->group_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->pack_size;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->presentstock,2);?></td>
                </tr>
                <?php $ttotalclosing=$ttotalclosing+$pstock;  } ?>
            <tr style="font-size:12px; font-weight:bold; border: solid 1px #999;">
                <td colspan="7" style="text-align:right;border: solid 1px #999;"> Total</td>
                <td style="text-align:center;border: solid 1px #999; width: auto"><?=number_format($ttotalclosing,2)?></td>
            </tr>
            </tbody>
        </table></div>
        </div>
        </div>


    <?php elseif ($_POST['report_id']=='1008003'):?>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h5 align="center" style="margin-top:-15px">Present Stock (Finish Goods)</h5>
        <h6 align="center" style="margin-top:-15px">Warehouse Name: <?=find_a_field('warehouse','warehouse_name','warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
        <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center"  style="width:80%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                <th style="border: solid 1px #999; padding:2px">S/L</th>
                <th style="border: solid 1px #999; padding:2px">Code</th>
                <th style="border: solid 1px #999; padding:2px">FG Description</th>
                <!--th style="border: solid 1px #999; padding:2px">FG Sub Group</th>
                <th style="border: solid 1px #999; padding:2px">FG Group</div></th-->
                <th style="border: solid 1px #999; padding:2px">UOM</th>
                <th style="border: solid 1px #999; padding:2px">Present Stock</th>
                <th style="border: solid 1px #999; padding:2px">Rate</th>
                <th style="border: solid 1px #999; padding:2px">Amount</th>
            </tr>
            </thead>

            <tbody>
            <?php $ismail = 0;
            $fgresult="Select  j.item_id, i.item_id,i.item_name,i.finish_goods_code as custom_Code,i.unit_name,i.pack_size,i.serial, s.sub_group_id, s.group_id, g.group_id,s.sub_group_name,g.group_name,
SUM(j.item_in-j.item_ex) as presentstock,i.d_price as rate
from
item_info i,
journal_item j,
item_sub_group s,
item_group g
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$t_date."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
g.group_id in ('500000000')
group by j.item_id order by g.group_id DESC,i.finish_goods_code";
            $persentrow = mysqli_query($conn, $fgresult);
            while($data=mysqli_fetch_object($persentrow)){ ?>
                <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
                    <!--td style="border: solid 1px #999; text-align:center"><?=$data->sub_group_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->group_name;?></td-->
                    <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->presentstock,2);?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->rate;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=number_format($pstocks=$data->presentstock*$data->rate,2);?></td>
                </tr>
                <?php $ttotalclosing=$ttotalclosing+$pstock;
                $ttotalclosing_in_value = $ttotalclosing_in_value+$pstocks;} ?>
            <tr style="font-size:12px; font-weight:bold; border: solid 1px #999;">
                <td colspan="4" style="text-align:right;border: solid 1px #999;"> Total</td>
                <td style="text-align:center;border: solid 1px #999; width: auto"><?=number_format($ttotalclosing,2)?></td>
                <td style="text-align:center;border: solid 1px #999; width: auto"></td>
                <td style="text-align:center;border: solid 1px #999; width: auto"><?=number_format($ttotalclosing_in_value,2)?></td>
            </tr>
            </tbody>
        </table></div>
        </div>
        </div>




    <?php elseif ($_POST['report_id']=='1008004'):?>
        <h2 align="center"><?=$_SESSION['company_name'];?></h2>
        <h5 align="center" style="margin-top:-15px">Present Stock (Asset)</h5>
        <h6 align="center" style="margin-top:-15px">Warehouse Name: <?=find_a_field('warehouse','warehouse_name','warehouse_id="'.$_POST['warehouse_id'].'"');?> </h6>
        <h6 align="center" style="margin-top:-15px">Report From <?=$_POST['f_date']?> to <?=$_POST['t_date']?></h6>
        <table align="center"  style="width:80%; border: solid 1px #999; border-collapse:collapse; ">
            <thead>
            <p style="width:90%; text-align:right; font-size:11px; font-weight:normal">Reporting Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></p>
            <tr style="border: solid 1px #999;font-weight:bold; font-size:12px">
                <th style="border: solid 1px #999; padding:2px">S/L</th>
                <th style="border: solid 1px #999; padding:2px">Code</th>
                <th style="border: solid 1px #999; padding:2px">FG Description</th>
                <th style="border: solid 1px #999; padding:2px">FG Sub Group</th>
                <th style="border: solid 1px #999; padding:2px">FG Group</div></th>
                <th style="border: solid 1px #999; padding:2px">UOM</th>
                <th style="border: solid 1px #999; padding:2px">Pk. Size</th>
                <th style="border: solid 1px #999; padding:2px">Present Stock</th>
            </tr>
            </thead>

            <tbody>
            <?php $ismail=0;
            $fgresult="Select  j.item_id, i.item_id,i.item_name,i.finish_goods_code,i.unit_name,i.pack_size,i.serial, s.sub_group_id, s.group_id, g.group_id,s.sub_group_name,g.group_name,
SUM(j.item_in-j.item_ex) as presentstock
from
item_info i,
journal_item j,
item_sub_group s,
item_group g
where
j.item_id=i.item_id and
j.warehouse_id='".$_POST['warehouse_id']."' and
j.ji_date <= '".$t_date."' and
j.ji_date NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
g.group_id in ('".$_POST['group_id']."')
group by j.item_id order by g.group_id DESC,i.serial";
            $persentrow = mysqli_query($conn, $fgresult);
            while($data=mysqli_fetch_object($persentrow)){ ?>
                <tr style="border: solid 1px #999; font-size:11px; font-weight:normal">
                    <td style="border: solid 1px #999; text-align:center"><?=$ismail=$ismail+1;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->item_id;?></td>
                    <td style="border: solid 1px #999; text-align:left"><?=$data->item_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->sub_group_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->group_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->unit_name;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=$data->pack_size;?></td>
                    <td style="border: solid 1px #999; text-align:center"><?=number_format($pstock=$data->presentstock,2);?></td>
                </tr>
                <?php $ttotalclosing=$ttotalclosing+$pstock;  } ?>
            <tr style="font-size:12px; font-weight:bold; border: solid 1px #999;">
                <td colspan="7" style="text-align:right;border: solid 1px #999;"> Total</td>
                <td style="text-align:center;border: solid 1px #999; width: auto"><?=number_format($ttotalclosing,2)?></td>
            </tr>
            </tbody>
        </table></div>
        </div>
        </div>
    <?php elseif ($_POST['report_id']=='1002002'):
        $sql="Select v.ledger_id,v.vendor_id,v.ledger_id,v.vendor_name,FORMAT(SUM(j.dr_amt-j.cr_amt),2) as balance  from
vendor v,
journal j
where
    j.visible_status=1 and
j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and
v.ledger_id=j.ledger_id group by v.ledger_id order by v.vendor_id"; echo reportview($sql,'Accounts Payable Status','98',0,'',0); ?>

    <?php elseif ($_POST['report_id']=='1006001'):
        $sql="Select v.ledger_id,v.vendor_id,v.ledger_id,v.vendor_name,FORMAT(SUM(j.dr_amt),2) as Dr_amt,FORMAT(SUM(j.cr_amt),2) as Cr_amt,FORMAT(SUM(j.dr_amt-j.cr_amt),2) as Closing_Balance  from
vendor v,
journal j
where
    j.visible_status=1 and
v.ledger_id=j.ledger_id and
j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'
group by v.ledger_id order by v.vendor_name"; echo reportview($sql,'Outstanding Balance','98',0,'',0); ?>


    <?php elseif ($_POST['report_id']=='1011001'):
        if($_POST['v_type']!=''){$v_type .= "AND j.tr_from = '".$_POST['v_type']."'";}
        $sql="Select i.item_id,i.item_id,i.finish_goods_code as custom_code,i.item_name,i.unit_name, s.sub_group_name, g.group_name,lc.landad_cost,lc.entry_date as last_updated_date from
item_info i,
item_sub_group s,
item_group g,
item_landad_cost lc
where
i.item_id=lc.item_id and
lc.status='Active' and
i.sub_group_id=s.sub_group_id and
s.group_id=g.group_id and
s.group_id in (".selectmultipleoptions($_POST['group_id']).")"; echo reportview($sql,'Material Costing','80',0,'',0); ?>

    <?php endif; ?>
    </body>
</html>
