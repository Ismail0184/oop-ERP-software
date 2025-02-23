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

if(isset($_GET['report_id']) && $_GET['report_id']=='1012001') {
    $fileName = "Purchase Data.xls";
    $fields = array('Po No', 'Po Date', 'Vendor Name', 'Item Id', 'FG Code (Custom Code)', 'Mat. Description', 'UoM', 'Qty','Rate','Amount');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012002'){
    $fileName = "Sales Data.xls";
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012011'){
    $fileName = "Sales Return Data.xls";
    $fields = array('T.ID', 'Depot', 'DB Code', 'Dealer Name', 'Dealer Type', 'Do No', 'Do Date','Territory','Region','FG Code','FG Description','UoM','Pack Size','Unit Price','Qty','Amount');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012003'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012004'){
    $fileName = "Customer Outstanding Report.xls";
    $fields = array('DB Code','Ledger Id','Dealer Name', 'Dealer Type', 'Territory', 'Region','Current Credit Limit','Balance');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012005'){
    $fileName = "Invoice List.xls";
    $fields = array('Chalan No', 'Chalan Date', 'Do No', 'Do Date', 'Do Type','Dealer Code','Dealer Name','Territory','Depot','Invoice Amount','Discount','Commission');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012006'){
    $fileName = "Collection and Shipment Report.xls";
    $fields = array('DB Code', 'Dealer Name', 'Dealer Type', 'Territory', 'Region','Collection','Shipment');
} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012007'){
    $fileName = "Stock Report.xls";
    $fields = array('Finish Goods Code', 'Item Name', 'Unit Name', 'Pack Size', 'Available Stock Balance');

} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012008'){
    $fileName = "Customer Details.xls";
    $fields = array('Dealer Code', 'Dealer Custom Code','Ledger ID','Customer Name', 'Town', 'Territory','Region','Propritor Name','Contact Person','Contact Number','Address','National Id','TIN / BIN');

} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012012'){
    $fileName = "Collection Register.xls";
    $fields = array('Collection Id', 'Collection Date','Customer Code','Ledger ID', 'Customer Name', 'Customer Group','Territory','Address','Phone No','Bank','Particulars','Amount');

} elseif (isset($_GET['report_id']) && $_GET['report_id']=='1012014'){
    $fileName = "Adjustment Register.xls";
    $fields = array('Ref No.', 'Entry Date','Customer Code','Ledger ID', 'Customer Name', 'Customer Group','Territory','Address','Phone No','Particulars','Amount');
}

