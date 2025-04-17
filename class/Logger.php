<?php
class Logger {
    private $logFile;

    public function __construct($filePath) {
        $this->logFile = $filePath;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, "[]"); // Initialize as empty JSON array
        }
    }

    public function log($data) {
        $logs = json_decode(file_get_contents($this->logFile), true);
        $logs[] = $data;
        file_put_contents($this->logFile, json_encode($logs, JSON_PRETTY_PRINT));
    }
}
?>
