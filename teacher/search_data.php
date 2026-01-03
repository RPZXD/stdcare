<?php 
/**
 * Search Data - MVC Controller
 * Search for teachers and students
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Student.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";
require_once "../config/Setting.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$student = new Student($db);
$teacher = new Teacher($db);
$setting = new Setting($db);

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

// Get image profile links
$imgProfileTeacher = $setting->getImgProfile();
$imgProfileStudent = $setting->getImgProfileStudent();

// Set page title
$title = 'ค้นหาข้อมูล';

// Load the view
include __DIR__ . '/../views/teacher/search_data.php';
?>
