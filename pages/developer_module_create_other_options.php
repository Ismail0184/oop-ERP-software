<?php
require_once 'support_file.php';
$title='Create Meta Data';
$now=time();
$unique='id';
$unique_field='meta_name';
$table='dev_usage_control_metas';
$page="developer_module_create_other_options.php";
$crud      =new crud($table);


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

$query='SELECT id,meta_name,meta_key,meta_value,status from dev_usage_control_metas';
?>



<?php require_once 'header_content.php'; ?>
<style> input[type=text] {font-size:11px}</style>
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
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Meta Name <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="sub_menu_name" style="width:100%"  required  name="meta_name" value="<?=$meta_name?>" class="form-control col-md-7 col-xs-12" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Meta Key <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="sub_menu_details" style="width:100%"   name="meta_key" value="<?=$meta_key;?>" class="form-control col-md-7 col-xs-12" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Meta Value <span class="required text-danger">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="sub_url" style="width:100%"   name="meta_value" value="<?=$meta_value;?>" class="form-control col-md-7 col-xs-12" >
                                    </div>
                                </div>

                                <?php if($_GET[$unique]):  ?>
                                    <div class="form-group" style="width: 100%">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Status</label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="select2_single form-control" style="width:100%; font-size:11px" name="status" id="status">
                                                <option value="active"<?=($status=='active')? ' Selected' : '' ?>>Active</option>
                                                <option value="postpone"<?=($status=='postpone')? ' Selected' : '' ?>>Postpone</option>
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
                                        </div>
                                    </div>
                                <?php else : ?>

                                    <div class="form-group" style="margin-left:40%">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="submit" name="cancel" id="cancel" style="font-size:12px" data-dismiss="modal"  class="btn btn-danger">Cancel</button>
                                            <button type="submit" name="record" id="record" style="font-size:12px" class="btn btn-primary">Record</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if(!isset($_GET[$unique])): ?>
            </div>
        <?php endif; ?>
            <?php if(!isset($_GET[$unique])):?>
                <?=$crud->report_templates_with_add_new($query,$title,12,$action=$_SESSION["userlevel"],$create=1);?>
            <?php endif; ?>
            <?=$html->footer_content();mysqli_close($conn);?>
            <?php ob_end_flush();ob_flush(); ?>
