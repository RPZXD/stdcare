<?php 
/**
 * Teacher Visit Home Report - MVC Controller
 * Handles authentication and page variables
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    header("Location: ../login.php");
    exit;
}

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Fetch room teachers
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Include the view
include __DIR__ . '/../views/teacher/visithome_report_class.php';
