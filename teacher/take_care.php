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
      <div class="container mx-auto px-4 py-8 ">
        <div class="mx-auto">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 text-blue-800 p-8 rounded-xl shadow-lg mb-8">
            <h3 class="text-center text-3xl font-bold mb-6 text-gray-800">
              🏠 ระบบการดูแลช่วยเหลือนักเรียน 5 ขั้นตอน
            </h3>
            <div class="text-center mb-6">
              <p class="text-lg text-gray-700">โรงเรียนพิชัยได้ดำเนินการตามระบบการดูแลช่วยเหลือนักเรียนโดยยึดหลัก 5 ใจ 1 G</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- ขั้นตอนที่ 1 -->
              <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  ❤️ <span class="ml-2">ขั้นตอนที่ 1 ใส่ใจ - รู้รอบกรอบบุคคล</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="data_student.php" class="text-red-600 hover:text-red-800 hover:underline flex items-center"><span class="mr-2">👤</span>1.1 ข้อมูลนักเรียนรายบุคคล</a></li>
                  <li><a href="visithome.php" class="text-red-600 hover:text-red-800 hover:underline flex items-center"><span class="mr-2">🏠</span>1.2 ข้อมูลการเยี่ยมบ้านนักเรียน</a></li>
                  <li><a href="poor.php" class="text-red-600 hover:text-red-800 hover:underline flex items-center"><span class="mr-2">💰</span>1.3 ข้อมูลนักเรียนยากจน</a></li>
                  <li><a href="https://student.phichai.ac.th/teacher/stucare14.pdf" class="text-red-600 hover:text-red-800 hover:underline flex items-center"><span class="mr-2">📄</span>1.4 ดาวน์โหลดแบบบันทึกการเยี่ยมบ้านนักเรียน</a></li>
                </ul>
              </div>
              <!-- ขั้นตอนที่ 2 -->
              <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  🧠 <span class="ml-2">ขั้นตอนที่ 2 เข้าใจ - กรองกมลบูรณาการ</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="sdq.php" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center"><span class="mr-2">📊</span>2.1 แบบประเมินพฤติกรรมนักเรียน (SDQ)</a></li>
                  <li><a href="eq.php" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center"><span class="mr-2">🧠</span>2.2 แบบประเมินความฉลาดทางอารมณ์ (EQ)</a></li>
                  <li><a href="screen11.php" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center"><span class="mr-2">🔍</span>2.3 แบบคัดกรองนักเรียน 11 ด้าน</a></li>
                  <li><a href="#" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center"><span class="mr-2">👁️</span>2.4 แบบคัดกรองนักเรียนเชิงประจักษ์</a></li>
                </ul>
              </div>
              <!-- ขั้นตอนที่ 3 -->
              <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  🤝 <span class="ml-2">ขั้นตอนที่ 3 พร้อมใจ - ประสานเสริมให้พัฒนา</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="home_room.php" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">🏫</span>3.1 กิจกรรมโฮมรูมประจำวัน</a></li>
                  <li><a href="https://student.phichai.ac.th/teacher/stucare32.pdf" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">📝</span>3.2 แบบบันทึกการส่งเสริมและพัฒนานักเรียน</a></li>
                  <li><a href="board_parent.php" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">👨‍👩‍👧‍👦</span>3.3 ข้อมูลคณะกรรมการเครือข่ายผู้ปกครอง</a></li>
                  <li><a href="picture_meeting.php" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">📸</span>3.4 อัปโหลดภาพกิจกรรมประชุมผู้ปกครองชั้นเรียน</a></li>
                  <li><a href="wroom.php" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">🏛️</span>3.5 ข้อมูลโครงสร้างองค์กรห้องเรียนสีขาว</a></li>
                  <li><a href="#" class="text-yellow-600 hover:text-yellow-800 hover:underline flex items-center"><span class="mr-2">🪧</span>3.6 อัปโหลดภาพป้ายนิเทศในห้องเรียน</a></li>
                </ul>
              </div>
              <!-- ขั้นตอนที่ 4 -->
              <div class="bg-purple-50 border-l-4 border-purple-500 text-purple-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  � <span class="ml-2">ขั้นตอนที่ 4 เชื่อใจ - คลายปัญหาเป็นระบบ</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="https://student.phichai.ac.th/teacher/stucare41.pdf" class="text-purple-600 hover:text-purple-800 hover:underline flex items-center"><span class="mr-2">📋</span>4.1 แบบบันทึกการดูแลช่วยเหลือนักเรียนเป็นรายบุคคล</a></li>
                  <li><a href="https://student.phichai.ac.th/teacher/stucare42.pdf" class="text-purple-600 hover:text-purple-800 hover:underline flex items-center"><span class="mr-2">📈</span>4.2 แบบสรุปผลการดำเนินการป้องกันและแก้ไขปัญหานักเรียน</a></li>
                </ul>
              </div>
              <!-- ขั้นตอนที่ 5 -->
              <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  � <span class="ml-2">ขั้นตอนที่ 5 มั่นใจ - เมื่อพานพบรีบส่งต่อ</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="https://student.phichai.ac.th/teacher/stucare51.pdf" class="text-green-600 hover:text-green-800 hover:underline flex items-center"><span class="mr-2">📤</span>5.1 แบบบันทึกการส่งต่อนักเรียน</a></li>
                  <li><a href="https://student.phichai.ac.th/teacher/stucare52.pdf" class="text-green-600 hover:text-green-800 hover:underline flex items-center"><span class="mr-2">✅</span>5.2 แบบสรุปผลการส่งต่อนักเรียน</a></li>
                </ul>
              </div>
              <!-- คะแนนพฤติกรรม -->
              <div class="bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                <h5 class="font-semibold text-lg mb-4 flex items-center">
                  ⭐ <span class="ml-2">คะแนนพฤติกรรม</span>
                </h5>
                <ul class="space-y-3">
                  <li><a href="behavior.php" class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center"><span class="mr-2">⚠️</span>บันทึกคะแนนความผิด</a></li>
                  <li><a href="#" class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center"><span class="mr-2">👍</span>บันทึกคะแนนความดี</a></li>
                </ul>
              </div>
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
