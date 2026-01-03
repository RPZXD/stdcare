<?php
/**
 * View: Student Checktime
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design with tabs
 */
ob_start();

// Status cards data
$statusCards = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅', 'color' => 'emerald', 'gradient' => 'from-emerald-500 to-green-600'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌', 'color' => 'red', 'gradient' => 'from-red-500 to-rose-600'],
    '3' => ['label' => 'มาสาย', 'emoji' => '⏰', 'color' => 'amber', 'gradient' => 'from-amber-500 to-orange-600'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒', 'color' => 'blue', 'gradient' => 'from-blue-500 to-indigo-600'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝', 'color' => 'purple', 'gradient' => 'from-purple-500 to-violet-600'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉', 'color' => 'pink', 'gradient' => 'from-pink-500 to-rose-600'],
];
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-clock text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">เวลาเรียน</h1>
                        <p class="text-purple-200 font-bold">ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?></p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-white font-bold text-sm">
                        <i class="fas fa-calendar mr-1"></i> รวม <?= count($attendanceRows) ?> วัน
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="glass-effect rounded-2xl p-2 border border-white/50 shadow-lg">
        <div class="grid grid-cols-3 gap-1" id="tabNav">
            <button class="tab-btn active px-2 py-3 rounded-xl font-bold text-xs md:text-base transition-all text-center" data-tab="attendance">
                <i class="fas fa-list block md:inline mb-1 md:mb-0 md:mr-1 text-base"></i>
                <span class="block md:inline text-[11px] md:text-base">การมาเรียน</span>
            </button>
            <button class="tab-btn px-2 py-3 rounded-xl font-bold text-xs md:text-base transition-all text-center" data-tab="monthly">
                <i class="fas fa-calendar-alt block md:inline mb-1 md:mb-0 md:mr-1 text-base"></i>
                <span class="block md:inline text-[11px] md:text-base">รายเดือน</span>
            </button>
            <button class="tab-btn px-2 py-3 rounded-xl font-bold text-xs md:text-base transition-all text-center" data-tab="term">
                <i class="fas fa-chart-pie block md:inline mb-1 md:mb-0 md:mr-1 text-base"></i>
                <span class="block md:inline text-[11px] md:text-base">รายภาค</span>
            </button>
        </div>
    </div>

    <!-- Tab 1: Attendance List -->
    <div id="tab-attendance" class="tab-content">
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-list-alt text-xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white">รายการมาเรียน</h3>
                        <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest"><?= count($attendanceRows) ?> รายการ</p>
                    </div>
                </div>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">#</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">วันที่</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">เวลาเข้า</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">เวลาออก</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">สถานะ</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php if (count($attendanceRows) > 0): ?>
                            <?php foreach ($attendanceRows as $i => $row): 
                                $status = attendance_status_text($row['attendance_status']);
                            ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-center font-bold text-slate-600 dark:text-slate-400"><?= $i + 1 ?></td>
                                <td class="px-4 py-3 text-center font-bold text-slate-700 dark:text-white"><?= thai_date($row['attendance_date']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($row['attendance_time']): ?>
                                    <span class="px-2 py-1 bg-<?= $status['color'] ?>-100 dark:bg-<?= $status['color'] ?>-900/30 text-<?= $status['color'] ?>-600 rounded-lg font-bold text-sm">
                                        <?= htmlspecialchars($row['attendance_time']) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-600 dark:text-slate-400">
                                    <?= $row['leave_time'] ? htmlspecialchars($row['leave_time']) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-<?= $status['color'] ?>-100 dark:bg-<?= $status['color'] ?>-900/30 text-<?= $status['color'] ?>-600 rounded-full font-bold text-sm">
                                        <?= $status['emoji'] ?> <?= $status['text'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-slate-500 dark:text-slate-400"><?= htmlspecialchars($row['reason'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar-times text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 font-bold">ไม่พบข้อมูลการมาเรียน</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="md:hidden p-3 space-y-4">
                <?php if (count($attendanceRows) > 0): ?>
                    <?php foreach ($attendanceRows as $i => $row): 
                        $status = attendance_status_text($row['attendance_status']);
                        $colorMap = [
                            'emerald' => ['bg' => '#d1fae5', 'text' => '#059669', 'border' => '#a7f3d0'],
                            'red' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#fecaca'],
                            'amber' => ['bg' => '#fef3c7', 'text' => '#d97706', 'border' => '#fde68a'],
                            'blue' => ['bg' => '#dbeafe', 'text' => '#2563eb', 'border' => '#bfdbfe'],
                            'purple' => ['bg' => '#ede9fe', 'text' => '#7c3aed', 'border' => '#ddd6fe'],
                            'pink' => ['bg' => '#fce7f3', 'text' => '#db2777', 'border' => '#fbcfe8'],
                            'slate' => ['bg' => '#f1f5f9', 'text' => '#64748b', 'border' => '#e2e8f0'],
                        ];
                        $c = $colorMap[$status['color']] ?? $colorMap['slate'];
                    ?>
                    <div class="rounded-2xl overflow-hidden shadow-lg" style="border: 2px solid <?= $c['border'] ?>;">
                        <!-- Status Header -->
                        <div class="px-4 py-3 flex items-center justify-between" style="background: <?= $c['bg'] ?>;">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm shadow" style="background: <?= $c['text'] ?>;">
                                    <?= $i + 1 ?>
                                </span>
                                <span class="font-bold" style="color: <?= $c['text'] ?>;">
                                    <?= thai_date($row['attendance_date']) ?>
                                </span>
                            </div>
                            <span class="text-2xl"><?= $status['emoji'] ?></span>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="bg-white dark:bg-slate-800 p-4">
                            <!-- Status Badge -->
                            <div class="flex items-center justify-center mb-3">
                                <span class="px-4 py-2 rounded-full font-bold text-base" style="background: <?= $c['bg'] ?>; color: <?= $c['text'] ?>;">
                                    <?= $status['emoji'] ?> <?= $status['text'] ?>
                                </span>
                            </div>
                            
                            <!-- Time Info -->
                            <div class="flex gap-2">
                                <div class="flex-1 text-center p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">เวลาเข้า</p>
                                    <p class="font-black text-base" style="color: <?= $c['text'] ?>;">
                                        <?= $row['attendance_time'] ? substr($row['attendance_time'], 0, 5) : '-' ?>
                                    </p>
                                </div>
                                <div class="flex-1 text-center p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">เวลาออก</p>
                                    <p class="font-black text-base text-slate-600 dark:text-slate-300">
                                        <?= $row['leave_time'] ? substr($row['leave_time'], 0, 5) : '-' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Reason (if any) -->
                            <?php if (!empty($row['reason'])): ?>
                            <div class="mt-3 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                <p class="text-xs text-amber-700 dark:text-amber-300">
                                    <i class="fas fa-comment-alt mr-1"></i> <?= htmlspecialchars($row['reason']) ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-times text-4xl text-slate-300"></i>
                        </div>
                        <p class="text-slate-500 font-bold text-lg">ไม่พบข้อมูลการมาเรียน</p>
                        <p class="text-slate-400 text-sm mt-2">ยังไม่มีการบันทึกเวลาเรียนในภาคเรียนนี้</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 2: Monthly Summary -->
    <div id="tab-monthly" class="tab-content hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4 mb-6">
            <?php foreach ($statusCards as $key => $card): ?>
            <div class="glass-effect rounded-2xl p-4 border border-white/50 shadow-lg hover:scale-105 transition-all text-center group">
                <span class="text-3xl md:text-4xl block mb-2 group-hover:scale-110 transition-transform"><?= $card['emoji'] ?></span>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1"><?= $card['label'] ?></p>
                <p class="text-2xl md:text-3xl font-black text-<?= $card['color'] ?>-600"><?= $monthStats[$key] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Chart -->
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-6">
            <h4 class="text-lg font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie"></i>
                </span>
                สรุปเดือนนี้
            </h4>
            <div class="flex justify-center">
                <div class="w-full max-w-xs">
                    <canvas id="monthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Table -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-5">
                <h3 class="text-lg font-black text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i> รายการเดือนนี้ (<?= count($monthRows) ?> วัน)
                </h3>
            </div>
            <div class="p-4 max-h-96 overflow-y-auto">
                <?php if (count($monthRows) > 0): ?>
                <div class="space-y-2">
                    <?php foreach ($monthRows as $row): 
                        $status = attendance_status_text($row['attendance_status']);
                    ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="font-bold text-slate-700 dark:text-white"><?= thai_date($row['attendance_date']) ?></span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-<?= $status['color'] ?>-100 dark:bg-<?= $status['color'] ?>-900/30 text-<?= $status['color'] ?>-600 rounded-full font-bold text-sm">
                            <?= $status['emoji'] ?> <?= $status['text'] ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-slate-500 font-bold">ไม่มีข้อมูลในเดือนนี้</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 3: Term Summary -->
    <div id="tab-term" class="tab-content hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4 mb-6">
            <?php foreach ($statusCards as $key => $card): ?>
            <div class="glass-effect rounded-2xl p-4 border border-white/50 shadow-lg hover:scale-105 transition-all text-center group">
                <span class="text-3xl md:text-4xl block mb-2 group-hover:scale-110 transition-transform"><?= $card['emoji'] ?></span>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1"><?= $card['label'] ?></p>
                <p class="text-2xl md:text-3xl font-black text-<?= $card['color'] ?>-600"><?= $termStats[$key] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Chart -->
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-6">
            <h4 class="text-lg font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie"></i>
                </span>
                สรุปภาคเรียนที่ <?= $term ?>/<?= $pee ?>
            </h4>
            <div class="flex justify-center">
                <div class="w-full max-w-xs">
                    <canvas id="termChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
            <h4 class="text-lg font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-percentage"></i>
                </span>
                อัตราการมาเรียน
            </h4>
            <?php 
            $totalDays = array_sum($termStats);
            $presentDays = $termStats['1'] ?? 0;
            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
            ?>
            <div class="text-center mb-4">
                <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <span class="text-4xl font-black text-white"><?= $attendanceRate ?>%</span>
                </div>
                <p class="mt-3 text-slate-500 font-bold">มาเรียน <?= $presentDays ?> จาก <?= $totalDays ?> วัน</p>
            </div>
            <div class="w-full h-4 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-green-600 rounded-full transition-all duration-500" style="width: <?= $attendanceRate ?>%"></div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-btn {
    background: transparent;
    color: #64748b;
}
.tab-btn.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}
.tab-btn:not(.active):hover {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
            document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        });
    });

    // Chart.js
    const chartConfig = {
        type: 'doughnut',
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            cutout: '60%'
        }
    };

    const chartColors = ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
    const chartLabels = ['มาเรียน', 'ขาดเรียน', 'มาสาย', 'ลาป่วย', 'ลากิจ', 'กิจกรรม'];

    // Monthly Chart
    new Chart(document.getElementById('monthChart'), {
        ...chartConfig,
        data: {
            labels: chartLabels,
            datasets: [{
                data: [<?= implode(',', array_values($monthStats)) ?>],
                backgroundColor: chartColors,
                borderWidth: 0
            }]
        }
    });

    // Term Chart
    new Chart(document.getElementById('termChart'), {
        ...chartConfig,
        data: {
            labels: chartLabels,
            datasets: [{
                data: [<?= implode(',', array_values($termStats)) ?>],
                backgroundColor: chartColors,
                borderWidth: 0
            }]
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
