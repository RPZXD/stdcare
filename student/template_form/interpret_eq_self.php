<?php
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500">ไม่ได้รับอนุญาต</div>';
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/EQ.php');

$student_id = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (!$student_id || !$pee || !$term) {
    echo '<div class="text-red-500">ไม่พบข้อมูลที่ต้องการ</div>';
    exit;
}

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$eq = new EQ($conn);

$eqData = $eq->getEQData($student_id, $pee, $term);

if (!$eqData) {
    echo '<div class="text-red-500">ยังไม่มีการบันทึกข้อมูล EQ สำหรับปีการศึกษาและภาคเรียนนี้</div>';
    exit;
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

// คะแนนรวม
$totalEQ = 0;
for ($i = 1; $i <= 52; $i++) {
    $totalEQ += isset($eqData["EQ$i"]) ? (int)$eqData["EQ$i"] : 0;
}

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
$mainScores = [];
foreach ($eqStructure as $main => $subs) {
    $sum = 0;
    foreach ($subs as $sub) {
        foreach ($sub['items'] as $q) {
            $sum += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
        }
    }
    $mainScores[$main] = $sum;
}
?>
<div class="space-y-4">
    <div class="bg-white p-4 rounded-lg border shadow">
        <p class="text-center text-gray-700 font-bold">คะแนนรวม EQ</p>
        <p class="text-center text-4xl font-bold text-blue-600"><?= $totalEQ ?></p>
        <p class="text-center text-2xl font-bold <?= match(eqLevel($totalEQ)) {
            'EQ ดีมาก' => 'text-green-600',
            'EQ ดี' => 'text-emerald-500',
            'EQ ปานกลาง' => 'text-yellow-500',
            default => 'text-red-500'
        } ?>">
            <?= eqLevel($totalEQ) ?>
        </p>
    </div>

    <div class="bg-white border rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold mb-2">📋 สรุปผล EQ รายด้าน</h3>
        <?php foreach ($eqStructure as $main => $subs): ?>
            <div class="mb-4">
                <h4 class="font-bold text-indigo-700 mb-1"><?= $main ?></h4>
                <table class="min-w-full text-sm border border-gray-300 mb-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">ด้านย่อย</th>
                            <th class="border px-2 py-1">ช่วงคะแนน</th>
                            <th class="border px-2 py-1">คะแนน</th>
                            <th class="border px-2 py-1">ผล</th>
                            <th class="border px-2 py-1">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subs as $sub):
                            $score = 0;
                            foreach ($sub['items'] as $q) {
                                $score += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
                            }
                            $result = eqResult($score, $sub['range']);
                            $color = eqColor($result);
                            $maxScore = count($sub['items']) * 3;
                            $percent = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
                        ?>
                        <tr>
                            <td class="border px-2 py-1"><?= $sub['label'] ?></td>
                            <td class="border text-center"><?= $sub['range'][0] ?> - <?= $sub['range'][1] ?></td>
                            <td class="border text-center"><?= $score ?></td>
                            <td class="border text-center">
                                <span class="inline-block px-2 py-1 rounded text-white <?= $color ?>"><?= $result ?></span>
                            </td>
                            <td class="border px-2 py-1">
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="<?= $color ?> h-4 rounded-full" style="width: <?= $percent ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach ?>
    </div>
    <div class="mt-4 text-gray-500 text-sm">
        <strong>หมายเหตุ:</strong> การแปลผลนี้เป็นการประเมินเบื้องต้นเพื่อการพัฒนาตนเอง
    </div>
</div>
