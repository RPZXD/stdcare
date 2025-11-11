<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student = new Student($db);

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">

            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-3xl shadow-2xl border border-blue-100 p-8">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <span class="text-4xl mr-3">📚</span>
                            จัดการข้อมูลนักเรียน
                        </h2>
                        
                        <div class="flex flex-wrap gap-3">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-500">🏫</span>
                                <select id="filterClass" class="pl-10 pr-4 py-4 bg-white border-2 border-purple-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 text-gray-700 font-semibold text-lg">
                                    <option value="">-- เลือกชั้น --</option>
                                </select>
                            </div>
                            
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500">🚪</span>
                                <select id="filterRoom" class="pl-10 pr-4 py-4 bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-gray-700 font-semibold text-lg">
                                    <option value="">-- เลือกห้อง --</option>
                                </select>
                            </div>
                            
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-500">📊</span>
                                <select id="filterStatus" class="pl-10 pr-4 py-4 bg-white border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 text-gray-700 font-semibold text-lg">
                                    <option value="">-- สถานะ --</option>
                                    <option value="1">✅ ปกติ</option>
                                    <option value="2">🎓 จบการศึกษา</option>
                                    <option value="3">🚚 ย้ายโรงเรียน</option>
                                    <option value="4">❌ ออกกลางคัน</option>
                                    <option value="9">🕊️ เสียชีวิต</option>
                                </select>
                            </div>
                            
                            <button id="btnAddStudent" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-sm flex items-center">
                                <span class="text-lg mr-2">➕</span>
                                เพิ่มนักเรียน
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <table id="studentTable" class="w-full text-sm text-left">
                            <thead class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center">📷 รูป</th>
                                    <th class="px-6 py-4 font-bold text-center">📋 เลขที่</th>
                                    <th class="px-6 py-4 font-bold text-center">🆔 รหัสนักเรียน</th>
                                    <th class="px-6 py-4 font-bold">👤 ชื่อ-นามสกุล</th>
                                    <th class="px-6 py-4 font-bold text-center">🏫 ชั้น/ห้อง</th>
                                    <th class="px-6 py-4 font-bold text-center">📊 สถานะ</th>
                                    <th class="px-6 py-4 font-bold text-center">⚙️ จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Data will be injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Modal for adding/editing student -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-green-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center" id="addStudentModalLabel">
                            <span class="text-2xl mr-3">➕</span>
                            เพิ่มข้อมูลนักเรียน
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-8">
                        <form id="addStudentForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="addStu_id" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🆔</span>
                                        รหัสนักเรียน
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white" id="addStu_id" name="addStu_id" maxlength="10" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_no" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🔢</span>
                                        เลขที่
                                    </label>
                                    <select name="addStu_no" id="addStu_no" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white text-base font-semibold text-gray-800">
                                        <option value="">-- โปรดเลือกเลขที่ --</option>
                                        <?php for ($i = 1; $i <= 50; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_pre" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">👤</span>
                                        คำนำหน้าชื่อ
                                    </label>
                                    <select name="addStu_pre" id="addStu_pre" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white text-base font-semibold text-gray-800">
                                        <option value="">-- โปรดเลือกคำนำหน้า --</option>
                                        <option value="เด็กชาย">👦 เด็กชาย</option>
                                        <option value="เด็กหญิง">👧 เด็กหญิง</option>
                                        <option value="นาย">👨 นาย</option>
                                        <option value="นางสาว">👩 นางสาว</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_name" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📝</span>
                                        ชื่อ
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white" id="addStu_name" name="addStu_name" maxlength="100" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_sur" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📝</span>
                                        นามสกุล
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white" id="addStu_sur" name="addStu_sur" maxlength="100" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_major" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🏫</span>
                                        ชั้น
                                    </label>
                                    <select name="addStu_major" id="addStu_major" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white text-base font-semibold text-gray-800">
                                        <option value="">-- โปรดเลือกชั้น --</option>
                                        <?php for ($i = 1; $i <= 6; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addStu_room" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🚪</span>
                                        ห้อง
                                    </label>
                                    <select name="addStu_room" id="addStu_room" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 bg-white text-base font-semibold text-gray-800">
                                        <option value="">-- โปรดเลือกห้อง --</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-end space-x-3">
                        <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">
                            <span class="mr-2">❌</span>
                            ปิด
                        </button>
                        <button type="button" id="submitAddStudentForm" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                            <span class="mr-2">💾</span>
                            บันทึก
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal (structure similar to Add Modal, with ids changed to edit...) -->
        <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-yellow-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center" id="editStudentModalLabel">
                            <span class="text-2xl mr-3">✏️</span>
                            แก้ไขข้อมูลนักเรียน
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-8" style="max-height: 70vh; overflow-y: auto;">
                        <form id="editStudentForm">
                            <input type="hidden" id="editStu_id_old" name="editStu_id_old" required>
                            
                            <!-- ข้อมูลพื้นฐาน -->
                            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b-2 border-yellow-300 pb-2">
                                <span class="text-2xl mr-2">📋</span> ข้อมูลพื้นฐาน
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="form-group">
                                    <label for="editStu_id" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🆔</span> รหัสนักเรียน
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_id" name="editStu_id" maxlength="10" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_citizenid" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🪪</span> เลขบัตรประชาชน
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_citizenid" name="editStu_citizenid" maxlength="13" pattern="[0-9]{13}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_no" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🔢</span> เลขที่
                                    </label>
                                    <select name="editStu_no" id="editStu_no" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white">
                                        <option value="">-- โปรดเลือกเลขที่ --</option>
                                        <?php for ($i = 1; $i <= 50; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_pre" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">👤</span> คำนำหน้าชื่อ
                                    </label>
                                    <select name="editStu_pre" id="editStu_pre" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white">
                                        <option value="เด็กชาย">👦 เด็กชาย</option>
                                        <option value="เด็กหญิง">👧 เด็กหญิง</option>
                                        <option value="นาย">👨 นาย</option>
                                        <option value="นางสาว">👩 นางสาว</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_name" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📝</span> ชื่อ
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_name" name="editStu_name" maxlength="50" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_sur" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📝</span> นามสกุล
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_sur" name="editStu_sur" maxlength="50" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_nick" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">😊</span> ชื่อเล่น
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_nick" name="editStu_nick" maxlength="30">
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_birth" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🎂</span> วันเกิด
                                    </label>
                                    <input type="date" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_birth" name="editStu_birth">
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_religion" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🙏</span> ศาสนา
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_religion" name="editStu_religion" maxlength="30">
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_blood" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🩸</span> หมู่เลือด
                                    </label>
                                    <select class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_blood" name="editStu_blood">
                                        <option value="">-- เลือกหมู่เลือด --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_phone" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📱</span> เบอร์โทร
                                    </label>
                                    <input type="tel" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_phone" name="editStu_phone" maxlength="15">
                                </div>
                                
                                <div class="form-group">
                                    <label for="editVehicle" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🚗</span> ยานพาหนะ
                                    </label>
                                    <select class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editVehicle" name="editVehicle">
                                        <option value="0">ไม่มี</option>
                                        <option value="1">มี</option>
                                    </select>
                                </div>
                                
                                <div class="form-group md:col-span-2">
                                    <label for="editStu_addr" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🏠</span> ที่อยู่
                                    </label>
                                    <textarea class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editStu_addr" name="editStu_addr" rows="2" maxlength="100"></textarea>
                                </div>
                            </div>
                            
                            <!-- ข้อมูลการศึกษา -->
                            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b-2 border-yellow-300 pb-2">
                                <span class="text-2xl mr-2">🏫</span> ข้อมูลการศึกษา
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="form-group">
                                    <label for="editStu_major" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📚</span> ชั้น
                                    </label>
                                    <select name="editStu_major" id="editStu_major" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white">
                                        <option value="">-- โปรดเลือกชั้น --</option>
                                        <?php for ($i = 1; $i <= 6; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_room" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🚪</span> ห้อง
                                    </label>
                                    <select name="editStu_room" id="editStu_room" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white">
                                        <option value="">-- โปรดเลือกห้อง --</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editStu_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📊</span> สถานะ
                                    </label>
                                    <select class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" name="editStu_status" id="editStu_status">
                                        <option value="1">✅ ปกติ</option>
                                        <option value="2">🎓 จบการศึกษา</option>
                                        <option value="3">🚚 ย้ายโรงเรียน</option>
                                        <option value="4">❌ ออกกลางคัน</option>
                                        <option value="9">🕊️ เสียชีวิต</option>
                                    </select>
                                </div>
                                
                                <div class="form-group md:col-span-3">
                                    <label for="editRisk_group" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">⚠️</span> กลุ่มเสี่ยง
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editRisk_group" name="editRisk_group" maxlength="2">
                                </div>
                            </div>
                            
                            <!-- ข้อมูลบิดา -->
                            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b-2 border-yellow-300 pb-2">
                                <span class="text-2xl mr-2">👨</span> ข้อมูลบิดา
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="form-group">
                                    <label for="editFather_name" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อบิดา</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editFather_name" name="editFather_name" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editFather_occu" class="block text-sm font-semibold text-gray-700 mb-2">อาชีพ</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editFather_occu" name="editFather_occu" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editFather_income" class="block text-sm font-semibold text-gray-700 mb-2">รายได้/เดือน</label>
                                    <input type="number" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editFather_income" name="editFather_income" min="0">
                                </div>
                            </div>
                            
                            <!-- ข้อมูลมารดา -->
                            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b-2 border-yellow-300 pb-2">
                                <span class="text-2xl mr-2">👩</span> ข้อมูลมารดา
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="form-group">
                                    <label for="editMother_name" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อมารดา</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editMother_name" name="editMother_name" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editMother_occu" class="block text-sm font-semibold text-gray-700 mb-2">อาชีพ</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editMother_occu" name="editMother_occu" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editMother_income" class="block text-sm font-semibold text-gray-700 mb-2">รายได้/เดือน</label>
                                    <input type="number" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editMother_income" name="editMother_income" min="0">
                                </div>
                            </div>
                            
                            <!-- ข้อมูลผู้ปกครอง -->
                            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b-2 border-yellow-300 pb-2">
                                <span class="text-2xl mr-2">👥</span> ข้อมูลผู้ปกครอง
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="editPar_name" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อผู้ปกครอง</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_name" name="editPar_name" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editPar_relate" class="block text-sm font-semibold text-gray-700 mb-2">ความสัมพันธ์</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_relate" name="editPar_relate" maxlength="30">
                                </div>
                                <div class="form-group">
                                    <label for="editPar_occu" class="block text-sm font-semibold text-gray-700 mb-2">อาชีพ</label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_occu" name="editPar_occu" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label for="editPar_income" class="block text-sm font-semibold text-gray-700 mb-2">รายได้/เดือน</label>
                                    <input type="number" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_income" name="editPar_income" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="editPar_phone" class="block text-sm font-semibold text-gray-700 mb-2">เบอร์โทร</label>
                                    <input type="tel" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_phone" name="editPar_phone" maxlength="15">
                                </div>
                                <div class="form-group md:col-span-2">
                                    <label for="editPar_addr" class="block text-sm font-semibold text-gray-700 mb-2">ที่อยู่ผู้ปกครอง</label>
                                    <textarea class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200 bg-white" id="editPar_addr" name="editPar_addr" rows="2" maxlength="100"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-end space-x-3">
                        <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">
                            <span class="mr-2">❌</span>
                            ปิด
                        </button>
                        <button type="button" id="submitEditStudentForm" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                            <span class="mr-2">💾</span>
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View Photo Modal -->
        <div class="modal fade" id="viewPhotoModal" tabindex="-1" role="dialog" aria-labelledby="viewPhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-blue-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center" id="viewPhotoModalLabel">
                            <span class="text-2xl mr-3">📷</span>
                            รูปโปรไฟล์นักเรียน
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-8 text-center bg-gradient-to-br from-gray-50 to-blue-50">
                        <div class="relative inline-block">
                            <img id="viewPhotoImg" src="../dist/img/default-avatar.svg" class="rounded-3xl shadow-2xl mx-auto border-4 border-white" style="max-height: 500px; max-width: 100%; object-fit: contain;" onerror="this.src='../dist/img/default-avatar.svg'">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 shadow-lg">
                                <span class="text-sm font-semibold text-blue-600">📷 รูปโปรไฟล์</span>
                            </div>
                        </div>
                        <h4 id="viewPhotoName" class="mt-6 font-bold text-gray-800 text-2xl"></h4>
                    </div>
                    <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-center">
                        <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">
                            <span class="mr-2">❌</span>
                            ปิด
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Photo Modal -->
        <div class="modal fade" id="editPhotoModal" tabindex="-1" role="dialog" aria-labelledby="editPhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-purple-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center" id="editPhotoModalLabel">
                            <span class="text-2xl mr-3">🖼️</span>
                            แก้ไขรูปโปรไฟล์
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-8">
                        <form id="editPhotoForm" enctype="multipart/form-data">
                            <input type="hidden" id="editPhotoStuId" name="stu_id">
                            
                            <div class="text-center mb-6">
                                <div class="relative inline-block mb-4">
                                    <img id="editPhotoPreview" src="../dist/img/default-avatar.svg" class="rounded-circle mx-auto shadow-lg border-4 border-purple-200" style="width: 180px; height: 180px; object-fit: cover;" onerror="this.src='../dist/img/default-avatar.svg'">
                                    <div class="absolute bottom-0 right-0 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full p-3 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h5 id="editPhotoStuName" class="font-bold text-gray-800 text-xl"></h5>
                            </div>
                            
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 mb-4">
                                <label for="photoFile" class="block text-sm font-bold text-gray-700 mb-3 flex items-center justify-center">
                                    <span class="text-2xl mr-2">📁</span>
                                    <span class="text-lg">เลือกรูปภาพใหม่</span>
                                </label>
                                <input type="file" class="form-control-file w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600 cursor-pointer" id="photoFile" name="photo" accept="image/*" required>
                                <small class="text-gray-600 mt-2 block text-center">
                                    <span class="font-semibold">รองรับไฟล์:</span> JPG, JPEG, PNG, GIF 
                                    <span class="font-semibold ml-2">ขนาดไม่เกิน:</span> 5MB
                                </small>
                            </div>
                            
                            <div class="form-group mt-4">
                                <div id="newPhotoPreview" class="text-center bg-white rounded-2xl p-4 shadow-inner" style="display:none;">
                                    <label class="block text-sm font-bold text-purple-700 mb-3 flex items-center justify-center">
                                        <span class="text-xl mr-2">👁️</span>
                                        ตัวอย่างรูปใหม่
                                    </label>
                                    <img id="newPhotoImg" src="" class="rounded-circle mx-auto shadow-lg border-4 border-purple-300" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-end space-x-3">
                        <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">
                            <span class="mr-2">❌</span>
                            ยกเลิก
                        </button>
                        <button type="button" id="submitEditPhotoForm" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                            <span class="mr-2">💾</span>
                            อัปโหลดรูปภาพ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
    <script>
        // ลบ token key ออก (ไม่ต้องใช้)
        // const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
        let studentTable;
        let studentTableInterval = null;

        $(document).ready(function() {
            studentTable = $('#studentTable').DataTable({
                columnDefs: [
                    { className: 'text-center px-6 py-4', width: '10%', targets: 0, orderable: false },  // Photo (เพิ่มจาก 8% เป็น 10%)
                    { className: 'text-center px-6 py-4 text-gray-800 font-semibold', width: '5%', targets: 1 }, // No
                    { className: 'text-center px-6 py-4 text-blue-600 font-bold', width: '10%', targets: 2 }, // ID
                    { className: 'px-6 py-4 text-gray-800 font-medium', width: '20%', targets: 3 }, // Name
                    { className: 'text-center px-6 py-4 text-purple-600 font-semibold', width: '10%', targets: 4 }, // Class/Room
                    { className: 'text-center px-6 py-4', width: '10%', targets: 5 }, // Status
                    { className: 'text-center px-6 py-4', width: '30%', targets: 6 } // Actions (ลดจาก 32% เป็น 30%)
                ],
                autoWidth: false,
                order: [[1, 'asc']], // เปลี่ยนจาก 0 เป็น 1 (เรียงตามเลขที่)
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: 'full_numbers',
                searching: true,
                language: {
                    "zeroRecords": "😔 ไม่พบข้อมูลนักเรียน",
                    "info": "📊 แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "processing": "⏳ กำลังโหลด...",
                    "search": "🔍 ค้นหา:",
                    "lengthMenu": "📋 แสดง _MENU_ รายการต่อหน้า",
                    "paginate": {
                        "first": "⏮️ หน้าแรก",
                        "last": "⏭️ หน้าสุดท้าย", 
                        "next": "➡️ ถัดไป",
                        "previous": "⬅️ ก่อนหน้า"
                    }
                },
                initComplete: function() {
                    $('.dataTables_wrapper .dataTables_filter input').addClass('rounded-lg border-2 border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-3 py-2');
                    $('.dataTables_wrapper .dataTables_length select').addClass('rounded-lg border-2 border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-3 py-2');
                },
                drawCallback: function() {
                    // Add hover effects to rows
                    $('#studentTable tbody tr').hover(
                        function() { $(this).addClass('bg-gradient-to-r from-blue-50 to-purple-50 transform scale-[1.01] transition-all duration-200'); },
                        function() { $(this).removeClass('bg-gradient-to-r from-blue-50 to-purple-50 transform scale-[1.01] transition-all duration-200'); }
                    );
                }
            });
            loadStudents();
            populateFilterSelects();

            // Start polling for real-time updates every 5 seconds
            studentTableInterval = setInterval(loadStudents, 5000);

            // Pause polling when modals are open, resume when closed
            $('#addStudentModal, #editStudentModal').on('show.bs.modal', function() {
                if (studentTableInterval) clearInterval(studentTableInterval);
            }).on('hidden.bs.modal', function() {
                studentTableInterval = setInterval(loadStudents, 5000);
            });
            
            // Reset photo form when modal closes
            $('#editPhotoModal').on('hidden.bs.modal', function() {
                $('#editPhotoForm')[0].reset();
                $('#newPhotoPreview').hide();
            });

            $('#btnAddStudent').on('click', function() {
                $('#addStudentForm')[0].reset();
                $('#addStudentModalLabel').text('เพิ่มข้อมูลนักเรียน');
                $('#addStudentModal').modal('show');
            });

            $('#submitAddStudentForm').on('click', async function() {
                const form = document.getElementById('addStudentForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                const res = await fetch('../controllers/StudentController.php?action=create', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#addStudentModal').modal('hide');
                    loadStudents();
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 บันทึกข้อมูลสำเร็จ!',
                        text: 'เพิ่มนักเรียนใหม่เรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timer: 2000,
                        background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                        color: 'white'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '😞 เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });

            $('#submitEditStudentForm').on('click', async function() {
                const form = document.getElementById('editStudentForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                const res = await fetch('../controllers/StudentController.php?action=update', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editStudentModal').modal('hide');
                    loadStudents();
                    Swal.fire({
                        icon: 'success',
                        title: '✨ บันทึกข้อมูลสำเร็จ!',
                        text: 'แก้ไขข้อมูลนักเรียนเรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timer: 2000,
                        background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                        color: 'white'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '😞 เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });

            $('#filterClass, #filterRoom, #filterStatus').on('change', function() {
                loadStudents();
            });
            
            // Photo file preview with validation
            $('#photoFile').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // ตรวจสอบขนาดไฟล์ (5MB = 5 * 1024 * 1024 bytes)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: '⚠️ ไฟล์ใหญ่เกินไป',
                            text: 'กรุณาเลือกรูปภาพที่มีขนาดไม่เกิน 5MB',
                            confirmButtonColor: '#f59e0b'
                        });
                        $(this).val('');
                        $('#newPhotoPreview').hide();
                        return;
                    }
                    
                    // ตรวจสอบชนิดไฟล์
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'warning',
                            title: '⚠️ ชนิดไฟล์ไม่ถูกต้อง',
                            text: 'กรุณาเลือกไฟล์ภาพ (JPG, PNG, GIF) เท่านั้น',
                            confirmButtonColor: '#f59e0b'
                        });
                        $(this).val('');
                        $('#newPhotoPreview').hide();
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#newPhotoImg').attr('src', e.target.result);
                        $('#newPhotoPreview').fadeIn(300);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Submit Edit Photo
            $('#submitEditPhotoForm').on('click', async function() {
                const form = document.getElementById('editPhotoForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                const formData = new FormData(form);
                
                // แสดง loading state
                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="mr-2">⏳</span>กำลังอัปโหลด...');
                
                try {
                    const res = await fetch('../controllers/StudentController.php?action=upload_photo', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await res.json();
                    
                    if (result.success) {
                        $('#editPhotoModal').modal('hide');
                        loadStudents();
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 อัปโหลดรูปภาพสำเร็จ!',
                            text: 'เปลี่ยนรูปโปรไฟล์เรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 2000,
                            background: 'linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%)',
                            color: 'white'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '😞 เกิดข้อผิดพลาด',
                            text: result.message || 'ไม่สามารถอัปโหลดรูปภาพได้',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '😞 เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    // คืนค่าปุ่มเดิม
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
        // เพิ่มเติม: โหลดค่า filter class/room
        function populateFilterSelects() {
            fetch('../controllers/StudentController.php?action=get_filters')
                .then(res => res.json())
                .then(data => {
                    // เติม class
                    const classSel = document.getElementById('filterClass');
                    classSel.innerHTML = '<option value="">-- เลือกชั้น --</option>';
                    
                    // !! KEV: แก้ไขบรรทัดนี้ !!
                    data.majors.forEach(cls => { // <--- แก้จาก .classes เป็น .majors
                        if (cls) classSel.innerHTML += `<option value="${cls}">${cls}</option>`;
                    });

                    // เติม room
                    const roomSel = document.getElementById('filterRoom');
                    roomSel.innerHTML = '<option value="">-- เลือกห้อง --</option>';
                    data.rooms.forEach(room => {
                        if (room) roomSel.innerHTML += `<option value="${room}">${room}</option>`;
                    });
                });
        }

                async function loadStudents() {
            const classVal = document.getElementById('filterClass').value;
            const roomVal = document.getElementById('filterRoom').value;
            const statusVal = document.getElementById('filterStatus').value;
            
            let url = '../controllers/StudentController.php?action=list_for_officer'; 
            
            if (classVal) url += '&class=' + encodeURIComponent(classVal);
            if (roomVal) url += '&room=' + encodeURIComponent(roomVal);
            if (statusVal) url += '&status=' + encodeURIComponent(statusVal);
            
            const res = await fetch(url);
            const responseData = await res.json();
            
            studentTable.clear();
            
            if (responseData && responseData.success && Array.isArray(responseData.data)) {
                responseData.data.forEach(student => {
                    // เก็บ URL จริงไว้ใน data-attribute แต่ไม่โหลดทันที
                    const photoUrl = student.Stu_picture ? 
                        `../photo/${student.Stu_picture}` : 
                        '../dist/img/default-avatar.svg';
                    
                    // สร้าง Avatar แบบตัวอักษรจากชื่อ
                    const fullName = `${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}`;
                    const initials = getInitials(student.Stu_name, student.Stu_sur);
                    const avatarColor = getAvatarColor(student.Stu_id);
                    
                    const photoHtml = `
                        <div class="relative inline-block photo-container" style="width: 60px; height: 60px;">
                            <div class="avatar-placeholder rounded-circle cursor-pointer view-photo-btn border-2 border-blue-200" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: ${avatarColor}; color: white; font-weight: bold; font-size: 18px; transition: all 0.3s ease;"
                                 data-id="${student.Stu_id}"
                                 data-name="${fullName}"
                                 data-photo="${photoUrl}"
                                 data-has-photo="${student.Stu_picture ? 'true' : 'false'}"
                                 title="คลิกเพื่อดูรูปขนาดใหญ่">
                                ${initials}
                            </div>
                            <button class="edit-photo-btn absolute bottom-0 right-0 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-full p-1.5 shadow-lg transform hover:scale-110 transition-all duration-200 border-2 border-white" 
                                    style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;"
                                    data-id="${student.Stu_id}"
                                    data-name="${fullName}"
                                    data-photo="${photoUrl}"
                                    title="แก้ไขรูปภาพ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    studentTable.row.add([
                        photoHtml,
                        student.Stu_no,
                        student.Stu_id,
                        student.Stu_pre + student.Stu_name + ' ' + student.Stu_sur,
                        'ม.' + student.Stu_major + '/' + student.Stu_room,
                        getStatusEmoji(student.Stu_status),
                        `<div class="flex justify-center space-x-2">
                            <button class="editStudentBtn bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white px-4 py-2 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 text-sm font-semibold" data-id="${student.Stu_id}">
                                ✏️ แก้ไข
                            </button>
                            <button class="deleteStudentBtn bg-gradient-to-r from-red-400 to-pink-500 hover:from-red-500 hover:to-pink-600 text-white px-4 py-2 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 text-sm font-semibold" data-id="${student.Stu_id}">
                                🗑️ ลบ
                            </button>
                            <button class="resetStuPwdBtn bg-gradient-to-r from-purple-400 to-indigo-500 hover:from-purple-500 hover:to-indigo-600 text-white px-4 py-2 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 text-sm font-semibold" data-id="${student.Stu_id}">
                                🔑 รีเซ็ต
                            </button>
                        </div>`
                    ]);
                });
            } else {
                console.error("ไม่สามารถโหลดข้อมูลนักเรียนได้ หรือ format ข้อมูลไม่ถูกต้อง:", responseData);
            }
            
            studentTable.draw();
            makeTableEditable();
        }

                // ฟังก์ชันสร้างตัวอักษรย่อจากชื่อ
        function getInitials(firstName, lastName) {
            const first = firstName ? firstName.charAt(0) : '';
            const last = lastName ? lastName.charAt(0) : '';
            return (first + last).toUpperCase() || '?';
        }
        
        // ฟังก์ชันสร้างสีจาก ID
        function getAvatarColor(id) {
            const colors = [
                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                'linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%)',
                'linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%)',
                'linear-gradient(135deg, #fdcbf1 0%, #e6dee9 100%)'
            ];
            // ใช้ ID เพื่อเลือกสีแบบสม่ำเสมอ
            const hash = id.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
            return colors[hash % colors.length];
        }
        
        // View Photo Event - โหลดรูปจริงทันที
        $(document).on('click', '.view-photo-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const photoUrl = $(this).data('photo');
            const name = $(this).data('name');
            
            console.log('View photo clicked:', { photoUrl, name });
            
            // โหลดรูปจริงทันที
            $('#viewPhotoImg').attr('src', photoUrl);
            $('#viewPhotoName').text(name);
            $('#viewPhotoModal').modal('show');
        });
        
        // Edit Photo Event - โหลดรูปจริงทันที
        $(document).on('click', '.edit-photo-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = $(this).data('id');
            const name = $(this).data('name');
            const photoUrl = $(this).data('photo');
            
            console.log('Edit photo clicked:', { id, name, photoUrl });
            
            $('#editPhotoStuId').val(id);
            $('#editPhotoStuName').text(name);
            
            // โหลดรูปจริงทันที
            $('#editPhotoPreview').attr('src', photoUrl);
            $('#photoFile').val('');
            $('#newPhotoPreview').hide();
            $('#editPhotoModal').modal('show');
        });

        // Status mapping:
        // 1 = ปกติ
        // 2 = จบการศึกษา
        // 3 = ย้ายโรงเรียน
        // 4 = ออกกลางคัน
        // 9 = เสียชีวิต

        function getStatusEmoji(status) {
            switch (status) {
                case 1: return '✅ ปกติ';
                case 2: return '🎓 จบการศึกษา';
                case 3: return '🚚 ย้ายโรงเรียน';
                case 4: return '❌ ออกกลางคัน';
                case 9: return '🕊️ เสียชีวิต';
                default: return '❓ ไม่ระบุ';
            }
        }

        $(document).on('click', '.editStudentBtn', function() {
            const id = $(this).data('id');
            openEditStudentModal(id);
        });
        $(document).on('click', '.deleteStudentBtn', function() {
            const id = $(this).data('id');
            deleteStudent(id);
        });
        $(document).on('click', '.resetStuPwdBtn', async function() {
            const id = $(this).data('id');
            const result = await Swal.fire({
                title: '🔑 รีเซ็ตรหัสผ่าน?',
                text: "ต้องการรีเซ็ตรหัสผ่านเป็นรหัสนักเรียนหรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8b5cf6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ใช่, รีเซ็ตเลย!',
                cancelButtonText: 'ยกเลิก 😅',
                background: 'linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%)'
            });
            if (!result.isConfirmed) return;
            const res = await fetch('../controllers/StudentController.php?action=resetpwd', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            });
            const response = await res.json();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '🔑 รีเซ็ตรหัสผ่านสำเร็จ!',
                    text: 'รหัสผ่านใหม่คือรหัสนักเรียน',
                    showConfirmButton: false,
                    timer: 2500,
                    background: 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                    color: 'white'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '😞 เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้',
                    confirmButtonColor: '#ef4444'
                });
            }
        });

        async function openEditStudentModal(id) {
            const res = await fetch('../controllers/StudentController.php?action=get&id=' + id);
            const data = await res.json();
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: data.message || 'ไม่สามารถโหลดข้อมูลนักเรียนได้ หรือข้อมูลไม่สมบูรณ์'
                });
                return;
            }
            if (!data || !data.Stu_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถโหลดข้อมูลนักเรียนได้ หรือข้อมูลไม่สมบูรณ์'
                });
                return;
            }
            const form = document.getElementById('editStudentForm');
            form.reset();
            
            // ข้อมูลพื้นฐาน
            document.getElementById('editStu_id_old').value = data.Stu_id;
            document.getElementById('editStu_id').value = data.Stu_id;
            document.getElementById('editStu_citizenid').value = data.Stu_citizenid || '';
            document.getElementById('editStu_no').value = data.Stu_no || '';
            document.getElementById('editStu_pre').value = data.Stu_pre || '';
            document.getElementById('editStu_name').value = data.Stu_name || '';
            document.getElementById('editStu_sur').value = data.Stu_sur || '';
            document.getElementById('editStu_nick').value = data.Stu_nick || '';
            document.getElementById('editStu_birth').value = data.Stu_birth || '';
            document.getElementById('editStu_religion').value = data.Stu_religion || '';
            document.getElementById('editStu_blood').value = data.Stu_blood || '';
            document.getElementById('editStu_phone').value = data.Stu_phone || '';
            document.getElementById('editStu_addr').value = data.Stu_addr || '';
            document.getElementById('editVehicle').value = data.vehicle || '0';
            
            // ข้อมูลการศึกษา
            document.getElementById('editStu_major').value = data.Stu_major || '';
            document.getElementById('editStu_room').value = data.Stu_room || '';
            document.getElementById('editStu_status').value = data.Stu_status || '1';
            document.getElementById('editRisk_group').value = data.Risk_group || '';
            
            // ข้อมูลบิดา
            document.getElementById('editFather_name').value = data.Father_name || '';
            document.getElementById('editFather_occu').value = data.Father_occu || '';
            document.getElementById('editFather_income').value = data.Father_income || '';
            
            // ข้อมูลมารดา
            document.getElementById('editMother_name').value = data.Mother_name || '';
            document.getElementById('editMother_occu').value = data.Mother_occu || '';
            document.getElementById('editMother_income').value = data.Mother_income || '';
            
            // ข้อมูลผู้ปกครอง
            document.getElementById('editPar_name').value = data.Par_name || '';
            document.getElementById('editPar_relate').value = data.Par_relate || '';
            document.getElementById('editPar_occu').value = data.Par_occu || '';
            document.getElementById('editPar_income').value = data.Par_income || '';
            document.getElementById('editPar_phone').value = data.Par_phone || '';
            document.getElementById('editPar_addr').value = data.Par_addr || '';
            
            $('#editStudentModalLabel').text('แก้ไขข้อมูลนักเรียน');
            $('#editStudentModal').modal('show');
        }

        async function deleteStudent(id) {
            const result = await Swal.fire({
                title: '🗑️ ยืนยันการลบข้อมูลนักเรียนนี้?',
                text: "การดำเนินการนี้ไม่สามารถย้อนกลับได้นะ!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก 😅',
                background: 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)'
            });
            if (!result.isConfirmed) return;
            const res = await fetch('../controllers/StudentController.php?action=delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            });
            const response = await res.json();
            if (response.success) {
                loadStudents();
                Swal.fire({
                    icon: 'success',
                    title: '✅ ลบข้อมูลสำเร็จ!',
                    text: 'ข้อมูลนักเรียนถูกลบเรียบร้อยแล้ว',
                    showConfirmButton: false,
                    timer: 2000,
                    background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    color: 'white'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '😞 เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถลบข้อมูลได้',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // เพิ่ม inline edit ให้กับ cell ที่ต้องการ
        function makeTableEditable() {
            $('#studentTable tbody').off('dblclick').on('dblclick', 'td', function () {
                const cell = studentTable.cell(this);
                const colIdx = cell.index().column;
                const rowIdx = cell.index().row;
                const rowData = studentTable.row(rowIdx).data();
                // เฉพาะคอลัมน์ที่อนุญาตให้แก้ไข
                // 0: เลขที่, 2: ชื่อ-นามสกุล, 3: ชั้น, 4: สถานะ
                if (![0,2,3,4].includes(colIdx)) return;

                let field, oldValue = cell.data(), input;
                const stu_id = rowData[1];

                if (colIdx === 0) { // เลขที่
                    field = 'Stu_no';
                    input = `<input type="number" min="1" max="50" class="form-control form-control-sm" value="${oldValue}" style="width:60px;">`;
                    cell.data(input).draw();
                    const $input = $(cell.node()).find('input').first();
                    $input.focus();
                    $input.on('keydown', async function(e) {
                        if (e.key === 'Enter') {
                            await saveInlineEdit(cell, field, stu_id, colIdx);
                        }
                    }).on('blur', async function() {
                        await saveInlineEdit(cell, field, stu_id, colIdx);
                    });
                } else if (colIdx === 2) { // ชื่อ-นามสกุล
                    // SweetAlert2 modal
                    const preMatch = rowData[2].match(/^(เด็กชาย|เด็กหญิง|นาย|นางสาว)/);
                    const pre = preMatch ? preMatch[1] : '';
                    const nameSur = rowData[2].replace(pre, '').trim().split(' ');
                    const name = nameSur[0] || '';
                    const sur = nameSur[1] || '';
                    Swal.fire({
                        title: 'แก้ไขชื่อ-นามสกุล',
                        html:
                            `<select id="swal-pre" class="swal2-input" style="width:90%;margin-bottom:8px;">
                                <option value="">-- โปรดเลือกคำนำหน้า --</option>
                                <option value="เด็กชาย"${pre === 'เด็กชาย' ? ' selected' : ''}>เด็กชาย</option>
                                <option value="เด็กหญิง"${pre === 'เด็กหญิง' ? ' selected' : ''}>เด็กหญิง</option>
                                <option value="นาย"${pre === 'นาย' ? ' selected' : ''}>นาย</option>
                                <option value="นางสาว"${pre === 'นางสาว' ? ' selected' : ''}>นางสาว</option>
                            </select>
                            <input id="swal-name" class="swal2-input" placeholder="ชื่อ" value="${name}">
                            <input id="swal-sur" class="swal2-input" placeholder="นามสกุล" value="${sur}">`,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'บันทึก',
                        cancelButtonText: 'ยกเลิก',
                        preConfirm: () => {
                            const preVal = $('#swal-pre').val();
                            const nameVal = $('#swal-name').val();
                            const surVal = $('#swal-sur').val();
                            if (!preVal || !nameVal || !surVal) {
                                Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบถ้วน');
                                return false;
                            }
                            return { pre: preVal, name: nameVal, sur: surVal };
                        }
                    }).then(async (result) => {
                        if (result.isConfirmed && result.value) {
                            // ส่งข้อมูลไป API
                            let value = { pre: result.value.pre, name: result.value.name, sur: result.value.sur };
                            let body = `id=${encodeURIComponent(stu_id)}&field=Stu_pre_name_sur&value=${encodeURIComponent(JSON.stringify(value))}`;
                            const res = await fetch('../controllers/StudentController.php?action=inline_update', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body
                            });
                            const apiResult = await res.json();
                            if (apiResult.success) {
                                // โหลดข้อมูลใหม่เฉพาะแถวนี้
                                const res2 = await fetch('../controllers/StudentController.php?action=get&id=' + stu_id);
                                const data = await res2.json();
                                if (data && data.Stu_id) {
                                    cell.data(data.Stu_pre + data.Stu_name + ' ' + data.Stu_sur).draw();
                                }
                            } else {
                                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: apiResult.message || 'ไม่สามารถบันทึกข้อมูลได้' });
                                cell.data(cell.data()).draw();
                            }
                        }
                    });
                } else if (colIdx === 3) { // ชั้น/ห้อง
                    field = 'Stu_major_room';
                    const [major, room] = rowData[3].replace('ม.','').split('/');
                    input = `<input type="number" min="1" max="6" class="form-control form-control-sm" value="${major}" style="width:50px;display:inline-block;"> / <input type="number" min="1" max="12" class="form-control form-control-sm" value="${room}" style="width:50px;display:inline-block;">`;
                    cell.data(input).draw();
                    const $inputs = $(cell.node()).find('input');
                    $inputs.first().focus();
                    $inputs.on('keydown', async function(e) {
                        if (e.key === 'Enter') {
                            await saveInlineEdit(cell, field, stu_id, colIdx);
                        }
                    }).on('blur', async function() {
                        await saveInlineEdit(cell, field, stu_id, colIdx);
                    });
                } else if (colIdx === 4) { // สถานะ
                    field = 'Stu_status';
                    input = `<select class="form-control form-control-sm" style="width:120px;">
                        <option value="1">✅ ปกติ</option>
                        <option value="2">🎓 จบการศึกษา</option>
                        <option value="3">🚚 ย้ายโรงเรียน</option>
                        <option value="4">❌ ออกกลางคัน</option>
                        <option value="9">🕊️ เสียชีวิต</option>
                    </select>`;
                    cell.data(input).draw();
                    const $input = $(cell.node()).find('select').first();
                    $input.focus();
                    $input.on('keydown', async function(e) {
                        if (e.key === 'Enter') {
                            await saveInlineEdit(cell, field, stu_id, colIdx);
                        }
                    }).on('blur', async function() {
                        await saveInlineEdit(cell, field, stu_id, colIdx);
                    });
                }
            });
        }

        async function saveInlineEdit(cell, field, stu_id, colIdx) {
            let value;
            if (field === 'Stu_no') {
                value = $(cell.node()).find('input').val();
            } else if (field === 'Stu_major_room') {
                const major = $(cell.node()).find('input').eq(0).val();
                const room = $(cell.node()).find('input').eq(1).val();
                value = { major, room };
            } else if (field === 'Stu_status') {
                value = $(cell.node()).find('select').val();
            }
            // ส่งข้อมูลไป API
            let body = `id=${encodeURIComponent(stu_id)}&field=${encodeURIComponent(field)}&value=${encodeURIComponent(typeof value === 'object' ? JSON.stringify(value) : value)}`;
            const res = await fetch('../controllers/StudentController.php?action=inline_update', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body
            });
            const result = await res.json();
            if (result.success) {
                // โหลดข้อมูลใหม่เฉพาะแถวนี้
                const res2 = await fetch('../controllers/StudentController.php?action=get&id=' + stu_id);
                const data = await res2.json();
                if (data && data.Stu_id) {
                    if (colIdx === 0) cell.data(data.Stu_no).draw();
                    if (colIdx === 3) cell.data('ม.' + data.Stu_major + '/' + data.Stu_room).draw();
                    if (colIdx === 4) cell.data(getStatusEmoji(data.Stu_status)).draw();
                }
            } else {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: result.message || 'ไม่สามารถบันทึกข้อมูลได้' });
                cell.data(cell.data()).draw(); // คืนค่าเดิม
            }
        }
        </script>

<style>
/* Photo Container Effects */
.photo-container {
    position: relative;
    display: inline-block;
}

.photo-container .avatar-placeholder:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    border-color: #3b82f6 !important;
}

.photo-container img.view-photo-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    border-color: #3b82f6 !important;
}

.photo-container .edit-photo-btn {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.photo-container:hover .edit-photo-btn {
    opacity: 1;
    transform: scale(1);
}

.photo-container .edit-photo-btn:hover {
    transform: scale(1.2) !important;
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.5);
}

/* Avatar placeholder styling */
.avatar-placeholder {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    user-select: none;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* File Input Styling */
input[type="file"]::file-selector-button {
    cursor: pointer;
    transition: all 0.3s ease;
}

input[type="file"]:hover::file-selector-button {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

/* Photo Preview Animation */
#newPhotoPreview {
    animation: fadeInScale 0.4s ease-out;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* View Photo Modal Image Hover */
#viewPhotoImg {
    transition: transform 0.3s ease;
}

#viewPhotoImg:hover {
    transform: scale(1.02);
}

/* Custom animations and effects */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.5s ease-out;
}

/* Enhanced table styling */
#studentTable tbody tr {
    transition: all 0.3s ease;
}

#studentTable tbody tr:hover {
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 50%, #90cdf4 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
}

/* Button hover effects */
.btn-gradient {
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Enhanced focus states */
.form-control:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Modal enhancements */
.modal-content {
    animation: bounceIn 0.5s ease-out;
}

/* DataTables custom styling */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%) !important;
    color: white !important;
    border-radius: 8px !important;
}

.dataTables_wrapper .dataTables_info {
    color: #4a5568 !important;
    font-weight: 600 !important;
    padding: 15px !important;
}


/* SweetAlert2 custom styling */
.swal2-popup {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
}

.swal2-confirm {
    border-radius: 12px !important;
    font-weight: bold !important;
}

.swal2-cancel {
    border-radius: 12px !important;
    font-weight: bold !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    #studentTable {
        font-size: 0.875rem;
    }
    
    .modal-dialog {
        margin: 10px;
    }
}

/* Loading animation */
.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Gradient text effects */
.gradient-text {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Enhanced button effects */
.editStudentBtn, .deleteStudentBtn, .resetStuPwdBtn {
    position: relative;
    overflow: hidden;
}

.editStudentBtn::before,
.deleteStudentBtn::before,
.resetStuPwdBtn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.editStudentBtn:hover::before,
.deleteStudentBtn:hover::before,
.resetStuPwdBtn:hover::before {
    left: 100%;
}

/* Enhanced select styling for better text visibility and consistency */
select.form-control {
    color: #1f2937 !important;
    font-weight: 600 !important;
    font-size: 16px !important;
    line-height: 1.8 !important;
    height: auto !important;
    min-height: 48px !important;
    padding: 12px 2.5rem 12px 16px !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-color: white !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
    background-position: right 0.5rem center !important;
    background-repeat: no-repeat !important;
    background-size: 1.5em 1.5em !important;
    vertical-align: middle !important;
    display: flex !important;
    align-items: center !important;
}

select.form-control option {
    color: #1f2937 !important;
    background-color: white !important;
    padding: 12px 16px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    line-height: 1.8 !important;
    min-height: 40px !important;
}

select.form-control:focus {
    outline: none !important;
}

select.form-control:focus option {
    background-color: #f9fafb !important;
}

select.form-control:focus option:checked {
    background-color: #dbeafe !important;
    color: #1e40af !important;
}

</style>

<?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>

