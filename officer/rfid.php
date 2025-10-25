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

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('navbar.php'); ?>
        <?php include_once('sidebar.php'); ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">จัดการบัตร RFID นักเรียน</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">RFID</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="bg-white shadow-lg rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4 text-gray-800">ลงทะเบียน RFID</h3>
                                <form id="rfidFormDirect" class="space-y-4">
                                    <div>
                                        <label for="rfid_input" class="block text-sm font-medium text-gray-700">สแกนบัตร RFID</label>
                                        <input type="text" id="rfid_input" name="rfid_code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" id="clearBtn" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                                            ล้าง
                                        </button>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            ค้นหา
                                        </button>
                                    </div>
                                </form>
                                <div id="rfidCheckResult" class="mt-4"></div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="bg-white shadow-lg rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4 text-gray-800">เลือกนักเรียน</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                    <select id="filter_major" class="form-control">
                                        <option value="">ทุกระดับชั้น</option>
                                    </select>
                                    <select id="filter_room" class="form-control">
                                        <option value="">ทุกห้อง</option>
                                    </select>
                                </div>
                                <div class="table-responsive">
                                    <table id="studentTable" class="table table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>รหัสนักเรียน</th>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>ระดับชั้น/ห้อง</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-800">นักเรียนที่ลงทะเบียนแล้ว</h3>
                            <button id="printCardRoomBtn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-print"></i> พิมพ์บัตรตามห้อง
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="rfidTable" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>รหัส RFID</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>ระดับชั้น/ห้อง</th>
                                        <th>วันที่ลงทะเบียน</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div id="studentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h3 class="text-lg font-semibold">ลงทะเบียนให้นักเรียน</h3>
                        <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <div class="mt-4 text-center">
                        <img id="studentPhoto" src="../images/student_avatar.png" alt="Student Photo" class="w-24 h-24 rounded-full mx-auto mb-4 border border-gray-300">
                        <h4 id="studentName" class="text-xl font-semibold"></h4>
                        <p id="studentId" class="text-gray-600"></p>
                        <p id="studentClass" class="text-gray-600"></p>
                    </div>
                    <form id="rfidModalForm" class="mt-6 space-y-4">
                        <div>
                            <label for="rfid_input_modal" class="block text-sm font-medium text-gray-700">สแกนบัตร RFID</label>
                            <input type="text" id="rfid_input_modal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div class="text-right">
                            <button type="button" id="cancelModal" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                                ยกเลิก
                            </button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include_once('footer.php'); ?>
    </div>

    <script src="../assets/jquery/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/adminlte/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
    $(document).ready(function() {
        // --- ตัวแปร ---
        let majors = []; // ยังคงใช้สำหรับปุ่ม "พิมพ์บัตรตามห้อง"
        let rooms = []; // ยังคงใช้สำหรับปุ่ม "พิมพ์บัตรตามห้อง"
        let selectedStudentData = null; // เก็บข้อมูลนักเรียนที่ถูกเลือกชั่วคราว
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
            // ใช้ action ใหม่ที่สร้างใน StudentController.php
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
                pageLength: 25, // แสดงทีละ 25 แถว
                order: [
                    [2, 'asc'],
                    [1, 'asc']
                ], // เรียงตาม ห้อง -> ชื่อ
                ajax: {
                    url: '../controllers/StudentController.php?action=list_ssp',
                    type: 'POST',
                    data: function(d) {
                        // ส่งค่าจากฟิลเตอร์ที่เราสร้างเอง (ห้อง, ระดับชั้น) ไปด้วย
                        d.filter_major = $('#filter_major').val();
                        d.filter_room = $('#filter_room').val();
                    }
                },
                columns: [{
                    data: 'Stu_id'
                }, {
                    data: 'Stu_name', // ใช้ Stu_name แค่เป็นตัวแทน
                    render: function(data, type, row) {
                        return (row.Stu_pre || '') + (row.Stu_name || '') + ' ' + (row.Stu_sur || '');
                    }
                }, {
                    data: 'Stu_room', // ใช้ Stu_room แค่เป็นตัวแทน
                    render: function(data, type, row) {
                        return 'ม.' + (row.Stu_major || '') + '/' + (row.Stu_room || '');
                    }
                }, {
                    data: 'rfid_id', // คอลัมน์นี้มาจาก JOIN ใน Model
                    orderable: false,
                    render: function(data, type, row) {
                        // data คือค่าของ rfid_id (ถ้ามีค่า = ลงทะเบียนแล้ว)
                        if (data) {
                            return '<span class="bg-green-100 text-green-700 px-3 py-1 rounded font-semibold">ถูกเลือกไปแล้ว</span>';
                        } else {
                            // ส่งข้อมูลนักเรียน (row) ทั้งหมดไปในปุ่ม
                            const studentJson = encodeURIComponent(JSON.stringify(row));
                            return `<button class="selectStudent bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" data-student-json="${studentJson}">เลือก</button>`;
                        }
                    }
                }],
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
                order: [
                    [3, 'desc']
                ], // เรียงตามวันที่ลงทะเบียนล่าสุด
                ajax: {
                    url: '../controllers/StudentRfidController.php?action=list_ssp',
                    type: 'POST'
                },
                columns: [{
                    data: 'rfid_code'
                }, {
                    data: 'stu_name_full' // ใช้คอลัมน์ที่ Model สร้างให้
                }, {
                    data: 'stu_major',
                    render: function(data, type, row) {
                        return 'ม.' + (row.stu_major || '') + '/' + (row.stu_room || '');
                    }
                }, {
                    data: 'registered_at',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleString('th-TH') : '';
                    }
                }, {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class="editRfid bg-yellow-500 text-white px-2 py-1 rounded" data-id="${data}" data-rfid="${row.rfid_code}" data-name="${row.stu_name_full}">แก้ไข</button>
                                <button class="deleteRfid bg-red-500 text-white px-2 py-1 rounded" data-id="${data}" data-name="${row.stu_name_full}">ลบ</button>`;
                    }
                }],
                language: datatableLang
            });
        }

        // --- 6. Event Listeners ---

        // --- ฟังก์ชันสำหรับ Refresh ตาราง ---
        function refreshTables() {
            if (studentTableInstance) studentTableInstance.draw(false); // false = ไม่กลับไปหน้าแรก
            if (rfidTableInstance) rfidTableInstance.draw(false);
        }

        // --- เลือกนักเรียนจากตาราง ---
        $('#studentTable').on('click', '.selectStudent', function() {
            const studentJson = decodeURIComponent($(this).data('student-json'));
            const stu = JSON.parse(studentJson);

            selectedStudentData = stu; // เก็บข้อมูลนักเรียนที่เลือก
            showStudentModal(stu);
        });

        // --- แสดง Modal ---
        function showStudentModal(stu) {
            selectedStudentData = stu; // เก็บข้อมูลไว้เผื่อ submit
            $('#studentName').text((stu.Stu_pre || '') + (stu.Stu_name || '') + ' ' + (stu.Stu_sur || ''));
            $('#studentId').text(stu.Stu_id || '');
            $('#studentClass').text('ม.' + (stu.Stu_major || '') + '/' + (stu.Stu_room || ''));
            $('#studentPhoto').attr('src', stu.Stu_picture ? `../${stu.Stu_picture}` : '../images/student_avatar.png');
            $('#studentModal').fadeIn().css('display', 'flex'); // ใช้ flex เพื่อจัดกลาง
            // โฟกัสที่ input RFID
            setTimeout(() => $('#rfid_input_modal').val('').focus(), 100);
        }

        // --- ปิด Modal ---
        function closeModal() {
            $('#studentModal').fadeOut();
            selectedStudentData = null;
            $('#rfid_input_modal').val('');
        }
        $('#closeModal, #cancelModal').on('click', closeModal);

        // --- Submit Form ใน Modal ---
        $('#rfidModalForm').submit(function(e) {
            e.preventDefault();
            if (!selectedStudentData) return;

            const stu_id = selectedStudentData.Stu_id;
            const rfid_code = $('#rfid_input_modal').val();

            if (!stu_id || !rfid_code) {
                Swal.fire('ผิดพลาด!', 'ข้อมูลไม่ครบถ้วน', 'error');
                return;
            }

            $.post('../controllers/StudentRfidController.php?action=register', {
                stu_id,
                rfid_code
            }, function(res) {
                if (res.success) {
                    Swal.fire('สำเร็จ!', 'ลงทะเบียน RFID เรียบร้อยแล้ว', 'success');
                    closeModal();
                    refreshTables(); // โหลดตารางใหม่
                } else {
                    Swal.fire('ผิดพลาด!', res.error || 'ไม่สามารถลงทะเบียนได้', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('ผิดพลาด!', 'การเชื่อมต่อล้มเหลว', 'error');
            });
        });

        // --- ปุ่ม "ล้าง" ในฟอร์มหลัก ---
        $('#clearBtn').on('click', function() {
            $('#rfid_input').val('').focus();
            $('#rfidCheckResult').html('');
        });

        // --- Submit ฟอร์มหลัก (ค้นหา RFID) ---
        $('#rfidFormDirect').submit(function(e) {
            e.preventDefault();
            const rfid_code = $('#rfid_input').val();
            $('#rfidCheckResult').html('<p class="text-blue-500">กำลังตรวจสอบ...</p>');

            $.post('../controllers/StudentRfidController.php?action=getByRfid', {
                rfid_code
            }, function(res) {
                if (res.student) {
                    const stu = res.student;
                    $('#rfidCheckResult').html(`
                        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded">
                            <p class="font-semibold">RFID นี้ลงทะเบียนแล้ว:</p>
                            <p>${stu.Stu_id} - ${stu.Stu_pre || ''}${stu.Stu_name} ${stu.Stu_sur}</p>
                            <p>ม.${stu.Stu_major}/${stu.Stu_room}</p>
                        </div>
                    `);
                } else if (res.error) {
                    $('#rfidCheckResult').html(`<p class="text-red-500">${res.error}</p>`);
                } else {
                    $('#rfidCheckResult').html(`
                        <p class="text-yellow-600 font-semibold">RFID นี้ยังไม่ถูกลงทะเบียน</p>
                        <p>กรุณาเลือกนักเรียนจากตารางด้านล่างเพื่อลงทะเบียน</p>
                    `);
                }
            }, 'json').fail(function() {
                $('#rfidCheckResult').html('<p class="text-red-500">ไม่สามารถเชื่อมต่อได้</p>');
            });
        });

        // --- แก้ไข RFID (จากตาราง RFID) ---
        $('#rfidTable').on('click', '.editRfid', async function() {
            const id = $(this).data('id');
            const oldRfid = $(this).data('rfid');
            const name = $(this).data('name');

            const {
                value: newRfid
            } = await Swal.fire({
                title: `แก้ไข RFID ของ ${name}`,
                input: 'text',
                inputValue: oldRfid,
                inputLabel: 'รหัส RFID ใหม่',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกรหัส RFID!'
                    }
                }
            });

            if (newRfid && newRfid !== oldRfid) {
                $.post('../controllers/StudentRfidController.php?action=update', {
                    id: id,
                    rfid_code: newRfid
                }, function(res) {
                    if (res.success) {
                        Swal.fire('สำเร็จ!', 'อัปเดต RFID เรียบร้อยแล้ว', 'success');
                        refreshTables();
                    } else {
                        Swal.fire('ผิดพลาด!', res.error || 'ไม่สามารถอัปเดตได้', 'error');
                    }
                }, 'json');
            }
        });

        // --- ลบ RFID (จากตาราง RFID) ---
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
                    $.post('../controllers/StudentRfidController.php?action=delete', {
                        id
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('ลบแล้ว!', 'ข้อมูล RFID ถูกลบแล้ว', 'success');
                            refreshTables();
                        } else {
                            Swal.fire('ผิดพลาด!', 'ไม่สามารถลบได้', 'error');
                        }
                    }, 'json');
                }
            });
        });

        // --- พิมพ์บัตรตามห้อง ---
        $('#printCardRoomBtn').on('click', function() {
            Swal.fire({
                title: 'เลือกห้องสำหรับพิมพ์บัตร',
                html: `
                    <div class="grid grid-cols-2 gap-4 p-4">
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
                    return {
                        major,
                        room
                    };
                }
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    const {
                        major,
                        room
                    } = result.value;
                    window.open(
                        'print_card_room.php?major=' + encodeURIComponent(major) +
                        '&room=' + encodeURIComponent(room),
                        '_blank'
                    );
                }
            });
        });

        // --- 7. โหลดข้อมูลเมื่อเริ่มต้น ---
        loadDropdownFilters(); // <-- เริ่มจากโหลดฟิลเตอร์
        setupRfidDataTable(); // <-- โหลดตาราง RFID
        
        // --- Autofocus (เหมือนเดิม) ---
        setTimeout(() => { $('#rfid_input').focus(); }, 500);
    });
    </script>
</body>
</html>