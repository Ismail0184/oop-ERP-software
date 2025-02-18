<?php
require_once 'support_file.php';
$title="Leave Application";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$unique='id';
$$unique = @$_GET[$unique];
$unique_field='type';
$table="hrm_leave_info";
$now = date("Y-m-d h:i:sa");

$current_status=find_a_field("".$table."","status","".$unique."=".$$unique."");
$required_status="PENDING";
$authorused_status="ACCEPTED";
$page="hrm_pending_leave.php";
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
    $_POST['granted_by']=$_SESSION['userid'];
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

    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME," - ", d.DEPT_DESC,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.PBI_ID and p2.PBI_DESIGNATION=de.DESG_ID and
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Applicant,concat(r.s_date," <strong> to</strong> ",r.e_date) as "Leave Duration",r.total_days,r.reason as remarks, r.status as status
     		  from '.$table.' r
     		  WHERE   
     		  r.s_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and r.e_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and 
     		  r.half_or_full in ("Full")
     		  order by r.'.$unique.' DESC';
} else {

    $res='select r.'.$unique.',r.'.$unique.' as No,r.entry_at as "Application Date",
     (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME," - ", d.DEPT_DESC,")") FROM personnel_basic_info p2,department d,designation de where p2.PBI_ID=r.PBI_ID and p2.PBI_DESIGNATION=de.DESG_ID and 
     p2.PBI_DEPARTMENT=d.DEPT_ID) as Applicant,concat(r.s_date," <strong> to</strong> ",r.e_date) as "Leave Duration",r.total_days,r.reason as remarks,r.status as status
     from '.$table.' r
     WHERE
     r.status not in  ("DRAFTED","REJECTED","GRANTED") and
     r.half_or_full in ("Full")
     order by r.'.$unique.' DESC';
} ?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {
            myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=800,left = 250,top = -1");
        }

        function GetDays(){
            var dropdt = new Date(document.getElementById("s_date").value);
            var pickdt = new Date(document.getElementById("e_date").value);
            return parseInt((pickdt - dropdt) / (24 * 3600 * 1000))+1;
        }
        function cal(){
            if(document.getElementById("e_date")){
                document.getElementById("total_days").value=GetDays();
            }
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

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?=$title;?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px">
                    <tr>
                        <th style="width: 10%">Application ID</th>
                        <th style="width: 1%">:</th>
                        <td style="width: 24%"><?=$LeaveMasterData->id;?></td>
                        <th style="width: 10%">Date</th>
                        <th style="width: 1%">:</th>
                        <td style="width: 24%"><?=$LeaveMasterData->entry_at;?></td>
                        <th style="width: 10%">Leave Reason</th>
                        <th style="width: 1%">:</th>
                        <td style="width: 24%"><?=$LeaveMasterData->reason;?></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: middle">Leave Type</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=find_a_field('hrm_leave_type','leave_type_name','id='.$LeaveMasterData->type);?></td>
                        <th style="vertical-align: middle">Address</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$LeaveMasterData->leave_address;?></td>
                        <th style="vertical-align: middle">Mobile</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$LeaveMasterData->leave_mobile_number;?></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: middle">Leave Duration</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$LeaveMasterData->s_date;?> to <?=$LeaveMasterData->e_date;?></td>
                        <th style="vertical-align: middle">Total Days</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$LeaveMasterData->total_days;?></td>
                        <th style="vertical-align: middle">Sent for Approval</th>
                        <th style="vertical-align: middle">:</th>
                        <td style="vertical-align: middle"><?=$LeaveMasterData->sent_at;?></td>
                    </tr>
                </table>
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px;">
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
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px;">
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
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px;">
                    <thead>
                    <tr class="<?php if ($LeaveMasterData->approved_status!=='APPROVED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                        <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Approval Status</th>
                    </tr>
                    </thead>
                    <tr>
                        <th style="width: 10%; vertical-align: middle">Person</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle"><?=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$LeaveMasterData->approved_by);?></td>
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
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px">
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
                            <td style="text-align: center"><?=$leave_row->yearly_leave_days;?> Days</td>
                            <?php
                            $totalPolicy=$totalPolicy+$leave_row->yearly_leave_days;
                        } ?>
                        <td style="text-align: center"><?=$totalPolicy;?> Days</td>
                    </tr>


                    <tr>
                        <td>Taken</td>
                        <?php $res=mysqli_query($conn, "select * from hrm_leave_type where status=1");
                        $total_taken = 0;
                        while($leave_row=mysqli_fetch_object($res)){ ?>
                            <td style="text-align: center"><?php $leave_taken=find_a_field("".$table."","SUM(total_days)","type='".$leave_row->id."' and s_date between '$dfrom' and '$dto' and PBI_ID='".$LeaveMasterData->PBI_ID."'"); if($leave_taken>0){ echo number_format($leave_taken),' Days';} else echo ''; ?></td>
                            <?php
                            $total_taken=$total_taken+$leave_taken;
                        } ?>
                        <td style="text-align: center"><?=$total_taken;?> Days</td>
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
                        <th style="text-align: center"><?=$totalPolicy-$total_taken;?> Days</th>
                    </tr>
                    </tbody>
                </table>

                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
                <table align="center" class="table table-striped table-bordered" style="width:99%;font-size:11px; margin-top: 5px">
                    <thead>
                    <tr class="<?php if ($LeaveMasterData->granted_status!=='GRANTED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                        <th colspan="7" style="text-align: center; font-size: 13px; font-weight: bold;">Leave Request</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align:middle; width: 5%">Type</th>
                        <th style="text-align: center; vertical-align:middle; width: 40%">Leave Duration<br>(From & to)</th>
                        <th style="text-align: center; vertical-align:middle; width: 10%">Applied Days</th>
                        <th style="text-align: center; vertical-align:middle; width: 10%">Granted Days</th>
                        <th style="text-align: center; vertical-align:middle">Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align:middle"><?=find_a_field("hrm_leave_type","leave_type_name","id=".$LeaveMasterData->type."");?></td>
                        <td style="text-align: center; vertical-align: middle">
                            <input type="date" id="s_date" style="width:48%; font-size:11px" required="required" name="s_date" value="<?=$LeaveMasterData->s_date?>" onchange="cal()" class="form-control col-md-7 col-xs-12" >
                            <input type="date" id="e_date" style="width:48%; margin-left: 4%; font-size:11px" required="required" name="e_date" value="<?=$LeaveMasterData->e_date?>" onchange="cal()" class="form-control col-md-7 col-xs-12" >
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            <input type="text" id="applied" readonly name="applied" value="<?=number_format($LeaveMasterData->total_days);?>" style="width:99%; font-size:11px;float:right" placeholder="total applied days" class="form-control col-md-7 col-xs-12" >
                        </td>
                        <td style="text-align: center; vertical-align: middle">
                            <input type="text" id="total_days" readonly name="total_days" value="<?=number_format($LeaveMasterData->total_days);?>" style="width:99%; font-size:11px;float:right" placeholder="total applied days" class="form-control col-md-7 col-xs-12" >
                        </td>
                        <td style="text-align: center; vertical-align:middle"><input type="text" class="form-control col-md-7 col-xs-12" placeholder="Enter a note for the application, if necessary" required name="remarks_while_granted" style="width: 99%; font-size: 11px"></td>
                    </tr>
                    </tbody>
                </table>
                    <?php if($current_status=="APPROVED") { ?>
                    <input type="hidden" name="<?=$unique?>" value="<?=$$unique?>">
                    <table align="center" style="width:90%;font-size:12px;">
                        <tr>
                            <td>
                                <button type="submit" style="font-size:12px; float:left" onclick='return window.confirm("Are you confirm to reject the application?");' name="reject" class="btn btn-danger"><i class="fa fa-ban"></i> Reject & Send Back</button>
                                <button type="submit" style="font-size:12px; float:right" onclick='return window.confirm("Are you confirm to grant the application?");' name="confirm" class="btn btn-success">I granted the application <i class="fa fa-check"></i></button>
                            </td>
                        </tr>
                    </table>
                    <?php } else { ?>
                        <h6 class="text-danger text-center"><i>This application has not yet been approved. Wait until approval!!</i></h6>
                    <?php } ?>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
<?=$html->footer_content();?>