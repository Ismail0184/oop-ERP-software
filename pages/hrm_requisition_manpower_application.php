 <?php
require_once 'support_file.php';
$title="Manpower Requisition";

$dfrom=date('Y-1-1');
$dto=date('Y-m-d');

$now=time();

$table="man_power_application";
$unique = 'manPowerApp_id';   // Primary Key of this Database table


$page="hrm_requisition_manpower_application.php";
$crud      =new crud($table);
$taken=getSVALUE("".$table_deatils."", "SUM(qty)", " where oi_date between '$dfrom' and '$dto' and  issued_to='".$_SESSION[PBI_ID]."' and item_id=".$_GET[item_code_GET]."");
$unit=getSVALUE("item_info", "unit_name", " where item_id=".$_GET[item_code_GET]."");
$department=getSVALUE("personnel_basic_info", "PBI_DEPARTMENT", " where PBI_ID=".$_SESSION[PBI_ID]."");
$targeturl="<meta http-equiv='refresh' content='0;$page'>";

if(prevent_multi_submit()){
   
    if(isset($_POST['initiate']))
    {		
		
		
		$_POST['section_id'] = $_SESSION['sectionid'];
		$_POST['company_id'] = $_SESSION['companyid'];
		
		$_POST['entry_by'] = $_SESSION['userid'];
        $_POST['entry_at'] = date('Y-m-d H:s:i');
		
		$ap=$_POST[application_date]; 
		$_POST[application_date]=date('Y-m-d' , strtotime($ap));
		
		
		$pdof=$_POST[preferred_date_of_joining]; 
        $_POST[preferred_date_of_joining]=date('Y-m-d' , strtotime($pdof));	
				
	    $_POST['issue_type'] = 'Office Issue';	
	    $_POST['status'] = 'MANUAL';
		$_POST['requisition_from'] = $department;
	    $_POST['warehouse_id'] = '11';
		$_POST['PBI_ID'] = $_SESSION[PBI_ID];
		$_SESSION['initiate_manpower_requisition']=$_POST[$unique];		
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
		$sd=$_POST[travel_date]; 
		$_POST[travel_date]=date('Y-m-d' , strtotime($sd));		
	    $_POST['issue_type'] = 'Office Issue';	
	    $_POST['status'] = 'MANUAL';
		$_POST['requisition_from'] = $_SESSION["department"];
	    $_POST['warehouse_id'] = '11';
		$_POST['recommend_qty'] = $_POST['qty'];
		$_POST['request_qty'] = $_POST['qty'];
		$_POST['issued_to'] = $_SESSION[PBI_ID];
		$_POST[oi_no]=$_SESSION['initiate_manpower_requisition'];	
        $crud      =new crud($table_deatils);
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';		

        unset($_POST);
        unset($$unique);
    }
    
    
//for modify..................................
if(isset($_POST['modify']))
{
	
	$ap=$_POST[application_date]; 
		$_POST[application_date]=date('Y-m-d' , strtotime($ap));
	$sd=$_POST[trvDate_from]; 
    $_POST[trvDate_from]=date('Y-m-d' , strtotime($sd));
	
	
	$sd=$_POST[trvDate_to]; 
    $_POST[trvDate_to]=date('Y-m-d' , strtotime($sd));
	
	
	$sd=$_POST[departure_date]; 
    $_POST[departure_date]=date('Y-m-d' , strtotime($sd));
	
	
	$sd=$_POST[return_date]; 
    $_POST[return_date]=date('Y-m-d' , strtotime($sd));
	
	
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
	$sd=$_POST[oi_date]; 
    $_POST[oi_date]=date('Y-m-d' , strtotime($sd));
    $crud->update($unique);
    $type=1;
    //echo $targeturl;
    
}

//for Delete..................................
if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}


