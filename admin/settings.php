<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");

// --- (เพิ่ม) ---
// (เพิ่ม Model สำหรับดึงค่า Setting เวลา)
require_once(__DIR__ . "/../models/SettingModel.php");
use App\Models\SettingModel;
// --- (สิ้นสุดส่วนที่เพิ่ม) ---

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');

// (ดึงข้อมูล ชั้น/ห้อง สำหรับ dropdown)
$studentClass = $db->query("SELECT DISTINCT Stu_major FROM student WHERE Stu_major IS NOT NULL AND Stu_status = '1' ORDER BY Stu_major")->fetchAll(PDO::FETCH_COLUMN);
$studentRoom = $db->query("SELECT DISTINCT Stu_room FROM student WHERE Stu_room IS NOT NULL AND Stu_status = '1' ORDER BY Stu_room")->fetchAll(PDO::FETCH_COLUMN);

// --- (เพิ่ม) ดึงค่าเวลาปัจจุบัน ---
$settingsModel = new SettingModel($db);
$timeSettings = $settingsModel->getAllTimeSettings();
$arrival_late_time = $timeSettings['arrival_late_time'] ?? '08:00:00';
$arrival_absent_time = $timeSettings['arrival_absent_time'] ?? '10:00:00';
$leave_early_time = $timeSettings['leave_early_time'] ?? '15:40:00';
$scan_crossover_time = $timeSettings['scan_crossover_time'] ?? '12:00:00';
// --- (สิ้นสุดส่วนที่เพิ่ม) ---

?>

