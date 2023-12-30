<?php
require_once 'support_file.php';
$proj_id	= @$_SESSION['proj_id'];
$vdate		= @$_REQUEST['vdate'];
$jv_no =  @$_REQUEST['v_no'];
$cheq_no = @$_POST["cheq_no"];
$cheq_date = strtotime(@$_POST["cheq_date"]);
$vdate = strtotime(@$_POST["vdate"]);
$page = '';


if(isset($_POST['narr']))
{$count = $_POST["count"];
$sql2="select a.id,a.tr_id from secondary_journal a where  a.jv_no='$jv_no' and 1";
$data2=mysqli_query($conn, $sql2);
while($datas=mysqli_fetch_row($data2)){
$ledger_old=$_POST['ledger_'.$datas[0]];
$ledger_new = $ledger_old;
$ledger = $_POST['ledger_'.$datas[0]];
$c_no=$_POST['c_no'];
$c_date=$_POST['c_date'];
$narration=$_POST['narration_'.$datas[0]];
$dr_amt=$_POST['dr_amt_'.$datas[0]];
$cr_amt=$_POST['cr_amt_'.$datas[0]];
$sqldate2 = "UPDATE secondary_journal SET jv_date='$vdate',cheq_no='$cheq_no',cheq_date='$cheq_date',ledger_id='".$ledger."',narration='$narration',dr_amt='$dr_amt',cr_amt='$cr_amt' WHERE id = ".$datas[0];
if(isset($sqldate1))@mysqli_query($conn, $sqldate1);
@mysqli_query($conn, $sqldate2);
echo '<script type="text/javascript">window.opener.location.reload(true);window.close();</script>';
	}}

if(isset($_REQUEST['view']) && $_REQUEST['view']=='Show')
{
	$sql1="select narration,cheq_no,cheq_date,' ',jv_date from secondary_journal where jv_no='$jv_no' limit 1";
	$data1=mysqli_fetch_row(mysqli_query($conn, $sql1));
	$sql1."<br>";
?>
<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content_without_menu.php'; ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <form action="" method="post" name="form2">
                <table width="99%" border="1" align="center" style="border-collapse:collapse;" bordercolor="#c1dad7" id="vbig">
        <tr>
          <td>
		  <table width="100%" border="0" align="center" bordercolor="#0099FF" bgcolor="#D9EFFF" cellspacing="0">
              <tr>
                <td width="12%" height="20" align="right">Received From:</td>
                <td width="22%" align="left"><?=$data1[1];?>&nbsp;</td>
                <td width="9%" align="right" valign="top">Purpose:</td>
                <td width="28%" align="left" valign="top"><?=$data1[0];?>&nbsp;</td>
                <td align="right">Voucher Date:</td>
                <td align="left"><input name="vdate" id="vdate" type="text" value="<?=date("d-m-Y",$data1[4]);?>" /></td>
              </tr>
              <tr>
                <td height="20" align="right">&nbsp;</td>
                <td height="20" align="left">&nbsp;</td>
                <td width="9%" align="right" valign="top">&nbsp;</td>
                <td width="28%" align="left" valign="top">&nbsp;</td>
                <td width="12%" align="right">Voucher  No:</td>
                <td width="17%" align="left"><?=$jv_no;?>&nbsp;</td>
              </tr>
          </table>
		  </td>
        </tr>
        <tr>
          <td valign="top"><table width='100%' border="1" bordercolor="#c1dad7" bgcolor="#FFFFFF" style="border-collapse:collapse">
              <tr align="center">
                <td>S/L</td>
                <td>A/C Ledger</td>
                <td>Narration</td>
                <td>Debit</td>
                <td>Credit</td>
              </tr>
<?php
$pi=0;
$d_total=0;
$c_total=0;
$sql2="select a.dr_amt,a.cr_amt,b.ledger_name,b.ledger_id,a.narration,a.id from accounts_ledger b, secondary_journal a where a.ledger_id=b.ledger_id and a.jv_no='$jv_no' and 1";
$data2=mysqli_query($conn, $sql2);
while($info=mysqli_fetch_row($data2)){ $pi++;
if($info[0]==0) $type='Credit';
			  else $type='Debit';
			  $d_total=$d_total+$info[0];
			  $c_total=$c_total+$info[1];
			  ?>
              <tr>
                <td><?=$pi;?>&nbsp;</td>
                <td>
                    <select class="select2_single form-control" style="width:100%; font-size: 11px;text-align: left; margin: 10px" tabindex="-1" required="required"  name="ledger_<?=$info[5]?>" id="ledger_<?=$info[5]?>">
                      <option></option>
                      <?=foreign_relation('accounts_ledger', 'ledger_id', 'ledger_name', $info[3], 'status=1'); ?>
                  </select>
                </td>
                  <td><textarea type="text" name="narration_<?=$info[5];?>" id="narration_<?=$info[5];?>" style="width: 99%"><?=$info[4];?></textarea>
                <input type="hidden" name="l_<?=$pi;?>" id="l_<?=$pi;?>" value="<?=$info[3];?>" />
                </td>
                <td><input name="dr_amt_<?=$info[5];?>" type="text" id="dr_amt_<?=$info[5];?>" value="<?=$info[0]?>" style="width:80px;" /></td>
                <td><input name="cr_amt_<?=$info[5];?>" type="text" id="cr_amt_<?=$info[5];?>" value="<?=$info[1]?>" style="width:80px;" /></td>
              </tr>
			   <?php }?>
              <tr>
                <td colspan="3" align="right">Total Amount :</td>
                <td><?=$d_total;?>&nbsp;</td>
                <td><?=$c_total;?>&nbsp;</td>
              </tr>
          </table></td>
        </tr>
      </table>

      <br />

<div align="center" style="margin-top:10px;">
<table border="0" cellspacing="10" cellpadding="0" align="center" style="width:400px;">
  <tr>
    <td><input name="narr" type="submit" class="btn btn-primary" value="Edit Voucher" onmouseover="this.style.cursor='pointer';" /></td>
    <td>&nbsp;</td>
    <td><div class="btn_p">

    </div></td>
  </tr>
</table>
</div>
<?php } ?>
<script type="application/javascript">
function loadinparent(url)
{   self.opener.location = url;
	self.blur();
}
</script>
<input name="count" id="count" type="hidden" value="<?=$pi;?>" />
    </form>
    </div>
    </div>
    </div>
<?=$html->footer_content();mysqli_close($conn);?>