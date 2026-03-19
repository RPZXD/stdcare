<?php
/**
 * Teacher EQ Assessment View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
ob_start();
?>

<!-- Page Header -->
<div class="mb-6 md:mb-8 no-print">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-orange-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-rose-500/30">
                    <i class="fas fa-heartbeat"></i>
                </div>
                ประเมินฉลาดทางอารมณ์ (EQ)
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm md:text-base">แบบประเมิน EQ นักเรียน ชั้น ม.<?= $class ?>/<?= $room ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if ($term == 2): ?>
                <button onclick="bulkCopyTerm1()" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold shadow-lg shadow-amber-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm no-print">
                    <i class="fas fa-copy"></i>
                    คัดลอกเทอม 1 ทั้งห้อง
                </button>
            <?php endif; ?>
            <a href="report_eq_all.php" class="px-4 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-teal-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-chart-bar"></i>
                รายงานสถิติ EQ
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm no-print">
                <i class="fas fa-print"></i>
                พิมพ์รายงานสถานะ
            </button>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print" id="summaryStats">
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
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ประเมินแล้ว</p>
                <p class="text-xl font-black text-emerald-600" id="stat_done">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ยังไม่ประเมิน</p>
                <p class="text-xl font-black text-rose-600" id="stat_pending">-</p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 flex items-center justify-center no-print">
        <form method="GET" class="flex items-center gap-3">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">ภาคเรียน</span>
                <select name="term" onchange="this.form.submit()" class="bg-transparent border-0 text-slate-800 dark:text-white font-black p-0 focus:ring-0 cursor-pointer text-sm">
                    <option value="1" <?= $term == 1 ? 'selected' : '' ?>>เทอม 1</option>
                    <option value="2" <?= $term == 2 ? 'selected' : '' ?>>เทอม 2</option>
                </select>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">ปีการศึกษา</span>
                <input type="number" name="pee" value="<?= $pee ?>" onchange="this.form.submit()" 
                    class="bg-transparent border-0 text-slate-800 dark:text-white font-black p-0 focus:ring-0 w-12 text-sm text-center">
            </div>
        </form>
    </div>
</div>

<!-- Main Card -->
<div class="glass-card rounded-[2rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden no-print">
    <!-- Card Header -->
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-rose-500 to-orange-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white">
                    <i class="fas fa-list-ol text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white">รายชื่อนักเรียน</h2>
                    <p class="text-white/70 text-sm italic">คลิกปุ่ม "บันทึก/แก้ไข" เพื่อประเมิน EQ</p>
                </div>
            </div>
            <!-- Search Box -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="ค้นหานักเรียน..."
                    class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-white/20 backdrop-blur border-0 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:outline-none transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/60"></i>
            </div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full" id="eqTable">
            <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">เลขที่</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                    <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">แบบประเมิน EQ</th>
                    <th class="px-4 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">การจัดการ</th>
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
            <p class="text-slate-400 font-bold">กำลังโหลดข้อมูลนักเรียน...</p>
        </div>
    </div>
</div>

<!-- Professional Print Layout (Hidden on Screen) -->
<!-- Print Header -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4 mb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600 font-bold">สรุปสถานะการประเมินความฉลาดทางอารมณ์ (EQ)</p>
    </div>
    <div class="flex justify-between items-end mb-4 text-sm font-bold">
        <div>มัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?></div>
        <div>ปีการศึกษา <?= $pee ?> ภาคเรียนที่ <?= $term ?></div>
    </div>
    
    <div class="grid grid-cols-3 gap-4 mb-6 text-center text-sm">
        <div class="border p-2 rounded">ทั้งหมด: <span id="print_total">-</span> คน</div>
        <div class="border p-2 rounded text-emerald-600">ประเมินแล้ว: <span id="print_done">-</span> คน</div>
        <div class="border p-2 rounded text-rose-600">ยังไม่ประเมิน: <span id="print_pending">-</span> คน</div>
    </div>
</div>

<!-- Print Table -->
<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center w-12">เลขที่</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-24">รหัส</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ชื่อ-นามสกุล</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-32">สถานะประเมิน</th>
                <th class="border border-slate-300 px-2 py-2 text-center">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Populated by JS -->
        </tbody>
    </table>
</div>

<!-- Print Signature -->
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
        </div> 
        -->
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-8 italic">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- Styles -->
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
.dark .glass-card {
    background: rgba(30, 41, 59, 0.95);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.fade-in-up {
    animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

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
    .no-print, #sidebar, #navbar, #preloader, footer {
        display: none !important;
    }
    #printHeader, #printTable, #printSignature {
        display: block !important;
    }
    #printTableContent th, #printTableContent td {
        padding: 6px;
        border: 1px solid #cbd5e1;
    }
    .status-done { color: #16a34a; font-weight: bold; }
    .status-pending { color: #dc2626; font-weight: bold; }
}

@media screen {
    #printHeader, #printTable, #printSignature {
        display: none !important;
    }
}
</style>

<!-- Scripts -->
<script>
const classId = <?= $class ?>;
const roomId = <?= $room ?>;
const peeId = <?= $pee ?>;
const termId = <?= $term ?>;

document.addEventListener('DOMContentLoaded', function() {
    loadStudentData();
    
    document.getElementById('searchInput').addEventListener('input', function(e) {
        filterStudents(e.target.value.toLowerCase());
    });
});

/**
 * Bulk copy EQ data from Term 1 to Term 2 for the entire classroom
 */
