<?php
/**
 * View: Report Summary (Officer)
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

    /* Custom Styles for Report Menu Cards */
    .report-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
    }
    .icon-box {
        transition: transform 0.5s ease;
    }
    .report-card:hover .icon-box {
        transform: rotate(12deg) scale(1.1);
    }
    .menu-item {
        transition: all 0.3s ease;
    }
    .menu-item:hover {
        padding-left: 1.5rem;
        background: rgba(99, 102, 241, 0.05);
    }

    /* Mobile Table to Card Transformation */
    @media (max-width: 768px) {
        .report-content table, 
        .report-content thead, 
        .report-content tbody, 
        .report-content th, 
        .report-content td, 
        .report-content tr { 
            display: block; 
        }
        .report-content thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        .report-content tr { 
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 2rem;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .report-content td { 
            border: none;
            position: relative;
            padding-left: 45%; 
            padding-bottom: 0.75rem;
            text-align: right;
            font-size: 0.875rem;
        }
        .report-content td:before { 
            position: absolute;
            top: 0;
            left: 1rem;
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.65rem;
            color: #64748b;
            content: attr(data-label);
        }
        .report-content td:last-child {
            padding-bottom: 0;
        }
    }

    /* Printing Enhancements */
    @media print {
        @page { size: A4 portrait; margin: .5cm; }
        body { background: white !important; font-size: 11pt; color: #000 !important; }
        .no-print, 
        .tab-transition, 
        button, 
        form, 
        #lateForm,
        .no-print-essential { 
            display: none !important; 
        }
        
        .glass-effect {
            background: transparent !important;
            backdrop-filter: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .max-w-\[1600px\] {
            max-width: none !important;
            padding: 0 !important;
        }

        /* Force Table View and reset card styles */
        .report-content table { 
            display: table !important; 
            width: 100% !important; 
            border-collapse: collapse !important;
            margin-top: 0.5cm !important;
            page-break-inside: auto;
        }
        .report-content thead { 
            display: table-header-group !important; 
            background: #f8fafc !important;
            -webkit-print-color-adjust: exact;
        }
        .report-content tr { 
            display: table-row !important; 
            background: transparent !important; 
            margin: 0 !important; 
            box-shadow: none !important;
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .report-content th {
            display: table-cell !important;
            background: #f1f5f9 !important;
            color: #000 !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            font-size: 9pt !important;
            border: 1px solid #cbd5e1 !important;
            padding: 10px 5px !important;
            -webkit-print-color-adjust: exact;
        }
        .report-content td { 
            display: table-cell !important; 
            padding: 8px 5px !important; 
            text-align: inherit !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 10pt !important;
            background: transparent !important;
        }
        .report-content td:before { display: none !important; }
        
        /* Specific Fixes for Report Late */
        .report-content td div { display: inline-flex !important; }
        .report-content td .rounded-lg, 
        .report-content td .rounded-full { 
            border-radius: 0 !important; 
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .report-content td .text-indigo-600,
        .report-content td .text-rose-600,
        .report-content td .text-amber-600 {
            color: #000 !important;
            font-weight: bold !important;
        }

        /* Print Header - Visible only when printing */
        #print-header { 
            display: block !important; 
            text-align: center;
            margin-bottom: 1cm;
            border-bottom: 2px solid #333;
            padding-bottom: 0.5cm;
        }

        .report-content .shadow-sm, 
        .report-content .shadow-lg, 
        .report-content .shadow-2xl {
            box-shadow: none !important;
        }

        .report-content .rounded-2xl,
        .report-content .rounded-3xl,
        .report-content .rounded-[2rem],
        .report-content .rounded-[2.5rem] {
            border-radius: 0 !important;
        }
    }
    
    #print-header { display: none; }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Print Header (Hidden on screen) -->
    <div id="print-header">
        <h1 class="text-2xl font-bold uppercase tracking-widest"><?php echo $global['nameschool']; ?></h1>
        <h2 class="text-xl font-bold mt-2">รายงานสรุปข้อมูลนักเรียน: <span id="print-report-name"><?php echo $pageTitle; ?></span></h2>
        <p class="text-sm italic mt-2">พิมพ์โดย: <?php echo $userData['Teach_name']; ?> | วันที่: <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn no-print">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-rose-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-rose-600 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-chart-pie text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-none">
                            <span class="text-indigo-600 italic">รายงานสรุป</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-bold mt-2 uppercase tracking-widest text-[11px] italic">
                            Statistics & Comprehensive Reports Center
                        </p>
                    </div>
                </div>

                <!-- Optional: placeholder for tab-specific actions if needed -->
                <div></div>
            </div>
        </div>
    </div>

    <?php if (empty($tab)): ?>
    <!-- Visual Report Menu Dashboard (similar to views/teacher/report.php but for officer) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Card 1: พฤติกรรมและการมาเรียน -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-fadeIn" style="animation-delay: 0.1s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-user-clock text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">พฤติกรรมและการมาเรียน</h2>
                            <p class="text-sm text-slate-500">ข้อมูลสรุปเวลาเรียนและคะแนนพฤติกรรม</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="report.php?tab=late" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-indigo-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-indigo-500 group-hover:text-white transition-all text-indigo-500">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">เวลาเรียนและการมาสาย</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                    </a>

                    <a href="report.php?tab=deduct-room" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-indigo-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-indigo-500 group-hover:text-white transition-all text-indigo-500">
                            <i class="fas fa-school"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">คะแนนพฤติกรรม (รายห้อง)</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                    </a>

                    <a href="report.php?tab=deduct-group" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-indigo-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-indigo-500 group-hover:text-white transition-all text-indigo-500">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">คะแนนพฤติกรรม (กลุ่ม)</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card 2: การเยี่ยมบ้านและผู้ปกครอง -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-fadeIn" style="animation-delay: 0.2s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">เยี่ยมบ้านและผู้ปกครอง</h2>
                            <p class="text-sm text-slate-500">ข้อมูลสรุปการเยี่ยมบ้านและเครือข่ายผู้ปกครอง</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="report.php?tab=homevisit" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-emerald-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
                            <i class="fas fa-home"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายงานสรุปการเยี่ยมบ้าน</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                    </a>

                    <a href="report_class_visithome.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-emerald-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
                            <i class="fas fa-house-chimney-user"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายงานการเยี่ยมบ้านรายห้อง</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                    </a>

                    <a href="report_student_visithome.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-emerald-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
                            <i class="fas fa-house-user"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายงานการเยี่ยมบ้านรายบุคคล</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                    </a>

                    <a href="report.php?tab=parent-leader" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-emerald-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">ประธานเครือข่ายผู้ปกครอง</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card 3: การประเมินและคัดกรอง (SDQ, EQ, 11 ด้าน) -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-fadeIn" style="animation-delay: 0.3s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-brain text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">การประเมินและคัดกรอง</h2>
                            <p class="text-sm text-slate-500">ข้อมูลสรุป SDQ, EQ และการคัดกรอง 11 ด้าน</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- SDQ Section -->
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ประเมินจุดแข็งและจุดอ่อน (SDQ)</div>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="report.php?tab=sdq_room" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายห้อง</a>
                            <a href="report.php?tab=sdq_class" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายชั้น</a>
                            <a href="report.php?tab=sdq_school" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">ภาพรวมโรงเรียน</a>
                        </div>
                    </div>

                    <!-- EQ Section -->
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ความฉลาดทางอารมณ์ (EQ)</div>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="report.php?tab=eq_room" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายห้อง</a>
                            <a href="report.php?tab=eq_class" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายชั้น</a>
                            <a href="report.php?tab=eq_school" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">ภาพรวมโรงเรียน</a>
                        </div>
                    </div>

                    <!-- 11 Areas Screening Section -->
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">การคัดกรอง 11 ด้าน</div>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="report.php?tab=screen11_room" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายห้อง</a>
                            <a href="report.php?tab=screen11_class" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">รายชั้น</a>
                            <a href="report.php?tab=screen11_school" class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-400 hover:bg-amber-500/5 text-center text-xs font-black text-slate-700 dark:text-slate-300 transition-all">ภาพรวมโรงเรียน</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: โครงการห้องเรียนสีขาว -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-fadeIn" style="animation-delay: 0.4s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-slate-100 dark:bg-slate-900 text-slate-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">โครงการห้องเรียนสีขาว</h2>
                            <p class="text-sm text-slate-500">ข้อมูลโครงสร้างและผลการประเมินห้องเรียนสีขาว</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="report.php?tab=whiteclass" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-slate-400 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-slate-700 group-hover:text-white transition-all text-slate-600">
                            <i class="fas fa-clipboard-list"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">สรุปผลการคัดกรองห้องเรียนสีขาว</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-slate-600 transition-colors"></i>
                    </a>

                    <a href="report.php?tab=whiteclass-list" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-slate-400 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-slate-700 group-hover:text-white transition-all text-slate-600">
                            <i class="fas fa-address-book"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายชื่อคณะกรรมการห้องเรียนสีขาว</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-slate-600 transition-colors"></i>
                    </a>

                    <a href="report.php?tab=whiteclass-structure" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-slate-400 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-slate-700 group-hover:text-white transition-all text-slate-600">
                            <i class="fas fa-sitemap"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">โครงสร้างคณะกรรมการห้องเรียนสีขาว</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-slate-600 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Tips Footer -->
    <div class="mt-12 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl border border-indigo-100 dark:border-indigo-800 animate-fadeIn" style="animation-delay: 0.5s">
        <div class="flex items-center gap-3 text-indigo-700 dark:text-indigo-400">
            <i class="fas fa-lightbulb text-xl"></i>
            <span class="font-bold">คำแนะนำ:</span>
            <p class="text-sm font-medium">คุณสามารถคลิกเลือกประเภทรายงานต่างๆ เพื่อดูรายละเอียดและดาวน์โหลด/สั่งพิมพ์ได้ทันที</p>
        </div>
    </div>

    <?php else: ?>
    <!-- Navigation Tabs -->
    <div class="mb-10 animate-fadeIn relative z-40" style="animation-delay: 0.1s">
        <div class="flex flex-wrap items-center gap-3 no-print">
            <a href="report.php" class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border border-slate-200 dark:border-slate-700 transition-all">
                <i class="fas fa-arrow-left"></i> กลับหน้าเมนู
            </a>
            <?php foreach ($mainTabs as [$key, $label, $color]): 
                $isActive = $tab === $key;
                $colors = [
                    'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50'],
                    'emerald' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-50'],
                    'rose' => ['bg' => 'bg-rose-500', 'text' => 'text-rose-600', 'light' => 'bg-rose-50'],
                    'pink' => ['bg' => 'bg-pink-500', 'text' => 'text-pink-600', 'light' => 'bg-pink-50'],
                ];
                $c = $colors[$color] ?? $colors['indigo'];
            ?>
                <a href="?tab=<?php echo $key; ?>" 
                   class="tab-transition px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3 border shadow-sm
                   <?php echo $isActive 
                        ? "{$c['bg']} text-white border-transparent shadow-lg shadow-{$color}-500/20 scale-105" 
                        : "bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-{$color}-500" ?>">
                    <?php echo $label; ?>
                    <?php if($isActive): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-50"></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

                <!-- More Tabs Dropdown -->
                <div class="relative no-print" id="more-tabs-dropdown">
                    <button id="more-tabs-btn" class="px-6 py-3.5 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-800 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        รายงานเพิ่มเติม
                        <i class="fas fa-chevron-down text-[10px] transition-transform" id="more-tabs-icon"></i>
                    </button>
                    <div id="more-tabs-menu" class="absolute left-0 mt-3 w-72 bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-800 p-3 z-[100] hidden">
                        <div class="grid grid-cols-1 gap-1 max-h-[60vh] overflow-y-auto">
                            <?php foreach ($moreTabs as [$key, $label, $color]): 
                                $isActive = $tab === $key;
                            ?>
                                <a href="?tab=<?php echo $key; ?>" 
                                   class="px-4 py-3 rounded-xl font-bold text-[13px] flex items-center justify-between transition-all
                                   <?php echo $isActive 
                                        ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" 
                                        : "text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-500" ?>">
                                    <?php echo $label; ?>
                                    <?php if($isActive): ?>
                                        <i class="fas fa-check-circle text-xs"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <!-- Report Content Container -->
    <div class="animate-fadeIn relative z-10" style="animation-delay: 0.2s">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-white/50 dark:border-slate-700/50 report-content">
            <?php
            // Include target report file
            if (isset($tabFiles[$tab]) && file_exists(__DIR__ . '/../../officer/' . $tabFiles[$tab])) {
                include(__DIR__ . '/../../officer/' . $tabFiles[$tab]);
            } else {
                echo '
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mb-6">
                        <i class="fas fa-folder-open text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">ไม่พบรายงานที่เลือก</h3>
                    <p class="text-sm text-slate-400 mt-2 font-bold italic">โปรดเลือกประเภทรายงานจากรายการด้านบน</p>
                </div>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function(){
    // Helper to extract clean labels from headers
    function updateMobileLabels() {
        $('.report-content table:not(.no-card-mobile)').each(function() {
            const $table = $(this);
            const $thead = $table.find('thead');
            if ($thead.length === 0) return;

            // Simple column mapping for card labels
            const lastHeaderRow = $thead.find('tr').last();
            const labels = [];
            lastHeaderRow.find('th').each(function() {
                labels.push($(this).text().trim());
            });

            $table.find('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    if (labels[index]) {
                        $(this).attr('data-label', labels[index]);
                    }
                });
            });
        });
    }

    // Custom Print Function
    window.printReport = function() {
        // Try to find the title of the current active tab or report
        const activeTab = $('a.bg-indigo-500').first().text().trim() || 
                         $('.report-content h2').first().text().trim() || 
                         'รายงานสรุป';
        $('#print-report-name').text(activeTab);
        window.print();
    };

    // Replace the button trigger
    $('.no-print button[onclick="window.print()"]').attr('onclick', 'printReport()');

    // Run on load and after AJAX content is updated
    updateMobileLabels();
    
    // Monitor for changes in report-content
    const observer = new MutationObserver(updateMobileLabels);
    const targetNode = document.querySelector('.report-content');
    if (targetNode) observer.observe(targetNode, { childList: true, subtree: true });

    // Dropdown Toggle for More Tabs
    const $dropdownBtn = $('#more-tabs-btn');
    const $dropdownMenu = $('#more-tabs-menu');
    const $dropdownIcon = $('#more-tabs-icon');

    $dropdownBtn.on('click', function(e) {
        e.stopPropagation();
        const isHidden = $dropdownMenu.hasClass('hidden');
        if (isHidden) {
            $dropdownMenu.removeClass('hidden').addClass('animate-fadeIn');
            $dropdownIcon.addClass('rotate-180');
        } else {
            $dropdownMenu.addClass('hidden').removeClass('animate-fadeIn');
            $dropdownIcon.removeClass('rotate-180');
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#more-tabs-dropdown').length) {
            $dropdownMenu.addClass('hidden');
            $dropdownIcon.removeClass('rotate-180');
        }
    });
});
</script>


<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
