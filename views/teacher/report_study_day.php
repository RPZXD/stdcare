<?php
/**
 * View: Daily Attendance Report
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Cards
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.05);
    }
    .status-badge {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #eee !important; }
        body { background: white !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-teal-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-calendar-check text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานเวลาเรียนประจำวัน
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            ตรวจสอบสถิติการมาเรียนรายห้องเรียนรายวัน สำหรับคุณครูที่ปรึกษา
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl border border-emerald-100 dark:border-emerald-800 text-center">
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest block mb-1 italic">เทอม/ปีการศึกษา</span>
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400"><?php echo $term; ?>/<?php echo $pee; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">วันที่</label>
                <div class="relative">
                    <input type="date" name="date" value="<?php echo htmlspecialchars($report_date); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo ($report_class == $i) ? 'selected' : ''; ?>>มัธยมศึกษาปีที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้องเรียน</label>
                <div class="relative">
                    <select name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo ($report_room == $i) ? 'selected' : ''; ?>>ห้องเรียนที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black rounded-2xl shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> แสดงรายงาน
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($students)) : ?>
        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8 animate-fadeIn" style="animation-delay: 0.15s">
            <?php 
                $statusColors = [
                    '1' => 'emerald',
                    '2' => 'rose',
                    '3' => 'amber',
                    '4' => 'blue',
                    '5' => 'indigo',
                    '6' => 'violet'
                ];
                foreach($summary->status_labels as $key => $info): 
                    $count = $summary->status_count[$key];
                    $percent = count($students) > 0 ? round($count * 100 / count($students), 1) : 0;
                    $color = $statusColors[$key] ?? 'slate';
            ?>
                <div class="glass-effect p-6 rounded-[2rem] border-b-4 border-<?php echo $color; ?>-500 shadow-lg shadow-<?php echo $color; ?>-500/10 hover:scale-105 transition-transform">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-<?php echo $color; ?>-100 dark:bg-<?php echo $color; ?>-900/30 text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 rounded-lg flex items-center justify-center text-sm">
                            <?php echo $info['emoji']; ?>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none italic"><?php echo $info['label']; ?></span>
                    </div>
                    <div class="text-2xl font-black text-slate-800 dark:text-white tabular-nums"><?php echo $count; ?> <span class="text-xs text-slate-400 font-medium">คน</span></div>
                    <div class="text-[10px] font-bold text-<?php echo $color; ?>-500/70 mt-1"><?php echo $percent; ?>% ของชั้นเรียน</div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Main Content Table/Cards -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden animate-fadeIn" style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-list-ul text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white italic">มัธยมศึกษาปีที่ <?php echo $report_class . "/" . $report_room; ?></h3>
                        <p class="text-sm text-slate-500 font-medium">ข้อมูลประจำวันที่ <?php echo thaiDateShort($report_date); ?> (นักเรียน <?php echo count($students); ?> คน)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                </div>
            </div>

            <!-- Print Header -->
            <div class="print-only text-center mb-10 border-b-2 border-slate-900 pb-6 uppercase tracking-widest">
                <h1 class="text-3xl font-black">โรงเรียนพิชัยรัตนาคาร</h1>
                <h2 class="text-xl font-bold mt-2 italic">รายงานเวลาเรียนประจำวันชั้น ม.<?php echo $report_class . "/" . $report_room; ?></h2>
                <p class="text-sm font-medium mt-1">วันที่ <?php echo thaiDateShort($report_date); ?> | เทอม <?php echo $term; ?>/<?php echo $pee; ?></p>
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden lg:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เลขที่</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">รหัสนักเรียน</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic leading-relaxed">ชื่อ-นามสกุล</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">สถานะ</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">หมายเหตุ/เหตุผล</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <?php foreach ($students as $stu) :
                            $status_key = $stu['attendance_status'] ?? '2';
                            $status_info = $summary->status_labels[$status_key] ?? ['label' => 'ไม่ทราบ', 'emoji' => '❓'];
                            $color = $statusColors[$status_key] ?? 'slate';
                        ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-slate-400 italic">#<?php echo $stu['Stu_no']; ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-slate-400 italic"><?php echo $stu['Stu_id']; ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-<?php echo $color; ?>-400 to-<?php echo $color; ?>-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-<?php echo $color; ?>-500/20 group-hover:scale-110 transition-transform">
                                            <?php echo mb_substr($stu['Stu_name'], 0, 1, 'UTF-8'); ?>
                                        </div>
                                        <div class="font-black text-slate-800 dark:text-white leading-tight">
                                            <?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black bg-<?php echo $color; ?>-50 dark:bg-<?php echo $color; ?>-900/40 text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 border border-<?php echo $color; ?>-100 dark:border-<?php echo $color; ?>-900/50 uppercase tracking-widest italic italic">
                                        <span class="text-xs"><?php echo $status_info['emoji']; ?></span>
                                        <?php echo $status_info['label']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-medium text-slate-500 italic"><?php echo htmlspecialchars($stu['reason'] ?? '-'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Cards -->
            <div class="lg:hidden grid grid-cols-1 gap-4">
                <?php foreach ($students as $stu) :
                    $status_key = $stu['attendance_status'] ?? '2';
                    $status_info = $summary->status_labels[$status_key] ?? ['label' => 'ไม่ทราบ', 'emoji' => '❓'];
                    $color = $statusColors[$status_key] ?? 'slate';
                ?>
                    <div class="glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-<?php echo $color; ?>-400 to-<?php echo $color; ?>-600 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-<?php echo $color; ?>-500/20">
                                <?php echo mb_substr($stu['Stu_name'], 0, 1, 'UTF-8'); ?>
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black text-slate-400 italic">เลขที่ <?php echo $stu['Stu_no']; ?></span>
                                    <span class="text-[10px] font-black text-slate-400 italic">ID: <?php echo $stu['Stu_id']; ?></span>
                                </div>
                                <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words">
                                    <?php echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']); ?>
                                </h4>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between p-3 bg-<?php echo $color; ?>-50 dark:bg-<?php echo $color; ?>-900/30 rounded-2xl border border-<?php echo $color; ?>-100 dark:border-<?php echo $color; ?>-900/30">
                                <span class="text-[10px] font-black text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 uppercase italic">สถานะปัจจุบัน</span>
                                <span class="text-sm font-black text-<?php echo $color; ?>-700 dark:text-<?php echo $color; ?>-300">
                                    <?php echo $status_info['emoji'] . ' ' . $status_info['label']; ?>
                                </span>
                            </div>
                            <?php if (!empty($stu['reason'])): ?>
                                <div class="px-4 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                                    <p class="text-[10px] font-black text-slate-400 uppercase italic mb-1">หมายเหตุ</p>
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($stu['reason']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-[3rem] py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner animate-fadeIn">
            <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
                <i class="fas fa-calendar-times text-5xl text-slate-200"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest animate-pulse italic">ไม่พบข้อมูลเวลาเรียน</h3>
            <p class="text-slate-400 mt-2 font-medium italic">ยังไม่พบรายการเช็คชื่อสำหรับห้องเรียนนี้ในวันที่เลือกครับผม</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
