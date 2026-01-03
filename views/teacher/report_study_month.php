<?php
/**
 * View: Monthly Attendance Report
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
    .table-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .table-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { 
            box-shadow: none !important; 
            border: none !important; 
            background: white !important; 
            padding: 0 !important;
            margin: 0 !important;
        }
        body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; }
        @page { size: landscape; margin: 0.5cm; }
        .sticky-col { position: static !important; background: white !important; }
        
        /* Table enhancements for print */
        table { border-collapse: collapse !important; width: 100% !important; table-layout: fixed !important; }
        th, td { 
            border: 1px solid #1e293b !important; 
            padding: 2px !important; 
            font-size: 8px !important; 
            line-height: 1 !important;
        }
        th { background-color: #f8fafc !important; color: #1e293b !important; }
        .bg-indigo-50\/30 { background-color: transparent !important; }
        
        /* Legends for print */
        .print-legend {
            display: flex !important;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            font-size: 8px;
            font-weight: bold;
        }
    }
    .print-only { display: none; }
    
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: inherit;
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-violet-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-calendar-alt text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานเวลาเรียนประจำเดือน
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            สรุปภาพรวมการมาเรียนของนักเรียนรายห้องเรียนตลอดทั้งเดือน
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 no-print">
                    <div class="px-6 py-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl border border-indigo-100 dark:border-indigo-800 text-center">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-lg font-black text-indigo-600 dark:text-indigo-400"><?php echo $term; ?>/<?php echo $current_buddhist_year; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">เดือน</label>
                <div class="relative">
                    <select name="month" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <?php foreach ($thai_months as $val => $name) : ?>
                            <option value="<?php echo $val; ?>" <?php echo ($report_month == $val) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ปี (พ.ศ.)</label>
                <input type="number" name="year" value="<?php echo htmlspecialchars($report_year); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
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
                    <select name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
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
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($students)) : ?>
        <!-- Report Content -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden animate-fadeIn" style="animation-delay: 0.2s" id="report-container">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 relative z-10 no-print">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-table text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white italic">ตารางมาเรียน ม.<?php echo $report_class . "/" . $report_room; ?></h3>
                        <p class="text-sm text-slate-500 font-medium"><?php echo $thai_months[$report_month] . " " . $report_year; ?></p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-indigo-500 hover:text-indigo-600 transition-all shadow-sm">
                        <i class="fas fa-print text-indigo-500"></i> พิมพ์รายงาน
                    </button>
                    <button onclick="exportToExcel('report-table', 'report_month.xls')" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-file-excel text-emerald-500"></i> ส่งออก Excel
                    </button>
                </div>
            </div>

            <!-- Print Header -->
            <div class="print-only text-center mb-4">
                <h1 class="text-xl font-black">โรงเรียนพิชัย</h1>
                <h2 class="text-base font-bold">รายงานสถิติการมาเรียนประจำเดือน <?php echo $thai_months[$report_month] . " " . $report_year; ?></h2>
                <div class="flex justify-center gap-6 mt-1 text-xs font-bold">
                    <span>ชั้นมัธยมศึกษาปีที่ <?php echo $report_class . "/" . $report_room; ?></span>
                    <span>เทอม/ปีการศึกษา: <?php echo $term; ?>/<?php echo $current_buddhist_year; ?></span>
                </div>
            </div>

            <!-- Legend Section -->
            <div class="mb-6 flex flex-wrap items-center gap-4 no-print">
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest italic mr-2">คำอธิบาย:</span>
                <?php foreach ($status_labels_legend as $key => $info) : ?>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700 shadow-sm">
                        <span class="text-sm"><?php echo $info['emoji']; ?></span>
                        <span class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-tighter"><?php echo $info['label']; ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700 shadow-sm">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">(-) ยังไม่เช็คชื่อ</span>
                </div>
            </div>

            <!-- Desktop Table View (Forced visible on print) -->
            <div class="hidden lg:block print:block overflow-x-auto table-scroll rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-inner bg-white dark:bg-slate-900">
                <table class="w-full text-left" id="report-table">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest italic sticky-col bg-slate-50/80 dark:bg-slate-900/80" style="width: 180px;">เลขที่ / ชื่อ-นามสกุล</th>
                            <?php for ($day = 1; $day <= $days_in_month; $day++) : ?>
                                <th class="px-0.5 py-3 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest italic border-l border-slate-100 dark:border-slate-800" style="width: 28px;"><?php echo $day; ?></th>
                            <?php endfor; ?>
                            <?php foreach ($status_symbols as $key => $sym): ?>
                                <th class="px-1 py-3 text-center text-[10px] font-black text-slate-800 dark:text-white bg-slate-100/50 dark:bg-slate-800/50 border-l border-slate-200 dark:border-slate-700" style="width: 35px;"><?php echo $sym; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <?php foreach ($students as $stu) : $stu_id = $stu['Stu_id']; ?>
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors group">
                                <td class="px-4 py-2 whitespace-nowrap text-xs sticky-col group-hover:bg-indigo-50/30">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] text-slate-400 font-black italic">#<?php echo $stu['Stu_no']; ?></span>
                                        <span class="font-black text-slate-800 dark:text-white text-[10px]"><?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?></span>
                                    </div>
                                </td>
                                <?php for ($day = 1; $day <= $days_in_month; $day++) : 
                                    $status = $attendance_map[$stu_id][$day] ?? null;
                                    $symbol = $status ? ($status_symbols[$status] ?? '❓') : '-';
                                    $colorClass = $status ? 'text-slate-800 dark:text-white' : 'text-slate-300 dark:text-slate-700';
                                ?>
                                    <td class="px-0.5 py-2 text-center text-[10px] border-l border-slate-50 dark:border-slate-800/50 <?php echo $colorClass; ?>"><?php echo $symbol; ?></td>
                                <?php endfor; ?>
                                
                                <?php foreach ($status_symbols as $key => $sym): 
                                    $count = $summary_map[$stu_id][$key] ?? 0;
                                    $color = $status_labels_legend[$key]['color'] ?? 'slate';
                                ?>
                                    <td class="px-1 py-2 text-center text-[11px] font-black bg-<?php echo $color; ?>-50/20 text-<?php echo $color; ?>-600 border-l border-slate-100 dark:border-slate-800"><?php echo $count; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Print Legend Footer -->
            <div class="print-only print-legend">
                <?php foreach ($status_labels_legend as $info) : ?>
                    <div class="flex items-center gap-1">
                        <span><?php echo $info['emoji']; ?></span>
                        <span>=</span>
                        <span><?php echo $info['label']; ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="flex items-center gap-1">
                    <span>(-)</span>
                    <span>=</span>
                    <span>ยังไม่เช็คชื่อ</span>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden grid grid-cols-1 gap-4 no-print">
                <?php foreach ($students as $stu) : $stu_id = $stu['Stu_id']; ?>
                    <div class="glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white text-lg font-black shadow-lg">
                                <?php echo $stu['Stu_no']; ?>
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words">
                                    <?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?>
                                </h4>
                                <p class="text-[10px] font-black text-slate-400 mt-1 italic uppercase tracking-widest">รหัสประจำตัว: <?php echo $stu['Stu_id']; ?></p>
                            </div>
                        </div>
                        
                        <!-- Summary in Mobile -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <?php foreach ($status_labels_legend as $key => $info): 
                                $count = $summary_map[$stu_id][$key] ?? 0;
                            ?>
                                <div class="p-3 bg-<?php echo $info['color']; ?>-50/50 dark:bg-<?php echo $info['color']; ?>-900/20 rounded-2xl border border-<?php echo $info['color']; ?>-100/50 text-center">
                                    <span class="text-sm block mb-1"><?php echo $info['emoji']; ?></span>
                                    <span class="text-xs font-black text-<?php echo $info['color']; ?>-600"><?php echo $count; ?> <span class="text-[8px] opacity-70">ครั้ง</span></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <details class="group/details">
                            <summary class="list-none cursor-pointer flex items-center justify-center gap-2 py-2 px-4 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs font-black text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                                <span>ดูประวัติรายวัน</span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform group-open/details:rotate-180"></i>
                            </summary>
                            <div class="mt-4 grid grid-cols-7 gap-1">
                                <?php for ($day = 1; $day <= $days_in_month; $day++) : 
                                    $status = $attendance_map[$stu_id][$day] ?? null;
                                    $symbol = $status ? ($status_symbols[$status] ?? '❓') : '-';
                                ?>
                                    <div class="aspect-square flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/30 rounded-lg border border-slate-100 dark:border-slate-700">
                                        <span class="text-[8px] text-slate-400 mb-0.5"><?php echo $day; ?></span>
                                        <span class="text-[10px]"><?php echo $symbol; ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </details>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="glass-effect rounded-[3rem] py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner animate-fadeIn">
            <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
                <i class="fas fa-calendar-times text-5xl text-slate-200"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest animate-pulse italic">ไม่พบข้อมูลเวลาเรียน</h3>
            <p class="text-slate-400 mt-2 font-medium italic">ยังไม่พบรายการเช็คชื่อสำหรับห้องเรียนนี้ในเดือนที่เลือกครับผม</p>
        </div>
    <?php endif; ?>
