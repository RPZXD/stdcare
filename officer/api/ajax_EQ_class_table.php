<?php
include_once("../../config/Database.php");
include_once("../../class/EQ.php");
require_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$EQ = new EQ($db);
$user = new UserLogin($db);

$class = $_GET['class'] ?? '';
$term = $user->getTerm();
$pee = $user->getPee();

$roomList = [];
if ($class) {
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->bindParam(':class', $class);
    $stmt->execute();
    $roomList = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// เตรียมข้อมูลสรุปแต่ละห้อง
$roomSummary = [];
foreach ($roomList as $room) {
    $roomSummary[$room] = $EQ->getEQClassRoomSummary($class, $room, $pee, $term);
}
// สรุปรวมทั้งชั้น
$classSummary = $EQ->getEQClassRoomSummary($class, '', $pee, $term);

// โครงสร้างการแปลผล (เหมือน form_eq_result.php)
$eqStructure = [
    'ดี' => [
        ['label' => '1.1 ควบคุมตนเอง', 'range' => [13, 17], 'items' => range(1, 6)],
        ['label' => '1.2 เห็นใจผู้อื่น', 'range' => [16, 20], 'items' => range(7, 12)],
        ['label' => '1.3 รับผิดชอบ', 'range' => [16, 22], 'items' => range(13, 18)],
        ['label' => 'รวมองค์ประกอบดี', 'range' => [48, 58], 'items' => range(1, 18)],
    ],
    'เก่ง' => [
        ['label' => '2.1 มีแรงจูงใจ', 'range' => [14, 20], 'items' => range(19, 24)],
        ['label' => '2.2 ตัดสินใจแก้ปัญหา', 'range' => [13, 19], 'items' => range(25, 30)],
        ['label' => '2.3 สัมพันธภาพ', 'range' => [14, 20], 'items' => range(31, 36)],
        ['label' => 'รวมองค์ประกอบเก่ง', 'range' => [45, 57], 'items' => range(19, 36)],
    ],
    'สุข' => [
        ['label' => '3.1 ภูมิใจในตนเอง', 'range' => [9, 13], 'items' => range(37, 40)],
        ['label' => '3.2 พอใจชีวิต', 'range' => [16, 22], 'items' => range(41, 46)],
        ['label' => '3.3 สุขสงบทางใจ', 'range' => [15, 21], 'items' => range(47, 52)],
        ['label' => 'รวมองค์ประกอบสุข', 'range' => [40, 45], 'items' => range(37, 52)],
    ],
];

// ฟังก์ชันแปลผล
function eqResult($score, $range) {
    if ($score > $range[1]) return 'สูงกว่าปกติ';
    if ($score >= $range[0]) return 'เกณฑ์ปกติ';
    return 'ต่ำกว่าปกติ';
}
function eqColor($result) {
    return match($result) {
        'สูงกว่าปกติ' => 'bg-green-500',
        'เกณฑ์ปกติ' => 'bg-yellow-500',
        default => 'bg-red-500'
    };
}
function eqLevel($score) {
    return match(true) {
        $score >= 170 => 'EQ ดีมาก',
        $score >= 140 => 'EQ ดี',
        $score >= 100 => 'EQ ปานกลาง',
        default => 'EQ ต้องปรับปรุง'
    };
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <?php
    $cntAll = $classSummary['total'];
    $cntVeryGood = $classSummary['verygood'];
    $cntGood = $classSummary['good'];
    $cntMid = $classSummary['mid'];
    $cntLow = $classSummary['low'];
    ?>
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนในชั้นนี้</div>
        <div class="text-5xl font-extrabold text-blue-700 mb-2 animate-bounce"><?= $cntAll ?></div>
        <div class="flex flex-col gap-1 text-center text-lg">
            <div class="text-green-700">🌟 ดีมาก <span class="font-bold"><?= $cntVeryGood ?></span> คน</div>
            <div class="text-yellow-700">👍 ดี <span class="font-bold"><?= $cntGood ?></span> คน</div>
            <div class="text-blue-700">🙂 ปานกลาง <span class="font-bold"><?= $cntMid ?></span> คน</div>
            <div class="text-red-700">⚠️ ต้องปรับปรุง <span class="font-bold"><?= $cntLow ?></span> คน</div>
        </div>
        <div class="w-full flex flex-col gap-2 mt-4">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-green-400 flex items-center justify-center text-white text-lg">🌟</span>
                <div class="flex-1 bg-green-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-700" style="width: <?= $cntAll ? round($cntVeryGood/$cntAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-green-700"><?= $cntAll ? round($cntVeryGood/$cntAll*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-yellow-400 flex items-center justify-center text-white text-lg">👍</span>
                <div class="flex-1 bg-yellow-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-yellow-400 h-4 rounded-full transition-all duration-700" style="width: <?= $cntAll ? round($cntGood/$cntAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-yellow-700"><?= $cntAll ? round($cntGood/$cntAll*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-400 flex items-center justify-center text-white text-lg">🙂</span>
                <div class="flex-1 bg-blue-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-blue-500 h-4 rounded-full transition-all duration-700" style="width: <?= $cntAll ? round($cntMid/$cntAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-blue-700"><?= $cntAll ? round($cntMid/$cntAll*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-400 flex items-center justify-center text-white text-lg">⚠️</span>
                <div class="flex-1 bg-red-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-red-500 h-4 rounded-full transition-all duration-700" style="width: <?= $cntAll ? round($cntLow/$cntAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-red-700"><?= $cntAll ? round($cntLow/$cntAll*100) : 0 ?>%</span>
            </div>
        </div>
    </div>
</div>
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm mb-2 animate-fade-in">
    <thead>
        <tr class="bg-blue-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">ห้อง</th>
            <th class="py-2 px-3 border-b text-center">จำนวนนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">ส่งแบบประเมิน(คน)</th>
            <th class="py-2 px-3 border-b text-center text-green-700">ดีมาก</th>
            <th class="py-2 px-3 border-b text-center text-yellow-700">ดี</th>
            <th class="py-2 px-3 border-b text-center text-blue-700">ปานกลาง</th>
            <th class="py-2 px-3 border-b text-center text-red-700">ต้องปรับปรุง</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roomList as $r): $sum = $roomSummary[$r]; ?>
        <tr>
            <td class="px-3 py-2 text-center font-bold">ห้อง <?= htmlspecialchars($r) ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['total'] ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['have'] ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $sum['verygood'] ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $sum['good'] ?></td>
            <td class="px-3 py-2 text-center text-blue-700"><?= $sum['mid'] ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $sum['low'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-blue-100 font-bold">
            <td class="px-3 py-2 text-center">รวม</td>
            <td class="px-3 py-2 text-center"><?= $classSummary['total'] ?></td>
            <td class="px-3 py-2 text-center"><?= $classSummary['have'] ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $classSummary['verygood'] ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $classSummary['good'] ?></td>
            <td class="px-3 py-2 text-center text-blue-700"><?= $classSummary['mid'] ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $classSummary['low'] ?></td>
        </tr>
    </tbody>
</table>
<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.7s; }
</style>
