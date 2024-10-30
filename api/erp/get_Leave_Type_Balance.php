<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");

// Get the leave category from the AJAX request
if (isset($_GET['type']) && isset($_GET['user_id'])) {
    $leaveType = @$_GET['type'];
    $userId = @$_GET['user_id'];
    $userPersonalIdQuery = mysqli_query($conn, "SELECT PBI_ID FROM users WHERE user_id = " . $userId);
    $userPersonalId = mysqli_fetch_object($userPersonalIdQuery);
    $leaveTypeQuery = mysqli_query($conn, "SELECT yearly_leave_days, status FROM hrm_leave_type WHERE id = " . $leaveType);
    $leaveTypePolicy = mysqli_fetch_object($leaveTypeQuery);

    $year = date('Y');
    $s_date_s = "$year-01-01";
    $s_date_e = "$year-12-31";

    // Prepare the SQL query to get the balance for the selected leave type
    $sql = "SELECT SUM(total_days) as total_days FROM hrm_leave_info WHERE type = " . $leaveType . " AND half_or_full='Full' AND PBI_ID = " . $userPersonalId->PBI_ID . " AND e_date BETWEEN '$s_date_s' AND '$s_date_e' AND s_date BETWEEN '$s_date_s' AND '$s_date_e'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the balance
        $row = mysqli_fetch_assoc($result);
        // Send the response as JSON
        echo json_encode([
            'totalLeaveTaken' => $row['total_days'],
            'leavePolicy' => $leaveTypePolicy->yearly_leave_days,
            'applicableFor' => $leaveTypePolicy->status,
            'leaveBalance' => $leaveTypePolicy->yearly_leave_days - $row['total_days']
        ]);
    } else {
        echo json_encode([
            'totalLeaveTaken' => '0',
            'leavePolicy' => '0',
            'applicableFor' => null,
        ]);
    }

    mysqli_free_result($result); // Free result memory
}

$conn->close();
?>
