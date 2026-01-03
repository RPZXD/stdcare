<?php
/**
 * Controller: Student SDQ (std_sdq.php)
 * MVC Pattern - SDQ assessment page
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
require_once __DIR__ . '/../class/SDQ.php';

// Initialize database
$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);
$sdq = new SDQ($studentConn);

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

// Get SDQ status
$selfData = $sdq->getSDQSelfData($student_id, $pee, $term);
$selfSaved = !empty($selfData['answers']);

$parentData = $sdq->getSDQParData($student_id, $pee, $term);
$parentSaved = !empty($parentData['answers']);

// Set page metadata
$pageTitle = 'แบบประเมิน SDQ';
$activePage = 'sdq';

// Render view
include __DIR__ . '/../views/student/sdq.php';
?>
