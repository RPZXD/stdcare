<?php
/**
 * Component: UI Stat Card
 * Parameters: $statData ['label', 'value', 'icon', 'color', 'unit', 'status']
 */
$s = $statData ?? [];
$color = $s['color'] ?? 'indigo';
?>

<div class="glass-effect p-4 lg:p-6 rounded-2xl lg:rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden group hover:scale-[1.02] lg:hover:scale-105 transition-all duration-300">
    <div class="absolute -right-4 -top-4 w-16 lg:w-20 h-16 lg:h-20 bg-<?php echo $color; ?>-500/10 rounded-full blur-xl group-hover:bg-<?php echo $color; ?>-500/20 transition-all"></div>
    <div class="flex items-center gap-3 lg:gap-4 mb-3 lg:mb-4 relative z-10">
        <div class="w-10 h-10 lg:w-14 lg:h-14 bg-<?php echo $color; ?>-100 dark:bg-<?php echo $color; ?>-900/30 text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 rounded-lg lg:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-<?php echo $color; ?>-600 group-hover:text-white transition-all">
            <i class="fas <?php echo $s['icon'] ?? 'fa-chart-line'; ?> text-xl lg:text-2xl"></i>
        </div>
        <?php if (!empty($s['status'])): ?>
        <div class="text-right flex-1">
            <span class="text-[8px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-0.5 lg:mb-1">Status</span>
            <span class="text-[8px] lg:text-[9px] font-black px-1.5 lg:px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-full border border-emerald-500/20 uppercase"><?php echo $s['status']; ?></span>
        </div>
        <?php endif; ?>
    </div>
    <div class="relative z-10">
        <p class="text-[9px] lg:text-[11px] font-black text-slate-400 uppercase tracking-[0.1em] lg:tracking-[0.15em] mb-1 lg:mb-2 italic"><?php echo $s['label'] ?? ''; ?></p>
        <h3 class="text-2xl lg:text-4xl font-black text-slate-800 dark:text-white tabular-nums tracking-tighter">
            <?php echo $s['value'] ?? '0'; ?>
            <?php if (!empty($s['unit'])): ?>
            <span class="text-xs lg:text-sm font-medium opacity-50 ml-0.5 lg:ml-1 uppercase"><?php echo $s['unit']; ?></span>
            <?php endif; ?>
        </h3>
    </div>
</div>
