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
    $customer_name = isset($data['customer_name']) ? trim($data['customer_name']) : null;
    $address = isset($data['address']) ? trim($data['address']) : null;
    $mobile_no = isset($data['mobile_no']) ? trim($data['mobile_no']) : null;
    $contact_person_name = isset($data['contact_person_name']) ? trim($data['contact_person_name']) : null;
    $contact_person_designation = isset($data['contact_person_designation']) ? trim($data['contact_person_designation']) : null;
    $tin = isset($data['tin']) ? trim($data['tin']) : null;
    $bin = isset($data['bin']) ? trim($data['bin']) : null;
    $nid = isset($data['nid']) ? trim($data['nid']) : null;
    $customer_type = isset($data['customer_type']) ? trim($data['customer_type']) : null;
    $territory = isset($data['territory']) ? trim($data['territory']) : null;
    $entryBy = isset($data['entryBy']) ? trim($data['entryBy']) : null;
    $photo = isset($data['photo']) ? trim($data['photo']) : null;
    $entryAt = $access_time;

    // Check for missing required fields
    if (!$customer_name || !$mobile_no || !$customer_type || !$entryBy) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid input data. All fields are required.",
        ]);
        exit;
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO app_get_customer_data (customer_name, address, mobile_no, contact_person_name, contact_person_designation, tin, bin,nid,customer_type,territory, entry_by, entry_at,photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to prepare statement: " . $conn->error,
        ]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("sssssssssssss", $customer_name, $address, $mobile_no, $contact_person_name, $contact_person_designation, $tin, $bin,$nid,$customer_type,$territory, $entryBy, $entryAt, $photo);

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
$conn->close();
?>
