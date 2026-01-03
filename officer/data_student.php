<?php
/**
 * Controller: Student Data Management (Officer)
 * MVC Pattern - Handles authentication and prepares data for the student list view
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
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
$student_class = new Student($db);

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Prepare Data for View
$pageTitle = 'ข้อมูลนักเรียน';
$activeMenu = 'student_data';

// (5) Render View
include __DIR__ . '/../views/officer/data_student.php';
?>
