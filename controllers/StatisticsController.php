<?php
namespace App\Controllers;

require_once __DIR__ . '/../models/Statistics.php';

use App\Models\Statistics;

class StatisticsController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        date_default_timezone_set('Asia/Bangkok');
        
        // Get date from request or use today
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        // Initialize model
        $model = new Statistics($this->db);
        
        // Get all data
        $studentCounts = $model->getStudentCounts();
        $attendanceStats = $model->getAttendanceStats($date);
        $juniorStats = $model->getDetailedStatsByLevel($date, 'junior');
        $seniorStats = $model->getDetailedStatsByLevel($date, 'senior');
        
        // Prepare data for view
        $data = [
            'title' => 'สถิติการมาเรียน',
            'date' => $date,
            'studentCounts' => $studentCounts,
            'attendanceStats' => $attendanceStats,
            'juniorStats' => $juniorStats,
            'seniorStats' => $seniorStats
        ];
        
        // Extract to make variables available in view
        extract($data);
        
        // Include view
        include __DIR__ . '/../views/statistics/index.php';
    }
}
