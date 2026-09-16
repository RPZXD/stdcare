<?php
$pageTitle = $title ?? 'ข้อมูลทั่วไป / ประกาศ';

ob_start();
?>

<!-- Custom Styles for Announce Page -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.7);
    }
    .info-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .info-card:hover {
        transform: translateY(-8px) scale(1.02);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .timeline-connector {
        position: relative;
    }
    .timeline-connector:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 17px;
        top: 40px;
        width: 2px;
        height: calc(100% - 20px);
        background: linear-gradient(to bottom, rgba(99, 102, 241, 0.3), transparent);
    }
    .gradient-text {
        background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.1; transform: scale(1.5); }
    }
</style>

<!-- Hero Header Section -->
<div class="relative mb-6 md:mb-8 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-amber-500/20 to-red-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-purple-500/20 to-pink-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
            <!-- Logo -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-400 to-red-500 rounded-2xl md:rounded-3xl blur-lg opacity-50 pulse-ring"></div>
                <div class="relative w-16 h-16 md:w-24 md:h-24 bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 rounded-2xl md:rounded-3xl flex items-center justify-center shadow-xl floating-icon overflow-hidden">
                    <img src="dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 md:w-20 md:h-20 object-contain">
                </div>
            </div>
            
            <!-- School Info -->
            <div class="text-center md:text-left">
                <h1 class="text-2xl md:text-4xl font-black gradient-text tracking-tight">
                    <?php echo htmlspecialchars($school['name']); ?>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm md:text-base mt-1">
                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                    <?php echo htmlspecialchars($school['location']); ?> • <?php echo htmlspecialchars($school['district']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
    
    <!-- Left Column: Strategies & Steps -->
    <div class="lg:col-span-1 space-y-4 md:space-y-6">
        <!-- Strategies Card -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-3 mb-4 md:mb-6">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-chess text-white text-lg md:text-xl"></i>
                </div>
                <h3 class="text-base md:text-xl font-black text-slate-800 dark:text-white">กลยุทธ์ & ขั้นตอน</h3>
            </div>
            
            <!-- Strategy Timeline -->
            <div class="space-y-3 md:space-y-4">
                <?php foreach ($strategies as $i => $strategy): 
                    $colors = [
                        'emerald' => 'from-emerald-400 to-green-500',
                        'blue' => 'from-blue-400 to-indigo-500',
                        'amber' => 'from-amber-400 to-orange-500'
                    ];
                    $gradient = $colors[$strategy['color']] ?? 'from-gray-400 to-gray-500';
                ?>
                <div class="timeline-connector flex gap-3 pb-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br <?php echo $gradient; ?> rounded-xl flex items-center justify-center text-white font-black text-sm md:text-base shadow-lg flex-shrink-0">
                        <?php echo $i + 1; ?>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white text-sm md:text-base"><?php echo htmlspecialchars($strategy['title']); ?></p>
                        <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($strategy['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Important Steps -->
            <div class="mt-4 md:mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg md:text-xl">✨</span>
                    <h4 class="font-bold text-slate-700 dark:text-slate-300 text-sm md:text-base">ขั้นตอนสำคัญ 5 ขั้นตอน</h4>
                </div>
                <ol class="space-y-2">
                    <?php foreach ($steps as $i => $step): ?>
                    <li class="flex items-center gap-2 text-xs md:text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer group">
                        <span class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-[10px] font-bold group-hover:bg-indigo-500 group-hover:text-white transition-all">
                            <?php echo $i + 1; ?>
                        </span>
                        <span><?php echo htmlspecialchars($step); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- Center Column: Vision, Mission, Goals -->
    <div class="lg:col-span-1 space-y-4 md:space-y-6">
        <!-- Vision Card -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border-l-4 md:border-l-8 border-amber-400 shadow-xl info-card bg-gradient-to-r from-amber-50/50 to-orange-50/50 dark:from-amber-900/20 dark:to-orange-900/20">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl md:text-3xl">🎯</span>
                <h3 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">วิสัยทัศน์</h3>
            </div>
            <p class="text-base md:text-xl text-slate-700 dark:text-slate-300 italic font-medium leading-relaxed">
                "<?php echo htmlspecialchars($vision); ?>"
            </p>
        </div>
        
        <!-- Mission & Goals Grid -->
        <div class="grid grid-cols-1 gap-4">
            <!-- Mission -->
            <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-bullseye text-white text-sm md:text-base"></i>
                    </div>
                    <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">พันธกิจ</h4>
                </div>
                <ul class="space-y-2">
                    <?php foreach ($missions as $i => $mission): ?>
                    <li class="flex items-start gap-2 text-xs md:text-sm text-slate-600 dark:text-slate-400">
                        <span class="w-5 h-5 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                            <?php echo $i + 1; ?>
                        </span>
                        <span><?php echo htmlspecialchars($mission); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Goals -->
            <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-flag-checkered text-white text-sm md:text-base"></i>
                    </div>
                    <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">เป้าหมาย</h4>
                </div>
                <ul class="space-y-2">
                    <?php foreach ($goals as $i => $goal): ?>
                    <li class="flex items-start gap-2 text-xs md:text-sm text-slate-600 dark:text-slate-400">
                        <span class="w-5 h-5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                            <?php echo $i + 1; ?>
                        </span>
                        <span><?php echo htmlspecialchars($goal); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Identity, Uniqueness, Values -->
    <div class="lg:col-span-1 space-y-4 md:space-y-6">
        <!-- Identity -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card bg-gradient-to-br from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl md:text-2xl">🎯</span>
                <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">อัตลักษณ์</h4>
            </div>
            <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 font-medium">
                <?php echo htmlspecialchars($identity); ?>
            </p>
        </div>
        
        <!-- Uniqueness -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card bg-gradient-to-br from-pink-50/50 to-rose-50/50 dark:from-pink-900/20 dark:to-rose-900/20">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl md:text-2xl">⭐</span>
                <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">เอกลักษณ์</h4>
            </div>
            <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 font-medium">
                <?php echo htmlspecialchars($uniqueness); ?>
            </p>
        </div>
        
        <!-- Values & Culture -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl md:text-2xl">💎</span>
                <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">ค่านิยม & วัฒนธรรม</h4>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php 
                $valueColors = [
                    ['from' => 'from-amber-100', 'to' => 'to-amber-200', 'text' => 'text-amber-800', 'dark' => 'dark:bg-amber-900/30 dark:text-amber-300'],
                    ['from' => 'from-emerald-100', 'to' => 'to-emerald-200', 'text' => 'text-emerald-800', 'dark' => 'dark:bg-emerald-900/30 dark:text-emerald-300'],
                    ['from' => 'from-blue-100', 'to' => 'to-blue-200', 'text' => 'text-blue-800', 'dark' => 'dark:bg-blue-900/30 dark:text-blue-300']
                ];
                foreach ($values as $i => $value): 
                    $color = $valueColors[$i % count($valueColors)];
                ?>
                <span class="px-3 py-1.5 bg-gradient-to-r <?php echo $color['from']; ?> <?php echo $color['to']; ?> <?php echo $color['text']; ?> <?php echo $color['dark']; ?> rounded-full text-xs md:text-sm font-bold shadow-sm hover:scale-105 transition-transform cursor-pointer">
                    ✓ <?php echo htmlspecialchars($value); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Core Competencies -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl md:text-2xl">📊</span>
                <h4 class="text-base md:text-lg font-black text-slate-800 dark:text-white">สมรรถนะหลัก</h4>
            </div>
            <p class="text-xs md:text-sm text-slate-600 dark:text-slate-400 mb-3">ระบบการบริหารจัดการที่มีคุณภาพ</p>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 md:h-4 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-400 via-purple-500 to-pink-500 h-full rounded-full relative" style="width: 100%">
                    <div class="absolute inset-0 shimmer"></div>
                </div>
            </div>
            <p class="text-right text-[10px] md:text-xs text-slate-500 mt-1 font-bold">100%</p>
        </div>
    </div>
</div>

<!-- 5 ใจ 1-G MODEL & Student Care System Section -->
<div class="mt-8 md:mt-12 space-y-6 md:space-y-8">
    <!-- Model Showcase Card with Image -->
    <div class="glass-card rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl info-card">
        <div class="flex flex-col lg:flex-row items-center gap-8">
            <!-- Image with zoom overlay -->
            <div class="w-full lg:w-3/5 flex flex-col items-center">
                <div class="relative group cursor-pointer overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-md transition-all duration-300 hover:shadow-xl w-full flex justify-center bg-slate-50 dark:bg-slate-900/50" onclick="openModelModal()">
                    <img src="dist/img/5jai-1g-model.png" alt="5 ใจ 1-G MODEL โรงเรียนพิชัย" class="w-full max-w-2xl h-auto object-contain transition-transform duration-500 group-hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="px-4 py-2 bg-white/90 dark:bg-slate-800/90 backdrop-blur text-slate-800 dark:text-white rounded-full text-sm font-semibold shadow flex items-center gap-2">
                            <i class="fas fa-search-plus text-indigo-600"></i> คลิกเพื่อดูภาพขยาย
                        </span>
                    </div>
                </div>
                <button type="button" onclick="openModelModal()" class="mt-3 text-xs md:text-sm text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1.5 font-medium">
                    <i class="fas fa-expand-alt"></i> เปิดดูรูปภาพ 5 ใจ 1-G MODEL ขนาดเต็ม
                </button>
            </div>

            <!-- Description / Highlights of the Model -->
            <div class="w-full lg:w-2/5 flex flex-col justify-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-3 w-fit">
                    <i class="fas fa-sitemap"></i> โมเดลการขับเคลื่อนคุณภาพ
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-4">
                    5 ใจ 1-G MODEL
                </h2>
                <p class="text-sm md:text-base text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                    กรอบแนวทางการดำเนินงานระบบการดูแลช่วยเหลือนักเรียนของโรงเรียนพิชัยที่เชื่อมโยงนโยบายสู่การปฏิบัติอย่างเป็นรูปธรรม
                </p>

                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900/30">
                        <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            IN
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wide">INPUT &bull; ปัจจัยนำเข้า</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">นโยบาย สพฐ. / นโยบาย สพม. พิษณุโลก อุตรดิตถ์ / นโยบาย วิสัยทัศน์ พันธกิจ คุณธรรมอัตลักษณ์ รร.พิชัย</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            5 ใจ
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wide">PROCESS &bull; กระบวนการ 5 ใจ</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">ใส่ใจ &bull; เข้าใจ &bull; พร้อมใจ &bull; เชื่อใจ &bull; มั่นใจ</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/30">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            1-G
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wide">OUTPUT &bull; ผลลัพธ์</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5"><span class="font-bold text-emerald-700 dark:text-emerald-400">GOOD STUDENT</span> พัฒนาผู้เรียนให้เป็นคนดี มีคุณธรรม และศักยภาพรอบด้าน</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Governance & Structure Section -->
    <div class="glass-card rounded-2xl md:rounded-3xl p-6 md:p-10 border border-white/30 dark:border-slate-700/50 shadow-2xl info-card">
        <div class="max-w-4xl mx-auto text-center mb-8">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-3">
                <i class="fas fa-clipboard-list"></i> แนวทางการดำเนินงาน
            </span>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-4">
                แนวทางการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน
            </h2>
            <p class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed text-justify md:text-center">
                โรงเรียนพิชัยมีการแต่งตั้งคณะกรรมการอำนวยการ (ทีมนำ) คณะกรรมการประสานงาน (ทีมประสาน) คณะกรรมการดำเนินการ (ทีมทำ) ในการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน มีปฏิทินปฏิบัติงานระบบการดูแลช่วยเหลือนักเรียนแนวทางการดำเนินงาน ครูและบุคลากรมีความรู้ความเข้าใจในวิธีการกระบวนการและขั้นตอนดำเนินงานระบบการดูแลช่วยเหลือนักเรียนในสถานศึกษาทั้ง 5 ขั้นตอนภายใต้หลัก <span class="font-bold text-indigo-600 dark:text-indigo-400">5 ใจ - 1 G โมเดล</span> โดยมีการดำเนินงานสอดคล้องเป็นระบบ ดังนี้
            </p>
        </div>

        <!-- 3 Teams (ทีมนำ / ทีมประสาน / ทีมทำ) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-5xl mx-auto pt-2 border-t border-slate-100 dark:border-slate-700/60">
            <div class="p-5 rounded-2xl bg-white/60 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                    <i class="fas fa-chess-king"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">บทบาทที่ 1</div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมนำ</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการอำนวยการ กำหนดนโยบาย ทิศทาง และสนับสนุนการดำเนินงาน</p>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white/60 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">บทบาทที่ 2</div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมประสาน</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการประสานงาน เชื่อมโยงข้อมูล บุคลากร ฝ่ายต่างๆ และหน่วยงานภายนอก</p>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white/60 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">บทบาทที่ 3</div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมทำ</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการดำเนินการ ระดับสายชั้น (หัวหน้าระดับ, รองหัวหน้าระดับ, เลขานุการ, ครูที่ปรึกษา)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Steps Detailed Guide Cards -->
    <div class="space-y-4 md:space-y-6">
        <!-- Step 1: การรู้จักนักเรียนเป็นรายบุคคล (ใส่ใจ) -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-7 border-l-4 md:border-l-8 border-red-500 border-t border-r border-b border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center font-black text-sm">
                        1
                    </span>
                    <div>
                        <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">1. การรู้จักนักเรียนเป็นรายบุคคล (ใส่ใจ)</h3>
                        <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">รู้รอบกรอบบุคคล</span>
                    </div>
                </div>
                <a href="https://std.phichai.ac.th/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 hover:bg-red-100 text-xs font-bold transition-colors w-fit">
                    <i class="fas fa-globe"></i> https://std.phichai.ac.th/
                </a>
            </div>
            <div class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed text-justify">
                <p>
                    โรงเรียนได้สร้างระบบโปรแกรมออนไลน์ STDCARE <a href="https://std.phichai.ac.th/" target="_blank" class="text-red-600 dark:text-red-400 font-semibold underline underline-offset-2">https://std.phichai.ac.th/</a> เพื่อเก็บข้อมูลนักเรียนเป็นรายบุคคลข้อมูลพื้นฐานของนักเรียน สามารถนำข้อมูลมาวิเคราะห์ เพื่อการคัดกรองนักเรียน เป็นประโยชน์ในการส่งเสริม การป้องกันและแก้ไขปัญหานักเรียนได้อย่างทั่วถึง ถ้าหากนักเรียนมีปัญหาจะได้ดำเนินการช่วยเหลือได้ทันเวลา
                </p>
            </div>
        </div>

        <!-- Step 2: การคัดกรองนักเรียน (เข้าใจ) -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-7 border-l-4 md:border-l-8 border-blue-500 border-t border-r border-b border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center font-black text-sm">
                    2
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">2. การคัดกรองนักเรียน (เข้าใจ)</h3>
                    <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">กรองกมลบูรณาการ 11 ด้าน</span>
                </div>
            </div>

            <div class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed space-y-3.5 text-justify">
                <p>
                    การคัดกรองนักเรียนด้วยการบูรณาการ (เข้าใจ) มีการคัดกรองนักเรียน ด้วยโปรแกรมระบบการดูแลช่วยเหลือนักเรียน STDCARE มีข้อมูลในการวางแผนดูแลช่วยเหลือนักเรียนทั้งสิ้น 11 ด้าน การคัดกรองนักเรียนเป็นการพิจารณาข้อมูลที่เกี่ยวกับตัวนักเรียนเพื่อการจัดกลุ่มนักเรียน อาจนิยามกลุ่มได้ 4 กลุ่ม คือ
                </p>

                <!-- 4 Groups Definition Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40">
                        <div class="flex items-center gap-2 font-bold text-emerald-800 dark:text-emerald-300 mb-1 text-xs md:text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            กลุ่มปกติ
                        </div>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            คือ นักเรียนที่ได้รับการวิเคราะห์ข้อมูลต่าง ๆ ตามเกณฑ์การคัดกรองของโรงเรียนแล้วอยู่ในเกณฑ์ของกลุ่มปกติ ซึ่งควรได้รับการสร้างเสริมภูมิคุ้มกันและการส่งเสริมพัฒนา
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40">
                        <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300 mb-1 text-xs md:text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                            กลุ่มเสี่ยง
                        </div>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            คือ นักเรียนที่จัดอยู่ในเกณฑ์ของกลุ่มเสี่ยงตามเกณฑ์การคัดกรองของโรงเรียนซึ่งโรงเรียนต้องให้การป้องกันหรือแก้ไขปัญหาตามแต่กรณี
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40">
                        <div class="flex items-center gap-2 font-bold text-rose-800 dark:text-rose-300 mb-1 text-xs md:text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                            กลุ่มมีปัญหา
                        </div>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            คือ นักเรียนที่จัดอยู่ในเกณฑ์ของกลุ่มมีปัญหาตามเกณฑ์การคัดกรองของโรงเรียนซึ่งโรงเรียนต้องช่วยเหลือและแก้ปัญหาโดยเร่งด่วน
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-purple-50/70 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40">
                        <div class="flex items-center gap-2 font-bold text-purple-800 dark:text-purple-300 mb-1 text-xs md:text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>
                            กลุ่มพิเศษ
                        </div>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            คือ นักเรียนที่มีความสามารถพิเศษ มีความเป็นอัจฉริยะ แสดงออกซึ่งความสามารถอันโดดเด่นด้านใดด้านหนึ่งหรือหลายด้าน อย่างเป็นที่ประจักษ์เมื่อเทียบกับผู้มีอายุในระดับเดียวกันภายใต้สภาพแวดล้อมเดียวกัน ซึ่งโรงเรียนต้องให้การส่งเสริมนักเรียนได้พัฒนาศักยภาพความสามารถพิเศษนั้นจนถึงขั้นสูงสุด
                        </p>
                    </div>
                </div>

                <p class="pt-2 text-xs text-slate-600 dark:text-slate-300 bg-slate-50/80 dark:bg-slate-900/30 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                    <i class="fas fa-info-circle text-blue-500 mr-1.5"></i>
                    <strong>ประโยชน์ของการจัดกลุ่ม:</strong> การจัดกลุ่มนักเรียนนี้มีประโยชน์ต่อครูที่ปรึกษาในการหาวิธีการเพื่อดูแลช่วยเหลือนักเรียนได้อย่างถูกต้อง โดยเฉพาะการแก้ไขปัญหาให้ตรงกับปัญหาของนักเรียนยิ่งขึ้น และมีความรวดเร็วในการแก้ไขปัญหาเพราะมีข้อมูลของนักเรียนในด้านต่าง ๆ ซึ่งหากครูที่ปรึกษาไม่ได้คัดกรองนักเรียนเพื่อการจัดกลุ่มแล้ว ความชัดเจนในเป้าหมายเพื่อการแก้ไขปัญหาของนักเรียนจะมีน้อยลง มีผลต่อความรวดเร็วในการช่วยเหลือ ซึ่งบางกรณีจำเป็นต้องแก้ไขโดยเร่งด่วน
                </p>
            </div>
        </div>

        <!-- Step 3: การส่งเสริม และพัฒนานักเรียน / ป้องกันและแก้ไขปัญหา (พร้อมใจ) -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-7 border-l-4 md:border-l-8 border-amber-500 border-t border-r border-b border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center font-black text-sm">
                    3
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">3. การป้องกันและแก้ไขปัญหา (พร้อมใจ)</h3>
                    <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">ประสานเสริมให้พัฒนา</span>
                </div>
            </div>

            <div class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed space-y-3 text-justify">
                <p>
                    การส่งเสริม และพัฒนานักเรียน (พร้อมใจ) นำข้อมูลในการวางแผนการจัดกิจกรรมที่ตอบสนองต่อความต้องการดูแลช่วยเหลือได้อย่างเหมาะสมกับกลุ่มปกติ กลุ่มเสี่ยง และกลุ่มมีปัญหา โดยในกลุ่มปกติ สามารถนำข้อมูลวางแนวทางจัดกิจกรรมที่จะส่งเสริมคุณลักษณะด้านต่างๆ ตามความถนัดและความสนใจของนักเรียน
                </p>
                <p>
                    ในการดูแลช่วยเหลือนักเรียน ครูควรให้ความเอาใจใส่กับนักเรียนทุกคนอย่างเท่าเทียมกันแต่สำหรับนักเรียนกลุ่มเสี่ยง/มีปัญหานั้น จำเป็นอย่างมากที่ต้องให้ความดูแลเอาใจใส่อย่างใกล้ชิดและหาวิธีการช่วยเหลือทั้งการป้องกันและการแก้ไขปัญหา โดยไม่ปล่อยปละละเลยนักเรียนจนกลายเป็นปัญหาของสังคม การสร้างภูมิคุ้มกัน การป้องกันและแก้ไขปัญหาของนักเรียน จึงเป็นภาระงานที่ยิ่งใหญ่และมีคุณค่าอย่างมากในการพัฒนาให้นักเรียนเติบโตเป็นบุคคลที่มีคุณภาพของสังคมต่อไป
                </p>
                
                <div class="mt-3 p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-800/40">
                    <h4 class="font-bold text-amber-800 dark:text-amber-300 text-xs md:text-sm mb-1.5 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> สิ่งที่ครูประจำชั้น/ครูที่ปรึกษาจำเป็นต้องดำเนินการอย่างน้อย 2 ประการ:
                    </h4>
                    <ul class="space-y-1 text-xs text-slate-700 dark:text-slate-300 pl-4 list-disc">
                        <li><strong>1. การให้คำปรึกษาเบื้องต้น</strong></li>
                        <li><strong>2. การจัดกิจกรรมเพื่อป้องกันและแก้ไขปัญหา</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 4: การป้องกันและแก้ไขปัญหาอย่างเป็นระบบ (เชื่อใจ) -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-7 border-l-4 md:border-l-8 border-purple-500 border-t border-r border-b border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center font-black text-sm">
                    4
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">4. การป้องกันและแก้ไขปัญหาอย่างเป็นระบบ (เชื่อใจ)</h3>
                    <span class="text-xs font-semibold text-purple-500 uppercase tracking-wider">คลายปัญหาเป็นระบบ</span>
                </div>
            </div>

            <div class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed text-justify">
                <p>
                    มีคำสั่งแต่งตั้งคณะทำงานเกี่ยวกับระบบการดูแลช่วยเหลือนักเรียน การเสริมสร้างทักษะชีวิตและการคุ้มครองนักเรียนในแต่ละปีการศึกษา แต่งตั้งครูและบุคลากรในตำแหน่งต่างๆ แบ่งการทำงานเป็นระดับสายชั้น ประกอบด้วยหัวหน้าระดับ รองหัวหน้าระดับ เลขานุการ และครูที่ปรึกษา โดยมีทีมนำ ทีมประสานและทีมทำ เพื่อให้มีการขับเคลื่อนงานระบบการดูแลช่วยเหลือนักเรียนได้อย่างมีประสิทธิภาพ มีความทั่วถึงและต่อเนื่อง โรงเรียนมีการดำเนินการดูแลช่วยเหลือนักเรียนในด้านต่างๆ อย่างเป็นระบบ
                </p>
            </div>
        </div>

        <!-- Step 5: การส่งต่อนักเรียนอย่างมีคุณภาพ (มั่นใจ) -->
        <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-7 border-l-4 md:border-l-8 border-emerald-500 border-t border-r border-b border-white/30 dark:border-slate-700/50 shadow-xl info-card">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center font-black text-sm">
                    5
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">5. การส่งต่อนักเรียนอย่างมีคุณภาพ (มั่นใจ)</h3>
                    <span class="text-xs font-semibold text-emerald-500 uppercase tracking-wider">เมื่อพานพบรีบส่งต่อ</span>
                </div>
            </div>

            <div class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed space-y-3.5 text-justify">
                <p>
                    มีการดำเนินการส่งต่อให้ผู้เชี่ยวชาญเฉพาะด้าน โดยการส่งต่อนักเรียนแบ่งเป็น 2 กรณี คือ:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="p-3.5 rounded-2xl bg-white/60 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-xs md:text-sm font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center text-xs"><i class="fas fa-arrow-right"></i></span>
                            การส่งต่อภายใน
                        </h4>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            ครูที่ปรึกษาส่งต่อไปยังครูที่สามารถให้การช่วยเหลือนักเรียนได้ ทั้งนี้ขึ้นอยู่กับลักษณะปัญหา
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white/60 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-xs md:text-sm font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center text-xs"><i class="fas fa-external-link-alt"></i></span>
                            การส่งต่อภายนอก
                        </h4>
                        <p class="text-[11px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            ครูแนะแนวหรือฝ่ายกิจการนักเรียนเป็นผู้ดำเนินการส่งต่อไปยังผู้เชี่ยวชาญภายนอกในการส่งต่อนักเรียนส่วนใหญ่ของโรงเรียนพิชัย
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Development, Promotion & Good Student (1G) -->
    <div class="glass-card rounded-2xl md:rounded-3xl p-6 md:p-10 border border-white/30 dark:border-slate-700/50 shadow-2xl info-card bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg md:text-xl shadow-md shadow-indigo-500/30">
                    <i class="fas fa-seedling"></i>
                </div>
                <div>
                    <h3 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">การพัฒนาและส่งเสริมผู้เรียน</h3>
                    <p class="text-xs md:text-sm text-indigo-600 dark:text-indigo-400 font-medium">การสนับสนุนผู้เรียนให้พัฒนาเต็มศักยภาพอย่างต่อเนื่อง</p>
                </div>
            </div>

            <p class="text-slate-600 dark:text-slate-300 text-xs md:text-sm leading-relaxed text-justify mb-6">
                การพัฒนาและส่งเสริมนักเรียนเป็นการสนับสนุนให้นักเรียนทุกคน ไม่ว่าจะเป็นนักเรียนกลุ่มปกติหรือกลุ่มเสี่ยง/มีปัญหา กลุ่มความสามารถพิเศษ ให้มีคุณภาพมากขึ้น ได้พัฒนาเต็มศักยภาพมีความภาคภูมิใจในตนเองในด้านต่าง ๆ ซึ่งจะช่วยป้องกันมิให้นักเรียนที่อยู่ในกลุ่มปกติและกลุ่มพิเศษกลายเป็นนักเรียนกลุ่มเสี่ยง/มีปัญหา และเป็นการช่วยให้นักเรียนกลุ่มเสี่ยง/มีปัญหากลับมาเป็นนักเรียนกลุ่มปกติและมีคุณภาพตามมาตรฐานที่โรงเรียนหรือชุมชนคาดหวังต่อไป
            </p>

            <!-- 4 Core Activities -->
            <div class="bg-white/80 dark:bg-slate-900/50 rounded-2xl p-4 md:p-6 border border-indigo-100 dark:border-slate-700/60 mb-6">
                <h4 class="font-bold text-slate-800 dark:text-white text-xs md:text-sm mb-3.5 flex items-center gap-2">
                    <i class="fas fa-tasks text-indigo-600 dark:text-indigo-400"></i>
                    กิจกรรมหลักสำคัญที่โรงเรียนต้องดำเนินการ (4 กิจกรรม):
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">1</span>
                        <span class="font-semibold text-xs md:text-sm text-slate-700 dark:text-slate-200">การจัดกิจกรรมโฮมรูม</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">2</span>
                        <span class="font-semibold text-xs md:text-sm text-slate-700 dark:text-slate-200">การเยี่ยมบ้าน</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">3</span>
                        <span class="font-semibold text-xs md:text-sm text-slate-700 dark:text-slate-200">การจัดประชุมผู้ปกครองชั้นเรียน (Classroom Meeting)</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">4</span>
                        <span class="font-semibold text-xs md:text-sm text-slate-700 dark:text-slate-200">การจัดกิจกรรมเสริมสร้างทักษะการดำรงชีวิตและกิจกรรมพัฒนาผู้เรียน</span>
                    </div>
                </div>
            </div>

            <!-- Good Student (1G) Result Card -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 rounded-2xl md:rounded-3xl p-5 md:p-7 text-white shadow-xl shadow-emerald-600/20">
                <div class="flex flex-col md:flex-row items-center gap-5">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center border-2 border-white/30 flex-shrink-0">
                        <i class="fas fa-heart text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <span class="px-3 py-1 rounded-full bg-white/20 text-white font-bold text-[10px] md:text-xs uppercase tracking-wider mb-2 inline-block">
                            OUTPUT &bull; ผลผลิตของโมเดล
                        </span>
                        <h3 class="text-xl md:text-2xl font-black mb-2">
                            GOOD STUDENT (1G)
                        </h3>
                        <p class="text-emerald-50 text-xs md:text-sm leading-relaxed mb-3">
                            จากการดำเนินงานทำให้ผู้เรียนมี Good Student (1G) ได้แก่:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-emerald-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> มีสุขภาพกาย สุขภาพจิต และสุขลักษณะนิสัยที่ดี
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> มีทักษะในการหลีกเลี่ยง ป้องกันภัยอันตราย และพฤติกรรมที่ไม่พึงประสงค์
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> รักและเห็นคุณค่าในตนเองและผู้อื่น
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> สามารถจัดการกับปัญหาและอารมณ์ของตนเองได้
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> เป็นสมาชิกที่ดีของครอบครัว โรงเรียน ชุมชน และสังคม
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-white flex-shrink-0"></i> มีเจตคติที่ดี และมีทักษะพื้นฐานในการประกอบอาชีพสุจริต
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lightbox for 5 ใจ 1-G MODEL Image -->
<div id="modelImageModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300" onclick="if(event.target === this) closeModelModal()">
    <div class="relative max-w-5xl w-full bg-white dark:bg-slate-800 rounded-3xl p-4 md:p-6 shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i class="fas fa-project-diagram text-sm"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold text-slate-800 dark:text-white">5 ใจ 1-G MODEL - แผนผังระบบดูแลช่วยเหลือนักเรียน</h3>
            </div>
            <button type="button" onclick="closeModelModal()" class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-2 max-h-[78vh] overflow-auto">
            <img src="dist/img/5jai-1g-model.png" alt="5 ใจ 1-G MODEL" class="w-full h-auto max-h-[72vh] object-contain rounded-xl shadow-sm">
        </div>
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <span>กรอบการดำเนินงาน: INPUT (3 นโยบาย) &rarr; PROCESS (5 ใจ) &rarr; OUTPUT (GOOD STUDENT)</span>
            <a href="dist/img/5jai-1g-model.png" target="_blank" download="5jai-1g-model.png" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 font-semibold transition-colors flex items-center gap-1.5">
                <i class="fas fa-download"></i> ดาวน์โหลดภาพ
            </a>
        </div>
    </div>
</div>

<!-- Footer Reference -->
<div class="mt-6 md:mt-10 text-center">
    <div class="inline-flex items-center gap-2 md:gap-3 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 px-4 md:px-6 py-2 md:py-3 rounded-full border border-indigo-200 dark:border-indigo-800 shadow-lg hover:shadow-xl transition-all info-card">
        <span class="text-lg md:text-xl">📄</span>
        <p class="text-slate-700 dark:text-slate-300 font-medium text-xs md:text-sm">
            เอกสารอ้างอิง: วิสัยทัศน์ นโยบาย และแนวทางการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน ปี <?php echo date('Y') + 543 + 1; ?>
        </p>
        <span class="text-lg md:text-xl">✨</span>
    </div>
</div>



<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function exportToPDF() {
    Swal.fire({
        title: '📄 กำลังสร้าง PDF...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const content = document.querySelector('main');
        const canvas = await html2canvas(content, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#f8fafc',
            logging: false
        });
        
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });
        
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const imgProps = { width: canvas.width, height: canvas.height };
        const ratio = Math.min(pageWidth / imgProps.width, (pageHeight - 40) / imgProps.height);
        const imgWidth = imgProps.width * ratio;
        const imgHeight = imgProps.height * ratio;
        
        pdf.addImage(imgData, 'JPEG', (pageWidth - imgWidth) / 2, 20, imgWidth, imgHeight);
        pdf.save('วิสัยทัศน์_โรงเรียนพิชัย.pdf');
        
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: 'ดาวน์โหลดไฟล์ PDF เรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message
        });
    }
}

// Add hover effects
document.querySelectorAll('.info-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px) scale(1.02)';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});

// Modal functions for 5 ใจ 1-G MODEL
function openModelModal() {
    const modal = document.getElementById('modelImageModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeModelModal() {
    const modal = document.getElementById('modelImageModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModelModal();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
