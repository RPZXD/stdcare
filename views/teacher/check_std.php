<?php
/**
 * Teacher Check Attendance View
 * MVC Pattern - Premium Modern UI with Tailwind CSS
 * Mobile-First: Card Layout for Mobile, Table for Desktop
 */
$pageTitle = $pageTitle ?? 'เช็คชื่อนักเรียน';

ob_start();
?>

<!-- Premium Custom Styles -->
<style>
    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.85);
    }
    
    /* Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .floating { animation: float 3s ease-in-out infinite; }
    
    /* Card Hover */
    .student-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    
    /* Radio Pill Effects */
    .radio-pill {
        transition: all 0.25s ease;
    }
    .radio-pill:active {
        transform: scale(0.95);
    }
    .radio-pill input:checked + span {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }
    
    /* Status Badge */
    .status-badge {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    
    /* Edit Form Slide */
    .edit-form {
        display: none;
        animation: slideDown 0.3s ease;
    }
    .edit-form.active {
        display: block;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Button Shine */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
        transform: rotate(45deg) translateY(-100%);
        transition: 0.6s;
    }
    .btn-shine:hover::after {
        transform: rotate(45deg) translateY(100%);
    }
    
    /* Table Row Hover (Desktop) */
    .table-row {
        transition: all 0.2s ease;
    }
    .table-row:hover {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.08) 100%);
    }
</style>

<!-- Page Header -->
<div class="relative mb-5 md:mb-8 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-8 border border-white/40 dark:border-slate-700/50 shadow-2xl relative overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute top-0 right-0 w-32 md:w-80 h-32 md:h-80 bg-gradient-to-br from-blue-400/30 to-indigo-500/30 rounded-full blur-3xl -z-10 floating"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-60 h-24 md:h-60 bg-gradient-to-tr from-violet-400/30 to-purple-500/30 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <!-- Title -->
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl md:rounded-2xl blur-lg opacity-60"></div>
                    <div class="relative w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl md:rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-clipboard-check text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white">📋 เช็คชื่อนักเรียน</h1>
                    <div class="flex flex-wrap items-center gap-2 text-xs md:text-sm mt-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full font-semibold">
                            <i class="fas fa-users"></i>
                            ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full font-semibold">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo htmlspecialchars($dateDisplay); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Date Picker -->
            <form method="get" class="w-full lg:w-auto">
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-xl rounded-xl p-3 border border-white/50 dark:border-slate-600/50 shadow-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center shadow flex-shrink-0">
                            <i class="fas fa-calendar-day text-white text-sm"></i>
                        </div>
                        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" 
                               class="flex-1 min-w-0 border-2 border-gray-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-800 dark:text-white dark:bg-slate-700 font-medium focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        <button type="submit" class="btn-shine bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 font-bold text-sm transition shadow-lg flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            <span class="hidden sm:inline">แสดง</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-4 gap-2 md:gap-4 mb-5 md:mb-6">
    <div class="glass-card rounded-xl p-2 md:p-4 border border-white/40 dark:border-slate-700/50 shadow-lg text-center">
        <p class="text-lg md:text-2xl font-black text-slate-800 dark:text-white"><?php echo $totalStudents; ?></p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500">ทั้งหมด</p>
    </div>
    <div class="glass-card rounded-xl p-2 md:p-4 border border-white/40 dark:border-slate-700/50 shadow-lg text-center">
        <p class="text-lg md:text-2xl font-black text-green-600"><?php echo $presentCount; ?></p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500">มาเรียน</p>
    </div>
    <div class="glass-card rounded-xl p-2 md:p-4 border border-white/40 dark:border-slate-700/50 shadow-lg text-center">
        <p class="text-lg md:text-2xl font-black text-red-600"><?php echo $absentCount; ?></p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500">ขาดเรียน</p>
    </div>
    <div class="glass-card rounded-xl p-2 md:p-4 border border-white/40 dark:border-slate-700/50 shadow-lg text-center">
        <p class="text-lg md:text-2xl font-black text-amber-600"><?php echo $notChecked; ?></p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500">รอเช็ค</p>
    </div>
</div>

