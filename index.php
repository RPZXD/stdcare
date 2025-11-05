<?php 

require_once('header.php');
require_once('config/Setting.php');
require_once('class/Utils.php');
require_once('config/Database.php');
require_once('class/Attendance.php');
require_once('class/UserLogin.php');
require_once('class/Student.php');

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);


// กำหนดวันที่ (วันนี้ หรือจาก GET)
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

date_default_timezone_set('Asia/Bangkok');
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$term = isset($user) ? $user->getTerm() : 1;
$pee = isset($user) ? $user->getPee() : date('Y');

// คำนวณสัปดาห์ปัจจุบัน
$weekStart = date('Y-m-d', strtotime('monday this week', strtotime($date)));
$weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

// คำนวณเดือนปัจจุบัน
$monthStart = date('Y-m-01', strtotime($date));
$monthEnd = date('Y-m-t', strtotime($date));

// เตรียมข้อมูลสรุป
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'purple', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'pink', 'bg' => 'bg-pink-100', 'text' => 'text-pink-700'],
];

// ตรวจสอบว่าตาราง student_attendance มีอยู่หรือไม่
function tableExists($db, $tableName) {
    try {
        $stmt = $db->prepare("SHOW TABLES LIKE :tableName");
        $stmt->execute([':tableName' => $tableName]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

$attendanceTableExists = tableExists($db, 'student_attendance');

// ดึงสถิติเพิ่มเติม
$stats = [
    'today' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0],
    'week' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0],
    'month' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0]
];

