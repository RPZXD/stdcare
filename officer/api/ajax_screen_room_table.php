<?php
/**
 * API: Screening 11 Result Table by Room
 * Modern UI with Tailwind CSS & Glassmorphism
 */
include_once("../../config/Database.php");
include_once("../../class/Screeningdata.php");
include_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screen = new ScreeningData($db);
$user = new UserLogin($db);

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$term = $user->getTerm();
$pee = $user->getPee();

$students = [];
if ($class && $room) {
    $students = $screen->getScreenByClassAndRoom($class, $room, $pee);
}

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

$summary = $screen->getScreeningSummaryByClassRoom($class, $room, $pee);
$totalStudents = count($students);

function interpretStatus($status) {
    if ($status === 'ปกติ' || $status == 1 || $status === '1') return ['text' => 'ปกติ', 'color' => 'emerald', 'bg' => 'emerald-500/10'];
    if ($status === 'เสี่ยง' || $status == 2 || $status === '2') return ['text' => 'เสี่ยง', 'color' => 'amber', 'bg' => 'amber-500/10'];
    if ($status === 'มีปัญหา' || $status == 3 || $status === '3') return ['text' => 'มีปัญหา', 'color' => 'rose', 'bg' => 'rose-500/10'];
    return ['text' => '-', 'color' => 'slate', 'bg' => 'slate-100'];
}

function showValue($val) {
    if (is_array($val)) return implode(', ', array_map('htmlspecialchars', $val));
    return htmlspecialchars($val ?? '-');
}

function showSpecialAbilityDetail($val) {
    if (!is_array($val)) return '';
    $subjects = [
        'special_0' => '🧮 คณิต', 'special_1' => '📚 ไทย', 'special_2' => '🌏 ต่างประเทศ',
        'special_3' => '🔬 วิทย์', 'special_4' => '🎨 ศิลปะ', 'special_5' => '🛠️ กอท.',
        'special_6' => '🏃‍♂️ พละ', 'special_7' => '🕌 สังคม'
    ];
    $html = '<div class="mt-2 text-[10px] space-y-1 text-left text-slate-500">';
    foreach ($val as $key => $details) {
        if (empty($details)) continue;
        $subj = $subjects[$key] ?? $key;
        $detailStr = is_array($details) ? implode(', ', array_filter($details)) : $details;
        if (!empty($detailStr)) $html .= "<div><span class='font-black text-indigo-500'>$subj:</span> $detailStr</div>";
    }
    $html .= '</div>';
    return $html;
}
?>

<!-- Premium Stats Dashboard -->
<div class="mb-10 animate-fadeIn overflow-x-auto">
    <div class="min-w-[800px] glass-effect rounded-[2.5rem] p-8 border border-white/40 shadow-xl shadow-indigo-500/5">
        <div class="flex items-center gap-6 mb-8 border-b border-slate-100 dark:border-slate-800 pb-6">
            <div class="w-16 h-16 bg-indigo-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">คัดกรอง 11 ด้าน: สรุปภาพรวมห้อง <?= "$class/$room" ?></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Students: <?= $totalStudents ?></p>
            </div>
        </div>

        <div class="grid grid-cols-11 gap-2">
            <?php foreach ($screenFields as $key => $label): 
                $sum = $summary[$key] ?? ['normal' => 0, 'risk' => 0, 'problem' => 0];
                $totalS = array_sum($sum);
                $problemCount = ($sum['risk'] ?? 0) + ($sum['problem'] ?? 0);
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
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">นักเรียน</th>
                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รหัส/เลขที่</th>
                <?php foreach ($screenFields as $label): ?>
                    <th class="px-2 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest italic text-center"><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-700 dark:text-slate-300">
            <?php if ($totalStudents > 0): ?>
                <?php foreach ($students as $stu):
                    $data = $screen->getScreeningDataByStudentId($stu['Stu_id'], $pee);
                ?>
                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                    <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="นักเรียน">
                        <div class="text-[13px] font-black text-slate-800 dark:text-white"><?= $stu['full_name'] ?></div>
                    </td>
                    <td class="px-4 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="รหัส/เลขที่">
                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-black text-slate-400">ID: <?= $stu['Stu_id'] ?></span>
                            <span class="text-[10px] font-black text-indigo-500 italic">No. <?= $stu['Stu_no'] ?></span>
                        </div>
                    </td>
                    <?php foreach ($screenFields as $key => $label): 
                        $val = '-';
                        $color = 'slate';
                        $bg = 'slate-50';
                        $subText = '';

                        if ($key === 'special_ability') {
                            $val = $data['special_ability'] ?? '-';
                            $color = 'indigo';
                            $subText = !empty($data['special_ability_detail']) ? showSpecialAbilityDetail($data['special_ability_detail']) : '';
                        } elseif ($key === 'special_need') {
                            $st = interpretStatus($data['special_need_status'] ?? null);
                            $val = $st['text'];
                            $color = $st['color'];
                            if (!empty($data['special_need_type'])) $subText = '<div class="mt-1 text-[9px] text-blue-500">' . showValue($data['special_need_type']) . '</div>';
                        } else {
                            $st = interpretStatus($data[$key . '_status'] ?? null);
                            $val = $st['text'];
                            $color = $st['color'];
                            if (($data[$key . '_status'] ?? null) === 'เสี่ยง' && !empty($data[$key . '_risk'])) {
                                $subText = '<div class="text-[9px] text-amber-500">' . showValue($data[$key . '_risk']) . '</div>';
                            } elseif (($data[$key . '_status'] ?? null) === 'มีปัญหา' && !empty($data[$key . '_problem'])) {
                                $subText = '<div class="text-[9px] text-rose-500">' . showValue($data[$key . '_problem']) . '</div>';
                            }
                        }
                    ?>
                    <td class="px-2 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="<?= $label ?>">
                        <div class="flex flex-col items-center min-w-[80px]">
                            <span class="text-[10px] font-black text-<?= $color ?>-600 uppercase tracking-widest italic"><?= $val ?></span>
                            <?= $subText ?>
                        </div>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="px-6 py-20 text-center bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                        <p class="text-sm font-bold text-slate-400 italic">ไม่พบข้อมูลการคัดกรองสำหรับห้องนี้</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
