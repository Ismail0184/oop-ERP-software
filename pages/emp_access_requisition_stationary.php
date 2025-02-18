 <?php
require_once 'support_file.php';
$title="Stationary Requisition";

$dfrom=date('Y-1-1');
$dto=date('Y-m-d');
$now=time();
$table="warehouse_other_issue";
$unique = 'oi_no';   // Primary Key of this Database table
$table_details = 'warehouse_other_issue_detail';
$details_unique = 'id';
$page="emp_access_requisition_stationary.php";
$crud      =new crud($table);
$getItemCode = @$_GET['item_code_GET'];
$PBI_ID = @$_SESSION['PBI_ID'];
$taken=getSVALUE("".$table_details."", "SUM(qty)", " where oi_date between '$dfrom' and '$dto' and  issued_to='".$PBI_ID."' and item_id=".$getItemCode."");
$unit=getSVALUE("item_info", "unit_name", " where item_id=".$getItemCode."");
$department=getSVALUE("personnel_basic_info", "PBI_DEPARTMENT", " where PBI_ID=".$PBI_ID."");


if(prevent_multi_submit()){
   
    if(isset($_POST['initiate']))
    {   $_POST['section_id'] = $_SESSION['sectionid'];
		$_POST['company_id'] = $_SESSION['companyid'];
		$_POST['entry_by'] = $_SESSION['userid'];
        $_POST['entry_at'] = date('Y-m-d H:s:i');
		$sd=$_POST['oi_date'];
		$_POST['oi_date']=date('Y-m-d' , strtotime($sd));
	    $_POST['issue_type'] = 'Office Issue';	
	    $_POST['status'] = 'MANUAL';
		$_POST['requisition_from'] = $department;
	    $_POST['warehouse_id'] = '11';
		$_POST['issued_to'] = $PBI_ID;
		$_SESSION['initiate_hrm_stationary_requisition']=$_POST[$unique];		
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        unset($_POST);
        unset($$unique);
    }
	
	
	if(isset($_POST['add']))
    {
		$_POST['entry_by'] = $_SESSION['userid'];
        $_POST['entry_at'] = date('Y-m-d H:s:i');
		$sd=$_POST['oi_date'];
		$_POST['oi_date']=date('Y-m-d' , strtotime($sd));
	    $_POST['issue_type'] = 'Office Issue';	
	    $_POST['status'] = 'MANUAL';
		$_POST['requisition_from'] = $_SESSION["department"];
	    $_POST['warehouse_id'] = '11';
		$_POST['recommend_qty'] = $_POST['qty'];
		$_POST['request_qty'] = $_POST['qty'];
		$_POST['issued_to'] = $PBI_ID;
		$_POST['oi_no']=$_SESSION['initiate_hrm_stationary_requisition'];
        $crud      =new crud($table_details);
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        unset($_POST);
        unset($$unique);
    }
    
    
//for modify..................................
if(isset($_POST['modify']))
{
	$sd=$_POST['oi_date'];
    $_POST['oi_date']=date('Y-m-d' , strtotime($sd));
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
	$sd=$_POST['oi_date'];
    $_POST['oi_date']=date('Y-m-d' , strtotime($sd));
    $crud->update($unique);
    $type=1;
}}

 //for Delete..................................
 if(isset($_POST['cancel']))
 {   $crud = new crud($table_details);
     $condition =$unique."=".$_SESSION['initiate_hrm_stationary_requisition'];
     $crud->delete_all($condition);
     $crud = new crud($table);
     $condition=$unique."=".$_SESSION['initiate_hrm_stationary_requisition'];
     $crud->delete($condition);
     unset($_SESSION['initiate_hrm_stationary_requisition']);
     unset($_POST);
     unset($$unique);
 }


 //for Delete..................................
 if(isset($_POST['confirm']))
 {   $name=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$PBI_ID);
     $emailId=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$recommended_by);
     $emailIds=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$authorised_person);
     $to = $emailId;
     $subject = "Requisition for Stationary";
     $txt1 = "<p>Dear Sir,</p>				
				<p>A requisition is pending for your Recommendation. Please enter Employee Access module to approve the requisition. </p>				
				<p>Requisition By- ".$name."</p>				
				<p><b><i>This EMAIL is automatically generated by ERP Software.</i></b></p>";
     $txt=$txt1.$txt2.$tr;
     $from = 'erp@icpbd.com';
     $headers = "";
     $headers .= "From: ERP Software<erp@".$_SERVER['SERVER_NAME']."> \r\n";
     $headers .= "Reply-To:" . $from . "\r\n" ."X-Mailer: PHP/" . phpversion();
     $headers .= 'MIME-Version: 1.0' . "\r\n";
     $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     mail($to,$subject,$txt,$headers);
     mysqli_query($conn, "Update warehouse_other_issue set status='PENDING' where oi_no=".$_SESSION['initiate_hrm_stationary_requisition']."");
     unset($_SESSION['initiate_hrm_stationary_requisition']);
     unset($_POST);
     unset($$unique);
 }

