<?php
header("Content-Type: application/json");
require("../../../../../app/db/base.php");

if (!isset($_GET['userid'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'userid' parameter"]);
    exit;
}

$userid = $_GET['userid'];
$startOfMonth = date("Y-m-01");
$endOfMonth = date("Y-m-d");

$sql = "SELECT date, clock_in, clock_out, clock_in_status, clock_out_status, late, early 
        FROM ZKTeco_attendance 
        WHERE employee_id = ? 
        AND date BETWEEN ? AND ? 
        ORDER BY date DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('iss', $userid, $startOfMonth, $endOfMonth);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[$row['date']] = $row;
    }

    $pbi_id = $userid;

    $allDates = [];
    $current = strtotime($startOfMonth);
    $end = strtotime($endOfMonth);

    while ($current <= $end) {
        $currentDate = date("Y-m-d", $current);
        $dayOfWeek = date('l', $current);

        if (isset($attendanceData[$currentDate])) {
            $allDates[] = $attendanceData[$currentDate];
        } else {
            $clock_in = null;
            $clock_out = null;
            $clock_in_status = "";
            $clock_out_status = "";

            if ($dayOfWeek == 'Friday') {
                $clock_in = $clock_out = $clock_in_status = $clock_out_status = 'Friday';
            } else {
                // 1. Check Leave
                $leaveSql = "SELECT status FROM hrm_leave_info 
                             WHERE PBI_ID = ? AND ? BETWEEN s_date AND e_date";
                $leaveStmt = $conn->prepare($leaveSql);
                $leaveStmt->bind_param("ss", $pbi_id, $currentDate);
                $leaveStmt->execute();
                $leaveResult = $leaveStmt->get_result();
                $leaveRow = $leaveResult->fetch_assoc();
                $getLeaveStatus = $leaveRow['status'];
                $leaveStmt->close();

                if ($getLeaveStatus) {
                    if ($getLeaveStatus == 'PENDING') {
                        $clock_in_status = 'Applied for Leave';
                    } elseif ($getLeaveStatus == 'DRAFTED') {
                        $clock_in_status = 'You drafted a leave';
                    } elseif ($getLeaveStatus == 'RECOMMENDED') {
                        $clock_in_status = 'Leave is RECOMMENDED';
                    } elseif ($getLeaveStatus == 'APPROVED') {
                        $clock_in_status = 'Leave is Approved';
                    } elseif ($getLeaveStatus == 'REJECTED') {
                        $clock_in_status = 'Leave application is REJECTED';
                    } else {
                        $clock_in_status = 'On Leave';
                    }
                    $clock_in = $clock_out = 'leave';
                    $clock_out_status = $clock_in_status;
                } else {
                    // 2. Check Holiday
                    $holidaySql = "SELECT reason FROM salary_holy_day WHERE holy_day = ?";
                    $holidayStmt = $conn->prepare($holidaySql);
                    $holidayStmt->bind_param("s", $currentDate);
                    $holidayStmt->execute();
                    $holidayResult = $holidayStmt->get_result();
                    $holidayRow = $holidayResult->fetch_assoc();
                    $holidayReason = $holidayRow['reason'];
                    $holidayStmt->close();

                    if ($holidayReason) {
                        $clock_in = $clock_out = 'Holiday';
                        $clock_in_status = $clock_out_status = $holidayReason;
                    } else {
                        // 3. Check OSD
                        $osdSql = "SELECT status FROM hrm_od_attendance WHERE PBI_ID = ? AND attendance_date = ?";
                        $osdStmt = $conn->prepare($osdSql);
                        $osdStmt->bind_param("ss", $pbi_id, $currentDate);
                        $osdStmt->execute();
                        $osdResult = $osdStmt->get_result();
                        $osdRow = $osdResult->fetch_assoc();
                        $getOSDStatus = $osdRow['status'];
                        $osdStmt->close();

                        if ($getOSDStatus) {
                            if ($getOSDStatus == 'PENDING') {
                                $clock_in_status = 'Applied for OSD';
                            } elseif ($getOSDStatus == 'APPROVED') {
                                $clock_in_status = 'OSD is Approved';
                            } elseif ($getOSDStatus == 'REJECTED') {
                                $clock_in_status = 'OSD is REJECTED';
                            } else {
                                $clock_in_status = 'On Outside Duty';
                            }
                            $clock_in = $clock_out = 'OSD';
                            $clock_out_status = $clock_in_status;
                        } else {
                            // 4. Check Work From Home (WFH)
                            $wfhSql = "SELECT status FROM emp_access_work_from_home_application WHERE user_id = ? AND attendance_date = ?";
                            $wfhStmt = $conn->prepare($wfhSql);
                            $wfhStmt->bind_param("ss", $pbi_id, $currentDate);
                            $wfhStmt->execute();
                            $wfhResult = $wfhStmt->get_result();
                            $wfhRow = $wfhResult->fetch_assoc();
                            $getWFHStatus = $wfhRow['status'];
                            $wfhStmt->close();

                            if ($getWFHStatus) {
                                if ($getWFHStatus == 'PENDING') {
                                    $clock_in_status = 'Applied for WFH';
                                } elseif ($getWFHStatus == 'APPROVED') {
                                    $clock_in_status = 'Work from Home is Approved';
                                } elseif ($getWFHStatus == 'REJECTED') {
                                    $clock_in_status = 'WFH is REJECTED';
                                } else {
                                    $clock_in_status = 'Worked from Home';
                                }
                                $clock_in = $clock_out = 'WFH';
                                $clock_out_status = $clock_in_status;
                            } else {
                                // 5. Final fallback
                                $clock_in = $clock_out = 'Finger Missing';
                                $clock_in_status = $clock_out_status = 'Finger Missing';
                            }
                        }
                    }
                }
            }

            $allDates[] = [
                "date" => $currentDate,
                "clock_in" => $clock_in,
                "clock_out" => $clock_out,
                "clock_in_status" => $clock_in_status,
                "clock_out_status" => $clock_out_status,
                "late" => null,
                "early" => null
            ];
        }

        $current = strtotime("+1 day", $current);
    }

    usort($allDates, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    echo json_encode(["status" => "200", "data" => $allDates]);
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
}

$conn->close();
?>
