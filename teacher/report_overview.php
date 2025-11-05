<?php
// ตรวจสอบสิทธิ์และเตรียมข้อมูล
if (!isset($user) || !isset($db)) {
    echo '<div class="text-red-500">ไม่สามารถเข้าถึงข้อมูลได้</div>';
    return;
}

require_once("../class/Attendance.php");
$attendance = new Attendance($db);

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

// Helper for Thai Months
$thai_months = [
    '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
    '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
    '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
    '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
];

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงข้อมูลห้องของครูที่ login
$teacher_class = $userData['Teach_class'] ?? null;
$teacher_room = $userData['Teach_room'] ?? null;

if (!$teacher_class || !$teacher_room) {
    echo '<div class="text-red-500">ไม่พบข้อมูลห้องเรียนของท่าน กรุณาติดต่อผู้ดูแลระบบ</div>';
    return;
}

// รับค่าจากฟอร์ม: เลือกระหว่าง "วัน", "เดือน", หรือ "เทอม"
$report_type = $_GET['report_type'] ?? 'day'; // 'day', 'month', 'term'
$report_date = $_GET['date'] ?? date('Y-m-d'); // สำหรับรายวัน
$report_month = $_GET['month'] ?? date('m'); // สำหรับรายเดือน
$report_year = $_GET['year'] ?? $pee; // ปี พ.ศ.
$report_term = $_GET['term'] ?? $term; // เทอม

// กำหนด date range ตามประเภทรายงาน
$date_start = null;
$date_end = null;
$report_title = '';

switch ($report_type) {
    case 'month':
        // รายเดือน: คำนวณวันแรกและวันสุดท้ายของเดือน
        $gregorian_year = $report_year - 543;
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $report_month, $gregorian_year);
        $date_start = sprintf('%04d-%02d-01', $gregorian_year, $report_month);
        $date_end = sprintf('%04d-%02d-%02d', $gregorian_year, $report_month, $days_in_month);
        $report_title = 'เดือน' . $thai_months[$report_month] . ' พ.ศ. ' . $report_year;
        break;
        
    case 'term':
        // รายเทอม: คำนวณช่วงวันของเทอม
        $gregorian_year = $report_year - 543;
        if ($report_term == 1) {
            // เทอม 1: พ.ค. - ต.ค.
            $date_start = sprintf('%04d-05-01', $gregorian_year);
            $date_end = sprintf('%04d-10-31', $gregorian_year);
            $report_title = 'ภาคเรียนที่ 1 ปีการศึกษา ' . $report_year;
        } else {
            // เทอม 2: พ.ย. - มี.ค. (ปีถัดไป)
            $date_start = sprintf('%04d-11-01', $gregorian_year);
            $date_end = sprintf('%04d-03-31', $gregorian_year + 1);
            $report_title = 'ภาคเรียนที่ 2 ปีการศึกษา ' . $report_year;
        }
        break;
        
    case 'day':
    default:
        // รายวัน: ใช้วันที่เดียว
        $date_start = $report_date;
        $date_end = $report_date;
        $report_title = 'วันที่ ' . thaiDateShort($report_date);
        break;
}

// ลบส่วนดึงข้อมูลทุกห้อง - ใช้เฉพาะห้องของครู
// ดึงข้อมูลนักเรียนในห้องของครูเท่านั้น
try {
    // Query ข้อมูลในช่วงวันที่ สำหรับห้องของครู
    $query = "SELECT s.*, sa.attendance_status, sa.attendance_date
              FROM student s
              LEFT JOIN student_attendance sa ON s.Stu_id = sa.student_id 
                AND sa.attendance_date >= :date_start 
                AND sa.attendance_date <= :date_end
              WHERE s.Stu_status = 1 
                AND s.Stu_major = :class 
                AND s.Stu_room = :room
              ORDER BY s.Stu_no ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':date_start' => $date_start,
        ':date_end' => $date_end,
        ':class' => $teacher_class,
        ':room' => $teacher_room
    ]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="text-red-500">เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . htmlspecialchars($e->getMessage()) . '</div>';
    return;
}

// ตรวจสอบว่ามีข้อมูลหรือไม่
if (empty($records)) {
    echo '<div class="text-center text-gray-500 py-8">ไม่พบข้อมูลนักเรียนในช่วงเวลาที่เลือก</div>';
    return;
}

