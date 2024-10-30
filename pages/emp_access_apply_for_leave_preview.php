<?php
require_once 'support_file.php';
$title="Leave Application Status";
$unique = 'id';
$$unique = @$_GET[$unique];
$table="hrm_leave_info";
$crud      =new crud($table);
$LeaveMasterData = find_all_field('hrm_leave_info','','id='.$$unique);


if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    echo "<script>self.opener.location = 'emp_acess_apply_for_leave.php'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

if(isset($_POST['confirm']))
{
    $_POST['status']="PENDING";
    $_POST['sent_at']=date("Y-m-d h:i:sa");
    $crud->update($unique);
    echo "<script>self.opener.location = 'emp_acess_apply_for_leave.php'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}
?>

<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content_without_menu.php'; ?>

<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                <input type="hidden" name="<?=$unique?>" value="<?=$$unique?>">
                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px">
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

                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
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

                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
                    <thead>
                    <tr class="<?php if ($LeaveMasterData->recommended_status!=='RECOMMENDED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                        <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Recommendation Status
                        </th>
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

                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
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

                <table align="center" class="table table-striped table-bordered" style="width:100%;font-size:11px;">
                    <thead>
                    <tr class="<?php if ($LeaveMasterData->granted_status!=='GRANTED') { ?> bg-danger <?php } else { ?>bg-success text-white <?php } ?>">
                        <th colspan="9" style="text-align: center; font-size: 13px; font-weight: bold;">Granted Status (by HR department)
                        </th>
                    </tr>
                    </thead>
                    <tr>
                        <th style="width: 10%; vertical-align: middle">Person</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle">
                        <?php if (!empty($LeaveMasterData->granted_by)) { ?>
                            <?=find_a_field('users','fname','user_id='.$LeaveMasterData->granted_by);?>
                        <?php } else { ?>
                            Don't know yet.
                        <?php } ?>
                        </td>

                        <th style="width: 10%; vertical-align: middle">View Status</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle">
                            <?php if (!empty($LeaveMasterData->granted_viewed_at)) { ?>
                                <span class="label label-success" style="font-size:10px">Viewed</span>
                            <?php } else { ?>
                                <span class="label label-danger" style="font-size:10px">Pending</span>
                            <?php } ?>
                        </td>

                        <th style="width: 10%; vertical-align: middle">Viewed At</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle">
                            <?php if (!empty($LeaveMasterData->granted_viewed_at)) { ?>
                                <?=$LeaveMasterData->granted_viewed_at;?>
                            <?php } else { ?>
                                <span class="label label-danger" style="font-size:10px">Pending</span>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <th style="width: 10%; vertical-align: middle">Acceptance Status</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle">
                            <?php if (($LeaveMasterData->granted_status)=='REJECTED') { ?>
                                <span class="label label-danger" style="font-size:10px">Rejected</span>
                            <?php } elseif (($LeaveMasterData->granted_status)=='PENDING') { ?>
                                <span class="label label-danger" style="font-size:10px">Pending</span>
                            <?php } else { ?>
                                <span class="label label-success" style="font-size:10px">GRANTED</span>
                            <?php } ?>
                        </td>

                        <th style="width: 10%; vertical-align: middle">Acceptance At</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle">
                            <?php if (($LeaveMasterData->granted_status)!=='GRANTED') { ?>
                                <span class="label label-danger" style="font-size:10px">Pending</span>
                            <?php } else { ?>
                                <?=$LeaveMasterData->granted_at;?>
                            <?php } ?>
                        </td>

                        <th style="width: 10%; vertical-align: middle">Remarks</th>
                        <th style="width: 1%;  vertical-align: middle">:</th>
                        <td style="width: 24%; vertical-align: middle"><?=$LeaveMasterData->remarks_while_granted;?></td>
                    </tr>
                </table>

                <?php if($LeaveMasterData->status=='DRAFTED'){ ?>
                    <table align="center" style="width:90%;font-size:12px;">
                        <tr>
                            <td>
                                <button type="submit" style="font-size:12px; float:left" onclick='return window.confirm("Are you confirm to Deleted?");' name="delete" class="btn btn-danger"><i class="fa fa-eraser"></i> Delete the application</button>
                                <button type="submit" style="font-size:12px; float:right" onclick='return window.confirm("Are you confirm to Send?");' name="confirm" class="btn btn-success">Send for Approval <i class="fa fa-send-o"></i></button>
                            </td>
                        </tr>
                    </table>
                <?php } else { ?>
                    <h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been <?=$LeaveMasterData->status?> !!</i></h6>
                <?php } ?>

                <?php if($LeaveMasterData->status=="REJECTED"){ ?>
                    <h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been Rejected</i></h6>
                    <table align="center" style="width:90%;font-size:12px;">
                        <tr>
                            <td>
                                <button type="submit" style="font-size:12px;" onclick='return window.confirm("Are you confirm to Deleted?");' name="delete" class="btn btn-danger"><i class="fa fa-eraser"></i> Delete the application</button>
                            </td>
                        </tr>
                    </table>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<?=$html->footer_content();?>