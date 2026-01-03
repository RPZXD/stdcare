<?php
/**
 * View: Manage RFID Data (Officer)
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Design
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.05);
    }
    
    .rfid-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rfid-card:hover {
        transform: translateY(-4px);
    }

    /* DataTable Overrides */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.4rem 0.8rem !important;
        margin: 0 0.2rem !important;
        border-radius: 0.75rem !important;
        border: none !important;
        font-weight: 800 !important;
        background: transparent !important;
        font-size: 0.75rem !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: white !important;
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40 group">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl group-hover:bg-indigo-500/20 transition-all duration-700"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-slate-500/10 rounded-full -ml-40 -mb-40 blur-3xl group-hover:bg-slate-500/20 transition-all duration-700"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-slate-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-id-card text-3xl text-indigo-100"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            จัดการข้อมูล <span class="text-indigo-600 italic">บัตร RFID</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            ลงทะเบียนและแก้ไขรหัสบัตรประจำตัวนักเรียน (ปีการศึกษา <?php echo "$pee/$term"; ?>)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Not Registered Table -->
        <div class="glass-effect rounded-[2.5rem] p-6 sm:p-8 shadow-xl border border-white/50 dark:border-slate-700/50 flex flex-col animate-fadeIn" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight leading-none">ยังไม่มีบัตร</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic pt-1">Unregistered Students</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 mb-6 bg-slate-50/50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                <div class="flex-1 min-w-[120px]">
                    <select id="filterClass" class="w-full px-4 py-2 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-xs"></select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <select id="filterRoom" class="w-full px-4 py-2 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-xs"></select>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto overflow-y-visible">
                <table id="studentTable" class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic border-b border-slate-100 dark:border-slate-800">
                            <th class="px-4 py-3">เลขที่</th>
                            <th class="px-4 py-3">รหัส</th>
                            <th class="px-4 py-3">ชื่อ-สกุล</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="font-bold text-slate-700 dark:text-slate-300"></tbody>
                </table>
            </div>

            <!-- Mobile Card View (Populated by JS) -->
            <div id="studentMobileView" class="md:hidden space-y-4"></div>
        </div>

        <!-- Registered Table -->
        <div class="glass-effect rounded-[2.5rem] p-6 sm:p-8 shadow-xl border border-white/50 dark:border-slate-700/50 flex flex-col animate-fadeIn" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight leading-none">มีบัตรแล้ว</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic pt-1">Registered RFID Cards</p>
                    </div>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto overflow-y-visible">
                <table id="rfidTable" class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic border-b border-slate-100 dark:border-slate-800">
                            <th class="px-4 py-3">เลขที่</th>
                            <th class="px-4 py-3">ชื่อ-สกุล</th>
                            <th class="px-4 py-3">RFID Code</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="font-bold text-slate-700 dark:text-slate-300"></tbody>
                </table>
            </div>

            <!-- Mobile Card View (Populated by JS) -->
            <div id="rfidMobileView" class="md:hidden space-y-4"></div>
        </div>
    </div>

    <!-- CSV Section -->
    <div class="glass-effect rounded-[2.5rem] p-6 sm:p-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.3s">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                <i class="fas fa-file-csv"></i>
            </div>
            <div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">ระบบจัดการแบบกลุ่ม (CSV)</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic pt-1">Batch Registration via CSV File</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">
            <!-- Download Section -->
            <div class="space-y-6 bg-slate-50/50 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h5 class="text-sm font-black text-slate-700 dark:text-slate-300">1. ดาวน์โหลดเทมเพลต</h5>
                    <p class="text-[11px] font-bold text-slate-400 italic">เลือกชั้นและห้องเพื่อดูว่าใครยังไม่มีบัตร</p>
                </div>
                <div class="flex flex-wrap items-end gap-3">
                    <div class="w-full sm:w-auto">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">ชั้น</label>
                        <select id="csvFilterClass" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-sm min-w-[120px]"></select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">ห้อง</label>
                        <select id="csvFilterRoom" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-sm min-w-[120px]"></select>
                    </div>
                    <button id="downloadTemplateBtn" class="w-full sm:w-auto px-6 py-3.5 bg-slate-800 text-white rounded-xl font-black text-xs hover:bg-black transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i> ดาวน์โหลด Template
                    </button>
                </div>
            </div>

            <!-- Upload Section -->
            <form id="csvUploadForm" class="space-y-6 bg-indigo-50/30 dark:bg-indigo-900/10 p-6 rounded-3xl border border-indigo-100 dark:border-indigo-800/50">
                <div class="space-y-1">
                    <h5 class="text-sm font-black text-slate-700 dark:text-slate-300">2. อัปโหลดข้อมูล</h5>
                    <p class="text-[11px] font-bold text-slate-400 italic">เลือกไฟล์ CSV ที่เติมข้อมูลรหัสบัตรเรียบร้อยแล้ว</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                    <div class="flex-1">
                        <input type="file" id="csv_file_input" name="rfid_csv_file" accept=".csv" required
                            class="w-full file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:transition-all text-xs text-slate-500 font-bold bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-100 dark:border-slate-700 cursor-pointer">
                    </div>
                    <button type="submit" id="uploadCsvBtn" class="px-8 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-xs shadow-xl shadow-indigo-600/20 hover:scale-105 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-upload"></i> อัปโหลดและลงทะเบียน
                    </button>
                </div>
                <div id="uploadResult" class="mt-4 empty:hidden bg-white/70 dark:bg-slate-900/70 p-4 rounded-2xl text-xs"></div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    const STUDENT_API = '../controllers/StudentController.php';
    const RFID_API = '../controllers/StudentRfidController.php';
    let studentTable, rfidTable;

    // Helper: Escape HTML
    function escapeHtml(str) {
        if (!str && str !== 0) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // --- 1. Load Dropdowns ---
    async function initFilters() {
        try {
            const res = await $.getJSON(`${STUDENT_API}?action=get_filters`);
            const opts = '<option value="">-- เลือก --</option>';
            const $c1 = $('#filterClass').html(opts);
            const $r1 = $('#filterRoom').html(opts);
            const $c2 = $('#csvFilterClass').html(opts);
            const $r2 = $('#csvFilterRoom').html(opts);

            res.majors.forEach(m => { $c1.append(`<option value="${m}">${m}</option>`); $c2.append(`<option value="${m}">${m}</option>`); });
            res.rooms.forEach(r => { $r1.append(`<option value="${r}">${r}</option>`); $r2.append(`<option value="${r}">${r}</option>`); });
            
            setupDataTables();
        } catch (e) { console.error(e); }
    }

    // --- 2. DataTables Setup ---
    function setupDataTables() {
        // Students without cards
        studentTable = $('#studentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${STUDENT_API}?action=search_student`,
                type: 'POST',
                data: d => { d.major = $('#filterClass').val(); d.room = $('#filterRoom').val(); }
            },
            columns: [
                { data: "Stu_no", className: "px-4 py-4 text-xs font-black italic text-indigo-500" },
                { data: "Stu_id", className: "px-4 py-4 text-[11px] font-bold text-slate-400" },
                { 
                    data: null, 
                    className: "px-4 py-4 text-[13px]",
                    render: (a,b,row) => `<div class="truncate">${row.Stu_name} ${row.Stu_sur}</div>` 
                },
                { 
                    data: "Stu_id",
                    className: "px-4 py-4 text-right",
                    render: (data) => `<button onclick="doRegister('${data}')" class="px-4 py-1.5 bg-indigo-500 text-white rounded-lg text-[10px] font-black hover:bg-indigo-600 transition-all">ลงทะเบียน</button>`,
                    orderable: false
                }
            ],
            dom: 'rtp',
            pageLength: 8,
            language: { zeroRecords: "ไม่พบนักเรียนที่ยังไม่มีบัตร" },
            drawCallback: function() {
                renderMobileCards('student', this.api().rows({page:'current'}).data().toArray());
            }
        });

        // Students with cards
        rfidTable = $('#rfidTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: `${RFID_API}?action=list_ssp`, type: 'POST' },
            columns: [
                { data: "stu_no", className: "px-4 py-4 text-xs font-black italic text-emerald-500" },
                { 
                    data: null, 
                    className: "px-4 py-4 text-[13px]",
                    render: (a,b,row) => `<div class="truncate">${row.stu_name} ${row.stu_sur}</div>` 
                },
                { 
                    data: "rfid_code",
                    className: "px-4 py-4 font-mono text-[11px] text-slate-400",
                    render: d => `<span class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">${d}</span>`
                },
                { 
                    data: "id",
                    className: "px-4 py-4 text-right",
                    render: (data, t, row) => `
                        <div class="flex gap-1 justify-end">
                            <button onclick="doEdit('${data}', '${row.rfid_code}', '${row.stu_name}')" class="p-2 bg-amber-500/10 text-amber-600 rounded-lg text-[10px] items-center justify-center hover:bg-amber-500 hover:text-white transition-all"><i class="fas fa-edit"></i></button>
                            <button onclick="doDelete('${data}', '${row.stu_name}')" class="p-2 bg-rose-500/10 text-rose-600 rounded-lg text-[10px] items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></button>
                        </div>
                    `,
                    orderable: false
                }
            ],
            dom: 'rtp',
            pageLength: 8,
            order: [[0, "asc"]],
            language: { zeroRecords: "ไม่พบนักเรียนที่มีบัตร" },
            drawCallback: function() {
                renderMobileCards('rfid', this.api().rows({page:'current'}).data().toArray());
            }
        });

        $('#filterClass, #filterRoom').on('change', () => studentTable.ajax.reload());
    }

    // --- 3. Mobile Render ---
    function renderMobileCards(type, data) {
        const $container = type === 'student' ? $('#studentMobileView') : $('#rfidMobileView');
        $container.empty();
        data.forEach(row => {
            if (type === 'student') {
                $container.append(`
                    <div class="rfid-card glass-effect p-5 rounded-3xl border border-white/50 shadow-lg">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-500 flex items-center justify-center text-white text-[10px] font-black italic shadow-inner">${row.Stu_no}</span>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-white">${row.Stu_name} ${row.Stu_sur}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase italic">ID: ${row.Stu_id}</p>
                                </div>
                            </div>
                        </div>
                        <button onclick="doRegister('${row.Stu_id}')" class="w-full py-3 bg-indigo-600 text-white rounded-2xl font-black text-[11px] shadow-lg shadow-indigo-600/20 active:scale-95 transition-all uppercase tracking-widest">ลงทะเบียน RFID</button>
                    </div>
                `);
            } else {
                $container.append(`
                    <div class="rfid-card glass-effect p-5 rounded-3xl border border-white/50 shadow-lg">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center text-white text-[10px] font-black italic shadow-inner">${row.stu_no}</span>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-white">${row.stu_name} ${row.stu_sur}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase italic">ID: ${row.stu_id}</p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <button onclick="doEdit('${row.id}', '${row.rfid_code}', '${row.stu_name}')" class="w-8 h-8 bg-amber-500/10 text-amber-600 rounded-lg flex items-center justify-center"><i class="fas fa-edit text-xs"></i></button>
                                <button onclick="doDelete('${row.id}', '${row.stu_name}')" class="w-8 h-8 bg-rose-500/10 text-rose-600 rounded-lg flex items-center justify-center"><i class="fas fa-trash text-xs"></i></button>
                            </div>
                        </div>
                        <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-3 rounded-2xl border border-emerald-100/50 dark:border-emerald-800/20 text-center">
                            <p class="text-[8px] font-black text-emerald-600 uppercase tracking-widest mb-1 italic">RFID CODE</p>
                            <p class="font-mono text-sm font-black text-slate-700 dark:text-slate-300 tracking-wider">${row.rfid_code}</p>
                        </div>
                    </div>
                `);
            }
        });
    }

    // --- 4. Actions ---
    window.doRegister = async function(stuId) {
        const { value: code } = await Swal.fire({
            title: 'ลงทะเบียน RFID',
            input: 'text',
            inputLabel: `ID: ${stuId}`,
            inputPlaceholder: 'แตะบัตรที่เครื่องอ่าน...',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            confirmButtonColor: '#4f46e5',
            borderRadius: '1.5rem'
        });
        if (code) {
            const res = await $.post(`${RFID_API}?action=register`, { stu_id: stuId, rfid_code: code }, null, 'json');
            if (res.success) { Swal.fire('สำเร็จ', 'บันทึกแล้ว', 'success'); refresh(); }
            else Swal.fire('ผิดพลาด', res.error, 'error');
        }
    };

    window.doEdit = async function(id, oldCode, name) {
        const { value: code } = await Swal.fire({
            title: `แก้ไข RFID - ${name}`,
            input: 'text',
            inputValue: oldCode,
            showCancelButton: true,
            confirmButtonText: 'อัปเดต',
            confirmButtonColor: '#f59e0b',
            borderRadius: '1.5rem'
        });
        if (code && code !== oldCode) {
            const res = await $.post(`${RFID_API}?action=update`, { id: id, rfid_code: code }, null, 'json');
            if (res.success) { Swal.fire('สำเร็จ', 'อัปเดตแล้ว', 'success'); refresh(); }
            else Swal.fire('ผิดพลาด', res.error, 'error');
        }
    };

    window.doDelete = async function(id, name) {
        const res = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `ต้องการยกเลิกการลงทะเบียนของ ${name}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            confirmButtonText: 'ใช่, ลบเลย',
            borderRadius: '1.5rem'
        });
        if (res.isConfirmed) {
            const data = await $.post(`${RFID_API}?action=delete`, { id: id }, null, 'json');
            if (data.success) { Swal.fire('สำเร็จ', 'ลบแล้ว', 'success'); refresh(); }
            else Swal.fire('ผิดพลาด', data.error, 'error');
        }
    };

    function refresh() { studentTable.ajax.reload(null, false); rfidTable.ajax.reload(null, false); }

    // --- 5. CSV Handlers ---
    $('#downloadTemplateBtn').click(function(){
        const m = $('#csvFilterClass').val(), r = $('#csvFilterRoom').val();
        if (!m || !r) return Swal.fire('ข้อมูลไม่ครบ', 'เลือกชั้นและห้องก่อนดาวน์โหลด', 'info');
        window.location.href = `${RFID_API}?action=download_unregistered_csv&major=${encodeURIComponent(m)}&room=${encodeURIComponent(r)}`;
    });

    $('#csvUploadForm').on('submit', function(e){
        e.preventDefault();
        const btn = $('#uploadCsvBtn'), resDiv = $('#uploadResult');
        const formData = new FormData(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังอัปโหลด');
        resDiv.empty();

        $.ajax({
            url: `${STUDENT_API}?action=upload_rfid_csv`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: r => {
                if (r.status === 'completed') {
                    const rep = r.report;
                    resDiv.html(`
                        <div class="space-y-2">
                            <p class="font-black text-emerald-600 mb-2 italic tracking-widest uppercase text-[10px]">Upload Results:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl border border-emerald-100 dark:border-emerald-800 font-bold text-emerald-700 dark:text-emerald-400">✅ ใหม่: ${rep.success}</div>
                                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-100 dark:border-blue-800 font-bold text-blue-700 dark:text-blue-400">🔄 อัปเดต: ${rep.updated}</div>
                                <div class="p-2 bg-rose-50 dark:bg-rose-900/30 rounded-xl border border-rose-100 dark:border-rose-800 font-bold text-rose-700 dark:text-rose-400">❌ พลาด: ${rep.failed}</div>
                                <div class="p-2 bg-slate-50 dark:bg-slate-900/30 rounded-xl border border-slate-100 dark:border-slate-800 font-bold text-slate-500">⏩ ข้าม: ${rep.skipped}</div>
                            </div>
                        </div>
                    `);
                    refresh();
                } else Swal.fire('แจ้งเตือน', r.message || 'ไฟล์ว่าง', 'warning');
            },
            complete: () => {
                btn.prop('disabled', false).html('<i class="fas fa-upload mr-2"></i> อัปโหลดและลงทะเบียน');
                $('#csv_file_input').val('');
            }
        });
    });

    initFilters();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
