<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

// Check if userid is provided
if (!isset($_GET['customer_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'customer_id' parameter"]);
    exit;
}

$userid = $_GET['userid'];

// Fetch attendance data
$sql = "
SELECT 
    c.id as customer_id,
    c.customer_name as customer_name,
    c.address as address,
    c.contact_person_name as contact_person_name,
    c.contact_person_designation as contact_person_designation,
    c.tin as tin,
    c.bin as bin,
    c.nid as nid,
    dt.typedetails as customer_type,
    c.mobile_no as mobile_no,
    c.status as status,
    t.AREA_NAME as territory_name 
from 
    app_get_customer_data c,
    area t,
    distributor_type dt

where
    c.territory=t.AREA_CODE and 
    c.id=".$_GET['customer_id']." and 
    dt.id=c.customer_type
";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["status" => "200", "data" => $data]);

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
}

$conn->close();

?>