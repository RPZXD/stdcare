<?php
$pageTitle = $title ?? 'สถิติการมาเรียน';

function thaiDateShort($date) {
    $months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}

$statusConfig = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'gradient' => 'from-emerald-400 to-green-500', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-600', 'border' => 'border-emerald-300'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'gradient' => 'from-rose-400 to-red-500', 'bg' => 'bg-rose-500/10', 'text' => 'text-rose-600', 'border' => 'border-rose-300'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'gradient' => 'from-amber-400 to-orange-500', 'bg' => 'bg-amber-500/10', 'text' => 'text-amber-600', 'border' => 'border-amber-300'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'gradient' => 'from-sky-400 to-blue-500', 'bg' => 'bg-sky-500/10', 'text' => 'text-sky-600', 'border' => 'border-sky-300'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'gradient' => 'from-violet-400 to-purple-500', 'bg' => 'bg-violet-500/10', 'text' => 'text-violet-600', 'border' => 'border-violet-300'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'gradient' => 'from-pink-400 to-rose-500', 'bg' => 'bg-pink-500/10', 'text' => 'text-pink-600', 'border' => 'border-pink-300'],
];

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.7);
    }
    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
    }
    .glow-emerald { box-shadow: 0 20px 40px -12px rgba(16, 185, 129, 0.35); }
    .glow-blue { box-shadow: 0 20px 40px -12px rgba(59, 130, 246, 0.35); }
    .glow-purple { box-shadow: 0 20px 40px -12px rgba(139, 92, 246, 0.35); }
    .glow-orange { box-shadow: 0 20px 40px -12px rgba(249, 115, 22, 0.35); }
    .glow-teal { box-shadow: 0 20px 40px -12px rgba(20, 184, 166, 0.35); }
    .glow-amber { box-shadow: 0 20px 40px -12px rgba(245, 158, 11, 0.35); }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .chart-container {
        position: relative;
        min-height: 300px;
    }
    @media (max-width: 768px) {
        .chart-container {
            min-height: 250px;
        }
    }
</style>

<!-- Hero Header Section -->
<div class="relative mb-6 md:mb-8 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-teal-500/20 to-emerald-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 md:gap-6">
            <!-- Left: Title -->
            <div class="flex items-center gap-3 md:gap-5">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl md:rounded-2xl blur-lg opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-500 to-pink-600 text-white p-3 md:p-5 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-chart-bar text-xl md:text-3xl floating-icon"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-tight">
                        📊 สถิติการมาเรียน
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm md:text-base flex items-center gap-2 mt-1">
                        <i class="far fa-calendar-alt text-purple-500"></i>
                        <span>วันที่ <?php echo htmlspecialchars(thaiDateShort($date)); ?></span>
                    </p>
                </div>
            </div>
            
            <!-- Right: Date Picker & Export -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <form method="get" class="flex items-center gap-2 bg-white/50 dark:bg-slate-800/50 px-3 py-2 rounded-xl border border-slate-200/50 dark:border-slate-600/50">
                    <i class="far fa-calendar-alt text-purple-500"></i>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" 
                           class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 font-bold text-sm w-full">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all active:scale-95 whitespace-nowrap">
                        <i class="fas fa-search mr-1"></i> ดูข้อมูล
                    </button>
                </form>
                
                <!-- Export Buttons -->
                <div class="flex gap-2">
                    <button onclick="exportToExcel()" class="flex-1 sm:flex-none bg-gradient-to-r from-emerald-500 to-green-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-file-excel"></i>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button onclick="exportToPNG()" class="flex-1 sm:flex-none bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-image"></i>
                        <span class="hidden sm:inline">PNG</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
    <!-- วันนี้ -->
    <div class="stat-card bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-emerald">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-calendar-day text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2">วันที่เลือก</p>
            <p class="text-2xl md:text-4xl font-black mb-1"><?php echo number_format($attendanceStats['today']['present'] ?? 0); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2">
                สาย <?php echo $attendanceStats['today']['late'] ?? 0; ?> • ขาด <?php echo $attendanceStats['today']['absent'] ?? 0; ?>
            </p>
            <div class="pt-2 border-t border-white/20">
                <span class="text-lg md:text-2xl font-black">
                    <?php echo ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0; ?>%
                </span>
            </div>
        </div>
    </div>

    <!-- สัปดาห์นี้ -->
    <div class="stat-card bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-blue">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-calendar-week text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2">สัปดาห์นี้</p>
            <p class="text-2xl md:text-4xl font-black mb-1"><?php echo number_format($attendanceStats['week']['present'] ?? 0); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2">
                สาย <?php echo $attendanceStats['week']['late'] ?? 0; ?> • ขาด <?php echo $attendanceStats['week']['absent'] ?? 0; ?>
            </p>
            <div class="pt-2 border-t border-white/20">
                <span class="text-lg md:text-2xl font-black">
                    <?php echo ($attendanceStats['week']['total'] ?? 0) > 0 ? round((($attendanceStats['week']['present'] ?? 0) / $attendanceStats['week']['total']) * 100, 1) : 0; ?>%
                </span>
            </div>
        </div>
    </div>

    <!-- เดือนนี้ -->
    <div class="stat-card bg-gradient-to-br from-violet-500 via-purple-600 to-fuchsia-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-purple">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-chart-bar text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2">เดือนนี้</p>
            <p class="text-2xl md:text-4xl font-black mb-1"><?php echo number_format($attendanceStats['month']['present'] ?? 0); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2">
                สาย <?php echo $attendanceStats['month']['late'] ?? 0; ?> • ขาด <?php echo $attendanceStats['month']['absent'] ?? 0; ?>
            </p>
            <div class="pt-2 border-t border-white/20">
                <span class="text-lg md:text-2xl font-black">
                    <?php echo ($attendanceStats['month']['total'] ?? 0) > 0 ? round((($attendanceStats['month']['present'] ?? 0) / $attendanceStats['month']['total']) * 100, 1) : 0; ?>%
                </span>
            </div>
        </div>
    </div>

    <!-- เป้าหมาย -->
    <?php $avg = ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0; ?>
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-orange-500/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-orange-400 to-rose-500 rounded-xl flex items-center justify-center text-white">
            <i class="fas fa-bullseye text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400 mb-2">อัตราเฉลี่ย</p>
            <p class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo $avg; ?>%</p>
            <p class="text-[10px] md:text-xs font-semibold text-slate-500 mb-2">เป้าหมาย: 95%</p>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-400 to-rose-500 h-full rounded-full transition-all duration-1000 relative" style="width: <?php echo min($avg, 100); ?>%">
                        <div class="absolute inset-0 shimmer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Count Cards -->
