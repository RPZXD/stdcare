<?php
/**
 * Controller: Report Summary (Officer)
 * MVC Pattern - Handles authentication and prepares data for the view
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../class/UserLogin.php";
require_once __DIR__ . "/../class/Student.php";
require_once __DIR__ . "/../class/Utils.php";

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);
$student = new Student($db);

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);

// (4) Fetch Current School Term and Year
$term = $user->getTerm();
$pee = $user->getPee();

// (5) Prepare Data for View
$pageTitle = 'รายงานสรุประบบ';
$activeMenu = 'report';

// รับค่า tab จาก query string
$tab = $_GET['tab'] ?? 'late';

// Mapping tab => File
$tabFiles = [
    'late' => 'report_late.php',
    'homevisit' => 'report_homevisit.php',
    'deduct-room' => 'report_deduct_room.php',
    'deduct-group' => 'report_deduct_group.php',
    'parent-leader' => 'report_parent_leader.php',
    'sdq_room' => 'report_sdq_room.php',
    'sdq_class' => 'report_sdq_class.php',
    'sdq_school' => 'report_sdq_school.php',
    'eq_room' => 'report_eq_room.php',
    'eq_class' => 'report_eq_class.php',
    'eq_school' => 'report_eq_school.php',
    'screen11_room' => 'report_screen11_room.php',
    'screen11_class' => 'report_screen11_class.php',
    'screen11_school' => 'report_screen11_school.php',
    'whiteclass' => 'report_whiteclass.php',
    'whiteclass-list' => 'report_whiteclass_list.php',
    'whiteclass-structure' => 'report_whiteclass_structure.php',
];

// Definition of Tab Groups
$mainTabs = [
    ['late', '⏰ ข้อมูลมาสาย', 'indigo'],
    ['homevisit', '🏠 การเยี่ยมบ้าน', 'emerald'],
    ['deduct-room', '🏫 หักคะแนน (รายห้อง)', 'rose'],
    ['deduct-group', '📊 หักคะแนน (กลุ่ม)', 'pink'],
];

$moreTabs = [
    ['parent-leader', '👨‍👩‍👧‍👦 ประธานเครือข่าย', 'violet'],
    ['sdq_room', '🧠 SDQ (รายห้อง)', 'red'],
    ['sdq_class', '🧠 SDQ (รายชั้น)', 'red'],
    ['sdq_school', '🧠 SDQ (โรงเรียน)', 'red'],
    ['eq_room', '💡 EQ (รายห้อง)', 'amber'],
    ['eq_class', '💡 EQ (รายชั้น)', 'amber'],
    ['eq_school', '💡 EQ (โรงเรียน)', 'amber'],
    ['screen11_room', '🔬 11 ด้าน (รายห้อง)', 'teal'],
    ['screen11_class', '🔬 11 ด้าน (รายชั้น)', 'teal'],
    ['screen11_school', '🔬 11 ด้าน (โรงเรียน)', 'teal'],
    ['whiteclass', '⚪ ห้องเรียนสีขาว (สรุป)', 'slate'],
    ['whiteclass-list', '📋 ห้องเรียนสีขาว (รายชื่อ)', 'slate'],
    ['whiteclass-structure', '🏠 ห้องเรียนสีขาว (ผัง)', 'slate'],
];

// (6) Render View
include __DIR__ . "/../views/officer/report.php";
?>
