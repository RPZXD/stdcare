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


    <h2 class="text-2xl font-bold text-indigo-700 mb-6 text-center tracking-wide drop-shadow">👥 โครงสร้างห้องเรียนสีขาว</h2>
    <form id="roomForm" method="get" class="mb-8 flex flex-wrap gap-4 items-center justify-center">
        <label class="font-semibold text-gray-700">เลือกห้อง:</label>
        <select name="class" id="classSelect" class="border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
            <option value="">-- ชั้น --</option>
            <?php foreach(array_unique(array_column($rooms, 'Stu_major')) as $c): ?>
                <option value="<?= $c ?>">ม.<?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <select name="room" id="roomSelect" class="border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
            <option value="">-- ห้อง --</option>
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow font-semibold transition-all duration-150 transform hover:scale-105">แสดง</button>
    </form>
    <div id="resultArea" class="mt-6"></div>


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
        if (!selectedClass || r.Stu_major == selectedClass) {
            const sel = (selectedRoom && r.Stu_room == selectedRoom) ? 'selected' : '';
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
    if (classVal && roomVal) {
        document.getElementById('roomSelect').value = roomVal;
        fetchStructure(classVal, roomVal);
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
        fetchStructure(classVal, roomVal);
    } else {
        document.getElementById('resultArea').innerHTML = '<div class="text-gray-500 text-center">กรุณาเลือกห้องเพื่อดูโครงสร้างห้องเรียนสีขาว</div>';
    }
});

