<?php
/**
 * View: Admin Settings
 * Modern UI with Tailwind CSS & Glassmorphism
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-violet-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl animate-pulse">
                    <i class="fas fa-cog"></i>
                </span>
                ตั้งค่า <span class="text-violet-600 italic">ระบบ</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">System Settings & Configuration</p>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Card 1: Academic Year/Term -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r from-sky-500 to-cyan-600 p-6 relative">
                <div class="absolute top-0 right-0 opacity-10 text-8xl">📅</div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center text-white">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">ปีการศึกษา / เทอม</h3>
                        <p class="text-sky-100 text-sm">ตั้งค่าปีและภาคเรียนปัจจุบัน</p>
                    </div>
                </div>
            </div>
            <form id="termPeeForm" class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ปีการศึกษา (พ.ศ.)</label>
                    <input type="number" name="academic_year" value="<?= htmlspecialchars($pee) ?>" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เทอม</label>
                    <select name="term" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all cursor-pointer">
                        <option value="1" <?= ($term == 1) ? 'selected' : '' ?>>📚 เทอม 1</option>
                        <option value="2" <?= ($term == 2) ? 'selected' : '' ?>>📖 เทอม 2</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-500 to-cyan-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> บันทึกการตั้งค่า
                </button>
            </form>
        </div>

        <!-- Card 2: Time Settings -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6 relative">
                <div class="absolute top-0 right-0 opacity-10 text-8xl">⏰</div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center text-white">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">ตั้งค่าเวลาสแกน</h3>
                        <p class="text-purple-100 text-sm">กำหนดเวลาเข้า-ออก และสาย-ขาด</p>
                    </div>
                </div>
            </div>
            <form id="timeSettingsForm" class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block flex items-center gap-1">
                            <i class="fas fa-user-clock text-amber-500"></i> เวลาเริ่มสาย
                        </label>
                        <input type="time" name="arrival_late_time" value="<?= htmlspecialchars($arrival_late_time) ?>" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle text-red-500"></i> ตัดขาดเรียน
                        </label>
                        <input type="time" name="arrival_absent_time" value="<?= htmlspecialchars($arrival_absent_time) ?>" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block flex items-center gap-1">
                            <i class="fas fa-door-open text-orange-500"></i> ตัดกลับก่อน
                        </label>
                        <input type="time" name="leave_early_time" value="<?= htmlspecialchars($leave_early_time) ?>" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block flex items-center gap-1">
                            <i class="fas fa-exchange-alt text-green-500"></i> ตัดเช้า/บ่าย
                        </label>
                        <input type="time" name="scan_crossover_time" value="<?= htmlspecialchars($scan_crossover_time) ?>" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none transition-all">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> บันทึกการตั้งค่าเวลา
                </button>
            </form>
        </div>

        <!-- Card 3: Promote Students (Danger Zone) -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-red-200 dark:border-red-900/50 shadow-xl hover:shadow-2xl transition-all lg:col-span-2">
            <div class="bg-gradient-to-r from-red-500 to-orange-600 p-6 relative">
                <div class="absolute top-0 right-0 opacity-10 text-8xl">⚠️</div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center text-white animate-pulse">
                        <i class="fas fa-level-up-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">⚠️ เลื่อนชั้นปีนักเรียน</h3>
                        <p class="text-red-100 text-sm">Danger Zone - ควรกระทำเพียงปีละ 1 ครั้ง</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-red-500 mt-1"></i>
                        <div>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                การดำเนินการนี้จะเลื่อนชั้นนักเรียนทั้งหมด และตั้งค่าสถานะ <strong class="text-red-600">"จบการศึกษา"</strong> ให้กับนักเรียน ม.3 และ ม.6
                            </p>
                            <p class="text-sm text-red-600 font-bold mt-2">⚠️ ควรกระทำเพียงปีละ 1 ครั้ง หลังปิดเทอมเท่านั้น!</p>
                        </div>
                    </div>
                </div>
                <button type="button" id="promoteBtn" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-red-500 to-orange-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-shield-alt"></i> ยืนยันการเลื่อนชั้นปี
                </button>
            </div>
        </div>
    </div>

    <!-- CSV Upload Section -->
    <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
        <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 p-6 relative">
            <div class="absolute top-0 right-0 opacity-10 text-8xl">📊</div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center text-white">
                    <i class="fas fa-file-upload text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white">อัปเดตข้อมูลด้วย CSV</h3>
                    <p class="text-emerald-100 text-sm">นำเข้าและส่งออกข้อมูลนักเรียน</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section 1: Update Student Numbers -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas fa-sort-numeric-down text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-lg font-black text-slate-800 dark:text-white mb-2">🔢 อัปเดตเลขที่นักเรียน</h5>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">ดาวน์โหลดข้อมูลปัจจุบัน แก้ไขเลขที่ แล้วอัปโหลดกลับ</p>
                        
                        <form id="uploadNumberForm" class="space-y-4">
                            <a href="../controllers/SettingController.php?action=download_number_template" 
                               class="inline-flex items-center px-5 py-3 bg-white dark:bg-slate-800 border-2 border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 font-bold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                                <i class="fas fa-download mr-2"></i> ดาวน์โหลดเทมเพลต (ทั้งหมด)
                            </a>
                            
                            <div class="bg-blue-100 dark:bg-blue-900/30 border-l-4 border-blue-400 p-4 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                    <strong>📝 วิธีการใช้งาน:</strong><br>
                                    1. ดาวน์โหลดเทมเพลต CSV<br>
                                    2. เปิดไฟล์ด้วย Excel<br>
                                    3. <strong class="text-red-600 dark:text-red-400">กรอกเลขที่ใหม่ในคอลัมน์ "Stu_no_new"</strong><br>
                                    4. บันทึกและอัปโหลดกลับ
                                </p>
                            </div>
                            
                            <input type="file" name="number_csv" id="number_csv" accept=".csv" required
                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-500/20 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white file:font-bold file:cursor-pointer">
                            
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i> อัปโหลดและอัปเดตเลขที่
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section 2: Update by Room -->
            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border border-cyan-200 dark:border-cyan-800 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-cyan-500 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas fa-door-closed text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-lg font-black text-slate-800 dark:text-white mb-2">🏫 อัปเดตเลขที่ (รายห้อง)</h5>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">เลือกชั้นและห้องเพื่อดาวน์โหลดเทมเพลตเฉพาะห้อง</p>
                        
                        <form id="uploadNumberByRoomForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <select name="number_pe" id="number_pe" required class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-cyan-500/20 outline-none">
                                    <option value="">🎯 เลือกชั้น</option>
                                    <?php foreach ($studentClass as $class): ?>
                                    <option value="<?= $class ?>">ม.<?= $class ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="number_room" id="number_room" required class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-cyan-500/20 outline-none">
                                    <option value="">🚪 เลือกห้อง</option>
                                    <?php foreach ($studentRoom as $room): ?>
                                    <option value="<?= $room ?>">ห้อง <?= $room ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="downloadNumberByRoomBtn" class="px-5 py-3 bg-white dark:bg-slate-800 border-2 border-cyan-300 dark:border-cyan-700 text-cyan-600 dark:text-cyan-400 font-bold rounded-xl hover:bg-cyan-50 dark:hover:bg-cyan-900/30 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-download"></i> ดาวน์โหลดเทมเพลต
                                </button>
                            </div>
                            
                            <input type="file" name="number_room_csv" id="number_room_csv" accept=".csv" required
                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-cyan-500/20 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-cyan-500 file:text-white file:font-bold file:cursor-pointer">
                            
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i> อัปโหลดและอัปเดต (รายห้อง)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section 3: New Student Template -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-lg font-black text-slate-800 dark:text-white mb-2">👨‍🎓 เทมเพลตนักเรียนใหม่</h5>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">สำหรับเพิ่มนักเรียนใหม่เข้าระบบ</p>
                        
                        <div class="flex flex-wrap gap-3">
                            <a href="../controllers/SettingController.php?action=download_new_student_template" 
                               class="inline-flex items-center px-5 py-3 bg-white dark:bg-slate-800 border-2 border-green-300 dark:border-green-700 text-green-600 dark:text-green-400 font-bold rounded-xl hover:bg-green-50 dark:hover:bg-green-900/30 transition-all">
                                <i class="fas fa-download mr-2"></i> ดาวน์โหลดเทมเพลต
                            </a>
                            <button type="button" id="uploadNewStudentBtn" class="inline-flex items-center px-5 py-3 bg-green-500 text-white font-bold rounded-xl shadow-lg hover:bg-green-600 transition-all">
                                <i class="fas fa-upload mr-2"></i> อัปโหลดนักเรียนใหม่
                            </button>
                        </div>
                        <input type="file" id="new_student_csv" name="new_student_csv" accept=".csv" class="hidden">
                    </div>
                </div>
            </div>

            <!-- Section 4: Full Data Update -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas fa-database text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-lg font-black text-slate-800 dark:text-white mb-2">📋 อัปเดตข้อมูลนักเรียนทั้งหมด</h5>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">ดาวน์โหลดข้อมูลตามชั้น/ห้อง แก้ไข แล้วอัปโหลดกลับ</p>
                        
                        <form id="uploadFullDataForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <select name="pe" id="pe" class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none">
                                    <option value="">🎯 ทั้งหมด</option>
                                    <?php foreach ($studentClass as $class): ?>
                                    <option value="<?= $class ?>">ม.<?= $class ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="room" id="room" class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-purple-500/20 outline-none">
                                    <option value="">🚪 ทั้งหมด</option>
                                    <?php foreach ($studentRoom as $room): ?>
                                    <option value="<?= $room ?>">ห้อง <?= $room ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="downloadFullDataBtn" class="px-5 py-3 bg-white dark:bg-slate-800 border-2 border-purple-300 dark:border-purple-700 text-purple-600 dark:text-purple-400 font-bold rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/30 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-download"></i> ดาวน์โหลด
                                </button>
                            </div>
                            
                            <input type="file" name="student_csv" id="student_csv" accept=".csv" required
                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-purple-500/20 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-500 file:text-white file:font-bold file:cursor-pointer">
                            
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-black rounded-xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i> อัปโหลดและอัปเดตข้อมูลทั้งหมด
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Helper functions
    function showLoading(title = 'กำลังประมวลผล...') {
        Swal.fire({
            title: title,
            html: '<div class="flex justify-center"><i class="fas fa-spinner fa-spin text-4xl text-violet-600"></i></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });
    }

    function showSuccess(message) {
        Swal.fire({ icon: 'success', title: '✅ สำเร็จ!', text: message, timer: 2000, showConfirmButton: false });
        setTimeout(() => location.reload(), 2000);
    }

    function showError(message) {
        Swal.fire({ icon: 'error', title: '❌ ล้มเหลว!', text: message });
    }

    async function handleFetch(url, formData, successMsg) {
        showLoading();
        try {
            const res = await fetch(url, { method: 'POST', body: formData });
            const data = await res.json();
            if (res.ok && data.success) {
                showSuccess(data.message || successMsg);
            } else {
                showError(data.message || 'เกิดข้อผิดพลาด');
            }
        } catch (err) {
            showError('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์');
        }
    }

    // 1. Academic Year/Term Form
    $('#termPeeForm').submit(function(e) {
        e.preventDefault();
        handleFetch('../controllers/SettingController.php?action=update_term', new FormData(this), 'บันทึกปีการศึกษา/เทอมสำเร็จ');
    });

    // 2. Time Settings Form
    $('#timeSettingsForm').submit(function(e) {
        e.preventDefault();
        handleFetch('../controllers/SettingController.php?action=update_times', new FormData(this), 'บันทึกการตั้งค่าเวลาสำเร็จ');
    });

    // 3. Promote Students
    $('#promoteBtn').click(function() {
        Swal.fire({
            title: '⚠️ ยืนยันการเลื่อนชั้นปี?',
            html: `
                <div class="text-left space-y-2 p-4">
                    <p>การดำเนินการนี้จะทำให้:</p>
                    <ul class="list-disc list-inside text-slate-600 space-y-1">
                        <li>นักเรียน ม.3 และ ม.6 จะถูกตั้งสถานะ <strong class="text-red-600">"จบการศึกษา"</strong></li>
                        <li>นักเรียนทุกคนจะถูกเลื่อนชั้นขึ้น 1 ระดับ</li>
                        <li><strong class="text-red-600">ไม่สามารถย้อนกลับได้!</strong></li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '✅ ใช่, ยืนยัน',
            cancelButtonText: '❌ ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                handleFetch('../controllers/SettingController.php?action=promote_students', new FormData(), 'เลื่อนชั้นนักเรียนสำเร็จ');
            }
        });
    });

    // 4. Upload Number Data
    $('#uploadNumberForm').submit(function(e) {
        e.preventDefault();
        const file = $('#number_csv')[0].files[0];
        if (!file) return showError('กรุณาเลือกไฟล์ CSV');
        if (file.size > 5 * 1024 * 1024) return showError('ไฟล์ใหญ่เกิน 5MB');
        handleFetch('../controllers/SettingController.php?action=upload_number_data', new FormData(this), 'อัปเดตเลขที่นักเรียนสำเร็จ');
    });

    // 5. Download Number by Room
    $('#downloadNumberByRoomBtn').click(function() {
        const pe = $('#number_pe').val();
        const room = $('#number_room').val();
        if (!pe || !room) return showError('กรุณาเลือกชั้นและห้อง');
        window.location.href = `../controllers/SettingController.php?action=download_number_template&pe=${pe}&room=${room}`;
    });

    // 6. Upload Number by Room
    $('#uploadNumberByRoomForm').submit(function(e) {
        e.preventDefault();
        const file = $('#number_room_csv')[0].files[0];
        if (!file) return showError('กรุณาเลือกไฟล์ CSV');
        handleFetch('../controllers/SettingController.php?action=upload_number_data', new FormData(this), 'อัปเดตเลขที่สำเร็จ');
    });

    // 7. New Student Upload
    $('#uploadNewStudentBtn').click(function() {
        $('#new_student_csv').click();
    });

    $('#new_student_csv').change(async function() {
        const file = this.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('new_student_csv', file);
        handleFetch('../controllers/SettingController.php?action=upload_new_student', formData, 'เพิ่มนักเรียนใหม่สำเร็จ');
    });

    // 8. Download Full Data
    $('#downloadFullDataBtn').click(function() {
        const pe = $('#pe').val();
        const room = $('#room').val();
        window.location.href = `../controllers/SettingController.php?action=download_student_data&pe=${pe}&room=${room}`;
    });

    // 9. Upload Full Data
    $('#uploadFullDataForm').submit(function(e) {
        e.preventDefault();
        const file = $('#student_csv')[0].files[0];
        if (!file) return showError('กรุณาเลือกไฟล์ CSV');
        handleFetch('../controllers/SettingController.php?action=upload_student_data', new FormData(this), 'อัปเดตข้อมูลนักเรียนสำเร็จ');
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
