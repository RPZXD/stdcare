<?php
/**
 * Teacher Home Room - MVC Entry Point
 * Refactored to use MVC architecture
 */

session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Teacher.php';
require_once __DIR__ . '/../controllers/HomeroomController.php';
require_once __DIR__ . '/../class/Utils.php';

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);
$homeroomController = new HomeroomController($db);

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

// Get types for forms
$types = $homeroomController->getTypes();

// Get teachers for this room
$teacherList = $teacher->getTeachersByClassAndRoom($class, $room);

// Set page title
$pageTitle = 'กิจกรรมโฮมรูม';
$currentPage = 'home_room';

// Include the view
include __DIR__ . '/../views/teacher/home_room.php';
