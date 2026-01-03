<?php
/**
 * Report Wroom View - Committee List
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
$pageTitle = $title ?? 'รายชื่อคณะกรรมการห้องเรียนสีขาว';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.9);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    .position-card {
        transition: all 0.3s ease;
    }
    .position-card:hover {
        transform: translateY(-2px);
    }
    
    /* Print Styles */
    @media print {
        @page { 
            size: A4 portrait; 
            margin: 12mm 10mm 15mm 10mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body { 
            background: white !important; 
            font-family: 'Mali', 'TH Sarabun New', sans-serif !important; 
            font-size: 11pt;
            line-height: 1.4;
        }
        .no-print, #sidebar, #navbar, footer, .floating-icon { 
            display: none !important; 
        }
        .glass-card { 
            background: white !important; 
            box-shadow: none !important; 
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            page-break-inside: avoid;
        }
        .position-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        #printHeader {
            display: block !important;
        }
        #printSignature {
            display: block !important;
        }
        .grid {
            display: grid !important;
        }
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .gap-4 {
            gap: 0.75rem !important;
        }
    }
    @media screen {
        #printHeader, #printSignature {
            display: none !important;
        }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-teal-500/20 to-emerald-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">📋</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    รายชื่อคณะกรรมการห้องเรียนสีขาว
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-teal-500 mr-1"></i>
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?>
                    <span class="mx-1">•</span>
                    ปีการศึกษา <?= htmlspecialchars($pee) ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <button onclick="window.print()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-print"></i>
        <span>🖨️ พิมพ์รายชื่อ</span>
    </button>
    <button onclick="location.href='report_wroom2.php'" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-sitemap"></i>
        <span>ผังโครงสร้าง</span>
    </button>
    <button onclick="location.href='wroom.php'" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-slate-500 to-gray-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-arrow-left"></i>
        <span>กลับ</span>
    </button>
</div>

<!-- Print Header -->
<div id="printHeader" class="text-center mb-6 pb-4 border-b-2 border-slate-300">
    <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
    <h1 class="text-xl font-bold text-slate-800">รายชื่อคณะกรรมการห้องเรียนสีขาว</h1>
    <p class="text-sm text-slate-600">โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์</p>
    <div class="flex justify-center gap-8 mt-2 text-sm font-semibold">
        <span>ชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?></span>
        <span>ปีการศึกษา <?= $pee ?></span>
    </div>
</div>

<!-- Print Content -->
<div id="printContent">

    <!-- Advisors -->
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-lg mb-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center shadow">
                <span class="text-lg">👨‍🏫</span>
            </div>
            <h3 class="font-bold text-slate-800 dark:text-white">ครูที่ปรึกษา</h3>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php foreach ($roomTeachers as $t): ?>
            <div class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-full">
                <?php if (!empty($t['Teach_photo'])): ?>
                <img src="https://std.phichai.ac.th/teacher/uploads/phototeach/<?= htmlspecialchars($t['Teach_photo']) ?>" class="w-8 h-8 rounded-full object-cover">
                <?php else: ?>
                <div class="w-8 h-8 bg-indigo-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-indigo-500 text-xs"></i>
                </div>
                <?php endif; ?>
                <span class="font-semibold text-indigo-700 dark:text-indigo-300 text-sm"><?= htmlspecialchars($t['Teach_name']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Committee Positions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <?php foreach ($positions as $key => $pos): ?>
            <?php if ($key === "advisors") continue; ?>
            <?php $members = $grouped[$key] ?? []; ?>
            <div class="position-card glass-card rounded-xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-8 bg-<?= $pos['color'] ?>-100 dark:bg-<?= $pos['color'] ?>-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-sm"><?= $pos['emoji'] ?></span>
                    </span>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm"><?= $pos['label'] ?></h4>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 bg-<?= $pos['color'] ?>-100 text-<?= $pos['color'] ?>-600 rounded-full">
                        <?= count($members) ?>/<?= $pos['limit'] ?>
                    </span>
                </div>
                
                <?php if (count($members) > 0): ?>
                <div class="space-y-2">
                    <?php foreach ($members as $idx => $stu): ?>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-5 h-5 bg-slate-200 dark:bg-slate-700 rounded text-center text-[10px] font-bold flex items-center justify-center"><?= $idx + 1 ?></span>
                        <span class="text-slate-700 dark:text-slate-300"><?= htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-sm text-slate-400 italic">- ยังไม่มี -</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Maxim -->
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-lg">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow">
                <span class="text-lg">✍️</span>
            </div>
            <h3 class="font-bold text-slate-800 dark:text-white">คติพจน์ห้องเรียนสีขาว</h3>
        </div>
        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
            <?php if ($maxim): ?>
            <p class="font-bold text-amber-800 dark:text-amber-200 text-lg">"<?= htmlspecialchars($maxim) ?>"</p>
            <?php else: ?>
            <p class="text-slate-400 italic">- ยังไม่ได้กรอกคติพจน์ -</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Print Signature Section -->
<div id="printSignature" class="mt-8 pt-6 border-t border-slate-200">
    <div class="grid grid-cols-<?= count($roomTeachers) > 1 ? '2' : '1' ?> gap-8 max-w-2xl mx-auto px-8">
        <?php foreach ($roomTeachers as $t): ?>
        <div class="text-center">
            <p class="mb-12">ลงชื่อ ..........................................</p>
            <p class="font-bold text-slate-800">(<?= htmlspecialchars($t['Teach_name']) ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-8 italic">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
