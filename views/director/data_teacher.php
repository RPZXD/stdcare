<?php
/**
 * View: Director Teacher Management
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive DataTables/Cards
 */
ob_start();
?>

<style>
    /* Mobile Card Responsive Styling */
    @media (max-width: 768px) {
        #teacherTable, #teacherTable thead, #teacherTable tbody, #teacherTable th, #teacherTable td, #teacherTable tr { 
            display: block; 
        }
        #teacherTable thead { display: none; }
        #teacherTable tr {
            margin-bottom: 1.5rem;
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 1.5rem;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        #teacherTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid rgba(241, 245, 249, 1);
            text-align: right;
        }
        #teacherTable td:last-child { border-bottom: none; }
        #teacherTable td::before {
            content: attr(data-label);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            text-align: left;
        }
        .dark #teacherTable tr { background: #0f172a; border-color: #1e293b; }
        .dark #teacherTable td { border-color: #1e293b; }
    }
</style>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-sky-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-chalkboard-teacher"></i>
                </span>
                ทะเบียนข้อมูล <span class="text-sky-600 italic">บุคลากรทั้งหมด</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Personnel Directory & Role Management</p>
        </div>
    </div>

    <!-- Executive Toolbar: Search & Filters -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 p-4 glass-effect rounded-[2rem] border border-white/50 shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Department Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-building text-xs text-sky-400 group-focus-within:text-sky-600 transition-colors"></i>
                </div>
                <select id="filterMajor" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-sky-500/10 transition-all appearance-none cursor-pointer min-w-[180px]">
                    <option value="">กลุ่มสาระทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-user-tag text-xs text-sky-400 group-focus-within:text-sky-600 transition-colors"></i>
                </div>
                <select id="filterRole" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-sky-500/10 transition-all appearance-none cursor-pointer min-w-[160px]">
                    <option value="">ตำแหน่งทั้งหมด</option>
                    <option value="DIR">ผู้อำนวยการ</option>
                    <option value="VP">รองผู้อำนวยการ</option>
                    <option value="T">ครู</option>
                    <option value="OF">เจ้าหน้าที่</option>
                    <option value="ADM">ผู้ดูแลระบบ</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- Academic Info -->
        <div class="px-6 py-2 bg-sky-600 rounded-full flex items-center gap-3 shadow-lg shadow-sky-600/20">
            <span class="text-[9px] font-black text-sky-100 uppercase tracking-widest">Academic Year</span>
            <span class="text-xs font-black text-white"><?php echo $pee; ?></span>
        </div>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Personnel -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Personnel</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTotal" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-sky-500 bg-sky-50 dark:bg-sky-900/40 px-2 py-0.5 rounded-full uppercase tracking-tighter">Staff</span>
            </div>
        </div>

        <!-- Card 2: Teachers -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ครูผู้สอน</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTeachers" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statTeachersPercent" class="text-[9px] font-black text-indigo-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 3: Directors -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ผู้บริหาร</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statDirectors" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statDirectorsPercent" class="text-[9px] font-black text-amber-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 4: Officers -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-cog text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เจ้าหน้าที่</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statOfficers" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statOfficersPercent" class="text-[9px] font-black text-emerald-500 uppercase italic">0%</div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="glass-effect rounded-[3rem] p-4 md:p-8 shadow-2xl border-t border-white/50 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-sky-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 overflow-x-auto">
            <table id="teacherTable" class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="bg-sky-50/50 dark:bg-slate-900/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">รหัสครู</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชื่อ-นามสกุล</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">กลุ่มสาระ</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ชั้น/ห้อง</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">บทบาท</th>
                    </tr>
                </thead>
                <tbody id="teacherTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
    let teacherTable;
    let allTeachers = []; // Store all teachers for filtering

    teacherTable = $('#teacherTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        deferRender: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
        },
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-sky-600 !text-white');
        },
        columns: [
            { 
                data: 'Teach_id',
                className: 'text-center',
                render: function(data) {
                    return `<span class="text-slate-400 text-[11px] font-black tracking-widest italic bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200/50 dark:border-slate-700">#${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(row.Teach_name)}&background=random`;
                    return `<div class="flex items-center gap-3">
                        <img src="${avatar}" class="w-9 h-9 rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-slate-800 dark:text-slate-200 font-bold">${row.Teach_name}</span>
                     </div>`;
                }
            },
            { 
                data: 'Teach_major',
                render: function(data) {
                    return data ? `<span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-bold">${data}</span>` : '<span class="text-slate-300 italic text-xs">-</span>';
                }
            },
            { 
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    if (row.Teach_class && row.Teach_room) {
                        return `<span class="px-3 py-1 bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 rounded-full text-xs font-bold">ม.${row.Teach_class}/${row.Teach_room}</span>`;
                    }
                    return '<span class="text-slate-300 italic text-xs">-</span>';
                }
            },
            { 
                data: 'role_std',
                className: 'text-center',
                render: function(data) {
                    const roleMap = {
                        'DIR': { label: 'ผู้อำนวยการ', class: 'bg-amber-500 text-white' },
                        'VP': { label: 'รอง ผอ.', class: 'bg-orange-500 text-white' },
                        'T': { label: 'ครู', class: 'bg-indigo-500 text-white' },
                        'OF': { label: 'เจ้าหน้าที่', class: 'bg-emerald-500 text-white' },
                        'ADM': { label: 'Admin', class: 'bg-rose-500 text-white' }
                    };
                    const r = roleMap[data] || { label: data || '-', class: 'bg-slate-200 text-slate-600' };
                    return `<span class="px-3 py-1 ${r.class} rounded-full text-[9px] uppercase font-black tracking-widest shadow-sm">${r.label}</span>`;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300');
            $('td', row).each(function(index) {
                const labels = ['รหัสครู', 'ชื่อ-นามสกุล', 'กลุ่มสาระ', 'ชั้น/ห้อง', 'บทบาท'];
                $(this).attr('data-label', labels[index]);
            });
        }
    });

    loadTeachers();
    populateMajorFilter();

    $('#filterMajor, #filterRole').on('change', applyFilters);

    async function loadTeachers() {
        try {
            const res = await fetch(`api/api_teacher.php?action=list&token=${encodeURIComponent(API_TOKEN_KEY)}`);
            const data = await res.json();
            allTeachers = data;
            applyFilters();
        } catch (err) {
            console.error(err);
        }
    }

    function applyFilters() {
        const major = $('#filterMajor').val();
        const role = $('#filterRole').val();
        
        let filtered = allTeachers;
        
        if (major) {
            filtered = filtered.filter(t => t.Teach_major === major);
        }
        if (role) {
            filtered = filtered.filter(t => t.role_std === role);
        }
        
        teacherTable.clear().rows.add(filtered).draw();
        updateExecutiveStats(filtered);
    }

    function updateExecutiveStats(data) {
        const total = data.length;
        let teachers = 0, directors = 0, officers = 0;

        data.forEach(t => {
            if (t.role_std === 'T') teachers++;
            if (t.role_std === 'DIR' || t.role_std === 'VP') directors++;
            if (t.role_std === 'OF') officers++;
        });

        $('#statTotal').text(total.toLocaleString());
        $('#statTeachers').text(teachers.toLocaleString());
        $('#statDirectors').text(directors.toLocaleString());
        $('#statOfficers').text(officers.toLocaleString());

        if (total > 0) {
            $('#statTeachersPercent').text(Math.round((teachers/total)*100) + '%');
            $('#statDirectorsPercent').text(Math.round((directors/total)*100) + '%');
            $('#statOfficersPercent').text(Math.round((officers/total)*100) + '%');
        } else {
            $('#statTeachersPercent').text('0%');
            $('#statDirectorsPercent').text('0%');
            $('#statOfficersPercent').text('0%');
        }
    }

    function populateMajorFilter() {
        fetch(`api/fet_major.php`)
            .then(res => res.json())
            .then(data => {
                const select = $('#filterMajor');
                data.forEach(item => {
                    if (item.Teach_major) {
                        select.append(`<option value="${item.Teach_major}">${item.Teach_major}</option>`);
                    }
                });
            });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/director_app.php';
?>
