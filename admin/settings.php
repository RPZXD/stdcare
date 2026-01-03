<?php
/**
 * Controller: Admin Settings
 * MVC Pattern - System settings management
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';
require_once __DIR__ . '/../models/SettingModel.php';

use App\DatabaseUsers;
use App\Models\SettingModel;

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
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();

$user = new UserLogin($db);

// (3) Fetch Core Context
$userid = $_SESSION['Admin_login'];
$userData = $user->userData($userid);

// Store in session for layout
$_SESSION['admin_data'] = $userData;

// (4) Fetch settings data
$term = $user->getTerm();
$pee = $user->getPee();

// Get time settings
$settingsModel = new SettingModel($db);
$timeSettings = $settingsModel->getAllTimeSettings();
$arrival_late_time = $timeSettings['arrival_late_time'] ?? '08:00:00';
$arrival_absent_time = $timeSettings['arrival_absent_time'] ?? '10:00:00';
$leave_early_time = $timeSettings['leave_early_time'] ?? '15:40:00';
$scan_crossover_time = $timeSettings['scan_crossover_time'] ?? '12:00:00';

// Get class/room options for dropdowns
try {
    $studentClass = $db->query("SELECT DISTINCT Stu_major FROM student WHERE Stu_major IS NOT NULL AND Stu_status = '1' ORDER BY Stu_major")->fetchAll(PDO::FETCH_COLUMN);
    $studentRoom = $db->query("SELECT DISTINCT Stu_room FROM student WHERE Stu_room IS NOT NULL AND Stu_status = '1' ORDER BY Stu_room")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $studentClass = [1, 2, 3, 4, 5, 6];
    $studentRoom = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
}

// (5) Set Page Metadata
$pageTitle = 'ตั้งค่าระบบ';
$activePage = 'settings';

// (6) Render View
include __DIR__ . '/../views/admin/settings.php';
?>