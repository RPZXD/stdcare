<?php
/**
 * Check Attendance Controller
 * MVC Pattern - Handles teacher check attendance logic
 */

namespace App\Controllers;

require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Attendance.php';
require_once __DIR__ . '/../class/Utils.php';

class CheckAttendanceController
{
    private $db;
    private $user;
    private $attendance;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new \UserLogin($db);
        $this->attendance = new \Attendance($db);
    }

    /**
     * แปลงวันที่เป็นรูปแบบไทย
     */
    private function thaiDate($date)
    {
        $months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            $year = (int)$m[1];
            $month = (int)$m[2];
            $day = (int)$m[3];
            if ($year < 2500) $year += 543;
            return $day . ' ' . $months[$month] . ' ' . $year;
        }
        return $date;
    }

    public function index()
    {
        // Check login
        if (!isset($_SESSION['Teacher_login'])) {
            header('Location: ../login.php');
            exit;
        }

        $userid = $_SESSION['Teacher_login'];
        $userData = $this->user->userData($userid);
        
        // Save to session
        $_SESSION['teacher_data'] = $userData;
        
        $term = $this->user->getTerm();
        $pee = $this->user->getPee();
        
        $class = $userData['Teach_class'];
        $room = $userData['Teach_room'];
        
        // Get date from GET or use today
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $dateDisplay = $this->thaiDate($date);
        
        // Get students with attendance
        $students = $this->attendance->getStudentsWithAttendance($date, $class, $room, $term, $pee);
        
        // Calculate stats
        $totalStudents = count($students);
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $notChecked = 0;
        
        foreach ($students as $std) {
            if (!empty($std['attendance_status'])) {
                switch ($std['attendance_status']) {
                    case '1':
                        $presentCount++;
                        break;
                    case '2':
                        $absentCount++;
                        break;
                    case '3':
                        $lateCount++;
                        break;
                    case '4':
                    case '5':
                    case '6':
                        // ลา/กิจกรรม count as present for stats
                        $presentCount++;
                        break;
                }
            } else {
                $notChecked++;
            }
        }
        
        // Prepare data for view
        $data = [
            'pageTitle' => 'เช็คชื่อนักเรียน',
            'userData' => $userData,
            'class' => $class,
            'room' => $room,
            'term' => $term,
            'pee' => $pee,
            'date' => $date,
            'dateDisplay' => $dateDisplay,
            'students' => $students,
            'totalStudents' => $totalStudents,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'notChecked' => $notChecked
        ];
        
        extract($data);
        
        include __DIR__ . '/../views/teacher/check_std.php';
    }
}
