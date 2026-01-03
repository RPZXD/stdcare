<?php
// ดึงค่า term และ pee
$term = $user->getTerm();
$pee = $user->getPee();
?>

<!-- Report: Late Students -->
<div class="space-y-6">
    <!-- Filter Form -->
    <div class="glass-effect rounded-2xl p-6 border border-white/50">
        <form id="lateForm" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">📅 วันที่เริ่มต้น</label>
                <input type="date" id="date_start" name="date_start" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">📅 วันที่สิ้นสุด</label>
                <input type="date" id="date_end" name="date_end" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
                <button type="button" onclick="printLateTable()" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-black text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-2" id="lateTable">
            <thead>
                <tr class="bg-amber-50/50 dark:bg-slate-800/50">
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center">#</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">รหัสนักเรียน</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อนักเรียน</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">เลขที่</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ห้อง</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">เบอร์ผู้ปกครอง</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">จำนวนครั้ง</th>
                </tr>
            </thead>
            <tbody id="lateTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                <!-- Data injected by JS -->
            </tbody>
        </table>
        <div id="lateTableEmpty" class="text-center py-10 text-slate-400 italic hidden">
            <i class="fas fa-calendar-alt text-4xl mb-4 opacity-30"></i>
            <p>กรุณาเลือกช่วงวันที่และกดค้นหา</p>
        </div>
    </div>
</div>

<script>
function thaiDateRange(start, end) {
    const months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
    function toThai(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return `${d.getDate()} ${months[d.getMonth() + 1]} พ.ศ. ${d.getFullYear() + 543}`;
    }
    return `ตั้งแต่ ${toThai(start)} ถึง ${toThai(end)}`;
}

function printLateTable() {
    const start = document.getElementById('date_start').value;
    const end = document.getElementById('date_end').value;
    const table = document.getElementById('lateTable');
    const rangeText = start && end ? `<div style="font-size:1.25rem;font-weight:bold;margin-bottom:1rem;text-align:center;">นักเรียนที่มาสายเกิน 3 ครั้ง<br>(${thaiDateRange(start, end)})</div>` : '';
    const style = `<style>body{font-family:'TH SarabunPSK','Sarabun',sans-serif;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:8px;text-align:center;}th{background:#e0e7ff;}tr:nth-child(even){background:#f1f5f9;}</style>`;
    const win = window.open('', '', 'width=900,height=700');
    win.document.write(`<html><head><title>พิมพ์รายงานมาสาย</title>${style}</head><body>${rangeText}${table.outerHTML}</body></html>`);
    win.document.close();
    setTimeout(() => { win.print(); win.close(); }, 500);
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('lateForm');
    const tbody = document.getElementById('lateTableBody');
    const emptyMsg = document.getElementById('lateTableEmpty');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        tbody.innerHTML = '';
        emptyMsg.classList.add('hidden');
        const start = document.getElementById('date_start').value;
        const end = document.getElementById('date_end').value;
        if (!start || !end) {
            emptyMsg.innerHTML = '<i class="fas fa-exclamation-circle text-4xl mb-4 opacity-30"></i><p>กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด</p>';
            emptyMsg.classList.remove('hidden');
            return;
        }
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</td></tr>';
        fetch(`api/fetch_checklate.php?start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach((row, idx) => {
                        tbody.innerHTML += `
                            <tr class="bg-white dark:bg-slate-800/50 hover:bg-amber-50 dark:hover:bg-slate-700/50 transition-all rounded-xl">
                                <td class="px-4 py-3 text-center rounded-l-xl">${idx + 1}</td>
                                <td class="px-4 py-3 text-center"><span class="text-slate-400 text-xs">#</span>${row.Stu_id || '-'}</td>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">${row.name || '-'}</td>
                                <td class="px-4 py-3 text-center">${row.Stu_no || '-'}</td>
                                <td class="px-4 py-3 text-center"><span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-bold">${row.classroom || '-'}</span></td>
                                <td class="px-4 py-3 text-center">${row.parent_tel ? `<a href="tel:${row.parent_tel}" class="text-emerald-600 hover:underline">${row.parent_tel}</a>` : '-'}</td>
                                <td class="px-4 py-3 text-center rounded-r-xl"><span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full text-sm font-black">${row.count_late || 0}</span></td>
                            </tr>`;
                    });
                } else {
                    emptyMsg.innerHTML = '<i class="fas fa-search text-4xl mb-4 opacity-30"></i><p>ไม่พบข้อมูลมาสายในช่วงวันที่ที่เลือก</p>';
                    emptyMsg.classList.remove('hidden');
                }
            })
            .catch(() => {
                emptyMsg.innerHTML = '<i class="fas fa-exclamation-triangle text-4xl mb-4 text-rose-400"></i><p>เกิดข้อผิดพลาดในการดึงข้อมูล</p>';
                emptyMsg.classList.remove('hidden');
            });
    });
});
</script>
