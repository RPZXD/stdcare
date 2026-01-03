<?php
/**
 * EQ Assessment Result Template
 * Modern UI with Tailwind CSS
 */
require_once '../../class/EQ.php';
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
$eq = new EQ($db);
$eqData = $eq->getEQData($student_id, $pee, $term);

if (!$eqData) {
    echo '<div class="p-8 text-center text-slate-400"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="font-bold">ไม่พบข้อมูลการประเมิน</p></div>';
    exit;
}

$eqStructure = [
    'ดี' => [
        'icon' => 'fa-smile-beam',
        'color' => 'from-emerald-500 to-teal-600',
        'subs' => [
            ['label' => '1.1 ควบคุมตนเอง', 'range' => [13, 17], 'items' => range(1, 6)],
            ['label' => '1.2 เห็นใจผู้อื่น', 'range' => [16, 20], 'items' => range(7, 12)],
            ['label' => '1.3 รับผิดชอบ', 'range' => [16, 22], 'items' => range(13, 18)],
            ['label' => 'รวมองค์ประกอบดี', 'range' => [48, 58], 'items' => range(1, 18), 'is_main' => true],
        ]
    ],
    'เก่ง' => [
        'icon' => 'fa-brain',
        'color' => 'from-blue-500 to-indigo-600',
        'subs' => [
            ['label' => '2.1 มีแรงจูงใจ', 'range' => [14, 20], 'items' => range(19, 24)],
            ['label' => '2.2 ตัดสินใจแก้ปัญหา', 'range' => [13, 19], 'items' => range(25, 30)],
            ['label' => '2.3 สัมพันธภาพ', 'range' => [14, 20], 'items' => range(31, 36)],
            ['label' => 'รวมองค์ประกอบเก่ง', 'range' => [45, 57], 'items' => range(19, 36), 'is_main' => true],
        ]
    ],
    'สุข' => [
        'icon' => 'fa-heart',
        'color' => 'from-rose-500 to-pink-600',
        'subs' => [
            ['label' => '3.1 ภูมิใจในตนเอง', 'range' => [9, 13], 'items' => range(37, 40)],
            ['label' => '3.2 พอใจชีวิต', 'range' => [16, 22], 'items' => range(41, 46)],
            ['label' => '3.3 สุขสงบทางใจ', 'range' => [15, 21], 'items' => range(47, 52)],
            ['label' => 'รวมองค์ประกอบสุข', 'range' => [40, 45], 'items' => range(37, 52), 'is_main' => true],
        ]
    ],
];

$totalEQ = 0;
for ($i = 1; $i <= 52; $i++) {
    $totalEQ += isset($eqData["EQ$i"]) ? (int)$eqData["EQ$i"] : 0;
}

function eqResult($score, $range) {
    if ($score > $range[1]) return 'สูงกว่าปกติ';
    if ($score >= $range[0]) return 'เกณฑ์ปกติ';
    return 'ต่ำกว่าปกติ';
}

function eqColorClass($result) {
    return match($result) {
        'สูงกว่าปกติ' => 'bg-emerald-500 shadow-emerald-500/30',
        'เกณฑ์ปกติ' => 'bg-blue-500 shadow-blue-500/30',
        default => 'bg-rose-500 shadow-rose-500/30'
    };
}

function eqTextClass($result) {
    return match($result) {
        'สูงกว่าปกติ' => 'text-emerald-600',
        'เกณฑ์ปกติ' => 'text-blue-600',
        default => 'text-rose-600'
    };
}

