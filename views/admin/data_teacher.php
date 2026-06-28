<?php
/**
 * View: Admin Teacher Data
 * Modern UI with Tailwind CSS, Glassmorphism & Full CRUD
 */
ob_start();
$pageTitle = "จัดการครูและบุคลากร";
$activePage = "teacher";

// API URL for controller
$apiUrl = '../controllers/TeacherController.php';
$photoBaseUrl = 'https://std.phichai.ac.th/teacher/uploads/phototeach/';

// Define major options
$majors = [
    'ผู้อำนวยการ', 'รองผู้อำนวยการ', 'วิทยาศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 
    'คณิตศาสตร์', 'คอมพิวเตอร์', 'การงานอาชีพ', 'ศิลปะ', 'สุขศึกษาและพลศึกษา',
    'สังคมศึกษา ศาสนา และวัฒนธรรม', 'กิจกรรมพัฒนาผู้เรียน', 'เจ้าหน้าที่ธุรการ',
    'เจ้าหน้าที่งานการเงิน', 'เจ้าหน้าที่ห้องพยาบาล', 'เจ้าหน้าที่โสตทัศนศึกษา',
    'เจ้าหน้าที่บริหารงานทั่วไป', 'นักการภารโรง', 'แม่บ้าน', 'พนักงานขับรถ'
];

$roles = [
    'T' => 'ครู',
    'OF' => 'เจ้าหน้าที่',
    'VP' => 'รองผู้อำนวยการ',
    'DIR' => 'ผู้อำนวยการ',
    'ADM' => 'Admin'
];

$statuses = [
    '1' => ['label' => 'ปกติ', 'color' => 'emerald', 'icon' => '✅'],
    '2' => ['label' => 'ย้าย', 'color' => 'sky', 'icon' => '🔁'],
    '3' => ['label' => 'เกษียณ', 'color' => 'slate', 'icon' => '🎖️'],
    '4' => ['label' => 'ลาออก', 'color' => 'amber', 'icon' => '⚠️'],
    '9' => ['label' => 'เสียชีวิต', 'color' => 'gray', 'icon' => '⚰️']
];
?>

