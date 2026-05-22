<?php
/**
 * Student Sidebar Component
 * Modern Multi-level Sidebar with Glassmorphism & Alpine.js (Blue Theme)
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$activePage = $activePage ?? 'dashboard';
$student = $_SESSION['student_data'] ?? [];

$menuGroups = [
    [
        'label' => 'เมนูหลัก',
        'items' => [
            [
                'key' => 'dashboard',
                'name' => 'หน้าหลัก',
                'url' => 'index.php',
                'icon' => 'fa-gauge-high',
                'gradient' => ['from' => 'blue-400', 'to' => 'indigo-500'],
            ],
            [
                'key' => 'information',
                'name' => 'ข้อมูลส่วนตัว',
                'url' => 'std_information.php',
                'icon' => 'fa-user-graduate',
                'gradient' => ['from' => 'cyan-400', 'to' => 'blue-500'],
            ],
        ]
    ],
    [
        'label' => 'ข้อมูลและการเรียน',
        'items' => [
            [
                'key' => 'reports',
                'name' => 'รายงานข้อมูล',
                'icon' => 'fa-chart-pie',
                'gradient' => ['from' => 'violet-400', 'to' => 'purple-500'],
                'submenu' => [
                    ['key' => 'checktime', 'name' => 'เวลาเรียน', 'url' => 'std_checktime.php', 'icon' => 'fa-clock'],
                    ['key' => 'roomdata', 'name' => 'ข้อมูลห้องเรียน', 'url' => 'std_roomdata.php', 'icon' => 'fa-door-open'],
                    ['key' => 'behavior', 'name' => 'คะแนนพฤติกรรม', 'url' => 'std_behavior.php', 'icon' => 'fa-star'],
                    ['key' => 'search', 'name' => 'ค้นหาข้อมูล', 'url' => 'std_search_data.php', 'icon' => 'fa-search'],
                ]
            ]
        ]
    ],
    [
        'label' => 'ระบบบันทึกผล',
        'items' => [
            [
                'key' => 'records',
                'name' => 'บันทึกข้อมูล',
                'icon' => 'fa-edit',
                'gradient' => ['from' => 'rose-400', 'to' => 'pink-500'],
                'submenu' => [
                    ['key' => 'visithome', 'name' => 'บันทึกเยี่ยมบ้าน', 'url' => 'std_visit_home.php', 'icon' => 'fa-home'],
                    ['key' => 'sdq', 'name' => 'บันทึก SDQ', 'url' => 'std_sdq.php', 'icon' => 'fa-clipboard-list'],
                    ['key' => 'eq', 'name' => 'บันทึก EQ', 'url' => 'std_eq.php', 'icon' => 'fa-heart'],
                    ['key' => 'screen11', 'name' => 'คัดกรอง 11 ด้าน', 'url' => 'std_screen11.php', 'icon' => 'fa-clipboard-check'],
                    ['key' => 'gps', 'name' => 'บันทึกพิกัด GPS', 'url' => 'std_gps.php', 'icon' => 'fa-map-marker-alt'],
                ]
            ]
        ]
    ]
];

$studentPicture = $student['Stu_picture'] ?? '';
$imgPath = '../dist/img/default-avatar.svg';

if ($studentPicture) {
    $localFile = __DIR__ . '/../../photo/' . $studentPicture;
    if (file_exists($localFile)) {
        $imgPath = '../photo/' . $studentPicture;
    } else {
        $imgPath = 'https://std.phichai.ac.th/photo/' . $studentPicture;
    }
}
?>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-slate-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl flex flex-col no-print overflow-hidden">
    
    <!-- Premium Gradient Background Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-indigo-600/10 to-transparent pointer-events-none"></div>

    <!-- Logo Area -->
    <div class="relative h-24 flex items-center px-6 gap-4 border-b border-white/5 overflow-hidden">
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <i class="fas fa-graduation-cap text-xl text-white"></i>
        </div>
        <div class="relative">
            <span class="text-xl font-black tracking-tight text-white block leading-none mb-1">STUDENT</span>
            <span class="text-[9px] font-black text-blue-400 uppercase tracking-[0.3em]">CARE PORTAL</span>
        </div>
    </div>
    
    <!-- User Profile Card -->
    <div class="relative p-6 group">
        <div class="absolute inset-x-4 inset-y-2 bg-white/5 rounded-[2rem] border border-white/5 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
        <div class="relative flex items-center gap-4">
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <img src="<?= $imgPath ?>" alt="Avatar" class="relative w-14 h-14 rounded-2xl border border-white/10 shadow-xl object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 shadow-lg"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-black text-white truncate"><?= htmlspecialchars(($student['Stu_name'] ?? '') . ' ' . ($student['Stu_sur'] ?? '')) ?></p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded-md text-[9px] font-black uppercase">ม.<?= $student['Stu_major'] ?? '-' ?>/<?= $student['Stu_room'] ?? '-' ?></span>
                    <span class="text-[9px] font-bold text-slate-500">เลขที่ <?= $student['Stu_no'] ?? '-' ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Container -->
    <nav class="relative flex-1 px-4 pb-10 space-y-6 overflow-y-auto custom-scrollbar">
        
        <?php foreach ($menuGroups as $group): ?>
        <div class="space-y-1">
            <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3"><?= $group['label'] ?></p>
            
            <?php foreach ($group['items'] as $item): 
                $hasSubmenu = isset($item['submenu']) && !empty($item['submenu']);
                $isParentActive = false;
                if ($hasSubmenu) {
                    foreach ($item['submenu'] as $sub) {
                        if ($activePage === $sub['key']) {
                            $isParentActive = true;
                            break;
                        }
                    }
                }
                $isActive = ($activePage === $item['key']) || $isParentActive;
                $fromColor = $item['gradient']['from'];
                $toColor = $item['gradient']['to'];
                $colorName = explode('-', $fromColor)[0];
            ?>
            
            <?php if ($hasSubmenu): ?>
            <div x-data="{ open: <?= $isParentActive ? 'true' : 'false' ?> }" class="mb-1">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 group
                               <?= $isActive ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-<?= $fromColor ?> to-<?= $toColor ?> flex items-center justify-center shadow-lg shadow-<?= $colorName ?>-500/20 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas <?= $item['icon'] ?> text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-bold tracking-tight"><?= $item['name'] ?></span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </button>
                
                <div x-show="open" x-collapse class="mt-2 ml-10 space-y-1">
                    <?php foreach ($item['submenu'] as $sub): 
                        $isSubActive = ($activePage === $sub['key']);
                    ?>
                    <a href="<?= $sub['url'] ?>" 
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
                              <?= $isSubActive 
                                  ? 'text-white bg-blue-500/20' 
                                  : 'text-slate-500 hover:text-white hover:bg-white/5' ?>"
                       onclick="closeSidebarOnMobile()">
                        <i class="fas <?= $sub['icon'] ?> scale-75 <?= $isSubActive ? 'text-blue-400' : '' ?>"></i>
                        <?= $sub['name'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php else: ?>
            <a href="<?= $item['url'] ?>" 
               class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 group
                      <?= $isActive 
                          ? 'bg-white/10 text-white shadow-xl border border-white/5' 
                          : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>"
               onclick="closeSidebarOnMobile()">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-<?= $fromColor ?> to-<?= $toColor ?> flex items-center justify-center shadow-lg shadow-<?= $colorName ?>-500/20 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas <?= $item['icon'] ?> text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight"><?= $item['name'] ?></span>
                </div>
                <?php if ($isActive): ?>
                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 shadow-[0_0_10px_#60a5fa] animate-pulse"></div>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <!-- Divider -->
        <div class="h-px bg-gradient-to-r from-transparent via-white/5 to-transparent my-6"></div>
        
        <!-- Logout -->
        <a href="../logout.php" 
           class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition-all duration-300 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform">
                <i class="fas fa-sign-out-alt text-white text-sm"></i>
            </div>
            <span class="text-sm font-bold tracking-tight">ออกจากระบบ</span>
        </a>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="relative p-6 bg-slate-900/50 border-t border-white/5">
        <div class="flex flex-col items-center">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Student Care System</p>
            <div class="flex items-center gap-2">
                <div class="w-1 h-1 bg-emerald-500 rounded-full"></div>
                <p class="text-[8px] font-bold text-slate-700 italic">Version 2.0 (Premium)</p>
            </div>
        </div>
    </div>
</aside>

<!-- Alpine.js for collapse functionality -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.1); }
</style>
