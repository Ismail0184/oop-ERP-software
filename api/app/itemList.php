<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");


// Fetch business partners
$sql = "SELECT i.item_id as id,
       i.finish_goods_code as code,
       i.item_name as name,
       i.d_price as rate 
FROM 
    item_info i,
    item_brand b
WHERE 
    i.product_nature in ('Salable','Both') and 
    i.brand_id=b.brand_id and  
    i.status='Active' 
order by i.item_name";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["statusCode" => "200", "data" => $data]);

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
}

$conn->close();

?>