<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Parent.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$parent = new StudentParent($db);

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
                        <h5 class="m-0">จัดการข้อมูลผู้ปกครอง</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card container mx-auto px-4 py-6 ">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">ข้อมูลผู้ปกครองนักเรียน</h2>
                    <div>
                        <select id="filterClass" class="form-control d-inline-block" style="width:auto;display:inline-block;">
                            <option value="">-- เลือกชั้น --</option>
                        </select>
                        <select id="filterRoom" class="form-control d-inline-block" style="width:auto;display:inline-block;">
                            <option value="">-- เลือกห้อง --</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="parentTable" class="min-w-full divide-y divide-gray-200 table-auto" style="width:100%">
                        <thead class="bg-indigo-500">
                            <tr>
                                <th class="px-2 py-2 text-center text-white border-b">เลขที่</th>
                                <th class="px-2 py-2 text-center text-white border-b">รหัสนักเรียน</th>
                                <th class="px-2 py-2 text-center text-white border-b">ชื่อ-นามสกุล</th>
                                <th class="px-2 py-2 text-center text-white border-b">ชั้น</th>
                                <th class="px-2 py-2 text-center text-white border-b">ชื่อบิดา</th>
                                <th class="px-2 py-2 text-center text-white border-b">อาชีพบิดา</th>
                                <th class="px-2 py-2 text-center text-white border-b">ชื่อมารดา</th>
                                <th class="px-2 py-2 text-center text-white border-b">อาชีพมารดา</th>
                                <th class="px-2 py-2 text-center text-white border-b">ผู้ปกครอง</th>
                                <th class="px-2 py-2 text-center text-white border-b">ความสัมพันธ์</th>
                                <th class="px-2 py-2 text-center text-white border-b">เบอร์โทร</th>
                                <th class="px-2 py-2 text-center text-white border-b">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="parentTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <!-- Modal for editing parent info -->
        <div class="modal fade" id="editParentModal" tabindex="-1" role="dialog" aria-labelledby="editParentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editParentModalLabel">แก้ไขข้อมูลผู้ปกครอง</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editParentForm">
                            <input type="hidden" id="editStu_id" name="editStu_id">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="editFather_name">ชื่อบิดา</label>
                                    <input type="text" class="form-control" id="editFather_name" name="editFather_name">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editFather_occu">อาชีพบิดา</label>
                                    <input type="text" class="form-control" id="editFather_occu" name="editFather_occu">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editFather_income">รายได้บิดา</label>
                                    <input type="text" class="form-control" id="editFather_income" name="editFather_income">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="editMother_name">ชื่อมารดา</label>
                                    <input type="text" class="form-control" id="editMother_name" name="editMother_name">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editMother_occu">อาชีพมารดา</label>
                                    <input type="text" class="form-control" id="editMother_occu" name="editMother_occu">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editMother_income">รายได้มารดา</label>
                                    <input type="text" class="form-control" id="editMother_income" name="editMother_income">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="editPar_name">ชื่อผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_name" name="editPar_name">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editPar_relate">ความสัมพันธ์</label>
                                    <input type="text" class="form-control" id="editPar_relate" name="editPar_relate">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editPar_occu">อาชีพผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_occu" name="editPar_occu">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="editPar_income">รายได้ผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_income" name="editPar_income">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editPar_addr">ที่อยู่ผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_addr" name="editPar_addr">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="editPar_phone">เบอร์โทร</label>
                                    <input type="text" class="form-control" id="editPar_phone" name="editPar_phone">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" id="submitEditParentForm" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        // ใส่ token key ที่นี่ (ต้องตรงกับใน api ที่จะสร้าง)
        const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
        let parentTable;
        $(document).ready(function() {
            parentTable = $('#parentTable').DataTable({
                columnDefs: [
                    { className: 'text-center', width: '5%', targets: 0 },
                    { className: 'text-center', width: '10%', targets: 1 },
                    { className: 'text-left', width: '15%', targets: 2 },
                    { className: 'text-center', width: '7%', targets: 3 },
                    { className: 'text-left', width: '10%', targets: 4 },
                    { className: 'text-left', width: '10%', targets: 5 },
                    { className: 'text-left', width: '10%', targets: 6 },
                    { className: 'text-left', width: '10%', targets: 7 },
                    { className: 'text-left', width: '10%', targets: 8 },
                    { className: 'text-left', width: '7%', targets: 9 },
                    { className: 'text-center', width: '8%', targets: 10 },
                    { className: 'text-center', width: '8%', targets: 11 }
                ],
                autoWidth: false,
                order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: 'full_numbers',
                searching: true,
            });
            loadParents();
            populateFilterSelects();

            $('#filterClass, #filterRoom').on('change', function() {
                loadParents();
            });
        });

        function populateFilterSelects() {
            fetch('api/api_student.php?action=filters&token=' + encodeURIComponent(API_TOKEN_KEY))
                .then(res => res.json())
                .then(data => {
                    // เติม class
                    const classSel = document.getElementById('filterClass');
                    classSel.innerHTML = '<option value="">-- เลือกชั้น --</option>';
                    data.classes.forEach(cls => {
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

        async function loadParents() {
            const classVal = document.getElementById('filterClass').value;
            const roomVal = document.getElementById('filterRoom').value;
            let url = 'api/api_parent.php?action=list&token=' + encodeURIComponent(API_TOKEN_KEY);
            if (classVal) url += '&class=' + encodeURIComponent(classVal);
            if (roomVal) url += '&room=' + encodeURIComponent(roomVal);
            const res = await fetch(url);
            const data = await res.json();
            parentTable.clear();
            data.forEach(parent => {
                parentTable.row.add([
                    parent.Stu_no,
                    parent.Stu_id,
                    parent.Stu_pre + parent.Stu_name + ' ' + parent.Stu_sur,
                    'ม.' + parent.Stu_major + '/' + parent.Stu_room,
                    parent.Father_name,
                    parent.Father_occu,
                    parent.Mother_name,
                    parent.Mother_occu,
                    parent.Par_name,
                    parent.Par_relate,
                    parent.Par_phone,
                    `<button class="btn btn-warning btn-sm editParentBtn" data-id="${parent.Stu_id}">แก้ไข</button>`
                ]);
            });
            parentTable.draw();
        }

        $(document).on('click', '.editParentBtn', function() {
            const id = $(this).data('id');
            openEditParentModal(id);
        });

        async function openEditParentModal(id) {
            const res = await fetch('api/api_parent.php?action=get&id=' + id + '&token=' + encodeURIComponent(API_TOKEN_KEY));
            const data = await res.json();
            if (!data || !data[0] || !data[0].Stu_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถโหลดข้อมูลผู้ปกครองได้ หรือข้อมูลไม่สมบูรณ์'
                });
                return;
            }
            const p = data[0];
            const form = document.getElementById('editParentForm');
            form.reset();
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
            $('#editParentModalLabel').text('แก้ไขข้อมูลผู้ปกครอง');
            $('#editParentModal').modal('show');
        }

        $('#submitEditParentForm').on('click', async function() {
            const form = document.getElementById('editParentForm');
            const formData = new FormData(form);
            formData.append('token', API_TOKEN_KEY);
            const res = await fetch('api/api_parent.php?action=update&token=' + encodeURIComponent(API_TOKEN_KEY), {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                $('#editParentModal').modal('hide');
                loadParents();
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
        </script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
