<?php
/**
 * Controller: Parent Data Management (Officer)
 * MVC Pattern - Handles authentication and prepares data for the parent list view
 */
session_start();

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';

use App\DatabaseUsers;

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);

// (4) Prepare Data for View
$pageTitle = 'จัดการข้อมูลผู้ปกครอง';
$activeMenu = 'parent_data';

// (5) Render View
include __DIR__ . '/../views/officer/data_parent.php';
?>