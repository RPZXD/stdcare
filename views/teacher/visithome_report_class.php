<?php
/**
 * Teacher Visit Home Report View - MVC Pattern
 * Enhanced UI/UX with Tailwind CSS - Mobile Responsive
 */
ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }
    
    .float-animation { animation: floating 3s ease-in-out infinite; }
    @keyframes floating { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    
    .pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }
    @keyframes pulseGlow { 0%, 100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.7); } 50% { box-shadow: 0 0 0 15px rgba(168, 85, 247, 0); } }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    @media (max-width: 768px) {
        .mobile-card { display: block !important; }
        .desktop-table { display: none !important; }
    }
    @media (min-width: 769px) {
        .mobile-card { display: none !important; }
        .desktop-table { display: block !important; }
    }

    /* Print Styles */
    @media print {
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            background: white !important;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print, 
        #chartTopicSelector, 
        .content-header, 
        .main-footer, 
        .main-sidebar, 
        .navbar,
        button {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
            background: white !important;
        }
        .glass-card {
            background: white !important;
            backdrop-filter: none !important;
            border: 1px solid #eee !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin-bottom: 2rem !important;
            overflow: visible !important;
        }
        .max-h-\[700px\] {
            max-height: none !important;
            overflow: visible !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
        }
        .print-title {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .print-subtitle {
            font-size: 14pt;
            color: #444;
        }
        .desktop-table {
            display: block !important;
        }
        .mobile-card {
            display: none !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            font-size: 10pt !important;
        }
        th {
            background-color: #f8fafc !important;
            color: black !important;
        }
        .bg-gradient-to-r {
            background: #f8fafc !important;
            color: black !important;
        }
        .text-white {
            color: black !important;
        }
        canvas {
            max-width: 100% !important;
            height: auto !important;
        }
        .page-break {
            page-break-before: always;
        }
        .signature-section {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 4rem;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px dotted #000;
            width: 200px;
            margin: 0 auto 0.5rem;
        }
    }
    
    .print-header, .signature-section, .print-footer {
        display: none;
    }
</style>

<!-- Print Only Header -->
<div class="print-header">
    <div class="flex items-center justify-center gap-4 mb-4">
        <!-- Placeholder for school logo if available -->
        <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center text-[8px] font-bold no-print-background">LOGO</div>
        <div class="text-left">
            <div class="print-title">รายงานสถิติการเยี่ยมบ้านนักเรียน</div>
            <div class="print-subtitle">โรงเรียนพิชัย | ปีการศึกษา <?php echo $pee; ?></div>
        </div>
    </div>
    <div class="grid grid-cols-2 text-sm text-left border-t border-b border-slate-200 py-2">
        <div>ชั้นเรียน: ม.<?php echo $class; ?>/<?php echo $room; ?></div>
        <div class="text-right">ภาคเรียนที่: <?php echo $term; ?></div>
        <div>ครูที่ปรึกษา: <?php echo implode(', ', array_map(function($t) { return $t['Teach_name']; }, $roomTeachers)); ?></div>
        <div class="text-right">วันที่พิมพ์: <?php echo date('d/m/Y H:i'); ?></div>
    </div>
</div>

<!-- Page Header -->
<div class="mb-6 no-print">
    <div class="relative glass-card rounded-[2rem] p-6 md:p-8 shadow-xl overflow-hidden border border-white/50 dark:border-slate-700/50">
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center text-white shadow-lg float-animation">
                    <i class="fas fa-chart-bar text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white leading-tight">
                        รายงานสถิติการเยี่ยมบ้าน
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 flex items-center gap-2">
                        <i class="fas fa-school text-blue-500"></i>
                        ชั้น ม.<?php echo $class; ?>/<?php echo $room; ?> | ปีการศึกษา <?php echo $pee; ?>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2 md:gap-3">
                <button onclick="window.location.href='visithome.php'" class="px-5 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl font-bold shadow-lg border border-slate-100 dark:border-slate-700 hover:-translate-y-1 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    ย้อนกลับ
                </button>
                <button onclick="issueFullReport()" class="px-5 py-3 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 rounded-2xl font-bold shadow-lg border border-blue-100 dark:border-blue-900/50 hover:-translate-y-1 transition flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    ออกเล่มรายงาน
                </button>
                <button onclick="window.print()" class="px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/25 hover:-translate-y-1 transition flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    พิมพ์หน้านี้
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Info & Filters -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 no-print">
    <!-- Teacher Card -->
    <div class="lg:col-span-2 glass-card rounded-3xl p-6 shadow-lg border border-white/50 dark:border-slate-700/50 flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 border-4 border-white dark:border-slate-700 shadow-xl flex items-center justify-center text-slate-400">
            <i class="fas fa-user-tie text-3xl"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">ครูที่ปรึกษา</h3>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-2">
                <?php foreach ($roomTeachers as $t): ?>
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full text-sm font-bold border border-blue-200 dark:border-blue-800">
                        <?php echo $t['Teach_name']; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="w-full md:w-px h-px md:h-12 bg-slate-200 dark:bg-slate-700 mx-4"></div>
        <div class="flex flex-col items-center md:items-start gap-1">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">ภาคเรียนล่าสุด</span>
            <div class="flex items-center gap-3">
                <select id="select_term" class="bg-transparent border-0 text-2xl font-black text-blue-600 focus:ring-0 cursor-pointer">
                    <option value="1" <?php echo $term == 1 ? 'selected' : ''; ?>>เทอม 1</option>
                    <option value="2" <?php echo $term == 2 ? 'selected' : ''; ?>>เทอม 2</option>
                </select>
                <i class="fas fa-chevron-down text-slate-400 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Quick Stats Circle -->
    <div class="glass-card rounded-3xl p-6 shadow-lg border border-white/50 dark:border-slate-700/50 flex flex-col justify-center relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
        
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ความคืบหน้ารวม</p>
                <h2 class="text-4xl font-black text-slate-800 dark:text-white" id="completionRate">0%</h2>
                <p class="text-xs text-emerald-500 font-bold mt-1">เยี่ยมบ้านแล้ว</p>
            </div>
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-home"></i>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between text-[11px] font-bold">
                <span class="text-slate-400 uppercase">กรอกข้อมูลเบื้องต้น (รวมรูป)</span>
                <span class="text-blue-500" id="anyDataRate">0%</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content: Stats & Charts -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    <!-- Detailed Stats Table -->
    <div class="glass-card rounded-[2.5rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-gradient-to-r from-blue-500 to-indigo-600 no-print">
            <h3 class="text-lg font-black text-white flex items-center gap-3">
                <i class="fas fa-list-ul"></i>
                รายละเอียดสถิติรายข้อ
            </h3>
        </div>
        
        <div class="max-h-[700px] overflow-y-auto">
            <!-- Desktop Table -->
            <div class="desktop-table">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">รายการ</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">คำตอบ</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">จำนวน</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">ร้อยละ</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-slate-100 dark:divide-slate-700">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="reportMobileCards" class="mobile-card p-4 space-y-4">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="space-y-6">
        <!-- Chart 1: Summary -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50">
            <div class="flex items-center justify-between mb-8 no-print">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">ภาพรวมการเยี่ยมบ้าน</h3>
                    <p class="text-sm text-slate-500">สัดส่วนนักเรียนที่ได้รับการเยี่ยมบ้าน</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <canvas id="summaryChart" style="max-height: 250px;"></canvas>
                <div id="summaryLegend" class="grid grid-cols-2 gap-4 mt-8 w-full"></div>
            </div>
        </div>

        <!-- Chart 2: Trends/Details -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50 page-break">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 no-print">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">สถิติตามหัวข้อ</h3>
                    <p class="text-sm text-slate-500">เลือกดูสถิติรายหัวข้อ</p>
                </div>
                <select id="chartTopicSelector" class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border-0 rounded-xl font-bold text-sm text-blue-600 focus:ring-0 cursor-pointer max-w-[250px]">
                    <!-- Populated by JS -->
                </select>
            </div>
            <canvas id="detailChart" style="max-height: 350px;"></canvas>
    </div>
</div>

<!-- Print Only Signatures -->
<div class="signature-section mt-12 grid grid-cols-2 gap-y-16 gap-x-12">
    <?php foreach ($roomTeachers as $t): ?>
    <div class="text-center">
        <p class="mb-12 text-sm">ลงชื่อ...........................................................</p>
        <p class="font-bold">( <?php echo $t['Teach_name']; ?> )</p>
        <p class="text-xs text-slate-500">ครูที่ปรึกษา</p>
    </div>
    <?php endforeach; ?>
    
    <div class="text-center">
        <p class="mb-12 text-sm">ลงชื่อ...........................................................</p>
        <p class="font-bold">( ........................................................... )</p>
        <p class="text-xs text-slate-500">หัวหน้าสายชั้น / ผู้บริหาร</p>
    </div>
</div>

<div class="print-footer mt-8 text-center text-[10px] text-slate-400 border-t pt-4">
    <p>เอกสารสรุปรายงานสถิติอัตโนมัติจากระบบ Student Care - โรงเรียนพิชัย</p>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const classId = <?php echo $class; ?>;
    const roomId = <?php echo $room; ?>;
    const peeId = <?php echo $pee; ?>;

    let summaryChart = null;
    let detailChart = null;
    let allData = [];

    document.addEventListener('DOMContentLoaded', function() {
        loadData();
        
        document.getElementById('select_term').addEventListener('change', function() {
            loadData();
        });

        document.getElementById('chartTopicSelector').addEventListener('change', function() {
            updateBarChart(this.value);
        });
    });

    async function loadData() {
        const term = document.getElementById('select_term').value;
        const loadingHtml = `
            <tr>
                <td colspan="4" class="p-12 text-center border-0">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-slate-400 font-bold">กำลังประมวลผลข้อมูลสถิติ...</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('reportTableBody').innerHTML = loadingHtml;
        document.getElementById('reportMobileCards').innerHTML = '';

        try {
            const response = await fetch(`api/fetch_visithomeclass.php?class=${classId}&room=${roomId}&pee=${peeId}&term=${term}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                allData = result.data;
                renderTable(allData);
                renderCharts(allData);
                updateGeneralStats(result.summary);
                populateTopicSelector(allData);
            }
        } catch (error) {
            console.error('Error loading data:', error);
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลสถิติได้', 'error');
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('reportTableBody');
        const mobileContainer = document.getElementById('reportMobileCards');
        let html = '';
        let mobileHtml = '';
        
        let currentGroup = '';
        
        data.forEach((item, index) => {
            const isNewGroup = item.display_header;
            
            // Desktop Row
            html += `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        ${isNewGroup ? `<span class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">${item.item_type}</span>` : ''}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">${item.item_list}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm font-black text-slate-600 dark:text-slate-400">${item.Stu_total}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-12 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: ${item.Persent}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-500">${item.Persent}%</span>
                        </div>
                    </td>
                </tr>
            `;

            // Mobile Card (Only groups with data or just the headings as headers)
            if (isNewGroup) {
                mobileHtml += `<div class="pt-4 pb-2"><h5 class="text-xs font-black text-blue-600 uppercase tracking-widest">${item.item_type}</h5></div>`;
            }
            
            mobileHtml += `
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">${item.item_list}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: ${item.Persent}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400">${item.Persent}%</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-slate-800 dark:text-white">${item.Stu_total}</span>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">นักเรียน</p>
                    </div>
                </div>
            `;
        });
        
        tbody.innerHTML = html;
        mobileContainer.innerHTML = mobileHtml;
    }

    function renderCharts(data) {
        // Find Summary Info (Visits) - Usually the first group in the API logic
        // We know that visit_status counts are part of the stats
        // But the API returns question-based data.
        
        // Let's create a summary from the data
        // For example, use the first question results as a representative
        const firstQuestionItems = data.filter(item => item.item_type === data[0].item_type);
        
        // Summary Progress Chart
        const summaryCtx = document.getElementById('summaryChart').getContext('2d');
        if (summaryChart) summaryChart.destroy();
        
        summaryChart = new Chart(summaryCtx, {
            type: 'doughnut',
            data: {
                labels: firstQuestionItems.map(i => i.item_list),
                datasets: [{
                    data: firstQuestionItems.map(i => i.Stu_total),
                    backgroundColor: [
                        '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // Legend for Summary
        const legendContainer = document.getElementById('summaryLegend');
        legendContainer.innerHTML = firstQuestionItems.map((i, idx) => `
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" style="background: ${summaryChart.data.datasets[0].backgroundColor[idx] || '#cbd5e1'}"></div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">${i.item_list}</p>
                    <p class="text-sm font-black text-slate-700 dark:text-slate-300">${i.Stu_total} คน (${i.Persent}%)</p>
                </div>
            </div>
        `).join('');

        // Initial Bar Chart
        const groups = [...new Set(data.map(i => i.item_type))];
        updateBarChart(groups[0]);
    }

    function populateTopicSelector(data) {
        const groups = [...new Set(data.map(i => i.item_type))];
        const selector = document.getElementById('chartTopicSelector');
        selector.innerHTML = groups.map(group => `<option value="${group}">${group}</option>`).join('');
    }

    function updateBarChart(topic) {
        const detailItems = allData.filter(item => item.item_type === topic);
        const detailCtx = document.getElementById('detailChart').getContext('2d');
        
        if (detailChart) detailChart.destroy();

        detailChart = new Chart(detailCtx, {
            type: 'bar',
            data: {
                labels: detailItems.map(i => i.item_list),
                datasets: [{
                    label: topic,
                    data: detailItems.map(i => i.Stu_total),
                    backgroundColor: '#6366F1',
                    borderRadius: 12,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' }, precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                }
            }
        });
    }

    function updateGeneralStats(summary) {
        // completionRate = visited_photo_percent
        document.getElementById('completionRate').textContent = summary.visited_photo_percent + '%';
        // anyDataRate = visited_any_percent
        document.getElementById('anyDataRate').textContent = summary.visited_any_percent + '%';
    }

    function issueFullReport() {
        const term = document.getElementById('select_term').value;
        window.open(`visithome_report_print.php?term=${term}`, '_blank');
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
