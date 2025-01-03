
<style>/* CSS Document */
    body
    {
        background-color: #ffffff;
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:11px;
        line-height:20px;
        margin-top:20px;
        color:#000000;
        text-align:none;
        text-decoration:none;
    }
    .header
    {
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:11px;
        color:#000000;
        text-decoration:none;
        line-height:25px;
    }

    .tabledesign {
        width:820px;
        border-color:000000;
        border-collapse:collapse;
        font-size:14px;
        text-align:center;
    }
    .tabledesign td{
        padding:3px;
        border:solid 1px;
        border-color:000000;

    }
    .tabledesign1 {
        width:860px;
        border-color:000000;
        border-collapse:collapse;
        font-size:14px;
        text-align:center;
        line-height:12px;
    }
    .tabledesign1 td{
        border:solid 0px;
        border-color:000000;

    }

    .title{
        font-weight:bold;
        font-size:25px;
        line-height:30px;
    }
    .logo{
        width:300px;
        height:50px;
    }
    .debit_box{
        width:380px;
        height:32px;
        font-weight:bold;
        font-size:25px;
        margin: 0px auto -1px auto;
    }
    .line{
        background:url(../images/line.jpg) repeat-x;
        height:2px;
        line-height:0px;}

    .tabledesign_text{
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:14px;
        line-height:20px;
        margin-top:20px;
        color:#000000;
        text-align:none;
        text-decoration:none;
    }
</style><?php
require_once 'support_file.php';
require_once 'class.numbertoword.php';
$title='Voucher View';
$proj_id=@$_SESSION['proj_id'];
$vtype= strtolower($_REQUEST['v_type']);
if($vtype=='receipt'){$voucher_name='RECEIPT VOUCHER';$vtypes='receipt';}
elseif($vtype=='purchase'){$voucher_name='PURCHASE VOUCHER';$vtypes='secondary_journal';}
elseif($vtype=='payment'){$voucher_name='PAYMENT VOUCHER';$vtypes='payment';}
elseif($vtype=='payment_bank'){$voucher_name='CHEQUE PAYMENT';$vtypes='secondary_payment';}
elseif($vtype=='journal_info'){$voucher_name='JOURNAL VOUCHER';$vtypes='journal_info';}
elseif($vtype=='contra'){$voucher_name='CONTRA VOUCHER';$vtype='coutra';$vtypes='contra';}
else{$vtype==$_REQUEST['v_type'];$voucher_name=$_REQUEST['v_type'];$vtypes=$_REQUEST['v_type'];}



$no=$vtype."_no";
$vdate=$vtype."_date";

$address=getSVALUE('project_info','proj_address',"where 1");
if(isset($_REQUEST['vo_no']))
{ if($vtype=='purchase'){
    $vo_no = getSVALUE('journal','jv_no','where jv_no='.$_REQUEST['vo_no'].' and tr_from = "Purchase"');
    $sql1="select jvdate,cc_code,user_id,time from journal where jv_no=$vo_no and tr_from = 'Purchase' limit 1";
}elseif ($vtype=='payment_bank')
{   $vo_no = $_REQUEST['vo_no'];
    $sql1="select paymentdate,cc_code,entry_by,time,cheque_id,maturity_date,received_from from secondary_payment where payment_no=$vo_no  limit 1";
} else {
    $vo_no = getSVALUE('journal','tr_no','where jv_no='.$_REQUEST['vo_no'].' and tr_from = "'.ucwords($vtypes).'"');
    $sql1="select jvdate,cc_code,user_id,time from journal where tr_no=$vo_no and tr_from = '".$vtypes."' limit 1";
}

$data1=mysqli_fetch_row(mysqli_query($conn, $sql1));
$user_name = getSVALUE('users','fname',"where user_id=".$data1[2]);
$vo_date=find_a_field('journal','jvdate','tr_no='.$vo_no);
$cccode=$data1[1];}
$pi=0;
$cr_amt=0;
$dr_amt=0;
if($_SESSION['usergroup']==3)
$sql2="SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, journal b where
b.jv_no='".$_GET['vo_no']."' and tr_from='".$_GET['v_type']."' and a.ledger_id=b.ledger_id order by b.id";
else
if ($vtype=='payment_bank') {
    $sql2 = "SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, secondary_payment b where b.payment_no='".$_GET['vo_no']."' and a.ledger_id=b.ledger_id order by b.id";
}
else {
    $sql2 = "SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, journal b where b.jv_no='".$_GET['vo_no']."' and tr_from='".$_GET['v_type']."' and a.ledger_id=b.ledger_id order by b.id";
}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Voucher :.</title>
<link href="../css/voucher_print.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript">
function hide()
{
    document.getElementById("pr").style.display="none";
}
</script></head>
<body style="height: auto">
<table width="820" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td><div class="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="1%">
			<? $path=1;
			if(is_file($path)) echo '<img src="'.$path.'" height="80" />';?>			</td>
            <td width="83%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" class="title"><?=$_SESSION['company_name'];?></td>
              </tr>
              <tr>
                <td align="center"><?=$_SESSION['company_address'];?></td>
              </tr>
              <tr>
                <td align="center"><table  class="debit_box" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td>&nbsp;</td>
                      <td width="325"><div align="center"><?=$voucher_name?></div></td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
	    </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
    </table>
    </div></td>
  </tr>
  <tr><td></td>
  </tr>
  <tr>



