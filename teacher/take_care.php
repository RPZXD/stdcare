<?php 
session_start();


include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

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

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content flex flex-col justify-center items-center w-full p-8">
    <!-- Content Header (Page header) -->

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container">

        <div class="col-md-12 mx-auto">
          <div class="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-6 rounded-lg shadow">
            <h3 class="text-center text-xl font-semibold mb-4">
              🏠 ระบบการดูแลช่วยเหลือนักเรียน 5 ขั้นตอน
            </h3>
            <div class="text-green-700 bg-green-100 border-l-4 border-green-500  p-4 rounded-lg">
              <h5 class="text-center font-semibold">โรงเรียนพิชัยได้ดำเนินการตามระบบการดูแลช่วยเหลือนักเรียนโดยยึดหลัก 5 ใจ 1 G</h5>
              <hr class="my-4">
              <ul class="space-y-4">
                <li>
                  <p>📌 <strong>ขั้นตอนที่ 1 ใส่ใจ - รู้รอบกรอบบุคคล</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="data_student.php" class="text-blue-500 hover:underline">1.1 ข้อมูลนักเรียนรายบุคคล</a></li>
                    <li><a href="visithome.php" class="text-blue-500 hover:underline">1.2 ข้อมูลการเยี่ยมบ้านนักเรียน</a></li>
                    <li><a href="poor.php" class="text-blue-500 hover:underline">1.3 ข้อมูลนักเรียนยากจน</a></li>
                    <li><a href="https://student.phichai.ac.th/teacher/stucare14.pdf" class="text-blue-500 hover:underline">1.4 ดาวน์โหลดแบบบันทึกการเยี่ยมบ้านนักเรียน</a></li>
                  </ul>
                </li>
                <li>
                  <p>📌 <strong>ขั้นตอนที่ 2 เข้าใจ - กรองกมลบูรณาการ</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="sdq.php" class="text-blue-500 hover:underline">2.1 แบบประเมินพฤติกรรมนักเรียน (SDQ)</a></li>
                    <li><a href="eq.php" class="text-blue-500 hover:underline">2.2 แบบประเมินความฉลาดทางอารมณ์ (EQ)</a></li>
                    <li><a href="screen11.php" class="text-blue-500 hover:underline">2.3 แบบคัดกรองนักเรียน 11 ด้าน</a></li>
                    <li><a href="#" class="text-blue-500 hover:underline">2.4 แบบคัดกรองนักเรียนเชิงประจักษ์</a></li>
                  </ul>
                </li>
                <li>
                  <p>📌 <strong>ขั้นตอนที่ 3 พร้อมใจ - ประสานเสริมให้พัฒนา</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="home_room.php" class="text-blue-500 hover:underline">3.1 กิจกรรมโฮมรูมประจำวัน</a></li>
                    <li><a href="https://student.phichai.ac.th/teacher/stucare32.pdf" class="text-blue-500 hover:underline">3.2 แบบบันทึกการส่งเสริมและพัฒนานักเรียน</a></li>
                    <li><a href="board_parent.php" class="text-blue-500 hover:underline">3.3 ข้อมูลคณะกรรมการเครือข่ายผู้ปกครอง</a></li>
                    <li><a href="picture_meeting.php" class="text-blue-500 hover:underline">3.4 อัปโหลดภาพกิจกรรมประชุมผู้ปกครองชั้นเรียน</a></li>
                    <li><a href="wroom.php" class="text-blue-500 hover:underline">3.5 ข้อมูลโครงสร้างองค์กรห้องเรียนสีขาว</a></li>
                    <li><a href="#" class="text-blue-500 hover:underline">3.6 อัปโหลดภาพป้ายนิเทศในห้องเรียน</a></li>
                  </ul>
                </li>
                <li>
                  <p>📌 <strong>ขั้นตอนที่ 4 เชื่อใจ - คลายปัญหาเป็นระบบ</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="https://student.phichai.ac.th/teacher/stucare41.pdf" class="text-blue-500 hover:underline">4.1 แบบบันทึกการดูแลช่วยเหลือนักเรียนเป็นรายบุคคล</a></li>
                    <li><a href="https://student.phichai.ac.th/teacher/stucare42.pdf" class="text-blue-500 hover:underline">4.2 แบบสรุปผลการดำเนินการป้องกันและแก้ไขปัญหานักเรียน</a></li>
                  </ul>
                </li>
                <li>
                  <p>📌 <strong>ขั้นตอนที่ 5 มั่นใจ - เมื่อพานพบรีบส่งต่อ</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="https://student.phichai.ac.th/teacher/stucare51.pdf" class="text-blue-500 hover:underline">5.1 แบบบันทึกการส่งต่อนักเรียน</a></li>
                    <li><a href="https://student.phichai.ac.th/teacher/stucare52.pdf" class="text-blue-500 hover:underline">5.2 แบบสรุปผลการส่งต่อนักเรียน</a></li>
                  </ul>
                </li>
                <li>
                  <p>📌 <strong>คะแนนพฤติกรรม</strong></p>
                  <ul class="ml-6 space-y-2">
                    <li><a href="behavior.php" class="text-blue-500 hover:underline">บันทึกคะแนนความผิด</a></li>
                    <li><a href="#" class="text-blue-500 hover:underline">บันทึกคะแนนความดี</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->

<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
