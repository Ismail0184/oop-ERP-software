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

// Validate required fields
$requiredFields = ['customer_id', 'customer_name', 'mobile_no'];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing or empty required field: $field"
        ]);
        exit;
    }
}

$customer_id = (int)$data['customer_id'];
$customer_name = $data['customer_name'];
$address = isset($data['address']) ? $data['address'] : '';
$mobile_no = $data['mobile_no'];
$contact_person_name = isset($data['contact_person_name']) ? $data['contact_person_name'] : '';
$contact_person_designation = isset($data['contact_person_designation']) ? $data['contact_person_designation'] : '';
$tin = isset($data['tin']) ? $data['tin'] : '';
$bin = isset($data['bin']) ? $data['bin'] : '';
$nid = isset($data['nid']) ? $data['nid'] : '';
$customer_type = isset($data['customer_type']) ? $data['customer_type'] : '';
$territory = isset($data['territory']) ? $data['territory'] : '';
$entryBy = isset($data['entryBy']) ? $data['entryBy'] : null;

// Prepare SQL update query
$sql = "UPDATE app_get_customer_data SET 
            customer_name = ?, 
            address = ?, 
            mobile_no = ?, 
            contact_person_name = ?, 
            contact_person_designation = ?, 
            tin = ?, 
            bin = ?, 
            nid = ?, 
            customer_type = ?, 
            territory = ?, 
            updated_by = ?, 
            updated_at = NOW() 
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "ssssssssisii",
    $customer_name,
    $address,
    $mobile_no,
    $contact_person_name,
    $contact_person_designation,
    $tin,
    $bin,
    $nid,
    $customer_type,
    $territory,
    $entryBy,
    $customer_id
);

// Execute the query
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Customer updated successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update customer: " . $stmt->error
    ]);
}

// Clean up
$stmt->close();
$conn->close();
?>
