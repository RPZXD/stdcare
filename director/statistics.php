<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");;
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

if (isset($_SESSION['Director_login'])) {
    $userid = $_SESSION['Director_login'];
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
                        <h5 class="m-0">ดูสถิติ</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-4">
                <div class="bg-white rounded-lg shadow p-6 mt-4">
                    <h6 class="mb-4 font-bold text-lg">สถิติภาพรวมระบบ</h6>
                    <!-- ตัวอย่างสถิติ สามารถปรับปรุง/เพิ่มข้อมูลได้ -->
                    <ul class="list-disc pl-6 text-gray-700">
                        <li>จำนวนนักเรียนทั้งหมด: ...</li>
                        <li>จำนวนครูและบุคลากร: ...</li>
                        <li>จำนวนการเยี่ยมบ้าน: ...</li>
                        <li>จำนวนการหักคะแนน: ...</li>
                        <!-- เพิ่มสถิติอื่น ๆ ตามต้องการ -->
                    </ul>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
