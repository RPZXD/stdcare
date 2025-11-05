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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$currentDate = date('Y-m-d');

// คำนวณสัปดาห์และเดือนจากวันที่ปัจจุบัน
$weekStart = date('Y-m-d', strtotime('monday this week', strtotime($currentDate)));
$weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($currentDate)));
$monthStart = date('Y-m-01', strtotime($currentDate));
$monthEnd = date('Y-m-t', strtotime($currentDate));

// ดึงสถิติภาพรวมการเข้าเรียน
$attendanceStats = [
    'today' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'business' => 0, 'activity' => 0],
    'week' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'business' => 0, 'activity' => 0],
    'month' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'business' => 0, 'activity' => 0]
];

// สถิติวันนี้
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late,
        SUM(CASE WHEN attendance_status = 4 THEN 1 ELSE 0 END) as sick,
        SUM(CASE WHEN attendance_status = 5 THEN 1 ELSE 0 END) as business,
        SUM(CASE WHEN attendance_status = 6 THEN 1 ELSE 0 END) as activity
    FROM student_attendance 
    WHERE attendance_date = ?
");
$stmt->execute([$date]);
$attendanceStats['today'] = $stmt->fetch(PDO::FETCH_ASSOC);

// สถิติสัปดาห์นี้
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late,
        SUM(CASE WHEN attendance_status = 4 THEN 1 ELSE 0 END) as sick,
        SUM(CASE WHEN attendance_status = 5 THEN 1 ELSE 0 END) as business,
        SUM(CASE WHEN attendance_status = 6 THEN 1 ELSE 0 END) as activity
    FROM student_attendance 
    WHERE attendance_date BETWEEN ? AND ?
");
$stmt->execute([$weekStart, $weekEnd]);
$attendanceStats['week'] = $stmt->fetch(PDO::FETCH_ASSOC);

// สถิติเดือนนี้
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late,
        SUM(CASE WHEN attendance_status = 4 THEN 1 ELSE 0 END) as sick,
        SUM(CASE WHEN attendance_status = 5 THEN 1 ELSE 0 END) as business,
        SUM(CASE WHEN attendance_status = 6 THEN 1 ELSE 0 END) as activity
    FROM student_attendance 
    WHERE attendance_date BETWEEN ? AND ?
