<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

// Check for database connection errors
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error,
    ]));
}

// Get the item ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$itemId = isset($data['id']) ? (int)$data['id'] : null;

if (!$itemId) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid item ID.",
    ]);
    exit;
}

// Delete item from the database
$sql = "DELETE FROM sales_get_order_from_app WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare statement: " . $conn->error,
    ]);
    exit;
}

$stmt->bind_param("i", $itemId);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Item deleted successfully.",
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete item.",
    ]);
}

$stmt->close();
$conn->close();
?>
