<?php
/**
 * Teacher Navbar Component
 * MVC Pattern - Top navigation bar for teacher pages (Student Care System)
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load config
$configPath = __DIR__ . '/../../config.json';
$config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
$global = $config['global'] ?? ['pageTitle' => 'ระบบดูแลช่วยเหลือนักเรียน'];

// Get teacher data from various sources (priority order)
$teacherData = $_SESSION['teacher_data'] ?? $userData ?? [];

$userName = 'ครู';
if (!empty($teacherData['Teach_name'])) {
    $userName = $teacherData['Teach_name'];
}

$userClass = $teacherData['Teach_class'] ?? '';
$userRoom = $teacherData['Teach_room'] ?? '';
$userRole = (!empty($userClass) && !empty($userRoom)) ? "ม.{$userClass}/{$userRoom}" : 'ครูที่ปรึกษา';
?>

<!-- Mobile Menu Button -->
<div class="lg:hidden fixed top-4 left-4 z-50 no-print">
    <button onclick="toggleSidebar()" class="p-3 rounded-xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl transition-all">
        <i class="fas fa-bars text-gray-700 dark:text-gray-200"></i>
    </button>
</div>

<!-- Top Navbar -->
<header class="sticky top-0 z-30 glass border-b border-white/10">
    <div class="flex items-center justify-between px-4 md:px-6 py-4">
        <!-- Left: Page Title -->
        <div class="flex items-center space-x-4">
            <div class="hidden lg:block">
                <h1 class="text-lg font-bold text-gray-800 dark:text-white"><?php echo $global['pageTitle'] ?? 'ระบบดูแลช่วยเหลือนักเรียน'; ?></h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <?php 
                    $thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                    echo date('j') . ' ' . $thaiMonths[(int)date('n')] . ' ' . (date('Y') + 543);
                    ?>
                </p>
            </div>
        </div>
        
        <!-- Right: User Menu & Actions -->
        <div class="flex items-center space-x-3 no-print">
            <!-- Dark Mode Toggle -->
            <button onclick="toggleDarkMode()" class="p-2 rounded-xl bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                <i class="fas fa-sun text-amber-500 dark:hidden"></i>
                <i class="fas fa-moon text-indigo-400 hidden dark:inline"></i>
            </button>
            
            <!-- User Menu -->
            <div class="flex items-center space-x-2 px-3 py-2 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border border-blue-200 dark:border-blue-800/50">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                    <i class="fas fa-chalkboard-teacher text-white text-sm"></i>
                </div>
                <div class="hidden sm:block">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-xs text-blue-600 dark:text-blue-400"><?php echo htmlspecialchars($userRole); ?></p>
                </div>
            </div>
        </div>
    </div>
</header>
