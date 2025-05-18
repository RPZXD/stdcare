<?php
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = ''; $term = '';
// ดึงปี/เทอมจากระบบ (หรือ session/global)
include_once("../../class/UserLogin.php");
$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room && $pee && $term) {
    $students = $sdq->getSDQByClassAndRoom($class, $room, $pee, $term);
}

// ฟังก์ชันคำนวณคะแนนแต่ละด้าน
function calc_sdq_scores($answers) {
    $map = [
        'อารมณ์ 😖' => [3, 8, 13, 16, 24],
        'เกเร 😠' => [5, 12, 18, 22],
        'สมาธิ/ไฮเปอร์ ⚡' => [2, 10, 15, 21],
        'เพื่อน 🧍‍♂️🧍‍♀️' => [6, 11, 14, 19, 23],
        'จุดแข็ง 🤝' => [1, 4, 7, 9, 17, 20, 25],
    ];
    $scores = [];
    foreach ($map as $key => $qs) {
        $sum = 0;
        foreach ($qs as $q) {
            $sum += (int)($answers["q$q"] ?? 0);
        }
        $scores[$key] = $sum;
    }
    return $scores;
}

// ฟังก์ชันตีความคะแนนแต่ละด้าน
function scoreLevel($score, $category) {
    // กำหนดเกณฑ์จาก SDQ จริง
    $cutoffs = [
        'อารมณ์ 😖' => [4, 6],
        'เกเร 😠' => [3, 5],
        'สมาธิ/ไฮเปอร์ ⚡' => [5, 7],
        'เพื่อน 🧍‍♂️🧍‍♀️' => [3, 6],
        'จุดแข็ง 🤝' => [5, 6], // สูงดี
    ];
    [$normal, $borderline] = $cutoffs[$category] ?? [0, 0];
    if ($category == 'จุดแข็ง 🤝') {
        return $score >= $borderline ? 'ปกติ/มีจุดแข็ง' : ($score >= $normal ? 'ภาวะเสี่ยง' : 'มีปัญหา');
    }
    return $score <= $normal ? 'ปกติ' : ($score <= $borderline ? 'ภาวะเสี่ยง' : 'มีปัญหา');
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Card: ตัวเลขรวม -->
    <?php
    $countAll = count($students);
    $countNormal = $countRisk = $countProblem = 0;
    foreach ($students as $stu) {
        $sdqData = $sdq->getSDQSelfData($stu['Stu_id'], $pee, $term);
        $scores = calc_sdq_scores($sdqData['answers'] ?? []);
        $totalProblemScore =
            ($scores['อารมณ์ 😖'] ?? 0) +
            ($scores['เกเร 😠'] ?? 0) +
            ($scores['สมาธิ/ไฮเปอร์ ⚡'] ?? 0) +
            ($scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? 0);
        if ($totalProblemScore >= 20) $countProblem++;
        elseif ($totalProblemScore >= 14) $countRisk++;
        else $countNormal++;
    }
    ?>
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนในห้องนี้</div>
        <div class="text-5xl font-extrabold text-blue-700 mb-2 animate-bounce"><?= $countAll ?></div>
        <div class="flex flex-col gap-1 text-center text-lg">
            <div class="text-green-700">🟢 ปกติ <span class="font-bold"><?= $countNormal ?></span> คน</div>
            <div class="text-yellow-700">🟡 เสี่ยง <span class="font-bold"><?= $countRisk ?></span> คน</div>
            <div class="text-red-700">🔴 มีปัญหา <span class="font-bold"><?= $countProblem ?></span> คน</div>
        </div>
        <div class="w-full flex flex-col gap-2 mt-4">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-green-400 flex items-center justify-center text-white text-lg">🟢</span>
                <div class="flex-1 bg-green-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-700" style="width: <?= $countAll ? round($countNormal/$countAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-green-700"><?= $countAll ? round($countNormal/$countAll*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-yellow-400 flex items-center justify-center text-white text-lg">🟡</span>
                <div class="flex-1 bg-yellow-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-yellow-400 h-4 rounded-full transition-all duration-700" style="width: <?= $countAll ? round($countRisk/$countAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-yellow-700"><?= $countAll ? round($countRisk/$countAll*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-400 flex items-center justify-center text-white text-lg">🔴</span>
                <div class="flex-1 bg-red-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-red-500 h-4 rounded-full transition-all duration-700" style="width: <?= $countAll ? round($countProblem/$countAll*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-red-700"><?= $countAll ? round($countProblem/$countAll*100) : 0 ?>%</span>
            </div>
        </div>
    </div>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm animate-fade-in">
        <thead>
            <tr class="bg-gradient-to-r from-blue-100 to-pink-100 text-gray-700">
                <th class="py-2 px-3 border-b text-center">🆔 เลขประจำตัว</th>
                <th class="py-2 px-3 border-b text-center">👨‍🎓 ชื่อนักเรียน</th>
                <th class="py-2 px-3 border-b text-center">🔢 เลขที่</th>
                <th class="py-2 px-3 border-b text-center">🏫 ห้อง</th>
                <th class="py-2 px-3 border-b text-center">คะแนนรวม</th>
                <th class="py-2 px-3 border-b text-center">อารมณ์ 😖</th>
                <th class="py-2 px-3 border-b text-center">เกเร 😠</th>
                <th class="py-2 px-3 border-b text-center">สมาธิ/ไฮเปอร์ ⚡</th>
                <th class="py-2 px-3 border-b text-center">เพื่อน 🧍‍♂️🧍‍♀️</th>
                <th class="py-2 px-3 border-b text-center">จุดแข็ง 🤝</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $stu):
                    $sdqData = $sdq->getSDQSelfData($stu['Stu_id'], $pee, $term);
                    $scores = calc_sdq_scores($sdqData['answers'] ?? []);
                    $totalProblemScore =
                        ($scores['อารมณ์ 😖'] ?? 0) +
                        ($scores['เกเร 😠'] ?? 0) +
                        ($scores['สมาธิ/ไฮเปอร์ ⚡'] ?? 0) +
                        ($scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? 0);
                ?>
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                    <td class="px-3 py-2"><?= htmlspecialchars($stu['full_name']) ?></td>
                    <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                    <td class="px-3 py-2 text-center"><?= htmlspecialchars($room) ?></td>
                    <td class="px-3 py-2 text-center">
                        <div>
                            <p class="text-center text-2xl text-gray-900 font-bold"><?= htmlspecialchars($totalProblemScore) ?></p>
                            <p class="text-center text-base font-bold
                                <?php 
                                    if ($totalProblemScore >= 20) {
                                        echo 'text-red-500';
                                    } elseif ($totalProblemScore >= 14) {
                                        echo 'text-yellow-500';
                                    } else {
                                        echo 'text-green-500';
                                    }
                                ?>">
                                <?php if ($totalProblemScore >= 20): ?>
                                    มีปัญหา 😥
                                <?php elseif ($totalProblemScore >= 14): ?>
                                    ภาวะเสี่ยง 😐
                                <?php else: ?>
                                    ปกติ 😄
                                <?php endif; ?>
                            </p>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?= $scores['อารมณ์ 😖'] ?? '-' ?>
                        <span class="block text-xs <?= scoreLevel($scores['อารมณ์ 😖'] ?? 0, 'อารมณ์ 😖') === 'ปกติ' ? 'text-green-600' : (scoreLevel($scores['อารมณ์ 😖'] ?? 0, 'อารมณ์ 😖') === 'ภาวะเสี่ยง' ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= scoreLevel($scores['อารมณ์ 😖'] ?? 0, 'อารมณ์ 😖') ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?= $scores['เกเร 😠'] ?? '-' ?>
                        <span class="block text-xs <?= scoreLevel($scores['เกเร 😠'] ?? 0, 'เกเร 😠') === 'ปกติ' ? 'text-green-600' : (scoreLevel($scores['เกเร 😠'] ?? 0, 'เกเร 😠') === 'ภาวะเสี่ยง' ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= scoreLevel($scores['เกเร 😠'] ?? 0, 'เกเร 😠') ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?= $scores['สมาธิ/ไฮเปอร์ ⚡'] ?? '-' ?>
                        <span class="block text-xs <?= scoreLevel($scores['สมาธิ/ไฮเปอร์ ⚡'] ?? 0, 'สมาธิ/ไฮเปอร์ ⚡') === 'ปกติ' ? 'text-green-600' : (scoreLevel($scores['สมาธิ/ไฮเปอร์ ⚡'] ?? 0, 'สมาธิ/ไฮเปอร์ ⚡') === 'ภาวะเสี่ยง' ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= scoreLevel($scores['สมาธิ/ไฮเปอร์ ⚡'] ?? 0, 'สมาธิ/ไฮเปอร์ ⚡') ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?= $scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? '-' ?>
                        <span class="block text-xs <?= scoreLevel($scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? 0, 'เพื่อน 🧍‍♂️🧍‍♀️') === 'ปกติ' ? 'text-green-600' : (scoreLevel($scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? 0, 'เพื่อน 🧍‍♂️🧍‍♀️') === 'ภาวะเสี่ยง' ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= scoreLevel($scores['เพื่อน 🧍‍♂️🧍‍♀️'] ?? 0, 'เพื่อน 🧍‍♂️🧍‍♀️') ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?= $scores['จุดแข็ง 🤝'] ?? '-' ?>
                        <span class="block text-xs <?= strpos(scoreLevel($scores['จุดแข็ง 🤝'] ?? 0, 'ปกติ') , 'ปกติ') !== false ? 'text-green-600' : (strpos(scoreLevel($scores['จุดแข็ง 🤝'] ?? 0, 'เสี่ยง') , 'เสี่ยง') !== false ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= scoreLevel($scores['จุดแข็ง 🤝'] ?? 0, 'จุดแข็ง 🤝') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center text-gray-400 py-6">ไม่พบข้อมูล SDQ สำหรับห้องนี้</td>
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
