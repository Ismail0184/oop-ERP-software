<?php
require_once 'support_file.php';
$title=$_GET['journal_type'].' Vouchers';
$page='voucher_print1.php';
$unique='vo_no';

$date_checking = find_a_field('dev_software_data_locked','id','status="LOCKED" and section_id="'.$_SESSION['sectionid'].'" and company_id="'.$_SESSION['companyid'].'"');
if($date_checking>0) {
    $lockedStartInterval = @$date_checking->start_date;
    $lockedEndInterval = @$date_checking->end_date;
} else
{
    $lockedStartInterval = '';
    $lockedEndInterval = '';
}

if(isset($_POST['viewReport'])){
    if(($_POST['checked']==''))
    {
        $statusConn .= " AND j.checked = '".$_POST['checked']."'";
    } else {
        $statusConn .= " and 1 ";
    }
	$sql = "select j.jv_no,a.voucherno,a.voucher_date,a.paid_to,FORMAT((a.amount),2) as amount
from  journal_voucher_master a, journal j where  j.tr_no=a.voucherno and a.voucher_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' and a.journal_type='Receipt' and a.entry_by='".$_SESSION['userid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."') and a.company_id='".$_SESSION['companyid']."' group by j.tr_no  order by a.id desc";

} else {

    $sql = "select j.jv_no,a.voucherno,a.voucher_date,a.paid_to,FORMAT((a.amount),2) as amount
from  journal_voucher_master a, journal j where  j.tr_no=a.voucherno and a.journal_type='Receipt' and a.entry_by='".$_SESSION['userid']."' and a.section_id in ('400000','".$_SESSION['sectionid']."') and a.company_id='".$_SESSION['companyid']."' group by j.tr_no  order by a.id desc limit 10";

}
?>

<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
    function DoNavPOPUP(lk)
    {myWindow = window.open("<?=$page?>?v_type=receipt&<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=600,left = 250,top = -1");}
</script>

<?php require_once 'body_content_nva_sm.php'; ?>


<div class="col-md-12 col-xs-12">
    <div class="<?php if(isset($_POST['viewReport'])){ ?> row <?php } else { echo 'row collapse';} ?>" id="experience2">
        <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
            <table align="center" style="width: 50%;">
                <tr><td><input type="date"  style="width:150px; font-size: 11px; height: 25px" max="<?=date('Y-m-d');?>"  value="<?=(@$_POST['f_date']!='')? $_POST['f_date'] : date('Y-m-01') ?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                    <td style="width:10px; text-align:center"> -</td>
                    <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?=(@$_POST['t_date']!='')? $_POST['t_date'] : date('Y-m-d') ?>" required  max="<?=date('Y-m-d');?>" name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                    <td style="width:10px; text-align:center"> -</td>
                    <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary">View <?=$_GET['journal_type']?> Vouchers</button></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?=$crud->report_templates_with_status_with_filtering($sql,$title,'');?>
<?=$html->footer_content();?>