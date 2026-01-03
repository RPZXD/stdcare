<?php
/**
 * Unified SDQ Result Template
 * Supports: self (นักเรียน), teach (ครู), par (ผู้ปกครอง)
 */
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../class/SDQ.php';

// Get parameters
$type = $_GET['type'] ?? 'self'; // self | teach | par
$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

// Type configurations
$typeConfig = [
    'self' => [
        'title' => 'นักเรียนประเมินตนเอง',
        'icon' => 'fa-user',
        'color' => 'from-blue-500 to-indigo-600',
        'getMethod' => 'getSDQSelfData'
    ],
    'teach' => [
        'title' => 'ครูเป็นผู้ประเมิน',
        'icon' => 'fa-chalkboard-teacher',
        'color' => 'from-amber-500 to-orange-600',
        'getMethod' => 'getSDQTeachData'
    ],
    'par' => [
        'title' => 'ผู้ปกครองเป็นผู้ประเมิน',
        'icon' => 'fa-user-friends',
        'color' => 'from-purple-500 to-pink-600',
        'getMethod' => 'getSDQParData'
    ]
];

$config = $typeConfig[$type] ?? $typeConfig['self'];

// Initialize database and get data
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

$method = $config['getMethod'];
$existingData = $sdq->$method($student_id, $pee, $term);
$answers = $existingData['answers'] ?? [];
$memo = $existingData['memo'] ?? '';
$impact = $existingData['impact'] ?? [];

// Category scoring
$categories = [
    'อารมณ์' => ['questions' => [3, 8, 13, 16, 24], 'icon' => '😖', 'color' => 'blue', 'cutoffs' => [4, 6]],
    'เกเร' => ['questions' => [5, 12, 18, 22], 'icon' => '😠', 'color' => 'rose', 'cutoffs' => [3, 5]],
    'สมาธิ' => ['questions' => [2, 10, 15, 21], 'icon' => '⚡', 'color' => 'amber', 'cutoffs' => [5, 7]],
    'เพื่อน' => ['questions' => [6, 11, 14, 19, 23], 'icon' => '🧍', 'color' => 'purple', 'cutoffs' => [3, 6]],
    'จุดแข็ง' => ['questions' => [1, 4, 7, 9, 17, 20, 25], 'icon' => '🤝', 'color' => 'emerald', 'cutoffs' => [5, 6]],
];

$categoryScores = [];
foreach ($categories as $label => $data) {
    $score = 0;
    foreach ($data['questions'] as $qnum) {
        $score += (int)($answers["q$qnum"] ?? 0);
    }
    $categoryScores[$label] = $score;
}

$totalProblemScore = $categoryScores['อารมณ์'] + $categoryScores['เกเร'] + $categoryScores['สมาธิ'] + $categoryScores['เพื่อน'];

function getScoreLevel($score, $cutoffs, $isStrength = false) {
    [$normal, $borderline] = $cutoffs;
    if ($isStrength) {
        return $score >= $borderline ? ['ปกติ', 'emerald'] : ($score >= $normal ? ['เสี่ยง', 'amber'] : ['มีปัญหา', 'rose']);
    }
    return $score <= $normal ? ['ปกติ', 'emerald'] : ($score <= $borderline ? ['เสี่ยง', 'amber'] : ['มีปัญหา', 'rose']);
}

function getTotalLevel($score) {
    if ($score >= 20) return ['มีปัญหา', 'rose', '😥'];
    if ($score >= 14) return ['เสี่ยง', 'amber', '😐'];
    return ['ปกติ', 'emerald', '😄'];
}

$totalLevel = getTotalLevel($totalProblemScore);
?>

