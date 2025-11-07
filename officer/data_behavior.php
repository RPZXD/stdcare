<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");
include_once("../config/Setting.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok'); // (เพิ่ม)

$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)


if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
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

$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);
$setting = new Setting();

require_once('header.php');

// (เพิ่ม) รายการพฤติกรรม
$behavior_options = [
    "ความดี" => [
        "จิตอาสา", "ช่วยเหลือครู", "เก็บของได้ส่งคืน", "ช่วยเหลือเพื่อน", "อื่นๆ (ความดี)"
    ],
    "ความผิด" => [
        "หนีเรียนหรือออกนอกสถานศึกษา", "เล่นการพนัน", "มาโรงเรียนสาย", 
        "แต่งกาย/ทรงผมผิดระเบียบ", "พกพาอาวุธหรือวัตถุระเบิด", 
        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์", "สูบบุหรี่", "เสพยาเสพติด", 
        "ลักทรัพย์ กรรโชกทรัพย์", "ก่อเหตุทะเลาะวิวาท", "แสดงพฤติกรรมทางชู้สาว", 
        "จอดรถในที่ห้ามจอด", "แสดงพฤติกรรมก้าวร้าว", "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
    ]
];

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0 text-2xl font-bold text-gray-800 flex items-center">
                            <span class="text-3xl mr-3">📊</span>
                            จัดการข้อมูลพฤติกรรม (เทอม <?php echo "$term/$pee"; ?>)
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-3xl shadow-xl border border-indigo-100 p-8">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                        <button class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-sm flex items-center" data-toggle="modal" data-target="#addBehaviorModal">
                            <span class="text-lg mr-2">➕</span>
                            เพิ่มข้อมูลพฤติกรรม
                        </button>
                        
                        <div class="flex items-center bg-white rounded-2xl shadow-md px-4 py-2 border border-gray-200">
                            <span class="text-xl mr-3">🔍</span>
                            <input type="text" id="behaviorSearch" placeholder="ค้นหานักเรียน ชื่อ รหัส หรือพฤติกรรม..." class="bg-transparent border-0 outline-none text-gray-700 placeholder-gray-400 w-64 focus:ring-0">
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <table id="behaviorTable" class="w-full text-sm text-left">
                            <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center">📅 วันที่</th>
                                    <th class="px-6 py-4 font-bold">🆔 รหัสนักเรียน</th>
                                    <th class="px-6 py-4 font-bold">👤 ชื่อ-สกุล</th>
                                    <th class="px-6 py-4 font-bold">🏫 ชั้น/ห้อง</th>
                                    <th class="px-6 py-4 font-bold text-center">🏷️ ประเภท</th>
                                    <th class="px-6 py-4 font-bold">📋 รายการ</th>
                                    <th class="px-6 py-4 font-bold text-center">⭐ คะแนน</th>
                                    <th class="px-6 py-4 font-bold text-center">⚙️ จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="addBehaviorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-blue-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center">
                            <span class="text-2xl mr-3">➕</span>
                            เพิ่มข้อมูลพฤติกรรม
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addBehaviorForm">
                        <div class="modal-body p-8">
                            <div id="addStudentPreview" class="text-center mb-6" style="min-height: 100px;"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🆔</span>
                                        รหัสนักเรียน
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="addStu_id" id="addStu_id" required>
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📅</span>
                                        วันที่
                                    </label>
                                    <input type="date" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="addBehavior_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">🏷️</span>
                                    ประเภทพฤติกรรม
                                </label>
                                <select class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white behavior-type-select" name="addBehavior_type" data-target="addBehavior_name">
                                    <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="ความดี">🌟 ความดี</option>
                                    <option value="ความผิด">⚠️ ความผิด</option>
                                </select>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">📋</span>
                                    รายการพฤติกรรม
                                </label>
                                <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="addBehavior_name" id="addBehavior_name" required>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">⭐</span>
                                    คะแนน (เช่น 10)
                                </label>
                                <input type="number" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="addBehavior_score" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-end space-x-3">
                            <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">❌ ปิด</button>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">💾 บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editBehaviorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content bg-gradient-to-br from-white to-blue-50 rounded-3xl shadow-2xl border-0">
                    <div class="modal-header bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-t-3xl border-0">
                        <h5 class="modal-title text-xl font-bold flex items-center">
                            <span class="text-2xl mr-3">✏️</span>
                            แก้ไขข้อมูลพฤติกรรม
                        </h5>
                        <button type="button" class="close text-white text-2xl hover:text-gray-200 transition-colors" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editBehaviorForm">
                        <input type="hidden" name="editId" id="editId">
                        <div class="modal-body p-8">
                            <div id="editStudentPreview" class="text-center mb-6" style="min-height: 100px;"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">🆔</span>
                                        รหัสนักเรียน
                                    </label>
                                    <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="editStu_id" id="editStu_id" required>
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <span class="text-lg mr-2">📅</span>
                                        วันที่
                                    </label>
                                    <input type="date" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="editBehavior_date" id="editBehavior_date" required>
                                </div>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">🏷️</span>
                                    ประเภทพฤติกรรม
                                </label>
                                <select class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white behavior-type-select" name="editBehavior_type" id="editBehavior_type" data-target="editBehavior_name">
                                    <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="ความดี">🌟 ความดี</option>
                                    <option value="ความผิด">⚠️ ความผิด</option>
                                </select>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">📋</span>
                                    รายการพฤติกรรม
                                </label>
                                <input type="text" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="editBehavior_name" id="editBehavior_name" required>
                            </div>
                            
                            <div class="form-group mt-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">⭐</span>
                                    คะแนน (เช่น 10)
                                </label>
                                <input type="number" class="form-control w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white" name="editBehavior_score" id="editBehavior_score" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-gray-50 rounded-b-3xl border-0 p-6 flex justify-end space-x-3">
                            <button type="button" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-200 font-semibold" data-dismiss="modal">❌ ปิด</button>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">💾 บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        const API_URL = '../controllers/BehaviorController.php'; // (URL ใหม่)
        let behaviorTable;
        
        // (เพิ่ม) เก็บรายการพฤติกรรมใน JS
        const behaviorOptions = <?php echo json_encode($behavior_options); ?>;

        // (เพิ่ม) ฟังก์ชันอัปเดต Dropdown รายการพฤติกรรม
        function updateBehaviorNameSelect(selectElement, behaviorType) {
            const options = behaviorOptions[behaviorType] || [];
            selectElement.innerHTML = ''; // เคลียร์ตัวเลือกเก่า
            
            if (options.length === 0) {
                 selectElement.innerHTML = '<option value="">กรุณาระบุ</option>'; // (กรณีไม่มีในลิสต์)
            }
            
            options.forEach(option => {
                selectElement.innerHTML += `<option value="${option}">${option}</option>`;
            });
        }
        
        // (เพิ่ม) ฟังก์ชันค้นหานักเรียน
        // -- เพิ่ม debounce --
        function debounce(func, delay) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        async function searchStudent(stuId, previewElementId) {
            const previewEl = document.getElementById(previewElementId);
            if (!stuId) {
                previewEl.innerHTML = '';
                return;
            }
            try {
                const res = await fetch(`${API_URL}?action=search_student&id=${encodeURIComponent(stuId)}`);
                const data = await res.json();
                if (data && data.Stu_id) {
                    let imgPath = data.Stu_picture ? `https://std.phichai.ac.th/photo/${data.Stu_picture}` : 'https://std.phichai.ac.th/dist/img/logo-phicha.png';
                    previewEl.innerHTML = `
                        <div class="card shadow student-preview-card mx-auto p-3" style="max-width:350px;border-radius:16px;">
                            <div class="student-img-zoom-wrap position-relative mx-auto mb-2">
                                <img src="${imgPath}" alt="รูปนักเรียน" class="img-thumbnail rounded-circle shadow student-img-zoom" width="110" height="110" style="object-fit: cover; border:4px solid #fff; background:#fafbfc; cursor: zoom-in;" onerror="this.src='../student/uploads/default.png';" />
                                <span class="zoom-icon"><i class='fas fa-search-plus'></i></span>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="font-weight-bold text-primary mb-1 mt-2" style="font-size:1.1em;">${data.Stu_name} ${data.Stu_sur}</h6>
                                <span class="badge badge-info mb-1">รหัส: ${data.Stu_id}</span>
                                <div class="small text-muted mt-2">ม.${data.Stu_major}/${data.Stu_room}</div>
                            </div>
                        </div>
                    `;
                } else {
                    previewEl.innerHTML = '<div class="alert alert-danger">ไม่พบข้อมูลนักเรียน</div>';
                }
            } catch (err) {
                previewEl.innerHTML = '<div class="alert alert-warning">กำลังค้นหา...</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            behaviorTable = $('#behaviorTable').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "url": API_URL + "?action=list", // (เรียก Controller)
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "behavior_date",  
                      "orderable": false ,
                      "className": "text-center px-6 py-4 text-gray-700 font-medium" ,
                      "width": "10%"
                    },
                    { "data": "stu_id", "className": "px-6 py-4 text-gray-800 font-semibold" },
                    { "data": null, "render": (data, type, row) => `<span class="text-gray-800 font-medium">${row.Stu_name || ''} ${row.Stu_sur || ''}</span>`, "className": "px-6 py-4" },
                    { "data": null, "render": (data, type, row) => `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">ม.${row.Stu_major || ''}/${row.Stu_room || ''}</span>`, "className": "px-6 py-4" },
                    { "data": "behavior_type", "render": (data) => 
                        data === 'ความดี' ? 
                        `<span class="bg-gradient-to-r from-green-400 to-emerald-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-md transform hover:scale-105 transition-all duration-200">🌟 ${data}</span>` : 
                        `<span class="bg-gradient-to-r from-red-400 to-pink-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-md transform hover:scale-105 transition-all duration-200">⚠️ ${data}</span>`
                    , "className": "text-center px-6 py-4" },
                    { "data": "behavior_name", "className": "px-6 py-4 text-gray-700" },
                    { 
                        "data": "behavior_score" ,
                        "className": "text-center px-6 py-4" ,
                        "width": "5%",
                        "render": (data, type, row) => {
                            const isGood = row.behavior_type === 'ความดี';
                            return `<span class="text-2xl font-bold ${isGood ? 'text-green-600' : 'text-red-600'}">${isGood ? '⭐' : '💢'} ${data}</span>`;
                        }
                    },
                    { 
                        "data": "id",
                        "render": function(data) {
                            return `
                                <div class="flex justify-center space-x-2">
                                    <button class="editBehaviorBtn bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white px-4 py-2 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 text-sm font-semibold" data-id="${data}">
                                        ✏️ แก้ไข
                                    </button>
                                    <button class="deleteBehaviorBtn bg-gradient-to-r from-red-400 to-pink-500 hover:from-red-500 hover:to-pink-600 text-white px-4 py-2 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 text-sm font-semibold" data-id="${data}">
                                        🗑️ ลบ
                                    </button>
                                </div>
                            `;
                        },
                        "orderable": false ,
                        "className": "px-6 py-4" ,
                        "width": "15%"
                    }
                ],
                "language": { 
                    "zeroRecords": "😔 ไม่พบข้อมูลพฤติกรรม", 
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
                "initComplete": function() {
                    // Hide default search box since we have custom one
                    $('.dataTables_filter').hide();
                },
                "drawCallback": function() {
                    // Add hover effects to rows
                    $('#behaviorTable tbody tr').hover(
                        function() { $(this).addClass('bg-indigo-50 transform scale-[1.01] transition-all duration-200'); },
                        function() { $(this).removeClass('bg-indigo-50 transform scale-[1.01] transition-all duration-200'); }
                    );
                }
            });

            // Custom search functionality
            $('#behaviorSearch').on('input', function() {
                behaviorTable.search(this.value).draw();
            });

            // (ฟังก์ชันโหลดข้อมูลใหม่)
            window.loadBehaviors = function() {
                behaviorTable.ajax.reload(null, false);
            }

            // --- (เพิ่ม) Event Listeners สำหรับฟีเจอร์ใหม่ ---
            
            // (1. ค้นหานักเรียน ตอนกรอก ID) -- ใช้ input + debounce
            $('#addStu_id').on('input', debounce(function() { searchStudent(this.value, 'addStudentPreview'); }, 350));
            $('#editStu_id').on('input', debounce(function() { searchStudent(this.value, 'editStudentPreview'); }, 350));
            
            // (2. เปลี่ยน Dropdown รายการพฤติกรรม)
            $('.behavior-type-select').on('change', function() {
                const targetId = $(this).data('target');
                const targetSelect = document.getElementById(targetId);
                updateBehaviorNameSelect(targetSelect, this.value);
            });
            // (ตั้งค่าเริ่มต้นให้ "Add Modal" ที่เลือก "ความผิด" ไว้)
            updateBehaviorNameSelect(document.getElementById('addBehavior_name'), 'ความผิด');

            // --- (Event: Add Behavior - แก้ไข) ---
            document.getElementById('addBehaviorForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // (ลบ token)
                
                const res = await fetch(API_URL + "?action=create", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#addBehaviorModal').modal('hide');
                    this.reset();
                    $('#addStudentPreview').html(''); // (ล้าง Preview)
                    loadBehaviors();
                    Swal.fire('สำเร็จ 🎉', 'เพิ่มข้อมูลพฤติกรรมเรียบร้อยแล้ว!', 'success');
                } else {
                    Swal.fire('ล้มเหลว 😞', result.message || 'ไม่สามารถเพิ่มข้อมูลได้', 'error');
                }
            });

            // --- (Event: Show Edit Modal - แก้ไข) ---
            $('#behaviorTable').on('click', '.editBehaviorBtn', async function() {
                const id = $(this).data('id');
                const res = await fetch(API_URL + "?action=get&id=" + id);
                const data = await res.json();
                
                if (data && data.id) {
                    $('#editId').val(data.id);
                    $('#editStu_id').val(data.stu_id);
                    $('#editBehavior_date').val(data.behavior_date);
                    $('#editBehavior_type').val(data.behavior_type);
                    
                    // (อัปเดต Dropdown รายการพฤติกรรมก่อน)
                    const nameSelect = document.getElementById('editBehavior_name');
                    updateBehaviorNameSelect(nameSelect, data.behavior_type);
                    // (แล้วค่อยเลือกค่าที่ถูก)
                    $(nameSelect).val(data.behavior_name); 
                    
                    $('#editBehavior_score').val(data.behavior_score);
                    
                    // (ค้นหาข้อมูลนักเรียนมาแสดง)
                    searchStudent(data.stu_id, 'editStudentPreview'); 
                    
                    $('#editBehaviorModal').modal('show');
                }
            });

            // --- (Event: Edit Behavior - ไม่ต้องแก้) ---
            document.getElementById('editBehaviorForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const res = await fetch(API_URL + "?action=update", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editBehaviorModal').modal('hide');
                    loadBehaviors();
                    Swal.fire('สำเร็จ ✨', 'แก้ไขข้อมูลเรียบร้อยแล้ว!', 'success');
                } else {
                    Swal.fire('ล้มเหลว 😞', result.message || 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                }
            });

            // --- (Event: Delete Behavior - ไม่ต้องแก้) ---
            $('#behaviorTable').on('click', '.deleteBehaviorBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'ยืนยันการลบข้อมูล? 🗑️',
                    text: "การดำเนินการนี้ไม่สามารถย้อนกลับได้นะ!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                });
                if (!result.isConfirmed) return;
                
                const res = await fetch(API_URL + "?action=delete", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                });
                const response = await res.json();
                if (response.success) {
                    loadBehaviors();
                    Swal.fire('สำเร็จ ✅', 'ลบข้อมูลเรียบร้อยแล้ว!', 'success');
                } else {
                    Swal.fire('ล้มเหลว 😞', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                }
            });
        });
        </script>

