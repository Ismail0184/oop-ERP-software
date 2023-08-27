<?php
require_once 'support_file.php';
$title='Mushak 6.8';
$page="acc_mushak_6.3.php";
$table='purchase_return_details';
$unique='id';
$$unique=$_GET[$unique];
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$timess = $dateTime->format("h:i A");
$year=date('Y');
$now=date('Y-m-d h:s:i');
$table_VAT_Master='VAT_mushak_6_6';
$table_VAT_details='VAT_mushak_6_8_details';
$mushaks=find_all_field('VAT_mushak_6_6','','source in ("Purchase_Returned") and do_no='.$$unique);

if(prevent_multi_submit()){
if(isset($_POST['record'])){
  if($_POST['mushak_no']>0 && !empty($_POST['issue_date'])){
  $_POST['do_no']=$$unique;
  $_POST['mushak_no']=$_POST['mushak_no'];
  $_POST['warehouse_id']=$_POST['warehouse_id'];
  $_POST['dealer_code']=$_POST['dealer_code'];
  $_POST['issue_date']=$_POST['issue_date'];
  $_POST['issue_time']=$_POST['issue_time'];
  $_POST['responsible_person']=$_POST['responsible_person'];
  $_POST['entry_by']=$_SESSION['userid'];
  $_POST['entry_at']=$now;
  $_POST['year']=$year;
  $crud = new crud($table_VAT_Master);
  $crud->insert();

  $query="SELECT sdc.*,SUM(sdc.amount) as total_unit,i.item_name,i.unit_name,i.VAT from ".$table." sdc, item_info i where sdc.item_id=i.item_id and i.item_id not in ('1096000100010312') and sdc.".$unique."=".$$unique." group by i.item_id order by i.finish_goods_code";
  $result=mysqli_query($conn, $query);
  while($data=mysqli_fetch_object($result)):
    $id=$data->item_id;
    $_POST['item_id']=$id;
    $_POST['challen_no_and_date']=$_POST['challen_no_and_date'.$id];
    $_POST['remarks_of_debit_note']=$_POST['remarks_of_debit_note'.$id];
    $_POST['price4']=$_POST['price4'.$id];
    $_POST['qty5']=$_POST['qty5'.$id];
    $_POST['qty6']=$_POST['qty6'.$id];
    $_POST['qty7']=$_POST['qty7'.$id];
    $_POST['price8']=$_POST['price8'.$id];
    $_POST['qty9']=$_POST['qty9'.$id];
    $_POST['qty10']=$_POST['qty10'.$id];
    $_POST['qty11']=$_POST['qty11'.$id];
    if($_POST['total_unit'.$id]>0){
    $crud = new crud($table_VAT_details);
    $crud->insert();}
  endwhile;
  mysqli_query($conn, "Update purchase_return_master SET mushak_challan_status='RECORDED' where id=".$_GET[$unique]);
  unset($_POST);
  //echo "<script>window.close(); </script>";
}}}

if (isset($_POST['skipandforward']))
{
    mysqli_query($conn, "Update purchase_return_master SET mushak_challan_status='RECORDED' where id=".$_GET[$unique]);
    unset($_POST);
    echo "<script>window.close(); </script>";
}

$mushak=find_all_field('VAT_mushak_6_6','','source in ("Purchase_Returned") and do_no='.$_GET[$unique]);
$COUNT_mushak=find_a_field('VAT_mushak_6_6','COUNT(mushak_no)','source in ("Purchase_Returned") and do_no='.$_GET[$unique]);
$status=find_a_field('VAT_mushak_6_6','COUNT(id)','source in ("Purchase_Returned") and do_no='.$_GET[$unique]);
$do_master=find_all_field('purchase_return_master','','id='.$_GET[$unique]);
$warehouse_master=find_all_field('warehouse','','warehouse_id='.$do_master->warehouse_id);
$dealer_master=find_all_field('vendor','','vendor_id='.$do_master->vendor_id);
$VAT_master=find_all_field('VAT_mushak_6_6','','source="Purchase_Returned" and do_no='.$_GET[$unique]);
$latest_id=find_a_field('VAT_mushak_6_6','MAX(mushak_no)','year='.$year.' and warehouse_id='.$do_master->warehouse_id);

