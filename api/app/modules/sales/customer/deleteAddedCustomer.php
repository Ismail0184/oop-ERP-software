<?php
header("Content-Type: application/json");

// Include database connection
require("../../../../../app/db/base.php");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. POST required."
    ]);
    exit;
}

// Check for database connection
if (!$conn || $conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

// Get and decode the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate customer ID
if (!isset($data['customer_id']) || !is_numeric($data['customer_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or missing customer ID."
    ]);
    exit;
}

$itemId = (int)$data['customer_id'];

// Prepare SQL delete query
$sql = "DELETE FROM app_get_customer_data WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $itemId);

// Execute the query
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Customer deleted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Customer not found or already deleted."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete customer."
    ]);
}

// Clean up
$stmt->close();
$conn->close();
?>