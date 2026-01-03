<?php
/**
 * Sub-View: Parent Network Leader Report (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
include_once("../class/BoardParent.php");

$boardParent = new BoardParent($db);
$pee = $user->getPee();

// ดึงระดับชั้นทั้งหมด
$levels = [];
try {
    $sql = "SELECT DISTINCT parn_lev
            FROM tb_parnet
            WHERE parn_pee = :pee
            AND parn_lev IS NOT NULL
            AND parn_lev != ''
            ORDER BY parn_lev";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
    $stmt->execute();
    $levels = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $levels = [];
}

$selected_level = isset($_GET['level']) ? $_GET['level'] : '';
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-violet-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-people-roof"></i>
                </span>
                รายงาน <span class="text-violet-600 italic">ประธานเครือข่ายผู้ปกครอง</span>
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Parent Network Leaders • Academic Year <?= htmlspecialchars($pee) ?></p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกระดับชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
                    <select id="level-filter" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-violet-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- ทุกระดับชั้น --</option>
                        <?php foreach ($levels as $lev): ?>
                            <option value="<?= htmlspecialchars($lev) ?>"><?= htmlspecialchars($lev) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="md:col-span-3 md:col-start-10">
                <button onclick="window.printReport ? window.printReport() : window.print()" class="w-full py-3.5 bg-violet-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-violet-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2 no-print">
                    <i class="fas fa-print"></i> พิมพ์รายงาน
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div id="stats-container" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 hidden animate-fadeIn">
        <div class="bg-violet-50/50 dark:bg-violet-900/20 px-6 py-5 rounded-[2rem] border border-violet-100/50 dark:border-violet-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-violet-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-violet-600/60 dark:text-violet-400 uppercase tracking-widest italic">จำนวนผู้ปกครอง</p>
                <p id="stat-total" class="text-2xl font-black text-slate-800 dark:text-white">0 คน</p>
            </div>
        </div>
        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 px-6 py-5 rounded-[2rem] border border-indigo-100/50 dark:border-indigo-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-school text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-600/60 dark:text-indigo-400 uppercase tracking-widest italic">ระดับชั้น</p>
                <p id="stat-level" class="text-2xl font-black text-slate-800 dark:text-white">ทั้งหมด</p>
            </div>
        </div>
        <div class="bg-amber-50/50 dark:bg-amber-900/20 px-6 py-5 rounded-[2rem] border border-amber-100/50 dark:border-amber-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-amber-600/60 dark:text-amber-400 uppercase tracking-widest italic">ปีการศึกษา</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white"><?= htmlspecialchars($pee) ?></p>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-left border-separate border-spacing-y-2" id="parent-leader-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl text-center">ลำดับ</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชื่อ - นามสกุล</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ระดับชั้น / ห้อง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ตำแหน่ง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">เบอร์โทรศัพท์</th>
                </tr>
            </thead>
            <tbody id="parent-leader-tbody" class="font-bold text-slate-700 dark:text-slate-300">
                <!-- Loaded via JS -->
            </tbody>
        </table>
        
        <!-- Empty/Loading State -->
        <div id="table-empty-state" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 border-4 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-500 italic mt-4">กำลังโหลดข้อมูลผู้ปกครอง...</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const $tbody = $('#parent-leader-tbody');
    const $emptyState = $('#table-empty-state');
    const $stats = $('#stats-container');
    const $levelFilter = $('#level-filter');
    const pee = <?= json_encode($pee) ?>;

    function fetchLeaders(level = '') {
        $tbody.empty();
        $emptyState.html(`
            <div class="flex flex-col items-center gap-4 py-10">
                <div class="w-12 h-12 border-4 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm font-bold text-slate-500 italic">กำลังโหลดข้อมูลผู้ปกครอง...</p>
            </div>
        `).show();
        $stats.addClass('hidden');

        const params = new URLSearchParams();
        params.append('pee', pee);
        if (level) params.append('level', level);

        fetch('api/api_parent_leader.php?' + params.toString())
            .then(res => res.json())
            .then(json => {
                $tbody.empty();
                const data = json.data || [];
                
                if (data.length > 0) {
                    $emptyState.hide();
                    $stats.removeClass('hidden');
                    $('#stat-total').text(data.length + ' คน');
                    $('#stat-level').text(level ? 'ม.' + level : 'ทั้งหมด');

                    data.forEach((row, idx) => {
                        const html = `
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800 text-center" data-label="ลำดับ">
                                    <span class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-600 inline-flex items-center justify-center text-[11px] font-black italic">
                                        ${idx + 1}
                                    </span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800" data-label="ชื่อ - นามสกุล">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-lg">
                                            ${(row.parn_name || '?').charAt(0)}
                                        </div>
                                        <div class="text-[13px] font-black text-slate-800 dark:text-white">${row.parn_name || '-'}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ระดับชั้น / ห้อง">
                                    <span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black italic">
                                        ม.${row.parn_lev || '-'}/${row.parn_room || '-'}
                                    </span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ตำแหน่ง">
                                    <span class="px-3 py-1.5 bg-violet-500/10 text-violet-600 dark:text-violet-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-violet-500/20">
                                        <i class="fas fa-crown mr-1 text-[8px]"></i> ประธาน
                                    </span>
                                </td>
                                <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center" data-label="เบอร์โทรศัพท์">
                                    <span class="text-[12px] font-black text-slate-600 dark:text-slate-300">
                                        <i class="fas fa-phone-alt mr-1.5 text-violet-400 text-[10px]"></i> ${row.parn_tel || 'ไม่ระบุ'}
                                    </span>
                                </td>
                            </tr>
                        `;
                        $tbody.append(html);
                    });

                    if (typeof updateMobileLabels === 'function') updateMobileLabels();
                    
                } else {
                    $stats.addClass('hidden');
                    $emptyState.html(`
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-500 mb-6">
                                <i class="fas fa-users-slash text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-white">ไม่พบข้อมูล</h3>
                            <p class="text-sm text-slate-400 mt-2 font-bold italic">ไม่พบข้อมูลประธานเครือข่ายผู้ปกครองในระดับชั้นที่เลือก</p>
                        </div>
                    `).show();
                }
            })
            .catch(err => {
                console.error(err);
                $emptyState.html(`
                    <div class="text-rose-500 font-black italic py-10">
                        <i class="fas fa-circle-exclamation mr-2"></i> เกิดข้อผิดพลาดในการโหลดข้อมูล
                    </div>
                `).show();
            });
    }

    // Initial load
    fetchLeaders($levelFilter.val());

    // Filter change
    $levelFilter.on('change', function() {
        fetchLeaders(this.value);
    });
});
</script>
