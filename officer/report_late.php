<?php
/**
 * Sub-View: Late Report (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
?>
<div class="animate-fadeIn">
    <!-- Header & Search Form -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10 no-print">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-clock"></i>
                </span>
                ประวัติการ <span class="text-indigo-600 italic">มาสาย</span> (สถิติ 3 ครั้งขึ้นไป)
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Student Lateness History Report</p>
        </div>
        
        <button id="print-late-btn" onclick="window.printReport ? window.printReport() : window.print()" class="px-6 py-3 bg-slate-900 dark:bg-slate-800 text-white rounded-2xl font-black text-xs shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 group no-print border border-slate-700 hidden">
            <i class="fas fa-print text-indigo-400 group-hover:text-indigo-300"></i> พิมพ์รายงานนี้
        </button>
    </div>

    <!-- Enhanced Search Box -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8 no-print">
        <form id="lateForm" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">วันที่เริ่มต้น</label>
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <input type="date" id="date_start" name="date_start" required
                        class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm">
                </div>
            </div>
            <div class="md:col-span-1 flex justify-center pb-4 hidden md:flex">
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
            <div class="md:col-span-4 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">วันที่สิ้นสุด</label>
                <div class="relative">
                    <i class="fas fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <input type="date" id="date_end" name="date_end" required
                        class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm">
                </div>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats (Loaded via JS) -->
    <div id="lateStats" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 hidden animate-fadeIn no-print">
        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 px-6 py-5 rounded-[2rem] border border-indigo-100/50 dark:border-indigo-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-users-viewfinder text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-600/60 dark:text-indigo-400 uppercase tracking-widest italic">นักเรียนที่พบ</p>
                <p id="total-late-count" class="text-2xl font-black text-slate-800 dark:text-white">0 คน</p>
            </div>
        </div>
        <div class="bg-amber-50/50 dark:bg-amber-900/20 px-6 py-5 rounded-[2rem] border border-amber-100/50 dark:border-amber-800/30 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-amber-600/60 dark:text-amber-400 uppercase tracking-widest italic">เฉลี่ยมาสาย</p>
                <p id="avg-late-count" class="text-2xl font-black text-slate-800 dark:text-white">0 ครั้ง</p>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-left border-separate border-spacing-y-2" id="lateTable">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">เลขที่ / นักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รหัสนักเรียน</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ชั้น / ห้อง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ติดต่อผู้ปกครอง</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">จำนวนครั้งที่สาย</th>
                </tr>
            </thead>
            <tbody id="lateTableBody" class="font-bold text-slate-700 dark:text-slate-300">
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div id="lateTableEmpty" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mb-6">
                <i class="fas fa-magnifying-glass text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">กรุณาเลือกช่วงวันที่</h3>
            <p class="text-sm text-slate-400 mt-2 font-bold italic">ระบุช่วงวันที่ต้องการตรวจสอบและกดปุ่มค้นหา</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Helper: Escape HTML
    function escapeHtml(str) {
        if (!str && str !== 0) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Helper: Format Thai Date Range
    function thaiDateRange(start, end) {
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        const toThai = (s) => {
            const [y, m, d] = s.split('-');
            return `${parseInt(d)} ${months[parseInt(m)]} ${parseInt(y) + 543}`;
        };
        return `ตั้งแต่วันที่ ${toThai(start)} - ${toThai(end)}`;
    }

    const $form = $('#lateForm');
    const $tbody = $('#lateTableBody');
    const $empty = $('#lateTableEmpty');
    const $stats = $('#lateStats');

    $form.on('submit', function(e) {
        e.preventDefault();
        const start = $('#date_start').val();
        const end = $('#date_end').val();

        $tbody.empty();
        $empty.removeClass('hidden').html(`
            <div class="flex flex-col items-center gap-4 py-10">
                <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm font-bold text-slate-500 italic">กำลังรวบรวมข้อมูลสถิติ...</p>
            </div>
        `);
        $stats.addClass('hidden');
        $('#print-late-btn').addClass('hidden');

        fetch(`api/fetch_checklate.php?start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(data => {
                $tbody.empty();
                if (Array.isArray(data) && data.length > 0) {
                    $empty.addClass('hidden');
                    $stats.removeClass('hidden');
                    $('#print-late-btn').removeClass('hidden');
                    
                    let totalLate = 0;
                    data.forEach((row, idx) => {
                        totalLate += row.count_late;
                        
                        // Badge color logic
                        const count = parseInt(row.count_late);
                        let badgeClasses = 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20';
                        if (count > 8) {
                            badgeClasses = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20';
                        } else if (count > 5) {
                            badgeClasses = 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20';
                        } else if (count >= 3) {
                            badgeClasses = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
                        }

                        const html = `
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                <td class="px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-l border-slate-100 dark:border-slate-800" data-label="เลขที่ / นักเรียน">
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-indigo-500 text-[10px] font-black italic">
                                            ${row.Stu_no}
                                        </div>
                                        <div class="text-[13px] font-black text-slate-800 dark:text-white">${row.name}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="รหัสนักเรียน">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest font-mono italic">ID: ${row.Stu_id}</span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800 text-center" data-label="ชั้น / ห้อง">
                                    <span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black italic">
                                        ${row.classroom}
                                    </span>
                                </td>
                                <td class="px-6 py-5 bg-white dark:bg-slate-900 shadow-sm border-y border-slate-100 dark:border-slate-800" data-label="ติดต่อผู้ปกครอง">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-slate-600 dark:text-slate-300">
                                            <i class="fas fa-phone-alt mr-1.5 text-indigo-400 text-[10px]"></i> ${row.parent_tel || 'ไม่ระบุเบอร์'}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm border-y border-r border-slate-100 dark:border-slate-800 text-center" data-label="จำนวนครั้งที่สาย">
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 ${badgeClasses} rounded-full border shadow-sm">
                                        <span class="text-sm font-black italic">${row.count_late}</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest italic opacity-70">ครั้ง</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $tbody.append(html);
                    });

                    $('#total-late-count').text(data.length + ' คน');
                    $('#avg-late-count').text((totalLate / data.length).toFixed(1) + ' ครั้ง');

                } else {
                    $stats.addClass('hidden');
                    $empty.removeClass('hidden').html(`
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-500 mb-6 group-hover:rotate-12 transition-transform">
                                <i class="fas fa-triangle-exclamation text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-white">ไม่พบข้อมูล</h3>
                            <p class="text-sm text-slate-400 mt-2 font-bold italic">ไม่พบนักเรียนที่มาสายเกิน 3 ครั้งในช่วงวันที่ระบุ</p>
                        </div>
                    `);
                }
            })
            .catch(err => {
                console.error(err);
                $empty.removeClass('hidden').html(`
                    <div class="text-rose-500 font-black italic py-10">
                        <i class="fas fa-circle-exclamation mr-2"></i> เกิดข้อผิดพลาดในการโหลดข้อมูล
                    </div>
                `);
            });
    });
});
</script>
