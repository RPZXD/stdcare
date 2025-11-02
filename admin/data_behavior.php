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


if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
} else {
    if (isset($_SESSION['Teacher_login'])) {
         $userid = $_SESSION['Teacher_login'];
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
                        <h5 class="m-0">จัดการข้อมูลพฤติกรรม (เทอม <?php echo "$term/$pee"; ?>)</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body">
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBehaviorModal"><i class="fas fa-plus"></i> เพิ่มข้อมูลพฤติกรรม</button>
                        <table id="behaviorTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>วันที่</th>
                                    <th>รหัสนักเรียน</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th>ชั้น/ห้อง</th>
                                    <th>ประเภท</th>
                                    <th>รายการ</th>
                                    <th>คะแนน</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="addBehaviorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">เพิ่มข้อมูลพฤติกรรม</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addBehaviorForm">
                        <div class="modal-body">
                            <div id="addStudentPreview" class="text-center mb-3" style="min-height: 100px;"></div>
                            
                            <div class="form-group">
                                <label>รหัสนักเรียน</label>
                                <input type="text" class="form-control" name="addStu_id" id="addStu_id" required>
                            </div>
                            <div class="form-group">
                                <label>วันที่</label>
                                <input type="date" class="form-control" name="addBehavior_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>ประเภท</label>
                                <select class="form-control behavior-type-select" name="addBehavior_type" data-target="addBehavior_name">
                                <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="หนีเรียนหรือออกนอกสถานศึกษา">หนีเรียนหรือออกนอกสถานศึกษา</option>
                                    <option value="เล่นการพนัน">เล่นการพนัน</option>
                                    <option value="มาโรงเรียนสาย">มาโรงเรียนสาย</option>
                                    <option value="แต่งกาย/ทรงผมผิดระเบียบ">แต่งกาย/ทรงผมผิดระเบียบ</option>
                                    <option value="พกพาอาวุธหรือวัตถุระเบิด">พกพาอาวุธหรือวัตถุระเบิด</option>
                                    <option value="เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์">เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์</option>
                                    <option value="สูบบุหรี่">สูบบุหรี่</option>
                                    <option value="เสพยาเสพติด">เสพยาเสพติด</option>
                                    <option value="ลักทรัพย์ กรรโชกทรัพย์">ลักทรัพย์ กรรโชกทรัพย์</option>
                                    <option value="ก่อเหตุทะเลาะวิวาท">ก่อเหตุทะเลาะวิวาท</option>
                                    <option value="แสดงพฤติกรรมทางชู้สาว">แสดงพฤติกรรมทางชู้สาว</option>
                                    <option value="จอดรถในที่ห้ามจอด">จอดรถในที่ห้ามจอด</option>
                                    <option value="แสดงพฤติกรรมก้าวร้าว">แสดงพฤติกรรมก้าวร้าว</option>
                                    <option value="มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ">มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>รายการพฤติกรรม</label>
                                <input type="text" class="form-control" name="addBehavior_name" id="addBehavior_name" required>
                            </div>
                            <div class="form-group">
                                <label>คะแนน (เช่น 10)</label>
                                <input type="number" class="form-control" name="addBehavior_score" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editBehaviorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขข้อมูลพฤติกรรม</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editBehaviorForm">
                        <input type="hidden" name="editId" id="editId">
                        <div class="modal-body">
                            <div id="editStudentPreview" class="text-center mb-3" style="min-height: 100px;"></div>
                            
                            <div class="form-group">
                                <label>รหัสนักเรียน</label>
                                <input type="text" class="form-control" name="editStu_id" id="editStu_id" required>
                            </div>
                            <div class="form-group">
                                <label>วันที่</label>
                                <input type="date" class="form-control" name="editBehavior_date" id="editBehavior_date" required>
                            </div>
                            <div class="form-group">
                                <label>ประเภท</label>
                                <select class="form-control behavior-type-select" name="editBehavior_type" id="editBehavior_type" data-target="editBehavior_name">
                                <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="หนีเรียนหรือออกนอกสถานศึกษา">หนีเรียนหรือออกนอกสถานศึกษา</option>
                                    <option value="เล่นการพนัน">เล่นการพนัน</option>
                                    <option value="มาโรงเรียนสาย">มาโรงเรียนสาย</option>
                                    <option value="แต่งกาย/ทรงผมผิดระเบียบ">แต่งกาย/ทรงผมผิดระเบียบ</option>
                                    <option value="พกพาอาวุธหรือวัตถุระเบิด">พกพาอาวุธหรือวัตถุระเบิด</option>
                                    <option value="เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์">เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์</option>
                                    <option value="สูบบุหรี่">สูบบุหรี่</option>
                                    <option value="เสพยาเสพติด">เสพยาเสพติด</option>
                                    <option value="ลักทรัพย์ กรรโชกทรัพย์">ลักทรัพย์ กรรโชกทรัพย์</option>
                                    <option value="ก่อเหตุทะเลาะวิวาท">ก่อเหตุทะเลาะวิวาท</option>
                                    <option value="แสดงพฤติกรรมทางชู้สาว">แสดงพฤติกรรมทางชู้สาว</option>
                                    <option value="จอดรถในที่ห้ามจอด">จอดรถในที่ห้ามจอด</option>
                                    <option value="แสดงพฤติกรรมก้าวร้าว">แสดงพฤติกรรมก้าวร้าว</option>
                                    <option value="มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ">มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>รายการพฤติกรรม</label>
                                <input type="text" class="form-control" name="editBehavior_name" id="editBehavior_name" required>
                            </div>
                            <div class="form-group">
                                <label>คะแนน (เช่น 10)</label>
                                <input type="number" class="form-control" name="editBehavior_score" id="editBehavior_score" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
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
                      "className": "text-center" ,
                      "width": "10%"
                    },
                    { "data": "stu_id" },
                    { "data": null, "render": (data, type, row) => `${row.Stu_name || ''} ${row.Stu_sur || ''}` },
                    { "data": null, "render": (data, type, row) => `ม.${row.Stu_major || ''}/${row.Stu_room || ''}` },
                    { "data": "behavior_type", "render": (data) => 
                        data === 'ความดี' ? `<span class="badge badge-success">${data}</span>` : `<span class="badge badge-danger">${data}</span>`
                    },
                    { "data": "behavior_name" },
                    { 
                        "data": "behavior_score" ,
                        "className": "text-center" ,
                        "width": "5%"
                    },
                    { 
                        "data": "id",
                        "render": function(data) {
                            return `
                                <button class="btn btn-warning btn-sm editBehaviorBtn" data-id="${data}"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm deleteBehaviorBtn" data-id="${data}"><i class="fas fa-trash"></i></button>
                            `;
                        },
                        "orderable": false ,
                        "className": "text-center" ,
                        "width": "10%"
                    }
                ],
                "language": { "zeroRecords": "ไม่พบข้อมูล", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", "processing": "กำลังโหลด..." }
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
                    Swal.fire('สำเร็จ', 'เพิ่มข้อมูลพฤติกรรมเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถเพิ่มข้อมูลได้', 'error');
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
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                }
            });

            // --- (Event: Delete Behavior - ไม่ต้องแก้) ---
            $('#behaviorTable').on('click', '.deleteBehaviorBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'ยืนยันการลบข้อมูลนี้?',
                    text: "การดำเนินการนี้ไม่สามารถย้อนกลับได้",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย',
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
                    Swal.fire('สำเร็จ', 'ลบข้อมูลสำเร็จ', 'success');
                } else {
                    Swal.fire('ล้มเหลว', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
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
.student-preview-card {
    background: #f7fafd !important;
    border: 0;
    box-shadow: 0 2px 16px 0 #d1e7fa52;
    text-align: center;
    transition: box-shadow 0.2s;
}
.student-preview-card:hover {
    box-shadow: 0 4px 24px 0 #b0c6e852;
    background: #f0f6ff;
}
#addStudentPreview, #editStudentPreview {
    min-height: 118px;
}
.student-img-zoom-wrap {
    display:inline-block; position:relative;
}
.student-img-zoom-wrap .zoom-icon {
    position:absolute; bottom:8px; right:10px; color:#3799e5; font-size:1.1em; opacity:0; pointer-events:none;
    transition: opacity 0.18s;
    text-shadow: 0 1px 5px #fff, 0 1px 12px #2684bc50;
}
.student-img-zoom-wrap:hover .zoom-icon {
    opacity:1;
}
.student-img-zoom {
    transition: filter .18s, box-shadow .18s;
}
.student-img-zoom-wrap:hover .student-img-zoom {
    filter: brightness(1.08) drop-shadow(0 3px 9px #60b3e68a);
    box-shadow: 0 3px 24px #90ccfd55, 0 1.5px 6px #3799e550;
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