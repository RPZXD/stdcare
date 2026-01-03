<?php
/**
 * Controller: Student Search Data (std_search_data.php)
 * MVC Pattern - Search students and teachers page
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
require_once __DIR__ . '/../config/Setting.php';

// Initialize database
$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);
$setting = new Setting($studentConn);

// Get student data
$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Get image profile paths
$imgProfileStudent = 'https://std.phichai.ac.th/photo/';
$imgProfile = $setting->getImgProfile();

// Set page metadata
$pageTitle = 'ค้นหาข้อมูล';
$activePage = 'search';

// Render view
include __DIR__ . '/../views/student/search_data.php';
?>
