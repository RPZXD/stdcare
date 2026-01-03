<?php
$pageTitle = $title ?? 'หน้าหลักครูที่ปรึกษา';

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
        min-height: 280px;
    }
</style>

<!-- Welcome Header -->
<div class="relative mb-6 md:mb-8 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-blue-500/20 to-indigo-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-violet-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
            <!-- Avatar -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl blur-lg opacity-50"></div>
                <div class="relative w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <i class="fas fa-chalkboard-teacher text-white text-2xl md:text-3xl"></i>
                </div>
            </div>
            <!-- Welcome Text -->
            <div class="text-center md:text-left">
                <h1 class="text-xl md:text-3xl font-black text-slate-800 dark:text-white">
                    👩‍🏫 ยินดีต้อนรับ คุณครู<?php echo htmlspecialchars($teacher_name); ?>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm md:text-base mt-1">
                    <i class="fas fa-users text-blue-500 mr-2"></i>
                    ครูที่ปรึกษา ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                    <span class="mx-2">•</span>
                    <i class="far fa-calendar-alt text-blue-500 mr-1"></i>
                    ภาคเรียนที่ <?php echo htmlspecialchars($term); ?>/<?php echo htmlspecialchars($pee); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards Row 1 -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
    <!-- Attendance Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🗓️</span>
        </div>
        <p class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo $countAll; ?></p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">นักเรียนทั้งหมด</p>
        <div class="flex justify-center gap-2 mt-3">
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-[10px] md:text-xs font-bold">
                ✅ <?php echo $countStdCome; ?>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-full text-[10px] md:text-xs font-bold">
                ❌ <?php echo $countStdAbsent; ?>
            </span>
        </div>
    </div>

    <!-- Behavior Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">📝</span>
        </div>
        <p class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-1" id="behaviorCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">พฤติกรรม (เดือนนี้)</p>
    </div>

    <!-- Visit Home Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🏠</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-slate-800 dark:text-white mb-1" id="visitCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">เยี่ยมบ้าน (ปีนี้)</p>
    </div>

    <!-- Poor Student Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">💸</span>
        </div>
        <p class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-1" id="poorCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">นักเรียนยากจน</p>
    </div>
</div>

