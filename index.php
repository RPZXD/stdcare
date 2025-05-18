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
function convertToBuddhistYear($date) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);
        if ($year < 2500) $year += 543;
        return $year . '-' . $month . '-' . $day;
    }
    return $date;
}
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
$dateC = convertToBuddhistYear($date);

$term = isset($user) ? $user->getTerm() : 1;
$pee = isset($user) ? $user->getPee() : date('Y');

// เตรียมข้อมูลสรุป
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'purple', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'pink', 'bg' => 'bg-pink-100', 'text' => 'text-pink-700'],
];

// ดึงข้อมูล attendance ของทุกห้องในวันที่เลือกในครั้งเดียว
$stmt = $db->prepare("
    SELECT s.Stu_major, s.Stu_room, a.attendance_status
    FROM student s
    LEFT JOIN student_attendance a
        ON s.Stu_id = a.student_id
        AND a.attendance_date = :dateC
        AND a.term = :term
        AND a.year = :pee
    WHERE s.Stu_status=1
");
$stmt->execute([
    ':dateC' => $dateC,
    ':term' => $term,
    ':pee' => $pee
]);
$all_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card: นักเรียนทั้งหมด -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-blue-400 to-blue-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2">
                    <?php
                    // นับยอดนักเรียนทั้งหมด
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo number_format($row['total']);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนทั้งหมด</div>
                <div class="mt-2 text-sm opacity-80">รวมทุกระดับชั้น</div>
            </div>
            <!-- Card: นักเรียน ม.ต้น -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-green-400 to-green-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2">
                    <?php
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (1,2,3)");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo number_format($row['total']);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมต้น</div>
                <div class="mt-2 text-sm opacity-80">ชั้น ม.1 - ม.3</div>
            </div>
            <!-- Card: นักเรียน ม.ปลาย -->
            <div class="rounded-xl shadow-lg bg-gradient-to-br from-yellow-400 to-yellow-600 text-white flex flex-col items-center justify-center p-6 hover:scale-105 transition-transform duration-200">
                <div class="text-5xl font-bold mb-2">
                    <?php
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (4,5,6)");
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo number_format($row['total']);
                    ?>
                </div>
                <div class="text-lg font-semibold tracking-wide">นักเรียนมัธยมปลาย</div>
                <div class="mt-2 text-sm opacity-80">ชั้น ม.4 - ม.6</div>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap gap-4 items-center">
            <div class="text-yellow-700 font-semibold text-xl flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                สรุปภาพรวมการมาเรียนทั้งโรงเรียน วันที่ <?= htmlspecialchars(thaiDateShort($date)) ?>
            </div>
            <form method="get" class="flex items-center gap-2">
                <label for="date" class="text-gray-700 font-medium">เลือกวันที่:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border rounded px-2 py-1 focus:ring-2 focus:ring-yellow-400 transition">
                <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 shadow">แสดง</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <?php foreach ($status_labels as $key => $info): ?>
                <div class="rounded-xl shadow-lg <?= $info['bg'] ?> flex flex-col items-center p-6 hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-3xl"><?= $info['emoji'] ?></span>
                        <span class="font-bold <?= $info['text'] ?> text-lg"><?= $info['label'] ?></span>
                    </div>
                    <div class="text-4xl font-bold <?= $info['text'] ?>"><?= $status_count[$key] ?></div>
                    <div class="text-xs text-gray-500 mt-1">คิดเป็น <?= $total ? round($status_count[$key]*100/$total,1) : 0 ?>%</div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col md:flex-row gap-8 mb-8">
            <!-- ซ้าย: Pie Chart -->
            <div class="w-full md:w-1/2 flex justify-center items-center">
                <div class="bg-white rounded-xl shadow-lg p-4 w-full">
                    <canvas id="pieChartOverview" width="220" height="220"></canvas>
                </div>
            </div>
            <!-- ขวา: DataTable -->
            <div class="w-full md:w-1/2">
                <div class="overflow-x-auto rounded-xl shadow-lg bg-white p-4">
                    <!-- Filter dropdown -->
                    <div class="mb-4 flex flex-wrap gap-2 items-center">
                        <label for="classFilter" class="font-medium text-gray-700">กรองตามชั้น:</label>
                        <select id="classFilter" class="border rounded px-2 py-1 focus:ring-2 focus:ring-yellow-400 transition">
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
                    <table id="attendanceTable" class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-yellow-100">
                            <tr>
                                <th class="px-3 py-2 border text-center">ชั้น</th>
                                <th class="px-3 py-2 border text-center">ห้อง</th>
                                <th class="px-3 py-2 border text-center">จำนวนนักเรียน</th>
                                <?php foreach ($status_labels as $info): ?>
                                    <th class="px-2 py-2 border text-center"><?= $info['emoji'] ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $c): ?>
                                <tr class="hover:bg-yellow-50 transition-colors">
                                    <td class="px-3 py-2 border text-center">ม.<?= htmlspecialchars($c['Stu_major']) ?></td>
                                    <td class="px-3 py-2 border text-center"><?= htmlspecialchars($c['Stu_room']) ?></td>
                                    <td class="px-3 py-2 border text-center"><?= $c['count'] ?></td>
                                    <?php foreach ($status_labels as $k => $info): ?>
                                        <td class="px-2 py-2 border text-center <?= $info['text'] ?>"><?= $c['status'][$k] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
    // Pie Chart
    var ctx = document.getElementById('pieChartOverview').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
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
                    '#bbf7d0', // green
                    '#fecaca', // red
                    '#fef9c3', // yellow
                    '#bae6fd', // blue
                    '#e9d5ff', // purple
                    '#fbcfe8', // pink
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 14 } }
                }
            }
        }
    });

    // DataTable init
    var table = $('#attendanceTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
        },
        columnDefs: [
            { targets: [0], orderable: true }, // ชั้น
            { targets: [1], orderable: true }, // ห้อง
            { targets: '_all', orderable: false }
        ]
    });

    // Filter by class
    $('#classFilter').on('change', function() {
        var val = $(this).val();
        if (val) {
            table.column(0).search('^' + val + '$', true, false).draw();
        } else {
            table.column(0).search('').draw();
        }
    });
});
</script>
<?php require_once('script.php');?>
</body>
</html>