// เตรียมข้อมูลสรุป
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'purple', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'pink', 'bg' => 'bg-pink-100', 'text' => 'text-pink-700'],
];
$status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
$total = 0;
$student_list = []; // เก็บรายชื่อนักเรียนแต่ละสถานะ

// ประมวลผลข้อมูล
$student_ids = [];
$attendance_map = []; // เก็บข้อมูลการเข้าเรียนแต่ละวัน
$date_list = []; // เก็บรายการวันที่ทั้งหมด

foreach ($records as $r) {
    $stu_id = $r['Stu_id'];
    $st = $r['attendance_status'] ?? null;
    $att_date = $r['attendance_date'] ?? null;
    
    // นับเฉพาะนักเรียนที่ไม่ซ้ำ
    if (!isset($student_ids[$stu_id])) {
        $student_ids[$stu_id] = [
            'name' => $r['Stu_pre'] . $r['Stu_name'] . ' ' . $r['Stu_sur'],
            'no' => $r['Stu_no'],
            'status_count' => ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0]
        ];
    }
    
    // เก็บข้อมูลการเข้าเรียนแต่ละวัน
    if ($att_date && $st) {
        if (!isset($attendance_map[$stu_id])) {
            $attendance_map[$stu_id] = [];
        }
        $attendance_map[$stu_id][$att_date] = $st;
        
        // เก็บรายการวันที่
        if (!in_array($att_date, $date_list)) {
            $date_list[] = $att_date;
        }
        
        // นับสถานะ
        $status_count[$st]++;
        $student_ids[$stu_id]['status_count'][$st]++;
        $total++;
    }
}

// เรียงวันที่
sort($date_list);

$total_students = count($student_ids);

// Helper for status symbols
$status_symbols = [
    '1' => '✅', '2' => '❌', '3' => '🕒',
    '4' => '🤒', '5' => '📝', '6' => '🎉',
];
?>

<div class="mb-4">
    <!-- ส่วนหัวรายงาน -->
    <div class="text-yellow-700 font-bold text-xl mb-4">
        📊 สรุปภาพรวมการมาเรียน ม.<?= $teacher_class ?>/<?= $teacher_room ?> <?= $report_title ?>
        <div class="text-sm text-gray-600 font-normal mt-1">
            จำนวนนักเรียน: <?= $total_students ?> คน | จำนวนครั้งที่บันทึก: <?= number_format($total) ?> ครั้ง
        </div>
    </div>
    
    <!-- ฟอร์มค้นหา -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <h3 class="text-lg font-semibold mb-3">🔍 เลือกประเภทรายงาน</h3>
        <form method="get" id="reportForm">
            <input type="hidden" name="tab" value="overview">
            
            <!-- เลือกประเภทรายงาน -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ประเภท</label>
                    <select name="report_type" id="report_type" class="w-full border rounded px-3 py-2" onchange="toggleDateInputs()">
                        <option value="day" <?= $report_type === 'day' ? 'selected' : '' ?>>รายวัน</option>
                        <option value="month" <?= $report_type === 'month' ? 'selected' : '' ?>>รายเดือน</option>
                        <option value="term" <?= $report_type === 'term' ? 'selected' : '' ?>>รายเทอม</option>
                    </select>
                </div>
                
                <!-- รายวัน -->
                <div id="day_input" style="display: <?= $report_type === 'day' ? 'block' : 'none' ?>;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">เลือกวันที่</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($report_date) ?>" class="w-full border rounded px-3 py-2">
                </div>
                
                <!-- รายเดือน -->
                <div id="month_input" style="display: <?= $report_type === 'month' ? 'block' : 'none' ?>;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">เลือกเดือน</label>
                    <select name="month" class="w-full border rounded px-3 py-2">
                        <?php foreach ($thai_months as $month_val => $month_name): ?>
                            <option value="<?= $month_val ?>" <?= $report_month == $month_val ? 'selected' : '' ?>><?= $month_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- ปี (สำหรับเดือนและเทอม) -->
                <div id="year_input" style="display: <?= $report_type !== 'day' ? 'block' : 'none' ?>;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ปี (พ.ศ.)</label>
                    <input type="number" name="year" value="<?= htmlspecialchars($report_year) ?>" class="w-full border rounded px-3 py-2" min="2560" max="2570">
                </div>
                
                <!-- เทอม -->
                <div id="term_input" style="display: <?= $report_type === 'term' ? 'block' : 'none' ?>;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">เลือกเทอม</label>
                    <select name="term" class="w-full border rounded px-3 py-2">
                        <option value="1" <?= $report_term == 1 ? 'selected' : '' ?>>เทอม 1</option>
                        <option value="2" <?= $report_term == 2 ? 'selected' : '' ?>>เทอม 2</option>
                    </select>
                </div>
                
                <div class="self-end">
                    <button type="submit" class="w-full bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 font-medium">
                        📊 แสดงรายงาน
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDateInputs() {
    const reportType = document.getElementById('report_type').value;
    document.getElementById('day_input').style.display = reportType === 'day' ? 'block' : 'none';
    document.getElementById('month_input').style.display = reportType === 'month' ? 'block' : 'none';
    document.getElementById('year_input').style.display = reportType !== 'day' ? 'block' : 'none';
    document.getElementById('term_input').style.display = reportType === 'term' ? 'block' : 'none';
}
</script>

