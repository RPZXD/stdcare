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
                    <button id="btnAddTeacher" class="btn btn-primary">+ เพิ่มครู</button>
                </div>
                <div class="overflow-x-auto">
                    <table id="teacherTable" class="min-w-full divide-y divide-gray-200 table-auto " style="width:100%">
                        <thead class="bg-indigo-500">
                            <tr>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">รหัสครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ชื่อครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">กลุ่ม</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ชั้น</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ห้อง</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">Role</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">สถานะ</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="teacherTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
        <!-- Modal for adding/editing teacher -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeacherModalLabel">เพิ่มข้อมูลครู</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addTeacherForm">
                        <div class="form-group">
                            <label for="addTeach_id">รหัสครู : </label>
                            <input type="text" class="form-control text-center" id="addTeach_id" name="addTeach_id" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label for="addTeach_name">ชื่อ - นามสกุล : <span class="text-danger">ใส่คำนำหน้าด้วย</span></label>
                            <input type="text" class="form-control text-center" id="addTeach_name" name="addTeach_name" maxlength="200" required>
                        </div>
                        <div class="form-group">
                            <label for="addTeach_major">กลุ่มสาระ : </label>
                            <select class="form-control text-center" name="addTeach_major" id="addTeach_major">
                                <option value="">-- โปรดเลือกกลุ่มสาระ --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addTeach_class">ครูที่ปรึกษาระดับชั้น : </label>
                            <select class="form-control text-center" name="addTeach_class" id="addTeach_class">
                                <option value="">-- โปรดเลือกระดับชั้น --</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addTeach_room">ห้อง : </label>
                            <select class="form-control text-center" name="addTeach_room" id="addTeach_room">
                                <option value="">-- โปรดเลือกห้อง --</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addTeach_status">สถานะ : </label>
                            <select class="form-control text-center" name="addTeach_status" id="addTeach_status">
                                <option value="">-- โปรดเลือกสถานะ --</option>
                                <option value="1">ปกติ</option>
                                <option value="2">ย้าย รร.</option>
                                <option value="3">เกษียณอายุราชการ</option>
                                <option value="4">ลาออก</option>
                                <option value="9">เสียชีวิต</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addrole_std">บทบาทในโปรแกรมรายงานการสอน : </label>
                            <select class="form-control text-center" name="addrole_std" id="addrole_std">
                                <option value="">-- โปรดเลือกบทบาท --</option>
                                <option value="T">ครู</option>
                                <option value="HOD">หัวหน้าแผนก/กลุ่มสาระ</option>
                                <option value="VP">รองผู้อำนวยการ</option>
                                <option value="DIR">ผู้อำนวยการ</option>
                                <option value="ADM">Admin</option>
                            </select>
                        </div>
                        </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" id="submitAddForm" class="btn btn-primary">บันทึก</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTeacherModalLabel">แก้ไขข้อมูลครู</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editTeacherForm">
                    <div class="form-group">
                        <input type="hidden" id="editTeach_id_old" name="editTeach_id_old" required>
                        <label for="editTeach_id">username : </label>
                        <input type="text" class="form-control text-center" id="editTeach_id" name="editTeach_id" maxlength="5" required>
                    </div>
                    <div class="form-group">
                        <label for="editTeach_name">ชื่อ - นามสกุล : <span class="text-danger">ใส่คำนำหน้าด้วย</span></label>
                        <input type="text" class="form-control text-center" id="editTeach_name" name="editTeach_name" maxlength="200" required>
                    </div>
                    <div class="form-group">
                        <label for="editTeach_major">กลุ่มสาระ : </label>
                        <select class="form-control text-center" name="editTeach_major" id="editTeach_major">
                            <option value="">-- โปรดเลือกกลุ่มสาระ --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editTeach_class">ครูที่ปรึกษาระดับชั้น : </label>
                        <select class="form-control text-center" name="editTeach_class" id="editTeach_class">
                            <option value="">-- โปรดเลือกระดับชั้น --</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editTeach_room">ห้อง : </label>
                        <select class="form-control text-center" name="editTeach_room" id="editTeach_room">
                            <option value="">-- โปรดเลือกห้อง --</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editTeach_status">สถานะ : </label>
                        <select class="form-control text-center" name="editTeach_status" id="editTeach_status">
                            <option value="">-- โปรดเลือกสถานะ --</option>
                            <option value="1">ปกติ</option>
                            <option value="2">ย้าย รร.</option>
                            <option value="3">เกษียณอายุราชการ</option>
                            <option value="4">ลาออก</option>
                            <option value="9">เสียชีวิต</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editrole_std">บทบาทในโปรแกรมรายงานการสอน : </label>
                        <select class="form-control text-center" name="editrole_std" id="editrole_std">
                            <option value="">-- โปรดเลือกบทบาท --</option>
                            <option value="T">ครู</option>
                            <option value="OF">เจ้าหน้าที่</option>
                            <option value="VP">รองผู้อำนวยการ</option>
                            <option value="DIR">ผู้อำนวยการ</option>
                            <option value="ADM">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" id="submitEditForm" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </div>
    </div>
