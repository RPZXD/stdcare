<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);

    // เรียกมาเฉพาะ class และ room ของครูผู้ใช้ปัจจุบัน
    $class = $๊userData['Teach_class'];
    $room = $userData['Teach_room'];
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">รายงานสรุป</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-4">
                <?php
                // รับค่า tab จาก query string
                $tab = $_GET['tab'] ?? 'check';
                // สร้าง array สำหรับ mapping tab => ไฟล์
                $tabFiles = [
                    'check' => 'check_std.php',
                    'summary-class' => 'report_summary_class.php',
                    'overview' => 'report_overview.php'
                ];
                ?>
                <!-- Tabs -->
                <div class="flex border-b mb-6">
                    <a href="?tab=check" class="px-4 py-2 -mb-px font-semibold border-b-2 transition-all <?= $tab === 'check' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-blue-600' ?>">
                        📋 เช็คชื่อนักเรียน
                    </a>
                    <a href="?tab=summary-class" class="px-4 py-2 -mb-px font-semibold border-b-2 transition-all <?= $tab === 'summary-class' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-green-600' ?>">
                        📑 รายงานสรุปรายชั้น
                    </a>
                    <a href="?tab=overview" class="px-4 py-2 -mb-px font-semibold border-b-2 transition-all <?= $tab === 'overview' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-yellow-600' ?>">
                        📊 สรุปภาพรวม
                    </a>
                </div>
                <div class="bg-white rounded-lg shadow p-6 mt-4">
                    <?php
                    // include ไฟล์ตาม tab ที่เลือก
                    if (isset($tabFiles[$tab]) && file_exists($tabFiles[$tab])) {
                        include($tabFiles[$tab]);
                    } else {
                        echo '<div class="text-gray-600">ไม่พบรายงานที่เลือก</div>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
