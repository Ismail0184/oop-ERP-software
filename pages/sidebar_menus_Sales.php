<?php
require_once ('support_file.php');
$companyid = @$_SESSION['companyid'];
$sectionid = @$_SESSION['sectionid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
    $sec_com_connection_wa=' and 1';
} else {
    $sec_com_connection=" and d.company_id='".$companyid."' and d.section_id in ('400000','".$sectionid."')";
    $sec_com_connection_wa=" and company_id='".$companyid."' and section_id in ('400000','".$sectionid."')";
}
$so_target=find_a_field('ims_monthly_target_master','count(id)','status="UNCHECKED"');
$stock_transfer=find_a_field('ims_transfer_from_super_DB_master','count(do_no)','status="UNCHECKED"');

$creditLimitRequested = find_a_field('dealer_credit_limit_request','count(id)','status="PENDINGS"'.$sec_com_connection_wa.'');
$sales_IMS_Management=$so_target+$stock_transfer;

$pendingCustomerFromApp = find_a_field('app_get_customer_data','count(id)','status="PENDING"');
$totalAppDate = $pendingCustomerFromApp;

?>

<div class="menu_section">
    <h3></h3>
    <ul class="nav side-menu">
        <li><a href="dashboard.php"><i class="fa fa-home"></i><?php
                if($_SESSION['language']=='Bangla') {?>
                    হোম <?php } else if($_SESSION['language']=='English') {?> Home
                <?php } ?>
            </a>
        </li>
        <?php
        $result = mysqli_query($conn, "SET NAMES utf8");//the main trick
        if($_SESSION['language']=='Bangla') {
        $result="Select
		pmm.*,
		dmm.faicon as iconmain,
		dmm.main_menu_name_BN as main_menu_name,
		dmm.sl,
		dmm.url as main_url
		from
		user_permission_matrix_main_menu pmm,
		dev_main_menu dmm
		where
		pmm.main_menu_id=dmm.main_menu_id and
		pmm.user_id='".$_SESSION["userid"]."' and
		pmm.company_id='".$_SESSION['companyid']."'  and
		dmm.module_id='".$_SESSION['module_id']."' and
		dmm.status=1 and pmm.status=1
		order by dmm.sl";
        } else if($_SESSION['language']=='English') {
            $result = "Select
		pmm.*,
		dmm.faicon as iconmain,
		dmm.main_menu_name,
		dmm.sl,
		dmm.url as main_url
		from
		user_permission_matrix_main_menu pmm,
		dev_main_menu dmm
		where
		pmm.main_menu_id=dmm.main_menu_id and
		pmm.user_id='" . $_SESSION["userid"] . "' and
		pmm.company_id='" . $_SESSION['companyid'] . "'  and
		dmm.module_id='" . $_SESSION['module_id'] . "' and
		dmm.status=1 and pmm.status=1
		order by dmm.sl";
        }
        $master_result=mysqli_query($conn, $result);
        while($mainrow=mysqli_fetch_object($master_result)):  ?>
            <?php if($mainrow->main_menu_name!="Sales Reports"): ?>
                <li><a href="#"><i class="<?=$mainrow->iconmain;?>"></i><?=$mainrow->main_menu_name;?>
                        <?php if($mainrow->main_menu_id=="10024") if($totalAppDate>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$totalAppDate.' </span>]'?><?php } else {echo'';} ?>
                        <?php if($mainrow->main_menu_id=="10012") if($creditLimitRequested>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$creditLimitRequested.' </span>]'?><?php } else {echo'';} ?>
                        <?php if($mainrow->main_menu_id=="10024") if($sales_IMS_Management>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$sales_IMS_Management.' </span>]'?><?php } else {echo'';} ?>
						<?php if($mainrow->main_url=='#'):?><span class="fa fa-chevron-down"></span><?php endif; ?></a>
                    <ul class="nav child_menu">
                        <?php
                        if($_SESSION['language']=='Bangla') {
                        $result="Select
						psm.*,
						dsm.sub_menu_id,
						dsm.sub_menu_name_BN as sub_menu_name,
						dsm.sub_url
						from
						user_permission_matrix_sub_menu psm,
						dev_sub_menu dsm
						where
						dsm.sub_menu_id=psm.sub_menu_id and
						psm.user_id='".$_SESSION["userid"]."' and
						psm.company_id='".$_SESSION['companyid']."' and
						psm.main_menu_id='".$mainrow->main_menu_id."' and
						dsm.module_id='".$_SESSION['module_id']."' and
						dsm.status=1 and psm.status=1
						order by dsm.sl";
                        } else if($_SESSION['language']=='English') {
                            $result = "Select
						psm.*,
						dsm.sub_menu_id,
						dsm.sub_menu_name,
						dsm.sub_url
						from
						user_permission_matrix_sub_menu psm,
						dev_sub_menu dsm
						where
						dsm.sub_menu_id=psm.sub_menu_id and
						psm.user_id='" . $_SESSION["userid"] . "' and
						psm.company_id='" . $_SESSION['companyid'] . "' and
						psm.main_menu_id='" . $mainrow->main_menu_id . "' and
						dsm.module_id='" . $_SESSION['module_id'] . "' and
						dsm.status=1 and psm.status=1
						order by dsm.sl";
                        }
                        $sub_menu=mysqli_query($conn, $result);
                        while($subnrow=mysqli_fetch_object($sub_menu)): ?>
                            <li><a href="<?=$subnrow->sub_url;?>"><?=$subnrow->sub_menu_name;?>
                                    <?php if($subnrow->sub_menu_id=="20260") if($pendingCustomerFromApp>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$pendingCustomerFromApp.' </span>]'?><?php } else {echo'';} ?>
                                    <?php if($subnrow->sub_menu_id=="20030") if($creditLimitRequested>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$creditLimitRequested.' </span>]'?><?php } else {echo'';} ?>
                                    <?php if($subnrow->sub_menu_id=="20167") if($so_target>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$so_target.' </span>]'?><?php } else {echo'';} ?>
                                    <?php if($subnrow->sub_menu_id=="20178") if($stock_transfer>0) { ?><?='[<span style="color:red;font-weight:bold; font-size:15px"> '.$stock_transfer.' </span>]'?><?php } else {echo'';} ?>
                                </a>
                            </li>
                        <?php endwhile; ?></ul></li>
            <?php else : ?>
                <li><a href="<?=$mainrow->main_url;?>"><i class="<?=$mainrow->iconmain;?>"></i><?=$mainrow->main_menu_name?></a></li>
            <?php endif; ?>
        <?php endwhile; ?>
    </ul>
    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
</div>


