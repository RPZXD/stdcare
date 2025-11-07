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

/* Print styles for better report printing */
@media print {
    body {
        font-family: 'Sarabun', 'Mali', sans-serif;
        font-size: 12pt;
        line-height: 1.4;
        color: #000;
        background: white !important;
        margin: 0;
        padding: 0;
    }
    
    /* Hide everything except print content */
    body > *:not(.print-content) {
        display: none !important;
    }
    
    .print-content {
        display: block !important;
        padding: 20px;
    }
    
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        page-break-after: avoid;
    }
    
    .print-header img {
        max-width: 80px;
        height: auto;
        display: block;
        margin: 0 auto 10px;
    }
    
    .print-header h1 {
        font-size: 18pt;
        font-weight: bold;
        margin: 5px 0;
    }
    
    .print-header h2, .print-header h3 {
        font-size: 12pt;
        margin: 3px 0;
    }
    
    .print-header p {
        font-size: 10pt;
        margin: 2px 0;
    }
    
    .print-table {
        margin-top: 20px;
    }
    
    .print-table table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 10pt !important;
    }
    
    .print-table table th, .print-table table td {
        border: 1px solid #000 !important;
        padding: 8px !important;
        text-align: center !important;
    }
    
    .print-table table th {
        background: #f0f0f0 !important;
        font-weight: bold !important;
        color: #000 !important;
    }
    
    .print-table table td:nth-child(3) {
        text-align: left !important;
    }
    
    .print-table table tbody tr:nth-child(even) {
        background: #f9f9f9 !important;
    }
    
    /* Progress bar styling for print */
    .w-full.bg-gray-200 {
        background: #e5e5e5 !important;
        border: 1px solid #000 !important;
        height: 20px !important;
        position: relative !important;
    }
    
    .bg-gradient-to-r.from-red-500 {
        background: #dc2626 !important;
        height: 100% !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
    
    .absolute.inset-0 {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: white !important;
        font-weight: bold !important;
        font-size: 8pt !important;
    }
    
    /* Page setup */
    @page {
        size: A4 portrait;
        margin: 1cm;
    }
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

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Modal -->

    <section class="content">
        <div class="container-fluid px-4 py-6">
            <div class="max-w-7xl mx-auto">
                <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl shadow-lg p-8 mb-8">
                    <div class="text-center mb-6">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="w-16 h-16 mx-auto mb-4 rounded-full shadow-md">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">📊 รายงานคะแนนพฤติกรรมของนักเรียน</h1>
                        <h2 class="text-xl text-gray-600">ระดับชั้นมัธยมศึกษาปีที่ <span class="font-semibold text-blue-600"><?=$class."/".$room?></span></h2>
                        <h3 class="text-lg text-gray-500">ภาคเรียนที่ <span class="font-medium"><?=$term?></span> ปีการศึกษา <span class="font-medium"><?=$pee?></span></h3>
                    </div>

                    <div class="flex flex-wrap gap-4 justify-center mb-6">
                        <button type="button" id="addButton" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" data-toggle="modal" data-target="#addBehaviorModal">
                            <span class="text-lg">➕</span> หักคะแนนนักเรียน <span class="text-lg">➕</span>
                        </button>
                        <a href="show_behavior.php" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2 no-underline">
                            <span class="text-lg">🔍</span> แสดงข้อมูลการหักคะแนนของครู <span class="text-lg">🔍</span>
                        </a>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" id="printButton" onclick="printPage()">
                            <span class="text-lg">🖨️</span> พิมพ์รายงาน <span class="text-lg">🖨️</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="record_table" class="w-full table-auto">
                                <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-center font-semibold">เลขที่</th>
                                        <th class="px-4 py-3 text-center font-semibold">เลขประจำตัว</th>
                                        <th class="px-4 py-3 text-center font-semibold">ชื่อ-นามสกุล</th>
                                        <th class="px-4 py-3 text-center font-semibold">ถูกหัก</th>
                                        <th class="px-4 py-3 text-center font-semibold">ถูกหัก (%)</th>
                                        <th class="px-4 py-3 text-center font-semibold">ถูกหักโดย</th>
                                        <th class="px-4 py-3 text-center font-semibold">รายละเอียด</th>
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
                            <span class="text-xl mr-3 mt-1">�</span>
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

<!-- Modal สำหรับแสดงรายละเอียดการถูกหัก -->
<div class="modal fade" tabindex="-1" id="studentDetailsModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-xl shadow-2xl border-0">
            <div class="modal-header bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-xl">
                <h5 class="modal-title text-xl font-bold flex items-center">
                    <span class="text-2xl mr-3">📋</span> รายละเอียดการถูกหักคะแนน
                </h5>
                <button type="button" class="close text-white text-2xl hover:text-gray-200 transition duration-200" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-6 bg-gray-50">
                <div id="studentInfo" class="mb-6 p-4 bg-white rounded-lg shadow-sm border">
                    <!-- ข้อมูลนักเรียนจะแสดงที่นี่ -->
                </div>
                <div id="behaviorDetails" class="space-y-4">
                    <!-- รายละเอียดการถูกหักจะแสดงที่นี่ -->
                </div>
            </div>
            <div class="modal-footer justify-center pt-6 border-t border-gray-200">
                <button type="button" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2" data-dismiss="modal">
                    <span class="text-lg">❌</span> ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>
</div>

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


<?php require_once('script.php');?>

<script>
$(document).ready(function() {

// Function to handle printing
window.printPage = function() {
    // Get PHP variables for print header
    const classRoom = "<?=$class."/".$room?>";
    const term = "<?=$term?>";
    const pee = "<?=$pee?>";
    const teacherName = "<?=$teacher_name?>";
    
    // Create print content
    const printContent = `
        <div class="print-content">
            <div class="print-header">
                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo">
                <h1>📊 รายงานคะแนนพฤติกรรมของนักเรียน</h1>
                <h2>ระดับชั้นมัธยมศึกษาปีที่ ${classRoom}</h2>
                <h3>ภาคเรียนที่ ${term} ปีการศึกษา ${pee}</h3>
                <p>ครูที่ปรึกษา: ${teacherName}</p>
                <p>วันที่พิมพ์: ${new Date().toLocaleDateString('th-TH', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}</p>
                <hr style="border: 1px solid #000; margin: 10px 0;">
            </div>
            <div class="print-table">
                ${$('#record_table').parent().html()}
            </div>
        </div>
    `;
    
    // Hide the entire body content and show only print content
    $('body').children().hide();
    $('body').append(printContent);
    
    // Trigger print
    window.print();
    
    // Cleanup after printing
    setTimeout(() => {
        $('.print-content').remove();
        $('body').children().show();
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

function convertToThaiDate(dateString) {
    const months = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // Convert to Buddhist year
    return `${day} ${month} ${year}`;
}

// Auto score mapping for behavior types (used to auto-fill readonly score input)
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

// When teacher selects a behavior type in the add modal, auto-fill the read-only score field
$('#addBehavior_type').on('change', function() {
    const t = $(this).val();
    $('#addBehavior_score').val(getScoreForType(t));
});

async function loadTable() {
    try {
        var classValue = <?=$class?>;
        var roomValue = <?=$room?>;

        const response = await $.ajax({
            url: '../controllers/BehaviorController.php?action=class_list',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, term: <?=$term?>, pee: <?=$pee?> }
        });

        if (!response || response.success !== true) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

        const table = $('#record_table').DataTable({
            destroy: true, // Destroy the previous instance of DataTable
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'asc']], // Sort by the first column (index 0)
            columnDefs: [
                { targets: 0, className: 'px-4 py-3 text-center font-medium text-gray-900' }, // Center align first column
                { targets: 1, className: 'px-4 py-3 text-center font-medium text-gray-700' }, // Center align second column
                { targets: 2, className: 'px-4 py-3 text-left font-medium text-gray-900' }, // Left align third column
                { targets: 3, className: 'px-4 py-3 text-center font-bold text-red-600' }, // Center align fourth column
                { targets: 4, className: 'px-4 py-3 text-center' }, // Center align fifth column
                { targets: 5, className: 'px-4 py-3 text-left font-medium text-blue-600' }, // Left align sixth column
                { targets: 6, className: 'px-4 py-3 text-center' } // Center align seventh column (details button)
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

        // Clear old data without destroying DataTable
        table.clear();

        if (!response.data || response.data.length === 0) {
            table.row.add([
                '<td colspan="7" class="text-center">ไม่พบข้อมูล</td>'
            ]);
        } else {
            response.data.forEach((item, index) => {
                const thaiDate = convertToThaiDate(item.behavior_date);
                const teacherNames = item.teacher_names || ' - ';
                const score = item.total_behavior_score || 0;
                const maxScore = 100;
                const progress = (score / maxScore) * 100;

                table.row.add([
                    (index + 1),
                    item.Stu_id,
                    item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur,
                    `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                        <span class="mr-1">⚠️</span>${item.total_behavior_score}
                    </span>`,
                    `<div class="w-full bg-gray-200 rounded-full h-6 relative overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-6 rounded-full transition-all duration-500 ease-out" style="width: ${progress}%">
                            <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white">
                                ${score}/${maxScore}
                            </span>
                        </div>
                    </div>`,
                    `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <span class="mr-1">👨‍🏫</span>${teacherNames}
                    </span>`,
                    `<button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-1 text-sm" onclick="showStudentDetails('${item.Stu_id}', '${item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur}')">
                        <span class="text-base">📋</span> ดูรายละเอียด
                    </button>`
                ]);
            });
        }

        // Re-draw table after data is updated
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

loadTable(); // Load data when page is loaded

// Function to show student behavior details
window.showStudentDetails = function(stuId, studentName) {
    // Show loading state
    $('#studentInfo').html('<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">กำลังโหลดข้อมูล...</p></div>');
    $('#behaviorDetails').html('');
    
    // Show modal
    $('#studentDetailsModal').modal('show');
    
    // Load student info
    $('#studentInfo').html(`
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                    👤
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">${studentName}</h3>
                <p class="text-sm text-gray-600">รหัสนักเรียน: ${stuId}</p>
            </div>
        </div>
    `);
    
    // Load behavior details
    $.ajax({
        url: '../controllers/BehaviorController.php?action=student_details',
        method: 'GET',
        data: { stu_id: stuId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                let html = '<div class="space-y-3">';
                let totalScore = 0;
                
                response.data.forEach((item, index) => {
                    totalScore += parseInt(item.behavior_score);
                    const thaiDate = convertToThaiDate(item.behavior_date);
                    const teacherName = item.Teach_name ? `${item.Teach_name} ${item.Teach_surname || ''}` : 'ไม่ระบุ';
                    
                    html += `
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-full mr-2">
                                        #${index + 1}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900">${item.behavior_type}</span>
                                </div>
                                <span class="bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                                    -${item.behavior_score} คะแนน
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <strong>วันที่:</strong> ${thaiDate}
                            </div>
                            ${item.behavior_name ? `<div class="text-sm text-gray-600 mb-2"><strong>รายละเอียด:</strong> ${item.behavior_name}</div>` : ''}
                            <div class="text-sm text-gray-600">
                                <strong>ผู้บันทึก:</strong> ${teacherName}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                    <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg border border-red-200 p-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-red-800">รวมคะแนนที่ถูกหัก:</span>
                            <span class="text-2xl font-bold text-red-600">${totalScore} คะแนน</span>
                        </div>
                    </div>
                `;
                
                html += '</div>';
                $('#behaviorDetails').html(html);
            } else {
                $('#behaviorDetails').html(`
                    <div class="text-center py-8">
                        <div class="text-4xl mb-4">✅</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">นักเรียนคนนี้ไม่มีประวัติการถูกหักคะแนน</h3>
                        <p class="text-gray-600">เยี่ยม! นักเรียนคนนี้มีพฤติกรรมที่ดี</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            $('#behaviorDetails').html(`
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">❌</div>
                    <h3 class="text-lg font-medium text-red-900 mb-2">เกิดข้อผิดพลาดในการโหลดข้อมูล</h3>
                    <p class="text-red-600">กรุณาลองใหม่อีกครั้ง</p>
                </div>
            `);
            console.error('Error loading student details:', error);
        }
    });
};

});


</script>
</body>
</html>
