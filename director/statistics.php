<?php
/**
 * Controller: Director Statistics
 * MVC Pattern - Handles authentication and logic for director statistics page
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Director_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student = new Student($db);

// (3) Fetch Core Context
$userid = $_SESSION['Director_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Fetch Statistics Data (Real Data with Error Handling)
$stats = [
    'students' => 0,
    'teachers' => 0,
    'homevisit' => 0,
    'behavior' => 0,
];

try {
    $stats['students'] = $db->query("SELECT COUNT(*) FROM student WHERE Stu_status=1")->fetchColumn() ?: 0;
} catch (Exception $e) {
    $stats['students'] = 0;
}

try {
    $stats['teachers'] = $db->query("SELECT COUNT(*) FROM teacher WHERE Teach_status=1")->fetchColumn() ?: 0;
} catch (Exception $e) {
    $stats['teachers'] = 0;
}

try {
    // Use correct table name: visithome (not homevisit)
    $stmt = $db->prepare("SELECT COUNT(*) FROM visithome WHERE Term = :term AND Pee = :pee");
    $stmt->execute([':term' => $term, ':pee' => $pee]);
    $stats['homevisit'] = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $stats['homevisit'] = 0;
}

try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM behavior WHERE behavior_term = :term AND behavior_pee = :pee");
    $stmt->execute([':term' => $term, ':pee' => $pee]);
    $stats['behavior'] = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $stats['behavior'] = 0;
}

// (5) Set Page Metadata
$pageTitle = 'สถิติภาพรวมระบบ - Director';
$activePage = 'stats';

// (6) Render View
include __DIR__ . '/../views/director/statistics.php';
?>
