<?php
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");
require_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
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
    $classSummary[$class] = $sdq->getSDQResultSummary($class, '', $pee, $term, 'self');
}

// สรุปรวมทั้งโรงเรียน
$total = $have = $normal = $risk = $problem = 0;
foreach ($classSummary as $sum) {
    $total += $sum['total'];
    $have += $sum['have'];
    $normal += $sum['normal'];
    $risk += $sum['risk'];
    $problem += $sum['problem'];
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Card: ตัวเลขรวม -->
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 นักเรียนทั้งหมด</div>
        <div class="text-5xl font-extrabold text-blue-700 mb-2 animate-bounce"><?= $total ?></div>
        <div class="flex flex-col gap-1 text-center text-lg">
            <div>📋 ส่ง SDQ แล้ว <span class="font-bold text-blue-700"><?= $have ?></span> คน</div>
            <div class="text-green-700">🟢 ปกติ <span class="font-bold"><?= $normal ?></span> คน</div>
            <div class="text-yellow-700">🟡 เสี่ยง <span class="font-bold"><?= $risk ?></span> คน</div>
            <div class="text-red-700">🔴 มีปัญหา <span class="font-bold"><?= $problem ?></span> คน</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-pink-100 to-pink-50 rounded-lg shadow p-6 flex flex-col items-center border border-pink-200">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">📊 สัดส่วน SDQ</div>
        <div class="w-full flex flex-col gap-2 mt-2">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-green-400 flex items-center justify-center text-white text-lg">🟢</span>
                <div class="flex-1 bg-green-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($normal/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-green-700"><?= $total ? round($normal/$total*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-yellow-400 flex items-center justify-center text-white text-lg">🟡</span>
                <div class="flex-1 bg-yellow-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-yellow-400 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($risk/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-yellow-700"><?= $total ? round($risk/$total*100) : 0 ?>%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-400 flex items-center justify-center text-white text-lg">🔴</span>
                <div class="flex-1 bg-red-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-red-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total ? round($problem/$total*100) : 0 ?>%"></div>
                </div>
                <span class="ml-2 font-bold text-red-700"><?= $total ? round($problem/$total*100) : 0 ?>%</span>
            </div>
        </div>
    </div>
</div>
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm mb-2 animate-fade-in">
    <thead>
        <tr class="bg-gradient-to-r from-blue-100 to-pink-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">🏫 ชั้น</th>
            <th class="py-2 px-3 border-b text-center">👩‍🎓 จำนวนนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">📋 ส่ง SDQ (ตนเอง)</th>
            <th class="py-2 px-3 border-b text-center text-green-700">🟢 ปกติ</th>
            <th class="py-2 px-3 border-b text-center text-yellow-700">🟡 เสี่ยง</th>
            <th class="py-2 px-3 border-b text-center text-red-700">🔴 มีปัญหา</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classList as $c): $sum = $classSummary[$c]; ?>
        <tr class="hover:bg-blue-50 transition-colors duration-150">
            <td class="px-3 py-2 text-center font-bold"><?= htmlspecialchars($c) ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['total'] ?></td>
            <td class="px-3 py-2 text-center"><?= $sum['have'] ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $sum['normal'] ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $sum['risk'] ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $sum['problem'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-pink-100 font-bold">
            <td class="px-3 py-2 text-center">รวม</td>
            <td class="px-3 py-2 text-center"><?= $total ?></td>
            <td class="px-3 py-2 text-center"><?= $have ?></td>
            <td class="px-3 py-2 text-center text-green-700"><?= $normal ?></td>
            <td class="px-3 py-2 text-center text-yellow-700"><?= $risk ?></td>
            <td class="px-3 py-2 text-center text-red-700"><?= $problem ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('sdqPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['ปกติ', 'เสี่ยง', 'มีปัญหา'],
            datasets: [{
                data: [<?= $normal ?>, <?= $risk ?>, <?= $problem ?>],
                backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
