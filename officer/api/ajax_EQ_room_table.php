<?php
/**
 * API: EQ Result Table by Room
 * Modern UI with Tailwind CSS & Glassmorphism
 */
include_once("../../config/Database.php");
include_once("../../class/EQ.php");
include_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$EQ = new EQ($db);
$user = new UserLogin($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room) {
    $students = $EQ->getEQByClassAndRoom($class, $room, $pee, $term);
}

// Stats Calculation
$countAll = count($students);
$countVeryGood = $countGood = $countMid = $countLow = $countNotEvaluated = 0;

function calc_eq_score($answers) {
    if (empty($answers)) return null;
    $sum = 0;
    for ($i = 1; $i <= 52; $i++) {
        $sum += (int)($answers["EQ$i"] ?? 0);
    }
    return $sum;
}

function eqLevel($score) {
    if ($score === null) return "ยังไม่ได้ทำ";
    return match(true) {
        $score >= 170 => 'EQ ดีมาก',
        $score >= 140 => 'EQ ดี',
        $score >= 100 => 'EQ ปานกลาง',
        default => 'EQ ต้องปรับปรุง'
    };
}

$processedData = [];
foreach ($students as $stu) {
    $eqData = $EQ->getEQData($stu['Stu_id'], $pee, $term);
    $score = calc_eq_score($eqData);
    $level = eqLevel($score);
    
    if ($score === null) $countNotEvaluated++;
    elseif ($level === "EQ ดีมาก") $countVeryGood++;
    elseif ($level === "EQ ดี") $countGood++;
    elseif ($level === "EQ ปานกลาง") $countMid++;
    else $countLow++;
    
    $processedData[] = [
        'info' => $stu,
        'score' => $score,
        'level' => $level,
        'data' => $eqData
    ];
}

$evaluatedCount = $countAll - $countNotEvaluated;
$percentEvaluated = $countAll > 0 ? round(($evaluatedCount / $countAll) * 100) : 0;
?>

<!-- Premium Stats Dashboard -->
<div class="mb-10 animate-fadeIn">
    <div class="xl:col-span-12 glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border border-white/40 shadow-amber-500/5">
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10">
            <!-- Overall Circle Performance -->
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="439.8" stroke-dashoffset="<?= 439.8 * (1 - $percentEvaluated/100) ?>" class="text-amber-500 shadow-lg transition-all duration-1000" stroke-linecap="round" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-4xl font-black text-slate-800 dark:text-white"><?= $percentEvaluated ?>%</span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Evaluated</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-[11px] font-black text-amber-500 uppercase tracking-widest italic">ประเมินแล้ว <?= $evaluatedCount ?> / <?= $countAll ?> คน</p>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-emerald-500 mb-1"><?= $countVeryGood ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ดีมาก</span>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-amber-500 mb-1"><?= $countGood ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ดี</span>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-blue-500 mb-1"><?= $countMid ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ปานกลาง</span>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-rose-500 mb-1"><?= $countLow ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic text-center">ปรับปรุง</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="overflow-x-auto overflow-y-visible">
    <table class="w-full text-left border-separate border-spacing-y-2">
        <thead>
            <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">เลขที่ / นักเรียน</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รหัสนักเรียน</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนนรวม</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">การแปลผล</th>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-700 dark:text-slate-300">
            <?php if (!empty($processedData)): ?>
                <?php foreach ($processedData as $row): 
                    $stu = $row['info'];
                    $score = $row['score'];
                    $level = $row['level'];
                    
                    $levelColor = 'slate';
                    if ($level === 'EQ ดีมาก') $levelColor = 'emerald';
                    elseif ($level === 'EQ ดี') $levelColor = 'amber';
                    elseif ($level === 'EQ ปานกลาง') $levelColor = 'blue';
                    elseif ($level === 'EQ ต้องปรับปรุง') $levelColor = 'rose';
                ?>
                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                    <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="เลขที่ / นักเรียน">
                        <div class="flex items-center gap-4">
                            <span class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-[10px] font-black italic"><?= $stu['Stu_no'] ?></span>
                            <div class="text-[13px] font-black text-slate-800 dark:text-white"><?= $stu['full_name'] ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="รหัสนักเรียน">
                        <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest font-mono italic">ID: <?= $stu['Stu_id'] ?></span>
                    </td>
                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="คะแนนรวม">
                        <span class="text-base font-black text-<?= $levelColor ?>-600 italic"><?= $score ?? '-' ?></span>
                    </td>
                    <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center" data-label="การแปลผล">
                        <span class="px-4 py-1.5 bg-<?= $levelColor ?>-500/10 text-<?= $levelColor ?>-600 dark:text-<?= $levelColor ?>-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-<?= $levelColor ?>-500/20">
                            <?= $level ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                        <p class="text-sm font-bold text-slate-400 italic text-center">ไม่พบข้อมูล EQ สำหรับห้องที่เลือก</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
