<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'action' => 'update_times',
    'arrival_late_time' => '08:00:00',
    'arrival_absent_time' => '10:00:00',
    'leave_early_time' => '15:40:00',
    'scan_crossover_time' => '12:00:00',
    'term_start_date' => '2026-05-01',
    'term_end_date' => '2026-10-31'
];
session_start();
$_SESSION['Admin_login'] = 'test';
$_SESSION['role'] = 'Admin';

require_once __DIR__ . '/controllers/SettingController.php';
