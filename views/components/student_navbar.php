<?php
/**
 * Student Navbar Component
 * Top navigation bar for student section (Matching admin style)
 */
$student = $_SESSION['student_data'] ?? [];

$imgPath = isset($student['Stu_picture']) && $student['Stu_picture'] 
    ? "https://std.phichai.ac.th/photo/{$student['Stu_picture']}" 
    : '../dist/img/default-avatar.svg';
?>

<header class="sticky top-0 z-30 glass-effect border-b border-slate-200/50 dark:border-slate-700/50 no-print">
    <div class="flex items-center justify-between px-4 py-3 md:px-6 lg:px-8">
        <!-- Left: Mobile Menu Toggle & Breadcrumb -->
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Page Title (Desktop) -->
            <div class="hidden md:block">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Student Portal</p>
                <h1 class="text-lg font-black text-slate-700 dark:text-white"><?= htmlspecialchars($pageTitle ?? 'หน้าหลัก') ?></h1>
            </div>
        </div>
        
        <!-- Right: Actions & Profile -->
        <div class="flex items-center gap-2 md:gap-4">
            <!-- Dark Mode Toggle -->
            <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-300 hover:scale-110 active:scale-95 transition-all" title="เปลี่ยนธีม">
                <i class="fas fa-moon text-lg dark:hidden"></i>
                <i class="fas fa-sun text-lg hidden dark:block text-yellow-400"></i>
            </button>
            
            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    <div class="relative">
                        <img src="<?= $imgPath ?>" alt="Avatar" class="w-9 h-9 rounded-xl border-2 border-slate-200 dark:border-slate-600 object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white dark:border-slate-800"></span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-bold text-slate-700 dark:text-white"><?= htmlspecialchars(($student['Stu_name'] ?? '') . ' ' . ($student['Stu_sur'] ?? '')) ?></p>
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">นักเรียน</p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-slate-400 hidden md:block ml-1"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="absolute right-0 top-full mt-2 w-56 origin-top-right scale-95 opacity-0 invisible group-hover:scale-100 group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="glass-effect rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">ลงชื่อเข้าใช้เป็น</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-white truncate"><?= htmlspecialchars(($student['Stu_name'] ?? '') . ' ' . ($student['Stu_sur'] ?? '')) ?></p>
                        </div>
                        <div class="p-2">
                            <a href="std_information.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-sm transition-all">
                                <i class="fas fa-user w-5 text-center text-blue-500"></i> ข้อมูลส่วนตัว
                            </a>
                            <a href="std_behavior.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-sm transition-all">
                                <i class="fas fa-star w-5 text-center text-amber-500"></i> คะแนนพฤติกรรม
                            </a>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-700 p-2">
                            <a href="../logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-all">
                                <i class="fas fa-sign-out-alt w-5 text-center"></i> ออกจากระบบ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
