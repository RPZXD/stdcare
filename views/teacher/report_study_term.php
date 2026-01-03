<?php
/**
 * View: Termly Attendance Report
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
        .glass-effect { 
            box-shadow: none !important; 
            border: none !important; 
            background: white !important; 
            padding: 0 !important;
            margin: 0 !important;
        }
        body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; }
        @page { size: landscape; margin: 1cm; }
        
        /* Force table to show and fit */
        .lg\:block { display: block !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { 
            border: 1px solid #e2e8f0 !important; 
            padding: 8px !important;
            font-size: 10pt !important;
        }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-book-reader text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานเวลาเรียนรายภาคเรียน
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            สรุปสถิติการมาเรียนรายภาคเรียน/ปีการศึกษา สำหรับคุณครูที่ปรึกษา
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 no-print">
                    <div class="px-6 py-3 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-100 dark:border-blue-800 text-center">
                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest block mb-1">สถานะปัจจุบัน</span>
                        <span class="text-lg font-black text-blue-600 dark:text-blue-400">เทอม <?php echo $current_term; ?>/<?php echo $current_buddhist_year; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ภาคเรียน</label>
                <div class="relative">
                    <select name="term" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="1" <?php echo ($report_term == '1') ? 'selected' : ''; ?>>ภาคเรียนที่ 1</option>
                        <option value="2" <?php echo ($report_term == '2') ? 'selected' : ''; ?>>ภาคเรียนที่ 2</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ปีการศึกษา (พ.ศ.)</label>
                <input type="number" name="year" value="<?php echo htmlspecialchars($report_year); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
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
                    <select name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
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
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> แสดงรายงาน
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($students)) : ?>
        <!-- Main Content -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden animate-fadeIn" style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 relative z-10 no-print">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white italic">มัธยมศึกษาปีที่ <?php echo $report_class . "/" . $report_room; ?></h3>
                        <p class="text-sm text-slate-500 font-medium">ภาคเรียนที่ <?php echo $report_term; ?> ปีการศึกษา <?php echo $report_year; ?> (นักเรียน <?php echo count($students); ?> คน)</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                    <button onclick="exportToExcel('report-table', 'report_term.xls')" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-file-excel text-emerald-500"></i> ส่งออก Excel
                    </button>
                </div>
            </div>

            <!-- Print Header -->
            <div class="print-only text-center mb-10 border-b-2 border-slate-900 pb-6 uppercase tracking-widest">
                <h1 class="text-3xl font-black">โรงเรียนพิชัย</h1>
                <h2 class="text-xl font-bold mt-2 italic">รายงานสถิติการมาเรียนรายภาคเรียน</h2>
                <div class="mt-2 text-sm font-black flex justify-center gap-8">
                    <span>ชั้น ม.<?php echo $report_class . "/" . $report_room; ?></span>
                    <span>ภาคเรียนที่ <?php echo $report_term; ?>/<?php echo $report_year; ?></span>
                </div>
            </div>

            <!-- Desktop View: Table (Forced on print) -->
            <div class="hidden lg:block print:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner bg-white dark:bg-slate-900">
                <table class="w-full text-left" id="report-table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เลขที่ / ชื่อ-นามสกุล</th>
                            <?php foreach ($status_labels as $info) : ?>
                                <th class="px-4 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic"><?php echo $info['emoji'] . ' ' . $info['label']; ?></th>
                            <?php endforeach; ?>
                            <th class="px-6 py-6 text-center text-[10px] font-black text-blue-500 uppercase tracking-widest italic">รวมการมาเรียน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <?php foreach ($students as $stu) : 
                            $stu_id = $stu['Stu_id'];
                            $total = 0;
                        ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform italic">
                                            #<?php echo $stu['Stu_no']; ?>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 dark:text-white leading-tight">
                                                <?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-black italic mt-0.5 tracking-widest uppercase">ID: <?php echo $stu['Stu_id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                
                                <?php foreach ($status_labels as $key => $info) : 
                                    $count = $summary_map[$stu_id][$key] ?? 0;
                                    $total += $count;
                                    $color = $info['color'];
                                ?>
                                    <td class="px-4 py-6 text-center">
                                        <span class="text-sm font-black <?php echo $count > 0 ? "text-{$color}-600 dark:text-{$color}-400" : "text-slate-200 dark:text-slate-700"; ?>">
                                            <?php echo $count; ?>
                                        </span>
                                    </td>
                                <?php endforeach; ?>

                                <td class="px-6 py-6 text-center text-base font-black text-blue-600 dark:text-blue-400 bg-blue-50/10"><?php echo $total; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Cards -->
            <div class="lg:hidden grid grid-cols-1 gap-4 no-print">
                <?php foreach ($students as $stu) : 
                    $stu_id = $stu['Stu_id'];
                    $total = 0;
                ?>
                    <div class="glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-blue-500/20">
                                <?php echo $stu['Stu_no']; ?>
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words">
                                    <?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?>
                                </h4>
                                <p class="text-[10px] font-black text-slate-400 mt-1 italic uppercase tracking-widest">รหัส: <?php echo $stu['Stu_id']; ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3">
                            <?php foreach ($status_labels as $key => $info) : 
                                $count = $summary_map[$stu_id][$key] ?? 0;
                                $total += $count;
                                $color = $info['color'];
                            ?>
                                <div class="p-3 bg-<?php echo $color; ?>-50/30 dark:bg-<?php echo $color; ?>-900/20 rounded-2xl border border-<?php echo $color; ?>-100/30 text-center">
                                    <span class="text-xs block mb-1"><?php echo $info['emoji']; ?></span>
                                    <span class="text-xs font-black text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 underline decoration-2 decoration-<?php echo $color; ?>-200/50"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center px-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">รวมการมาเรียนทั้งหมด</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400 tabular-nums"><?php echo $total; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-[3rem] py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner animate-fadeIn">
            <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
                <i class="fas fa-search-minus text-5xl text-slate-200"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest animate-pulse italic">ไม่พบข้อมูลรายงาน</h3>
            <p class="text-slate-400 mt-2 font-medium italic">กรุณาระบุเงื่อนไขการค้นหาใหม่ หรือรอข้อมูลจากระบบครับผม</p>
        </div>
    <?php endif; ?>
