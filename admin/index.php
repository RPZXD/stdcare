<?php
/**
 * Controller: Admin Dashboard
 * MVC Pattern - Handles authentication and data preparation
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Admin_login'])) {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student = new Student($db);

// (3) Fetch Core Context
$userid = $_SESSION['Admin_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// Store in session for layout
$_SESSION['admin_data'] = $userData;

// (4) Fetch Statistics Data
$stats = [
    'students' => 0,
    'teachers' => 0,
    'behavior' => 0,
    'behaviorScore' => 0,
];

try {
    $stats['students'] = $db->query("SELECT COUNT(*) FROM student WHERE Stu_status=1")->fetchColumn() ?: 0;
} catch (Exception $e) {}

try {
    $stats['teachers'] = $db->query("SELECT COUNT(*) FROM teacher WHERE Teach_status=1")->fetchColumn() ?: 0;
} catch (Exception $e) {}

try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM behavior WHERE behavior_term = :term AND behavior_pee = :pee");
    $stmt->execute([':term' => $term, ':pee' => $pee]);
    $stats['behavior'] = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}

try {
    $stmt = $db->prepare("SELECT COALESCE(SUM(behavior_score), 0) FROM behavior WHERE behavior_term = :term AND behavior_pee = :pee");
    $stmt->execute([':term' => $term, ':pee' => $pee]);
    $stats['behaviorScore'] = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}

// (5) Fetch Score Groups for Chart
$scoreGroups = [
    'เข้าค่าย (<50)' => 0,
    'บำเพ็ญ 20 ชม. (50-70)' => 0,
    'บำเพ็ญ 10 ชม. (71-99)' => 0,
    'ปกติ (100)' => 0
];

try {
    $stmt = $db->prepare("
        SELECT s.Stu_id, COALESCE(SUM(b.behavior_score), 0) AS total_score
        FROM student s
        LEFT JOIN behavior b ON s.Stu_id = b.stu_id AND b.behavior_term = :term AND b.behavior_pee = :pee
        WHERE s.Stu_status = 1
        GROUP BY s.Stu_id
    ");
    $stmt->execute([':term' => $term, ':pee' => $pee]);
    
    $totalStudentsForGraph = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $score = 100 - (int)($row['total_score'] ?? 0);
        if ($score < 50) {
            $scoreGroups['เข้าค่าย (<50)']++;
        } elseif ($score <= 70) {
            $scoreGroups['บำเพ็ญ 20 ชม. (50-70)']++;
        } elseif ($score <= 99) {
            $scoreGroups['บำเพ็ญ 10 ชม. (71-99)']++;
        } else {
            $scoreGroups['ปกติ (100)']++;
        }
        $totalStudentsForGraph++;
    }
    
    // Add students without behavior records
    $diff = $stats['students'] - $totalStudentsForGraph;
    if ($diff > 0) {
        $scoreGroups['ปกติ (100)'] += $diff;
    }
} catch (Exception $e) {}

// (6) Set Page Metadata
$pageTitle = 'แดชบอร์ด';
$activePage = 'dashboard';

// (7) Render View
include __DIR__ . '/../views/admin/index.php';
?>
