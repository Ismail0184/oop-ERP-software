<?php
require_once 'support_file.php';
$title='Create Other Options';
$now=time();
$unique='id';
$unique_field='other_option_id';
$table='dev_other_options';
$page="developer_module_create_other_options.php";
$crud      =new crud($table);

$jv_no=mysqli_query($conn,"SELECT MAX(".$unique_field.") AS MAXCODE FROM ".$table." where 1");
$jv_noROW=mysqli_fetch_array($jv_no);
$GET_sub_menu_id=$jv_noROW[MAXCODE]+1;

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))
{
$$unique = $_POST[$unique];
if(isset($_POST['record']))
{
    $_POST['module_id'] = find_a_field('dev_main_menu','module_id','main_menu_id='.$_POST['main_menu_id']);
    $_POST['sub_url'] = $_POST['sub_url'].'.php';
    $_POST['status']=1;
    $crud->insert();
    unset($_POST);
}}

if(isset($_POST['modify'])){
    $crud->update($unique);
    unset($_POST);
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}
}

if(isset($_POST['cancel'])){echo "<script>window.close(); </script>";}
if(isset($_GET[$unique]))
{   $condition=$unique."=".$_GET[$unique];
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data))
    { $$key=$value;}}

$query='Select mm.id,mm.sub_menu_id,mm.sub_menu_name,mm.sub_url,mm.faicon,dm.modulename as module,mm.table_name,IF(mm.status=1, "Active", "Inactive") as status from '.$table.' mm, dev_modules dm
where mm.module_id=dm.id
order by dm.module_id,mm.sl';
?>



<?php require_once 'header_content.php'; ?>
<style> input[type=text] {font-size:11px}
    </style>
<?php if(isset($_GET[$unique])): 
 require_once 'body_content_without_menu.php'; else :  
 require_once 'body_content.php'; endif;  ?>
<?php if(isset($_GET[$unique])): ?>
<div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?=$title;?></h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <div class="input-group pull-right"></div>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
							<?php else: ?>                            
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add New Record
          <button class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
          </h5>
        </div>
        <div class="modal-body">
        <?php endif; ?>

<form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" style="font-size: 11px">
                                    <? require_once 'support_html.php';?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Under Main Menu<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="select2_single form-control" style="width:100%; font-size: 11px" tabindex="-1" required="required" id="main_menu_id"  name="main_menu_id">
                                                    <option></option>
                                                    <?php foreign_relation('dev_main_menu', 'main_menu_id', 'CONCAT(main_menu_id," : ", main_menu_name)', $main_menu_id, 'status in (\'1\')', 'order by module_id,main_menu_name'); ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Sub Menu ID <span class="required text-danger">*</span></label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input type="text" id="sub_menu_id" style="width:100%"  required readonly  name="sub_menu_id" value="<?=($sub_menu_id>0)? $sub_menu_id:$GET_sub_menu_id?>" class="form-control col-md-7 col-xs-12" >
                                                </div>
                                        </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Sub Menu Name <span class="required text-danger">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="sub_menu_name" style="width:100%"  required  name="sub_menu_name" value="<?=$sub_menu_name?>" class="form-control col-md-7 col-xs-12" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Sub Module Details <span class="required text-danger">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="sub_menu_details" style="width:100%"   name="sub_menu_details" value="<?=$sub_menu_details;?>" class="form-control col-md-7 col-xs-12" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Sub URL <span class="required text-danger">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="sub_url" style="width:100%"   name="sub_url" value="<?=$sub_url;?>" class="form-control col-md-7 col-xs-12" >
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Fa Icon <span class="required text-danger">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="faicon" style="width:100%"   name="faicon" value="<?=$faicon;?>" class="form-control col-md-7 col-xs-12" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Table Name <span class="required text-danger">*</span></label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input type="text" id="table_name" style="width:100%" <?php if($_GET[$unique]):  ?> readonly <?php endif;?>  required   name="table_name" value="<?=$table_name?>" class="form-control col-md-7 col-xs-12" >
                                                </div>
                                        </div>
                                            <?php if($_GET[$unique]):  ?>
                                    <div class="form-group" style="width: 100%">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Status</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="select2_single form-control" style="width:100%; font-size:11px" name="status" id="status">
                                                <option value="1"<?=($status=='1')? ' Selected' : '' ?>>Active</option>
                                                <option value="0"<?=($status=='0')? ' Selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                            </div>
                        <?php endif ?>    

                                    <hr> 

                                    <?php if($_GET[$unique]):  ?>
                                    <div class="form-group" style="margin-left:30%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <button type="submit" name="cancel" id="cancel" style="font-size:12px"  class="btn btn-danger">Cancel</button>
                                        <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-primary">Modify</button>
                                        </div></div>
                                    <?php else : ?>
                                        <div class="form-group" style="margin-left:40%">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <button type="submit" name="cancel" id="cancel" style="font-size:12px" data-dismiss="modal"  class="btn btn-danger">Cancel</button>
                                                <button type="submit" name="record" id="record" style="font-size:12px" class="btn btn-primary">Record</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>     
                                    </form></div></div></div><?php if(!isset($_GET[$unique])): ?></div><?php endif; ?>

                   
                           

<?php if(!isset($_GET[$unique])):?> 
<?=$crud->report_templates_with_add_new($query,$title,12,$action=$_SESSION["userlevel"],$create=1);?>  
<?php endif; ?>
<?=$html->footer_content();mysqli_close($conn);?>
<?php ob_end_flush();
ob_flush(); ?>                            
