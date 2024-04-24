<?php
require_once 'support_file.php';
$proj_id	= @$_SESSION['proj_id'];
$vdate		= @$_REQUEST['vdate'];
$jv_no =  @$_REQUEST['v_no'];
$cheq_no = @$_POST["cheq_no"];
$cheq_date = strtotime(@$_POST["cheq_date"]);
$vdate = strtotime(@$_POST["vdate"]);
$page = '';
$user_id = @$_SESSION['userid'];


if(isset($_POST['narr']))
{$count = $_POST["count"];
$sql2="select a.id,a.tr_id,a.tr_from,a.tr_no from secondary_journal a where  a.jv_no='$jv_no' and 1";
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
$sqldate2 = "UPDATE secondary_journal SET jvdate='".$_POST['vdate']."',cheq_no='$cheq_no',cheq_date='$cheq_date',ledger_id='".$ledger."',narration='$narration',dr_amt='$dr_amt',cr_amt='$cr_amt' WHERE id = ".$datas[0];
if(isset($sqldate1))@mysqli_query($conn, $sqldate1);
@mysqli_query($conn, $sqldate2);
echo '<script type="text/javascript">window.opener.location.reload(true);window.close();</script>';
	}
    $masterDate = find_all_field('secondary_journal','','jv_no='.$jv_no.'');
