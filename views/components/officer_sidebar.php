<?php 
/**
 * Officer Sidebar Component
 * MVC Pattern - Sidebar navigation for officer pages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$configPath = __DIR__ . '/../../config.json';
$config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
$global = $config['global'] ?? ['logoLink' => 'logo-phicha.png', 'nameTitle' => 'StdCare', 'nameschool' => 'โรงเรียนพิชัย'];

// Get current user info
$officerData = $_SESSION['officer_data'] ?? $userData ?? [];
$userName = $officerData['Teach_name'] ?? 'เจ้าหน้าที่';
$userPhoto = $officerData['Teach_photo'] ?? '';
$userRole = 'เจ้าหน้าที่ระบบ';

// Menu configuration for Officer
$menuItems = [
    [
        'key' => 'home',
        'name' => 'หน้าหลัก',
        'url' => 'index.php',
        'icon' => 'fa-house-chimney',
        'gradient' => ['from' => 'blue-500', 'to' => 'indigo-600'],
    ],
    [
        'key' => 'student_data',
        'name' => 'ข้อมูลนักเรียน',
        'url' => 'data_student.php',
        'icon' => 'fa-user-graduate',
        'gradient' => ['from' => 'emerald-500', 'to' => 'teal-600'],
    ],
    [
        'key' => 'teacher_data',
        'name' => 'ครูและบุคลากร',
        'url' => 'data_teacher.php',
        'icon' => 'fa-chalkboard-teacher',
        'gradient' => ['from' => 'violet-500', 'to' => 'purple-600'],
    ],
    [
        'key' => 'parent_data',
        'name' => 'ข้อมูลผู้ปกครอง',
        'url' => 'data_parent.php',
        'icon' => 'fa-users-rectangle',
        'gradient' => ['from' => 'sky-500', 'to' => 'cyan-600'],
    ],
    [
        'key' => 'behavior',
        'name' => 'หักคะแนนพฤติกรรม',
        'url' => 'data_behavior.php',
        'icon' => 'fa-shield-heart',
        'gradient' => ['from' => 'rose-500', 'to' => 'pink-600'],
    ],
    [
        'key' => 'forgot_rfid',
        'name' => 'บันทึกลืมบัตร',
        'url' => 'data_forgotrfid.php',
        'icon' => 'fa-id-card-clip',
        'gradient' => ['from' => 'amber-500', 'to' => 'orange-600'],
    ],
    [
        'key' => 'attendance',
        'name' => 'ข้อมูลเช็คชื่อ',
        'url' => 'data_attendance.php',
        'icon' => 'fa-clipboard-check',
        'gradient' => ['from' => 'teal-500', 'to' => 'emerald-600'],
    ],
    [
        'key' => 'rfid_manage',
        'name' => 'จัดการ RFID',
        'url' => 'rfid.php',
        'icon' => 'fa-credit-card',
        'gradient' => ['from' => 'slate-600', 'to' => 'slate-800'],
    ],
    [
        'key' => 'report',
        'name' => 'รายงานข้อมูล',
        'url' => 'report.php',
        'icon' => 'fa-chart-pie',
        'gradient' => ['from' => 'indigo-500', 'to' => 'violet-600'],
    ],
];
?>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity duration-300 no-print" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-72 sm:w-64 h-screen transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div class="h-full overflow-y-auto bg-gradient-to-b from-slate-900 via-slate-950 to-black">
        
        <!-- Logo Section -->
        <div class="px-6 py-8 border-b border-white/5">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center space-x-4 group flex-1">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full blur-lg opacity-40 group-hover:opacity-70 transition-opacity"></div>
                        <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" class="relative w-12 h-12 rounded-full ring-2 ring-white/10 group-hover:ring-blue-400/50 transition-all" alt="Logo">
                    </div>
                    <div>
                        <span class="text-xl font-black text-white tracking-tight uppercase"><?php echo $global['nameTitle'] ?? 'STDCARE'; ?></span>
                        <p class="text-[10px] font-bold text-blue-400 tracking-[0.2em] uppercase">Officer Portal</p>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-white rounded-xl hover:bg-white/5">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="px-6 py-4 border-b border-white/5">
            <div class="flex items-center space-x-3">
                <?php if (!empty($userPhoto)): ?>
                <img src="/stdcare/teacher/uploads/phototeach/<?php echo htmlspecialchars($userPhoto); ?>" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-400/50" alt="Profile">
                <?php else: ?>
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-white"></i>
                </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-xs text-blue-300"><?php echo htmlspecialchars($userRole); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-6 px-3 pb-24">
            <div class="mb-4 px-4 flex items-center justify-between">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Management Tools</p>
            </div>
            <ul class="space-y-1.5">
                <?php foreach ($menuItems as $menu): 
                    $fromColor = $menu['gradient']['from'];
                    $toColor = $menu['gradient']['to'];
                    $isActive = basename($_SERVER['PHP_SELF']) == basename($menu['url']);
                    $colorName = explode('-', $fromColor)[0];
                ?>
                <li>
                    <a href="<?php echo htmlspecialchars($menu['url']); ?>" 
                       onclick="closeSidebarOnMobile()"
                       class="sidebar-item flex items-center px-4 py-3 rounded-2xl transition-all group active:scale-[0.98] <?php echo $isActive ? 'bg-white/10 text-white border border-white/5 shadow-xl shadow-black/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                        <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-<?php echo $fromColor; ?> to-<?php echo $toColor; ?> rounded-xl shadow-lg shadow-<?php echo $colorName; ?>-500/20 group-hover:shadow-<?php echo $colorName; ?>-500/40 transition-shadow">
                            <i class="fas <?php echo $menu['icon']; ?> text-white text-base"></i>
                        </span>
                        <span class="ml-4 font-bold text-sm tracking-tight"><?php echo htmlspecialchars($menu['name']); ?></span>
                        <?php if($isActive): ?>
                            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.6)] animate-pulse"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
                
                <li class="my-6 px-4">
                    <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                </li>
                
                <li>
                    <a href="../logout.php" class="sidebar-item flex items-center px-4 py-3 text-gray-400 rounded-2xl hover:bg-rose-500/10 hover:text-rose-400 group">
                        <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-rose-500 to-red-600 rounded-xl shadow-lg shadow-rose-500/20 group-hover:shadow-rose-500/40 transition-shadow">
                            <i class="fas fa-power-off text-white"></i>
                        </span>
                        <span class="ml-4 font-bold text-sm tracking-tight uppercase italic">ออกจากระบบ</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Bottom Section -->
        <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
            <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest text-center truncate"><?php echo $global['nameschool'] ?? 'โรงเรียนพิกุลทอง'; ?></p>
            </div>
        </div>
    </div>
</aside>

<!-- Alpine.js Collapse Plugin + Core (plugin must load first) -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
