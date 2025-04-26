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

// Helper for special_ability_detail: show only subjects that have value
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
    <div class="bg-blue-100 rounded px-4 py-2 font-bold flex items-center gap-2">✨ 1. ความสามารถพิเศษ: <span class="ml-2"><?= showValue($data['special_ability']) ?></span></div>
    <?php if ($data['special_ability'] === 'มี'): ?>
        <div class="pl-4"><?= showSpecialAbilityDetail($data['special_ability_detail']) ?></div>
    <?php endif; ?>

    <div class="bg-yellow-50 rounded px-4 py-2 font-bold flex items-center gap-2">📖 2. ด้านการเรียน: <span class="ml-2"><?= showValue($data['study_status']) ?></span></div>
    <?php if ($data['study_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['study_risk']) ?></div>
    <?php elseif ($data['study_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['study_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-green-50 rounded px-4 py-2 font-bold flex items-center gap-2">💪 3. ด้านสุขภาพ: <span class="ml-2"><?= showValue($data['health_status']) ?></span></div>
    <?php if ($data['health_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['health_risk']) ?></div>
    <?php elseif ($data['health_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['health_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-orange-50 rounded px-4 py-2 font-bold flex items-center gap-2">💰 4. ด้านเศรษฐกิจ: <span class="ml-2"><?= showValue($data['economic_status']) ?></span></div>
    <?php if ($data['economic_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['economic_risk']) ?></div>
    <?php elseif ($data['economic_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['economic_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-pink-50 rounded px-4 py-2 font-bold flex items-center gap-2">🏠 5. ด้านสวัสดิภาพและความปลอดภัย: <span class="ml-2"><?= showValue($data['welfare_status']) ?></span></div>
    <?php if ($data['welfare_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['welfare_risk']) ?></div>
    <?php elseif ($data['welfare_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['welfare_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-purple-50 rounded px-4 py-2 font-bold flex items-center gap-2">🚬 6. ด้านพฤติกรรมการใช้สารเสพติด: <span class="ml-2"><?= showValue($data['drug_status']) ?></span></div>
    <?php if ($data['drug_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['drug_risk']) ?></div>
    <?php elseif ($data['drug_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['drug_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-red-50 rounded px-4 py-2 font-bold flex items-center gap-2">💢 7. ด้านพฤติกรรมการใช้ความรุนแรง: <span class="ml-2"><?= showValue($data['violence_status']) ?></span></div>
    <?php if ($data['violence_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['violence_risk']) ?></div>
    <?php elseif ($data['violence_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['violence_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-indigo-50 rounded px-4 py-2 font-bold flex items-center gap-2">❤️ 8. ด้านพฤติกรรมทางเพศ: <span class="ml-2"><?= showValue($data['sex_status']) ?></span></div>
    <?php if ($data['sex_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['sex_risk']) ?></div>
    <?php elseif ($data['sex_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['sex_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-teal-50 rounded px-4 py-2 font-bold flex items-center gap-2">🎮 9. ด้านการติดเกม: <span class="ml-2"><?= showValue($data['game_status']) ?></span></div>
    <?php if ($data['game_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['game_risk']) ?></div>
    <?php elseif ($data['game_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['game_problem']) ?></div>
    <?php endif; ?>

    <div class="bg-gray-100 rounded px-4 py-2 font-bold flex items-center gap-2">♿ 10. นักเรียนที่มีความต้องการพิเศษ: <span class="ml-2"><?= showValue($data['special_need_status']) ?></span></div>
    <?php if ($data['special_need_status'] === 'มี'): ?>
        <div class="pl-4 text-blue-700"><?= showValue($data['special_need_type']) ?></div>
    <?php endif; ?>

    <div class="bg-gray-200 rounded px-4 py-2 font-bold flex items-center gap-2">📱 11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์: <span class="ml-2"><?= showValue($data['it_status']) ?></span></div>
    <?php if ($data['it_status'] === 'เสี่ยง'): ?>
        <div class="pl-4 text-yellow-700"><?= showValue($data['it_risk']) ?></div>
    <?php elseif ($data['it_status'] === 'มีปัญหา'): ?>
        <div class="pl-4 text-red-700"><?= showValue($data['it_problem']) ?></div>
    <?php endif; ?>
</div>
