<?php
/**
 * Controller: Director Teacher Data
 * MVC Pattern - Handles authentication and includes view
 */
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check authentication
if (isset($_SESSION['Director_login'])) {
    $userid = $_SESSION['Director_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

// Set page title for layout
$pageTitle = 'ข้อมูลบุคลากร';

// Set active sidebar menu
$activePage = 'teacher';

// Include the view
include_once('../views/director/data_teacher.php');
?>
