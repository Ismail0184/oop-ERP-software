<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

// Set timezone and access time
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$access_time = $dateTime->format("Y-m-d H:i:s");

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error,
    ]));
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input data
    $orderNo = isset($data['orderNo']) ? trim($data['orderNo']) : null;
    $businessCenter = isset($data['businessCenter']) ? trim($data['businessCenter']) : null;
    $customer = isset($data['customer']) ? trim($data['customer']) : null;
    $item = isset($data['item']) ? trim($data['item']) : null;
    $quantity = isset($data['quantity']) && is_numeric($data['quantity']) ? (int)$data['quantity'] : null;
    $rate = isset($data['rate']) && is_numeric($data['rate']) ? (float)$data['rate'] : null;
    $amount = isset($data['amount']) && is_numeric($data['amount']) ? (float)$data['amount'] : null;

    // Support both "entry_by" and "entryBy" keys
    $entryBy = isset($data['entryBy']) ? trim($data['entryBy']) : (isset($data['entry_by']) ? trim($data['entry_by']) : null);

    $entryAt = $access_time;

    // Check for missing required fields
    if (!$orderNo || !$businessCenter || !$customer || !$item || $quantity === null || $rate === null || $amount === null || !$entryBy) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid input data. All fields are required.",
        ]);
        exit;
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO sales_get_order_from_app (orderNo, businessCenter, customer, item, quantity, rate, amount, entry_by, entry_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to prepare statement: " . $conn->error,
        ]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("ssssiddss", $orderNo, $businessCenter, $customer, $item, $quantity, $rate, $amount, $entryBy, $entryAt);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode([
            "statusCode" => "200",
            "message" => "Item added successfully.",
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to add item: " . $stmt->error,
        ]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Use POST.",
    ]);
}

// Close the database connection
$conn->close();
?>
