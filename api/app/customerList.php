<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Check if userid is provided
if (!isset($_GET['businessCenterId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'businessCenter' parameter"]);
    exit;
}

$businessCenter = $_GET['businessCenterId'];

// Fetch business partners
//$sql = "SELECT d.dealer_code as id,dealer_custom_code as code,d.dealer_name_e as name,d.address_e as address,(select sum(cr_amt-dr_amt) from journal where ledger_id=d.account_code) as balance FROM dealer_info d WHERE d.dealer_category = '".$businessCenter."' and d.canceled='Yes' order by d.dealer_name_e";
$sql = "SELECT d.dealer_code as id,dealer_custom_code as code,d.dealer_name_e as name,d.address_e as address FROM dealer_info d WHERE d.dealer_category = '".$businessCenter."' and d.canceled='Yes' order by d.dealer_name_e";
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