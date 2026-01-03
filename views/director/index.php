<?php
/**
 * View: Director Dashboard
 * Modern UI with Tailwind CSS, Glassmorphism & High-Level Statistics
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
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-violet-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-crown text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-white tracking-tight">
                            Director <span class="text-indigo-600 italic uppercase">Executive</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-2 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            ยินดีต้อนรับท่านผู้อำนวยการ <?php echo htmlspecialchars($userData['Teach_name'] ?? ''); ?>
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="px-6 py-3 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-white/40 dark:border-slate-700 shadow-sm backdrop-blur-md">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-xl font-black text-violet-600 dark:text-violet-400">
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
                    <p class="text-xs text-indigo-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-user-graduate"></i> นักเรียนในระบบทัั้งหมด
                    </p>
                </div>
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1.5 w-full bg-indigo-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Personnel -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.3s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Personnel</p>
                    <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($teacherCount); ?></h3>
                    <p class="text-xs text-violet-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-user-tie"></i> บุคลากรทางการศึกษา
                    </p>
                </div>
                <div class="w-14 h-14 bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1.5 w-full bg-violet-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-violet-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Behavior records -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.4s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Behaviors</p>
                    <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($behaviorCount); ?></h3>
                    <p class="text-xs text-rose-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> บันทึกเหตุพฤติกรรม
                    </p>
                </div>
                <div class="w-14 h-14 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-file-invoice text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1.5 w-full bg-rose-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-rose-600 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Summary time -->
        <div class="stat-card glass-effect rounded-[2rem] p-8 shadow-xl border-t border-white/50 animate-fadeIn" style="animation-delay: 0.5s">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Real-time status</p>
                    <h3 id="current_time" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">--:--:--</h3>
                    <p class="text-xs text-amber-500 font-bold mt-2 flex items-center gap-1">
                        <i class="fas fa-sync-alt animate-spin"></i> ระบบประมวลผลปกติ
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 h-1.5 w-full bg-amber-100 dark:bg-slate-700 rounded-full overflow-hidden">
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
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">พฤติกรรมแยกตามกลุ่มเกณฑ์</h2>
                    <p class="text-sm text-slate-500 font-medium">ภาพรวมระดับความประพฤติสกัดจากกลุ่มคะแนนพฤติกรรมนักเรียน</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
            </div>
            
            <div class="relative h-[450px]">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <!-- Highlights & Quick Actions -->
        <div class="space-y-6 animate-fadeIn" style="animation-delay: 0.7s">
            <!-- Score Groups Summary -->
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50 h-full">
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-6 italic border-l-4 border-indigo-600 pl-4">ข้อมูลสรุปเชิงลึก</h3>
                
                <div class="space-y-4">
                    <?php 
                    $colors = ['rose', 'amber', 'indigo', 'emerald'];
                    $icons = ['fa-user-slash', 'fa-walking', 'fa-hands-helping', 'fa-check-circle'];
                    $i = 0;
                    foreach ($scoreGroups as $label => $count) : 
                        $percentage = ($studentCount > 0) ? round(($count / $studentCount) * 100, 1) : 0;
                        $color = $colors[$i % 4];
                        $icon = $icons[$i % 4];
                        $i++;
                    ?>
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 hover:border-<?php echo $color; ?>-500/50 transition-all group cursor-pointer hover:shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-<?php echo $color; ?>-100 dark:bg-<?php echo $color; ?>-900/30 text-<?php echo $color; ?>-600 flex items-center justify-center shadow-sm">
                                    <i class="fas <?php echo $icon; ?> text-sm"></i>
                                </div>
                                <span class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight"><?php echo $label; ?></span>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 italic group-hover:text-<?php echo $color; ?>-500 transition-colors tracking-widest"><?php echo $percentage; ?>%</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-<?php echo $color; ?>-500 rounded-full transition-all duration-1000 ease-out" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span class="text-sm font-black text-slate-700 dark:text-white tabular-nums"><?php echo number_format($count); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Footer Summary Card -->
                <div class="mt-8 p-6 rounded-[2rem] bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 text-white shadow-2xl overflow-hidden relative group">
                    <div class="absolute -right-6 -top-6 p-4 opacity-10 transform scale-150 rotate-12 group-hover:rotate-45 transition-transform duration-700">
                        <i class="fas fa-tachometer-alt text-7xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] opacity-70 mb-2 italic">Cumulative Behavior Points</p>
                    <div class="text-4xl font-black mb-2 tracking-tighter">
                        <?php echo number_format($behaviorScore); ?>
                        <span class="text-sm font-medium opacity-60 ml-1">PTS</span>
                    </div>
                    <div class="h-1 w-12 bg-white/30 rounded-full mb-3"></div>
                    <p class="text-[11px] font-bold italic opacity-85 leading-relaxed tracking-wide">คะแนนพฤติกรรมสะสมทั้งโรงเรียน<br>ประจำปีการศึกษาปัจจุบัน</p>
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
    const gradRose = ctx.createLinearGradient(0, 0, 0, 450);
    gradRose.addColorStop(0, '#fb7185');
    gradRose.addColorStop(1, '#e11d48');

    const gradAmber = ctx.createLinearGradient(0, 0, 0, 450);
    gradAmber.addColorStop(0, '#fbbf24');
    gradAmber.addColorStop(1, '#d97706');

    const gradIndigo = ctx.createLinearGradient(0, 0, 0, 450);
    gradIndigo.addColorStop(0, '#818cf8');
    gradIndigo.addColorStop(1, '#4f46e5');

    const gradEmerald = ctx.createLinearGradient(0, 0, 0, 450);
    gradEmerald.addColorStop(0, '#34d399');
    gradEmerald.addColorStop(1, '#10b981');

    const scoreChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($scoreGroups)); ?>,
            datasets: [{
                label: 'จำนวนนักเรียน',
                data: <?php echo json_encode(array_values($scoreGroups)); ?>,
                backgroundColor: [gradRose, gradAmber, gradIndigo, gradEmerald],
                borderRadius: 25,
                borderSkipped: false,
                barThickness: 50,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 18,
                    titleFont: { size: 15, weight: 'bold', family: 'Mali' },
                    bodyFont: { size: 14, family: 'Mali' },
                    cornerRadius: 20,
                    displayColors: false,
                    caretSize: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { font: { family: 'Mali', weight: '900', size: 12 }, color: '#94a3b8', padding: 10 }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { family: 'Mali', weight: '900', size: 12 }, 
                        color: '#64748b',
                        padding: 10
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
include __DIR__ . '/../layouts/director_app.php';
?>
