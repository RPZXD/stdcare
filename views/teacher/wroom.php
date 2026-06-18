<?php
/**
 * White Room View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
$pageTitle = $title ?? 'ห้องเรียนสีขาว';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.85);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    .position-select {
        transition: all 0.2s ease;
    }
    .position-select:focus {
        transform: scale(1.02);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .slide-in {
        animation: slideIn 0.3s ease-out forwards;
    }
    /* Mobile card view */
    @media (max-width: 767px) {
        #desktopTable { display: none !important; }
    }
    @media (min-width: 768px) {
        #mobileCards { display: none !important; }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-teal-500/20 to-emerald-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">🏠</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    ห้องเรียนสีขาว
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-teal-500 mr-1"></i>
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-teal-500 mr-1"></i>
                    ปีการศึกษา <?= htmlspecialchars($pee) ?>
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    ครูที่ปรึกษา: 
                    <?php foreach ($roomTeachers as $t): ?>
                        <span class="font-semibold"><?= $t['Teach_name'] ?></span>
                    <?php endforeach; ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Instructions Card -->
<div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-lg mb-4 md:mb-6">
    <div class="flex items-start gap-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow flex-shrink-0">
            <span class="text-lg">📌</span>
        </div>
        <div>
            <h3 class="font-bold text-slate-800 dark:text-white">คำชี้แจง</h3>
            <p class="text-xs text-slate-500 mt-1">เลือกตำแหน่งคณะกรรมการดำเนินงานห้องเรียนสีขาว</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-xs">
        <div class="bg-rose-50 dark:bg-rose-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>👤</span>
            <div><span class="font-bold">หัวหน้าห้อง</span><span class="text-rose-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>📘</span>
            <div><span class="font-bold">รอง ฝ่ายเรียน</span><span class="text-blue-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>🛠️</span>
            <div><span class="font-bold">รอง ฝ่ายการงาน</span><span class="text-orange-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>🎉</span>
            <div><span class="font-bold">รอง ฝ่ายกิจกรรม</span><span class="text-purple-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>🚨</span>
            <div><span class="font-bold">รอง ฝ่ายสารวัตร</span><span class="text-red-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>📝</span>
            <div><span class="font-bold">เลขานุการ</span><span class="text-teal-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>🗂️</span>
            <div><span class="font-bold">ผู้ช่วยเลขาฯ</span><span class="text-cyan-500 ml-1">(1)</span></div>
        </div>
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2 flex items-center gap-2">
            <span>📚</span>
            <div><span class="font-bold">แกนนำ 4 ฝ่าย</span><span class="text-indigo-500 ml-1">(4×4)</span></div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="grid grid-cols-2 gap-2 md:gap-4 mb-4 md:mb-6">
    <button onclick="location.href='report_wroom.php'" class="btn-action glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-left hover:border-teal-400">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-lg flex items-center justify-center shadow">
                <i class="fas fa-users text-white"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 dark:text-white text-sm">รายชื่อคณะกรรมการ</p>
                <p class="text-[10px] text-slate-500">ดูรายชื่อทั้งหมด</p>
            </div>
        </div>
    </button>
    <button onclick="location.href='report_wroom2.php'" class="btn-action glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-left hover:border-blue-400">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center shadow">
                <i class="fas fa-sitemap text-white"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 dark:text-white text-sm">ผังโครงสร้าง</p>
                <p class="text-[10px] text-slate-500">แผนผังองค์กร</p>
            </div>
        </div>
    </button>
</div>

<!-- Form -->
<form id="wroomForm">
    <!-- Desktop Table -->
    <div id="desktopTable" class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl mb-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-xl flex items-center justify-center shadow">
                <i class="fas fa-table text-white"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">📋 รายชื่อนักเรียน</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="studentTable">
                <thead>
                    <tr class="bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                        <th class="px-3 py-3 text-center rounded-tl-xl w-16">เลขที่</th>
                        <th class="px-3 py-3 text-center w-28">รหัส</th>
                        <th class="px-3 py-3 text-left">ชื่อ-นามสกุล</th>
                        <th class="px-3 py-3 text-center rounded-tr-xl w-48">ตำแหน่ง</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody" class="divide-y divide-slate-200 dark:divide-slate-700">
                    <tr>
                        <td colspan="4" class="text-center py-8">
                            <div class="animate-spin w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            <p class="text-slate-500">กำลังโหลด...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div id="mobileCards" class="space-y-2 mb-4">
        <div id="mobileCardsContainer">
            <div class="glass-card rounded-2xl p-8 text-center">
                <div class="animate-spin w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                <p class="text-slate-500 text-sm">กำลังโหลด...</p>
            </div>
        </div>
    </div>

    <!-- Maxim Section -->
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-lg mb-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow">
                <span class="text-lg">✍️</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white">คติพจน์ห้องเรียนสีขาว</h3>
                <p class="text-xs text-slate-500">กรอกคติพจน์ประจำห้อง</p>
            </div>
        </div>
        <textarea name="maxim" id="maxim" rows="3" 
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white dark:bg-slate-800 text-slate-800 dark:text-white resize-none"
                  placeholder="เช่น 'ห้องเรียนสะอาด น่าอยู่ น่าเรียน'"></textarea>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" name="major" value="<?= htmlspecialchars($class) ?>">
    <input type="hidden" name="room" value="<?= htmlspecialchars($room) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">

    <!-- Submit Button -->
    <button type="submit" class="btn-action w-full py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-bold text-lg rounded-2xl shadow-lg flex items-center justify-center gap-2">
        <i class="fas fa-save"></i>
        บันทึกข้อมูล
    </button>
</form>

<!-- Scripts -->
<script>
(function() {
    const stu_major = '<?= addslashes($class) ?>';
    const stu_room = '<?= addslashes($room) ?>';
    const pee = '<?= addslashes($pee) ?>';

    // Position data
    const positions = [
        { key: "", value: "สมาชิก", emoji: "👥", color: "slate" },
        { key: "1", value: "หัวหน้าห้อง", emoji: "👤", color: "rose" },
        { key: "2", value: "รองฯ ฝ่ายการเรียน", emoji: "📘", color: "blue" },
        { key: "3", value: "รองฯ ฝ่ายการงาน", emoji: "🛠️", color: "orange" },
        { key: "4", value: "รองฯ ฝ่ายกิจกรรม", emoji: "🎉", color: "purple" },
        { key: "5", value: "รองฯ ฝ่ายสารวัตร", emoji: "🚨", color: "red" },
        { key: "6", value: "แกนนำ ฝ่ายการเรียน", emoji: "📚", color: "sky" },
        { key: "7", value: "แกนนำ ฝ่ายการงาน", emoji: "🔧", color: "amber" },
        { key: "8", value: "แกนนำ ฝ่ายกิจกรรม", emoji: "🎭", color: "violet" },
        { key: "9", value: "แกนนำ ฝ่ายสารวัตร", emoji: "🛡️", color: "pink" },
        { key: "10", value: "เลขานุการ", emoji: "📝", color: "teal" },
        { key: "11", value: "ผู้ช่วยเลขานุการ", emoji: "🗂️", color: "cyan" }
    ];

    const positionLimits = {
        "1": 1, "2": 1, "3": 1, "4": 1, "5": 1, "10": 1, "11": 1,
        "6": 4, "7": 4, "8": 4, "9": 4
    };

    // Fetch data
    async function fetchWroomData() {
        const res = await fetch('../teacher/api/api_wroom.php?major=' + encodeURIComponent(stu_major) + '&room=' + encodeURIComponent(stu_room) + '&pee=' + encodeURIComponent(pee));
        return await res.json();
    }

    async function fetchMaxim() {
        const res = await fetch('../teacher/api/api_wroom_maxim.php?major=' + encodeURIComponent(stu_major) + '&room=' + encodeURIComponent(stu_room) + '&pee=' + encodeURIComponent(pee));
        const data = await res.json();
        document.getElementById('maxim').value = data.maxim || '';
    }

    // Build select dropdown
    function buildSelect(currentValue, studentId) {
        const options = positions.map(pos => 
            `<option value="${pos.key}" ${currentValue == pos.key ? 'selected' : ''}>${pos.emoji} ${pos.value}</option>`
        ).join('');
        return `
            <select name="position[]" data-student-id="${studentId}" class="position-select w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 bg-white dark:bg-slate-800">
                ${options}
            </select>
            <input type="hidden" name="stdid[]" value="${studentId}">
        `;
    }

    // Render desktop table
    function renderTable(students) {
        const tbody = document.getElementById('studentTableBody');
        let html = '';
        students.forEach((row, idx) => {
            html += `
                <tr class="${idx % 2 === 0 ? 'bg-white dark:bg-slate-800' : 'bg-slate-50 dark:bg-slate-800/50'} slide-in" style="animation-delay: ${idx * 0.02}s">
                    <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-300">${row.Stu_no}</td>
                    <td class="px-3 py-3 text-center text-sm text-slate-600 dark:text-slate-400">${row.Stu_id}</td>
                    <td class="px-3 py-3 text-left font-semibold text-slate-800 dark:text-white">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</td>
                    <td class="px-3 py-3 text-center">${buildSelect(row.wposit, row.Stu_id)}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // Render mobile cards
    function renderMobileCards(students) {
        const container = document.getElementById('mobileCardsContainer');
        let html = '';
        students.forEach((row, idx) => {
            html += `
                <div class="glass-card rounded-xl p-3 border border-white/30 dark:border-slate-700/50 shadow slide-in" style="animation-delay: ${idx * 0.02}s">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">${row.Stu_no}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 dark:text-white text-sm truncate">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</p>
                            <p class="text-[10px] text-slate-500">${row.Stu_id}</p>
                        </div>
                    </div>
                    <div>${buildSelect(row.wposit, row.Stu_id)}</div>
                </div>
            `;
        });
        container.innerHTML = html || '<div class="text-center py-8 text-slate-500">ไม่พบข้อมูล</div>';
    }

    // Sync dropdown changes between desktop and mobile views
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('position-select')) {
            const studentId = e.target.getAttribute('data-student-id');
            const newValue = e.target.value;
            const otherSelects = document.querySelectorAll(`select.position-select[data-student-id="${studentId}"]`);
            otherSelects.forEach(sel => {
                if (sel !== e.target) {
                    sel.value = newValue;
                }
            });
        }
    });

    // Form submit
    document.getElementById('wroomForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const isMobile = window.innerWidth < 768;
        const activeContainer = isMobile ? document.getElementById('mobileCards') : document.getElementById('desktopTable');
        const inactiveContainer = isMobile ? document.getElementById('desktopTable') : document.getElementById('mobileCards');
        
        // Temporarily disable inactive inputs so they are not submitted/validated
        const inactiveInputs = inactiveContainer.querySelectorAll('select, input');
        inactiveInputs.forEach(input => input.disabled = true);
        
        // Validate position limits
        const selects = activeContainer.querySelectorAll('select[name="position[]"]');
        const count = {};
        selects.forEach(sel => {
            const val = sel.value;
            if (val && positionLimits[val]) {
                count[val] = (count[val] || 0) + 1;
            }
        });
        
        let over = [];
        for (const key in positionLimits) {
            if ((count[key] || 0) > positionLimits[key]) {
                const pos = positions.find(p => p.key === key);
                over.push(`${pos.emoji} ${pos.value} (${count[key]}/${positionLimits[key]})`);
            }
        }
        
        if (over.length > 0) {
            // Re-enable inactive inputs before returning
            inactiveInputs.forEach(input => input.disabled = false);
            
            Swal.fire({
                icon: 'error',
                title: 'เลือกตำแหน่งเกินกำหนด',
                html: over.join('<br>'),
                confirmButtonColor: '#14b8a6'
            });
            return;
        }

        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        const formData = new FormData(this);
        
        // Re-enable inactive inputs immediately after constructing FormData
        inactiveInputs.forEach(input => input.disabled = false);

        const res = await fetch('../teacher/api/api_wroom_save.php', { method: 'POST', body: formData });
        const result = await res.json();

        if (result.success) {
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: result.message, confirmButtonColor: '#14b8a6' });
        }
    });

    // Initial load
    (async function() {
        const students = await fetchWroomData();
        renderTable(students);
        renderMobileCards(students);
        await fetchMaxim();
    })();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
