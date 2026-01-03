<?php
/**
 * View: Student Leave/Absent Report
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Design
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.05);
    }
    
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #1e293b !important; background: white !important; }
        body { background: white !important; }
        .content-wrapper { margin: 0 !important; }
        @page { size: portrait; margin: 1cm; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #e2e8f0 !important; padding: 10px !important; color: black !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-rose-500 to-orange-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-user-clock text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายชื่อนักเรียนที่ไม่มาเรียน
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            ติดตามความเคลื่อนไหว และสาเหตุการขาดลาของนักเรียนแบบรายวัน
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 no-print">
                    <div class="px-6 py-3 bg-rose-50 dark:bg-rose-900/30 rounded-2xl border border-rose-100 dark:border-rose-800 text-center">
                        <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest block mb-1">พบทั้งหมด</span>
                        <span class="text-2xl font-black text-rose-600 dark:text-rose-400"><?php echo count($absent_students); ?> <span class="text-sm">คน</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">วันที่ระบุ</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($report_date); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 dark:focus:ring-rose-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 dark:focus:ring-rose-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="all" <?php echo ($report_class === 'all') ? 'selected' : ''; ?>>ทั้งหมด (ม.1 - ม.6)</option>
                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo ($report_class == $i) ? 'selected' : ''; ?>>มัธยมศึกษาปีที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้องเรียน</label>
                <div class="relative">
                    <select name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 dark:focus:ring-rose-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="all" <?php echo ($report_room === 'all') ? 'selected' : ''; ?>>ทั้งหมด (ทุกห้อง)</option>
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo ($report_room == $i) ? 'selected' : ''; ?>>ห้องเรียนที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-rose-600 to-orange-600 hover:from-rose-700 hover:to-orange-700 text-white font-black rounded-2xl shadow-lg shadow-rose-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> ค้นหาข้อมูล
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($absent_students)) : ?>
        <!-- Report Content -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden animate-fadeIn" style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 relative z-10 no-print">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white italic">บัญชีรายชื่อนักเรียน</h3>
                        <p class="text-sm text-slate-500 font-medium tracking-tight">ประจำวันที่ <?php echo thaiDateShort($report_date); ?></p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-rose-500 hover:text-rose-600 transition-all shadow-sm">
                        <i class="fas fa-print"></i> พิมพ์ใบรายงาน
                    </button>
                    <button onclick="exportToExcel('absent-table', 'absent_list.xls')" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-file-excel text-emerald-500"></i> บันทึก Excel
                    </button>
                </div>
            </div>

            <!-- Print Header -->
            <div class="print-only text-center mb-8 pb-6 border-b-2 border-slate-900">
                <h1 class="text-3xl font-black">โรงเรียนพิชัยรัตนาคาร</h1>
                <h2 class="text-xl font-bold mt-1">ใบรายงานรายชื่อนักเรียนที่ไม่มาเรียน</h2>
                <div class="mt-2 text-sm font-bold flex justify-center gap-10 italic">
                    <span>ประจำวันที่ <?php echo thaiDateShort($report_date); ?></span>
                    <span>ระดับชั้น: <?php echo ($report_class === 'all' ? 'ทุกระดับชั้น' : "ม. $report_class"); ?></span>
                </div>
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden lg:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner bg-white dark:bg-slate-900">
                <table class="w-full text-left" id="absent-table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชั้น/ห้อง</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เลขที่</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชื่อ - นามสกุล</th>
                            <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">สถานะ</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">หมายเหตุ/สาเหตุ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <?php foreach ($absent_students as $s) : ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-black italic tracking-tighter"><?php echo $s['Stu_major'] . '/' . $s['Stu_room']; ?></span>
                                </td>
                                <td class="px-6 py-6 text-sm tabular-nums"><?php echo $s['Stu_no']; ?></td>
                                <td class="px-8 py-6">
                                    <div class="font-black text-slate-800 dark:text-white leading-tight">
                                        <?php echo htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']); ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-black italic mt-0.5 tracking-widest uppercase truncate max-w-[150px]">ID: <?php echo $s['Stu_id']; ?></div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-<?php echo $s['status_color']; ?>-50/50 dark:bg-<?php echo $s['status_color']; ?>-900/20 rounded-full border border-<?php echo $s['status_color']; ?>-100/50">
                                        <span class="text-sm"><?php echo $s['display_status']['emoji']; ?></span>
                                        <span class="text-xs font-black text-<?php echo $s['status_color']; ?>-600 dark:text-<?php echo $s['status_color']; ?>-400 uppercase tracking-tighter"><?php echo $s['display_status']['label']; ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-medium italic text-slate-500">
                                    <?php echo htmlspecialchars($s['reason'] ?? '-'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Cards -->
            <div class="lg:hidden grid grid-cols-1 gap-4 no-print">
                <?php foreach ($absent_students as $s) : ?>
                    <div class="glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group">
                        <!-- Top Indicator -->
                        <div class="absolute top-0 right-0 mt-4 mr-4 px-3 py-1 bg-<?php echo $s['status_color']; ?>-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg shadow-<?php echo $s['status_color']; ?>-500/25">
                            <?php echo $s['display_status']['label']; ?>
                        </div>

                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-2xl flex items-center justify-center text-slate-600 dark:text-slate-300 text-xl font-black shadow-inner italic">
                                #<?php echo $s['Stu_no']; ?>
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words pr-12">
                                    <?php echo htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']); ?>
                                </h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-2 py-0.5 bg-slate-50 dark:bg-slate-900/50 rounded-lg text-[10px] font-black text-slate-400 border border-slate-100 dark:border-slate-800 italic">ม.<?php echo $s['Stu_major'] . '/' . $s['Stu_room']; ?></span>
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">ID: <?php echo $s['Stu_id']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-800">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 italic">หมายเหตุ/สาเหตุการขาดลา:</span>
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic">
                                <i class="fas fa-quote-left text-[10px] mr-2 opacity-30"></i>
                                <?php echo htmlspecialchars($s['reason'] ?? 'ไม่มีระบุข้อมูลเพิ่มเติม'); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-[3rem] py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner animate-fadeIn">
            <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
                <i class="fas fa-user-check text-5xl text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest italic">นักเรียนมาเรียนครบทุกคน</h3>
            <p class="text-slate-400 mt-2 font-medium italic tracking-tight">ยินดีด้วยครับ! วันนี้ไม่มีนักเรียนขาดเรียนในเงื่อนไขที่คุณเลือกครับผม ✨</p>
        </div>
    <?php endif; ?>
</div>

<script>
    /**
     * Enhanced Export to Excel
     */
    function exportToExcel(tableId, filename) {
        const table = document.getElementById(tableId);
        const title = "บัญชีรายชื่อนักเรียนที่ไม่มาเรียน วันที่ <?php echo thaiDateShort($report_date); ?>";
        const subtitle = "ระดับชั้น: <?php echo ($report_class === 'all' ? 'ทุกระดับชั้น' : "ม. $report_class"); ?> | โรงเรียนพิชัยรัตนาคาร";
        
        let excelHeader = `
            <table border="1">
                <tr><th colspan="5" style="font-size: 20px; height: 50px; background-color: #e11d48; color: #ffffff; vertical-align: middle;">${title}</th></tr>
                <tr><th colspan="5" style="font-size: 14px; height: 30px; background-color: #f8fafc; vertical-align: middle;">${subtitle}</th></tr>
                <tr><td colspan="5" style="height: 10px;"></td></tr>
            </table>
        `;
        
        const clonedTable = table.cloneNode(true);
        clonedTable.setAttribute('border', '1');
        clonedTable.style.borderCollapse = 'collapse';
        clonedTable.querySelectorAll('th').forEach(th => { th.style.backgroundColor = '#f1f5f9'; th.style.padding = '8px'; th.style.fontWeight = 'bold'; });
        clonedTable.querySelectorAll('td').forEach(td => { td.style.padding = '5px'; td.style.verticalAlign = 'middle'; });

        const excelHTML = excelHeader + clonedTable.outerHTML;

        const template = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head><meta charset="UTF-8"></head>
            <body>${excelHTML}</body>
            </html>`;
        
        const blob = new Blob([template], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
