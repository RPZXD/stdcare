<?php
/**
 * Sub-View: White Class Committee List (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 */
require_once("../config/Database.php");
require_once("../class/Wroom.php");
require_once("../class/Teacher.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// ดึงรายชื่อห้องทั้งหมดเพื่อใช้ใน Filter
$rooms = [];
$stmt = $db->query("SELECT Stu_major, Stu_room FROM student WHERE Stu_status=1 GROUP BY Stu_major, Stu_room ORDER BY Stu_major, Stu_room");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rooms[] = $row;
}

$selectedClass = $_GET['class'] ?? '';
$selectedRoom = $_GET['room'] ?? '';
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-sky-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-users"></i>
                </span>
                รายชื่อคณะกรรมการ <span class="text-sky-600 italic">ห้องเรียนสีขาว</span>
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Committee Members • By Room</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8 no-print">
        <form id="roomForm" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-4 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ระดับชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-sky-400"></i>
                    <select name="class" id="classSelect" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- ชั้น --</option>
                        <?php foreach(array_unique(array_column($rooms, 'Stu_major')) as $c): ?>
                            <option value="<?= $c ?>" <?= $c == $selectedClass ? 'selected' : '' ?>>มัธยมศึกษาปีที่ <?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="md:col-span-4 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ห้องเรียน</label>
                <div class="relative">
                    <i class="fas fa-door-open absolute left-4 top-1/2 -translate-y-1/2 text-sky-400"></i>
                    <select name="room" id="roomSelect" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- ห้อง --</option>
                    </select>
                </div>
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="w-full py-3.5 bg-sky-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-sky-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> แสดงข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Content Container -->
    <div id="resultArea" class="space-y-8 min-h-[200px]">
        <div class="flex flex-col items-center justify-center py-20 text-center text-slate-400 italic font-bold">
            <i class="fas fa-mouse-pointer text-4xl mb-4 opacity-20"></i>
            <p>กรุณาเลือกห้องเพื่อดูรายชื่อคณะกรรมการ</p>
        </div>
    </div>

    <!-- Action Buttons (Hidden by default) -->
    <div id="actionButtons" class="hidden flex flex-wrap justify-center gap-4 mt-10 no-print">
        <button id="printBtn" class="px-8 py-3.5 bg-emerald-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-print"></i> พิมพ์รายชื่อ
        </button>
        <button id="wordBtn" class="px-8 py-3.5 bg-blue-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-file-word"></i> ส่งออกเป็น Word
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    const allRooms = <?= json_encode($rooms) ?>;
    const $classSelect = $('#classSelect');
    const $roomSelect = $('#roomSelect');
    const $resultArea = $('#resultArea');
    const $actionButtons = $('#actionButtons');

    function updateRoomSelect(selectedClass, selectedRoom = '') {
        $roomSelect.html('<option value="">-- ห้อง --</option>');
        allRooms.forEach(r => {
            if (!selectedClass || r.Stu_major == selectedClass) {
                const isSelected = (selectedRoom && r.Stu_room == selectedRoom) ? 'selected' : '';
                $roomSelect.append(`<option value="${r.Stu_room}" ${isSelected}>${r.Stu_room}</option>`);
            }
        });
    }

    $classSelect.on('change', function() {
        updateRoomSelect($(this).val());
    });

    $('#roomForm').on('submit', function(e) {
        e.preventDefault();
        const classVal = $classSelect.val();
        const roomVal = $roomSelect.val();

        if (classVal && roomVal) {
            fetchCommittee(classVal, roomVal);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกข้อมูล',
                text: 'กรุณาเลือกชั้นและห้องให้ครบถ้วน',
                confirmButtonColor: '#0ea5e9'
            });
        }
    });

    function fetchCommittee(classVal, roomVal) {
        $resultArea.html(`
            <div class="flex flex-col items-center justify-center py-20 text-center animate-pulse">
                <div class="w-16 h-16 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-sm font-bold text-slate-500 italic">กำลังโหลดข้อมูลคณะกรรมการ...</p>
            </div>
        `);
        $actionButtons.addClass('hidden');

        fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
            .then(res => res.json())
            .then(data => {
                if (!data || !data.positions) {
                    $resultArea.html('<div class="text-slate-400 text-center py-20 font-bold italic">ไม่พบข้อมูล</div>');
                    return;
                }

                let html = `
                    <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 border border-white/40 shadow-xl">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 border-b border-slate-100 dark:border-slate-800 pb-8">
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">ห้อง ม.${classVal}/${roomVal}</h3>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 bg-sky-500/10 text-sky-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-sky-500/20">ครูที่ปรึกษา</span>
                                    <span class="text-[13px] font-black text-slate-500 italic">
                                        ${data.advisors && data.advisors.length ? data.advisors.map(a => a.Teach_name).join(', ') : '-'}
                                    </span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">ปีการศึกษา</span>
                                <span class="text-xl font-black text-sky-600 italic">2568</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                `;

                Object.entries(data.positions).forEach(([key, label]) => {
                    const members = data.grouped[key] || [];
                    const memberNames = members.length 
                        ? members.map(s => s.Stu_pre + s.Stu_name + ' ' + s.Stu_sur).join(', ') 
                        : '- ไม่มี -';
                    
                    html += `
                        <div class="flex flex-col md:flex-row items-start md:items-center py-4 px-6 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                            <span class="w-full md:w-64 text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-1 md:mb-0">${label}</span>
                            <div class="flex-1">
                                <span class="text-[14px] font-black ${members.length ? 'text-slate-700 dark:text-slate-300' : 'text-slate-300'}">
                                    ${memberNames}
                                </span>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>

                        <div class="mt-10 p-8 rounded-[2rem] bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-xl shadow-sky-600/20 relative overflow-hidden group">
                            <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-all duration-700"></div>
                            <div class="relative z-10 text-center">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-2 block italic">✍️ คติพจน์ประจำห้องเรียน</span>
                                <p class="text-xl md:text-2xl font-black italic">${data.maxim || '- ยังไม่ได้กรอก -'}</p>
                            </div>
                        </div>
                    </div>
                `;

                $resultArea.hide().html(html).fadeIn(500);
                $actionButtons.removeClass('hidden');
            })
            .catch(() => {
                $resultArea.html('<div class="text-rose-500 text-center py-20 font-bold italic">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            });
    }

    // Reuse and modernize print logic
    function buildOrderedList(data) {
        let list = [];
        if (data.advisors) data.advisors.forEach(a => list.push({ name: a.Teach_name, pos: 'ครูที่ปรึกษา' }));
        const order = ['1', '10', '11']; // Head, Secretary, Assist
        order.forEach(k => {
            if (data.grouped[k]) data.grouped[k].forEach(s => list.push({ name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`, pos: data.positions[k] }));
        });
        Object.keys(data.positions).forEach(k => {
            if (order.includes(k)) return;
            if (data.grouped[k]) data.grouped[k].forEach(s => list.push({ name: `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`, pos: data.positions[k] }));
        });
        return list;
    }

    $('#printBtn').on('click', function() {
        const classVal = $classSelect.val();
        const roomVal = $roomSelect.val();
        fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
            .then(res => res.json())
            .then(data => {
                const list = buildOrderedList(data);
                const toThaiNum = n => String(n).replace(/\d/g, d => '๐๑๒๓๔๕๖๗๘๙'[d]);
                let lines = [`ระดับชั้นมัธยมศึกษาปีที่ ${toThaiNum(classVal)}/${toThaiNum(roomVal)}`];
                list.forEach((item, idx) => {
                    let p = item.name.split(' ');
                    lines.push(`${toThaiNum(idx+1)}.${p[0] || ''}\t${p.slice(1).join(' ')}\t\t${item.pos}`);
                });
                const win = window.open('', '', 'width=900,height=650');
                win.document.write(`<html><body onload="window.print();window.close()"><pre style="font-family:Tahoma; line-height:2;">${lines.join('\n')}</pre></body></html>`);
                win.document.close();
            });
    });

    $('#wordBtn').on('click', function() {
        // Simple Word Export Logic
        const classVal = $classSelect.val();
        const roomVal = $roomSelect.val();
        fetch(`api/api_wroom_committee.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
            .then(res => res.json())
            .then(data => {
                const list = buildOrderedList(data);
                const toThaiNum = n => String(n).replace(/\d/g, d => '๐๑๒๓๔๕๖๗๘๙'[d]);
                let content = `ระดับชั้นมัธยมศึกษาปีที่ ${toThaiNum(classVal)}/${toThaiNum(roomVal)}\n\n`;
                list.forEach((item, idx) => content += `${idx+1}. ${item.name} (${item.pos})\n`);
                let blob = new Blob([content], {type: 'application/msword'});
                let url = URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = `WhiteClass_${classVal}_${roomVal}.doc`;
                a.click();
            });
    });

    // Check query params
    const urlParams = new URLSearchParams(window.location.search);
    const cParam = urlParams.get('class');
    const rParam = urlParams.get('room');
    if (cParam) {
        $classSelect.val(cParam);
        updateRoomSelect(cParam, rParam);
        if (rParam) fetchCommittee(cParam, rParam);
    }
});
</script>