<!-- Modal for zoomed image (แทรกก่อน </body>) -->
<div class="modal fade" id="studentImgZoomModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <img id="zoomedStudentImg" src="#" style="max-width:95vw; max-height:70vh; box-shadow:0 4px 48px #3338; background:#fff; border-radius:16px;" />
      </div>
    </div>
  </div>
</div>

<style>
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

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
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

.behavior-table-container {
    animation: fadeInUp 0.6s ease-out;
}

.behavior-good {
    background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
    border-left: 4px solid #48bb78;
}

.behavior-bad {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border-left: 4px solid #f56565;
}

.table-hover tbody tr:hover {
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 50%, #90cdf4 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.btn-gradient {
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.modal-content {
    animation: bounceIn 0.5s ease-out;
}

.form-control:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-radius: 8px !important;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border-radius: 12px !important;
    border: 2px solid #e2e8f0 !important;
    padding: 8px 12px !important;
}

.dataTables_wrapper .dataTables_info {
    color: #4a5568 !important;
    font-weight: 600 !important;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}

/* Loading animation */
.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
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

/* Responsive design improvements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 0.875rem;
    }
}

/* Enhanced student preview card */
.student-preview-card {
    background: linear-gradient(135deg, #f7fafd 0%, #edf2f7 100%) !important;
    border: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    text-align: center;
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease-out;
}

.student-preview-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.student-img-zoom-wrap {
    display: inline-block;
    position: relative;
}

.student-img-zoom-wrap .zoom-icon {
    position: absolute;
    bottom: 8px;
    right: 10px;
    color: #4299e1;
    font-size: 1.1em;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.18s;
    text-shadow: 0 1px 5px #fff, 0 1px 12px #2684bc50;
}

.student-img-zoom-wrap:hover .zoom-icon {
    opacity: 1;
}

.student-img-zoom {
    transition: filter 0.18s, box-shadow 0.18s, transform 0.18s;
}

.student-img-zoom-wrap:hover .student-img-zoom {
    filter: brightness(1.08) drop-shadow(0 3px 9px #60b3e68a);
    box-shadow: 0 3px 24px #90ccfd55, 0 1.5px 6px #3799e550;
    transform: scale(1.05);
}
</style>
<script>
// ฟีเจอร์ซูมรูป (สำหรับทั้ง add/edit modal)
$(document).on('click', '.student-img-zoom', function(){
    var imgSrc = $(this).attr('src');
    $('#zoomedStudentImg').attr('src', imgSrc);
    $('#studentImgZoomModal').modal('show');
});
$('#studentImgZoomModal').on('hidden.bs.modal', function(){
    $('#zoomedStudentImg').attr('src', '#'); // Clean up
});
</script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>