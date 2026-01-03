<?php
/**
 * Controller: Admin Teacher Data
 * MVC Pattern - Teacher management for admin
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';

use App\DatabaseUsers;

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
$term = $user->getTerm();
$pee = $user->getPee();

// Store in session for layout
$_SESSION['admin_data'] = $userData;

// (4) Set Page Metadata
$pageTitle = 'จัดการข้อมูลครู';
$activePage = 'teacher';

// (5) Render View
include __DIR__ . '/../views/admin/data_teacher.php';
?>