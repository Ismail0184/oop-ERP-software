<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

$sql = "SELECT c.id as customer_id,c.customer_name as customer_name,c.mobile_no as mobile_no,t.AREA_NAME as territory_name,c.status as status,c.photo as photo 
from 
    app_get_customer_data c,
    area t

where
    c.territory=t.AREA_CODE";
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