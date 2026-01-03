<?php 
/**
 * Teacher Report Controller
 * Refactored to MVC Pattern
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/Database.php");
require_once("../config/Setting.php");
require_once("../class/UserLogin.php");
require_once("../class/Student.php");
require_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$student = new Student($db);
$setting = new Setting();

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Page metadata
$pageTitle = "สรุปรายงานต่างๆ - ระบบดูแลช่วยเหลือนักเรียน";
$activePage = "report";

// Load View
include __DIR__ . '/../views/teacher/report.php';
?>
