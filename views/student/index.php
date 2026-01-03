<?php
/**
 * View: Student Dashboard
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();

// Get behavior score class
if ($behavior_score < 50) {
    $score_class = "text-red-500";
    $score_bg = "from-red-500 to-rose-600";
    $score_ring = "ring-red-500/30";
} elseif ($behavior_score >= 50 && $behavior_score <= 70) {
    $score_class = "text-amber-500";
    $score_bg = "from-amber-500 to-orange-600";
    $score_ring = "ring-amber-500/30";
} elseif ($behavior_score >= 71 && $behavior_score <= 99) {
    $score_class = "text-blue-500";
    $score_bg = "from-blue-500 to-indigo-600";
    $score_ring = "ring-blue-500/30";
} else {
    $score_class = "text-emerald-500";
    $score_bg = "from-emerald-500 to-green-600";
    $score_ring = "ring-emerald-500/30";
}

$imgPath = isset($student['Stu_picture']) && $student['Stu_picture'] 
    ? "https://std.phichai.ac.th/photo/{$student['Stu_picture']}" 
    : '../dist/img/default-avatar.svg';
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Welcome Hero Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 md:p-10 shadow-2xl">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">
            <!-- Profile Image -->
            <div class="relative group">
                <div class="w-28 h-28 md:w-36 md:h-36 rounded-[1.5rem] md:rounded-[2rem] overflow-hidden border-4 border-white/30 shadow-2xl bg-white/20 backdrop-blur-sm group-hover:scale-105 transition-transform duration-300">
                    <img src="<?= $imgPath ?>" alt="Avatar" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-400 rounded-xl flex items-center justify-center text-white shadow-lg border-2 border-white">
                    <i class="fas fa-check text-sm"></i>
                </div>
            </div>
            
            <!-- Welcome Text -->
            <div class="text-center md:text-left">
                <p class="text-blue-200 text-sm font-bold uppercase tracking-widest mb-2">
                    <i class="fas fa-hand-wave mr-1"></i> ยินดีต้อนรับ
                </p>
                <h1 class="text-2xl md:text-4xl font-black text-white mb-2 leading-tight">
                    <?= htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']) ?>
                </h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-blue-100">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                        <i class="fas fa-id-badge mr-1"></i> <?= htmlspecialchars($student['Stu_id']) ?>
                    </span>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                        <i class="fas fa-school mr-1"></i> ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?>
                    </span>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                        <i class="fas fa-calendar mr-1"></i> เทอม <?= $term ?>/<?= $pee ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview - Mobile Card / Desktop Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Behavior Score Card -->
        <div class="col-span-2 glass-effect rounded-[1.5rem] p-5 border border-white/50 shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br <?= $score_bg ?> opacity-10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform"></div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br <?= $score_bg ?> flex items-center justify-center text-white shadow-lg ring-4 <?= $score_ring ?> flex-shrink-0">
                    <span class="text-3xl font-black"><?= $behavior_score ?></span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">คะแนนพฤติกรรมสุทธิ</p>
                        <p class="text-lg font-black <?= $score_class ?>">
                            <?php if ($behavior_score >= 100): ?>ยอดเยี่ยม! 🌟
                            <?php elseif ($behavior_score >= 71): ?>ดี 👍
                            <?php elseif ($behavior_score >= 50): ?>ปานกลาง ⚠️
                            <?php else: ?>ต้องปรับปรุง 🔴<?php endif; ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800">
                            <i class="fas fa-minus-circle text-red-500"></i>
                            <span class="text-xs font-bold text-red-600 dark:text-red-400">หัก <?= $behavior_deduction ?></span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-100 dark:border-teal-800">
                            <i class="fas fa-plus-circle text-teal-500"></i>
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400">จิตอาสา +<?= $behavior_bonus ?></span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 rounded-lg">
                            <i class="fas fa-calculator text-slate-500"></i>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">100 - <?= $behavior_deduction ?> + <?= $behavior_bonus ?> = <?= $behavior_score ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Card -->
        <div class="glass-effect rounded-[1.5rem] p-5 border border-white/50 shadow-xl hover:shadow-2xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">มาเรียน</p>
                    <p class="text-2xl font-black text-emerald-600"><?= $attendance_stats['1'] ?> <span class="text-sm text-slate-400">วัน</span></p>
                </div>
            </div>
        </div>
        
        <!-- Late Card -->
        <div class="glass-effect rounded-[1.5rem] p-5 border border-white/50 shadow-xl hover:shadow-2xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">มาสาย</p>
                    <p class="text-2xl font-black text-amber-600"><?= $attendance_stats['3'] ?> <span class="text-sm text-slate-400">ครั้ง</span></p>
                </div>
            </div>
        </div>
        
        <!-- Absent Card -->
        <div class="glass-effect rounded-[1.5rem] p-5 border border-white/50 shadow-xl hover:shadow-2xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-times text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ขาดเรียน</p>
                    <p class="text-2xl font-black text-red-600"><?= $attendance_stats['2'] ?> <span class="text-sm text-slate-400">วัน</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards - Desktop Table / Mobile Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Student Info Card -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-user-graduate text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">ข้อมูลนักเรียน</h3>
                    <p class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Student Information</p>
                </div>
            </div>
            
            <!-- Desktop: Table View -->
            <div class="hidden md:block">
                <table class="w-full">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 w-1/3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัสนักเรียน</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_id']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_pre'] ?? '') . htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ชั้น/ห้อง</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white">ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขที่</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_no'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-blue-600"><?= htmlspecialchars($student['Stu_phone'] ?? '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile: Card View -->
            <div class="md:hidden p-4 space-y-3">
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">รหัสนักเรียน</p>
                        <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_id']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</p>
                        <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ชั้น/ห้อง • เลขที่</p>
                        <p class="font-bold text-slate-700 dark:text-white">ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?> เลขที่ <?= htmlspecialchars($student['Stu_no'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 text-sky-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</p>
                        <p class="font-bold text-blue-600"><?= htmlspecialchars($student['Stu_phone'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Info Card -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">ข้อมูลผู้ปกครอง</h3>
                    <p class="text-[10px] font-bold text-teal-200 uppercase tracking-widest">Parent Information</p>
                </div>
            </div>
            
            <!-- Desktop: Table View -->
            <div class="hidden md:block">
                <table class="w-full">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 w-1/3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อผู้ปกครอง</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_name'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ความสัมพันธ์</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_relation'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-teal-600"><?= htmlspecialchars($student['Par_phone'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ที่อยู่</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white text-sm"><?= htmlspecialchars($student['Par_addr'] ?? '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile: Card View -->
            <div class="md:hidden p-4 space-y-3">
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ชื่อผู้ปกครอง</p>
                        <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_name'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ความสัมพันธ์</p>
                        <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_relation'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</p>
                        <p class="font-bold text-teal-600"><?= htmlspecialchars($student['Par_phone'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ที่อยู่</p>
                        <p class="font-bold text-slate-700 dark:text-white text-sm"><?= htmlspecialchars($student['Par_addr'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Details -->
    <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-calendar-check text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">สรุปการมาเรียน</h3>
                    <p class="text-[10px] font-bold text-violet-200 uppercase tracking-widest">เทอม <?= $term ?>/<?= $pee ?></p>
                </div>
            </div>
            <a href="std_checktime.php" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-white font-bold text-sm transition-all flex items-center gap-2">
                <span class="hidden sm:inline">ดูรายละเอียด</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="p-5">
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4">
                <div class="text-center p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 hover:scale-105 transition-all border border-emerald-100 dark:border-emerald-800">
                    <span class="text-3xl mb-2 block">✅</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">มาเรียน</p>
                    <p class="text-2xl font-black text-emerald-600"><?= $attendance_stats['1'] ?></p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 hover:scale-105 transition-all border border-red-100 dark:border-red-800">
                    <span class="text-3xl mb-2 block">❌</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ขาดเรียน</p>
                    <p class="text-2xl font-black text-red-600"><?= $attendance_stats['2'] ?></p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 hover:scale-105 transition-all border border-amber-100 dark:border-amber-800">
                    <span class="text-3xl mb-2 block">⏰</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">มาสาย</p>
                    <p class="text-2xl font-black text-amber-600"><?= $attendance_stats['3'] ?></p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-orange-50 dark:bg-orange-900/20 hover:scale-105 transition-all border border-orange-100 dark:border-orange-800">
                    <span class="text-3xl mb-2 block">🤒</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ลาป่วย</p>
                    <p class="text-2xl font-black text-orange-600"><?= $attendance_stats['4'] ?></p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 hover:scale-105 transition-all border border-blue-100 dark:border-blue-800">
                    <span class="text-3xl mb-2 block">📝</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ลากิจ</p>
                    <p class="text-2xl font-black text-blue-600"><?= $attendance_stats['5'] ?></p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/20 hover:scale-105 transition-all border border-purple-100 dark:border-purple-800">
                    <span class="text-3xl mb-2 block">🎉</span>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">กิจกรรม</p>
                    <p class="text-2xl font-black text-purple-600"><?= $attendance_stats['6'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2">
                <span class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-rocket"></i>
                </span>
                เมนูลัด
            </h4>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="std_information.php" class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">ข้อมูลนักเรียน</p>
            </a>
            <a href="std_checktime.php" class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">เวลาเรียน</p>
            </a>
            <a href="std_behavior.php" class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">พฤติกรรม</p>
            </a>
            <a href="std_sdq.php" class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">SDQ</p>
            </a>
            <a href="std_eq.php" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-heart text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">EQ</p>
            </a>
            <a href="std_visit_home.php" class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-900/20 border border-teal-100 dark:border-teal-800 hover:scale-105 hover:shadow-lg transition-all text-center group">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                    <i class="fas fa-home text-xl"></i>
                </div>
                <p class="font-bold text-slate-700 dark:text-white text-sm">เยี่ยมบ้าน</p>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
