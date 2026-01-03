<?php
/**
 * Controller: Student Information (std_information.php)
 * MVC Pattern - Student profile information page
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

// Get student ID and data
$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Check edit permission
$class = $student['Stu_major'];
$room = $student['Stu_room'];
$canEdit = false;
$jsonFile = __DIR__ . '/../teacher/api/student_edit_permission.json';
if (file_exists($jsonFile)) {
    $json = json_decode(file_get_contents($jsonFile), true);
    $key = $class . '-' . $room;
    if (isset($json['permissions'][$key]) && !empty($json['permissions'][$key]['allowEdit'])) {
        $canEdit = true;
    }
}

// Get profile image path
$profileImgPath = 'https://std.phichai.ac.th/photo/' . $student['Stu_picture'];

// Thai date function
function thai_date($strDate) {
    if (empty($strDate)) return '-';
    $strYear = date("Y", strtotime($strDate));
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $thaiMonths = [
        "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
    ];
    $strMonthThai = $thaiMonths[$strMonth];
    return "$strDay $strMonthThai $strYear";
}

// Set page metadata
$pageTitle = 'ข้อมูลนักเรียน';
$activePage = 'information';

// Render view
include __DIR__ . '/../views/student/information.php';
?>
