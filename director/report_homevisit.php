<!-- Report: Home Visit Statistics -->
<div class="space-y-6">
    <!-- Print Button -->
    <div class="flex justify-end">
        <button id="print-main-btn" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-print"></i> พิมพ์ตารางหลัก
        </button>
    </div>

    <!-- Main Table -->
    <div id="homevisit-report" class="overflow-x-auto">
        <div class="text-center py-10 text-slate-400">
            <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
            <p>กำลังโหลดข้อมูล...</p>
        </div>
    </div>
</div>

<!-- Modal: Room Detail -->
<div id="modal-room-detail" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden">
        <div class="p-6 bg-indigo-600 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white"><i class="fas fa-school"></i></div>
                <h4 class="font-black text-white text-lg">รายละเอียดรายห้อง <span id="modal-class-label" class="text-indigo-200"></span></h4>
            </div>
            <button id="close-modal-btn" class="text-white/60 hover:text-white text-2xl"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-room-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>
        </div>
    </div>
</div>

<!-- Modal: Student List -->
<div id="modal-student-list" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
        <div class="p-6 bg-sky-600 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white"><i class="fas fa-users"></i></div>
                <h4 class="font-black text-white text-lg">การเยี่ยมบ้านนักเรียนของชั้น <span id="modal-room-label" class="text-sky-200"></span></h4>
            </div>
            <button id="close-student-modal-btn" class="text-white/60 hover:text-white text-2xl"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-student-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>
        </div>
    </div>
</div>

