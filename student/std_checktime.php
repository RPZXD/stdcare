<?php
/**
 * Controller: Student Checktime (std_checktime.php)
 * MVC Pattern - Student attendance time page
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

// Get attendance records
$attendanceRows = [];
try {
    $stmt = $studentConn->prepare("SELECT * FROM student_attendance WHERE student_id = :stu_id AND term = :term AND year = :year ORDER BY attendance_date DESC");
    $stmt->execute([':stu_id' => $student_id, ':term' => $term, ':year' => $pee]);
    $attendanceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $attendanceRows = [];
}

// Calculate statistics
$currentMonth = date('Y-m');
$parts = explode('-', $currentMonth);
if (count($parts) === 2) {
    $parts[0] = (string)((int)$parts[0] + 543);
    $currentMonth = implode('-', $parts);
}

$monthStats = ['1'=>0, '2'=>0, '3'=>0, '4'=>0, '5'=>0, '6'=>0];
$termStats = ['1'=>0, '2'=>0, '3'=>0, '4'=>0, '5'=>0, '6'=>0];
$monthRows = [];

foreach ($attendanceRows as $row) {
    $status = $row['attendance_status'];
    if (isset($termStats[$status])) {
        $termStats[$status]++;
    }
    if (!empty($row['attendance_date']) && strpos($row['attendance_date'], $currentMonth) === 0) {
        if (isset($monthStats[$status])) {
            $monthStats[$status]++;
        }
        $monthRows[] = $row;
    }
}

// Status text function
function attendance_status_text($status) {
    $statuses = [
        '1' => ['text' => 'มาเรียน', 'color' => 'emerald', 'emoji' => '✅', 'bg' => 'bg-emerald-500'],
        '2' => ['text' => 'ขาดเรียน', 'color' => 'red', 'emoji' => '❌', 'bg' => 'bg-red-500'],
        '3' => ['text' => 'มาสาย', 'color' => 'amber', 'emoji' => '⏰', 'bg' => 'bg-amber-500'],
        '4' => ['text' => 'ลาป่วย', 'color' => 'blue', 'emoji' => '🤒', 'bg' => 'bg-blue-500'],
        '5' => ['text' => 'ลากิจ', 'color' => 'purple', 'emoji' => '📝', 'bg' => 'bg-purple-500'],
        '6' => ['text' => 'กิจกรรม', 'color' => 'pink', 'emoji' => '🎉', 'bg' => 'bg-pink-500'],
    ];
    return $statuses[$status] ?? ['text' => 'ไม่ระบุ', 'color' => 'slate', 'emoji' => '⚪', 'bg' => 'bg-slate-400'];
}

// Thai date function
function thai_date($strDate) {
    if (empty($strDate)) return '-';
    $strYear = date("Y", strtotime($strDate));
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $thaiMonths = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    return "$strDay {$thaiMonths[$strMonth]} $strYear";
}

// Set page metadata
$pageTitle = 'เวลาเรียน';
$activePage = 'checktime';

// Render view
include __DIR__ . '/../views/student/checktime.php';
?>
