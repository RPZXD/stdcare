<?php
require_once("../config/Database.php");
require_once("../class/Wroom.php");
require_once("../class/Teacher.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// ดึงรายชื่อห้องทั้งหมด
$rooms = [];
$stmt = $db->query("SELECT Stu_major, Stu_room FROM student WHERE Stu_status=1 GROUP BY Stu_major, Stu_room ORDER BY Stu_major, Stu_room");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rooms[] = $row;
}
?>
<div class="max-w-full mx-auto bg-white rounded-xl shadow p-6 mt-6">
    <form id="roomForm" method="get" class="mb-6 flex flex-wrap gap-3 items-center justify-center">
        <label class="font-semibold text-gray-700">เลือกห้อง:</label>
        <select name="class" id="classSelect" class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <option value="">-- ชั้น --</option>
            <?php foreach(array_unique(array_column($rooms, 'Stu_major')) as $c): ?>
                <option value="<?= $c ?>">ม.<?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <select name="room" id="roomSelect" class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <option value="">-- ห้อง --</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow font-semibold transition">แสดง</button>
    </form>
    <div id="resultArea" class="mt-4"></div>
    <div class="flex justify-center mt-4">
        <button id="printBtn" type="button" class="hidden bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow font-semibold transition">
            🖨️ พิมพ์รายชื่อ
        </button>
        <button id="wordBtn" type="button" class="hidden bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded shadow font-semibold transition ml-2">
            ⬇️ ส่งออกเป็น Word
        </button>
    </div>
</div>
<script>
const allRooms = <?php echo json_encode($rooms); ?>;

// ดึงค่าจาก query string (ถ้ามี)
function getQueryParam(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name) || '';
}

// กรอกห้องอัตโนมัติเมื่อเลือกชั้น
function updateRoomSelect(selectedClass, selectedRoom = '') {
    const roomSelect = document.getElementById('roomSelect');
    roomSelect.innerHTML = '<option value="">-- ห้อง --</option>';
    allRooms.forEach(r => {
        if (!selectedClass || r.Stu_major == selectedClass) { // เปลี่ยน === เป็น ==
            const sel = (selectedRoom && r.Stu_room == selectedRoom) ? 'selected' : ''; // เปลี่ยน === เป็น ==
            roomSelect.innerHTML += `<option value="${r.Stu_room}" ${sel}>${r.Stu_room}</option>`;
        }
    });
}

// โหลดค่าจาก query string (ถ้ามี)
document.addEventListener('DOMContentLoaded', function() {
    const classVal = getQueryParam('class');
    const roomVal = getQueryParam('room');
    if (classVal) {
        document.getElementById('classSelect').value = classVal;
        updateRoomSelect(classVal, roomVal);
    }
    // ถ้ามีค่า room ใน query string ให้ set ค่า
    if (classVal && roomVal) {
        document.getElementById('roomSelect').value = roomVal;
        fetchCommittee(classVal, roomVal);
    }
});

// อัปเดตห้องเมื่อเลือกชั้น
document.getElementById('classSelect').addEventListener('change', function() {
    updateRoomSelect(this.value);
    document.getElementById('roomSelect').value = '';
});

// ดัก submit ฟอร์ม
document.getElementById('roomForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const classVal = document.getElementById('classSelect').value;
    const roomVal = document.getElementById('roomSelect').value;
    if (classVal && roomVal) {
        // อัปเดต query string
        const url = new URL(window.location.href);
        url.searchParams.set('class', classVal);
        url.searchParams.set('room', roomVal);
        window.history.replaceState({}, '', url);
        fetchCommittee(classVal, roomVal);
    } else {
        document.getElementById('resultArea').innerHTML = '<div class="text-gray-500 text-center">กรุณาเลือกห้องเพื่อดูรายชื่อคณะกรรมการ</div>';
    }
});

