<?php
/**
 * Controller: Daily Attendance Report
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
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// (5) Fetch Report Data
$students = $attendance->getStudentsWithAttendance($report_date, $report_class, $report_room, $term, $pee);
$summary = new AttendanceSummary($students, $report_class, $report_room, $report_date, $term, $pee);

// (6) Set Page Metadata
$pageTitle = 'เวลาเรียนประจำวัน';
$activeMenu = 'care_system';
$activeSubMenu = 'report_study_day';

// (7) Render View
include __DIR__ . '/../views/teacher/report_study_day.php';
?>