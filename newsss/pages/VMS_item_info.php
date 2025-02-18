<?php require_once 'support_file.php';?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
ob_start();
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));

$title='Item Info';
$unique='item_id';
$unique_field='item_name';
$table='item_info';
$page="VMS_item_info.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];


if(isset($_POST[$unique_field]))
{ $$unique = $_POST[$unique];
//for Record..................................
    $_POST['item_name'] = str_replace('"',"``",@$_POST['item_name']);
    $_POST['item_name'] = str_replace("'","`",@$_POST['item_name']);
	$_POST['item_description'] = str_replace(Array("\r\n","\n","\r"), " ", @$_POST['item_description']);
    $_POST['item_description'] = str_replace('"',"``",@$_POST['item_description']);
    $_POST['item_description'] = str_replace("'","`",@$_POST['item_description']);
    if(isset($_POST['record']))
    {
        $_POST['entry_at']=time();
        $_POST['entry_by']=$_SESSION['userid'];
        $min=number_format(@$_POST['sub_group_id'] + 1, 0, '.', '');
        $max=number_format(@$_POST['sub_group_id'] + 10000, 0, '.', '');
        $_POST[$unique]=number_format(next_value('item_id','item_info','1',$min,$min,$max), 0, '.', '');
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        unset($_POST);
        unset($$unique);}



//for Modify..................................
    if(isset($_POST['modify']))
    {   $_POST['item_name'] = str_replace('"',"``",$_POST['item_name']);
        $_POST['item_name'] = str_replace("'","`",$_POST['item_name']);
        $_POST['item_description'] = str_replace(Array("\r\n","\n","\r"), " ", $_POST['item_description']);
        $_POST['item_description'] = str_replace('"',"``",$_POST['item_description']);
        $_POST['item_description'] = str_replace("'","`",$_POST['item_description']);
		$_POST['edit_at']=time();
        $_POST['edit_by']=$_SESSION['userid'];
        $crud->update($unique);
        $type=1;
		echo "<script>self.opener.location = '$page'; self.blur(); </script>";
        echo "<script>window.close(); </script>";}



//for Delete..................................
if(isset($_POST['delete']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
}}



if(isset($$unique))
{   $condition=$unique."=".$$unique;
    $data=db_fetch_object($table,$condition);
    while (list($key, $value)=each($data)){ $$key=$value;}}

$finish_goods_code  = @$finish_goods_code;
$item_name          = @$item_name;
$SD                 = @$SD;
$SD_percentage      = @$SD_percentage;
$VAT                = @$VAT;
$VAT_percentage     = @$VAT_percentage;
$VAT_item_group     = @$VAT_item_group;
$H_S_code           = @$H_S_code;
$item_id            = @$item_id;


$res='select
                                i.'.$unique.',
                                i.'.$unique.' as code,
                                i.finish_goods_code as FG_Code,
                                i.'.$unique_field.',
                                sg.sub_group_name,
								g.group_name,
                                i.unit_name,
								ib.brand_name,
								i.status
                                from
                                '.$table.' i,
                                item_sub_group sg,
								item_group g,
								item_brand ib

                                WHERE

                                i.sub_group_id=sg.sub_group_id and
								sg.group_id=g.group_id and
                                ib.id=i.brand_id
                                order by g.group_id,sg.sub_group_id,i.'.$unique;

								 $sql = "SELECT sg.sub_group_id,concat(sg.sub_group_id,' : ',sg.sub_group_name,' : ',g.group_name) FROM
                        item_sub_group sg,
                        item_group g
                        where
                        sg.group_id=g.group_id
                        order by sg.sub_group_id";
$sql_unit="select unit_name, unit_name from unit_management";
$sql_item_type="Select item_type,item_type from item_type";
$sql_brand="Select id,brand_name from item_brand";
$sql_brand_category="Select category_name,category_name from brand_category"
?>
<?php require_once 'header_content.php'; ?>
<style>
    input[type=text]{
        font-size: 11px;
    }
</style>
<?php if(isset($_GET[$unique])):
 require_once 'body_content_without_menu.php'; else :
 require_once 'body_content.php'; endif;  ?>


<?php if(isset($_GET[$unique])): ?>
<div class="col-md-5 col-sm-12 col-xs-12">
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
            <form  name="addem" id="addem" class="form-horizontal form-label-left" style="font-size: 11px" method="post">
                <? require_once 'support_html.php';?>

                <div class="form-group">
                     <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Custom Code<span class="required">*</span></label>
                     <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                         <input type="text" readonly name="finish_goods_code" id="finish_goods_code" value="<?=$finish_goods_code?>" style="width:100%; font-size: 12px" class="form-control col-md-7 col-xs-12" required />
                     </div>
                 </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Item Name<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                        <input type="text" id="item_name" readonly style="width:100%; font-size: 12px"  required   name="item_name" value="<?=$item_name;?>" class="form-control col-md-7 col-xs-12" >
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">SD :</label>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                        <input type="text" id="SD_percentage" style="width:48%; font-size: 11px; float:left" name="SD_percentage" value="<?=$SD_percentage;?>" class="form-control col-md-7 col-xs-12" placeholder="Percentage" title="Percentage">
                        <input type="text" id="SD" style="width:48%; font-size: 11px; float:right" name="SD" value="<?=$SD;?>" class="form-control col-md-7 col-xs-12" placeholder="SD Price" title="SD Price">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">VAT :</label>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                        <input type="text" id="VAT_percentage" style="width:48%; font-size: 11px; float:left" name="VAT_percentage" value="<?=$VAT_percentage;?>" class="form-control col-md-7 col-xs-12" placeholder="Percentage">
                        <input type="text" id="VAT" style="width:48%; font-size: 11px; float:right" name="VAT" value="<?=$VAT;?>" class="form-control col-md-7 col-xs-12" placeholder="VAT Price">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">VAT Item Group:<span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                        <select style="width: 100%" class="select2_single form-control" name="VAT_item_group" id="VAT_item_group">
                            <option></option>
                            <?php foreign_relation('VAT_item_group', 'group_id', 'group_name', $VAT_item_group, 'status=1'); ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" style="width: 30%">H.S Code</label>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                        <select class="select2_single form-control" style="width: 100%" tabindex="-1" name="H_S_code" >
                            <option></option>
                            <?php foreign_relation('item_tariff_master', 'id', 'CONCAT(id," : ", H_S_code)', $H_S_code, '1'); ?>
                        </select>
                    </div>
                </div>

                <hr/>

                <?php $GetItemId = @$_GET[$unique]; if($GetItemId):  ?>
                    <div class="form-group" style="margin-left:40%">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" name="modify" id="modify" class="btn btn-primary">Modify Item</button>
                        </div></div>
                <?php else : ?>
                    <div class="form-group" style="margin-left:40%">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" name="record" id="record"  style="font-size:12px" class="btn btn-primary">Add New Item</button>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
      </div>
    </div>
    <?php if(!isset($_GET[$unique])): ?></div><?php endif; ?>
<?php if(!isset($_GET[$unique])):?>
<?=$crud->report_templates_with_add_new($res,$title,12,$action=$_SESSION["userlevel"],$create=1,'');?>
<?php endif; ?>
<?=$html->footer_content();mysqli_close($conn);?>
<?php ob_end_flush();
ob_flush(); ?>
