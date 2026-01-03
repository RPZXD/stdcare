<?php
/**
 * Sub-View: White Class (ห้องเรียนสีขาว) Overall Report (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 */
require_once("../config/Database.php");
require_once("../class/Wroom.php");
require_once("../class/Teacher.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$wroomObj = new Wroom($db);
$teacherObj = new Teacher($db);

// ดึงปีการศึกษาล่าสุด
$pee = date('Y') + 543;

// ดึงรายชื่อห้องทั้งหมด
$rooms = [];
$stmt = $db->query("SELECT Stu_major, Stu_room FROM student WHERE Stu_status=1 GROUP BY Stu_major, Stu_room ORDER BY Stu_major, Stu_room");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rooms[] = $row;
}

// Calculate some stats
$totalRooms = count($rooms);
$completedRooms = 0;
$processedRooms = [];

foreach($rooms as $r) {
    $class = $r['Stu_major'];
    $room = $r['Stu_room'];
    $advisors = $teacherObj->getTeachersByClassAndRoom($class, $room);
    $advisorsStr = implode(', ', array_map(fn($a) => $a['Teach_name'], $advisors));
    $wroom = $wroomObj->getWroomStudents($class, $room, $pee);
    $maxim = $wroomObj->getMaxim($class, $room, $pee);
    $committeeCount = count(array_filter($wroom, fn($w) => $w['wposit'] != ''));
    $isComplete = ($committeeCount >= 18 && $maxim);
    if ($isComplete) $completedRooms++;
    
    $processedRooms[] = [
        'class' => $class,
        'room' => $room,
        'advisors' => $advisorsStr,
        'committeeCount' => $committeeCount,
        'hasMaxim' => (bool)$maxim,
        'isComplete' => $isComplete
    ];
}

$percentComplete = $totalRooms > 0 ? round(($completedRooms / $totalRooms) * 100) : 0;
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-sky-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-shield-alt"></i>
                </span>
                รายงานสรุป <span class="text-sky-600 italic">ห้องเรียนสีขาว</span> (ภาพรวม)
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">White Classroom Summary • Academic Year <?= $pee ?></p>
        </div>
        
        <div class="flex gap-2 no-print">
            <button onclick="window.printReport ? window.printReport() : window.print()" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-sky-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <!-- Premium Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-sm flex flex-col items-center text-center group hover:scale-105 transition-all outline outline-transparent hover:outline-sky-500/20">
            <span class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2 italic">ห้องเรียนทั้งหมด</span>
            <span class="text-4xl font-black text-slate-800 dark:text-white"><?= $totalRooms ?></span>
            <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Classrooms</span>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-sm flex flex-col items-center text-center group hover:scale-105 transition-all outline outline-transparent hover:outline-emerald-500/20">
            <span class="text-sm font-black text-emerald-500 uppercase tracking-widest mb-2 italic">ดำเนินการครบถ้วน</span>
            <span class="text-4xl font-black text-emerald-600"><?= $completedRooms ?></span>
            <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Completed</span>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-sm flex flex-col items-center text-center group hover:scale-105 transition-all outline outline-transparent hover:outline-sky-500/20">
            <span class="text-sm font-black text-sky-500 uppercase tracking-widest mb-2 italic">ร้อยละความสำเร็จ</span>
            <div class="relative w-16 h-16 flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="6" fill="transparent" stroke-dasharray="175.9" stroke-dashoffset="<?= 175.9 * (1 - $percentComplete/100) ?>" class="text-sky-500 transition-all duration-1000" stroke-linecap="round" />
                </svg>
                <span class="absolute text-sm font-black text-slate-800 dark:text-white"><?= $percentComplete ?>%</span>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl"># / ห้อง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ครูที่ปรึกษา</th>
                    <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">กรรมการ (คน)</th>
                    <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คติพจน์</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody class="font-bold text-slate-700 dark:text-slate-300">
                <?php foreach($processedRooms as $idx => $r): ?>
                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                    <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="# / ห้อง">
                        <div class="flex items-center gap-4">
                            <span class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-[10px] font-black italic"><?= $idx + 1 ?></span>
                            <div class="text-[14px] font-black text-slate-800 dark:text-white">ม.<?= $r['class'] ?>/<?= $r['room'] ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800" data-label="ครูที่ปรึกษา">
                        <span class="text-[13px] font-black text-slate-500 italic"><?= $r['advisors'] ?: '-' ?></span>
                    </td>
                    <td class="px-4 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center text-sky-600" data-label="กรรมการ (คน)">
                        <span class="text-sm font-black"><?= $r['committeeCount'] ?></span>
                    </td>
                    <td class="px-4 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="คติพจน์">
                        <?php if ($r['hasMaxim']): ?>
                            <span class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto shadow-sm">
                                <i class="fas fa-check"></i>
                            </span>
                        <?php else: ?>
                            <span class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
                                <i class="fas fa-times"></i>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center px-4" data-label="สถานะ">
                        <?php if ($r['isComplete']): ?>
                            <span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                ครบถ้วน
                            </span>
                        <?php else: ?>
                            <span class="px-4 py-1.5 bg-rose-500/10 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-500/20 text-nowrap">
                                ไม่ครบ
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-8 p-6 glass-effect rounded-[2rem] border border-white/50 bg-slate-50/50">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 italic">
            <i class="fas fa-info-circle text-sky-500"></i>
            หมายเหตุ: สถานะ "ครบถ้วน" หมายถึงมีคณะกรรมการครบ 18 คน และกรอกคติพจน์ห้องเรียนเรียบร้อยแล้ว
        </p>
    </div>
</div>
