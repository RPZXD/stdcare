<?php
require_once '../../class/Screeningdata.php';
require_once '../../config/Database.php';

$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screening = new ScreeningData($db);
// ดึงข้อมูลการคัดกรอง 11 ด้าน
$screenData = $screening->getScreeningDataByStudentId($student_id, $pee);

// โครงสร้าง 11 ด้าน
$screenStructure = [
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

// ฟังก์ชันแปลงสถานะเป็นสี
function screenColor($status, $key = null) {
    // ข้อ 1: ถ้าเป็น 'มี' ให้เป็นสีเขียว
    if ($key === 'special_ability' && $status === 'มี') {
        return 'bg-green-500';
    }
    return match($status) {
        'ปกติ', 'ไม่มี' => 'bg-green-500',
        'เสี่ยง', 'มี' => 'bg-yellow-500',
        'มีปัญหา' => 'bg-red-500',
        default => 'bg-gray-400'
    };
}

// ฟังก์ชันแปลงสถานะเป็นไอคอน
function screenIcon($status, $key = null) {
    // ข้อ 1: ถ้าเป็น 'มี' ให้เป็นถูก
    if ($key === 'special_ability' && $status === 'มี') {
        return '✅';
    }
    return match($status) {
        'ปกติ', 'ไม่มี' => '✅',
        'เสี่ยง', 'มี' => '⚠️',
        'มีปัญหา' => '❌',
        default => '❓'
    };
}

// ฟังก์ชันแสดงรายละเอียดความสามารถพิเศษ (array ซ้อน)
function renderSpecialAbilityDetail($detail) {
    if (!is_array($detail)) return htmlspecialchars($detail);
    $subjects = [
        'คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์',
        'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'
    ];
    $out = [];
    foreach ($detail as $key => $arr) {
        // key อาจเป็น special_0, special_1, ...
        $idx = is_numeric($key) ? intval($key) : intval(str_replace('special_', '', $key));
        $subject = $subjects[$idx] ?? $key;
        if (is_array($arr)) {
            $desc = implode(', ', array_filter($arr, fn($v) => trim($v) !== ''));
            if ($desc !== '') {
                $out[] = "<b>{$subject}</b>: " . htmlspecialchars($desc);
            }
        }
    }
    return implode('<br>', $out);
}
?>
<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-emerald-500 text-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-bold">🎓 ข้อมูลนักเรียน</h2>
            <p>ชื่อ: <?= htmlspecialchars($student_name) ?></p>
            <p>เลขที่: <?= htmlspecialchars($student_no) ?></p>
            <p>ชั้น: ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?></p>
            <p>ปีการศึกษา: <?= htmlspecialchars($pee) ?></p>
        </div>
        <div class="bg-white p-4 rounded-lg border shadow">
            <h3 class="text-center text-gray-700 font-bold">ผลการคัดกรอง 11 ด้าน</h3>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold mb-2">📋 สรุปผลการคัดกรอง 11 ด้าน</h3>
        <table class="min-w-full text-sm border border-gray-300 mb-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">ด้าน</th>
                    <th class="border px-2 py-1">สถานะ</th>
                    <th class="border px-2 py-1">รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($screenStructure as $item):
                    $key = $item['key'];
                    $status = $screenData[$key] ?? '-';
                    $color = screenColor($status, $key);
                    $icon = screenIcon($status, $key);
                    // รายละเอียดแต่ละด้าน
                    $detail = '';
                    switch ($key) {
                        case 'special_ability':
                            if ($status === 'มี') {
                                $detail = renderSpecialAbilityDetail($screenData['special_ability_detail'] ?? '');
                            }
                            break;
                        case 'study_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['study_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['study_problem'] ?? '';
                            break;
                        case 'health_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['health_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['health_problem'] ?? '';
                            break;
                        case 'economic_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['economic_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['economic_problem'] ?? '';
                            break;
                        case 'welfare_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['welfare_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['welfare_problem'] ?? '';
                            break;
                        case 'drug_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['drug_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['drug_problem'] ?? '';
                            break;
                        case 'violence_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['violence_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['violence_problem'] ?? '';
                            break;
                        case 'sex_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['sex_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['sex_problem'] ?? '';
                            break;
                        case 'game_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['game_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['game_problem'] ?? '';
                            break;
                        case 'special_need_status':
                            if ($status === 'มี') $detail = $screenData['special_need_type'] ?? '';
                            break;
                        case 'it_status':
                            if ($status === 'เสี่ยง') $detail = $screenData['it_risk'] ?? '';
                            if ($status === 'มีปัญหา') $detail = $screenData['it_problem'] ?? '';
                            break;
                    }
                    // ถ้าเป็น array ให้แสดงเป็น list
                    if (is_array($detail)) {
                        $detail = implode(', ', $detail);
                    }
                ?>
                <tr>
                    <td class="border px-2 py-1"><?= $item['label'] ?></td>
                    <td class="border text-center">
                        <span class="inline-block px-2 py-1 rounded text-white <?= $color ?>"><?= $icon ?> <?= $status ?></span>
                    </td>
                    <td class="border px-2 py-1"><?= $detail ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