window.bulkCopyTerm1 = function() {
    Swal.fire({
        title: 'ยืนยันคัดลอกข้อมูลทั้งห้อง?',
        text: "ระบบจะคัดลอกข้อมูล EQ จากเทอม 1 มายังเทอม 2 สำหรับนักเรียนทุกคนในห้องที่ยังไม่มีข้อมูลในเทอม 2",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ตกลง, คัดลอกเลย',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'api/bulk_copy_eq.php',
                method: 'GET',
                data: { class: classId, room: roomId, pee: peeId },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'คัดลอกสำเร็จ',
                            html: `คัดลอกสำเร็จ: ${response.success_count} คน<br>ข้าม (มีข้อมูลแล้ว): ${response.skip_count} คน`,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        loadStudentData(); // Refresh the list
                    } else if (response.status === 'warning') {
                        Swal.fire('แจ้งเตือน', response.message, 'info');
                    } else {
                        Swal.fire('ข้อผิดพลาด', response.message || 'เกิดข้อผิดพลาดในการคัดลอก', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
};

async function loadStudentData() {
    try {
        const response = await fetch(`api/fetch_eq_classroom.php?class=${classId}&room=${roomId}&pee=${peeId}&term=${termId}`);
        const result = await response.json();
        
        document.getElementById('loadingState').style.display = 'none';
        
        if (result.success && result.data.length > 0) {
            renderTable(result.data);
            updateStats(result.data);
            renderPrintTable(result.data);
        } else {
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading data:', error);
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลนักเรียนได้', 'error');
    }
}

function updateStats(data) {
    const total = data.length;
    const doneCount = data.filter(d => d.eq_ishave === 1).length;
    const pendingCount = total - doneCount;
    
    document.getElementById('stat_total').textContent = total;
    document.getElementById('stat_done').textContent = doneCount;
    document.getElementById('stat_pending').textContent = pendingCount;
    
    // Print stats
    document.getElementById('print_total').textContent = total;
    document.getElementById('print_done').textContent = doneCount;
    document.getElementById('print_pending').textContent = pendingCount;
}

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    const mobileContainer = document.getElementById('mobileCards');
    
    let dtHtml = '';
    let mbHtml = '';
    
    data.forEach((item, idx) => {
        const isHave = item.eq_ishave === 1;
        
        // Desktop
        dtHtml += `
            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors student-row" data-name="${item.full_name.toLowerCase()}" data-id="${item.Stu_id}">
                <td class="px-4 py-4 text-center font-bold text-slate-400">${item.Stu_no}</td>
                <td class="px-4 py-4 text-center font-semibold text-slate-600 dark:text-slate-300">${item.Stu_id}</td>
                <td class="px-4 py-4 text-left font-bold text-slate-800 dark:text-white">${item.full_name}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm">${isHave ? '✅' : '❌'}</span>
                        <button onclick="${isHave ? 'openEditModal' : 'openAddModal'}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classId}', '${roomId}', '${termId}', '${peeId}')"
                            class="px-4 py-1.5 ${isHave ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-500 hover:bg-blue-600'} text-white text-xs font-bold rounded-xl shadow transition-all">
                            <i class="fas ${isHave ? 'fa-edit' : 'fa-plus'} mr-1"></i>${isHave ? 'แก้ไข' : 'บันทึก'}
                        </button>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    ${isHave ? `
                        <button onclick="openResultModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classId}', '${roomId}', '${termId}', '${peeId}')"
                            class="px-4 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold rounded-xl shadow">
                            <i class="fas fa-chart-pie mr-1"></i>แปลผล
                        </button>
                    ` : `<span class="text-slate-400 text-xs italic">ยังไม่ประเมิน</span>`}
                </td>
            </tr>
        `;
        
        // Mobile
        mbHtml += `
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
                    <span class="text-xl">${isHave ? '✅' : '❌'}</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="${isHave ? 'openEditModal' : 'openAddModal'}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classId}', '${roomId}', '${termId}', '${peeId}')"
                        class="flex-1 py-2.5 ${isHave ? 'bg-amber-500' : 'bg-blue-500'} text-white text-xs font-black rounded-xl">
                        <i class="fas ${isHave ? 'fa-edit' : 'fa-plus'} mr-2"></i>${isHave ? 'แก้ไขข้อมูล' : 'บันทึก EQ'}
                    </button>
                    ${isHave ? `
                        <button onclick="openResultModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classId}', '${roomId}', '${termId}', '${peeId}')"
                            class="flex-1 py-2.5 bg-purple-500 text-white text-xs font-black rounded-xl">
                            <i class="fas fa-chart-pie mr-2"></i>แปลผล
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    tbody.innerHTML = dtHtml;
    mobileContainer.innerHTML = mbHtml;
}

function renderPrintTable(data) {
    const tbody = document.getElementById('printTableBody');
    let html = '';
    data.forEach(item => {
        const isHave = item.eq_ishave === 1;
        html += `
            <tr>
                <td class="border border-slate-300 px-2 py-1 text-center font-bold">${item.Stu_no}</td>
                <td class="border border-slate-300 px-2 py-1 text-center">${item.Stu_id}</td>
                <td class="border border-slate-300 px-2 py-1 text-left font-bold">${item.full_name}</td>
                <td class="border border-slate-300 px-2 py-1 text-center ${isHave ? 'status-done' : 'status-pending'}">
                    ${isHave ? '✅ ประเมินแล้ว' : '❌ ยังไม่ประเมิน'}
                </td>
                <td class="border border-slate-300 px-2 py-1"></td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

function filterStudents(term) {
    document.querySelectorAll('.student-row').forEach(row => {
        const match = row.dataset.name.includes(term) || row.dataset.id.includes(term);
        row.style.display = match ? '' : 'none';
    });
}

function showEmptyState() {
    const html = `<div class="p-12 text-center text-slate-400">
        <i class="fas fa-users-slash text-4xl mb-4"></i>
        <p class="font-bold">ไม่พบข้อมูลนักเรียน</p>
    </div>`;
    document.getElementById('tableBody').innerHTML = `<tr><td colspan="5">${html}</td></tr>`;
    document.getElementById('mobileCards').innerHTML = html;
}

// ========== EQ Modal Functions ==========

window.openAddModal = (id, name, no, cls, rm, trm, pee) => openEQModal('add', id, name, no, cls, rm, trm, pee);
window.openEditModal = (id, name, no, cls, rm, trm, pee) => openEQModal('edit', id, name, no, cls, rm, trm, pee);

function openEQModal(mode, studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
    const template = mode === 'edit' ? 'form_eq_edit.php' : 'form_eq.php';
    const api = mode === 'edit' ? 'api/update_eq.php' : 'api/insert_eq.php';
    const title = mode === 'edit' ? 'แก้ไขคะแนน EQ' : 'บันทึกคะแนน EQ';

    Swal.fire({ title: 'กำลังโหลด...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: `template_form/${template}`,
        method: 'GET',
        data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
        success: function(response) {
            Swal.close();
            $('#eqModal').remove();
            
            const modalHtml = `
                <div class="modal fade" id="eqModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content rounded-3xl overflow-hidden shadow-2xl">
                            <div class="modal-header bg-gradient-to-r from-rose-500 to-orange-600 text-white border-0 py-4">
                                <h5 class="modal-title font-bold"><i class="fas fa-heartbeat mr-2"></i>${title}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-6">${response}</div>
                            <div class="modal-footer border-0 bg-slate-50 p-4">
                                <button type="button" class="px-6 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                                <button type="button" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30" id="saveEQBtn">บันทึกข้อมูล</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('eqModal'));
            modal.show();

            $('#saveEQBtn').on('click', function() {
                const formId = mode === 'edit' ? '#eqEditForm' : '#eqForm';
                const $form = $(formId);
                
                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'กำลังบันทึก...',
                    text: 'กรุณารอสักครู่ระบบกำลังประมวลผล',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: api,
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        Swal.close(); // Close loading state
                        
                        if (res.success) {
                            // Hide modal immediately
                            if (modal) {
                                modal.hide();
                            }
                            $('#eqModal').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', '');

                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ',
                                text: res.message || 'ข้อมูลได้รับการบันทึกเรียบร้อยแล้ว',
                                showConfirmButton: true,
                                confirmButtonText: 'ตกลง',
                                confirmButtonColor: '#10b981',
                                allowOutsideClick: false
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: res.message || 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('Save Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'ล้มเหลว',
                            text: 'เกิดปัญหาในการติดต่อกับเซิร์ฟเวอร์ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });
        },
        error: () => Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้', 'error')
    });
}

window.openResultModal = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
    Swal.fire({ title: 'กำลังโหลด...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: 'template_form/form_eq_result.php',
        method: 'GET',
        data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
        success: function(response) {
            Swal.close();
            $('#resultModal').remove();
            
            const modalHtml = `
                <div class="modal fade" id="resultModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content rounded-3xl overflow-hidden shadow-2xl" id="printArea">
                            <div class="modal-header bg-gradient-to-r from-purple-500 to-indigo-600 text-white border-0 py-4 no-print">
                                <h5 class="modal-title font-bold"><i class="fas fa-chart-pie mr-2"></i>แปลผล EQ</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-6">${response}</div>
                            <div class="modal-footer border-0 bg-slate-50 p-4 no-print">
                                <button type="button" class="px-6 py-2 bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30" onclick="printModal()">🖨️ พิมพ์</button>
                                <button type="button" class="px-6 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            new bootstrap.Modal(document.getElementById('resultModal')).show();
        }
    });
};

window.printModal = function() {
    const printContents = document.querySelector('#printArea').innerHTML;
    const printWindow = window.open('', '', 'height=800,width=900');
    printWindow.document.write('<html><head><title>พิมพ์แปลผล EQ</title>');
    $('link[rel=stylesheet], style').each(function() { 
        printWindow.document.write(this.outerHTML); 
    });
    printWindow.document.write('<style>@media print { .no-print { display: none !important; } @page { size: A4 portrait; margin: 15mm; } .modal-footer, .modal-header { display: none !important; } }</style>');
    printWindow.document.write('</head><body style="background:white; padding: 20px;">');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 500);
};
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