<div class="animate-fadeIn teacher-admin-page">
    <!-- Page Header -->
    <?php 
    $headerData = [
        'title' => 'จัดการ <span class="text-sky-600 italic">ครูและบุคลากร</span>',
        'subtitle' => 'Teacher & Staff Management',
        'icon' => 'fa-chalkboard-teacher',
        'color' => 'sky',
        'actions' => [
            ['id' => 'btnExport', 'icon' => 'fa-file-export', 'text' => 'ส่งออกข้อมูล', 'color' => 'sky'],
            ['id' => 'btnAddTeacher', 'icon' => 'fa-user-plus', 'text' => 'เพิ่มข้อมูลครู', 'color' => 'emerald']
        ]
    ];
    include __DIR__ . '/../components/ui_header.php'; 
    ?>

    <!-- Summary Stats Cards -->
    <div class="teacher-stats-grid grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 md:mb-8">
        <?php
        $stats = [
            ['id' => 'totalTeachers', 'label' => 'รวมทั้งหมด', 'value' => '0', 'icon' => 'fa-users', 'color' => 'indigo'],
            ['id' => 'activeTeachers', 'label' => 'ปกติ', 'value' => '0', 'icon' => 'fa-check-circle', 'color' => 'emerald'],
            ['id' => 'teacherCount', 'label' => 'ครู', 'value' => '0', 'icon' => 'fa-chalkboard', 'color' => 'sky'],
            ['id' => 'staffCount', 'label' => 'เจ้าหน้าที่', 'value' => '0', 'icon' => 'fa-building', 'color' => 'amber']
        ];
        foreach ($stats as $stat):
            $statData = [
                'label' => $stat['label'],
                'value' => '<span id="' . $stat['id'] . '">' . $stat['value'] . '</span>',
                'icon' => $stat['icon'],
                'color' => $stat['color']
            ];
            include __DIR__ . '/../components/ui_stat_card.php';
        endforeach;
        ?>
    </div>

    <!-- Charts Section -->
    <div class="teacher-charts-grid grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="teacher-chart-card glass-effect rounded-2xl lg:rounded-[2rem] p-4 lg:p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-sky-500"></i> สถานะ
            </h4>
            <div class="h-48">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="teacher-chart-card glass-effect rounded-2xl lg:rounded-[2rem] p-4 lg:p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-layer-group text-indigo-500"></i> กลุ่มสาระ (Top 5)
            </h4>
            <div class="h-48">
                <canvas id="majorChart"></canvas>
            </div>
        </div>
        <div class="teacher-chart-card glass-effect rounded-2xl lg:rounded-[2rem] p-4 lg:p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-user-tag text-rose-500"></i> บทบาท
            </h4>
            <div class="h-48">
                <canvas id="roleChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="teacher-table-panel glass-effect rounded-2xl lg:rounded-[2.5rem] p-4 lg:p-8 shadow-xl border-t border-white/50">
        <div class="teacher-table-heading">
            <div>
                <p class="teacher-table-kicker">Teacher Directory</p>
                <h3>Teacher & Staff Directory</h3>
            </div>
            <span id="teacherMobileCount" class="teacher-table-count">0 records</span>
        </div>
        <div class="admin-table-shell">
            <table id="teacherTable" class="admin-responsive-table w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-sky-50/50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center w-16">รูป</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น/ห้อง</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">กลุ่มสาระ</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">สถานะ</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">บทบาท</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white !border-0 p-4 md:p-6">
                <h5 class="modal-title text-base md:text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-plus"></i> เพิ่มข้อมูลครู
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="addTeacherForm">
                <div class="modal-body p-4 md:p-8 bg-gradient-to-br from-white to-emerald-50 dark:from-slate-900 dark:to-slate-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รหัสครู *</label>
                            <input type="text" name="addTeach_id" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อ-สกุล *</label>
                            <input type="text" name="addTeach_name" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">กลุ่มสาระ</label>
                            <select name="addTeach_major" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($majors as $m): ?>
                                <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชั้น</label>
                                <select name="addTeach_class" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                    <option value="">-- เลือก --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                <thead>
                    <tr class="bg-sky-50/50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center w-16">รูป</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น/ห้อง</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">กลุ่มสาระ</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">สถานะ</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">บทบาท</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white !border-0 p-4 md:p-6">
                <h5 class="modal-title text-base md:text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-plus"></i> เพิ่มข้อมูลครู
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="addTeacherForm">
                <div class="modal-body p-4 md:p-8 bg-gradient-to-br from-white to-emerald-50 dark:from-slate-900 dark:to-slate-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รหัสครู *</label>
                            <input type="text" name="addTeach_id" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อ-สกุล *</label>
                            <input type="text" name="addTeach_name" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">กลุ่มสาระ</label>
                            <select name="addTeach_major" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($majors as $m): ?>
                                <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชั้น</label>
                                <select name="addTeach_class" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                    <option value="">-- เลือก --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ห้อง</label>
                                <input type="text" name="addTeach_room" placeholder="1, 2, A" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">สถานะ</label>
                            <select name="addTeach_status" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                <?php foreach ($statuses as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['icon'] ?> <?= $v['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">บทบาท</label>
                            <select name="addrole_std" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($roles as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เบอร์โทรศัพท์</label>
                            <input type="text" name="addTeach_phone" placeholder="08x-xxxxxxx" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">วันเกิด</label>
                            <input type="date" name="addTeach_birth" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ที่อยู่</label>
                            <textarea name="addTeach_addr" rows="2" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer !border-0 p-4 md:p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all hover:bg-slate-300" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/20 hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white !border-0 p-4 md:p-6">
                <h5 class="modal-title text-base md:text-xl font-black flex items-center gap-3">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลครู
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="editTeacherForm">
                <input type="hidden" name="editTeach_id_old">
                <div class="modal-body p-4 md:p-8 bg-gradient-to-br from-white to-amber-50 dark:from-slate-900 dark:to-slate-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รหัสครู</label>
                            <input type="text" name="editTeach_id" readonly class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-500 dark:text-slate-400 outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อ-สกุล *</label>
                            <input type="text" name="editTeach_name" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">กลุ่มสาระ</label>
                            <select name="editTeach_major" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($majors as $m): ?>
                                <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชั้น</label>
                                <select name="editTeach_class" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                                    <option value="">-- เลือก --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ห้อง</label>
                                <input type="text" name="editTeach_room" placeholder="1, 2, A" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">สถานะ</label>
                            <select name="editTeach_status" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                                <?php foreach ($statuses as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['icon'] ?> <?= $v['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">บทบาท</label>
                            <select name="editrole_std" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($roles as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เบอร์โทรศัพท์</label>
                            <input type="text" name="editTeach_phone" placeholder="08x-xxxxxxx" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">วันเกิด</label>
                            <input type="date" name="editTeach_birth" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">อีเมล</label>
                            <input type="email" name="editTeach_email" placeholder="example@phichai.ac.th" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เพศ</label>
                            <select name="editTeach_sex" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                                <option value="">-- เลือก --</option>
                                <option value="ชาย">ชาย</option>
                                <option value="หญิง">หญิง</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ตำแหน่ง</label>
                            <input type="text" name="editTeach_Position2" placeholder="เช่น ครู วิทยฐานะชำนาญการ" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">วุฒิการศึกษาสูงสุด</label>
                            <input type="text" name="editTeach_HiDegree" placeholder="เช่น ปริญญาโท การศึกษามหาบัณฑิต" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ที่อยู่</label>
                            <textarea name="editTeach_addr" rows="2" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none transition-all"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer !border-0 p-4 md:p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all hover:bg-slate-300" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-xl font-bold shadow-lg shadow-amber-600/20 hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Photo View Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-body p-4 bg-slate-100 dark:bg-slate-900 text-center">
                <img id="photoModalImg" src="" class="max-w-full h-auto rounded-2xl mx-auto">
            </div>
            <div class="modal-footer !border-0 p-4 bg-slate-50 dark:bg-slate-800">
                <button type="button" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-sky-500 to-blue-600 text-white !border-0 p-4 md:p-6">
                <h5 class="modal-title text-base md:text-xl font-black flex items-center gap-3">
                    <i class="fas fa-file-export"></i> ส่งออกข้อมูลครูและบุคลากร
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 md:p-8 bg-gradient-to-br from-white to-sky-50 dark:from-slate-900 dark:to-slate-800">
                <!-- Export Format Selection -->
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">รูปแบบการส่งออก</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl cursor-pointer hover:border-emerald-500 transition-all export-format-option">
                            <input type="radio" name="exportFormat" value="excel" checked class="w-5 h-5 text-emerald-600">
                            <div class="flex items-center gap-2">
                                <span class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-excel text-lg"></i>
                                </span>
                                <div>
                                    <p class="font-bold text-slate-700 dark:text-white">Excel (CSV)</p>
                                    <p class="text-[10px] text-slate-400">เหมาะสำหรับแก้ไขข้อมูล</p>
                                </div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 border-2 border-rose-200 dark:border-rose-800 rounded-2xl cursor-pointer hover:border-rose-500 transition-all export-format-option">
                            <input type="radio" name="exportFormat" value="pdf" class="w-5 h-5 text-rose-600">
                            <div class="flex items-center gap-2">
                                <span class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </span>
                                <div>
                                    <p class="font-bold text-slate-700 dark:text-white">PDF</p>
                                    <p class="text-[10px] text-slate-400">เหมาะสำหรับพิมพ์รายงาน</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Column Selection -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลือกคอลัมน์ที่ต้องการ</label>
                        <div class="flex gap-2">
                            <button type="button" id="selectAllCols" class="px-3 py-1 text-xs font-bold bg-sky-100 text-sky-600 rounded-lg hover:bg-sky-200 transition-all">เลือกทั้งหมด</button>
                            <button type="button" id="deselectAllCols" class="px-3 py-1 text-xs font-bold bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all">ยกเลิกทั้งหมด</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="columnCheckboxes">
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_id" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">รหัสครู</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_name" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ชื่อ-สกุล</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_sex" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เพศ</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_major" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">กลุ่มสาระ</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_class" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ชั้น</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_room" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ห้อง</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_phone" class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เบอร์โทร</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_status" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">สถานะ</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="role_std" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">บทบาท</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_birth" class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">วันเกิด</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <input type="checkbox" name="exportCols" value="Teach_addr" class="w-4 h-4 text-sky-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ที่อยู่</span>
                        </label>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">กรองข้อมูล (ไม่บังคับ)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">สถานะ</label>
                            <select id="exportFilterStatus" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200">
                                <option value="">-- ทั้งหมด --</option>
                                <option value="1">✅ ปกติ</option>
                                <option value="2">🔁 ย้าย</option>
                                <option value="3">🎖️ เกษียณ</option>
                                <option value="4">⚠️ ลาออก</option>
                                <option value="9">⚰️ เสียชีวิต</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">บทบาท</label>
                            <select id="exportFilterRole" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200">
                                <option value="">-- ทั้งหมด --</option>
                                <option value="T">ครู</option>
                                <option value="OF">เจ้าหน้าที่</option>
                                <option value="VP">รองผู้อำนวยการ</option>
                                <option value="DIR">ผู้อำนวยการ</option>
                                <option value="ADM">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Preview Count -->
                <div class="p-4 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 text-sky-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <div>
                            <p class="font-bold text-sky-700 dark:text-sky-300">ข้อมูลที่จะส่งออก</p>
                            <p class="text-sm text-sky-600 dark:text-sky-400"><span id="exportCount">0</span> รายการ</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer !border-0 p-4 md:p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all hover:bg-slate-300" data-dismiss="modal">ยกเลิก</button>
                <button type="button" id="btnDoExport" class="px-6 py-3 bg-sky-600 text-white rounded-xl font-bold shadow-lg shadow-sky-600/20 hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fas fa-download"></i> ดาวน์โหลด
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.teacher-table-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}
.teacher-table-heading h3 {
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 900;
    line-height: 1.25;
}
.dark .teacher-table-heading h3 { color: #fff; }
.teacher-table-kicker {
    color: #38bdf8;
    font-size: .65rem;
    font-weight: 900;
    letter-spacing: .16em;
    line-height: 1;
    text-transform: uppercase;
}
.teacher-table-count {
    flex-shrink: 0;
    border-radius: 999px;
    background: rgba(14, 165, 233, .1);
    color: #0284c7;
    font-size: .75rem;
    font-weight: 800;
    padding: .45rem .75rem;
}
.dark .teacher-table-count {
    background: rgba(14, 165, 233, .18);
    color: #7dd3fc;
}
.avatar-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 12px; cursor: pointer; border: 2px solid white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: transform 0.2s; }
.avatar-thumb:hover { transform: scale(1.1); }
.avatar-placeholder { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #64748b; font-size: 20px; }

@media (max-width: 767.98px) {
    .teacher-admin-page {
        margin-top: .25rem;
        max-width: 100%;
        overflow-x: hidden;
    }
    .teacher-admin-page .admin-page-header {
        gap: 1rem !important;
        max-width: 100%;
    }
    .teacher-admin-page .admin-page-header > div:first-child {
        align-items: center !important;
    }
    .teacher-admin-page .admin-page-header h2 {
        font-size: 1.18rem !important;
        line-height: 1.25 !important;
    }
    .teacher-admin-page .admin-page-header p {
        font-size: .55rem !important;
        letter-spacing: .12em !important;
    }
    .teacher-admin-page .admin-header-actions {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: .55rem !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .teacher-admin-page .admin-header-actions button {
        min-width: 0 !important;
        width: 100% !important;
        min-height: 2.6rem;
        padding: .65rem .5rem !important;
        border-radius: .8rem !important;
        box-shadow: 0 10px 22px -16px rgba(2, 132, 199, .75) !important;
    }
    .teacher-admin-page .admin-header-actions button span {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap !important;
    }
    .teacher-stats-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: .65rem !important;
    }
    .teacher-stats-grid > * {
        min-width: 0;
        padding: .85rem !important;
        border-radius: 1rem !important;
        box-shadow: 0 12px 28px -22px rgba(15, 23, 42, .55) !important;
    }
    .teacher-stats-grid > * > .flex {
        gap: .6rem !important;
        margin-bottom: .65rem !important;
    }
    .teacher-stats-grid h3 {
        font-size: 1.45rem !important;
        line-height: 1.1 !important;
    }
    .teacher-stats-grid p {
        font-size: .5rem !important;
        letter-spacing: .06em !important;
    }
    .teacher-charts-grid {
        display: flex !important;
        gap: .85rem !important;
        margin-left: 0;
        margin-right: 0;
        max-width: 100%;
        overflow-x: auto;
        padding: .15rem .1rem .85rem;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    .teacher-charts-grid::-webkit-scrollbar { display: none; }
    .teacher-chart-card {
        flex: 0 0 88%;
        max-width: 88%;
        scroll-snap-align: start;
        border-radius: 1.25rem !important;
        padding: 1rem !important;
    }
    .teacher-chart-card h4 {
        margin-bottom: .75rem !important;
        font-size: .85rem !important;
    }
    .teacher-chart-card .h-48 { height: 11.5rem; }
    .teacher-table-panel {
        border-radius: 1.25rem !important;
        padding: 1rem .85rem !important;
        max-width: 100%;
        overflow: hidden;
    }
    .teacher-table-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: .5rem;
        margin-bottom: .75rem;
    }
    .teacher-table-count {
        align-self: flex-start;
        font-size: .7rem;
        padding: .35rem .65rem;
    }
    #teacherTable .avatar-thumb,
    #teacherTable .avatar-placeholder { width: 40px; height: 40px; border-radius: 10px; }
    #teacherTable tbody td {
        grid-template-columns: minmax(5rem, 34%) minmax(0, 1fr) !important;
        gap: .6rem !important;
        font-size: .82rem;
        line-height: 1.35;
    }
    #teacherTable tbody td:nth-child(3) {
        font-size: .9rem;
        font-weight: 900;
    }
    #teacherTable tbody td:nth-child(5) span {
        display: block;
        margin-left: auto;
        max-width: 11rem;
    }
    #teacherTable tbody td:last-child > div {
        display: grid !important;
        grid-template-columns: repeat(3, 2.35rem);
        justify-content: end !important;
    }
    #teacherTable tbody td:last-child button {
        width: 2.35rem !important;
        height: 2.35rem !important;
        border-radius: .75rem !important;
    }
    #teacherTable_wrapper .row:first-child {
        align-items: stretch;
        gap: .65rem;
    }
    #teacherTable_filter input {
        min-height: 2.6rem;
    }
}
</style>