");
$stmt->execute([$monthStart, $monthEnd]);
$attendanceStats['month'] = $stmt->fetch(PDO::FETCH_ASSOC);

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function thaiDateShort($date) {
    $months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}
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
        <!-- Header Section with Date Picker -->
        <div class="mb-6 flex flex-wrap gap-4 items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-xl shadow">
            <div class="flex items-center gap-3">
                <div class="bg-purple-500 text-white p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">📊 สถิติการมาเรียน</h2>
                    <p class="text-gray-600">วันที่ <?= htmlspecialchars(thaiDateShort($date)) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="get" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow">
                    <label for="date" class="text-gray-700 font-medium">📅</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border-0 rounded px-3 py-2 focus:ring-2 focus:ring-purple-400 transition">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 shadow-md transition-all transform hover:scale-105">
                        แสดงข้อมูล
                    </button>
                </form>
                
                <!-- Export Buttons -->
                <div class="flex gap-2">
                    <button onclick="exportToExcel()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-green-700 shadow-md transition-all transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </button>
                    <button onclick="exportToPNG()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-md transition-all transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        PNG
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Overview Stats -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📈</span> ภาพรวมสถิติ
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- วันนี้ -->
                <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">วันนี้ (วันที่เลือก)</span>
                        <span class="text-2xl">📅</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($attendanceStats['today']['present'] ?? 0) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $attendanceStats['today']['late'] ?? 0 ?> / ขาด: <?= $attendanceStats['today']['absent'] ?? 0 ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- สัปดาห์นี้ -->
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">สัปดาห์นี้ (ปัจจุบัน)</span>
                        <span class="text-2xl">📆</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($attendanceStats['week']['present'] ?? 0) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $attendanceStats['week']['late'] ?? 0 ?> / ขาด: <?= $attendanceStats['week']['absent'] ?? 0 ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= ($attendanceStats['week']['total'] ?? 0) > 0 ? round((($attendanceStats['week']['present'] ?? 0) / $attendanceStats['week']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- เดือนนี้ -->
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">เดือนนี้ (ปัจจุบัน)</span>
                        <span class="text-2xl">📊</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($attendanceStats['month']['present'] ?? 0) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $attendanceStats['month']['late'] ?? 0 ?> / ขาด: <?= $attendanceStats['month']['absent'] ?? 0 ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= ($attendanceStats['month']['total'] ?? 0) > 0 ? round((($attendanceStats['month']['present'] ?? 0) / $attendanceStats['month']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- อัตราเข้าเรียนโดยรวม -->
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">อัตราเข้าเรียน</span>
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="text-3xl font-bold mb-1">
                        <?= ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0 ?>%
                    </div>
                    <div class="text-xs opacity-80">เป้าหมาย: 95%</div>
                    <div class="mt-2">
                        <div class="bg-white/30 rounded-full h-2 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-500" style="width: <?= ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Count Cards -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">👥</span> จำนวนนักเรียน
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Card: นักเรียนทั้งหมด -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-indigo-400 to-indigo-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200 cursor-pointer">
                <span class="text-4xl mb-2">🎓</span>
                <div class="text-5xl font-bold mb-2"><?= number_format($total_all) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนทั้งหมด</div>
                <div class="mt-2 text-sm opacity-80">รวมทุกระดับชั้น</div>
            </div>
            <!-- Card: นักเรียน ม.ต้น -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-teal-400 to-teal-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200 cursor-pointer">
                <span class="text-4xl mb-2">📚</span>
                <div class="text-5xl font-bold mb-2"><?= number_format($total_lower) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมต้น</div>
                <div class="mt-2 text-sm opacity-80">
                    ชั้น ม.1 - ม.3 (<?= $total_all > 0 ? round(($total_lower/$total_all)*100, 1) : 0 ?>%)
                </div>
            </div>
            <!-- Card: นักเรียน ม.ปลาย -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-amber-400 to-amber-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200 cursor-pointer">
                <span class="text-4xl mb-2">🎯</span>
                <div class="text-5xl font-bold mb-2"><?= number_format($total_upper) ?></div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมปลาย</div>
                <div class="mt-2 text-sm opacity-80">
                    ชั้น ม.4 - ม.6 (<?= $total_all > 0 ? round(($total_upper/$total_all)*100, 1) : 0 ?>%)
                </div>
            </div>
        </div>
        </div>

        <!-- Bar Charts Section -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📊</span> กราฟสถิติการเข้าเรียนแต่ละห้อง
            </h3>
            <div class="flex flex-col gap-6">
            <!-- ซ้าย: Bar Chart ม.ต้น -->
            <div class="w-full lg:w-full flex flex-col">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-6 py-4">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <span class="text-2xl">📚</span>
                            มัธยมศึกษาตอนต้น (ม.1-3)
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="barChart1" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- ขวา: Bar Chart ม.ปลาย -->
            <div class="w-full lg:w-full flex flex-col">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-4">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <span class="text-2xl">🎯</span>
                            มัธยมศึกษาตอนปลาย (ม.4-6)
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="barChart2" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Detailed Statistics Section -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📋</span> รายละเอียดสถิติการเข้าเรียนวันนี้
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="rounded-xl shadow-lg bg-white overflow-hidden hover:shadow-2xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-teal-400 to-teal-500 text-white px-6 py-4">
              <div class="text-xl font-bold flex items-center gap-2">
                <span class="text-2xl">📚</span>
                สถิติการเข้าเรียนวันนี้ (มัธยมต้น)
              </div>
            </div>
            <div class="p-6">
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
                  sa.attendance_status,
                  COUNT(*) as total
                FROM student_attendance sa
                INNER JOIN student s ON sa.student_id = s.Stu_id
                WHERE s.Stu_status=1 AND s.Stu_major IN (1,2,3) AND sa.attendance_date = ?
                GROUP BY sa.attendance_status
                ORDER BY sa.attendance_status
              ");
              $stmt->execute([$date]);
              $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              $statusColors = [
                '1' => 'bg-green-100 text-green-700 border-green-300',
                '2' => 'bg-red-100 text-red-700 border-red-300',
                '3' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                '4' => 'bg-blue-100 text-blue-700 border-blue-300',
                '5' => 'bg-purple-100 text-purple-700 border-purple-300',
                '6' => 'bg-pink-100 text-pink-700 border-pink-300'
              ];
              
              $statusEmojis = [
                '1' => '✅', '2' => '❌', '3' => '🕒', '4' => '🤒', '5' => '📝', '6' => '🎉'
              ];
              
              $totalLower = array_sum(array_column($stats, 'total'));
              
              if (count($stats) > 0): ?>
                <div class="space-y-3">
                  <?php foreach ($stats as $row): 
                    $percentage = $totalLower > 0 ? round(($row['total'] / $totalLower) * 100, 1) : 0;
                    $color = $statusColors[$row['attendance_status']] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                    $emoji = $statusEmojis[$row['attendance_status']] ?? '📌';
                  ?>
                    <div class="flex items-center justify-between p-3 <?= $color ?> rounded-lg border-2 hover:scale-[1.02] transition-transform">
                      <div class="flex items-center gap-2">
                        <span class="text-2xl"><?= $emoji ?></span>
                        <span class="font-semibold"><?= htmlspecialchars($row['status_name']) ?></span>
                      </div>
                      <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($row['total']) ?></div>
                        <div class="text-xs opacity-75"><?= $percentage ?>%</div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <div class="mt-4 p-3 bg-teal-50 border-2 border-teal-300 rounded-lg">
                    <div class="flex justify-between items-center">
                      <span class="font-bold text-teal-700">รวมทั้งหมด</span>
                      <span class="text-2xl font-bold text-teal-700"><?= number_format($totalLower) ?> คน</span>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                  <span class="text-4xl block mb-2">📭</span>
                  <p>ยังไม่มีข้อมูลการเช็คชื่อ</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="rounded-xl shadow-lg bg-white overflow-hidden hover:shadow-2xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-white px-6 py-4">
              <div class="text-xl font-bold flex items-center gap-2">
                <span class="text-2xl">🎯</span>
                สถิติการเข้าเรียนวันนี้ (มัธยมปลาย)
              </div>
            </div>
            <div class="p-6">
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
                  sa.attendance_status,
                  COUNT(*) as total
                FROM student_attendance sa
                INNER JOIN student s ON sa.student_id = s.Stu_id
                WHERE s.Stu_status=1 AND s.Stu_major IN (4,5,6) AND sa.attendance_date = ?
                GROUP BY sa.attendance_status
                ORDER BY sa.attendance_status
              ");
              $stmt->execute([$date]);
              $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
              $totalUpper = array_sum(array_column($stats, 'total'));
              
              if (count($stats) > 0): ?>
                <div class="space-y-3">
                  <?php foreach ($stats as $row): 
                    $percentage = $totalUpper > 0 ? round(($row['total'] / $totalUpper) * 100, 1) : 0;
                    $color = $statusColors[$row['attendance_status']] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                    $emoji = $statusEmojis[$row['attendance_status']] ?? '📌';
                  ?>
                    <div class="flex items-center justify-between p-3 <?= $color ?> rounded-lg border-2 hover:scale-[1.02] transition-transform">
                      <div class="flex items-center gap-2">
                        <span class="text-2xl"><?= $emoji ?></span>
                        <span class="font-semibold"><?= htmlspecialchars($row['status_name']) ?></span>
                      </div>
                      <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($row['total']) ?></div>
                        <div class="text-xs opacity-75"><?= $percentage ?>%</div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <div class="mt-4 p-3 bg-amber-50 border-2 border-amber-300 rounded-lg">
                    <div class="flex justify-between items-center">
                      <span class="font-bold text-amber-700">รวมทั้งหมด</span>
                      <span class="text-2xl font-bold text-amber-700"><?= number_format($totalUpper) ?> คน</span>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                  <span class="text-4xl block mb-2">📭</span>
                  <p>ยังไม่มีข้อมูลการเช็คชื่อ</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
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

<!-- Include libraries for export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Get date from URL or use today's date
const urlParams = new URLSearchParams(window.location.search);
const selectedDate = urlParams.get('date') || '<?= $date ?>';

function fetchChartData(chartId, apiUrl) {
    // Add date parameter to API URL
    const fullUrl = apiUrl + '&date=' + selectedDate;
    
    fetch(fullUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: true, 
                            position: 'bottom',
                            labels: {
                                font: { size: 12, weight: '500' },
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.parsed.y || 0;
                                    return label + ': ' + value + ' คน';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            stacked: true,
                            grid: { display: false },
                            ticks: { 
                                font: { size: 11, weight: '500' },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: { 
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { 
                                font: { size: 11 },
                                callback: function(value) {
                                    return Number.isInteger(value) ? value + ' คน' : '';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
            // Show error message in chart container
            const container = document.getElementById(chartId).parentElement;
            container.innerHTML = '<div class="text-center py-8 text-red-500"><span class="text-4xl block mb-2">⚠️</span><p>ไม่สามารถโหลดข้อมูลได้<br><small class="text-xs">' + error.message + '</small></p></div>';
        });
}

// Load charts with animation delay
document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading charts for date:', selectedDate);
    setTimeout(() => fetchChartData('barChart1', 'api/fetch_chartstu.php?level=1-3'), 100);
    setTimeout(() => fetchChartData('barChart2', 'api/fetch_chartstu.php?level=4-6'), 300);
});

// Auto-refresh notification
let lastRefresh = new Date();
setInterval(() => {
    let now = new Date();
    let diff = Math.floor((now - lastRefresh) / 1000 / 60);
    if (diff >= 5) {
        console.log('ข้อมูลอาจไม่เป็นปัจจุบัน กรุณารีเฟรชหน้าเว็บ');
    }
}, 60000);

// Export to Excel function
function exportToExcel() {
    const date = '<?= htmlspecialchars(thaiDateShort($date)) ?>';
    const wb = XLSX.utils.book_new();
    
    // Sheet 1: Overview Statistics
    const overviewData = [
        ['สถิติการมาเรียน - ' + date],
        [],
        ['ภาพรวมสถิติ'],
        ['ประเภท', 'มาเรียน', 'ขาดเรียน', 'มาสาย', 'ลาป่วย', 'ลากิจ', 'กิจกรรม', 'รวม', 'อัตราเข้าเรียน'],
        [
            'วันนี้ (วันที่เลือก)',
            <?= $attendanceStats['today']['present'] ?? 0 ?>,
            <?= $attendanceStats['today']['absent'] ?? 0 ?>,
            <?= $attendanceStats['today']['late'] ?? 0 ?>,
            <?= $attendanceStats['today']['sick'] ?? 0 ?>,
            <?= $attendanceStats['today']['business'] ?? 0 ?>,
            <?= $attendanceStats['today']['activity'] ?? 0 ?>,
            <?= $attendanceStats['today']['total'] ?? 0 ?>,
            '<?= ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0 ?>%'
        ],
        [
            'สัปดาห์นี้ (ปัจจุบัน)',
            <?= $attendanceStats['week']['present'] ?? 0 ?>,
            <?= $attendanceStats['week']['absent'] ?? 0 ?>,
            <?= $attendanceStats['week']['late'] ?? 0 ?>,
            <?= $attendanceStats['week']['sick'] ?? 0 ?>,
            <?= $attendanceStats['week']['business'] ?? 0 ?>,
            <?= $attendanceStats['week']['activity'] ?? 0 ?>,
            <?= $attendanceStats['week']['total'] ?? 0 ?>,
            '<?= ($attendanceStats['week']['total'] ?? 0) > 0 ? round((($attendanceStats['week']['present'] ?? 0) / $attendanceStats['week']['total']) * 100, 1) : 0 ?>%'
        ],
        [
            'เดือนนี้ (ปัจจุบัน)',
            <?= $attendanceStats['month']['present'] ?? 0 ?>,
            <?= $attendanceStats['month']['absent'] ?? 0 ?>,
            <?= $attendanceStats['month']['late'] ?? 0 ?>,
            <?= $attendanceStats['month']['sick'] ?? 0 ?>,
            <?= $attendanceStats['month']['business'] ?? 0 ?>,
            <?= $attendanceStats['month']['activity'] ?? 0 ?>,
            <?= $attendanceStats['month']['total'] ?? 0 ?>,
            '<?= ($attendanceStats['month']['total'] ?? 0) > 0 ? round((($attendanceStats['month']['present'] ?? 0) / $attendanceStats['month']['total']) * 100, 1) : 0 ?>%'
        ],
        [],
        ['จำนวนนักเรียน'],
        ['ทั้งหมด', 'มัธยมต้น (ม.1-3)', 'มัธยมปลาย (ม.4-6)'],
        [<?= $total_all ?>, <?= $total_lower ?>, <?= $total_upper ?>]
    ];
    
    const ws1 = XLSX.utils.aoa_to_sheet(overviewData);
    XLSX.utils.book_append_sheet(wb, ws1, 'ภาพรวม');
    
    <?php
    // Export Junior High Stats
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
        ORDER BY sa.attendance_status
    ");
    $stmt->execute([$date]);
    $statsLower = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    // Sheet 2: Junior High Details
    const juniorData = [
        ['สถิติการเข้าเรียนวันนี้ (มัธยมต้น)'],
        [],
        ['สถานะ', 'จำนวน'],
        <?php foreach ($statsLower as $row): ?>
        ['<?= addslashes($row['status_name']) ?>', <?= $row['total'] ?>],
        <?php endforeach; ?>
        [],
        ['รวมทั้งหมด', <?= array_sum(array_column($statsLower, 'total')) ?>]
    ];
    
    const ws2 = XLSX.utils.aoa_to_sheet(juniorData);
    XLSX.utils.book_append_sheet(wb, ws2, 'มัธยมต้น');
    
    <?php
    // Export Senior High Stats
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
        ORDER BY sa.attendance_status
    ");
    $stmt->execute([$date]);
    $statsUpper = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    // Sheet 3: Senior High Details
    const seniorData = [
        ['สถิติการเข้าเรียนวันนี้ (มัธยมปลาย)'],
        [],
        ['สถานะ', 'จำนวน'],
        <?php foreach ($statsUpper as $row): ?>
        ['<?= addslashes($row['status_name']) ?>', <?= $row['total'] ?>],
        <?php endforeach; ?>
        [],
        ['รวมทั้งหมด', <?= array_sum(array_column($statsUpper, 'total')) ?>]
    ];
    
    const ws3 = XLSX.utils.aoa_to_sheet(seniorData);
    XLSX.utils.book_append_sheet(wb, ws3, 'มัธยมปลาย');
    
    // Download the file
    const filename = 'สถิติการมาเรียน_' + selectedDate + '.xlsx';
    XLSX.writeFile(wb, filename);
    
    // Show success message
    alert('✅ ดาวน์โหลดไฟล์ Excel สำเร็จ!');
}

// Export to PNG function
async function exportToPNG() {
    const date = '<?= htmlspecialchars(thaiDateShort($date)) ?>';
    
    // Show loading
    const loadingDiv = document.createElement('div');
    loadingDiv.innerHTML = '<div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.3);z-index:10000;"><div style="text-align:center;"><div style="font-size:40px;margin-bottom:10px;">📸</div><div style="font-size:18px;font-weight:bold;">กำลังสร้างภาพ...</div><div style="font-size:14px;color:#666;margin-top:5px;">กรุณารอสักครู่</div></div></div>';
    document.body.appendChild(loadingDiv);
    
    try {
        // Capture the content section
        const contentSection = document.querySelector('.content');
        
        // Temporarily remove scrollbars and set background
        const originalOverflow = contentSection.style.overflow;
        const originalBg = contentSection.style.background;
        contentSection.style.overflow = 'visible';
        contentSection.style.background = '#f3f4f6';
        
        // Add padding for better screenshot
        const wrapper = document.createElement('div');
        wrapper.style.padding = '20px';
        wrapper.style.background = '#f3f4f6';
        contentSection.parentNode.insertBefore(wrapper, contentSection);
        wrapper.appendChild(contentSection);
        
        const canvas = await html2canvas(wrapper, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#f3f4f6',
            logging: false,
            windowWidth: wrapper.scrollWidth,
            windowHeight: wrapper.scrollHeight
        });
        
        // Restore original styles
        contentSection.style.overflow = originalOverflow;
        contentSection.style.background = originalBg;
        wrapper.parentNode.insertBefore(contentSection, wrapper);
        wrapper.remove();
        
        // Convert canvas to blob and download
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'สถิติการมาเรียน_' + selectedDate + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            // Remove loading
            document.body.removeChild(loadingDiv);
            
            // Show success message
            alert('✅ ดาวน์โหลดภาพ PNG สำเร็จ!');
        });
        
    } catch (error) {
        console.error('Error generating PNG:', error);
        document.body.removeChild(loadingDiv);
        alert('❌ เกิดข้อผิดพลาดในการสร้างภาพ: ' + error.message);
    }
}
</script>
<?php require_once('script.php');?>
</body>
</html>
