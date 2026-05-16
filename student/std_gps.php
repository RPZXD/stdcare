<?php
/**
 * Controller: Record GPS (std_gps.php)
 * MVC Pattern - Student GPS location recording page
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

// Check authentication
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

// Include dependencies
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';

// Initialize database
$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

// Get student ID and data
$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Fetch current GPS if exists
$queryGps = "SELECT * FROM student_gps WHERE Stu_id = :id LIMIT 1";
$stmtGps = $studentConn->prepare($queryGps);
$stmtGps->bindParam(":id", $student_id);
$stmtGps->execute();
$currentGps = $stmtGps->fetch(PDO::FETCH_ASSOC);

// Set page metadata
$pageTitle = 'บันทึกพิกัด GPS';
$activePage = 'gps';
$role = 'student';

// Render view
include __DIR__ . '/../views/student/std_gps.php';
?>