if($status>0){
  if($_GET['group_by']=='VAT_item_group'){

  $query="SELECT
  mus.*,
  SUM(mus.total_unit) as total_unit,
  mus.total_price,
  vtg.group_name as item_name,
  mus.rate_of_SD,
  mus.amount_of_SD,
  mus.rate_of_VAT,
  mus.amount_of_VAT,
  mus.total_including_all,
  i.unit_name,
  i.SD AS VAT,
  mus.source
  from
  item_info i,
  VAT_mushak_6_8_details mus,
  VAT_item_group vtg
  where
  i.VAT_item_group=vtg.group_id and
  mus.source in ('Purchase_Returned') and
  mus.item_id=i.item_id and
  i.item_id not in ('1096000100010312') and
  mus.do_no=".$_GET[$unique]."
  group by i.VAT_item_group order by i.finish_goods_code";
} else {
  $query="SELECT
  mus.*,
  SUM(mus.total_unit) as total_unit,
  mus.total_price,
  i.item_name,
  mus.rate_of_SD,
  mus.amount_of_SD,
  mus.rate_of_VAT,
  mus.amount_of_VAT,
  mus.total_including_all,
  i.unit_name,
  i.SD AS VAT,
  mus.source
  from
  item_info i,
  VAT_mushak_6_8_details mus
  where
  mus.source in ('Purchase_Returned') and
  mus.item_id=i.item_id and
  i.item_id not in ('1096000100010312') and
  mus.do_no=".$_GET[$unique]."
  group by mus.item_id order by i.finish_goods_code";
}


} else {
$query="SELECT sdc.*,SUM(sdc.qc_qty) as total_unit,i.item_name,i.unit_name,i.SD AS VAT,i.VAT_percentage,i.SD_percentage from ".$table." sdc, item_info i where sdc.item_id=i.item_id and i.item_id not in ('1096000100010312') and sdc.m_id=".$_GET[$unique]." group by i.item_id order by i.finish_goods_code";
}
$result=mysqli_query($conn, $query);
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<header>
    <title><?=$title?></title>
    <script type="text/javascript">
        function hide()
        {
            document.getElementById("pr").style.display = "none";
        }
    </script>
    <style>
        #customers {}
        #customers td {}
        #customers tr:ntd-child(even)
        {background-color: #white;}
        #customers tr:hover {background-color: #F0F0F0;}
        td{}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
</header>
<body style="font-size: 11px">
  <?php if($status>0){ ?>
<div id="pr" style="margin-left:48%">
    <div align="left">
        <form id="form1" name="form1" method="post" action="">
            <p><input name="button" type="button" onclick="hide();window.print();" value="Print" /></p>
        </form>
    </div>
<form action="<?=$pate?>" method="get">
<input type="hidden" name="<?=$unique?>"  value="<?=$$unique?>" />
<?php if($_GET['group_by']=='item_id'){?>
          <input type="hidden" name="group_by"  value="VAT_item_group" />
                  <p><input type="submit"  value="View by GROUP" /></p>
<?php } elseif($_GET['group_by']=='VAT_item_group'){?>
<input type="hidden" name="group_by"  value="item_id" />
        <p><input type="submit"  value="View by Item" /></p>
      <?php } else { ?>
        <input type="hidden" name="group_by"  value="VAT_item_group" />
                <p><input type="submit"  value="View by GROUP" /></p>
      <?php } ?>
    </form>
</div>
<?php } ?>
<form method="post" action="">
  <input type="hidden" name="warehouse_id" value="<?=$do_master->warehouse_id?>">
  <input type="hidden" name="dealer_code" value="<?=$do_master->vendor_id?>">
  <input type="hidden" name="responsible_person" value="<?=$warehouse_master->VAT_responsible_person?>">
  <input type="hidden" name="chalan_no" value="<?=$chalan_no?>">
  <input type="hidden" name="jv_no" value="<?=$jv_no?>">
  <input type="hidden" name="jvdate" value="<?=$mushak->issue_date?>">
  <input type="hidden" name="source" value="Purchase_Returned">
<table style="width: 100%">
<td style="width: 30%; text-align: right;"><img src="../assets/images/bd.png" width="50" height="50" style="margin-top: 50px;
  padding:0px;"></td>
    <td style="text-align: center"></td>
    <td style="width: 30%"><div style="text-align: center;height: 30px; margin-top: 50px; vertical-align:middle; width: 100px; border: 1px solid black; font-size: 13px;"><strong>মূসক-৬.৮</strong></div></td>
</table>
<div style="text-align: center; margin-top: -50px"><strong>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</strong></div>
<div style="text-align: center"><strong>জাতীয় রাজস্ব র্বোড</strong></div>
<div style="text-align: center"><strong>ডেবিট নোট</strong></div>
    <div style="text-align: center"><strong>[বিধি ৪০ এর উপ-বিধি (১) এর দফা (ছ) দ্রষ্টব্য]</strong></div>
<br>
<table style="width: 100%;">
    <tr>
        <th style="width: 10%; text-align: left">নিবন্ধিত ব্যক্তির নাম:</th><th style="width: 1%">:</th><td style="width: 50%"><?=$_SESSION['company_name']?></td>
        <th style="width: 10%; text-align: right">চালানপত্র নম্বর</th><th style="width: 1%">:</th><td style="width: 29%">
            <?php if($status>0){ echo $VAT_master->mushak_no; } else { ?>
                <input type="text" style="font-size:11px;width:40px" value="<?=$latest_id?>" readonly name="mushak_no_current">
                <input type="text" style="font-size:11px;width:73px" value="<?=$latest_id+1?>" name="mushak_no">
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">নিবন্ধিত ব্যক্তির বিআইএন:</th><th style="width: 1%">:</th><td style="width: 50%"><?=find_a_field('company','BIN','company_id="'.$_SESSION['companyid'].'" and section_id='.$_SESSION['sectionid'])?></td>
        <th style="width: 10%; text-align: right">ইস্যুর তারিখ</th><th style="width: 1%">:</th><td style="width: 29%"><?php if($status>0){ echo $VAT_master->issue_date; } else { ?>
                <input type="date" value="<?=date('Y-m-d')?>" style="font-size:11px;" name="issue_date">
            <?php } ?></td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">চালানপত্র ইস্যুর ঠিকানা :</th><th style="width: 1%">:</th><td style="width: 50%"><?=$_SESSION['company_address'];?></td>
        <th style="width: 10%; text-align: right">ইস্যুর সময়</th><th style="width: 1%">:</th><td style="width: 29%"><?php if($status>0){ echo $VAT_master->issue_time; } else { ?>
                <input type="text" value="<?=$timess;?>" style="font-size:11px;" name="issue_time"><?php } ?></td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">ক্রেতার / গ্রহীতার নাম</th><th style="width: 1%">:</th><td style="width: 50%"><?=$dealer_master->vendor_name?></td>
        <th style="width: 10%; text-align: right"></th><th style="width: 1%"></th><td style="width: 29%"></td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">ক্রেতার / গ্রহীতার বিআইএন</th><th style="width: 1%">:</th><td style="width: 50%"><?=$dealer_master->TIN_BIN?></td>
        <th style="width: 10%; text-align: right"></th><th style="width: 1%"></th><td style="width: 29%"></td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">ঠিকানা</th><th style="width: 1%">:</th><td style="width: 50%"><?=$dealer_master->address?></td>
        <th style="width: 10%; text-align: right"></th><th style="width: 1%"></th><td style="width: 29%"></td>
    </tr>
    <tr>
        <th style="width: 10%; text-align: left">যানবাহনের প্রকৃতি ও নম্বর</th><th style="width: 1%">:</th><td style="width: 50%"></td>
        <th style="width: 10%; text-align: right"></th><th style="width: 1%"></th><td style="width: 29%"></td>
    </tr>
</table>
<br>

<table id="customers" style="border-collapse: collapse; border: 1px solid #CCC; width: 100%">
    <thead>
    <tr>
        <th style="border: 1px solid #CCC;width: 1%" rowspan="2">ক্রমিক</th>
        <th style="border: 1px solid #CCC;" rowspan="2">কর চালান পত্রের নম্বর ও তারিখ</th>
        <th style="border: 1px solid #CCC;" rowspan="2">ডেভিড নোট ইস্যুর কারণ</th>
        <th style="border: 1px solid #CCC;" colspan="4">চালান পত্রে উল্লেখিত সরবরাহের</th>
        <th style="border: 1px solid #CCC;width: 5%" colspan="4">বৃদ্ধিকারী সমন্বয়ের সহিত সংশ্লিষ্ট সমন্বয়যোগ্য</th>
    </tr>
    <tr>
        <th style="border: 1px solid #CCC;width: 5%">মূল্য</th>
        <th style="border: 1px solid #CCC;width: 5%">পরিমান</th>
        <th style="border: 1px solid #CCC;width: 5%">মূল্য সংযোজন করের পরিমান</th>
        <th style="border: 1px solid #CCC;width: 5%">সম্পূরক শুল্কের পরিমাণ</th>

        <th style="border: 1px solid #CCC;width: 5%">মূল্য</th>
        <th style="border: 1px solid #CCC;width: 5%">পরিমান</th>
        <th style="border: 1px solid #CCC;width: 5%">মূল্য সংযোজন করের পরিমান</th>
        <th style="border: 1px solid #CCC;width: 5%">সম্পূরক শুল্কের পরিমাণ</th>
    </tr>
    <tr>
        <th style="border: 1px solid #CCC; text-align: center">(1)</th>
        <th style="border: 1px solid #CCC; text-align: center">(2)</th>
        <th style="border: 1px solid #CCC; text-align: center">(3)</th>
        <th style="border: 1px solid #CCC; text-align: center">(4)</th>
        <th style="border: 1px solid #CCC; text-align: center">(5)</th>
        <th style="border: 1px solid #CCC; text-align: center">(6)</th>
        <th style="border: 1px solid #CCC; text-align: center">(7)</th>
        <th style="border: 1px solid #CCC; text-align: center">(8)</th>
        <th style="border: 1px solid #CCC; text-align: center">(9)</th>
        <th style="border: 1px solid #CCC; text-align: center">(10)</th>
        <th style="border: 1px solid #CCC; text-align: center">(11)</th>
    </tr>
    </thead>


    <tbody>
      <?php
      if($status>0):
      while($data=mysqli_fetch_object($result)):
        $id=$data->item_id;
        $ab=$data->SD_percentage;$ef=$data->VAT_percentage;
        $cd=$data->total_unit*$data->VAT*$ab;?>
      <tr>
      <td style="border: 1px solid #CCC;text-align: center; margin: 10px"><?=$i=$i+1?></td>
      <td style="border: 1px solid #CCC;text-align: left; margin: 10px"><?=$data->item_name?></td>
      <td style="border: 1px solid #CCC;text-align: center"><?=$data->unit_name?></td>
      <td style="border: 1px solid #CCC;text-align: center"><?=$data->total_unit?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=number_format($data->unit_price,2)?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=number_format($data->total_price,2)?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=($data->rate_of_SD>0)? $data->rate_of_SD : '0' ?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=($data->amount_of_SD>0)? number_format($data->amount_of_SD,2) : '-' ?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=($data->rate_of_VAT>0)? number_format($data->rate_of_VAT) : '0' ?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=($data->amount_of_VAT>0)? number_format($data->amount_of_VAT,2) : '-' ?></td>
      <td style="border: 1px solid #CCC;text-align: right;"><?=($data->total_including_all>0)? number_format($data->total_including_all,2) : '-' ?></td>
      </tr>
      <?php
      $total_unit=$total_unit+$data->total_unit;
      $total_total_price=$total_total_price+$data->total_price;
      $total_amount_of_SD=$total_amount_of_SD+$data->amount_of_SD;
          $total_amount_of_VAT=$total_amount_of_VAT+$data->amount_of_VAT;
          $total_total_including_all=$total_total_including_all+$data->total_including_all;
      endwhile; ?>
      <tr><th>Total</th><td></td><td></td><th style="border: 1px solid #CCC;text-align: center;"><?=$total_unit?></th>
      <td></td>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_total_price,2)?></th>
          <th style="border: 1px solid #CCC;text-align: right;"></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_amount_of_SD,2)?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=$c?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_amount_of_VAT,2)?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_total_including_all,2)?></th>
      </tr>
    <?php else:
      while($data=mysqli_fetch_object($result)):
        $id=$data->item_id;
        $ab=$data->SD_percentage;$ef=$data->VAT_percentage;
        $cd=$data->total_unit*$data->VAT*$ab;?>
      <tr>
      <td style="border: 1px solid #CCC;text-align: center; margin: 10px"><?=$i=$i+1?></td>
      <td style="border: 1px solid #CCC;text-align: left; margin: 10px"><input type="text" tabindex="-1"  value=""  style="font-size:11px; text-align:left;border: 1px solid #999999;width: 97%" name="challen_no_and_date<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: center"><input type="text" tabindex="-1"  value="<?=$do_master->remarks?>"style="font-size:11px; text-align:left;border: 1px solid #999999;width: 97%"  name="remarks_of_debit_note<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: center"><input type="text"                value=""  style="font-size:11px; text-align:center;" name="price4<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value=""  style="font-size:11px; text-align:right;border: 1px solid #999999;" name="qty5<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value=""  style="font-size:11px; text-align:right;border: 1px solid #999999;" name="qty6<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value=""  style="font-size:11px; text-align:right;border: 1px solid #999999;" name="qty7<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value="<?=$data->amount?>" style="font-size:11px; text-align:right;border: 1px solid #999999;" name="price8<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value="<?=$data->qty?>" style="font-size:11px; text-align:right;border: 1px solid #999999;" name="qty9<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value="<?=($data->amount/100)*15?>" style="font-size:11px; text-align:right;border: 1px solid #999999;" name="qty10<?=$id?>"></td>
      <td style="border: 1px solid #CCC;text-align: right;"><input type="text" tabindex="-1"  value=""  style="font-size:11px; text-align:right;border: 1px solid #999999;" name="total_including_all<?=$id?>" id="qty11<?=$id?>"></td>
      </tr>

      <?php
      $total_unit=$total_unit+$data->total_unit;
      $total_unit_amounts=$total_unit_amounts+$total_unit_amount;
      $total_VATs=$total_VATs+$total_VAT;
          $actual_VATs=$actual_VATs+$actual_VAT;
          $total_SD_amount=$total_SD_amount+$data->amount_of_SD;
      endwhile;
          if($status>0): ?>
      <tr><th>Total</th><td></td><td></td><th style="border: 1px solid #CCC;text-align: center;"><?=$total_unit?></th>
      <td></td>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_unit_amounts,2)?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=$a?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=$b?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=$c?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($total_VATs,2)?></th>
          <th style="border: 1px solid #CCC;text-align: right;"><?=number_format($actual_VATs,2)?></th>
      </tr>
          <?php endif; ?>
    <?php endif; ?>
    </tbody>
