<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$access_time = $dateTime->format("Y-m-d H:i:s");

// Check if a 'userID' is passed in the query parameters
if (isset($_GET['userID']) && !empty($_GET['userID'])) {
    // Get the userID from the query parameter
    $userID = $_GET['userID'];

    // Validate that the userID is a valid integer
    if (is_numeric($userID) && $userID > 0) {
        // Get the current timestamp (in seconds since Unix epoch)
        $timestamp = time();

        // Generate the invoice number using the user ID and timestamp
        $invoiceNo = $userID.$timestamp;

        // Respond with the invoice number in JSON format
        echo json_encode(['status' => 'success', 'invoiceNumber' => $invoiceNo]);
    } else {
        // If the userID is invalid, return an error message
        echo json_encode(['status' => 'error', 'message' => 'Invalid userID']);
    }
} else {
    // If no userID is provided, return an error message
    echo json_encode(['status' => 'error', 'message' => 'userID parameter is required']);
}
?>
