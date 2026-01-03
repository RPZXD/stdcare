<?php
/**
 * Controller: Student Attendance Data (Officer)
 * MVC Pattern - Handles authentication and prepares data for the view
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../class/UserLogin.php";
require_once __DIR__ . "/../class/Student.php";
require_once __DIR__ . "/../config/Setting.php";
require_once __DIR__ . "/../class/Utils.php";
require_once __DIR__ . "/../class/Attendance.php";

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);
$attendance = new Attendance($db);

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);

// (4) Fetch Current School Term and Year
$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);

// (5) Prepare Filter Data
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$class = isset($_GET['class']) ? $_GET['class'] : null;
$room = isset($_GET['room']) ? $_GET['room'] : null;

// (6) Prepare Data for View
$pageTitle = 'ข้อมูลการเข้าเรียน';
$activeMenu = 'attendance';

// Helper functions for the view
function convertToBuddhistYearView($date) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);
        if ($year < 2500) $year += 543;
        return $year . '-' . $month . '-' . $day;
    }
    return $date;
}

function thaiDateView($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}

// (7) Render View
include __DIR__ . "/../views/officer/data_attendance.php";
?>
