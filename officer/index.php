<?php
/**
 * Controller: Officer Dashboard
 * MVC Pattern - Handles authentication and data aggregation for the dashboard
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student_class = new Student($db); // Use a distinct variable name to avoid confusion if needed

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Fetch Statistics Data
// - Total Students
$studentCount = $db->query("SELECT COUNT(*) FROM student WHERE Stu_status=1")->fetchColumn() ?: 0;
// - Total personnel
$teacherCount = $db->query("SELECT COUNT(*) FROM teacher WHERE Teach_status=1")->fetchColumn() ?: 0;
// - Total behavior records for this term/year
$behaviorCount = $db->query("SELECT COUNT(*) FROM behavior WHERE behavior_term = '$term' AND behavior_pee = '$pee'")->fetchColumn() ?: 0;
// - Total behavior score sum
$behaviorScore = $db->query("SELECT SUM(behavior_score) FROM behavior WHERE behavior_term = '$term' AND behavior_pee = '$pee'")->fetchColumn() ?: 0;

// (5) Aggregate Score Groups for Chart
$scoreGroups = [
    'เข้าค่ายปรับพฤติกรรม (<50)' => 0,
    'บำเพ็ญประโยชน์ 20 ชม. (50-70)' => 0,
    'บำเพ็ญประโยชน์ 10 ชม. (71-99)' => 0,
    'ปกติ/ไม่มีคะแนน' => 0
];

$scoreStmt = $db->query("
    SELECT s.Stu_id, COALESCE(SUM(b.behavior_score),0) AS total_score
    FROM student s
    LEFT JOIN behavior b ON s.Stu_id = b.stu_id AND b.behavior_term = '$term' AND b.behavior_pee = '$pee'
    WHERE s.Stu_status=1
    GROUP BY s.Stu_id
");

while ($row = $scoreStmt->fetch(PDO::FETCH_ASSOC)) {
    $score = (int)$row['total_score'];
    if ($score == 0) {
        $scoreGroups['ปกติ/ไม่มีคะแนน']++;
    } elseif ($score < 50) {
        $scoreGroups['เข้าค่ายปรับพฤติกรรม (<50)']++;
    } elseif ($score <= 70) {
        $scoreGroups['บำเพ็ญประโยชน์ 20 ชม. (50-70)']++;
    } elseif ($score <= 99) {
        $scoreGroups['บำเพ็ญประโยชน์ 10 ชม. (71-99)']++;
    }
}

// (6) Set Page Metadata
$pageTitle = 'Officer Dashboard - ระบบดูแลช่วยเหลือนักเรียน';
$activePage = 'dashboard';

// (7) Render View
include __DIR__ . '/../views/officer/index.php';
?>