<!-- ปุ่มพิมพ์/ส่งออก -->
<div class="bg-white p-4 rounded-lg shadow-md mb-6 no-print">
    <h3 class="text-lg font-semibold mb-4">🖨️ พิมพ์/ส่งออก</h3>
    <div class="flex flex-wrap gap-4">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            <span>📄</span> พิมพ์รายงาน
        </button>
        <button onclick="exportToExcel('classTable', 'รายงานภาพรวม_<?= $report_type ?>.xls')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            <span>📊</span> ดาวน์โหลด Excel
        </button>
    </div>
</div>

<div class="flex flex-col gap-8 mb-8">
    <!-- ตารางการมาเรียน -->
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-bold mb-4 text-gray-700">
            📋 ตารางการมาเรียน ม.<?= $teacher_class ?>/<?= $teacher_room ?> <?= $report_title ?>
        </h3>
        
        <div class="overflow-x-auto" id="table-container">
            <table class="min-w-full divide-y divide-gray-200 border text-sm" id="report-table">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sticky left-0 bg-yellow-50 z-10 border">
                            ชื่อ - สกุล
                        </th>
                        <?php foreach ($date_list as $date): ?>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border" style="min-width: 50px;">
                                <?= date('d/m', strtotime($date)) ?>
                            </th>
                        <?php endforeach; ?>
                        <?php foreach ($status_symbols as $symbol): ?>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase tracking-wider bg-gray-100 border" style="min-width: 45px;">
                                <?= $symbol ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-700 uppercase tracking-wider bg-green-100 border" style="min-width: 60px;">
                            % มาเรียน
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    // เรียงนักเรียนตามเลขที่
                    uasort($student_ids, function($a, $b) {
                        return $a['no'] - $b['no'];
                    });
                    
                    foreach ($student_ids as $stu_id => $student): 
                        // คำนวณ % การมาเรียน
                        $total_attend = array_sum($student['status_count']);
                        $attend_count = $student['status_count']['1']; // มาเรียน
                        $attend_percent = $total_attend > 0 ? round(($attend_count / $total_attend) * 100, 1) : 0;
                    ?>
                        <tr class="hover:bg-yellow-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm sticky left-0 bg-white z-10 border">
                                <span class="font-medium text-gray-700">(<?= $student['no'] ?>)</span> 
                                <?= htmlspecialchars($student['name']) ?>
                            </td>
                            <?php foreach ($date_list as $date): 
                                $status = $attendance_map[$stu_id][$date] ?? null;
                                $symbol = $status ? ($status_symbols[$status] ?? '❓') : '-';
                            ?>
                                <td class="px-2 py-2 whitespace-nowrap text-center text-base border">
                                    <?= $symbol ?>
                                </td>
                            <?php endforeach; ?>
                            <?php foreach ($status_symbols as $key => $symbol): 
                                $count = $student['status_count'][$key] ?? 0;
                            ?>
                                <td class="px-2 py-2 whitespace-nowrap text-center text-sm font-bold bg-gray-50 border">
                                    <?= $count ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="px-2 py-2 whitespace-nowrap text-center text-sm font-bold bg-green-50 border <?= $attend_percent >= 80 ? 'text-green-700' : ($attend_percent >= 60 ? 'text-yellow-700' : 'text-red-700') ?>">
                                <?= $attend_percent ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- คำอธิบายสัญลักษณ์ -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <h4 class="font-semibold mb-2 text-gray-700">คำอธิบายสัญลักษณ์:</h4>
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm">
                <?php foreach ($status_labels as $key => $info): ?>
                    <span class="text-gray-600">
                        <?= $status_symbols[$key] ?> = <?= $info['label'] ?>
                    </span>
                <?php endforeach; ?>
                <span class="text-gray-600">- = ยังไม่เช็คชื่อ</span>
            </div>
        </div>
    </div>
    
    <!-- สถิติสรุป -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- สถิติแบบ Progress Bar -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-bold mb-4 text-gray-700">📊 สถิติสรุปแบบแท่ง</h3>
            <div class="space-y-3">
                <?php foreach ($status_labels as $key => $info): ?>
                    <?php 
                    $count = $status_count[$key];
                    $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    ?>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium <?= $info['text'] ?>">
                                <?= $info['emoji'] ?> <?= $info['label'] ?>
                            </span>
                            <span class="text-sm font-bold <?= $info['text'] ?>">
                                <?= $count ?> ครั้ง (<?= $percent ?>%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                            <div class="<?= $info['bg'] ?> h-6 rounded-full flex items-center justify-center transition-all duration-300" 
                                 style="width: <?= $percent ?>%">
                                <span class="text-xs font-bold <?= $info['text'] ?>"><?= $percent ?>%</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- สรุปรวม -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">รวมทั้งหมด:</span>
                    <span class="text-lg font-bold text-gray-900"><?= number_format($total) ?> ครั้ง</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm font-semibold text-gray-700">จำนวนนักเรียน:</span>
                    <span class="text-lg font-bold text-gray-900"><?= $total_students ?> คน</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm font-semibold text-gray-700">จำนวนวัน:</span>
                    <span class="text-lg font-bold text-gray-900"><?= count($date_list) ?> วัน</span>
                </div>
            </div>
        </div>
        
        <!-- Pie Chart -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-bold mb-4 text-gray-700">🥧 กราฟแสดงสัดส่วน</h3>
            <div class="flex justify-center items-center">
                <canvas id="pieChartOverview" width="300" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Chart
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
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = <?= $total ?>;
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' ครั้ง (' + percent + '%)';
                        }
                    }
                }
            }
        }
    });
});

