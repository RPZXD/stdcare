<?php
/**
 * Student Sidebar Component
 * Modern Tailwind CSS sidebar with glassmorphism effect (Blue theme)
 */

$activePage = $activePage ?? 'dashboard';
$student = $_SESSION['student_data'] ?? [];

$menuItems = [
    ['key' => 'dashboard', 'href' => 'index.php', 'icon' => 'fa-gauge-high', 'text' => 'หน้าหลัก'],
    ['key' => 'information', 'href' => 'std_information.php', 'icon' => 'fa-user-graduate', 'text' => 'ข้อมูลนักเรียน'],
];

$dataMenuItems = [
    ['key' => 'checktime', 'href' => 'std_checktime.php', 'icon' => 'fa-clock', 'text' => 'เวลาเรียน'],
    ['key' => 'roomdata', 'href' => 'std_roomdata.php', 'icon' => 'fa-door-open', 'text' => 'ข้อมูลห้องเรียน'],
    ['key' => 'behavior', 'href' => 'std_behavior.php', 'icon' => 'fa-star', 'text' => 'คะแนนพฤติกรรม'],
    ['key' => 'search', 'href' => 'std_search_data.php', 'icon' => 'fa-search', 'text' => 'ค้นหาข้อมูล'],
];

$recordMenuItems = [
    ['key' => 'visithome', 'href' => 'std_visit_home.php', 'icon' => 'fa-home', 'text' => 'บันทึกเยี่ยมบ้าน'],
    ['key' => 'sdq', 'href' => 'std_sdq.php', 'icon' => 'fa-clipboard-list', 'text' => 'บันทึก SDQ'],
    ['key' => 'eq', 'href' => 'std_eq.php', 'icon' => 'fa-heart', 'text' => 'บันทึก EQ'],
    ['key' => 'screen11', 'href' => 'std_screen11.php', 'icon' => 'fa-clipboard-check', 'text' => 'คัดกรอง 11 ด้าน'],
];

$imgPath = isset($student['Stu_picture']) && $student['Stu_picture'] 
    ? "https://std.phichai.ac.th/photo/{$student['Stu_picture']}" 
    : '../dist/img/default-avatar.svg';
?>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-gradient-to-b from-blue-600 via-blue-700 to-indigo-800 dark:from-slate-900 dark:via-slate-950 dark:to-black text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl overflow-hidden">
    
    <!-- Logo Area -->
    <div class="h-20 flex items-center justify-center gap-3 border-b border-white/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,...')] opacity-5"></div>
        <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shadow-inner">
            <i class="fas fa-user-graduate text-xl text-white/90"></i>
        </div>
        <div>
            <span class="text-lg font-black tracking-tight text-white">STUDENT</span>
            <p class="text-[9px] font-bold text-blue-200/70 uppercase tracking-[0.2em]">Portal</p>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-4">
            <div class="relative">
                <img src="<?= $imgPath ?>" alt="Avatar" class="w-14 h-14 rounded-2xl border-2 border-white/20 shadow-lg object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                <span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-400 rounded-full border-2 border-blue-700 dark:border-slate-900"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate"><?= htmlspecialchars(($student['Stu_name'] ?? '') . ' ' . ($student['Stu_sur'] ?? '')) ?></p>
                <p class="text-[10px] font-bold text-blue-200/70 uppercase tracking-widest">ม.<?= $student['Stu_major'] ?? '-' ?>/<?= $student['Stu_room'] ?? '-' ?></p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto h-[calc(100vh-280px)]">
        <p class="px-4 py-2 text-[9px] font-black text-blue-300/50 uppercase tracking-[0.2em]">เมนูหลัก</p>
        
        <?php foreach ($menuItems as $item): 
            $isActive = ($activePage === $item['key']);
        ?>
        <a href="<?= $item['href'] ?>" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 group
                  <?= $isActive 
                      ? 'bg-white/15 text-white shadow-lg shadow-blue-900/20' 
                      : 'text-blue-100/70 hover:bg-white/10 hover:text-white' ?>"
           onclick="closeSidebarOnMobile()">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 
                        <?= $isActive 
                            ? 'bg-white text-blue-600 shadow-md' 
                            : 'bg-white/10 group-hover:bg-white/20' ?>">
                <i class="fas <?= $item['icon'] ?> text-sm"></i>
            </div>
            <span class="text-sm font-bold"><?= $item['text'] ?></span>
            <?php if ($isActive): ?>
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        
        <!-- Data Section -->
        <p class="px-4 py-2 mt-4 text-[9px] font-black text-blue-300/50 uppercase tracking-[0.2em]">ข้อมูล</p>
        
        <?php foreach ($dataMenuItems as $item): 
            $isActive = ($activePage === $item['key']);
        ?>
        <a href="<?= $item['href'] ?>" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 group
                  <?= $isActive 
                      ? 'bg-white/15 text-white shadow-lg shadow-blue-900/20' 
                      : 'text-blue-100/70 hover:bg-white/10 hover:text-white' ?>"
           onclick="closeSidebarOnMobile()">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 
                        <?= $isActive 
                            ? 'bg-white text-blue-600 shadow-md' 
                            : 'bg-white/10 group-hover:bg-white/20' ?>">
                <i class="fas <?= $item['icon'] ?> text-sm"></i>
            </div>
            <span class="text-sm font-bold"><?= $item['text'] ?></span>
            <?php if ($isActive): ?>
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        
        <!-- Record Section -->
        <p class="px-4 py-2 mt-4 text-[9px] font-black text-blue-300/50 uppercase tracking-[0.2em]">บันทึก</p>
        
        <?php foreach ($recordMenuItems as $item): 
            $isActive = ($activePage === $item['key']);
        ?>
        <a href="<?= $item['href'] ?>" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 group
                  <?= $isActive 
                      ? 'bg-white/15 text-white shadow-lg shadow-blue-900/20' 
                      : 'text-blue-100/70 hover:bg-white/10 hover:text-white' ?>"
           onclick="closeSidebarOnMobile()">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 
                        <?= $isActive 
                            ? 'bg-white text-blue-600 shadow-md' 
                            : 'bg-white/10 group-hover:bg-white/20' ?>">
                <i class="fas <?= $item['icon'] ?> text-sm"></i>
            </div>
            <span class="text-sm font-bold"><?= $item['text'] ?></span>
            <?php if ($isActive): ?>
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        
        <!-- Separator -->
        <div class="my-4 border-t border-white/10"></div>
        
        <!-- Logout -->
        <a href="../logout.php" 
           class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-2xl text-blue-200/70 hover:bg-red-500/20 hover:text-white transition-all duration-300 group">
            <div class="w-10 h-10 rounded-xl bg-red-500/20 group-hover:bg-red-500/30 flex items-center justify-center transition-all">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </div>
            <span class="text-sm font-bold">ออกจากระบบ</span>
        </a>
    </nav>
    
    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 bg-black/20">
        <p class="text-[9px] text-center text-blue-200/50 font-bold uppercase tracking-widest">
            StdCare Student v2.0
        </p>
    </div>
</aside>
