<?php
/**
 * StdCare System - LOVES MODEL Router
 * แสดงข้อมูลนวัตกรรม LOVES MODEL
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "LOVES MODEL - ระบบดูแลช่วยเหลือนักเรียน";

include __DIR__ . '/views/loves_model/index.php';
