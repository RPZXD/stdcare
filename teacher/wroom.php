<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacher = new Teacher($db);

$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

$class = $userData['Teach_class'];
$room = $userData['Teach_room'];


require_once('header.php');


require_once('wrapper.php');
?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <!-- ...existing code for header/wrapper... -->

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"></h1>
                </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3 d-block">
                        <div class="text-base font-bold text-center mb-4">
                            🏠 แบบฟอร์มบันทึกรายชื่อคณะกรรมการห้องเรียนสีขาว ปีการศึกษา <?= $pee ?>
                            <p>
                                โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์
                            </p>
                            <p>
                                ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?> ปีการศึกษา <?= $pee ?>
                            </p>
                            <p>
                            ครูที่ปรึกษา <?php
                         
                                    $teachers = $teacher->getTeachersByClassAndRoom($class, $room);

                                            foreach ($teachers as $row) {
                                                echo $row['Teach_name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
                                            }
                            
                                    ?>
                            </p></div>
                            <div class="bg-gray-100 border border-gray-300 rounded-xl p-6 text-gray-800">
                                <h2 class="text-lg font-semibold mb-4">📌 คำชี้แจง</h2>
                                <p class="mb-4">
                                    โปรดเลือกตำแหน่งของนักเรียนในการเป็น <span class="font-medium text-blue-600">คณะกรรมการดำเนินงานห้องเรียนสีขาว</span> 
                                    โดยพิจารณาตามรายการตำแหน่งด้านล่างนี้:
                                </p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>👤 <strong>หัวหน้าห้อง</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📘 <strong>รองหัวหน้าฝ่ายการเรียน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🛠️ <strong>รองหัวหน้าฝ่ายการงาน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🎉 <strong>รองหัวหน้าฝ่ายกิจกรรม</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🚨 <strong>รองหัวหน้าฝ่ายสารวัตรนักเรียน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📝 <strong>เลขานุการ</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🗂️ <strong>ผู้ช่วยเลขานุการ</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📚 <strong>นักเรียนแกนนำฝ่ายการเรียน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🔧 <strong>นักเรียนแกนนำฝ่ายการงาน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🎭 <strong>นักเรียนแกนนำฝ่ายกิจกรรม</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🛡️ <strong>นักเรียนแกนนำฝ่ายสารวัตรนักเรียน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                </ul>
                                <p class="mt-4">
                                    👥 นักเรียนที่ <span class="underline">ไม่ได้รับเลือก</span> ในตำแหน่งใด ๆ จะถือว่าเป็น <span class="font-medium text-blue-600">สมาชิกทั่วไป</span> 
                                    ของคณะกรรมการห้องเรียนสีขาว
                                </p>
                                <p class="mt-4">
                                    ✍️ <strong>โปรดกรอก "คติพจน์ของห้องเรียนสีขาว"</strong> ให้เรียบร้อยก่อนกดปุ่มบันทึก
                                </p>
                            </div>

                        
                            <div class="flex w-full mt-4">
                                <button type="button"
                                    class="w-[calc(50%-0.25rem)] mr-2 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600"
                                    onclick="location.href='report_wroom.php'">
                                    <i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;ดูรายชื่อคณะกรรมดำเนินงานห้องเรียนสีขาว
                                </button>

                                <button type="button"
                                    class="w-[calc(50%-0.25rem)] bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600"
                                    onclick="location.href='report_wroom2.php'">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>&nbsp;&nbsp;ดูผังโครงสร้างองค์กรห้องเรียนสีขาว
                                </button>
                            </div>



                        <div class="table-responsive">
                        
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

</script>
</body>
</html>
