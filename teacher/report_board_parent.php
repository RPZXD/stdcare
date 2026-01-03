<?php
/**
 * Controller: Board Parent Report
 * MVC Pattern - Handles authentication and initial data (classes) for the view
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Teacher.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$user = new UserLogin($db);

// (3) Fetch Core Data
$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$term = $user->getTerm();
$pee = $user->getPee();

// (4) Fetch Available Classes for Filter
$available_classes = [];
try {
    $stmt = $db->query("SELECT DISTINCT parn_lev 
                        FROM tb_parnet 
                        WHERE parn_lev IS NOT NULL 
                        AND parn_lev != '' 
                        AND parn_lev != '0' 
                        AND parn_lev BETWEEN 1 AND 6
                        ORDER BY parn_lev ASC");
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $available_classes[] = $row['parn_lev'];
        }
    }
} catch (Exception $e) {
    // Silent fail
}

// (5) Set Page Metadata
$pageTitle = 'รายงานคณะกรรมการเครือข่ายผู้ปกครอง';
$activeMenu = 'report';
$activeSubMenu = 'report_board_parent';

// (6) Render View
include __DIR__ . '/../views/teacher/report_board_parent.php';
