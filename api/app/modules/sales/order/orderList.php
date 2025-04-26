<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

// Check if userid is provided
if (!isset($_GET['userid'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'userid' parameter"]);
    exit;
}

$userid = $_GET['userid'];

$sql = "SELECT 
    s.orderNo AS orderNo, 
    c.dealer_name_e AS customer,
    SUM(s.amount) AS amount
FROM sales_get_order_from_app s
JOIN dealer_info c ON s.customer = c.dealer_code
WHERE s.entry_by = '".$userid."'
GROUP BY s.orderNo
ORDER BY s.orderNo DESC";

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