<?php
/**
 * EQ Interpretation View
 * Modern UI with Tailwind CSS
 */
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500 text-center py-4">ไม่ได้รับอนุญาต</div>';
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/EQ.php');

$student_id = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (!$student_id || !$pee || !$term) {
    echo '<div class="text-center py-8"><div class="text-red-500 font-bold">ไม่พบข้อมูลที่ต้องการ</div></div>';
    exit;
}

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$eq = new EQ($conn);

$eqData = $eq->getEQData($student_id, $pee, $term);

if (!$eqData) {
    echo '<div class="text-center py-8"><div class="text-amber-500 font-bold">ยังไม่มีข้อมูล EQ</div></div>';
    exit;
}

// EQ Structure for interpretation
$eqStructure = [
    'ดี' => [
        'icon' => '😊', 'color' => 'rose', 'gradient' => 'from-rose-500 to-red-600',
        'subs' => [
            ['label' => 'ควบคุมตนเอง', 'range' => [13, 17], 'items' => range(1, 6)],
            ['label' => 'เห็นใจผู้อื่น', 'range' => [16, 20], 'items' => range(7, 12)],
            ['label' => 'รับผิดชอบ', 'range' => [16, 22], 'items' => range(13, 18)],
        ]
    ],
    'เก่ง' => [
        'icon' => '🧠', 'color' => 'fuchsia', 'gradient' => 'from-fuchsia-500 to-purple-600',
        'subs' => [
            ['label' => 'มีแรงจูงใจ', 'range' => [14, 20], 'items' => range(19, 24)],
            ['label' => 'ตัดสินใจ/แก้ปัญหา', 'range' => [13, 19], 'items' => range(25, 30)],
            ['label' => 'สัมพันธภาพ', 'range' => [14, 20], 'items' => range(31, 36)],
        ]
    ],
    'สุข' => [
        'icon' => '💖', 'color' => 'pink', 'gradient' => 'from-pink-500 to-rose-600',
        'subs' => [
            ['label' => 'ภูมิใจในตนเอง', 'range' => [9, 13], 'items' => range(37, 40)],
            ['label' => 'พอใจชีวิต', 'range' => [16, 22], 'items' => range(41, 46)],
            ['label' => 'สุขสงบทางใจ', 'range' => [15, 21], 'items' => range(47, 52)],
        ]
    ],
];

// Calculate total
$totalEQ = 0;
for ($i = 1; $i <= 52; $i++) {
    $totalEQ += isset($eqData["EQ$i"]) ? (int)$eqData["EQ$i"] : 0;
}

// Level interpretation
function getEQLevel($score) {
    if ($score >= 170) return ['level' => 'ดีมาก', 'color' => 'emerald', 'emoji' => '🌟'];
    if ($score >= 140) return ['level' => 'ดี', 'color' => 'green', 'emoji' => '😊'];
    if ($score >= 100) return ['level' => 'ปานกลาง', 'color' => 'amber', 'emoji' => '😐'];
    return ['level' => 'ควรปรับปรุง', 'color' => 'red', 'emoji' => '😔'];
}

function getSubResult($score, $range) {
    if ($score > $range[1]) return ['result' => 'สูง', 'level' => 'high'];
    if ($score >= $range[0]) return ['result' => 'ปกติ', 'level' => 'normal'];
    return ['result' => 'ต่ำ', 'level' => 'low'];
}

$totalLevel = getEQLevel($totalEQ);

$levelColors = [
    'high' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400', 'light' => 'bg-emerald-100 dark:bg-emerald-950/30'],
    'normal' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600 dark:text-amber-400', 'light' => 'bg-amber-100 dark:bg-amber-950/30'],
    'low' => ['bg' => 'bg-red-500', 'text' => 'text-red-600 dark:text-red-400', 'light' => 'bg-red-100 dark:bg-red-950/30'],
];
?>

<!-- Header -->
<div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl p-4 mb-6 text-white">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-chart-line text-xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-lg">ผลการประเมิน EQ</h4>
            <p class="text-pink-200 text-sm">ความฉลาดทางอารมณ์</p>
        </div>
    </div>
</div>

<!-- Total Score Card -->
<div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 mb-6 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-<?= $totalLevel['color'] ?>-500/20 to-transparent"></div>
    <div class="relative z-10">
        <p class="text-slate-400 uppercase tracking-widest text-xs mb-2">คะแนน EQ รวม</p>
        <div class="text-6xl font-black text-white mb-2"><?= $totalEQ ?></div>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-<?= $totalLevel['color'] ?>-500 text-white font-bold">
            <span class="text-2xl"><?= $totalLevel['emoji'] ?></span>
            <span>EQ <?= $totalLevel['level'] ?></span>
        </div>
        <p class="text-slate-400 text-sm mt-3">คะแนนเต็ม 156 คะแนน</p>
    </div>
</div>

<!-- Main Categories (ดี, เก่ง, สุข) -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <?php foreach ($eqStructure as $main => $data):
        $mainScore = 0;
        foreach ($data['subs'] as $sub) {
            foreach ($sub['items'] as $q) {
                $mainScore += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
            }
        }
        $maxScore = count($data['subs']) * 6 * 3; // approx
    ?>
    <div class="bg-gradient-to-br <?= $data['gradient'] ?> rounded-xl p-4 text-white text-center">
        <span class="text-3xl"><?= $data['icon'] ?></span>
        <h5 class="font-black text-lg mt-1"><?= $main ?></h5>
        <p class="text-2xl font-bold mt-1"><?= $mainScore ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Detailed Breakdown -->
<div class="space-y-4">
    <?php foreach ($eqStructure as $main => $data): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="bg-gradient-to-r <?= $data['gradient'] ?> p-3 flex items-center gap-2">
            <span class="text-xl"><?= $data['icon'] ?></span>
            <h5 class="font-bold text-white"><?= $main ?></h5>
        </div>
        <div class="p-4 space-y-3">
            <?php foreach ($data['subs'] as $sub):
                $score = 0;
                foreach ($sub['items'] as $q) {
                    $score += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
                }
                $maxScore = count($sub['items']) * 3;
                $percent = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
                $result = getSubResult($score, $sub['range']);
                $lc = $levelColors[$result['level']];
            ?>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="font-medium text-slate-700 dark:text-slate-300 text-sm"><?= $sub['label'] ?></span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400"><?= $score ?>/<?= $maxScore ?></span>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full <?= $lc['light'] ?> <?= $lc['text'] ?>">
                            <?= $result['result'] ?>
                        </span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div class="h-full rounded-full <?= $lc['bg'] ?> transition-all" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Legend -->
<div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
    <h5 class="font-bold text-slate-600 dark:text-slate-400 text-sm mb-3">เกณฑ์การแปลผล EQ รวม</h5>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-lg font-bold">
            🌟 ดีมาก (170+)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-green-100 dark:bg-green-950/30 text-green-700 dark:text-green-400 rounded-lg font-bold">
            😊 ดี (140-169)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 rounded-lg font-bold">
            😐 ปานกลาง (100-139)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400 rounded-lg font-bold">
            😔 ควรปรับปรุง (&lt;100)
        </span>
    </div>
</div>

<!-- Note -->
<div class="mt-4 text-center text-slate-400 text-xs">
    <i class="fas fa-info-circle"></i> การแปลผลนี้เป็นการประเมินเบื้องต้นเพื่อการพัฒนาตนเอง
</div>
