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

// Check if userid is provided
if (!isset($_GET['orderNo'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'orderNo' parameter"]);
    exit;
}

$orderNo = $_GET['orderNo'];

// Fetch items from the database
$sql = "
SELECT 
    s.*, 
    i.item_name AS itemName 
FROM 
    sales_get_order_from_app s
JOIN 
    item_info i 
ON 
    s.item = i.item_id
WHERE 
    s.orderNo = '".$orderNo."'
ORDER BY 
    s.id DESC
";

$result = $conn->query($sql);

// Check if any items are found
if ($result->num_rows > 0) {
    // Create an array to hold the data
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'businessCenter' => $row['businessCenter'],
            'customer' => $row['customer'],
            'item' => $row['item'],
            'quantity' => $row['quantity'],
            'rate' => $row['rate'],
            'amount' => $row['amount'],
            'entryBy' => $row['entry_by'],
            'entryAt' => $row['entry_at'],
            'itemName' => $row['itemName'],
        ];
    }

    echo json_encode([
        "status" => "success",
        "message" => "Items fetched successfully.",
        "data" => $items,
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No items found.",
    ]);
}

// Close the connection
$conn->close();
?>
