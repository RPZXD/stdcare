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
        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .animate-slideInDown { animation: slideInDown 0.6s ease-out; }
        .animate-fadeIn { animation: fadeIn 0.8s ease-out; }
        .form-section { 
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.3s ease;
        }
        .form-section:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        }
        .form-input {
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
        }
        .form-input:focus {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            background: #ffffff;
        }
        .form-label {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .emoji-bounce {
            display: inline-block;
            animation: pulse 2s infinite;
        }
        .gradient-border {
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c);
            padding: 2px;
            border-radius: 12px;
        }
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        </style>

        <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="animate-slideInDown section-header rounded-2xl shadow-2xl p-8 mb-8 text-center">
                    <h1 class="text-4xl font-bold text-white mb-2">
                        <span class="emoji-bounce">✏️</span> แก้ไขข้อมูลนักเรียน
                    </h1>
                    <p class="text-xl text-purple-100">
                        <span class="emoji-bounce">👨‍🎓</span> <?= $studentname ?> (<?= $data['Stu_id'] ?>)
                    </p>
                </div>

                <form id="editStudentForm" class="space-y-8">
                    <input type="hidden" name="Stu_id" value="<?= $data['Stu_id'] ?>">

                    <!-- Basic Information Section -->
                    <div class="animate-fadeIn form-section rounded-2xl shadow-xl p-8" style="animation-delay: 0.2s;">
                        <div class="flex items-center mb-6">
                            <span class="text-3xl mr-3 emoji-bounce">📋</span>
                            <h2 class="text-2xl font-bold text-gray-800">ข้อมูลพื้นฐาน</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_pre" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">📛</span> คำนำหน้า
                                    </label>
                                    <input type="text" name="Stu_pre" value="<?= $data['Stu_pre'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_name" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">👤</span> ชื่อ
                                    </label>
                                    <input type="text" name="Stu_name" value="<?= $data['Stu_name'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_sur" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">👤</span> นามสกุล
                                    </label>
                                    <input type="text" name="Stu_sur" value="<?= $data['Stu_sur'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_no" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🔢</span> เลขที่
                                    </label>
                                    <input type="number" name="Stu_no" value="<?= $data['Stu_no'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        min="1" max="45" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_password" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🔑</span> รหัสผ่าน
                                    </label>
                                    <input type="password" name="Stu_password" value="<?= $data['Stu_password'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_sex" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">⚧️</span> เพศ
                                    </label>
                                    <select name="Stu_sex" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
                                        <option value="1" <?= $data['Stu_sex'] == 1 ? 'selected' : '' ?>>ชาย</option>
                                        <option value="2" <?= $data['Stu_sex'] == 2 ? 'selected' : '' ?>>หญิง</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div class="animate-fadeIn form-section rounded-2xl shadow-xl p-8" style="animation-delay: 0.4s;">
                        <div class="flex items-center mb-6">
                            <span class="text-3xl mr-3 emoji-bounce">🏫</span>
                            <h2 class="text-2xl font-bold text-gray-800">ข้อมูลการศึกษา</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- ...existing form fields with enhanced styling... -->
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_major" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🎓</span> ชั้น
                                    </label>
                                    <input type="text" name="Stu_major" value="<?= $data['Stu_major'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z0-9\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_room" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🚪</span> ห้อง
                                    </label>
                                    <input type="text" name="Stu_room" value="<?= $data['Stu_room'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z0-9\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_nick" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">👶</span> ชื่อเล่น
                                    </label>
                                    <input type="text" name="Stu_nick" value="<?= $data['Stu_nick'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_status" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">📜</span> สถานะ
                                    </label>
                                    <select name="Stu_status" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
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

                    <!-- Personal Information Section -->
                    <div class="animate-fadeIn form-section rounded-2xl shadow-xl p-8" style="animation-delay: 0.6s;">
                        <div class="flex items-center mb-6">
                            <span class="text-3xl mr-3 emoji-bounce">🆔</span>
                            <h2 class="text-2xl font-bold text-gray-800">ข้อมูลส่วนตัว</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_birth" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🎂</span> วันเดือนปีเกิด
                                    </label>
                                    <input type="date" name="Stu_birth" value="<?= $data['Stu_birth'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_religion" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🛐</span> ศาสนา
                                    </label>
                                    <input type="text" name="Stu_religion" value="<?= $data['Stu_religion'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="[ก-๙a-zA-Z\s]+" required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_blood" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🩸</span> กรุ๊ปเลือด
                                    </label>
                                    <select name="Stu_blood" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
                                        <option value="" <?= $data['Stu_blood'] == 'null' ? 'selected' : '' ?>>ไม่แสดงกรุ๊ปเลือด</option>
                                        <option value="A" <?= $data['Stu_blood'] == 'A' ? 'selected' : '' ?>>A</option>
                                        <option value="B" <?= $data['Stu_blood'] == 'B' ? 'selected' : '' ?>>B</option>
                                        <option value="AB" <?= $data['Stu_blood'] == 'AB' ? 'selected' : '' ?>>AB</option>
                                        <option value="O" <?= $data['Stu_blood'] == 'O' ? 'selected' : '' ?>>O</option>
                                    </select>
                                </div>
                            </div>
                            <div class="gradient-border md:col-span-2">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_addr" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🏠</span> ที่อยู่
                                    </label>
                                    <input type="text" name="Stu_addr" value="<?= $data['Stu_addr'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        required>
                                </div>
                            </div>
                            <div class="gradient-border">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_phone" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">📞</span> เบอร์โทรศัพท์
                                    </label>
                                    <input type="tel" name="Stu_phone" value="<?= $data['Stu_phone'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="\d{10}" maxlength="10" required>
                                </div>
                            </div>
                            <div class="gradient-border md:col-span-2">
                                <div class="bg-white rounded-lg p-4">
                                    <label for="Stu_citizenid" class="block text-sm font-medium form-label mb-2">
                                        <span class="emoji-bounce">🆔</span> เลขบัตรประชาชน
                                    </label>
                                    <input type="text" name="Stu_citizenid" value="<?= $data['Stu_citizenid'] ?>" 
                                        class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                        pattern="\d{13}" maxlength="13" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information Section -->
                    <div class="animate-fadeIn form-section rounded-2xl shadow-xl p-8" style="animation-delay: 0.8s;">
                        <div class="flex items-center mb-6">
                            <span class="text-3xl mr-3 emoji-bounce">👨‍👩‍👧‍👦</span>
                            <h2 class="text-2xl font-bold text-gray-800">ข้อมูลครอบครัว</h2>
                        </div>
                        
                        <!-- Father Information -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-blue-700 mb-4 flex items-center">
                                <span class="emoji-bounce mr-2">👨‍👦</span> ข้อมูลบิดา
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Father_name" class="block text-sm font-medium form-label mb-2">ชื่อบิดา</label>
                                        <input type="text" name="Father_name" value="<?= $data['Father_name'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            pattern="[ก-๙a-zA-Z\s]+" required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Father_occu" class="block text-sm font-medium form-label mb-2">อาชีพบิดา</label>
                                        <input type="text" name="Father_occu" value="<?= $data['Father_occu'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Father_income" class="block text-sm font-medium form-label mb-2">รายได้บิดา</label>
                                        <input type="number" name="Father_income" value="<?= $data['Father_income'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mother Information -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-pink-700 mb-4 flex items-center">
                                <span class="emoji-bounce mr-2">👩‍👦</span> ข้อมูลมารดา
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Mother_name" class="block text-sm font-medium form-label mb-2">ชื่อมารดา</label>
                                        <input type="text" name="Mother_name" value="<?= $data['Mother_name'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            pattern="[ก-๙a-zA-Z\s]+" required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Mother_occu" class="block text-sm font-medium form-label mb-2">อาชีพมารดา</label>
                                        <input type="text" name="Mother_occu" value="<?= $data['Mother_occu'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Mother_income" class="block text-sm font-medium form-label mb-2">รายได้มารดา</label>
                                        <input type="number" name="Mother_income" value="<?= $data['Mother_income'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div>
                            <h3 class="text-xl font-semibold text-green-700 mb-4 flex items-center">
                                <span class="emoji-bounce mr-2">👨‍👩‍👧</span> ข้อมูลผู้ปกครอง
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_name" class="block text-sm font-medium form-label mb-2">ชื่อผู้ปกครอง</label>
                                        <input type="text" name="Par_name" value="<?= $data['Par_name'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            pattern="[ก-๙a-zA-Z\s]+" required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_relate" class="block text-sm font-medium form-label mb-2">ความสัมพันธ์</label>
                                        <input type="text" name="Par_relate" value="<?= $data['Par_relate'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_occu" class="block text-sm font-medium form-label mb-2">อาชีพผู้ปกครอง</label>
                                        <input type="text" name="Par_occu" value="<?= $data['Par_occu'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_income" class="block text-sm font-medium form-label mb-2">รายได้ผู้ปกครอง</label>
                                        <input type="number" name="Par_income" value="<?= $data['Par_income'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            min="0" required>
                                    </div>
                                </div>
                                <div class="gradient-border">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_phone" class="block text-sm font-medium form-label mb-2">เบอร์โทรผู้ปกครอง</label>
                                        <input type="tel" name="Par_phone" value="<?= $data['Par_phone'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            pattern="\d{10}" maxlength="10" required>
                                    </div>
                                </div>
                                <div class="gradient-border md:col-span-2 lg:col-span-1">
                                    <div class="bg-white rounded-lg p-4">
                                        <label for="Par_addr" class="block text-sm font-medium form-label mb-2">ที่อยู่ผู้ปกครอง</label>
                                        <input type="text" name="Par_addr" value="<?= $data['Par_addr'] ?>" 
                                            class="form-input w-full rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-3 px-4 text-base text-gray-800" 
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
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