else {
    $fileName = "export.xls";
    $fields = array('no record found in the report');

}
// Display column names as first row
$excelData = implode("\t", array_values($fields)) . "\n";

 if($_GET['report_id']=='1012001') {
     $query = $db->query("SELECT p.po_no as po_no,m.po_no,m.po_date as po_date,v.vendor_name as vendor,i.item_id as item_id,i.finish_goods_code as finish_goods_code,i.item_name as item_name,i.unit_name as unit_name,p.qty as qty,p.rate as rate,p.amount as amount 
from purchase_invoice p,purchase_master m,vendor v,item_info i 
where 
p.po_no=m.po_no and m.vendor_id=v.vendor_id  and
i.item_id=p.item_id and 
v.vendor_id='".$_GET['pc_code']."' and 
m.po_date between '" . $_GET['f_date'] . "' and '" . $_GET['t_date'] . "'
order by m.po_no,v.vendor_id");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['po_no'], $row['po_date'], $row['vendor'], $row['item_id'], $row['finish_goods_code'], $row['item_name'], $row['unit_name'], $row['qty'],$row['rate'],$row['amount']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     } else {
         $excelData .= 'No records found...' . "\n";
     }
 } elseif ($_GET['report_id']=='1012002') {
     $query = $db->query("WITH cash_discount_cte AS (
    SELECT 
        do_no,
        gift_on_item,
        SUM(total_amt) AS cash_discount
    FROM 
        sale_do_details
    WHERE 
        item_id = '1096000100010312'
    GROUP BY 
        do_no, gift_on_item
),
commission_cte AS (
    SELECT 
        do_no,
        SUM(total_unit) AS total_units_for_commission,
        SUM(CASE WHEN total_amt > 0 THEN total_unit ELSE 0 END) AS total_positive_units
    FROM 
        sale_do_details
    GROUP BY 
        do_no
)
SELECT 
    sdd.id,
    sdd.id AS `TID`,
    w.warehouse_name AS Depot,
    d.dealer_custom_code AS DBCode,
    d.dealer_name_e AS DealerName,
    d.dealer_type,
    sdd.do_no,
    sdd.do_date,
    sdd.do_type,
    t.AREA_NAME AS Territory,
    r.BRANCH_NAME AS region,
    i.finish_goods_code AS FGCode,
    i.item_name AS FGDescription,
    i.unit_name AS UoM,
    i.pack_size,
    sdd.unit_price,
    sdd.total_unit AS qty,
    FORMAT(sdd.total_amt, 2) AS amount,
    cdc.cash_discount,
    CASE 
        WHEN sdd.total_amt > 0 THEN 
            (m.commission_amount / cc.total_positive_units) * sdd.total_unit
        ELSE '-' 
    END AS commission,
    CASE 
        WHEN sdd.total_amt > 0 THEN 'sales' 
        ELSE 'free' 
    END AS sales_for
FROM 
    sale_do_details sdd
JOIN 
    warehouse w ON sdd.depot_id = w.warehouse_id
JOIN 
    dealer_info d ON sdd.dealer_code = d.dealer_code
JOIN 
    branch r ON d.region = r.BRANCH_ID
JOIN 
    area t ON d.area_code = t.AREA_CODE
JOIN 
    item_info i ON sdd.item_id = i.item_id
JOIN 
    sale_do_master m ON m.do_no = sdd.do_no
LEFT JOIN 
    cash_discount_cte cdc ON cdc.do_no = sdd.do_no AND cdc.gift_on_item = sdd.item_id
LEFT JOIN 
    commission_cte cc ON cc.do_no = sdd.do_no
WHERE 
    d.dealer_category = '".$_GET['pc_code']."' 
    AND sdd.item_id NOT IN ('1096000100010312') 
    AND sdd.do_date BETWEEN '".$_GET['f_date']."' AND '".$_GET['t_date']."'");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['TID'], $row['Depot'], $row['DBCode'], $row['DealerName'], $row['dealer_type'], $row['do_no'],$row['do_date'],$row['do_type'],$row['Territory'],$row['region'],
                 $row['FGCode'],$row['FGDescription'],$row['UoM'],$row['pack_size'],$row['unit_price'],$row['qty'],$row['amount'],$row['cash_discount'],$row['commission'],$row['sales_for']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }

 } elseif ($_GET['report_id']=='1012011') {
     $query = $db->query("SELECT sdd.id,sdd.id as TID,w.warehouse_name as Depot,d.dealer_custom_code as DBCode,
d.dealer_name_e as DealerName,d.dealer_type,sdd.do_no,sdd.do_date,t.AREA_NAME as 'Territory',r.BRANCH_NAME as region,
i.finish_goods_code as FGCode,i.item_name as FGDescription,i.unit_name as UoM,i.pack_size,sdd.unit_price,sdd.total_unit as qty,
sdd.total_amt as amount

from sale_return_details sdd,warehouse w,dealer_info d,branch r,area t,item_info i
where sdd.depot_id=w.warehouse_id and
      sdd.dealer_code=d.dealer_code and
      d.dealer_category='".$_GET['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      sdd.item_id=i.item_id and 
      sdd.item_id not in ('1096000100010312') and
      sdd.do_date between '".$_GET['f_date']."' and '".$_GET['t_date']."'");
     if ($query->num_rows > 0) {
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['TID'], $row['Depot'], $row['DBCode'], $row['DealerName'], $row['dealer_type'], $row['do_no'],$row['do_date'],$row['Territory'],$row['region'],
                 $row['FGCode'],$row['FGDescription'],$row['UoM'],$row['pack_size'],$row['unit_price'],$row['qty'],$row['amount']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }



 } elseif ($_GET['report_id']=='1012014') {
     $query = $db->query("SELECT 
    c.id,
    c.journal_info_no, 
    c.j_date,
    d.dealer_custom_code,
    c.ledger_id, 
    d.dealer_name_e,
    r.BRANCH_NAME,
    t.AREA_NAME,    
    d.address_e,
    d.mobile_no,
    c.narration,
    FORMAT(c.cr_amt,2) as Amount
FROM 
    journal_info c,
    dealer_info d,
    branch r,
    area t
    
WHERE 
    d.region=r.BRANCH_ID and 
    d.area_code=t.AREA_CODE and
    c.ledger_id=d.account_code and
    c.type = 'Credit' AND 
    c.cr_amt > 0 AND 
    c.j_date between '".$_GET['f_date']."' and '".$_GET['t_date']."' and 
    c.journal_info_no IN (
        SELECT journal_info_no
        FROM journal_info
        WHERE ledger_id = '2002018700000000' AND dr_amt > 0
    ) order by c.j_date,c.journal_info_no");
     if ($query->num_rows > 0) {
         while ($row = $query->fetch_assoc()) {
             $row['journal_info_no'] = "'" . $row['journal_info_no'];
             $lineData = array($row['journal_info_no'], $row['j_date'], $row['dealer_custom_code'],
                 $row['ledger_id'], $row['dealer_name_e'], $row['BRANCH_NAME'],$row['AREA_NAME'],$row['address_e'],
                 $row['mobile_no'],
                 $row['narration'],$row['Amount']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }



 } elseif ($_GET['report_id']=='1012003') {
     $query = $db->query("Select i.item_id,i.finish_goods_code as finish_goods_code,i.item_name as item_name,i.unit_name as uom,i.pack_size as pack_size,
REPLACE(FORMAT(SUM(j.item_in-j.item_ex), 0), ',', '') as Available_stock_balance
from
item_info i,
journal_item j,
item_brand b

where
    
j.item_id=i.item_id and
j.warehouse_id='".$_GET['warehouse_id']."' and
j.ji_date <= '".$_GET['t_date']."' and
i.brand_id=b.brand_id and
b.vendor_id='".$_GET['pc_code']."'
group by j.item_id");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['finish_goods_code'], $row['item_name'], $row['uom'], $row['pack_size'], $row['Available_stock_balance']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 } elseif ($_GET['report_id']=='1012004') {
     $query = $db->query("SELECT d.dealer_code,d.dealer_custom_code as DBCode,d.account_code,
d.dealer_name_e as DealerName,d.dealer_type as type,t.AREA_NAME as Territory,r.BRANCH_NAME as region,
d.credit_limit as CurrentCreditLimit,
IF(SUM(j.dr_amt-j.cr_amt)>'0',CONCAT(' (Dr) ', SUM(j.dr_amt-j.cr_amt)),CONCAT('(Cr) ',SUBSTR(SUM(j.dr_amt-j.cr_amt),2))) as balance                                               
from dealer_info d,branch r,area t,journal j
where 
      d.dealer_category='".$_GET['pc_code']."' and
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      j.jvdate<='".$_GET['t_date']."' and 
      d.account_code=j.ledger_id group by d.account_code");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['DBCode'], $row['account_code'],$row['DealerName'], $row['type'], $row['Territory'], $row['region'],$row['CurrentCreditLimit'],$row['balance']);
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
and m.status in ('CHECKED','COMPLETED') and m.do_no=c.do_no and  m.dealer_code=d.dealer_code and m.do_section not in ('Rice') and w.warehouse_id=m.depot_id 
and m.do_date between '".$_GET['f_date']."' and '".$_GET['t_date']."' and 
m.depot_id='".$_GET['warehouse_id']."' and
a.PBI_ID=p.PBI_ID group by c.do_no order by c.do_no");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['chalan_no'], $row['chalan_date'], $row['do_no'], $row['do_date'], $row['do_type'],
                 $row['dealer_custom_code'], $row['dealer_name'], $row['territory'], $row['depot'], $row['invoice_amount'], $row['discount'],$row['comissionamount']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 }

 elseif ($_GET['report_id']=='1012006') {
     $query = $db->query("SELECT d.dealer_code,d.dealer_custom_code as DBCode,
d.dealer_name_e as DealerName,d.dealer_type as type,t.AREA_NAME as Territory,r.BRANCH_NAME as region,
(select SUM(cr_amt) from receipt where ledger_id=d.account_code and receiptdate between '".$_GET['f_date']."' and '".$_GET['t_date']."') as collection,
(select SUM(total_amt) from sale_do_details where dealer_code=d.dealer_code and do_date between '".$_GET['f_date']."' and '".$_GET['t_date']."') as shipment                                      
from dealer_info d,branch r,area t
where 
      d.dealer_category='3' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE  group by d.account_code
      ");
     if ($query->num_rows > 0) {
         // Output each row of the data
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['DBCode'], $row['DealerName'], $row['type'], $row['Territory'], $row['region'],
                 $row['collection'], $row['shipment']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 } elseif ($_GET['report_id']=='1012008') {
     $query = $db->query("SELECT d.dealer_code,d.dealer_code as dealer_code,d.dealer_custom_code as dealer_custom_code,d.account_code,d.dealer_name_e as customer_name,t.town_name as town,a.AREA_NAME as territory,b.BRANCH_NAME as region,d.propritor_name_e as propritor_name,d.contact_person as contact_person,d.contact_number as contact_number,d.address_e as address,d.national_id as national_id,d.TIN_BIN as TINBIN  from dealer_info d, town t, area a, branch b WHERE
d.town_code=t.town_code and a.AREA_CODE=d.area_code and b.BRANCH_ID=d.region and d.dealer_category in ('3')  order by d.dealer_code");
     if ($query->num_rows > 0) {
         while ($row = $query->fetch_assoc()) {
             $lineData = array($row['dealer_code'], $row['dealer_custom_code'], $row['account_code'], $row['customer_name'], $row['town'], $row['territory'],
                 $row['region'], $row['propritor_name'],$row['contact_person'], $row['contact_number'],$row['address'], $row['national_id'] ,$row['TINBIN']);
             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }
     }
     else {
         $excelData .= 'No records found...' . "\n";
     }
 }

 elseif ($_GET['report_id']=='1012012') {
     $query = $db->query("SELECT c.id,c.receipt_no,c.receiptdate,d.account_code,d.dealer_custom_code,d.dealer_name_e,r.BRANCH_NAME,t.AREA_NAME,d.address_e,d.mobile_no,c.bank,c.narration,c.cr_amt

from receipt c,
     dealer_info d,
     branch r,
     area t
where c.ledger_id=d.account_code and
      d.dealer_category='".$_GET['pc_code']."' and 
      d.region=r.BRANCH_ID and 
      d.area_code=t.AREA_CODE and
      c.receiptdate between '".$_GET['f_date']."' and '".$_GET['t_date']."' order by c.receipt_no desc");
     if ($query->num_rows > 0) {

         while ($row = $query->fetch_assoc()) {
             // Add a single quote to receipt_no to prevent Excel from formatting it
             $row['receipt_no'] = "'" . $row['receipt_no'];

             $lineData = array(
                 $row['receipt_no'],
                 $row['receiptdate'],
                 $row['dealer_custom_code'],
                 $row['account_code'],
                 $row['dealer_name_e'],
                 $row['BRANCH_NAME'],
                 $row['AREA_NAME'],
                 $row['address_e'],
                 $row['mobile_no'],
                 $row['bank'],
                 $row['narration'],
                 $row['cr_amt']
             );

             array_walk($lineData, 'filterData');
             $excelData .= implode("\t", array_values($lineData)) . "\n";
         }}

     else {
         $excelData .= 'No records found...' . "\n";
     }
 }



 // Download the generated CSV file
 header('Content-Type: text/csv');
 header('Content-Disposition: attachment; filename=' . $fileName);
 readfile($fileName);
// Render excel data
echo $excelData;
exit;