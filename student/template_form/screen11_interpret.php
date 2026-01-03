<?php
/**
 * Screen11 Interpretation View
 * Modern UI with Tailwind CSS
 */
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500 text-center py-4">ไม่ได้รับอนุญาต</div>';
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

$student_id = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (!$student_id || !$pee) {
    echo '<div class="text-center py-8"><div class="text-red-500 font-bold">ไม่พบข้อมูลที่ต้องการ</div></div>';
    exit;
}

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$screening = new ScreeningData($conn);
$data = $screening->getScreeningDataByStudentId($student_id, $pee);

if (!$data) {
    echo '<div class="text-center py-8"><div class="text-amber-500 font-bold">ยังไม่มีข้อมูลการคัดกรอง 11 ด้าน</div></div>';
    exit;
}

// Aspects config
$aspects = [
    ['id' => 1, 'name' => 'ความสามารถพิเศษ', 'icon' => '🌟', 'color' => 'amber', 'field' => 'special_ability'],
    ['id' => 2, 'name' => 'ด้านการเรียน', 'icon' => '📚', 'color' => 'blue', 'field' => 'study_status'],
    ['id' => 3, 'name' => 'ด้านสุขภาพ', 'icon' => '🏥', 'color' => 'emerald', 'field' => 'health_status'],
    ['id' => 4, 'name' => 'ด้านเศรษฐกิจ', 'icon' => '💰', 'color' => 'yellow', 'field' => 'economic_status'],
    ['id' => 5, 'name' => 'ด้านสวัสดิภาพ', 'icon' => '🛡️', 'color' => 'indigo', 'field' => 'welfare_status'],
    ['id' => 6, 'name' => 'สารเสพติด', 'icon' => '🚭', 'color' => 'red', 'field' => 'drug_status'],
    ['id' => 7, 'name' => 'ความรุนแรง', 'icon' => '⚠️', 'color' => 'orange', 'field' => 'violence_status'],
    ['id' => 8, 'name' => 'พฤติกรรมทางเพศ', 'icon' => '💕', 'color' => 'pink', 'field' => 'sex_status'],
    ['id' => 9, 'name' => 'การติดเกม', 'icon' => '🎮', 'color' => 'purple', 'field' => 'game_status'],
    ['id' => 10, 'name' => 'ความต้องการพิเศษ', 'icon' => '♿', 'color' => 'teal', 'field' => 'special_need_status'],
    ['id' => 11, 'name' => 'สื่ออิเล็กทรอนิกส์', 'icon' => '📱', 'color' => 'cyan', 'field' => 'it_status'],
];

// Count statuses
$normalCount = 0;
$riskCount = 0;
$problemCount = 0;

foreach ($aspects as $a) {
    $status = $data[$a['field']] ?? '';
    if ($status === 'ปกติ' || $status === 'ไม่มี') $normalCount++;
    elseif ($status === 'เสี่ยง') $riskCount++;
    elseif ($status === 'มีปัญหา' || $status === 'มี') $problemCount++;
}

// Overall level
if ($problemCount >= 3) {
    $overallLevel = ['status' => 'มีปัญหา', 'color' => 'red', 'emoji' => '😟'];
} elseif ($riskCount >= 3 || $problemCount >= 1) {
    $overallLevel = ['status' => 'ภาวะเสี่ยง', 'color' => 'amber', 'emoji' => '😐'];
} else {
    $overallLevel = ['status' => 'ปกติ', 'color' => 'emerald', 'emoji' => '😊'];
}
?>