</div>


<script>
        let teacherTable;
        // Initial load
        $(document).ready(function() {
            // สร้าง DataTable ครั้งเดียว
            teacherTable = $('#teacherTable').DataTable({
                columnDefs: [
                    { className: 'text-center', width: '8%', targets: 0 },  // ✅ ถูกต้อง
                    { className: 'text-left', width: '20%', targets: 1 },
                    { className: 'text-left', width: '20%', targets: 2 },
                    { className: 'text-center', targets: 3 },
                    { className: 'text-center', targets: 4 },
                    { className: 'text-center', targets: 5 },
                    { className: 'text-center', targets: 6 },
                    { className: 'text-center', targets: 7 }
                ],
                autoWidth: false,
                order: [[0, 'asc']], // Default sort by first column (รหัสครู)
                pageLength: 10, // Default number of rows per page
                lengthMenu: [10, 25, 50, 100], // Options for number of rows per page
                pagingType: 'full_numbers', // Full pagination controls
                searching: true, // Enable search box
                
            });
            loadTeachers();

            // ปุ่มเพิ่มครู
            $('#btnAddTeacher').on('click', function() {
                $('#addTeacherForm')[0].reset();
                $('#addTeacherModalLabel').text('เพิ่มข้อมูลครู');
                $('#addTeacherModal').modal('show');
            });

            // ปุ่มบันทึกใน modal เพิ่ม
            $('#submitAddForm').on('click', async function() {
                const form = document.getElementById('addTeacherForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                const res = await fetch('api/api_teacher.php?action=create', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                const result = await res.json();
                if (result.success) {
                    $('#addTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            });

            // ปุ่มบันทึกใน modal แก้ไข
            $('#submitEditForm').on('click', async function() {
                const form = document.getElementById('editTeacherForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                const res = await fetch('api/api_teacher.php?action=update', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                const result = await res.json();
                if (result.success) {
                    $('#editTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            });
        });

        // เพิ่มฟังก์ชันนี้ก่อน fetch('api/fet_major.php')
function populateSelectElement(selectId, data) {
    const select = document.getElementById(selectId);
    if (!select) return;
    // ลบ option เดิม ยกเว้นตัวแรก
    while (select.options.length > 1) {
        select.remove(1);
    }
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.Teach_major || '';
        option.text = item.Teach_major || '';
        select.appendChild(option);
    });
}

// Fetch majors and populate the selects
fetch('api/fet_major.php')
    .then(response => response.json())
    .then(data => {
        populateSelectElement('addTeach_major', data);
        populateSelectElement('editTeach_major', data);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
});

        // Fetch and render teacher data
        async function loadTeachers() {
            const res = await fetch('api/api_teacher.php?action=list');
            const data = await res.json();
            teacherTable.clear();
            data.forEach(teacher => {
                // Map role_std code to Thai description
                let roleDisplay = '';
                switch (teacher.role_std) {
                    case 'T':
                        roleDisplay = 'ครู';
                        break;
                    case 'DIR':
                        roleDisplay = 'ผู้อำนวยการ';
                        break;
                    case 'VP':
                        roleDisplay = 'รองผู้อำนวยการ';
                        break;
                    case 'OF':
                        roleDisplay = 'เจ้าหน้าที่';
                        break;
                    case 'ADM':
                        roleDisplay = 'Administrator';
                        break;
                    default:
                        roleDisplay = teacher.role_std || '';
                }
                teacherTable.row.add([
                    teacher.Teach_id,
                    teacher.Teach_name,
                    teacher.Teach_major || '',
                    teacher.Teach_class || '',
                    teacher.Teach_room || '',
                    roleDisplay,
                    teacher.Teach_status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน',
                    `<button class="btn btn-warning btn-sm editBtn" data-id="${teacher.Teach_id}">แก้ไข</button>
                     <button class="btn btn-danger btn-sm deleteBtn" data-id="${teacher.Teach_id}">ลบ</button>
                     <button class="btn btn-secondary btn-sm resetPwdBtn" data-id="${teacher.Teach_id}">รีเซ็ตรหัสผ่าน</button>`
                ]);
            });
            teacherTable.draw();
            // Attach event listeners
            // เปลี่ยนจาก document.querySelectorAll เป็น event delegation
            // document.querySelectorAll('.editBtn').forEach(btn => {
            //     btn.onclick = () => openEditModal(btn.dataset.id);
            // });
            // document.querySelectorAll('.deleteBtn').forEach(btn => {
            //     btn.onclick = () => deleteTeacher(btn.dataset.id);
            // });
        }

        // ใช้ event delegation สำหรับปุ่ม edit และ delete
        $(document).on('click', '.editBtn', function() {
            const id = $(this).data('id');
            openEditModal(id);
        });
        $(document).on('click', '.deleteBtn', function() {
            const id = $(this).data('id');
            deleteTeacher(id);
        });
        // เพิ่ม event delegation สำหรับปุ่ม reset password
        $(document).on('click', '.resetPwdBtn', async function() {
            const id = $(this).data('id');
            const result = await Swal.fire({
                title: 'รีเซ็ตรหัสผ่าน?',
                text: "ต้องการรีเซ็ตรหัสผ่านเป็นรหัสครูหรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, รีเซ็ต',
                cancelButtonText: 'ยกเลิก'
            });
            if (!result.isConfirmed) return;
            const res = await fetch('api/api_teacher.php?action=resetpwd', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            });
            const response = await res.json();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'รีเซ็ตรหัสผ่านสำเร็จ',
                    text: 'รหัสผ่านใหม่คือรหัสครู',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้'
                });
            }
        });

        // เปิด modal แก้ไข
        async function openEditModal(id) {
            const res = await fetch('api/api_teacher.php?action=get&id=' + id);
            const data = await res.json();
            // console.log(data); // debug ดูข้อมูลที่ได้
            // ตรวจสอบข้อมูลก่อนแสดง modal
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: data.message || 'ไม่สามารถโหลดข้อมูลครูได้ หรือข้อมูลไม่สมบูรณ์'
                });
                return;
            }
            if (!data || !data.Teach_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถโหลดข้อมูลครูได้ หรือข้อมูลไม่สมบูรณ์'
                });
                return;
            }
            const form = document.getElementById('editTeacherForm');
            form.reset();
            document.getElementById('editTeach_id_old').value = data.Teach_id;
            document.getElementById('editTeach_id').value = data.Teach_id;
            document.getElementById('editTeach_name').value = data.Teach_name;
            document.getElementById('editTeach_major').value = data.Teach_major || '';
            document.getElementById('editTeach_class').value = data.Teach_class || '';
            document.getElementById('editTeach_room').value = data.Teach_room || '';
            document.getElementById('editTeach_status').value = data.Teach_status;
            document.getElementById('editrole_std').value = data.role_std || '';
            $('#editTeacherModalLabel').text('แก้ไขข้อมูลครู');
            $('#editTeacherModal').modal('show');
        }

        async function deleteTeacher(id) {
            // เปลี่ยนจาก confirm เป็น SweetAlert2
            const result = await Swal.fire({
                title: 'ยืนยันการลบข้อมูลครูนี้?',
                text: "คุณต้องการลบข้อมูลนี้หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            });
            if (!result.isConfirmed) return;
            const res = await fetch('api/api_teacher.php?action=delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id),
                credentials: 'same-origin'
            });
            const response = await res.json();
            if (response.success) {
                loadTeachers();
                Swal.fire({
                    icon: 'success',
                    title: 'ลบข้อมูลสำเร็จ',
                    showConfirmButton: false,
                    timer: 1200
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถลบข้อมูลได้'
                });
            }
        }
</script>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
        <!-- Bootstrap 5, jQuery, DataTables scripts -->

</body>
</html>
