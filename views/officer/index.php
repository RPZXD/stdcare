<?php
/**
 * View: Officer Dashboard
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Statistics
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.05);
    }
    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-10px) scale(1.02);
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="mb-10 animate-fadeIn" style="animation-delay: 0.1s">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-purple-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-blue-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-white tracking-tight">
                            Officer <span class="text-blue-600 italic">Dashboard</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-2 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            ยินดีต้อนรับคุณ <?php echo htmlspecialchars($userData['Teach_name'] ?? 'เจ้าหน้าที่'); ?>
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="px-6 py-3 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-white/40 dark:border-slate-700 shadow-sm backdrop-blur-md">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                            <?php echo $term; ?>/<?php echo $pee; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Students -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.2s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Students</p>
                    <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($studentCount); ?></h3>
                    <p class="text-xs text-emerald-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-user-graduate"></i> นักเรียนในระบบ
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1 w-full bg-blue-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.3s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Active Staff</p>
                    <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($teacherCount); ?></h3>
                    <p class="text-xs text-blue-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-chalkboard-teacher"></i> บุคลากรที่ปฏิบัติงาน
                    </p>
                </div>
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1 w-full bg-emerald-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Behavior count -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.4s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Behaviors</p>
                    <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($behaviorCount); ?></h3>
                    <p class="text-xs text-rose-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> รายการพฤติกรรม
                    </p>
                </div>
                <div class="w-14 h-14 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1 w-full bg-rose-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-rose-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- System Status -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.5s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Real-time status</p>
                    <h3 id="current_time" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">--:--:--</h3>
                    <p class="text-xs text-amber-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-sync-alt animate-spin"></i> ระบบพร้อมใช้งาน
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1 w-full bg-amber-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-amber-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Main Chart -->
        <div class="lg:col-span-2 glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.6s">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white italic">พฤติกรรมแยกตามกลุ่มคะแนน</h2>
                    <p class="text-sm text-slate-500">สรุปภาพรวมความประพฤตินักเรียนตามเกณฑ์โรงเรียน</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
            </div>
            
            <div class="relative h-[400px]">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <!-- Summary Info -->
        <div class="space-y-6 animate-fadeIn" style="animation-delay: 0.7s">
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50 h-full">
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-6 italic">ข้อมูลสรุปด่วน</h3>
                
                <div class="space-y-4">
                    <?php 
                    $colors = ['red', 'amber', 'blue', 'emerald'];
                    $icons = ['fa-user-slash', 'fa-walking', 'fa-hands-helping', 'fa-check-circle'];
                    $i = 0;
                    foreach ($scoreGroups as $label => $count) : 
                        $percentage = ($studentCount > 0) ? round(($count / $studentCount) * 100, 1) : 0;
                        $color = $colors[$i % 4];
                        $icon = $icons[$i % 4];
                        $i++;
                    ?>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 hover:border-<?php echo $color; ?>-500/50 transition-colors group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-<?php echo $color; ?>-100 dark:bg-<?php echo $color; ?>-900/30 text-<?php echo $color; ?>-600 flex items-center justify-center">
                                    <i class="fas <?php echo $icon; ?> text-xs"></i>
                                </div>
                                <span class="text-sm font-black text-slate-700 dark:text-slate-300 truncate max-w-[150px]"><?php echo $label; ?></span>
                            </div>
                            <span class="text-sm font-black text-slate-400 italic group-hover:text-<?php echo $color; ?>-500 transition-colors"><?php echo $percentage; ?>%</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-<?php echo $color; ?>-500 rounded-full transition-all duration-1000" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span class="text-xs font-black text-slate-800 dark:text-white tabular-nums"><?php echo number_format($count); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 p-4 rounded-[2rem] bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-lg overflow-hidden relative group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 transform scale-150 rotate-12 group-hover:rotate-45 transition-transform duration-700">
                        <i class="fas fa-shield-alt text-6xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-1">Total Score Points</p>
                    <div class="text-3xl font-black mb-1"><?php echo number_format($behaviorScore); ?></div>
                    <p class="text-[11px] font-medium italic opacity-90 leading-tight">คะแนนพฤติกรรมสะสมรวม<br>ประจำปีการศึกษาปัจจุบัน</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Time update
    function updateClock() {
        const now = new Date();
        $('#current_time').text(now.toLocaleTimeString('th-TH', { hour12: false }));
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart
    const ctx = document.getElementById('scoreChart').getContext('2d');
    
    // Create gradients
    const gradRed = ctx.createLinearGradient(0, 0, 0, 400);
    gradRed.addColorStop(0, '#f87171');
    gradRed.addColorStop(1, '#dc2626');

    const gradAmber = ctx.createLinearGradient(0, 0, 0, 400);
    gradAmber.addColorStop(0, '#fbbf24');
    gradAmber.addColorStop(1, '#d97706');

    const gradBlue = ctx.createLinearGradient(0, 0, 0, 400);
    gradBlue.addColorStop(0, '#60a5fa');
    gradBlue.addColorStop(1, '#2563eb');

    const gradEmerald = ctx.createLinearGradient(0, 0, 0, 400);
    gradEmerald.addColorStop(0, '#34d399');
    gradEmerald.addColorStop(1, '#059669');

    const scoreChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($scoreGroups)); ?>,
            datasets: [{
                label: 'จำนวนนักเรียน',
                data: <?php echo json_encode(array_values($scoreGroups)); ?>,
                backgroundColor: [gradRed, gradAmber, gradBlue, gradEmerald],
                borderRadius: 20,
                borderSkipped: false,
                barThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 15,
                    titleFont: { size: 14, weight: 'bold', family: 'Mali' },
                    bodyFont: { size: 13, family: 'Mali' },
                    cornerRadius: 15,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: { font: { family: 'Mali', weight: 'bold' }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { family: 'Mali', weight: 'bold', size: 11 }, 
                        color: '#64748b',
                    }
                }
            }
        }
    });

    // Mobile specific optimization: ensure bar labels don't overlap
    if (window.innerWidth < 768) {
        scoreChart.options.scales.x.ticks.maxRotation = 45;
        scoreChart.options.scales.x.ticks.minRotation = 45;
        scoreChart.update();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
