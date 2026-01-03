<?php
/**
 * Teacher Report View
 * Modern UI with Tailwind CSS & Glassmorphism
 */
ob_start();
?>

<!-- Custom Styles for Report Page -->
<style>
    .report-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.9);
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
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-in {
        animation: slideIn 0.5s ease-out forwards;
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-10 animate-slide-in">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white flex items-center gap-4">
                    <span class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-xl">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    ศูนย์รวมรายงาน
                </h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400 font-medium">เข้าถึงข้อมูลและสรุปผลรอบด้านเพื่อประสิทธิผลในการดูแลนักเรียน</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <span class="text-xs font-bold text-slate-400 block uppercase">ปีการศึกษา</span>
                    <span class="text-sm font-black text-indigo-600"><?php echo $pee; ?></span>
                </div>
                <div class="px-4 py-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <span class="text-xs font-bold text-slate-400 block uppercase">ภาคเรียนที่</span>
                    <span class="text-sm font-black text-purple-600"><?php echo $term; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Category 1: Individual Reports -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-slide-in" style="animation-delay: 0.1s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-user-tag text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">รายงานรายบุคคล</h2>
                            <p class="text-sm text-slate-500">ข้อมูลเจาะลึกนักเรียนแต่ละคน</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="report_study_single.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-amber-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-amber-500 group-hover:text-white transition-all text-amber-500">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="flex-1">
                            <div class="font-bold text-slate-700 dark:text-slate-200 uppercase text-xs tracking-wider mb-0.5">Time Attendance</div>
                            <div class="text-lg font-black text-slate-800 dark:text-white">เวลาเรียนรายบุคคล</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-amber-500 transition-colors"></i>
                    </a>

                    <a href="report_student_sdq_single.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-amber-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-amber-500 group-hover:text-white transition-all text-amber-500">
                            <i class="fas fa-brain"></i>
                        </span>
                        <div class="flex-1">
                            <div class="font-bold text-slate-700 dark:text-slate-200 uppercase text-xs tracking-wider mb-0.5">SDQ Assessment</div>
                            <div class="text-lg font-black text-slate-800 dark:text-white">ข้อมูล SDQ รายบุคคล</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-amber-500 transition-colors"></i>
                    </a>

                    <a href="report_behavior_single.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-amber-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-amber-500 group-hover:text-white transition-all text-amber-500">
                            <i class="fas fa-star"></i>
                        </span>
                        <div class="flex-1">
                            <div class="font-bold text-slate-700 dark:text-slate-200 uppercase text-xs tracking-wider mb-0.5">Student Behavior</div>
                            <div class="text-lg font-black text-slate-800 dark:text-white">คะแนนพฤติกรรมรายบุคคล</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-amber-500 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Category 2: Group/Room Reports -->
        <div class="report-card glass-effect rounded-[2.5rem] overflow-hidden border border-white/50 dark:border-slate-700/50 shadow-xl animate-slide-in" style="animation-delay: 0.2s">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="icon-box w-14 h-14 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-users-viewfinder text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">รายงานรายกลุ่ม / ห้อง</h2>
                            <p class="text-sm text-slate-500">ข้อมูลสรุปภาพรวมระดับห้องเรียน</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <!-- Item -->
                    <a href="report_study_late.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all text-blue-600">
                            <i class="fas fa-history"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายงานการมาสาย-ขาดเรียน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_class_visithome.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all text-blue-600">
                            <i class="fas fa-house-chimney-user"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายงานการเยี่ยมบ้าน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_study_day.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all text-blue-600">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">เวลาเรียนประจำวัน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_study_month.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all text-blue-600">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">เวลาเรียนประจำเดือน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_study_term.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all text-blue-600">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">สรุปเวลาเรียนประจำภาคเรียน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_study_leave.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-rose-500 group-hover:text-white transition-all text-rose-500">
                            <i class="fas fa-user-xmark"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายชื่อนักเรียนที่ไม่มาเรียน</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-rose-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>

                    <a href="report_board_parent.php" class="menu-item flex items-center gap-4 p-4 rounded-2xl border border-slate-50 dark:border-slate-700/50 hover:border-blue-200 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
                            <i class="fas fa-id-badge"></i>
                        </span>
                        <div class="flex-1">
                            <div class="text-lg font-black text-slate-800 dark:text-white">รายชื่อประธานเครือข่ายผู้ปกครอง</div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-300 group-hover:text-emerald-600 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Tips Footer -->
    <div class="mt-12 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl border border-indigo-100 dark:border-indigo-800 animate-slide-in" style="animation-delay: 0.3s">
        <div class="flex items-center gap-3 text-indigo-700 dark:text-indigo-400">
            <i class="fas fa-lightbulb text-xl"></i>
            <span class="font-bold">คำแนะนำ:</span>
            <p class="text-sm font-medium">คุณสามารถดาวน์โหลดหรือพิมพ์รายงานเหล่านี้เพื่อเป็นหลักฐานในการดำเนินการดูแลช่วยเหลือนักเรียน (SAR/System Log)</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
