<?php
/**
 * Teacher Take Care Page - MVC Entry Point
 * Handles student care system overview
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Student.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$student = new Student($db);

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

// Page configuration
$pageTitle = "ระบบดูแลช่วยเหลือ - ระบบดูแลช่วยเหลือนักเรียน";
$activePage = "take_care";

// Include modern view
include __DIR__ . '/../views/teacher/take_care.php';
