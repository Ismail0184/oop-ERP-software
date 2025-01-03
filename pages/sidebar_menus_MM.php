<?php
require_once ('support_file.php');
$mushak_challan= find_a_field('sale_do_master','count(do_no)','mushak_challan_status="UNRECORDED" and depot_id='.$_SESSION['warehouse'].' and status="COMPLETED"');
$mushak_challan_IR= find_a_field('purchase_return_master','count(id)','mushak_challan_status="UNRECORDED" and type="shortage" and warehouse_id='.$_SESSION['warehouse'].' and status="PROCESSING"');
$mushak_challan_6_8= find_a_field('purchase_return_master','count(id)','mushak_challan_status="UNRECORDED" and type in ("damage","other") and warehouse_id='.$_SESSION['warehouse'].' and status="PROCESSING"');
$SD_VAT_TAX=$mushak_challan+$mushak_challan_IR;
?>


<div class="menu_section">
    <h3></h3>
    <ul class="nav side-menu">
        <li><a href="dashboard.php"><i class="fa fa-home"></i>Home</a></li>

        <?php
        $result = mysqli_query($conn, "SET NAMES utf8");//the main trick
        if($_SESSION['language']=='Bangla') {
            $result = "Select
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
								pmm.user_id='" . $_SESSION["userid"] . "' and
								pmm.company_id='" . $_SESSION['companyid'] . "'  and
								dmm.module_id='" . $_SESSION['module_id'] . "' and
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
            <?php if($mainrow->main_menu_name!="Material Reports"): ?>
                <li><a href="#"><i class="<?=$mainrow->iconmain;?>"></i><?=$mainrow->main_menu_name;?>
                        <?php if($mainrow->main_menu_id=="10043") if($SD_VAT_TAX>0) : ?><?='[<span style="color:red;font-weight:bold;">'.$SD_VAT_TAX.'</span>]'?><?php else : echo''; endif; ?>
                        <span class="fa fa-chevron-down"></span></a>
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
                            $result="Select
								psm.*,
								dsm.sub_menu_id,
								dsm.sub_menu_name,
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
                        }
                        $sub_menu=mysqli_query($conn, $result);
                        while($subnrow=mysqli_fetch_object($sub_menu)): ?>
                            <li><a href="<?=$subnrow->sub_url;?>"><?=$subnrow->sub_menu_name;?>
                                    <?php if($subnrow->sub_menu_id=="20183") if($mushak_challan>0) : ?><?='[<span style="color:red;font-weight:bold;"> '.$mushak_challan.' </span>]'?><?php else : echo'';endif; ?>
                                    <?php if($subnrow->sub_menu_id=="20184") if($mushak_challan_IR>0) : ?><?='[<span style="color:red;font-weight:bold;"> '.$mushak_challan_IR.' </span>]'?><?php else : echo'';endif; ?>
                                    <?php if($subnrow->sub_menu_id=="20250") if($mushak_challan_6_8>0) : ?><?='[<span style="color:red;font-weight:bold;"> '.$mushak_challan_6_8.' </span>]'?><?php else : echo'';endif; ?>
                                </a>
                            </li>
                        <?php endwhile; ?></ul></li>
            <?php else : ?>
                <li><a href="<?=$mainrow->main_url;?>"><i class="<?=$mainrow->iconmain;?>"></i><?=$mainrow->main_menu_name?></a></li>
            <?php endif; ?>
        <?php endwhile; ?>
    </ul>
    <br /><br />
</div>
