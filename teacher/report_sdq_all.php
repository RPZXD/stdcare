<?php
/**
 * SDQ Report All - Controller
 * MVC Pattern
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
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$pageTitle = 'รายงานสถิติ SDQ';

// Fetch all teachers in this room
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Include the view
include __DIR__ . '/../views/teacher/report_sdq_all.php';
