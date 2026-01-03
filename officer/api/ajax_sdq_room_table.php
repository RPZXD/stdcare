<?php
/**
 * API: SDQ Result Table by Room
 * Modern UI with Tailwind CSS & Glassmorphism
 */
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");
include_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
$user = new UserLogin($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room) {
    $students = $sdq->getSDQByClassAndRoom($class, $room, $pee, $term);
}

// Stats Calculation
$countAll = count($students);
$countNormal = 0;
$countRisk = 0;
$countProblem = 0;
$countNotEvaluated = 0;

function calc_total_problem_score($answers) {
    if (empty($answers)) return null;
    $map = [
        'อารมณ์' => [3, 8, 13, 16, 24],
        'เกเร' => [5, 12, 18, 22],
        'สมาธิ' => [2, 10, 15, 21],
        'เพื่อน' => [6, 11, 14, 19, 23],
    ];
    $sum = 0;
    foreach ($map as $qs) {
        foreach ($qs as $q) {
            $sum += (int)($answers["q$q"] ?? 0);
        }
    }
    return $sum;
}

function get_interpretation($score) {
    if ($score === null) return "ยังไม่ได้ทำ";
    if ($score >= 20) return "มีปัญหา";
    if ($score >= 14) return "ภาวะเสี่ยง";
    return "ปกติ";
}

$processedData = [];
foreach ($students as $stu) {
    $sdqData = $sdq->getSDQSelfData($stu['Stu_id'], $pee, $term);
    $score = calc_total_problem_score($sdqData['answers'] ?? []);
    $level = get_interpretation($score);
    
    if ($score === null) $countNotEvaluated++;
    elseif ($level === "มีปัญหา") $countProblem++;
    elseif ($level === "ภาวะเสี่ยง") $countRisk++;
    else $countNormal++;
    
    $processedData[] = [
        'info' => $stu,
        'score' => $score,
        'level' => $level,
        'answers' => $sdqData['answers'] ?? []
    ];
}

$evaluatedCount = $countAll - $countNotEvaluated;
$percentEvaluated = $countAll > 0 ? round(($evaluatedCount / $countAll) * 100) : 0;
?>

<!-- Premium Stats Dashboard -->
<div class="mb-10 animate-fadeIn">
    <div class="xl:col-span-12 glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border border-white/40 shadow-rose-500/5">
        <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10">
            <!-- Overall Circle Performance -->
            <div class="flex flex-col items-center shrink-0">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="439.8" stroke-dashoffset="<?= 439.8 * (1 - $percentEvaluated/100) ?>" class="text-rose-500 shadow-lg transition-all duration-1000" stroke-linecap="round" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-4xl font-black text-slate-800 dark:text-white"><?= $percentEvaluated ?>%</span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Evaluated</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-[11px] font-black text-rose-500 uppercase tracking-widest italic">ประเมินแล้ว <?= $evaluatedCount ?> / <?= $countAll ?> คน</p>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Stat Card -->
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-emerald-500 mb-1"><?= $countNormal ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ปกติ</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: <?= $evaluatedCount > 0 ? ($countNormal/$evaluatedCount)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <!-- Stat Card -->
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-amber-500 mb-1"><?= $countRisk ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ภาวะเสี่ยง</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: <?= $evaluatedCount > 0 ? ($countRisk/$evaluatedCount)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <!-- Stat Card -->
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-rose-500 mb-1"><?= $countProblem ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">มีปัญหา</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: <?= $evaluatedCount > 0 ? ($countProblem/$evaluatedCount)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <!-- Stat Card -->
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-slate-400 mb-1"><?= $countNotEvaluated ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ไม่ได้ทำ</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 opacity-30"></div>
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
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนนรวม</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">อารมณ์</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">เกเร</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">สมาธิ</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">เพื่อน</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">จุดแข็ง</th>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-700 dark:text-slate-300">
            <?php if (!empty($processedData)): ?>
                <?php foreach ($processedData as $row): 
                    $stu = $row['info'];
                    $score = $row['score'];
                    $level = $row['level'];
                    
                    $levelColor = 'slate';
                    if ($level === 'ปกติ') $levelColor = 'emerald';
                    elseif ($level === 'ภาวะเสี่ยง') $levelColor = 'amber';
                    elseif ($level === 'มีปัญหา') $levelColor = 'rose';
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
                    <td class="px-4 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="คะแนนรวม">
                        <div class="flex flex-col items-center">
                            <span class="text-base font-black text-<?= $levelColor ?>-600 italic"><?= $score ?? '-' ?></span>
                            <span class="text-[9px] font-black text-<?= $levelColor ?>-500 uppercase tracking-widest italic"><?= $level ?></span>
                        </div>
                    </td>
                    <!-- Individual aspects could be shown here as well, but for brevity and clean card view, we focus on the main ones -->
                    <?php 
                    $aspects = [
                        ['อารมณ์', [3, 8, 13, 16, 24], [4, 6]],
                        ['เกเร', [5, 12, 18, 22], [3, 5]],
                        ['สมาธิ', [2, 10, 15, 21], [5, 7]],
                        ['เพื่อน', [6, 11, 14, 19, 23], [3, 6]],
                        ['จุดแข็ง', [1, 4, 7, 9, 17, 20, 25], [5, 6]]
                    ];
                    foreach ($aspects as $aspect):
                        $label = $aspect[0];
                        $qs = $aspect[1];
                        $cutoffs = $aspect[2];
                        
                        $s_aspect = 0;
                        if (empty($row['answers'])) {
                            $s_aspect = null;
                        } else {
                            foreach ($qs as $q) $s_aspect += (int)($row['answers']["q$q"] ?? 0);
                        }
                        
                        $asp_level = 'ปกติ';
                        $asp_color = 'emerald';
                        
                        if ($s_aspect === null) {
                            $asp_level = '-';
                            $asp_color = 'slate';
                        } else {
                            if ($label === 'จุดแข็ง') {
                                if ($s_aspect >= $cutoffs[1]) { $asp_level = 'ดีมาก'; $asp_color = 'emerald'; }
                                elseif ($s_aspect >= $cutoffs[0]) { $asp_level = 'ปกติ'; $asp_color = 'emerald'; }
                                else { $asp_level = 'มีปัญหา'; $asp_color = 'rose'; }
                            } else {
                                if ($s_aspect <= $cutoffs[0]) { $asp_level = 'ปกติ'; $asp_color = 'emerald'; }
                                elseif ($s_aspect <= $cutoffs[1]) { $asp_level = 'ภาวะเสี่ยง'; $asp_color = 'amber'; }
                                else { $asp_level = 'มีปัญหา'; $asp_color = 'rose'; }
                            }
                        }
                    ?>
                    <td class="px-4 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="<?= $label ?>">
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-black text-slate-700 dark:text-slate-300"><?= $s_aspect ?? '-' ?></span>
                            <span class="text-[8px] font-black text-<?= $asp_color ?>-500 uppercase tracking-widest italic"><?= $asp_level ?></span>
                        </div>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="px-6 py-20 text-center bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                        <div class="flex flex-col items-center justify-center gap-4">
                            <i class="fas fa-folder-open text-4xl text-slate-200"></i>
                            <p class="text-sm font-bold text-slate-400 italic">ไม่พบข้อมูล SDQ สำหรับห้องที่เลือก</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
