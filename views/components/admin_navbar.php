<?php
/**
 * Admin Navbar Component
 * Modern Tailwind CSS navbar with glassmorphism effect
 */

$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'] ?? ['nameschool' => 'โรงเรียน'];
?>

<!-- Navbar -->
<header class="sticky top-0 z-30 glass-effect border-b border-slate-200/50 dark:border-slate-700/50 no-print">
    <div class="flex items-center justify-between h-16 px-4 md:px-8">
        <!-- Left: Hamburger & Breadcrumb -->
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Button -->
            <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Breadcrumb -->
            <div class="hidden md:flex items-center gap-2 text-sm">
                <span class="text-rose-600 dark:text-rose-400 font-bold"><i class="fas fa-shield-alt mr-1"></i> Admin Panel</span>
                <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                <span class="text-slate-500 dark:text-slate-400 font-bold"><?php echo $pageTitle ?? 'Dashboard'; ?></span>
            </div>
        </div>
        
        <!-- Right: Actions -->
        <div class="flex items-center gap-2 md:gap-4">
            <!-- Theme Toggle -->
            <button onclick="toggleDarkMode()" class="w-10 h-10 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all hover:scale-110">
                <i class="fas fa-sun dark:hidden"></i>
                <i class="fas fa-moon hidden dark:inline"></i>
            </button>
            
            <!-- Notifications -->
            <button class="relative w-10 h-10 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all hover:scale-110">
                <i class="fas fa-bell"></i>
                <span class="absolute top-1 right-1 w-3 h-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 animate-pulse"></span>
            </button>
            
            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 pl-2 pr-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userData['Teach_name'] ?? 'Admin'); ?>&background=f43f5e&color=fff&size=40" 
                         alt="Avatar" class="w-9 h-9 rounded-xl border border-rose-200 dark:border-rose-700">
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($userData['Teach_name'] ?? 'Admin'); ?></p>
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest">Administrator</p>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden md:block"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2">
                    <div class="p-2">
                        <a href="settings.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            <i class="fas fa-cog text-rose-500"></i> ตั้งค่าระบบ
                        </a>
                        <a href="log.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            <i class="fas fa-history text-amber-500"></i> ประวัติกิจกรรม
                        </a>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-2"></div>
                        <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-colors">
                            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
