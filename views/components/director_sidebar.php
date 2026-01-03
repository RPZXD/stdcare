<?php
/**
 * Component: Director Sidebar
 * Modern UI with Tailwind CSS & Responsive Design
 */
$sidebarActive = $activePage ?? 'dashboard';

// Menu mapping
$menuItems = [
    ['href' => 'index.php', 'icon' => 'fa-home', 'text' => 'หน้าหลัก', 'key' => 'dashboard'],
    ['href' => 'data_student.php', 'icon' => 'fa-user-graduate', 'text' => 'ข้อมูลนักเรียน', 'key' => 'student'],
    ['href' => 'data_teacher.php', 'icon' => 'fa-chalkboard-teacher', 'text' => 'ครูและบุคลากร', 'key' => 'teacher'],
    ['href' => 'data_parent.php', 'icon' => 'fa-users', 'text' => 'ข้อมูลผู้ปกครอง', 'key' => 'parent'],
    ['href' => 'data_behavior.php', 'icon' => 'fa-frown', 'text' => 'หักคะแนนพฤติกรรม', 'key' => 'behavior'],
    ['href' => 'report.php', 'icon' => 'fa-file-alt', 'text' => 'รายงานข้อมูล', 'key' => 'report'],
    ['href' => 'statistics.php', 'icon' => 'fa-chart-bar', 'text' => 'กราฟสถิติ', 'key' => 'stats'],
];
?>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar Container -->
<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-slate-900 border-r border-indigo-100 dark:border-slate-800 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
    
    <!-- Sidebar Header -->
    <div class="h-24 flex items-center justify-center border-b border-indigo-50 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-crown text-xl"></i>
            </div>
            <div>
                <span class="text-lg font-black text-slate-800 dark:text-white block h-5">Director</span>
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic">Management</span>
            </div>
        </div>
    </div>

    <!-- User Profile Summary (Sidebar) -->
    <div class="px-6 py-8 border-b border-indigo-50 dark:border-slate-800">
        <div class="flex flex-col items-center">
            <div class="relative group">
                <div class="absolute inset-0 bg-indigo-500 rounded-full blur-lg opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <?php 
                $avatarUrl = (!empty($userData['Teach_photo'])) 
                    ? '../teacher/uploads/phototeach/' . $userData['Teach_photo'] 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($userData['Teach_name']) . '&background=6366f1&color=fff';
                ?>
                <img src="<?php echo $avatarUrl; ?>" alt="Avatar" class="w-20 h-20 rounded-full border-4 border-white dark:border-slate-800 shadow-xl relative object-cover">
            </div>
            <div class="mt-4 text-center">
                <h4 class="font-black text-slate-800 dark:text-white text-sm"><?php echo htmlspecialchars($userData['Teach_name'] ?? 'ผู้บริหาร'); ?></h4>
                <div class="mt-1 flex items-center justify-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">ผู้บริหารสถานศึกษา</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-280px)]">
        <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 italic">เมนูหลัก</p>
        
        <?php foreach ($menuItems as $item): 
            $isActive = ($sidebarActive === $item['key']);
        ?>
        <a href="<?php echo $item['href']; ?>" class="sidebar-item flex items-center gap-4 px-4 py-3.5 rounded-2xl group transition-all <?php echo $isActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:bg-indigo-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors <?php echo $isActive ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-white dark:group-hover:bg-slate-700'; ?>">
                <i class="fas <?php echo $item['icon']; ?> <?php echo $isActive ? '' : 'text-slate-400 group-hover:text-indigo-500'; ?>"></i>
            </div>
            <span class="font-bold text-sm"><?php echo $item['text']; ?></span>
            <?php if ($isActive): ?>
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>

        <!-- Logout Special Item -->
        <hr class="my-6 border-indigo-50 dark:border-slate-800">
        <a href="../logout.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center group-hover:bg-white dark:group-hover:bg-rose-800 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span class="font-bold text-sm">ออกจากระบบ</span>
        </a>
    </nav>
</aside>
