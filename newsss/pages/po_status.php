<?php
require_once 'support_file.php';
$title='View Work Order';
$table = 'purchase_master';
$table_details="purchase_invoice";
$unique = 'po_no';
$status = 'UNCHECKED';
$page="po_status_single.php";
$print_page="po_print_view.php";
$crud      =new crud($table);
$$unique=@$_GET[$unique];
if (isset($_POST['reprocess'])) {

        $_POST['status'] = 'MANUAL';
        $crud->update($table);
        $_SESSION['initiate_po_no'] = $_GET[$unique];
        $type = 1;
        echo "<script>self.opener.location = 'po_create_item.php'; self.blur(); </script>";
        echo "<script>window.close(); </script>";
    }
$po_master=find_all_field(''.$table.'','',''.$unique.'='.$$unique.'');	
$GET_status=find_a_field(''.$table.'','status',''.$unique.'='.$$unique);

if(isset($_POST['viewReport'])){
    $res='select  
    a.po_no,
    a.vendor_id, 
    a.po_no, 
    a.return_comments,
    a.exim_status as type, 
    a.po_date as Work_order_Date, 
    v.vendor_name, 
    b.warehouse_name as final_Destination,
    a.work_order_for_department as Created_By_Department,
    c.fname,
    p.PBI_NAME as Check_By,
    a.delivery_within,
    a.status 
    from 
    purchase_master a,
    warehouse b,
    users c,
    vendor v,
    personnel_basic_info p 
    where  
    a.warehouse_id=b.warehouse_id and 
    a.entry_by=c.user_id and 
    a.checkby=p.PBI_ID and 
    a.vendor_id=v.vendor_id and
    a.po_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"
    order by a.po_no desc';
    } else { 
    $res='select  
    a.po_no,
    a.vendor_id, 
    a.po_no, 
    a.return_comments,
    a.exim_status as type, 
    a.po_date as Work_order_Date, 
    v.vendor_name, 
    b.warehouse_name as final_Destination,
    a.work_order_for_department as Created_By_Department,
    c.fname,
    p.PBI_NAME as Check_By,
    a.delivery_within,a.status 
    from 
    purchase_master a,
    warehouse b,
    users c,
    vendor v,
    personnel_basic_info p 
    where  
    a.warehouse_id=b.warehouse_id and 
    a.entry_by=c.user_id and 
    a.checkby=p.PBI_ID and 
    a.vendor_id=v.vendor_id and
    a.status in ("MANUAL","CANCELED","UNCHECKED") 
    order by a.po_no desc';
    }

$PostFDate = @$_POST['f_date'];
$PostTDate = @$_POST['t_date'];

$cash_discount = @$cash_discount;
$tax_ait = @$tax_ait;
$asf = @$asf;
$transport_bill = @$transport_bill;
$labor_bill = @$labor_bill;
$tax = @$tax;
	?>


<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?<?=$unique?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=900,height=600,left = 250,top = -1");}
    </script>
</head>


<?php require_once 'body_content.php';?>

<?php if(!isset($$unique)){ ?>
    <div class="col-md-12 col-xs-12">
        <div class="<?php if(isset($_POST['viewReport'])){ ?> row <?php } else { echo 'row collapse';} ?>" id="experience2">
            <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
                <table align="center" style="width: 50%;">
                    <tr><td>
                            <input type="date"  style="width:150px; font-size: 11px; height: 25px"  value="<?=(@$_POST['f_date']!='')? $_POST['f_date'] : date('Y-m-01') ?>" required   name="f_date" class="form-control col-md-7 col-xs-12" >
                        <td style="width:10px; text-align:center"> -</td>
                        <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?=(@$_POST['t_date']!='')? $_POST['t_date'] : date('Y-m-d') ?>" required   name="t_date" class="form-control col-md-7 col-xs-12" ></td>
                        <td style="padding:10px"><button type="submit" style="font-size: 11px; height: 30px" name="viewReport"  class="btn btn-primary"><i class="fa fa-eye"></i> View Work Order</button></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><?=$title;?></h2> <span class="text-right h5" style="float: right" data-toggle="collapse" data-target="#experience2">Filter <i class="fa fa-filter"></i></span>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table id="datatable-buttons" class="table table-striped table-bordered th" style="width:100%; font-size: 11px">
                <thead>
                <tr class="bg-primary text-white">
                    <th style="height:50px;vertical-align:middle; text-align:center">SL</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">PO</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">Date</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">Vendor Name</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">Final Destination</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">Entry By</th>
                    <th style="height:50px;vertical-align:middle; text-align:center">Status</th>
                    <th style="height:50px;vertical-align:middle; text-align:center; width:8%">Print View</th>
                </tr>
                </thead>

                <tbody>

                <? $qqq=mysqli_query($conn, $res); $i = 0;
                while($data=mysqli_fetch_object($qqq)){
                    $i=$i+1; ?>
                    <tr style=" cursor: pointer">
                        <td style="text-align: center; vertical-align: middle" onclick="DoNavPOPUP('<?=$data->po_no?>', 'TEST!?', 900, 600)"><?=$i?></td>
                        <td  align="center" style="padding:5px; vertical-align: middle">
                            <a href="../page/po_documents/qoutationDoc/<?=$data->$unique.'.pdf';?>" target="_blank" style="color:#06F" title="Quotation Attached"><u><strong>
                        <?=$data->po_no?></strong></u>
                            </a>
                        </td>

                        <td  align="center" style="padding:5px;width:8%; vertical-align: middle">
                            <a href="../page/po_documents/mailCommDoc/<?=$data->$unique.'.pdf';?>" target="_blank" style="color:#06F" title="Email Conversation Attached"><u><strong><?=$data->Work_order_Date?></strong></u></a></td>
                        <td  align="left" style="padding:5px; vertical-align: middle" onclick="DoNavPOPUP('<?=$data->po_no?>', 'TEST!?', 900, 600)"><?=$data->vendor_name?></td>
                        <td  align="left" style="padding:5px; vertical-align: middle" onclick="DoNavPOPUP('<?=$data->po_no?>', 'TEST!?', 900, 600)"><?=$data->final_Destination?></td>
                        <td  align="left" style="padding:5px; vertical-align: middle" onclick="DoNavPOPUP('<?=$data->po_no?>', 'TEST!?', 900, 600)"><?=$data->fname?></td>
                        <td  align="center" style="padding:5px; vertical-align: middle">
                            <span class="label label-<?php if( $data->status=='COMPLETED') { echo 'success';  } else if ( $data->status=='UNCHECKED') { echo 'default';  } else if ( $data->status=='MANUAL') { echo 'default';  } else if ( $data->status=='CHECKED') { echo 'primary';  } else if ( $data->status=='PROCESSING') { echo 'info';  } else if ( $data->status=='RETURNED') { echo 'danger';  }  ?>" style="font-size:10px"><?=$data->status?></span>
                        </td>
                        <td style="text-align: center; vertical-align: middle"><a target="_blank" href="<?=$print_page;?>?<?=$unique;?>=<?=$data->po_no;?>"><img src="http://icpbd-erp.com/51816/warehouse_mod/images/print.png" width="20" height="20" /></a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>
<?=$html->footer_content();mysqli_close($conn);?>