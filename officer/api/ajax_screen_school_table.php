<?php
include_once("../../config/Database.php");
include_once("../../class/Screeningdata.php");
include_once("../../class/UserLogin.php");
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screen = new ScreeningData($db);
$user = new UserLogin($db);

$class = $_GET['class'] ?? '';
$term = $user->getTerm();
$pee = $user->getPee();
// 11 ด้าน
$screenFields = [
    'special_ability' => 'ความสามารถพิเศษ',
    'study' => 'การเรียน',
    'health' => 'สุขภาพ',
    'economic' => 'เศรษฐกิจ',
    'welfare' => 'สวัสดิการ',
    'drug' => 'ยาเสพติด',
    'violence' => 'ความรุนแรง',
    'sex' => 'เพศ',
    'game' => 'เกม/สื่อ',
    'special_need' => 'ความต้องการพิเศษ',
    'it' => 'เทคโนโลยี'
];

// ดึงชั้นเรียนทั้งหมด
$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

// เตรียมข้อมูลสรุปแต่ละชั้น
$classSummary = [];
$totalSummary = [];
$totalStudents = 0;
foreach ($screenFields as $key => $label) {
    $totalSummary[$key] = ['normal' => 0, 'risk' => 0, 'problem' => 0];
}
foreach ($classList as $class) {
    $sum = $screen->getScreeningSummaryByClassRoom($class, '', $pee); // null pee = ทุกปี
    $classSummary[$class] = $sum;
    // รวมผลทุกชั้น
    foreach ($screenFields as $key => $label) {
        $totalSummary[$key]['normal'] += $sum[$key]['normal'] ?? 0;
        $totalSummary[$key]['risk'] += $sum[$key]['risk'] ?? 0;
        $totalSummary[$key]['problem'] += $sum[$key]['problem'] ?? 0;
    }
    // นับจำนวนนักเรียนในชั้น
    $stmt2 = $db->prepare("SELECT COUNT(*) FROM student WHERE Stu_major = :class AND Stu_status = 1");
    $stmt2->bindParam(':class', $class);
    $stmt2->execute();
    $totalStudents += (int)$stmt2->fetchColumn();
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200 w-full">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 สรุปผลการคัดกรอง 11 ด้าน (ทั้งโรงเรียน)</div>
        <div class="text-lg text-blue-700 mb-2">นักเรียนทั้งหมด: <span class="font-bold"><?= $totalStudents ?></span> คน</div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-xs mb-2">
                <thead>
                    <tr class="bg-blue-100 text-gray-700">
                        <th class="py-1 px-2 border-b text-center">ด้าน</th>
                        <th class="py-1 px-2 border-b text-center text-green-700">ปกติ</th>
                        <th class="py-1 px-2 border-b text-center text-yellow-700">เสี่ยง</th>
                        <th class="py-1 px-2 border-b text-center text-red-700">มีปัญหา</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($screenFields as $key => $label): ?>
                    <tr>
                        <td class="px-2 py-1 text-left font-semibold"><?= $label ?></td>
                        <td class="px-2 py-1 text-center text-green-700"><?= $totalSummary[$key]['normal'] ?? 0 ?></td>
                        <td class="px-2 py-1 text-center text-yellow-700"><?= $totalSummary[$key]['risk'] ?? 0 ?></td>
                        <td class="px-2 py-1 text-center text-red-700"><?= $totalSummary[$key]['problem'] ?? 0 ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-xs mb-2">
    <thead>
        <tr class="bg-blue-100 text-gray-700">
            <th class="py-1 px-2 border-b text-center">ชั้น</th>
            <?php foreach ($screenFields as $label): ?>
                <th class="py-1 px-2 border-b text-center"><?= $label ?></th>
            <?php endforeach; ?>
        </tr>
        <tr class="bg-blue-50 text-gray-700">
            <th class="py-1 px-2 border-b text-center"></th>
            <?php foreach ($screenFields as $key => $label): ?>
                <th class="py-1 px-2 border-b text-center">
                    <span class="text-green-700">ปกติ</span> /
                    <span class="text-yellow-700">เสี่ยง</span> /
                    <span class="text-red-700">มีปัญหา</span>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classList as $class): $sum = $classSummary[$class]; ?>
        <tr>
            <td class="px-2 py-1 text-center font-bold"><?= htmlspecialchars($class) ?></td>
            <?php foreach ($screenFields as $key => $label): ?>
                <td class="px-2 py-1 text-center">
                    <span class="text-green-700"><?= $sum[$key]['normal'] ?? 0 ?></span> /
                    <span class="text-yellow-700"><?= $sum[$key]['risk'] ?? 0 ?></span> /
                    <span class="text-red-700"><?= $sum[$key]['problem'] ?? 0 ?></span>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-blue-100 font-bold">
            <td class="px-2 py-1 text-center">รวม</td>
            <?php foreach ($screenFields as $key => $label): ?>
                <td class="px-2 py-1 text-center">
                    <span class="text-green-700"><?= $totalSummary[$key]['normal'] ?? 0 ?></span> /
                    <span class="text-yellow-700"><?= $totalSummary[$key]['risk'] ?? 0 ?></span> /
                    <span class="text-red-700"><?= $totalSummary[$key]['problem'] ?? 0 ?></span>
                </td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>
