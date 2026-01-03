<?php
// สมมติว่ามี $term และ $pee จาก session หรือ context
?>

<!-- Report: Deduct by Group -->
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="glass-effect rounded-2xl p-6 border border-white/50">
        <div class="flex flex-wrap items-end gap-4">
            <!-- Tab Buttons -->
            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl" id="tab-group">
                <button data-type="all" class="tab-btn px-4 py-2 rounded-lg font-bold text-sm transition-all bg-white dark:bg-slate-700 text-indigo-600 shadow-sm">รวมทั้งหมด</button>
                <button data-type="level" class="tab-btn px-4 py-2 rounded-lg font-bold text-sm transition-all text-slate-500 hover:bg-white/50">แยกช่วงชั้น</button>
                <button data-type="class" class="tab-btn px-4 py-2 rounded-lg font-bold text-sm transition-all text-slate-500 hover:bg-white/50">แยกตามระดับชั้น</button>
            </div>
            
            <!-- Group Select -->
            <div class="min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">📊 กลุ่มคะแนน</label>
                <select id="group-select" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-rose-500/20 outline-none transition-all">
                    <option value="">-- เลือกกลุ่ม --</option>
                    <option value="1">ต่ำกว่า 50 คะแนน</option>
                    <option value="2">อยู่ระหว่าง 50-70 คะแนน</option>
                    <option value="3">อยู่ระหว่าง 71-99 คะแนน</option>
                </select>
            </div>

            <!-- Level Select -->
            <div class="min-w-[160px] hidden" id="level-wrapper">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">📚 ช่วงชั้น</label>
                <select id="level-select" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-rose-500/20 outline-none transition-all">
                    <option value="">-- เลือกช่วงชั้น --</option>
                    <option value="lower">ม.ต้น</option>
                    <option value="upper">ม.ปลาย</option>
                </select>
            </div>

            <!-- Class Select -->
            <div class="min-w-[160px] hidden" id="class-wrapper">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">🏫 ระดับชั้น</label>
                <select id="class-select" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-rose-500/20 outline-none transition-all">
                    <option value="">-- เลือกระดับชั้น --</option>
                    <option value="1">ม.1</option>
                    <option value="2">ม.2</option>
                    <option value="3">ม.3</option>
                    <option value="4">ม.4</option>
                    <option value="5">ม.5</option>
                    <option value="6">ม.6</option>
                </select>
            </div>

            <button id="print-btn" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-black text-sm shadow-lg shadow-rose-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-2" id="group-table">
            <thead>
                <tr class="bg-rose-50/50 dark:bg-slate-800/50">
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center">ลำดับ</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">รหัส</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">เลขที่</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">คะแนนหัก</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">Score</th>
                </tr>
            </thead>
            <tbody id="group-table-body" class="font-bold text-slate-700 dark:text-slate-300">
                <tr><td colspan="7" class="text-center py-10 text-slate-400 italic"><i class="fas fa-filter text-3xl mb-3 opacity-30 block"></i>กรุณาเลือกกลุ่มคะแนน</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const groupSelect = document.getElementById('group-select');
const groupTableBody = document.getElementById('group-table-body');
const tabGroup = document.getElementById('tab-group');
const printBtn = document.getElementById('print-btn');
const levelSelect = document.getElementById('level-select');
const classSelect = document.getElementById('class-select');
const levelWrapper = document.getElementById('level-wrapper');
const classWrapper = document.getElementById('class-wrapper');
const term = typeof window.term !== 'undefined' ? window.term : <?= isset($term) ? json_encode($term) : '1' ?>;
const pee = typeof window.pee !== 'undefined' ? window.pee : <?= isset($pee) ? json_encode($pee) : '2567' ?>;

let currentTab = 'all';

function getGroupText(val) {
    switch (val) {
        case "1": return "ต่ำกว่า 50 คะแนน";
        case "2": return "อยู่ระหว่าง 50-70 คะแนน";
        case "3": return "อยู่ระหว่าง 71-99 คะแนน";
        default: return "";
    }
}

function updateSelectVisibility() {
    levelWrapper.classList.toggle('hidden', currentTab !== 'level');
    classWrapper.classList.toggle('hidden', currentTab !== 'class');
}

tabGroup.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        tabGroup.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-white', 'dark:bg-slate-700', 'text-indigo-600', 'shadow-sm');
            b.classList.add('text-slate-500');
        });
        this.classList.add('bg-white', 'dark:bg-slate-700', 'text-indigo-600', 'shadow-sm');
        this.classList.remove('text-slate-500');
        currentTab = this.getAttribute('data-type');
        updateSelectVisibility();
        fetchAndRender();
    });
});

groupSelect.addEventListener('change', fetchAndRender);
levelSelect.addEventListener('change', fetchAndRender);
classSelect.addEventListener('change', fetchAndRender);

function fetchAndRender() {
    const groupVal = groupSelect.value;
    let levelVal = levelSelect.value;
    let classVal = classSelect.value;
    
    if (!groupVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic">กรุณาเลือกกลุ่มคะแนน</td></tr>';
        return;
    }
    if (currentTab === 'level' && !levelVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic">กรุณาเลือกช่วงชั้น</td></tr>';
        return;
    }
    if (currentTab === 'class' && !classVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic">กรุณาเลือกระดับชั้น</td></tr>';
        return;
    }
    
    groupTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</td></tr>';
    
    let url = `api/get_deduct_group_tab.php?group=${groupVal}&type=${currentTab}&term=${term}&pee=${pee}`;
    if (currentTab === 'level') url += `&level=${levelVal}`;
    if (currentTab === 'class') url += `&class=${classVal}`;
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                groupTableBody.innerHTML = data.html;
            } else {
                groupTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-400 italic"><i class="fas fa-inbox text-3xl mb-3 opacity-30 block"></i>ไม่พบข้อมูล</td></tr>';
            }
        });
}

printBtn.addEventListener('click', function() {
    const groupVal = groupSelect.value;
    const groupText = getGroupText(groupVal);
    let typeText = currentTab === "all" ? "รวมทั้งหมด" : currentTab === "level" ? `แยกช่วงชั้น (${levelSelect.value === "lower" ? "ม.ต้น" : "ม.ปลาย"})` : `แยกตามระดับชั้น (ม.${classSelect.value})`;
    
    const printContent = `<div style="text-align:center;font-family:Tahoma;"><h2>รายงานสถิติการหักคะแนนนักเรียนจำแนกตามกลุ่ม</h2><p>ประเภทกลุ่ม: <strong>${groupText}</strong> | ${typeText}</p><p>ภาคเรียนที่ ${term} ปีการศึกษา ${pee}</p></div>`;
    const tableHtml = document.getElementById('group-table').outerHTML;
    const printWindow = window.open('', '', 'width=900,height=700');
    printWindow.document.write(`<html><head><title>Print</title><style>body{font-family:Tahoma,sans-serif;margin:30px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #888;padding:8px;text-align:center;}th{background:#fce7f3;color:#9f1239;}</style></head><body>${printContent}${tableHtml}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
});

updateSelectVisibility();
</script>
