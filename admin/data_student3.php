<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);

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
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}
require_once('../models/Student.php');
$studentModel = new \App\Models\Student();
$students = $studentModel->getAll();

require_once('header.php');
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-tailwind@5/tailwind.min.css">
<body class="hold-transition sidebar-mini layout-fixed light-mode bg-gray-50">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper bg-gray-50">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-2xl font-bold text-blue-700 flex items-center">
                        👨‍🎓 จัดการผู้ใช้ (นักเรียน)
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-8 flex justify-center">
                <div class="max-w-6xl w-full">
                    <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center text-center border border-blue-100">
                        <div class="w-full flex justify-end mb-4">
                            <div>
                            <span class="text-red-500 mr-2">** สามารถคลิกที่ชื่อเพื่อแก้ไขชื่อนักเรียนได้</span>
                            </div>
                            <button id="btnAddStudent" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-semibold transition flex items-center gap-2">➕ เพิ่มผู้ใช้ใหม่</button>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <!-- Filter controls -->
                            <div class="flex flex-wrap gap-4 mb-4 items-center">
                                <select id="filter-room" class="border border-blue-200 rounded px-2 py-1">
                                    <option value="">-- ห้องเรียนทั้งหมด --</option>
                                </select>
                                <select id="filter-status" class="border border-blue-200 rounded px-2 py-1">
                                    <option value="">-- สถานะทั้งหมด --</option>
                                    <option value="1">🟢 ปกติ</option>
                                    <option value="2">🚚 ย้าย</option>
                                    <option value="3">🎉 จบการศึกษา</option>
                                    <option value="4">🏠 ลาออก</option>
                                    <option value="9">⚰️ เสียชีวิต</option>
                                </select>
                                <button id="filter-clear" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">ล้างตัวกรอง</button>
                            </div>
                            <table id="studentTable" class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-indigo-500 text-white">
                                <tr>
                                    <th class="px-4 py-2 text-center font-semibold">🔢 เลขที่</th>
                                    <th class="px-4 py-2 text-center font-semibold">🆔 รหัสนักเรียน</th>
                                    <th class="px-4 py-2 text-center font-semibold">👨‍🎓 ชื่อนักเรียน</th>
                                    <th class="px-4 py-2 text-center font-semibold">🏫 ห้องเรียน</th>
                                    <th class="px-4 py-2 text-center font-semibold">✅ สถานะ</th>
                                    <th class="px-4 py-2 text-center font-semibold">⚙️ จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <!-- JS will fill -->
                            </tbody>
                        </table>
                        </div>
                        <div class="mt-4 text-sm text-gray-500">* สามารถเพิ่ม/แก้ไข/ลบผู้ใช้ได้ในอนาคต</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function renderStatusSwitch(student) {
    const statusMap = {
        1: { text: '🟢 ปกติ', color: 'text-green-600' },
        2: { text: '🚚 ย้าย', color: 'text-blue-500' },
        3: { text: '🎉 จบการศึกษา', color: 'text-yellow-600' },
        4: { text: '🏠 ลาออก', color: 'text-gray-500' },
        9: { text: '⚰️ เสียชีวิต', color: 'text-red-600' }
    };
    const current = statusMap[student.Stu_status] || { text: 'ไม่ทราบ', color: 'text-gray-400' };
    return `
        <div class="relative inline-block text-left">
            <button type="button" class="status-dropdown-btn ${current.color} font-semibold flex items-center gap-1" data-id="${student.Std_id}">
                ${current.text}
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div class="status-dropdown-menu absolute z-10 mt-1 hidden bg-white border border-gray-200 rounded shadow-lg min-w-[120px]" data-id="${student.Std_id}">
                <div class="py-1">
                    <button class="status-option block w-full text-left px-4 py-2 text-green-600 hover:bg-gray-100" data-status="1">🟢 ปกติ</button>
                    <button class="status-option block w-full text-left px-4 py-2 text-blue-500 hover:bg-gray-100" data-status="2">🚚 ย้าย</button>
                    <button class="status-option block w-full text-left px-4 py-2 text-yellow-600 hover:bg-gray-100" data-status="3">🎉 จบการศึกษา</button>
                    <button class="status-option block w-full text-left px-4 py-2 text-gray-500 hover:bg-gray-100" data-status="4">🏠 ลาออก</button>
                    <button class="status-option block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100" data-status="9">⚰️ เสียชีวิต</button>
                </div>
            </div>
        </div>
    `;
}
function getStatusOptions(selected) {
    const statusList = [
        { val: 1, label: '🟢 ปกติ' },
        { val: 2, label: '🚚 ย้าย' },
        { val: 3, label: '🎉 จบการศึกษา' },
        { val: 4, label: '🏠 ลาออก' },
        { val: 9, label: '⚰️ เสียชีวิต' }
    ];
    return statusList.map(s => `<option value="${s.val}" ${parseInt(selected) === s.val ? 'selected' : ''}>${s.label}</option>`).join('');
}
function getRoomOptions(selected) {
    let html = '<option value="">-- เลือกห้องเรียน --</option>';
    if (!window._rooms) return html;
    window._rooms.forEach(room => {
        html += `<option value="${room.name}" ${selected === room.name ? 'selected' : ''}>${room.name}</option>`;
    });
    return html;
}
function reloadTable() {
    $.getJSON('../controllers/StudentController.php?action=list', function(data) {
        const room = $('#filter-room').val();
        const status = $('#filter-status').val();
        let tbody = '';
        let filtered = data.filter(function(student) {
            let ok = true;
            if (room && (`${student.Stu_major}/${student.Stu_room}` !== room)) ok = false;
            if (status && String(student.Stu_status) !== String(status)) ok = false;
            return ok;
        });
        filtered.forEach(function(student) {
            const fullName = [student.Stu_pre, student.Stu_name, student.Stu_sur].filter(Boolean).join(' ');
            const roomDisplay = `ม.${student.Stu_major}/${student.Stu_room}`;
            tbody += `<tr data-id="${student.Stu_id}">
                <td class="px-4 py-2 text-center editable" data-field="Stu_no">${student.Stu_no}</td>
                <td class="px-4 py-2 text-center">${student.Stu_id}</td>
                <td class="px-4 py-2 text-left editable" data-field="Std_name">${fullName}</td>
                <td class="px-4 py-2 text-center editable" data-field="Stu_room">${roomDisplay}</td>
                <td class="px-4 py-2 text-center">${renderStatusSwitch(student)}</td>
                <td class="px-4 py-2 text-center flex gap-2 justify-center">
                    <button class="btn-resetpwd bg-gray-400 hover:bg-gray-500 text-white px-2 py-1 rounded flex items-center gap-1" data-id="${student.Stu_id}">🔑 รีเซ็ตรหัสผ่าน</button>
                    <button class="btn-delete bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded flex items-center gap-1" data-id="${student.Stu_id}">🗑️ ลบ</button>
                </td>
            </tr>`;
        });
        if ($.fn.DataTable.isDataTable('#studentTable')) {
            $('#studentTable').DataTable().destroy();
        }
        $('#studentTable tbody').html(tbody);
        $('#studentTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
            order: [[1, 'asc']],
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50, 100],
            autoWidth: false,
            responsive: true
        });
    });
}
function showStudentModal(type, student = {}) {
    const stuPre = student.Stu_pre || '';
    const stuName = student.Stu_name || '';
    const stuSur = student.Stu_sur || '';
    const fullName = [stuPre, stuName, stuSur].filter(Boolean).join(' ');
    Swal.fire({
        title: type === 'edit' ? '✏️ แก้ไขข้อมูลนักเรียน' : '➕ เพิ่มผู้ใช้ใหม่',
        html: `
            <form id="studentForm" class="space-y-3 text-left">
                <div class="mb-2">
                    <label class="block text-sm font-semibold mb-1 text-blue-700">🆔 รหัสนักเรียน</label>
                    <input type="text" id="Std_id" class=" w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400" placeholder="รหัสนักเรียน" value="${student.Std_id || ''}" ${type === 'edit' ? 'readonly' : ''} required>
                </div>
                <div class="mb-2 flex gap-2">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-blue-700">คำนำหน้า</label>
                        <input type="text" id="Stu_pre" class="w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400" placeholder="คำนำหน้า" value="${stuPre}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-blue-700">ชื่อ</label>
                        <input type="text" id="Stu_name" class="w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400" placeholder="ชื่อ" value="${stuName}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-blue-700">สกุล</label>
                        <input type="text" id="Stu_sur" class="w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400" placeholder="สกุล" value="${stuSur}">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-xs text-gray-500">ชื่อเต็ม: <span id="fullname-preview">${fullName}</span></label>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-semibold mb-1 text-blue-700">🏫 ห้องเรียน</label>
                    <select id="Stu_room" class=" w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400">${getRoomOptions(student.Stu_room || '')}</select>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-semibold mb-1 text-blue-700">✅ สถานะ</label>
                    <select id="Stu_status" class=" w-full border border-blue-200 rounded focus:ring-2 focus:ring-blue-400">${getStatusOptions(student.Stu_status ?? 1)}</select>
                </div>
            </form>
        `,
        customClass: {
            htmlContainer: 'text-left',
            confirmButton: 'bg-blue-600 text-white px-6 py-2 rounded font-semibold hover:bg-blue-700',
            cancelButton: 'bg-gray-200 text-gray-700 px-6 py-2 rounded font-semibold hover:bg-gray-300'
        },
        showCancelButton: true,
        confirmButtonText: type === 'edit' ? 'บันทึก' : 'เพิ่ม',
        cancelButtonText: 'ยกเลิก',
        focusConfirm: false,
        preConfirm: () => {
            const pre = $('#Stu_pre').val().trim();
            const name = $('#Stu_name').val().trim();
            const sur = $('#Stu_sur').val().trim();
            return {
                Std_id: $('#Std_id').val().trim(),
                Stu_pre: pre,
                Stu_name: name,
                Stu_sur: sur,
                Std_name: [pre, name, sur].filter(Boolean).join(' '),
                Stu_room: $('#Stu_room').val().trim(),
                Stu_status: $('#Stu_status').val()
            };
        },
        didOpen: () => {
            $('#Stu_pre, #Stu_name, #Stu_sur').on('input', function() {
                const pre = $('#Stu_pre').val().trim();
                const name = $('#Stu_name').val().trim();
                const sur = $('#Stu_sur').val().trim();
                $('#fullname-preview').text([pre, name, sur].filter(Boolean).join(' '));
            });
            $('#studentForm input, #studentForm select').on('keydown', function(e) {
                if (e.key === 'Enter') e.preventDefault();
            });
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            if (type === 'edit') {
                $.ajax({
                    url: '../controllers/StudentController.php?action=update',
                    type: 'POST',
                    data: result.value,
                    success: function(res) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        if ($.fn.DataTable.isDataTable('#studentTable')) {
                            $('#studentTable').DataTable().destroy();
                        }
                        reloadTable();
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'ไม่สามารถแก้ไขข้อมูลได้',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            } else {
                $.ajax({
                    url: '../controllers/StudentController.php?action=create',
                    type: 'POST',
                    data: result.value,
                    success: function(res) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'เพิ่มผู้ใช้ใหม่เรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        if ($.fn.DataTable.isDataTable('#studentTable')) {
                            $('#studentTable').DataTable().destroy();
                        }
                        reloadTable();
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'ไม่สามารถเพิ่มผู้ใช้ได้',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        }
    });
}
$(document).ready(function() {
    $.getJSON('../controllers/RoomController.php?action=list', function(rooms) {
        window._rooms = rooms;
        let html = '<option value="">-- ห้องเรียนทั้งหมด --</option>';
        rooms.forEach(room => {
            html += `<option value="${room.name}">${room.name}</option>`;
        });
        $('#filter-room').html(html);
        reloadTable();
    });

    $('#btnAddStudent').on('click', function() {
        showStudentModal('create');
    });

    $('#filter-room, #filter-status').on('change', function() {
        reloadTable();
    });
    $('#filter-clear').on('click', function() {
        $('#filter-room').val('');
        $('#filter-status').val('');
        reloadTable();
    });

    $('#studentTable').on('click', 'td.editable', function() {
        if ($(this).find('input').length > 0) return;
        const td = $(this);
        const oldVal = td.text();
        const field = td.data('field');
        const tr = td.closest('tr');
        const id = tr.data('id');
        if (field === 'Std_name') {
            $.getJSON('../controllers/StudentController.php?action=get&id=' + encodeURIComponent(id), function(student) {
                Swal.fire({
                    title: 'แก้ไขชื่อ',
                    html: `
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="edit_Stu_pre" class="w-1/4 border border-blue-200 rounded px-1" placeholder="คำนำหน้า" value="${student.Stu_pre || ''}">
                            <input type="text" id="edit_Stu_name" class="w-1/3 border border-blue-200 rounded px-1" placeholder="ชื่อ" value="${student.Stu_name || ''}">
                            <input type="text" id="edit_Stu_sur" class="w-1/3 border border-blue-200 rounded px-1" placeholder="สกุล" value="${student.Stu_sur || ''}">
                        </div>
                        <div class="text-xs text-gray-500">ชื่อเต็ม: <span id="edit_fullname_preview">${[student.Stu_pre, student.Stu_name, student.Stu_sur].filter(Boolean).join(' ')}</span></div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'บันทึก',
                    cancelButtonText: 'ยกเลิก',
                    didOpen: () => {
                        $('#edit_Stu_pre, #edit_Stu_name, #edit_Stu_sur').on('input', function() {
                            const pre = $('#edit_Stu_pre').val().trim();
                            const name = $('#edit_Stu_name').val().trim();
                            const sur = $('#edit_Stu_sur').val().trim();
                            $('#edit_fullname_preview').text([pre, name, sur].filter(Boolean).join(' '));
                        });
                    },
                    preConfirm: () => {
                        const pre = $('#edit_Stu_pre').val().trim();
                        const name = $('#edit_Stu_name').val().trim();
                        const sur = $('#edit_Stu_sur').val().trim();
                        return {
                            Std_id: id,
                            Stu_pre: pre,
                            Stu_name: name,
                            Stu_sur: sur,
                            Std_name: [pre, name, sur].filter(Boolean).join(' ')
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: '../controllers/StudentController.php?action=update',
                            type: 'POST',
                            data: result.value,
                            success: function(res) {
                                Swal.fire({toast:true,position:'top-end',icon:'success',title:'บันทึกแล้ว',showConfirmButton:false,timer:1200});
                                reloadTable();
                            },
                            error: function() {
                                Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            }
                        });
                    }
                });
            });
        } else if (field === 'Stu_no') {
            td.html(`<input type="number" min="1" class="inline-edit-input w-full border border-blue-300 rounded px-1" value="${oldVal}" />`);
            td.find('input').focus().select();
            td.find('input').on('blur keydown', function(e) {
                if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                    const newVal = $(this).val().trim();
                    if (newVal !== oldVal && newVal !== '') {
                        $.ajax({
                            url: '../controllers/StudentController.php?action=update',
                            type: 'POST',
                            data: {
                                Std_id: id,
                                Stu_no: newVal
                            },
                            success: function(res) {
                                td.text(newVal);
                                Swal.fire({toast:true,position:'top-end',icon:'success',title:'บันทึกแล้ว',showConfirmButton:false,timer:1200});
                                reloadTable();
                            },
                            error: function() {
                                td.text(oldVal);
                                Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            }
                        });
                    } else {
                        td.text(oldVal);
                    }
                }
            });
        } else {
            const newVal = oldVal;
            td.html(`<input type="text" class="inline-edit-input w-full border border-blue-300 rounded px-1" value="${oldVal}" />`);
            td.find('input').focus().select();

            td.find('input').on('blur keydown', function(e) {
                if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                    const newVal = $(this).val().trim();
                    if (newVal !== oldVal && newVal !== '') {
                        const Std_id = id;
                        let Stu_room = field === 'Stu_room' ? newVal : tr.find('td[data-field="Stu_room"]').text().trim();
                        let Stu_status = tr.find('.status-dropdown-btn').length
                            ? (() => {
                                const btn = tr.find('.status-dropdown-btn');
                                const statusText = btn.contents().filter(function() {
                                    return this.nodeType === 3;
                                }).text().trim();
                                const statusMap = {
                                    '🟢 ปกติ': 1,
                                    '🚚 ย้าย': 2,
                                    '🎉 จบการศึกษา': 3,
                                    '🏠 ลาออก': 4,
                                    '⚰️ เสียชีวิต': 9
                                };
                                for (const [k, v] of Object.entries(statusMap)) {
                                    if (statusText.startsWith(k)) return v;
                                }
                                return 1;
                            })()
                            : 1;

                        $.ajax({
                            url: '../controllers/StudentController.php?action=update',
                            type: 'POST',
                            data: {
                                Std_id: Std_id,
                                Stu_room: Stu_room,
                                Stu_status: Stu_status
                            },
                            success: function(res) {
                                td.text(newVal);
                                Swal.fire({toast:true,position:'top-end',icon:'success',title:'บันทึกแล้ว',showConfirmButton:false,timer:1200});
                                reloadTable();
                            },
                            error: function() {
                                td.text(oldVal);
                                Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            }
                        });
                    } else {
                        td.text(oldVal);
                    }
                }
            });
        }
    });

    $('#studentTable').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ลบผู้ใช้?',
            text: 'คุณแน่ใจว่าต้องการลบผู้ใช้นี้หรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/StudentController.php?action=delete',
                    type: 'POST',
                    data: { Std_id: id },
                    success: function(response) {
                        let res = {};
                        try { res = typeof response === 'object' ? response : JSON.parse(response); } catch {}
                        if (res.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'ลบผู้ใช้เรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            reloadTable();
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาดในการลบผู้ใช้',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาดในการลบผู้ใช้',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });

    $('#studentTable').on('click', '.btn-resetpwd', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'รีเซ็ตรหัสผ่าน?',
            text: 'คุณต้องการรีเซ็ตรหัสผ่านของผู้ใช้นี้หรือไม่? (รหัสผ่านจะถูกตั้งค่าใหม่เป็นค่าเริ่มต้น)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e42',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/StudentController.php?action=resetpwd',
                    type: 'POST',
                    data: { Std_id: id },
                    success: function(response) {
                        let res = {};
                        try { res = typeof response === 'object' ? response : JSON.parse(response); } catch {}
                        if (res.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });

    $('#studentTable').on('click', '.status-dropdown-btn', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        $('.status-dropdown-menu').not(`[data-id="${id}"]`).addClass('hidden');
        $(`.status-dropdown-menu[data-id="${id}"]`).toggleClass('hidden');
    });

    $('#studentTable').on('click', '.status-option', function(e) {
        e.stopPropagation();
        const id = $(this).closest('.status-dropdown-menu').data('id');
        const newStatus = $(this).data('status');
        $.getJSON('../controllers/StudentController.php?action=get&id=' + encodeURIComponent(id), function(student) {
            if (!student) return;
            $.ajax({
                url: '../controllers/StudentController.php?action=update',
                type: 'POST',
                data: {
                    Std_id: id,
                    Stu_pre: student.Stu_pre,
                    Stu_name: student.Stu_name,
                    Stu_sur: student.Stu_sur,
                    Std_name: [student.Stu_pre, student.Stu_name, student.Stu_sur].filter(Boolean).join(' '),
                    Stu_room: student.Stu_room,
                    Stu_status: newStatus
                },
                success: function(res) {
                    reloadTable();
                },
                error: function() {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถอัปเดตสถานะได้', 'error');
                }
            });
        });
        $('.status-dropdown-menu').addClass('hidden');
    });

    $(document).on('click', function() {
        $('.status-dropdown-menu').addClass('hidden');
    });
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>
