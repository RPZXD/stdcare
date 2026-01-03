<?php
$pageTitle = $title ?? 'ภาพรวมระบบดูแลช่วยเหลือนักเรียน';

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

$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'gradient' => 'from-emerald-400 to-green-500', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-500/20'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'gradient' => 'from-rose-400 to-red-500', 'bg' => 'bg-rose-500/10', 'text' => 'text-rose-600', 'ring' => 'ring-rose-500/20'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒', 'gradient' => 'from-amber-400 to-orange-500', 'bg' => 'bg-amber-500/10', 'text' => 'text-amber-600', 'ring' => 'ring-amber-500/20'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'gradient' => 'from-sky-400 to-blue-500', 'bg' => 'bg-sky-500/10', 'text' => 'text-sky-600', 'ring' => 'ring-sky-500/20'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'gradient' => 'from-violet-400 to-purple-500', 'bg' => 'bg-violet-500/10', 'text' => 'text-violet-600', 'ring' => 'ring-violet-500/20'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'gradient' => 'from-pink-400 to-rose-500', 'bg' => 'bg-pink-500/10', 'text' => 'text-pink-600', 'ring' => 'ring-pink-500/20'],
];

ob_start();
?>

<!-- Custom Styles for this page -->
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
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.1; transform: scale(1.5); }
    }
    .count-up {
        animation: countUp 1s ease-out;
    }
    @keyframes countUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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
</style>

