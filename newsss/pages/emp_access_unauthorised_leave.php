<?php
require_once 'support_file.php';
$title="Leave Application for Authorized";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$unique='id';
$$unique = @$_GET[$unique];
$unique_field='PBI_IN_CHARGE';
$table="hrm_leave_info";
$now = date("Y-m-d h:i:sa");

$current_status=find_a_field("".$table."","approved_status","".$unique."=".$$unique."");
$required_status="PENDING";
$authorused_status="APPROVED";
$page="emp_access_unauthorised_leave.php";
$crud      =new crud($table);
$LeaveMasterData=find_all_field(''.$table.'','',''.$unique.'='.$$unique);

if (empty($LeaveMasterData->approved_viewed_at))
{
    mysqli_query($conn, "UPDATE ".$table." SET approved_viewed_at='".$now."' WHERE ".$unique."=".$$unique);
}


if(isset($_POST['confirm']))
{
    $_POST['status']="APPROVED";
    $_POST['approved_status']="APPROVED";
    $_POST['approved_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['reject']))
{
    $_POST['status']="REJECTED";
    $_POST['approved_status']="REJECTED";
    $_POST['approved_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}




// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}


if(isset($_POST['viewReport'])){

    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.PBI_ID and p2.PBI_DESIGNATION=de.DESG_ID and
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Application_By,r.s_date as Start_date,r.e_date as End_date,r.total_days,r.reason, r.approved_status as status
     		  from '.$table.' r
     		  WHERE 
     		  r.approved_by="'.$_SESSION['PBI_ID'].'"	and 
     		  r.approved_status ="APPROVED" and
     		  r.s_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and r.e_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and 
     		  r.half_or_full in ("Full")
     		  order by r.'.$unique.' DESC';
} else {

    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.PBI_ID and p2.PBI_DESIGNATION=de.DESG_ID and 
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Application_By,r.s_date as Start_date,r.e_date as End_date,r.total_days,r.reason,r.approved_status as status
     from '.$table.' r
     WHERE
     r.status = "RECOMMENDED" and
     r.approved_by="'.$_SESSION['PBI_ID'].'"	and 
     r.approved_status="'.$required_status.'" and
     r.half_or_full in ("Full")
     order by r.'.$unique.' DESC';
} ?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {
            myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=900,left = 250,top = -1");
        }
    </script>

<?php if(isset($_GET[$unique])):
    require_once 'body_content_without_menu.php';
else :
    require_once 'body_content.php'; endif;
?>

<?php if(!isset($_GET[$unique])){ ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
        <table align="center" style="width: 50%;">
            <tr><td>
                    <input type="date" style="width:150px; font-size: 11px; height: 25px"  value="<?php if(isset($_POST['f_date'])) echo $_POST['f_date']; else echo date('Y-m-01');?>" max="<?=date('Y-m-d');?>" required   name="f_date" ></td>
                <td style="width:10px; text-align:center"> -</td>
                <td><input type="date" style="width:150px;font-size: 11px; height: 25px"  value="<?php if(isset($_POST['t_date'])) { echo $_POST['t_date']; } else { echo date('Y-m-d'); }?>" max="<?=date('Y-m-d')?>" required   name="t_date"></td>
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary">View Leave</button></td>
            </tr>
        </table>

        <?=$crud->report_templates_with_status_employee_dashboard($res,$link)?>
    </form>
<?php } ?>




<?php if(isset($_GET[$unique])){ ?>
    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
        <? require_once 'support_html.php';?>

        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px;">
            <thead>
            <tr class="<?php if ($LeaveMasterData->responsible_person_acceptance_status!=='ACCEPTED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Responsible Person Acceptance Status</th>
            </tr>
            </thead>
            <tr>
                <th style="width: 10%; vertical-align: middle">Person</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$LeaveMasterData->leave_responsibility_name);?></td>
                <th style="width: 10%; vertical-align: middle">View Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->responsible_person_viewed_at)) { ?>
                        <span class="label label-success" style="font-size:10px">Viewed</span>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Viewed At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->responsible_person_viewed_at)) { ?>
                        <?=$LeaveMasterData->responsible_person_viewed_at;?>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th style="width: 10%; vertical-align: middle">Acceptance Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->responsible_person_acceptance_status)=='REJECTED') { ?>
                        <span class="label label-danger" style="font-size:10px">Rejected</span>
                    <?php } elseif (($LeaveMasterData->responsible_person_acceptance_status)=='PENDING') { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } else { ?>
                        <span class="label label-success" style="font-size:10px">Accepted</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Acceptance At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->responsible_person_acceptance_status)=='PENDING') { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } else { ?>
                        <?=$LeaveMasterData->responsible_person_acceptance_at;?>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Remarks</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=$LeaveMasterData->remarks_for_responsible_person;?></td>
            </tr>
        </table>
        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px;">
            <thead>
            <tr class="<?php if ($LeaveMasterData->recommended_status!=='RECOMMENDED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Recommendation Status</th>
            </tr>
            </thead>

            <tr>
                <th style="width: 10%; vertical-align: middle">Person</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$LeaveMasterData->recommended_by);?></td>
                <th style="width: 10%; vertical-align: middle">View Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->recommended_viewed_at)) { ?>
                        <span class="label label-success" style="font-size:10px">Viewed</span>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Viewed At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->recommended_viewed_at)) { ?>
                        <?=$LeaveMasterData->recommended_viewed_at;?>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th style="width: 10%; vertical-align: middle">Acceptance Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->recommended_status)=='REJECTED') { ?>
                        <span class="label label-danger" style="font-size:10px">Rejected</span>
                    <?php } elseif (($LeaveMasterData->recommended_status)=='PENDING') { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } else { ?>
                        <span class="label label-success" style="font-size:10px">RECOMMENDED</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Acceptance At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->recommended_status)!=='RECOMMENDED') { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } else { ?>
                        <?=$LeaveMasterData->recommended_at;?>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Remarks</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=$LeaveMasterData->remarks_while_recommended;?></td>
            </tr>
        </table>

        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px">
            <thead>
            <tr class="bg-primary text-white">
                <th colspan="8" style="text-align: center; font-size: 13px; font-weight: bold;">Leave Policy & Current Year Status</th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th rowspan="2" style="width: 2%; vertical-align:middle">Leave</th>
                <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                while($leave_row=mysqli_fetch_object($res)){
                    ?>
                    <th style="text-align: center; vertical-align:middle"><?=$leave_row->leave_type_name;?></th>
                <?php } ?>
                <th style="text-align: center; vertical-align:middle">Total</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Policy</td>
                <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                $totalPolicy = 0;
                while($leave_row=mysqli_fetch_object($res)){ ?>
                    <td style="text-align: center"><?=$leave_row->yearly_leave_days;?></td>
                    <?php
                    $totalPolicy=$totalPolicy+$leave_row->yearly_leave_days;
                } ?>
                <td style="text-align: center"><?=$totalPolicy;?></td>
            </tr>

            <tr>
                <td>Taken</td>
                <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                $total_taken = 0;
                while($leave_row=mysqli_fetch_object($res)){ ?>
                    <td style="text-align: center"><?php $leave_taken=find_a_field("".$table."","SUM(total_days)","type='".$leave_row->id."' and s_date between '$dfrom' and '$dto' and PBI_ID='".$LeaveMasterData->PBI_ID."'"); if($leave_taken>0){ echo number_format($leave_taken),'';} else echo ''; ?></td>
                    <?php
                    $total_taken=$total_taken+$leave_taken;
                } ?>
                <td style="text-align: center"><?=$total_taken;?></td>
            </tr>
            </tbody>

            <tr>
                <th>Balance</th>
                <?php
                $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                while($leave_row=mysqli_fetch_object($res)){
                    $balance=$leave_row->yearly_leave_days - find_a_field("".$table."","SUM(total_days)","type='".$leave_row->id."' and s_date between '$dfrom' and '$dto' and PBI_ID='".$LeaveMasterData->PBI_ID."'");?>
                    <th class="<?php if($balance==0){?> bg-danger <?php } ?>" style="text-align: center"><?=$balance?></th>
                <?php } ?>
                <th style="text-align: center"><?=$totalPolicy-$total_taken;?></th>
            </tr>
            </tbody>
        </table>

        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px; margin-top: 5px">
            <thead>
            <tr class="bg-danger text-white">
                <th colspan="7" style="text-align: center; font-size: 13px; font-weight: bold;">Leave Request</th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th style="text-align: center; vertical-align:middle; width: 5%">Type</th>
                <th style="text-align: center; vertical-align:middle; width: 20%">Leave Duration</th>
                <th style="text-align: center; vertical-align:middle; width: 5%">Days</th>
                <th style="text-align: center; vertical-align:middle; width: 15%">Reason</th>
                <th style="text-align: center; vertical-align:middle">Remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="text-align: center; vertical-align:middle"><?=find_a_field("hrm_leave_type","leave_type_name","id=".$LeaveMasterData->type."");?></td>
                <td style="text-align: center; vertical-align: middle"><?php if($$unique>0){ echo $LeaveMasterData->s_date; } else { echo ''; } ?> - <?php if($$unique>0){ echo $LeaveMasterData->e_date; } else { echo ''; } ?></td>
                <td style="text-align: center; vertical-align: middle"><?=number_format($LeaveMasterData->total_days);?></td>
                <td style="text-align: center; vertical-align:middle"><?=$LeaveMasterData->reason;?></td>
                <td style="text-align: center; vertical-align:middle"><input type="text" class="form-control col-md-7 col-xs-12" placeholder="Enter a note for the application, if necessary" name="remarks_while_approved" style="width: 99%; font-size: 11px"></td>
            </tr>
            </tbody>
        </table>
        <?php if($current_status!=$required_status && $current_status!="MANUAL" && $current_status!="RETURNED"){ echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been Approved!!</i></h6>';} else { ?>
            <table align="center" style="width:90%;font-size:12px;">
                <tr>
                    <td>
                        <button type="submit" style="font-size:12px; float:left" onclick='return window.confirm("Are you confirm to reject the Application?");' name="reject" class="btn btn-danger"><i class="fa fa-ban"></i> Reject & Back</button>
                        <button type="submit" style="font-size:12px; float:right" onclick='return window.confirm("Are you confirm to Recommend the Application?");' name="confirm" id="confirm" class="btn btn-success">I Approved <i class="fa fa-check"></i></button>
                    </td>
                </tr>
            </table>
        <?php } ?>
        <?php } ?>
    </form>
<?=$html->footer_content();?>