<?php
/**
 * Controller: Monthly Attendance Report
 * MVC Pattern - Handles authentication and data preparation for the view
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);
$student_obj = new Student($db);

// (3) Fetch Core Data
$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$current_buddhist_year = $user->getPee();

// (4) Handle Filters
$report_year = $_GET['year'] ?? $current_buddhist_year;
$report_month = $_GET['month'] ?? date('m');
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// Days in month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $report_month, ($report_year - 543));

// (5) Fetch Report Data
$students = $student_obj->fetchFilteredStudents2($report_class, $report_room);
$attendance_map = [];
$summary_map = [];

if (!empty($students)) {
    $query = "SELECT student_id, attendance_date, attendance_status
              FROM student_attendance sa
              JOIN student s ON sa.student_id = s.Stu_id
              WHERE s.Stu_major = :class AND s.Stu_room = :room
                AND YEAR(sa.attendance_date) = :year 
                AND MONTH(sa.attendance_date) = :month";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':class' => $report_class,
        ':room' => $report_room,
        ':year' => ($report_year - 543),
        ':month' => $report_month
    ]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as $record) {
        $day = (int)date('j', strtotime($record['attendance_date']));
        $stu_id = $record['student_id'];
        $status = $record['attendance_status'];
        
        $attendance_map[$stu_id][$day] = $status;

        if (!isset($summary_map[$stu_id][$status])) {
            $summary_map[$stu_id][$status] = 0;
        }
        $summary_map[$stu_id][$status]++;
    }
}

// Data for View
$thai_months = [
    '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
    '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
    '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
    '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
];

$status_symbols = [
    '1' => '✅', '2' => '❌', '3' => '🕒',
    '4' => '🤒', '5' => '📝', '6' => '🎉',
];

$status_labels_legend = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'emerald'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'rose'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'amber'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'indigo'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'violet'],
];

// (6) Set Page Metadata
$pageTitle = 'รายงานเวลาเรียนประจำเดือน';
$activeMenu = 'care_system';
$activeSubMenu = 'report_study_month';

// (7) Render View
include __DIR__ . '/../views/teacher/report_study_month.php';
?>