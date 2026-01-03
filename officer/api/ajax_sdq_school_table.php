<?php
/**
 * API: SDQ Result Summary for Full School
 * Modern UI with Tailwind CSS & Glassmorphism
 */
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");
include_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();

$classList = [];
$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

$schoolSummary = [];
$totalAll = 0; $totalHave = 0; $totalNormal = 0; $totalRisk = 0; $totalProblem = 0;

foreach ($classList as $class) {
    $sum = $sdq->getSDQResultSummary($class, '', $pee, $term, 'self');
    $schoolSummary[$class] = $sum;
    $totalAll += $sum['total'];
    $totalHave += $sum['have'];
    $totalNormal += $sum['normal'];
    $totalRisk += $sum['risk'];
    $totalProblem += $sum['problem'];
}

$percentEvaluated = $totalAll > 0 ? round(($totalHave / $totalAll) * 100) : 0;
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
                    <p class="text-[11px] font-black text-rose-500 uppercase tracking-widest italic">ทั้งโรงเรียน <?= $totalHave ?> / <?= $totalAll ?> คน</p>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-emerald-500 mb-1"><?= $totalNormal ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ปกติ</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: <?= $totalHave > 0 ? ($totalNormal/$totalHave)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-amber-500 mb-1"><?= $totalRisk ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">ภาวะเสี่ยง</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: <?= $totalHave > 0 ? ($totalRisk/$totalHave)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-rose-500 mb-1"><?= $totalProblem ?></span>
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none italic">มีปัญหา</span>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: <?= $totalHave > 0 ? ($totalProblem/$totalHave)*100 : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-white/50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center group hover:scale-105 transition-all">
                    <span class="text-3xl font-black text-slate-400 mb-1"><?= ($totalAll - $totalHave) ?></span>
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
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">ระดับชั้น</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">นักเรียนทั้งหมด</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ประเมินแล้ว</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center text-emerald-600">ปกติ</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center text-amber-600">ภาวะเสี่ยง</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center text-rose-600">มีปัญหา</th>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-700 dark:text-slate-300">
            <?php foreach ($classList as $class): $sum = $schoolSummary[$class]; ?>
            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="ระดับชั้น">
                    <div class="flex items-center gap-4">
                        <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-rose-500 text-[13px] font-black italic">ม.<?= $class ?></span>
                        <div class="text-[14px] font-black text-slate-800 dark:text-white">มัธยมศึกษาปีที่ <?= $class ?></div>
                    </div>
                </td>
                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="นักเรียนทั้งหมด">
                    <span class="text-sm font-black text-slate-400 italic"><?= $sum['total'] ?></span>
                </td>
                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ประเมินแล้ว">
                    <div class="flex flex-col items-center">
                        <span class="text-sm font-black text-slate-800 dark:text-white"><?= $sum['have'] ?></span>
                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest italic"><?= $sum['total'] > 0 ? round(($sum['have']/$sum['total'])*100) : 0 ?>%</span>
                    </div>
                </td>
                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center text-emerald-600" data-label="ปกติ">
                    <span class="text-sm font-black italic"><?= $sum['normal'] ?></span>
                </td>
                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center text-amber-600" data-label="ภาวะเสี่ยง">
                    <span class="text-sm font-black italic"><?= $sum['risk'] ?></span>
                </td>
                <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center text-rose-600" data-label="มีปัญหา">
                    <span class="text-sm font-black italic"><?= $sum['problem'] ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <!-- Grand Total Row -->
            <tr class="bg-rose-500 shadow-xl shadow-rose-500/20 text-white animate-pulse">
                <td class="px-6 py-6 rounded-l-[1.5rem] font-black italic">ภาพรวมทั้งโรงเรียน</td>
                <td class="px-6 py-6 text-center font-black italic"><?= $totalAll ?></td>
                <td class="px-6 py-6 text-center font-black italic"><?= $totalHave ?> (<?= $percentEvaluated ?>%)</td>
                <td class="px-6 py-6 text-center font-black italic"><?= $totalNormal ?></td>
                <td class="px-6 py-6 text-center font-black italic"><?= $totalRisk ?></td>
                <td class="px-6 py-6 rounded-r-[1.5rem] text-center font-black italic"><?= $totalProblem ?></td>
            </tr>
        </tbody>
    </table>
</div>
