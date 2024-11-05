<?php
include 'ZKLib.php';

$zk = new ZKLib('192.168.0.222', 4370); // Replace with your device IP and port

// Connect to the device
if ($zk->connect()) {
    echo "Connected to ZKTeco device!";
} else {
    echo "Failed to connect.";
}
?>