</table>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>প্রতিষ্ঠান কতৃপক্ষের দায়িত্বপ্রাপ্ত ব্যক্তির নাম : <?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$warehouse_master->VAT_responsible_person);?></div>
<div>পদবী : Depot Incharge</div>
<div>স্বাক্ষর :</div>
<div>সিল :</div>
<div style="border-bottom: 1px solid black; width: 28%; margin-top: 30px;border-collapse: collapse;"></div>
<div style="width:28%;">
    <span style="">সকল প্রকার কর ব্যতীত মূল্য :</span>
    <span style="margin-left: 50px"><?=number_format($total_unit_amounts,2)?></span>
</div>

        <?php
        $GET_status=find_a_field('purchase_return_master','mushak_challan_status','d='.$_GET[$unique]);
            if($status>0){?><h3 style="text-align: center;color: red;  font-weight: bold"><i>Mushak challan has been recorded & forwarded to the relevant warehouse!!</i></h3>
          <?php } else { if ($GET_status=='UNRECORDED'){?><h1 align="center">
                <input type="submit" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you confirm to Record & Create?");' name="record" value="Record & Create VAT Challan">
                <input type="submit" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you confirm to Skip");' name="skipandforward" value="Skip & Forward">
                <?php } ?>
                </h1>
<?php } ?>
        </form>
</body>
</html>
