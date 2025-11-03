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
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <!-- <h5 class="m-0">จัดการข้อมูลนักเรียน</h5> -->
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card card-primary card-outline px-4 py-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">จัดการข้อมูลนักเรียน</h2>
                    <div>
                        <select id="filterClass" class="form-control d-inline-block" style="width:auto;display:inline-block;">
                            <option value="">-- เลือกชั้น --</option>
                        </select>
                        <select id="filterRoom" class="form-control d-inline-block" style="width:auto;display:inline-block;">
                            <option value="">-- เลือกห้อง --</option>
                        </select>
                        <select id="filterStatus" class="form-control d-inline-block" style="width:auto;display:inline-block;">
                            <option value="">-- สถานะ --</option>
                            <option value="1">✅ ปกติ</option>
                            <option value="2">🎓 จบการศึกษา</option>
                            <option value="3">🚚 ย้ายโรงเรียน</option>
                            <option value="4">❌ ออกกลางคัน</option>
                            <option value="9">🕊️ เสียชีวิต</option>
                        </select>
                        <button id="btnAddStudent" class="btn btn-primary ml-2">+ เพิ่มนักเรียน</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="studentTable" class="min-w-full divide-y divide-gray-200 table-auto" style="width:100%">
                        <thead class="bg-indigo-500">
                            <tr>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">เลขที่</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">รหัสนักเรียน</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ชื่อ-นามสกุล</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ชั้น</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">สถานะ</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <!-- Modal for adding/editing student -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">เพิ่มข้อมูลนักเรียน</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addStudentForm">
                            <div class="form-group">
                                <label for="addStu_id">รหัสนักเรียน : </label>
                                <input type="text" class="form-control text-center" id="addStu_id" name="addStu_id" maxlength="10" required>
                            </div>
                            <div class="form-group">
                                <label for="addStu_no">เลขที่ : </label>
                                <select name="addStu_no" id="addStu_no" class="form-control text-center">
                                    <option value="">-- โปรดเลือกเลขที่ --</option>
                                    <?php for ($i = 1; $i <= 50; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="addStu_pre">คำนำหน้าชื่อ : </label>
                                <select name="addStu_pre" id="addStu_pre" class="form-control text-center">
                                    <option value="">-- โปรดเลือกคำนำหน้า --</option>
                                    <option value="เด็กชาย">เด็กชาย</option>
                                    <option value="เด็กหญิง">เด็กหญิง</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นางสาว">นางสาว</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="addStu_name">ชื่อ : </label>
                                <input type="text" class="form-control text-center" id="addStu_name" name="addStu_name" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="addStu_sur">นามสกุล : </label>
                                <input type="text" class="form-control text-center" id="addStu_sur" name="addStu_sur" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="addStu_major">ชั้น : </label>
                                <select name="addStu_major" id="addStu_major" class="form-control text-center">
                                    <option value="">-- โปรดเลือกชั้น --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="addStu_room">ห้อง : </label>
                                <select name="addStu_room" id="addStu_room" class="form-control text-center">
                                    <option value="">-- โปรดเลือกห้อง --</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" id="submitAddStudentForm" class="btn btn-primary">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal (structure similar to Add Modal, with ids changed to edit...) -->
        <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel">แก้ไขข้อมูลนักเรียน</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editStudentForm">
                            <input type="hidden" id="editStu_id_old" name="editStu_id_old" required>
                            <div class="form-group">
                                <label for="editStu_id">รหัสนักเรียน : </label>
                                <input type="text" class="form-control text-center" id="editStu_id" name="editStu_id" maxlength="10" required>
                            </div>
                            <div class="form-group">
                                <label for="editStu_no">เลขที่ : </label>
                                <select name="editStu_no" id="editStu_no" class="form-control text-center">
                                    <option value="">-- โปรดเลือกเลขที่ --</option>
                                    <?php for ($i = 1; $i <= 50; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editStu_pre">คำนำหน้าชื่อ : </label>
                                <select name="editStu_pre" id="editStu_pre" class="form-control text-center">
                                    <option value="เด็กชาย">เด็กชาย</option>
                                    <option value="เด็กหญิง">เด็กหญิง</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นางสาว">นางสาว</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editStu_name">ชื่อ : </label>
                                <input type="text" class="form-control text-center" id="editStu_name" name="editStu_name" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="editStu_sur">นามสกุล : </label>
                                <input type="text" class="form-control text-center" id="editStu_sur" name="editStu_sur" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="editStu_major">ชั้น : </label>
                                <select name="editStu_major" id="editStu_major" class="form-control text-center">
                                    <option value="">-- โปรดเลือกชั้น --</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editStu_room">ห้อง : </label>
                                <select name="editStu_room" id="editStu_room" class="form-control text-center">
                                    <option value="">-- โปรดเลือกห้อง --</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editStu_status">สถานะ : </label>
                                <select class="form-control text-center" name="editStu_status" id="editStu_status">
                                        <option value="1">ปกติ</option>
                                        <option value="2">จบการศึกษา</option>
                                        <option value="3">ย้ายโรงเรียน</option>
                                        <option value="4">ออกกลางคัน</option>
                                        <option value="9">เสียชีวิต</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" id="submitEditStudentForm" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        // ลบ token key ออก (ไม่ต้องใช้)
        // const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
        let studentTable;
        let studentTableInterval = null; // <-- Add this line

        $(document).ready(function() {
            studentTable = $('#studentTable').DataTable({
                columnDefs: [
                    { className: 'text-center', width: '5%', targets: 0 },
                    { className: 'text-center', width: '10%', targets: 1 },
                    { className: 'text-left', width: '25%', targets: 2 },
                    { className: 'text-center', width: '10%', targets: 3 },
                    { className: 'text-center', width: '10%', targets: 4 },
                    { className: 'text-center', width: '35%', targets: 5 }
                ],
                autoWidth: false,
                order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: 'full_numbers',
                searching: true,
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

            $('#filterClass, #filterRoom, #filterStatus').on('change', function() {
                loadStudents();
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
            
            // 1. เปลี่ยน action ที่เรียกจาก 'list' เป็น 'list_for_officer'
            let url = '../controllers/StudentController.php?action=list_for_officer'; 
            
            if (classVal) url += '&class=' + encodeURIComponent(classVal);
            if (roomVal) url += '&room=' + encodeURIComponent(roomVal);
            if (statusVal) url += '&status=' + encodeURIComponent(statusVal);
            
            const res = await fetch(url);
            
            // 2. เปลี่ยนการประมวลผล data ให้รับ format { success: true, data: [...] }
            const responseData = await res.json(); // เปลี่ยนชื่อตัวแปรเป็น responseData
            
            studentTable.clear();
            
            // ตรวจสอบว่ามี data และเป็น array ก่อนวนลูป
            if (responseData && responseData.success && Array.isArray(responseData.data)) {
                
                // 3. วนลูปที่ responseData.data แทน
                responseData.data.forEach(student => { 
                    studentTable.row.add([
                        student.Stu_no,
                        student.Stu_id,
                        student.Stu_pre + student.Stu_name + ' ' + student.Stu_sur,
                        'ม.' + student.Stu_major + '/' + student.Stu_room,
                        getStatusEmoji(student.Stu_status),
                        // (เพิ่ม space ท้ายปุ่มเพื่อความสวยงาม)
                        `<button class="btn btn-warning btn-sm editStudentBtn" data-id="${student.Stu_id}">แก้ไข</button> ` +
                        `<button class="btn btn-danger btn-sm deleteStudentBtn" data-id="${student.Stu_id}">ลบ</button> ` +
                        `<button class="btn btn-secondary btn-sm resetStuPwdBtn" data-id="${student.Stu_id}">รีเซ็ตรหัสผ่าน</button>`
                    ]);
                });

            } else {
                console.error("ไม่สามารถโหลดข้อมูลนักเรียนได้ หรือ format ข้อมูลไม่ถูกต้อง:", responseData);
            }
            
            studentTable.draw();
            makeTableEditable(); // เรียกทุกครั้งหลังโหลดข้อมูล
        }

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
                title: 'รีเซ็ตรหัสผ่าน?',
                text: "ต้องการรีเซ็ตรหัสผ่านเป็นรหัสนักเรียนหรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, รีเซ็ต',
                cancelButtonText: 'ยกเลิก'
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
                    title: 'รีเซ็ตรหัสผ่านสำเร็จ',
                    text: 'รหัสผ่านใหม่คือรหัสนักเรียน',
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
            document.getElementById('editStu_id_old').value = data.Stu_id;
            document.getElementById('editStu_no').value = data.Stu_no;
            document.getElementById('editStu_id').value = data.Stu_id;
            document.getElementById('editStu_pre').value = data.Stu_pre;
            document.getElementById('editStu_name').value = data.Stu_name;
            document.getElementById('editStu_sur').value = data.Stu_sur;
            document.getElementById('editStu_major').value = data.Stu_major;
            document.getElementById('editStu_room').value = data.Stu_room;
            document.getElementById('editStu_status').value = data.Stu_status;
            $('#editStudentModalLabel').text('แก้ไขข้อมูลนักเรียน');
            $('#editStudentModal').modal('show');
        }

        async function deleteStudent(id) {
            const result = await Swal.fire({
                title: 'ยืนยันการลบข้อมูลนักเรียนนี้?',
                text: "คุณต้องการลบข้อมูลนี้หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
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
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