if (isset($_POST['confirmsave'])){

mysql_query("Update ".$table." set status='PENDING' where ".$unique."=".$_SESSION['initiate_manpower_requisition']."");

$approved_by= find_a_field(''.$table.'','recommend_by',''.$unique.'='.$_SESSION['initiate_manpower_requisition']);
$authorised_person=find_a_field(''.$table.'','authorise_by','trvClaim_id='.$_SESSION['initiate_manpower_requisition']);
$myid=$_SESSION[PBI_ID];
$name=find_a_field('personnel_basic_info','PBI_NAME','PBI_ID='.$myid);
$emailId=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$approved_by);
$emailIds=find_a_field('essential_info','ESS_CORPORATE_EMAIL','PBI_ID='.$authorised_personss);
	
	 
		//if($emailId!=''){
				$to = $emailId;
				$subject = "Manpower Requisition" ;
				$txt1 = "<p>Dear Sir,</p>				
				<p>A Manpower requisition is pending for your Recommendation/Authorization. Please enter Employee Access module to approve the requisition. </p>				<p><strong>Requisition By-</strong> ".$name."</p>				
				<p><b><i>This EMAIL is automatically generated by ERP Software.</i></b></p>";
				
				$txt=$txt1.$txt2.$tr;
				
				
				$from = 'erp@icpbd.com';
				$headers = "";
$headers .= "From: ICP ERP <erp@icpbd.com> \r\n";
$headers .= "Reply-To:" . $from . "\r\n" ."X-Mailer: PHP/" . phpversion();
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";        
mail($to,$subject,$txt,$headers);
								
								
                                unset($_SESSION['initiate_manpower_requisition']);
}

// data query..................................
if(isset($_SESSION[initiate_manpower_requisition]))
{   $condition=$unique."=".$_SESSION[initiate_manpower_requisition];
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}
?>

<?php require_once 'header_content.php'; ?>

<SCRIPT language=JavaScript>
function reload(form){
	var val=form.item_id.options[form.item_id.options.selectedIndex].value;
	self.location='<?=$page;?>?item_code_GET=' + val ;}
</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   
<script>
    $('#lodging_fair_rqst').keyup(function(){
        var transport_fair_rqst;
        var lodging_fair_rqst;
        transport_fair_rqst = parseFloat($('#transport_fair_rqst').val());
        lodging_fair_rqst = parseFloat($('#lodging_fair_rqst').val());
        var total_amount = transport_fair_rqst + lodging_fair_rqst;
        $('#total_amount').val(total_amount.toFixed(2));


    });
</script>   

<?php require_once 'body_content.php'; ?>




                    <div class="col-md-12 col-xs-12">
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
                            <form action="" enctype="multipart/form-data" method="post" name="addem" id="addem" >
                                 <? //require_once 'support_html.php';?>
                                     <table style="width:100%"  cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:50%;">
                                            <div class="form-group">                                            
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 40%">Requisition No<span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <input type="text" id="<?=$unique?>"   required="required" name="<?=$unique?>" value="<? if($_SESSION['initiate_manpower_requisition']>0) { echo  $_SESSION['initiate_manpower_requisition']; 
											
														} else { echo find_a_field($table,'max('.$unique.')+1','1');											
											if($$unique<1) $$unique = 1;}?>" class="form-control col-md-7 col-xs-12"  readonly style="width:100%">
                                                    </div>
                                                </div></td>


                                            <td style="width:50%">
                                            <div class="form-group" style="width: 100%">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 40%">Requisition Date<span class="required">*</span></label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input type="text" id="application_date" readonly  required="required" name="application_date" value="<?php if($_SESSION[initiate_manpower_requisition]>0){ echo date('m/d/y' , strtotime($application_date)); } else { echo ''; } ?>" class="form-control col-md-7 col-xs-12" style="width:100%" >      </div>
                                                </div>
                                            </td></tr>
   
