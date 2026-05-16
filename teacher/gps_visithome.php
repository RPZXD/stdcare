<?php
/**
 * Teacher GPS Visit Home Page - MVC Entry Point
 * Displays a map with all student locations for the teacher's class
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

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

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Fetch student GPS data for the class
$sql = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_no, 
               g.latitude, g.longitude, g.updated_at
        FROM student s
        JOIN student_gps g ON s.Stu_id = g.Stu_id
        WHERE s.Stu_major = ? AND s.Stu_room = ?
        ORDER BY CAST(s.Stu_no AS UNSIGNED)";
$stmt = $db->prepare($sql);
$stmt->execute([$class, $room]);
$studentGpsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Main page configuration
$pageTitle = "แผนที่บ้านนักเรียน";
$activePage = "visithome";

// Include view
include __DIR__ . '/../views/teacher/gps_visithome.php';
