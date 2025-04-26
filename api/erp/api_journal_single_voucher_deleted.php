<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Check if journal_info_no is provided
if (!isset($_GET['journal_info_no'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'journal_info_no' parameter"]);
    exit;
}

$journal_info_no = $_GET['journal_info_no'];

// Prepare and execute the delete query
$sql = "DELETE FROM journal_info WHERE journal_info_no = ? AND journal_info_date = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // The second parameter should be a valid timestamp or value for receipt_date
    $journal_info_date = '1845854380'; // You may need to dynamically assign this value or get it from somewhere
    $stmt->bind_param('ss', $journal_info_no, $journal_info_date);  // 'ss' for two strings, adjust if needed

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
