 <?php
require_once 'support_file.php';
$title="Employees";
$now=time();
$unique='PBI_ID';
$unique_field='PBI_ID_UNIQUE';
$table="personnel_basic_info";
$page="hrm_employee_edit.php";
$crud      =new crud($table);
$$unique = $_GET[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";
 $jobinfo="hrm_employee_job_info.php".'?'.$unique.'='.$$unique;
 $targeturlJOBINFO="<meta http-equiv='refresh' content='0;$jobinfo'>";

if(prevent_multi_submit()){
if(isset($_POST['recordNewEmployee']))
{


}

} // prevent multi submit

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}
	
$res='select p.'.$unique.',p.'.$unique.' as Code,p.'.$unique_field.' as Employee_ID,p.PBI_NAME as Name, (select DESG_SHORT_NAME from designation where DESG_ID=p.PBI_DESIGNATION) as designation,
                                 (select DEPT_DESC from department where DEPT_ID=p.PBI_DEPARTMENT) as Department,DATE_FORMAT(p.PBI_DOJ, "%M %d, %Y") as DOJ,p.PBI_EMAIL,p.PBI_MOBILE as mobile,p.PBI_JOB_STATUS as status
                                 from '.$table.' p where p.PBI_JOB_STATUS in ("In Service","Not In Service") order by p.'.$unique;	
$family_member_view='SELECT f.id,f.fi_name as name,r.RELATION_NAME,f.fi_contact_number from hrm_emp_family_info f,relation r where f.fi_relationship=r.RELATION_CODE';                                 

$education_view='SELECT e.id,en.EXAM_NAME as Education,e.ei_passing_year as passed_year,e.ei_grade as Grade,i.institute_name as Institute  from 
edu_exam_title en,hrm_emp_education_info e,institute i where e.ei_education_degree=en.EXAM_CODE and e.ei_institute=i.institute_id'; 
 
 $education_view1='SELECT e.id,en.EXAM_NAME as Education,e.ei_passing_year as Passed_Year,e.Grade,i.institute_name as Institute 
from hrm_emp_education_info e,institute i,edu_exam_title en
 where e.ei_institute=i.institute_id and e.ei_education_degree=en.EXAM_CODE'; 

 $employment_history_view='SELECT em.id,em.eh_company_name as company_name,em.eh_job_title as job_title,em.eh_start_date as start_date,em.eh_end_date as end_date 
 from hrm_emp_employment_history em where 1'; 
$hrm_emp_supervisor_info='SELECT hes.id,concat(p.PBI_ID_UNIQUE," : ",p.PBI_NAME) as supervisor,hes.level,hes.effective_date from hrm_emp_supervisor_info hes, personnel_basic_info p where hes.supervisor=p.PBI_ID';

$supv="SELECT  p.PBI_ID,concat(p.PBI_ID_UNIQUE,' : ',p.PBI_NAME,' : ',d.DEPT_SHORT_NAME) FROM 							 
							personnel_basic_info p,
							department d,
							essential_info e
							 where 
							 p.PBI_JOB_STATUS in ('In Service') and 							 
							 p.PBI_DEPARTMENT=d.DEPT_ID	and
							 p.PBI_ID=e.PBI_ID and 
							 e.ESS_JOB_LOCATION=1 group by p.PBI_ID							 
							  order by p.PBI_NAME";
 $hrm_emp_language_skill='SELECT el.id,concat(l.language_code," : ",l.language_name) as language,p.proficiency_name as proficiency from hrm_emp_language_skill el, languages l,proficiency p where 
 el.ls_language=l.id and el.ls_proficiency=p.id';

$hrm_emp_passport_info='SELECT p.id,c.en_short_name as country,p.pi_passport_no as Passport_no,p.pi_issued_date as Issued_date,p.pi_expire_date as expiry_date from hrm_emp_passport_info p, apps_nationality c where p.pi_country=c.num_code';
$hrm_emp_talent_info ='SELECT ht.id,t.talent_type as talent,ht.pi_talent_details as Talent_Details from hrm_emp_talent_info ht, talent t where ht.pi_talent_ype=t.id';
$hrm_emp_bank_account_info ='SELECT ba.id,b.BANK_NAME,ba.bai_account_no as account_no,ba.bai_account_name as account_name,ba.bai_routing_no as routing_no from hrm_emp_bank_account_info ba, bank b where ba.bai_bank=b.BANK_CODE';
$hrm_emp_social_media_info ='SELECT hsm.id,sm.name,hsm.sm_profile_name as profile_name,hsm.sm_profile_URL as profile_URL from hrm_emp_social_media_info hsm, social_media sm where hsm.sm_id=sm.sm_id';

?>



 <?php require_once 'header_content.php'; ?>
 <script type="text/javascript">
     function DoNavPOPUP(lk)
     {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=990,height=600,left = 230,top = 5");}
 </script>

 <style>
     input[type=text]{font-size: 11px;}
     select{font-size: 11px;}.rcom{color:red}
 </style>
 <?php require_once 'body_content.php'; ?>

 <?=$crud->report_templates_with_add_new($res,$title,'12',$action=$_SESSION["userlevel"],$create=0);?>
<?=$html->footer_content();?>
