<?php
/**
 * Unified SDQ Interpretation
 * Types: self, parent
 * Modern UI with Tailwind CSS
 */
require_once '../../class/SDQ.php';
require_once '../../config/Database.php';

$student_id = $_GET['stuId'] ?? $_GET['student_id'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
$type = $_GET['type'] ?? 'self'; // self or parent

// DB & SDQ
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

// Get data based on type
if ($type === 'self') {
    $existingData = $sdq->getSDQSelfData($student_id, $pee, $term);
    $typeTitle = 'นักเรียนประเมินตนเอง';
    $typeIcon = 'fa-user-check';
} else {
    $existingData = $sdq->getSDQParData($student_id, $pee, $term);
    $typeTitle = 'ผู้ปกครองประเมิน';
    $typeIcon = 'fa-user-friends';
}

$answers = $existingData['answers'] ?? [];
$memo = $existingData['memo'] ?? '';
$impact = $existingData['impact'] ?? [];

// Categories and questions
$categories = [
    'emotional' => ['label' => 'ด้านอารมณ์', 'icon' => '😖', 'questions' => [3, 8, 13, 16, 24], 'color' => 'red'],
    'conduct' => ['label' => 'ด้านพฤติกรรม', 'icon' => '😠', 'questions' => [5, 12, 18, 22], 'color' => 'orange'],
    'hyperactivity' => ['label' => 'สมาธิ/ซน', 'icon' => '⚡', 'questions' => [2, 10, 15, 21, 25], 'color' => 'amber'],
    'peer' => ['label' => 'ด้านเพื่อน', 'icon' => '👥', 'questions' => [6, 11, 14, 19, 23], 'color' => 'sky'],
    'prosocial' => ['label' => 'จุดแข็ง', 'icon' => '🤝', 'questions' => [1, 4, 7, 9, 17, 20], 'color' => 'emerald'],
];

// Calculate scores
$categoryScores = [];
foreach ($categories as $key => $cat) {
    $score = 0;
    foreach ($cat['questions'] as $qnum) {
        $score += (int)($answers["q$qnum"] ?? 0);
    }
    $categoryScores[$key] = $score;
}

// Total problem score (exclude prosocial)
$totalProblemScore = $categoryScores['emotional'] + $categoryScores['conduct'] + $categoryScores['hyperactivity'] + $categoryScores['peer'];

// Score level function
function getScoreLevel($score, $key) {
    $cutoffs = [
        'emotional' => [4, 6],
        'conduct' => [3, 5],
        'hyperactivity' => [5, 7],
        'peer' => [3, 6],
        'prosocial' => [5, 6],
    ];
    [$normal, $borderline] = $cutoffs[$key] ?? [0, 0];
    
    if ($key === 'prosocial') {
        if ($score >= $borderline) return ['status' => 'ปกติ', 'level' => 'normal'];
        if ($score >= $normal) return ['status' => 'ภาวะเสี่ยง', 'level' => 'warning'];
        return ['status' => 'มีปัญหา', 'level' => 'danger'];
    }
    
    if ($score <= $normal) return ['status' => 'ปกติ', 'level' => 'normal'];
    if ($score <= $borderline) return ['status' => 'ภาวะเสี่ยง', 'level' => 'warning'];
    return ['status' => 'มีปัญหา', 'level' => 'danger'];
}

// Total level
if ($totalProblemScore >= 20) {
    $totalLevel = ['status' => 'มีปัญหา', 'level' => 'danger', 'emoji' => '😥'];
} elseif ($totalProblemScore >= 14) {
    $totalLevel = ['status' => 'ภาวะเสี่ยง', 'level' => 'warning', 'emoji' => '😐'];
} else {
    $totalLevel = ['status' => 'ปกติ', 'level' => 'normal', 'emoji' => '😄'];
}

$levelColors = [
    'normal' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-100'],
    'warning' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600', 'light' => 'bg-amber-100'],
    'danger' => ['bg' => 'bg-red-500', 'text' => 'text-red-600', 'light' => 'bg-red-100'],
];
?>

<!-- Header -->
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 mb-6 text-white">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas <?= $typeIcon ?> text-xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-lg">ผลการประเมิน SDQ</h4>
            <p class="text-purple-200 text-sm"><?= $typeTitle ?></p>
        </div>
    </div>
</div>

<!-- Total Score Card -->
<div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 mb-6 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r <?= $totalLevel['level'] === 'normal' ? 'from-emerald-500/20' : ($totalLevel['level'] === 'warning' ? 'from-amber-500/20' : 'from-red-500/20') ?> to-transparent"></div>
    <div class="relative z-10">
        <p class="text-slate-400 uppercase tracking-widest text-xs mb-2">คะแนนรวมปัญหา</p>
        <div class="text-6xl font-black text-white mb-2"><?= $totalProblemScore ?></div>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full <?= $levelColors[$totalLevel['level']]['bg'] ?> text-white font-bold">
            <span class="text-2xl"><?= $totalLevel['emoji'] ?></span>
            <span><?= $totalLevel['status'] ?></span>
        </div>
    </div>
</div>

<!-- Category Scores -->
<div class="space-y-3 mb-6">
    <?php foreach ($categories as $key => $cat): 
        $score = $categoryScores[$key];
        $maxScore = count($cat['questions']) * 2;
        $percent = min(100, round(($score / $maxScore) * 100));
        $level = getScoreLevel($score, $key);
        $lc = $levelColors[$level['level']];
    ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="text-xl"><?= $cat['icon'] ?></span>
                <span class="font-bold text-slate-700 dark:text-slate-300"><?= $cat['label'] ?></span>
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full <?= $lc['light'] ?> <?= $lc['text'] ?>">
                <?= $level['status'] ?>
            </span>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-3 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 <?= $lc['bg'] ?>" style="width: <?= $percent ?>%"></div>
            </div>
            <span class="text-sm font-bold text-slate-600 dark:text-slate-400 w-16 text-right"><?= $score ?>/<?= $maxScore ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Impact Section -->
<?php if (!empty($impact)): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
    <h5 class="font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-500"></i>
        ผลกระทบต่อชีวิตประจำวัน
    </h5>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php 
        $impactItems = [
            'home' => ['label' => 'ที่บ้าน', 'icon' => 'fa-home'],
            'leisure' => ['label' => 'กิจกรรมยามว่าง', 'icon' => 'fa-running'],
            'friend' => ['label' => 'การคบเพื่อน', 'icon' => 'fa-users'],
            'classroom' => ['label' => 'ในห้องเรียน', 'icon' => 'fa-school'],
        ];
        foreach ($impactItems as $ikey => $item): 
            $iScore = $impact[$ikey] ?? 0;
            $iLevel = $iScore === 0 ? 'normal' : ($iScore === 1 ? 'warning' : 'danger');
            $iText = $iScore === 0 ? 'ปกติ' : ($iScore === 1 ? 'มีปัญหา' : 'รุนแรง');
        ?>
        <div class="text-center p-3 rounded-xl <?= $levelColors[$iLevel]['light'] ?>">
            <i class="fas <?= $item['icon'] ?> text-lg <?= $levelColors[$iLevel]['text'] ?> mb-2"></i>
            <p class="text-xs text-slate-600 dark:text-slate-400"><?= $item['label'] ?></p>
            <p class="font-bold text-sm <?= $levelColors[$iLevel]['text'] ?>"><?= $iText ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Memo -->
<?php if (!empty($memo)): ?>
<div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
    <h5 class="font-bold text-purple-700 dark:text-purple-400 mb-2 flex items-center gap-2">
        <i class="fas fa-comment-dots"></i>
        ความคิดเห็นเพิ่มเติม
    </h5>
    <p class="text-slate-600 dark:text-slate-300 text-sm"><?= htmlspecialchars($memo) ?></p>
</div>
<?php endif; ?>

<!-- Legend -->
<div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
    <h5 class="font-bold text-slate-600 dark:text-slate-400 text-sm mb-3">เกณฑ์การแปลผล</h5>
    <div class="flex flex-wrap gap-3">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> ปกติ (0-13)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span> ภาวะเสี่ยง (14-19)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-red-500"></span> มีปัญหา (20+)
        </span>
    </div>
</div>