<body class="hold-transition sidebar-mini layout-fixed bg-gradient-to-br from-gray-50 to-blue-50">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper bg-transparent">
        <!-- Enhanced Header with Gradient -->
        <div class="content-header bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white shadow-2xl rounded-b-3xl mx-4 mt-4 mb-6 animate-gradient">
            <div class="container-fluid py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-lg p-4 rounded-2xl shadow-lg transform hover:scale-110 transition-all duration-300">
                            <i class="fas fa-cog text-4xl animate-spin-slow"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1 drop-shadow-lg">⚙️ ตั้งค่าระบบ</h1>
                            <p class="text-white/80 text-sm">จัดการการตั้งค่าและข้อมูลนักเรียน</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-lg px-4 py-2 rounded-full">
                        <i class="fas fa-user-shield text-yellow-300"></i>
                        <span class="text-sm font-medium">ผู้ดูแลระบบ</span>
                    </div>
                </div>
            </div>
        </div>

        <section class="content px-4">
            <div class="container-fluid">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Card 1: Academic Year/Term Settings -->
                    <div class="transform hover:scale-105 transition-all duration-300">
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-4 border-blue-500 hover:shadow-blue-200">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 opacity-10">
                                    <i class="fas fa-graduation-cap text-9xl"></i>
                                </div>
                                <div class="relative z-10 flex items-center space-x-3">
                                    <div class="bg-white/20 backdrop-blur-lg p-3 rounded-xl">
                                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">📅 ตั้งค่าปีการศึกษา / เทอม</h3>
                                </div>
                            </div>
                            <form id="termPeeForm" class="p-6 space-y-4">
                                <div class="group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                                        ปีการศึกษา
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 hover:border-blue-300" 
                                           id="academic_year" 
                                           name="academic_year" 
                                           value="<?php echo htmlspecialchars($pee); ?>" 
                                           required
                                           placeholder="พ.ศ.">
                                </div>
                                <div class="group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-list-ol text-blue-500 mr-2"></i>
                                        เทอม
                                    </label>
                                    <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 hover:border-blue-300" 
                                            id="term" 
                                            name="term" 
                                            required>
                                        <option value="1" <?php echo ($term == 1) ? 'selected' : ''; ?>>📚 เทอม 1</option>
                                        <option value="2" <?php echo ($term == 2) ? 'selected' : ''; ?>>📖 เทอม 2</option>
                                    </select>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                    <i class="fas fa-save"></i>
                                    <span>บันทึกการตั้งค่า</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Card 2: Time Settings -->
                    <div class="transform hover:scale-105 transition-all duration-300">
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-4 border-purple-500 hover:shadow-purple-200">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 opacity-10">
                                    <i class="fas fa-clock text-9xl"></i>
                                </div>
                                <div class="relative z-10 flex items-center space-x-3">
                                    <div class="bg-white/20 backdrop-blur-lg p-3 rounded-xl">
                                        <i class="fas fa-clock text-2xl text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">⏰ ตั้งค่าเวลาสแกน</h3>
                                </div>
                            </div>
                            <form id="timeSettingsForm" class="p-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-user-clock text-yellow-500 mr-2"></i>
                                            เวลาเริ่มสาย
                                        </label>
                                        <input type="time" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               id="arrival_late_time" 
                                               name="arrival_late_time" 
                                               value="<?php echo htmlspecialchars($arrival_late_time); ?>" 
                                               required>
                                    </div>
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                            เวลาตัดขาดเรียน
                                        </label>
                                        <input type="time" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               id="arrival_absent_time" 
                                               name="arrival_absent_time" 
                                               value="<?php echo htmlspecialchars($arrival_absent_time); ?>" 
                                               required>
                                    </div>
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-door-open text-orange-500 mr-2"></i>
                                            เวลาตัดกลับก่อน
                                        </label>
                                        <input type="time" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               id="leave_early_time" 
                                               name="leave_early_time" 
                                               value="<?php echo htmlspecialchars($leave_early_time); ?>" 
                                               required>
                                    </div>
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-exchange-alt text-green-500 mr-2"></i>
                                            เวลาตัดเช้า/บ่าย
                                        </label>
                                        <input type="time" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               id="scan_crossover_time" 
                                               name="scan_crossover_time" 
                                               value="<?php echo htmlspecialchars($scan_crossover_time); ?>" 
                                               required>
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="w-full mt-6 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                    <i class="fas fa-save"></i>
                                    <span>บันทึกการตั้งค่าเวลา</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Card 3: Promote Students (Danger Zone) -->
                    <div class="transform hover:scale-105 transition-all duration-300">
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-4 border-red-500 hover:shadow-red-200">
                            <div class="bg-gradient-to-r from-red-500 to-orange-500 p-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 opacity-10">
                                    <i class="fas fa-exclamation-triangle text-9xl"></i>
                                </div>
                                <div class="relative z-10 flex items-center space-x-3">
                                    <div class="bg-white/20 backdrop-blur-lg p-3 rounded-xl animate-pulse">
                                        <i class="fas fa-level-up-alt text-2xl text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">⚠️ เลื่อนชั้นปีนักเรียน</h3>
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-red-500 mr-3 mt-1"></i>
                                        <div>
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                การดำเนินการนี้จะเลื่อนชั้นนักเรียนทั้งหมด และตั้งค่าสถานะ <strong class="text-red-600">"จบการศึกษา"</strong> 
                                                ให้กับนักเรียน ม.3 และ ม.6
                                            </p>
                                            <p class="text-sm text-red-600 font-bold mt-2">
                                                ⚠️ ควรกระทำเพียงปีละ 1 ครั้ง
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" 
                                        id="promoteBtn" 
                                        class="w-full bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>ยืนยันการเลื่อนชั้นปี</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                </div>

                <!-- CSV Upload Section - Full Width -->
                <div class="mt-6 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-4 border-emerald-500">
                        <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 opacity-10">
                                <i class="fas fa-file-csv text-9xl"></i>
                            </div>
                            <div class="relative z-10 flex items-center space-x-3">
                                <div class="bg-white/20 backdrop-blur-lg p-3 rounded-xl">
                                    <i class="fas fa-file-upload text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">📊 อัปเดตข้อมูลด้วย CSV</h3>
                                    <p class="text-white/80 text-sm mt-1">นำเข้าและส่งออกข้อมูลนักเรียนด้วยไฟล์ CSV</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-8 space-y-6">
                            
                            <!-- Section 1: Update Student Numbers -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-blue-500 text-white p-4 rounded-xl shadow-lg">
                                        <i class="fas fa-sort-numeric-down text-3xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-xl font-bold text-gray-800 mb-2">🔢 อัปเดตเลขที่นักเรียน</h5>
                                        <p class="text-gray-600 text-sm mb-4">ดาวน์โหลดข้อมูลปัจจุบัน แก้ไขเลขที่ แล้วอัปโหลดกลับ</p>
                                        
                                        <form id="uploadNumberForm" class="space-y-4">
                                            <div class="flex flex-wrap gap-3">
                                                <a href="../controllers/SettingController.php?action=download_number_template" 
                                                   class="inline-flex items-center px-5 py-3 bg-white border-2 border-blue-300 text-blue-600 font-semibold rounded-xl hover:bg-blue-50 hover:border-blue-400 transform hover:scale-105 transition-all duration-300 shadow-md">
                                                    <i class="fas fa-download mr-2"></i>
                                                    ดาวน์โหลดเทมเพลต
                                                </a>
                                            </div>
                                            
                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    <i class="fas fa-file-csv text-blue-500 mr-2"></i>
                                                    อัปโหลดไฟล์ CSV (ที่แก้ไข 'Stu_no_new')
                                                </label>
                                                <input type="file" 
                                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                                       id="number_csv" 
                                                       name="number_csv" 
                                                       accept=".csv" 
                                                       required>
                                            </div>
                                            
                                            <button type="submit" 
                                                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                                <i class="fas fa-upload"></i>
                                                <span>อัปโหลดและอัปเดตเลขที่</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section 2: New Student Template -->
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-green-500 text-white p-4 rounded-xl shadow-lg">
                                        <i class="fas fa-user-plus text-3xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-xl font-bold text-gray-800 mb-2">👨‍🎓 เทมเพลตนักเรียนใหม่</h5>
                                        <p class="text-gray-600 text-sm mb-4">สำหรับเพิ่มนักเรียนใหม่ (อัปโหลดที่หน้า "จัดการข้อมูลนักเรียน")</p>
                                        
                                        <a href="../controllers/SettingController.php?action=download_new_student_template" 
                                           class="inline-flex items-center px-5 py-3 bg-white border-2 border-green-300 text-green-600 font-semibold rounded-xl hover:bg-green-50 hover:border-green-400 transform hover:scale-105 transition-all duration-300 shadow-md">
                                            <i class="fas fa-download mr-2"></i>
                                            ดาวน์โหลดเทมเพลต นร. ใหม่
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Full Data Update -->
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-purple-500 text-white p-4 rounded-xl shadow-lg">
                                        <i class="fas fa-database text-3xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-xl font-bold text-gray-800 mb-2">📋 อัปเดตข้อมูลนักเรียนทั้งหมด</h5>
                                        <p class="text-gray-600 text-sm mb-4">ดาวน์โหลดข้อมูลตามชั้น/ห้อง แก้ไข แล้วอัปโหลดกลับ</p>
                                        
                                        <form id="uploadFullDataForm" class="space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-layer-group text-purple-500 mr-2"></i>
                                                        เลือกชั้น
                                                    </label>
                                                    <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all" 
                                                            id="pe" 
                                                            name="pe">
                                                        <option value="">🎯 ทั้งหมด</option>
                                                        <?php foreach ($studentClass as $class): ?>
                                                            <option value="<?php echo $class; ?>">ม.<?php echo $class; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-door-open text-purple-500 mr-2"></i>
                                                        เลือกห้อง
                                                    </label>
                                                    <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all" 
                                                            id="room" 
                                                            name="room">
                                                        <option value="">🚪 ทั้งหมด</option>
                                                        <?php foreach ($studentRoom as $room): ?>
                                                            <option value="<?php echo $room; ?>">ห้อง <?php echo $room; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                                                    <button type="button" 
                                                            id="downloadFullDataBtn" 
                                                            class="w-full bg-white border-2 border-purple-300 text-purple-600 font-semibold py-3 px-4 rounded-xl hover:bg-purple-50 hover:border-purple-400 transform hover:scale-105 transition-all duration-300 shadow-md flex items-center justify-center">
                                                        <i class="fas fa-download mr-2"></i>
                                                        ดาวน์โหลด
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="border-t-2 border-gray-200 my-4"></div>
                                            
                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    <i class="fas fa-file-csv text-purple-500 mr-2"></i>
                                                    อัปโหลดไฟล์ CSV (ที่แก้ไขแล้ว)
                                                </label>
                                                <input type="file" 
                                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" 
                                                       id="student_csv" 
                                                       name="student_csv" 
                                                       accept=".csv" 
                                                       required>
                                            </div>
                                            
                                            <button type="submit" 
                                                    class="w-full bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                                <i class="fas fa-upload"></i>
                                                <span>อัปโหลดและอัปเดตข้อมูลทั้งหมด</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>

