<?php
/**
 * Controller: Student Visit Home Report (Officer)
 * MVC Pattern - Handles authentication and prepares context
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

// (3) Fetch Core Data
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Set Page Metadata
$pageTitle = 'รายงานการเยี่ยมบ้านรายบุคคล';
$activePage = 'report'; // Highlights sidebar item

// Preload Student details if student_id is passed via GET
$student_id = $_GET['student_id'] ?? '';
$preloadStudentName = '';
if (!empty($student_id)) {
    $preloadSql = "SELECT Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room FROM student WHERE Stu_id = :student_id";
    $preloadStmt = $db->prepare($preloadSql);
    $preloadStmt->bindParam(':student_id', $student_id);
    $preloadStmt->execute();
    $preloadStudent = $preloadStmt->fetch(PDO::FETCH_ASSOC);
    if ($preloadStudent) {
        $preloadStudentName = $preloadStudent['Stu_pre'] . $preloadStudent['Stu_name'] . ' ' . $preloadStudent['Stu_sur'] . ' (ม.' . $preloadStudent['Stu_major'] . '/' . $preloadStudent['Stu_room'] . ')';
    }
}

// Fetch 3 recently searched students from session
$recentStudents = $_SESSION['visithome_search_history'] ?? [];

// (5) Render View
include __DIR__ . '/../views/officer/report_student_visithome.php';
?>
