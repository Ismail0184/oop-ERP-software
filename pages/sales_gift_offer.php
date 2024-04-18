<?php require_once 'support_file.php'; ?>
<?=(check_permission(basename($_SERVER['SCRIPT_NAME']))>0)? '' : header('Location: dashboard.php');
$now=time();
$unique='id';
$unique_field='offer_name';
$table="sale_gift_offer";
$page="sales_gift_offer.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];
$GetUnique = @$_GET[$unique];
$title='Trade Scheme';

if($sectionid=='400000'){
    $sec_com_connection=' and 1';
    $sec_com_connection_wa=' and 1';
} else {
    $sec_com_connection=" and ts.company_id='".$_SESSION['companyid']."' and ts.section_id in ('400000','".$_SESSION['sectionid']."')";
    $sec_com_connection_wa=" and company_id='".$_SESSION['companyid']."' and section_id in ('400000','".$_SESSION['sectionid']."')";
}

if(prevent_multi_submit()){
if(isset($_POST[$unique_field]))

//for insert..................................
{    $$unique = $_POST[$unique];
    if(isset($_POST['record']))
    {
        $crud->insert();
        $type=1;
        $msg='New Entry Successfully Inserted.';
        //unset($_POST);
        unset($$unique);
    }

//for modify..................................
if(isset($_POST['modify']))
{
    $_POST['edit_at']=time();
    $_POST['edit_by']=$_SESSION['userid'];
    $crud->update($unique);
    $type=1;
    //echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}

//for Delete..................................
if(isset($_POST['deleteRequest']))
{   $condition=$unique."=".$$unique;
    $crud->delete($condition);
    unset($$unique);
    $type=1;
    $msg='Successfully Deleted.';
    echo "<script>self.opener.location = '$page'; self.blur(); </script>";
    echo "<script>window.close(); </script>";
}}

} // prevent multi submit

// data query..................................
if(isset($$unique)) {
    $condition = $unique . "=" . $$unique;
    $data = db_fetch_object($table, $condition);
    $array = (array)$data;
    foreach ($array as $key => $value) {
        $$key = $value;
    }
}

$offer_name = @$offer_name;
$dealer_type = @$dealer_type;
$start_date = @$start_date;
$end_date = @$end_date;
$item_qty = @$item_qty;
$gift_id = @$gift_id;
$gift_qty = @$gift_qty;
$gift_type = @$gift_type;
$calculation = @$calculation;
$item_id = @$item_id;

$PostOfferName = @$_POST['offer_name'];
$PostDealerType = @$_POST['dealer_type'];
$PostStartDate = @$_POST['start_date'];
$PostEndDate = @$_POST['end_date'];
$PostGiftType = @$_POST['gift_type'];

$sql = "SELECT typeshorname, typedetails from distributor_type
                        where 1 order by typedetails";
$sql_item = "SELECT item_id, concat(item_id,' : ',finish_goods_code,' : ', item_name) from item_info
                        where product_nature in ('Salable','Both') order by finish_goods_code";
$res="SELECT ts.id,ts.offer_name,concat(ts.start_date,' to ',ts.end_date) as 'Duration',concat(i.item_id,' : ',i.finish_goods_code,' : ',i.item_name) as Buy_item,ts.item_qty as Buy_qty,(select item_name from item_info where item_id=ts.gift_id) as Get_item_name,ts.gift_qty,ts.gift_type from ".$table." ts, item_info i where ts.item_id=i.item_id ".$sec_com_connection." order by ts.id desc";
?>



<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?id="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=750,height=600,left = 350,top = -1");}
    </script>
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
        <h5 class="modal-title">Add New TS
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
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Trade Scheme Name</label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="text" id="offer_name" style="width:100%; font-size: 12px"  required   name="offer_name" value="<?=($GetUnique>0)? $offer_name : $PostOfferName; ?>" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Offer for Customer Type <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="dealer_type" id="dealer_type">
                    <option></option>
                    <?=advance_foreign_relation($sql,($GetUnique>0)? $dealer_type : $PostDealerType);?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Start Date <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="date" id="start_date" style="width:100%; font-size: 11px"  required   name="start_date" value="<?=($GetUnique>0)? $start_date : $PostStartDate; ?>" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">End Date <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="date" id="end_date" style="width:100%; font-size: 11px"  required   name="end_date" value="<?=($GetUnique>0)? $end_date : $PostEndDate; ?>" class="form-control col-md-7 col-xs-12" >
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Buy Item Name <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="item_id" id="item_id">
                    <option></option>
                    <?=advance_foreign_relation($sql_item,$item_id);?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Buy Qty: <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="text" id="item_qty" style="width:100%; font-size: 11px; left:left" name="item_qty" value="<?=$item_qty;?>" class="form-control col-md-7 col-xs-12" placeholder="Buy Qty" title="Buy Qty">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Get Item Name <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select class="select2_single form-control" style="width: 100%;" tabindex="-1" required="required" name="gift_id" id="gift_id">
                    <option></option>
                    <?=advance_foreign_relation($sql_item,$gift_id);?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Get Qty / Cash amount: <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <input type="number" id="gift_qty" style="width:100%; font-size: 11px; left:left" name="gift_qty" value="<?=$gift_qty;?>" class="form-control col-md-7 col-xs-12" placeholder="Get Qty / Amount" title="Get Qty / Amount" step="any">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Gift Type: <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select style="width: 100%" class="select2_single form-control" name="gift_type" id="gift_type">
                    <option></option>
                    <?=$sql11="select type,type from sales_TS_type where status>0"?>
                    <?=advance_foreign_relation($sql11,($GetUnique>0)? $gift_type : $PostGiftType);?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name" style="width: 30%">Calculation Mode: <span class="required text-danger">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12" style="width: 60%">
                <select style="width: 100%" class="select2_single form-control" name="calculation" id="calculation">
                    <option></option>
                    <option value="1" <?php if($calculation=='Auto') echo 'selected' ?>>Auto</option>
                    <option value="0" <?php if($calculation=='Manual') echo 'selected' ?>>Manual</option>
                </select></div>
        </div>

        <?php if($GetUnique):  ?>
            <div class="form-group" style="margin-left:40%">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php  $has_transaction = find_a_field('sale_do_details','COUNT(gift_id)','gift_id='.$GetUnique); if($has_transaction>0) { } else { ?>
                    <button type="submit" class="btn btn-danger" name="deleteRequest" style="font-size: 12px"><i class="fa fa-eraser"></i> Delete</button>
                    <?php } ?>
                    <button type="submit" name="modify" id="modify" style="font-size:12px" class="btn btn-primary">Update <i class="fa fa-edit"></i></button>
                </div>
            </div>
        <?php else : ?>
            <div class="form-group" style="margin-left:40%">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button type="submit" name="record" id="record"  style="font-size:12px" class="btn btn-primary">Add New TS</button></div></div> <?php endif; ?>
    </form>
    </div></div></div><?php if(!isset($GetUnique)): ?></div><?php endif; ?>
<?php if(!isset($GetUnique)):?>
<?=$crud->report_templates_with_add_new($res,$title,12,$action=$_SESSION["userlevel"],$create=1,'');?>
<?php endif; ?>
<?=$html->footer_content();
mysqli_close($conn);?>