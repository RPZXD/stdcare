<?php
/**
 * View: Admin Teacher Data
 * Modern UI with Tailwind CSS, Glassmorphism & Full CRUD
 */
ob_start();

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

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-sky-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-chalkboard-teacher"></i>
                </span>
                จัดการ <span class="text-sky-600 italic">ครูและบุคลากร</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Teacher & Staff Management</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button id="btnExport" class="px-6 py-3 bg-sky-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-sky-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-file-export"></i> ส่งออกข้อมูล
            </button>
            <button id="btnAddTeacher" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-user-plus"></i> เพิ่มข้อมูลครู
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รวมทั้งหมด</p>
                    <h3 id="totalTeachers" class="text-3xl font-black text-slate-800 dark:text-white">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ปกติ</p>
                    <h3 id="activeTeachers" class="text-3xl font-black text-emerald-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-chalkboard text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ครู</p>
                    <h3 id="teacherCount" class="text-3xl font-black text-sky-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] border border-white/50 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เจ้าหน้าที่</p>
                    <h3 id="staffCount" class="text-3xl font-black text-amber-600">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-sky-500"></i> สถานะ
            </h4>
            <div class="h-48">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-layer-group text-indigo-500"></i> กลุ่มสาระ (Top 5)
            </h4>
            <div class="h-48">
                <canvas id="majorChart"></canvas>
            </div>
        </div>
        <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
            <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-user-tag text-rose-500"></i> บทบาท
            </h4>
            <div class="h-48">
                <canvas id="roleChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
        <div class="overflow-x-auto">
            <table id="teacherTable" class="w-full text-left border-separate border-spacing-y-2">
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
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-plus"></i> เพิ่มข้อมูลครู
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="addTeacherForm">
                <div class="modal-body p-8 bg-gradient-to-br from-white to-emerald-50 dark:from-slate-900 dark:to-slate-800">
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
                    </div>
                </div>
                <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
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
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลครู
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="editTeacherForm">
                <input type="hidden" name="editTeach_id_old">
                <div class="modal-body p-8 bg-gradient-to-br from-white to-amber-50 dark:from-slate-900 dark:to-slate-800">
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
                    </div>
                </div>
                <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
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
            <div class="modal-header bg-gradient-to-r from-sky-500 to-blue-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-file-export"></i> ส่งออกข้อมูลครูและบุคลากร
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-8 bg-gradient-to-br from-white to-sky-50 dark:from-slate-900 dark:to-slate-800">
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
            <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all hover:bg-slate-300" data-dismiss="modal">ยกเลิก</button>
                <button type="button" id="btnDoExport" class="px-6 py-3 bg-sky-600 text-white rounded-xl font-bold shadow-lg shadow-sky-600/20 hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fas fa-download"></i> ดาวน์โหลด
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 12px; cursor: pointer; border: 2px solid white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: transform 0.2s; }
.avatar-thumb:hover { transform: scale(1.1); }
.avatar-placeholder { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #64748b; font-size: 20px; }

/* Mobile Card View */
@media (max-width: 1024px) {
    #teacherTable thead { display: none; }
    #teacherTable tbody tr {
        display: block;
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .dark #teacherTable tbody tr { background: rgba(30, 41, 59, 0.5); }
    #teacherTable tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    #teacherTable tbody td:last-child { border-bottom: none; }
    #teacherTable tbody td::before {
        content: attr(data-label);
        font-weight: 800;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
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
            renderCharts();
        }
    });

    // Update stats cards
    function updateStats(data) {
        $('#totalTeachers').text(data.length);
        $('#activeTeachers').text(data.filter(r => String(r.Teach_status) === '1').length);
        $('#teacherCount').text(data.filter(r => r.role_std === 'T').length);
        $('#staffCount').text(data.filter(r => r.role_std === 'OF').length);
    }

    // Render charts
    function renderCharts() {
        if (!allTeachersData.length) return;
        
        // Status Chart
        const statusCounts = { '1': 0, '2': 0, '3': 0, '4': 0, '9': 0, '0': 0 };
        allTeachersData.forEach(r => { statusCounts[String(r.Teach_status || '0')]++; });
        
        if (window.statusChartObj) window.statusChartObj.destroy();
        window.statusChartObj = new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['ปกติ', 'ย้าย', 'เกษียณ', 'ลาออก', 'เสียชีวิต', 'ไม่ใช้งาน'],
                datasets: [{
                    data: [statusCounts['1'], statusCounts['2'], statusCounts['3'], statusCounts['4'], statusCounts['9'], statusCounts['0']],
                    backgroundColor: ['#10b981', '#0ea5e9', '#64748b', '#f59e0b', '#1f2937', '#f43f5e']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });

        // Major Chart (Top 5)
        const majorCounts = {};
        allTeachersData.forEach(r => { const m = r.Teach_major || 'ไม่ระบุ'; majorCounts[m] = (majorCounts[m] || 0) + 1; });
        const topMajors = Object.entries(majorCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);
        
        if (window.majorChartObj) window.majorChartObj.destroy();
        window.majorChartObj = new Chart(document.getElementById('majorChart'), {
            type: 'bar',
            data: {
                labels: topMajors.map(([k]) => k.length > 10 ? k.substring(0, 10) + '...' : k),
                datasets: [{ data: topMajors.map(([, v]) => v), backgroundColor: '#6366f1', borderRadius: 8 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        // Role Chart
        const roleCounts = {};
        allTeachersData.forEach(r => { const role = r.role_std || 'UNK'; roleCounts[role] = (roleCounts[role] || 0) + 1; });
        const roleMap = { 'T': 'ครู', 'OF': 'เจ้าหน้าที่', 'VP': 'รองผอ.', 'DIR': 'ผอ.', 'ADM': 'Admin', 'UNK': 'อื่นๆ' };
        
        if (window.roleChartObj) window.roleChartObj.destroy();
        window.roleChartObj = new Chart(document.getElementById('roleChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(roleCounts).map(k => roleMap[k] || k),
                datasets: [{ data: Object.values(roleCounts), backgroundColor: ['#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899', '#10b981', '#64748b'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });
    }

    // Add Teacher
    $('#btnAddTeacher').click(() => {
        $('#addTeacherForm')[0].reset();
        $('#addTeacherModal').modal('show');
    });

    $('#addTeacherForm').submit(async function(e) {
        e.preventDefault();
        const res = await fetch(API_URL + '?action=create', { method: 'POST', body: new FormData(this) });
        const result = await res.json();
        if (result.success) {
            $('#addTeacherModal').modal('hide');
            teacherTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'เพิ่มข้อมูลครูเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถเพิ่มข้อมูลได้' });
        }
    });

    // Edit Teacher
    $('#teacherTable').on('click', '.editTeacherBtn', async function() {
        const id = $(this).data('id');
        const res = await fetch(API_URL + '?action=get&id=' + id);
        const data = await res.json();
        if (data && data.Teach_id) {
            $('[name="editTeach_id_old"]').val(data.Teach_id);
            $('[name="editTeach_id"]').val(data.Teach_id);
            $('[name="editTeach_name"]').val(data.Teach_name);
            $('[name="editTeach_major"]').val(data.Teach_major);
            $('[name="editTeach_class"]').val(data.Teach_class);
            $('[name="editTeach_room"]').val(data.Teach_room);
            $('[name="editTeach_status"]').val(data.Teach_status);
            $('[name="editrole_std"]').val(data.role_std);
            $('#editTeacherModal').modal('show');
        }
    });

    $('#editTeacherForm').submit(async function(e) {
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