<style>
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 6s ease infinite;
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

/* Hover effects for inputs */
input:focus, select:focus {
    transform: translateY(-2px);
}

/* Smooth transitions for all interactive elements */
button, a, input, select {
    transition: all 0.3s ease;
}

/* Shadow pulse effect on hover */
.hover\:shadow-blue-200:hover {
    box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.3), 0 10px 10px -5px rgba(59, 130, 246, 0.2);
}

.hover\:shadow-purple-200:hover {
    box-shadow: 0 20px 25px -5px rgba(168, 85, 247, 0.3), 0 10px 10px -5px rgba(168, 85, 247, 0.2);
}

.hover\:shadow-red-200:hover {
    box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.3), 0 10px 10px -5px rgba(239, 68, 68, 0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Enhanced loading animation
    function showLoading(title = 'กำลังประมวลผล...', text = 'กรุณารอสักครู่') {
        Swal.fire({
            title: title,
            html: `
                <div class="flex flex-col items-center space-y-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-500"></div>
                    <p class="text-gray-600">${text}</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)'
        });
    }

    // Enhanced success message
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: '<span class="text-2xl">✅ สำเร็จ!</span>',
            html: `<p class="text-lg text-gray-700">${message}</p>`,
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#10b981',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-3'
            },
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            }
        });
    }

    // Enhanced error message
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: '<span class="text-2xl">❌ ล้มเหลว!</span>',
            html: `<p class="text-lg text-gray-700">${message}</p>`,
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-3'
            },
            showClass: {
                popup: 'animate__animated animate__shakeX'
            }
        });
    }

    // Helper function for fetch requests
    async function handleFetch(url, formData, successMessage = 'ดำเนินการสำเร็จ') {
        showLoading();

        try {
            const res = await fetch(url, { method: 'POST', body: formData });
            const data = await res.json();

            if (res.ok && data.success) {
                showSuccess(data.message || successMessage);
                // Reload page after 1.5 seconds
                setTimeout(() => location.reload(), 1500);
            } else {
                showError(data.message || 'เกิดข้อผิดพลาด');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            showError('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    }

    // 1. Academic Year/Term Form
    const termPeeForm = document.getElementById('termPeeForm');
    if (termPeeForm) {
        termPeeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=update_term', formData, 'บันทึกปีการศึกษา/เทอมสำเร็จ');
        });
    }

    // 2. Promote Students Button
    const promoteBtn = document.getElementById('promoteBtn');
    if (promoteBtn) {
        promoteBtn.addEventListener('click', function() {
            Swal.fire({
                title: '<span class="text-2xl">⚠️ ยืนยันการเลื่อนชั้นปี?</span>',
                html: `
                    <div class="text-left space-y-3 p-4">
                        <p class="text-gray-700">การดำเนินการนี้จะทำให้:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>นักเรียน ม.3 และ ม.6 จะถูกตั้งสถานะ <strong class="text-red-600">"จบการศึกษา"</strong></li>
                            <li>นักเรียนทุกคนจะถูกเลื่อนชั้นขึ้น 1 ระดับ</li>
                            <li><strong class="text-red-600">ไม่สามารถย้อนกลับได้!</strong></li>
                        </ul>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mt-4 rounded">
                            <p class="text-sm text-yellow-800">
                                💡 <strong>คำแนะนำ:</strong> ควรกระทำเพียงปีละ 1 ครั้ง หลังจากปิดเทอมเท่านั้น
                            </p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '✅ ใช่, ยืนยันการเลื่อนชั้น',
                cancelButtonText: '❌ ยกเลิก',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                },
                showClass: {
                    popup: 'animate__animated animate__headShake'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    handleFetch('../controllers/SettingController.php?action=promote_students', new FormData(), 'เลื่อนชั้นนักเรียนสำเร็จ');
                }
            });
        });
    }

    // 3. Time Settings Form
    const timeSettingsForm = document.getElementById('timeSettingsForm');
    if (timeSettingsForm) {
        timeSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=update_times', formData, 'บันทึกการตั้งค่าเวลาสำเร็จ');
        });
    }

    // 4. Upload Number Data Form
    const uploadNumberForm = document.getElementById('uploadNumberForm');
    if (uploadNumberForm) {
        uploadNumberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('number_csv');
            if (!fileInput.files || fileInput.files.length === 0) {
                showError('กรุณาเลือกไฟล์ CSV');
                return;
            }
            
            // File size validation (max 5MB)
            if (fileInput.files[0].size > 5 * 1024 * 1024) {
                showError('ไฟล์มีขนาดใหญ่เกิน 5MB');
                return;
            }
            
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=upload_number_data', formData, 'อัปเดตเลขที่นักเรียนสำเร็จ');
        });
    }

    // 5. Download Full Data Button
    const downloadFullDataBtn = document.getElementById('downloadFullDataBtn');
    if (downloadFullDataBtn) {
        downloadFullDataBtn.addEventListener('click', function() {
            const pe = document.getElementById('pe').value;
            const room = document.getElementById('room').value;
            
            Swal.fire({
                title: '📥 กำลังเตรียมดาวน์โหลด...',
                html: `
                    <div class="space-y-2 text-left">
                        <p><strong>ชั้น:</strong> ${pe || 'ทั้งหมด'}</p>
                        <p><strong>ห้อง:</strong> ${room || 'ทั้งหมด'}</p>
                    </div>
                `,
                icon: 'info',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            
            const url = `../controllers/SettingController.php?action=download_full_data_template&pe=${pe}&room=${room}`;
            window.open(url, '_blank');
        });
    }

    // 6. Upload Full Data Form
    const uploadFullDataForm = document.getElementById('uploadFullDataForm');
    if (uploadFullDataForm) {
        uploadFullDataForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('student_csv');
            if (!fileInput.files || fileInput.files.length === 0) {
                showError('กรุณาเลือกไฟล์ CSV');
                return;
            }
            
            // File size validation (max 10MB)
            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                showError('ไฟล์มีขนาดใหญ่เกิน 10MB');
                return;
            }
            
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=upload_full_data', formData, 'อัปเดตข้อมูลนักเรียนทั้งหมดสำเร็จ');
        });
    }

    // Add Animate.css for animations
    if (!document.querySelector('link[href*="animate.css"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
        document.head.appendChild(link);
    }

    // Add entrance animations to cards
    const cards = document.querySelectorAll('.transform');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

});
</script>
</body>
</html>