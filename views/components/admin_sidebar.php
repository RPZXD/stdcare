<?php
/**
 * Admin Sidebar Component
 * Modern Tailwind CSS sidebar with glassmorphism effect
 */

$activePage = $activePage ?? 'dashboard';
$menuItems = [
    ['key' => 'dashboard', 'href' => 'index.php', 'icon' => 'fa-gauge-high', 'text' => 'แดชบอร์ด'],
    ['key' => 'teacher', 'href' => 'data_teacher.php', 'icon' => 'fa-chalkboard-teacher', 'text' => 'ครูและบุคลากร'],
    ['key' => 'student', 'href' => 'data_student.php', 'icon' => 'fa-user-graduate', 'text' => 'ข้อมูลนักเรียน'],
    ['key' => 'parent', 'href' => 'data_parent.php', 'icon' => 'fa-users', 'text' => 'ข้อมูลผู้ปกครอง'],
    ['key' => 'behavior', 'href' => 'data_behavior.php', 'icon' => 'fa-frown', 'text' => 'หักคะแนนพฤติกรรม'],
    ['key' => 'settings', 'href' => 'settings.php', 'icon' => 'fa-cog', 'text' => 'การตั้งค่า'],
    ['key' => 'log', 'href' => 'log.php', 'icon' => 'fa-clipboard-list', 'text' => 'Log กิจกรรม'],
];
?>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-gradient-to-b from-rose-700 via-rose-800 to-rose-900 dark:from-slate-900 dark:via-slate-950 dark:to-black text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl overflow-hidden">
    
    <!-- Logo Area -->
    <div class="h-20 flex items-center justify-center gap-3 border-b border-white/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,...')] opacity-5"></div>
        <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shadow-inner">
            <i class="fas fa-user-shield text-xl text-white/90"></i>
        </div>
        <div>
            <span class="text-lg font-black tracking-tight text-white">ADMIN</span>
            <p class="text-[9px] font-bold text-rose-200/70 uppercase tracking-[0.2em]">Control Panel</p>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-4">
            <div class="relative">
                <img src="https://std.phichai.ac.th/teacher/uploads/phototeach/<?php echo $userData['Teach_photo'] ?? 'Admin'; ?>" 
                     alt="Avatar" class="w-14 h-14 rounded-2xl border-2 border-white/20 shadow-lg">
                <span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-400 rounded-full border-2 border-rose-800 dark:border-slate-900"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate"><?php echo htmlspecialchars($userData['Teach_name'] ?? 'ผู้ดูแลระบบ'); ?></p>
                <p class="text-[10px] font-bold text-rose-200/70 uppercase tracking-widest">Administrator</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto h-[calc(100vh-280px)]">
        <p class="px-4 py-2 text-[9px] font-black text-rose-300/50 uppercase tracking-[0.2em]">เมนูหลัก</p>
        
        <?php foreach ($menuItems as $item): 
            $isActive = ($activePage === $item['key']);
        ?>
        <a href="<?php echo $item['href']; ?>" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 group
                  <?php echo $isActive 
                      ? 'bg-white/15 text-white shadow-lg shadow-rose-900/20' 
                      : 'text-rose-100/70 hover:bg-white/10 hover:text-white'; ?>"
           onclick="closeSidebarOnMobile()">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 
                        <?php echo $isActive 
                            ? 'bg-white text-rose-600 shadow-md' 
                            : 'bg-white/10 group-hover:bg-white/20'; ?>">
                <i class="fas <?php echo $item['icon']; ?> text-sm"></i>
            </div>
            <span class="text-sm font-bold"><?php echo $item['text']; ?></span>
            <?php if ($isActive): ?>
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        
        <!-- Separator -->
        <div class="my-4 border-t border-white/10"></div>
        
        <!-- Logout -->
        <a href="../logout.php" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl text-rose-200/70 hover:bg-rose-500/20 hover:text-white transition-all duration-300 group">
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 group-hover:bg-rose-500/30 flex items-center justify-center transition-all">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </div>
            <span class="text-sm font-bold">ออกจากระบบ</span>
        </a>
    </nav>
    
    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 bg-black/20">
        <p class="text-[9px] text-center text-rose-200/50 font-bold uppercase tracking-widest">
            StdCare Admin v2.0
        </p>
    </div>
</aside>
