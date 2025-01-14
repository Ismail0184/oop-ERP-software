<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Get the leave category from the AJAX request
if (isset($_GET['item_id']) && isset($_GET['warehouse_id'])) {

    $sql="Select i.item_id,i.finish_goods_code,i.item_name,i.unit_name,i.pack_size,
    REPLACE(FORMAT(SUM(j.item_in-j.item_ex), 0), ',', '') as Available_stock_balance
    from
    item_info i,
    journal_item j,
    lc_lc_received_batch_split bsp
    
    where
    
    j.item_id=i.item_id and
    j.warehouse_id='".$_GET['warehouse_id']."' and
    bsp.batch=j.batch and 
    bsp.status='PROCESSING' and 
    j.item_id='".$_GET['item_id']."'
    group by j.item_id order by i.item_id";
    $result = mysqli_query($conn, $sql);



    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the balance
        $ps_data=mysqli_fetch_assoc($result);
        $inventory_stock = @$ps_data['Available_stock_balance'];
        $ordered_qty_sql = "SELECT 
        SUM(total_unit) AS ordered_qty 
    FROM 
        sale_do_details 
    WHERE 
        item_id = '".$_GET['item_id']."' AND 
        depot_id = '".$_GET['warehouse_id']."' AND 
        status IN ('UNCHECKED', 'PROCESSING', 'MANUAL')";
        $ordered_qty_result = mysqli_query($conn, $ordered_qty_sql);
        $row = mysqli_fetch_assoc($ordered_qty_result);
        $in_stock_pcs= $inventory_stock-(int)$row['ordered_qty'];

        $item_SQL = "SELECT * from item_info where item_id='".$_GET['item_id']."'";
        $item_result = mysqli_query($conn, $item_SQL);
        $itemRow = mysqli_fetch_assoc($item_result);




        // Send the response as JSON
        echo json_encode([
            'inStock_pcs' => $in_stock_pcs,
            'd_price' => $itemRow['d_price'],
            'unit_price' => $itemRow['d_price'],
        ]);
    } else {
        echo json_encode([
            'inStock_pcs' => '0',
            'd_price' => '0',
            'unit_price' => '0',
        ]);
    }

    mysqli_free_result($result); // Free result memory
}

$conn->close();
?>
