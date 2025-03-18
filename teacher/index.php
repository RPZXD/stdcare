<?php 
session_start();


include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database_User();
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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));
// $count = $student->getStudyStatusCountClassRoom2($class, $room, Utils::convertToThaiDatePlusNum(date("Y-m-d")));
$countStdCome = $student->getStatusCountClassRoom($class, $room, [1, 3, 6] , $currentDate);
$countStdAbsent = $student->getStatusCountClassRoom($class, $room, [2, 4, 5] , $currentDate);
$countAll = $student->getCountClassRoom($class, $room);

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

        <div class="container-fluid">
            <div class="row">
                <div class="w-full">
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 text-center">
                            <h4 class="text-lg font-semibold">ยินดีต้อนรับคุณครู <?php echo $userData['Teach_name']. ' ' . $setting->getPageTitle()?></h4>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                <h3 class="text-lg font-semibold text-gray-900 mt-6">ยอดนักเรียนมาเรียน/ไม่มาเรียน</h3>

                </div>
                    
                <div class="row justify-content-center">
                    <div class="col-md-8">
                    <div class="flex flex-wrap mt-4">
                        <div class="w-full md:w-1/3 px-2 mb-4">
                        <!-- small box -->
                        <div class="bg-blue-500 text-white p-4 rounded-lg shadow">
                            <div class="flex justify-between items-center">
                            <h3 class="text-3xl font-bold"><?=$countAll?></h3>
                            <i class="ion ion-person-add text-4xl"></i>
                            </div>
                            <p class="mt-2">จำนวนนักเรียนทั้งห้อง</p>
                        </div>
                        </div>
                        <!-- ./col -->
                        <div class="w-full md:w-1/3 px-2 mb-4">
                        <!-- small box -->
                        <div class="bg-green-500 text-white p-4 rounded-lg shadow">
                            <div class="flex justify-between items-center">
                            <h3 class="text-3xl font-bold"><?=$countStdCome?></h3>
                            <i class="ion ion-person-add text-4xl"></i>
                            </div>
                            <p class="mt-2">มาเรียน</p>
                        </div>
                        </div>

                        <div class="w-full md:w-1/3 px-2 mb-4">
                        <!-- small box -->
                        <div class="bg-red-500 text-white p-4 rounded-lg shadow">
                            <div class="flex justify-between items-center">
                            <h3 class="text-3xl font-bold"><?=$countStdAbsent?></h3>
                            <i class="ion ion-person-add text-4xl"></i>
                            </div>
                            <p class="mt-2">ไม่มาเรียน</p>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-8">

                        <div class="w-full md:w-1/1 px-2 mb-4">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">สรุปการมาเรียนของนักเรียนประจำวันที่ <?=$currentDate2?></h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- /.container-fluid -->

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
