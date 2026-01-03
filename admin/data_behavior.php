<?php
/**
 * Controller: Admin Behavior Data
 * MVC Pattern - Behavior management for admin
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';
require_once __DIR__ . '/../config/Setting.php';

use App\DatabaseUsers;

// (1) Check Permission
if (!isset($_SESSION['Admin_login']) && !isset($_SESSION['Teacher_login'])) {
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
$setting = new Setting();

// (3) Fetch Core Context
$userid = $_SESSION['Admin_login'] ?? $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);

// Store in session for layout
$_SESSION['admin_data'] = $userData;

// (4) Behavior options
$behavior_options = [
    "ความดี" => [
        "จิตอาสา", "ช่วยเหลือครู", "เก็บของได้ส่งคืน", "ช่วยเหลือเพื่อน", "อื่นๆ (ความดี)"
    ],
    "ความผิด" => [
        "หนีเรียนหรือออกนอกสถานศึกษา", "เล่นการพนัน", "มาโรงเรียนสาย", 
        "แต่งกาย/ทรงผมผิดระเบียบ", "พกพาอาวุธหรือวัตถุระเบิด", 
        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์", "สูบบุหรี่", "เสพยาเสพติด", 
        "ลักทรัพย์ กรรโชกทรัพย์", "ก่อเหตุทะเลาะวิวาท", "แสดงพฤติกรรมทางชู้สาว", 
        "จอดรถในที่ห้ามจอด", "แสดงพฤติกรรมก้าวร้าว", "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
    ]
];

// (5) Set Page Metadata
$pageTitle = "จัดการข้อมูลพฤติกรรม (เทอม $term/$pee)";
$activePage = 'behavior';

// (6) Render View
include __DIR__ . '/../views/admin/data_behavior.php';
?>