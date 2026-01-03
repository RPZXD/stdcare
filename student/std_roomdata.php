<?php
/**
 * Controller: Student Roomdata (std_roomdata.php)
 * MVC Pattern - Classmates list page
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
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Get term and year
$term = $user->getTerm();
$pee = $user->getPee();

// Get classmates from same room
$query = "SELECT * FROM student WHERE Stu_major = :major AND Stu_room = :room AND Stu_status = 1 ORDER BY Stu_no ASC";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":major", $student['Stu_major']);
$stmt->bindParam(":room", $student['Stu_room']);
$stmt->execute();
$classmates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count statistics
$totalStudents = count($classmates);
$maleCount = 0;
$femaleCount = 0;
foreach ($classmates as $c) {
    if (in_array($c['Stu_pre'], ['นาย', 'เด็กชาย'])) {
        $maleCount++;
    } else {
        $femaleCount++;
    }
}

// Set page metadata
$pageTitle = 'ข้อมูลห้องเรียน';
$activePage = 'roomdata';

// Render view
include __DIR__ . '/../views/student/roomdata.php';
?>
