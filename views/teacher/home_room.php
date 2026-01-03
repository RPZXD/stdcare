<?php
/**
 * Teacher Home Room View
 * MVC Pattern - Premium Modern UI with Tailwind CSS
 * Mobile-First Design
 */

$pageTitle = $pageTitle ?? 'กิจกรรมโฮมรูม';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }
    
    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(2deg); }
    }
    .floating { animation: float 4s ease-in-out infinite; }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
    }
    .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .gradient-animate {
        background-size: 200% 200%;
        animation: gradient-shift 3s ease infinite;
    }
    
    /* Button Effects */
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .btn-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    .btn-action:hover::before {
        left: 100%;
    }
    .btn-action:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .btn-action:active {
        transform: scale(0.98);
    }
    
    /* Activity Card */
    .activity-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .activity-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--card-color, #3b82f6), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .activity-card:hover::before {
        opacity: 1;
    }
    .activity-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.12);
    }
    
    /* Type Badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Stats Card Hover */
    .stat-card {
        transition: all 0.3s ease;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    /* Skeleton Loading */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s ease-in-out infinite;
        border-radius: 8px;
    }
    .dark .skeleton {
        background: linear-gradient(90deg, #334155 25%, #475569 50%, #334155 75%);
    }
    
    /* Filter Button Effects */
    .type-filter-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
    }
    .type-filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .type-filter-btn.active {
        border-color: rgba(255,255,255,0.3);
    }
    
    /* Timeline Effects */
    .timeline-item {
        transition: all 0.3s ease;
    }
    .timeline-item:hover {
        transform: translateX(5px);
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #94a3b8, #64748b); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #64748b, #475569); }
    .dark ::-webkit-scrollbar-track { background: #1e293b; }
    .dark ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #475569, #334155); }
    
    /* Glow Effect */
    .glow-blue { box-shadow: 0 0 30px rgba(59, 130, 246, 0.4); }
    .glow-green { box-shadow: 0 0 30px rgba(34, 197, 94, 0.4); }
    .glow-purple { box-shadow: 0 0 30px rgba(168, 85, 247, 0.4); }
    
    /* Icon Bounce */
    .icon-bounce:hover i {
        animation: bounce 0.5s ease;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-8 border border-white/40 dark:border-slate-700/50 shadow-2xl relative">
        <!-- Background Orbs -->
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-sky-400/30 to-blue-500/30 rounded-full blur-3xl -z-10 floating"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-indigo-400/30 to-violet-500/30 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <!-- Title -->
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl blur-xl opacity-60"></div>
                    <div class="relative w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-school text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-1">
                        📚 กิจกรรมโฮมรูม
                    </h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-sky-100 to-blue-100 dark:from-sky-900/40 dark:to-blue-900/40 text-sky-700 dark:text-sky-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-chalkboard-teacher"></i>
                            ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-indigo-100 to-violet-100 dark:from-indigo-900/40 dark:to-violet-900/40 text-indigo-700 dark:text-indigo-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-calendar-alt"></i>
                            ภาคเรียน <?php echo htmlspecialchars($term); ?>/<?php echo htmlspecialchars($pee); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                <button onclick="openAddModal()" class="btn-action bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-sky-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>เพิ่มกิจกรรม</span>
                </button>
                <button onclick="printReport()" class="btn-action bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-emerald-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-sky-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
            <i class="fas fa-list-check text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-blue-600" id="totalActivities">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">กิจกรรมทั้งหมด</p>
    </div>
    
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-violet-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
            <i class="fas fa-calendar-week text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-600" id="thisMonth">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">เดือนนี้</p>
    </div>
    
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-amber-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
            <i class="fas fa-tags text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600" id="totalTypes">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">ประเภท</p>
    </div>
    
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
            <i class="fas fa-images text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-green-600" id="withImages">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">มีภาพประกอบ</p>
    </div>
</div>

<!-- Activities List -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800 dark:to-slate-900">
        <!-- Header Row -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-clipboard-list text-sky-500"></i>
                รายการกิจกรรมโฮมรูม
            </h2>
            <div class="flex items-center gap-3">
                <!-- View Toggle -->
                <div class="flex bg-slate-200 dark:bg-slate-700 rounded-lg p-1">
                    <button onclick="setView('card')" id="viewCard" class="px-3 py-1.5 rounded-md text-sm font-medium transition bg-white dark:bg-slate-600 text-slate-800 dark:text-white shadow">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button onclick="setView('timeline')" id="viewTimeline" class="px-3 py-1.5 rounded-md text-sm font-medium transition text-slate-500 dark:text-slate-400">
                        <i class="fas fa-stream"></i>
                    </button>
                </div>
                <!-- Search -->
                <div class="relative w-full sm:w-48">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" id="searchActivity" placeholder="ค้นหา..." 
                           class="w-full pl-9 pr-3 py-2 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-sky-400 transition">
                </div>
            </div>
        </div>
        
        <!-- Type Filter -->
        <div class="flex flex-wrap items-center gap-2" id="typeFilters">
            <button onclick="filterByType('all')" data-type="all" class="type-filter-btn active px-4 py-2 rounded-full text-sm font-bold transition bg-gradient-to-r from-slate-700 to-slate-800 text-white shadow-lg">
                <i class="fas fa-layer-group mr-1"></i> ทั้งหมด
            </button>
            
            <!-- Hidden Type Buttons Container -->
            <div id="moreFilters" class="hidden flex flex-wrap gap-2">
                <?php 
                // Dynamic color palette - cycles through 16 colors
                $colorPalette = [
                    ['bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'text' => 'text-emerald-700 dark:text-emerald-300', 'hover' => 'hover:bg-emerald-200'],
                    ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-700 dark:text-blue-300', 'hover' => 'hover:bg-blue-200'],
                    ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-700 dark:text-purple-300', 'hover' => 'hover:bg-purple-200'],
                    ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-700 dark:text-amber-300', 'hover' => 'hover:bg-amber-200'],
                    ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-700 dark:text-red-300', 'hover' => 'hover:bg-red-200'],
                    ['bg' => 'bg-teal-100 dark:bg-teal-900/40', 'text' => 'text-teal-700 dark:text-teal-300', 'hover' => 'hover:bg-teal-200'],
                    ['bg' => 'bg-indigo-100 dark:bg-indigo-900/40', 'text' => 'text-indigo-700 dark:text-indigo-300', 'hover' => 'hover:bg-indigo-200'],
                    ['bg' => 'bg-orange-100 dark:bg-orange-900/40', 'text' => 'text-orange-700 dark:text-orange-300', 'hover' => 'hover:bg-orange-200'],
                    ['bg' => 'bg-pink-100 dark:bg-pink-900/40', 'text' => 'text-pink-700 dark:text-pink-300', 'hover' => 'hover:bg-pink-200'],
                    ['bg' => 'bg-cyan-100 dark:bg-cyan-900/40', 'text' => 'text-cyan-700 dark:text-cyan-300', 'hover' => 'hover:bg-cyan-200'],
                    ['bg' => 'bg-lime-100 dark:bg-lime-900/40', 'text' => 'text-lime-700 dark:text-lime-300', 'hover' => 'hover:bg-lime-200'],
                    ['bg' => 'bg-rose-100 dark:bg-rose-900/40', 'text' => 'text-rose-700 dark:text-rose-300', 'hover' => 'hover:bg-rose-200'],
                    ['bg' => 'bg-violet-100 dark:bg-violet-900/40', 'text' => 'text-violet-700 dark:text-violet-300', 'hover' => 'hover:bg-violet-200'],
                    ['bg' => 'bg-sky-100 dark:bg-sky-900/40', 'text' => 'text-sky-700 dark:text-sky-300', 'hover' => 'hover:bg-sky-200'],
                    ['bg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/40', 'text' => 'text-fuchsia-700 dark:text-fuchsia-300', 'hover' => 'hover:bg-fuchsia-200'],
                    ['bg' => 'bg-yellow-100 dark:bg-yellow-900/40', 'text' => 'text-yellow-700 dark:text-yellow-300', 'hover' => 'hover:bg-yellow-200'],
                ];
                
                foreach ($types as $type): 
                    $colorIndex = ($type['th_id'] - 1) % count($colorPalette);
                    $color = $colorPalette[$colorIndex];
                ?>
                <button onclick="filterByType(<?php echo $type['th_id']; ?>)" 
                        data-type="<?php echo $type['th_id']; ?>"
                        class="type-filter-btn px-3 py-1.5 rounded-full text-xs font-semibold transition border-2 border-transparent <?php echo $color['bg'] . ' ' . $color['text'] . ' ' . $color['hover']; ?>">
                    <?php echo htmlspecialchars($type['th_name']); ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Toggle Button -->
            <button onclick="toggleFilters()" id="toggleFiltersBtn" class="px-3 py-2 rounded-full text-sm font-medium transition bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 hover:bg-sky-200 flex items-center gap-1">
                <i class="fas fa-chevron-down text-xs" id="toggleIcon"></i>
                <span id="toggleText">แสดงประเภท</span>
            </button>
        </div>
    </div>
    
    <!-- Activity Cards Grid -->
    <div id="activityGrid" class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Loading Skeleton -->
        <div class="skeleton-card glass-card rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="skeleton h-4 w-24 m-4 rounded"></div>
            <div class="p-4 space-y-3">
                <div class="skeleton h-6 w-3/4 rounded"></div>
                <div class="skeleton h-4 w-full rounded"></div>
            </div>
        </div>
    </div>
    
    <!-- Timeline View -->
    <div id="activityTimeline" class="p-5 hidden">
        <!-- Timeline items loaded via JS -->
    </div>
    
    <!-- Empty State -->
    <div id="emptyState" class="hidden p-12 text-center">
        <div class="w-24 h-24 mx-auto bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-6 floating">
            <i class="fas fa-clipboard text-gray-400 text-4xl"></i>
        </div>
        <p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">ยังไม่มีกิจกรรมโฮมรูม</p>
        <p class="text-slate-500 mb-6">เริ่มเพิ่มกิจกรรมเพื่อบันทึกการดำเนินงานโฮมรูม</p>
        <button onclick="openAddModal()" class="btn-action bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i> เพิ่มกิจกรรมแรก
        </button>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="fixed inset-4 md:inset-10 lg:inset-y-10 lg:left-1/4 lg:right-1/4 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-4 md:p-5 border-b dark:border-slate-700 bg-gradient-to-r from-sky-500 to-blue-600 text-white">
            <h3 class="text-lg md:text-xl font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> เพิ่มกิจกรรมโฮมรูม
            </h3>
            <button onclick="closeAddModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-6">
            <form id="addForm" enctype="multipart/form-data">
                <!-- Type -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fas fa-tags mr-1 text-sky-500"></i> ประเภทกิจกรรม <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="addType" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-sky-400 transition">
                        <option value="">-- เลือกประเภท --</option>
                        <?php foreach ($types as $type): ?>
                        <option value="<?php echo $type['th_id']; ?>"><?php echo htmlspecialchars($type['th_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Title -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fas fa-heading mr-1 text-sky-500"></i> หัวข้อเรื่อง <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="addTitle" required placeholder="กรอกหัวข้อเรื่องสั้นๆ"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-sky-400 transition">
                </div>
                
                <!-- Detail -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fas fa-align-left mr-1 text-sky-500"></i> รายละเอียดกิจกรรม <span class="text-red-500">*</span>
                    </label>
                    <textarea name="detail" id="addDetail" rows="4" required placeholder="อธิบายรายละเอียดกิจกรรม..."
                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-sky-400 transition resize-none"></textarea>
                </div>
                
                <!-- Expected Result -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fas fa-bullseye mr-1 text-sky-500"></i> ผลที่คาดว่าจะได้รับ <span class="text-red-500">*</span>
                    </label>
                    <textarea name="result" id="addResult" rows="3" required placeholder="ผลลัพธ์ที่คาดหวัง..."
                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-sky-400 transition resize-none"></textarea>
                </div>
                
                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-image mr-1 text-sky-500"></i> ภาพประกอบ 1
                        </label>
                        <input type="file" name="image1" id="addImage1" accept="image/*"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                        <img id="addPreview1" src="" class="hidden mt-3 w-full h-32 object-cover rounded-xl border-2 border-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-image mr-1 text-sky-500"></i> ภาพประกอบ 2
                        </label>
                        <input type="file" name="image2" id="addImage2" accept="image/*"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                        <img id="addPreview2" src="" class="hidden mt-3 w-full h-32 object-cover rounded-xl border-2 border-gray-200">
                    </div>
                </div>
                
                <!-- Hidden Fields -->
                <input type="hidden" name="class" value="<?php echo $class; ?>">
                <input type="hidden" name="room" value="<?php echo $room; ?>">
                <input type="hidden" name="term" value="<?php echo $term; ?>">
                <input type="hidden" name="pee" value="<?php echo $pee; ?>">
            </form>
        </div>
        <div class="p-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-900 flex justify-end gap-3">
            <button onclick="closeAddModal()" class="px-5 py-2.5 bg-gray-400 hover:bg-gray-500 text-white rounded-xl font-medium transition">ยกเลิก</button>
            <button onclick="submitAdd()" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl font-bold shadow-lg hover:shadow-sky-500/30 transition flex items-center gap-2">
                <i class="fas fa-save"></i> บันทึก
            </button>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="fixed inset-4 md:inset-10 lg:inset-y-10 lg:left-1/4 lg:right-1/4 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-4 md:p-5 border-b dark:border-slate-700 bg-gradient-to-r from-emerald-500 to-green-600 text-white">
            <h3 class="text-lg md:text-xl font-bold flex items-center gap-2">
                <i class="fas fa-eye"></i> รายละเอียดกิจกรรม
            </h3>
            <button onclick="closeViewModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-6" id="viewModalBody">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="fixed inset-4 md:inset-10 lg:inset-y-10 lg:left-1/4 lg:right-1/4 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-4 md:p-5 border-b dark:border-slate-700 bg-gradient-to-r from-amber-500 to-orange-600 text-white">
            <h3 class="text-lg md:text-xl font-bold flex items-center gap-2">
                <i class="fas fa-edit"></i> แก้ไขกิจกรรม
            </h3>
            <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-6" id="editModalBody">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
const classValue = <?php echo json_encode($class); ?>;
const roomValue = <?php echo json_encode($room); ?>;
const termValue = <?php echo json_encode($term); ?>;
const peeValue = <?php echo json_encode($pee); ?>;
const teachers = <?php echo json_encode($teacherList); ?>;
const types = <?php echo json_encode($types); ?>;

let allActivities = [];

document.addEventListener('DOMContentLoaded', function() {
    loadActivities();
    
    // Search
    document.getElementById('searchActivity').addEventListener('input', debounce(filterActivities, 300));
    
    // Image previews
    setupImagePreview('addImage1', 'addPreview1');
    setupImagePreview('addImage2', 'addPreview2');
});

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function setupImagePreview(inputId, previewId) {
    document.getElementById(inputId).addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById(previewId);
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });
}

async function loadActivities() {
    try {
        const response = await fetch(`api/fetch_homeroom.php?class=${classValue}&room=${roomValue}&term=${termValue}&pee=${peeValue}`);
        const data = await response.json();
        
        if (data.success) {
            allActivities = data.data;
            renderActivities(allActivities);
            updateStats(allActivities);
        }
    } catch (error) {
        console.error('Error loading activities:', error);
    }
}

function renderActivities(activities) {
    const grid = document.getElementById('activityGrid');
    const timeline = document.getElementById('activityTimeline');
    const emptyState = document.getElementById('emptyState');
    
    grid.querySelectorAll('.skeleton-card').forEach(el => el.remove());
    
    if (activities.length === 0) {
        grid.innerHTML = '';
        timeline.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    
    // Card View
    let cardHtml = '';
    activities.forEach((item, index) => {
        const date = convertToThaiDate(item.h_date);
        const colors = getTypeColors(item.th_id);
        
        cardHtml += `
        <div class="activity-card glass-card rounded-xl overflow-hidden fade-in-up border-l-4 ${colors.border}" style="animation-delay: ${index * 0.05}s">
            <div class="p-4 ${colors.bg}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <span class="type-badge ${colors.badge} mb-2">${item.th_name}</span>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg mb-1 line-clamp-1">${item.h_topic}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i class="fas fa-calendar-day"></i> ${date}
                        </p>
                    </div>
                    ${item.h_pic1 ? `<img src="uploads/homeroom/${item.h_pic1}" class="w-16 h-16 object-cover rounded-lg shadow ml-3">` : ''}
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">${item.h_detail}</p>
                <div class="flex items-center gap-2">
                    <button onclick="viewActivity(${item.h_id})" class="flex-1 btn-action py-2 px-3 bg-white/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium text-sm flex items-center justify-center gap-1 shadow-sm">
                        <i class="fas fa-eye"></i> ดู
                    </button>
                    <button onclick="editActivity(${item.h_id})" class="flex-1 btn-action py-2 px-3 bg-white/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium text-sm flex items-center justify-center gap-1 shadow-sm">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                    <button onclick="deleteActivity(${item.h_id})" class="btn-action py-2 px-3 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg font-medium text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    });
    grid.innerHTML = cardHtml;
    
    // Timeline View
    let timelineHtml = '<div class="relative border-l-2 border-sky-300 dark:border-sky-700 ml-4">';
    activities.forEach((item, index) => {
        const date = convertToThaiDate(item.h_date);
        const colors = getTypeColors(item.th_id);
        
        timelineHtml += `
        <div class="mb-6 ml-6 fade-in-up" style="animation-delay: ${index * 0.08}s">
            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 ${colors.dot} rounded-full ring-4 ring-white dark:ring-slate-800 shadow">
                <i class="fas fa-circle text-[6px] text-white"></i>
            </span>
            <div class="p-4 glass-card rounded-xl border ${colors.borderLight} shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="type-badge ${colors.badge} text-xs">${item.th_name}</span>
                    <time class="text-xs text-slate-500">${date}</time>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">${item.h_topic}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-3">${item.h_detail}</p>
                <div class="flex gap-2">
                    <button onclick="viewActivity(${item.h_id})" class="text-xs px-3 py-1 bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 rounded-full font-medium"><i class="fas fa-eye mr-1"></i>ดู</button>
                    <button onclick="editActivity(${item.h_id})" class="text-xs px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-full font-medium"><i class="fas fa-edit mr-1"></i>แก้ไข</button>
                </div>
            </div>
        </div>
        `;
    });
    timelineHtml += '</div>';
    timeline.innerHTML = timelineHtml;
}

function getTypeColors(typeId) {
    // Dynamic color palette - cycles through 16 colors
    const colorPalette = [
        { border: 'border-emerald-500', bg: 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20', badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300', dot: 'bg-emerald-500', borderLight: 'border-emerald-200 dark:border-emerald-800' },
        { border: 'border-blue-500', bg: 'bg-gradient-to-br from-blue-50 to-sky-50 dark:from-blue-900/20 dark:to-sky-900/20', badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300', dot: 'bg-blue-500', borderLight: 'border-blue-200 dark:border-blue-800' },
        { border: 'border-purple-500', bg: 'bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20', badge: 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300', dot: 'bg-purple-500', borderLight: 'border-purple-200 dark:border-purple-800' },
        { border: 'border-amber-500', bg: 'bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20', badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300', dot: 'bg-amber-500', borderLight: 'border-amber-200 dark:border-amber-800' },
        { border: 'border-red-500', bg: 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20', badge: 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300', dot: 'bg-red-500', borderLight: 'border-red-200 dark:border-red-800' },
        { border: 'border-teal-500', bg: 'bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20', badge: 'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300', dot: 'bg-teal-500', borderLight: 'border-teal-200 dark:border-teal-800' },
        { border: 'border-indigo-500', bg: 'bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20', badge: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300', dot: 'bg-indigo-500', borderLight: 'border-indigo-200 dark:border-indigo-800' },
        { border: 'border-orange-500', bg: 'bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20', badge: 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300', dot: 'bg-orange-500', borderLight: 'border-orange-200 dark:border-orange-800' },
        { border: 'border-pink-500', bg: 'bg-gradient-to-br from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20', badge: 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300', dot: 'bg-pink-500', borderLight: 'border-pink-200 dark:border-pink-800' },
        { border: 'border-cyan-500', bg: 'bg-gradient-to-br from-cyan-50 to-sky-50 dark:from-cyan-900/20 dark:to-sky-900/20', badge: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300', dot: 'bg-cyan-500', borderLight: 'border-cyan-200 dark:border-cyan-800' },
        { border: 'border-lime-500', bg: 'bg-gradient-to-br from-lime-50 to-green-50 dark:from-lime-900/20 dark:to-green-900/20', badge: 'bg-lime-100 text-lime-700 dark:bg-lime-900/50 dark:text-lime-300', dot: 'bg-lime-500', borderLight: 'border-lime-200 dark:border-lime-800' },
        { border: 'border-rose-500', bg: 'bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20', badge: 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300', dot: 'bg-rose-500', borderLight: 'border-rose-200 dark:border-rose-800' },
        { border: 'border-violet-500', bg: 'bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20', badge: 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300', dot: 'bg-violet-500', borderLight: 'border-violet-200 dark:border-violet-800' },
        { border: 'border-sky-500', bg: 'bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-900/20 dark:to-blue-900/20', badge: 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300', dot: 'bg-sky-500', borderLight: 'border-sky-200 dark:border-sky-800' },
        { border: 'border-fuchsia-500', bg: 'bg-gradient-to-br from-fuchsia-50 to-pink-50 dark:from-fuchsia-900/20 dark:to-pink-900/20', badge: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/50 dark:text-fuchsia-300', dot: 'bg-fuchsia-500', borderLight: 'border-fuchsia-200 dark:border-fuchsia-800' },
        { border: 'border-yellow-500', bg: 'bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20', badge: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300', dot: 'bg-yellow-500', borderLight: 'border-yellow-200 dark:border-yellow-800' },
    ];
    
    const colorIndex = (typeId - 1) % colorPalette.length;
    return colorPalette[colorIndex];
}

let currentView = 'card';
let currentTypeFilter = 'all';

function setView(view) {
    currentView = view;
    const grid = document.getElementById('activityGrid');
    const timeline = document.getElementById('activityTimeline');
    const btnCard = document.getElementById('viewCard');
    const btnTimeline = document.getElementById('viewTimeline');
    
    if (view === 'card') {
        grid.classList.remove('hidden');
        timeline.classList.add('hidden');
        btnCard.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-800', 'dark:text-white', 'shadow');
        btnCard.classList.remove('text-slate-500', 'dark:text-slate-400');
        btnTimeline.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-800', 'dark:text-white', 'shadow');
        btnTimeline.classList.add('text-slate-500', 'dark:text-slate-400');
    } else {
        grid.classList.add('hidden');
        timeline.classList.remove('hidden');
        btnTimeline.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-800', 'dark:text-white', 'shadow');
        btnTimeline.classList.remove('text-slate-500', 'dark:text-slate-400');
        btnCard.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-800', 'dark:text-white', 'shadow');
        btnCard.classList.add('text-slate-500', 'dark:text-slate-400');
    }
}

function filterByType(typeId) {
    currentTypeFilter = typeId;
    
    // If selecting a specific type, expand filters
    if (typeId !== 'all') {
        const moreFilters = document.getElementById('moreFilters');
        if (moreFilters.classList.contains('hidden')) {
            toggleFilters();
        }
    }
    
    // Type-specific colors (matching getTypeColors)
    const typeColorMap = {
        1: { from: 'from-emerald-500', to: 'to-emerald-600', bg: 'bg-emerald-100', darkBg: 'dark:bg-emerald-900/40', text: 'text-emerald-700', darkText: 'dark:text-emerald-300' },
        2: { from: 'from-blue-500', to: 'to-blue-600', bg: 'bg-blue-100', darkBg: 'dark:bg-blue-900/40', text: 'text-blue-700', darkText: 'dark:text-blue-300' },
        3: { from: 'from-purple-500', to: 'to-purple-600', bg: 'bg-purple-100', darkBg: 'dark:bg-purple-900/40', text: 'text-purple-700', darkText: 'dark:text-purple-300' },
        4: { from: 'from-amber-500', to: 'to-amber-600', bg: 'bg-amber-100', darkBg: 'dark:bg-amber-900/40', text: 'text-amber-700', darkText: 'dark:text-amber-300' },
        5: { from: 'from-red-500', to: 'to-red-600', bg: 'bg-red-100', darkBg: 'dark:bg-red-900/40', text: 'text-red-700', darkText: 'dark:text-red-300' },
        6: { from: 'from-teal-500', to: 'to-teal-600', bg: 'bg-teal-100', darkBg: 'dark:bg-teal-900/40', text: 'text-teal-700', darkText: 'dark:text-teal-300' },
        7: { from: 'from-indigo-500', to: 'to-indigo-600', bg: 'bg-indigo-100', darkBg: 'dark:bg-indigo-900/40', text: 'text-indigo-700', darkText: 'dark:text-indigo-300' },
        8: { from: 'from-orange-500', to: 'to-orange-600', bg: 'bg-orange-100', darkBg: 'dark:bg-orange-900/40', text: 'text-orange-700', darkText: 'dark:text-orange-300' },
        9: { from: 'from-pink-500', to: 'to-pink-600', bg: 'bg-pink-100', darkBg: 'dark:bg-pink-900/40', text: 'text-pink-700', darkText: 'dark:text-pink-300' },
        10: { from: 'from-slate-500', to: 'to-slate-600', bg: 'bg-slate-100', darkBg: 'dark:bg-slate-700', text: 'text-slate-600', darkText: 'dark:text-slate-300' },
        11: { from: 'from-cyan-500', to: 'to-cyan-600', bg: 'bg-cyan-100', darkBg: 'dark:bg-cyan-900/40', text: 'text-cyan-700', darkText: 'dark:text-cyan-300' },
        12: { from: 'from-lime-500', to: 'to-lime-600', bg: 'bg-lime-100', darkBg: 'dark:bg-lime-900/40', text: 'text-lime-700', darkText: 'dark:text-lime-300' },
    };
    const defaultColor = { from: 'from-slate-600', to: 'to-slate-700', bg: 'bg-slate-100', darkBg: 'dark:bg-slate-700', text: 'text-slate-600', darkText: 'dark:text-slate-300' };
    
    // Update button styles
    document.querySelectorAll('.type-filter-btn').forEach(btn => {
        const btnType = btn.dataset.type;
        const btnTypeId = parseInt(btnType);
        const colors = typeColorMap[btnTypeId] || defaultColor;
        
        // Remove all possible gradient classes first
        btn.classList.remove('bg-gradient-to-r', 'from-slate-700', 'to-slate-800', 
            'from-emerald-500', 'to-emerald-600', 'from-blue-500', 'to-blue-600', 
            'from-purple-500', 'to-purple-600', 'from-amber-500', 'to-amber-600',
            'from-red-500', 'to-red-600', 'from-teal-500', 'to-teal-600',
            'from-indigo-500', 'to-indigo-600', 'from-orange-500', 'to-orange-600',
            'from-pink-500', 'to-pink-600', 'from-slate-500', 'to-slate-600',
            'from-cyan-500', 'to-cyan-600', 'from-lime-500', 'to-lime-600',
            'text-white', 'shadow-lg', 'font-bold', 'border-white/30');
        
        if (btnType == typeId || (typeId === 'all' && btnType === 'all')) {
            // Active state - use type-specific gradient
            if (btnType === 'all') {
                btn.classList.add('bg-gradient-to-r', 'from-slate-700', 'to-slate-800', 'text-white', 'shadow-lg', 'font-bold');
            } else {
                btn.classList.add('bg-gradient-to-r', colors.from, colors.to, 'text-white', 'shadow-lg', 'font-bold', 'border-white/30');
            }
            // Remove normal state classes
            btn.classList.remove(colors.bg, colors.darkBg, colors.text, colors.darkText);
        } else {
            // Normal state - restore original colors
            if (btnType !== 'all') {
                btn.classList.add(colors.bg, colors.darkBg, colors.text, colors.darkText);
            } else {
                btn.classList.remove('bg-gradient-to-r', 'from-slate-700', 'to-slate-800', 'text-white', 'shadow-lg', 'font-bold');
                btn.classList.add('bg-slate-200', 'dark:bg-slate-600', 'text-slate-700', 'dark:text-slate-300');
            }
        }
    });
    
    // Filter activities
    const filtered = typeId === 'all' 
        ? allActivities 
        : allActivities.filter(a => a.th_id == typeId);
    
    renderActivities(filtered);
}

let filtersExpanded = false;

function toggleFilters() {
    const moreFilters = document.getElementById('moreFilters');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    
    filtersExpanded = !filtersExpanded;
    
    if (filtersExpanded) {
        moreFilters.classList.remove('hidden');
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
        toggleText.textContent = 'ซ่อน';
    } else {
        moreFilters.classList.add('hidden');
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
        toggleText.textContent = 'แสดงประเภท';
    }
}

function getTypeColor(typeId) {
    return getTypeColors(typeId).badge;
}

function updateStats(activities) {
    const total = activities.length;
    const thisMonth = activities.filter(a => {
        const date = new Date(a.h_date);
        const now = new Date();
        return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
    }).length;
    const uniqueTypes = [...new Set(activities.map(a => a.th_id))].length;
    const withImages = activities.filter(a => a.h_pic1 || a.h_pic2).length;
    
    animateNumber('totalActivities', total);
    animateNumber('thisMonth', thisMonth);
    animateNumber('totalTypes', uniqueTypes);
    animateNumber('withImages', withImages);
}

function animateNumber(elementId, target) {
    const el = document.getElementById(elementId);
    const duration = 800;
    const start = parseInt(el.textContent) || 0;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        el.textContent = Math.round(start + (target - start) * easeOutQuart);
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

function filterActivities() {
    const query = document.getElementById('searchActivity').value.toLowerCase().trim();
    const filtered = allActivities.filter(a => 
        a.h_topic.toLowerCase().includes(query) || 
        a.h_detail.toLowerCase().includes(query) ||
        a.th_name.toLowerCase().includes(query)
    );
    renderActivities(filtered);
}

function convertToThaiDate(dateString) {
    const months = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543;
    return `${day} ${month} ${year}`;
}

// Modal Functions
function openAddModal() {
    document.getElementById('addForm').reset();
    document.getElementById('addPreview1').classList.add('hidden');
    document.getElementById('addPreview2').classList.add('hidden');
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

async function submitAdd() {
    const form = document.getElementById('addForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('api/insert_homeroom.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', timer: 1500, showConfirmButton: false });
            closeAddModal();
            loadActivities();
        } else {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: result.message || '' });
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
    }
}

async function viewActivity(id) {
    try {
        const response = await fetch(`api/fetch_single_homeroom.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const item = data.data[0];
            const date = convertToThaiDate(item.h_date);
            
            document.getElementById('viewModalBody').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <i class="fas fa-calendar-day text-2xl text-sky-500"></i>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">วันที่</p>
                            <p class="font-bold text-slate-800 dark:text-white">${date}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <i class="fas fa-tags text-2xl text-violet-500"></i>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">ประเภท</p>
                            <p class="font-bold text-slate-800 dark:text-white">${item.th_name}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1"><i class="fas fa-heading mr-1"></i> หัวข้อ</p>
                        <p class="font-bold text-slate-800 dark:text-white text-lg">${item.h_topic}</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1"><i class="fas fa-align-left mr-1"></i> รายละเอียด</p>
                        <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap">${item.h_detail}</p>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1"><i class="fas fa-bullseye mr-1"></i> ผลที่คาดหวัง</p>
                        <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap">${item.h_result}</p>
                    </div>
                    ${item.h_pic1 || item.h_pic2 ? `
                    <div class="grid grid-cols-2 gap-4">
                        ${item.h_pic1 ? `<img src="uploads/homeroom/${item.h_pic1}" class="w-full h-40 object-cover rounded-xl shadow">` : ''}
                        ${item.h_pic2 ? `<img src="uploads/homeroom/${item.h_pic2}" class="w-full h-40 object-cover rounded-xl shadow">` : ''}
                    </div>
                    ` : ''}
                </div>
            `;
            document.getElementById('viewModal').classList.remove('hidden');
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'ไม่สามารถโหลดข้อมูลได้' });
    }
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

async function editActivity(id) {
    try {
        const response = await fetch(`api/fetch_single_homeroom.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const item = data.data[0];
            
            let typeOptions = types.map(t => 
                `<option value="${t.th_id}" ${t.th_id == item.th_id ? 'selected' : ''}>${t.th_name}</option>`
            ).join('');
            
            document.getElementById('editModalBody').innerHTML = `
                <form id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="${item.h_id}">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">ประเภท</label>
                        <select name="type" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl">
                            ${typeOptions}
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">หัวข้อ</label>
                        <input type="text" name="title" value="${item.h_topic}" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl">
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">รายละเอียด</label>
                        <textarea name="detail" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl">${item.h_detail}</textarea>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">ผลที่คาดหวัง</label>
                        <textarea name="result" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl">${item.h_result}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">ภาพ 1</label>
                            ${item.h_pic1 ? `<img src="uploads/homeroom/${item.h_pic1}" class="w-full h-24 object-cover rounded-xl mb-2">` : ''}
                            <input type="file" name="image1" accept="image/*" class="w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">ภาพ 2</label>
                            ${item.h_pic2 ? `<img src="uploads/homeroom/${item.h_pic2}" class="w-full h-24 object-cover rounded-xl mb-2">` : ''}
                            <input type="file" name="image2" accept="image/*" class="w-full text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 bg-gray-400 text-white rounded-xl">ยกเลิก</button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold">บันทึก</button>
                    </div>
                </form>
            `;
            
            document.getElementById('editForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                try {
                    const res = await fetch('api/update_homeroom.php', { method: 'POST', body: formData });
                    const result = await res.json();
                    if (result.success) {
                        Swal.fire({ icon: 'success', title: 'อัปเดตสำเร็จ!', timer: 1500, showConfirmButton: false });
                        closeEditModal();
                        loadActivities();
                    } else {
                        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
                }
            });
            
            document.getElementById('editModal').classList.remove('hidden');
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'ไม่สามารถโหลดข้อมูลได้' });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

async function deleteActivity(id) {
    const result = await Swal.fire({
        title: 'ยืนยันการลบ?',
        text: 'คุณต้องการลบกิจกรรมนี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ลบเลย',
        cancelButtonText: 'ยกเลิก'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('api/del_homeroom.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', timer: 1500, showConfirmButton: false });
                loadActivities();
            } else {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
        }
    }
}

function printReport() {
    let teacherSignatures = '<div class="text-right mt-8">';
    teachers.forEach(t => {
        teacherSignatures += `<p class="mb-1">ลงชื่อ...................................ครูที่ปรึกษา</p>`;
        teacherSignatures += `<p class="mb-4">(${t.Teach_name})</p>`;
    });
    teacherSignatures += '</div>';
    
    let tableRows = allActivities.map((item, index) => `
        <tr>
            <td class="border px-2 py-1 text-center">${index + 1}</td>
            <td class="border px-2 py-1 text-center">${convertToThaiDate(item.h_date)}</td>
            <td class="border px-2 py-1">${item.th_name}</td>
            <td class="border px-2 py-1">${item.h_topic}</td>
            <td class="border px-2 py-1">${item.h_detail}</td>
            <td class="border px-2 py-1">${item.h_result}</td>
        </tr>
    `).join('');
    
    const printWindow = window.open('', '', 'width=900,height=700');
    printWindow.document.write(`
        <html>
        <head>
            <title>รายงานกิจกรรมโฮมรูม</title>
            <script src="https://cdn.tailwindcss.com"><\/script>
            <style>
                @media print { @page { size: A4 landscape; margin: 10mm; } }
                body { font-family: 'Sarabun', sans-serif; }
            </style>
        </head>
        <body class="p-4 text-sm">
            <h1 class="text-xl font-bold text-center mb-2">รายงานกิจกรรมโฮมรูม ม.${classValue}/${roomValue}</h1>
            <p class="text-center mb-4">ภาคเรียนที่ ${termValue} ปีการศึกษา ${peeValue}</p>
            <table class="w-full border-collapse border text-sm">
                <thead>
                    <tr class="bg-purple-100">
                        <th class="border px-2 py-1">#</th>
                        <th class="border px-2 py-1">วันที่</th>
                        <th class="border px-2 py-1">ประเภท</th>
                        <th class="border px-2 py-1">หัวข้อ</th>
                        <th class="border px-2 py-1">รายละเอียด</th>
                        <th class="border px-2 py-1">ผลที่คาดหวัง</th>
                    </tr>
                </thead>
                <tbody>${tableRows}</tbody>
            </table>
            ${teacherSignatures}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
