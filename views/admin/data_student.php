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
        
        <button id="btnAddStudent" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-user-plus"></i> เพิ่มนักเรียน
        </button>
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

<style>
.avatar-placeholder { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 700; font-size: 14px; }
.btn-sm { padding: 4px 8px; border: none; background: none; cursor: pointer; font-size: 16px; transition: transform 0.15s; }
.btn-sm:hover { transform: scale(1.2); }

@media (max-width: 768px) {
    #studentTable { font-size: 13px; }
    #studentTable th, #studentTable td { padding: 8px 6px; }
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
            url: '../controllers/StudentController.php?action=list',
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
                    return `<button class="editStudentBtn btn-sm" data-id="${data}">✏️</button>
                            <button class="deleteStudentBtn btn-sm" data-id="${data}">🗑️</button>
                            <button class="resetPwdBtn btn-sm" data-id="${data}">🔑</button>`;
                },
                orderable: false,
                width: '100px'
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
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