// เรียก API สำหรับโครงสร้างห้องเรียนสีขาว
function fetchStructure(classVal, roomVal) {
    document.getElementById('resultArea').innerHTML = '<div class="text-gray-400 text-center animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch(`api/api_wroom_structure.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
        .then(res => res.json())
        .then(data => {
            if (!data || !data.positions) {
                document.getElementById('resultArea').innerHTML = '<div class="text-gray-500 text-center">ไม่พบข้อมูล</div>';
                return;
            }
            // ฟังก์ชันแสดงรูป
            function renderPic(stu) {
                if (!stu.Stu_picture) return '';
                const url = 'https://std.phichai.ac.th/photo/' + stu.Stu_picture;
                return `<a href="${url}" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                    <img src="${url}" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                </a><br>`;
            }
            function renderTeacherPic(teacher) {
                if (!teacher.Teach_photo) return '';
                const url = 'https://std.phichai.ac.th/teacher/uploads/phototeach/' + teacher.Teach_photo ;
                return `<a href="${url}" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                    <img src="${url}" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                </a><br>`;
            }
            // โครงสร้างห้องเรียนสีขาว (แบบผัง)
            let html = `
                <div class="mb-6 text-center">
                    <span class="inline-block px-4 py-2 bg-indigo-50 rounded-full text-indigo-700 font-semibold shadow-sm animate-fade-in-down">
                        ห้อง ม.${classVal}/${roomVal}
                    </span>
                </div>
                <div class="mb-4 text-center">
                    <span class="font-semibold text-gray-700">ครูที่ปรึกษา:</span>
                    <span class="text-indigo-700 font-medium">
                        ${
                            (data.advisors && data.advisors.length)
                            ? data.advisors.map(a =>
                                `<div class="inline-block mx-2 align-top text-center">
                                    ${a.Teach_photo ? renderTeacherPic(a) + '<br>' : ''}
                                    <span>${a.Teach_name}</span>
                                </div>`
                            ).join('')
                            : '-'
                        }
                    </span>
                </div>
                <div class="overflow-x-auto flex justify-center">
                    <table class="min-w-full mx-auto border border-indigo-200 bg-white rounded-xl shadow-lg animate-fade-in">
                        <tbody>
                            <tr>
                                <td colspan="4" class="bg-indigo-100 text-center font-bold py-3 border-b border-indigo-200 text-lg tracking-wide shadow-inner animate-bounce-in">
                                    👤 หัวหน้าห้อง
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    ${
                                        (data.grouped['1'] && data.grouped['1'].length)
                                        ? data.grouped['1'].map(s => renderPic(s) + `<span class="font-semibold text-gray-800">${s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur}</span>`).join('<span class="mx-2">,</span>')
                                        : '<span class="text-gray-400">- ไม่มี -</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🚨 รองฯฝ่ายสารวัตร</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📘 รองฯฝ่ายการเรียน</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🛠️ รองฯฝ่ายการงาน</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🎉 รองฯฝ่ายกิจกรรม</td>
                            </tr>
                            <tr>
                                ${['5','2','3','4'].map(key =>
                                    `<td class="text-center py-2">` +
                                    ((data.grouped[key] && data.grouped[key].length)
                                        ? data.grouped[key].map(s => renderPic(s) + `<span class="font-xs">${s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur}</span>`).join('<span class="mx-2">,</span>')
                                        : '<span class="text-gray-400">-</span>') +
                                    `</td>`
                                ).join('')}
                            </tr>
                            <tr>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🛡️ กรรมการฝ่ายสารวัตร</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📚 กรรมการฝ่ายการเรียน</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🔧 กรรมการฝ่ายการงาน</td>
                                <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🎭 กรรมการฝ่ายกิจกรรม</td>
                            </tr>
                            <tr>
                                ${['9','6','7','8'].map(key =>
                                    `<td class="text-center">` +
                                    ((data.grouped[key] && data.grouped[key].length)
                                        ? data.grouped[key].map(s =>
                                    `<div class=" flex flex-col items-center">
                                        ${renderPic(s)}
                                        <span class="font-xs block">${s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur}
                                    </div>`
                                ).join('')
                                        : '<span class="text-gray-400">-</span>') +
                                    `</td>`
                                ).join('')}
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📝 เลขานุการ</td>
                                <td colspan="2" class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🗂️ ผู้ช่วยเลขานุการ</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center py-2">
                                    ${
                                        (data.grouped['10'] && data.grouped['10'].length)
                                        ? data.grouped['10'].map(s => renderPic(s) + `<span class="font-medium">${s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur}</span>`).join('<span class="mx-2">,</span>')
                                        : '<span class="text-gray-400">-</span>'
                                    }
                                </td>
                                <td colspan="2" class="text-center py-2">
                                    ${
                                        (data.grouped['11'] && data.grouped['11'].length)
                                        ? data.grouped['11'].map(s => renderPic(s) + `<span class="font-medium">${s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur}</span>`).join('<span class="mx-2">,</span>')
                                        : '<span class="text-gray-400">-</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="bg-indigo-100 text-center font-semibold py-3 border-t border-indigo-200 text-lg">✍️ คติพจน์ห้องเรียนสีขาว</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    ${
                                        data.maxim
                                        ? `<span class="text-indigo-700 font-bold text-lg animate-pulse">${data.maxim}</span>`
                                        : "<span class='text-gray-400 italic'>- ยังไม่ได้กรอก -</span>"
                                    }
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-8 flex justify-center gap-4">
                    <button onclick="window.print()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center transition-all duration-150 hover:scale-105 animate-bounce-in">
                        <i class="fa fa-print mr-2"></i> พิมพ์โครงสร้าง
                    </button>
                </div>
            `;
            document.getElementById('resultArea').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('resultArea').innerHTML = '<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

// Tailwind Animate
const style = document.createElement('style');
style.innerHTML = `
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes fade-in-down { from { opacity: 0; transform: translateY(-16px);} to { opacity: 1; transform: translateY(0);} }
@keyframes bounce-in { 0% { transform: scale(0.9); opacity: 0.7;} 60% { transform: scale(1.05);} 80% { transform: scale(0.98);} 100% { transform: scale(1); opacity: 1;} }
.animate-fade-in { animation: fade-in 0.7s; }
.animate-fade-in-down { animation: fade-in-down 0.7s; }
.animate-bounce-in { animation: bounce-in 0.7s; }
`;
document.head.appendChild(style);
</script>
