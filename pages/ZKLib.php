<?php

class ZKLib
{
    private $ip;
    private $port;
    private $zk;
    private $connected = false;

    public function __construct($ip, $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->zk = fsockopen($ip, $port, $errno, $errstr, 5);

        if ($this->zk) {
            $this->connected = true;
        } else {
            throw new Exception("Could not connect to ZKTeco device: $errstr ($errno)");
        }
    }

    public function connect()
    {
        return $this->connected;
    }

    public function disconnect()
    {
        if ($this->zk) {
            fclose($this->zk);
        }
    }

    public function getAttendance()
    {
        if (!$this->connected) {
            throw new Exception("Not connected to the device.");
        }

        // Send a command to the device to retrieve attendance logs
        $command = "\x1D\x00\x07\x00"; // Adjust if needed for your device
        fwrite($this->zk, $command);

        $response = fread($this->zk, 1024);

        // Example parse logic, depending on your device's data format
        $logs = [];
        for ($i = 0; $i < strlen($response); $i += 40) {
            $data = substr($response, $i, 40);
            $userId = unpack("V", substr($data, 0, 4))[1];
            $timestamp = $this->convertToDate(substr($data, 4, 4));
            $status = ord($data[8]); // 0: check-in, 1: check-out

            $logs[] = [
                'id' => $userId,
                'timestamp' => $timestamp,
                'status' => $status
            ];
        }

        return $logs;
    }

    private function convertToDate($data)
    {
        $timestamp = unpack("V", $data)[1];
        return date("Y-m-d H:i:s", $timestamp);
    }
}
?>
