 <?php
require_once 'support_file.php';
$title="Primary DO List";
 $table_master='sale_do_master';
 $unique_master='do_no';
 $table_detail='sale_do_details';
 $unique_detail='id';
 $table_chalan='sale_do_chalan';
 $unique_chalan='id';
 $$unique_master=@$_POST[$unique_master];
 $table='sale_do_master';
 $show='dealer_code';
 $id='do_no';
 $text_field_id='old_do_no';
 $target_url = 'MIS_invoice_view.php';
 $page = 'MIS_data_delete_invoice_delete.php';
 ?>

 <?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
         function DoNavPOPUP(lk,lkg)
         {myWindow = window.open("<?=$target_url;?>?<?=$unique_master;?>="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=950,height=570,left = 280,top = -1");}
     </script>
 <?php require_once 'body_content.php'; ?>


 <form  name="addem" id="addem" class="form-horizontal form-label-left" method="post" >
 <table align="center" style="width: 50%;">
     <tr><td>
             <input type="date"  style="width:150px; font-size: 11px; height: 25px"  value="<?php if(isset($_POST['f_date'])) echo $_POST['f_date']; else echo date('Y-m-01');?>" max="<?=date('Y-m-d');?>" required   name="f_date"  >
         <td style="width:10px; text-align:center"> -</td>
         <td><input type="date"  style="width:150px;font-size: 11px; height: 25px"  value="<?php if(isset($_POST['t_date'])) { echo $_POST['t_date']; } else { echo date('Y-m-d'); }?>" max="<?=date('Y-m-d')?>" required   name="t_date" ></td>
         <td style="padding:10px"><button type="submit" style="font-size: 11px;" name="viewreport"  class="btn btn-primary">View Order</button></td>
     </tr></table>
 </form>


<?php
if(isset($_POST['viewreport'])){

    $res = "select m.do_no,m.do_no,m.do_date,concat(d.dealer_code,' - ',d.dealer_name_e) as dealer_name,w.warehouse_name as 'Warehouse',
concat(u.fname) as Invoice_By,m.status
from
sale_do_master m,
dealer_info d ,
sale_do_details dt,
users u,
warehouse w
where
m.dealer_code=d.dealer_code and
m.do_no=dt.do_no  and
m.depot_id=w.warehouse_id and
m.entry_by=u.user_id and
m.do_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' 
group by m.do_no order by m.do_no desc";
}
    ?>
<?=$crud->report_templates_with_status($res,$title);?>
<?=$html->footer_content();?>