<tr><td style="height:5px"></td></tr>

                      
                 
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Requisition for Department :<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="requisition_for_department" id="requisition_for_department">
                      <option></option>
         <? foreign_relation('department','DEPT_DESC','DEPT_DESC',$requisition_for_department,' 1 order by DEPT_ID asc');?></select></div></div>
                      </td>
                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Requisition for Designation<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<select style="width: 100%" class="select2_single form-control" name="requisition_for_designation" id="requisition_for_designation">
                      <option></option>
         <? foreign_relation('designation','DESG_DESC','DESG_DESC',$requisition_for_designation,'1 order by DESG_DESC');?>
                     </select></div></div>
                 </td>
                  </tr>
                  
                  
                  
                     <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Reason for Requisition (1):<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="reason_for_requisition_1" id="reason_for_requisition_1">
                      <option></option>
         <? foreign_relation('manpower_reason_for_requisition','reason_details','reason_details',$reason_for_requisition_1)?></select></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Reason for Requisition (2):<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<select style="width: 100%" class="select2_single form-control" name="reason_for_requisition_2" id="reason_for_requisition_2">
                      <option></option>
         <? foreign_relation('manpower_reason_for_requisition','reason_details','reason_details',$reason_for_requisition_2)?>
                     </select></div></div>
                 </td>
                  </tr>  
                  
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Preferred Related Experience (1):<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="preferred_related_experience_1" id="preferred_related_experience_1">
                      <option></option>
         <? foreign_relation('manpower_related_experience','related_experience','related_experience',$preferred_related_experience_1)?></select></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Preferred Related Experience (2):<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<select style="width: 100%" class="select2_single form-control" name="preferred_related_experience_2" id="preferred_related_experience_2">
                      <option></option>
         <? foreign_relation('manpower_related_experience','related_experience','related_experience',$preferred_related_experience_2)?>
                     </select></div></div>
                 </td>
                  </tr> 
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Preferred Education:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="preferred_education" id="preferred_education">
                      <option></option>
         <? foreign_relation('edu_qua','EDU_QUA_DESC','EDU_QUA_DESC',$preferred_education);?></select></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Experience (Year):<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<input name="preferred_experience" type="text" id="preferred_experience" value="<?=$preferred_experience?>" class="form-control col-md-7 col-xs-12" /></div></div>
                 </td>
                  </tr>
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Gender:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
         <select name="preferred_gender" id="preferred_gender" style="width: 100%" class="select2_single form-control">
								  <option><?=$preferred_gender?></option>
                                    <option>Male</option>
									<option>Female</option>
									<option>Both</option>
                                  </select></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Age Limit:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<input name="age_limit" type="text" id="age_limit" value="<?=$age_limit?>" class="form-control col-md-7 col-xs-12" /></div></div>
                 </td>
                  </tr>
                  
                  
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">No of Vacancies:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
         <input name="no_of_vacancies" type="text" id="no_of_vacancies" value="<?=$no_of_vacancies?>" class="form-control col-md-7 col-xs-12" /></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Type of Engagement:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<input name="type_of_engagement" type="text" id="type_of_engagement" value="<?=$type_of_engagement?>" class="form-control col-md-7 col-xs-12" /></div></div>
                 </td>
                  </tr>
                  
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Job Location :<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
         <input name="job_location" type="text" id="job_location" value="<?=$job_location?>" class="form-control col-md-7 col-xs-12" /></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Date of Joining :<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<input name="preferred_date_of_joining" type="text" id="preferred_date_of_joining" value="<?php if($_SESSION[initiate_manpower_requisition]>0){ echo date('m/d/y' , strtotime($preferred_date_of_joining)); } else { echo ''; } ?>" class="form-control col-md-7 col-xs-12" /></div></div>
                 </td>
                  </tr>
                  
                  
                  
                  <tr><td style="height:5px"></td></tr>                     
                      <tr><td>
                      <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Key Skills and Abilities :<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
         <textarea name="key_skills" id="key_skills" style="width:100%"><?=$key_skills;?></textarea></div></div>
         <br /><br /><br />

<div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Training/ Project/ Professional Qualification:<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<textarea name="professional_qualification" id="professional_qualification" style="width:100%"><?=$professional_qualification;?></textarea></div></div>
                      </td>                      
                      <td>
                 <div class="form-group">
<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Key Responsible Area's  :<span class="required">*</span></label>
<div class="col-md-6 col-sm-6 col-xs-12">
<textarea style="width:100%" name="key_responsible" id="key_responsible"><?=$key_responsible;?></textarea></div></div>
<br /><br /><br /><div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name" style="width: 40%">Priority<span class="required">*</span></label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12"><select style="width: 100%" class="select2_single form-control" name="Priority" id="Priority">
                      <option></option>
                      <option value="Urgent" <?php if ($Priority=='Urgent') echo 'selected'; else echo '';?> >Urgent</option>
                      <option value="High" <?php if ($Priority=='High') echo 'selected'; else echo '';?>>High</option>
                      <option value="Medium" <?php if ($Priority=='Medium') echo 'selected'; else echo '';?>>Medium</option>
                      <option value="Low" <?php if ($Priority=='Low') echo 'selected'; else echo '';?>>Low</option>                      
                      </select></div></div>

                 </td>
                  </tr> 
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                                 
                  

