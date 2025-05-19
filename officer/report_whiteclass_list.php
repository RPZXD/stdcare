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
    fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
        .then(res => res.json())
        .then(data => {
            if (!data || !data.positions) {
                document.getElementById('resultArea').innerHTML = '<div class="text-gray-500 text-center">ไม่พบข้อมูล</div>';
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
        })
        .catch(() => {
            document.getElementById('resultArea').innerHTML = '<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}
</script>
