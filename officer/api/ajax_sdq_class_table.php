<?php
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");
require_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
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

$roomSummary = [];
foreach ($roomList as $room) {
    $roomSummary[$room] = $sdq->getSDQResultSummary($class, $room, $pee, $term, 'self');
}
$classSummary = $sdq->getSDQResultSummary($class, '', $pee, $term, 'self');
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <?php
    $countAll = $classSummary['total'];
    $countNormal = $classSummary['normal'];
    $countRisk = $classSummary['risk'];
    $countProblem = $classSummary['problem'];
    ?>
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนในชั้นนี้</div>
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
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm mb-2 animate-fade-in">
    <thead>
        <tr class="bg-gradient-to-r from-blue-100 to-pink-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">🏫 ห้อง</th>
            <th class="py-2 px-3 border-b text-center">👩‍🎓 จำนวนนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">📋 ส่ง SDQ (ตนเอง)</th>
            <th class="py-2 px-3 border-b text-center text-green-700">🟢 ปกติ</th>
            <th class="py-2 px-3 border-b text-center text-yellow-700">🟡 เสี่ยง</th>
            <th class="py-2 px-3 border-b text-center text-red-700">🔴 มีปัญหา</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roomList as $r): $sum = $roomSummary[$r]; ?>
        <tr class="hover:bg-blue-50 transition-colors duration-150">
            <td class="px-3 py-2 text-center font-bold"><?= htmlspecialchars($r) ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['total'] ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['have'] ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $sum['normal'] ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $sum['risk'] ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $sum['problem'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-pink-100 font-bold">
            <td class="px-3 py-2 text-center">รวม</td>
            <td class="px-3 py-2 text-center"><?= $classSummary['total'] ?></td>
            <td class="px-3 py-2 text-center"><?= $classSummary['have'] ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $classSummary['normal'] ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $classSummary['risk'] ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $classSummary['problem'] ?></td>
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