<!-- Attendance Form -->
<form id="attendance-form" method="post">
    <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
    <input type="hidden" name="term" value="<?php echo htmlspecialchars($term); ?>">
    <input type="hidden" name="pee" value="<?php echo htmlspecialchars($pee); ?>">
    
    <!-- ==================== MOBILE CARD LAYOUT ==================== -->
    <div class="md:hidden space-y-3 mb-24">
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $std): ?>
                <div class="student-card glass-card rounded-2xl p-4 border border-white/40 dark:border-slate-700/50 shadow-lg" data-stu-id="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                    <!-- Header: Number + Name + Status -->
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl font-black text-lg flex items-center justify-center shadow-lg">
                            <?php echo htmlspecialchars($std['Stu_no']); ?>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 dark:text-white truncate">
                                <?php echo htmlspecialchars($std['Stu_name'] . ' ' . $std['Stu_sur']); ?>
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($std['Stu_id']); ?></p>
                        </div>
                        <!-- Current Status Badge -->
                        <?php if (!empty($std['attendance_status'])): ?>
                            <?php
                            $statusBadges = [
                                '1' => ['✅ มา', 'from-green-400 to-emerald-500'],
                                '2' => ['❌ ขาด', 'from-red-400 to-rose-500'],
                                '3' => ['🕒 สาย', 'from-yellow-400 to-orange-500'],
                                '4' => ['🤒 ป่วย', 'from-blue-400 to-cyan-500'],
                                '5' => ['📝 กิจ', 'from-purple-400 to-indigo-500'],
                                '6' => ['🎉 กิจกรรม', 'from-pink-400 to-fuchsia-500'],
                            ];
                            $badge = $statusBadges[$std['attendance_status']] ?? ['➖', 'from-gray-300 to-gray-400'];
                            ?>
                            <span class="status-badge px-3 py-1.5 bg-gradient-to-r <?php echo $badge[1]; ?> rounded-full text-white text-xs font-bold shadow-lg">
                                <?php echo $badge[0]; ?>
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1.5 bg-gray-200 dark:bg-slate-700 rounded-full text-gray-500 text-xs font-bold">⏳ รอเช็ค</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Attendance Selection -->
                    <?php if (!empty($std['attendance_status'])): ?>
                        <!-- Already checked - Show edit button -->
                        <div class="flex flex-col gap-2">
                            <button type="button" class="edit-btn-mobile w-full btn-shine bg-gradient-to-r from-amber-400 to-orange-500 text-white py-2.5 rounded-xl font-bold text-sm shadow-lg flex items-center justify-center gap-2" data-stu-id="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                <i class="fas fa-edit"></i> แก้ไขสถานะ
                            </button>
                            
                            <!-- Edit Form (Hidden) -->
                            <div class="edit-form bg-slate-50 dark:bg-slate-800 rounded-xl p-4 mt-2" id="edit-form-mobile-<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">เลือกสถานะใหม่:</p>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <?php
                                    $opts = [
                                        '1' => ['✅ มา', 'from-green-400 to-emerald-500'],
                                        '2' => ['❌ ขาด', 'from-red-400 to-rose-500'],
                                        '3' => ['🕒 สาย', 'from-yellow-400 to-orange-500'],
                                        '4' => ['🤒 ป่วย', 'from-blue-400 to-cyan-500'],
                                        '5' => ['📝 กิจ', 'from-purple-400 to-indigo-500'],
                                        '6' => ['🎉 กรม', 'from-pink-400 to-fuchsia-500'],
                                    ];
                                    foreach ($opts as $val => [$lbl, $grad]):
                                    ?>
                                    <label class="radio-pill cursor-pointer">
                                        <input type="radio" name="edit_status_mobile_<?php echo htmlspecialchars($std['Stu_id']); ?>" value="<?php echo $val; ?>" class="hidden peer" <?php echo $std['attendance_status'] == $val ? 'checked' : ''; ?>>
                                        <span class="block text-center px-2 py-2 rounded-lg text-xs font-bold bg-gray-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 peer-checked:bg-gradient-to-r peer-checked:<?php echo $grad; ?> peer-checked:text-white transition">
                                            <?php echo $lbl; ?>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <input type="text" name="edit_reason_mobile_<?php echo htmlspecialchars($std['Stu_id']); ?>" placeholder="สาเหตุ (ถ้ามี)" value="<?php echo htmlspecialchars($std['reason'] ?? ''); ?>" class="w-full border-2 border-gray-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm mb-3 dark:bg-slate-700 dark:text-white">
                                <div class="flex gap-2">
                                    <button type="button" class="save-edit-mobile flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 rounded-lg text-sm font-bold shadow">
                                        <i class="fas fa-save mr-1"></i> บันทึก
                                    </button>
                                    <button type="button" class="cancel-edit-mobile flex-1 bg-gray-400 text-white py-2 rounded-lg text-sm font-bold">
                                        <i class="fas fa-times mr-1"></i> ยกเลิก
                                    </button>
                                </div>
                                <input type="hidden" name="edit_stu_id_mobile" value="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                            </div>
                            
                            <!-- Scan Time & Checked By Info -->
                            <?php if (!empty($std['arrival_time']) || !empty($std['checked_by'])): ?>
                                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                                    <?php if (!empty($std['arrival_time'])): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">
                                            🟢 เข้า <?php echo htmlspecialchars(substr($std['arrival_time'], 0, 5)); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($std['checked_by'])): ?>
                                        <?php
                                        $cb = $std['checked_by'];
                                        if ($cb === 'teacher' || $cb === 'system') {
                                            echo '<span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">👨‍🏫 ครู</span>';
                                        } elseif ($cb === 'rfid' || $cb === 'RFID') {
                                            echo '<span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full">💳 สแกน</span>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Not checked yet - Show radio buttons -->
                        <div>
                            <input type="hidden" name="Stu_id[]" value="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                            <input type="hidden" name="teach_id[<?php echo htmlspecialchars($std['Stu_id']); ?>]" value="<?php echo htmlspecialchars($_SESSION['Teacher_login'] ?? ''); ?>">
                            
                            <div class="grid grid-cols-6 gap-1 mb-2">
                                <?php
                                $options = [
                                    '1' => ['✅', 'มา', 'from-green-400 to-emerald-500', true],
                                    '2' => ['❌', 'ขาด', 'from-red-400 to-rose-500', false],
                                    '3' => ['🕒', 'สาย', 'from-yellow-400 to-orange-500', false],
                                    '4' => ['🤒', 'ป่วย', 'from-blue-400 to-cyan-500', false],
                                    '5' => ['📝', 'กิจ', 'from-purple-400 to-indigo-500', false],
                                    '6' => ['🎉', 'กรม', 'from-pink-400 to-fuchsia-500', false],
                                ];
                                foreach ($options as $val => [$icon, $label, $gradient, $checked]):
                                ?>
                                <label class="radio-pill cursor-pointer">
                                    <input type="radio" name="attendance_status[<?php echo htmlspecialchars($std['Stu_id']); ?>]" value="<?php echo $val; ?>" class="hidden peer" <?php echo $checked ? 'checked' : ''; ?>>
                                    <span class="flex flex-col items-center justify-center py-2 rounded-xl text-xs font-bold bg-gray-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 peer-checked:bg-gradient-to-r peer-checked:<?php echo $gradient; ?> peer-checked:text-white transition">
                                        <span class="text-base"><?php echo $icon; ?></span>
                                        <span class="text-[10px] mt-0.5"><?php echo $label; ?></span>
                                    </span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <input type="text" name="reason[<?php echo htmlspecialchars($std['Stu_id']); ?>]" placeholder="💬 สาเหตุ (ถ้ามี)" class="w-full border-2 border-gray-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:bg-slate-700 dark:text-white">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="glass-card rounded-2xl p-8 text-center">
                <div class="w-20 h-20 mx-auto bg-gray-200 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users-slash text-gray-400 text-3xl"></i>
                </div>
                <p class="text-lg font-bold text-slate-700 dark:text-slate-300">ไม่พบนักเรียน</p>
                <p class="text-sm text-slate-500">กรุณาตรวจสอบข้อมูลหรือเลือกวันที่อื่น</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- ==================== DESKTOP TABLE LAYOUT ==================== -->
    <div class="hidden md:block glass-card rounded-2xl border border-white/40 dark:border-slate-700/50 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table id="attendance-table" class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600">
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">เลขที่</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">รหัส</th>
                        <th class="px-4 py-4 text-left text-white font-bold text-sm">ชื่อ-สกุล</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">การเช็คชื่อ</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">สถานะ</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">เวลา</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">สาเหตุ</th>
                        <th class="px-4 py-4 text-center text-white font-bold text-sm">เช็คโดย</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $std): ?>
                            <tr data-stu-id="<?php echo htmlspecialchars($std['Stu_id']); ?>" class="table-row bg-white dark:bg-slate-800/50">
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg font-bold shadow">
                                        <?php echo htmlspecialchars($std['Stu_no']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-mono text-xs text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?php echo htmlspecialchars($std['Stu_id']); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-800 dark:text-white"><?php echo htmlspecialchars($std['Stu_pre'] . $std['Stu_name'] . ' ' . $std['Stu_sur']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if (!empty($std['attendance_status'])): ?>
                                        <button type="button" class="edit-btn bg-gradient-to-r from-amber-400 to-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow hover:shadow-lg transition" data-stu-id="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                            <i class="fas fa-edit mr-1"></i> แก้ไข
                                        </button>
                                        <div class="edit-form mt-2 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-indigo-200 dark:border-indigo-700" id="edit-form-<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                            <input type="hidden" name="edit_stu_id" value="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                            <div class="grid grid-cols-6 gap-1 mb-2">
                                                <?php foreach ($opts ?? [
                                                    '1' => ['✅', 'from-green-400 to-emerald-500'],
                                                    '2' => ['❌', 'from-red-400 to-rose-500'],
                                                    '3' => ['🕒', 'from-yellow-400 to-orange-500'],
                                                    '4' => ['🤒', 'from-blue-400 to-cyan-500'],
                                                    '5' => ['📝', 'from-purple-400 to-indigo-500'],
                                                    '6' => ['🎉', 'from-pink-400 to-fuchsia-500'],
                                                ] as $v => [$ic, $gr]): ?>
                                                <label class="radio-pill cursor-pointer">
                                                    <input type="radio" name="edit_status_<?php echo htmlspecialchars($std['Stu_id']); ?>" value="<?php echo $v; ?>" class="hidden peer" <?php echo $std['attendance_status'] == $v ? 'checked' : ''; ?>>
                                                    <span class="block text-center py-1.5 rounded text-sm bg-gray-100 dark:bg-slate-700 peer-checked:bg-gradient-to-r peer-checked:<?php echo $gr; ?> peer-checked:text-white transition"><?php echo $ic; ?></span>
                                                </label>
                                                <?php endforeach; ?>
                                            </div>
                                            <input type="text" name="edit_reason_<?php echo htmlspecialchars($std['Stu_id']); ?>" placeholder="สาเหตุ" value="<?php echo htmlspecialchars($std['reason'] ?? ''); ?>" class="w-full border rounded px-2 py-1 text-sm mb-2 dark:bg-slate-700 dark:text-white">
                                            <div class="flex gap-1">
                                                <button type="button" class="save-edit-btn flex-1 bg-blue-500 text-white py-1 rounded text-xs font-bold"><i class="fas fa-save"></i></button>
                                                <button type="button" class="cancel-edit-btn flex-1 bg-gray-400 text-white py-1 rounded text-xs font-bold"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            <input type="hidden" name="Stu_id[]" value="<?php echo htmlspecialchars($std['Stu_id']); ?>">
                                            <input type="hidden" name="teach_id[<?php echo htmlspecialchars($std['Stu_id']); ?>]" value="<?php echo htmlspecialchars($_SESSION['Teacher_login'] ?? ''); ?>">
                                            <?php foreach ([
                                                '1' => ['✅มา', 'from-green-400 to-emerald-500', true],
                                                '2' => ['❌ขาด', 'from-red-400 to-rose-500', false],
                                                '3' => ['🕒สาย', 'from-yellow-400 to-orange-500', false],
                                                '4' => ['🤒ป่วย', 'from-blue-400 to-cyan-500', false],
                                                '5' => ['📝กิจ', 'from-purple-400 to-indigo-500', false],
                                                '6' => ['🎉กรม', 'from-pink-400 to-fuchsia-500', false],
                                            ] as $v => [$l, $g, $c]): ?>
                                            <label class="radio-pill cursor-pointer">
                                                <input type="radio" name="attendance_status[<?php echo htmlspecialchars($std['Stu_id']); ?>]" value="<?php echo $v; ?>" class="hidden peer" <?php echo $c ? 'checked' : ''; ?>>
                                                <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 dark:bg-slate-700 peer-checked:bg-gradient-to-r peer-checked:<?php echo $g; ?> peer-checked:text-white transition"><?php echo $l; ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php
                                    if (!empty($std['attendance_status'])) {
                                        $sb = [
                                            '1' => ['✅ มาเรียน', 'from-green-400 to-emerald-500'],
                                            '2' => ['❌ ขาด', 'from-red-400 to-rose-500'],
                                            '3' => ['🕒 สาย', 'from-yellow-400 to-orange-500'],
                                            '4' => ['🤒 ป่วย', 'from-blue-400 to-cyan-500'],
                                            '5' => ['📝 กิจ', 'from-purple-400 to-indigo-500'],
                                            '6' => ['🎉 กิจกรรม', 'from-pink-400 to-fuchsia-500'],
                                        ];
                                        $s = $sb[$std['attendance_status']] ?? ['➖', 'from-gray-300 to-gray-400'];
                                        echo '<span class="status-badge px-3 py-1.5 bg-gradient-to-r ' . $s[1] . ' rounded-full text-white text-xs font-bold shadow">' . $s[0] . '</span>';
                                    } else {
                                        echo '<span class="px-3 py-1.5 bg-gray-200 dark:bg-slate-700 rounded-full text-gray-500 text-xs font-bold">⏳ รอเช็ค</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    <?php if (!empty($std['arrival_time'])): ?>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full">🟢<?php echo substr($std['arrival_time'], 0, 5); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if (!empty($std['attendance_status'])): ?>
                                        <?php echo !empty($std['reason']) ? '<span class="text-sm text-slate-600 dark:text-slate-400">' . htmlspecialchars($std['reason']) . '</span>' : '<span class="text-gray-400">—</span>'; ?>
                                    <?php else: ?>
                                        <input type="text" name="reason[<?php echo htmlspecialchars($std['Stu_id']); ?>]" placeholder="สาเหตุ" class="w-full max-w-[100px] border rounded px-2 py-1 text-xs dark:bg-slate-700 dark:text-white">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    <?php
                                    if (!empty($std['checked_by'])) {
                                        $cb = $std['checked_by'];
                                        if ($cb === 'teacher' || $cb === 'system') echo '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full">👨‍🏫ครู</span>';
                                        elseif ($cb === 'rfid' || $cb === 'RFID') echo '<span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full">💳สแกน</span>';
                                        else echo '<span class="text-slate-500">' . htmlspecialchars($cb) . '</span>';
                                    } else {
                                        echo '<span class="text-gray-400">—</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-12 text-slate-500">ไม่พบนักเรียน</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Desktop Save Button -->
    <?php if (!empty($students)): ?>
        <div class="hidden md:flex justify-end mt-6">
            <button id="btn-save-bulk" type="submit" class="btn-shine bg-gradient-to-r from-green-500 to-emerald-600 text-white px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-[0_20px_50px_rgba(16,185,129,0.3)] transition-all flex items-center gap-3 transform hover:-translate-y-1">
                <i class="fas fa-save text-2xl"></i>
                <span>บันทึกการเช็คชื่อทั้งห้อง</span>
                <i class="fas fa-check-double text-2xl"></i>
            </button>
        </div>
    <?php endif; ?>
</form>

<!-- Fixed Mobile Save Button -->
<?php if (!empty($students) && $notChecked > 0): ?>
<div class="md:hidden fixed bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-white dark:from-slate-900 via-white/95 to-transparent z-40">
    <button id="btn-save-mobile" type="button" onclick="document.getElementById('attendance-form').dispatchEvent(new Event('submit'))" class="w-full btn-shine bg-gradient-to-r from-green-500 to-emerald-600 text-white py-4 rounded-2xl font-bold text-lg shadow-2xl flex items-center justify-center gap-3">
        <i class="fas fa-save text-xl"></i>
        <span>บันทึกทั้งหมด (<?php echo $notChecked; ?> คน)</span>
    </button>
</div>
<?php endif; ?>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTables (Desktop only)
    if ($.fn.DataTable && window.innerWidth >= 768) {
        $('#attendance-table').DataTable({
            responsive: false,
            autoWidth: false,
            lengthChange: false,
            pageLength: 50,
            paging: true,
            searching: true,
            ordering: true,
            order: [[0, 'asc']],
            language: {
                search: "🔍 ค้นหา:",
                searchPlaceholder: "พิมพ์ชื่อ...",
                info: "แสดง _START_-_END_ จาก _TOTAL_",
                paginate: { next: "→", previous: "←" }
            }
        });
    }
    
    // Desktop Edit Buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.edit-form').forEach(f => f.classList.remove('active'));
            const form = document.getElementById('edit-form-' + btn.dataset.stuId);
            if (form) form.classList.add('active');
        });
    });
    
    // Mobile Edit Buttons
    document.querySelectorAll('.edit-btn-mobile').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.edit-form').forEach(f => f.classList.remove('active'));
            const form = document.getElementById('edit-form-mobile-' + btn.dataset.stuId);
            if (form) form.classList.add('active');
        });
    });
    
    // Cancel Buttons
    document.querySelectorAll('.cancel-edit-btn, .cancel-edit-mobile').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.edit-form')?.classList.remove('active'));
    });
    
    // Save Edit (Desktop)
    document.querySelectorAll('.save-edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const panel = btn.closest('.edit-form');
            const stuId = panel.querySelector('input[name="edit_stu_id"]').value;
            saveEdit(stuId, 'edit_status_', 'edit_reason_', btn);
        });
    });
    
    // Save Edit (Mobile)
    document.querySelectorAll('.save-edit-mobile').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const panel = btn.closest('.edit-form');
            const stuId = panel.querySelector('input[name="edit_stu_id_mobile"]').value;
            saveEdit(stuId, 'edit_status_mobile_', 'edit_reason_mobile_', btn);
        });
    });
    
    function saveEdit(stuId, statusPrefix, reasonPrefix, btn) {
        const statusRadio = document.querySelector('input[name="' + statusPrefix + stuId + '"]:checked');
        const reasonInput = document.querySelector('input[name="' + reasonPrefix + stuId + '"]');
        
        if (!statusRadio) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือกสถานะ', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            return;
        }
        
        const formData = new FormData();
        formData.append('Stu_id[]', stuId);
        formData.append('attendance_status[' + stuId + ']', statusRadio.value);
        formData.append('reason[' + stuId + ']', reasonInput?.value || '');
        formData.append('teach_id[' + stuId + ']', '<?php echo htmlspecialchars($_SESSION['Teacher_login'] ?? ''); ?>');
        formData.append('date', '<?php echo htmlspecialchars($date); ?>');
        formData.append('term', '<?php echo htmlspecialchars($term); ?>');
        formData.append('pee', '<?php echo htmlspecialchars($pee); ?>');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
        
        fetch('../controllers/AttendanceController.php?action=save_bulk', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(json => {
            if (json.success) {
                Swal.fire({ icon: 'success', title: '✅ บันทึกสำเร็จ!', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            } else {
                Swal.fire({ icon: 'error', title: 'บันทึกล้มเหลว', text: json.error || '' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> บันทึก';
            }
        }).catch(() => {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> บันทึก';
        });
    }
    
    // Bulk Save
    const form = document.getElementById('attendance-form');
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('btn-save-bulk') || document.getElementById('btn-save-mobile');
        if (!saveBtn || saveBtn.dataset.busy === '1') return;
        saveBtn.dataset.busy = '1';
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-xl"></i> กำลังบันทึก...';
        
        const formData = new FormData();
        formData.append('date', form.querySelector('input[name="date"]').value);
        formData.append('term', form.querySelector('input[name="term"]').value);
        formData.append('pee', form.querySelector('input[name="pee"]').value);
        
        // Collect from both mobile cards and desktop table
        document.querySelectorAll('[data-stu-id]').forEach(el => {
            if (el.querySelector('.edit-btn, .edit-btn-mobile')) return; // Skip already checked
            
            const stuId = el.dataset.stuId;
            const radio = el.querySelector('input[name="attendance_status[' + stuId + ']"]:checked');
            const reason = el.querySelector('input[name="reason[' + stuId + ']"]');
            const teachId = el.querySelector('input[name="teach_id[' + stuId + ']"]');
            
            if (radio && !formData.getAll('Stu_id[]').includes(stuId)) {
                formData.append('Stu_id[]', stuId);
                formData.append('attendance_status[' + stuId + ']', radio.value);
                if (reason) formData.append('reason[' + stuId + ']', reason.value);
                if (teachId) formData.append('teach_id[' + stuId + ']', teachId.value);
            }
        });
        
        fetch('../controllers/AttendanceController.php?action=save_bulk', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(json => {
            if (json.success) {
                Swal.fire({ 
                    icon: 'success', 
                    title: '✅ บันทึกสำเร็จ!', 
                    text: (json.saved || 0) + ' รายการ',
                    toast: true, 
                    position: 'center',
                    timer: 2000, 
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ icon: 'error', title: 'บันทึกล้มเหลว', text: json.error || '' });
                saveBtn.disabled = false;
                saveBtn.dataset.busy = '0';
                saveBtn.innerHTML = '<i class="fas fa-save text-xl"></i> บันทึกทั้งหมด';
            }
        }).catch(() => {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
            saveBtn.disabled = false;
            saveBtn.dataset.busy = '0';
            saveBtn.innerHTML = '<i class="fas fa-save text-xl"></i> บันทึกทั้งหมด';
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
