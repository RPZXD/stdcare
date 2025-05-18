<?php 

require_once('header.php');
require_once('config/Setting.php');
require_once('class/Utils.php');
require_once('config/Database.php');
require_once('class/Student.php');

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$student = new Student($db);

// นับยอดนักเรียน
$stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1");
$stmt->execute();
$total_all = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (1,2,3)");
$stmt->execute();
$total_lower = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (4,5,6)");
$stmt->execute();
$total_upper = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d');
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
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container-fluid">
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card: นักเรียนทั้งหมด -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-blue-400 to-blue-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2"><?= number_format($total_all) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนทั้งหมด</div>
                <div class="mt-2 text-sm opacity-80">รวมทุกระดับชั้น</div>
            </div>
            <!-- Card: นักเรียน ม.ต้น -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-green-400 to-green-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2"><?= number_format($total_lower) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมต้น</div>
                <div class="mt-2 text-sm opacity-80">ชั้น ม.1 - ม.3</div>
            </div>
            <!-- Card: นักเรียน ม.ปลาย -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-yellow-400 to-yellow-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2"><?= number_format($total_upper) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมปลาย</div>
                <div class="mt-2 text-sm opacity-80">ชั้น ม.4 - ม.6</div>
            </div>
        </div>

        <h4 class="text-xl font-bold text-blue-700 mb-4">สรุปการมาเรียนของนักเรียนประจำวันที่ <?=Utils::convertToThaiDatePlus($date);?> </h4>
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            <!-- ซ้าย: Bar Chart ม.ต้น -->
            <div class="w-full md:w-1/2 flex flex-col items-center">
                <div class="card card-primary w-full mb-4">
                    <div class="card-header bg-green-500 text-white rounded-t">
                        <h3 class="card-title">มัธยมศึกษาตอนต้น</h3>
                    </div>
                    <div class="card-body bg-white rounded-b">
                        <canvas id="barChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <!-- ขวา: Bar Chart ม.ปลาย -->
            <div class="w-full md:w-1/2 flex flex-col items-center">
                <div class="card card-primary w-full mb-4">
                    <div class="card-header bg-yellow-500 text-white rounded-t">
                        <h3 class="card-title">มัธยมศึกษาตอนปลาย</h3>
                    </div>
                    <div class="card-body bg-white rounded-b">
                        <canvas id="barChart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- เพิ่ม: สถิติรวมการเข้าเรียนวันนี้ -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="rounded-xl shadow-lg bg-white p-6">
            <div class="text-lg font-semibold mb-2 text-blue-700">สถิติการเข้าเรียนวันนี้ (มัธยมต้น)</div>
            <ul class="space-y-1">
              <?php
              // สถิติการเข้าเรียนวันนี้ ม.ต้น
              $stmt = $db->prepare("
                SELECT 
                  CASE 
                    WHEN sa.attendance_status = 1 THEN 'มาเรียน'
                    WHEN sa.attendance_status = 2 THEN 'ขาดเรียน'
                    WHEN sa.attendance_status = 3 THEN 'มาสาย'
                    WHEN sa.attendance_status = 4 THEN 'ลาป่วย'
                    WHEN sa.attendance_status = 5 THEN 'ลากิจ'
                    WHEN sa.attendance_status = 6 THEN 'กิจกรรม'
                    ELSE 'ไม่ระบุ'
                  END AS status_name,
                  COUNT(*) as total
                FROM student_attendance sa
                INNER JOIN student s ON sa.student_id = s.Stu_id
                WHERE s.Stu_status=1 AND s.Stu_major IN (1,2,3) AND sa.attendance_date = ?
                GROUP BY sa.attendance_status
              ");
              $stmt->execute([$date]);
              $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($stats as $row): ?>
                <li class="flex justify-between border-b pb-1">
                  <span><?= htmlspecialchars($row['status_name']) ?></span>
                  <span class="font-bold"><?= number_format($row['total']) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="rounded-xl shadow-lg bg-white p-6">
            <div class="text-lg font-semibold mb-2 text-yellow-700">สถิติการเข้าเรียนวันนี้ (มัธยมปลาย)</div>
            <ul class="space-y-1">
              <?php
              // สถิติการเข้าเรียนวันนี้ ม.ปลาย
              $stmt = $db->prepare("
                SELECT 
                  CASE 
                    WHEN sa.attendance_status = 1 THEN 'มาเรียน'
                    WHEN sa.attendance_status = 2 THEN 'ขาดเรียน'
                    WHEN sa.attendance_status = 3 THEN 'มาสาย'
                    WHEN sa.attendance_status = 4 THEN 'ลาป่วย'
                    WHEN sa.attendance_status = 5 THEN 'ลากิจ'
                    WHEN sa.attendance_status = 6 THEN 'กิจกรรม'
                    ELSE 'ไม่ระบุ'
                  END AS status_name,
                  COUNT(*) as total
                FROM student_attendance sa
                INNER JOIN student s ON sa.student_id = s.Stu_id
                WHERE s.Stu_status=1 AND s.Stu_major IN (4,5,6) AND sa.attendance_date = ?
                GROUP BY sa.attendance_status
              ");
              $stmt->execute([$date]);
              $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($stats as $row): ?>
                <li class="flex justify-between border-b pb-1">
                  <span><?= htmlspecialchars($row['status_name']) ?></span>
                  <span class="font-bold"><?= number_format($row['total']) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function fetchChartData(chartId, apiUrl) {
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

fetchChartData('barChart1', 'api/fetch_chartstu.php?level=1-3');
fetchChartData('barChart2', 'api/fetch_chartstu.php?level=4-6');
</script>
<?php require_once('script.php');?>
</body>
</html>
