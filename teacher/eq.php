<?php
/**
 * Teacher EQ Assessment - MVC Controller
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

// Fetch all teachers in this room for signatures
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

$pageTitle = 'แบบประเมิน EQ';

// Include the view
include __DIR__ . '/../views/teacher/eq.php';