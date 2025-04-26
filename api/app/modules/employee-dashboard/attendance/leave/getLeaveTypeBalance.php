<?php
header("Content-Type: application/json");
require("../../../../../../app/db/base.php");

$response = [
    "status" => "error",
    "message" => "Invalid request",
];

if (isset($_GET['type']) && isset($_GET['user_id'])) {
    $leaveType = intval($_GET['type']);
    $userId = intval($_GET['user_id']);

    // Step 1: Get PBI_ID from user_id
    $userQuery = mysqli_query($conn, "SELECT PBI_ID FROM users WHERE PBI_ID = $userId");

    if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
        $response['message'] = "User not found";
        echo json_encode($response);
        exit;
    }

    $user = mysqli_fetch_assoc($userQuery);
    $pbiId = $user['PBI_ID'];

    // Step 2: Get leave policy
    $policyQuery = mysqli_query($conn, "SELECT yearly_leave_days, status FROM hrm_leave_type WHERE id = $leaveType");

    if (!$policyQuery || mysqli_num_rows($policyQuery) === 0) {
        $response['message'] = "Leave type not found";
        echo json_encode($response);
        exit;
    }

    $policy = mysqli_fetch_assoc($policyQuery);
    $yearlyLeaveDays = (int)$policy['yearly_leave_days'];
    $applicableFor = $policy['status'];

    // Step 3: Calculate leave taken
    $year = date('Y');
    $startOfYear = "$year-01-01";
    $endOfYear = "$year-12-31";

    $leaveQuery = mysqli_query($conn, "
        SELECT SUM(total_days) AS total_days 
        FROM hrm_leave_info 
        WHERE type = $leaveType 
        AND half_or_full = 'Full' 
        AND PBI_ID = '$pbiId' 
        AND s_date BETWEEN '$startOfYear' AND '$endOfYear' 
        AND e_date BETWEEN '$startOfYear' AND '$endOfYear'
    ");

    $totalLeaveTaken = 0;

    if ($leaveQuery && mysqli_num_rows($leaveQuery) > 0) {
        $leaveResult = mysqli_fetch_assoc($leaveQuery);
        $totalLeaveTaken = (int) $leaveResult['total_days'];
    }

    $leaveBalance = $yearlyLeaveDays - $totalLeaveTaken;

    $response = [
        "status" => "success",
        "totalLeaveTaken" => $totalLeaveTaken,
        "leavePolicy" => $yearlyLeaveDays,
        "applicableFor" => $applicableFor,
        "leaveBalance" => $leaveBalance
    ];
}

echo json_encode($response);
$conn->close();
?>
