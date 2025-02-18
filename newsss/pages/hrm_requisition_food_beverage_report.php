 <?php
require_once 'support_file.php';
$title="Food Requisition Report";
$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$todayss=$dateTime->format("d/m/Y  h:i A");

$now=time();
$unique='oi_no';
$unique_field='oi_date';
$table="warehouse_other_issue";
$table_details="warehouse_other_issue_detail";
$current_status=find_a_field("".$table."","status","".$unique."=".$_GET[$unique]."");
$required_status="PENDING";
$page="hrm_requisition_food_beverage_report.php";
$crud      =new crud($table);
$$unique = $_GET[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";

if(prevent_multi_submit()){
  
    if(isset($_POST['Return']))
    {		
    $_POST['status']='RETURNED';
	$_POST['return_comments']=$_POST['return_comments'];
	$_POST['recommended_date']=$todayss;	
    $crud->update($unique);
    $type=1;
    //echo $targeturl;
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
?>



<?php require_once 'header_content.php'; ?>
<script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=500,left = 200,top = -1");}
    </script>
<?php require_once 'body_content.php'; ?>

 <?php if(!isset($_GET[$unique])){ ?>
     <!-------------------list view ------------------------->
     <div class="col-md-12 col-sm-12 col-xs-12">
         <div class="x_panel">
             <div class="x_title">
                 <h2><?=$title;?></h2>
                 <div class="clearfix"></div>
             </div>

             <div class="x_content">
             <table class="table table-striped table-bordered" style="width:100%;font-size:12px">
                   <thead>
                   <tr>
                       <th style="width: 2%">#</th>
                       <th style="">Req. No</th>
                       <th style="">Req. Date</th>
                       <th style="">Remarks</th>
                       <th style="">Priority</th>
                       <th style="text-align:center">Recommended <br />By</th>
                       <th style="">Authorised Person</th>
                       <th style="">Approved At</th>
                       <th style="">Status</th>
                   </tr>
                     </thead>
                      <tbody>
                      <? 	$res=mysql_query('select r.'.$unique.',r.'.$unique.' as Req_No,r.'.$unique_field.' as Req_Date,r.recommended_date,
				 (SELECT concat(p2.PBI_NAME," # ","(",de.DESG_SHORT_NAME,")") FROM 
							 
							personnel_basic_info p2,
							department d,
							designation de 
							 where 
							 p2.PBI_ID=r.issued_to and
							 p2.PBI_DESIGNATION=de.DESG_ID and  							 
							 p2.PBI_DEPARTMENT=d.DEPT_ID) as Req_By,r.oi_subject as Remarks,r.Priority,
							 (select PBI_NAME from personnel_basic_info where PBI_ID=r.recommended_by) as recommended_by,
							 (select PBI_NAME from personnel_basic_info where PBI_ID=r.authorised_person) as authorised_person,r.status,r.authorised_date
				  from '.$table.' r
				  WHERE r.issued_to='.$_SESSION['PBI_ID'].' and
				  r.req_category in ("1500010000")			  
				   order by r.'.$unique.' DESC');
                      while($req=mysql_fetch_object($res)){

                          ?>
                   <tr style="cursor: pointer" onclick="DoNavPOPUP('<?=$req->$unique;?>', 'TEST!?', 600, 700)">
                       <td><?=$i=$i+1;?></td>
                       <td><?=$req->$unique;?></td>
                       <td><?=$req->Req_Date;?></td>
                       <td><?=$req->Remarks;?></td>
                       <td><?=$req->Priority;?></td>
                       <td><?=$req->recommended_by;?></td>
                       <td><?=$req->authorised_person;?></td>
                       <td><?=$req->authorised_date;?></td>
                       <td><?=$req->status;?></td>
                                </tr>
                                <?php } ?>
                                
                                </tbody>
                                </table>
                
             </div>

         </div></div>
     <!-------------------End of  List View --------------------->
 <?php } ?>
<?php if(isset($_GET[$unique])){ ?>


                    <!-- input section-->
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?=$title;?></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br />

                                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post">
                                    <? require_once 'support_html.php';?>
                                    

                                    
                                   <table class="table table-striped table-bordered" style="width:100%;font-size:12px">
                   <thead>
                    <tr>
                     <th style="width: 2%">#</th>
                     <!--th style="">Req. No</th>
                     <th style="">Req. Date</th-->
                     <th style="text-align:center">Serving<br />Date</th>
                                    <th style="text-align:center">Serving<br />Time</th>
                                    <th style="text-align:center">Serving<br />Place</th>
                                    <th style="text-align:center">Purpose of Requisition</th>
                                    <th style="text-align:center">Person number to be Served</th>
                                    <th style="text-align:center">Prefered Restaurent/Shop:</th>
                                    <th style="text-align:center">Preffered Item</th>                                    
                                    <th style="text-align:center">Price</th>
                                    <th style="text-align:center">Number of Item</th>
                                    
                     <th style="">Already Taken<br /> (Current Year)</th>  
                     <th>Deleted ?</th>                                        
                     </tr>
                     </thead>
                      <tbody>
                      <?php 
if($_GET[deleteid]){
	
	mysql_query("Delete From ".$table_details." where ".$unique."=".$$unique." and id='$_GET[id]'"); ?>
<meta http-equiv="refresh" content="0;<?=$page;?>?<?=$unique;?>=<?php echo $_GET[$unique]; ?>">	
<?php } ?>
                 <? 	$res=mysql_query('Select td.*,i.* from '.$table_details.' td,
				 item_info i
				  where td.item_id=i.item_id and 				  
				  td.oi_no='.$_GET[$unique].'');
				   while($req_data=mysql_fetch_object($res)){
					   
					   
					   
					   $recommend_qty=$_POST['recemmended_qty_'.$req_data->id];
					   if(isset($_POST[recommend])){
					   
mysql_query("Update ".$table_details." SET recommend_qty=".$recommend_qty." where oi_no=".$_GET[$unique]." and id=".$req_data->id."");

					   }
				   ?>
                   <tr>
                                <td><?=$i=$i+1;?></td>
                                <!--td><?=$req_data->$unique;?></td>
                                <td><?=$req_data->oi_date;?></td-->
                                <td><?=$req_data->serving_date;?></td>
                                <td><?=$req_data->serving_time;?></td>
                                <td><?=$req_data->serving_place;?></td>
                                <td><?=$req_data->requisition_purpose;?></td>
                                <td><?=$req_data->served_person;?></td>
                                <td><?=$req_data->restaurent;?></td>   
                                <td><?=$req_data->item_name;?>,<?=$req_data->item_details;?></td>
                                <td style="text-align:center"><?=$req_data->rate;?></td>
                                <td><input type="text" name="recemmended_qty_<?=$req_data->id;?>" id="recemmended_qty_<?=$req_data->id;?>" value="<?=$req_data->qty;?>" style="width:80px" /></td>
                                <td style="text-align:center"><?=$taken=getSVALUE("".$table_details."", "SUM(qty)", " where oi_date between '$dfrom' and '$dto' and  oi_no='".$_GET[$unique]."' and item_id=".$req_data->item_id."");?> <?=$req_data->unit_name;?>'s</td>
                                <td style="text-align:center">
                                <?php if($current_status!=$required_status){ echo 'Done';} else { ?>
                                <a onclick='return window.confirm("Mr. <?php echo $_SESSION['userfname']; ?>, Are you sure you want to Delete the Item?");' href="<?=$page?>?<?=$unique?>=<?php echo $_GET[$unique]; ?>&id=<?=$req_data->id;?>&deleteid=confrim" style="text-align:center"><img src="delete.png" style="margin-left:10px" height="20" width="20" /></a>
                                <?php } ?>
                                </td>
                                
                                </tr>
                                <?php } ?>
                                
                                </tbody>
                                </table>
                                
                                <?php
                                if(isset($_POST[recommend])){
								mysql_query("Update ".$table." SET status='RECOMMENDED',recommended_date='$todayss' where oi_no=".$_GET[$unique]."");
					   
					   /// fint authorised person name
$chid=find_a_field('warehouse_other_issue','authorised_person',''.$unique.'='.$_GET[$unique]);
$maild=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$chid);

$cby=find_a_field('warehouse_other_issue','issued_to','oi_no='.$_GET[$unique]);

///////////////////////// to authorise	
				
                $to = $maild;
				$subject = "Food & Beverage Requisition";
				$txt = "<p>Dear Sir/Madam,</p>
				<p>A new Requisition has been created. Requisition No is: <b>".$_GET[$unique]."</b></p>
				<p>Need your Authorization. <b>Please enter Employee Access module to Authorise the Requisition.<b></p>
				
				<p>Recommended By- <strong>".find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$_SESSION['PBI_ID'])."</strong></p>
				<p>Prepared By- <strong>".find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$cby)."</strong></p>
				<p><i>This EMAIL is automatically generated by ERP Software.</i></p>";
				
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
								?>
                                    
                                     <?php if($current_status!=$required_status){ echo '<h5 style="text-align:center; color:red; font-weight:bold"><i>This requisition has been recommended!!</i></h5>';} else { ?>
                                     <table style="width:100%;font-size:12px">

                                         <tr>
                                             <td style="width:50%">
                                                 <div class="form-group">
                                                     <div class="col-md-6 col-sm-6 col-xs-12">
                                                         <button type="submit" onclick='return window.confirm("Are you confirm to Deleted?");' name="Deleted" id="Deleted" class="btn btn-danger">Cancel & Deleted</button>
                                                     </div></div></td>



                                             <td style="width:50%; float:right">
                                                 <div class="form-group">
                                                     <div class="col-md-6 col-sm-6 col-xs-12">
                                                         <button type="submit" onclick='return window.confirm("Are you confirm to Recommended the Requisition?");' name="update" id="update" class="btn btn-success">Update the requisition</button>
                                                     </div></div>

                                             </td>
                                         </tr></table>
                                            <?php } ?>                               
                                                                                                                                   
                                    


                                </form>
                                </div>
                                </div>
                                </div>
<?php } ?>


                
        
<?php require_once 'footer_content.php' ?>