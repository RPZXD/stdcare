<?php
include_once("../../config/Database.php");
include_once("../../class/EQ.php");
require_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$EQ = new EQ($db);
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงชั้นเรียนทั้งหมด
$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

// สรุปผลแต่ละชั้น
$classSummary = [];
foreach ($classList as $class) {
    $classSummary[$class] = $EQ->getEQClassRoomSummary($class, '', $pee, $term);
}

// สรุปรวมทั้งโรงเรียน
$total = $have = $verygood = $good = $mid = $low = 0;
foreach ($classSummary as $sum) {
    $total += $sum['total'];
    $have += $sum['have'];
    $verygood += $sum['verygood'];
    $good += $sum['good'];
    $mid += $sum['mid'];
    $low += $sum['low'];
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนทั้งโรงเรียน</div>
        <div class="text-5xl font-extrabold text-blue-700 mb-2 animate-bounce"><?= $total ?></div>
        <div class="flex flex-col gap-1 text-center text-lg">
            <div class="text-green-700">🌟 ดีมาก <span class="font-bold"><?= $verygood ?></span> คน</div>
            <div class="text-yellow-700">👍 ดี <span class="font-bold"><?= $good ?></span> คน</div>
            <div class="text-blue-700">🙂 ปานกลาง <span class="font-bold"><?= $mid ?></span> คน</div>
            <div class="text-red-700">⚠️ ต้องปรับปรุง <span class="font-bold"><?= $low ?></span> คน</div>
        </div>
        <div class="w-full flex flex-col gap-2 mt-4">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-green-400 flex items-center justify-center text-white text-lg">🌟</span>
                <div class="flex-1 bg-green-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($verygood/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-green-700"><?= $total ? round($verygood/$total*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-yellow-400 flex items-center justify-center text-white text-lg">👍</span>
                <div class="flex-1 bg-yellow-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-yellow-400 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($good/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-yellow-700"><?= $total ? round($good/$total*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-400 flex items-center justify-center text-white text-lg">🙂</span>
                <div class="flex-1 bg-blue-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-blue-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($mid/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-blue-700"><?= $total ? round($mid/$total*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-400 flex items-center justify-center text-white text-lg">⚠️</span>
                <div class="flex-1 bg-red-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-red-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($low/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-red-700"><?= $total ? round($low/$total*100) : 0 ?>%</span>
            </div>
        </div>
    </div>
</div>
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm mb-2 animate-fade-in">
    <thead>
        <tr class="bg-blue-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">ระดับชั้น</th>
            <th class="py-2 px-3 border-b text-center">จำนวนนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">ส่งแบบประเมิน(คน)</th>
            <th class="py-2 px-3 border-b text-center text-green-700">ดีมาก</th>
            <th class="py-2 px-3 border-b text-center text-yellow-700">ดี</th>
            <th class="py-2 px-3 border-b text-center text-blue-700">ปานกลาง</th>
            <th class="py-2 px-3 border-b text-center text-red-700">ต้องปรับปรุง</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classList as $c): $sum = $classSummary[$c]; ?>
        <tr>
            <td class="px-3 py-2 text-center font-bold">ม.<?= htmlspecialchars($c) ?></td>
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
            <td class="px-3 py-2 text-center"><?= $total ?></td>
            <td class="px-3 py-2 text-center"><?= $have ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $verygood ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $good ?></td>
            <td class="px-3 py-2 text-center text-blue-700"><?= $mid ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $low ?></td>
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
