<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Check if userid is provided
if (!isset($_GET['itemId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'item' parameter"]);
    exit;
}

$item_id = $_GET['itemId'];

// Fetch business partners
$sql = "SELECT i.d_price as rate,SUM(j.item_in-j.item_ex) as stock  FROM journal_item j,item_info i WHERE i.item_id = '".$item_id."' and i.item_id=j.item_id";
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