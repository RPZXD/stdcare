<?php
/**
 * Controller: Student Visit Home (std_visit_home.php)
 * MVC Pattern - Home visit records page
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

// Get visit records for this student
$visitRecords = [];
try {
    $stmt = $studentConn->prepare("SELECT * FROM visithome WHERE Stu_id = :stu_id AND Pee = :pee ORDER BY Term ASC");
    $stmt->execute([':stu_id' => $student_id, ':pee' => $pee]);
    $visitRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $visitRecords = [];
}

// Map visits by Term (1 or 2)
$visits = [];
foreach ($visitRecords as $v) {
    $visits[$v['Term']] = $v;
}

// Set page metadata
$pageTitle = 'บันทึกการเยี่ยมบ้าน';
$activePage = 'visit_home';

// Render view
include __DIR__ . '/../views/student/visit_home.php';
?>
