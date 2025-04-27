<?php
// สมมติว่ามี $term และ $pee จาก session หรือ context เดียวกับ report.php
?>
<div>
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        📊 รายงานการหักคะแนน (แบ่งตามกลุ่ม)
    </h2>
    <div class="flex items-center gap-4 mb-6">
        <label class="font-medium" for="group-select">เลือกกลุ่มคะแนน:</label>
        <select id="group-select" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">-- เลือกกลุ่ม --</option>
            <option value="1">คะแนนพฤติกรรมต่ำกว่า 50 คะแนน</option>
            <option value="2">คะแนนพฤติกรรมอยู่ระหว่าง 50 - 70 คะแนน</option>
            <option value="3">คะแนนพฤติกรรมอยู่ระหว่าง 71 - 99 คะแนน</option>
        </select>
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
const term = typeof window.term !== 'undefined' ? window.term : <?= isset($term) ? json_encode($term) : '1' ?>;
const pee = typeof window.pee !== 'undefined' ? window.pee : <?= isset($pee) ? json_encode($pee) : '2567' ?>;

groupSelect.addEventListener('change', function() {
    if (!this.value) {
        groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">กรุณาเลือกกลุ่มคะแนน</td></tr>';
        return;
    }
    groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-400">กำลังโหลดข้อมูล...</td></tr>';
    fetch(`api/get_deduct_group.php?group=${this.value}&term=${term}&pee=${pee}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.students.length > 0) {
                groupTableBody.innerHTML = '';
                data.students.forEach((stu, idx) => {
                    // คะแนนเต็ม 100 หัก behavior_count
                    const score = 100 - parseInt(stu.behavior_count, 10);
                    // สี bar ตามช่วงคะแนน
                    let barColor = 'bg-green-500';
                    if (score < 50) barColor = 'bg-red-500';
                    else if (score <= 70) barColor = 'bg-yellow-400';

                    groupTableBody.innerHTML += `
                        <tr class="border-b hover:bg-pink-50 transition">
                            <td class="py-2 px-4 text-center">${idx + 1}</td>
                            <td class="py-2 px-4 text-center">${stu.Stu_id}</td>
                            <td class="py-2 px-4">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</td>
                            <td class="py-2 px-4 text-center">ม.${stu.Stu_major}/${stu.Stu_room}</td>
                            <td class="py-2 px-4 text-center">${stu.Stu_no}</td>
                            <td class="py-2 px-4 text-center text-red-600 font-semibold">${stu.behavior_count} ✂️</td>
                            <td class="py-2 px-4">
                                <div class="w-32 bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div class="${barColor} h-4 rounded-full transition-all" style="width: ${score}%;"></div>
                                </div>
                                <div class="text-xs text-gray-600 mt-1 text-center">${score} / 100</div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                groupTableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
            }
        });
});
</script>
