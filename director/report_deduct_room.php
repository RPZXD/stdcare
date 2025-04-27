<?php
// ดึงค่า term และ pee จาก session หรือค่าที่กำหนดไว้
// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

?>
<div>
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        🏫 รายงานการหักคะแนน (รายห้อง)
    </h2>
    <div class="flex flex-wrap gap-4 mb-6">
        <div>
            <label class="block mb-1 font-medium">ชั้น</label>
            <select id="select-class" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- เลือกชั้น --</option>
            </select>
        </div>
        <div>
            <label class="block mb-1 font-medium">ห้อง</label>
            <select id="select-room" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                <option value="">-- เลือกห้อง --</option>
            </select>
        </div>
    </div>
    <button
        id="print-btn"
        class="mb-4 px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition"
        style="display:none"
    >
        🖨️ พิมพ์ข้อมูล
    </button>
    <div class="overflow-x-auto" id="print-area-wrapper">
        <div id="print-header" style="display:none;">
            <div class="flex flex-col items-center justify-center">
                <div class="mb-2 font-bold text-lg text-center">รายงานสถิติการหักคะแนนนักเรียนจำแนกตามห้องเรียน</div>
                <div class="mb-1 text-center" id="print-class-title"></div>
                <div class="mb-4 text-center" id="print-term-title"></div>
            </div>
        </div>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow" id="deduct-table">
            <thead>
                <tr class="bg-blue-100 text-blue-900">
                    <th class="py-3 px-4 text-center">เลขที่</th>
                    <th class="py-3 px-4 text-center">เลขประจำตัว</th>
                    <th class="py-3 px-4 text-left">👤 ชื่อ - สกุล</th>
                    <th class="py-3 px-4 text-center">ชั้น</th>
                    <th class="py-3 px-4 text-center">✂️ คะแนนที่ถูกหัก</th>
                    <th class="py-3 px-4 text-center">กลุ่ม</th>
                    <th class="py-3 px-4 text-center rounded-tr-lg">📋 สรุป</th>
                </tr>
            </thead>
            <tbody id="deduct-table-body">
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">กรุณาเลือกชั้นและห้อง</td>
                </tr>
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
const printHeader = document.getElementById('print-header');
const printClassTitle = document.getElementById('print-class-title');
const printTermTitle = document.getElementById('print-term-title');
const printAreaWrapper = document.getElementById('print-area-wrapper');

// โหลดชั้นเรียน
fetch('api/get_classes.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            data.classes.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = cls.Stu_major;
                selectClass.appendChild(opt);
            });
        }
    });

// เมื่อเลือกชั้น ให้โหลดห้อง
selectClass.addEventListener('change', function() {
    selectRoom.innerHTML = '<option value="">-- เลือกห้อง --</option>';
    selectRoom.disabled = true;
    tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-center text-gray-500">กรุณาเลือกชั้นและห้อง</td></tr>';
    if (this.value) {
        fetch('api/get_rooms.php?class=' + encodeURIComponent(this.value))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.rooms.forEach(room => {
                        const opt = document.createElement('option');
                        opt.value = room.Stu_room;
                        opt.textContent = room.Stu_room;
                        selectRoom.appendChild(opt);
                    });
                    selectRoom.disabled = false;
                }
            });
    }
});

