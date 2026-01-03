<?php
namespace App\Controllers;

use App\Models\Home;
use App\Models\User; // Assuming User model for term/year or similar
require_once __DIR__ . '/../class/UserLogin.php';

class HomeController
{
    private $db;
    private $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new \UserLogin($db);
    }

    public function index()
    {
        $homeModel = new Home($this->db);

        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $term = $this->user->getTerm();
        $year = $this->user->getPee();

        $stats = $homeModel->getAttendanceStats($date, $term, $year);
        $studentCounts = $homeModel->getStudentCounts();
        $classAttendance = $homeModel->getClassAttendance($date, $term, $year);

        $data = [
            'date' => $date,
            'stats' => $stats,
            'studentCounts' => $studentCounts,
            'classes' => $classAttendance['classes'],
            'status_count' => $classAttendance['status_count'],
            'total' => $classAttendance['total'],
            'title' => 'ระบบดูแลช่วยเหลือนักเรียน - หน้าแรก'
        ];

        // Pass data to view
        extract($data);
        include __DIR__ . '/../views/home/index.php';
    }
}
