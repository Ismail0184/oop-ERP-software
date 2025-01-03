 <?php
require_once 'support_file.php';
$title="Holy Day Setup";

$now=time();
$unique='id';
$unique_field='holy_day';
$table="salary_holy_day";
$page="hrm_setup_holy_day.php";
$crud      =new crud($table);
$$unique = $_GET[$unique];
$targeturl="<meta http-equiv='refresh' content='0;$page'>";

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))

//for insert..................................
{    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        unset($_POST);
        unset($$unique);
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
if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}}

// data query..................................
if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}
?>



<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content.php'; ?>

 <?php if(!isset($_GET[$unique])){ ?>
     <!-------------------list view ------------------------->
     <div class="col-md-7 col-sm-12 col-xs-12">
         <div class="x_panel">
             <div class="x_title">
                 <h2>List of <?=$title;?></h2>
                 <div class="clearfix"></div>
             </div>

             <div class="x_content">
                 <? 	$res='select '.$unique.','.$unique.' as Code,'.$unique_field.', reason from '.$table.' order by '.$unique.' desc';
                 echo $crud->link_report_popup($res,$link);?>
                 <?=paging(10);?>
             </div>

         </div></div>
     <!-------------------End of  List View --------------------->
 <?php } ?>
 <!---page content----->

                    <!-- input section-->
                    <div class="col-md-5 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?=$title;?></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br />

                                <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" style="font-size:11px">
                                    <? require_once 'support_html.php';?>
                                    
                                    <div class="form-group" style="display: none">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name"><?=$unique?><span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="<?=$unique?>" style="width:100%"   name="<?=$unique?>" value="<?=$$unique?>" class="form-control col-md-7 col-xs-12" >
                                    </div></div>                                   
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Day / Date<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="date" id="<?=$unique_field?>" style="width:100%; font-size:11px"  required   name="<?=$unique_field?>" value="<?=$$unique_field?>" class="form-control col-md-7 col-xs-12" >
                                    </div></div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Holy Day Reason<span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="reason" style="width:100%;font-size:11px"  required   name="reason" value="<?=$reason?>" class="form-control col-md-7 col-xs-12" >
                                        </div></div>

                                  
                                        <?php if($_GET[$unique]){  ?>                                            
                                            <div class="form-group" style="margin-left:40%">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="modify" id="modify" class="btn btn-success">Modify</button>
                                            </div></div>
                                            <? if($_SESSION['userid']=="10019"){?>                                            
                                             <div class="form-group" style="margin-left:40%;">
                                             <div class="col-md-6 col-sm-6 col-xs-12">
                                             <input  name="delete" type="submit" class="btn btn-danger" id="delete" value="Delete"/>
                                             </div></div>                                             
                                             <? }?>                                         
                                            <?php } else {?>                                           
                                            <div class="form-group" style="margin-left:40%">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="record" id="record"  class="btn btn-primary">Add Holy Day </button>
                                            </div></div>                                                                                        
                                            <?php } ?> 
                                </form>
                            </div>
                        </div>
                    </div>


 <?=$html->footer_content();mysqli_close($conn);?>

