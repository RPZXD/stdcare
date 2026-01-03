<?php
/**
 * View: Director Student Management
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive DataTables
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-user-graduate"></i>
                </span>
                ทะเบียนข้อมูล <span class="text-indigo-600 italic">นักเรียนทั้งหมด</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Student Directory & Academic Tracking</p>
        </div>
    </div>

    <!-- Executive Toolbar: Search & Filters -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 p-4 glass-effect rounded-[2rem] border border-white/50 shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Class Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-layer-group text-xs text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                </div>
                <select id="filterClass" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ชั้นเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>

            <!-- Room Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-door-open text-xs text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                </div>
                <select id="filterRoom" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ห้องเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-info-circle text-xs text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                </div>
                <select id="filterStatus" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer min-w-[160px]">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="1">ปกติ</option>
                    <option value="2">จบการศึกษา</option>
                    <option value="3">ย้ายสถานศึกษา</option>
                    <option value="4">ออกกลางคัน</option>
                    <option value="9">เสียชีวิต</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- Academic Info -->
        <div class="px-6 py-2 bg-indigo-600 rounded-full flex items-center gap-3 shadow-lg shadow-indigo-600/20">
            <span class="text-[9px] font-black text-indigo-100 uppercase tracking-widest">Academic Year</span>
            <span class="text-xs font-black text-white"><?php echo $pee; ?></span>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Students -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Count</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTotal" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 rounded-full uppercase tracking-tighter">Students</span>
            </div>
        </div>

        <!-- Card 2: Male Students -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Male Students</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statMale" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statMalePercent" class="text-[9px] font-black text-sky-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 3: Female Students -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Female Students</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statFemale" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statFemalePercent" class="text-[9px] font-black text-rose-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 4: Normal Status -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">กำลังศึกษา</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statActive" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statActivePercent" class="text-[9px] font-black text-emerald-500 uppercase italic">0%</div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="glass-effect rounded-[3rem] p-4 md:p-8 shadow-2xl border-t border-white/50 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 overflow-x-auto">
            <table id="studentTable" class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-slate-900/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">เลขที่</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">รหัสประจำตัว</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชื่อ-นามสกุล</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center text-nowrap">ชั้น/ห้อง</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
    let studentTable;

    studentTable = $('#studentTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        deferRender: true, // Performance optimization
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
        },
        drawCallback: function() {
            // Apply custom styling to pagination
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-indigo-600 !text-white');
        },
        columns: [
            { 
                data: 'Stu_no',
                className: 'text-center',
                render: function(data) {
                    return `<span class="text-indigo-600">${data}</span>`;
                }
            },
            { 
                data: 'Stu_id',
                className: 'text-center',
                render: function(data) {
                    return `<span class="text-slate-400 text-sm italic font-black">#${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(row.Stu_name)}&background=random`;
                    return `<div class="flex items-center gap-3">
                        <img src="${avatar}" class="w-8 h-8 rounded-full border border-slate-100">
                        <span class="text-slate-800 dark:text-slate-200">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</span>
                     </div>`;
                }
            },
            { 
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full text-xs">ม.${row.Stu_major}/${row.Stu_room}</span>`;
                }
            },
            { 
                data: 'Stu_status',
                className: 'text-center',
                render: function(data) {
                    const statusMap = {
                        1: { label: 'ปกติ', class: 'bg-emerald-100/50 text-emerald-600 border-emerald-500/20' },
                        2: { label: 'จบการศึกษา', class: 'bg-indigo-100/50 text-indigo-600 border-indigo-500/20' },
                        3: { label: 'ย้ายสถานศึกษา', class: 'bg-amber-100/50 text-amber-600 border-amber-500/20' },
                        4: { label: 'ออกกลางคัน', class: 'bg-rose-100/50 text-rose-600 border-rose-500/20' },
                        9: { label: 'เสียชีวิต', class: 'bg-slate-200 text-slate-600 border-slate-300' }
                    };
                    const s = statusMap[data] || { label: 'ไม่ทราบสถานะ', class: 'bg-slate-100 text-slate-400 border-slate-200' };
                    return `<span class="px-3 py-1 ${s.class} rounded-full text-[9px] uppercase font-black px-4 tracking-tighter border whitespace-nowrap">${s.label}</span>`;
                }
            }
        ]
    });

    loadStudents();
    populateFilterSelects();


    $('#filterClass, #filterRoom, #filterStatus').on('change', () => loadStudents());

    async function loadStudents() {
        const c = $('#filterClass').val();
        const r = $('#filterRoom').val();
        const s = $('#filterStatus').val();
        let url = `api/api_student.php?action=list&token=${encodeURIComponent(API_TOKEN_KEY)}`;
        if (c) url += `&class=${encodeURIComponent(c)}`;
        if (r) url += `&room=${encodeURIComponent(r)}`;
        if (s) url += `&status=${encodeURIComponent(s)}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            
            // Optimization: Clear and add all at once
            studentTable.clear().rows.add(data).draw();

            // Calculate Executive Stats
            updateExecutiveStats(data);
        } catch (err) {
            console.error(err);
        }
    }

    function updateExecutiveStats(data) {
        const total = data.length;
        let males = 0;
        let females = 0;
        let active = 0;

        data.forEach(s => {
            // Count gender based on prefix
            if (['เด็กชาย', 'นาย'].includes(s.Stu_pre)) males++;
            if (['เด็กหญิง', 'นางสาว'].includes(s.Stu_pre)) females++;
            
            // Count active status
            if (s.Stu_status == 1) active++;
        });

        // Animate numbers (simple)
        $('#statTotal').text(total.toLocaleString());
        $('#statMale').text(males.toLocaleString());
        $('#statFemale').text(females.toLocaleString());
        $('#statActive').text(active.toLocaleString());

        // Update percentages
        if (total > 0) {
            $('#statMalePercent').text(Math.round((males/total)*100) + '%');
            $('#statFemalePercent').text(Math.round((females/total)*100) + '%');
            $('#statActivePercent').text(Math.round((active/total)*100) + '%');
        } else {
            $('#statMalePercent').text('0%');
            $('#statFemalePercent').text('0%');
            $('#statActivePercent').text('0%');
        }
    }

    function populateFilterSelects() {
        fetch(`api/api_student.php?action=filters&token=${encodeURIComponent(API_TOKEN_KEY)}`)
            .then(res => res.json())
            .then(data => {
                const cSelect = $('#filterClass');
                const rSelect = $('#filterRoom');
                data.classes.forEach(c => c && cSelect.append(`<option value="${c}">${c}</option>`));
                data.rooms.forEach(r => r && rSelect.append(`<option value="${r}">${r}</option>`));
            });
    }

});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/director_app.php';
?>
