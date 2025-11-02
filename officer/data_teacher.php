<?php

require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php"); // (ยังใช้ UserLogin ตัวเก่า)
include_once("../class/Utils.php");
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
                        <h5 class="m-0">ข้อมูลครู</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <table id="teacherTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ชื่อ-สกุล</th>
                                    <th>กลุ่มสาระ</th>
                                    <th>ครูที่ปรึกษา</th>
                                    <th>สถานะ</th>
                                    <th>บทบาท</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>


<script>
        let teacherTable;
        // (URL ใหม่ ชี้ไปที่ Controller)
        const API_URL = '../controllers/TeacherController.php'; 

        document.addEventListener('DOMContentLoaded', function() {
            teacherTable = $('#teacherTable').DataTable({
                "processing": true,
                "serverSide": false, // (เราจะใช้ Client-side สำหรับ list (ตามโค้ดเดิม))
                "ajax": {
                    "url": API_URL + "?action=list", // (เรียก list)
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "Teach_name" },
                    { "data": "Teach_major" },
                    { 
                        "data": null,
                        "render": function(data, type, row) {
                            return row.Teach_class && row.Teach_room ? `ม.${row.Teach_class}/${row.Teach_room}` : '-';
                        }
                    },
                    { 
                        "data": "Teach_status",
                        "render": function(data) {
                            return data == '1' ? '<span class="badge badge-success">ปกติ</span>' : '<span class="badge badge-danger">ไม่ใช้งาน</span>';
                        }
                    },
                    { "data": "role_std",
                        "render": function(data) {
                            let roleText = '';
                            switch(data) {
                                case 'T': roleText = 'ครู'; break;
                                case 'OF': roleText = 'เจ้าหน้าที่'; break;
                                case 'VP': roleText = 'รองผู้อำนวยการ'; break;
                                case 'DIR': roleText = 'ผู้อำนวยการ'; break;
                                case 'ADM': roleText = 'Admin'; break;
                                default: roleText = data; 
                            }
                            return roleText;
                        }
                    }
                ],
                "language": {
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    // ... (ภาษาไทยอื่นๆ) ...
                }
            });

            // (ฟังก์ชันโหลดข้อมูลใหม่)
            window.loadTeachers = function() {
                teacherTable.ajax.reload(null, false); // โหลดใหม่แบบไม่รีเซ็ตหน้า
            }

            // (Event: Add Teacher)
            document.getElementById('addTeacherForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const res = await fetch(API_URL + "?action=create", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#addTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'เพิ่มข้อมูลครูเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถเพิ่มข้อมูลได้', 'error');
                }
            });

            // (Event: Show Edit Modal)
            $('#teacherTable').on('click', '.editTeacherBtn', async function() {
                const id = $(this).data('id');
                const res = await fetch(API_URL + "?action=get&id=" + id);
                const data = await res.json();
                
                if (data && data.Teach_id) {
                    $('[name="editTeach_id_old"]').val(data.Teach_id);
                    $('[name="editTeach_id"]').val(data.Teach_id);
                    $('[name="editTeach_name"]').val(data.Teach_name);
                    $('[name="editTeach_major"]').val(data.Teach_major);
                    $('[name="editTeach_status"]').val(data.Teach_status);
                    $('[name="editrole_std"]').val(data.role_std);
                    $('#editTeacherModal').modal('show');
                }
            });

            // (Event: Edit Teacher)
            document.getElementById('editTeacherForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // (Controller ใหม่รับ Teach_id ใน body ไม่ใช่ใน URL)
                const res = await fetch(API_URL + "?action=update", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                }
            });

            // (Event: Delete Teacher)
            $('#teacherTable').on('click', '.deleteTeacherBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'ยืนยันการลบข้อมูลครูนี้?',
                    text: "ข้อมูลจะถูกตั้งค่าเป็น 'ไม่ใช้งาน'",
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
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'ลบข้อมูลสำเร็จ', 'success');
                } else {
                    Swal.fire('ล้มเหลว', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                }
            });

            // (Event: Reset Password)
             $('#teacherTable').on('click', '.resetTeacherPwdBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'รีเซ็ตรหัสผ่าน?',
                    text: `รหัสผ่านของ ${id} จะถูกตั้งค่าเป็น ${id}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, รีเซ็ต',
                    cancelButtonText: 'ยกเลิก'
                });
                if (!result.isConfirmed) return;
                
                const res = await fetch(API_URL + "?action=resetpwd", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                });
                const response = await res.json();
                if (response.success) {
                    Swal.fire('สำเร็จ', 'รีเซ็ตรหัสผ่านเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', response.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้', 'error');
                }
            });
        });
</script>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>