<?php
// ดึงค่า term และ pee จาก session หรือค่าที่กำหนดไว้
$term = isset($term) ? $term : ($_SESSION['term'] ?? 1);
$pee = isset($pee) ? $pee : ($_SESSION['pee'] ?? 2567);
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
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow" id="deduct-table">
            <thead>
                <tr class="bg-blue-100 text-blue-900">
                    <th class="py-3 px-4 text-center rounded-tl-lg">#</th>
                    <th class="py-3 px-4 text-center">เลขประจำตัว</th>
                    <th class="py-3 px-4 text-left">👤 ชื่อ - สกุล</th>
                    <th class="py-3 px-4 text-center">ชั้น</th>
                    <th class="py-3 px-4 text-center">เลขที่</th>
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

// โหลดชั้นเรียน
fetch('../api/get_classes.php')
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
        fetch('../api/get_rooms.php?class=' + encodeURIComponent(this.value))
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
        fetch(`../api/get_deduct_room.php?class=${encodeURIComponent(selectClass.value)}&room=${encodeURIComponent(this.value)}&term=${term}&pee=${pee}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students.length > 0) {
                    tableBody.innerHTML = '';
                    data.students.forEach((stu, idx) => {
                        // กำหนดกลุ่มตามคะแนน
                        let group = '';
                        let groupEmoji = '';
                        if (stu.behavior_count > 50) {
                            group = 'A';
                            groupEmoji = '🌟';
                        } else if (stu.behavior_count >= 30) {
                            group = 'B';
                            groupEmoji = '👍';
                        } else if (stu.behavior_count >= 1) {
                            group = 'C';
                            groupEmoji = '⚠️';
                        } else {
                            group = '-';
                            groupEmoji = '';
                        }
                        tableBody.innerHTML += `
                            <tr class="border-b hover:bg-blue-50 transition">
                                <td class="py-2 px-4 text-center">${idx + 1}</td>
                                <td class="py-2 px-4 text-center">${stu.Stu_id}</td>
                                <td class="py-2 px-4">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</td>
                                <td class="py-2 px-4 text-center">${stu.Stu_major}</td>
                                <td class="py-2 px-4 text-center">${stu.Stu_no}</td>
                                <td class="py-2 px-4 text-center text-red-600 font-semibold">${stu.behavior_count} ✂️</td>
                                <td class="py-2 px-4 text-center">${group} ${groupEmoji}</td>
                                <td class="py-2 px-4 text-center">${stu.behavior_count > 0 ? 'มีการหักคะแนน' : 'ไม่มีการหักคะแนน'}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
                }
            });
    } else {
        tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-center text-gray-500">กรุณาเลือกชั้นและห้อง</td></tr>';
    }
});
</script>
