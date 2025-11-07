<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
  <div class="content-wrapper">

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

    <section class="content py-8">
      <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
          <h2 class="text-3xl font-bold text-center mb-8 text-gray-800 animate-pulse">📊 รายงานต่างๆ</h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 border-l-4 border-yellow-500 text-yellow-800 p-8 rounded-xl shadow-lg hover:shadow-2xl hover:shadow-yellow-500/50 hover:scale-105 transition-all duration-300 transform">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">📋 รายบุคคล</h3>
                <div class="text-4xl animate-pulse">👤</div>
              </div>
              <div>
                <ul class="space-y-4">
                  <li><a href="report_study_single.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-yellow-200 hover:to-yellow-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-yellow-700 hover:text-yellow-900 transform"><span class="mr-3">⏰</span>เวลาเรียน</a></li>
                  <li><a href="report_student_sdq_single.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-yellow-200 hover:to-yellow-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-yellow-700 hover:text-yellow-900 transform"><span class="mr-3">📊</span>ข้อมูล SDQ</a></li>
                  <li><a href="report_behavior_single.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-yellow-200 hover:to-yellow-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-yellow-700 hover:text-yellow-900 transform"><span class="mr-3">📝</span>คะแนนพฤติกรรม</a></li>
                </ul>
              </div>
            </div>
            <div class="bg-gradient-to-br from-blue-100 to-blue-200 border-l-4 border-blue-500 text-blue-800 p-8 rounded-xl shadow-lg hover:shadow-2xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 transform">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">👥 รายกลุ่ม/ทั้งหมด</h3>
                <div class="text-4xl animate-pulse">📈</div>
              </div>
              <div>
                <ul class="space-y-4">
                  <li><a href="report_study_late.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">⏳</span>รายงานการมาสาย-ขาดเรียนรายห้อง</a></li>
                  <li><a href="report_class_visithome.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">🏠</span>รายงานการเยี่ยมบ้านรายห้อง</a></li>
                  <li><a href="report_study_day.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">📅</span>เวลาเรียนประจำวัน</a></li>
                  <li><a href="report_study_month.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">📆</span>เวลาเรียนประจำเดือน</a></li>
                  <li><a href="report_study_term.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">📚</span>เวลาเรียนประจำภาคเรียน/ปีการศึกษา</a></li>
                  <li><a href="report_study_leave.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">🚫</span>รายชื่อนักเรียนที่ไม่มาเรียน</a></li>
                  <li><a href="report_board_parent.php" class="flex items-center p-3 bg-white bg-opacity-50 rounded-lg hover:bg-gradient-to-r hover:from-blue-200 hover:to-blue-300 hover:scale-105 hover:shadow-md transition-all duration-200 text-blue-700 hover:text-blue-900 transform"><span class="mr-3">👨‍👩‍👧‍👦</span>รายงานรายชื่อประธานเครือข่ายผู้ปกครองระดับชั้น</a></li>
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
