<?php
require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

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
    if ($status === 'ปกติ') return '<span class="text-green-600 font-bold">ปกติ</span>';
    if ($status === 'เสี่ยง') return '<span class="text-yellow-600 font-bold">เสี่ยง</span>';
    if ($status === 'มีปัญหา') return '<span class="text-red-600 font-bold">มีปัญหา</span>';
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
<div class="space-y-4">
    <div class="text-xl font-bold mb-4 flex items-center gap-2">📝 แปลผลแบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)</div>
    <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded overflow-hidden">
        <thead class="bg-blue-100">
            <tr>
                <th class="px-4 py-2 text-left">ด้าน</th>
                <th class="px-4 py-2 text-left">ผลการประเมิน</th>
                <th class="px-4 py-2 text-left">รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($fields as $f): ?>
            <tr class="hover:bg-blue-50 transition">
                <td class="px-4 py-2 font-semibold">
                    <?php
                    // Add emoji for each field
                    $emojis = [
                        0 => '✨', 1 => '📖', 2 => '💪', 3 => '💰', 4 => '🏠', 5 => '🚬',
                        6 => '💢', 7 => '❤️', 8 => '🎮', 9 => '♿', 10 => '📱'
                    ];
                    $idx = array_search($f, $fields, true);
                    $emoji = $emojis[$idx] ?? '';
                    echo $emoji . ' ' . htmlspecialchars($f['label']);
                    ?>
                </td>
                <td class="px-4 py-2"><?= interpretStatus($data[$f['key']] ?? '') ?></td>
                <td class="px-4 py-2">
                    <?php
                    // Show details for risk/problem if not normal
                    $key = $f['key'];
                    if (isset($data[$key])) {
                        if ($data[$key] === 'เสี่ยง' && isset($data[str_replace('status', 'risk', $key)])) {
                            echo '<span class="text-yellow-700">' . showValue($data[str_replace('status', 'risk', $key)]) . '</span>';
                        } elseif ($data[$key] === 'มีปัญหา' && isset($data[str_replace('status', 'problem', $key)])) {
                            echo '<span class="text-red-700">' . showValue($data[str_replace('status', 'problem', $key)]) . '</span>';
                        } elseif ($key === 'special_ability' && $data[$key] === 'มี' && isset($data['special_ability_detail'])) {
                            echo showSpecialAbilityDetail($data['special_ability_detail']);
                        } elseif ($key === 'special_need_status' && $data[$key] === 'มี' && isset($data['special_need_type'])) {
                            echo '<span class="text-blue-700">' . showValue($data['special_need_type']) . '</span>';
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="mt-4 text-sm text-gray-500">
        <b>หมายเหตุ:</b> สี <span class="text-green-600 font-bold">ปกติ</span> = ไม่มีความเสี่ยง,
        <span class="text-yellow-600 font-bold">เสี่ยง</span> = มีความเสี่ยง,
        <span class="text-red-600 font-bold">มีปัญหา</span> = มีปัญหาชัดเจน
    </div>
</div>
