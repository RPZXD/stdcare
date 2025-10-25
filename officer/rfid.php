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

if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-tailwind@5/tailwind.min.css">
<body class="hold-transition sidebar-mini layout-fixed bg-gray-50">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper bg-gray-50 min-h-screen">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                            🏷️ จัดการ RFID นักเรียน
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
        <div class="container mx-auto py-8 flex flex-col gap-6 max-w-8xl">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1 flex flex-col gap-6">
                    <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                        <div class="mb-2 font-semibold text-blue-700">สแกน/อ่านหมายเลข RFID</div>
                        <div class="flex gap-2 items-center">
                            <input type="text" id="rfid_input" class="border border-blue-300 rounded px-3 py-2 text-lg font-mono w-72" placeholder="แตะบัตร RFID..." autocomplete="off" autofocus inputmode="latin">
                            <button id="btnClearRfid" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded text-gray-700">เคลียร์</button>
                        </div>
                        <div id="rfid_status" class="mt-2 text-sm text-gray-500"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                        <div class="mb-2 font-semibold text-blue-700 flex items-center gap-4">
                            รายการ RFID ที่ลงทะเบียนแล้ว
                            <button id="btnPrintRoomCards" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-1 rounded font-semibold text-xs shadow">
                                🖨️ พิมพ์บัตรทั้งห้อง
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="rfidTable" class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-indigo-500 text-white">
                                        <th class="px-2 py-1">RFID</th>
                                        <th class="px-2 py-1">รหัสนักเรียน</th>
                                        <th class="px-2 py-1">ชื่อ</th>
                                        <th class="px-2 py-1">ห้อง</th>
                                        <th class="px-2 py-1">วันที่ลงทะเบียน</th>
                                        <th class="px-2 py-1">แก้ไข</th>
                                        <th class="px-2 py-1">ลบ</th>
                                        <th class="px-2 py-1">พิมพ์บัตร</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-6">
                    <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                        <div class="mb-2 font-semibold text-blue-700">ค้นหานักเรียน</div>
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="student_search" class="border border-blue-300 rounded px-3 py-2 w-72" placeholder="รหัส/ชื่อ/ห้อง" autocomplete="off">
                            <select id="filter_major" class="border border-blue-200 rounded px-2 py-1">
                                <option value="">ทุกระดับชั้น</option>
                            </select>
                            <select id="filter_room" class="border border-blue-200 rounded px-2 py-1">
                                <option value="">ทุกห้อง</option>
                            </select>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="studentTable" class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-indigo-500 text-white">
                                        <th class="px-2 py-1">รหัส</th>
                                        <th class="px-2 py-1">ชื่อ</th>
                                        <th class="px-2 py-1">ห้อง</th>
                                        <th class="px-2 py-1">เลือก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 border border-blue-100" id="student_detail_box" style="display:none;">
                        <div class="mb-2 font-semibold text-blue-700">ข้อมูลนักเรียนที่เลือก</div>
                        <div class="flex gap-4 items-center">
                            <img id="stu_picture" src="" alt="student" class="w-24 h-24 rounded border object-cover bg-gray-100" onerror="this.src='../dist/img/logo-phicha.png'">
                            <div>
                                <div id="stu_id" class="font-bold text-lg"></div>
                                <div id="stu_name" class="text-md"></div>
                                <div id="stu_major_room" class="text-gray-600"></div>
                                <div id="stu_rfid_status" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4" id="rfid_action_box" style="display:none;">
                        <button id="btnLinkRfid" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded font-semibold">เชื่อม RFID กับนักเรียน</button>
                    </div>
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
$(document).ready(function() {
    // --- ตัวแปร ---
    let students = [];
    let filteredStudents = [];
    let majors = [];
    let rooms = [];
    let selectedStudent = null;
    let selectedStudentData = null;
    let selectedRfid = '';
    let rfidTable = null;

    // --- โหลดข้อมูลนักเรียนทั้งหมด ---
    function loadStudents() {
        // ===== ⬇️⬇️⬇️ จุดที่แก้ไข ⬇️⬇️⬇️ =====
        $.getJSON('../controllers/StudentRfidController.php?action=listStudents', function(data) {
        // ===== ⬆️⬆️⬆️ จุดที่แก้ไข ⬆️⬆️⬆️ =====
            // กรองเฉพาะ Stu_status == 1
            students = (data || []).filter(s => String(s.Stu_status) === '1');
            majors = [...new Set(students.map(s => s.Stu_major).filter(Boolean))];
            rooms = [...new Set(students.map(s => s.Stu_room).filter(Boolean))];
            fillMajorRoomFilter();
            filterStudents();
        });
    }

    // --- กรอง/ค้นหา/แสดงนักเรียนในตาราง ---
    function filterStudents() {
        const search = $('#student_search').val().trim().toLowerCase();
        const major = $('#filter_major').val();
        const room = $('#filter_room').val();
        filteredStudents = students.filter(s => {
            let ok = true;
            const stuMajor = (s.Stu_major ?? '').toString().trim();
            const filterMajor = (major ?? '').toString().trim();
            const stuRoom = (s.Stu_room ?? '').toString().trim();
            const filterRoom = (room ?? '').toString().trim();
            if (filterMajor && stuMajor !== filterMajor) ok = false;
            if (filterRoom && stuRoom !== filterRoom) ok = false;
            if (search) {
                const txt = (s.Stu_id + ' ' + s.Stu_name + ' ' + (s.Stu_sur||'') + ' ' + (s.Stu_room||'')).toLowerCase();
                if (!txt.includes(search)) ok = false;
            }
            return ok;
        });
        fillStudentTable();
    }

    function fillStudentTable() {
        const $tbody = $('#studentTable tbody');
        if ($.fn.DataTable.isDataTable('#studentTable')) {
            $('#studentTable').DataTable().destroy();
        }
        $tbody.empty();
        if (filteredStudents.length === 0) {
            $tbody.append('<tr><td colspan="4" class="text-center text-gray-400">ไม่พบข้อมูล</td></tr>');
            return;
        }
        filteredStudents.forEach(s => {
            // ตรวจสอบว่านักเรียนนี้มี RFID แล้วหรือยัง
            let rfidRegistered = false;
            if (window.rfidList && Array.isArray(window.rfidList)) {
                rfidRegistered = window.rfidList.some(r => r.stu_id == s.Stu_id);
            }
            $tbody.append(`<tr>
                <td class="px-2 py-1">${s.Stu_id}</td>
                <td class="px-2 py-1">${s.Stu_name} ${s.Stu_sur||''}</td>
                <td class="px-2 py-1">ม.${s.Stu_major||''}/${s.Stu_room||''}</td>
                <td class="px-2 py-1">
                    ${
                        rfidRegistered
                        ? '<span class="bg-green-100 text-green-700 px-3 py-1 rounded font-semibold">ถูกเลือกไปแล้ว</span>'
                        : `<button class="selectStudent bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" data-id="${s.Stu_id}">เลือก</button>`
                    }
                </td>
            </tr>`);
        });
        $('#studentTable').DataTable({
            destroy: true,
            searching: true,
            paging: true,
            info: true,
            pageLength: 50,
            order: [],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                paginate: { previous: "ก่อนหน้า", next: "ถัดไป" },
                zeroRecords: "ไม่พบข้อมูล"
            }
        });
    }

    // --- กรองสาขา/ห้อง ---
    function fillMajorRoomFilter() {
        const $major = $('#filter_major');
        const $room = $('#filter_room');
        $major.empty().append('<option value="">ทุกระดับชั้น</option>');
        majors.forEach(m => $major.append(`<option value="${m}">${m}</option>`));
        $room.empty().append('<option value="">ทุกห้อง</option>');
        rooms.forEach(r => $room.append(`<option value="${r}">${r}</option>`));
    }

    $('#student_search').on('input', filterStudents);
    $('#filter_major, #filter_room').on('change', filterStudents);

    // --- เลือกนักเรียน ---
    $('#studentTable').on('click', '.selectStudent', function() {
        const id = String($(this).data('id')).trim();
        selectedStudent = students.find(s => String(s.Stu_id).trim() === id);
        selectedStudentData = selectedStudent;
        showStudentModal(selectedStudent);
    });

    // --- Modal แสดงข้อมูลนักเรียนและยืนยันลงทะเบียน RFID ---
    function showStudentModal(stu) {
        if (!stu) return;
        $.getJSON('../controllers/StudentRfidController.php?action=getByStudent&stu_id=' + stu.Stu_id, function(r) {
            let html = `
                <div class="flex gap-4 items-center">
                    <img src="../photo/${stu.Stu_picture || 'noimg.jpg'}" alt="student" class="w-24 h-24 rounded border object-cover bg-gray-100" onerror="this.src='../dist/img/logo-phicha.png'">
                    <div>
                        <div class="font-bold text-lg">${stu.Stu_id}</div>
                        <div class="text-md">${(stu.Stu_name||'') + ' ' + (stu.Stu_sur||'')}</div>
                        <div class="text-gray-600">ระดับชั้น: ${(stu.Stu_major||'-')} | ห้อง: ${(stu.Stu_room||'-')}</div>
                        <div class="mt-2" id="modal_rfid_status"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <input type="text" id="modal_rfid_input" class="border border-blue-300 rounded px-3 py-2 text-lg font-mono w-72" placeholder="แตะบัตร RFID..." autocomplete="off" inputmode="latin">
                </div>
            `;
            let rfidStatusHtml = '';
            let showRegisterBtn = true;
            if (r && r.rfid_code) {
                rfidStatusHtml = `<span class="text-green-600">RFID: ${r.rfid_code}</span>`;
                showRegisterBtn = false;
            } else {
                rfidStatusHtml = `<span class="text-red-600">ยังไม่ได้ลงทะเบียน RFID</span>`;
                showRegisterBtn = true;
            }
            Swal.fire({
                title: 'ข้อมูลนักเรียน',
                html: html,
                showCancelButton: true,
                showConfirmButton: showRegisterBtn,
                confirmButtonText: 'ยืนยันลงทะเบียน RFID',
                cancelButtonText: 'ปิด',
                didOpen: () => {
                    $('#modal_rfid_status').html(rfidStatusHtml);
                    setTimeout(() => { $('#modal_rfid_input').focus(); }, 300);
                },
                preConfirm: () => {
                    if (!showRegisterBtn) return false;
                    const rfid = $('#modal_rfid_input').val().trim();
                    if (!rfid) {
                        Swal.showValidationMessage('กรุณาสแกน/กรอก RFID');
                        return false;
                    }
                    return rfid;
                }
            }).then(result => {
                if (result.isConfirmed && showRegisterBtn) {
                    const rfid = result.value;
                    $.getJSON('../controllers/StudentRfidController.php?action=getByRfid&rfid_code=' + encodeURIComponent(rfid), function(r2) {
                        if (r2 && r2.rfid_code) {
                            Swal.fire('RFID นี้ถูกใช้แล้ว', '', 'error');
                        } else {
                            Swal.fire({
                                title: 'ยืนยันการลงทะเบียน?',
                                text: `RFID: ${rfid}\nกับนักเรียน: ${stu.Stu_id} ${stu.Stu_name}`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'ยืนยัน',
                                cancelButtonText: 'ยกเลิก'
                            }).then(confirmRes => {
                                if (confirmRes.isConfirmed) {
                                    $.post('../controllers/StudentRfidController.php?action=register', {
                                        stu_id: stu.Stu_id,
                                        rfid_code: rfid
                                    }, function(res) {
                                        if (res.success) {
                                            Swal.fire('สำเร็จ', 'เชื่อม RFID เรียบร้อย', 'success');
                                            loadRfidTable();
                                        } else {
                                            Swal.fire('ผิดพลาด', res.error || 'เกิดข้อผิดพลาด', 'error');
                                        }
                                    }, 'json');
                                }
                            });
                        }
                    });
                }
            });
        });
    }

    // --- ช่อง RFID input ---
    $('#btnClearRfid').click(function() {
        $('#rfid_input').val('').focus();
        $('#rfid_status').text('');
        selectedRfid = '';
    });

    $('#rfid_input').on('keydown', function(e) {
        if (e.key && /[ก-๙]/.test(e.key)) {
            e.preventDefault();
            Swal.fire('กรุณาเปลี่ยนเป็นภาษาอังกฤษ (EN) ก่อนสแกน/กรอก RFID', '', 'warning');
            this.value = '';
            this.focus();
        }
    });

    $('#rfid_input').on('input', function() {
        let val = $(this).val();
        let newVal = val.replace(/[ก-๙]/gi, '');
        if (val !== newVal) {
            $(this).val(newVal);
            Swal.fire('กรุณาใช้ภาษาอังกฤษ (EN) เท่านั้น', '', 'warning');
        }
        selectedRfid = newVal;
        if (!newVal) {
            $('#rfid_status').text('');
            return;
        }
        $.getJSON('../controllers/StudentRfidController.php?action=getByRfid&rfid_code=' + encodeURIComponent(newVal), function(r) {
            if (r && r.rfid_code) {
                $('#rfid_status').html('<span class="text-red-600">RFID นี้ถูกใช้แล้ว</span>');
            } else {
                $('#rfid_status').html('<span class="text-green-600">RFID นี้สามารถใช้ได้</span>');
            }
        });
    });

    // --- เชื่อม RFID กับนักเรียน ---
    $('#btnLinkRfid').click(function() {
        if (!selectedStudent) {
            Swal.fire('กรุณาเลือกนักเรียน', '', 'warning');
            return;
        }
        const rfid = $('#rfid_input').val().trim();
        if (!rfid) {
            Swal.fire('กรุณาสแกน/กรอก RFID', '', 'warning');
            $('#rfid_input').focus();
            return;
        }
        Swal.fire({
            title: 'ยืนยันเชื่อม RFID?',
            text: `RFID: ${rfid}\nกับนักเรียน: ${selectedStudent.Stu_id} ${selectedStudent.Stu_name}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('../controllers/StudentRfidController.php?action=register', {
                    stu_id: selectedStudent.Stu_id,
                    rfid_code: rfid
                }, function(res) {
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'เชื่อม RFID เรียบร้อย', 'success');
                        $('#rfid_input').val('');
                        $('#rfid_status').text('');
                        showStudentDetail(selectedStudent);
                        loadRfidTable();
                    } else {
                        Swal.fire('ผิดพลาด', res.error || 'เกิดข้อผิดพลาด', 'error');
                    }
                }, 'json');
            }
        });
    });

    // --- ตาราง RFID ที่ลงทะเบียนแล้ว ---
    function loadRfidTable() {
        $.getJSON('../controllers/StudentRfidController.php?action=list', function(data) {
            window.rfidList = data || []; // เก็บไว้ใช้เช็คใน fillStudentTable
            const $tbody = $('#rfidTable tbody');
            // Destroy DataTable ก่อน (ถ้ามี)
            if ($.fn.DataTable.isDataTable('#rfidTable')) {
                $('#rfidTable').DataTable().destroy();
            }
            $tbody.empty();
            if (!data || data.length === 0) {
                $tbody.append('<tr><td colspan="8" class="text-center text-gray-400">ไม่มีข้อมูล</td></tr>');
                return;
            }
            data.forEach(row => {
                $tbody.append(`<tr>
                    <td class="px-2 py-1 font-mono">${row.rfid_code}</td>
                    <td class="px-2 py-1">${row.stu_id||''}</td>
                    <td class="px-2 py-1">${row.stu_name||''}</td>
                    <td class="px-2 py-1">ม.${row.stu_major||''}/${row.stu_room||''}</td>
                    <td class="px-2 py-1">${row.registered_at||''}</td>
                    <td class="px-2 py-1">
                        <button class="editRfid bg-yellow-400 hover:bg-yellow-500 px-2 py-1 rounded" data-id="${row.id}" data-rfid="${row.rfid_code}">แก้ไข</button>
                    </td>
                    <td class="px-2 py-1">
                        <button class="deleteRfid bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded" data-id="${row.id}">ลบ</button>
                    </td>
                    <td class="px-2 py-1">
                        <button class="printCard bg-indigo-500 hover:bg-indigo-600 text-white px-2 py-1 rounded" 
                            data-id="${row.id}" 
                            data-stu_id="${row.stu_id||''}" 
                            data-stu_name="${row.stu_name||''}" 
                            data-stu_major="${row.stu_major||''}" 
                            data-stu_room="${row.stu_room||''}" 
                            data-rfid="${row.rfid_code||''}">
                            🖨️ พิมพ์บัตร
                        </button>
                    </td>
                </tr>`);
            });
            // สร้าง DataTable ใหม่
            $('#rfidTable').DataTable({
                destroy: true,
                searching: true,
                paging: true,
                info: true,
                order: [],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    paginate: { previous: "ก่อนหน้า", next: "ถัดไป" },
                    zeroRecords: "ไม่พบข้อมูล"
                }
            });
            // อัปเดตตารางนักเรียนให้ปุ่ม "เลือก" เปลี่ยนเป็น "ถูกเลือกไปแล้ว"
            filterStudents();
        });
    }

    // --- ลบ RFID ---
    $('#rfidTable').on('click', '.deleteRfid', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ลบ RFID นี้?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('../controllers/StudentRfidController.php?action=delete', {id}, function(res) {
                    if (res.success) {
                        Swal.fire('ลบแล้ว', '', 'success');
                        loadRfidTable();
                    } else {
                        Swal.fire('ผิดพลาด', res.error || '', 'error');
                    }
                }, 'json');
            }
        });
    });

    // --- แก้ไข RFID ---
    $('#rfidTable').on('click', '.editRfid', function() {
        const id = $(this).data('id');
        const oldRfid = $(this).data('rfid');
        Swal.fire({
            title: 'แก้ไข RFID',
            input: 'text',
            inputValue: oldRfid,
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            inputValidator: value => {
                if (!value) return 'กรุณากรอก RFID';
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.post('../controllers/StudentRfidController.php?action=update', {
                    id: id,
                    rfid_code: result.value
                }, function(res) {
                    if (res.success) {
                        Swal.fire('บันทึกแล้ว', '', 'success');
                        loadRfidTable();
                    } else {
                        Swal.fire('ผิดพลาด', res.error || '', 'error');
                    }
                }, 'json');
            }
        });
    });

    // --- ปุ่มพิมพ์บัตร ---
    $('#rfidTable').on('click', '.printCard', function() {
        const stu_id = $(this).data('stu_id');
        const stu_name = $(this).data('stu_name');
        const stu_major = $(this).data('stu_major');
        const stu_room = $(this).data('stu_room');
        const rfid = $(this).data('rfid');
        // ตัวอย่าง: เปิดหน้าพิมพ์บัตรใหม่ (คุณต้องสร้าง print_card.php เอง)
        window.open(
            'print_card.php?stu_id=' + encodeURIComponent(stu_id) +
            '&stu_name=' + encodeURIComponent(stu_name) +
            '&stu_major=' + encodeURIComponent(stu_major) +
            '&stu_room=' + encodeURIComponent(stu_room) +
            '&rfid=' + encodeURIComponent(rfid),
            '_blank'
        );
    });

    // --- ปุ่มพิมพ์บัตรทั้งห้อง ---
    $('#btnPrintRoomCards').click(function() {
        // เลือกห้องที่ต้องการพิมพ์
        Swal.fire({
            title: 'เลือกห้องที่ต้องการพิมพ์บัตร',
            html: `
                <div class="flex flex-col gap-2 items-center">
                    <select id="swal_major" class="border border-blue-200 rounded px-2 py-1">
                        <option value="">เลือกระดับชั้น</option>
                        ${majors.map(m => `<option value="${m}">${m}</option>`).join('')}
                    </select>
                    <select id="swal_room" class="border border-blue-200 rounded px-2 py-1">
                        <option value="">เลือกห้อง</option>
                        ${rooms.map(r => `<option value="${r}">${r}</option>`).join('')}
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'พิมพ์บัตร',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                const major = $('#swal_major').val();
                const room = $('#swal_room').val();
                if (!major || !room) {
                    Swal.showValidationMessage('กรุณาเลือกระดับชั้นและห้อง');
                    return false;
                }
                return {major, room};
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                const {major, room} = result.value;
                window.open(
                    'print_card_room.php?major=' + encodeURIComponent(major) +
                    '&room=' + encodeURIComponent(room),
                    '_blank'
                );
            }
        });
    });

    // --- Autofocus RFID input เมื่อโหลดหน้า ---
    setTimeout(() => { $('#rfid_input').focus(); }, 500);

    // --- โหลดข้อมูลเมื่อเริ่มต้น ---
    loadStudents();
    loadRfidTable();
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>