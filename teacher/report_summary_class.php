<?php
// ตรวจสอบสิทธิ์และเตรียมข้อมูล
if (!isset($user) || !isset($db)) {
    echo '<div class="text-red-500">ไม่สามารถเข้าถึงข้อมูลได้</div>';
    return;
}

require_once("../class/Attendance.php");
$attendance = new Attendance($db);

// รับค่าชั้นและห้องจาก $userData
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// กำหนดวันที่ (วันนี้ หรือจาก GET)
function convertToBuddhistYear($date) {
    // ตรวจสอบว่ารูปแบบเป็น YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);

        // ถ้าเป็นปี ค.ศ. ให้บวก 543
        if ($year < 2500) {
            $year += 543;
        }

        return $year . '-' . $month . '-' . $day;
    }
    // ถ้า format ไม่ถูกต้อง คืนค่าเดิม
    return $date;
}

// ฟังก์ชันแปลงวันที่เป็น วัน เดือน ปี พ.ศ. ภาษาไทย
function thaiDate($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
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

// ใช้งาน
date_default_timezone_set('Asia/Bangkok');
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateC = convertToBuddhistYear($date);

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงข้อมูลนักเรียนห้องของครู
$students_all = $attendance->getStudentsWithAttendance($dateC, $class, $room, $term, $pee);

// นับแต่ละประเภท
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'purple', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'pink', 'bg' => 'bg-pink-100', 'text' => 'text-pink-700'],
];
$status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
$status_names = ['1'=>[],'2'=>[],'3'=>[],'4'=>[],'5'=>[],'6'=>[]];

foreach ($students_all as $s) {
    $st = $s['attendance_status'] ?? null;
    if ($st && isset($status_count[$st])) {
        $status_count[$st]++;
        // เฉพาะกรณีที่ไม่ใช่ "มาเรียน" (1) เท่านั้นที่แสดงรายชื่อ
        if ($st !== '1') {
            $status_names[$st][] = $s['Stu_pre'].$s['Stu_name'].' '.$s['Stu_sur'].' ('.$s['Stu_no'].')';
        }
    }
}
$total = count($students_all);

// ฟังก์ชันแปลงวันที่เป็น วัน เดือน ปี พ.ศ. ภาษาไทย
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

<div class="mb-4 flex flex-wrap gap-4 items-center" >
    <div class="text-green-700 font-semibold">
        สรุปการมาเรียน ชั้น ม.<?= htmlspecialchars($class) ?> ห้อง <?= htmlspecialchars($room) ?> วันที่ <?= htmlspecialchars(thaiDateShort($date)) ?>
    </div>
    <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="summary-class">
        <label for="date" class="text-gray-700">เลือกวันที่:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border rounded px-2 py-1">
        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">แสดง</button>
    </form>
    <button onclick="window.print()" class="ml-auto bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 print:hidden">
        🖨️ พิมพ์รายงานนี้
    </button>
</div>

<!-- Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <?php foreach ($status_labels as $key => $info): ?>
        <div class="rounded-lg shadow <?= $info['bg'] ?> p-4 flex flex-col">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-2xl"><?= $info['emoji'] ?></span>
                <span class="font-bold <?= $info['text'] ?>"><?= $info['label'] ?></span>
            </div>
            <div class="text-3xl font-bold <?= $info['text'] ?>"><?= $status_count[$key] ?></div>
            <div class="text-xs text-gray-500 mt-1">คิดเป็น <?= $total ? round($status_count[$key]*100/$total,1) : 0 ?>%</div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pie Chart -->
<div class="mb-8 flex flex-col md:flex-row gap-8">
    <div class="w-full md:w-1/3 flex justify-center items-center">
        <canvas id="pieChart" width="180" height="180"></canvas>
    </div>
    <div class="w-full md:w-2/3">
        <?php foreach ($status_labels as $key => $info): ?>
            <div class="mb-3">
                <div class="font-semibold <?= $info['text'] ?>"><?= $info['emoji'] ?> <?= $info['label'] ?> (<?= $status_count[$key] ?>)</div>
                <?php if (!empty($status_names[$key])): ?>
                    <ul class="list-disc ml-6 text-sm text-gray-700">
                        <?php foreach ($status_names[$key] as $name): ?>
                            <li><?= htmlspecialchars($name) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-gray-400 text-xs ml-6">- ไม่มี -</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('pieChart').getContext('2d');
    var chart = new Chart(ctx, {
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
                    labels: { font: { size: 16 } }
                }
            }
        }
    });
});
</script>
<style>
@media print {
    .print\:hidden { display: none !important; }
    body { background: #fff !important; }
    .content-header, .sidebar, .navbar, .footer, .wrapper > aside { display: none !important; }
    .content-wrapper, .container, .container-fluid { box-shadow: none !important; }
}
</style>
