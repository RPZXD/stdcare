<?php
/**
 * View: Admin Student Data
 * Modern UI with Tailwind CSS, Glassmorphism & Full CRUD
 */
ob_start();

// Status options
$statuses = [
    '1' => ['label' => 'ปกติ', 'color' => 'emerald', 'icon' => '✅'],
    '2' => ['label' => 'จบการศึกษา', 'color' => 'sky', 'icon' => '🎓'],
    '3' => ['label' => 'ย้ายโรงเรียน', 'color' => 'amber', 'icon' => '🚚'],
    '4' => ['label' => 'ออกกลางคัน', 'color' => 'rose', 'icon' => '❌'],
    '9' => ['label' => 'เสียชีวิต', 'color' => 'gray', 'icon' => '🕊️']
];

$prefixes = ['เด็กชาย', 'เด็กหญิง', 'นาย', 'นางสาว'];
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-user-graduate"></i>
                </span>
                จัดการ <span class="text-indigo-600 italic">ข้อมูลนักเรียน</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Student Data Management</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button id="btnExport" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-file-export"></i> ส่งออกข้อมูล
            </button>
            <button id="btnAddStudent" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-user-plus"></i> เพิ่มนักเรียน
            </button>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-8">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-filter"></i>
                </div>
                <span class="text-sm font-black text-slate-600 dark:text-slate-300">ตัวกรอง</span>
            </div>
            
            <div class="flex-1 flex flex-wrap gap-3">
                <select id="filterClass" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all min-w-[120px]">
                    <option value="">ทุกชั้น</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                    <?php endfor; ?>
                </select>
                
                <select id="filterRoom" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all min-w-[120px]">
                    <option value="">ทุกห้อง</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>">ห้อง <?= $i ?></option>
                    <?php endfor; ?>
                </select>
                
                <select id="filterStatus" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all min-w-[130px]">
                    <option value="">ทุกสถานะ</option>
                    <?php foreach ($statuses as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v['icon'] ?> <?= $v['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ทั้งหมด</p>
                    <h3 id="totalStudents" class="text-2xl font-black text-slate-800 dark:text-white">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 text-sky-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-mars text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ชาย</p>
                    <h3 id="maleCount" class="text-2xl font-black text-sky-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 text-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-venus text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">หญิง</p>
                    <h3 id="femaleCount" class="text-2xl font-black text-pink-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">กำลังศึกษา</p>
                    <h3 id="activeCount" class="text-2xl font-black text-emerald-600">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-effect rounded-[2.5rem] p-6 md:p-8 shadow-xl border-t border-white/50">
        <div class="overflow-x-auto">
            <table id="studentTable" class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-slate-800/50">
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center w-14">รูป</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-12">เลขที่</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น/ห้อง</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">สถานะ</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-plus"></i> เพิ่มนักเรียนใหม่
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="addStudentForm">
                <div class="modal-body p-6 md:p-8 bg-gradient-to-br from-white to-emerald-50 dark:from-slate-900 dark:to-slate-800 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รหัสนักเรียน *</label>
                            <input type="text" name="addStu_id" required maxlength="10" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เลขที่</label>
                            <select name="addStu_no" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                                <option value="">-- เลือก --</option>
                                <?php for ($i = 1; $i <= 50; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">คำนำหน้า *</label>
                            <select name="addStu_pre" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                                <option value="">-- เลือก --</option>
                                <?php foreach ($prefixes as $p): ?>
                                <option value="<?= $p ?>"><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อ *</label>
                            <input type="text" name="addStu_name" required maxlength="100" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">นามสกุล *</label>
                            <input type="text" name="addStu_sur" required maxlength="100" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชั้น</label>
                                <select name="addStu_major" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                                    <option value="">-- เลือก --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ห้อง</label>
                                <select name="addStu_room" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/20 outline-none">
                                    <option value="">-- เลือก --</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลนักเรียน
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="editStudentForm">
                <input type="hidden" name="editStu_id_old">
                <div class="modal-body p-6 md:p-8 bg-gradient-to-br from-white to-amber-50 dark:from-slate-900 dark:to-slate-800 max-h-[70vh] overflow-y-auto">
                    <!-- Basic Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-amber-200 pb-2">
                        <i class="fas fa-user text-amber-500"></i> ข้อมูลพื้นฐาน
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รหัสนักเรียน</label>
                            <input type="text" name="editStu_id" readonly class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เลขประชาชน</label>
                            <input type="text" name="editStu_citizenid" maxlength="13" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เลขที่</label>
                            <select name="editStu_no" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                                <?php for ($i = 1; $i <= 50; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">คำนำหน้า</label>
                            <select name="editStu_pre" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                                <?php foreach ($prefixes as $p): ?>
                                <option value="<?= $p ?>"><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อ</label>
                            <input type="text" name="editStu_name" maxlength="50" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">นามสกุล</label>
                            <input type="text" name="editStu_sur" maxlength="50" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อเล่น</label>
                            <input type="text" name="editStu_nick" maxlength="30" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">วันเกิด</label>
                            <input type="date" name="editStu_birth" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เบอร์โทร</label>
                            <input type="tel" name="editStu_phone" maxlength="15" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                    </div>

                    <!-- Education Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-amber-200 pb-2">
                        <i class="fas fa-school text-amber-500"></i> ข้อมูลการศึกษา
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชั้น</label>
                            <select name="editStu_major" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>">ม.<?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ห้อง</label>
                            <select name="editStu_room" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">สถานะ</label>
                            <select name="editStu_status" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                                <?php foreach ($statuses as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['icon'] ?> <?= $v['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Parent Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-amber-200 pb-2">
                        <i class="fas fa-users text-amber-500"></i> ข้อมูลผู้ปกครอง
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อบิดา</label>
                            <input type="text" name="editFather_name" maxlength="50" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อมารดา</label>
                            <input type="text" name="editMother_name" maxlength="50" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เบอร์ผู้ปกครอง</label>
                            <input type="tel" name="editPar_phone" maxlength="15" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-amber-500/20 outline-none">
                        </div>
                    </div>
                </div>
                <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="viewPhotoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-body p-4 bg-slate-100 dark:bg-slate-900 text-center">
                <img id="viewPhotoImg" src="" class="max-w-full max-h-[60vh] rounded-2xl mx-auto">
                <h4 id="viewPhotoName" class="mt-4 font-black text-slate-800 dark:text-white"></h4>
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
            <div class="modal-header bg-gradient-to-r from-indigo-500 to-purple-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-file-export"></i> ส่งออกข้อมูลนักเรียน
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-8 bg-gradient-to-br from-white to-indigo-50 dark:from-slate-900 dark:to-slate-800">
                <!-- Export Format Selection -->
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">รูปแบบการส่งออก</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl cursor-pointer hover:border-emerald-500 transition-all">
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
                        <label class="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 border-2 border-rose-200 dark:border-rose-800 rounded-2xl cursor-pointer hover:border-rose-500 transition-all">
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
                            <button type="button" id="selectAllCols" class="px-3 py-1 text-xs font-bold bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-all">เลือกทั้งหมด</button>
                            <button type="button" id="deselectAllCols" class="px-3 py-1 text-xs font-bold bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all">ยกเลิกทั้งหมด</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="columnCheckboxes">
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_id" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">รหัสนักเรียน</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_no" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เลขที่</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_pre" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">คำนำหน้า</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_name" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ชื่อ</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_sur" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">นามสกุล</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_major" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ชั้น</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_room" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">ห้อง</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_status" checked class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">สถานะ</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_phone" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เบอร์โทร</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_citizenid" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เลขประชาชน</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Stu_birth" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">วันเกิด</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="checkbox" name="exportCols" value="Par_phone" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="font-bold text-sm text-slate-700 dark:text-white">เบอร์ผู้ปกครอง</span>
                        </label>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">กรองข้อมูล (ไม่บังคับ)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">ชั้น</label>
                            <select id="exportFilterClass" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200">
                                <option value="">-- ทั้งหมด --</option>
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>">	ม.<?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">ห้อง</label>
                            <select id="exportFilterRoom" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200">
                                <option value="">-- ทั้งหมด --</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>">ห้อง <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">สถานะ</label>
                            <select id="exportFilterStatus" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200">
                                <option value="">-- ทั้งหมด --</option>
                                <?php foreach ($statuses as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['icon'] ?> <?= $v['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Preview Count -->
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <div>
                            <p class="font-bold text-indigo-700 dark:text-indigo-300">ข้อมูลที่จะส่งออก</p>
                            <p class="text-sm text-indigo-600 dark:text-indigo-400"><span id="exportCount">0</span> รายการ</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all hover:bg-slate-300" data-dismiss="modal">ยกเลิก</button>
                <button type="button" id="btnDoExport" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/20 hover:scale-105 transition-all flex items-center gap-2">
                    <i class="fas fa-download"></i> ดาวน์โหลด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Profile Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-indigo-600 to-purple-700 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-graduate"></i> <span id="profileName">ข้อมูลนักเรียน</span>
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0 bg-gradient-to-br from-white to-indigo-50 dark:from-slate-900 dark:to-slate-800 max-h-[75vh] overflow-y-auto">
                <div id="profileContent" class="p-8">
                    <div class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i>
                        <p class="mt-4 text-slate-500 font-bold">กำลังโหลดข้อมูล...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800">
                <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-placeholder { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 700; font-size: 14px; }
.btn-sm { padding: 6px 8px; border: none; background: none; cursor: pointer; font-size: 15px; transition: all 0.15s; border-radius: 8px; }
.btn-sm:hover { transform: scale(1.15); background: rgba(0,0,0,0.05); }
.action-buttons { display: flex; gap: 2px; justify-content: center; flex-wrap: nowrap; }

/* Make table more compact */
#studentTable th { white-space: nowrap; }
#studentTable td { vertical-align: middle; }
#studentTable .dataTables_wrapper { overflow-x: auto; }

@media (max-width: 768px) {
    #studentTable { font-size: 12px; }
    #studentTable th, #studentTable td { padding: 6px 4px; }
    .btn-sm { padding: 4px 5px; font-size: 13px; }
    .action-buttons { gap: 0; }
}
</style>

<script>
const PHOTO_BASE_URL = '../uploads/student/';
let studentTable;
let allStudentsData = [];

$(document).ready(function() {
    studentTable = $('#studentTable').DataTable({
        processing: true,
        serverSide: false,
        deferRender: true, // Performance: render rows only when visible
        ajax: {
            url: '../controllers/StudentController.php?action=list_all',
            dataSrc: function(json) {
                allStudentsData = json.data || json;
                setTimeout(() => updateStats(allStudentsData), 0);
                return allStudentsData;
            }
        },
        columns: [
            {
                data: 'Stu_id',
                render: function(data, type, row) {
                    // Simple placeholder - no external API call
                    const initial = ((row.Stu_name || 'S')[0]).toUpperCase();
                    const isMale = ['เด็กชาย', 'นาย'].includes(row.Stu_pre);
                    const bg = isMale ? '#3b82f6' : '#ec4899';
                    return `<div class="avatar-placeholder" style="background:${bg};color:#fff" data-id="${data}">${initial}</div>`;
                },
                orderable: false,
                width: '50px'
            },
            { data: 'Stu_no', className: 'text-center', width: '50px' },
            { data: 'Stu_id', className: 'font-bold text-indigo-600', width: '100px' },
            {
                data: 'Stu_name',
                render: function(data, type, row) {
                    return `${row.Stu_pre || ''}${data || ''} ${row.Stu_sur || ''}`;
                }
            },
            {
                data: 'Stu_major',
                render: function(data, type, row) {
                    return `ม.${data || '-'}/${row.Stu_room || '-'}`;
                },
                className: 'text-center',
                width: '80px'
            },
            {
                data: 'Stu_status',
                render: function(data) {
                    const m = {'1':'ปกติ','2':'จบ','3':'ย้าย','4':'ออก','9':'✝'};
                    return m[String(data)] || '-';
                },
                className: 'text-center',
                width: '60px'
            },
            {
                data: 'Stu_id',
                render: function(data) {
                    return `<div class="action-buttons">
                        <button class="viewProfileBtn btn-sm" data-id="${data}" title="ดูโปรไฟล์">👁️</button>
                        <button class="editStudentBtn btn-sm" data-id="${data}" title="แก้ไข">✏️</button>
                        <button class="deleteStudentBtn btn-sm" data-id="${data}" title="ลบ">🗑️</button>
                        <button class="resetPwdBtn btn-sm" data-id="${data}" title="รีเซ็ตรหัส">🔑</button>
                    </div>`;
                },
                orderable: false,
                className: 'text-center',
                width: '140px'
            }
        ],
        order: [[1, 'asc']],
        pageLength: 50,
        lengthMenu: [25, 50, 100],
        language: { 
            processing: '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-indigo-600"></i></div>',
            zeroRecords: 'ไม่พบข้อมูล',
            info: 'แสดง _START_-_END_ จาก _TOTAL_',
            infoEmpty: 'ไม่มีข้อมูล',
            lengthMenu: 'แสดง _MENU_ รายการ',
            search: 'ค้นหา:',
            paginate: { first: '«', previous: '‹', next: '›', last: '»' }
        }
    });

    function updateStats(data) {
        const total = data.length;
        const male = data.filter(r => ['เด็กชาย', 'นาย'].includes(r.Stu_pre)).length;
        const female = total - male;
        const active = data.filter(r => String(r.Stu_status) === '1').length;
        $('#totalStudents').text(total);
        $('#maleCount').text(male);
        $('#femaleCount').text(female);
        $('#activeCount').text(active);
    }

    // Filters - use custom search function
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'studentTable') return true;
        const row = allStudentsData[dataIndex];
        if (!row) return true;
        
        const cls = $('#filterClass').val();
        const room = $('#filterRoom').val();
        const status = $('#filterStatus').val();
        
        if (cls && String(row.Stu_major) !== cls) return false;
        if (room && String(row.Stu_room) !== room) return false;
        if (status && String(row.Stu_status) !== status) return false;
        return true;
    });

    $('#filterClass, #filterRoom, #filterStatus').change(function() {
        studentTable.draw();
    });

    // View Profile
    $('#studentTable').on('click', '.viewProfileBtn', async function() {
        const id = $(this).data('id');
        $('#profileName').text('กำลังโหลด...');
        $('#profileContent').html(`
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i>
                <p class="mt-4 text-slate-500 font-bold">กำลังโหลดข้อมูล...</p>
            </div>
        `);
        $('#viewProfileModal').modal('show');
        
        try {
            const res = await fetch('../controllers/StudentController.php?action=get&id=' + id);
            const data = await res.json();
            
            if (data && data.Stu_id) {
                $('#profileName').text(`${data.Stu_pre || ''}${data.Stu_name || ''} ${data.Stu_sur || ''}`);
                $('#profileContent').html(renderProfileContent(data));
            } else {
                $('#profileContent').html(`
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-circle text-4xl text-rose-500"></i>
                        <p class="mt-4 text-rose-500 font-bold">ไม่พบข้อมูลนักเรียน</p>
                    </div>
                `);
            }
        } catch (error) {
            $('#profileContent').html(`
                <div class="text-center py-12">
                    <i class="fas fa-times-circle text-4xl text-rose-500"></i>
                    <p class="mt-4 text-rose-500 font-bold">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>
                </div>
            `);
        }
    });

    function renderProfileContent(data) {
        const statusMap = { '1': 'ปกติ', '2': 'จบการศึกษา', '3': 'ย้ายโรงเรียน', '4': 'ออกกลางคัน', '9': 'เสียชีวิต' };
        const statusColorMap = { '1': 'emerald', '2': 'sky', '3': 'amber', '4': 'rose', '9': 'gray' };
        const status = statusMap[data.Stu_status] || 'ไม่ระบุ';
        const statusColor = statusColorMap[data.Stu_status] || 'slate';
        const isMale = ['เด็กชาย', 'นาย'].includes(data.Stu_pre);
        const avatarBg = isMale ? '#3b82f6' : '#ec4899';
        const initial = (data.Stu_name || 'S')[0].toUpperCase();
        const photoUrl = data.Stu_picture ? `https://std.phichai.ac.th/photo/${data.Stu_picture}` : '';
        
        return `
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left: Photo & Basic Info -->
                <div class="lg:w-1/3">
                    <div class="text-center">
                        ${photoUrl ? 
                            `<img src="${photoUrl}" class="w-40 h-40 rounded-3xl mx-auto border-4 border-white shadow-xl object-cover" onerror="this.outerHTML='<div class=\\'w-40 h-40 rounded-3xl mx-auto border-4 border-white shadow-xl flex items-center justify-center text-5xl font-black text-white\\' style=\\'background:${avatarBg}\\'>${initial}</div>'">` :
                            `<div class="w-40 h-40 rounded-3xl mx-auto border-4 border-white shadow-xl flex items-center justify-center text-5xl font-black text-white" style="background:${avatarBg}">${initial}</div>`
                        }
                        <h3 class="mt-4 text-xl font-black text-slate-800 dark:text-white">${data.Stu_pre || ''}${data.Stu_name || ''} ${data.Stu_sur || ''}</h3>
                        <p class="text-slate-500 font-bold">${data.Stu_nick ? `"${data.Stu_nick}"` : ''}</p>
                        <p class="mt-2 text-lg font-black text-indigo-600">รหัส: ${data.Stu_id}</p>
                        <span class="inline-block mt-2 px-4 py-2 bg-${statusColor}-100 text-${statusColor}-600 rounded-xl font-bold text-sm">${status}</span>
                    </div>
                    
                    <div class="mt-6 glass-effect rounded-2xl p-4 border border-white/50">
                        <h4 class="text-sm font-black text-slate-700 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-indigo-500"></i> ข้อมูลการศึกษา
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="text-center p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ชั้น</p>
                                <p class="text-xl font-black text-indigo-600">ม.${data.Stu_major || '-'}</p>
                            </div>
                            <div class="text-center p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ห้อง</p>
                                <p class="text-xl font-black text-indigo-600">${data.Stu_room || '-'}</p>
                            </div>
                            <div class="text-center p-3 bg-slate-50 dark:bg-slate-800 rounded-xl col-span-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">เลขที่</p>
                                <p class="text-xl font-black text-indigo-600">${data.Stu_no || '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Detailed Info -->
                <div class="lg:w-2/3 space-y-6">
                    <!-- Personal Info -->
                    <div class="glass-effect rounded-2xl p-6 border border-white/50">
                        <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700 pb-3">
                            <i class="fas fa-id-card text-indigo-500"></i> ข้อมูลส่วนตัว
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">เลขประชาชน</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Stu_citizenid || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">วันเกิด</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Stu_birth || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">เบอร์โทร</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Stu_phone || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ศาสนา</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Stu_religion || '-'}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ที่อยู่</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Stu_addr || '-'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Family Info -->
                    <div class="glass-effect rounded-2xl p-6 border border-white/50">
                        <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700 pb-3">
                            <i class="fas fa-users text-indigo-500"></i> ข้อมูลครอบครัว
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-xs font-black text-sky-600 mb-2">👨 บิดา</h5>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ชื่อ</p>
                                <p class="font-bold text-slate-700 dark:text-white mb-2">${data.Father_name || '-'}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">อาชีพ</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Father_occu || '-'}</p>
                            </div>
                            <div>
                                <h5 class="text-xs font-black text-pink-600 mb-2">👩 มารดา</h5>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ชื่อ</p>
                                <p class="font-bold text-slate-700 dark:text-white mb-2">${data.Mother_name || '-'}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">อาชีพ</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Mother_occu || '-'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Guardian Info -->
                    <div class="glass-effect rounded-2xl p-6 border border-white/50">
                        <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700 pb-3">
                            <i class="fas fa-user-shield text-indigo-500"></i> ข้อมูลผู้ปกครอง
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ชื่อผู้ปกครอง</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Par_name || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ความสัมพันธ์</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Par_relate || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">เบอร์โทรผู้ปกครอง</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Par_phone || '-'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">อาชีพ</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Par_occu || '-'}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">ที่อยู่ผู้ปกครอง</p>
                                <p class="font-bold text-slate-700 dark:text-white">${data.Par_addr || '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Add Student
    $('#btnAddStudent').click(() => {
        $('#addStudentForm')[0].reset();
        $('#addStudentModal').modal('show');
    });

    $('#addStudentForm').submit(async function(e) {
        e.preventDefault();
        const res = await fetch('../controllers/StudentController.php?action=create', { method: 'POST', body: new FormData(this) });
        const result = await res.json();
        if (result.success) {
            $('#addStudentModal').modal('hide');
            studentTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'เพิ่มนักเรียนเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถเพิ่มข้อมูลได้' });
        }
    });

    // Edit Student
    $('#studentTable').on('click', '.editStudentBtn', async function() {
        const id = $(this).data('id');
        const res = await fetch('../controllers/StudentController.php?action=get&id=' + id);
        const data = await res.json();
        if (data && data.Stu_id) {
            $('[name="editStu_id_old"]').val(data.Stu_id);
            $('[name="editStu_id"]').val(data.Stu_id);
            $('[name="editStu_citizenid"]').val(data.Stu_citizenid);
            $('[name="editStu_no"]').val(data.Stu_no);
            $('[name="editStu_pre"]').val(data.Stu_pre);
            $('[name="editStu_name"]').val(data.Stu_name);
            $('[name="editStu_sur"]').val(data.Stu_sur);
            $('[name="editStu_nick"]').val(data.Stu_nick);
            $('[name="editStu_birth"]').val(data.Stu_birth);
            $('[name="editStu_phone"]').val(data.Stu_phone);
            $('[name="editStu_major"]').val(data.Stu_major);
            $('[name="editStu_room"]').val(data.Stu_room);
            $('[name="editStu_status"]').val(data.Stu_status);
            $('[name="editFather_name"]').val(data.Father_name);
            $('[name="editMother_name"]').val(data.Mother_name);
            $('[name="editPar_phone"]').val(data.Par_phone);
            $('#editStudentModal').modal('show');
        }
    });

    $('#editStudentForm').submit(async function(e) {
        e.preventDefault();
        const res = await fetch('../controllers/StudentController.php?action=update', { method: 'POST', body: new FormData(this) });
        const result = await res.json();
        if (result.success) {
            $('#editStudentModal').modal('hide');
            studentTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'แก้ไขข้อมูลเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถแก้ไขข้อมูลได้' });
        }
    });

    // Delete Student
    $('#studentTable').on('click', '.deleteStudentBtn', async function() {
        const id = $(this).data('id');
        const confirm = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'ข้อมูลนักเรียนจะถูกลบ',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        });
        if (!confirm.isConfirmed) return;
        
        const res = await fetch('../controllers/StudentController.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        });
        const result = await res.json();
        if (result.success) {
            studentTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    });

    // Reset Password
    $('#studentTable').on('click', '.resetPwdBtn', async function() {
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
        
        const res = await fetch('../controllers/StudentController.php?action=resetpwd', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        });
        const result = await res.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'รีเซ็ตรหัสผ่านเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    });

    // View Photo
    $('#studentTable').on('click', '.avatar-thumb', function() {
        $('#viewPhotoImg').attr('src', $(this).attr('src'));
        $('#viewPhotoName').text($(this).data('name'));
        $('#viewPhotoModal').modal('show');
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
    $('#exportFilterClass, #exportFilterRoom, #exportFilterStatus').change(updateExportCount);

    function updateExportCount() {
        const filteredData = getFilteredExportData();
        $('#exportCount').text(filteredData.length);
    }

    function getFilteredExportData() {
        let data = [...allStudentsData];
        const classFilter = $('#exportFilterClass').val();
        const roomFilter = $('#exportFilterRoom').val();
        const statusFilter = $('#exportFilterStatus').val();
        
        if (classFilter) {
            data = data.filter(r => String(r.Stu_major) === classFilter);
        }
        if (roomFilter) {
            data = data.filter(r => String(r.Stu_room) === roomFilter);
        }
        if (statusFilter) {
            data = data.filter(r => String(r.Stu_status) === statusFilter);
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
        
        const data = getFilteredExportData();
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
        'Stu_id': 'รหัสนักเรียน',
        'Stu_no': 'เลขที่',
        'Stu_pre': 'คำนำหน้า',
        'Stu_name': 'ชื่อ',
        'Stu_sur': 'นามสกุล',
        'Stu_major': 'ชั้น',
        'Stu_room': 'ห้อง',
        'Stu_status': 'สถานะ',
        'Stu_phone': 'เบอร์โทร',
        'Stu_citizenid': 'เลขประชาชน',
        'Stu_birth': 'วันเกิด',
        'Par_phone': 'เบอร์ผู้ปกครอง'
    };

    const statusLabels = { '1': 'ปกติ', '2': 'จบการศึกษา', '3': 'ย้ายโรงเรียน', '4': 'ออกกลางคัน', '9': 'เสียชีวิต' };

    function formatCellValue(col, value) {
        if (col === 'Stu_status') return statusLabels[String(value)] || value || '-';
        if (col === 'Stu_major') return value ? 'ม.' + value : '-';
        return value || '-';
    }

    function exportToExcel(data, cols) {
        // Create CSV with BOM for Thai support
        let csv = '\uFEFF';
        csv += cols.map(c => colNames[c]).join(',') + '\n';
        
        data.forEach(row => {
            const values = cols.map(col => {
                let val = formatCellValue(col, row[col]);
                val = String(val).replace(/"/g, '""');
                if (val.includes(',') || val.includes('\n')) {
                    val = '"' + val + '"';
                }
                return val;
            });
            csv += values.join(',') + '\n';
        });
        
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `ข้อมูลนักเรียน_${new Date().toLocaleDateString('th-TH')}.csv`;
        link.click();
        URL.revokeObjectURL(url);
        
        Swal.fire({ icon: 'success', title: 'ส่งออกสำเร็จ!', text: 'ดาวน์โหลดไฟล์ Excel เรียบร้อย', timer: 2000, showConfirmButton: false });
    }

    function exportToPDF(data, cols) {
        const printWindow = window.open('', '_blank');
        const now = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
        
        let html = `
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>รายงานข้อมูลนักเรียน</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: 'Sarabun', 'TH Sarabun New', sans-serif; font-size: 14px; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #6366f1; }
                .header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
                .header p { color: #64748b; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; padding: 12px 8px; text-align: left; font-weight: 600; font-size: 12px; }
                td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
                tr:nth-child(even) { background: #f8fafc; }
                tr:hover { background: #f1f5f9; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 12px; }
                .stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
                .stat-box { text-align: center; padding: 15px 25px; background: #f1f5f9; border-radius: 12px; }
                .stat-box .number { font-size: 28px; font-weight: 700; color: #6366f1; }
                .stat-box .label { font-size: 12px; color: #64748b; }
                @media print { body { padding: 0; } .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📋 รายงานข้อมูลนักเรียน</h1>
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
