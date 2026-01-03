<?php 
/**
 * Board Parent - MVC Controller
 * Handles authentication and page variables
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Student.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check login
if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    header("Location: ../login.php");
    exit;
}

// Extract teacher information
$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Get teachers for this room
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Set page title
$title = 'คณะกรรมการเครือข่ายผู้ปกครอง';

// Load the view
include __DIR__ . '/../views/teacher/board_parent.php';
?>
