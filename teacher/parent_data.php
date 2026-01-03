<?php
/**
 * Parent Data Page - MVC Entry Point
 * View parent/guardian information for students in teacher's class
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'] ?? 0;
$room = $userData['Teach_room'] ?? 0;

// Fetch all homeroom teachers for this class/room (only active teachers)
$roomTeachers = [];
if ($class && $room) {
    $stmt = $db->prepare("SELECT Teach_id, Teach_name FROM teacher WHERE Teach_class = ? AND Teach_room = ? AND Teach_status = 1 ORDER BY Teach_id");
    $stmt->execute([$class, $room]);
    $roomTeachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Layout configuration
$pageTitle = "ข้อมูลผู้ปกครอง - ระบบดูแลช่วยเหลือนักเรียน";
$activePage = "parent_data";

// Include the view
include __DIR__ . '/../views/teacher/parent_data.php';
