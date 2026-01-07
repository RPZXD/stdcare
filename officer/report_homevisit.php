<?php
/**
 * Sub-View: Home Visit Report (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10 no-print">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-house-chimney-user"></i>
                </span>
                ประวัติการ <span class="text-emerald-600 italic">เยี่ยมบ้านนักเรียน</span> (แบ่งตามระดับชั้น)
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Student Home Visit Progress Statistics</p>
        </div>
        
        <div class="flex gap-3 no-print">
            <button onclick="location.reload()" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center gap-2 border border-slate-200 dark:border-slate-700">
                <i class="fas fa-sync-alt"></i> รีเฟรช
            </button>
            <button onclick="window.printReport ? window.printReport() : window.print()" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center gap-2 border border-slate-700">
                <i class="fas fa-print text-emerald-400"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <!-- Main Report Table -->
    <div id="homevisit-report" class="overflow-x-auto overflow-y-visible">
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-500 italic mt-4">รวบรวมข้อมูลสถิติการเยี่ยมบ้าน...</p>
        </div>
    </div>
</div>

<!-- Premium Modals System -->
<style>
    .modal-backdrop-premium {
        background-color: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
    }
    .modal-content-premium {
        animation: modalFadeIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

<!-- Room Detail Modal -->
<div id="modal-room-detail" class="fixed inset-0 z-50 hidden modal-backdrop-premium flex items-center justify-center p-4">
    <div class="glass-effect rounded-[2.5rem] w-full max-w-6xl shadow-2xl border border-white/50 dark:border-slate-700/50 flex flex-col max-h-[90vh] modal-content-premium">
        <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white leading-none">รายละเอียดรายห้อง</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">ระดับชั้น <span id="modal-class-label" class="text-indigo-500 font-black"></span></p>
                </div>
            </div>
            <button onclick="closeModal('modal-room-detail')" class="w-10 h-10 rounded-full hover:bg-rose-50 hover:text-rose-500 text-slate-400 flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modal-room-content" class="p-8 overflow-y-auto"></div>
    </div>
</div>

<!-- Student List Modal -->
<div id="modal-student-list" class="fixed inset-0 z-50 hidden modal-backdrop-premium flex items-center justify-center p-4">
    <div class="glass-effect rounded-[2.5rem] w-full max-w-4xl shadow-2xl border border-white/50 dark:border-slate-700/50 flex flex-col max-h-[85vh] modal-content-premium">
        <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white leading-none">รายชื่อการเยี่ยมบ้าน</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">ห้อง <span id="modal-room-label" class="text-blue-500 font-black"></span></p>
                </div>
            </div>
            <button onclick="closeModal('modal-student-list')" class="w-10 h-10 rounded-full hover:bg-rose-50 hover:text-rose-500 text-slate-400 flex items-center justify-center transition-all">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div id="modal-student-content" class="p-8 overflow-y-auto"></div>
    </div>
</div>

<!-- Summary Answer Modal -->
<div id="modal-summary" class="fixed inset-0 z-50 hidden modal-backdrop-premium flex items-center justify-center p-4">
    <div class="glass-effect rounded-[2.5rem] w-full max-w-5xl shadow-2xl border border-white/50 dark:border-slate-700/50 flex flex-col max-h-[90vh] modal-content-premium">
        <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white leading-none">สรุปคำตอบการเยี่ยมบ้าน</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">กลุ่ม <span id="modal-summary-label" class="text-amber-500 font-black"></span></p>
                </div>
            </div>
            <button onclick="closeModal('modal-summary')" class="w-10 h-10 rounded-full hover:bg-rose-50 hover:text-rose-500 text-slate-400 flex items-center justify-center transition-all">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div id="modal-summary-content" class="p-8 overflow-y-auto"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    function refreshLabels() {
        if (typeof updateMobileLabels === 'function') updateMobileLabels();
    }

    // --- 1. Load Main Report ---
    fetch('api/api_report_homevisit.php')
        .then(res => res.json())
        .then(res => {
            if (!res.success) throw new Error('Data fetch failed');
            const data = res.data;
            const levels = [
                { label: 'ม.1', major: 1 }, { label: 'ม.2', major: 2 }, { label: 'ม.3', major: 3 },
                { label: 'ม.4', major: 4 }, { label: 'ม.5', major: 5 }, { label: 'ม.6', major: 6 }
            ];

            let html = `
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">ระดับชั้น</th>
                            <th class="px-6 py-4 text-[10px] font-black text-emerald-600 uppercase tracking-widest italic text-center">เยี่ยมบ้าน เทอม 1</th>
                            <th class="px-6 py-4 text-[10px] font-black text-emerald-600 uppercase tracking-widest italic text-center">เยี่ยมบ้าน เทอม 2</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">นักเรียนทั้งหมด</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-right">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="font-bold text-slate-700 dark:text-slate-300">
            `;

            levels.forEach(level => {
                const row = data.find(r => r.class === level.label) || {
                    class: level.label, major: level.major,
                    visited_term1: 0, percent_term1: 0, visited_term2: 0, percent_term2: 0, total: 0
                };
                
                html += `
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                        <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="ระดับชั้น">
                             <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-[13px] font-black italic">${row.class}</span>
                                <span class="text-sm font-black text-slate-800 dark:text-white uppercase">ระดับชั้น ${row.class}</span>
                             </div>
                        </td>
                        <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="เยี่ยมบ้าน เทอม 1">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-slate-800 dark:text-white">${row.visited_term1} คน</span>
                                <span class="text-[10px] font-black text-emerald-500 uppercase italic">${row.percent_term1}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="เยี่ยมบ้าน เทอม 2">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-slate-800 dark:text-white">${row.visited_term2} คน</span>
                                <span class="text-[10px] font-black text-amber-500 uppercase italic">${row.percent_term2}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="นักเรียนทั้งหมด">
                            <span class="text-sm font-bold text-slate-400 font-mono italic">${row.total}</span>
                        </td>
                        <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-right">
                             <div class="flex justify-end gap-2">
                                <button onclick="showRoomModal('${row.major}', '${row.class}')" class="px-3 py-1.5 bg-indigo-500/10 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-indigo-500 hover:text-white transition-all">ห้องเรียน</button>
                                <button onclick="showSummaryModal('${row.major}', 'all', '${row.class}')" class="px-3 py-1.5 bg-amber-500/10 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-amber-500 hover:text-white transition-all">สรุปสถิติ</button>
                             </div>
                        </td>
                    </tr>
                `;
            });
            html += `</tbody></table>`;
            $('#homevisit-report').html(html);
            refreshLabels();
        });

    // --- 2. Room Modal Logic ---
    window.showRoomModal = function(major, label) {
        $('#modal-class-label').text(label);
        $('#modal-room-detail').show().removeClass('hidden');
        $('#modal-room-content').html(`<div class="flex flex-col items-center justify-center py-20"><div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div></div>`);
        
        fetch('api/api_report_homevisit_room.php?major=' + encodeURIComponent(major))
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error('Fetch failed');
                const data = res.data;
                let html = `
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="bg-indigo-50/50 dark:bg-indigo-950/20">
                                <th class="px-6 py-4 text-[9px] font-black text-indigo-500 uppercase tracking-widest rounded-l-2xl">ห้อง</th>
                                <th class="px-4 py-4 text-[9px] font-black text-emerald-500 uppercase tracking-widest text-center">เทอม 1 (%)</th>
                                <th class="px-4 py-4 text-[9px] font-black text-amber-500 uppercase tracking-widest text-center">เทอม 2 (%)</th>
                                <th class="px-4 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">นักเรียน</th>
                                <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest rounded-r-2xl text-right">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="font-bold text-[13px] text-slate-700 dark:text-slate-300">
                `;
                data.forEach(row => {
                    html += `
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all">
                            <td class="px-6 py-4 rounded-l-xl bg-white dark:bg-slate-900 border-y border-l border-slate-100 dark:border-slate-800" data-label="ห้อง">ห้อง ${row.room}</td>
                            <td class="px-4 py-4 bg-white dark:bg-slate-900 border-y border-slate-100 dark:border-slate-800 text-center text-emerald-600" data-label="เทอม 1 (%)">${row.visited_term1} (${row.percent_term1}%)</td>
                            <td class="px-4 py-4 bg-white dark:bg-slate-900 border-y border-slate-100 dark:border-slate-800 text-center text-amber-600" data-label="เทอม 2 (%)">${row.visited_term2} (${row.percent_term2}%)</td>
                            <td class="px-4 py-4 bg-white dark:bg-slate-900 border-y border-slate-100 dark:border-slate-800 text-center" data-label="นักเรียน">${row.total}</td>
                            <td class="px-6 py-4 rounded-r-xl bg-white dark:bg-slate-900 border-y border-r border-slate-100 dark:border-slate-800 text-right">
                                <div class="flex justify-end gap-1">
                                    <button onclick="showStudentListModal('${major}', '${row.room}', '${label}/${row.room}')" class="p-2 bg-blue-500/10 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all"><i class="fas fa-list-ul"></i></button>
                                    <button onclick="showSummaryModal('${major}', '${row.room}', '${label}/${row.room}')" class="p-2 bg-amber-500/10 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all"><i class="fas fa-chart-simple"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                $('#modal-room-content').html(html);
                refreshLabels();
            });
    }

    // --- 3. Student List Logic ---
    window.showStudentListModal = function(major, room, label) {
        $('#modal-room-label').text(label);
        $('#modal-student-list').show().removeClass('hidden');
        $('#modal-student-content').html(`<div class="flex flex-col items-center justify-center py-20"><div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div></div>`);
        
        fetch('api/api_report_homevisit_students.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
            .then(res => res.json())
            .then(res => {
                const data = res.data;
                let html = `
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="bg-blue-50/50 dark:bg-blue-950/20">
                                <th class="px-6 py-3 text-[9px] font-black text-blue-500 rounded-l-xl uppercase tracking-widest text-center">เลขที่</th>
                                <th class="px-4 py-3 text-[9px] font-black text-blue-500 uppercase tracking-widest">ชื่อ-สกุล</th>
                                <th class="px-4 py-3 text-[9px] font-black text-blue-500 uppercase tracking-widest text-center">เทอม 1</th>
                                <th class="px-6 py-3 text-[9px] font-black text-blue-500 rounded-r-xl uppercase tracking-widest text-center">เทอม 2</th>
                            </tr>
                        </thead>
                        <tbody class="text-[12px] font-bold text-slate-700 dark:text-slate-300">
                `;
                data.forEach(r => {
                    const s1 = r.visit_status1 == 1 ? '<i class="fas fa-check-circle text-emerald-500 text-sm"></i>' : '<i class="fas fa-circle-xmark text-slate-200 dark:text-slate-800 text-sm"></i>';
                    const s2 = r.visit_status2 == 1 ? '<i class="fas fa-check-circle text-emerald-500 text-sm"></i>' : '<i class="fas fa-circle-xmark text-slate-200 dark:text-slate-800 text-sm"></i>';
                    html += `
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-3 rounded-l-xl bg-white dark:bg-slate-900 text-center" data-label="เลขที่">${r.Stu_no}</td>
                            <td class="px-4 py-3 bg-white dark:bg-slate-900" data-label="ชื่อ-สกุล">${r.FullName}</td>
                            <td class="px-4 py-3 bg-white dark:bg-slate-900 text-center" data-label="เทอม 1">${s1}</td>
                            <td class="px-6 py-3 rounded-r-xl bg-white dark:bg-slate-900 text-center" data-label="เทอม 2">${s2}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                $('#modal-student-content').html(html);
                refreshLabels();
            });
    }

    // --- 4. Summary Stats Modal ---
    window.showSummaryModal = function(major, room, label) {
        $('#modal-summary-label').text(label);
        $('#modal-summary').show().removeClass('hidden');
        $('#modal-summary-content').html(`<div class="flex flex-col items-center justify-center py-20"><div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div></div>`);
        
        fetch('api/api_report_homevisit_summary.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
            .then(res => res.json())
            .then(res => {
                if (!res.success || !res.data.length) {
                    $('#modal-summary-content').html(`<div class="text-center py-20 font-bold text-slate-400 italic">ไม่พบข้อมูลสถิติของรายการนี้</div>`);
                    return;
                }
                const data = res.data;
                let html = '<div class="space-y-12">';
                
                // Answers Table (Optional display, let's keep it compact)
                html += `<div class="bg-amber-50/30 dark:bg-amber-900/10 p-6 rounded-3xl border border-amber-100 dark:border-amber-800 mb-10 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-amber-500 text-white"><tr class="rounded-t-2xl"><th class="px-4 py-2 border-r rounded-tl-xl">หัวข้อประเมิน</th><th class="px-4 py-2 border-r">คำตอบ</th><th class="px-4 py-2 text-center rounded-tr-xl">ร้อยละ</th></tr></thead>
                        <tbody class="bg-white dark:bg-slate-900 border border-amber-100 font-bold">`;
                
                data.forEach(row => {
                    const answers = row.answers || [];
                    answers.forEach((ans, idx) => {
                        html += `<tr>`;
                        if (idx === 0) html += `<td class="p-4 border border-amber-50" rowspan="${answers.length}">${row.question}</td>`;
                        html += `<td class="p-4 border border-amber-100">${ans.answer}</td><td class="p-4 border border-amber-100 text-center text-amber-600">${ans.percent}%</td></tr>`;
                    });
                });
                html += `</tbody></table></div>`;

                // Chart Container
                html += `<div id="summary-charts" class="grid grid-cols-1 md:grid-cols-2 gap-8"></div></div>`;
                $('#modal-summary-content').html(html);

                // Render Charts
                data.forEach((row, idx) => {
                    const chartId = `summary-chart-${idx}`;
                    $('#summary-charts').append(`
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                            <h5 class="text-[13px] font-black text-slate-800 dark:text-white mb-6 text-center leading-relaxed h-[40px] flex items-center justify-center">${row.question}</h5>
                            <div class="h-[200px] relative"><canvas id="${chartId}"></canvas></div>
                        </div>
                    `);
                    
                    new Chart(document.getElementById(chartId), {
                        type: 'doughnut',
                        data: {
                            labels: row.answers.map(a => a.answer),
                            datasets: [{
                                data: row.answers.map(a => a.count),
                                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'],
                                borderJoinStyle: 'round', borderRadius: 5, borderDashOffset: 2
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { font: { size: 10, family: 'Mali' }, usePointStyle: true, boxWidth: 6, boxHeight: 6 } } }
                        }
                    });
                });
            });
    }

    window.closeModal = function(id) {
        $(`#${id}`).addClass('hidden').hide();
    }

    // Global Modal Close on Outside Click
    $('.modal-backdrop-premium').on('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
