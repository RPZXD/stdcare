<?php
/**
 * Teacher Visit Home View - MVC Pattern
 * Modern UI for Home Visit System with Tailwind CSS
 */
ob_start();
?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-12 text-center fade-in-up">
        <div class="bg-white dark:bg-slate-800 rounded-3xl md:rounded-[2.5rem] p-5 md:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transform transition-transform hover:rotate-6">
                    <i class="fas fa-house-user text-4xl"></i>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-2 leading-tight">
                        แบบฟอร์มบันทึกการเยี่ยมบ้านนักเรียน
                    </h1>
                    <p class="text-lg text-slate-500 dark:text-slate-400 font-medium">
                        ชั้นมัธยมศึกษาปีที่ <span class="text-blue-600 dark:text-blue-400"><?= htmlspecialchars($class . "/" . $room); ?></span> • ปีการศึกษา <?= htmlspecialchars($pee); ?>
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="visithome_report_class.php" class="px-6 py-3 bg-white dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-2xl shadow-md border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all flex items-center gap-2">
                        <i class="fas fa-chart-pie text-indigo-500"></i> สถิติข้อมูล
                    </a>
                    <button onclick="printPage()" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all flex items-center gap-2">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden fade-in-up" style="animation-delay: 0.1s">
        <div class="px-4 py-5 md:px-8 md:py-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-base md:text-xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-users text-sm md:text-base"></i>
                </span>
                <span class="leading-tight truncate md:whitespace-nowrap">รายชื่อนักเรียนและสถานะ</span>
            </h2>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-[10px] md:text-sm">
                <span class="flex items-center gap-1.5 text-slate-500 font-medium"><i class="fas fa-check-circle text-emerald-500"></i> เยี่ยมแล้ว</span>
                <span class="hidden sm:inline text-slate-300">|</span>
                <span class="flex items-center gap-1.5 text-slate-500 font-medium"><i class="fas fa-times-circle text-rose-500"></i> ยังไม่ได้เยี่ยม</span>
            </div>
        </div>

        <div class="p-0 md:p-8">
            <!-- Desktop Table -->
            <div class="desktop-table overflow-x-auto p-8">
                <table id="record_table" class="w-full">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 text-sm uppercase tracking-wider">
                            <th class="px-4 py-4 text-center">เลขที่</th>
                            <th class="px-4 py-4 text-center">รหัสนักเรียน</th>
                            <th class="px-4 py-4 text-left">ชื่อ-นามสกุล</th>
                            <th class="px-4 py-4 text-center">เยี่ยมบ้านครั้งที่ 1 (100%)</th>
                            <th class="px-4 py-4 text-center">เยี่ยมบ้านครั้งที่ 2 (25%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                        <!-- Loaded by DataTable via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="mobileCards" class="mobile-card p-4 space-y-4 bg-slate-50/50 dark:bg-slate-900/10">
                <!-- Populated by JS via DataTable drawCallback -->
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 fade-in-up" style="animation-delay: 0.2s">
        <div class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/20 rounded-3xl p-6">
            <h4 class="text-emerald-800 dark:text-emerald-400 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i> เยี่ยมบ้านครั้งที่ 1
            </h4>
            <p class="text-emerald-700 dark:text-emerald-500/80 text-sm leading-relaxed">
                ดำเนินการเยี่ยมบ้านและกรอกข้อมูลนักเรียนให้ครบทุกคน (100%) เพื่อทำความรู้จักและสร้างความสัมพันธ์อันดีกับผู้ปกครอง
            </p>
        </div>
        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/20 rounded-3xl p-6">
            <h4 class="text-amber-800 dark:text-amber-400 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> เยี่ยมบ้านครั้งที่ 2
            </h4>
            <p class="text-amber-700 dark:text-amber-500/80 text-sm leading-relaxed">
                ดำเนินการเยี่ยมบ้านเฉพาะนักเรียนกลุ่มเสี่ยง หรือกลุ่มที่มีความต้องการพิเศษ อย่างน้อยร้อยละ 25 ของจำนวนนักเรียนทั้งหมด
            </p>
        </div>
    </div>
</div>

<!-- Add/Edit Visit Modals -->
<div id="addVisitModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('addVisitModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-100 dark:border-slate-700">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white flex items-center gap-3">
                    <i class="fas fa-plus-circle"></i> บันทึกข้อมูลการเยี่ยมบ้าน
                </h3>
                <button onclick="closeModal('addVisitModal')" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="addVisitContent" class="p-8 max-h-[70vh] overflow-y-auto">
                <!-- Form content loaded via AJAX -->
            </div>
            <div class="p-8 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button onclick="closeModal('addVisitModal')" class="px-6 py-3 bg-white dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-2xl border border-slate-200 dark:border-slate-600 hover:bg-slate-50 transition-all">ยกเลิก</button>
                <button id="saveAddVisit" class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<div id="editVisitModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('editVisitModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-100 dark:border-slate-700">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white flex items-center gap-3">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลการเยี่ยมบ้าน
                </h3>
                <button onclick="closeModal('editVisitModal')" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="editVisitContent" class="p-8 max-h-[70vh] overflow-y-auto">
                <!-- Form content loaded via AJAX -->
            </div>
            <div class="p-8 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button onclick="closeModal('editVisitModal')" class="px-6 py-3 bg-white dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-2xl border border-slate-200 dark:border-slate-600 hover:bg-slate-50 transition-all">ยกเลิก</button>
                <button id="saveEditVisit" class="px-8 py-3 bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 hover:bg-amber-700 transition-all">บันทึกการแก้ไข</button>
            </div>
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

@media (max-width: 768px) {
    .mobile-card { display: block !important; }
    .desktop-table { display: none !important; }
    .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_paginate {
        text-align: center !important;
        margin-top: 1rem !important;
    }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
    .desktop-table { display: block !important; }
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 2rem;
}
.dataTables_wrapper .dataTables_filter input {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 0.75rem 1.25rem;
    width: 100%;
    max-width: 300px;
    font-weight: 500;
    transition: all 0.3s;
}
@media (max-width: 640px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        width: 100%;
        margin-bottom: 1rem;
        display: block;
        text-align: left !important;
    }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
    }
    .dataTables_wrapper .dataTables_length select {
        width: 100% !important;
        border-radius: 1rem;
        padding: 0.75rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
    }
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    outline: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 0.75rem !important;
    padding: 0.5rem 1rem !important;
    margin: 0 0.125rem !important;
    border: 1px solid #e2e8f0 !important;
    background: white !important;
    font-weight: 600 !important;
    color: #475569 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #3b82f6 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
}

