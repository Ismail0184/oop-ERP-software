<?php require_once 'support_file.php';
$title='Material Report';
$page='mm_select_report.php';

$sql_item_id="SELECT i.item_id,concat(i.item_id,' : ',i.finish_goods_code,' : ',i.item_name,' (',sg.sub_group_name,')') FROM  item_info i,
item_sub_group sg,
item_group g WHERE  i.sub_group_id=sg.sub_group_id and
sg.group_id=g.group_id
order by i.item_name";
$report_id = @$_REQUEST['report_id']; ?>



<?php require_once 'header_content.php'; ?>
 <SCRIPT language=JavaScript>
function reload(form)
{
	var val=form.report_id.options[form.report_id.options.selectedIndex].value;
	self.location='<?=$page;?>?report_id=' + val ;
}
</script>
<style>
    input[type=text]{
        font-size: 11px;
    }

</style>
<?php require_once 'body_content_nva_sm.php'; ?>

<form class="form-horizontal form-label-left" method="POST" action="material_reportview.php" style="font-size: 11px" target="_blank">
    <div class="col-md-5 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <?=$crud->select_a_report(15);?>
            </div>
        </div>
    </div>
    <div class="col-md-7 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><small class="text-danger">field marked with * are mandatory</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <?php if ($report_id=='1501001'):?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required text-danger">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="select2_single form-control" style="width:100%" required tabindex="-1"  name="status"  id="status">
                                <option></option>
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Order by <span class="required text-danger">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="select2_single form-control" style="width:100%" required tabindex="-1"  name="order_by"  id="order_by">
                                <option></option>
                                <?php
                                $sql = mysqli_query($conn, "SHOW COLUMNS FROM item_info like 'item_ids'");
                                while($row=mysqli_fetch_assoc($sql)){ ?>
                                    <option><?=$row['Field']?></option>
                                <?php } ?>
                                <option></option>
                                <option value="serial">Item serial</option>
                                <option value="item_id" selected>ERP Id</option>
                                <option value="finish_goods_code">Custom Code</option>
                                <option value="item_name">Item Name</option>
                            </select>
                        </div>
                    </div>
                <?php elseif ($report_id=='1502001'):  ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Item Name:</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select class="select2_single form-control" style="width: 100%" tabindex="-1" name="item_id" id="item_id" required>
                                <option value="0">All</option>
                                <?=advance_foreign_relation($sql_item_id,'');?>
                            </select>
                        </div>
                    </div>

                <?php  else:  ?>
                    <h5 class="text-danger" style="text-align: center">Please select a report from left</h5>
                <?php endif; ?>

                <?php if ($report_id>0): ?>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            <a href="<?=$page?>"  class="btn btn-danger" style="font-size: 12px"><i class="fa fa-close"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary" name="getstarted" style="font-size: 12px"><i class="fa fa-file"></i> Generate Report</button>
                        </div>
                    </div>
                <?php  else:  ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>
<?=$html->footer_content();?>
