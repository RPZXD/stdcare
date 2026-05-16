<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

// Get student ID from query parameter
$stu_id = isset($_GET['stu_id']) ? $_GET['stu_id'] : '';

if (!empty($stu_id)) {
    $studentData = $student->getStudentById($stu_id);
    if ($studentData) {
        $data = $studentData[0];
        $studentname = $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur'];
        ?>
        <style>
        .form-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
        }
        .dark .form-input {
            background-color: #0f172a;
            border-color: #1e293b;
            color: #f1f5f9;
        }
        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            background-color: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -2px rgba(99, 102, 241, 0.05);
            transform: translateY(-1px);
        }
        .dark .form-input:focus {
            background-color: #1e293b;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        .section-card {
            background: #ffffff;
            border-radius: 2.5rem;
            padding: 2rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            transition: all 0.4s ease;
        }
        .dark .section-card {
            background: rgba(30, 41, 59, 0.4);
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }
        .section-card:hover {
            border-color: #e0e7ff;
            transform: translateY(-2px);
        }
        .dark .section-card:hover {
            border-color: #334155;
        }
        .section-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        </style>

        <div class="p-4 md:p-8 lg:p-10 space-y-10">
            <form id="editStudentForm" class="space-y-10">
                <input type="hidden" name="Stu_id" value="<?= $data['Stu_id'] ?>">

                <!-- Row 1: Identity & Academic -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Basic Information Card -->
                    <div class="section-card">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="section-icon bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight">ข้อมูลพื้นฐาน</h2>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Primary Identity</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2 grid grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <label class="form-label">คำนำหน้า</label>
                                    <input type="text" name="Stu_pre" value="<?= $data['Stu_pre'] ?>" class="form-input" placeholder="นาย/นางสาว">
                                </div>
                                <div class="col-span-2">
                                    <label class="form-label">ชื่อจริง</label>
                                    <input type="text" name="Stu_name" value="<?= $data['Stu_name'] ?>" class="form-input" required>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" name="Stu_sur" value="<?= $data['Stu_sur'] ?>" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">ชื่อเล่น</label>
                                <input type="text" name="Stu_nick" value="<?= $data['Stu_nick'] ?>" class="form-input" placeholder="ระบุชื่อเล่น">
                            </div>
                            <div>
                                <label class="form-label">เพศ</label>
                                <select name="Stu_sex" class="form-input" required>
                                    <option value="1" <?= $data['Stu_sex'] == 1 ? 'selected' : '' ?>>👨 ชาย</option>
                                    <option value="2" <?= $data['Stu_sex'] == 2 ? 'selected' : '' ?>>👩 หญิง</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Details Card -->
                    <div class="section-card">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="section-icon bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight">การศึกษา & สถานะ</h2>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Academic Status</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="form-label">ชั้นเรียน (ม.)</label>
                                <input type="text" name="Stu_major" value="<?= $data['Stu_major'] ?>" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">ห้อง</label>
                                <input type="text" name="Stu_room" value="<?= $data['Stu_room'] ?>" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">เลขที่</label>
                                <input type="number" name="Stu_no" value="<?= $data['Stu_no'] ?>" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">รหัสผ่าน</label>
                                <input type="text" name="Stu_password" value="<?= $data['Stu_password'] ?>" class="form-input" required>
                            </div>
                            <div class="col-span-2">
                                <label class="form-label">สถานะปัจจุบัน</label>
                                <select name="Stu_status" class="form-input" required>
                                    <option value="1" <?= $data['Stu_status'] == 1 ? 'selected' : '' ?>>🟢 ปกติ</option>
                                    <option value="2" <?= $data['Stu_status'] == 2 ? 'selected' : '' ?>>🎓 จบการศึกษา</option>
                                    <option value="3" <?= $data['Stu_status'] == 3 ? 'selected' : '' ?>>🏫 ย้ายโรงเรียน</option>
                                    <option value="4" <?= $data['Stu_status'] == 4 ? 'selected' : '' ?>>❌ ออกกลางคัน</option>
                                    <option value="9" <?= $data['Stu_status'] == 9 ? 'selected' : '' ?>>💔 เสียชีวิต</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Personal Details Full Width -->
                <div class="section-card">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="section-icon bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight">ข้อมูลส่วนตัวเพิ่มเติม</h2>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Personal Information</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="form-label">เลขบัตรประชาชน</label>
                            <input type="text" name="Stu_citizenid" value="<?= $data['Stu_citizenid'] ?>" class="form-input" maxlength="13" required>
                        </div>
                        <div>
                            <label class="form-label">วันเดือนปีเกิด</label>
                            <input type="date" name="Stu_birth" value="<?= $data['Stu_birth'] ?>" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">ศาสนา</label>
                            <input type="text" name="Stu_religion" value="<?= $data['Stu_religion'] ?>" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">กรุ๊ปเลือด</label>
                            <select name="Stu_blood" class="form-input">
                                <option value="" <?= empty($data['Stu_blood']) ? 'selected' : '' ?>>ไม่ระบุ</option>
                                <option value="A" <?= $data['Stu_blood'] == 'A' ? 'selected' : '' ?>>A</option>
                                <option value="B" <?= $data['Stu_blood'] == 'B' ? 'selected' : '' ?>>B</option>
                                <option value="AB" <?= $data['Stu_blood'] == 'AB' ? 'selected' : '' ?>>AB</option>
                                <option value="O" <?= $data['Stu_blood'] == 'O' ? 'selected' : '' ?>>O</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="tel" name="Stu_phone" value="<?= $data['Stu_phone'] ?>" class="form-input" maxlength="10">
                        </div>
                        <div class="md:col-span-3">
                            <label class="form-label">ที่อยู่ตามทะเบียนบ้าน</label>
                            <textarea name="Stu_addr" class="form-input h-[54px] resize-none py-3"><?= $data['Stu_addr'] ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Parent Information -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Father Card -->
                    <div class="section-card border-l-4 border-l-blue-500">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">👨</div>
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wider text-sm">ข้อมูลบิดา</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">ชื่อ-นามสกุลบิดา</label>
                                <input type="text" name="Father_name" value="<?= $data['Father_name'] ?>" class="form-input" placeholder="ระบุชื่อ-นามสกุล">
                            </div>
                            <div>
                                <label class="form-label">อาชีพ</label>
                                <input type="text" name="Father_occu" value="<?= $data['Father_occu'] ?>" class="form-input" placeholder="ระบุอาชีพ">
                            </div>
                        </div>
                    </div>

                    <!-- Mother Card -->
                    <div class="section-card border-l-4 border-l-pink-500">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-pink-50 dark:bg-pink-900/30 text-pink-600 rounded-xl flex items-center justify-center">👩</div>
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wider text-sm">ข้อมูลมารดา</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">ชื่อ-นามสกุลมารดา</label>
                                <input type="text" name="Mother_name" value="<?= $data['Mother_name'] ?>" class="form-input" placeholder="ระบุชื่อ-นามสกุล">
                            </div>
                            <div>
                                <label class="form-label">อาชีพ</label>
                                <input type="text" name="Mother_occu" value="<?= $data['Mother_occu'] ?>" class="form-input" placeholder="ระบุอาชีพ">
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Card -->
                    <div class="section-card border-l-4 border-l-emerald-500">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">👪</div>
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wider text-sm">ข้อมูลผู้ปกครอง</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">ชื่อ-นามสกุลผู้ปกครอง</label>
                                <input type="text" name="Par_name" value="<?= $data['Par_name'] ?>" class="form-input" placeholder="ระบุชื่อ-นามสกุล">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">ความสัมพันธ์</label>
                                    <input type="text" name="Par_relate" value="<?= $data['Par_relate'] ?>" class="form-input" placeholder="เช่น ลุง, ป้า">
                                </div>
                                <div>
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="tel" name="Par_phone" value="<?= $data['Par_phone'] ?>" class="form-input" maxlength="10">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add enhanced interactions
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.parentElement.style.transform = 'scale(1)';
                });
            });

            // Form validation feedback
            const form = document.getElementById('editStudentForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Add loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> กำลังบันทึก...';
                submitBtn.disabled = true;
                
                // Simulate form processing
                setTimeout(() => {
                    submitBtn.innerHTML = '<span class="mr-2">✅</span> บันทึกสำเร็จ!';
                    submitBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                }, 2000);
            });
        });
        </script>
        <?php
    } else {
        echo "<div class='min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-100'>";
        echo "<div class='bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md mx-auto animate-fadeIn'>";
        echo "<div class='text-6xl mb-4 emoji-bounce'>🚨</div>";
        echo "<h2 class='text-2xl font-bold text-red-600 mb-2'>ไม่พบข้อมูลนักเรียน</h2>";
        echo "<p class='text-gray-600'>ไม่สามารถค้นหาข้อมูลนักเรียนที่ต้องการได้</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='min-h-screen flex items-center justify-center bg-gradient-to-br from-yellow-50 to-orange-100'>";
    echo "<div class='bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md mx-auto animate-fadeIn'>";
    echo "<div class='text-6xl mb-4 emoji-bounce'>⚠️</div>";
    echo "<h2 class='text-2xl font-bold text-yellow-600 mb-2'>รหัสนักเรียนไม่ถูกต้อง</h2>";
    echo "<p class='text-gray-600'>โปรดระบุรหัสนักเรียนที่ถูกต้อง</p>";
    echo "</div>";
    echo "</div>";
}
?>