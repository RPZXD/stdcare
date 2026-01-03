<?php
/**
 * View: Director Behavior Data (Read-Only Executive View)
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive DataTables
 */
ob_start();
?>

<style>
    /* Mobile Card Responsive Styling */
    @media (max-width: 768px) {
        #behaviorTable, #behaviorTable thead, #behaviorTable tbody, #behaviorTable th, #behaviorTable td, #behaviorTable tr { 
            display: block; 
        }
        #behaviorTable thead { display: none; }
        #behaviorTable tr {
            margin-bottom: 1.5rem;
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 1.5rem;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        #behaviorTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid rgba(241, 245, 249, 1);
            text-align: right;
        }
        #behaviorTable td:last-child { border-bottom: none; }
        #behaviorTable td::before {
            content: attr(data-label);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            text-align: left;
        }
        .dark #behaviorTable tr { background: #0f172a; border-color: #1e293b; }
        .dark #behaviorTable td { border-color: #1e293b; }
    }
</style>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-frown"></i>
                </span>
                รายงาน <span class="text-rose-600 italic uppercase">พฤติกรรมนักเรียน</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Behavioral Records & Discipline Monitoring</p>
        </div>
    </div>

    <!-- Executive Toolbar -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 p-4 glass-effect rounded-[2rem] border border-white/50 shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Class Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-layer-group text-xs text-rose-400 group-focus-within:text-rose-600 transition-colors"></i>
                </div>
                <select id="filterClass" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-rose-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ชั้นเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>

            <!-- Room Filter -->
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-door-open text-xs text-rose-400 group-focus-within:text-rose-600 transition-colors"></i>
                </div>
                <select id="filterRoom" class="pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-black text-slate-600 dark:text-slate-300 outline-none focus:ring-4 focus:ring-rose-500/10 transition-all appearance-none cursor-pointer min-w-[140px]">
                    <option value="">ห้องเรียนทั้งหมด</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- Academic Info -->
        <div class="px-6 py-2 bg-rose-600 rounded-full flex items-center gap-3 shadow-lg shadow-rose-600/20">
            <span class="text-[9px] font-black text-rose-100 uppercase tracking-widest">ปีการศึกษา</span>
            <span class="text-xs font-black text-white"><?php echo $pee; ?></span>
        </div>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Records -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Records</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTotal" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-900/40 px-2 py-0.5 rounded-full uppercase tracking-tighter">รายการ</span>
            </div>
        </div>

        <!-- Card 2: Total Score Deducted -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-minus-circle text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">คะแนนที่หักรวม</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statTotalScore" class="text-3xl font-black text-amber-600 dark:text-amber-400 tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-amber-500 uppercase italic">Points</span>
            </div>
        </div>

        <!-- Card 3: Students Involved -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">นักเรียนที่เกี่ยวข้อง</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statStudents" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-indigo-500 uppercase italic">คน</span>
            </div>
        </div>

        <!-- Card 4: Average Score -->
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เฉลี่ย/รายการ</span>
            </div>
            <div class="flex items-end justify-between">
                <h3 id="statAverage" class="text-3xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">0</h3>
                <span class="text-[10px] font-bold text-emerald-500 uppercase italic">Points</span>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="glass-effect rounded-[3rem] p-4 md:p-8 shadow-2xl border-t border-white/50 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-rose-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 overflow-x-auto">
            <table id="behaviorTable" class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="bg-rose-50/50 dark:bg-slate-900/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">วันที่</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลนักเรียน</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">พฤติกรรม / รายละเอียด</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนน</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">ครูผู้บันทึก</th>
                    </tr>
                </thead>
                <tbody id="behaviorTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
    let behaviorTable;

    behaviorTable = $('#behaviorTable').DataTable({
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        deferRender: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
        order: [[0, 'desc']],
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-rose-600 !text-white');
        },
        columns: [
            { 
                data: 'behavior_date',
                className: 'text-center',
                render: function(data) {
                    return `<span class="text-xs font-black text-slate-400">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(row.Stu_name)}&background=random`;
                    return `<div class="flex items-center gap-3">
                        <img src="${avatar}" class="w-9 h-9 rounded-xl border border-slate-100 shadow-sm">
                        <div class="text-left">
                            <div class="text-sm font-black text-slate-800 dark:text-white">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</div>
                            <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">ม.${row.Stu_major}/${row.Stu_room} • #${row.Stu_id}</div>
                        </div>
                     </div>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<div class="text-left">
                        <div class="text-xs font-black text-rose-600 mb-1">${row.behavior_type}</div>
                        <div class="text-[11px] font-bold text-slate-500 line-clamp-2 italic">${row.behavior_name}</div>
                    </div>`;
                }
            },
            { 
                data: 'behavior_score',
                className: 'text-center',
                render: function(data) {
                    return `<span class="px-3 py-1.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl text-sm font-black">-${data}</span>`;
                }
            },
            { 
                data: 'teacher_behavior',
                className: 'text-center',
                render: function(data) {
                    return `<span class="text-[11px] font-bold text-slate-400 italic">${data || '-'}</span>`;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all duration-300');
            $('td', row).each(function(index) {
                const labels = ['วันที่', 'นักเรียน', 'พฤติกรรม', 'คะแนน', 'ครูผู้บันทึก'];
                $(this).attr('data-label', labels[index]);
            });
        }
    });

    loadBehaviors();
    populateFilterSelects();

    $('#filterClass, #filterRoom').on('change', () => loadBehaviors());

    async function loadBehaviors() {
        const c = $('#filterClass').val();
        const r = $('#filterRoom').val();
        let url = `api/api_behavior.php?action=list&token=${encodeURIComponent(API_TOKEN_KEY)}`;
        if (c) url += `&class=${encodeURIComponent(c)}`;
        if (r) url += `&room=${encodeURIComponent(r)}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            behaviorTable.clear().rows.add(data).draw();
            updateExecutiveStats(data);
        } catch (err) {
            console.error(err);
        }
    }

    function updateExecutiveStats(data) {
        const total = data.length;
        let totalScore = 0;
        const uniqueStudents = new Set();

        data.forEach(b => {
            totalScore += parseInt(b.behavior_score) || 0;
            uniqueStudents.add(b.Stu_id);
        });

        const avg = total > 0 ? (totalScore / total).toFixed(1) : 0;

        $('#statTotal').text(total.toLocaleString());
        $('#statTotalScore').text(totalScore.toLocaleString());
        $('#statStudents').text(uniqueStudents.size.toLocaleString());
        $('#statAverage').text(avg);
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