$create_date = date('Y-m-d');
if (isset($_POST['add_ledger']) && ($_POST['add_dr_amt'] || $_POST['add_cr_amt'])) {
    $insert_adding = mysqli_query($conn, "INSERT INTO secondary_journal (jv_no,jvdate,ledger_id,narration,dr_amt,cr_amt,tr_from,tr_no,cc_code,user_id,tr_id,grn_inventory_type,section_id,company_id,checked,create_date) 
VALUES ('" . $jv_no . "','" . $masterDate->jvdate . "','" . $_POST['add_ledger'] . "','" . $_POST['add_narration'] . "','" . $_POST['add_dr_amt'] . "','" . $_POST['add_cr_amt'] . "','" . $masterDate->tr_from . "','" . $masterDate->tr_no . "','','" . $user_id . "','" . $masterDate->tr_id . "','" . $masterDate->grn_inventory_type . "','" . $masterDate->section_id . "','" . $masterDate->company_id . "','" . $masterDate->checked . "','" . $create_date . "')");
}
}
if(isset($_REQUEST['view']) && $_REQUEST['view']=='Show')
{
	$sql1="select narration,cheq_no,cheq_date,' ',jvdate from secondary_journal where jv_no='$jv_no' limit 1";
	$data1=mysqli_fetch_row(mysqli_query($conn, $sql1));
	$sql1."<br>";
?>
<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content_without_menu.php'; ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <form action="" method="post" name="form2">
                <table class="table table-striped table-bordered" style="font-size: 11px">
                    <tr>
                        <th style="vertical-align: middle">Voucher  No</th>
                        <th style="width: 1%; vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$jv_no;?>&nbsp;</td>
                        <th style="vertical-align: middle">Voucher Date</th>
                        <th style="width: 1%; vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><input name="vdate" id="vdate" type="date" style="font-size: 11px" value="<?=$data1[4];?>" class="form-control" /></td>

                    </tr>
                </table>

                <table class="table table-striped table-bordered" style="font-size: 11px">
                    <tr align="center">
                        <th style="width: 1%; text-align: center">#</th>
                        <th>A/C Ledger</th>
                        <th>Narration</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>

                    <?php
                    $pi=0;
                    $d_total=0;
                    $c_total=0;
                    $sql2="select a.dr_amt,a.cr_amt,b.ledger_name,b.ledger_id,a.narration,a.id from accounts_ledger b, secondary_journal a where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jv_no='$jv_no' and 1";
                    $data2=mysqli_query($conn, $sql2);
                    while($info=mysqli_fetch_row($data2)){ $pi++;
                        if($info[0]==0) $type='Credit';
                        else $type='Debit';
                        $d_total=$d_total+$info[0];
                        $c_total=$c_total+$info[1];
                        $ids = $info[5];
                        if(isset($_POST['delSingleItem'.$ids]))
                        {
                            mysqli_query($conn, "Update secondary_journal set visible_status='0' where id=".$ids."");
                        }
                        ?>

                    <tr>
                        <td style="text-align: center; vertical-align: middle">
                            <button type="submit" name="delSingleItem<?=$info[5]?>" style="background-color: transparent; border: none" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you confirm?");'><?=$pi;?></button>
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            <select class="select2_single form-control" style="width:98%; font-size: 11px;text-align: left; margin: 10px" tabindex="-1" required="required"  name="ledger_<?=$info[5]?>" id="ledger_<?=$info[5]?>">
                                <option></option>
                                <?=foreign_relation('accounts_ledger', 'ledger_id', 'concat(ledger_id, " : " ,ledger_name)', $info[3], 'status=1 and show_in_transaction="1"'); ?>
                            </select>
                        </td>

                        <td style="text-align: center; vertical-align: middle">
                            <textarea type="text" name="narration_<?=$info[5];?>" id="narration_<?=$info[5];?>" style="width: 95%; font-size: 11px" class="form-control"><?=$info[4];?></textarea>
                            <input type="hidden" name="l_<?=$pi;?>" id="l_<?=$pi;?>" value="<?=$info[3];?>" />
                        </td>
                        <td style="text-align: center;vertical-align: middle; width: 10%"><input name="dr_amt_<?=$info[5];?>" type="text" id="dr_amt_<?=$info[5];?>" value="<?=$info[0]?>" style="width:100px;font-size:11px; text-align: right"  class="form-control col-md-6 col-xs-12" /></td>
                        <td style="text-align: center;vertical-align: middle; width: 10%"><input name="cr_amt_<?=$info[5];?>" type="text" id="cr_amt_<?=$info[5];?>" value="<?=$info[1]?>" style="width:100px;font-size:11px; text-align: right" class="form-control col-md-6 col-xs-12" /></td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="5" style="height: 10px;vertical-align: middle "><span class="required text-danger"> <i class="fa fa-plus"></i> Extra Column for Adding *</span></td>
                    </tr>

                    <tr>
                        <td style="text-align: center; vertical-align: middle"><?=$pi+1;?>&nbsp;</td>
                        <td style="text-align: center; vertical-align: middle">
                            <select class="select2_single form-control" style="width:98%; font-size: 11px;text-align: left; margin: 10px" tabindex="-1"   name="add_ledger">
                                <option></option>
                                <?=foreign_relation('accounts_ledger', 'ledger_id', 'concat(ledger_id, " : " ,ledger_name)','', 'status=1 and show_in_transaction="1"'); ?>
                            </select>
                        </td>


                        <td style="text-align: center; vertical-align: middle">
                            <textarea type="text" name="add_narration" style="width: 95%; font-size: 11px" class="form-control"></textarea>
                        </td>
                        <td style="text-align: center; vertical-align: middle"><input name="add_dr_amt" type="text" style=" width: 100px; font-size:11px; text-align: right" class="form-control col-md-6 col-xs-12" /></td>
                        <td style="text-align: center; vertical-align: middle"><input name="add_cr_amt" type="text" style=" width: 100px; font-size:11px; text-align: right" class="form-control col-md-6 col-xs-12" /></td>
                    </tr>


                    <tr>
                        <th colspan="3" style="text-align: right">Total Amount :</th>
                        <th style="text-align: right"><?=number_format($d_total,2);?>&nbsp;</th>
                        <th style="text-align: right"><?=number_format($c_total,2);?>&nbsp;</th>
                    </tr>
                </table>

    <a href="javascript:window.open('','_self').close();" style="font-size: 12px; float: left" class="btn btn-danger"> <i class="fa fa-close"></i> Close</a>
    <?php //if (number_format($d_total,2)==number_format($c_total,2)){ ?>
    <button type="submit" class="btn btn-primary" name="narr" style="font-size: 12px; float: right" onmouseover="this.style.cursor='pointer';">Update  <i class="fa fa-edit"></i></button>
        <?php //} else { echo '<h5 style="font-weight: bold; color: red; text-align: right">This voucher is invalid!!</h5>';} ?>


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