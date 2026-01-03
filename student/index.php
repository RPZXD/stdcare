<?php
/**
 * Controller: Student Dashboard (index.php)
 * MVC Pattern - Student portal main page
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

// Get student ID and data
$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for layout
$_SESSION['student_data'] = $student;

// Get term and year
$term = method_exists($user, 'getTerm') ? $user->getTerm() : 1;
$pee = method_exists($user, 'getPee') ? $user->getPee() : (date('Y') + 543);

// Calculate behavior score (with bonus points)
$behavior_deduction = 0; // คะแนนหัก
$behavior_bonus = 0;     // คะแนนบวก (จิตอาสา/ความดี)

// List of good deed types that add points
$goodDeedTypes = ['ความดี', 'จิตอาสาช่วยเหลือครู', 'ช่วยเหลือเพื่อน', 'เก็บของได้ส่งคืน', 'บำเพ็ญประโยชน์'];

if ($term && $pee) {
    $behavior = new Behavior($studentConn);
    $behaviors = $behavior->getBehaviorsByStudentId($student_id, $term, $pee);
    if ($behaviors && is_array($behaviors)) {
        foreach ($behaviors as $b) {
            $score = (int)$b['behavior_score'];
            $type = $b['behavior_type'] ?? '';
            
            // Check if it's a good deed (add points) or misconduct (deduct points)
            if (in_array($type, $goodDeedTypes)) {
                $behavior_bonus += $score;
            } else {
                $behavior_deduction += $score;
            }
        }
    }
}

// Net score = 100 - deduction + bonus (capped at 0-100)
$behavior_score = max(0, min(100, 100 - $behavior_deduction + $behavior_bonus));

// Get attendance stats
$attendance_stats = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
try {
    $stmt = $studentConn->prepare("
        SELECT attendance_status, COUNT(*) as total
        FROM student_attendance
        WHERE student_id = :stu_id AND term = :term AND year = :year
        GROUP BY attendance_status
    ");
    $stmt->bindParam(':stu_id', $student_id);
    $stmt->bindParam(':term', $term);
    $stmt->bindParam(':year', $pee);
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $attendance_stats[$row['attendance_status']] = $row['total'];
    }
} catch (Exception $e) {
    // Keep defaults
}

// Set page metadata
$pageTitle = 'หน้าหลัก';
$activePage = 'dashboard';

// Render view
include __DIR__ . '/../views/student/index.php';
?>