<!-- Modal: Summary -->
<div id="modal-summary" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="p-6 bg-amber-500 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white"><i class="fas fa-chart-pie"></i></div>
                <h4 class="font-black text-white text-lg">สรุปคำตอบ <span id="modal-summary-label" class="text-amber-100"></span></h4>
            </div>
            <button id="close-summary-modal-btn" class="text-white/60 hover:text-white text-2xl"><i class="fas fa-times"></i></button>
        </div>
        <div id="modal-summary-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/api_report_homevisit.php')
        .then(res => res.json())
        .then(res => {
            if (!res.success) throw new Error('ไม่สามารถโหลดข้อมูลได้');
            const data = res.data;
            
            let html = `<table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-emerald-50/50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl" rowspan="2">ระดับชั้น</th>
                        <th class="px-4 py-3 text-[10px] font-black text-emerald-600 uppercase tracking-widest text-center" colspan="2">ภาคเรียนที่ 1 (100%)</th>
                        <th class="px-4 py-3 text-[10px] font-black text-sky-600 uppercase tracking-widest text-center" colspan="2">ภาคเรียนที่ 2 (25%)</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center" rowspan="2">รวม</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center rounded-r-xl" rowspan="2">Actions</th>
                    </tr>
                    <tr class="bg-emerald-50/30 dark:bg-slate-800/30">
                        <th class="px-3 py-2 text-[9px] font-bold text-emerald-500 text-center">จำนวน</th>
                        <th class="px-3 py-2 text-[9px] font-bold text-rose-500 text-center">%</th>
                        <th class="px-3 py-2 text-[9px] font-bold text-sky-500 text-center">จำนวน</th>
                        <th class="px-3 py-2 text-[9px] font-bold text-rose-500 text-center">%</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300">`;

            const levels = [
                { label: 'ม.1', major: 1 }, { label: 'ม.2', major: 2 }, { label: 'ม.3', major: 3 },
                { label: 'ม.4', major: 4 }, { label: 'ม.5', major: 5 }, { label: 'ม.6', major: 6 }
            ];

            levels.forEach(level => {
                const row = data.find(r => r.class === level.label) || {
                    class: level.label, major: level.major, visited_term1: 0, percent_term1: 0, visited_term2: 0, percent_term2: 0, total: 0
                };
                html += `<tr class="bg-white dark:bg-slate-800/50 hover:bg-emerald-50 dark:hover:bg-slate-700/50 transition-all">
                    <td class="px-4 py-3 rounded-l-xl font-black text-slate-800 dark:text-white">${row.class}</td>
                    <td class="px-4 py-3 text-center text-emerald-600 font-bold">${row.visited_term1 ?? 0}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-lg text-xs font-bold">${row.percent_term1 ?? 0}%</span></td>
                    <td class="px-4 py-3 text-center text-sky-600 font-bold">${row.visited_term2 ?? 0}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-lg text-xs font-bold">${row.percent_term2 ?? 0}%</span></td>
                    <td class="px-4 py-3 text-center">${row.total ?? 0}</td>
                    <td class="px-4 py-3 text-center rounded-r-xl">
                        <button class="view-room-btn px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:scale-105 transition-transform mr-1" data-major="${row.major}" data-label="${row.class}"><i class="fas fa-eye mr-1"></i>ดู</button>
                        <button class="view-summary-btn px-3 py-1.5 bg-amber-500 text-white rounded-lg text-xs font-bold hover:scale-105 transition-transform" data-major="${row.major}" data-room="all" data-label="${row.class}"><i class="fas fa-chart-pie mr-1"></i>สรุป</button>
                    </td>
                </tr>`;
            });
            html += `</tbody></table>`;
            document.getElementById('homevisit-report').innerHTML = html;

            document.querySelectorAll('.view-room-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showRoomModal(this.getAttribute('data-major'), this.getAttribute('data-label'));
                });
            });
            document.querySelectorAll('.view-summary-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showSummaryModal(this.getAttribute('data-major'), this.getAttribute('data-room'), this.getAttribute('data-label'));
                });
            });
        })
        .catch(err => {
            document.getElementById('homevisit-report').innerHTML = `<div class="text-center py-10 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p>${err.message}</p></div>`;
        });

    // Room Modal
    function showRoomModal(major, label) {
        document.getElementById('modal-class-label').textContent = label;
        document.getElementById('modal-room-detail').classList.remove('hidden');
        document.getElementById('modal-room-content').innerHTML = `<div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>`;
        
        fetch('api/api_report_homevisit_room.php?major=' + encodeURIComponent(major))
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error('ไม่สามารถโหลดข้อมูลรายห้องได้');
                const data = res.data;
                let html = `<table class="w-full text-left border-separate border-spacing-y-2">
                    <thead><tr class="bg-indigo-50/50 dark:bg-slate-800/50">
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase rounded-l-xl">ห้อง</th>
                        <th class="px-3 py-2 text-[9px] font-black text-emerald-600 uppercase text-center">T1</th>
                        <th class="px-3 py-2 text-[9px] font-black text-rose-600 uppercase text-center">%</th>
                        <th class="px-3 py-2 text-[9px] font-black text-sky-600 uppercase text-center">T2</th>
                        <th class="px-3 py-2 text-[9px] font-black text-rose-600 uppercase text-center">%</th>
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase text-center">รวม</th>
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase text-center rounded-r-xl">Actions</th>
                    </tr></thead><tbody class="font-bold text-slate-700">`;
                data.forEach(row => {
                    html += `<tr class="bg-white hover:bg-indigo-50 transition-all">
                        <td class="px-3 py-2 rounded-l-xl font-black">${row.room}</td>
                        <td class="px-3 py-2 text-center text-emerald-600">${row.visited_term1}</td>
                        <td class="px-3 py-2 text-center"><span class="text-rose-500 text-xs">${row.percent_term1}%</span></td>
                        <td class="px-3 py-2 text-center text-sky-600">${row.visited_term2}</td>
                        <td class="px-3 py-2 text-center"><span class="text-rose-500 text-xs">${row.percent_term2}%</span></td>
                        <td class="px-3 py-2 text-center">${row.total}</td>
                        <td class="px-3 py-2 text-center rounded-r-xl">
                            <button class="view-student-btn px-2 py-1 bg-sky-600 text-white rounded text-[10px] font-bold mr-1" data-major="${major}" data-room="${row.room}" data-label="${label}/${row.room}">รายชื่อ</button>
                            <button class="view-summary-btn px-2 py-1 bg-amber-500 text-white rounded text-[10px] font-bold" data-major="${major}" data-room="${row.room}" data-label="${label}/${row.room}">สรุป</button>
                        </td>
                    </tr>`;
                });
                html += `</tbody></table>`;
                document.getElementById('modal-room-content').innerHTML = html;

                document.querySelectorAll('.view-student-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        showStudentListModal(this.getAttribute('data-major'), this.getAttribute('data-room'), this.getAttribute('data-label'));
                    });
                });
                document.querySelectorAll('.view-summary-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        showSummaryModal(this.getAttribute('data-major'), this.getAttribute('data-room'), this.getAttribute('data-label'));
                    });
                });
            });
    }

    // Student List Modal
    function showStudentListModal(major, room, label) {
        document.getElementById('modal-room-label').textContent = label;
        document.getElementById('modal-student-list').classList.remove('hidden');
        document.getElementById('modal-student-content').innerHTML = `<div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>`;
        
        fetch('api/api_report_homevisit_students.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error('ไม่สามารถโหลดรายชื่อนักเรียนได้');
                let html = `<table class="w-full text-left border-separate border-spacing-y-1">
                    <thead><tr class="bg-sky-50/50">
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase rounded-l-xl">เลขที่</th>
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase">ชื่อ-สกุล</th>
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase text-center">T1</th>
                        <th class="px-3 py-2 text-[9px] font-black text-slate-400 uppercase text-center rounded-r-xl">T2</th>
                    </tr></thead><tbody class="font-bold text-slate-700">`;
                res.data.forEach(row => {
                    html += `<tr class="bg-white hover:bg-sky-50 transition-all">
                        <td class="px-3 py-2 rounded-l-xl text-center">${row.Stu_no}</td>
                        <td class="px-3 py-2">${row.FullName}</td>
                        <td class="px-3 py-2 text-center">${row.visit_status1 == 1 ? '<span class="text-emerald-500">✓</span>' : '<span class="text-rose-400">✗</span>'}</td>
                        <td class="px-3 py-2 text-center rounded-r-xl">${row.visit_status2 == 1 ? '<span class="text-emerald-500">✓</span>' : '<span class="text-rose-400">✗</span>'}</td>
                    </tr>`;
                });
                html += `</tbody></table>`;
                document.getElementById('modal-student-content').innerHTML = html;
            });
    }

    // Close modals
    document.getElementById('close-modal-btn').onclick = () => document.getElementById('modal-room-detail').classList.add('hidden');
    document.getElementById('close-student-modal-btn').onclick = () => document.getElementById('modal-student-list').classList.add('hidden');
    document.getElementById('close-summary-modal-btn').onclick = () => document.getElementById('modal-summary').classList.add('hidden');
    
    ['modal-room-detail', 'modal-student-list', 'modal-summary'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    // Print main table
    document.getElementById('print-main-btn').onclick = function() {
        const table = document.querySelector('#homevisit-report table');
        if (!table) return;
        const clone = table.cloneNode(true);
        clone.querySelectorAll('button').forEach(b => b.remove());
        const win = window.open('', '', 'width=900,height=700');
        win.document.write(`<html><head><title>พิมพ์รายงาน</title><style>body{font-family:Tahoma,sans-serif;margin:30px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px;text-align:center;}th{background:#d1fae5;}</style></head><body><h2 style="text-align:center;">สถิติการเยี่ยมบ้านนักเรียน</h2>${clone.outerHTML}</body></html>`);
        win.document.close();
        setTimeout(() => { win.print(); win.close(); }, 500);
    };
});

