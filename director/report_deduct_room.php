<?php
$term = $user->getTerm();
$pee = $user->getPee();
?>

<!-- Report: Deduct by Room -->
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="glass-effect rounded-2xl p-6 border border-white/50">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[160px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">🏫 เลือกชั้น</label>
                <select id="select-class" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-sky-500/20 outline-none transition-all">
                    <option value="">-- เลือกชั้น --</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">🚪 เลือกห้อง</label>
                <select id="select-room" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-sky-500/20 outline-none transition-all" disabled>
                    <option value="">-- เลือกห้อง --</option>
                </select>
            </div>
            <button id="print-btn" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-black text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all items-center gap-2 hidden">
                <i class="fas fa-print mr-2"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <!-- Print Header (Hidden) -->
    <div id="print-header" class="hidden text-center mb-4">
        <h3 class="text-xl font-black text-slate-800">รายงานสถิติการหักคะแนนนักเรียนจำแนกตามห้องเรียน</h3>
        <p id="print-class-title" class="text-slate-600 font-bold"></p>
        <p id="print-term-title" class="text-slate-400 text-sm"></p>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto" id="print-area-wrapper">
        <table class="w-full text-left border-separate border-spacing-y-2" id="deduct-table">
            <thead>
                <tr class="bg-sky-50/50 dark:bg-slate-800/50">
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center">เลขที่</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">รหัส</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">คะแนนหัก</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">กลุ่ม</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">สรุป</th>
                </tr>
            </thead>
            <tbody id="deduct-table-body" class="font-bold text-slate-700 dark:text-slate-300">
                <tr><td colspan="7" class="text-center py-10 text-slate-400 italic"><i class="fas fa-hand-pointer text-3xl mb-3 opacity-30 block"></i>กรุณาเลือกชั้นและห้อง</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const selectClass = document.getElementById('select-class');
const selectRoom = document.getElementById('select-room');
const tableBody = document.getElementById('deduct-table-body');
const term = <?= json_encode($term) ?>;
const pee = <?= json_encode($pee) ?>;
const printBtn = document.getElementById('print-btn');
const printClassTitle = document.getElementById('print-class-title');
const printTermTitle = document.getElementById('print-term-title');

fetch('api/get_classes.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            data.classes.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = `ม.${cls.Stu_major}`;
                selectClass.appendChild(opt);
            });
        }
    });

selectClass.addEventListener('change', function() {
    selectRoom.innerHTML = '<option value="">-- เลือกห้อง --</option>';
    selectRoom.disabled = true;
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic">กรุณาเลือกชั้นและห้อง</td></tr>';
    printBtn.classList.add('hidden');
    if (this.value) {
        fetch('api/get_rooms.php?class=' + encodeURIComponent(this.value))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.rooms.forEach(room => {
                        const opt = document.createElement('option');
                        opt.value = room.Stu_room;
                        opt.textContent = `ห้อง ${room.Stu_room}`;
                        selectRoom.appendChild(opt);
                    });
                    selectRoom.disabled = false;
                }
            });
    }
});

selectRoom.addEventListener('change', function() {
    if (selectClass.value && this.value) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</td></tr>';
        fetch(`api/get_deduct_room.php?class=${encodeURIComponent(selectClass.value)}&room=${encodeURIComponent(this.value)}&term=${term}&pee=${pee}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students.length > 0) {
                    tableBody.innerHTML = '';
                    data.students.forEach(stu => {
                        const score = 100 - parseInt(stu.behavior_count, 10);
                        let groupText = '', groupClass = '', summaryText = '';
                        
                        if (score < 50) {
                            groupText = 'ต่ำกว่า 50';
                            groupClass = 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
                            summaryText = 'เข้าค่ายปรับพฤติกรรม';
                        } else if (score >= 50 && score <= 70) {
                            groupText = '50-70';
                            groupClass = 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
                            summaryText = 'บำเพ็ญประโยชน์ 20 ชม.';
                        } else if (score >= 71 && score <= 99) {
                            groupText = '71-99';
                            groupClass = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                            summaryText = 'บำเพ็ญประโยชน์ 10 ชม.';
                        }

                        tableBody.innerHTML += `
                            <tr class="bg-white dark:bg-slate-800/50 hover:bg-sky-50 dark:hover:bg-slate-700/50 transition-all">
                                <td class="px-4 py-3 text-center rounded-l-xl">${stu.Stu_no}</td>
                                <td class="px-4 py-3 text-center text-slate-400 text-xs">#${stu.Stu_id}</td>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</td>
                                <td class="px-4 py-3 text-center"><span class="px-2 py-1 bg-sky-50 dark:bg-sky-900/30 text-sky-600 rounded-lg text-xs font-bold">ม.${stu.Stu_major}/${stu.Stu_room}</span></td>
                                <td class="px-4 py-3 text-center"><span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-full text-sm font-black">-${stu.behavior_count}</span></td>
                                <td class="px-4 py-3 text-center"><span class="px-2 py-1 ${groupClass} rounded-lg text-xs font-bold">${groupText}</span></td>
                                <td class="px-4 py-3 text-center rounded-r-xl text-xs text-slate-500">${summaryText}</td>
                            </tr>`;
                    });
                    printBtn.classList.remove('hidden');
                    printClassTitle.textContent = `ชั้นมัธยมศึกษาปีที่ ${selectClass.value}/${selectRoom.value}`;
                    printTermTitle.textContent = `ภาคเรียนที่ ${term} ปีการศึกษา ${pee}`;
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic"><i class="fas fa-search text-3xl mb-3 opacity-30 block"></i>ไม่พบข้อมูล</td></tr>';
                    printBtn.classList.add('hidden');
                }
            });
    }
});

printBtn.addEventListener('click', function() {
    const printContents = document.getElementById('print-header').outerHTML + document.getElementById('deduct-table').outerHTML;
    const printWindow = window.open('', '', 'height=800,width=1200');
    printWindow.document.write(`<html><head><title>รายงานหักคะแนน</title>
        <style>body{font-family:'TH SarabunPSK',sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px;text-align:center;}th{background:#e0e7ff;}.text-rose-600{color:#dc2626;}.text-amber-600{color:#d97706;}.text-emerald-600{color:#16a34a;}</style>
    </head><body>${printContents}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
});
</script>
