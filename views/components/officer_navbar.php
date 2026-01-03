<?php
/**
 * Officer Navbar Component
 * MVC Pattern - Top navigation bar for officer pages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load config
$configPath = __DIR__ . '/../../config.json';
$config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
$global = $config['global'] ?? ['pageTitle' => 'ระบบเจ้าหน้าที่'];

// Get user data
$officerData = $_SESSION['officer_data'] ?? $userData ?? [];
$userName = $officerData['Teach_name'] ?? 'เจ้าหน้าที่';
$userRole = 'เจ้าหน้าที่ระบบ';
?>

<!-- Mobile Menu Button (Floating) -->
<div class="lg:hidden fixed top-4 left-4 z-50 no-print">
    <button onclick="toggleSidebar()" class="p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-slate-100 dark:border-slate-700 hover:scale-105 active:scale-95 transition-all group">
        <i class="fas fa-bars-staggered text-blue-600 dark:text-blue-400 group-hover:rotate-12 transition-transform"></i>
    </button>
</div>

<!-- Top Navbar -->
<header class="sticky top-0 z-30 glass border-b border-white/20 dark:border-slate-800/50">
    <div class="flex items-center justify-between px-6 md:px-10 py-4">
        <!-- Left: Dashboard Info -->
        <div class="flex items-center space-x-5">
            <div class="hidden lg:flex items-center space-x-4">
                <div class="w-1.5 h-10 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 dark:text-white tracking-tight uppercase italic">
                        <?php echo $pageTitle ?? 'Management Dashboard'; ?>
                    </h1>
                    <div class="flex items-center gap-3 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-tighter">
                            <i class="fas fa-circle text-[6px] mr-1.5 animate-pulse"></i> System Active
                        </span>
                        <p class="text-[11px] font-bold text-slate-400">
                            <?php 
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            echo date('j') . ' ' . $thaiMonths[(int)date('n')] . ' ' . (date('Y') + 543);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right: Actions & Profile -->
        <div class="flex items-center space-x-4 no-print">


            <!-- Dark Mode Toggle -->
            <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 transition-all flex items-center justify-center group shadow-sm">
                <i class="fas fa-sun text-amber-500 dark:hidden group-hover:rotate-45 transition-transform"></i>
                <i class="fas fa-moon text-blue-400 hidden dark:inline group-hover:-rotate-12 transition-transform text-lg"></i>
            </button>
            
            <!-- Quick Profile -->
            <div class="flex items-center space-x-3 pl-4 border-l border-slate-200 dark:border-slate-700">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-black text-slate-800 dark:text-white"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-[10px] font-bold text-blue-500 dark:text-blue-400 italic">Officer Access</p>
                </div>
                <div class="relative group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-shield text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
