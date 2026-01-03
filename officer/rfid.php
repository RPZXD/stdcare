<?php
/**
 * Controller: Manage RFID Data (Officer)
 * MVC Pattern - Handles authentication and prepares data for the view
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . "/../classes/DatabaseUsers.php";
require_once __DIR__ . "/../class/UserLogin.php";
require_once __DIR__ . "/../class/Utils.php";
require_once __DIR__ . "/../config/Setting.php";

use App\DatabaseUsers;

// (1) Check Permission (Officer or Admin)
if (!isset($_SESSION['Officer_login']) && !isset($_SESSION['Admin_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
$setting = new Setting();

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'] ?? $_SESSION['Admin_login'];
$userData = $user->userData($userid);

// (4) Fetch Current School Term and Year
$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);

// (5) Prepare Data for View
$pageTitle = 'จัดการข้อมูลบัตร RFID';
$activeMenu = 'rfid_manage';

// (6) Render View
include __DIR__ . "/../views/officer/rfid.php";
?>