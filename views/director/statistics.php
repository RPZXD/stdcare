<?php
/**
 * View: Director Statistics
 * Modern UI with Tailwind CSS & High-Impact Analytics
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-violet-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-chart-line"></i>
                </span>
                กราฟสถิติ <span class="text-violet-600 italic">ภาพรวมโรงเรียน</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">School-wide Analytical Dashboard</p>
        </div>
    </div>

    <!-- Stats Cards Large -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <?php 
        $statItems = [
            ['label' => 'นักเรียนทั้งหมด', 'value' => $stats['students'], 'icon' => 'fa-user-graduate', 'color' => 'indigo'],
            ['label' => 'บุคลากรทั้งหมด', 'value' => $stats['teachers'], 'icon' => 'fa-chalkboard-teacher', 'color' => 'sky'],
            ['label' => 'การเยี่ยมบ้าน', 'value' => $stats['homevisit'], 'icon' => 'fa-house-user', 'color' => 'emerald'],
            ['label' => 'รายการพฤติกรรม', 'value' => $stats['behavior'], 'icon' => 'fa-clipboard-list', 'color' => 'rose'],
        ];
        foreach ($statItems as $item): 
        ?>
        <div class="glass-effect p-8 rounded-[2.5rem] border border-white/50 shadow-xl group hover:scale-105 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-<?php echo $item['color']; ?>-50 dark:bg-<?php echo $item['color']; ?>-900/30 text-<?php echo $item['color']; ?>-600 dark:text-<?php echo $item['color']; ?>-400 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-<?php echo $item['color']; ?>-600 group-hover:text-white transition-all">
                    <i class="fas <?php echo $item['icon']; ?> text-2xl"></i>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</span>
                    <span class="text-[9px] font-black px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-full border border-emerald-500/20 uppercase">Live</span>
                </div>
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 italic"><?php echo $item['label']; ?></p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter"><?php echo number_format($item['value']); ?></h3>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- More detailed stats can be added here -->
    <div class="glass-effect rounded-[3rem] p-10 shadow-2xl border-t border-white/50">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-info-circle"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white italic">สรุปรายละเอียดเชิงเทคนิค</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <p class="text-sm font-bold text-slate-500 border-l-4 border-indigo-500 pl-4 py-1 uppercase tracking-widest mb-6">สถิติด้านการเรียนและบุคลากร</p>
                <div class="flex items-center justify-between p-4 bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-white/20">
                    <span class="text-sm font-black text-slate-600 dark:text-slate-400">อัตราส่วนครูต่อนักเรียน</span>
                    <span class="text-sm font-black text-indigo-600 uppercase italic">1 : <?php echo $stats['teachers'] > 0 ? round($stats['students'] / $stats['teachers'], 1) : '-'; ?></span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-white/20">
                    <span class="text-sm font-black text-slate-600 dark:text-slate-400">จำนวนการเยี่ยมบ้านเฉลี่ย (ต่อปี)</span>
                    <span class="text-sm font-black text-emerald-600 uppercase italic"><?php echo $stats['students'] > 0 ? round(($stats['homevisit'] / $stats['students']) * 100, 1) : '0'; ?>%</span>
                </div>
            </div>
            
            <div class="space-y-4">
                <p class="text-sm font-bold text-slate-500 border-l-4 border-rose-500 pl-4 py-1 uppercase tracking-widest mb-6">สถิติด้านความประพฤติ</p>
                <div class="flex items-center justify-between p-4 bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-white/20">
                    <span class="text-sm font-black text-slate-600 dark:text-slate-400">รายการพฤติกรรมสะสม</span>
                    <span class="text-sm font-black text-rose-600 uppercase italic"><?php echo number_format($stats['behavior']); ?> รายการ</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-white/20">
                    <span class="text-sm font-black text-slate-600 dark:text-slate-400">สถานะระเบียนความประพฤติ</span>
                    <span class="text-sm font-black text-emerald-600 uppercase italic tracking-widest tracking-widest font-black uppercase tracking-widest">Normal</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/director_app.php';
?>
