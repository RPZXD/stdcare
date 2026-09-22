<?php
/**
 * StdCare System - Teacher Soft Power Router
 * แสดงข้อมูลรูปแบบการบริหารการขับเคลื่อน Soft Power สำหรับครูที่ปรึกษา
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Soft Power - รูปแบบการบริหารการขับเคลื่อน";
$activePage = "soft_power";

include __DIR__ . '/../views/soft_power/index.php';
