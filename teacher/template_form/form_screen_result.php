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
$screenData = $screening->getScreeningDataByStudentId($student_id, $pee);

$screenStructure = [
    ['label' => '1. ความสามารถพิเศษ', 'key' => 'special_ability', 'icon' => '⭐'],
    ['label' => '2. ด้านการเรียน', 'key' => 'study_status', 'icon' => '📚'],
    ['label' => '3. ด้านสุขภาพ', 'key' => 'health_status', 'icon' => '❤️'],
    ['label' => '4. ด้านเศรษฐกิจ', 'key' => 'economic_status', 'icon' => '💰'],
    ['label' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'key' => 'welfare_status', 'icon' => '🛡️'],
    ['label' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'key' => 'drug_status', 'icon' => '🚫'],
    ['label' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'key' => 'violence_status', 'icon' => '⚡'],
    ['label' => '8. ด้านพฤติกรรมทางเพศ', 'key' => 'sex_status', 'icon' => '👫'],
    ['label' => '9. ด้านการติดเกม', 'key' => 'game_status', 'icon' => '🎮'],
    ['label' => '10. นักเรียนที่มีความต้องการพิเศษ', 'key' => 'special_need_status', 'icon' => '🌟'],
    ['label' => '11. ด้านการใช้เครื่องมือสื่อสาร', 'key' => 'it_status', 'icon' => '📱'],
];

function screenColor($status, $key = null) {
    if ($key === 'special_ability' && $status === 'มี') return 'from-emerald-400 to-green-500';
    return match($status) {
        'ปกติ', 'ไม่มี' => 'from-emerald-400 to-green-500',
        'เสี่ยง', 'มี' => 'from-amber-400 to-orange-500',
        'มีปัญหา' => 'from-rose-400 to-red-500',
        default => 'from-slate-300 to-slate-400'
    };
}

function screenBgColor($status, $key = null) {
    if ($key === 'special_ability' && $status === 'มี') return 'bg-emerald-50 border-emerald-200';
    return match($status) {
        'ปกติ', 'ไม่มี' => 'bg-emerald-50 border-emerald-200',
        'เสี่ยง', 'มี' => 'bg-amber-50 border-amber-200',
        'มีปัญหา' => 'bg-rose-50 border-rose-200',
        default => 'bg-slate-50 border-slate-200'
    };
}

function screenIcon($status, $key = null) {
    if ($key === 'special_ability' && $status === 'มี') return '✅';
    return match($status) {
        'ปกติ', 'ไม่มี' => '✅',
        'เสี่ยง', 'มี' => '⚠️',
        'มีปัญหา' => '❌',
        default => '❓'
    };
}

function renderSpecialAbilityDetail($detail) {
    if (!is_array($detail)) return htmlspecialchars($detail);
    $subjects = ['คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์', 'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'];
    $out = [];
    foreach ($detail as $key => $arr) {
        $idx = is_numeric($key) ? intval($key) : intval(str_replace('special_', '', $key));
        $subject = $subjects[$idx] ?? $key;
        if (is_array($arr)) {
            $desc = implode(', ', array_filter($arr, fn($v) => trim($v) !== ''));
            if ($desc !== '') $out[] = "<span class='font-semibold text-emerald-700'>{$subject}</span>: " . htmlspecialchars($desc);
        }
    }
    return implode('<br>', $out);
}

// Count summary
$normalCount = 0; $riskCount = 0; $problemCount = 0;
foreach ($screenStructure as $item) {
    $status = $screenData[$item['key']] ?? '-';
    if ($item['key'] === 'special_ability') {
        if ($status === 'มี' || $status === 'ไม่มี') $normalCount++;
    } else {
        if ($status === 'ปกติ' || $status === 'ไม่มี') $normalCount++;
        elseif ($status === 'เสี่ยง' || $status === 'มี') $riskCount++;
        elseif ($status === 'มีปัญหา') $problemCount++;
    }
}
?>

