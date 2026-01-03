<?php
/**
 * Controller: Behavior Data Management (Officer)
 * MVC Pattern - Handles authentication and prepares data for the behavior list view
 */
session_start();

require_once __DIR__ . "/../classes/DatabaseUsers.php";
require_once __DIR__ . "/../class/UserLogin.php";
require_once __DIR__ . "/../class/Utils.php";
require_once __DIR__ . "/../config/Setting.php";

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
$setting = new Setting();

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);

// (4) Fetch Current School Term and Year
$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);

// (5) Prepare Data for View
$pageTitle = 'จัดการข้อมูลพฤติกรรม';
$activeMenu = 'behavior_data';

// (6) Render View
include __DIR__ . "/../views/officer/data_behavior.php";
?>