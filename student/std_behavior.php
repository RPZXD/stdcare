<?php
/**
 * Controller: Student Behavior (std_behavior.php)
 * MVC Pattern - Student behavior score page
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
require_once __DIR__ . '/../class/Behavior.php';

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

// Get behavior records
$behavior = new Behavior($studentConn);
$behaviors = $behavior->getBehaviorsByStudentId($student_id, $term, $pee);

// Good deed types (add points)
$goodDeedTypes = ['ความดี', 'จิตอาสาช่วยเหลือครู', 'ช่วยเหลือเพื่อน', 'เก็บของได้ส่งคืน', 'บำเพ็ญประโยชน์'];

// Calculate scores
$deductionPoints = 0;
$bonusPoints = 0;
$deductionRecords = [];
$bonusRecords = [];

if ($behaviors && is_array($behaviors)) {
    foreach ($behaviors as $b) {
        $score = abs((int)$b['behavior_score']);
        $isGoodDeed = in_array($b['behavior_type'], $goodDeedTypes);
        
        if ($isGoodDeed) {
            $bonusPoints += $score;
            $bonusRecords[] = $b;
        } else {
            $deductionPoints += $score;
            $deductionRecords[] = $b;
        }
    }
}

// Net score = 100 - deductions + bonus (capped at 0-100)
$netScore = max(0, min(100, 100 - $deductionPoints + $bonusPoints));

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
$pageTitle = 'คะแนนพฤติกรรม';
$activePage = 'behavior';

// Render view
include __DIR__ . '/../views/student/behavior.php';
?>
