<?php
/**
 * Controller: Study Late/Absent Report (Class-based)
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
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

// (3) Fetch Core Data
$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Render View
include __DIR__ . '/../views/teacher/report_study_late.php';