<!-- Summary Cards Row 2 -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
    <!-- SDQ Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🧠</span>
        </div>
        <p class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-1" id="sdqCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">SDQ ประเมินแล้ว</p>
    </div>

    <!-- EQ Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-indigo-400 to-blue-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🌈</span>
        </div>
        <p class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-1" id="eqCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">EQ ประเมินแล้ว</p>
    </div>

    <!-- Screening Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-orange-400 to-red-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🔎</span>
        </div>
        <p class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-1" id="screenCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">คัดกรอง 11 ด้าน</p>
    </div>

    <!-- Homeroom Card -->
    <div class="stat-card glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 md:w-16 md:h-16 mx-auto bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <span class="text-2xl md:text-3xl">🏫</span>
        </div>
        <p class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-1" id="homeroomCount">-</p>
        <p class="text-[10px] md:text-sm font-bold text-slate-500 uppercase">กิจกรรมโฮมรูม</p>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
    <!-- Attendance Donut Chart -->
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chart-pie text-white text-lg md:text-xl"></i>
            </div>
            <h3 class="text-base md:text-xl font-black text-slate-800 dark:text-white">📊 สรุปการมาเรียน (วันนี้)</h3>
        </div>
        <div class="chart-container">
            <canvas id="donutChart"></canvas>
        </div>
    </div>

    <!-- Behavior Bar Chart -->
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chart-bar text-white text-lg md:text-xl"></i>
            </div>
            <h3 class="text-base md:text-xl font-black text-slate-800 dark:text-white">📝 กราฟพฤติกรรม (ภาคเรียนนี้)</h3>
        </div>
        <div class="chart-container">
            <canvas id="behaviorChart"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 md:mt-8">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-1.5 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
        <h3 class="text-lg md:text-xl font-black text-slate-800 dark:text-white">⚡ เมนูลัด</h3>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-4">
        <a href="check_std.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">✅</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">เช็คชื่อ</p>
        </a>
        <a href="data_student.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">👨‍🎓</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">ข้อมูลนักเรียน</p>
        </a>
        <a href="visithome.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">🏠</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">เยี่ยมบ้าน</p>
        </a>
        <a href="behavior.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">📝</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">บันทึกพฤติกรรม</p>
        </a>
        <a href="sdq.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">🧠</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">SDQ</p>
        </a>
        <a href="report.php" class="stat-card glass-card rounded-xl md:rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center hover:shadow-xl">
            <span class="text-2xl md:text-3xl mb-2 block">📊</span>
            <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-slate-300">รายงาน</p>
        </a>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classId = '<?php echo $class; ?>';
    const roomId = '<?php echo $room; ?>';
    const currentDate = '<?php echo $currentDate; ?>';
    const term = '<?php echo $term; ?>';
    const pee = '<?php echo $pee; ?>';

    // Donut Chart - Attendance
    const donutCanvas = document.getElementById('donutChart');
    if (donutCanvas) {
        const ctx = donutCanvas.getContext('2d');
        const donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12, weight: 'bold', family: 'Mali' },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: 'Mali' },
                        bodyFont: { family: 'Mali' },
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw} คน`;
                            }
                        }
                    }
                }
            }
        });

        fetch(`api/fetch_chart_studentcome.php?class=${classId}&room=${roomId}&date=${currentDate}`)
            .then(response => response.json())
            .then(data => {
                donutChart.data.labels = data.map(item => item.status_name);
                donutChart.data.datasets[0].data = data.map(item => parseFloat(item.count_total));
                donutChart.update();
            })
            .catch(err => console.error('Error loading attendance chart:', err));
    }

    // Bar Chart - Behavior
    const behaviorCanvas = document.getElementById('behaviorChart');
    if (behaviorCanvas) {
        const behaviorCtx = behaviorCanvas.getContext('2d');
        const behaviorChart = new Chart(behaviorCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'จำนวนเหตุการณ์',
                    data: [],
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: '#f59e0b',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: 'Mali' },
                        bodyFont: { family: 'Mali' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { family: 'Mali' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Mali', size: 10 } }
                    }
                }
            }
        });

        fetch(`api/fetch_chart_behavior.php?class=${classId}&room=${roomId}&term=${term}&pee=${pee}`)
            .then(response => response.json())
            .then(data => {
                behaviorChart.data.labels = data.map(item => item.behavior_type);
                behaviorChart.data.datasets[0].data = data.map(item => parseInt(item.count_total));
                behaviorChart.update();
            })
            .catch(err => console.error('Error loading behavior chart:', err));
    }

    // Fetch dashboard summary
    fetch(`api/fetch_dashboard_summary.php?class=${classId}&room=${roomId}&term=${term}&pee=${pee}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('behaviorCount').textContent = data.behavior_count ?? '-';
            
            let visitText = '-';
            if (data.visit_count_t1 !== undefined && data.visit_count_t2 !== undefined) {
                visitText = `T1: ${data.visit_count_t1} | T2: ${data.visit_count_t2}`;
            } else if (data.visit_count !== undefined) {
                visitText = data.visit_count;
            }
            document.getElementById('visitCount').textContent = visitText;
            
            document.getElementById('poorCount').textContent = data.poor_count ?? '-';
            
            let sdqText = '-';
            if (data.sdq_count_t1 !== undefined && data.sdq_count_t2 !== undefined) {
                sdqText = `T1: ${data.sdq_count_t1} | T2: ${data.sdq_count_t2}`;
            } else if (data.sdq_count !== undefined) {
                sdqText = data.sdq_count;
            }
            document.getElementById('sdqCount').textContent = sdqText;
            
            let eqText = '-';
            if (data.eq_count_t1 !== undefined && data.eq_count_t2 !== undefined) {
                eqText = `T1: ${data.eq_count_t1} | T2: ${data.eq_count_t2}`;
            } else if (data.eq_count !== undefined) {
                eqText = data.eq_count;
            }
            document.getElementById('eqCount').textContent = eqText;
            
            let screenText = '-';
            if (data.screen_count_t1 !== undefined && data.screen_count_t2 !== undefined) {
                screenText = `T1: ${data.screen_count_t1} | T2: ${data.screen_count_t2}`;
            } else if (data.screen_count !== undefined) {
                screenText = data.screen_count;
            }
            document.getElementById('screenCount').textContent = screenText;
            
            document.getElementById('homeroomCount').textContent = data.homeroom_count ?? '-';
        })
        .catch(err => console.error('Error loading dashboard summary:', err));
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
