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

<!-- Footer Reference -->
<div class="mt-6 md:mt-10 text-center">
    <div class="inline-flex items-center gap-2 md:gap-3 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 px-4 md:px-6 py-2 md:py-3 rounded-full border border-indigo-200 dark:border-indigo-800 shadow-lg hover:shadow-xl transition-all info-card">
        <span class="text-lg md:text-xl">📄</span>
        <p class="text-slate-700 dark:text-slate-300 font-medium text-xs md:text-sm">
            เอกสารอ้างอิง: วิสัยทัศน์และนโยบาย ปี <?php echo date('Y') + 543 + 1; ?>
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
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
