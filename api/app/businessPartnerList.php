<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");


// Fetch business partners
$sql = "SELECT vendor_id,id,vendor_name as name FROM vendor WHERE distributor_of_the_vendor = 'Yes' and status='ACTIVE' order by vendor_id desc";
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