// data query..................................
if(isset($_SESSION[initiate_hrm_stationary_requisition]))
{   $condition=$unique."=".$_SESSION[initiate_hrm_stationary_requisition];
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

$COUNT_details_data=find_a_field(''.$table_details.'','Count(id)',''.$unique.'='.$_SESSION['initiate_hrm_stationary_requisition'].'');

 
$sql="select r.".$unique.",r.".$unique." as Req_No,r.oi_date as Req_Date,r.oi_subject as Remarks,r.status
				  from ".$table." r
				  WHERE r.issued_to=".$_SESSION['PBI_ID']." and
				  r.req_category not in ('1500010000') and 
				  r.status not in ('MANUAL')			  
				   order by r.".$unique." desc limit 10";
				   
$sql2="Select 
d.id,i.item_id as stationary_code,i.item_name as Stationary_details,i.unit_name,d.qty as Requested_Qty
from 
warehouse_other_issue_detail d,
item_info i
  where 
 d.item_id=i.item_id and 
 d.".$unique."='".$_SESSION['initiate_hrm_stationary_requisition']."'";	
 $result=mysqli_query($conn, $sql2);
 while($data=mysqli_fetch_object($result)){
	 $id=$data->id;
	 if(isset($_POST['deletedata'.$id]))
    {  mysqli_query($conn, ("DELETE FROM ".$table_details." WHERE id=".$id));
       unset($_POST);
    }
    if(isset($_POST['editdata'.$id]))
    {   mysqli_query($conn, ("UPDATE ".$table_details." SET item_id='".$_POST[item_id]."', qty='".$_POST[qty]."' WHERE id=".$id));
        unset($_POST);
    }}		   
if (isset($_GET[id])) {
    $edit_value=find_all_field(''.$table_details.'','','id='.$_GET[id].'');
} 
$sql_item_id="SELECT i.item_id,concat(i.item_id,' : ',i.item_name,' (',sg.sub_group_name,')') FROM  item_info i,
							item_sub_group sg,
							item_group g WHERE  i.sub_group_id=sg.sub_group_id and 
							 sg.group_id=g.group_id and 
							 g.group_id  in ('600000000','1100000000') 							 
							  order by i.item_name";
							  $sql_recommended_by="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' : ',d.DEPT_SHORT_NAME) FROM 							 
							personnel_basic_info p,
							department d,
							essential_info e
							 where 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID	and
							 p.PBI_ID=e.PBI_ID and 
							 e.ESS_JOB_LOCATION=1 group by p.PBI_ID					 
							  order by p.PBI_NAME";
							  $sql_authorised_person="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' : ',d.DEPT_SHORT_NAME) FROM 							 
							personnel_basic_info p,
							department d,
							essential_info e
							 where 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID	and
							 p.PBI_ID=e.PBI_ID and 
							 e.ESS_JOB_LOCATION=1 group by p.PBI_ID					 
							  order by p.PBI_NAME";
?>

<?php require_once 'header_content.php'; ?>
<SCRIPT language=JavaScript>
function reload(form){
	var val=form.item_id.options[form.item_id.options.selectedIndex].value;
	self.location='<?=$page;?>?item_code_GET=' + val ;}
</script>
 <style>
     input[type=text]
     {
         font-size: 11px;
     }
 </style>
<?php require_once 'body_content.php'; ?>
                    <div class="col-md-7 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?php echo $title; ?></h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <div class="input-group pull-right">
                                        </div>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            
                            
                            <div class="x_content"> 
                            <form action="" enctype="multipart/form-data" method="post" name="addem" id="addem" style="font-size: 11px">
                                 <? //require_once 'support_html.php';?>
                                     <table style="width:100%"  cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:50%;">
                                            <div class="form-group">                                            
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 40%">Req. No<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input type="text" id="<?=$unique?>"   required="required" name="<?=$unique?>" value="<? if($_SESSION['initiate_hrm_stationary_requisition']>0) { echo  $_SESSION['initiate_hrm_stationary_requisition']; 
											
														} else 
											
											{ echo find_a_field($table,'max('.$unique.')+1','1');											
											if($$unique<1) $$unique = 1;}?>" class="form-control col-md-7 col-xs-12"  readonly style="width:100%">
                                                    </div>
                                                </div></td>


                                            <td style="width:50%">
                                            <div class="form-group" style="width: 100%">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 40%">Date<span class="required">*</span></label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input type="date"  readonly  required="required" name="oi_date" value="<?=date('Y-m-d');?>" class="form-control col-md-7 col-xs-12" MAX=<?=date('Y-m-d')?> style="width:100%; font-size:11px" >      </div>
                                                </div>
                                            </td></tr>

<tr><td style="height:5px"></td></tr>
<tr>
                                            <td><div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Priority</label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="Priority" id="Priority">
                      <option></option>
                      <option value="Urgent" <?php if ($Priority=='Urgent') echo 'selected'; else echo '';?> >Urgent</option>
                      <option value="High" <?php if ($Priority=='High') echo 'selected'; else echo '';?>>High</option>
                      <option value="Medium" <?php if ($Priority=='Medium') echo 'selected'; else echo '';?>>Medium</option>
                      <option value="Low" <?php if ($Priority=='Low') echo 'selected'; else echo '';?>>Low</option>                      
                      </select>
                                                    </div></div></td>
                                                    
                                                    

                                            <td><div class="form-group">
               <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Remarks</label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="oi_subject" id="oi_subject" value="<?=$oi_subject?>" class="form-control col-md-7 col-xs-12" style="width: 100%;"></div></div></td>
                                        </tr>

<tr><td style="height:5px"></td></tr>
                                    <tr>
                                    <td>
                                    <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:40%">Recommended By<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="recommended_by" id="recommended_by">
                                                <option></option>
                                                <?=advance_foreign_relation($sql_recommended_by,$recommended_by);?>
                                            </select></div></div>
                                        </td>
                                        
                                        
                                                                          
                                    <td>
                                    <div class="form-group">
         <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:40%">Authorised By<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="authorised_person" id="authorised_person">
                                                <option></option>
                                                <?=advance_foreign_relation($sql_authorised_person,$authorised_person);?>
                                            </select>
                                    </div></div>
                                    </td>
                                    </tr>

                                        </table>



                                    <div class="form-group" style="margin-left:40%; margin-top: 15px">

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <?php if($_SESSION[initiate_hrm_stationary_requisition]){  ?>
                                               <button type="submit" name="modify" style="font-size: 12px" class="btn btn-primary" onclick='return window.confirm("Are you confirm to Update?");'>Update</button>
                                             <?php   } else {?>
                                                <button type="submit" name="initiate" style="font-size: 12px" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary">Initiate</button>
                                            <?php } ?>
                                        </div></div><br><br><br>
                                </form></div></div></div>


<?=recentdataview($sql,'','hrm_leave_info','200px','Recent Requisitions','hrm_requisition_stationary_report.php','5');?>  




                    <?php if($_SESSION[initiate_hrm_stationary_requisition]):  ?>
                                <form action="<?=$page;?>" enctype="multipart/form-data" name="addem" id="addem" class="form-horizontal form-label-left" method="post" style="font-size: 11px">
                                <input type="hidden" id="oi_date" readonly  required="required" name="oi_date" value="<?=$oi_date;?>" class="form-control col-md-7 col-xs-12" style="width:100%" >
                                <? require_once 'support_html.php';?>
                                    <table align="center" class="table table-striped table-bordered" style="width:98%; font-size: 11px">
                                        <thead>
                                        <tr style="background-color: bisque">
                                            <th style="text-align: center; vertical-align: middle;">Stationary Items</th>
                            <th style="text-align:center; vertical-align: middle; width:8%">Has Taken<br> (Current Year)</th>
                            <th style="text-align:center; vertical-align: middle; width:8%">Has Taken <br> (Current Month)</th>
                            <th style="text-align:center; vertical-align: middle; width:8%">Last Taken at</th>
                                            <th style="text-align:center; vertical-align: middle">Request Qty</th>
                                            <th style="text-align:center; vertical-align: middle">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="left" style="width: 25%; vertical-align:middle">
                                            <input type="hidden" name="oi_date" id="oi_date" value="<?=$oi_date;?>"  />
                                                <select class="select2_single form-control" style="width: 100%" tabindex="-1" required="required" name="item_id" id="item_id">
                                                    <option></option>
                                                    <?=advance_foreign_relation($sql_item_id,$edit_value->item_id);?>
                                                </select>
                                            </td>
                                           
                                               <td align="center" style="vertical-align:middle">
     <input type="hidden" id="taken" name="taken" value="<?=$taken;?>" >
      <input type="text" id="takens" style="width:100%; height:37px; font-size: 11px; text-align:center"    name="takens" class="form-control col-md-7 col-xs-12" value="" readonly > </td>
       <td align="center" style="vertical-align:middle">
      <input type="text" id="takens" style="width:100%; height:37px; font-size: 11px; text-align:center"    name="takens" class="form-control col-md-7 col-xs-12" value="" readonly > </td>
       <td align="center" style="vertical-align:middle">
      <input type="text" id="takens" style="width:100%; height:37px; font-size: 11px; text-align:center"    name="takens" class="form-control col-md-7 col-xs-12" value="" readonly > </td>


                                            <td align="center" style="width:8%; vertical-align:middle">
                                                 <input type="text" id="qty" style="width:100%; height:37px; font-size: 12px; text-align:center" value="<?=$edit_value->qty;?>" name="qty" class="form-control col-md-7 col-xs-12" placeholder="request qty"></td>
                                            <td align="center" style="width:5%; vertical-align:middle"><?php if (isset($_GET[id])) : ?><button type="submit" class="btn btn-primary" name="editdata<?=$_GET[id];?>" id="editdata<?=$_GET[id];?>" style="font-size: 11px">Update</button><br><a href="<?=$page;?>" style="font-size: 11px"  onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you sure you want to Delete the Voucher?");' class="btn btn-danger">Cancel</a>
                    <?php else: ?><button type="submit" class="btn btn-primary" name="add" id="add" style="font-size: 11px">Add</button> <?php endif; ?></td></tr>
                                            </tbody>
                                    </table>
                                </form>


<?=adds_data_delete_edit($sql2,$unique,$_SESSION['initiate_hrm_stationary_requisition'],$COUNT_details_data);?>  
<?php endif; mysqli_close($conn); ?> 
<?=$html->footer_content();?> 
