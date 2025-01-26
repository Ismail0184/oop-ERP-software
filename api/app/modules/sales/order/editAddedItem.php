<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error,
    ]));
}

$data = json_decode(file_get_contents('php://input'), true);

$itemId = isset($data['id']) ? (int)$data['id'] : null;
$businessCenter = isset($data['businessCenter']) ? trim($data['businessCenter']) : null;
$customer = isset($data['customer']) ? trim($data['customer']) : null;
$item = isset($data['item']) ? trim($data['item']) : null;
$quantity = isset($data['quantity']) && is_numeric($data['quantity']) ? (int)$data['quantity'] : null;
$rate = isset($data['rate']) && is_numeric($data['rate']) ? (float)$data['rate'] : null;
$amount = isset($data['amount']) && is_numeric($data['amount']) ? (float)$data['amount'] : null;

if (!$itemId || !$businessCenter || !$customer || !$item || $quantity === null || $rate === null || $amount === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input data. All fields are required.",
    ]);
    exit;
}

$sql = "UPDATE sales_get_order_from_app SET businessCenter = ?, customer = ?, item = ?, quantity = ?, rate = ?, amount = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare statement: " . $conn->error,
    ]);
    exit;
}

$stmt->bind_param("sssiddi", $businessCenter, $customer, $item, $quantity, $rate, $amount, $itemId);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Item updated successfully.",
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update item.",
    ]);
}

$stmt->close();
$conn->close();
?>
