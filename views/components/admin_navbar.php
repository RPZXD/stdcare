<?php
/**
 * Admin Navbar Component
 * Matches Teacher Navbar UX/UI pattern with admin identity
 */

// User info is already passed from layout
$userName = $userData['Teach_name'] ?? 'ผู้ดูแลระบบ';
?>

<!-- Mobile Menu Button -->
<div class="lg:hidden fixed top-4 left-4 z-50 no-print">
    <button onclick="toggleSidebar()" class="p-3 rounded-xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl transition-all">
        <i class="fas fa-bars text-slate-700 dark:text-slate-200"></i>
    </button>
</div>

<!-- Top Navbar -->
<header class="sticky top-0 z-30 glass-effect border-b border-white/20 dark:border-slate-800 no-print">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Left: Page Title -->
        <div class="flex items-center space-x-4">
            <div class="hidden lg:block">
                <h1 class="text-xl font-black text-slate-800 dark:text-white"><?php echo $pageTitle ?? 'Admin Panel'; ?></h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <?php 
                    $thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                    echo date('j') . ' ' . $thaiMonths[(int)date('n')] . ' ' . (date('Y') + 543);
                    ?>
                </p>
            </div>
        </div>
        
        <!-- Right: User Menu & Actions -->
        <div class="flex items-center space-x-4 no-print">
            <!-- Dark Mode Toggle -->
            <button onclick="toggleDarkMode()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all group">
                <i class="fas fa-sun text-amber-500 dark:hidden group-hover:rotate-45 transition-transform"></i>
                <i class="fas fa-moon text-indigo-400 hidden dark:inline group-hover:-rotate-12 transition-transform"></i>
            </button>
            
            <!-- User Menu -->
            <div class="flex items-center space-x-3 pl-4 border-l border-slate-200 dark:border-slate-800">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-black text-slate-800 dark:text-white leading-none"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest mt-1">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shadow-lg shadow-rose-500/20 ring-2 ring-white dark:ring-slate-800">
                    <i class="fas fa-user-shield text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>
</header>
