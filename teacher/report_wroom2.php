<?php 
/**
 * Report Wroom 2 - MVC Controller
 * Displays organization chart
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";
require_once "../class/Wroom.php";

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
if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    header("Location: ../login.php");
    exit;
}

// Extract teacher information
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Get teachers for this room
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Get wroom data
$wroomObj = new Wroom($db);
$wroom = $wroomObj->getWroomStudents($class, $room, $pee);
$maxim = $wroomObj->getMaxim($class, $room, $pee);

// Group students by position
$grouped = [];
foreach ($wroom as $stu) {
    $pos = $stu['wposit'];
    if (!isset($grouped[$pos])) $grouped[$pos] = [];
    $grouped[$pos][] = $stu;
}

// Set page title
$title = 'ผังโครงสร้างห้องเรียนสีขาว';

// Load the view
include __DIR__ . '/../views/teacher/report_wroom2.php';
?>