<script>
const API_URL = '../controllers/TeacherController.php';
const PHOTO_BASE_URL = '<?= $photoBaseUrl ?>';

let teacherTable;
let allTeachersData = [];

$(document).ready(function() {
    // Initialize DataTable
    teacherTable = $('#teacherTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: API_URL + '?action=list',
            dataSrc: function(json) {
                allTeachersData = json;
                updateStats(json);
                $('#teacherMobileCount').text(json.length + ' records');
                return json;
            }
        },
        columns: [
            { 
                data: 'Teach_photo',
                render: function(data, type, row) {
                    if (data) {
                        return `<img src="${PHOTO_BASE_URL}${data}" class="avatar-thumb" onerror="this.outerHTML='<div class=\\'avatar-placeholder\\'>👩‍🏫</div>'">`;
                    }
                    return `<div class="avatar-placeholder">👩‍🏫</div>`;
                },
                orderable: false
            },
            { data: 'Teach_id', className: 'font-bold text-sky-600' },
            { data: 'Teach_name', className: 'font-bold' },
            {
                data: null,
                render: function(row) {
                    const cls = row.Teach_class || '-';
                    const room = row.Teach_room || '-';
                    return `<span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 rounded-lg text-sm">ม.${cls}/${room}</span>`;
                },
                className: 'text-center'
            },
            { 
                data: 'Teach_major',
                render: function(data) {
                    return `<span class="text-sm">${data || '-'}</span>`;
                }
            },
            {
                data: 'Teach_status',
                render: function(data) {
                    const statusMap = {
                        '1': { label: 'ปกติ', color: 'emerald' },
                        '2': { label: 'ย้าย', color: 'sky' },
                        '3': { label: 'เกษียณ', color: 'slate' },
                        '4': { label: 'ลาออก', color: 'amber' },
                        '9': { label: 'เสียชีวิต', color: 'gray' },
                        '0': { label: 'ไม่ใช้งาน', color: 'rose' }
                    };
                    const s = statusMap[String(data)] || { label: data || '-', color: 'slate' };
                    return `<span class="px-2 py-1 bg-${s.color}-100 dark:bg-${s.color}-900/30 text-${s.color}-600 dark:text-${s.color}-400 rounded-lg text-xs font-bold">${s.label}</span>`;
                },
                className: 'text-center'
            },
            {
                data: 'role_std',
                render: function(data) {
                    const roleMap = { 'T': 'ครู', 'OF': 'เจ้าหน้าที่', 'VP': 'รองผอ.', 'DIR': 'ผอ.', 'ADM': 'Admin' };
                    return `<span class="text-sm font-bold">${roleMap[data] || data || '-'}</span>`;
                },
                className: 'text-center'
            },
            {
                data: 'Teach_id',
                render: function(data) {
                    return `
                        <div class="flex items-center justify-center gap-1">
                            <button class="editTeacherBtn w-8 h-8 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-all" data-id="${data}" title="แก้ไข">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button class="deleteTeacherBtn w-8 h-8 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all" data-id="${data}" title="ลบ">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <button class="resetPwdBtn w-8 h-8 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-all" data-id="${data}" title="รีเซ็ตรหัส">
                                <i class="fas fa-key text-xs"></i>
                            </button>
                        </div>
                    `;
                },
                orderable: false,
                className: 'text-center'
            }
        ],
        order: [[1, 'asc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
        drawCallback: function() {
            // Add data-label for mobile responsiveness
            $('#teacherTable tbody tr').each(function() {
                $(this).find('td').eq(0).attr('data-label', 'รูป');
                $(this).find('td').eq(1).attr('data-label', 'รหัส');
                $(this).find('td').eq(2).attr('data-label', 'ชื่อ');
                $(this).find('td').eq(3).attr('data-label', 'ชั้น/ห้อง');
                $(this).find('td').eq(4).attr('data-label', 'กลุ่มสาระ');
                $(this).find('td').eq(5).attr('data-label', 'สถานะ');
                $(this).find('td').eq(6).attr('data-label', 'บทบาท');
                $(this).find('td').eq(7).attr('data-label', 'จัดการ');
            });
            const info = teacherTable ? teacherTable.page.info() : null;
            if (info) {
                $('#teacherMobileCount').text(info.recordsDisplay + ' records');
            }
            renderCharts();
        }
        e.preventDefault();
        const res = await fetch(API_URL + '?action=update', { method: 'POST', body: new FormData(this) });
        const result = await res.json();
        if (result.success) {
            $('#editTeacherModal').modal('hide');
            teacherTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'แก้ไขข้อมูลเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถแก้ไขข้อมูลได้' });
        }
    });

    // Delete Teacher
    $('#teacherTable').on('click', '.deleteTeacherBtn', async function() {
        const id = $(this).data('id');
        const confirm = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'ข้อมูลจะถูกตั้งค่าเป็น "ไม่ใช้งาน"',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        });
        if (!confirm.isConfirmed) return;
        
        const res = await fetch(API_URL + '?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        });
        const result = await res.json();
        if (result.success) {
            teacherTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'ลบข้อมูลเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถลบข้อมูลได้' });
        }
    });

    // Reset Password
    $('#teacherTable').on('click', '.resetPwdBtn', async function() {
        const id = $(this).data('id');
        const confirm = await Swal.fire({
            title: 'รีเซ็ตรหัสผ่าน?',
            text: `รหัสผ่านจะถูกตั้งเป็น "${id}"`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ใช่, รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        });
        if (!confirm.isConfirmed) return;
        
        const res = await fetch(API_URL + '?action=resetpwd', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        });
        const result = await res.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'รีเซ็ตรหัสผ่านเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้' });
        }
    });

    // View Photo
    $('#teacherTable').on('click', '.avatar-thumb', function() {
        $('#photoModalImg').attr('src', $(this).attr('src'));
        $('#photoModal').modal('show');
    });

    // Export Functions
    $('#btnExport').click(() => {
        updateExportCount();
        $('#exportModal').modal('show');
    });

    // Select/Deselect all columns
    $('#selectAllCols').click(() => {
        $('input[name="exportCols"]').prop('checked', true);
    });
    $('#deselectAllCols').click(() => {
        $('input[name="exportCols"]').prop('checked', false);
    });

    // Update export count when filters change
    $('#exportFilterStatus, #exportFilterRole').change(updateExportCount);

    function updateExportCount() {
        const filteredData = getFilteredData();
        $('#exportCount').text(filteredData.length);
    }

    function getFilteredData() {
        let data = [...allTeachersData];
        const statusFilter = $('#exportFilterStatus').val();
        const roleFilter = $('#exportFilterRole').val();
        
        if (statusFilter) {
            data = data.filter(r => String(r.Teach_status) === statusFilter);
        }
        if (roleFilter) {
            data = data.filter(r => r.role_std === roleFilter);
        }
        return data;
    }

    // Do Export
    $('#btnDoExport').click(function() {
        const format = $('input[name="exportFormat"]:checked').val();
        const selectedCols = $('input[name="exportCols"]:checked').map((_, el) => el.value).get();
        
        if (selectedCols.length === 0) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือกคอลัมน์', text: 'เลือกอย่างน้อย 1 คอลัมน์' });
            return;
        }
        
        const data = getFilteredData();
        if (data.length === 0) {
            Swal.fire({ icon: 'warning', title: 'ไม่มีข้อมูล', text: 'ไม่พบข้อมูลตามเงื่อนไขที่เลือก' });
            return;
        }
        
        if (format === 'excel') {
            exportToExcel(data, selectedCols);
        } else {
            exportToPDF(data, selectedCols);
        }
        
        $('#exportModal').modal('hide');
    });

    // Column name mapping
    const colNames = {
        'Teach_id': 'รหัสครู',
        'Teach_name': 'ชื่อ-สกุล',
        'Teach_sex': 'เพศ',
        'Teach_major': 'กลุ่มสาระ',
        'Teach_class': 'ชั้น',
        'Teach_room': 'ห้อง',
        'Teach_phone': 'เบอร์โทร',
        'Teach_status': 'สถานะ',
        'role_std': 'บทบาท',
        'Teach_birth': 'วันเกิด',
        'Teach_addr': 'ที่อยู่'
    };

    const statusLabels = { '1': 'ปกติ', '2': 'ย้าย', '3': 'เกษียณ', '4': 'ลาออก', '9': 'เสียชีวิต', '0': 'ไม่ใช้งาน' };
    const roleLabels = { 'T': 'ครู', 'OF': 'เจ้าหน้าที่', 'VP': 'รองผู้อำนวยการ', 'DIR': 'ผู้อำนวยการ', 'ADM': 'Admin' };

    function formatCellValue(col, value) {
        if (col === 'Teach_status') return statusLabels[String(value)] || value || '-';
        if (col === 'role_std') return roleLabels[value] || value || '-';
        if (col === 'Teach_class') return value ? 'ม.' + value : '-';
        return value || '-';
    }

    function exportToExcel(data, cols) {
        // Create CSV with BOM for Thai support
        let csv = '\uFEFF';
        csv += cols.map(c => colNames[c]).join(',') + '\n';
        
        data.forEach(row => {
            const values = cols.map(col => {
                let val = formatCellValue(col, row[col]);
                // Escape quotes and wrap in quotes if contains comma
                val = String(val).replace(/"/g, '""');
                if (val.includes(',') || val.includes('\n')) {
                    val = '"' + val + '"';
                }
                return val;
            });
            csv += values.join(',') + '\n';
        });
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `ข้อมูลครูและบุคลากร_${new Date().toLocaleDateString('th-TH')}.csv`;
        link.click();
        URL.revokeObjectURL(url);
        
        Swal.fire({ icon: 'success', title: 'ส่งออกสำเร็จ!', text: 'ดาวน์โหลดไฟล์ Excel เรียบร้อย', timer: 2000, showConfirmButton: false });
    }

    function exportToPDF(data, cols) {
        // Create printable HTML
        const printWindow = window.open('', '_blank');
        const now = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
        
        let html = `
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>รายงานข้อมูลครูและบุคลากร</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: 'Sarabun', 'TH Sarabun New', sans-serif; font-size: 14px; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #0ea5e9; }
                .header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
                .header p { color: #64748b; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 12px 8px; text-align: left; font-weight: 600; font-size: 12px; }
                td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
                tr:nth-child(even) { background: #f8fafc; }
                tr:hover { background: #f1f5f9; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 12px; }
                .stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
                .stat-box { text-align: center; padding: 15px 25px; background: #f1f5f9; border-radius: 12px; }
                .stat-box .number { font-size: 28px; font-weight: 700; color: #0ea5e9; }
                .stat-box .label { font-size: 12px; color: #64748b; }
                @media print { body { padding: 0; } .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📋 รายงานข้อมูลครูและบุคลากร</h1>
                <p>โรงเรียนพิชัย • วันที่พิมพ์: ${now}</p>
            </div>
            <div class="stats">
                <div class="stat-box">
                    <div class="number">${data.length}</div>
                    <div class="label">จำนวนทั้งหมด</div>
                </div>
            </div>
            <table>
                <thead><tr>
                    <th style="width: 40px;">#</th>
                    ${cols.map(c => `<th>${colNames[c]}</th>`).join('')}
                </tr></thead>
                <tbody>
                    ${data.map((row, idx) => `
                        <tr>
                            <td>${idx + 1}</td>
                            ${cols.map(c => `<td>${formatCellValue(c, row[c])}</td>`).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            <div class="footer">
                พิมพ์จากระบบ STD Care • ${now} • หน้า 1
            </div>
            <script>window.onload = function() { window.print(); }<\/script>
        </body>
        </html>
        `;
        
        printWindow.document.write(html);
        printWindow.document.close();
        
        Swal.fire({ icon: 'success', title: 'ส่งออกสำเร็จ!', text: 'เปิดหน้าต่างพิมพ์ PDF เรียบร้อย', timer: 2000, showConfirmButton: false });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
