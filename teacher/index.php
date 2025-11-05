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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Use Gregorian date (Y-m-d) for DB/API calls
$currentDate = date("Y-m-d");
// For display to Thai users keep a Buddhist-year formatted string
$currentDateDisplay = Utils::convertToThaiDatePlus(date("Y-m-d"));
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
        <!-- Welcome -->
        <div class="row mb-4">
            <div class="w-full">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 text-center rounded shadow">
                    <h4 class="text-lg font-semibold">👩‍🏫 ยินดีต้อนรับคุณครู <?php echo $userData['Teach_name']. ' ' . $setting->getPageTitle()?></h4>
                </div>
            </div>
        </div>
        <!-- Card Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Attendance -->
            <div class="bg-blue-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🗓️</span>
                <div class="text-2xl font-bold text-blue-700"><?=$countAll?></div>
                <div class="text-sm text-gray-700">นักเรียนทั้งหมด</div>
                <div class="flex gap-2 mt-2">
                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">✅ มาเรียน <?=$countStdCome?></span>
                    <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs">❌ ไม่มาเรียน <?=$countStdAbsent?></span>
                </div>
            </div>
            <!-- Behavior -->
            <div class="bg-yellow-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">📝</span>
                <div class="text-2xl font-bold text-yellow-700" id="behaviorCount">-</div>
                <div class="text-sm text-gray-700">พฤติกรรม (เดือนนี้)</div>
            </div>
            <!-- Visit Home -->
            <div class="bg-purple-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🏠</span>
                <div class="text-lg font-bold text-purple-700" id="visitCount">-</div>
                <div class="text-sm text-gray-700">เยี่ยมบ้าน (ปีนี้)</div>
            </div>
            <!-- Poor Student -->
            <div class="bg-pink-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">💸</span>
                <div class="text-2xl font-bold text-pink-700" id="poorCount">-</div>
                <div class="text-sm text-gray-700">นักเรียนยากจน</div>
            </div>
        </div>

        <!-- Card Summary 2 -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- SDQ -->
            <div class="bg-green-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🧠</span>
                <div class="text-lg font-bold text-green-700" id="sdqCount">-</div>
                <div class="text-sm text-gray-700">SDQ ประเมินแล้ว</div>
            </div>
            <!-- EQ -->
            <div class="bg-indigo-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🌈</span>
                <div class="text-lg font-bold text-indigo-700" id="eqCount">-</div>
                <div class="text-sm text-gray-700">EQ ประเมินแล้ว</div>
            </div>
            <!-- Screening -->
            <div class="bg-orange-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🔎</span>
                <div class="text-lg font-bold text-orange-700" id="screenCount">-</div>
                <div class="text-sm text-gray-700">คัดกรอง11ด้านแล้ว</div>
            </div>
            <!-- Homeroom -->
            <div class="bg-teal-100 rounded-lg shadow p-4 flex flex-col items-center">
                <span class="text-4xl mb-2">🏫</span>
                <div class="text-lg font-bold text-teal-700" id="homeroomCount">-</div>
                <div class="text-sm text-gray-700">กิจกรรมโฮมรูม</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Attendance Donut -->
            <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" style="min-height:320px;">
                <h3 class="font-semibold mb-2">📊 สรุปการมาเรียน (วันนี้)</h3>
                <div class="w-full flex-1 flex items-center justify-center">
                    <canvas id="donutChart" style="max-height: 220px; max-width: 100%;"></canvas>
                </div>
            </div>
            <!-- Behavior Bar -->
            <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" style="min-height:320px;">
                <h3 class="font-semibold mb-2">📝 กราฟพฤติกรรม (ภาคเรียนนี้)</h3>
                <div class="w-full flex-1 flex items-center justify-center">
                    <canvas id="behaviorChart" style="max-height: 220px; max-width: 100%;"></canvas>
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
    // กราฟ donut การมาเรียน
    const donutCanvas = document.getElementById('donutChart');
    let donutChart = null;
    if (donutCanvas) {
        const ctx = donutCanvas.getContext('2d');
        donutChart = new Chart(ctx, {
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
                                return `${value} คน`;
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
                    donutChart.data.datasets[0].data = data.map(item => parseFloat(item.count_total));
                    donutChart.update();
                });
        }
        fetchData();
    }

    // กราฟพฤติกรรม
    const behaviorCanvas = document.getElementById('behaviorChart');
    let behaviorChart = null;
    if (behaviorCanvas) {
        const behaviorCtx = behaviorCanvas.getContext('2d');
        behaviorChart = new Chart(behaviorCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'จำนวนเหตุการณ์',
                    data: [],
                    backgroundColor: '#FFB300'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        function fetchBehaviorData() {
            fetch(`api/fetch_chart_behavior.php?class=<?=$class?>&room=<?=$room?>&term=<?=$term?>&pee=<?=$pee?>`)
                .then(response => response.json())
                .then(data => {
                    behaviorChart.data.labels = data.map(item => item.behavior_type);
                    behaviorChart.data.datasets[0].data = data.map(item => parseInt(item.count_total));
                    behaviorChart.update();
                });
        }
        fetchBehaviorData();
    }

    // Fetch and update card summary (AJAX)
    fetch('api/fetch_dashboard_summary.php?class=<?=$class?>&room=<?=$room?>&term=<?=$term?>&pee=<?=$pee?>')
        .then(res => res.json())
        .then(data => {
            // พฤติกรรม (เดือนนี้)
            document.getElementById('behaviorCount').textContent = data.behavior_count ?? '-';
            // เยี่ยมบ้าน (แสดงรวม 2 ภาคเรียนที่ )
            let visitText = '-';
            if (typeof data.visit_count_t1 !== 'undefined' && typeof data.visit_count_t2 !== 'undefined') {
                visitText = `ภาคเรียนที่ 1: ${data.visit_count_t1} | ภาคเรียนที่ 2: ${data.visit_count_t2}`;
            } else if (typeof data.visit_count !== 'undefined') {
                visitText = data.visit_count;
            }
            document.getElementById('visitCount').textContent = visitText;
            // นักเรียนยากจน
            document.getElementById('poorCount').textContent = data.poor_count ?? '-';
            // SDQ (แสดงรวม 2 ภาคเรียนที่ )
            let sdqText = '-';
            if (typeof data.sdq_count_t1 !== 'undefined' && typeof data.sdq_count_t2 !== 'undefined') {
                sdqText = `ภาคเรียนที่ 1: ${data.sdq_count_t1} | ภาคเรียนที่ 2: ${data.sdq_count_t2}`;
            } else if (typeof data.sdq_count !== 'undefined') {
                sdqText = data.sdq_count;
            }
            document.getElementById('sdqCount').textContent = sdqText;
            // EQ (แสดงรวม 2 ภาคเรียนที่ )
            let eqText = '-';
            if (typeof data.eq_count_t1 !== 'undefined' && typeof data.eq_count_t2 !== 'undefined') {
                eqText = `ภาคเรียนที่ 1: ${data.eq_count_t1} | ภาคเรียนที่ 2: ${data.eq_count_t2}`;
            } else if (typeof data.eq_count !== 'undefined') {
                eqText = data.eq_count;
            }
            document.getElementById('eqCount').textContent = eqText;
            // Screening (แสดงรวม 2 ภาคเรียนที่ )
            let screenText = '-';
            if (typeof data.screen_count_t1 !== 'undefined' && typeof data.screen_count_t2 !== 'undefined') {
                screenText = `ภาคเรียนที่ 1: ${data.screen_count_t1} | ภาคเรียนที่ 2: ${data.screen_count_t2}`;
            } else if (typeof data.screen_count !== 'undefined') {
                screenText = data.screen_count;
            }
            document.getElementById('screenCount').textContent = screenText;
            // Homeroom
            document.getElementById('homeroomCount').textContent = data.homeroom_count ?? '-';
        });
});
</script>
</body>
</html>
