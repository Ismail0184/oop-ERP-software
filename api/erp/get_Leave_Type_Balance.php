<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");
// Get the leave category from the AJAX request
if (isset($_GET['type']) && isset($_GET['user_id'])) {
    $leaveType = @$_GET['type'];
    $userId = @$_GET['user_id'];
    $userPersonalIdQuery = mysqli_query($conn, "select PBI_ID from users where user_id=".$userId."");
    $userPersonalId      = mysqli_fetch_object($userPersonalIdQuery);
    $leaveTypeQuery      = mysqli_query($conn, "SELECT yearly_leave_days,status from hrm_leave_type where id=".$leaveType."");
    $leaveTypePolicy     = mysqli_fetch_object($leaveTypeQuery);

    $year=date('Y');
    $s_date_s="".$year."-01-01";
    $s_date_e="".$year."-12-31";
    // Prepare the SQL query to get the balance for the selected leave type
    $sql = "SELECT SUM(total_days) as total_days FROM hrm_leave_info WHERE type = ".$leaveType." and half_or_full='Full' and PBI_ID=".$userPersonalId->PBI_ID." and e_date between '".$s_date_s."' and '".$s_date_e."'
	and s_date between '".$s_date_s."' and '".$s_date_e."'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $leaveType);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the balance
        $row = $result->fetch_assoc();
        // Send the response as JSON
        echo json_encode(
            [
                'totalLeaveTaken'   => $row['total_days'],
                'leavePolicy'       => $leaveTypePolicy->yearly_leave_days,
                'applicableFor'     => $leaveTypePolicy->status,
                'leaveBalance'      => $leaveTypePolicy->yearly_leave_days-$row['total_days']

                ]);
    } else {
        echo json_encode(

            [
                'totalLeaveTaken'   => '0',
                'leavePolicy'       => '0',
                'applicableFor'     => null,
            ]

        ); // If no record is found
    }

    $stmt->close();
}

$conn->close();
?>
