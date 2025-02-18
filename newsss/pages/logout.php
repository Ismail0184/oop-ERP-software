<?php
require_once 'support_file.php';
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$access_time_out=$dateTime->format("Y-m-d, h:i:s A");
$update = mysqli_query($conn, "Update user_activity_log set access_time_out='".$access_time_out."' where user_id='".$_SESSION['userid']."' and access_token='".$_SESSION['aToken']."'");
   session_destroy();
   unset($_POST);
   header('Location: ../pages/');
?>