// เรียก API
function fetchCommittee(classVal, roomVal) {
    document.getElementById('resultArea').innerHTML = '<div class="text-gray-400 text-center animate-pulse">กำลังโหลดข้อมูล...</div>';
    document.getElementById('printBtn').classList.add('hidden');
    document.getElementById('wordBtn').classList.add('hidden');
    fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
        .then(res => res.json())
        .then(data => {
            if (!data || !data.positions) {
                document.getElementById('resultArea').innerHTML = '<div class="text-gray-500 text-center">ไม่พบข้อมูล</div>';
                document.getElementById('printBtn').classList.add('hidden');
                document.getElementById('wordBtn').classList.add('hidden');
                return;
            }
            let html = `<div class='font-bold text-lg mb-2 text-blue-700 text-center'>ห้อง ม.${classVal}/${roomVal}</div>`;
            html += `<div class='mb-4 text-center'><span class="font-semibold">ครูที่ปรึกษา:</span> ${data.advisors && data.advisors.length ? data.advisors.map(a => a.Teach_name).join(', ') : '-'}</div>`;
            html += `<div class="divide-y divide-gray-200">`;
            Object.entries(data.positions).forEach(([key, label]) => {
                html += `<div class='py-2 flex flex-wrap items-center'><span class='font-semibold w-56'>${label}:</span> `;
                if (data.grouped[key] && data.grouped[key].length) {
                    html += `<span class="text-gray-800">${data.grouped[key].map(s => s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur).join(', ')}</span>`;
                } else {
                    html += "<span class='text-gray-400'>- ไม่มี -</span>";
                }
                html += "</div>";
            });
            html += `</div>`;
            html += `<div class='mt-6 p-4 bg-gray-50 border border-gray-200 rounded-xl text-center'><span class='font-semibold'>✍️ คติพจน์:</span> ${data.maxim ? `<span class="text-blue-700">${data.maxim}</span>` : "<span class='text-gray-400'>- ยังไม่ได้กรอก -</span>"}</div>`;
            document.getElementById('resultArea').innerHTML = html;
            document.getElementById('printBtn').classList.remove('hidden');
            document.getElementById('wordBtn').classList.remove('hidden');
        })
        .catch(() => {
            document.getElementById('resultArea').innerHTML = '<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
            document.getElementById('printBtn').classList.add('hidden');
            document.getElementById('wordBtn').classList.add('hidden');
        });
}

// ฟังก์ชันรวมลำดับตำแหน่งตามที่ต้องการ
function buildOrderedList(data) {
    // ฟังก์ชันแปลงเลขอารบิกเป็นเลขไทย
    function toThaiNum(num) {
        return String(num).replace(/\d/g, d => '๐๑๒๓๔๕๖๗๘๙'[d]);
    }
    let list = [];
    // ครูที่ปรึกษา
    if (data.advisors && data.advisors.length) {
        data.advisors.forEach(a => {
            list.push({
                name: `${a.Teach_pre || ''}${a.Teach_name} ${a.Teach_sur || ''}`.replace(/\s+/g, ' ').trim(),
                pos: 'ครูที่ปรึกษา'
            });
        });
    }
    // หัวหน้าห้อง
    if (data.grouped && data.grouped.head && data.grouped.head.length) {
        data.grouped.head.forEach(s => {
            list.push({
                name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`.replace(/\s+/g, ' ').trim(),
                pos: data.positions.head
            });
        });
    }
    // เลขานุการ
    if (data.grouped && data.grouped.secretary && data.grouped.secretary.length) {
        data.grouped.secretary.forEach(s => {
            list.push({
                name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`.replace(/\s+/g, ' ').trim(),
                pos: data.positions.secretary
            });
        });
    }
    // ผู้ช่วยเลขานุการ
    if (data.grouped && data.grouped.assist_secretary && data.grouped.assist_secretary.length) {
        data.grouped.assist_secretary.forEach(s => {
            list.push({
                name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`.replace(/\s+/g, ' ').trim(),
                pos: data.positions.assist_secretary
            });
        });
    }
    // ตำแหน่งอื่นๆ
    if (data.positions && data.grouped) {
        Object.entries(data.positions).forEach(([key, label]) => {
            if (['head','secretary','assist_secretary'].includes(key)) return;
            if (data.grouped[key] && data.grouped[key].length) {
                data.grouped[key].forEach(s => {
                    list.push({
                        name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`.replace(/\s+/g, ' ').trim(),
                        pos: label
                    });
                });
            }
        });
    }
    return list;
}