</div>

<script>
    /**
     * Enhanced Export to Excel
     * Adds Title, Subtitle and formatting for a professional look in Excel
     */
    function exportToExcel(tableId, filename) {
        const table = document.getElementById(tableId);
        const title = "รายงานสถิติการมาเรียนประจำเดือน <?php echo $thai_months[$report_month] . " " . $report_year; ?>";
        const subtitle = "โรงเรียนพิชัย | ชั้นมัธยมศึกษาปีที่ <?php echo $report_class . "/" . $report_room; ?> | เทอม/ปีการศึกษา: <?php echo $term; ?>/<?php echo $current_buddhist_year; ?>";
        
        // Create custom Excel Content with Header
        let excelHeader = `
            <table border="1">
                <tr><th colspan="<?php echo $days_in_month + 7; ?>" style="font-size: 20px; height: 50px; background-color: #4f46e5; color: #ffffff; vertical-align: middle;">${title}</th></tr>
                <tr><th colspan="<?php echo $days_in_month + 7; ?>" style="font-size: 14px; height: 30px; background-color: #f8fafc; vertical-align: middle;">${subtitle}</th></tr>
                <tr><td colspan="<?php echo $days_in_month + 7; ?>" style="height: 10px;"></td></tr>
            </table>
        `;
        
        // Clone and Clean Table
        const clonedTable = table.cloneNode(true);
        clonedTable.setAttribute('border', '1');
        
        // Apply inline styles to clone for Excel compatibility
        clonedTable.style.borderCollapse = 'collapse';
        clonedTable.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#f1f5f9';
            th.style.padding = '8px';
            th.style.fontWeight = 'bold';
        });
        clonedTable.querySelectorAll('td').forEach(td => {
            td.style.padding = '5px';
            td.style.verticalAlign = 'middle';
        });

        // Assemble Final HTML
        const excelHTML = excelHeader + clonedTable.outerHTML;

        const template = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <!--[if gte mso 9]>
                <xml>
                    <x:ExcelWorkbook>
                        <x:ExcelWorksheets>
                            <x:ExcelWorksheet>
                                <x:Name>รายงานการมาเรียน</x:Name>
                                <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                            </x:ExcelWorksheet>
                        </x:ExcelWorksheets>
                    </x:ExcelWorkbook>
                </xml>
                <![endif]-->
                <style>
                    br { mso-data-placement: same-cell; }
                    .sticky-col { background-color: #ffffff !important; }
                </style>
            </head>
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
