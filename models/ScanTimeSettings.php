<?php
namespace App\Models;

class ScanTimeSettings
{
    private $conn;

    public function __construct($pdo)
    {
        $this->conn = $pdo;
    }

    public function getSettings()
    {
        $stmt = $this->conn->prepare("SELECT * FROM scan_time_settings LIMIT 1");
        try {
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
        } catch (\Throwable $e) {
            // ignore and return defaults below
        }
        // Defaults if table missing or empty
        return [
            'arrival_cutoff' => '08:00:00',
            'arrival_absent_after' => '12:00:00',
            'leave_cutoff' => '15:00:00'
        ];
    }
}
