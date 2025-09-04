<?php
// สมมติว่ามี $term และ $pee จาก session หรือ context เดียวกับ report.php
?>
<div>
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        📊 รายงานการหักคะแนน (แบ่งตามกลุ่ม)
    </h2>
    <div class="flex items-center gap-4 mb-6">
        <!-- Tabs -->
        <div class="flex gap-1" id="tab-group">
            <button data-type="all" class="tab-btn bg-blue-100 text-blue-700 px-3 py-1 rounded border border-blue-300 font-medium">รวมทั้งหมด</button>
            <button data-type="level" class="tab-btn bg-gray-100 text-gray-700 px-3 py-1 rounded border border-gray-300 font-medium">แยกช่วงชั้น</button>
            <button data-type="class" class="tab-btn bg-gray-100 text-gray-700 px-3 py-1 rounded border border-gray-300 font-medium">แยกตามระดับชั้น</button>
            <button data-type="room" class="tab-btn bg-gray-100 text-gray-700 px-3 py-1 rounded border border-gray-300 font-medium">แยกตามห้อง</button>
        </div>
        <label class="font-medium" for="group-select">เลือกกลุ่มคะแนน:</label>
        <select id="group-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">-- เลือกกลุ่ม --</option>
            <option value="1">คะแนนพฤติกรรมต่ำกว่า 50 คะแนน</option>
            <option value="2">คะแนนพฤติกรรมอยู่ระหว่าง 50 - 70 คะแนน</option>
            <option value="3">คะแนนพฤติกรรมอยู่ระหว่าง 71 - 99 คะแนน</option>
        </select>
        <!-- เพิ่ม select สำหรับช่วงชั้น -->
        <select id="level-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 hidden">
            <option value="">-- เลือกช่วงชั้น --</option>
            <option value="lower">ม.ต้น</option>
            <option value="upper">ม.ปลาย</option>
        </select>
        <!-- เพิ่ม select สำหรับระดับชั้น -->
        <select id="class-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 hidden">
            <option value="">-- เลือกระดับชั้น --</option>
            <option value="1">ม.1</option>
            <option value="2">ม.2</option>
            <option value="3">ม.3</option>
            <option value="4">ม.4</option>
            <option value="5">ม.5</option>
            <option value="6">ม.6</option>
        </select>
        <!-- เพิ่ม select สำหรับแยกตามห้อง -->
        <select id="major-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 hidden">
            <option value="">-- เลือกชั้น --</option>
            <option value="1">ม.1</option>
            <option value="2">ม.2</option>
            <option value="3">ม.3</option>
            <option value="4">ม.4</option>
            <option value="5">ม.5</option>
            <option value="6">ม.6</option>
        </select>
        <select id="room-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 hidden">
            <option value="">-- เลือกห้อง --</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
        </select>
        <button id="print-btn" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded shadow flex items-center gap-2">
            🖨️ พิมพ์รายงาน
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow" id="group-table">
            <thead>
                <tr class="bg-pink-100 text-pink-900">
                    <th class="py-3 px-4 text-center">ลำดับ</th>
                    <th class="py-3 px-4 text-center">เลขประจำตัว</th>
                    <th class="py-3 px-4 text-left">👤 ชื่อ - สกุล</th>
                    <th class="py-3 px-4 text-center">ชั้น</th>
                    <th class="py-3 px-4 text-center">เลขที่</th>
                    <th class="py-3 px-4 text-center">✂️ คะแนนที่ถูกหัก</th>
                    <th class="py-3 px-4 text-center">Score Bar</th>
                </tr>
            </thead>
            <tbody id="group-table-body">
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกกลุ่มคะแนน</td>
                </tr>
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

const majorSelect = document.getElementById('major-select');
const roomSelect = document.getElementById('room-select');
const term = typeof window.term !== 'undefined' ? window.term : <?= isset($term) ? json_encode($term) : '1' ?>;
const pee = typeof window.pee !== 'undefined' ? window.pee : <?= isset($pee) ? json_encode($pee) : '2567' ?>;

let currentTab = 'all';

function getGroupText(val) {
    switch (val) {
        case "1": return "คะแนนพฤติกรรมต่ำกว่า 50 คะแนน";
        case "2": return "คะแนนพฤติกรรมอยู่ระหว่าง 50 - 70 คะแนน";
        case "3": return "คะแนนพฤติกรรมอยู่ระหว่าง 71 - 99 คะแนน";
        default: return "";
    }
}
function updateSelectVisibility() {
    if (currentTab === 'level') {
        levelSelect.classList.remove('hidden');
        classSelect.classList.add('hidden');
        majorSelect.classList.add('hidden');
        roomSelect.classList.add('hidden');
    } else if (currentTab === 'class') {
        classSelect.classList.remove('hidden');
        levelSelect.classList.add('hidden');
        majorSelect.classList.add('hidden');
        roomSelect.classList.add('hidden');
    } else if (currentTab === 'room') {
        majorSelect.classList.remove('hidden');
        roomSelect.classList.remove('hidden');
        levelSelect.classList.add('hidden');
        classSelect.classList.add('hidden');
    } else {
        levelSelect.classList.add('hidden');
        classSelect.classList.add('hidden');
        majorSelect.classList.add('hidden');
        roomSelect.classList.add('hidden');
    }
}

