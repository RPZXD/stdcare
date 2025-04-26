<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

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

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">จัดการข้อมูลครู</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card container mx-auto px-4 py-6 ">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">จัดการข้อมูลครู</h2>
                    <button id="btnAddTeacher" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal">+ เพิ่มครู</button>
                </div>
                <div class="overflow-x-auto">
                    <table id="teacherTable" class="min-w-full divide-y divide-gray-200 table-auto" style="width:100%">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">รหัสครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">ชื่อครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">กลุ่ม</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">ชั้น</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">ห้อง</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">Role</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">สถานะ</th>
                                <th class="px-4 py-2 text-center  font-medium text-gray-700 uppercase tracking-wider border-b">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="teacherTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="teacherModalLabel">เพิ่ม/แก้ไขข้อมูลครู</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form id="teacherForm">
                    <div class="modal-body">
                        <input type="hidden" name="Teach_id_old" id="Teach_id_old">
                        <div class="mb-3">
                            <label class="form-label">รหัสครู</label>
                            <input type="text" name="Teach_id" id="Teach_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อครู</label>
                            <input type="text" name="Teach_name" id="Teach_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สาขา</label>
                            <input type="text" name="Teach_major" id="Teach_major" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชั้น</label>
                            <input type="text" name="Teach_class" id="Teach_class" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ห้อง</label>
                            <input type="text" name="Teach_room" id="Teach_room" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="Teach_status" id="Teach_status" class="form-select">
                                <option value="1">ใช้งาน</option>
                                <option value="0">ไม่ใช้งาน</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน</label>
                            <input type="password" name="Teach_password" id="Teach_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บทบาท</label>
                            <input type="text" name="role_std" id="role_std" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                      <button type="submit" class="btn btn-success">บันทึก</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </section>

        <script>
        let teacherTable;
        // Initial load
        $(document).ready(function() {
            // สร้าง DataTable ครั้งเดียว
            teacherTable = $('#teacherTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                columnDefs: [
                    { className: 'text-center', width: '8%', targets: 0 },  // ✅ ถูกต้อง
                    { className: 'text-left', width: '20%', targets: 1 },
                    { className: 'text-left', width: '10%', targets: 2 },
                    { className: 'text-center', targets: 3 },
                    { className: 'text-center', targets: 4 },
                    { className: 'text-center', targets: 5 },
                    { className: 'text-center', targets: 6 },
                    { className: 'text-center', targets: 7 }
                ],
                autoWidth: false 
            });
            loadTeachers();
        });

        // Fetch and render teacher data
        async function loadTeachers() {
            const res = await fetch('api/api_teacher.php?action=list');
            const data = await res.json();
            // ลบข้อมูลเดิม
            teacherTable.clear();
            // เพิ่มข้อมูลใหม่
            data.forEach(teacher => {
                teacherTable.row.add([
                    teacher.Teach_id,
                    teacher.Teach_name,
                    teacher.Teach_major || '',
                    teacher.Teach_class || '',
                    teacher.Teach_room || '',
                    teacher.role_std || '',
                    teacher.Teach_status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน',
                    `<button class="btn btn-warning btn-sm editBtn" data-id="${teacher.Teach_id}" data-bs-toggle="modal" data-bs-target="#teacherModal">แก้ไข</button>
                     <button class="btn btn-danger btn-sm deleteBtn" data-id="${teacher.Teach_id}">ลบ</button>`
                ]);
            });
            teacherTable.draw();
            // Attach event listeners
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.onclick = () => openEditModal(btn.dataset.id);
            });
            document.querySelectorAll('.deleteBtn').forEach(btn => {
                btn.onclick = () => deleteTeacher(btn.dataset.id);
            });
        }

        // Modal logic
        const form = document.getElementById('teacherForm');
        const modalTitle = document.getElementById('teacherModalLabel');
        const btnAddTeacher = document.getElementById('btnAddTeacher');

        btnAddTeacher.onclick = () => {
            form.reset();
            document.getElementById('Teach_id_old').value = '';
            modalTitle.textContent = 'เพิ่มข้อมูลครู';
            // Bootstrap 5 modal will open automatically via data-bs-toggle
        };

        async function openEditModal(id) {
            const res = await fetch('api/api_teacher.php?action=get&id=' + id);
            const data = await res.json();
            form.reset();
            document.getElementById('Teach_id_old').value = data.Teach_id;
            document.getElementById('Teach_id').value = data.Teach_id;
            document.getElementById('Teach_name').value = data.Teach_name;
            document.getElementById('Teach_major').value = data.Teach_major || '';
            document.getElementById('Teach_class').value = data.Teach_class || '';
            document.getElementById('Teach_room').value = data.Teach_room || '';
            document.getElementById('Teach_status').value = data.Teach_status;
            document.getElementById('Teach_password').value = '';
            document.getElementById('role_std').value = data.role_std || '';
            modalTitle.textContent = 'แก้ไขข้อมูลครู';
            // Bootstrap 5 modal will open automatically via data-bs-toggle
        }

        form.onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            let action = formData.get('Teach_id_old') ? 'update' : 'create';
            const res = await fetch('api/api_teacher.php?action=' + action, {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                // Close modal using Bootstrap 5
                const modalEl = document.getElementById('teacherModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                loadTeachers();
            } else {
                alert(result.message || 'เกิดข้อผิดพลาด');
            }
        };

        async function deleteTeacher(id) {
            if (!confirm('ยืนยันการลบข้อมูลครูนี้?')) return;
            const res = await fetch('api/api_teacher.php?action=delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            });
            const result = await res.json();
            if (result.success) {
                loadTeachers();
            } else {
                alert(result.message || 'เกิดข้อผิดพลาด');
            }
        }
        </script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
