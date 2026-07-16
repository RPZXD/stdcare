<?php
/**
 * Controller: Public Weekend Attendance Report
 * Shows RFID scan history for Saturdays and Sundays (PDPA Compliant)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/classes/DatabaseUsers.php';
require_once __DIR__ . '/class/UserLogin.php';
require_once __DIR__ . '/class/Utils.php';

use App\DatabaseUsers;

// Initialize DB & Objects
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();

$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

// Set Page Metadata
$pageTitle = 'รายงานสแกนเสาร์-อาทิตย์';
$activePage = 'weekend_attendance';

// Render View
include __DIR__ . '/views/weekend_attendance.php';
?>
