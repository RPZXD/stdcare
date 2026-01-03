<?php
/**
 * Controller: Student EQ (std_eq.php)
 * MVC Pattern - EQ assessment page
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
require_once __DIR__ . '/../class/EQ.php';

// Initialize database
$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);
$eq = new EQ($studentConn);

// Get student data
$student_id = $_SESSION['Student_login'];
$term = $user->getTerm();
$pee = $user->getPee();

$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Get EQ status
$eqData = $eq->getEQData($student_id, $pee, $term);
$eqSaved = !empty($eqData);

// Set page metadata
$pageTitle = 'แบบประเมิน EQ';
$activePage = 'eq';

// Render view
include __DIR__ . '/../views/student/eq.php';
?>
