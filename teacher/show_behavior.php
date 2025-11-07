<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));


require_once('header.php');

?>

<style>
/* Custom styles for enhanced UI */
body {
    font-family: 'Mali', sans-serif;
}

.form-check-input {
    transform: scale(2);
    margin-right: 30px;
}

/* DataTables custom styling */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding: 1rem;
    color: #374151;
}

.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    transition: border-color 0.2s ease;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background: white;
    color: #374151;
    transition: all 0.2s ease;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Modal enhancements */
.modal-content {
    border: none;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Button hover effects */
.btn:hover {
    transform: translateY(-1px);
}

/* Progress bar animation */
.progress-bar {
    transition: width 0.5s ease-in-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced form styling */
.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Loading animation for buttons */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<?php
$currentDate = date("Y-m-d");

?>


<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <header class="bg-white">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <!-- space reserved for page controls or breadcrumbs if needed -->
            </div>
        </header>
    <!-- /.content-header -->
    <!-- Modal -->

    <section class="content">
        <div class="container-fluid px-4 py-6">
            <div class="max-w-7xl mx-auto">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-lg p-8 mb-8">
                    <div class="text-center mb-6">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="w-16 h-16 mx-auto mb-4 rounded-full shadow-md">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">📋 รายงานการหักคะแนนพฤติกรรม</h1>
                        <h2 class="text-xl text-gray-600">ของครู <span class="font-semibold text-blue-600"><?=$teacher_name?></span></h2>
                        <h3 class="text-lg text-gray-500">ภาคเรียนที่ <span class="font-medium"><?=$term?></span> ปีการศึกษา <span class="font-medium"><?=$pee?></span></h3>
                    </div>

                    <div class="flex flex-wrap gap-4 justify-center mb-6">
                        <button type="button" id="addButton" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" data-toggle="modal" data-target="#addBehaviorModal">
                            <span class="text-lg">➕</span> หักคะแนนนักเรียน <span class="text-lg">➕</span>
                        </button>
                        <a href="behavior.php" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2 no-underline">
                            <span class="text-lg">📊</span> ดูคะแนนพฤติกรรมชั้นเรียน <span class="text-lg">📊</span>
                        </a>
                        <button class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" id="printButton" onclick="printPage()">
                            <span class="text-lg">🖨️</span> พิมพ์รายงาน <span class="text-lg">🖨️</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="record_table" class="w-full table-auto">
                                <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-center font-semibold">#</th>
                                        <th class="px-4 py-3 text-center font-semibold">เลขประจำตัว</th>
                                        <th class="px-4 py-3 text-center font-semibold">ชื่อ-นามสกุล</th>
                                        <th class="px-4 py-3 text-center font-semibold">วันที่</th>
                                        <th class="px-4 py-3 text-center font-semibold">ประเภทพฤติกรรม</th>
                                        <th class="px-4 py-3 text-center font-semibold">รายละเอียด</th>
                                        <th class="px-4 py-3 text-center font-semibold">คะแนน</th>
                                        <th class="px-4 py-3 text-center font-semibold">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 rounded-lg shadow-md p-6">
                    <h4 class="text-2xl font-bold text-yellow-700 flex items-center mb-4">
                        <span class="text-3xl mr-3">⚠️</span> คำแนะนำ
                    </h4>
                    <div class="space-y-3 text-gray-700">
                        <p class="flex items-start">
                            <span class="text-xl mr-3 mt-1">📖</span>
                            <span>คลิกปุ่ม <strong class="text-red-600">หักคะแนนนักเรียน</strong> เพื่อเลือกนักเรียนที่ต้องการบันทึกคะแนน</span>
                        </p>
                        <p class="flex items-start">
                            <span class="text-xl mr-3 mt-1">✏️</span>
                            <span>ป้อนข้อมูลให้ครบทุกช่องจากนั้นคลิกปุ่ม <strong class="text-blue-600">"บันทึกข้อมูล"</strong> หรือ <strong class="text-gray-600">"ปิดหน้าต่าง"</strong> เมื่อต้องการล้างข้อมูล</span>
                        </p>
                        <p class="flex items-start">
                            <span class="text-xl mr-3 mt-1">🎯</span>
                            <span>คะแนนพฤติกรรมเต็ม <strong class="text-green-600">100 คะแนน</strong></span>
                        </p>
                        <div class="mt-4 space-y-2">
                            <p class="flex items-start ml-8">
                                <span class="text-lg mr-3">🚨</span>
                                <strong class="text-red-600">กลุ่มที่ 1:</strong> คะแนนต่ำกว่า 50 → เข้าค่ายปรับพฤติกรรม (โดยกลุ่มบริหารงานกิจการนักเรียน)
                            </p>
                            <p class="flex items-start ml-8">
                                <span class="text-lg mr-3">⚠️</span>
                                <strong class="text-orange-600">กลุ่มที่ 2:</strong> คะแนนระหว่าง 50 - 70 → บำเพ็ญประโยชน์ 20 ชั่วโมง (โดยหัวหน้าระดับ)
                            </p>
                            <p class="flex items-start ml-8">
                                <span class="text-lg mr-3">✅</span>
                                <strong class="text-green-600">กลุ่มที่ 3:</strong> คะแนนระหว่าง 71 - 99 → บำเพ็ญประโยชน์ 10 ชั่วโมง (โดยครูที่ปรึกษา)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Modal -->
<div class="modal fade" tabindex="-1" id="addBehaviorModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-xl shadow-2xl border-0">
            <div class="modal-header bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-t-xl">
                <h5 class="modal-title text-xl font-bold flex items-center">
                    <span class="text-2xl mr-3">📝</span> หักคะแนนนักเรียน
                </h5>
                <button type="button" class="close text-white text-2xl hover:text-gray-200 transition duration-200" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-8 bg-gray-50">
                <div id="searchResults" class="text-center mb-6 p-4 bg-white rounded-lg shadow-sm border"></div>
                <form id="addBehaviorForm" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-2">
                            <label for="addStu_id" class="block text-sm font-semibold text-gray-700 flex items-center">
                                <span class="text-lg mr-2">🆔</span> เลขประจำตัวนักเรียน:
                            </label>
                            <input type="text" name="addStu_id" id="addStu_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-center font-medium" maxlength="5" required>
                            <small class="text-red-500 text-xs" id="stuidError"></small>
                        </div>
                        <div class="space-y-2">
                            <label for="addBehavior_date" class="block text-sm font-semibold text-gray-700 flex items-center">
                                <span class="text-lg mr-2">📅</span> วันที่:
                            </label>
                            <input type="date" name="addBehavior_date" id="addBehavior_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-center" value="<?=$currentDate?>" required>
                            <small class="text-red-500 text-xs" id="dateError"></small>
                        </div>
                    </div>
                    <div class="space-y-2 mb-6">
                        <label for="addBehavior_type" class="block text-sm font-semibold text-gray-700 flex items-center">
                            <span class="text-lg mr-2">🚨</span> ประเภทพฤติกรรม:
                        </label>
                        <select name="addBehavior_type" id="addBehavior_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-center" required>
                            <option value="">-- โปรดเลือกพฤติกรรม --</option>
                            <?php 
                                $behaviors = array(
                                    "หนีเรียนหรือออกนอกสถานศึกษา",
                                    "เล่นการพนัน",
                                    "มาโรงเรียนสาย",
                                    "แต่งกาย/ทรงผมผิดระเบียบ",
                                    "พกพาอาวุธหรือวัตถุระเบิด",
                                    "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์",
                                    "สูบบุหรี่",
                                    "เสพยาเสพติด",
                                    "ลักทรัพย์ กรรโชกทรัพย์",
                                    "ก่อเหตุทะเลาะวิวาท",
                                    "แสดงพฤติกรรมทางชู้สาว",
                                    "จอดรถในที่ห้ามจอด",
                                    "แสดงพฤติกรรมก้าวร้าว",
                                    "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
                                );

                                foreach ($behaviors as $behavior) {
                                    echo '<option value="' . $behavior . '">' . $behavior . '</option>';
                                }
                            ?>
                        </select>
                        <small class="text-red-500 text-xs" id="typeError"></small>
                    </div>
                    <div class="space-y-2 mb-6">
                        <label for="addBehavior_name" class="block text-sm font-semibold text-gray-700 flex items-center">
                            <span class="text-lg mr-2">📝</span> รายละเอียด:
                        </label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" type="text" name="addBehavior_name" id="addBehavior_name">
                    </div>
                    <div class="space-y-2 mb-8">
                        <label for="addBehavior_score" class="block text-sm font-semibold text-gray-700 flex items-center">
                            <span class="text-lg mr-2">⚖️</span> จำนวนคะแนน (หัก):
                        </label>
                        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-center font-medium" type="number" name="addBehavior_score" id="addBehavior_score" min="0" max="100" value="0" readonly>
                        <small class="text-gray-500 text-xs block mt-1">ใส่คะแนนที่ต้องการหัก (เช่น 5 หรือ 10)</small>
                    </div>
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <button type="button" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" data-dismiss="modal">
                            <span class="text-lg">❌</span> ปิดหน้าต่าง
                        </button>
                        <div class="flex gap-3">
                            <input type="hidden" name="term" value="<?=$term?>">
                            <input type="hidden" name="pee" value="<?=$pee?>">
                            <input type="hidden" name="teacherid" value="<?=$teacher_id?>">
                            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2">
                                <span class="text-lg">💾</span> บันทึกข้อมูล
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Car Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editBehaviorForm" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
                <div class="modal-header flex justify-between items-center border-b pb-4">
                    <h5 class="text-lg font-semibold" id="editModalLabel">แก้ไขข้อมูลคะแนนพฤติกรรม</h5>
                    <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body space-y-4">
                    <div id="searchResultsEdit" class="text-center mb-3"></div>
                    <input type="hidden" id="editBehaviorId" name="editId">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editStuId">เลขประจำตัวนักเรียน</label>
                        <input type="text" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editStuId" name="editStu_id" placeholder="กรอกเลขประจำตัว" required>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorDate">วันที่</label>
                        <input type="date" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editBehaviorDate" name="editBehavior_date" placeholder="วันเดือนปีที่หัก(ค.ศ.)"  required>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorType">ประเภท</label>
                        <select name="editBehavior_type" id="editBehaviorType" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- โปรดเลือกพฤติกรรม --</option>
                                <?php 
                                    $behaviors = array(
                                        "หนีเรียนหรือออกนอกสถานศึกษา",
                                        "เล่นการพนัน",
                                        "มาโรงเรียนสาย",
                                        "แต่งกาย/ทรงผมผิดระเบียบ",
                                        "พกพาอาวุธหรือวัตถุระเบิด",
                                        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์",
                                        "สูบบุหรี่",
                                        "เสพยาเสพติด",
                                        "ลักทรัพย์ กรรโชกทรัพย์",
                                        "ก่อเหตุทะเลาะวิวาท",
                                        "แสดงพฤติกรรมทางชู้สาว",
                                        "จอดรถในที่ห้ามจอด",
                                        "แสดงพฤติกรรมก้าวร้าว",
                                        "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
                                    );

                                    foreach ($behaviors as $behavior) {
                                        echo '<option value="' . $behavior . '">' . $behavior . '</option>';
                                    }
                                ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorName">รายละเอียด</label>
                        <input type="text" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editBehaviorName" name="editBehavior_name" placeholder="กรอกรายละเอียด" required>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorScore">จำนวนคะแนน (หัก)</label>
                        <input type="number" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editBehaviorScore" name="editBehavior_score" placeholder="เช่น 5" min="0" max="100" readonly>
                    </div>
                </div>
                <div class="modal-footer flex justify-end space-x-2 border-t pt-4">
                    <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('script.php');?>

<script>
$(document).ready(function() {

    loadTable(); // Load data on page load


    function convertToThaiDate(dateString) {
        const months = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];
        const date = new Date(dateString);
        const day = date.getDate();
        const month = months[date.getMonth()];
        // Convert Gregorian year to Buddhist Era (พ.ศ.)
        const year = date.getFullYear() + 543;
        return `${day} ${month} ${year}`;
    }
    function convertToEngDate(thaiDate) {   
        const dateParts = thaiDate.split('-'); // แยกวันที่ด้วย "-"
        const year = parseInt(dateParts[0]) - 543;// แปลงปี พ.ศ. เป็น ค.ศ.
        return `${year}-${dateParts[1]}-${dateParts[2]}`; // คืนค่าผลลัพธ์เป็นปี ค.ศ.
    }

    // Auto score mapping for behavior types
    function getScoreForType(type) {
        const map = {
            "หนีเรียนหรือออกนอกสถานศึกษา": 10,
            "เล่นการพนัน": 20,
            "มาโรงเรียนสาย": 5,
            "แต่งกาย/ทรงผมผิดระเบียบ": 5,
            "พกพาอาวุธหรือวัตถุระเบิด": 20,
            "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์": 20,
            "สูบบุหรี่": 30,
            "เสพยาเสพติด": 30,
            "ลักทรัพย์ กรรโชกทรัพย์": 30,
            "ก่อเหตุทะเลาะวิวาท": 20,
            "แสดงพฤติกรรมทางชู้สาว": 20,
            "จอดรถในที่ห้ามจอด": 10,
            "แสดงพฤติกรรมก้าวร้าว": 10,
            "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ": 5
        };
        return map[type] ?? '';
    }

    // When teacher selects a behavior type, auto-fill the score (readonly input)
    $('#addBehavior_type').on('change', function() {
        const t = $(this).val();
        $('#addBehavior_score').val(getScoreForType(t));
    });

    $('#editBehaviorType').on('change', function() {
        const t = $(this).val();
        $('#editBehaviorScore').val(getScoreForType(t));
    });


    async function loadTable() {
        try {
            var TeacherId = <?=$teacher_id?>;

            const response = await $.ajax({
                url: '../controllers/BehaviorController.php?action=teacher_behaviors',
                method: 'GET',
                dataType: 'json',
                data: { teacher_id: TeacherId }
            });

            if (!response || response.success !== true) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                return;
            }

            

            const table = $('#record_table').DataTable({
                destroy: true,
                pageLength: 50,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, className: 'px-4 py-3 text-center font-medium text-gray-900' },
                    { targets: 1, className: 'px-4 py-3 text-center font-medium text-gray-700' },
                    { targets: 2, className: 'px-4 py-3 text-left font-medium text-gray-900' },
                    {
                        targets: 3, className: 'px-4 py-3 text-center font-medium text-gray-700'
                    },
                    { targets: 4, className: 'px-4 py-3 text-center font-medium text-gray-700' },
                    { targets: 5, className: 'px-4 py-3 text-left font-medium text-gray-700' },
                    { targets: 6, className: 'px-4 py-3 text-center font-bold text-red-600' },
                    { targets: 7, className: 'px-4 py-3 text-center' }
                ],
                autoWidth: false,
                info: true,
                lengthChange: true,
                ordering: true,
                responsive: true,
                paging: true,
                searching: true,
                language: {
                    search: "🔍 ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่พบข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    paginate: {
                        first: "แรก",
                        last: "สุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });

            // Clear old data
            table.clear();

            if (!response.data || response.data.length === 0) {
                // DataTables requires the same number of columns for row.add();
                // instead of adding a malformed row, clear and inject a single
                // tbody row with a colspan message so the table shows a full-width
                // "no data" message without triggering the Requested unknown parameter error.
                table.draw();
                $('#record_table tbody').html('<tr><td colspan="8" class="text-center py-8">ไม่พบข้อมูล</td></tr>');
            } else {
                response.data.forEach((item, index) => {
                    const thaiDate = convertToThaiDate(item.behavior_date);

                    // ปุ่มแก้ไขและลบ
                    const actionButtons = `
                        <button class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold py-2 px-3 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-1 text-sm mr-1" onclick="editBehavior(${item.id})">
                            <span class="text-base">✏️</span> แก้ไข
                        </button>
                        <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-2 px-3 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-1 text-sm" onclick="deleteBehavior(${item.id})">
                            <span class="text-base">🗑️</span> ลบ
                        </button>
                    `;

                    table.row.add([
                        (index + 1),
                        item.stu_id,
                        item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur,
                        thaiDate,
                        item.behavior_type,
                        item.behavior_name || '-',
                        `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                            <span class="mr-1">⚠️</span>${item.behavior_score}
                        </span>`,
                        actionButtons
                    ]);
                });
            }

            // Re-draw table
            table.draw();

        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            console.error(error);
        }
    }



    $('#addBehaviorModal form').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการ submit ฟอร์มปกติ

        // ตรวจสอบค่าก่อนส่ง
        let valid = true;
        let stuid = $('#addStu_id').val().trim();
        let date = $('#addBehavior_date').val().trim();
        let type = $('#addBehavior_type').val().trim();
        let score = $('#addBehavior_score').val().trim();

        // ล้าง error เดิม
        $('#stuidError').text('').removeClass('block');
        $('#dateError').text('').removeClass('block');
        $('#typeError').text('').removeClass('block');

        if (!stuid) {
            $('#stuidError').text('กรุณากรอกเลขประจำตัวนักเรียน').addClass('block');
            valid = false;
        }
        if (!date) {
            $('#dateError').text('กรุณาเลือกวันที่').addClass('block');
            valid = false;
        }
        if (!type) {
            $('#typeError').text('กรุณาเลือกประเภทพฤติกรรม').addClass('block');
            valid = false;
        }
        if (score === '' || isNaN(score)) {
            // score must be numeric
            $('#typeError').text('กรุณาระบุจำนวนคะแนนที่หัก').addClass('block');
            valid = false;
        }

        if (!valid) {
            return; // ไม่ส่งฟอร์มหากข้อมูลไม่ครบ
        }

        var formData = new FormData(this); // เก็บข้อมูลทั้งหมดจากฟอร์ม

        // แสดงข้อมูลใน FormData
        // for (var pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }

        $.ajax({
            url: '../controllers/BehaviorController.php?action=create',
            type: 'POST',
            data: formData,
            processData: false,  // ไม่ให้ jQuery จัดการกับข้อมูล
            contentType: false,  // ไม่กำหนด content-type ด้วยตัวเอง
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success');
                    $('#addBehaviorModal').modal('hide'); // ปิด modal หลังจากบันทึกข้อมูล
                    loadTable(); // รีเฟรชข้อมูลในตาราง
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
            }
        });
    });

    // Function to edit behavior
    window.editBehavior = function(id) {
        $.get(`../controllers/BehaviorController.php?action=get&id=${id}`, function (data) {
            if (data) {
                // แสดงข้อมูลใน modal
                $('#editBehaviorId').val(data.id);
                $('#editStuId').val(data.stu_id);
                $('#editBehaviorDate').val(data.behavior_date);
                $('#editBehaviorType').val(data.behavior_type);
                $('#editBehaviorName').val(data.behavior_name);
                // Populate the score field (avoid undefined by using empty string fallback)
                $('#editBehaviorScore').val(data.behavior_score ?? '');

                // ถ้ามี stu_id ให้ทำการค้นหาข้อมูล
                if (data.stu_id !== '') {
                    $.ajax({
                        type: 'GET',
                        url: '../controllers/BehaviorController.php?action=search_student',
                        data: { id: data.stu_id },
                        dataType: 'json',
                        success: function(response) {
                            if (response) {
                                var html = '<div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">';
                                if (response.Stu_picture) {
                                    html += '<img src="https://std.phichai.ac.th/photo/' + response.Stu_picture + '" alt="photo" class="w-36 h-36 rounded-full mr-4 shadow-md object-cover border-2 border-white">';
                                }
                                html += '<div class="flex-1">';
                                html += '<div class="font-bold text-lg text-gray-800 mb-1">' + (response.Stu_pre || '') + response.Stu_name + ' ' + response.Stu_sur + '</div>';
                                html += '<div class="text-sm text-gray-600"><span class="font-medium">รหัส:</span> ' + response.Stu_id + ' <span class="font-medium">ชั้น:</span> ' + (response.Stu_major || '-') + ' / ' + (response.Stu_room || '-') + '</div>';
                                html += '</div></div>';
                                $('#searchResultsEdit').html(html);
                            }
                        }
                    });
                } else {
                    $('#searchResultsEdit').empty();
                }
                
                // เปิด modal
                $('#editModal').modal('show');
            }
        }).fail(() => Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error'));
    };

    // Function to delete behavior
    window.deleteBehavior = function(id) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ใช่, ลบเลย!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/BehaviorController.php?action=delete',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: id }),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('ลบแล้ว!', 'ข้อมูลของคุณถูกลบแล้ว.', 'success');
                            loadTable();
                        } else {
                            Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                    }
                });
            }
        });
    };

    $('#editBehaviorForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '../controllers/BehaviorController.php?action=update',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    Swal.fire('สำเร็จ', 'ข้อมูลถูกอัพเดทเรียบร้อยแล้ว', 'success');
                    loadTable();
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถอัพเดทข้อมูลได้', 'error');
                }
            },
            error: function () {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถอัพเดทข้อมูลได้', 'error');
            }
        });
    });

    // Function to handle printing
    window.printPage = function() {
        let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');

        // Hide the export to Excel button
        $('#record_table_wrapper .dt-buttons').hide(); // Hides the export buttons

        // Hide the elements you want to exclude from the print
        elementsToHide.hide();
        $('thead').css('display', 'table-header-group'); // Ensure header shows

        setTimeout(() => {
            window.print();
            elementsToHide.show();
            $('#record_table_wrapper .dt-buttons').show();
        }, 100);
    };

    // Function to set up the print layout
    function setupPrintLayout() {
        var style = '@page { size: A4 portrait; margin: 0.5in; }';
        var printStyle = document.createElement('style');
        printStyle.appendChild(document.createTextNode(style));
        document.head.appendChild(printStyle);
    }

    $('#addStu_id').on('input', function() {
        var stuid = $(this).val();
        if (stuid !== '') {
            $.ajax({
                type: 'GET',
                url: '../controllers/BehaviorController.php?action=search_student',
                data: { id: stuid },
                dataType: 'json',
                success: function(response) {
                    if (!response) {
                        $('#searchResults').html('<div class="text-danger">ไม่พบข้อมูลนักเรียน</div>');
                        return;
                    }
                    // Build a small preview
                    var html = '<div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">';
                    if (response.Stu_picture) {
                        html += '<img src="https://std.phichai.ac.th/photo/' + response.Stu_picture + '" alt="photo" class="w-36 h-36 rounded-full mr-4 shadow-md object-cover border-2 border-white">';
                    }
                    html += '<div class="flex-1">';
                    html += '<div class="font-bold text-lg text-gray-800 mb-1">' + (response.Stu_pre || '') + response.Stu_name + ' ' + response.Stu_sur + '</div>';
                    html += '<div class="text-sm text-gray-600"><span class="font-medium">รหัส:</span> ' + response.Stu_id + ' <span class="font-medium">ชั้น:</span> ' + (response.Stu_major || '-') + ' / ' + (response.Stu_room || '-') + '</div>';
                    html += '</div></div>';
                    $('#searchResults').html(html);
                },
                error: function() {
                    $('#searchResults').html('<div class="text-danger">เกิดข้อผิดพลาดในการค้นหา</div>');
                }
            });
        } else {
            $('#searchResults').empty();
        }
    });
    $('#editStuId').on('input', function() {
        var stuid = $(this).val();
        if (stuid !== '') {
            $.ajax({
                type: 'GET',
                url: '../controllers/BehaviorController.php?action=search_student',
                data: { id: stuid },
                dataType: 'json',
                success: function(response) {
                    if (!response) {
                        $('#searchResultsEdit').html('<div class="text-danger">ไม่พบข้อมูลนักเรียน</div>');
                        return;
                    }
                    // Build a small preview
                    var html = '<div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">';
                    if (response.Stu_picture) {
                        html += '<img src="https://std.phichai.ac.th/photo/' + response.Stu_picture + '" alt="photo" class="w-36 h-36 rounded-full mr-4 shadow-md object-cover border-2 border-white">';
                    }
                    html += '<div class="flex-1">';
                    html += '<div class="font-bold text-lg text-gray-800 mb-1">' + (response.Stu_pre || '') + response.Stu_name + ' ' + response.Stu_sur + '</div>';
                    html += '<div class="text-sm text-gray-600"><span class="font-medium">รหัส:</span> ' + response.Stu_id + ' <span class="font-medium">ชั้น:</span> ' + (response.Stu_major || '-') + ' / ' + (response.Stu_room || '-') + '</div>';
                    html += '</div></div>';
                    $('#searchResultsEdit').html(html);
                },
                error: function() {
                    $('#searchResultsEdit').html('<div class="text-danger">เกิดข้อผิดพลาดในการค้นหา</div>');
                }
            });
        } else {
            $('#searchResultsEdit').empty();
        }
    });


});


</script>
</body>
</html>
