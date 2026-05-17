<?php
/**
 * View: Admin Dashboard
 * Modern UI with Tailwind CSS & Executive Analytics
 */
ob_start();
$pageTitle = "แดชบอร์ดผู้ดูแลระบบ";
$activePage = "dashboard";
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <?php 
    $headerData = [
        'title' => 'Admin <span class="text-rose-600 italic">Dashboard</span>',
        'subtitle' => 'System Control & Analytics Overview',
        'icon' => 'fa-gauge-high',
        'color' => 'rose',
        'actions' => [
            // No global actions for dashboard currently
        ]
    ];
    include __DIR__ . '/../components/ui_header.php'; 
    ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-10">
        <?php 
        $cards = [
            ['label' => 'นักเรียนทั้งหมด', 'value' => number_format($stats['students']), 'icon' => 'fa-user-graduate', 'color' => 'indigo', 'status' => 'Active'],
            ['label' => 'บุคลากรทั้งหมด', 'value' => number_format($stats['teachers']), 'icon' => 'fa-chalkboard-teacher', 'color' => 'sky', 'status' => 'Active'],
            ['label' => 'รายการพฤติกรรม', 'value' => number_format($stats['behavior']), 'icon' => 'fa-clipboard-list', 'color' => 'amber'],
            ['label' => 'คะแนนหักรวม', 'value' => '-' . number_format($stats['behaviorScore']), 'icon' => 'fa-minus-circle', 'color' => 'rose'],
        ];
        foreach ($cards as $card):
            $statData = $card;
            include __DIR__ . '/../components/ui_stat_card.php';
        endforeach;
        ?>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 mb-6 lg:mb-10">
        <!-- Behavior Score Groups Chart -->
        <div class="glass-effect rounded-3xl lg:rounded-[2.5rem] p-5 lg:p-8 shadow-xl border-t border-white/50">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-rose-600 rounded-xl lg:rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h3 class="text-base lg:text-lg font-black text-slate-800 dark:text-white leading-tight">สรุปกลุ่มคะแนนพฤติกรรม</h3>
                    <p class="text-[9px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Behavior Score Distribution</p>
                </div>
            </div>
            <div class="h-64 lg:h-72">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-effect rounded-3xl lg:rounded-[2.5rem] p-5 lg:p-8 shadow-xl border-t border-white/50">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-indigo-600 rounded-xl lg:rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <h3 class="text-base lg:text-lg font-black text-slate-800 dark:text-white leading-tight">การดำเนินการด่วน</h3>
                    <p class="text-[9px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Quick Actions</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:gap-4">
                <a href="data_student.php" class="p-4 lg:p-6 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded-2xl transition-all group">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mb-3 lg:mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <p class="font-bold text-sm lg:text-base text-slate-800 dark:text-white leading-tight">จัดการนักเรียน</p>
                    <p class="text-[10px] lg:text-xs text-slate-400 mt-1">เพิ่ม/แก้ไข/ลบ</p>
                </a>
                <a href="data_teacher.php" class="p-4 lg:p-6 bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/40 rounded-2xl transition-all group">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-sky-600 rounded-xl flex items-center justify-center text-white mb-3 lg:mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <p class="font-bold text-sm lg:text-base text-slate-800 dark:text-white leading-tight">จัดการบุคลากร</p>
                    <p class="text-[10px] lg:text-xs text-slate-400 mt-1">ครู/เจ้าหน้าที่</p>
                </a>
                <a href="data_behavior.php" class="p-4 lg:p-6 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-2xl transition-all group">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-rose-600 rounded-xl flex items-center justify-center text-white mb-3 lg:mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-frown"></i>
                    </div>
                    <p class="font-bold text-sm lg:text-base text-slate-800 dark:text-white leading-tight">พฤติกรรม</p>
                    <p class="text-[10px] lg:text-xs text-slate-400 mt-1">หักคะแนน</p>
                </a>
                <a href="settings.php" class="p-4 lg:p-6 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 rounded-2xl transition-all group">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-amber-600 rounded-xl flex items-center justify-center text-white mb-3 lg:mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-cog"></i>
                    </div>
                    <p class="font-bold text-sm lg:text-base text-slate-800 dark:text-white leading-tight">ตั้งค่าระบบ</p>
                    <p class="text-[10px] lg:text-xs text-slate-400 mt-1">Config</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Score Groups Table -->
    <div class="glass-effect rounded-3xl lg:rounded-[2.5rem] p-5 lg:p-8 shadow-xl border-t border-white/50">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-amber-500 rounded-xl lg:rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-table"></i>
            </div>
            <div>
                <h3 class="text-base lg:text-lg font-black text-slate-800 dark:text-white leading-tight">รายละเอียดกลุ่มคะแนน</h3>
                <p class="text-[9px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Score Group Details</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-rose-50/50 dark:bg-slate-800/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left rounded-l-2xl">กลุ่ม</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">จำนวน</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ร้อยละ</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left rounded-r-2xl">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php 
                    $colors = ['rose', 'amber', 'sky', 'emerald'];
                    $actions = ['เข้าค่ายปรับพฤติกรรม', 'บำเพ็ญประโยชน์ 20 ชม.', 'บำเพ็ญประโยชน์ 10 ชม.', '-'];
                    $i = 0;
                    foreach ($scoreGroups as $label => $count): 
                        $percent = $stats['students'] > 0 ? round(($count / $stats['students']) * 100, 1) : 0;
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="px-3 py-1.5 bg-<?php echo $colors[$i]; ?>-100 dark:bg-<?php echo $colors[$i]; ?>-900/30 text-<?php echo $colors[$i]; ?>-600 dark:text-<?php echo $colors[$i]; ?>-400 rounded-xl text-xs font-bold"><?php echo $label; ?></span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-white"><?php echo number_format($count); ?></td>
                        <td class="px-6 py-4 text-center font-bold text-slate-500"><?php echo $percent; ?>%</td>
                        <td class="px-6 py-4 text-sm text-slate-500"><?php echo $actions[$i]; ?></td>
                    </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('scoreChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($scoreGroups)); ?>,
        datasets: [{
            label: 'จำนวนนักเรียน',
            data: <?php echo json_encode(array_values($scoreGroups)); ?>,
            backgroundColor: ['#f43f5e', '#f59e0b', '#0ea5e9', '#10b981'],
            borderRadius: 12,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
