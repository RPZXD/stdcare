<?php
/**
 * Teacher SDQ Assessment View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
ob_start();
?>

<!-- Page Header -->
<div class="mb-6 md:mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-purple-500/30">
                    <i class="fas fa-brain"></i>
                </div>
                ประเมิน SDQ
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm md:text-base">แบบประเมินพฤติกรรมนักเรียน SDQ ชั้น ม.<?= $class ?>/<?= $room ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="report_sdq_all.php" class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 text-white rounded-xl font-bold shadow-lg shadow-rose-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-chart-bar"></i>
                รายงานสถิติ
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm no-print">
                <i class="fas fa-print"></i>
                พิมพ์รายงาน
            </button>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
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
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ประเมินตนเอง</p>
                <p class="text-xl font-black text-slate-800 dark:text-white" id="stat_self">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ครูประเมิน</p>
                <p class="text-xl font-black text-slate-800 dark:text-white" id="stat_teach">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-friends"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ผู้ปกครองประเมิน</p>
                <p class="text-xl font-black text-slate-800 dark:text-white" id="stat_parent">-</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="glass-card rounded-[2rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-indigo-500 to-purple-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white">รายชื่อนักเรียน</h2>
                    <p class="text-white/70 text-sm">ปีการศึกษา <?= $pee ?> ภาคเรียนที่ <?= $term ?></p>
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
        <table class="w-full" id="sdqTable">
            <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">เลขที่</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                    <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">นักเรียน</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">แปลผล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ครู</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">แปลผล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ผู้ปกครอง</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">แปลผล</th>
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
            <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-400 font-bold">กำลังโหลดข้อมูลนักเรียน...</p>
        </div>
    </div>
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
    #loadingState,
    .mb-6.md\:mb-8 {
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
        font-size: 11px;
        width: 100%;
    }
    
    #printTableContent th,
    #printTableContent td {
        padding: 6px 8px;
        border: 1px solid #cbd5e1;
    }
    
    #printTableContent th {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .status-done { color: #16a34a; font-weight: bold; }
    .status-pending { color: #dc2626; font-weight: bold; }
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

<!-- Print Header (Hidden on Screen) -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4 mb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600">สำนักงานเขตพื้นที่การศึกษามัธยมศึกษาพิษณุโลก อุตรดิตถ์</p>
    </div>
    <h2 class="text-lg font-bold text-center mb-1">สรุปสถานะการประเมินพฤติกรรมนักเรียน (SDQ) รายบุคคล</h2>
    <p class="text-center text-sm text-slate-600 mb-4">
        ระดับชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?> ปีการศึกษา <?= $pee ?> ภาคเรียนที่ <?= $term ?>
    </p>
    
    <!-- Print Stats Summary -->
    <div class="flex justify-center gap-8 mb-4 text-sm font-bold">
        <span>นักเรียนทั้งหมด: <span id="print_total">-</span> คน</span>
        <span class="text-blue-600">นักเรียนประเมิน: <span id="print_self">-</span> คน</span>
        <span class="text-amber-600">ครูประเมิน: <span id="print_teach">-</span> คน</span>
        <span class="text-purple-600">ผู้ปกครองประเมิน: <span id="print_parent">-</span> คน</span>
    </div>
</div>

<!-- Print Table (Hidden on Screen, Shown on Print) -->
<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse text-xs" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center w-12">เลขที่</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-24">รหัส</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ชื่อ-นามสกุล</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-24">นักเรียน</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-24">ครู</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-24">ผู้ปกครอง</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Populated by JS -->
        </tbody>
    </table>
</div>

<!-- Print Signature Section (Hidden on Screen) -->
<div id="printSignature" class="hidden print:block mt-8">
    <div class="grid grid-cols-2 gap-8 px-8">
        <?php foreach ($roomTeachers as $t): ?>
        <div class="text-center mb-2">
            <p class="mb-2">ลงชื่อ...........................................</p>
            <p class="font-bold">(<?= $t['Teach_name'] ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
<!--         
        <div class="text-center mb-2">
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


<!-- Scripts -->
<script>
const classId = <?= $class ?>;
const roomId = <?= $room ?>;
const peeId = <?= $pee ?>;
const termId = <?= $term ?>;

document.addEventListener('DOMContentLoaded', function() {
    loadStudentData();
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filterStudents(searchTerm);
    });
});

async function loadStudentData() {
    try {
        const response = await fetch(`api/fetch_sdq_classroom.php?class=${classId}&room=${roomId}&pee=${peeId}&term=${termId}`);
        const result = await response.json();
        
        document.getElementById('loadingState').style.display = 'none';
        
        if (result.success && result.data.length > 0) {
            renderTable(result.data);
            updateStats(result.data);
        } else {
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading data:', error);
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

function updateStats(data) {
    const total = data.length;
    const selfCount = data.filter(d => d.self_ishave === 1).length;
    const teachCount = data.filter(d => d.teach_ishave === 1).length;
    const parCount = data.filter(d => d.par_ishave === 1).length;
    
    // UI Stats
    document.getElementById('stat_total').textContent = total;
    document.getElementById('stat_self').textContent = `${selfCount}/${total}`;
    document.getElementById('stat_teach').textContent = `${teachCount}/${total}`;
    document.getElementById('stat_parent').textContent = `${parCount}/${total}`;
    
    // Print Stats
    document.getElementById('print_total').textContent = total;
    document.getElementById('print_self').textContent = selfCount;
    document.getElementById('print_teach').textContent = teachCount;
    document.getElementById('print_parent').textContent = parCount;
    
    renderPrintTable(data);
}

function renderPrintTable(data) {
    const tbody = document.getElementById('printTableBody');
    let html = '';
    
    data.forEach((item, idx) => {
        html += `
            <tr>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.Stu_no}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.Stu_id}</td>
                <td class="border border-slate-300 px-2 py-1 text-left">${item.full_name}</td>
                <td class="border border-slate-300 px-2 py-1 text-center ${item.self_ishave === 1 ? 'status-done' : 'status-pending'}">
                    ${item.self_ishave === 1 ? '✅ เรียบร้อย' : '❌ ยังไม่ทำ'}
                </td>
                <td class="border border-slate-300 px-2 py-1 text-center ${item.teach_ishave === 1 ? 'status-done' : 'status-pending'}">
                    ${item.teach_ishave === 1 ? '✅ เรียบร้อย' : '❌ ยังไม่ทำ'}
                </td>
                <td class="border border-slate-300 px-2 py-1 text-center ${item.par_ishave === 1 ? 'status-done' : 'status-pending'}">
                    ${item.par_ishave === 1 ? '✅ เรียบร้อย' : '❌ ยังไม่ทำ'}
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
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
                <td class="px-4 py-4 text-center font-bold text-slate-400">${item.Stu_no}</td>
                <td class="px-4 py-4 text-center font-semibold text-slate-600 dark:text-slate-300">${item.Stu_id}</td>
                <td class="px-4 py-4 text-left font-bold text-slate-800 dark:text-white">${item.full_name}</td>
                <td class="px-4 py-3 text-center">${createActionBtn(item.self_ishave === 1, 'std', item)}</td>
                <td class="px-4 py-3 text-center">${createResultBtn('std', item.self_ishave === 1, item)}</td>
                <td class="px-4 py-3 text-center">${createActionBtn(item.teach_ishave === 1, 'teach', item)}</td>
                <td class="px-4 py-3 text-center">${createResultBtn('teach', item.teach_ishave === 1, item)}</td>
                <td class="px-4 py-3 text-center">${createActionBtn(item.par_ishave === 1, 'par', item)}</td>
                <td class="px-4 py-3 text-center">${createResultBtn('par', item.par_ishave === 1, item)}</td>
            </tr>
        `;
        
        // Mobile Card
        mobileHtml += `
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm fade-in-up student-row" data-name="${item.full_name.toLowerCase()}" data-id="${item.Stu_id}" style="animation-delay: ${idx * 0.03}s">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white">${item.full_name}</h4>
                        <div class="flex gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            <span>เลขที่ ${item.Stu_no}</span>
                            <span>•</span>
                            <span>${item.Stu_id}</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Self Assessment -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-blue-500"></i>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">นักเรียน</span>
                        </div>
                        <div class="flex gap-2">
                            ${createMobileActionBtn(item.self_ishave === 1, 'std', item)}
                            ${createMobileResultBtn('std', item.self_ishave === 1, item)}
                        </div>
                    </div>
                    
                    <!-- Teacher Assessment -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chalkboard-teacher text-amber-500"></i>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">ครู</span>
                        </div>
                        <div class="flex gap-2">
                            ${createMobileActionBtn(item.teach_ishave === 1, 'teach', item)}
                            ${createMobileResultBtn('teach', item.teach_ishave === 1, item)}
                        </div>
                    </div>
                    
                    <!-- Parent Assessment -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-friends text-purple-500"></i>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">ผู้ปกครอง</span>
                        </div>
                        <div class="flex gap-2">
                            ${createMobileActionBtn(item.par_ishave === 1, 'par', item)}
                            ${createMobileResultBtn('par', item.par_ishave === 1, item)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    tbody.innerHTML = desktopHtml;
    mobileContainer.innerHTML = mobileHtml;
}

function createActionBtn(hasData, type, item) {
    const method = hasData ? `edit` : `add`;
    const actionFn = `${method}SDQ${type}`;
    const bgColor = hasData ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-500 hover:bg-blue-600';
    const icon = hasData ? 'fa-edit' : 'fa-plus';
    const text = hasData ? 'แก้ไข' : 'บันทึก';
    const status = hasData ? '✅' : '❌';
    
    return `
        <div class="flex items-center justify-center gap-2">
            <span>${status}</span>
            <button onclick="${actionFn}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', ${classId}, ${roomId}, ${termId}, ${peeId})"
                class="px-3 py-1.5 ${bgColor} text-white text-xs font-bold rounded-lg shadow transition-all">
                <i class="fas ${icon} mr-1"></i>${text}
            </button>
        </div>
    `;
}

function createResultBtn(type, hasData, item) {
    if (!hasData) {
        return `<span class="text-slate-400 text-xs">-</span>`;
    }
    return `
        <button onclick="resultSDQ${type}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', ${classId}, ${roomId}, ${termId}, ${peeId})"
            class="px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold rounded-lg shadow transition-all">
            <i class="fas fa-chart-pie mr-1"></i>แปลผล
        </button>
    `;
}

function createMobileActionBtn(hasData, type, item) {
    const method = hasData ? `edit` : `add`;
    const actionFn = `${method}SDQ${type}`;
    const bgColor = hasData ? 'bg-amber-500' : 'bg-blue-500';
    const icon = hasData ? 'fa-edit' : 'fa-plus';
    
    return `
        <button onclick="${actionFn}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', ${classId}, ${roomId}, ${termId}, ${peeId})"
            class="w-8 h-8 ${bgColor} text-white text-xs font-bold rounded-lg shadow flex items-center justify-center">
            <i class="fas ${icon}"></i>
        </button>
    `;
}

function createMobileResultBtn(type, hasData, item) {
    if (!hasData) {
        return `<span class="w-8 h-8 bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-slate-400 text-xs">-</span>`;
    }
    return `
        <button onclick="resultSDQ${type}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', ${classId}, ${roomId}, ${termId}, ${peeId})"
            class="w-8 h-8 bg-purple-500 text-white text-xs font-bold rounded-lg shadow flex items-center justify-center">
            <i class="fas fa-chart-pie"></i>
        </button>
    `;
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
                <i class="fas fa-users-slash text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-600 dark:text-slate-400">ไม่พบข้อมูลนักเรียน</h3>
            <p class="text-slate-400 text-sm mt-1">กรุณาตรวจสอบข้อมูลห้องเรียน</p>
        </div>
    `;
    document.getElementById('tableBody').innerHTML = `<tr><td colspan="9">${emptyHtml}</td></tr>`;
    document.getElementById('mobileCards').innerHTML = emptyHtml;
}

// ========== SDQ Modal Functions (Unified) ==========
// Type mapping: std -> self, teach -> teach, par -> par
const typeMap = { std: 'self', teach: 'teach', par: 'par' };
const saveUrlMap = { 
    self: { add: 'api/save_sdq_self.php', edit: 'api/update_sdq_self.php' },
    teach: { add: 'api/save_sdq_teach.php', edit: 'api/update_sdq_teach.php' },
    par: { add: 'api/save_sdq_par.php', edit: 'api/update_sdq_par.php' }
};

// Add/Edit SDQ functions
window.addSDQstd = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('self', 'add', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.editSDQstd = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('self', 'edit', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.addSDQteach = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('teach', 'add', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.editSDQteach = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('teach', 'edit', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.addSDQpar = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('par', 'add', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.editSDQpar = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openSDQModal('par', 'edit', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

// Result SDQ functions
window.resultSDQstd = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openResultModal('self', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.resultSDQteach = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openResultModal('teach', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

window.resultSDQpar = (studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) =>
    openResultModal('par', studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee);

// Unified Modal Opener
function openSDQModal(type, mode, studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
    // Remove any existing modal first
    $('#sdqModal').remove();
    $('.modal-backdrop').remove();
    
    const titles = { self: 'นักเรียนประเมินตนเอง', teach: 'ครูเป็นผู้ประเมิน', par: 'ผู้ปกครองเป็นผู้ประเมิน' };
    const actionText = mode === 'edit' ? 'บันทึกการแก้ไข' : 'บันทึก';
    const formUrl = `template_form/form_sdq.php`;
    const saveUrl = saveUrlMap[type][mode];

    // Show loading
    Swal.fire({ title: 'กำลังโหลดฟอร์ม...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: formUrl,
        method: 'GET',
        data: { type: type, mode: mode, student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
        success: function(response) {
            Swal.close();
            
            const modalHtml = `
                <div class="modal fade" id="sdqModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content rounded-3xl overflow-hidden">
                            <div class="modal-header bg-gradient-to-r from-indigo-500 to-purple-600 text-white border-0 py-4">
                                <h5 class="modal-title font-bold">
                                    <i class="fas fa-clipboard-list mr-2"></i>
                                    แบบประเมิน SDQ (${titles[type]})
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 md:p-6">
                                ${response}
                            </div>
                            <div class="modal-footer border-0 p-4 bg-slate-50 dark:bg-slate-800">
                                <button type="button" class="px-5 py-2.5 bg-slate-500 hover:bg-slate-600 text-white font-bold rounded-xl transition" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="button" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition" id="saveSDQBtn">${actionText}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('sdqModal'));
            modal.show();

            $('#saveSDQBtn').on('click', function() {
                const formId = mode === 'edit' ? '#sdqEditForm' : '#sdqForm';
                const $form = $(formId);
                
                // Validate form
                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกข้อมูลให้ครบ',
                        text: 'ยังมีคำถามที่ยังไม่ได้ตอบ',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                const formData = $form.serialize();

                Swal.fire({ 
                    title: 'กำลังบันทึก...', 
                    html: '<div class="flex justify-center"><div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: saveUrl,
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(res) {
                        console.log('SDQ Save Response:', res);
                        Swal.close(); // Close loading first
                        
                        if (res && res.success === true) {
                            // Close modal immediately
                            $('#sdqModal').modal('hide');
                            
                            Swal.fire({ 
                                icon: 'success',
                                title: 'บันทึกสำเร็จ!', 
                                text: res.message || 'บันทึกข้อมูล SDQ เรียบร้อยแล้ว',
                                confirmButtonText: 'ตกลง',
                                confirmButtonColor: '#10b981',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: res.message || 'ไม่สามารถบันทึกข้อมูลได้',
                                confirmButtonText: 'ลองใหม่',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('SDQ Save Error:', error, xhr.responseText);
                        Swal.close(); // Close loading first
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
                            footer: '<small class="text-gray-500">Error: ' + error + '</small>',
                            confirmButtonText: 'ลองใหม่',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            document.getElementById('sdqModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        },
        error: () => Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้', 'error')
    });
}

// Unified Result Modal
function openResultModal(type, studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
    // Remove any existing modal first
    $('#resultModal').remove();
    $('.modal-backdrop').remove();
    
    const titles = { self: 'นักเรียนประเมินตนเอง', teach: 'ครูเป็นผู้ประเมิน', par: 'ผู้ปกครองเป็นผู้ประเมิน' };
    const formUrl = `template_form/form_sdq_result.php`;

    // Show loading
    Swal.fire({ title: 'กำลังโหลดข้อมูล...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: formUrl,
        method: 'GET',
        data: { type: type, student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
        success: function(response) {
            Swal.close();
            
            const modalHtml = `
                <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content rounded-3xl overflow-hidden" id="modalContentToPrint">
                            <div class="modal-header bg-gradient-to-r from-purple-500 to-pink-600 text-white border-0 py-4">
                                <h5 class="modal-title font-bold">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    แปลผล SDQ (${titles[type]})
                                </h5>
                                <button type="button" class="btn-close btn-close-white print-hide" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 md:p-6">
                                ${response}
                            </div>
                            <div class="modal-footer border-0 p-4 bg-slate-50 dark:bg-slate-800 print-hide">
                                <button type="button" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition" id="printResultBtn">
                                    <i class="fas fa-print mr-2"></i>พิมพ์
                                </button>
                                <button type="button" class="px-5 py-2.5 bg-slate-500 hover:bg-slate-600 text-white font-bold rounded-xl transition" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('resultModal'));
            modal.show();

            $('#printResultBtn').on('click', function() {
                let printContents = document.getElementById('modalContentToPrint').innerHTML;
                let printWindow = window.open('', '', 'height=800,width=900');
                printWindow.document.write('<html><head><title>พิมพ์รายงาน SDQ</title>');
                $('link[rel=stylesheet], style').each(function() { printWindow.document.write(this.outerHTML); });
                printWindow.document.write(`<style>@media print { .print-hide { display: none !important; } } @page { size: A4 portrait; margin: 15mm; }</style>`);
                printWindow.document.write('</head><body style="background:white;">');
                printWindow.document.write('<div>' + printContents + '</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 500);
            });

            document.getElementById('resultModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        },
        error: () => Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error')
    });
}
</script>


<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