<div class="grid grid-cols-3 gap-3 md:gap-6 mb-6 md:mb-8">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl stat-card text-center">
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg">
            <i class="fas fa-user-graduate text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo number_format($studentCounts['total']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">นักเรียนทั้งหมด</p>
    </div>
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl stat-card text-center">
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-teal-400 to-emerald-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg">
            <i class="fas fa-book-reader text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo number_format($studentCounts['junior']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">ม.ต้น</p>
        <p class="text-[8px] md:text-xs text-slate-400 mt-1"><?php echo $studentCounts['total'] > 0 ? round(($studentCounts['junior']/$studentCounts['total'])*100, 1) : 0; ?>%</p>
    </div>
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl stat-card text-center">
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg">
            <i class="fas fa-user-tie text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo number_format($studentCounts['senior']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">ม.ปลาย</p>
        <p class="text-[8px] md:text-xs text-slate-400 mt-1"><?php echo $studentCounts['total'] > 0 ? round(($studentCounts['senior']/$studentCounts['total'])*100, 1) : 0; ?>%</p>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8 mb-6 md:mb-8">
    <!-- Junior High Chart -->
    <div class="glass-card rounded-2xl md:rounded-3xl border border-white/30 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-emerald-600 text-white px-4 md:px-6 py-3 md:py-4">
            <h3 class="text-base md:text-xl font-black flex items-center gap-2">
                <span class="text-xl md:text-2xl">📚</span>
                มัธยมศึกษาตอนต้น (ม.1-3)
            </h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="chart-container">
                <canvas id="barChart1"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Senior High Chart -->
    <div class="glass-card rounded-2xl md:rounded-3xl border border-white/30 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 text-white px-4 md:px-6 py-3 md:py-4">
            <h3 class="text-base md:text-xl font-black flex items-center gap-2">
                <span class="text-xl md:text-2xl">🎯</span>
                มัธยมศึกษาตอนปลาย (ม.4-6)
            </h3>
        </div>
        <div class="p-4 md:p-6">
            <div class="chart-container">
                <canvas id="barChart2"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
    <!-- Junior High Details -->
    <div class="glass-card rounded-2xl md:rounded-3xl border border-white/30 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-teal-400 to-teal-500 text-white px-4 md:px-6 py-3 md:py-4">
            <h3 class="text-base md:text-xl font-black flex items-center gap-2">
                <span class="text-xl md:text-2xl">📚</span>
                สถิติรายละเอียด (มัธยมต้น)
            </h3>
        </div>
        <div class="p-4 md:p-6">
            <?php 
            $totalJunior = array_sum(array_column($juniorStats, 'total'));
            if (count($juniorStats) > 0): ?>
            <div class="space-y-2 md:space-y-3">
                <?php foreach ($juniorStats as $row): 
                    $percentage = $totalJunior > 0 ? round(($row['total'] / $totalJunior) * 100, 1) : 0;
                    $config = $statusConfig[$row['attendance_status']] ?? ['emoji' => '📌', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300'];
                ?>
                <div class="flex items-center justify-between p-3 md:p-4 <?php echo $config['bg']; ?> dark:bg-slate-700/50 rounded-xl md:rounded-2xl border-2 <?php echo $config['border']; ?> dark:border-slate-600 stat-card">
                    <div class="flex items-center gap-2 md:gap-3">
                        <span class="text-xl md:text-3xl"><?php echo $config['emoji']; ?></span>
                        <span class="font-bold <?php echo $config['text']; ?> dark:text-white text-sm md:text-base"><?php echo htmlspecialchars($row['status_name']); ?></span>
                    </div>
                    <div class="text-right">
                        <div class="text-lg md:text-2xl font-black <?php echo $config['text']; ?> dark:text-white"><?php echo number_format($row['total']); ?></div>
                        <div class="text-[10px] md:text-xs font-bold text-slate-400"><?php echo $percentage; ?>%</div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="mt-3 md:mt-4 p-3 md:p-4 bg-teal-50 dark:bg-teal-900/20 border-2 border-teal-300 dark:border-teal-700 rounded-xl md:rounded-2xl">
                    <div class="flex justify-between items-center">
                        <span class="font-black text-teal-700 dark:text-teal-300 text-sm md:text-base">รวมทั้งหมด</span>
                        <span class="text-xl md:text-2xl font-black text-teal-700 dark:text-teal-300"><?php echo number_format($totalJunior); ?> คน</span>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-8 md:py-12 text-slate-400">
                <span class="text-4xl md:text-6xl block mb-3">📭</span>
                <p class="font-bold">ยังไม่มีข้อมูลการเช็คชื่อ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Senior High Details -->
    <div class="glass-card rounded-2xl md:rounded-3xl border border-white/30 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-white px-4 md:px-6 py-3 md:py-4">
            <h3 class="text-base md:text-xl font-black flex items-center gap-2">
                <span class="text-xl md:text-2xl">🎯</span>
                สถิติรายละเอียด (มัธยมปลาย)
            </h3>
        </div>
        <div class="p-4 md:p-6">
            <?php 
            $totalSenior = array_sum(array_column($seniorStats, 'total'));
            if (count($seniorStats) > 0): ?>
            <div class="space-y-2 md:space-y-3">
                <?php foreach ($seniorStats as $row): 
                    $percentage = $totalSenior > 0 ? round(($row['total'] / $totalSenior) * 100, 1) : 0;
                    $config = $statusConfig[$row['attendance_status']] ?? ['emoji' => '📌', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300'];
                ?>
                <div class="flex items-center justify-between p-3 md:p-4 <?php echo $config['bg']; ?> dark:bg-slate-700/50 rounded-xl md:rounded-2xl border-2 <?php echo $config['border']; ?> dark:border-slate-600 stat-card">
                    <div class="flex items-center gap-2 md:gap-3">
                        <span class="text-xl md:text-3xl"><?php echo $config['emoji']; ?></span>
                        <span class="font-bold <?php echo $config['text']; ?> dark:text-white text-sm md:text-base"><?php echo htmlspecialchars($row['status_name']); ?></span>
                    </div>
                    <div class="text-right">
                        <div class="text-lg md:text-2xl font-black <?php echo $config['text']; ?> dark:text-white"><?php echo number_format($row['total']); ?></div>
                        <div class="text-[10px] md:text-xs font-bold text-slate-400"><?php echo $percentage; ?>%</div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="mt-3 md:mt-4 p-3 md:p-4 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 rounded-xl md:rounded-2xl">
                    <div class="flex justify-between items-center">
                        <span class="font-black text-amber-700 dark:text-amber-300 text-sm md:text-base">รวมทั้งหมด</span>
                        <span class="text-xl md:text-2xl font-black text-amber-700 dark:text-amber-300"><?php echo number_format($totalSenior); ?> คน</span>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-8 md:py-12 text-slate-400">
                <span class="text-4xl md:text-6xl block mb-3">📭</span>
                <p class="font-bold">ยังไม่มีข้อมูลการเช็คชื่อ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const selectedDate = '<?php echo $date; ?>';

// Fetch and render charts
function fetchChartData(chartId, apiUrl) {
    const fullUrl = apiUrl + '&date=' + selectedDate;
    
    fetch(fullUrl)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: true, 
                            position: 'bottom',
                            labels: {
                                font: { size: window.innerWidth < 768 ? 10 : 12, weight: 'bold', family: 'Mali' },
                                padding: window.innerWidth < 768 ? 10 : 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            cornerRadius: 12,
                            titleFont: { size: 14, weight: 'bold', family: 'Mali' },
                            bodyFont: { size: 13, family: 'Mali' },
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.dataset.label}: ${context.parsed.y} คน`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            stacked: true,
                            grid: { display: false },
                            ticks: { 
                                font: { size: window.innerWidth < 768 ? 9 : 11, weight: 'bold', family: 'Mali' },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: { 
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { 
                                font: { size: 11, family: 'Mali' },
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById(chartId).parentElement.innerHTML = '<div class="text-center py-8 text-rose-500"><span class="text-4xl block mb-2">⚠️</span><p class="font-bold">ไม่สามารถโหลดข้อมูลได้</p></div>';
        });
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => fetchChartData('barChart1', 'api/fetch_chartstu.php?level=1-3'), 100);
    setTimeout(() => fetchChartData('barChart2', 'api/fetch_chartstu.php?level=4-6'), 300);
});

// Export functions
function exportToExcel() {
    Swal.fire({
        title: 'กำลังสร้างไฟล์ Excel...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    const wb = XLSX.utils.book_new();
    const overviewData = [
        ['สถิติการมาเรียน - ' + '<?php echo thaiDateShort($date); ?>'],
        [],
        ['ภาพรวมสถิติ'],
        ['ประเภท', 'มาเรียน', 'ขาดเรียน', 'มาสาย', 'ลาป่วย', 'ลากิจ', 'กิจกรรม', 'รวม', 'อัตราเข้าเรียน'],
        ['วันที่เลือก', <?php echo $attendanceStats['today']['present'] ?? 0; ?>, <?php echo $attendanceStats['today']['absent'] ?? 0; ?>, <?php echo $attendanceStats['today']['late'] ?? 0; ?>, <?php echo $attendanceStats['today']['sick'] ?? 0; ?>, <?php echo $attendanceStats['today']['business'] ?? 0; ?>, <?php echo $attendanceStats['today']['activity'] ?? 0; ?>, <?php echo $attendanceStats['today']['total'] ?? 0; ?>, '<?php echo ($attendanceStats['today']['total'] ?? 0) > 0 ? round((($attendanceStats['today']['present'] ?? 0) / $attendanceStats['today']['total']) * 100, 1) : 0; ?>%'],
        ['สัปดาห์นี้', <?php echo $attendanceStats['week']['present'] ?? 0; ?>, <?php echo $attendanceStats['week']['absent'] ?? 0; ?>, <?php echo $attendanceStats['week']['late'] ?? 0; ?>, <?php echo $attendanceStats['week']['sick'] ?? 0; ?>, <?php echo $attendanceStats['week']['business'] ?? 0; ?>, <?php echo $attendanceStats['week']['activity'] ?? 0; ?>, <?php echo $attendanceStats['week']['total'] ?? 0; ?>, '<?php echo ($attendanceStats['week']['total'] ?? 0) > 0 ? round((($attendanceStats['week']['present'] ?? 0) / $attendanceStats['week']['total']) * 100, 1) : 0; ?>%'],
        ['เดือนนี้', <?php echo $attendanceStats['month']['present'] ?? 0; ?>, <?php echo $attendanceStats['month']['absent'] ?? 0; ?>, <?php echo $attendanceStats['month']['late'] ?? 0; ?>, <?php echo $attendanceStats['month']['sick'] ?? 0; ?>, <?php echo $attendanceStats['month']['business'] ?? 0; ?>, <?php echo $attendanceStats['month']['activity'] ?? 0; ?>, <?php echo $attendanceStats['month']['total'] ?? 0; ?>, '<?php echo ($attendanceStats['month']['total'] ?? 0) > 0 ? round((($attendanceStats['month']['present'] ?? 0) / $attendanceStats['month']['total']) * 100, 1) : 0; ?>%'],
        [],
        ['จำนวนนักเรียน'],
        ['ทั้งหมด', 'มัธยมต้น', 'มัธยมปลาย'],
        [<?php echo $studentCounts['total']; ?>, <?php echo $studentCounts['junior']; ?>, <?php echo $studentCounts['senior']; ?>]
    ];
    
    const ws = XLSX.utils.aoa_to_sheet(overviewData);
    XLSX.utils.book_append_sheet(wb, ws, 'ภาพรวม');
    
    XLSX.writeFile(wb, 'สถิติการมาเรียน_' + selectedDate + '.xlsx');
    
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'ดาวน์โหลดไฟล์ Excel เรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false
    });
}

async function exportToPNG() {
    Swal.fire({
        title: '📸 กำลังสร้างภาพ...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const content = document.querySelector('main');
        const canvas = await html2canvas(content, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#f1f5f9',
            logging: false
        });
        
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'สถิติการมาเรียน_' + selectedDate + '.png';
            a.click();
            URL.revokeObjectURL(url);
            
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'ดาวน์โหลดภาพ PNG เรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message
        });
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
