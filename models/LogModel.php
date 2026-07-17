<?php
class LogModel {
    private $dataFile = __DIR__ . '/../data/sms_logs.json';

    public function __construct() {
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }
    }

    public function getAllLogs() {
        $json = file_get_contents($this->dataFile);
        $logs = json_decode($json, true);
        return is_array($logs) ? $logs : [];
    }

    public function addLog($logData) {
        $logs = $this->getAllLogs();
        // Insert at the beginning so newest logs are first
        array_unshift($logs, $logData);
        
        // Keep only last 1000 logs to prevent file from growing indefinitely
        if (count($logs) > 1000) {
            $logs = array_slice($logs, 0, 1000);
        }
        
        file_put_contents($this->dataFile, json_encode($logs, JSON_PRETTY_PRINT));
    }
}
