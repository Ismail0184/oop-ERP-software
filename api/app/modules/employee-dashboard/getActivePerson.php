<?php
header("Content-Type: application/json");
require("../../../../app/db/base.php");

$sql = "SELECT 
                p.PBI_ID as id,
                CONCAT(p.PBI_ID_UNIQUE, ' : ', p.PBI_NAME, ' : ', d.DEPT_SHORT_NAME) AS name
            FROM 
                personnel_basic_info p,
                department d,
                essential_info e
            WHERE 
                p.PBI_JOB_STATUS IN ('In Service') AND
                p.PBI_DEPARTMENT = d.DEPT_ID AND
                p.PBI_ID = e.PBI_ID AND
                e.ESS_JOB_LOCATION = 1
            GROUP BY 
                p.PBI_ID
            ORDER BY 
                p.PBI_NAME";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["statusCode" => "200", "data" => $data]);

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
}

$conn->close();

?>