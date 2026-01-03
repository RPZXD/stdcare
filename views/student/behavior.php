<?php
/**
 * View: Student Behavior
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();

// Determine score color
if ($netScore >= 80) {
    $scoreColor = 'emerald';
    $scoreGradient = 'from-emerald-500 to-green-600';
    $scoreBg = '#d1fae5';
    $scoreText = '#059669';
} elseif ($netScore >= 60) {
    $scoreColor = 'blue';
    $scoreGradient = 'from-blue-500 to-indigo-600';
    $scoreBg = '#dbeafe';
    $scoreText = '#2563eb';
} elseif ($netScore >= 40) {
    $scoreColor = 'amber';
    $scoreGradient = 'from-amber-500 to-orange-600';
    $scoreBg = '#fef3c7';
    $scoreText = '#d97706';
} else {
    $scoreColor = 'red';
    $scoreGradient = 'from-red-500 to-rose-600';
    $scoreBg = '#fee2e2';
    $scoreText = '#dc2626';
}
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br <?= $scoreGradient ?> shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-star text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">คะแนนพฤติกรรม</h1>
                        <p class="text-white/80 font-bold">ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?></p>
                    </div>
                </div>
                
                <!-- Score Display -->
                <div class="text-center">
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white/50 shadow-lg">
                        <span class="text-4xl md:text-5xl font-black text-white"><?= $netScore ?></span>
                    </div>
                    <p class="mt-2 text-white/80 font-bold text-sm">คะแนนคงเหลือ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Breakdown Cards -->
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <!-- Deduction -->
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-minus text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black text-red-600"><?= $deductionPoints ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">หักคะแนน</p>
        </div>
        
        <!-- Bonus -->
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black text-emerald-600"><?= $bonusPoints ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">จิตอาสา</p>
        </div>
        
        <!-- Net Score -->
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br <?= $scoreGradient ?> rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-calculator text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black" style="color: <?= $scoreText ?>;"><?= $netScore ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">คะแนนสุทธิ</p>
        </div>
    </div>

    <!-- Calculation Formula -->
    <div class="glass-effect rounded-2xl p-4 border border-white/50 shadow-lg">
        <div class="flex flex-wrap items-center justify-center gap-2 text-sm md:text-base font-bold">
            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg">100</span>
            <span class="text-red-500">−</span>
            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-lg"><?= $deductionPoints ?></span>
            <span class="text-emerald-500">+</span>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg"><?= $bonusPoints ?></span>
            <span class="text-slate-400">=</span>
            <span class="px-3 py-1 rounded-lg font-black" style="background: <?= $scoreBg ?>; color: <?= $scoreText ?>;"><?= $netScore ?></span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-effect rounded-2xl p-2 border border-white/50 shadow-lg">
        <div class="grid grid-cols-2 gap-1" id="tabNav">
            <button class="tab-btn active px-3 py-3 rounded-xl font-bold text-xs md:text-base transition-all text-center" data-tab="deduction">
                <i class="fas fa-minus-circle block md:inline mb-1 md:mb-0 md:mr-1 text-base text-red-500"></i>
                <span class="block md:inline">หักคะแนน (<?= count($deductionRecords) ?>)</span>
            </button>
            <button class="tab-btn px-3 py-3 rounded-xl font-bold text-xs md:text-base transition-all text-center" data-tab="bonus">
                <i class="fas fa-plus-circle block md:inline mb-1 md:mb-0 md:mr-1 text-base text-emerald-500"></i>
                <span class="block md:inline">จิตอาสา (<?= count($bonusRecords) ?>)</span>
            </button>
        </div>
    </div>

    <!-- Tab 1: Deduction Records -->
    <div id="tab-deduction" class="tab-content">
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-exclamation-triangle text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">รายการหักคะแนน</h3>
                    <p class="text-[10px] font-bold text-red-200 uppercase tracking-widest"><?= count($deductionRecords) ?> รายการ</p>
                </div>
            </div>

            <?php if (count($deductionRecords) > 0): ?>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">วันที่</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ประเภท</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">รายละเอียด</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">คะแนน</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ครูผู้บันทึก</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach ($deductionRecords as $b): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 text-center text-sm text-slate-600 dark:text-slate-400"><?= thai_date($b['behavior_date']) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg text-xs font-bold"><?= htmlspecialchars($b['behavior_type']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($b['behavior_name']) ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm font-black">-<?= abs($b['behavior_score']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($b['teacher_behavior']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden p-3 space-y-3">
                <?php foreach ($deductionRecords as $b): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border-l-4 border-red-500">
                    <div class="px-4 py-3 bg-red-50 dark:bg-red-900/20 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300"><?= thai_date($b['behavior_date']) ?></span>
                        <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm font-black">-<?= abs($b['behavior_score']) ?></span>
                    </div>
                    <div class="p-4">
                        <span class="inline-block px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg text-xs font-bold mb-2"><?= htmlspecialchars($b['behavior_type']) ?></span>
                        <p class="font-bold text-slate-800 dark:text-white mb-2"><?= htmlspecialchars($b['behavior_name']) ?></p>
                        <p class="text-xs text-slate-500"><i class="fas fa-user-tie mr-1"></i> <?= htmlspecialchars($b['teacher_behavior']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-4xl text-emerald-500"></i>
                </div>
                <p class="text-emerald-600 font-bold text-lg">ยอดเยี่ยม! 🎉</p>
                <p class="text-slate-400 text-sm mt-2">ไม่มีรายการหักคะแนน</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Bonus Records -->
    <div id="tab-bonus" class="tab-content hidden">
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-heart text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">รายการจิตอาสา</h3>
                    <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest"><?= count($bonusRecords) ?> รายการ</p>
                </div>
            </div>

            <?php if (count($bonusRecords) > 0): ?>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">วันที่</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ประเภท</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">รายละเอียด</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">คะแนน</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ครูผู้บันทึก</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach ($bonusRecords as $b): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 text-center text-sm text-slate-600 dark:text-slate-400"><?= thai_date($b['behavior_date']) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-xs font-bold"><?= htmlspecialchars($b['behavior_type']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($b['behavior_name']) ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-sm font-black">+<?= abs($b['behavior_score']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($b['teacher_behavior']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden p-3 space-y-3">
                <?php foreach ($bonusRecords as $b): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border-l-4 border-emerald-500">
                    <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300"><?= thai_date($b['behavior_date']) ?></span>
                        <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-sm font-black">+<?= abs($b['behavior_score']) ?></span>
                    </div>
                    <div class="p-4">
                        <span class="inline-block px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-xs font-bold mb-2"><?= htmlspecialchars($b['behavior_type']) ?></span>
                        <p class="font-bold text-slate-800 dark:text-white mb-2"><?= htmlspecialchars($b['behavior_name']) ?></p>
                        <p class="text-xs text-slate-500"><i class="fas fa-user-tie mr-1"></i> <?= htmlspecialchars($b['teacher_behavior']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-heart text-4xl text-amber-500"></i>
                </div>
                <p class="text-amber-600 font-bold text-lg">ยังไม่มีคะแนนจิตอาสา</p>
                <p class="text-slate-400 text-sm mt-2">ทำความดี ช่วยเหลือผู้อื่น เพื่อรับคะแนนเพิ่ม!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.tab-btn {
    background: transparent;
    color: #64748b;
}
.tab-btn.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}
.tab-btn:not(.active):hover {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
            document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
