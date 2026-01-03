<?php
/**
 * View: Admin Dashboard
 * Modern UI with Tailwind CSS & Executive Analytics
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-gauge-high"></i>
                </span>
                Admin <span class="text-rose-600 italic">Dashboard</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">System Control & Analytics Overview</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="px-6 py-2 bg-rose-600 rounded-full flex items-center gap-3 shadow-lg shadow-rose-600/20">
                <span class="text-[9px] font-black text-rose-100 uppercase tracking-widest">ปีการศึกษา</span>
                <span class="text-xs font-black text-white"><?php echo $pee; ?></span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1: Students -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div class="text-right flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</span>
                    <span class="text-[9px] font-black px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-full border border-emerald-500/20 uppercase">Active</span>
                </div>
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2 italic">นักเรียนทั้งหมด</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($stats['students']); ?></h3>
        </div>

        <!-- Card 2: Teachers -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-sky-600 group-hover:text-white transition-all">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <div class="text-right flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</span>
                    <span class="text-[9px] font-black px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-full border border-emerald-500/20 uppercase">Active</span>
                </div>
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2 italic">บุคลากรทั้งหมด</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($stats['teachers']); ?></h3>
        </div>

        <!-- Card 3: Behaviors -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2 italic">รายการพฤติกรรม</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($stats['behavior']); ?></h3>
        </div>

        <!-- Card 4: Score -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-rose-600 group-hover:text-white transition-all">
                    <i class="fas fa-minus-circle text-2xl"></i>
                </div>
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2 italic">คะแนนหักรวม</p>
            <h3 class="text-4xl font-black text-rose-600 dark:text-rose-400 tabular-nums tracking-tighter">-<?php echo number_format($stats['behaviorScore']); ?></h3>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Behavior Score Groups Chart -->
        <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">สรุปกลุ่มคะแนนพฤติกรรม</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Behavior Score Distribution</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">การดำเนินการด่วน</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Quick Actions</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a href="data_student.php" class="p-6 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded-2xl transition-all group">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-white">จัดการนักเรียน</p>
                    <p class="text-xs text-slate-400">เพิ่ม/แก้ไข/ลบ</p>
                </a>
                <a href="data_teacher.php" class="p-6 bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/40 rounded-2xl transition-all group">
                    <div class="w-12 h-12 bg-sky-600 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-white">จัดการบุคลากร</p>
                    <p class="text-xs text-slate-400">ครู/เจ้าหน้าที่</p>
                </a>
                <a href="data_behavior.php" class="p-6 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-2xl transition-all group">
                    <div class="w-12 h-12 bg-rose-600 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-frown"></i>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-white">พฤติกรรม</p>
                    <p class="text-xs text-slate-400">หักคะแนน</p>
                </a>
                <a href="settings.php" class="p-6 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 rounded-2xl transition-all group">
                    <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-cog"></i>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-white">ตั้งค่าระบบ</p>
                    <p class="text-xs text-slate-400">Configuration</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Score Groups Table -->
    <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-table"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">รายละเอียดกลุ่มคะแนน</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Score Group Details</p>
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
