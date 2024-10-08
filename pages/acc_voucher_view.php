<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$title='Voucher View';
$page='acc_voucher_view_popup.php';
$pages = "acc_voucher_view.php";
$unique='v_no';

$proj_id 	= @$_SESSION['proj_id'];
$vtype 		= @$_REQUEST['v_type'];
$fdate=@$_REQUEST["fdate"];
$tdate=@$_REQUEST["tdate"];
$vou_no=@$_REQUEST['vou_no'];
$jv_no=@$_REQUEST['jv_no'];
$tr_from = @$_POST['tr_from'];
$date_checking = find_all_field('dev_software_data_locked','','status="LOCKED" and section_id="'.$_SESSION['sectionid'].'" and company_id="'.$_SESSION['companyid'].'"');
if($date_checking>0) {
    $lockedStartInterval = @$date_checking->start_date;
    $lockedEndInterval = @$date_checking->end_date;
} else
{
    $lockedStartInterval = '';
    $lockedEndInterval = '';
}


if(isset($_REQUEST['show']))
{

	$user_id=@$_REQUEST['user_id'];
	if($user_id!='')
	$user_id = find_a_field('users','user_id',"username='".$user_id."'");

if (!empty($_POST['vou_no'])){
    $sql = "SELECT DISTINCT 
				  j.jv_no,
				  j.jv_no as voucher_no,
                  j.tr_no as Transaction_no,                
				  j.jvdate as date,
				  j.dr_amt,
				  j.cr_amt,				  
				  l.ledger_name,
				  j.tr_from as Voucher_type,
                  u.fname as entry_by,
                  j.entry_at,j.status
				FROM
				  users u,
				  journal j,
				  accounts_ledger l
				WHERE
				  j.tr_no='".$_POST['vou_no']."' and 
				  j.user_id=u.user_id and
				  j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'AND 
				  j.ledger_id = l.ledger_id group BY j.tr_no ";
} elseif (!empty($_POST['jv_no'])) {
    $sql = "SELECT DISTINCT 
				  j.jv_no,
				  j.jv_no as voucher_no,
                  j.tr_no as Transaction_no,                
				  j.jvdate as date,
				  j.dr_amt,
				  j.cr_amt,				  
				  l.ledger_name,
				  j.tr_from as Voucher_type,
                  u.fname as entry_by,
                  j.entry_at,j.status
				FROM
				  users u,
				  journal j,
				  accounts_ledger l
				WHERE
				  j.jv_no='".$_POST['jv_no']."' and 
				  j.user_id=u.user_id and
				  j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'AND 
				  j.ledger_id = l.ledger_id group BY j.jv_no ";

}else {

    $sql = "SELECT DISTINCT 
				  j.jv_no,
				  j.jv_no as Transaction_no,
                  j.tr_no as voucher_no,                
				  j.jvdate as date,
				  j.dr_amt,
				  j.cr_amt,				  
				  l.ledger_name,
				  j.tr_from as Voucher_type,
                  u.fname as entry_by,
                  j.entry_at,j.status
				FROM
				  users u,
				  journal j,
				  accounts_ledger l
				WHERE
				  j.jvdate BETWEEN '" . $_POST['fdate'] . "' AND '" . $_POST['tdate'] . "' and  
				  j.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."'AND 
				  j.ledger_id = l.ledger_id and 
				  j.user_id=u.user_id 
				   group BY j.jv_no ";}}
?>

<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
     function DoNavPOPUP(lk)
     {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=600,left = 230,top = 5");}
 </script>
<?php require_once 'body_content.php'; ?>


<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?><small class="text-danger">field marked with * are mandatory</small></h2>
            <ul class="nav navbar-right panel_toolbox">
                <div class="input-group pull-right">
                </div>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form id="form1" name="form1" method="post" action="" style="font-size: 11px">
                <table align="center" style="width: 100%">
                    <tr>
                        <th style="text-align: center">Date Interval <span class="required text-danger">*</span></th>
                        <th></th>
                        <th>Voucher No</th>
                        <th></th>
                        <th>Transaction No</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <input type="date" id="fdate" style="font-size: 11px; width:49%; float:left"  name="fdate" value="<?=($fdate!='')? $fdate : date('Y-m-01') ?>" max="<?=date('Y-m-d');?>"  class="form-control col-md-7 col-xs-12"> -
                            <input type="date" id="tdate" style="font-size: 11px; width:49%; float:right" value="<?=($tdate!='')? $tdate : date('Y-m-d') ?>" name="tdate"  class="form-control col-md-7 col-xs-12">
                        </td>
                        <td style="width: 1%"></td>

                        <td>
                            <input type="text" id="jv_no" style="font-size: 12px"  value="<?=$jv_no?>" name="jv_no"  class="form-control col-md-7 col-xs-12">
                        </td>

                        <td style="width: 1%"></td>
                        <td>
                            <input type="text" id="vou_no" style="font-size: 12px"  value="<?=$vou_no?>" name="vou_no"  class="form-control col-md-7 col-xs-12">
                        </td>
                        <td style="width: 1%"></td>
                        <td>
                            <div class="form-group">
                                <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                    <button type="submit" style="font-size:12px" class="btn btn-primary" name="show">Search Vouchers</button>
                                    <a href="<?=$pages?>" type="submit" style="font-size:12px" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php if(isset($_REQUEST['view'])||isset($_REQUEST['show'])) : echo $crud->report_templates_with_status($sql,$title);
endif; mysqli_close($conn); ?> 
<?=$html->footer_content();?> 