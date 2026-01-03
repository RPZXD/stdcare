<?php
/**
 * Controller: Director Reports
 * MVC Pattern - Handles authentication and logic for director report pages
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Director_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student = new Student($db);

// (3) Fetch Core Context
$userid = $_SESSION['Director_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Handle Reporting Tab Logic
$tab = $_GET['tab'] ?? 'late';

// (5) Set Page Metadata
$pageTitle = 'รายงานข้อมูลคุณภาพ - Director';
$activePage = 'report';

// (6) Render View
include __DIR__ . '/../views/director/report.php';
?>