<!-- Hero Header Section -->
<div class="relative mb-6 md:mb-8 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-emerald-500/20 to-teal-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 md:gap-6">
            <!-- Left: Title -->
            <div class="flex items-center gap-3 md:gap-5">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl md:rounded-2xl blur-lg opacity-50 pulse-ring"></div>
                    <div class="relative bg-gradient-to-br from-blue-500 to-indigo-600 text-white p-3 md:p-5 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-chart-pie text-xl md:text-3xl floating-icon"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-tight">
                        ภาพรวมการมาเรียน
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm md:text-base flex items-center gap-2 mt-1">
                        <i class="far fa-calendar-alt text-blue-500"></i>
                        <span>ประจำวันที่ <?php echo htmlspecialchars(thaiDateShort($date)); ?></span>
                    </p>
                </div>
            </div>
            
            <!-- Right: Date Picker -->
            <form method="get" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="flex items-center gap-3 bg-white/50 dark:bg-slate-800/50 px-4 py-3 rounded-xl md:rounded-2xl border border-slate-200/50 dark:border-slate-600/50 shadow-lg">
                    <i class="far fa-calendar-alt text-blue-500 text-lg"></i>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" 
                           class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 font-bold text-base w-full sm:w-auto">
                </div>
                <button type="submit" class="relative overflow-hidden bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 text-white px-6 md:px-8 py-3 md:py-3.5 rounded-xl md:rounded-2xl font-bold text-sm md:text-base shadow-xl hover:shadow-2xl hover:shadow-blue-500/30 transition-all active:scale-95 group">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        <span>แสดงข้อมูล</span>
                    </span>
                    <div class="absolute inset-0 shimmer"></div>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Quick Stats Cards - Overview -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
    <!-- Card: วันนี้ -->
    <div class="stat-card bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-emerald">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center">
            <i class="fas fa-calendar-day text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2 md:mb-3">วันนี้</p>
            <p class="text-2xl md:text-5xl font-black mb-1 count-up"><?php echo number_format($stats['today']['present']); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2 md:mb-4 leading-tight">
                สาย <?php echo $stats['today']['late']; ?> • ขาด <?php echo $stats['today']['absent']; ?>
            </p>
            <div class="pt-2 md:pt-4 border-t border-white/20">
                <div class="flex items-center gap-2">
                    <span class="text-lg md:text-2xl font-black">
                        <?php echo ($stats['today']['total'] > 0) ? round(($stats['today']['present'] / $stats['today']['total']) * 100, 1) : 0; ?>%
                    </span>
                    <span class="text-[8px] md:text-[10px] uppercase font-bold opacity-70">อัตราเข้าเรียน</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: สัปดาห์นี้ -->
    <div class="stat-card bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-blue">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center">
            <i class="fas fa-calendar-week text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2 md:mb-3">สัปดาห์นี้</p>
            <p class="text-2xl md:text-5xl font-black mb-1 count-up"><?php echo number_format($stats['week']['present']); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2 md:mb-4 leading-tight">
                สาย <?php echo $stats['week']['late']; ?> • ขาด <?php echo $stats['week']['absent']; ?>
            </p>
            <div class="pt-2 md:pt-4 border-t border-white/20">
                <div class="flex items-center gap-2">
                    <span class="text-lg md:text-2xl font-black">
                        <?php echo ($stats['week']['total'] > 0) ? round(($stats['week']['present'] / $stats['week']['total']) * 100, 1) : 0; ?>%
                    </span>
                    <span class="text-[8px] md:text-[10px] uppercase font-bold opacity-70">อัตราเข้าเรียน</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: เดือนนี้ -->
    <div class="stat-card bg-gradient-to-br from-violet-500 via-purple-600 to-fuchsia-600 rounded-2xl md:rounded-3xl p-4 md:p-6 text-white relative overflow-hidden glow-purple">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center">
            <i class="fas fa-chart-bar text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest opacity-80 mb-2 md:mb-3">เดือนนี้</p>
            <p class="text-2xl md:text-5xl font-black mb-1 count-up"><?php echo number_format($stats['month']['present']); ?></p>
            <p class="text-[10px] md:text-xs font-semibold opacity-80 mb-2 md:mb-4 leading-tight">
                สาย <?php echo $stats['month']['late']; ?> • ขาด <?php echo $stats['month']['absent']; ?>
            </p>
            <div class="pt-2 md:pt-4 border-t border-white/20">
                <div class="flex items-center gap-2">
                    <span class="text-lg md:text-2xl font-black">
                        <?php echo ($stats['month']['total'] > 0) ? round(($stats['month']['present'] / $stats['month']['total']) * 100, 1) : 0; ?>%
                    </span>
                    <span class="text-[8px] md:text-[10px] uppercase font-bold opacity-70">อัตราเข้าเรียน</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: เป้าหมาย -->
    <?php $avg = ($stats['today']['total'] > 0) ? round(($stats['today']['present'] / $stats['today']['total']) * 100, 1) : 0; ?>
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-20 md:w-32 h-20 md:h-32 bg-orange-500/10 rounded-full blur-2xl"></div>
        <div class="absolute right-2 top-2 md:right-4 md:top-4 w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-orange-400 to-rose-500 rounded-xl md:rounded-2xl flex items-center justify-center text-white">
            <i class="fas fa-bullseye text-sm md:text-xl"></i>
        </div>
        <div class="relative">
            <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400 mb-2 md:mb-3">อัตราเฉลี่ย</p>
            <p class="text-2xl md:text-5xl font-black mb-1 text-slate-800 dark:text-white count-up"><?php echo $avg; ?>%</p>
            <p class="text-[10px] md:text-xs font-semibold text-slate-500 mb-2 md:mb-4">เป้าหมาย: 95%</p>
            <div class="pt-2 md:pt-4 border-t border-slate-100 dark:border-slate-700">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 md:h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-400 via-rose-500 to-pink-500 h-full rounded-full transition-all duration-1000 relative" style="width: <?php echo min($avg, 100); ?>%">
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
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg shadow-blue-500/30">
            <i class="fas fa-user-graduate text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1 count-up"><?php echo number_format($studentCounts['total']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">นักเรียนทั้งหมด</p>
    </div>
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl stat-card text-center">
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg shadow-emerald-500/30">
            <i class="fas fa-book-reader text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1 count-up"><?php echo number_format($studentCounts['junior']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">ม.ต้น</p>
    </div>
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl stat-card text-center">
        <div class="w-10 h-10 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-4 shadow-lg shadow-purple-500/30">
            <i class="fas fa-user-tie text-white text-lg md:text-2xl"></i>
        </div>
        <p class="text-xl md:text-4xl font-black text-slate-800 dark:text-white mb-1 count-up"><?php echo number_format($studentCounts['senior']); ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase tracking-wider">ม.ปลาย</p>
    </div>
</div>

<!-- Attendance Status Summary Grid -->
<div class="mb-6 md:mb-8">
    <div class="flex items-center gap-3 mb-4 md:mb-6">
        <div class="w-1.5 h-8 md:h-10 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
        <h3 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">สรุปสถานะการมาเรียนวันนี้</h3>
    </div>
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4">
        <?php foreach ($status_labels as $key => $info): ?>
        <div class="glass-card rounded-2xl md:rounded-3xl border border-white/30 dark:border-slate-700/50 p-3 md:p-6 flex flex-col items-center stat-card group cursor-pointer active:scale-95 transition-all shadow-lg hover:shadow-xl">
            <div class="text-2xl md:text-5xl mb-2 md:mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"><?php echo $info['emoji']; ?></div>
            <p class="text-lg md:text-3xl font-black <?php echo $info['text']; ?> dark:text-white mb-0.5 md:mb-1 count-up"><?php echo number_format($status_count[$key]); ?></p>
            <p class="text-[8px] md:text-xs font-bold <?php echo $info['text']; ?> uppercase tracking-wider mb-1 md:mb-2"><?php echo $info['label']; ?></p>
            <span class="inline-block px-2 md:px-3 py-0.5 md:py-1 bg-gradient-to-r <?php echo $info['gradient']; ?> text-white text-[8px] md:text-[10px] font-black rounded-full shadow-sm">
                <?php echo $total ? round($status_count[$key]*100/$total,1) : 0; ?>%
            </span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Charts and Table Section -->
<div class="grid grid-cols-1 xl:grid-cols-5 gap-4 md:gap-8">
    <!-- Pie Chart -->
    <div class="xl:col-span-2 glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl">
        <div class="flex items-center gap-3 mb-4 md:mb-6">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/30">
                <i class="fas fa-chart-pie text-white text-base md:text-xl"></i>
            </div>
            <h4 class="text-base md:text-xl font-black text-slate-800 dark:text-white">กราฟสัดส่วนการมาเรียน</h4>
        </div>
        <div class="relative h-[250px] md:h-[320px]">
            <canvas id="pieChartOverview"></canvas>
        </div>
    </div>

    <!-- DataTable -->
    <div class="xl:col-span-3 glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4 mb-4 md:mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i class="fas fa-table text-white text-base md:text-xl"></i>
                </div>
                <h4 class="text-base md:text-xl font-black text-slate-800 dark:text-white">รายละเอียดแต่ละห้อง</h4>
            </div>
            <div class="flex items-center gap-2 md:gap-3 bg-white/50 dark:bg-slate-800/50 px-3 md:px-4 py-2 md:py-2.5 rounded-xl md:rounded-2xl border border-slate-200/50 dark:border-slate-600/50">
                <i class="fas fa-filter text-blue-500 text-sm"></i>
                <select id="classFilter" class="bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 font-bold text-sm cursor-pointer p-0">
                    <option value="">ทุกชั้น</option>
                    <?php
                    $class_set = [];
                    foreach ($classes as $c) {
                        $class_set[$c['Stu_major']] = true;
                    }
                    ksort($class_set);
                    foreach (array_keys($class_set) as $major) {
                        echo '<option value="ม.' . htmlspecialchars($major) . '">ม.' . htmlspecialchars($major) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table id="attendanceTable" class="min-w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-slate-100 dark:border-slate-700">
                            <th class="pb-3 md:pb-4 text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400">ชั้น</th>
                            <th class="pb-3 md:pb-4 text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400">ห้อง</th>
                            <th class="pb-3 md:pb-4 text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400">จำนวน</th>
                            <?php foreach ($status_labels as $info): ?>
                                <th class="pb-3 md:pb-4 text-center text-lg md:text-2xl"><?php echo $info['emoji']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach ($classes as $c): ?>
                        <tr class="group hover:bg-blue-50/50 dark:hover:bg-slate-700/50 transition-all duration-200">
                            <td class="py-3 md:py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-400 to-indigo-500 text-white font-black text-xs md:text-sm rounded-lg md:rounded-xl shadow-md">
                                    ม.<?php echo htmlspecialchars($c['Stu_major']); ?>
                                </span>
                            </td>
                            <td class="py-3 md:py-4 font-black text-slate-800 dark:text-white text-sm md:text-base"><?php echo htmlspecialchars($c['Stu_room']); ?></td>
                            <td class="py-3 md:py-4">
                                <span class="inline-block px-2 md:px-3 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-black rounded-lg text-xs md:text-sm">
                                    <?php echo $c['count']; ?>
                                </span>
                            </td>
                            <?php foreach ($status_labels as $k => $info): ?>
                            <td class="py-3 md:py-4 text-center">
                                <?php if($c['status'][$k] > 0): ?>
                                <span class="inline-block px-2 py-1 <?php echo $info['bg']; ?> <?php echo $info['text']; ?> dark:bg-slate-700 dark:text-white font-bold rounded-lg text-xs md:text-sm">
                                    <?php echo $c['status'][$k]; ?>
                                </span>
                                <?php else: ?>
                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Doughnut Chart with gradient
    var ctx = document.getElementById('pieChartOverview').getContext('2d');
    
    // Create gradients
    const gradients = [
        ctx.createLinearGradient(0, 0, 0, 300),
        ctx.createLinearGradient(0, 0, 0, 300),
        ctx.createLinearGradient(0, 0, 0, 300),
        ctx.createLinearGradient(0, 0, 0, 300),
        ctx.createLinearGradient(0, 0, 0, 300),
        ctx.createLinearGradient(0, 0, 0, 300)
    ];
    
    gradients[0].addColorStop(0, '#34d399'); gradients[0].addColorStop(1, '#10b981');
    gradients[1].addColorStop(0, '#f87171'); gradients[1].addColorStop(1, '#ef4444');
    gradients[2].addColorStop(0, '#fbbf24'); gradients[2].addColorStop(1, '#f59e0b');
    gradients[3].addColorStop(0, '#60a5fa'); gradients[3].addColorStop(1, '#3b82f6');
    gradients[4].addColorStop(0, '#a78bfa'); gradients[4].addColorStop(1, '#8b5cf6');
    gradients[5].addColorStop(0, '#f472b6'); gradients[5].addColorStop(1, '#ec4899');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                <?php foreach ($status_labels as $info): ?>
                    "<?php echo $info['label']; ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [<?php echo implode(',', array_values($status_count)); ?>],
                backgroundColor: gradients,
                borderWidth: 0,
                hoverOffset: 15,
                borderRadius: 8,
                spacing: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        font: { size: window.innerWidth < 768 ? 11 : 13, weight: 'bold', family: 'Mali' },
                        padding: window.innerWidth < 768 ? 12 : 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 14, weight: 'bold', family: 'Mali' },
                    bodyFont: { size: 13, family: 'Mali' },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${context.raw} คน (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });

    // DataTable with responsive styling
    var table = $('#attendanceTable').DataTable({
        paging: true,
        pageLength: 8,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: false,
        responsive: true,
        dom: 'rtp',
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
        },
        columnDefs: [
            { targets: '_all', orderable: false },
            { targets: [0, 1, 2], orderable: true }
        ],
        order: [[0, 'asc'], [1, 'asc']]
    });

    // Class Filter
    $('#classFilter').on('change', function() {
        var val = $(this).val();
        table.column(0).search(val ? val : '', true, false).draw();
    });

    // Count up animation
    const observerOptions = { threshold: 0.3 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('count-up');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.count-up').forEach(el => observer.observe(el));
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
