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
                                                    ดาวน์โหลดเทมเพลต (ทั้งหมด)
                                                </a>
                                            </div>
                                            
                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    <i class="fas fa-file-csv text-blue-500 mr-2"></i>
                                                    อัปโหลดไฟล์ CSV ที่แก้ไขแล้ว
                                                </label>
                                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-3 rounded-lg">
                                                    <div class="flex">
                                                        <div class="flex-shrink-0">
                                                            <i class="fas fa-info-circle text-blue-400"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm text-blue-700">
                                                                <strong>📝 วิธีการใช้งาน:</strong><br>
                                                                1. ดาวน์โหลดเทมเพลต CSV ด้านบน<br>
                                                                2. เปิดไฟล์ด้วย Excel หรือ Google Sheets<br>
                                                                3. <strong class="text-red-600">กรอกเลขที่ใหม่ในคอลัมน์ "Stu_no_new"</strong> (คอลัมน์ D)<br>
                                                                4. บันทึกไฟล์และอัปโหลดกลับมาที่นี่<br>
                                                                <em class="text-xs">⚠️ ห้ามแก้ไขคอลัมน์ Stu_id, Stu_major, Stu_room</em>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
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

                            <!-- Section 1.5: Update Student Numbers by Room -->
                            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 border-2 border-cyan-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-cyan-500 text-white p-4 rounded-xl shadow-lg">
                                        <i class="fas fa-door-closed text-3xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-xl font-bold text-gray-800 mb-2">🏫 อัปเดตเลขที่ แบบรายห้อง</h5>
                                        <p class="text-gray-600 text-sm mb-4">เลือกชั้นและห้องเพื่อดาวน์โหลดเทมเพลตเฉพาะห้องนั้นๆ</p>

                                        <form id="uploadNumberByRoomForm" class="space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-layer-group text-cyan-500 mr-2"></i>
                                                        เลือกชั้น
                                                    </label>
                                                    <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all"
                                                            id="number_pe"
                                                            name="number_pe"
                                                            required>
                                                        <option value="">🎯 เลือกชั้น</option>
                                                        <?php foreach ($studentClass as $class): ?>
                                                            <option value="<?php echo $class; ?>">ม.<?php echo $class; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-door-open text-cyan-500 mr-2"></i>
                                                        เลือกห้อง
                                                    </label>
                                                    <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all"
                                                            id="number_room"
                                                            name="number_room"
                                                            required>
                                                        <option value="">🚪 เลือกห้อง</option>
                                                        <?php foreach ($studentRoom as $room): ?>
                                                            <option value="<?php echo $room; ?>">ห้อง <?php echo $room; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                                                    <button type="button"
                                                            id="downloadNumberByRoomBtn"
                                                            class="w-full bg-white border-2 border-cyan-300 text-cyan-600 font-semibold py-3 px-4 rounded-xl hover:bg-cyan-50 hover:border-cyan-400 transform hover:scale-105 transition-all duration-300 shadow-md flex items-center justify-center">
                                                        <i class="fas fa-download mr-2"></i>
                                                        ดาวน์โหลดเทมเพลต
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="border-t-2 border-gray-200 my-4"></div>

                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    <i class="fas fa-file-csv text-cyan-500 mr-2"></i>
                                                    อัปโหลดไฟล์ CSV ที่แก้ไขแล้ว
                                                </label>
                                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-3 rounded-lg">
                                                    <div class="flex">
                                                        <div class="flex-shrink-0">
                                                            <i class="fas fa-info-circle text-blue-400"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm text-blue-700">
                                                                <strong>📝 วิธีการใช้งาน:</strong><br>
                                                                1. ดาวน์โหลดเทมเพลต CSV ด้านบน<br>
                                                                2. เปิดไฟล์ด้วย Excel หรือ Google Sheets<br>
                                                                3. <strong class="text-red-600">กรอกเลขที่ใหม่ในคอลัมน์ "Stu_no_new"</strong> (คอลัมน์ D)<br>
                                                                4. บันทึกไฟล์และอัปโหลดกลับมาที่นี่<br>
                                                                <em class="text-xs">⚠️ ห้ามแก้ไขคอลัมน์ Stu_id, Stu_major, Stu_room</em>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="file"
                                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100"
                                                       id="number_room_csv"
                                                       name="number_room_csv"
                                                       accept=".csv"
                                                       required>
                                            </div>

                                            <button type="submit"
                                                    class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center space-x-2">
                                                <i class="fas fa-upload"></i>
                                                <span>อัปโหลดและอัปเดตเลขที่ (รายห้อง)</span>
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
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <a href="../controllers/SettingController.php?action=download_new_student_template" 
                                               class="inline-flex items-center px-5 py-3 bg-white border-2 border-green-300 text-green-600 font-semibold rounded-xl hover:bg-green-50 hover:border-green-400 transform hover:scale-105 transition-all duration-300 shadow-md">
                                                <i class="fas fa-download mr-2"></i>
                                                ดาวน์โหลดเทมเพลต
                                            </a>
                                            
                                            <button type="button" 
                                                    id="uploadNewStudentBtn" 
                                                    class="inline-flex items-center px-5 py-3 bg-green-500 border-2 border-green-500 text-white font-semibold rounded-xl hover:bg-green-600 hover:border-green-600 transform hover:scale-105 transition-all duration-300 shadow-md">
                                                <i class="fas fa-upload mr-2"></i>
                                                อัปโหลดนักเรียนใหม่
                                            </button>
                                        </div>

                                        <!-- Hidden file input for new student upload -->
                                        <input type="file" 
                                               class="hidden" 
                                               id="new_student_csv" 
                                               name="new_student_csv" 
                                               accept=".csv" 
                                               required>
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
                                        
                                        <form id="uploadFullDataForm" method="post" enctype="multipart/form-data" class="space-y-4">
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
        // แยกข้อความหลักกับรายละเอียด error
        const parts = message.split('\n\n');
        const mainMessage = parts[0];
        const errorDetails = parts[1];

        let html = `<p class="text-lg text-gray-700 mb-4">${mainMessage}</p>`;

        if (errorDetails) {
            html += `
                <div class="bg-gray-50 border-l-4 border-red-400 p-4 rounded-lg max-h-60 overflow-y-auto">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">📋 รายละเอียดข้อผิดพลาด:</h4>
                    <pre class="text-xs text-gray-600 whitespace-pre-wrap font-mono">${errorDetails}</pre>
                </div>
            `;
        }

        Swal.fire({
            icon: 'error',
            title: '<span class="text-2xl">❌ ล้มเหลว!</span>',
            html: html,
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl max-w-2xl',
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

    // 4.5. Download Number Template by Room
    const downloadNumberByRoomBtn = document.getElementById('downloadNumberByRoomBtn');
    if (downloadNumberByRoomBtn) {
        downloadNumberByRoomBtn.addEventListener('click', function() {
            const pe = document.getElementById('number_pe').value;
            const room = document.getElementById('number_room').value;

            if (!pe || !room) {
                showError('กรุณาเลือกชั้นและห้อง');
                return;
            }

            Swal.fire({
                title: '📥 กำลังเตรียมดาวน์โหลด...',
                html: `
                    <div class="space-y-2 text-left">
                        <p><strong>ชั้น:</strong> ม.${pe}</p>
                        <p><strong>ห้อง:</strong> ห้อง ${room}</p>
                    </div>
                `,
                icon: 'info',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });

            const url = `../controllers/SettingController.php?action=download_number_template_by_room&pe=${pe}&room=${room}`;
            window.open(url, '_blank');
        });
    }

    // 4.6. Upload Number Data by Room Form
    const uploadNumberByRoomForm = document.getElementById('uploadNumberByRoomForm');
    if (uploadNumberByRoomForm) {
        uploadNumberByRoomForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('number_room_csv');
            const pe = document.getElementById('number_pe').value;
            const room = document.getElementById('number_room').value;

            if (!fileInput.files || fileInput.files.length === 0) {
                showError('กรุณาเลือกไฟล์ CSV');
                return;
            }

            if (!pe || !room) {
                showError('กรุณาเลือกชั้นและห้อง');
                return;
            }

            // File size validation (max 5MB)
            if (fileInput.files[0].size > 5 * 1024 * 1024) {
                showError('ไฟล์มีขนาดใหญ่เกิน 5MB');
                return;
            }

            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=upload_number_data_by_room', formData, 'อัปเดตเลขที่นักเรียน (รายห้อง) สำเร็จ');
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

    // 6. Upload New Student Button
    const uploadNewStudentBtn = document.getElementById('uploadNewStudentBtn');
    if (uploadNewStudentBtn) {
        uploadNewStudentBtn.addEventListener('click', function() {
            const fileInput = document.getElementById('new_student_csv');
            fileInput.click();
        });
    }

    // 7. Handle New Student CSV File Selection
    const newStudentCsvInput = document.getElementById('new_student_csv');
    if (newStudentCsvInput) {
        newStudentCsvInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            // File size validation (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('ไฟล์มีขนาดใหญ่เกิน 5MB');
                this.value = '';
                return;
            }

            Swal.fire({
                title: '<span class="text-2xl">📤 อัปโหลดนักเรียนใหม่?</span>',
                html: `
                    <div class="space-y-3 text-left">
                        <p class="text-gray-700">คุณต้องการเพิ่มนักเรียนใหม่จากไฟล์ CSV นี้หรือไม่?</p>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                            <p class="text-sm text-blue-800">
                                <strong>📋 ข้อมูลสำคัญ:</strong><br>
                                • ตรวจสอบให้แน่ใจว่าข้อมูลในไฟล์ถูกต้อง<br>
                                • นักเรียนที่มีรหัสซ้ำจะไม่ถูกเพิ่ม<br>
                                • ฟิลด์ที่จำเป็น: Stu_id, Stu_pre, Stu_name, Stu_sur
                            </p>
                        </div>
                        <p class="text-sm text-gray-600"><strong>ไฟล์:</strong> ${file.name}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '✅ เพิ่มนักเรียนใหม่',
                cancelButtonText: '❌ ยกเลิก',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('new_student_csv', file);
                    handleFetch('../controllers/SettingController.php?action=upload_new_student_data', formData, 'เพิ่มนักเรียนใหม่สำเร็จ');
                } else {
                    // Clear file input if cancelled
                    this.value = '';
                }
            });
        });
    }

    // 8. Upload Full Data Form
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