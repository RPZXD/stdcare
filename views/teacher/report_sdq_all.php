<?php
/**
 * SDQ Report All - View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
ob_start();
?>

<!-- Page Header -->
<div class="mb-6 md:mb-8 no-print">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-rose-500/30">
                    <i class="fas fa-chart-bar"></i>
                </div>
                รายงานสถิติ SDQ
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm md:text-base">
                สรุปผลการประเมินพฤติกรรมนักเรียน ม.<?= $class ?>/<?= $room ?> ปีการศึกษา <?= $pee ?> ภาคเรียนที่ <?= $term ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="sdq.php" class="px-4 py-2.5 bg-gradient-to-r from-slate-500 to-slate-600 text-white rounded-xl font-bold shadow-lg shadow-slate-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i>
                กลับหน้า SDQ
            </a>
            <button onclick="printReport()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-print"></i>
                พิมพ์รายงาน
            </button>
        </div>
    </div>
</div>

<!-- Summary Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="summaryStats">
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">นักเรียนทั้งหมด</p>
                <p class="text-xl font-black text-slate-800 dark:text-white" id="stat_total">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-smile"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ปกติ</p>
                <p class="text-xl font-black text-emerald-600" id="stat_normal">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">เสี่ยง</p>
                <p class="text-xl font-black text-amber-600" id="stat_risk">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-heart-broken"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">มีปัญหา</p>
                <p class="text-xl font-black text-rose-600" id="stat_problem">-</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Report Card -->
<div class="glass-card rounded-[2rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden mb-6">
    <!-- Card Header -->
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-rose-500 to-pink-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                    <i class="fas fa-table text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white">ตารางคะแนน SDQ รายบุคคล</h2>
                    <p class="text-white/70 text-sm">แสดงคะแนนและผลการแปลผลของนักเรียนทุกคน</p>
                </div>
            </div>
            <!-- Search Box -->
            <div class="relative no-print">
                <input type="text" id="searchInput" placeholder="ค้นหานักเรียน..."
                    class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-white/20 backdrop-blur border-0 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:outline-none">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/60"></i>
            </div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full" id="reportTable">
            <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">เลขที่</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                    <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">นักเรียน<br><span class="text-[8px]">(คะแนน)</span></th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ผล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ครู<br><span class="text-[8px]">(คะแนน)</span></th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ผล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ผู้ปกครอง<br><span class="text-[8px]">(คะแนน)</span></th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ผล</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-700">
                <!-- Populated by JS -->
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden p-4 space-y-4" id="mobileCards">
        <!-- Populated by JS -->
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="p-12 text-center">
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-400 font-bold">กำลังโหลดข้อมูล...</p>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="glass-card rounded-[2rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden no-print">
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-chart-pie text-rose-500"></i>
            สรุปผลการประเมิน SDQ
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Bar Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
                <h4 class="text-sm font-bold text-slate-600 dark:text-slate-400 mb-4">จำนวนนักเรียนในแต่ละระดับ</h4>
                <div style="height: 250px;">
                    <canvas id="sdqBarChart"></canvas>
                </div>
            </div>
            <!-- Pie Charts -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <h5 class="text-xs font-bold text-blue-600 mb-2">นักเรียน</h5>
                    <canvas id="pieStd"></canvas>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <h5 class="text-xs font-bold text-amber-600 mb-2">ครู</h5>
                    <canvas id="pieTeach"></canvas>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 text-center">
                    <h5 class="text-xs font-bold text-purple-600 mb-2">ผู้ปกครอง</h5>
                    <canvas id="piePar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Header (Hidden on Screen) -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600">สำนักงานเขตพื้นที่การศึกษามัธยมศึกษาพิษณุโลก อุตรดิตถ์</p>
    </div>
    <h2 class="text-lg font-bold text-center mb-1">รายงานผลการประเมินพฤติกรรมนักเรียน (SDQ)</h2>
    <p class="text-center text-sm text-slate-600 mb-4">
        ระดับชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?> ปีการศึกษา <?= $pee ?> ภาคเรียนที่ <?= $term ?>
    </p>
    
    <!-- Print Stats Summary -->
    <div class="flex justify-center gap-8 mb-4 text-sm">
        <span><strong>นักเรียนทั้งหมด:</strong> <span id="print_total">-</span> คน</span>
        <span class="text-emerald-600"><strong>😄 ปกติ:</strong> <span id="print_normal">-</span> คน</span>
        <span class="text-amber-600"><strong>😐 เสี่ยง:</strong> <span id="print_risk">-</span> คน</span>
        <span class="text-rose-600"><strong>😥 มีปัญหา:</strong> <span id="print_problem">-</span> คน</span>
    </div>
</div>

<!-- Print Table (Hidden on Screen, Shown on Print) -->
<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse text-xs" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center">เลขที่</th>
                <th class="border border-slate-300 px-2 py-2 text-center">รหัส</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ชื่อ-นามสกุล</th>
                <th class="border border-slate-300 px-2 py-2 text-center">นักเรียน<br>(คะแนน)</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ผล</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ครู<br>(คะแนน)</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ผล</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ผู้ปกครอง<br>(คะแนน)</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ผล</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Populated by JS -->
        </tbody>
    </table>
</div>

<!-- Print Signature Section (Hidden on Screen) -->
<div id="printSignature" class="hidden print:block mt-12">
    <div class="grid grid-cols-2 gap-8 px-8">
        <?php foreach ($roomTeachers as $t): ?>
        <div class="text-center mb-2">
            <p class="mb-2">ลงชื่อ...........................................</p>
            <p class="font-bold">(<?= $t['Teach_name'] ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
        
        <!-- <div class="text-center mb-2">
            <p class="mb-2">ลงชื่อ...........................................</p>
            <p class="font-bold">(........................................)</p>
            <p class="text-sm text-slate-600">หัวหน้าระดับชั้น</p>
        </div>
        <div class="text-center mb-2">
            <p class="mb-2">ลงชื่อ...........................................</p>
            <p class="font-bold">(........................................)</p>
            <p class="text-sm text-slate-600">รองผู้อำนวยการ</p>
        </div> -->
    </div>
    <p class="text-center text-xs text-slate-400 mt-8">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- Custom Styles -->
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
.dark .glass-card {
    background: rgba(30, 41, 59, 0.95);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.fade-in-up {
    animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Print Styles */
