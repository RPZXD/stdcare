<?php
/**
 * Sub-View: Deduct Point Report by Room (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
$term = $user->getTerm();
$pee = $user->getPee();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10 no-print">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-school"></i>
                </span>
                ประวัติการหักคะแนน <span class="text-indigo-600 italic">รายห้องเรียน</span>
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Score Deduction Report by Classroom</p>
        </div>
        
        <div class="flex gap-2 no-print">
            <button id="print-btn" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 hidden">
                <i class="fas fa-print"></i> พิมพ์ข้อมูล
            </button>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8 no-print">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกระดับชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <select id="select-class" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกห้องเรียน</label>
                <div class="relative">
                    <i class="fas fa-door-open absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <select id="select-room" disabled class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none disabled:opacity-50">
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div id="stat-container" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 hidden animate-fadeIn no-print">
        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 px-6 py-5 rounded-[2rem] border border-indigo-100/50 dark:border-indigo-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-600/60 dark:text-indigo-400 uppercase tracking-widest italic">นักเรียนทั้งหมด</p>
                <p id="stat-total" class="text-2xl font-black text-slate-800 dark:text-white">0 คน</p>
            </div>
        </div>
        <div class="bg-rose-50/50 dark:bg-rose-900/20 px-6 py-5 rounded-[2rem] border border-rose-100/50 dark:border-rose-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-minus-circle text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-rose-600/60 dark:text-rose-400 uppercase tracking-widest italic">นักเรียนที่ถูกหักคะแนน</p>
                <p id="stat-deducted" class="text-2xl font-black text-slate-800 dark:text-white">0 คน</p>
            </div>
        </div>
        <div class="bg-emerald-50/50 dark:bg-emerald-900/20 px-6 py-5 rounded-[2rem] border border-emerald-100/50 dark:border-emerald-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-check-double text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-600/60 dark:text-emerald-400 uppercase tracking-widest italic">คะแนนพฤติกรรมปกติ</p>
                <p id="stat-normal" class="text-2xl font-black text-slate-800 dark:text-white">0 คน</p>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-left border-separate border-spacing-y-2" id="deduct-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">เลขที่ / นักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รหัสนักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ชั้น / ห้อง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนนที่ถูกหัก</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">สถานะคะแนน</th>
                </tr>
            </thead>
            <tbody id="deduct-table-body" class="font-bold text-slate-700 dark:text-slate-300">
                <!-- Loaded via JS -->
            </tbody>
        </table>
        
        <!-- Empty/Loading State -->
        <div id="table-empty-state" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mb-6 transition-transform hover:rotate-12">
                <i class="fas fa-layer-group text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">กรุณาเลือกระดับชั้นและห้องเรียน</h3>
            <p class="text-sm text-slate-400 mt-2 font-bold italic">เลือกพารามิเตอร์ด้านบนเพื่อตรวจสอบข้อมูล</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const $selectClass = $('#select-class');
    const $selectRoom = $('#select-room');
    const $tbody = $('#deduct-table-body');
    const $emptyState = $('#table-empty-state');
    const $statContainer = $('#stat-container');
    const $printBtn = $('#print-btn');
    
    const term = <?= json_encode($term) ?>;
    const pee = <?= json_encode($pee) ?>;

    // Load Classes
    fetch('api/get_classes.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                data.classes.forEach(cls => {
                    $selectClass.append(`<option value="${cls.Stu_major}">${cls.Stu_major}</option>`);
                });
            }
        });

    // Class Change
    $selectClass.on('change', function() {
        $selectRoom.html('<option value="">-- เลือกห้อง --</option>').prop('disabled', true);
        $tbody.empty();
        $emptyState.show();
        $statContainer.addClass('hidden');
        $printBtn.addClass('hidden');

        if (this.value) {
            fetch('api/get_rooms.php?class=' + encodeURIComponent(this.value))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        data.rooms.forEach(room => {
                            $selectRoom.append(`<option value="${room.Stu_room}">${room.Stu_room}</option>`);
                        });
                        $selectRoom.prop('disabled', false);
                    }
                });
        }
    });

    // Room Change
    $selectRoom.on('change', function() {
        if ($selectClass.val() && this.value) {
            $tbody.empty();
            $emptyState.html(`
                <div class="flex flex-col items-center gap-4 py-10">
                    <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-slate-500 italic">กำลังดึงข้อมูลคะแนน...</p>
                </div>
            `).show();
            
            fetch(`api/get_deduct_room.php?class=${encodeURIComponent($selectClass.val())}&room=${encodeURIComponent(this.value)}&term=${term}&pee=${pee}`)
                .then(res => res.json())
                .then(data => {
                    $tbody.empty();
                    if (data.success && data.students.length > 0) {
                        $emptyState.hide();
                        $statContainer.removeClass('hidden');
                        $printBtn.removeClass('hidden');
                        
                        let totalCount = data.students.length;
                        let deductedCount = 0;
                        
                        data.students.forEach(stu => {
                            const count = parseInt(stu.behavior_count) || 0;
                            const score = 100 - count;
                            if (count > 0) deductedCount++;
                            
                            let statusBadge = '';
                            if (score < 50) {
                                statusBadge = `<div class="inline-flex items-center gap-2 px-4 py-1.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-full border border-rose-500/20 shadow-sm"><span class="text-[10px] font-black uppercase tracking-widest italic">🚨 ต่ำกว่า 50 คะแนน</span></div>`;
                            } else if (score <= 70) {
                                statusBadge = `<div class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-full border border-amber-500/20 shadow-sm"><span class="text-[10px] font-black uppercase tracking-widest italic">⚠️ 50 - 70 คะแนน</span></div>`;
                            } else if (score < 100) {
                                statusBadge = `<div class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full border border-emerald-500/20 shadow-sm"><span class="text-[10px] font-black uppercase tracking-widest italic">✅ 71 - 99 คะแนน</span></div>`;
                            } else {
                                statusBadge = `<div class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-full border border-indigo-500/20 shadow-sm"><span class="text-[10px] font-black uppercase tracking-widest italic">✨ ปกติ</span></div>`;
                            }

                            const html = `
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                    <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="เลขที่ / นักเรียน">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-indigo-500 text-[10px] font-black italic">
                                                ${stu.Stu_no}
                                            </div>
                                            <div class="text-[13px] font-black text-slate-800 dark:text-white">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="รหัสนักเรียน">
                                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest font-mono italic">ID: ${stu.Stu_id}</span>
                                    </td>
                                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ชั้น / ห้อง">
                                        <span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black italic">
                                            ม.${stu.Stu_major}/${stu.Stu_room}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="คะแนนที่ถูกหัก">
                                        <span class="text-sm font-black text-rose-500 italic">${count} <span class="text-[10px] opacity-70">✂️</span></span>
                                    </td>
                                    <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center" data-label="สถานะคะแนน">
                                        ${statusBadge}
                                    </td>
                                </tr>
                            `;
                            $tbody.append(html);
                        });
                        
                        $('#stat-total').text(totalCount + ' คน');
                        $('#stat-deducted').text(deductedCount + ' คน');
                        $('#stat-normal').text((totalCount - deductedCount) + ' คน');
                        
                        if (typeof updateMobileLabels === 'function') updateMobileLabels();
                        
                    } else {
                        $tbody.empty();
                        $emptyState.html(`
                            <div class="flex flex-col items-center justify-center py-20 text-center">
                                <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-500 mb-6 group-hover:rotate-12 transition-transform">
                                    <i class="fas fa-triangle-exclamation text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-white">ไม่พบข้อมูล</h3>
                                <p class="text-sm text-slate-400 mt-2 font-bold italic">ไม่พบประวัติการหักคะแนนในห้องที่เลือก</p>
                            </div>
                        `).show();
                        $printBtn.addClass('hidden');
                    }
                });
        }
    });

    // Integrated Print
    $printBtn.on('click', function() {
        if (typeof window.printReport === 'function') {
            window.printReport();
        } else {
            window.print();
        }
    });
});
</script>
