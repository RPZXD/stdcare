<?php
include_once("../../config/Database.php");
include_once("../../class/EQ.php");
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$EQ = new EQ($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
include_once("../../class/UserLogin.php");
$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room && $pee && $term) {
    $students = $EQ->getEQByClassAndRoom($class, $room, $pee, $term);
}

// ตัวอย่าง: สมมติว่าคะแนนรวม EQ คือผลรวม 52 ข้อ
function calc_eq_score($answers) {
    $sum = 0;
    for ($i = 1; $i <= 52; $i++) {
        $sum += (int)($answers["EQ$i"] ?? 0);
    }
    return $sum;
}

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

// สรุปจำนวนแต่ละกลุ่ม
$cntAll = count($students);
$cntVeryGood = $cntGood = $cntMid = $cntLow = 0;
foreach ($students as $stu) {
    $eqData = $EQ->getEQData($stu['Stu_id'], $pee, $term);
    $score = $eqData ? calc_eq_score($eqData) : 0;
    if ($eqData) {
        if ($score >= 170) $cntVeryGood++;
        elseif ($score >= 140) $cntGood++;
        elseif ($score >= 100) $cntMid++;
        else $cntLow++;
    }
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนในห้องนี้</div>
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
<div class="overflow-x-auto">
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm animate-fade-in">
    <thead>
        <tr class="bg-blue-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">🆔 เลขประจำตัว</th>
            <th class="py-2 px-3 border-b text-center">👨‍🎓 ชื่อนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">🔢 เลขที่</th>
            <th class="py-2 px-3 border-b text-center">คะแนนรวม</th>
            <th class="py-2 px-3 border-b text-center">ระดับ</th>
            <th class="py-2 px-3 border-b text-center">ดี</th>
            <th class="py-2 px-3 border-b text-center">เก่ง</th>
            <th class="py-2 px-3 border-b text-center">สุข</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $stu):
                $eqData = $EQ->getEQData($stu['Stu_id'], $pee, $term);
                $score = $eqData ? calc_eq_score($eqData) : '-';
                $level = $eqData ? eqLevel($score) : '-';
                // คำนวณคะแนนหลัก ดี เก่ง สุข
                $mainScores = [];
                if ($eqData) {
                    foreach ($eqStructure as $main => $subs) {
                        $sum = 0;
                        foreach ($subs as $sub) {
                            foreach ($sub['items'] as $q) {
                                $sum += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
                            }
                        }
                        $mainScores[$main] = $sum;
                    }
                }
            ?>
            <tr class="hover:bg-blue-50 transition-colors duration-150">
                <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                <td class="px-3 py-2"><?= htmlspecialchars($stu['full_name']) ?></td>
                <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                <td class="px-3 py-2 text-center"><?= $score ?></td>
                <td class="px-3 py-2 text-center font-bold <?= $eqData ? (str_contains($level, 'ดีมาก') ? 'text-green-600' : (str_contains($level, 'ดี') ? 'text-yellow-600' : (str_contains($level, 'ปานกลาง') ? 'text-blue-600' : 'text-red-600'))) : 'text-gray-400' ?>">
                    <?= $level ?>
                </td>
                <td class="px-3 py-2 text-center">
                    <?php if ($eqData): ?>
                        <span class="inline-block px-2 py-1 rounded text-white font-bold <?= eqColor(eqResult($mainScores['ดี'], $eqStructure['ดี'][3]['range'])) ?>">
                            <?= $mainScores['ดี'] ?> (<?= eqResult($mainScores['ดี'], $eqStructure['ดี'][3]['range']) ?>)
                        </span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>
                <td class="px-3 py-2 text-center">
                    <?php if ($eqData): ?>
                        <span class="inline-block px-2 py-1 rounded text-white font-bold <?= eqColor(eqResult($mainScores['เก่ง'], $eqStructure['เก่ง'][3]['range'])) ?>">
                            <?= $mainScores['เก่ง'] ?> (<?= eqResult($mainScores['เก่ง'], $eqStructure['เก่ง'][3]['range']) ?>)
                        </span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>
                <td class="px-3 py-2 text-center">
                    <?php if ($eqData): ?>
                        <span class="inline-block px-2 py-1 rounded text-white font-bold <?= eqColor(eqResult($mainScores['สุข'], $eqStructure['สุข'][3]['range'])) ?>">
                            <?= $mainScores['สุข'] ?> (<?= eqResult($mainScores['สุข'], $eqStructure['สุข'][3]['range']) ?>)
                        </span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center text-gray-400 py-6">ไม่พบข้อมูล EQ สำหรับห้องนี้</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.7s; }
</style>
