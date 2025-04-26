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

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
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
                        <h1 class="m-0">Admin Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-6 px-2">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200 text-center">
                    <h2 class="text-2xl font-bold mb-4">ยินดีต้อนรับผู้ดูแลระบบ</h2>
                    <p class="text-gray-700 mb-6">
                        คุณเข้าสู่ระบบในฐานะ <span class="font-semibold text-blue-700">Admin</span>
                    </p>
                    <div class="flex flex-col items-center gap-4">
                        <a href="../logout.php" class="inline-block bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">ออกจากระบบ</a>
                    </div>
                </div>
                <!-- เพิ่มเมนูหรือ dashboard อื่นๆ ที่ต้องการที่นี่ -->
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
