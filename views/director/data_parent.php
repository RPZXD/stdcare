<?php
/**
 * View: Director Parent Data Management
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive DataTables/Cards
 */
ob_start();
?>

<style>
    /* Mobile Card Responsive Styling */
    @media (max-width: 768px) {
        #parentTable, #parentTable thead, #parentTable tbody, #parentTable th, #parentTable td, #parentTable tr { 
            display: block; 
        }
        #parentTable thead { display: none; }
        #parentTable tr {
            margin-bottom: 1.5rem;
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 1.5rem;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        #parentTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid rgba(241, 245, 249, 1);
            text-align: right;
        }
        #parentTable td:last-child { border-bottom: none; }
        #parentTable td::before {
            content: attr(data-label);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            text-align: left;
        }
        .dark #parentTable tr { background: #0f172a; border-color: #1e293b; }
        .dark #parentTable td { border-color: #1e293b; }
    }
</style>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-users"></i>
                </span>
                ทะเบียนข้อมูล <span class="text-emerald-600 italic">ผู้ปกครองนักเรียน</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Parent & Guardian Directory</p>
        </div>
    </div>

    <!-- Executive Toolbar: Search & Filters -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 p-4 glass-effect rounded-[2rem] border border-white/50 shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Class Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-layer-group text-xs text-emerald-400 group-focus-within:text-emerald-600 transition-colors"></i>
                </div>
                <select id="filterClass" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ชั้นเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>

            <!-- Room Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-door-open text-xs text-emerald-400 group-focus-within:text-emerald-600 transition-colors"></i>
                </div>
                <select id="filterRoom" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ห้องเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- Academic Info -->
        <div class="px-6 py-2 bg-emerald-600 rounded-full flex items-center gap-3 shadow-lg shadow-emerald-600/20">
            <span class="text-[9px] font-black text-emerald-100 uppercase tracking-widest">Academic Year</span>
            <span class="text-xs font-black text-white"><?php echo $pee; ?></span>
        </div>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Records -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Records</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTotal" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 px-2 py-0.5 rounded-full uppercase tracking-tighter">Families</span>
            </div>
        </div>

        <!-- Card 2: Has Father Info -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลบิดา</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statFather" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statFatherPercent" class="text-[9px] font-black text-sky-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 3: Has Mother Info -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลมารดา</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statMother" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statMotherPercent" class="text-[9px] font-black text-rose-500 uppercase italic">0%</div>
            </div>
        </div>

        <!-- Card 4: Has Phone -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-phone text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">มีเบอร์โทร</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statPhone" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <div id="statPhonePercent" class="text-[9px] font-black text-amber-500 uppercase italic">0%</div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="glass-effect rounded-[3rem] p-4 md:p-8 shadow-2xl border-t border-white/50 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 overflow-x-auto">
            <table id="parentTable" class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="bg-emerald-50/50 dark:bg-slate-900/50">
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">นักเรียน</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ระดับชั้น</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลบิดา</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลมารดา</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ผู้ปกครอง</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">ติดต่อ</th>
                    </tr>
                </thead>
                <tbody id="parentTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
    let parentTable;

    parentTable = $('#parentTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        deferRender: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
        },
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-emerald-600 !text-white');
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row) {
                    const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(row.Stu_name)}&background=random`;
                    return `<div class="flex items-center gap-3">
                        <img src="${avatar}" class="w-9 h-9 rounded-xl border border-slate-100 shadow-sm">
                        <div>
                            <span class="text-slate-800 dark:text-slate-200 font-bold text-sm block">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</span>
                            <span class="text-[10px] text-slate-400 font-bold">#${row.Stu_id}</span>
                        </div>
                     </div>`;
                }
            },
            { 
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">ม.${row.Stu_major}/${row.Stu_room}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.Father_name) {
                        return `<div class="flex flex-col">
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-200">${row.Father_name}</span>
                            <span class="text-[10px] text-slate-400">${row.Father_occu || '-'}</span>
                        </div>`;
                    }
                    return '<span class="text-slate-300 italic text-xs">ไม่มีข้อมูล</span>';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.Mother_name) {
                        return `<div class="flex flex-col">
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-200">${row.Mother_name}</span>
                            <span class="text-[10px] text-slate-400">${row.Mother_occu || '-'}</span>
                        </div>`;
                    }
                    return '<span class="text-slate-300 italic text-xs">ไม่มีข้อมูล</span>';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.Par_name) {
                        return `<div class="flex flex-col">
                            <span class="font-bold text-sm text-slate-700 dark:text-slate-200">${row.Par_name}</span>
                            <span class="text-[10px] text-emerald-500 font-bold">${row.Par_relate || '-'}</span>
                        </div>`;
                    }
                    return '<span class="text-slate-300 italic text-xs">ไม่มีข้อมูล</span>';
                }
            },
            { 
                data: 'Par_phone',
                className: 'text-center',
                render: function(data) {
                    if (data) {
                        return `<a href="tel:${data}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500 text-white rounded-full text-[10px] font-black shadow-lg shadow-emerald-500/20 hover:scale-105 transition-transform">
                            <i class="fas fa-phone text-[8px]"></i>
                            ${data}
                        </a>`;
                    }
                    return '<span class="text-slate-300 italic text-xs">-</span>';
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300');
            $('td', row).each(function(index) {
                const labels = ['นักเรียน', 'ระดับชั้น', 'ข้อมูลบิดา', 'ข้อมูลมารดา', 'ผู้ปกครอง', 'ติดต่อ'];
                $(this).attr('data-label', labels[index]);
            });
        }
    });

    loadParents();
    populateFilterSelects();

    $('#filterClass, #filterRoom').on('change', () => loadParents());

    async function loadParents() {
        const c = $('#filterClass').val();
        const r = $('#filterRoom').val();
        let url = `api/api_parent.php?action=list&token=${encodeURIComponent(API_TOKEN_KEY)}`;
        if (c) url += `&class=${encodeURIComponent(c)}`;
        if (r) url += `&room=${encodeURIComponent(r)}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            parentTable.clear().rows.add(data).draw();
            updateExecutiveStats(data);
        } catch (err) {
            console.error(err);
        }
    }

    function updateExecutiveStats(data) {
        const total = data.length;
        let hasFather = 0, hasMother = 0, hasPhone = 0;

        data.forEach(p => {
            if (p.Father_name && p.Father_name.trim() !== '') hasFather++;
            if (p.Mother_name && p.Mother_name.trim() !== '') hasMother++;
            if (p.Par_phone && p.Par_phone.trim() !== '') hasPhone++;
        });

        $('#statTotal').text(total.toLocaleString());
        $('#statFather').text(hasFather.toLocaleString());
        $('#statMother').text(hasMother.toLocaleString());
        $('#statPhone').text(hasPhone.toLocaleString());

        if (total > 0) {
            $('#statFatherPercent').text(Math.round((hasFather/total)*100) + '%');
            $('#statMotherPercent').text(Math.round((hasMother/total)*100) + '%');
            $('#statPhonePercent').text(Math.round((hasPhone/total)*100) + '%');
        } else {
            $('#statFatherPercent').text('0%');
            $('#statMotherPercent').text('0%');
            $('#statPhonePercent').text('0%');
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