// ฟังก์ชันพิมพ์เฉพาะผลลัพธ์
document.getElementById('printBtn').addEventListener('click', function() {
    const classVal = document.getElementById('classSelect').value;
    const roomVal = document.getElementById('roomSelect').value;
    fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
        .then(res => res.json())
        .then(data => {
            function toThaiNum(num) {
                return String(num).replace(/\d/g, d => '๐๑๒๓๔๕๖๗๘๙'[d]);
            }
            let list = buildOrderedList(data);
            // แยกชื่อ-นามสกุล
            let lines = [];
            lines.push(`ระดับชั้นมัธยมศึกษาปีที่ ${toThaiNum(classVal)}/${toThaiNum(roomVal)}`);
            list.forEach((item, idx) => {
                // แยกคำนำหน้า ชื่อ นามสกุล
                let parts = item.name.trim().split(' ');
                let pre = parts[0] || '';
                let fname = parts[1] || '';
                let lname = parts.slice(2).join(' ') || '';
                // ถ้าไม่มีคำนำหน้า
                if (parts.length < 3) {
                    fname = parts[0] || '';
                    lname = parts[1] || '';
                    pre = '';
                }
                // 1 tab ระหว่างชื่อ-นามสกุล, 2 tab ระหว่างนามสกุล-ตำแหน่ง
                lines.push(`${toThaiNum(idx+1)}.${pre}${fname}\t${lname}\t\t${item.pos}`);
            });
            let printHtml = `<pre style="font-size:1.1rem;line-height:2;font-family:'TH SarabunPSK',Tahoma,monospace;">${lines.join('\n')}</pre>`;
            const win = window.open('', '', 'width=900,height=650');
            win.document.write(`
                <html>
                <head>
                    <title>พิมพ์รายชื่อคณะกรรมการห้อง</title>
                    <style>
                        body { padding: 2rem; font-family: 'TH SarabunPSK', 'Tahoma', sans-serif; }
                        pre { font-family: 'TH SarabunPSK', 'Tahoma', monospace; }
                    </style>
                </head>
                <body onload="window.print();window.close()">
                    ${printHtml}
                </body>
                </html>
            `);
            win.document.close();
        });
});

// ฟังก์ชันส่งออกเป็น Word
document.getElementById('wordBtn').addEventListener('click', function() {
    const classVal = document.getElementById('classSelect').value;
    const roomVal = document.getElementById('roomSelect').value;
    fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
        .then(res => res.json())
        .then(data => {
            function toThaiNum(num) {
                return String(num).replace(/\d/g, d => '๐๑๒๓๔๕๖๗๘๙'[d]);
            }
            let list = buildOrderedList(data);
            let lines = [];
            lines.push(`ระดับชั้นมัธยมศึกษาปีที่ ${toThaiNum(classVal)}/${toThaiNum(roomVal)}`);
            list.forEach((item, idx) => {
                let parts = item.name.trim().split(' ');
                let pre = parts[0] || '';
                let fname = parts[1] || '';
                let lname = parts.slice(2).join(' ') || '';
                if (parts.length < 3) {
                    fname = parts[0] || '';
                    lname = parts[1] || '';
                    pre = '';
                }
                lines.push(`${toThaiNum(idx+1)}.${pre}${fname}\t${lname}\t\t${item.pos}`);
            });
            let wordHtml = `<pre style="font-size:1.1rem;line-height:2;font-family:'TH SarabunPSK',Tahoma,monospace;">${lines.join('\n')}</pre>`;
            let blob = new Blob([
                `<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
                <head><meta charset='utf-8'><title>รายชื่อคณะกรรมการห้อง</title></head>
                <body>${wordHtml}</body></html>`
            ], {type: 'application/msword'});
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = `รายชื่อคณะกรรมการห้อง_${toThaiNum(classVal)}_${toThaiNum(roomVal)}.doc`;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 100);
        });
});
</script>