// Excel Export Function (removed table reference)
function exportToExcel(tableId, filename) {
    const table = document.getElementById('report-table');
    let tableHTML = table ? table.outerHTML : '';
    
    const template = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" 
              xmlns:x="urn:schemas-microsoft-com:office:excel" 
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8">
        </head>
        <body>
            <h2>ตารางการมาเรียน</h2>
            <p>ห้อง: ม.<?= $teacher_class ?>/<?= $teacher_room ?></p>
            <p><?= $report_title ?></p>
            <p>จำนวนนักเรียน: <?= $total_students ?> คน | จำนวนครั้งที่บันทึก: <?= number_format($total) ?> ครั้ง</p>
            ${tableHTML}
        </body>
        </html>`;

    const data_type = 'data:application/vnd.ms-excel';
    const encoded_template = encodeURIComponent(template);
    
    const a = document.createElement('a');
    a.href = data_type + ', ' + encoded_template;
    a.download = filename;
    a.click();
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .content-header, .sidebar, .navbar, .footer, .wrapper > aside { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    
    /* ปรับตารางสำหรับพิมพ์ */
    #table-container {
        overflow-x: visible !important;
        width: 100% !important;
    }
    .sticky {
        position: static !important;
    }
    #report-table {
        width: 100% !important;
        table-layout: auto;
        font-size: 10px;
    }
    #report-table th,
    #report-table td {
        padding: 2px 4px !important;
    }
}

/* สำหรับ sticky column */
.sticky {
    position: sticky;
    left: 0;
    z-index: 10;
}
</style>