</div>

<script>
    /**
     * Enhanced Export to Excel
     */
    function exportToExcel(tableId, filename) {
        const table = document.getElementById(tableId);
        const title = "รายงานเวลาเรียนรายภาคเรียน <?php echo $report_term . "/" . $report_year; ?>";
        const subtitle = "ชั้น ม.<?php echo $report_class . "/" . $report_room; ?> | โรงเรียนพิชัย";
        
        let excelHeader = `
            <table border="1">
                <tr><th colspan="8" style="font-size: 18px; height: 40px; background-color: #1e40af; color: #ffffff; vertical-align: middle;">${title}</th></tr>
                <tr><th colspan="8" style="font-size: 12px; height: 25px; background-color: #f8fafc; vertical-align: middle;">${subtitle}</th></tr>
                <tr><td colspan="8" style="height: 10px;"></td></tr>
            </table>
        `;
        
        const clonedTable = table.cloneNode(true);
        clonedTable.setAttribute('border', '1');
        clonedTable.style.borderCollapse = 'collapse';
        
        // Explicitly set styles for Excel
        clonedTable.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#f1f5f9';
            th.style.padding = '10px';
            th.style.fontWeight = 'bold';
        });
        clonedTable.querySelectorAll('td').forEach(td => {
            td.style.padding = '8px';
            td.style.verticalAlign = 'middle';
            // Ensure numbers are treated as numbers or clean strings
            td.innerText = td.innerText.trim();
        });

        const excelHTML = excelHeader + clonedTable.outerHTML;

        const template = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
                <x:Name>รายงานเทอม</x:Name>
                <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
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
