<?php
require_once("../../config/Database.php");
require_once("../../class/SDQ.php");

$class = $_GET['class'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);

if ($class && $pee && $term) {
    // ห้องทั้งหมดในชั้น
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->bindParam(':class', $class);
    $stmt->execute();
    $roomList = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $roomSummary = [];
    foreach ($roomList as $r) {
        $roomSummary[$r] = $sdq->getSDQResultSummary($class, $r, $pee, $term, 'self');
    }
    $classSummary = $sdq->getSDQResultSummary($class, '', $pee, $term, 'self');
    ?>
    <div class="mb-6">
        <h3 class="text-lg font-bold text-red-700 mb-2">สรุปผล SDQ รายชั้น (ชั้น ม.<?= htmlspecialchars($class) ?>)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm mb-2">
                <thead>
                    <tr class="bg-red-50 text-gray-700">
                        <th class="py-2 px-3 border-b text-center">ห้อง</th>
                        <th class="py-2 px-3 border-b text-center">จำนวนนักเรียน</th>
                        <th class="py-2 px-3 border-b text-center">ส่ง SDQ (ตนเอง)</th>
                        <th class="py-2 px-3 border-b text-center text-green-700">ปกติ</th>
                        <th class="py-2 px-3 border-b text-center text-yellow-700">เสี่ยง</th>
                        <th class="py-2 px-3 border-b text-center text-red-700">มีปัญหา</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomList as $r): $sum = $roomSummary[$r]; ?>
                    <tr>
                        <td class="px-3 py-2 text-center font-bold"><?= htmlspecialchars($r) ?></td>
                        <td class="px-3 py-2 text-center"><?= $sum['total'] ?></td>
                        <td class="px-3 py-2 text-center"><?= $sum['have'] ?></td>
                        <td class="px-3 py-2 text-center text-green-700"><?= $sum['normal'] ?></td>
                        <td class="px-3 py-2 text-center text-yellow-700"><?= $sum['risk'] ?></td>
                        <td class="px-3 py-2 text-center text-red-700"><?= $sum['problem'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-red-100 font-bold">
                        <td class="px-3 py-2 text-center">รวม</td>
                        <td class="px-3 py-2 text-center"><?= $classSummary['total'] ?></td>
                        <td class="px-3 py-2 text-center"><?= $classSummary['have'] ?></td>
                        <td class="px-3 py-2 text-center text-green-700"><?= $classSummary['normal'] ?></td>
                        <td class="px-3 py-2 text-center text-yellow-700"><?= $classSummary['risk'] ?></td>
                        <td class="px-3 py-2 text-center text-red-700"><?= $classSummary['problem'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>