<div class="space-y-4">
    <!-- Student Info Header -->
    <div class="bg-gradient-to-r from-purple-500 to-violet-600 rounded-2xl p-4 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                <span class="text-3xl">🎓</span>
            </div>
            <div class="flex-1">
                <h2 class="font-bold text-lg md:text-xl"><?= htmlspecialchars($student_name) ?></h2>
                <p class="text-sm opacity-90">เลขที่ <?= htmlspecialchars($student_no) ?> | ชั้น ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?></p>
                <p class="text-xs opacity-80">ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-2 md:gap-4">
        <div class="bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl p-3 text-white text-center shadow-lg">
            <p class="text-2xl md:text-3xl font-black"><?= $normalCount ?></p>
            <p class="text-xs font-bold opacity-90">✅ ปกติ</p>
        </div>
        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl p-3 text-white text-center shadow-lg">
            <p class="text-2xl md:text-3xl font-black"><?= $riskCount ?></p>
            <p class="text-xs font-bold opacity-90">⚠️ เสี่ยง</p>
        </div>
        <div class="bg-gradient-to-br from-rose-400 to-red-500 rounded-xl p-3 text-white text-center shadow-lg">
            <p class="text-2xl md:text-3xl font-black"><?= $problemCount ?></p>
            <p class="text-xs font-bold opacity-90">❌ มีปัญหา</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
            <h3 class="text-white font-bold text-center flex items-center justify-center gap-2">
                <span class="text-xl">📋</span> สรุปผลการคัดกรอง 11 ด้าน
            </h3>
        </div>
        
        <div class="divide-y divide-slate-100">
            <?php foreach ($screenStructure as $item):
                $key = $item['key'];
                $status = $screenData[$key] ?? '-';
                $color = screenColor($status, $key);
                $bgColor = screenBgColor($status, $key);
                $icon = screenIcon($status, $key);
                
                $detail = '';
                switch ($key) {
                    case 'special_ability':
                        if ($status === 'มี') $detail = renderSpecialAbilityDetail($screenData['special_ability_detail'] ?? '');
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
                if (is_array($detail)) $detail = implode(', ', $detail);
            ?>
            <div class="p-3 md:p-4 <?= $bgColor ?> border-l-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br <?= $color ?> rounded-lg flex items-center justify-center shadow flex-shrink-0">
                        <span class="text-sm md:text-lg"><?= $item['icon'] ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-bold text-xs md:text-sm text-slate-700"><?= $item['label'] ?></span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r <?= $color ?> text-white text-xs font-bold rounded-full">
                                <?= $icon ?> <?= $status ?>
                            </span>
                        </div>
                        <?php if ($detail): ?>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= $detail ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overall Assessment -->
    <?php
    $overallStatus = 'ปกติ';
    $overallColor = 'from-emerald-400 to-green-500';
    $overallIcon = '✅';
    $overallText = 'นักเรียนมีสภาพปกติในทุกด้าน';
    
    if ($problemCount > 0) {
        $overallStatus = 'ต้องได้รับการดูแลเป็นพิเศษ';
        $overallColor = 'from-rose-400 to-red-500';
        $overallIcon = '🚨';
        $overallText = "พบปัญหา {$problemCount} ด้าน ควรได้รับการดูแลช่วยเหลือ";
    } elseif ($riskCount > 0) {
        $overallStatus = 'ควรเฝ้าระวัง';
        $overallColor = 'from-amber-400 to-orange-500';
        $overallIcon = '⚠️';
        $overallText = "พบความเสี่ยง {$riskCount} ด้าน ควรเฝ้าระวัง";
    }
    ?>
    <div class="bg-gradient-to-r <?= $overallColor ?> rounded-2xl p-4 text-white shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="text-2xl"><?= $overallIcon ?></span>
            </div>
            <div>
                <h4 class="font-bold text-lg">สรุปผลการประเมิน: <?= $overallStatus ?></h4>
                <p class="text-sm opacity-90"><?= $overallText ?></p>
            </div>
        </div>
    </div>
</div>
