<?php
/**
 * Teacher Profile Page - MVC Entry Point
 * Handles teacher data retrieval and passes it to the view
 */
session_start();

require_once "../config/Database.php";
require_once "../config/Setting.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);
$setting = new Setting();

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

// Page configuration
$pageTitle = "โปรไฟล์ - ระบบดูแลช่วยเหลือนักเรียน";
$activePage = "profile";

// Include modern view
include __DIR__ . '/../views/teacher/profile.php';