function eqLevel($score) {
    return match(true) {
        $score >= 170 => 'อัจฉริยะทางอารมณ์',
        $score >= 140 => 'ดีมาก',
        $score >= 100 => 'ปานกลาง',
        default => 'ต้องได้รับการส่งเสริม'
    };
}
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Student Info Card -->
        <div class="flex-1 bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2rem] p-6 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black"><?= htmlspecialchars($student_name) ?></h3>
                        <p class="text-slate-400 text-sm">มัธยมศึกษาปีที่ <?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">เลขประจำตัว</p>
                        <p class="text-lg font-black"><?= htmlspecialchars($student_id) ?></p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">เลขที่</p>
                        <p class="text-lg font-black"><?= htmlspecialchars($student_no) ?></p>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Total Score Card -->
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-700 shadow-xl flex flex-col items-center justify-center text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">คะแนนรวมความฉลาดทางอารมณ์</p>
            <div class="relative mb-4">
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100 dark:text-slate-700"></circle>
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" 
                        stroke-dasharray="364.4" 
                        stroke-dashoffset="<?= 364.4 - (364.4 * ($totalEQ / 156)) ?>"
                        class="text-indigo-500 transition-all duration-1000"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-4xl font-black text-slate-800 dark:text-white"><?= $totalEQ ?></span>
                </div>
            </div>
            <p class="text-xl font-black <?= eqTextClass(eqLevel($totalEQ)) ?> mb-1"><?= eqLevel($totalEQ) ?></p>
            <p class="text-xs text-slate-400">(จากคะแนนเต็ม 156 คะแนน)</p>
        </div>
    </div>

    <!-- Results Breakdown -->
    <div class="grid grid-cols-1 gap-6">
        <?php foreach ($eqStructure as $mainTitle => $mainInfo): ?>
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden">
            <!-- Main Header -->
            <div class="px-6 py-4 bg-gradient-to-r <?= $mainInfo['color'] ?> text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas <?= $mainInfo['icon'] ?>"></i>
                    </div>
                    <h4 class="text-lg font-black uppercase tracking-wide">ด้าน<?= $mainTitle ?></h4>
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <th class="pb-3 px-2 font-bold uppercase tracking-widest text-[10px]">ด้านที่ประเมิน</th>
                            <th class="pb-3 px-2 text-center font-bold uppercase tracking-widest text-[10px] w-24">ช่วงคะแนน</th>
                            <th class="pb-3 px-2 text-center font-bold uppercase tracking-widest text-[10px] w-20">คะแนน</th>
                            <th class="pb-3 px-2 text-center font-bold uppercase tracking-widest text-[10px] w-32">ผลการแปลผล</th>
                            <th class="pb-3 px-2 font-bold uppercase tracking-widest text-[10px]">ระดับคะแนนเทียบร้อยละ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        <?php foreach ($mainInfo['subs'] as $sub): 
                            $score = 0;
                            foreach ($sub['items'] as $q) {
                                $score += isset($eqData["EQ$q"]) ? (int)$eqData["EQ$q"] : 0;
                            }
                            $result = eqResult($score, $sub['range']);
                            $colorClass = eqColorClass($result);
                            $maxSubScore = count($sub['items']) * 3;
                            $percent = round(($score / $maxSubScore) * 100);
                            $isMainRow = isset($sub['is_main']) && $sub['is_main'];
                        ?>
                        <tr class="<?= $isMainRow ? 'bg-slate-50/50 dark:bg-slate-900/30' : '' ?>">
                            <td class="py-4 px-2">
                                <span class="<?= $isMainRow ? 'font-black text-slate-800 dark:text-white underline' : 'font-bold text-slate-600 dark:text-slate-400' ?>">
                                    <?= $sub['label'] ?>
                                </span>
                            </td>
                            <td class="py-4 px-2 text-center text-slate-500 dark:text-slate-400 italic">
                                <?= $sub['range'][0] ?> - <?= $sub['range'][1] ?>
                            </td>
                            <td class="py-4 px-2 text-center font-black text-slate-800 dark:text-white">
                                <?= $score ?>
                            </td>
                            <td class="py-4 px-2 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black <?= $colorClass ?> text-white shadow-sm">
                                    <?= $result ?>
                                </span>
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                        <div class="h-full <?= $colorClass ?> transition-all duration-1000" style="width: <?= $percent ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-400 w-8"><?= $percent ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Print Footer Info -->
    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 text-center print:block hidden">
         <div class="grid grid-cols-2 gap-8 px-12 mt-12 mb-8">
            <div class="text-center">
                <p class="mb-16">ลงชื่อ...........................................</p>
                <p class="font-bold">( <?= htmlspecialchars($student_name) ?> )</p>
                <p class="text-sm text-slate-500">นักเรียนผู้ประเมิน</p>
            </div>
            <div class="text-center">
                <p class="mb-16">ลงชื่อ...........................................</p>
                <p class="font-bold">( ........................................ )</p>
                <p class="text-sm text-slate-500">คุณครูที่ปรึกษา</p>
            </div>
         </div>
    </div>
</div>
