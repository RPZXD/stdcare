<?php 
/**
 * Teacher Visit Home Memo Print - MVC Controller
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Poor.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacherObj = new Teacher($db);
$poorObj = new Poor($db);

// Fetch terms and pee from URL or session
$term = isset($_GET['term']) ? $_GET['term'] : $user->getTerm();
$pee = isset($_GET['pee']) ? $_GET['pee'] : $user->getPee();

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
$roomTeachers = $teacherObj->getTeachersByClassAndRoom($class, $room);

// Fetch poor students in this classroom
$poorStudents = $poorObj->getPoorByClassAndRoom($class, $room);

// Include the print view
include __DIR__ . '/../views/teacher/visithome_memo_print.php';
