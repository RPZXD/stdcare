<?php
/**
 * Component: Director Navbar
 * Modern UI with Tailwind CSS & Responsive Design
 */
?>
<header class="h-20 flex items-center justify-between px-6 md:px-10 border-b border-indigo-50 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 transition-all duration-300">
    
    <!-- Left: Mobile Toggle & Breadcrumbs -->
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="hidden sm:flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400 opacity-50"><i class="fas fa-crown"></i> Director</span>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 capitalize"><?php echo $activePage ?? 'Dashboard'; ?></span>
        </div>
    </div>

    <!-- Right: User Actions -->
    <div class="flex items-center gap-4">
        
        <!-- Theme Toggle -->
        <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-amber-50 dark:hover:bg-slate-700 transition-all group">
            <i class="fas fa-sun dark:hidden group-hover:text-amber-500 transition-colors"></i>
            <i class="fas fa-moon hidden dark:block group-hover:text-indigo-400 transition-colors"></i>
        </button>

        <!-- Notification Placeholder -->
        <div class="relative hidden sm:block">
            <button class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-sky-50 dark:hover:bg-slate-700 transition-all group">
                <i class="fas fa-bell group-hover:text-sky-500 transition-colors"></i>
                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></span>
            </button>
        </div>

        <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-800 mx-2 hidden md:block"></div>

        <!-- User Dropdown (Simplified for layout) -->
        <div class="flex items-center gap-3 pl-2 grayscale hover:grayscale-0 transition-all cursor-pointer">
            <div class="hidden md:block text-right">
                <span class="text-xs font-black text-slate-800 dark:text-white block"><?php echo htmlspecialchars($userData['Teach_name'] ?? 'ผู้บริหาร'); ?></span>
                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest italic">Online</span>
            </div>
            <img src="<?php echo $avatarUrl; ?>" alt="User" class="w-10 h-10 rounded-xl object-cover border-2 border-white dark:border-slate-800 shadow-md">
        </div>
    </div>
</header>
