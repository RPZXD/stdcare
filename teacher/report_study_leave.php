<?php
/**
 * Controller: Student Leave/Absent Report
 * MVC Pattern - Handles authentication and data preparation for the view
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Attendance.php';
require_once __DIR__ . '/../class/AttendanceSummary.php';
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
$attendance = new Attendance($db);

// (3) Fetch Core Data
$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Handle Filters
$report_date = $_GET['date'] ?? date('Y-m-d');
$report_class = $_GET['class'] ?? 'all';
$report_room = $_GET['room'] ?? 'all';

// (5) Fetch Report Data
$all_students = $attendance->getStudentsWithAttendance(
    $report_date, 
    ($report_class === 'all' ? null : $report_class), 
    ($report_room === 'all' ? null : $report_room), 
    $term, 
    $pee
);

// Helper for status labels from AttendanceSummary
$temp_summary = new AttendanceSummary([], 0, 0, '', '', '');
$status_labels = $temp_summary->status_labels;

// Filter for absent/late/leave students (status is NOT '1' or NULL)
$absent_students = [];
foreach ($all_students as $s) {
    $status = $s['attendance_status'];
    if ($status === null || $status != '1') {
        $status_key = $status ?? '2'; // Default null to '2' (Absent)
        $s['display_status'] = $status_labels[$status_key] ?? ['label' => 'ไม่ทราบ', 'emoji' => '❓'];
        
        // Assign colors for statuses
        $color_map = [
            '2' => 'rose',   // ขาด
            '3' => 'amber',  // สาย
            '4' => 'blue',   // ลาป่วย
            '5' => 'indigo', // ลากิจ
            '6' => 'violet', // กิจกรรม
        ];
        $s['status_color'] = $color_map[$status_key] ?? 'slate';
        
        $absent_students[] = $s;
    }
}

// (6) Set Page Metadata
$pageTitle = 'รายชื่อนักเรียนที่ไม่มาเรียน';
$activeMenu = 'care_system';
$activeSubMenu = 'report_study_leave';

// (7) Render View
include __DIR__ . '/../views/teacher/report_study_leave.php';
?>