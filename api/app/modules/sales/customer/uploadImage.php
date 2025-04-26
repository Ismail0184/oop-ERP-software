<?php
$response = [];

if (isset($_FILES['image'])) {
    // Set absolute path based on actual directory structure
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/customer/photo/';

    // Ensure the directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $response['status'] = 'success';
        $response['image_url'] = 'http://icpd.icpbd-erp.com/assets/images/customer/photo/' . $fileName;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to move uploaded file';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No file uploaded';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
