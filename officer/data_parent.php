<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
// (ไม่ต้อง include Parent.php ตัวเก่า)
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (ไม่ต้องสร้าง $parent = new StudentParent($db);)
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

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">👨‍👩‍👧‍👦 จัดการข้อมูลผู้ปกครอง</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-info shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-csv"></i> อัปเดตข้อมูลด้วย CSV</h3>
                    </div>
                    <div class="card-body">
                        <form id="csvUploadForm" class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label" for="csv_file">เลือกไฟล์ CSV ที่แก้ไขแล้ว</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-upload"></i> อัปโหลด</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> ตัวกรอง</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filterClass">ชั้น</label>
                                <select id="filterClass" class="form-control">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filterRoom">ห้อง</label>
                                <select id="filterRoom" class="form-control">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button id="filterButton" class="btn btn-primary" style="margin-top: 32px;">ค้นหา</button>
                            </div>
                            <div class="col-md-3 text-md-left">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" id="downloadTemplateBtn" class="btn btn-secondary mt-4">
                                    <i class="fas fa-download"></i> โหลดข้อมูล (CSV)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body">
                        <table id="parentTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>รหัสนักเรียน</th>
                                    <th>ชื่อนักเรียน</th>
                                    <th>ชั้น/ห้อง</th>
                                    <th>ชื่อบิดา</th>
                                    <th>ชื่อมารดา</th>
                                    <th>ชื่อผู้ปกครอง</th>
                                    <th>เบอร์โทรผู้ปกครอง</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="editParentModal" tabindex="-1" role="dialog" aria-labelledby="editParentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editParentModalLabel">📝 แก้ไขข้อมูลผู้ปกครอง</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editParentForm">
                        <div class="modal-body">
                            <input type="hidden" id="editStu_id" name="editStu_id">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อบิดา</label>
                                    <input type="text" class="form-control" id="editFather_name" name="editFather_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editFather_occu" name="editFather_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editFather_income" name="editFather_income">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อมารดา</label>
                                    <input type="text" class="form-control" id="editMother_name" name="editMother_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editMother_occu" name="editMother_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editMother_income" name="editMother_income">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_name" name="editPar_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>ความเกี่ยวข้อง</label>
                                    <input type="text" class="form-control" id="editPar_relate" name="editPar_relate">
                                </div>
                                 <div class="col-md-4 form-group">
                                    <label>เบอร์โทร</label>
                                    <input type="text" class="form-control" id="editPar_phone" name="editPar_phone">
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editPar_occu" name="editPar_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editPar_income" name="editPar_income">
                                </div>
                                 <div class="col-md-4 form-group">
                                    <label>ที่อยู่</label>
                                    <input type="text" class="form-control" id="editPar_addr" name="editPar_addr">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="button" id="submitEditParentForm" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
        // (API_TOKEN_KEY ไม่ต้องใช้แล้ว)
        // const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE'; 
        
        // (URL ใหม่ ชี้ไปที่ Controller)
        const API_URL = '../controllers/ParentController.php';

        let parentTable;

        document.addEventListener('DOMContentLoaded', function() {
            parentTable = $('#parentTable').DataTable({
                "processing": true,
                "serverSide": false, // (ใช้ Client-side เหมือนเดิม)
                "ajax": {
                    "url": API_URL + "?action=list", // (เรียก list)
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "Stu_id" },
                    { "data": null, "render": function(data, type, row) {
                        return (row.Stu_name || '') + ' ' + (row.Stu_sur || '');
                    }},
                    { "data": null, "render": function(data, type, row) {
                        return 'ม.' + (row.Stu_major || '') + '/' + (row.Stu_room || '');
                    }},
                    { "data": "Father_name" },
                    { "data": "Mother_name" },
                    { "data": "Par_name" },
                    { "data": "Par_phone" },
                    { 
                        "data": "Stu_id",
                        "render": function(data) {
                            return `<button class="btn btn-warning btn-sm editParentBtn" data-id="${data}"><i class="fas fa-edit"></i> แก้ไข</button>`;
                        },
                        "orderable": false
                    }
                ],
                "language": {
                    // (ภาษาไทย)
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "processing": "กำลังโหลดข้อมูล... ⏳",
                    "search": "ค้นหา:",
                    "paginate": { "next": "ถัดไป", "previous": "ก่อนหน้า" }
                }
            });

            // (ฟังก์ชันโหลดข้อมูลใหม่)
            window.loadParents = function() {
                const classVal = document.getElementById('filterClass').value;
                const roomVal = document.getElementById('filterRoom').value;
                
                // (สร้าง URL ใหม่สำหรับโหลดข้อมูล)
                const fetchUrl = `${API_URL}?action=list&class=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`;
                
                parentTable.ajax.url(fetchUrl).load();
            }

            // (Event: กดปุ่มค้นหา)
            document.getElementById('filterButton').addEventListener('click', loadParents);

            // (ฟังก์ชันโหลดตัวกรอง - เรียกจาก Controller ของ Student)
            async function populateFilterSelects() {
                // (ใช้ Controller ของ Student เพื่อดึง ชั้น/ห้อง)
                const res = await fetch('../controllers/StudentController.php?action=get_filters');
                const data = await res.json();
                
                // (แก้ data.classes เป็น data.majors)
                const classSel = document.getElementById('filterClass');
                data.majors.forEach(cls => {
                    if (cls) classSel.innerHTML += `<option value="${cls}">${cls}</option>`;
                });

                const roomSel = document.getElementById('filterRoom');
                data.rooms.forEach(room => {
                    if (room) roomSel.innerHTML += `<option value="${room}">${room}</option>`;
                });
            }
            populateFilterSelects();


            // (Event: Show Edit Modal)
            $('#parentTable').on('click', '.editParentBtn', async function() {
                const id = $(this).data('id');
                // (เรียก Controller ใหม่)
                const res = await fetch(API_URL + "?action=get&id=" + id);
                const p = await res.json();
                
                if (p && p.Stu_id) {
                    document.getElementById('editStu_id').value = p.Stu_id;
                    document.getElementById('editFather_name').value = p.Father_name || '';
                    document.getElementById('editFather_occu').value = p.Father_occu || '';
                    document.getElementById('editFather_income').value = p.Father_income || '';
                    document.getElementById('editMother_name').value = p.Mother_name || '';
                    document.getElementById('editMother_occu').value = p.Mother_occu || '';
                    document.getElementById('editMother_income').value = p.Mother_income || '';
                    document.getElementById('editPar_name').value = p.Par_name || '';
                    document.getElementById('editPar_relate').value = p.Par_relate || '';
                    document.getElementById('editPar_occu').value = p.Par_occu || '';
                    document.getElementById('editPar_income').value = p.Par_income || '';
                    document.getElementById('editPar_addr').value = p.Par_addr || '';
                    document.getElementById('editPar_phone').value = p.Par_phone || '';
                    $('#editParentModalLabel').text('แก้ไขข้อมูลผู้ปกครองของ: ' + p.Stu_name);
                    $('#editParentModal').modal('show');
                }
            });

            // (Event: Submit Edit Modal)
            $('#submitEditParentForm').on('click', async function() {
                const form = document.getElementById('editParentForm');
                const formData = new FormData(form);
                // (formData.append('token', API_TOKEN_KEY); ไม่ต้องใช้)
                
                // (เรียก Controller ใหม่ และตัด token ออก)
                const res = await fetch(API_URL + '?action=update', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editParentModal').modal('hide');
                    loadParents(); // โหลดข้อมูลใหม่
                    Swal.fire('✅ สำเร็จ', 'บันทึกข้อมูลสำเร็จ', 'success');
                } else {
                    Swal.fire('❌ ล้มเหลว', result.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            });

            // (Event: Submit CSV Upload)
            $('#csvUploadForm').on('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!formData.get('csv_file').name) {
                    Swal.fire('❌ ล้มเหลว', 'กรุณาเลือกไฟล์ CSV', 'error');
                    return;
                }
                
                Swal.fire({
                    title: 'กำลังอัปโหลด...',
                    text: 'กรุณารอสักครู่ กำลังประมวลผลไฟล์ CSV',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const res = await fetch(API_URL + '?action=upload_csv', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.status === 'completed') {
                    Swal.fire(
                        '✅ อัปโหลดสำเร็จ',
                        `บันทึกข้อมูลสำเร็จ: ${result.report.success} รายการ\nล้มเหลว: ${result.report.failed} รายการ`,
                        'success'
                    );
                    loadParents(); // โหลดข้อมูลใหม่
                } else {
                    Swal.fire('❌ ล้มเหลว', result.message || 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
                }
            });

            //
            // !! KEV: เพิ่มส่วนนี้ !!
            // (Event: Click Download Template Button)
            //
            $('#downloadTemplateBtn').on('click', function() {
                // (1) ดึงค่าจากตัวกรอง
                const classVal = $('#filterClass').val();
                const roomVal = $('#filterRoom').val();
                
                // (2) สร้าง URL พร้อมตัวกรอง
                const url = `${API_URL}?action=download_template&class=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`;
                
                // (3) สั่งให้เบราว์เซอร์ดาวน์โหลดไฟล์
                window.location.href = url;
            });

        });
        </script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>