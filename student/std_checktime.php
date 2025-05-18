<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

// Fetch terms and pee เฉพาะของนักเรียนคนนี้
$student_id = $_SESSION['Student_login'];
$term = $user->getTerm();
$pee = $user->getPee();

$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลการเช็คชื่อของนักเรียนรายบุคคล
$attendanceRows = [];
try {
    $stmt = $studentConn->prepare("SELECT * FROM student_attendance WHERE student_id = :stu_id AND term = :term AND year = :year ORDER BY attendance_date DESC");
    $stmt->execute([':stu_id' => $student_id, ':term' => $term, ':year' => $pee]);
    $attendanceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $attendanceRows = [];
}

// ฟังก์ชันแปลงสถานะ
function attendance_status_text($status) {
    switch ($status) {
        case '1': return ['text' => 'มาเรียน', 'color' => 'text-green-600', 'emoji' => '✅', 'icon' => '🟢'];
        case '2': return ['text' => 'ขาด', 'color' => 'text-red-600', 'emoji' => '❌', 'icon' => '🔴'];
        case '3': return ['text' => 'สาย', 'color' => 'text-yellow-600', 'emoji' => '🕒', 'icon' => '🟡'];
        case '4': return ['text' => 'ป่วย', 'color' => 'text-blue-600', 'emoji' => '🤒', 'icon' => '🔵'];
        case '5': return ['text' => 'กิจ', 'color' => 'text-purple-600', 'emoji' => '📝', 'icon' => '🟣'];
        case '6': return ['text' => 'กิจกรรม', 'color' => 'text-pink-600', 'emoji' => '🎉', 'icon' => '🟣'];
        default:  return ['text' => 'ไม่ระบุ', 'color' => 'text-gray-500', 'emoji' => '', 'icon' => '⚪'];
    }
}

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function thai_date($strDate) {
    $strYear = date("Y", strtotime($strDate)) ;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $thaiMonths = [
        "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
    ];
    $strMonthThai = $thaiMonths[$strMonth];
    return "$strDay $strMonthThai $strYear";
}
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">เวลาเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto px-4 py-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-center mb-6 flex items-center justify-center gap-2">
                        ⏰ ตารางเวลาการมาเรียนของฉัน ภาคเรียนที่ <?= htmlspecialchars($term) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?>
                    </h2>
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="flex space-x-4" id="attendanceTabs">
                            <button class="tab-btn px-4 py-2 text-blue-700 border-b-2 border-blue-700 font-semibold focus:outline-none" data-tab="tab1">การมาเรียน</button>
                            <button class="tab-btn px-4 py-2 text-gray-600 hover:text-blue-700 border-b-2 border-transparent font-semibold focus:outline-none" data-tab="tab2">สรุปรายเดือน</button>
                            <button class="tab-btn px-4 py-2 text-gray-600 hover:text-blue-700 border-b-2 border-transparent font-semibold focus:outline-none" data-tab="tab3">สรุปรายภาคเรียน</button>
                        </nav>
                    </div>
                    <!-- Tab Contents -->
                    <div id="tab1" class="tab-content">
                        <div class="overflow-x-auto">
                            <table id="attendanceTable" class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                                <thead>
                                    <tr class="bg-purple-100 text-gray-700">
                                        <th class="py-2 px-3 border-b text-center">ลำดับ</th>
                                        <th class="py-2 px-3 border-b text-center">วันที่เช็คชื่อ</th>
                                        <th class="py-2 px-3 border-b text-center">เวลาเข้าเรียน</th>
                                        <th class="py-2 px-3 border-b text-center">เวลาออก</th>
                                        <th class="py-2 px-3 border-b text-center">สถานะ</th>
                                        <th class="py-2 px-3 border-b text-center">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($attendanceRows) > 0): ?>
                                        <?php foreach ($attendanceRows as $i => $row): 
                                            $status = attendance_status_text($row['attendance_status']);
                                            $rowBg = $i % 2 === 0 ? 'bg-white' : 'bg-blue-50';
                                        ?>
                                            <tr class="<?= $rowBg ?> hover:bg-blue-100 transition-colors duration-150 text-[15px]">
                                                <td class="px-5 py-2"><?= $i+1 ?></td>
                                                <td class="px-5 py-2"><?= thai_date($row['attendance_date']) ?></td>
                                                <td class="px-5 py-2">
                                                    <?= $row['attendance_time'] ? '<span class="inline-flex items-center gap-1">' . htmlspecialchars($row['attendance_time']) . ' <span>' . $status['icon'] . '</span></span>' : '-' ?>
                                                </td>
                                                <td class="px-5 py-2">
                                                    <?= $row['leave_time'] ? htmlspecialchars($row['leave_time']) . " 🏁" : '-' ?>
                                                </td>
                                                <td class="px-5 py-2">
                                                    <span class="inline-flex items-center gap-1 <?= $status['color'] ?> font-bold">
                                                        <?= $status['emoji'] ?> <?= $status['text'] ?>
                                                    </span>
                                                </td>
                                                <td class="px-5 py-2"><?= htmlspecialchars($row['reason'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-400 py-6 bg-white rounded-b-xl">ไม่พบข้อมูลการมาเรียน</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab2" class="tab-content hidden">
                        <!-- สรุปรายเดือน: กราฟ + Card -->
                        <?php
                        // เตรียมข้อมูลสรุปรายเดือนใหม่ (นับแต่ละ status)
                        $currentMonth = date('Y-m');
                        $monthStats = [
                            '1'=>0, // มาเรียน
                            '2'=>0, // ขาด
                            '3'=>0, // สาย
                            '4'=>0, // ป่วย
                            '5'=>0, // กิจ
                            '6'=>0, // กิจกรรม
                        ];
                        $monthRows = [];
                        foreach ($attendanceRows as $row) {
                            if (strpos($row['attendance_date'], $currentMonth) === 0) {
                                $monthStats[$row['attendance_status']]++;
                                $monthRows[] = $row;
                            }
                        }
                        ?>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                            <div class="bg-green-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟢</span>
                                <span class="font-bold text-green-700 text-xl">มาเรียน</span>
                                <span id="month-present" class="text-2xl font-bold"><?= $monthStats['1'] ?></span>
                            </div>
                            <div class="bg-red-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🔴</span>
                                <span class="font-bold text-red-700 text-xl">ขาด</span>
                                <span id="month-absent" class="text-2xl font-bold"><?= $monthStats['2'] ?></span>
                            </div>
                            <div class="bg-yellow-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟡</span>
                                <span class="font-bold text-yellow-700 text-xl">สาย</span>
                                <span id="month-late" class="text-2xl font-bold"><?= $monthStats['3'] ?></span>
                            </div>
                            <div class="bg-blue-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🔵</span>
                                <span class="font-bold text-blue-700 text-xl">ป่วย</span>
                                <span id="month-sick" class="text-2xl font-bold"><?= $monthStats['4'] ?></span>
                            </div>
                            <div class="bg-purple-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟣</span>
                                <span class="font-bold text-purple-700 text-xl">กิจ</span>
                                <span id="month-activity" class="text-2xl font-bold"><?= $monthStats['5'] ?></span>
                            </div>
                            <div class="bg-pink-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟣</span>
                                <span class="font-bold text-pink-700 text-xl">กิจกรรม</span>
                                <span id="month-event" class="text-2xl font-bold"><?= $monthStats['6'] ?></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <canvas id="monthChart" height="100"></canvas>
                        </div>
                        <!-- ตารางสรุปรายเดือน -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm">
                                <thead>
                                    <tr class="bg-purple-50 text-gray-700">
                                        <th class="py-2 px-3 border-b text-center">วันที่</th>
                                        <th class="py-2 px-3 border-b text-center">สถานะ</th>
                                        <th class="py-2 px-3 border-b text-center">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($monthRows) > 0): ?>
                                        <?php foreach ($monthRows as $row):
                                            $status = attendance_status_text($row['attendance_status']);
                                        ?>
                                        <tr>
                                            <td class="px-3 py-2 text-center"><?= thai_date($row['attendance_date']) ?></td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="<?= $status['color'] ?> font-bold"><?= $status['emoji'] ?> <?= $status['text'] ?></span>
                                            </td>
                                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($row['reason'] ?? '-') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-gray-400 py-4">ไม่มีข้อมูลในเดือนนี้</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab3" class="tab-content hidden">
                        <!-- สรุปรายภาคเรียนที่: กราฟ + Card -->
                        <?php
                        // เตรียมข้อมูลสรุปรายภาคเรียนใหม่ (นับแต่ละ status)
                        $termStats = [
                            '1'=>0, '2'=>0, '3'=>0, '4'=>0, '5'=>0, '6'=>0
                        ];
                        foreach ($attendanceRows as $row) {
                            $termStats[$row['attendance_status']]++;
                        }
                        ?>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                            <div class="bg-green-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟢</span>
                                <span class="font-bold text-green-700 text-xl">มาเรียน</span>
                                <span id="term-present" class="text-2xl font-bold"><?= $termStats['1'] ?></span>
                            </div>
                            <div class="bg-red-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🔴</span>
                                <span class="font-bold text-red-700 text-xl">ขาด</span>
                                <span id="term-absent" class="text-2xl font-bold"><?= $termStats['2'] ?></span>
                            </div>
                            <div class="bg-yellow-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟡</span>
                                <span class="font-bold text-yellow-700 text-xl">สาย</span>
                                <span id="term-late" class="text-2xl font-bold"><?= $termStats['3'] ?></span>
                            </div>
                            <div class="bg-blue-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🔵</span>
                                <span class="font-bold text-blue-700 text-xl">ป่วย</span>
                                <span id="term-sick" class="text-2xl font-bold"><?= $termStats['4'] ?></span>
                            </div>
                            <div class="bg-purple-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟣</span>
                                <span class="font-bold text-purple-700 text-xl">กิจ</span>
                                <span id="term-activity" class="text-2xl font-bold"><?= $termStats['5'] ?></span>
                            </div>
                            <div class="bg-pink-100 rounded-lg p-4 flex flex-col items-center">
                                <span class="text-3xl">🟣</span>
                                <span class="font-bold text-pink-700 text-xl">กิจกรรม</span>
                                <span id="term-event" class="text-2xl font-bold"><?= $termStats['6'] ?></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <canvas id="termChart" height="100"></canvas>
                        </div>
                        <!-- ตารางสรุปรายภาคเรียนที่ -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm">
                                <thead>
                                    <tr class="bg-purple-50 text-gray-700">
                                        <th class="py-2 px-3 border-b text-center">วันที่</th>
                                        <th class="py-2 px-3 border-b text-center">สถานะ</th>
                                        <th class="py-2 px-3 border-b text-center">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($attendanceRows) > 0): 
                                        foreach ($attendanceRows as $row):
                                            $status = attendance_status_text($row['attendance_status']);
                                    ?>
                                    <tr>
                                        <td class="px-3 py-2 text-center"><?= thai_date($row['attendance_date']) ?></td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="<?= $status['color'] ?> font-bold"><?= $status['emoji'] ?> <?= $status['text'] ?></span>
                                        </td>
                                        <td class="px-3 py-2 text-center"><?= htmlspecialchars($row['reason'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-400 py-4">ไม่มีข้อมูลในภาคเรียนที่นี้</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => {
                b.classList.remove('text-blue-700', 'border-blue-700');
                b.classList.add('text-gray-600', 'border-transparent');
            });
            tabContents.forEach(tc => tc.classList.add('hidden'));
            this.classList.add('text-blue-700', 'border-blue-700');
            this.classList.remove('text-gray-600', 'border-transparent');
            document.getElementById(this.dataset.tab).classList.remove('hidden');
        });
    });

    // DataTable
    if (window.DataTable) {
        new DataTable('#attendanceTable', {
            destroy: true,
            perPage: 10,
            labels: {
                placeholder: "ค้นหา...",
                perPage: "{select} รายการ/หน้า",
                noRows: "ไม่พบข้อมูล",
                info: "แสดง {start} - {end} จาก {rows} รายการ"
            }
        });
    }

    // Prepare data for charts
    <?php
    // เตรียมข้อมูลสรุปรายเดือน
    $monthStats = [
        'present'=>0, 'absent'=>0, 'late'=>0, 'sick'=>0, 'activity'=>0, 'event'=>0
    ];
    $termStats = [
        'present'=>0, 'absent'=>0, 'late'=>0, 'sick'=>0, 'activity'=>0, 'event'=>0
    ];
    $currentMonth = date('Y-m');
    foreach ($attendanceRows as $row) {
        $status = $row['attendance_status'];
        $date = $row['attendance_date'];
        // รายเดือน
        if (strpos($date, $currentMonth) === 0) {
            if ($status == '1') $monthStats['present']++;
            elseif ($status == '2') $monthStats['absent']++;
            elseif ($status == '3') $monthStats['late']++;
            elseif ($status == '4') $monthStats['sick']++;
            elseif ($status == '5') $monthStats['activity']++;
            elseif ($status == '6') $monthStats['event']++;
        }
        // รายภาคเรียนที่
        if ($status == '1') $termStats['present']++;
        elseif ($status == '2') $termStats['absent']++;
        elseif ($status == '3') $termStats['late']++;
        elseif ($status == '4') $termStats['sick']++;
        elseif ($status == '5') $termStats['activity']++;
        elseif ($status == '6') $termStats['event']++;
    }
    ?>
    // Update card values
    document.getElementById('month-present').textContent = <?= $monthStats['present'] ?>;
    document.getElementById('month-absent').textContent = <?= $monthStats['absent'] ?>;
    document.getElementById('month-late').textContent = <?= $monthStats['late'] ?>;
    document.getElementById('month-sick').textContent = <?= $monthStats['sick'] ?>;
    document.getElementById('month-activity').textContent = <?= $monthStats['activity'] ?>;
    document.getElementById('month-event').textContent = <?= $monthStats['event'] ?>;
    document.getElementById('term-present').textContent = <?= $termStats['present'] ?>;
    document.getElementById('term-absent').textContent = <?= $termStats['absent'] ?>;
    document.getElementById('term-late').textContent = <?= $termStats['late'] ?>;
    document.getElementById('term-sick').textContent = <?= $termStats['sick'] ?>;
    document.getElementById('term-activity').textContent = <?= $termStats['activity'] ?>;
    document.getElementById('term-event').textContent = <?= $termStats['event'] ?>;

    // Chart.js - Monthly
    new Chart(document.getElementById('monthChart'), {
        type: 'doughnut',
        data: {
            labels: ['มาเรียน', 'ขาด', 'สาย', 'ป่วย', 'กิจ', 'กิจกรรม'],
            datasets: [{
                data: [
                    <?= $monthStats['present'] ?>,
                    <?= $monthStats['absent'] ?>,
                    <?= $monthStats['late'] ?>,
                    <?= $monthStats['sick'] ?>,
                    <?= $monthStats['activity'] ?>,
                    <?= $monthStats['event'] ?>
                ],
                backgroundColor: [
                    '#22c55e', // green
                    '#ef4444', // red
                    '#eab308', // yellow
                    '#3b82f6', // blue
                    '#a21caf', // purple
                    '#ec4899'  // pink
                ],
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    // Chart.js - Term
    new Chart(document.getElementById('termChart'), {
        type: 'doughnut',
        data: {
            labels: ['มาเรียน', 'ขาด', 'สาย', 'ป่วย', 'กิจ', 'กิจกรรม'],
            datasets: [{
                data: [
                    <?= $termStats['present'] ?>,
                    <?= $termStats['absent'] ?>,
                    <?= $termStats['late'] ?>,
                    <?= $termStats['sick'] ?>,
                    <?= $termStats['activity'] ?>,
                    <?= $termStats['event'] ?>
                ],
                backgroundColor: [
                    '#22c55e', // green
                    '#ef4444', // red
                    '#eab308', // yellow
                    '#3b82f6', // blue
                    '#a21caf', // purple
                    '#ec4899'  // pink
                ],
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
});
</script>
</body>
</html>