if ($attendanceTableExists) {
    // สถิติวันนี้
    $stmt = $db->prepare("
        SELECT 
            COUNT(DISTINCT student_id) as total,
            SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
        FROM student_attendance 
        WHERE attendance_date = :date AND term = :term AND year = :pee
    ");
    $stmt->execute([':date' => $date, ':term' => $term, ':pee' => $pee]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['total']) {
        $stats['today'] = $row;
    }

    // สถิติสัปดาห์นี้
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
        FROM student_attendance 
        WHERE attendance_date BETWEEN :start AND :end AND term = :term AND year = :pee
    ");
    $stmt->execute([':start' => $weekStart, ':end' => $weekEnd, ':term' => $term, ':pee' => $pee]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['total']) {
        $stats['week'] = $row;
    }

    // สถิติเดือนนี้
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
        FROM student_attendance 
        WHERE attendance_date BETWEEN :start AND :end AND term = :term AND year = :pee
    ");
    $stmt->execute([':start' => $monthStart, ':end' => $monthEnd, ':term' => $term, ':pee' => $pee]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['total']) {
        $stats['month'] = $row;
    }
}

// ดึงข้อมูล attendance ของทุกห้องในวันที่เลือกในครั้งเดียว
if ($attendanceTableExists) {
    $stmt = $db->prepare("
        SELECT s.Stu_major, s.Stu_room, a.attendance_status
        FROM student s
        LEFT JOIN student_attendance a
            ON s.Stu_id = a.student_id
            AND a.attendance_date = :dateC
            AND a.term = :term
            AND a.year = :pee
        WHERE s.Stu_status=1
        ORDER BY s.Stu_major, s.Stu_room
    ");
    $stmt->execute([
        ':dateC' => $date,
        ':term' => $term,
        ':pee' => $pee
    ]);
    $all_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // ถ้าไม่มีตาราง attendance ให้ดึงข้อมูลนักเรียนอย่างเดียว
    $stmt = $db->prepare("
        SELECT Stu_major, Stu_room, NULL as attendance_status
        FROM student
        WHERE Stu_status=1
        ORDER BY Stu_major, Stu_room
    ");
    $stmt->execute();
    $all_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// จัดกลุ่มข้อมูล
$class_map = [];
$status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
$total = 0;
foreach ($all_attendance as $row) {
    $major = $row['Stu_major'];
    $room = $row['Stu_room'];
    $status = $row['attendance_status'];
    $key = $major . '-' . $room;
    if (!isset($class_map[$key])) {
        $class_map[$key] = [
            'Stu_major' => $major,
            'Stu_room' => $room,
            'count' => 0,
            'status' => ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0]
        ];
    }
    $class_map[$key]['count']++;
    if ($status && isset($class_map[$key]['status'][$status])) {
        $class_map[$key]['status'][$status]++;
        $status_count[$status]++;
    }
    $total++;
}

// สร้าง $classes ใหม่สำหรับตาราง
$classes = array_values($class_map);
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
        <div class="mb-6 flex flex-wrap gap-4 items-center justify-between bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-xl shadow">
            <div class="flex items-center gap-3">
                <div class="bg-blue-500 text-white p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">ภาพรวมการมาเรียน</h2>
                    <p class="text-gray-600">วันที่ <?= htmlspecialchars(thaiDateShort($date)) ?></p>
                </div>
            </div>
            <form method="get" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow">
                <label for="date" class="text-gray-700 font-medium">📅</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border-0 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 transition">
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-purple-600 shadow-md transition-all transform hover:scale-105">
                    แสดงข้อมูล
                </button>
            </form>
        </div>

        <!-- Quick Stats Cards - Overview -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📊</span> สถิติภาพรวม
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Card: วันนี้ -->
                <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">วันนี้</span>
                        <span class="text-2xl">📅</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($stats['today']['present']) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $stats['today']['late'] ?> / ขาด: <?= $stats['today']['absent'] ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= $stats['today']['total'] > 0 ? round(($stats['today']['present'] / $stats['today']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- Card: สัปดาห์นี้ -->
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">สัปดาห์นี้</span>
                        <span class="text-2xl">📆</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($stats['week']['present']) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $stats['week']['late'] ?> / ขาด: <?= $stats['week']['absent'] ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= $stats['week']['total'] > 0 ? round(($stats['week']['present'] / $stats['week']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- Card: เดือนนี้ -->
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">เดือนนี้</span>
                        <span class="text-2xl">📊</span>
                    </div>
                    <div class="text-3xl font-bold mb-1"><?= number_format($stats['month']['present']) ?></div>
                    <div class="text-xs opacity-80">มาเรียน / สาย: <?= $stats['month']['late'] ?> / ขาด: <?= $stats['month']['absent'] ?></div>
                    <div class="mt-2 text-sm font-medium">
                        <?= $stats['month']['total'] > 0 ? round(($stats['month']['present'] / $stats['month']['total']) * 100, 1) : 0 ?>% เข้าเรียน
                    </div>
                </div>

                <!-- Card: เปอร์เซ็นต์โดยรวม -->
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">อัตราเข้าเรียน</span>
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="text-3xl font-bold mb-1">
                        <?= $stats['today']['total'] > 0 ? round(($stats['today']['present'] / $stats['today']['total']) * 100, 1) : 0 ?>%
                    </div>
                    <div class="text-xs opacity-80">เป้าหมาย: 95%</div>
                    <div class="mt-2">
                        <div class="bg-white/30 rounded-full h-2 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-500" style="width: <?= $stats['today']['total'] > 0 ? round(($stats['today']['present'] / $stats['today']['total']) * 100) : 0 ?>%"></div>
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
                <div class="text-5xl font-bold mb-2">
                    <?php
                    // นับยอดนักเรียนทั้งหมด
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $totalStudents = $row['total'];
                    echo number_format($totalStudents);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนทั้งหมด</div>
                <div class="mt-2 text-sm opacity-80">รวมทุกระดับชั้น</div>
            </div>
            <!-- Card: นักเรียน ม.ต้น -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-teal-400 to-teal-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200 cursor-pointer">
                <span class="text-4xl mb-2">📚</span>
                <div class="text-5xl font-bold mb-2">
                    <?php
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (1,2,3)");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $juniorStudents = $row['total'];
                    echo number_format($juniorStudents);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมต้น</div>
                <div class="mt-2 text-sm opacity-80">
                    ชั้น ม.1 - ม.3 (<?= $totalStudents > 0 ? round(($juniorStudents/$totalStudents)*100, 1) : 0 ?>%)
                </div>
            </div>
            <!-- Card: นักเรียน ม.ปลาย -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-amber-400 to-amber-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200 cursor-pointer">
                <span class="text-4xl mb-2">🎯</span>
                <div class="text-5xl font-bold mb-2">
                    <?php
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (4,5,6)");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $seniorStudents = $row['total'];
                    echo number_format($seniorStudents);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมปลาย</div>
                <div class="mt-2 text-sm opacity-80">
                    ชั้น ม.4 - ม.6 (<?= $totalStudents > 0 ? round(($seniorStudents/$totalStudents)*100, 1) : 0 ?>%)
                </div>
            </div>
        </div>
        </div>

        <!-- Attendance Status Summary -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📋</span> สรุปสถานะการมาเรียนวันนี้
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <?php foreach ($status_labels as $key => $info): ?>
                <div class="rounded-xl shadow-md <?= $info['bg'] ?> flex flex-col items-center p-4 hover:scale-105 transition-all duration-200 cursor-pointer border-2 border-transparent hover:border-gray-300">
                    <span class="text-4xl mb-2"><?= $info['emoji'] ?></span>
                    <div class="text-3xl font-bold <?= $info['text'] ?> mb-1"><?= number_format($status_count[$key]) ?></div>
                    <div class="font-semibold <?= $info['text'] ?> text-sm mb-1"><?= $info['label'] ?></div>
                    <div class="text-xs text-gray-600">
                        <?= $total ? round($status_count[$key]*100/$total,1) : 0 ?>%
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </div>

        <!-- Charts and Table Section -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="text-2xl">📈</span> รายละเอียดตามห้องเรียน
            </h3>
            <div class="flex flex-col lg:flex-row gap-6">
            <!-- ซ้าย: Pie Chart -->
            <div class="w-full lg:w-2/5 flex justify-center items-start">
                <div class="bg-white rounded-xl shadow-lg p-6 w-full">
                    <h4 class="text-lg font-bold text-gray-700 mb-4 text-center">กราฟสัดส่วนการมาเรียน</h4>
                    <canvas id="pieChartOverview" height="280"></canvas>
                </div>
            </div>
            <!-- ขวา: DataTable -->
            <div class="w-full lg:w-3/5">
                <div class="overflow-x-auto rounded-xl shadow-lg bg-white p-6">
                    <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-700">รายละเอียดแต่ละห้อง</h4>
                        <div class="flex items-center gap-2">
                            <label for="classFilter" class="font-medium text-gray-600 text-sm">🔍 กรอง:</label>
                            <select id="classFilter" class="border-2 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 transition text-sm">
                                <option value="">ทุกชั้น</option>
                                <?php
                                $class_set = [];
                                foreach ($classes as $c) {
                                    $class_set[$c['Stu_major']] = true;
                                }
                                foreach (array_keys($class_set) as $major) {
                                    echo '<option value="ม.' . htmlspecialchars($major) . '">ม.' . htmlspecialchars($major) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <table id="attendanceTable" class="min-w-full border-collapse">
                        <thead class="bg-gradient-to-r from-blue-500 to-purple-500 text-white">
                            <tr>
                                <th class="px-4 py-3 text-center font-semibold">ชั้น</th>
                                <th class="px-4 py-3 text-center font-semibold">ห้อง</th>
                                <th class="px-4 py-3 text-center font-semibold">จำนวน</th>
                                <?php foreach ($status_labels as $info): ?>
                                    <th class="px-3 py-3 text-center font-semibold" title="<?= $info['label'] ?>"><?= $info['emoji'] ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($classes as $c): ?>
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-4 py-3 text-center font-medium text-gray-700">ม.<?= htmlspecialchars($c['Stu_major']) ?></td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-700"><?= htmlspecialchars($c['Stu_room']) ?></td>
                                    <td class="px-4 py-3 text-center font-bold text-blue-600"><?= $c['count'] ?></td>
                                    <?php foreach ($status_labels as $k => $info): ?>
                                        <td class="px-3 py-3 text-center font-semibold <?= $info['text'] ?>">
                                            <?= $c['status'][$k] > 0 ? $c['status'][$k] : '-' ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

<!-- DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart with enhanced styling
    var ctx = document.getElementById('pieChartOverview').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                <?php foreach ($status_labels as $info): ?>
                    "<?= $info['emoji'].' '.$info['label'] ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [
                    <?= implode(',', array_values($status_count)) ?>
                ],
                backgroundColor: [
                    '#10b981', // green - มาเรียน
                    '#ef4444', // red - ขาดเรียน
                    '#f59e0b', // amber - มาสาย
                    '#3b82f6', // blue - ลาป่วย
                    '#8b5cf6', // purple - ลากิจ
                    '#ec4899', // pink - กิจกรรม
                ],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        font: { size: 13, weight: '500' },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' คน (' + percentage + '%)';
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // DataTable with enhanced configuration
    var table = $('#attendanceTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ทั้งหมด"]],
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        dom: '<"flex flex-wrap gap-2 items-center justify-between mb-3"lf>rtip',
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json",
            search: "_INPUT_",
            searchPlaceholder: "ค้นหา...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "ไม่มีข้อมูล",
            infoFiltered: "(กรองจาก _MAX_ รายการทั้งหมด)",
            paginate: {
                first: "แรก",
                last: "สุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        },
        columnDefs: [
            { targets: [0], orderable: true, className: 'font-bold' },
            { targets: [1], orderable: true },
            { targets: [2], orderable: true, className: 'font-bold text-blue-600' },
            { targets: '_all', orderable: false }
        ],
        order: [[0, 'asc'], [1, 'asc']],
        drawCallback: function() {
            // Add hover effects and styling after table draw
            $('#attendanceTable tbody tr').hover(
                function() { $(this).addClass('bg-blue-50 scale-[1.01] shadow-sm'); },
                function() { $(this).removeClass('bg-blue-50 scale-[1.01] shadow-sm'); }
            );
        }
    });

    // Enhanced filter with animation
    $('#classFilter').on('change', function() {
        var val = $(this).val();
        if (val) {
            table.column(0).search('^' + val + '$', true, false).draw();
        } else {
            table.column(0).search('').draw();
        }
        // Add animation effect
        $('#attendanceTable').addClass('animate-pulse');
        setTimeout(() => $('#attendanceTable').removeClass('animate-pulse'), 300);
    });

    // Auto-refresh notification (optional)
    let lastRefresh = new Date();
    setInterval(() => {
        let now = new Date();
        let diff = Math.floor((now - lastRefresh) / 1000 / 60);
        if (diff >= 5) {
            // Show refresh prompt after 5 minutes
            console.log('ข้อมูลอาจไม่เป็นปัจจุบัน กรุณารีเฟรชหน้าเว็บ');
        }
    }, 60000); // Check every minute
});
</script>
<?php require_once('script.php');?>
</body>
</html>
