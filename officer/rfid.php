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
                <!-- ซ้าย: สแกน/อ่าน RFID + รายการ RFID -->
                <div class="flex-1 flex flex-col gap-6">
                    <!-- 1. ช่องสำหรับสแกน/อ่านหมายเลข RFID -->
                    <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                        <div class="mb-2 font-semibold text-blue-700">สแกน/อ่านหมายเลข RFID</div>
                        <div class="flex gap-2 items-center">
                            <input type="text" id="rfid_input" class="border border-blue-300 rounded px-3 py-2 text-lg font-mono w-72" placeholder="แตะบัตร RFID..." autocomplete="off" autofocus inputmode="latin">
                            <button id="btnClearRfid" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded text-gray-700">เคลียร์</button>
                        </div>
                        <div id="rfid_status" class="mt-2 text-sm text-gray-500"></div>
                    </div>
                    <!-- 5. รายการ RFID ที่ลงทะเบียนแล้ว -->
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
                                    <!-- JS fill -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- ขวา: ค้นหานักเรียน -->
                <div class="flex-1 flex flex-col gap-6">
                    <!-- 2. ช่องค้นหานักเรียน -->
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
                                    <!-- JS fill -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- 3. ข้อมูลนักเรียนที่เลือก -->
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
                    <!-- 4. ปุ่มเชื่อม RFID กับนักเรียน -->
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
    // let students = []; // REMOVED
    // let filteredStudents = []; // REMOVED
    let majors = []; // ยังคงใช้สำหรับปุ่ม "พิมพ์บัตรตามห้อง"
    let rooms = []; // ยังคงใช้สำหรับปุ่ม "พิมพ์บัตรตามห้อง"
    let selectedStudentData = null; // ยังคงใช้
    let rfidTableInstance = null; // เก็บ instance ตาราง RFID
    let studentTableInstance = null; // เก็บ instance ตารางนักเรียน

    // --- ภาษาไทยสำหรับ DataTables ---
    const datatableLang = {
        search: "ค้นหา:",
        lengthMenu: "แสดง _MENU_ รายการ",
        info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
        infoEmpty: "ไม่มีข้อมูล",
        infoFiltered: "(กรองจาก _MAX_ รายการทั้งหมด)",
        paginate: {
            previous: "ก่อนหน้า",
            next: "ถัดไป"
        },
        zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
        processing: "กำลังประมวลผล..."
    };

    // --- 1. โหลดฟิลเตอร์ Dropdown (ระดับชั้น/ห้อง) ---
    function loadDropdownFilters() {
        $.getJSON('../controllers/StudentController.php?action=get_filters', function(data) {
            majors = data.majors || [];
            rooms = data.rooms || [];
            
            const $major = $('#filter_major');
            const $room = $('#filter_room');
            $major.empty().append('<option value="">ทุกระดับชั้น</option>');
            majors.forEach(m => $major.append(`<option value="${m}">${m}</option>`));
            $room.empty().append('<option value="">ทุกห้อง</option>');
            rooms.forEach(r => $room.append(`<option value="${r}">${r}</option>`));
            
            // --- 2. หลังจากโหลดฟิลเตอร์เสร็จ ค่อยเริ่มโหลดตารางนักเรียน ---
            setupStudentDataTable();
        }).fail(function() {
            console.error("Failed to load filters");
            // แม้ว่าจะล้มเหลว ก็ยังต้องโหลดตาราง
            setupStudentDataTable();
        });
    }

    // --- 3. ตั้งค่าตารางนักเรียน (Server-Side) ---
    function setupStudentDataTable() {
        if (studentTableInstance) {
            studentTableInstance.destroy();
        }
        
        studentTableInstance = $('#studentTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true, // เปิดการค้นหาของ DataTables (จะส่งไปให้ Server)
            paging: true,
            info: true,
            pageLength: 25, // แสดงทีละน้อยลง
            order: [[2, 'asc'], [1, 'asc']], // เรียงตาม ห้อง -> ชื่อ
            ajax: {
                url: '../controllers/StudentController.php?action=list_ssp',
                type: 'POST',
                data: function(d) {
                    // ส่งค่าจากฟิลเตอร์ที่เราสร้างเอง (ห้อง, ระดับชั้น) ไปด้วย
                    d.filter_major = $('#filter_major').val();
                    d.filter_room = $('#filter_room').val();
                }
            },
            columns: [
                { data: 'Stu_id' },
                { 
                    data: 'Stu_name', // เราใช้ Stu_name แค่เป็นตัวแทน
                    render: function(data, type, row) {
                        return (row.Stu_pre || '') + (row.Stu_name || '') + ' ' + (row.Stu_sur || '');
                    }
                },
                { 
                    data: 'Stu_room', // เราใช้ Stu_room แค่เป็นตัวแทน
                    render: function(data, type, row) {
                        return 'ม.' + (row.Stu_major || '') + '/' + (row.Stu_room || '');
                    }
                },
                {
                    data: 'rfid_id', // คอลัมน์นี้มาจาก JOIN
                    orderable: false, // คอลัมน์ปุ่มไม่ต้องเรียงลำดับ
                    render: function(data, type, row) {
                        // data คือค่าของ rfid_id
                        if (data) { // ถ้า data ไม่ใช่ null, แสดงว่ามี rfid_id
                            return '<span class="bg-green-100 text-green-700 px-3 py-1 rounded font-semibold">ถูกเลือกไปแล้ว</span>';
                        } else {
                            // **สำคัญมาก**: ส่งข้อมูลนักเรียน (row) ทั้งหมดไปในปุ่ม
                            const studentJson = encodeURIComponent(JSON.stringify(row));
                            return `<button class="selectStudent bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" data-student-json="${studentJson}">เลือก</button>`;
                        }
                    }
                }
            ],
            language: datatableLang
        });

        // --- 4. เมื่อมีการเปลี่ยนแปลงฟิลเตอร์ ให้โหลดตารางใหม่ ---
        $('#filter_major, #filter_room').on('change', function() {
            studentTableInstance.draw(); // สั่งให้ DataTables โหลดข้อมูลใหม่ (มันจะเรียก ajax)
        });
    }
    
    // --- 5. ตั้งค่าตาราง RFID (Server-Side) ---
    function setupRfidDataTable() {
        if (rfidTableInstance) {
            rfidTableInstance.destroy();
        }
        
        rfidTableInstance = $('#rfidTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging: true,
            pageLength: 25,
            order: [[3, 'desc']], // เรียงตามวันที่ลงทะเบียนล่าสุด
            ajax: {
                url: '../controllers/StudentRfidController.php?action=list_ssp',
                type: 'POST'
            },
            columns: [
                { data: 'rfid_code' },
                { data: 'stu_name_full' }, // ใช้คอลัมน์ที่ Model สร้างให้
                { 
                    data: 'stu_major',
                    render: function(data, type, row) {
                        return 'ม.' + (row.stu_major || '') + '/' + (row.stu_room || '');
                    }
                },
                { 
                    data: 'registered_at',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleString('th-TH') : '';
                    }
                },
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class="editRfid bg-yellow-500 text-white px-2 py-1 rounded" data-id="${data}" data-rfid="${row.rfid_code}" data-name="${row.stu_name_full}">แก้ไข</button>
                                <button class="deleteRfid bg-red-500 text-white px-2 py-1 rounded" data-id="${data}" data-name="${row.stu_name_full}">ลบ</button>`;
                    }
                }
            ],
            language: datatableLang
        });
    }

    // --- 6. Event Listeners (ส่วนใหญ่เหมือนเดิม) ---
    
    // --- เลือกนักเรียน ---
    $('#studentTable').on('click', '.selectStudent', function() {
        // --- CHANGED: ดึงข้อมูลจาก data attribute ---
        const studentJson = decodeURIComponent($(this).data('student-json'));
        const stu = JSON.parse(studentJson);
        
        selectedStudentData = stu; // เก็บข้อมูลนักเรียนที่เลือก
        showStudentModal(stu);
    });

    // --- แสดง Modal (เหมือนเดิม) ---
    function showStudentModal(stu) {
        $('#studentName').text((stu.Stu_pre || '') + (stu.Stu_name || '') + ' ' + (stu.Stu_sur || ''));
        $('#studentId').text(stu.Stu_id || '');
        $('#studentClass').text('ม.' + (stu.Stu_major || '') + '/' + (stu.Stu_room || ''));
        $('#studentPhoto').attr('src', stu.Stu_picture ? `../${stu.Stu_picture}` : '../images/student_avatar.png');
        $('#studentModal').fadeIn();
        // โฟกัสที่ input RFID
        setTimeout(() => $('#rfid_input_modal').val('').focus(), 100);
    }
    
    // ... (โค้ด closeModal, rfidForm, rfidModalForm, editRfid, deleteRfid, printCardRoomBtn 
    //      ยังคงเหมือนเดิม ไม่ต้องแก้ไข) ...
    
    // --- ฟังก์ชันสำหรับ Refresh ตาราง ---
    function refreshTables() {
        if(studentTableInstance) studentTableInstance.draw(false); // false = ไม่กลับไปหน้าแรก
        if(rfidTableInstance) rfidTableInstance.draw(false);
    }
    
    // (ปรับปรุงฟอร์ม register ให้เรียก refreshTables)
    $('#rfidForm').submit(function(e) {
        e.preventDefault();
        const stu_id = $('#studentId').text();
        const rfid_code = $('#rfid_input_modal').val();
        
        $.post('../controllers/StudentRfidController.php?action=register', { stu_id, rfid_code }, function(res) {
            if (res.success) {
                Swal.fire('สำเร็จ!', 'ลงทะเบียน RFID เรียบร้อยแล้ว', 'success');
                closeModal();
                refreshTables(); // --- CHANGED
            } else {
                Swal.fire('ผิดพลาด!', res.error || 'ไม่สามารถลงทะเบียนได้', 'error');
            }
        }, 'json').fail(function() {
             Swal.fire('ผิดพลาด!', 'การเชื่อมต่อล้มเหลว', 'error');
        });
    });
    
    // (ปรับปรุงฟอร์ม delete ให้เรียก refreshTables)
    $('#rfidTable').on('click', '.deleteRfid', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: `ลบ RFID ของ ${name}?`,
            text: "คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/StudentRfidController.php?action=delete', { id }, function(res) {
                    if (res.success) {
                        Swal.fire('ลบแล้ว!', 'ข้อมูล RFID ถูกลบแล้ว', 'success');
                        refreshTables(); // --- CHANGED
                    } else {
                        Swal.fire('ผิดพลาด!', 'ไม่สามารถลบได้', 'error');
                    }
                }, 'json');
            }
        });
    });
    
    // ... (ส่วน edit ก็ควรเรียก refreshTables() เมื่อสำเร็จ) ...


    // --- 7. โหลดข้อมูลเมื่อเริ่มต้น ---
    loadDropdownFilters(); // <-- CHANGED: เริ่มจากโหลดฟิลเตอร์
    setupRfidDataTable(); // <-- CHANGED: เรียกฟังก์ชัน SSP ใหม่
    
    // --- Autofocus (เหมือนเดิม) ---
    setTimeout(() => { $('#rfid_input').focus(); }, 500);
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>
