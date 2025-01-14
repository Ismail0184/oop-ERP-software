<?php
require_once 'support_file.php';
$title="Leave Application for Authorized";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');
$unique='id';
$$unique = @$_GET[$unique];
$now = date("Y-m-d h:i:sa");
$table="hrm_od_attendance";
$current_status=find_a_field("".$table."","status","".$unique."=".$$unique."");
$required_status="APPROVED";
$authorused_status="GRANTED";
$page="hrm_pending_osd_application.php";
$crud      =new crud($table);
$LeaveMasterData=find_all_field(''.$table.'','',''.$unique.'='.$$unique);

if (empty($LeaveMasterData->granted_viewed_at))
{
    mysqli_query($conn, "UPDATE ".$table." SET granted_viewed_at='".$now."' WHERE ".$unique."=".$$unique);
}


if(isset($_POST['confirm']))
{
    $_POST['status']="GRANTED";
    $_POST['granted_status']="GRANTED";
    $_POST['granted_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['reject']))
{
    $_POST['status']="REJECTED";
    $_POST['granted_status']="REJECTED";
    $_POST['granted_at']=date("Y-m-d h:i:sa");
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

    $res='select r.'.$unique.',r.'.$unique.' as AID,r.attendance_date as Date,
    (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM 
    personnel_basic_info p2,
    department d,
	designation de 
	where 
	p2.PBI_ID=r.PBI_ID and
	p2.PBI_DESIGNATION=de.DESG_ID and  							 
	p2.PBI_DEPARTMENT=d.DEPT_ID) as Applicant,
	r.place,
	r.late_reason as purpose,r.status as status
	
	from '.$table.' r
	
	WHERE  
	r.attendance_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"
	order by r.'.$unique.' DESC';

} else {

    $res='select r.'.$unique.',r.'.$unique.' as AID,r.attendance_date as Date,
    (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM 
    personnel_basic_info p2,
    department d,
	designation de 
	where 
	p2.PBI_ID=r.PBI_ID and
	p2.PBI_DESIGNATION=de.DESG_ID and  							 
	p2.PBI_DEPARTMENT=d.DEPT_ID) as Applicant,
	r.place,
	r.late_reason as purpose,r.status as status
	from '.$table.' r
	WHERE 1 order by r.'.$unique.' DESC';
} ?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {
            myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=600,left = 250,top = -1");
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
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary">View OSD</button></td>
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
            <tr class="<?php if ($LeaveMasterData->approved_status!=='APPROVED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Approval Status</th>
            </tr>
            </thead>
            <tr>
                <th style="width: 10%; vertical-align: middle">Person</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$LeaveMasterData->authorised_by);?></td>
                <th style="width: 10%; vertical-align: middle">View Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->approved_viewed_at)) { ?>
                        <span class="label label-success" style="font-size:10px">Viewed</span>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Viewed At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (!empty($LeaveMasterData->approved_viewed_at)) { ?>
                        <?=$LeaveMasterData->approved_viewed_at;?>
                    <?php } else { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th style="width: 10%; vertical-align: middle">Acceptance Status</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->approved_status)=='REJECTED') { ?>
                        <span class="label label-danger" style="font-size:10px">Rejected</span>
                    <?php } elseif (($LeaveMasterData->approved_status)=='PENDING') { ?>
                        <span class="label label-danger" style="font-size:10px">Pending</span>
                    <?php } else { ?>
                        <span class="label label-success" style="font-size:10px">APPROVED</span>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Acceptance At</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle">
                    <?php if (($LeaveMasterData->approved_status)!=='APPROVED') { ?>
                        <span class="label label-danger" style="font-size:10px"><?=$LeaveMasterData->approved_status?></span>
                    <?php } else { ?>
                        <?=$LeaveMasterData->approved_at;?>
                    <?php } ?>
                </td>
                <th style="width: 10%; vertical-align: middle">Remarks</th>
                <th style="width: 1%;  vertical-align: middle">:</th>
                <td style="width: 24%; vertical-align: middle"><?=$LeaveMasterData->remarks_while_approved;?></td>
            </tr>
        </table>


        <table align="center" class="table table-striped table-bordered" style="width:90%;font-size:11px; margin-top: 5px">
            <thead>
            <tr class="bg-danger text-white">
                <th colspan="7" style="text-align: center; font-size: 13px; font-weight: bold;">OutSide Duty Request</th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th style="text-align: center; vertical-align:middle; width: 20%">Date</th>
                <th style="text-align: center; vertical-align:middle">Place</th>
                <th style="text-align: center; vertical-align:middle">Purpose</th>
                <th style="text-align: center; vertical-align:middle">Remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="text-align: center; vertical-align:middle"><?=$LeaveMasterData->attendance_date;?></td>
                <td style="text-align: center; vertical-align: middle"><?=$LeaveMasterData->place;?></td>
                <td style="text-align: center; vertical-align:middle"><?=$LeaveMasterData->late_reason;?></td>
                <td style="text-align: center; vertical-align:middle"><input type="text" class="form-control col-md-7 col-xs-12" placeholder="Enter a note for the application, if necessary" name="remarks_while_granted" style="width: 99%; font-size: 11px"></td>
            </tr>
            </tbody>
        </table>
        <?php if($current_status!=$required_status && $current_status!="PENDING" && $current_status!="RETURNED"){ echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been Approved!!</i></h6>';} else { ?>
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