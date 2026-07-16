<?php
/**
 * Controller: Weekend Attendance Report
 * Shows RFID scan history for Saturdays and Sundays
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';
require_once __DIR__ . '/../class/SweetAlert2.php';

use App\DatabaseUsers;

// Check Permission
if (!isset($_SESSION['Admin_login'])) {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

// Initialize DB & Objects
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();

$user = new UserLogin($db);

// Fetch Core Context  
$userid = $_SESSION['Admin_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// Store in session for layout
$_SESSION['admin_data'] = $userData;

// Set Page Metadata
$pageTitle = 'รายงานสแกนเสาร์-อาทิตย์';
$activePage = 'weekend_attendance';

// Render View
include __DIR__ . '/../views/admin/weekend_attendance.php';
?>
