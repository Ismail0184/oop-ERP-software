<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Check if receipt_no is provided
if (!isset($_GET['receipt_no'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'receipt_no' parameter"]);
    exit;
}

$receipt_no = $_GET['receipt_no'];

// Prepare and execute the delete query
$sql = "DELETE FROM receipt WHERE receipt_no = ? AND receipt_date = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // The second parameter should be a valid timestamp or value for receipt_date
    $receipt_date = '1845854380'; // You may need to dynamically assign this value or get it from somewhere
    $stmt->bind_param('ss', $receipt_no, $receipt_date);  // 'ss' for two strings, adjust if needed

    if ($stmt->execute()) {
        // Check if a row was affected
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "200", "message" => "Voucher deleted successfully"]);
        } else {
            echo json_encode(["status" => "404", "message" => "Voucher not found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Query execution failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
}

$conn->close();
?>