<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" class="tabledesign_text">
<div id="pr">
<div align="left">
<input name="button" type="button" onClick="hide();window.print();" value="Print" />
<a href="voucher_print_view.php?v_type=<?=$_REQUEST['v_type']?>&vo_no=<?=$_REQUEST['vo_no']?>">Client Copy</a></div>
</div>
<?
$attachment = '../'.$vtype.'_attch/'.$vo_no.'.jpeg';
if(is_file($attachment)){?>
<div align="right">
<a href="../<?=$vtype?>_attch/<?=$vo_no?>.jpeg" target="_blank">View Attachment</a></div>
</div>
<? }?>
</td></tr>
     <?php if ($vtype=='payment_bank'): ?>
      <tr>
        <td class="tabledesign_text"> Cheque No  : <?=find_a_field('Cheque_Book','Cheque_number','id='.$data1[4]);?></td>
        <td class="tabledesign_text"> Cheque Rcvd. By  : <?=$data1[6];?></td>
      </tr><tr>
        <td class="tabledesign_text">Issued Date : <?=$data1[0];?></td>
        <td class="tabledesign_text">Maturity Date : <?=$data1[5];?></td>
      </tr>
            <?php  else:  ?>
                <tr>
                    <td class="tabledesign_text"> Voucher No  : <?php if ($_GET['v_type']=='Loan'): ?><?=$_GET['vo_no']?> <?php else: ?> <?=$vo_no?>  <?php endif;?></td>
                    <td class="tabledesign_text">Voucher Date : <?php if ($_GET['v_type']=='Loan'): ?><?=$_GET['v_date']?> <?php else: ?> <?=$vo_date?> <?php endif;?></td>
                </tr>
            <?php endif; ?>

    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#000000" class="tabledesign">
      <tr bgcolor="#F5F5F5">
          <td width="30%" rowspan="2" align="center">A/C Ledger Head</td>
          <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
        <td width="5%" rowspan="2" align="center">Cost Center</td>
          <?php } ?>
        <td style="width: auto" rowspan="2" align="center">Particulars</td>
        <td colspan="2">Amount (BDT) </td>
      </tr>
      <tr bgcolor="#F5F5F5">
        <td width="10%">Debit </td>
        <td width="10%">Credit</td>
      </tr>

            <?
$data2=mysqli_query($conn, $sql2);
			  while($info=mysqli_fetch_object($data2)){ $pi++;
			  $cr_amt=$cr_amt+$info->cr_amt;
			  $dr_amt=$dr_amt+$info->dr_amt;
              $bankGET = @$info->bank;
			  if($bankGET==''&&$info->cheq_no!='')
			  $narration=$info->narration.':: Cheq # '.$info->cheq_no.'; C.Date= '.$info->cheq_date;
			  elseif($info->cheq_no=='')
			  $narration=$info->narration;
			  else
			  $narration=$info->narration.':: Cheq # '.$info->cheq_no.'; C.Date= '.$info->cheq_date.'; Bank # '.$info->bank;?>

      <tr>
        <td align="left"><?=$info->ledger_name?> : <?=$info->ledger_id?></td>
          <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
          <td align="center"><?=($info->cc_code>0)? find_a_field('cost_center','center_name','id='.$info->cc_code) : 'N/A'; ?></td>
          <?php } ?>
        <td align="left"><?=$narration?></td>
        <td align="right"><?=number_format($info->dr_amt,2)?></td>
        <td align="right"><?=number_format($info->cr_amt,2)?></td>
      </tr>

<?php }?>

      <tr>
          <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
        <td colspan="3" align="right">Total Amount (BDT): </td>
          <?php } else { ?>
              <td colspan="2" align="right">Total Amount (BDT): </td>
          <?php } ?>
        <td align="right"><?=number_format($dr_amt,2)?></td>
        <td align="right"><?=number_format($cr_amt,2)?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
      <td><strong>Amount in Word :</strong> (<?=convertNumberCustom($cr_amt);?>)</td>
  </tr>

  <tr>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td class="tabledesign_text"><table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>



        <td align="center" valign="bottom"><?=$user_name?><br /><span style="font-size:10px">(<?=$data1[3];?>)</span></td>
        <td align="center" valign="bottom">&nbsp;</td>
          <td align="center" valign="bottom">&nbsp;</td>
        <td align="center" valign="bottom">&nbsp;</td>
          <td align="center" valign="bottom">&nbsp;</td>

          <?php if($cr_amt>999999): ?>
        <td align="center" valign="bottom">&nbsp;</td>
          <?php endif; ?>
      </tr>

      <tr>
        <td><div align="center">.................</div></td>
          <td><div align="center">.................</div></td>
        <td><div align="center">.................</div></td>
        <td><div align="center">.................</div></td>
          <td><div align="center">.................</div></td>

          <?php if($cr_amt>999999): ?>
        <td><div align="center">.................</div></td>
          <?php endif; ?>
      </tr>
      <tr>
        <td><div align="center">Prepared by</div></td>
          <td><div align="center">Received by</div></td>
        <td><div align="center">Checked by </div></td>
        <td><div align="center">Accounts</div></td>
          <td><div align="center">HOO</div></td>
          <?php if($cr_amt>999999): ?>
        <td><div align="center">COO</div></td>
          <?php endif; ?>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
