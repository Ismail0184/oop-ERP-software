<?php require_once 'support_file.php';

$companyid=@$_SESSION['companyid'];
$sectionid = @$_SESSION['sectionid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
} else {
    $sec_com_connection=" and j.company_id='".$_SESSION['companyid']."' and j.section_id in ('400000','".$_SESSION['sectionid']."')";
}
$date_checking = find_all_field('dev_software_data_locked','','status="LOCKED" and section_id="'.$_SESSION['sectionid'].'" and company_id="'.$_SESSION['companyid'].'"');
if($date_checking>0) {
    $lockedStartInterval = @$date_checking->start_date;
    $lockedEndInterval = @$date_checking->end_date;
} else
{
    $lockedStartInterval = '';
    $lockedEndInterval = '';
}

$itemId = isset($_POST['item_id']) && $_POST['item_id'] > 0 ? $_POST['item_id'] : null;

if (isset($itemId)) {
    $itemIdConn = ' and i.item_id=' . $itemId;
} else {
    $itemIdConn = '';
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript">
        function hide()
        {document.getElementById("pr").style.display = "none";}
    </script>
    <style>
        #customers {}
        #customers td {}
        #customers tr:ntd-child(even)
        {background-color: #f0f0f0;}
        #customers tr:hover {background-color: #f5f5f5;}
        td{}

        .span {
            background-color: #f0c4f5; /* choose your preferred color */
            color: #333; /* text color */
            padding: 2px 5px; /* optional padding */
            border-radius: 3px; /* optional rounded corners */
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
</head>
<body style="font-family: "Gill Sans", sans-serif;">
<div id="pr" style="margin-left:48%">
    <div align="left">
        <form id="form1" name="form1" method="post" action="">
            <p><input name="button" type="button" onclick="hide();window.print();" value="Print" /></p>
        </form>
    </div>
</div>


<?php if ($_POST['report_id']=='1501001'):
    $sql="SELECT i.item_id,i.item_id,i.finish_goods_code as custom_code,i.item_name,i.consumable_type,i.product_nature,i.unit_name,b.brand_name,i.d_price,i.t_price,i.m_price,FORMAT(i.production_cost,2) as pro_cost,i.material_cost,
FORMAT(i.SD,3) as SD,i.SD_percentage as 'SD (%)',FORMAT(i.VAT,3) as VAT,i.VAT_percentage as 'VAT (%)',(select group_name from VAT_item_group where i.VAT_item_group=group_id) as VAT_item_group,hs.H_S_code,sg.sub_group_name,g.group_name,s.section_name as branch
from item_info i,
item_sub_group sg,
item_group g,
item_tariff_master hs,
company s,
item_brand b
where
i.section_id=s.section_id and 
i.H_S_code=hs.id and
i.brand_id=b.brand_id and
i.sub_group_id=sg.sub_group_id and
sg.group_id=g.group_id and
i.status in ('".$_POST['status']."') order by i.".$_POST['order_by']."";
echo reportview($sql,'Item Information','99',0,'',0); ?>


<?php elseif ($_POST['report_id']=='1502001'):
    $sql="SELECT i.item_id,i.item_id,i.finish_goods_code as custom_code,i.item_name,ip.startDate as strat_from,ip.endDate as end_to,ip.cogsPrice,ip.dPrice,ip.tprice,ip.mPrice,ip.vatChargeablePrice,ip.status
from item_info_price_level_record ip,
item_sub_group sg,
item_group g,
item_info i
where
i.sub_group_id=sg.sub_group_id and
sg.group_id=g.group_id and 
i.item_id = ip.item_id ".$itemIdConn." order by ip.id";
    echo reportview($sql,'Material Price Level','99',0,'',0); ?>


<?php else: ?>
    <h5 style="text-align: center; color: red">You have to select a report.</h5>
<?php endif; ?>



</body>
</html>
