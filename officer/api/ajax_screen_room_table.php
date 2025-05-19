<?php
include_once("../../config/Database.php");
include_once("../../class/Screeningdata.php");
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screen = new ScreeningData($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
include_once("../../class/UserLogin.php");
$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room && $pee) {
    $students = $screen->getScreenByClassAndRoom($class, $room, $pee);
}

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

// --- รายห้อง: สรุปผลแต่ละด้าน (interpretation) ---
$summary = $screen->getScreeningSummaryByClassRoom($class, $room, $pee);
$totalStudents = count($students);

// Helper for showing array or string
function showValue($val) {
    if (is_array($val)) {
        return implode('<br>', array_map('htmlspecialchars', $val));
    }
    return htmlspecialchars($val ?? '-');
}
function interpretStatus($status) {
    if ($status === 'ปกติ' || $status == 1 || $status === '1') return '<span class="text-green-600 font-bold">ปกติ</span>';
    if ($status === 'เสี่ยง' || $status == 2 || $status === '2') return '<span class="text-yellow-600 font-bold">เสี่ยง</span>';
    if ($status === 'มีปัญหา' || $status == 3 || $status === '3') return '<span class="text-red-600 font-bold">มีปัญหา</span>';
    return '<span class="text-gray-500">-</span>';
}
function showSpecialAbilityDetail($val) {
    if (is_array($val)) {
        $subjects = [
            'special_0' => '🧮 คณิตศาสตร์',
            'special_1' => '📚 ภาษาไทย',
            'special_2' => '🌏 ภาษาต่างประเทศ',
            'special_3' => '🔬 วิทยาศาสตร์',
            'special_4' => '🎨 ศิลปะ',
            'special_5' => '🛠️ การงานอาชีพและเทคโนโลยี',
            'special_6' => '🏃‍♂️ สุขศึกษา และพลศึกษา',
            'special_7' => '🕌 สังคมศึกษา ศาสนา และวัฒนธรรม'
        ];
        $html = '';
        foreach ($val as $key => $details) {
            if (empty($details) || (is_array($details) && count(array_filter($details, fn($d) => trim($d) !== '')) === 0)) continue;
            $subject = $subjects[$key] ?? $key;
            $html .= "<div class='mb-1 flex items-start gap-2'><span class='font-semibold'>{$subject}:</span> ";
            if (is_array($details)) {
                $html .= "<span class='text-gray-700'>" . implode(', ', array_filter(array_map('htmlspecialchars', $details), fn($d) => trim($d) !== '')) . "</span>";
            } else {
                $html .= "<span class='text-gray-700'>" . htmlspecialchars($details) . "</span>";
            }
            $html .= "</div>";
        }
        return $html ?: '<span class="text-gray-400">-</span>';
    }
    return '<span class="text-gray-400">-</span>';
}
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow p-6 flex flex-col items-center border border-blue-200 w-full">
        <div class="font-bold text-2xl mb-2 flex items-center gap-2">👩‍🎓 สรุปผลการคัดกรอง 11 ด้าน (รายห้อง)</div>
        <div class="text-lg text-blue-700 mb-2">นักเรียนในห้องนี้: <span class="font-bold"><?= $totalStudents ?></span> คน</div>
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
                        <td class="px-2 py-1 text-center text-green-700"><?= $summary[$key]['normal'] ?? 0 ?></td>
                        <td class="px-2 py-1 text-center text-yellow-700"><?= $summary[$key]['risk'] ?? 0 ?></td>
                        <td class="px-2 py-1 text-center text-red-700"><?= $summary[$key]['problem'] ?? 0 ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ตารางรายบุคคล -->
<div class="overflow-x-auto">
<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow text-sm animate-fade-in">
    <thead>
        <tr class="bg-blue-100 text-gray-700">
            <th class="py-2 px-3 border-b text-center">🆔 เลขประจำตัว</th>
            <th class="py-2 px-3 border-b text-center">👨‍🎓 ชื่อนักเรียน</th>
            <th class="py-2 px-3 border-b text-center">🔢 เลขที่</th>
            <?php foreach ($screenFields as $key => $label): ?>
                <th class="py-2 px-3 border-b text-center"><?= $label ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $stu):
                $data = $screen->getScreeningDataByStudentId($stu['Stu_id'], $pee);
            ?>
            <tr class="hover:bg-blue-50 transition-colors duration-150">
                <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                <td class="px-3 py-2"><?= htmlspecialchars($stu['full_name']) ?></td>
                <td class="px-3 py-2 text-center"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                <?php foreach ($screenFields as $key => $label): ?>
                    <td class="px-3 py-2 text-center">
                        <?php
                        // แสดงผลแบบ interpret_screen11_self
                        if ($key === 'special_ability') {
                            echo htmlspecialchars($data['special_ability'] ?? '-');
                            if (!empty($data['special_ability_detail'])) {
                                echo '<div class="mt-1">' . showSpecialAbilityDetail($data['special_ability_detail']) . '</div>';
                            }
                        } elseif ($key === 'special_need') {
                            echo interpretStatus($data['special_need_status'] ?? null);
                            if (!empty($data['special_need_type'])) {
                                echo '<div class="mt-1 text-blue-700">' . showValue($data['special_need_type']) . '</div>';
                            }
                        } else {
                            echo interpretStatus($data[$key . '_status'] ?? null);
                            if (($data[$key . '_status'] ?? null) === 'เสี่ยง' && !empty($data[$key . '_risk'])) {
                                echo '<div class="text-yellow-700">' . showValue($data[$key . '_risk']) . '</div>';
                            } elseif (($data[$key . '_status'] ?? null) === 'มีปัญหา' && !empty($data[$key . '_problem'])) {
                                echo '<div class="text-red-700">' . showValue($data[$key . '_problem']) . '</div>';
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= 4 + count($screenFields) ?>" class="text-center text-gray-400 py-6">ไม่พบข้อมูลการคัดกรองสำหรับห้องนี้</td>
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