<tr><td style="height:5px"></td></tr>
                                    <tr>
                                    <td>
                                    <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:40%">Recommended By<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
           <select style="width: 100%" class="select2_single form-control" name="recommend_by" id="recommend_by">
                      <option></option>
                      <?php
                      $result=mysql_query("SELECT  p.*,d.* FROM 
							 
							personnel_basic_info p,
							department d
							 where 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID					 
							  order by p.PBI_NAME");
                      while($row=mysql_fetch_array($result)){  ?>
                          <option  value="<?=$row[PBI_ID]; ?>" <?php if($recommend_by==$row[PBI_ID]) echo 'selected' ?>><?=$row[PBI_ID_UNIQUE]; ?>#><?=$row[PBI_NAME];?>#> (<?=$row[DEPT_SHORT_NAME];?>)</option>
                      <?php } ?></select>
                                        </div></div> 
                                        </td>
                                        
                                        
                                                                          
                                    <td>
                                    <div class="form-group">
         <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width:40%">Authorised By<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
           <select style="width: 100%;" class="select2_single form-control" name="authorise_by" id="authorise_by">
                      <option></option>
                      <?php
                      $result=mysql_query("SELECT  p.*,d.* FROM 
							 
							personnel_basic_info p,
							department d
							 where 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID					 
							  order by p.PBI_NAME");
                      while($row=mysql_fetch_array($result)){  ?>
                          <option  value="<?=$row[PBI_ID]; ?>" <?php if($authorise_by==$row[PBI_ID]) echo 'selected' ?>><?=$row[PBI_ID_UNIQUE]; ?>#><?=$row[PBI_NAME];?>#> (<?=$row[DEPT_SHORT_NAME];?>)</option>
                      <?php } ?></select>
                                    </div></div>
                                    </td>
                                    </tr>

                                        </table>











                                    <div class="form-group" style="margin-left:35%; margin-top: 15px">

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <?php if($_SESSION[initiate_manpower_requisition]){  ?>
                                               <button type="submit" name="modify" class="btn btn-success" onclick='return window.confirm("Are you confirm to Update?");'>Update <?=$title;?></button>
                                               <?php   } else {?>
                                                <button type="submit" name="initiate" onclick='return window.confirm("Are you confirm?");' class="btn btn-primary">Initiate <?=$title;?></button>
                                            <?php } ?>
                                        </div></div>
                                </form></div></div></div>











                    <?php if($_SESSION[initiate_manpower_requisition]){  ?>

                <form id="ismail" name="ismail"  method="post"  class="form-horizontal form-label-left">
                    <table   style="width:100%">
                        <thead>
                        <tr>
                            <td   style="text-align:center">
                                <?php
                                if(isset($_POST[cancel])){
                                    $deletes=mysql_query("Delete From ".$table." where ".$unique."='$_SESSION[initiate_manpower_requisition]' and section_id='$_SESSION[sectionid]' and company_id='$_SESSION[companyid]'");
                                     unset($_SESSION["initiate_manpower_requisition"]); ?>
                                    <meta http-equiv="refresh" content="0;<?=$page;?>">
                                <?php } ?>
                                <button type="submit" name="cancel" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you sure you want to Delete the requisition?");' class="btn btn-danger">Delete the Requisition </button>
                                
                                    <button type="submit" onclick='return window.confirm("Mr. <?php echo $_SESSION["username"]; ?>, Are you Confirm?");' name="confirmsave" class="btn btn-success">Confirm and Finish Requisition </button>
                                


                            </td></tr></table></form>
    <?php } ?>
<?php require_once 'footer_content.php' ?>
<script>
    $(document).ready(function() {
        $('#application_date').daterangepicker({

            singleDatePicker: true,
            calender_style: "picker_4",

        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
    });
</script>



<script>
    $(document).ready(function() {
        $('#preferred_date_of_joining').daterangepicker({

            singleDatePicker: true,
            calender_style: "picker_4",

        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
    });
</script>
