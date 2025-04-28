<?php
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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateC = convertToBuddhistYear($date);

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงชั้น/ห้องทั้งหมด
$stmt = $db->prepare("SELECT DISTINCT Stu_major, Stu_room FROM student WHERE Stu_status=1 ORDER BY Stu_major, Stu_room");
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// รวมข้อมูลทุกห้อง
foreach ($classes as $c) {
    $students = $attendance->getStudentsWithAttendance($dateC, $c['Stu_major'], $c['Stu_room'], $term, $pee);
    foreach ($students as $s) {
        $st = $s['attendance_status'] ?? null;
        if ($st && isset($status_count[$st])) {
            $status_count[$st]++;
        }
        $total++;
    }
}
?>

<div class="mb-4 flex flex-wrap gap-4 items-center">
    <div class="text-yellow-700 font-semibold">
        สรุปภาพรวมการมาเรียนทั้งโรงเรียน วันที่ <?= htmlspecialchars(thaiDateShort($date)) ?>
    </div>
    <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="overview">
        <label for="date" class="text-gray-700">เลือกวันที่:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border rounded px-2 py-1">
        <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">แสดง</button>
    </form>
</div>

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

<div class="flex flex-col md:flex-row gap-8 mb-8">
    <div class="w-full md:w-1/2 flex justify-center items-center">
        <canvas id="pieChartOverview" width="220" height="220"></canvas>
    </div>
    <div class="w-full md:w-1/2">
        <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
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
                    <?php
                    $students = $attendance->getStudentsWithAttendance($dateC, $c['Stu_major'], $c['Stu_room'], $term, $pee);
                    $count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
                    foreach ($students as $s) {
                        $st = $s['attendance_status'] ?? null;
                        if ($st && isset($count[$st])) $count[$st]++;
                    }
                    ?>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="px-3 py-2 border text-center">ม.<?= htmlspecialchars($c['Stu_major']) ?></td>
                        <td class="px-3 py-2 border text-center"><?= htmlspecialchars($c['Stu_room']) ?></td>
                        <td class="px-3 py-2 border text-center"><?= count($students) ?></td>
                        <?php foreach ($status_labels as $k => $info): ?>
                            <td class="px-2 py-2 border text-center <?= $info['text'] ?>"><?= $count[$k] ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
