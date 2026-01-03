<?php
/**
 * Sub-View: Deduct Point Report by Group (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
$term = $user->getTerm();
$pee = $user->getPee();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-layer-group"></i>
                </span>
                รายงานการหักคะแนน <span class="text-rose-600 italic">แบ่งตามกลุ่ม</span>
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Behavioral Score Groups Analysis</p>
        </div>
        
        <div class="flex gap-2 no-print">
            <button id="print-btn" class="px-5 py-2.5 bg-rose-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-rose-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <!-- Interactive Navigation -->
    <div class="flex flex-wrap gap-2 mb-8 no-print" id="tab-group">
        <?php 
        $tabs = [
            ['id' => 'all', 'label' => 'รวมทั้งหมด', 'icon' => 'globe'],
            ['id' => 'level', 'label' => 'แยกช่วงชั้น', 'icon' => 'arrows-up-down'],
            ['id' => 'class', 'label' => 'แยกตามระดับชั้น', 'icon' => 'chevron-up'],
            ['id' => 'room', 'label' => 'แยกตามห้อง', 'icon' => 'door-open']
        ];
        foreach ($tabs as $idx => $tab): ?>
            <button data-type="<?= $tab['id'] ?>" class="tab-btn px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 <?= $idx === 0 ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'bg-white dark:bg-slate-900 text-slate-400 hover:text-rose-500 hover:bg-rose-50 border border-slate-100 dark:border-slate-800 shadow-sm' ?>">
                <i class="fas fa-<?= $tab['icon'] ?> opacity-60"></i>
                <?= $tab['label'] ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-end">
            <!-- Group Select - Always Visible except maybe for 'all' tab if we want -->
            <div class="lg:col-span-4 space-y-2 group-selector">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกกลุ่มคะแนน</label>
                <div class="relative">
                    <i class="fas fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                    <select id="group-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- เลือกกลุ่มคะแนน --</option>
                        <option value="1">ต่ำกว่า 50 คะแนน</option>
                        <option value="2">50 - 70 คะแนน</option>
                        <option value="3">71 - 99 คะแนน</option>
                    </select>
                </div>
            </div>

            <!-- Contextual Filters -->
            <div id="filter-level" class="lg:col-span-4 space-y-2 hidden">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกช่วงชั้น</label>
                <div class="relative">
                    <i class="fas fa-arrow-up-wide-short absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                    <select id="level-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- เลือกช่วงชั้น --</option>
                        <option value="lower">มัธยมศึกษาตอนต้น</option>
                        <option value="upper">มัธยมศึกษาตอนปลาย</option>
                    </select>
                </div>
            </div>

            <div id="filter-class" class="lg:col-span-4 space-y-2 hidden">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                    <select id="class-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- เลือกชั้น --</option>
                        <?php for($i=1;$i<=6;$i++): ?><option value="<?= $i ?>">ม.<?= $i ?></option><?php endfor; ?>
                    </select>
                </div>
            </div>

            <div id="filter-room" class="lg:col-span-8 grid grid-cols-2 gap-4 hidden">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ชั้น</label>
                    <div class="relative">
                         <i class="fas fa-chalkboard-user absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                         <select id="major-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                            <option value="">-- เลือกชั้น --</option>
                            <?php for($i=1;$i<=6;$i++): ?><option value="<?= $i ?>">ม.<?= $i ?></option><?php endfor; ?>
                         </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">ห้อง</label>
                    <div class="relative">
                         <i class="fas fa-door-open absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                         <select id="room-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                            <option value="">-- เลือกห้อง --</option>
                            <?php for($i=1;$i<=15;$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
                         </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-left border-separate border-spacing-y-2" id="group-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">ลำดับ / นักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รหัสนักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ชั้น / ห้อง / เลขที่</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนนที่ถูกหัก</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">ความสมบูรณ์คะแนน</th>
                </tr>
            </thead>
            <tbody id="group-table-body" class="font-bold text-slate-700 dark:text-slate-300">
                <!-- Loaded via JS -->
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div id="table-empty-state" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mb-6 transition-transform hover:rotate-12">
                <i class="fas fa-magnifying-glass-chart text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">กรุณาเลือกเกณฑ์การกรองข้อมูล</h3>
            <p class="text-sm text-slate-400 mt-2 font-bold italic">ระบุกลุ่มคะแนนและพารามิเตอร์เพื่อแสดงรายงาน</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const term = <?= json_encode($term) ?>;
    const pee = <?= json_encode($pee) ?>;
    let currentTab = 'all';

    const $tbody = $('#group-table-body');
    const $emptyState = $('#table-empty-state');
    const $groupSelect = $('#group-select');
    const $printBtn = $('#print-btn');

    function updateFilters() {
        $('#filter-level, #filter-class, #filter-room').addClass('hidden');
        $('.group-selector').toggle(currentTab !== 'all');
        
        if (currentTab === 'level') $('#filter-level').removeClass('hidden');
        else if (currentTab === 'class') $('#filter-class').removeClass('hidden');
        else if (currentTab === 'room') $('#filter-room').removeClass('hidden');
        
        fetchAndRender();
    }

    $('.tab-btn').on('click', function() {
        $('.tab-btn').removeClass('bg-rose-500 text-white shadow-lg shadow-rose-500/20')
                    .addClass('bg-white dark:bg-slate-900 text-slate-400 border-slate-100 dark:border-slate-800 shadow-sm');
        $(this).removeClass('bg-white dark:bg-slate-900 text-slate-400 border-slate-100 dark:border-slate-800 shadow-sm')
               .addClass('bg-rose-500 text-white shadow-lg shadow-rose-500/20');
               
        currentTab = $(this).data('type');
        updateFilters();
    });

    $('select').on('change', fetchAndRender);

    function fetchAndRender() {
        const groupVal = $groupSelect.val();
        const levelVal = $('#level-select').val();
        const classVal = $('#class-select').val();
        const majorVal = $('#major-select').val();
        const roomVal = $('#room-select').val();

        if (currentTab !== 'all' && !groupVal) {
            $tbody.empty();
            $emptyState.show();
            return;
        }

        if (currentTab === 'level' && !levelVal) return;
        if (currentTab === 'class' && !classVal) return;
        if (currentTab === 'room' && (!majorVal || !roomVal)) return;

        $tbody.empty();
        $emptyState.html(`
            <div class="flex flex-col items-center gap-4 py-10">
                <div class="w-12 h-12 border-4 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm font-bold text-slate-500 italic">กำลังรวบรวมข้อมูลตามกลุ่ม...</p>
            </div>
        `).show();

        let url = `api/get_deduct_group_tab.php?group=${groupVal}&type=${currentTab}&term=${term}&pee=${pee}`;
        if (currentTab === 'level') url += `&level=${levelVal}`;
        if (currentTab === 'class') url += `&class=${classVal}`;
        if (currentTab === 'room') url += `&major=${majorVal}&room=${roomVal}`;

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data && res.data.length > 0) {
                    $tbody.empty();
                    $emptyState.hide();
                    
                    res.data.forEach((stu, idx) => {
                        const count = parseInt(stu.behavior_count) || 0;
                        const score = 100 - count;
                        
                        let barColor = 'emerald';
                        if (score < 50) barColor = 'rose';
                        else if (score <= 70) barColor = 'amber';
                        
                        const html = `
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="ลำดับ / นักเรียน">
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-[10px] font-black italic">
                                            ${idx + 1}
                                        </div>
                                        <div class="text-[13px] font-black text-slate-800 dark:text-white">${stu.FullName}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="รหัสนักเรียน">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest italic">ID: ${stu.Stu_id}</span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ชั้น / ห้อง / เลขที่">
                                    <span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black italic">
                                        ${stu.ClassRoom} (${stu.Stu_no})
                                    </span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="คะแนนที่ถูกหัก">
                                    <span class="text-sm font-black text-rose-500 italic">${count} <span class="text-[10px] opacity-70">✂️</span></span>
                                </td>
                                <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center" data-label="ความสมบูรณ์คะแนน">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-24 bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                            <div class="bg-${barColor}-500 h-full transition-all duration-1000" style="width: ${score}%"></div>
                                        </div>
                                        <span class="text-[9px] font-black text-${barColor}-600 dark:text-${barColor}-400 italic">${score} / 100</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $tbody.append(html);
                    });
                    
                    if (typeof updateMobileLabels === 'function') updateMobileLabels();
                } else {
                    $tbody.empty();
                    $emptyState.html(`
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-500 mb-6 group-hover:rotate-12 transition-transform">
                                <i class="fas fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-white">ไม่พบข้อมูลตามเงื่อนไข</h3>
                            <p class="text-sm text-slate-400 mt-2 font-bold italic">ลองปรับเปลี่ยนตัวเลือกในการค้นหาอีกครั้ง</p>
                        </div>
                    `).show();
                }
            });
    }

    $printBtn.on('click', function() {
        if (typeof window.printReport === 'function') window.printReport();
        else window.print();
    });
});
</script>
