<?php
/**
 * StdCare System - Teacher LOVES MODEL Router
 * แสดงข้อมูลนวัตกรรม LOVES MODEL สำหรับครูที่ปรึกษา
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "LOVES MODEL - ระบบดูแลช่วยเหลือนักเรียน";
$activePage = "loves_model";

include __DIR__ . '/../views/loves_model/index.php';