.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Modal animation */
#addVisitModal, #editVisitModal {
    transition: opacity 0.3s ease-out;
}
#addVisitModal.hidden, #editVisitModal.hidden {
    opacity: 0;
    pointer-events: none;
    display: none;
}
#addVisitModal:not(.hidden), #editVisitModal:not(.hidden) {
    opacity: 1;
    display: block;
}
</style>

<script>
$(document).ready(function() {
    // Page load table
    loadTable();

    // Modal Close logic
    window.closeModal = function(modalId) {
        $(`#${modalId}`).addClass('hidden');
        $('body').css('overflow', 'auto');
    };

    window.openModal = function(modalId) {
        $(`#${modalId}`).removeClass('hidden');
        $('body').css('overflow', 'hidden');
    };

    // Print logic
    window.printPage = function() {
        window.print();
    };

    // Load Data into Table
    async function loadTable() {
        try {
            const table = $('#record_table').DataTable({
                destroy: true,
                ajax: {
                    url: 'api/fetch_visit_class.php',
                    data: { class: <?= $class ?>, room: <?= $room ?>, pee: <?= $pee ?> },
                    dataSrc: function(json) {
                        return json.success ? json.data : [];
                    }
                },
                columns: [
                    { 
                        data: null, 
                        render: (data, type, row, meta) => meta.row + 1, 
                        className: 'text-center font-bold text-slate-400',
                        createdCell: (td) => $(td).attr('data-label', 'เลขที่')
                    },
                    { 
                        data: 'Stu_id', 
                        className: 'text-center font-semibold text-slate-800 dark:text-white',
                        createdCell: (td) => $(td).attr('data-label', 'รหัสนักเรียน')
                    },
                    { 
                        data: 'FullName', 
                        className: 'text-left font-bold text-slate-800 dark:text-white',
                        createdCell: (td) => $(td).attr('data-label', 'ชื่อ-นามสกุล'),
                        render: (data) => `<span class="font-bold text-lg md:text-base">${data}</span>`
                    },
                    { 
                        data: 'visit_status1', 
                        className: 'text-center',
                        createdCell: (td) => $(td).attr('data-label', 'เยี่ยมบ้านครั้งที่ 1'),
                        render: function(data, type, row) {
                            let statusHtml = '';
                            if (data == 2) {
                                statusHtml = `
                                    <div class="flex items-center gap-2">
                                        <span class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0 border-2 border-white dark:border-slate-800" title="เยี่ยมแล้วและมีรูปภาพ"><i class="fas fa-check"></i></span>
                                        <span class="text-emerald-600 font-bold text-sm md:hidden">เยี่ยมแล้ว</span>
                                    </div>
                                `;
                            } else if (data == 1) {
                                statusHtml = `
                                    <div class="flex items-center gap-2">
                                        <span class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center shrink-0 border-2 border-white dark:border-slate-800" title="กรอกข้อมูลแล้วแต่ยังไม่มีรูปภาพ"><i class="fas fa-image"></i></span>
                                        <div class="flex flex-col items-start md:hidden">
                                            <span class="text-amber-600 font-bold text-[10px] leading-tight text-left">บันทึกแล้ว</span>
                                            <span class="text-slate-400 font-medium text-[8px] leading-tight text-left">ยังไม่มีรูป</span>
                                        </div>
                                    </div>
                                `;
                            } else {
                                statusHtml = `<span class="text-slate-400 font-bold text-sm md:hidden">ยังไม่ได้เยี่ยม</span>`;
                            }

                            return `
                                <div class="flex items-center justify-between w-full md:justify-center gap-3">
                                    ${statusHtml}
                                    <button onclick="${data > 0 ? 'editVisit(1, \'' + row.Stu_id + '\')' : 'addVisit(1, \'' + row.Stu_id + '\')'}" 
                                            class="px-5 py-2.5 ${data > 0 ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30'} text-white font-bold rounded-xl shadow-lg transition-all whitespace-nowrap">
                                        ${data > 0 ? '<i class="fas fa-edit"></i> แก้ไข' : '<i class="fas fa-plus mr-1"></i> บันทึก'}
                                    </button>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'visit_status2', 
                        className: 'text-center',
                        createdCell: (td) => $(td).attr('data-label', 'เยี่ยมบ้านครั้งที่ 2'),
                        render: function(data, type, row) {
                            let statusHtml = '';
                            if (data == 2) {
                                statusHtml = `
                                    <div class="flex items-center gap-2">
                                        <span class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0 border-2 border-white dark:border-slate-800" title="เยี่ยมแล้วและมีรูปภาพ"><i class="fas fa-check"></i></span>
                                        <span class="text-emerald-600 font-bold text-sm md:hidden">เยี่ยมแล้ว</span>
                                    </div>
                                `;
                            } else if (data == 1) {
                                statusHtml = `
                                    <div class="flex items-center gap-2">
                                        <span class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center shrink-0 border-2 border-white dark:border-slate-800" title="กรอกข้อมูลแล้วแต่ยังไม่มีรูปภาพ"><i class="fas fa-image"></i></span>
                                        <div class="flex flex-col items-start md:hidden">
                                            <span class="text-amber-600 font-bold text-[10px] leading-tight text-left">บันทึกแล้ว</span>
                                            <span class="text-slate-400 font-medium text-[8px] leading-tight text-left">ยังไม่มีรูป</span>
                                        </div>
                                    </div>
                                `;
                            } else {
                                statusHtml = `<span class="text-slate-400 font-bold text-sm md:hidden">ยังไม่ได้เยี่ยม</span>`;
                            }

                            return `
                                <div class="flex items-center justify-between w-full md:justify-center gap-3">
                                    ${statusHtml}
                                    <button onclick="${data > 0 ? 'editVisit(2, \'' + row.Stu_id + '\')' : 'addVisit(2, \'' + row.Stu_id + '\')'}" 
                                            class="px-5 py-2.5 ${data > 0 ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30'} text-white font-bold rounded-xl shadow-lg transition-all whitespace-nowrap">
                                        ${data > 0 ? '<i class="fas fa-edit"></i> แก้ไข' : '<i class="fas fa-plus mr-1"></i> บันทึก'}
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "ค้นหาชื่อหรือรหัสนักเรียน...",
                    emptyTable: "ไม่พบข้อมูลนักเรียนในห้องนี้",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ คน",
                    paginate: {
                        next: '<i class="fas fa-chevron-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>'
                    }
                },
                pageLength: 50,
                drawCallback: function(settings) {
                    const api = this.api();
                    const rows = api.rows({ page: 'current' }).data();
                    const mobileCards = $('#mobileCards');
                    mobileCards.empty();

                    if (rows.length === 0) {
                        mobileCards.html('<div class="text-center p-8 text-slate-400 font-medium">ไม่พบข้อมูล</div>');
                        return;
                    }

                    rows.each(function(row, i) {
                        const v1 = row.visit_status1;
                        const v2 = row.visit_status2;
                        
                        const getStatusBadge = (status) => {
                            if (status == 2) return '<span class="flex items-center gap-1.5 text-emerald-600 font-bold text-xs"><i class="fas fa-check-circle"></i> เยี่ยมแล้ว</span>';
                            if (status == 1) return '<span class="flex items-center gap-1.5 text-amber-500 font-bold text-xs"><i class="fas fa-exclamation-circle"></i> ยังไม่มีรูปภาพ</span>';
                            return '<span class="flex items-center gap-1.5 text-slate-400 font-bold text-xs"><i class="fas fa-history"></i> ยังไม่ได้เยี่ยม</span>';
                        };

                        const card = `
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm space-y-4 fade-in-up" style="animation-delay: ${i * 0.05}s">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-black text-slate-800 dark:text-white leading-tight mb-1">${row.FullName}</h4>
                                    <div class="flex gap-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <span>เลขที่: ${row.Stu_no}</span>
                                        <span>รหัส: ${row.Stu_id}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <!-- Visit 1 -->
                                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl p-4 flex items-center justify-between border border-slate-100 dark:border-slate-800">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">เยี่ยมบ้านครั้งที่ 1</span>
                                        <div class="flex items-center gap-2">
                                            ${getStatusBadge(v1)}
                                        </div>
                                    </div>
                                    <button onclick="${v1 > 0 ? 'editVisit(1, \'' + row.Stu_id + '\')' : 'addVisit(1, \'' + row.Stu_id + '\')'}" 
                                            class="px-4 py-2 ${v1 > 0 ? 'bg-amber-500' : 'bg-blue-600'} text-white text-xs font-bold rounded-xl shadow-lg transition-transform active:scale-95">
                                        ${v1 > 0 ? '<i class="fas fa-edit mr-1"></i> แก้ไข' : '<i class="fas fa-plus mr-1"></i> บันทึก'}
                                    </button>
                                </div>

                                <!-- Visit 2 -->
                                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl p-4 flex items-center justify-between border border-slate-100 dark:border-slate-800">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">เยี่ยมบ้านครั้งที่ 2</span>
                                        <div class="flex items-center gap-2">
                                            ${getStatusBadge(v2)}
                                        </div>
                                    </div>
                                    <button onclick="${v2 > 0 ? 'editVisit(2, \'' + row.Stu_id + '\')' : 'addVisit(2, \'' + row.Stu_id + '\')'}" 
                                            class="px-4 py-2 ${v2 > 0 ? 'bg-amber-500' : 'bg-blue-600'} text-white text-xs font-bold rounded-xl shadow-lg transition-transform active:scale-95">
                                        ${v2 > 0 ? '<i class="fas fa-edit mr-1"></i> แก้ไข' : '<i class="fas fa-plus mr-1"></i> บันทึก'}
                                    </button>
                                </div>
                            </div>
                        </div>
                        `;
                        mobileCards.append(card);
                    });
                }
            });
        } catch (error) {
            console.error('Error loading Table:', error);
        }
    }

    // Modal Form loading
    window.addVisit = function(term, stuId) {
        Swal.fire({
            title: 'กำลังโหลดฟอร์ม...',
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '?action=get_add_form',
            method: 'GET',
            data: { term: term, pee: <?= $pee ?>, stuId: stuId },
            success: function(response) {
                Swal.close();
                $('#addVisitContent').html(response);
                openModal('addVisitModal');
                initializeUploadAreas('addVisitForm');
            }
        });
    };

    window.editVisit = function(term, stuId) {
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '?action=get_edit_form',
            method: 'GET',
            data: { term: term, pee: <?= $pee ?>, stuId: stuId },
            success: function(response) {
                Swal.close();
                $('#editVisitContent').html(response);
                openModal('editVisitModal');
                initializeUploadAreas('editVisitForm');
            }
        });
    };

    // Save Actions
    $('#saveAddVisit').on('click', function() {
        submitVisitForm('addVisitForm');
    });

    $('#saveEditVisit').on('click', function() {
        submitVisitForm('editVisitForm');
    });

    function submitVisitForm(formId) {
        const formElement = document.getElementById(formId);
        if (!validateForm(formId)) return;

        const formData = new FormData(formElement);
        const url = formId === 'addVisitForm' ? 'api/save_visit_data.php' : 'api/update_visit_data.php';

        Swal.fire({
            title: 'กำลังบันทึก...',
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
                        closeModal(formId === 'addVisitForm' ? 'addVisitModal' : 'editVisitModal');
                        loadTable();
                    } else {
                        Swal.fire('ล้มเหลว', res.message, 'error');
                    }
                } catch(e) {
                    Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
                        closeModal(formId === 'addVisitForm' ? 'addVisitModal' : 'editVisitModal');
                        loadTable();
                }
            }
        });
    }

    function validateForm(formId) {
        const form = document.getElementById(formId);
        const required = form.querySelectorAll('[required]');
        let valid = true;
        
        required.forEach(el => {
            if (!el.value) {
                valid = false;
                el.classList.add('ring-2', 'ring-rose-500');
            } else {
                el.classList.remove('ring-2', 'ring-rose-500');
            }
        });

        if (!valid) {
            Swal.fire('ไม่สำเร็จ', 'กรุณากรอกข้อมูลที่สำคัญให้ครบถ้วน', 'warning');
        }
        return valid;
    }

    function initializeUploadAreas(formId) {
        // Reuse logic from current handler but with modern CSS
        const areas = document.querySelectorAll(`#${formId} .upload-area`);
        areas.forEach(area => {
            // Drag & Drop visual feedback
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('bg-blue-50', 'border-blue-400');
            });
            area.addEventListener('dragleave', () => {
                area.classList.remove('bg-blue-50', 'border-blue-400');
            });
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('bg-blue-50', 'border-blue-400');
            });
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