// Summary Modal
function showSummaryModal(major, room, label) {
    document.getElementById('modal-summary-label').textContent = label;
    document.getElementById('modal-summary').classList.remove('hidden');
    document.getElementById('modal-summary-content').innerHTML = `<div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</div>`;
    
    fetch('api/api_report_homevisit_summary.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
        .then(res => res.json())
        .then(res => {
            if (!res.success || !Array.isArray(res.data) || res.data.length === 0) {
                document.getElementById('modal-summary-content').innerHTML = `<div class="text-center py-8 text-slate-400">ไม่พบข้อมูลสรุป</div>`;
                return;
            }
            let html = `<div class="overflow-x-auto mb-6"><table class="w-full text-left border-separate border-spacing-y-1">
                <thead><tr class="bg-amber-50/50">
                    <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase rounded-l-xl">หัวข้อ</th>
                    <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase">คำตอบ</th>
                    <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase text-center">จำนวน</th>
                    <th class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase text-center rounded-r-xl">%</th>
                </tr></thead><tbody class="font-bold text-slate-700">`;
            res.data.forEach(row => {
                const answers = Array.isArray(row.answers) ? row.answers : [];
                answers.forEach((ans, idx) => {
                    html += `<tr class="bg-white"><td class="px-4 py-2 ${idx === 0 ? 'rounded-l-xl' : ''}"${idx === 0 ? ` rowspan="${answers.length}"` : ''}>${idx === 0 ? row.question : ''}</td><td class="px-4 py-2">${ans.answer}</td><td class="px-4 py-2 text-center">${ans.count}</td><td class="px-4 py-2 text-center ${idx === answers.length-1 ? 'rounded-r-xl' : ''}">${ans.percent}%</td></tr>`;
                });
            });
            html += `</tbody></table></div><div id="summary-charts" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>`;
            document.getElementById('modal-summary-content').innerHTML = html;

            res.data.forEach((row, idx) => {
                const answers = Array.isArray(row.answers) ? row.answers : [];
                if (answers.length === 0) return;
                const chartId = `summary-chart-${idx}`;
                const card = document.createElement('div');
                card.className = 'bg-white border rounded-2xl shadow-sm p-4';
                card.innerHTML = `<h5 class="text-center font-bold text-sm mb-4">${row.question}</h5><canvas id="${chartId}" style="max-height:180px"></canvas>`;
                document.getElementById('summary-charts').appendChild(card);

                new Chart(document.getElementById(chartId).getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: answers.map(i => i.answer),
                        datasets: [{ data: answers.map(i => i.count), backgroundColor: ['rgba(59,130,246,0.7)','rgba(34,197,94,0.7)','rgba(234,88,12,0.7)','rgba(239,68,68,0.7)','rgba(139,92,246,0.7)'], borderWidth: 1 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                });
            });
        });
}
</script>
