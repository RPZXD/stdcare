<?php
/**
 * Sub-View: White Class Organizational Structure (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 */
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

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-sitemap"></i>
                </span>
                โครงสร้าง <span class="text-indigo-600 italic">ห้องเรียนสีขาว</span>
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">White Classroom Structure • Organizational Chart</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8 no-print shadow-sm">
        <form id="roomForm" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-4 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ระดับชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <select name="class" id="classSelect" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- ชั้น --</option>
                        <?php foreach(array_unique(array_column($rooms, 'Stu_major')) as $c): ?>
                            <option value="<?= $c ?>">มัธยมศึกษาปีที่ <?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="md:col-span-4 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ห้องเรียน</label>
                <div class="relative">
                    <i class="fas fa-door-open absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <select name="room" id="roomSelect" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- ห้อง --</option>
                    </select>
                </div>
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search-plus"></i> แสดงแผนผัง
                </button>
            </div>
        </form>
    </div>

    <!-- Content Container -->
    <div id="resultArea" class="space-y-10 min-h-[300px]">
        <div class="flex flex-col items-center justify-center py-20 text-center text-slate-400 italic font-bold">
            <i class="fas fa-project-diagram text-5xl mb-4 opacity-10"></i>
            <p>เลือกห้องเพื่อแสดงโครงสร้างผังงาน</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const allRooms = <?= json_encode($rooms) ?>;
    const $classSelect = $('#classSelect');
    const $roomSelect = $('#roomSelect');
    const $resultArea = $('#resultArea');

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
        if (classVal && roomVal) fetchStructure(classVal, roomVal);
    });

    function fetchStructure(classVal, roomVal) {
        $resultArea.html(`
            <div class="flex flex-col items-center justify-center py-20 text-center animate-pulse">
                <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-sm font-bold text-slate-500 italic">กำลังสร้างผังโครงสร้าง...</p>
            </div>
        `);

        fetch(`api/api_wroom_structure.php?major=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`)
            .then(res => res.json())
            .then(data => {
                if (!data || !data.positions) {
                    $resultArea.html('<p class="text-center py-20 font-bold opacity-30">ไม่พบข้อมูล</p>');
                    return;
                }

                function getAvatar(stu) {
                    const url = stu.Stu_picture ? 'https://std.phichai.ac.th/photo/' + stu.Stu_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(stu.Stu_name) + '&background=random&color=fff';
                    return `
                        <div class="relative group">
                            <img src="${url}" class="w-14 h-14 md:w-16 md:h-16 rounded-2xl object-cover shadow-lg border-2 border-white dark:border-slate-800 transition-all group-hover:scale-110 group-hover:rotate-3">
                            <div class="absolute inset-0 rounded-2xl shadow-inner pointer-events-none"></div>
                        </div>
                    `;
                }

                function getTeacherAvatar(t) {
                    const url = t.Teach_photo ? 'https://std.phichai.ac.th/teacher/uploads/phototeach/' + t.Teach_photo : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(t.Teach_name) + '&background=6366f1&color=fff';
                    return `<img src="${url}" class="w-12 h-12 rounded-full object-cover border-2 border-indigo-200 shadow-sm mb-2 mx-auto">`;
                }

                function renderNode(key, label, color = 'indigo') {
                    const students = data.grouped[key] || [];
                    if (students.length === 0) return `<div class="p-4 rounded-3xl bg-slate-50 border border-slate-100 dark:bg-slate-800/50 dark:border-slate-700 opacity-50"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">${label}</span><span class="text-xs text-slate-300 italic">- ไม่มี -</span></div>`;
                    
                    return `
                        <div class="p-5 rounded-[2rem] bg-white dark:bg-slate-900 border border-slate-100 shadow-sm hover:shadow-md transition-all">
                            <span class="text-[9px] font-black text-${color}-500 uppercase tracking-[0.2em] block mb-3 italic">${label}</span>
                            <div class="flex flex-wrap justify-center gap-4">
                                ${students.map(s => `
                                    <div class="flex flex-col items-center">
                                        ${getAvatar(s)}
                                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 mt-2">${s.Stu_name}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                let html = `
                    <div class="glass-effect rounded-[3rem] p-8 md:p-12 border border-white/40 shadow-2xl relative overflow-hidden text-center">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                        
                        <!-- Header / Advisors -->
                        <div class="mb-12">
                            <div class="inline-block px-6 py-2 bg-indigo-500 text-white rounded-full text-[10px] font-black uppercase tracking-[0.3em] mb-6 shadow-lg shadow-indigo-500/20 italic">
                                ผังโครงสร้างระดับชั้น ม.${classVal}/${roomVal}
                            </div>
                            <div class="flex flex-wrap justify-center gap-8">
                                ${data.advisors.map(a => `
                                    <div class="flex flex-col items-center">
                                        ${getTeacherAvatar(a)}
                                        <span class="text-sm font-black text-slate-800 dark:text-white">${a.Teach_name}</span>
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic">ครูที่ปรึกษา</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <!-- Organization Tree -->
                        <div class="space-y-8">
                            <!-- Tier 1: Leader -->
                            <div class="max-w-md mx-auto">
                                ${renderNode('1', '👤 หัวหน้าห้อง', 'indigo')}
                            </div>

                            <!-- Tier 2: Vice Leaders -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                ${renderNode('5', '🚨 รองฯ ฝ่ายสารวัตร', 'rose')}
                                ${renderNode('2', '📘 รองฯ ฝ่ายการเรียน', 'sky')}
                                ${renderNode('3', '🛠️ รองฯ ฝ่ายการงาน', 'emerald')}
                                ${renderNode('4', '🎉 รองฯ ฝ่ายกิจกรรม', 'amber')}
                            </div>

                            <!-- Tier 3: Core Members -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                ${renderNode('9', '🛡️ กรรมการสารวัตร', 'rose')}
                                ${renderNode('6', '📚 กรรมการการเรียน', 'sky')}
                                ${renderNode('7', '🔧 กรรมการการงาน', 'emerald')}
                                ${renderNode('8', '🎭 กรรมการกิจกรรม', 'amber')}
                            </div>

                            <!-- Tier 4: Secretary -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                                ${renderNode('10', '📝 เลขานุการ', 'indigo')}
                                ${renderNode('11', '🗂️ ผู้ช่วยเลขานุการ', 'indigo')}
                            </div>
                        </div>

                        <!-- Motto -->
                        <div class="mt-12 p-8 rounded-[2.5rem] bg-indigo-600 text-white shadow-2xl shadow-indigo-600/20 relative group">
                            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                            <span class="text-[10px] font-black uppercase tracking-[0.4em] mb-3 block opacity-60">✍️ คติพจน์ห้องเรียนสีขาว</span>
                            <p class="text-2xl md:text-3xl font-black italic tracking-tight">${data.maxim || '- ยังไม่ได้กรอกคติพจน์ -'}</p>
                        </div>
                        
                        <div class="mt-8 no-print">
                            <button onclick="window.print()" class="px-8 py-3 bg-slate-800 hover:bg-black text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl">
                                <i class="fas fa-print mr-2"></i> พิมพ์โครงสร้าง
                            </button>
                        </div>
                    </div>
                `;

                $resultArea.hide().html(html).fadeIn(500);
            })
            .catch(() => {
                $resultArea.html('<p class="text-center py-20 text-rose-500 font-bold italic">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>');
            });
    }

    // Auto load if params exist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('class')) {
        const c = urlParams.get('class');
        const r = urlParams.get('room');
        $classSelect.val(c);
        updateRoomSelect(c, r);
        if (r) fetchStructure(c, r);
    }
});
</script>