@media print {
    @page {
        size: A4 portrait;
        margin: 5mm;
    }
    
    body {
        background: white !important;
        font-family: 'Mali', sans-serif !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Hide non-print elements */
    .no-print,
    #sidebar,
    #navbar,
    .glass-card,
    #summaryStats,
    #mobileCards,
    #loadingState {
        display: none !important;
    }
    
    /* Show print elements */
    #printHeader,
    #printTable,
    #printSignature {
        display: block !important;
    }
    
    /* Print table styling */
    #printTableContent {
        font-size: 10px;
        width: 100%;
    }
    
    #printTableContent th,
    #printTableContent td {
        padding: 4px 6px;
        border: 1px solid #cbd5e1;
    }
    
    #printTableContent th {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .result-normal { color: #16a34a; font-weight: bold; }
    .result-risk { color: #d97706; font-weight: bold; }
    .result-problem { color: #dc2626; font-weight: bold; }
}

/* Screen only - hide print elements */
@media screen {
    #printHeader,
    #printTable,
    #printSignature {
        display: none !important;
    }
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const classId = <?= $class ?>;
const roomId = <?= $room ?>;
const peeId = <?= $pee ?>;
const termId = <?= $term ?>;

let barChartInstance = null;
let pieCharts = {};
let summary = {
    std: { normal: 0, risk: 0, problem: 0 },
    teach: { normal: 0, risk: 0, problem: 0 },
    par: { normal: 0, risk: 0, problem: 0 }
};

document.addEventListener('DOMContentLoaded', function() {
    loadReportData();
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        filterStudents(e.target.value.toLowerCase());
    });
});