// เปลี่ยน tab
tabGroup.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        tabGroup.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-blue-100','text-blue-700','border-blue-300');
            b.classList.remove('bg-gray-100','text-gray-700','border-gray-300');
            b.classList.add('bg-gray-100','text-gray-700','border-gray-300');
        });
        this.classList.remove('bg-gray-100','text-gray-700','border-gray-300');
        this.classList.add('bg-blue-100','text-blue-700','border-blue-300');
        currentTab = this.getAttribute('data-type');
        updateSelectVisibility();
        groupSelect.value = "";
        fetchAndRender();
    });
});

groupSelect.addEventListener('change', fetchAndRender);
levelSelect.addEventListener('change', fetchAndRender);
classSelect.addEventListener('change', fetchAndRender);
majorSelect.addEventListener('change', fetchAndRender);
roomSelect.addEventListener('change', fetchAndRender);

function fetchAndRender() {
    const groupVal = groupSelect.value;
    let levelVal = levelSelect.value;
    let classVal = classSelect.value;
    let majorVal = majorSelect.value;
    let roomVal = roomSelect.value;
    if (!groupVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกกลุ่มคะแนน</td></tr>';
        return;
    }
    if (currentTab === 'level' && !levelVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกช่วงชั้น</td></tr>';
        return;
    }
    if (currentTab === 'class' && !classVal) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกระดับชั้น</td></tr>';
        return;
    }
    if (currentTab === 'room' && (!majorVal || !roomVal)) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกชั้นและห้อง</td></tr>';
        return;
    }
    groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-400">กำลังโหลดข้อมูล...</td></tr>';
    let url = `api/get_deduct_group_tab.php?group=${groupVal}&type=${currentTab}&term=${term}&pee=${pee}`;
    if (currentTab === 'level') url += `&level=${levelVal}`;
    if (currentTab === 'class') url += `&class=${classVal}`;
    if (currentTab === 'room') url += `&major=${majorVal}&room=${roomVal}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                groupTableBody.innerHTML = data.html;
            } else {
                groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
            }
        });
}

// พิมพ์เฉพาะข้อมูลที่แสดง
printBtn.addEventListener('click', function() {
    const groupVal = groupSelect.value;
    const groupText = getGroupText(groupVal);
    let typeText = "";
    if (currentTab === "all") typeText = "รวมทั้งหมด";
    else if (currentTab === "level") {
        typeText = "แยกช่วงชั้น (" + (levelSelect.value === "lower" ? "ม.ต้น" : levelSelect.value === "upper" ? "ม.ปลาย" : "-") + ")";
    }
    else if (currentTab === "class") typeText = "แยกตามระดับชั้น (" + (classSelect.value ? "ม." + classSelect.value : "-") + ")";
    let printContent = `
        <div style="text-align:center; font-family:Tahoma;">
            <h2 style="font-size:1.5em; margin-bottom:0.5em;">รายงานสถิติการหักคะแนนนักเรียนจำแนกตามกลุ่ม</h2>
            <div style="margin-bottom:0.5em;">ประเภทของกลุ่ม: <strong>${groupText || '-'}</strong></div>
            <div style="margin-bottom:0.5em;">${typeText}</div>
            <div style="margin-bottom:1em;">ภาคเรียนที่ <strong>${term}</strong> ปีการศึกษา <strong>${pee}</strong></div>
        </div>
    `;
    const tableHtml = document.getElementById('group-table').outerHTML;
    const printWindow = window.open('', '', 'width=900,height=700');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body { font-family: Tahoma, Arial, sans-serif; margin: 30px; }
                table { border-collapse: collapse; width: 100%; margin: 0 auto; }
                th, td { border: 1px solid #888; padding: 8px; text-align: center; }
                th { background: #f9c; color: #900; }
                h2 { margin-bottom: 0.5em; }
            </style>
        </head>
        <body>
            ${printContent}
            ${tableHtml}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
});

// โหลดข้อมูลเริ่มต้น
updateSelectVisibility();
const groupVal = groupSelect.value;
if (groupVal) {
    groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-400">กำลังโหลดข้อมูล...</td></tr>';
    fetchAndRender();
}
</script>
