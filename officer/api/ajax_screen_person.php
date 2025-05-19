<?php
require_once("../../config/Database.php");
require_once("../../class/Screeningdata.php");

$stuId = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (!$stuId || !$pee) {
    echo '<div class="text-red-500">ไม่พบข้อมูลนักเรียนหรือปีการศึกษา</div>';
    exit;
}

$db = (new Database("phichaia_student"))->getConnection();
$screening = new ScreeningData($db);
$data = $screening->getScreeningDataByStudentId($stuId, $pee);

if (!$data) {
    echo '<div class="text-gray-500 text-center py-8">ยังไม่มีข้อมูลการบันทึกแบบคัดกรอง 11 ด้าน</div>';
    exit;
}

// Helper for showing array or string
function showValue($val) {
    if (is_array($val)) {
        return implode('<br>', array_map('htmlspecialchars', $val));
    }
    return htmlspecialchars($val ?? '-');
}

// Example interpretation logic (customize as needed)
function interpretStatus($status) {
    if ($status === 'ปกติ' || $status == 1) return '<span class="text-green-600 font-bold">ปกติ</span>';
    if ($status === 'เสี่ยง' || $status == 2) return '<span class="text-yellow-600 font-bold">เสี่ยง</span>';
    if ($status === 'มีปัญหา' || $status == 3) return '<span class="text-red-600 font-bold">มีปัญหา</span>';
    return '<span class="text-gray-500">-</span>';
}

$fields = [
    ['label' => '1. ความสามารถพิเศษ', 'key' => 'special_ability'],
    ['label' => '2. ด้านการเรียน', 'key' => 'study_status'],
    ['label' => '3. ด้านสุขภาพ', 'key' => 'health_status'],
    ['label' => '4. ด้านเศรษฐกิจ', 'key' => 'economic_status'],
    ['label' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'key' => 'welfare_status'],
    ['label' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'key' => 'drug_status'],
    ['label' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'key' => 'violence_status'],
    ['label' => '8. ด้านพฤติกรรมทางเพศ', 'key' => 'sex_status'],
    ['label' => '9. ด้านการติดเกม', 'key' => 'game_status'],
    ['label' => '10. นักเรียนที่มีความต้องการพิเศษ', 'key' => 'special_need_status'],
    ['label' => '11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์', 'key' => 'it_status'],
];

// Helper for special_ability_detail: show only subjects that have value, with emoji
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
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6 mt-4">
    <h2 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">📝 ผลการคัดกรอง 11 ด้าน (รายบุคคล)</h2>
    <dl class="divide-y divide-gray-200">
        <?php foreach ($fields as $f): ?>
            <div class="py-3 flex flex-col sm:flex-row sm:items-center gap-2">
                <dt class="font-semibold w-full sm:w-1/3"><?= $f['label'] ?></dt>
                <dd class="flex-1">
                    <?php
                    if ($f['key'] === 'special_ability') {
                        echo htmlspecialchars($data['special_ability'] ?? '-');
                        if (!empty($data['special_ability_detail'])) {
                            echo '<div class="mt-1">' . showSpecialAbilityDetail($data['special_ability_detail']) . '</div>';
                        }
                    } else {
                        echo interpretStatus($data[$f['key']] ?? null);
                    }
                    ?>
                </dd>
            </div>
        <?php endforeach; ?>
    </dl>
</div>
