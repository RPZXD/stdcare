<?php
/**
 * Controller: Director Behavior Management
 * MVC Pattern - Handles authentication and logic for behavior management
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Behavior.php';
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

// (3) Fetch Core Context
$userid = $_SESSION['Director_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Set Page Metadata
$pageTitle = 'จัดการข้อมูลพฤติกรรมนักเรียน - Director';
$activePage = 'behavior';

// (5) Render View
include __DIR__ . '/../views/director/data_behavior.php';
?>