async function loadReportData() {
    try {
        const response = await fetch(`api/fetch_sdq_report_all.php?class=${classId}&room=${roomId}&pee=${peeId}&term=${termId}`);
        const result = await response.json();
        
        document.getElementById('loadingState').style.display = 'none';
        
        if (result.success && result.data.length > 0) {
            renderTable(result.data);
            calculateSummary(result.data);
            updateStats(result.data);
            renderCharts();
        } else {
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading data:', error);
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

function getResultBadge(result, score) {
    if (!result || result === '-') {
        return `<span class="text-slate-400 text-xs">-</span>`;
    }
    let color = 'slate';
    let icon = '';
    if (result.includes('ปกติ')) {
        color = 'emerald';
        icon = '😄';
    } else if (result.includes('เสี่ยง')) {
        color = 'amber';
        icon = '😐';
    } else if (result.includes('ปัญหา')) {
        color = 'rose';
        icon = '😥';
    }
    return `<span class="inline-flex items-center gap-1 px-2 py-1 bg-${color}-100 dark:bg-${color}-900/30 text-${color}-600 text-xs font-bold rounded-full">${icon} ${result}</span>`;
}

function getScoreBadge(score) {
    if (score === null || score === '-') {
        return `<span class="text-slate-400">-</span>`;
    }
    let color = score >= 20 ? 'rose' : (score >= 14 ? 'amber' : 'emerald');
    return `<span class="font-bold text-${color}-600">${score}</span>`;
}

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    const mobileContainer = document.getElementById('mobileCards');
    
    let desktopHtml = '';
    let mobileHtml = '';
    
    data.forEach((item, idx) => {
        // Desktop Row
        desktopHtml += `
            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors student-row" data-name="${item.full_name.toLowerCase()}" data-id="${item.Stu_id}">
                <td class="px-4 py-3 text-center font-bold text-slate-400">${item.Stu_no}</td>
                <td class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">${item.Stu_id}</td>
                <td class="px-4 py-3 text-left font-bold text-slate-800 dark:text-white">${item.full_name}</td>
                <td class="px-4 py-3 text-center">${getScoreBadge(item.std_score)}</td>
                <td class="px-4 py-3 text-center">${getResultBadge(item.std_result, item.std_score)}</td>
                <td class="px-4 py-3 text-center">${getScoreBadge(item.teach_score)}</td>
                <td class="px-4 py-3 text-center">${getResultBadge(item.teach_result, item.teach_score)}</td>
                <td class="px-4 py-3 text-center">${getScoreBadge(item.par_score)}</td>
                <td class="px-4 py-3 text-center">${getResultBadge(item.par_result, item.par_score)}</td>
            </tr>
        `;
        
        // Mobile Card
        mobileHtml += `
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm fade-in-up student-row" data-name="${item.full_name.toLowerCase()}" data-id="${item.Stu_id}" style="animation-delay: ${idx * 0.03}s">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white">${item.full_name}</h4>
                        <div class="flex gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            <span>เลขที่ ${item.Stu_no}</span>
                            <span>•</span>
                            <span>${item.Stu_id}</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-2">
                        <p class="text-[10px] font-bold text-blue-600 mb-1">นักเรียน</p>
                        <p class="font-bold text-lg">${item.std_score ?? '-'}</p>
                        ${getResultBadge(item.std_result, item.std_score)}
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-2">
                        <p class="text-[10px] font-bold text-amber-600 mb-1">ครู</p>
                        <p class="font-bold text-lg">${item.teach_score ?? '-'}</p>
                        ${getResultBadge(item.teach_result, item.teach_score)}
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-2">
                        <p class="text-[10px] font-bold text-purple-600 mb-1">ผู้ปกครอง</p>
                        <p class="font-bold text-lg">${item.par_score ?? '-'}</p>
                        ${getResultBadge(item.par_result, item.par_score)}
                    </div>
                </div>
            </div>
        `;
    });
    
    tbody.innerHTML = desktopHtml;
    mobileContainer.innerHTML = mobileHtml;
}

function calculateSummary(data) {
    summary = {
        std: { normal: 0, risk: 0, problem: 0 },
        teach: { normal: 0, risk: 0, problem: 0 },
        par: { normal: 0, risk: 0, problem: 0 }
    };
    
    data.forEach(item => {
        countResult('std', item.std_result);
        countResult('teach', item.teach_result);
        countResult('par', item.par_result);
    });
}

function countResult(type, result) {
    if (!result || result === '-') return;
    if (result.includes('ปกติ')) summary[type].normal++;
    else if (result.includes('เสี่ยง')) summary[type].risk++;
    else if (result.includes('ปัญหา')) summary[type].problem++;
}

function updateStats(data) {
    const total = data.length;
    
    // Screen stats
    document.getElementById('stat_total').textContent = total;
    document.getElementById('stat_normal').textContent = summary.std.normal;
    document.getElementById('stat_risk').textContent = summary.std.risk;
    document.getElementById('stat_problem').textContent = summary.std.problem;
    
    // Print stats
    document.getElementById('print_total').textContent = total;
    document.getElementById('print_normal').textContent = summary.std.normal;
    document.getElementById('print_risk').textContent = summary.std.risk;
    document.getElementById('print_problem').textContent = summary.std.problem;
    
    // Render print table
    renderPrintTable(data);
}

function renderPrintTable(data) {
    const tbody = document.getElementById('printTableBody');
    let html = '';
    
    data.forEach((item, idx) => {
        const stdClass = getResultClass(item.std_result);
        const teachClass = getResultClass(item.teach_result);
        const parClass = getResultClass(item.par_result);
        
        html += `
            <tr>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.Stu_no}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.Stu_id}</td>
                <td class="border border-slate-300 px-2 py-1 text-left">${item.full_name}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.std_score ?? '-'}</td>
                <td class="border border-slate-300 px-2 py-1 text-center ${stdClass}">${item.std_result ?? '-'}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.teach_score ?? '-'}</td>
                <td class="border border-slate-300 px-2 py-1 text-center ${teachClass}">${item.teach_result ?? '-'}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.par_score ?? '-'}</td>
                <td class="border border-slate-300 px-2 py-1 text-center ${parClass}">${item.par_result ?? '-'}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function getResultClass(result) {
    if (!result) return '';
    if (result.includes('ปกติ')) return 'result-normal';
    if (result.includes('เสี่ยง')) return 'result-risk';
    if (result.includes('ปัญหา')) return 'result-problem';
    return '';
}

function renderCharts() {
    // Bar Chart
    const ctx = document.getElementById('sdqBarChart').getContext('2d');
    if (barChartInstance) barChartInstance.destroy();
    
    barChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['ปกติ', 'ภาวะเสี่ยง', 'มีปัญหา'],
            datasets: [
                {
                    label: 'นักเรียน',
                    data: [summary.std.normal, summary.std.risk, summary.std.problem],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 8
                },
                {
                    label: 'ครู',
                    data: [summary.teach.normal, summary.teach.risk, summary.teach.problem],
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderRadius: 8
                },
                {
                    label: 'ผู้ปกครอง',
                    data: [summary.par.normal, summary.par.risk, summary.par.problem],
                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    
    // Pie Charts
    const pieColors = ['rgba(34, 197, 94, 0.8)', 'rgba(234, 179, 8, 0.8)', 'rgba(239, 68, 68, 0.8)'];
    
    ['Std', 'Teach', 'Par'].forEach((type, i) => {
        const key = type.toLowerCase();
        const pieCtx = document.getElementById(`pie${type}`).getContext('2d');
        if (pieCharts[key]) pieCharts[key].destroy();
        
        pieCharts[key] = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['ปกติ', 'เสี่ยง', 'ปัญหา'],
                datasets: [{
                    data: [summary[key].normal, summary[key].risk, summary[key].problem],
                    backgroundColor: pieColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    });
}

function filterStudents(term) {
    document.querySelectorAll('.student-row').forEach(row => {
        const name = row.dataset.name;
        const id = row.dataset.id;
        const match = name.includes(term) || id.includes(term);
        row.style.display = match ? '' : 'none';
    });
}

function showEmptyState() {
    const emptyHtml = `
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-bar text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-600 dark:text-slate-400">ไม่พบข้อมูลรายงาน</h3>
            <p class="text-slate-400 text-sm mt-1">ยังไม่มีข้อมูล SDQ สำหรับห้องเรียนนี้</p>
        </div>
    `;
    document.getElementById('tableBody').innerHTML = `<tr><td colspan="9">${emptyHtml}</td></tr>`;
    document.getElementById('mobileCards').innerHTML = emptyHtml;
}

function printReport() {
    window.print();
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
