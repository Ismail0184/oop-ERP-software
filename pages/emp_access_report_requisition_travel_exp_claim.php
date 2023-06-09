 <?php
require_once 'support_file.php';
$title="Un-Approved Travel Exp. Claim List";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$todayss=$dateTime->format("d/m/Y  h:i A");

$now=time();
$unique='trvClaim_id';
$unique_field='application_date';
$table="travel_application_claim_master";
$table_details="travel_application_claim_details";
$current_status=find_a_field("".$table."","status","".$unique."=".$_GET[$unique]."");
$required_status="RECOMMENDED";
$page="emp_access_report_requisition_travel_exp_claim.php";
$crud      =new crud($table);
$$unique = $_GET[$unique];
$target_page='emp_access_requisition_travel_exp_claim.php';

if(prevent_multi_submit()){
  
    if(isset($_POST['re_process'])){		
    $_POST['status']='MANUAL';
	$_SESSION['initiate_travel_exp_claim_requisition']=$_GET[$unique];		
    $crud->update($unique);
    echo "<script>self.opener.location = '$target_page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
    }
 
    
    if(isset($_POST[authorised])){
            mysqli_query($conn, "Update ".$table." SET status='".$authorised_status."',authorised_date='$todayss' where ".$unique."=".$_GET[$unique]."");
            $name=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$getid[PBI_ID]);
            $name2=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$getid[approved_by]);
            $name3=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$getid[authorised_person]);
            $creadtby=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$getid[PBI_ID]);
            //$hrexecutive='shanto@icpbd.com';
            $hrmanager='g.majid@icpbd.com';
            ///////////////////////// to admin
            $to = 'shanto@icpbd.com';
            $subject = "Approved Requisition for Travel Exp. Claim";
            $txt1 = "<p>Dear Admin,</p>

<p>An approved requisition is waiting for your action. Please check and take necessary actions to solve.</p>

<p><strong>Requisition By</strong>- ".$name."</p>
<p><strong>Recommended By</strong>- ".$name2."</p>
<p><strong>Authorised By</strong>- ".$name3."</p>

<p><b><em>This EMAIL is automatically generated by ERP Software</em>.</b></p>";

            $txt=$txt1.$txt2.$tr;
            $from = 'erp@icpbd.com';
            $headers = "";
            $headers .= "From: ERP Software<erp@".$_SERVER['SERVER_NAME']."> \r\n";
            $headers .= "Reply-To:" . $from . "\r\n" ."X-Mailer: PHP/" . phpversion();
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Cc: g.majid@icpbd.com' . "\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            mail($to,$subject,$txt,$headers);
            $to = $creadtby;
            $subject = "Approved Your Requisition";
            $txt1 = "<p>Dear ".$name. "</p>
<p>Your Requisition is Approved. Please contact with admin.</p>
<p>Recommended By- ".$name2."</p>
<p>Authorised By- ".$name3."</p>				
<p><b><em>This EMAIL is automatically generated by ERP Software.</em></b></p>";

            $txt=$txt1.$txt2.$tr;
            $from = 'erp@icpbd.com';
            $headers = "";
            $headers .= "From: ERP Software<erp@".$_SERVER['SERVER_NAME']."> \r\n";
            $headers .= "Reply-To:" . $from . "\r\n" ."X-Mailer: PHP/" . phpversion();
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            mail($to,$subject,$txt,$headers);

echo "<script>self.opener.location = '$page'; self.blur(); </script>";
echo "<script>window.close(); </script>";
        }
    
