<?php
require_once("../../config/Database.php");
require_once("../../class/SDQ.php");

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);

function statusText($ishave) {
    return $ishave ? '<span class="text-green-600 font-semibold">✔</span>' : '<span class="text-gray-400">-</span>';
}

if ($class && $room && $pee && $term) {
    $students = $sdq->getSDQByClassAndRoom($class, $room, $pee, $term);
    ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm">
            <thead>
                <tr class="bg-red-100 text-gray-700">
                    <th class="py-2 px-3 border-b text-center">เลขที่</th>
                    <th class="py-2 px-3 border-b text-center">รหัส</th>
                    <th class="py-2 px-3 border-b text-center">ชื่อ-สกุล</th>
                    <th class="py-2 px-3 border-b text-center">SDQ (ตนเอง)</th>
                    <th class="py-2 px-3 border-b text-center">SDQ (ผู้ปกครอง)</th>
                    <th class="py-2 px-3 border-b text-center">SDQ (ครู)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $stu): ?>
                        <tr class="hover:bg-red-50 transition-colors duration-150">
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($stu['full_name']) ?></td>
                            <td class="px-3 py-2 text-center"><?= statusText($stu['self_ishave']) ?></td>
                            <td class="px-3 py-2 text-center"><?= statusText($stu['par_ishave']) ?></td>
                            <td class="px-3 py-2 text-center"><?= statusText($stu['teach_ishave']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-6">ไม่พบข้อมูล SDQ สำหรับห้องนี้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
