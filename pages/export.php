 <?php
 $dbHost     = "localhost";
 $dbUsername = "icp_distribution";
 $dbPassword = "Allahis1!!@@##";
 $dbName     = "icp_distribution";
 // Create database connection
 $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
 // Check connection
 if ($db->connect_error) {
     die("Connection failed: " . $db->connect_error);
 }
// Filter the excel data
function filterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}
// Excel file name for download

if($_GET['report_id']=='1012001') {
    $fileName = "Purchase Data.xls";
    $fields = array('Po No', 'Po Date', 'Vendor Name', 'Item Id', 'FG Code (Custom Code)', 'Mat. Description', 'UoM', 'Qty');
} elseif ($_GET['report_id']=='1012002'){
    $fileName = "Sales Data.xls";
    $fields = array('T.ID', 'Depot', 'DB Code', 'Dealer Name', 'Dealer Type', 'Do No', 'Do Date', 'Do Type','Territory','Region','FG Code','FG Description','UoM','Pack Size','Unit Price','Qty','Amount','Sales For');
} elseif ($_GET['report_id']=='1012003'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');
} elseif ($_GET['report_id']=='1012004'){
    $fileName = "Customer Outstanding Report.xls";
    $fields = array('DB Code', 'Dealer Name', 'Dealer Type', 'Territory', 'Region','Balance');
} elseif ($_GET['report_id']=='1012005'){
    $fileName = "Invoice List.xls";
    $fields = array('Chalan No', 'Chalan Date', 'Do No', 'Do Date', 'Do Type','Dealer Code','Dealer Name','Territory','Depot','Invoice Amount','Discount','Commission','Receivable Amount');
} elseif ($_GET['report_id']=='1012006'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');
} elseif ($_GET['report_id']=='1012007'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');
} elseif ($_GET['report_id']=='1012008'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');
}

else {
    $fileName = "export.xls";
    $fields = array('no record found in the report');

}
// Display column names as first row
$excelData = implode("\t", array_values($fields)) . "\n";

 if($_GET['report_id']=='1012001') {
     $query = $db->query("SELECT p.po_no as po_no,m.po_no,m.po_date as po_date,v.vendor_name as vendor,i.item_id as item_id,i.finish_goods_code as finish_goods_code,i.item_name as item_name,i.unit_name as unit_name,p.qty as qty  
from purchase_invoice p,purchase_master m,vendor v,item_info i 
where 
p.po_no=m.po_no and m.vendor_id=v.vendor_id  and
i.item_id=p.item_id and 
v.vendor_id='13' and 
m.po_date between '" . $_GET['f_date'] . "' and '" . $_GET['t_date'] . "'
order by m.po_no,v.vendor_id");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['po_no'], $row['po_date'], $row['vendor'], $row['item_id'], $row['finish_goods_code'], $row['item_name'], $row['unit_name'], $row['qty']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     } else {
         $excelData .= 'No records found...' . "\n";
     }
 } elseif ($_GET['report_id']=='1012002') {
     $query = $db->query("SELECT sdd.id,sdd.id as TID,w.warehouse_name as Depot,d.dealer_custom_code as DBCode,
d.dealer_name_e as DealerName,d.dealer_type as dealer_type,sdd.do_no as do_no,sdd.do_date as do_date,sdd.do_type as do_type,t.AREA_NAME as Territory,r.BRANCH_NAME as region,
i.finish_goods_code as FGCode,i.item_name as FGDescription,i.unit_name as UoM,i.pack_size as pack_size,sdd.unit_price as unit_price,sdd.total_unit as qty,sdd.total_amt as amount,
IF(sdd.total_amt>'0', 'sales','free') as sales_for
from sale_do_details sdd,warehouse w,dealer_info d,branch r,area t,item_info i
where sdd.depot_id=w.warehouse_id and
      sdd.dealer_code=d.dealer_code and
      d.dealer_category='3' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      sdd.item_id=i.item_id and 
      sdd.do_date between '".$_GET['f_date']."' and '".$_GET['t_date']."'");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['TID'], $row['Depot'], $row['DBCode'], $row['DealerName'], $row['dealer_type'], $row['do_no'],$row['do_date'],$row['do_type'],$row['Territory'],$row['region'],
                 $row['FGCode'],$row['FGDescription'],$row['UoM'],$row['pack_size'],$row['unit_price'],$row['qty'],$row['amount'],$row['sales_for']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }

 } elseif ($_GET['report_id']=='1012004') {
     $query = $db->query("SELECT d.dealer_code,d.dealer_custom_code as DBCode,
d.dealer_name_e as DealerName,d.dealer_type as type,t.AREA_NAME as Territory,r.BRANCH_NAME as region,                                        
IF(SUM(j.dr_amt-j.cr_amt)>'0',CONCAT(' (Dr) ', SUM(j.dr_amt-j.cr_amt)),CONCAT('(Cr) ',SUBSTR(SUM(j.dr_amt-j.cr_amt),2))) as balance                                               
from dealer_info d,branch r,area t,journal j
where 
      d.dealer_category='3' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      d.account_code=j.ledger_id group by d.account_code");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['DBCode'], $row['DealerName'], $row['type'], $row['Territory'], $row['region'],$row['balance']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 } elseif ($_GET['report_id']=='1012005') {
     $query = $db->query("select
distinct c.chalan_no as chalan_no,
c.chalan_date as chalan_date,
m.do_no as do_no,
m.do_date as do_date,
d.dealer_custom_code as dealer_custom_code,
d.dealer_code as dealercode,
d.region,
d.area_code,
d.territory,
d.town_code,
p.PBI_NAME as tsm ,
d.dealer_name_e as dealer_name,
a.AREA_NAME as area,
a.ZONE_ID as Zonecode,
a.PBI_ID,
d.team_name as team,
w.warehouse_name as depot,
d.product_group as grp,
c.driver_name,
m.cash_discount commission,
m.commission_amount as comissionamount,
m.do_type,
SUM(c.total_amt)as invoice_amount,
(SELECT SUM(total_amt) from sale_do_details where do_no=c.do_no and item_id=1096000100010312) as discount
from
sale_do_master m,
sale_do_chalan c,
dealer_info d ,
warehouse w,
area a,
personnel_basic_info p

where
d.dealer_category='3' and 
a.AREA_CODE=d.area_code
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id and
m.do_date between '".$_GET['f_date']."' and '".$_GET['t_date']."' and 
m.depot_id='".$_POST['warehouse_id']."'
a.PBI_ID=p.PBI_ID group by c.do_no order by c.do_no");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['chalan_no'], $row['chalan_date'], $row['do_no'], $row['do_date'], $row['do_type'],
                 $row['dealer_custom_code'], $row['dealer_name'], $row['territory'], $row['depot'], $row['invoice_amount'], $row['discount']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 }



// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
echo $excelData;
exit;