<div class="space-y-6">
    <!-- Header Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Student Info -->
        <div class="bg-gradient-to-r <?= $config['color'] ?> rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <i class="fas <?= $config['icon'] ?> text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">ผลการประเมิน SDQ</h2>
                    <p class="text-white/80 text-sm"><?= $config['title'] ?></p>
                    <p class="text-white/60 text-xs mt-1">
                        <?= htmlspecialchars($student_name) ?> | ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Score -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-lg">
            <div class="text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">คะแนนรวมปัญหา</p>
                <div class="text-5xl font-black text-<?= $totalLevel[1] ?>-500 mb-2"><?= $totalProblemScore ?></div>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-<?= $totalLevel[1] ?>-100 dark:bg-<?= $totalLevel[1] ?>-900/30 rounded-full">
                    <span class="text-2xl"><?= $totalLevel[2] ?></span>
                    <span class="text-<?= $totalLevel[1] ?>-600 font-bold"><?= $totalLevel[0] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Scores -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-slate-400"></i>
            คะแนนรายด้าน
        </h3>
        <div class="space-y-4">
            <?php foreach ($categories as $label => $data): 
                $score = $categoryScores[$label];
                $isStrength = $label === 'จุดแข็ง';
                $maxScore = count($data['questions']) * 2;
                $percent = min(100, round(($score / $maxScore) * 100));
                $level = getScoreLevel($score, $data['cutoffs'], $isStrength);
            ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-lg"><?= $data['icon'] ?></span>
                            <span class="font-bold text-slate-700 dark:text-slate-300"><?= $label ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-500 font-medium"><?= $score ?> คะแนน</span>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-<?= $level[1] ?>-100 dark:bg-<?= $level[1] ?>-900/30 text-<?= $level[1] ?>-600">
                                <?= $level[0] ?>
                            </span>
                        </div>
                    </div>
                    <div class="h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-<?= $data['color'] ?>-500 rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Impact Assessment -->
    <?php if (!empty($impact)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-amber-500"></i>
            ผลกระทบต่อชีวิตประจำวัน
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <?php 
            $impactAreas = [
                'home' => ['label' => 'ความเป็นอยู่ที่บ้าน', 'icon' => 'fa-home'],
                'leisure' => ['label' => 'กิจกรรมยามว่าง', 'icon' => 'fa-gamepad'],
                'friend' => ['label' => 'การคบเพื่อน', 'icon' => 'fa-users'],
                'classroom' => ['label' => 'การเรียนในห้องเรียน', 'icon' => 'fa-school'],
            ];
            foreach ($impactAreas as $key => $area): 
                $val = $impact[$key] ?? 0;
                $impactLevel = $val == 0 ? ['ไม่มีปัญหา', 'emerald'] : ($val == 1 ? ['มีบ้าง', 'amber'] : ['รุนแรง', 'rose']);
            ?>
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                    <div class="flex items-center gap-2">
                        <i class="fas <?= $area['icon'] ?> text-slate-400"></i>
                        <span class="text-sm text-slate-600 dark:text-slate-400"><?= $area['label'] ?></span>
                    </div>
                    <span class="px-2 py-1 text-xs font-bold rounded-lg bg-<?= $impactLevel[1] ?>-100 dark:bg-<?= $impactLevel[1] ?>-900/30 text-<?= $impactLevel[1] ?>-600">
                        <?= $impactLevel[0] ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Memo -->
    <?php if (!empty($memo)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-lg">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-comment-dots text-slate-400"></i>
            ความคิดเห็นเพิ่มเติม
        </h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4">
            <?= nl2br(htmlspecialchars($memo)) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Summary -->
    <div class="bg-gradient-to-r from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">สรุปผลการประเมิน</h3>
        <div class="flex flex-wrap gap-2">
            <?php 
            $problemAreas = [];
            foreach (['อารมณ์', 'เกเร', 'สมาธิ', 'เพื่อน'] as $cat) {
                $level = getScoreLevel($categoryScores[$cat], $categories[$cat]['cutoffs'], false);
                if ($level[0] !== 'ปกติ') {
                    $problemAreas[] = $cat;
                }
            }
            if (empty($problemAreas)): ?>
                <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-sm font-bold">
                    ✅ ไม่พบปัญหาในทุกด้าน
                </span>
            <?php else: ?>
                <span class="text-slate-500 text-sm mr-2">พบปัญหา/ความเสี่ยงใน:</span>
                <?php foreach ($problemAreas as $area): ?>
                    <span class="px-3 py-1.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-full text-sm font-bold">
                        #<?= $area ?>
                    </span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
