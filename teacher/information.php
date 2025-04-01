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

    <section class="content">
      <div class="container mx-auto py-6">
        <div class="flex flex-wrap justify-center">
          <div class="w-full lg:w-1/3 md:w-2/3 sm:w-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
              <div class="bg-gray-800 text-white text-center py-4">
                <h2 class="text-2xl font-bold">
                  <span>👩‍🏫</span> ข้อมูลครู
                </h2>
              </div>
              <div class="p-6">
                <div class="text-center">
                  <img class="rounded-full mx-auto h-80 w-auto"
                       src="<?php echo $setting->getImgProfile().$userData['Teach_photo'];?>"
                       alt="<?php echo $userData['Teach_name'];?>">
                </div>
                <h3 class="text-center text-xl font-semibold mt-4"><?php echo $userData['Teach_name'];?></h3>
                <p class="text-center text-gray-600"><?php echo $userData['Teach_major'];?></p>
                <ul class="mt-4 space-y-2">
                  <li class="flex justify-between">
                    <span><b>🆔 รหัสประจำตัวครู:</b></span>
                    <span><?php echo $userData['Teach_id'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🚻 เพศ:</b></span>
                    <span><?php echo $userData['Teach_sex'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🎂 วัน/เดือน/ปีเกิด:</b></span>
                    <span><?php echo Utils::convertToThaiDate($userData['Teach_birth']);?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🏠 ที่อยู่:</b></span>
                    <span><?php echo $userData['Teach_addr'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>📞 เบอร์โทรศัพท์:</b></span>
                    <span><?php echo $userData['Teach_phone'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span>
                    <span><?php echo "ม.".$userData['Teach_class']."/".$userData['Teach_room'];?></span>
                  </li>
                </ul>
                <button type="button" class="form-control block mt-6 bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600" id="editBtn">
                  ✏️ แก้ไขข้อมูล
                </button>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('donutChart').getContext('2d');
    const donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF', '#FF9F40']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let value = tooltipItem.raw || 0;
                            return `${value} คน`; // เพิ่มหน่วย คน
                        }
                    }
                }
            }
        }
    });

    function fetchData() {
        fetch(`api/fetch_chart_studentcome.php?class=<?=$class?>&room=<?=$room?>&date=<?=$currentDate?>`)
            .then(response => response.json())
            .then(data => {
                donutChart.data.labels = data.map(item => item.status_name);
                donutChart.data.datasets[0].data = data.map(item => parseFloat(item.count_total)); // แปลงเป็นตัวเลข
                donutChart.update();
            });
    }

    fetchData(); // Initial fetch
});
</script>
</body>
</html>
