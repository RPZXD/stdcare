<?php
/**
 * Component: UI Header
 * Parameters: $headerData ['title', 'subtitle', 'icon', 'color', 'actions' => [['id', 'icon', 'text', 'color']]]
 */
$h = $headerData ?? [];
$color = $h['color'] ?? 'indigo';
?>

<div class="admin-page-header flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-6 mb-6 lg:mb-10">
    <div class="flex items-start gap-3 lg:gap-4 w-full lg:w-auto">
        <div class="shrink-0 w-10 h-10 lg:w-14 lg:h-14 bg-<?php echo $color; ?>-600 rounded-xl lg:rounded-2xl flex items-center justify-center text-white shadow-xl text-lg lg:text-2xl transition-all">
            <i class="fas <?php echo $h['icon'] ?? 'fa-circle'; ?>"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-xl lg:text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-tight">
                <?php echo $h['title'] ?? 'Title'; ?>
            </h2>
            <p class="text-[9px] lg:text-[11px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">
                <?php echo $h['subtitle'] ?? ''; ?>
            </p>
        </div>
    </div>
    
    <?php if (!empty($h['actions'])): ?>
    <div class="admin-header-actions flex flex-wrap gap-2 lg:gap-3 w-full lg:w-auto">
        <?php foreach ($h['actions'] as $action): ?>
        <button id="<?php echo $action['id']; ?>" 
                <?php if (!empty($action['onclick'])) echo 'onclick="' . $action['onclick'] . '"'; ?>
                <?php if (!empty($action['style'])) echo 'style="' . $action['style'] . '"'; ?>
                class="flex-1 lg:flex-none px-4 lg:px-6 py-2.5 lg:py-3 bg-<?php echo $action['color'] ?? $color; ?>-600 text-white rounded-lg lg:rounded-xl font-bold text-xs lg:text-sm shadow-lg shadow-<?php echo $action['color'] ?? $color; ?>-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2 min-w-0">
            <i class="fas <?php echo $action['icon'] ?? 'fa-plus'; ?>"></i> 
            <span><?php echo $action['text'] ?? ''; ?></span>
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
