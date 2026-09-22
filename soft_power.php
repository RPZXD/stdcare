<?php
/**
 * StdCare System - Soft Power Public / Root Entry Point
 * แสดงข้อมูลรูปแบบการบริหารการขับเคลื่อน Soft Power โรงเรียนพิชัย
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Soft Power - รูปแบบการบริหารการขับเคลื่อน";
$activePage = "soft_power";

include __DIR__ . '/views/soft_power/index.php';
