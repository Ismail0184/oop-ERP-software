<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");


// Check if userid is provided
if (!isset($_GET['userid'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'userid' parameter"]);
    exit;
}

$userid = $_GET['userid'];

// Fetch attendance data
$sql = "SELECT date,clock_in,clock_out,clock_in_status,clock_out_status,late,early FROM ZKTeco_attendance WHERE employee_id = ? and date between '".$_GET['start_date']."' and '".$_GET['end_date']."' order by date desc";
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