//for modify..................................
if(isset($_POST['modify']))
{
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
    $crud->update($unique);
    $type=1;
    //echo $targeturl;
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

//for Delete..................................
if(isset($_POST['Deleted']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);	
	$crud = new crud($table_details);
    $condition = $unique . "=" . $$unique;
    $crud->delete_all($condition);	
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

$res='Select td.* from '.$table_details.' td
where td.'.$unique.'='.$_GET[$unique].'';

?>



<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=500,left = 200,top = -1");}
    </script>
<?php if(isset($_GET[$unique])){
    require_once 'body_content_without_menu.php';
} else {
    require_once 'body_content.php';
} ?>

 
<?php if(isset($_GET[$unique])){ ?>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                    <div class="x_content">
                    <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                    <? require_once 'support_html.php';?>
                    <table class="table table-striped table-bordered" style="width:100%;font-size:11px">
                   <thead>
                   <tr style="background-color: bisque">
                        <th style="text-align:center; vertical-align:middle">Date</th>
                        <th style="text-align:center; vertical-align:middle">Place/location<br />(from - to)</th>
                        <th style="text-align:center; vertical-align:middle">Mode of Transport <br /> (Details - Cost)</th>
                        <th style="text-align:center; vertical-align:middle">Lodging Expense <br /> (Details - Cost)</th>
                        <th style="text-align:center; vertical-align:middle">Breakfast</th>
                        <th style="text-align:center; vertical-align:middle">Lunch</th>
                        <th style="text-align:center; vertical-align:middle">Dinner</th>
                        <th style="text-align:center; vertical-align:middle">Total</th>
                     </tr>
                     </thead><tbody>
                 <?php  $result=mysqli_query($conn, $res);
                  while($data=mysqli_fetch_object($result)){?>
                   <tr>         <td style="text-align: center"><?=$data->travel_date;?></td>
                                <td><?=$data->travel_from;?> - <?=$data->travel_to;?></td>
                                <td style="text-align: center"><?=$data->mode_of_transport;?> - <?=$data->transport_fair_rqst;?></td>
                                <td style="text-align: center"><?=$data->lodging_expense;?> - <?=$data->lodging_fair_rqst;?></td>
                                <td style="text-align: right"><?=$data->breakfast_rqst;?></td>
                                <td style="text-align: right"><?=$data->lunch_rqst;?></td>
                                <td style="text-align: right"><?=$data->dinner_rqst;?></td>
                                <td style="text-align: right"><?=number_format($data->total_amount,2);?></td>
                                </tr>
                                <?php $total_amounts=$total_amounts+$data->total_amount;} ?>
                                <tr>

                                <th colspan="7" style="text-align:right">Total</th>
                                <th style="text-align:right"><?=number_format($total_amounts,2)?></th>
                                </tr>
                                
                                </tbody>
                                </table>
                                     <?php if($current_status=='MANUAL' || $current_status=='UNCHECKED'){?>
                                     <table style="width:100%;font-size:12px">
                                          <tr>
                                          <td><button type="submit" name="re_process" id="re_process" class="btn btn-danger" style='font-size:12px' onclick='return window.confirm("Are you confirm to Return?");'>Re-process</button></td></tr></table>           
                                            <?php } else { echo '<h6 style="text-align:center; color:red; font-weight:bold"><i>This application has been '.$current_status.'!!</i></h6>';} ?>                               
                                </form>
                                </div>
                                </div>
                                </div>
<?php } ?>

<?php if(!isset($_GET[$unique])): ?>
<form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
        <table align="center" style="width: 50%;">
            <tr><td>
                <input type="date"  style="width:150px; font-size: 11px; height: 25px" max="<?=date('Y-m-d');?>"  value="<?php if($_POST[f_date]) echo $_POST[f_date]; else echo date('Y-m-01');?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                <td style="width:10px; text-align:center"> -</td>
                <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?php if($_POST[t_date]) echo $_POST[t_date]; else echo date('Y-m-d');?>" required  max="<?=date('Y-m-d');?>" name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewreport"  class="btn btn-primary">View Applications</button></td>
            </tr></table>
</form>


<?php 
if(isset($_POST[viewreport])):
$res='select r.'.$unique.',r.'.$unique.' as Req_No,r.'.$unique_field.' as application_date,r.travel_purpose,
concat((select PBI_NAME from personnel_basic_info where PBI_ID=r.checked_by),"<br> at: ",r.checked_at) as checked_by,
concat((select PBI_NAME from personnel_basic_info where PBI_ID=r.approved_by),"<br> at: ",r.approved_at) as approved_by,
concat((select PBI_NAME from personnel_basic_info where PBI_ID=r.granted_by),"<br>Viewed at: ",r.hrm_viewed_date,"<br> Granted at: ",r.granted_at) as granted_by_HR,
concat((select PBI_NAME from personnel_basic_info where PBI_ID=r.settled_by),"<br>Viewed at: ",r.hrm_viewed_date,"<br> Settled at: ",r.settled_at) as Settled_by_accounts,
IF(r.status="RETURNED", concat(r.status,"<br>",r.return_comments), r.status) as status
 from '.$table.' r
 WHERE 
 r.PBI_ID='.$_SESSION['PBI_ID'].'	  
order by r.'.$unique.' DESC';
endif;
echo $crud->report_templates_with_status($res,$title);?>
<?php endif;?>    
<?=$html->footer_content();mysqli_close($conn);?>