<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");


// Check if userid is provided
if (!isset($_GET['journal_info_no'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'voucher no' parameter"]);
    exit;
}

$receipt_no = $_GET['journal_info_no'];

// Fetch attendance data
$sql = "SELECT * from journal_info where journal_info_no='".$_GET['journal_info_no']."' and journal_info_date='1845854380' order by id";
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