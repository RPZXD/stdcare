<?php
/**
 * Controller: Termly Attendance Report
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
$current_term = $user->getTerm();
$current_buddhist_year = $user->getPee();

// (4) Handle Filters
$report_term = $_GET['term'] ?? $current_term;
$report_year = $_GET['year'] ?? $current_buddhist_year;
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// (5) Fetch Report Data
$students = $student_obj->fetchFilteredStudents2($report_class, $report_room);
$summary_map = [];

if (!empty($students)) {
    $query = "SELECT
                sa.student_id,
                sa.attendance_status,
                COUNT(sa.id) AS status_count
              FROM student_attendance sa
              JOIN student s ON sa.student_id = s.Stu_id
              WHERE s.Stu_major = :class AND s.Stu_room = :room
                AND sa.term = :term AND sa.year = :year
              GROUP BY sa.student_id, sa.attendance_status";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':class' => $report_class,
        ':room' => $report_room,
        ':term' => $report_term,
        ':year' => $report_year
    ]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as $record) {
        $summary_map[$record['student_id']][$record['attendance_status']] = $record['status_count'];
    }
}

// Support for Status Labels for View
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'emerald'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'rose'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'amber'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'indigo'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'violet'],
];

// (6) Set Page Metadata
$pageTitle = 'เวลาเรียนรายเทอม';
$activeMenu = 'care_system';
$activeSubMenu = 'report_study_term';

// (7) Render View
include __DIR__ . '/../views/teacher/report_study_term.php';
?>