// เมื่อเลือกห้อง ให้โหลดข้อมูลตาราง
selectRoom.addEventListener('change', function() {
    if (selectClass.value && this.value) {
        tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-center text-gray-400">กำลังโหลดข้อมูล...</td></tr>';
        fetch(`api/get_deduct_room.php?class=${encodeURIComponent(selectClass.value)}&room=${encodeURIComponent(this.value)}&term=${term}&pee=${pee}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students.length > 0) {
                    tableBody.innerHTML = '';
                    data.students.forEach((stu, idx) => {
                        // กำหนดกลุ่มตามคะแนน
                        let groupText = '';
                        let groupClass = '';
                        let groupEmoji = '';
                        const score = 100 - parseInt(stu.behavior_count, 10);

                        let summaryText = '';
                        if (score < 50) {
                            groupText = 'ต่ำกว่า 50 คะแนน';
                            groupClass = 'text-red-600 font-bold';
                            groupEmoji = '🚨';
                            summaryText = 'เข้าค่ายปรับพฤติกรรม (โดยกลุ่มบริหารงานกิจการนักเรียน)';
                        } else if (score >= 50 && score <= 70) {
                            groupText = 'อยู่ระหว่าง 50 - 70 คะแนน';
                            groupClass = 'text-yellow-500 font-semibold';
                            groupEmoji = '⚠️';
                            summaryText = 'บำเพ็ญประโยชน์ 20 ชั่วโมง (โดยหัวหน้าระดับ)';
                        } else if (score >= 71 && score <= 99) {
                            groupText = 'อยู่ระหว่าง 71 - 99 คะแนน';
                            groupClass = 'text-green-600 font-semibold';
                            groupEmoji = '✅';
                            summaryText = 'บำเพ็ญประโยชน์ 10 ชั่วโมง (โดยครูที่ปรึกษา)';
                        } else {
                            groupText = '';
                            groupClass = '';
                            groupEmoji = '';
                            summaryText = '';
                        }

                        tableBody.innerHTML += `
                            <tr class="border-b hover:bg-blue-50 transition">
                                <td class="py-2 px-4 text-center">${stu.Stu_no}</td>
                                <td class="py-2 px-4 text-center">${stu.Stu_id}</td>
                                <td class="py-2 px-4">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</td>
                                <td class="py-2 px-4 text-center">ม.${stu.Stu_major}/${stu.Stu_room}</td>
                                <td class="py-2 px-4 text-center text-red-600 font-semibold">${stu.behavior_count} ✂️</td>
                                <td class="py-2 px-4 text-center ${groupClass}">${groupText} ${groupEmoji}</td>
                                <td class="py-2 px-4 text-center">${summaryText}</td>
                            </tr>
                        `;
                    });
                    // แสดงปุ่มพิมพ์
                    printBtn.style.display = '';
                    // ตั้งค่าหัวข้อรายงาน
                    printClassTitle.textContent = `รายงานสถิติการหักคะแนนของนักเรียน ชั้นมัธยมศึกษาปีที่ ${selectClass.value}/${selectRoom.value}`;
                    printTermTitle.textContent = `เทอม ${term} ปีการศึกษา ${pee}`;
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
                    printBtn.style.display = 'none';
                }
            });
    } else {
        tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-center text-gray-500">กรุณาเลือกชั้นและห้อง</td></tr>';
    }
});

// ปุ่มพิมพ์
printBtn.addEventListener('click', function() {
    // แสดงหัวกระดาษสำหรับพิมพ์
    printHeader.style.display = '';
    // ซ่อนปุ่มพิมพ์ขณะพิมพ์
    printBtn.style.display = 'none';
    // สร้าง window สำหรับพิมพ์
    const printContents = printHeader.outerHTML + document.getElementById('deduct-table').outerHTML;
    const printWindow = window.open('', '', 'height=800,width=1200');
    printWindow.document.write(`
        <html>
        <head>
            <title>รายงานสถิติการหักคะแนนนักเรียนจำแนกตามห้องเรียน</title>
            <style>
                body { font-family: Tahoma, Arial, sans-serif; margin: 20px; }
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
                .font-semibold { font-weight: 600; }
                .text-lg { font-size: 1.125rem; }
                .mb-1 { margin-bottom: 0.25rem; }
                .mb-2 { margin-bottom: 0.5rem; }
                .mb-4 { margin-bottom: 1rem; }
                .flex { display: flex; }
                .flex-col { flex-direction: column; }
                .items-center { align-items: center; }
                .justify-center { justify-content: center; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
                th { background: #e0e7ff; }
                .text-red-600 { color: #dc2626; }
                .text-yellow-500 { color: #eab308; }
                .text-green-600 { color: #16a34a; }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
    // ซ่อนหัวกระดาษหลังพิมพ์
    printHeader.style.display = 'none';
    printBtn.style.display = '';
});
</script>
