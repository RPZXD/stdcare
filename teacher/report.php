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
      <div class="container mx-auto">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="col-span-1">
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">📋 รายบุคคล</h3>
                <div>

                </div>
              </div>
              <div class="mt-4">
                <ul class="space-y-2">
                  <li><a href="report_study_single.php" class="text-blue-500 hover:underline">⏰ เวลาเรียน</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">📊 ข้อมูล SDQ</a></li>
                  <li><a href="report_behavior_single.php" class="text-blue-500 hover:underline">📝 คะแนนพฤติกรรม</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-span-1">
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">👥 รายกลุ่ม/ทั้งหมด</h3>
                <div>
                </div>
              </div>
              <div class="mt-4">
                <ul class="space-y-2">
                  <li><a href="#" class="text-blue-500 hover:underline">🏠 รายงานโฮมรูมรายห้อง</a></li>
                  <li><a href="report_study_late.php" class="text-blue-500 hover:underline">⏳ รายงานการมาสาย-ขาดเรียนรายห้อง</a></li>
                  <li><a href="report_study_day.php" class="text-blue-500 hover:underline">📅 เวลาเรียนประจำวัน</a></li>
                  <li><a href="report_study_month.php" class="text-blue-500 hover:underline">📆 เวลาเรียนประจำเดือน</a></li>
                  <li><a href="report_study_term.php" class="text-blue-500 hover:underline">📚 เวลาเรียนประจำภาคเรียน/ปีการศึกษา</a></li>
                  <li><a href="report_study_leave.php" class="text-blue-500 hover:underline">🚫 รายชื่อนักเรียนที่ไม่มาเรียน</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">📈 SDQ (นักเรียนประเมิน)</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">📉 SDQ (ครูประเมิน)</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">👨‍👩‍👧‍👦 SDQ (ผู้ปกครองประเมิน)</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">🔄 SDQ (รวมทั้ง 3 ฉบับ)</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">📊 สรุป SDQ 3 ฉบับ(ย้อนหลัง)</a></li>
                  <li><a href="#" class="text-blue-500 hover:underline">📊 สรุปสถิติการคัดกรองนักเรียน</a></li>
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
