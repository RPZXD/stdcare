<?php
/**
 * API: Screening 11 Result Summary for Full School
 * Modern UI with Tailwind CSS & Glassmorphism
 */
include_once("../../config/Database.php");
include_once("../../class/Screeningdata.php");
include_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screen = new ScreeningData($db);
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();

$screenFields = [
    'special_ability' => 'ความสามารถพิเศษ',
    'study' => 'การเรียน',
    'health' => 'สุขภาพ',
    'economic' => 'เศรษฐกิจ',
    'welfare' => 'สวัสดิการ',
    'drug' => 'ยาเสพติด',
    'violence' => 'ความรุนแรง',
    'sex' => 'เพศ',
    'game' => 'เกม/สื่อ',
    'special_need' => 'ความต้องการพิเศษ',
    'it' => 'เทคโนโลยี'
];

$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

$classSummary = [];
$totalSummary = [];
$totalAllStudents = 0;

foreach ($screenFields as $key => $label) {
    $totalSummary[$key] = ['normal' => 0, 'risk' => 0, 'problem' => 0];
}

foreach ($classList as $class) {
    $sum = $screen->getScreeningSummaryByClassRoom($class, '', $pee);
    $classSummary[$class] = $sum;
    foreach ($screenFields as $key => $label) {
        $totalSummary[$key]['normal'] += $sum[$key]['normal'] ?? 0;
        $totalSummary[$key]['risk'] += $sum[$key]['risk'] ?? 0;
        $totalSummary[$key]['problem'] += $sum[$key]['problem'] ?? 0;
    }
    
    $stmt2 = $db->prepare("SELECT COUNT(*) FROM student WHERE Stu_major = :class AND Stu_status = 1");
    $stmt2->execute(['class' => $class]);
    $totalAllStudents += (int)$stmt2->fetchColumn();
}
?>

<!-- Premium Stats Dashboard -->
<div class="mb-10 animate-fadeIn overflow-x-auto">
    <div class="min-w-[800px] glass-effect rounded-[2.5rem] p-8 border border-white/40 shadow-xl shadow-indigo-500/5">
        <div class="flex items-center gap-6 mb-8 border-b border-slate-100 dark:border-slate-800 pb-6">
            <div class="w-16 h-16 bg-indigo-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">คัดกรอง 11 ด้าน: สรุปภาพรวมโรงเรียน</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total School Students: <?= $totalAllStudents ?></p>
            </div>
        </div>

        <div class="grid grid-cols-11 gap-2">
            <?php foreach ($screenFields as $key => $label): 
                $sum = $totalSummary[$key];
                $problemCount = $sum['risk'] + $sum['problem'];
                $totalS = $sum['normal'] + $problemCount;
                $problemPercent = $totalS > 0 ? round(($problemCount / $totalS) * 100) : 0;
            ?>
            <div class="flex flex-col items-center group">
                <div class="relative w-full aspect-square flex items-center justify-center mb-3">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="50%" cy="50%" r="40%" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                        <circle cx="50%" cy="50%" r="40%" stroke="currentColor" stroke-width="6" fill="transparent" stroke-dasharray="100" stroke-dashoffset="<?= 100 - $problemPercent ?>" class="text-rose-500 transition-all duration-1000" stroke-linecap="round" />
                    </svg>
                    <span class="absolute text-[10px] font-black text-slate-700 dark:text-white"><?= $problemPercent ?>%</span>
                </div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter text-center h-8 leading-none"><?= $label ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="overflow-x-auto">
    <table class="w-full text-left border-separate border-spacing-y-2">
        <thead>
            <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">ระดับชั้น</th>
                <?php foreach ($screenFields as $label): ?>
                    <th class="px-2 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest italic text-center"><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-700 dark:text-slate-300">
            <?php foreach ($classList as $class): $sum = $classSummary[$class]; ?>
            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="ระดับชั้น">
                    <div class="flex items-center gap-4">
                        <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-indigo-500 text-[13px] font-black italic">ม.<?= $class ?></span>
                        <div class="text-[14px] font-black text-slate-800 dark:text-white">มัธยมศึกษาปีที่ <?= $class ?></div>
                    </div>
                </td>
                <?php foreach ($screenFields as $key => $label): 
                    $n = $sum[$key]['normal'] ?? 0;
                    $risk = $sum[$key]['risk'] ?? 0;
                    $prob = $sum[$key]['problem'] ?? 0;
                ?>
                <td class="px-2 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="<?= $label ?>">
                    <div class="flex flex-col items-center min-w-[100px] text-[10px] space-y-1">
                        <span class="text-emerald-600">ปกติ: <?= $n ?></span>
                        <span class="text-amber-500 border-t border-slate-50 w-full pt-1">เสี่ยง: <?= $risk ?></span>
                        <span class="text-rose-500 border-t border-slate-50 w-full pt-1">ปัญหา: <?= $prob ?></span>
                    </div>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>

            <!-- Grand Total Row -->
            <tr class="bg-indigo-600 text-white shadow-xl shadow-indigo-600/20">
                <td class="px-6 py-6 rounded-l-[1.5rem] font-black italic">รวมทั้งโรงเรียน</td>
                <?php foreach ($screenFields as $key => $label): 
                    $sn = $totalSummary[$key]['normal'];
                    $sr = $totalSummary[$key]['risk'];
                    $sp = $totalSummary[$key]['problem'];
                ?>
                <td class="px-2 py-6 text-center text-[10px] font-black">
                    <div class="flex flex-col items-center">
                        <span>ปกติ: <?= $sn ?></span>
                        <span class="border-t border-white/20 w-full pt-1">เสี่ยง: <?= $sr ?></span>
                        <span class="border-t border-white/20 w-full pt-1">ปัญหา: <?= $sp ?></span>
                    </div>
                </td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>
