<?php
/**
 * View: Director Reports
 * Modern UI with Tailwind CSS & Responsive Tab System
 */
ob_start();

$tabs = [
    'late' => ['label' => 'รายงานข้อมูลมาสาย', 'icon' => 'fa-clock', 'file' => 'report_late.php'],
    'homevisit' => ['label' => 'สถิติการเยี่ยมบ้าน', 'icon' => 'fa-home', 'file' => 'report_homevisit.php'],
    'deduct-room' => ['label' => 'หักคะแนน (รายห้อง)', 'icon' => 'fa-school', 'file' => 'report_deduct_room.php'],
    'deduct-group' => ['label' => 'หักคะแนน (ตามกลุ่ม)', 'icon' => 'fa-chart-bar', 'file' => 'report_deduct_group.php'],
    'parent-leader' => ['label' => 'ประธานผู้ปกครอง', 'icon' => 'fa-users', 'file' => 'report_parent_leader.php'],
];

$activeTab = $tabs[$tab] ?? $tabs['late'];
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-file-invoice"></i>
                </span>
                คลังข้อมูล <span class="text-indigo-600 italic">รายงานผู้บริหาร</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Executive Insight & Advanced Reporting</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Academic Year</span>
                <span class="text-sm font-black text-indigo-600 tracking-wider"><?php echo $pee; ?></span>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-8 overflow-x-auto">
        <div class="flex p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-[2.5rem] min-w-max border border-slate-200/50 dark:border-slate-700/50">
            <?php foreach ($tabs as $key => $t): 
                $isActive = ($tab === $key);
            ?>
            <a href="?tab=<?php echo $key; ?>" class="px-8 py-3.5 rounded-full flex items-center gap-3 transition-all duration-300 group <?php echo $isActive ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xl shadow-indigo-500/10' : 'text-slate-500 hover:text-indigo-500 dark:hover:text-indigo-400 hover:bg-white/50 dark:hover:bg-slate-800'; ?>">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors <?php echo $isActive ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'bg-transparent group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/10'; ?>">
                    <i class="fas <?php echo $t['icon']; ?> text-[14px]"></i>
                </div>
                <span class="font-black text-sm whitespace-nowrap tracking-tight"><?php echo $t['label']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Report Content Area -->
    <div class="glass-effect rounded-[3rem] p-4 md:p-10 shadow-2xl border-t border-white/50 relative overflow-hidden min-h-[600px]">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10">
            <?php 
            if (isset($activeTab['file']) && file_exists(__DIR__ . '/../../director/' . $activeTab['file'])) {
                include __DIR__ . '/../../director/' . $activeTab['file'];
            } else {
                echo '
                <div class="flex flex-col items-center justify-center py-40 text-center">
                    <div class="w-24 h-24 bg-rose-50 dark:bg-rose-900/20 text-rose-500 rounded-[2rem] flex items-center justify-center text-4xl mb-6 animate-bounce">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800 dark:text-white mb-2 italic">ขออภัย สมาขิกข้อมูลนี้ไม่พบ</h4>
                    <p class="text-slate-500">กรุณาติดต่อผู้ดูแลระบบหากปัญหาพยังคงอยู่</p>
                </div>';
            }
            ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/director_app.php';
?>