<!-- Header -->
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 mb-6 text-white">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-chart-pie text-xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-lg">ผลการคัดกรอง 11 ด้าน</h4>
            <p class="text-indigo-200 text-sm">ประเมินตนเอง</p>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 text-center border border-emerald-200">
        <span class="text-3xl font-black text-emerald-600"><?= $normalCount ?></span>
        <p class="text-xs font-bold text-emerald-700 mt-1">ปกติ</p>
    </div>
    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 text-center border border-amber-200">
        <span class="text-3xl font-black text-amber-600"><?= $riskCount ?></span>
        <p class="text-xs font-bold text-amber-700 mt-1">เสี่ยง</p>
    </div>
    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center border border-red-200">
        <span class="text-3xl font-black text-red-600"><?= $problemCount ?></span>
        <p class="text-xs font-bold text-red-700 mt-1">มีปัญหา</p>
    </div>
</div>

<!-- Overall Status -->
<div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 mb-6 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-<?= $overallLevel['color'] ?>-500/20 to-transparent"></div>
    <div class="relative z-10">
        <p class="text-slate-400 uppercase tracking-widest text-xs mb-2">ภาพรวม</p>
        <div class="text-5xl mb-2"><?= $overallLevel['emoji'] ?></div>
        <span class="inline-block px-4 py-2 rounded-full bg-<?= $overallLevel['color'] ?>-500 text-white font-bold">
            <?= $overallLevel['status'] ?>
        </span>
    </div>
</div>

<!-- Detailed Results -->
<div class="space-y-3">
    <?php foreach ($aspects as $a):
        $status = $data[$a['field']] ?? '-';
        $fieldRisk = str_replace('_status', '_risk', $a['field']);
        $fieldProblem = str_replace('_status', '_problem', $a['field']);
        
        // Determine status level
        if ($status === 'ปกติ' || $status === 'ไม่มี') {
            $statusColor = 'emerald';
            $statusLabel = 'ปกติ';
        } elseif ($status === 'เสี่ยง') {
            $statusColor = 'amber';
            $statusLabel = 'เสี่ยง';
        } elseif ($status === 'มีปัญหา' || $status === 'มี') {
            $statusColor = 'red';
            $statusLabel = $a['field'] === 'special_ability' || $a['field'] === 'special_need_status' ? 'มี' : 'มีปัญหา';
        } else {
            $statusColor = 'slate';
            $statusLabel = '-';
        }
    ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl"><?= $a['icon'] ?></span>
                <div>
                    <h5 class="font-bold text-slate-700 dark:text-slate-300 text-sm"><?= $a['id'] ?>. <?= $a['name'] ?></h5>
                    <?php 
                    // Show details if risk or problem
                    $details = [];
                    if ($status === 'เสี่ยง' && isset($data[$fieldRisk])) {
                        $details = is_array($data[$fieldRisk]) ? $data[$fieldRisk] : [$data[$fieldRisk]];
                    } elseif (($status === 'มีปัญหา' || $status === 'มี') && isset($data[$fieldProblem])) {
                        $details = is_array($data[$fieldProblem]) ? $data[$fieldProblem] : [$data[$fieldProblem]];
                    } elseif ($a['field'] === 'special_need_status' && $status === 'มี' && isset($data['special_need_type'])) {
                        $details = [$data['special_need_type']];
                    }
                    
                    if (!empty($details)): ?>
                    <p class="text-xs text-<?= $statusColor ?>-600 mt-1">
                        <?= htmlspecialchars(implode(', ', array_filter($details))) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-700">
                <?= $statusLabel ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Legend -->
<div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
    <h5 class="font-bold text-slate-600 dark:text-slate-400 text-sm mb-3">เกณฑ์การแปลผล</h5>
    <div class="flex flex-wrap gap-2 text-xs">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg font-bold">
            😊 ปกติ
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 text-amber-700 rounded-lg font-bold">
            😐 เสี่ยง
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-lg font-bold">
            😟 มีปัญหา
        </span>
    </div>
</div>

<!-- Note -->
<div class="mt-4 text-center text-slate-400 text-xs">
    <i class="fas fa-info-circle"></i> การคัดกรองนี้เป็นเครื่องมือเบื้องต้นเพื่อการดูแลช่วยเหลือนักเรียน
</div>
