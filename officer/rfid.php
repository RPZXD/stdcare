<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok'); // (เพิ่ม)

$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (ไม่จำเป็นต้องสร้าง $student = new Student($db); ที่นี่)
// (สิ้นสุดการแก้ไข PHP)


// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
    $userData = $user->userData($userid);
} else {
    // (เพิ่ม) อนุญาตให้ Admin เข้าหน้านี้ได้ด้วย
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
                        <h5 class="m-0"><i class="fas fa-id-card"></i> จัดการข้อมูลบัตร RFID</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-plus"></i> นักเรียนที่ยังไม่มีบัตร (<?php echo "ปี $pee/$term";?>)</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="filterClass">ชั้น</label>
                                        <select id="filterClass" class="form-control form-control-sm"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="filterRoom">ห้อง</label>
                                        <select id="filterRoom" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                                <table id="studentTable" class="table table-bordered table-striped table-sm" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>เลขที่</th>
                                            <th>รหัส</th>
                                            <th>ชื่อ-สกุล</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                         <div class="card card-success card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-check"></i> นักเรียนที่มีบัตรแล้ว</h3>
                            </div>
                            <div class="card-body">
                                <table id="rfidTable" class="table table-bordered table-striped table-sm" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>เลขที่</th>
                                            <th>รหัส</th>
                                            <th>ชื่อ-สกุล</th>
                                            <th>RFID Code</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-csv"></i> ลงทะเบียนหลายรายการด้วย CSV</h3>
                    </div>
                    <div class="card-body">
                        <form id="csvUploadForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="csvFilterClass">ชั้น (สำหรับดาวน์โหลด)</label>
                                <select id="csvFilterClass" class="form-control"></select>
                            </div>
                            <div class="col-md-3">
                                <label for="csvFilterRoom">ห้อง (สำหรับดาวน์โหลด)</label>
                                <select id="csvFilterRoom" class="form-control"></select>
                            </div>
                            <div class="col-md-3">
                                <label class="d-block">&nbsp;</label>
                                <button type="button" id="downloadTemplateBtn" class="btn btn-secondary">
                                    <i class="fas fa-download"></i> โหลดเทมเพลต (ตามห้อง)
                                </button>
                            </div>
                            <div class="col-md-3">
                                </div>

                            <hr class="w-100 my-3">
                            
                            <div class="col-md-6">
                                <label class="form-label" for="csv_file_input">เลือกไฟล์ CSV (stu_id, rfid_code)</label>
                                <input type="file" class="form-control" id="csv_file_input" name="rfid_csv_file" accept=".csv" required>
                            </div>
                            <div class="col-md-3">
                                <label class="d-block">&nbsp;</label>
                                <button type="submit" id="uploadCsvBtn" class="btn btn-primary"><i class="fas fa-upload"></i> อัปโหลดและลงทะเบียน</button>
                            </div>
                        </form>
                        <div id="uploadResult" class="mt-3"></div>
                    </div>
                </div>

            </div>
        </section>
        
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script>
    const STUDENT_API_URL = '../controllers/StudentController.php';
    const RFID_API_URL = '../controllers/StudentRfidController.php';

    let studentTable;
    let rfidTable;

    // --- 1. โหลด Dropdown ฟิลเตอร์ ---
    // (!! KEV: แก้ไขฟังก์ชันนี้ !!)
    async function loadDropdownFilters() {
        try {
            const res = await $.ajax({
                url: STUDENT_API_URL + '?action=get_filters',
                type: 'GET',
                dataType: 'json'
            });
            
            // (กำหนดเป้าหมาย Dropdown ทั้ง 4 ช่อง)
            const $classSelTable = $('#filterClass').html('<option value="">-- เลือกชั้น --</option>');
            const $roomSelTable = $('#filterRoom').html('<option value="">-- เลือกห้อง --</option>');
            const $classSelCsv = $('#csvFilterClass').html('<option value="">-- เลือกชั้น --</option>');
            const $roomSelCsv = $('#csvFilterRoom').html('<option value="">-- เลือกห้อง --</option>');
            
            // (วนลูปใส่ข้อมูล)
            res.majors.forEach(cls => {
                $classSelTable.append(`<option value="${cls}">${cls}</option>`);
                $classSelCsv.append(`<option value="${cls}">${cls}</option>`);
            });
            res.rooms.forEach(room => {
                $roomSelTable.append(`<option value="${room}">${room}</option>`);
                $roomSelCsv.append(`<option value="${room}">${room}</option>`);
            });
            
            setupStudentDataTable();

        } catch (e) {
            console.error("Failed to load filters", e);
        }
    }

    // --- 2. ตั้งค่าตารางนักเรียน (ยังไม่มีบัตร) ---
    function setupStudentDataTable() {
        studentTable = $('#studentTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": STUDENT_API_URL + '?action=search_student', // (เรียก StudentController)
                "type": "POST",
                "data": function(d) {
                    d.major = $('#filterClass').val();
                    d.room = $('#filterRoom').val();
                }
            },
            "columns": [
                { "data": "Stu_no", "width": "10%" },
                { "data": "Stu_id", "width": "20%" },
                { "data": null, "render": (data, type, row) => `${row.Stu_name} ${row.Stu_sur}` },
                { 
                    "data": "Stu_id",
                    "render": (data) => `<button class="btn btn-primary btn-sm registerBtn" data-id="${data}">ลงทะเบียน</button>`,
                    "orderable": false, "width": "20%"
                }
            ],
            "language": { "zeroRecords": "ไม่พบนักเรียนที่ยังไม่มีบัตร", "processing": "กำลังโหลด..." }
        });

        // (Event: เมื่อเปลี่ยนฟิลเตอร์)
        $('#filterClass, #filterRoom').on('change', () => studentTable.ajax.reload());
    }

    // --- 3. ตั้งค่าตาราง RFID (มีบัตรแล้ว) ---
    function setupRfidDataTable() {
        rfidTable = $('#rfidTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": RFID_API_URL + '?action=list_ssp', // (เรียก StudentRfidController)
                "type": "POST"
            },
            "columns": [
                { "data": "stu_no", "width": "10%" },
                { "data": "stu_id", "width": "20%" },
                { "data": null, "render": (data, type, row) => `${row.stu_name} ${row.stu_sur}` },
                { "data": "rfid_code" },
                { 
                    "data": "id",
                    "render": (data, type, row) => `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${data}" data-rfid="${row.rfid_code}" data-name="${row.stu_name}">แก้ไข</button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${data}" data-name="${row.stu_name}">ลบ</button>
                    `,
                    "orderable": false, "width": "20%"
                }
            ],
             "order": [[ 0, "asc" ]], // (เรียงตามเลขที่)
             "language": { "zeroRecords": "ไม่พบนักเรียนที่มีบัตร", "processing": "กำลังโหลด..." }
        });
    }

    // --- 4. ฟังก์ชันรีเฟรชตาราง ---
    function refreshTables() {
        studentTable.ajax.reload(null, false);
        rfidTable.ajax.reload(null, false);
    }

    // --- 5. จัดการ Event การลงทะเบียน, แก้ไข, ลบ ---
    $(document).ready(function() {
        
        // (Event: ลงทะเบียน)
        $('#studentTable').on('click', '.registerBtn', async function() {
            const stuId = $(this).data('id');
            const { value: rfidCode } = await Swal.fire({
                title: 'ลงทะเบียน RFID',
                text: `ป้อนรหัส RFID สำหรับนักเรียน ID: ${stuId}`,
                input: 'text',
                inputPlaceholder: 'แตะบัตร หรือ ป้อนรหัส',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            });

            if (rfidCode) {
                $.post(RFID_API_URL + '?action=register', { stu_id: stuId, rfid_code: rfidCode }, (res) => {
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'ลงทะเบียน RFID เรียบร้อย', 'success');
                        refreshTables();
                    } else {
                        Swal.fire('ล้มเหลว', res.error, 'error');
                    }
                }, 'json');
            }
        });

        // (Event: แก้ไข)
        $('#rfidTable').on('click', '.editBtn', async function() {
            const id = $(this).data('id');
            const oldRfid = $(this).data('rfid');
            const name = $(this).data('name');
            
             const { value: newRfidCode } = await Swal.fire({
                title: `แก้ไข RFID ของ ${name}`,
                input: 'text',
                inputValue: oldRfid,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            });
            
            if (newRfidCode && newRfidCode !== oldRfid) {
                 $.post(RFID_API_URL + '?action=update', { id: id, rfid_code: newRfidCode }, (res) => {
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'แก้ไข RFID เรียบร้อย', 'success');
                        refreshTables();
                    } else {
                        Swal.fire('ล้มเหลว', res.error, 'error');
                    }
                }, 'json');
            }
        });

        // (Event: ลบ)
        $('#rfidTable').on('click', '.deleteBtn', async function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            const result = await Swal.fire({
                title: `ลบ RFID ของ ${name}?`,
                text: 'ยืนยันการลบข้อมูลนี้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            });

            if (result.isConfirmed) {
                $.post(RFID_API_URL + '?action=delete', { id: id }, (res) => {
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'ลบข้อมูลเรียบร้อย', 'success');
                        refreshTables();
                    } else {
                        Swal.fire('ล้มเหลว', res.error || 'ไม่สามารถลบได้', 'error');
                    }
                }, 'json');
            }
        });

        // --- 6. (Event: อัปโหลด CSV) ---
         $('#csvUploadForm').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $uploadBtn = $('#uploadCsvBtn');
            const $resultDiv = $('#uploadResult');
            const formData = new FormData(this);

            $uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังอัปโหลด...');
            $resultDiv.html('');

            $.ajax({
                url: STUDENT_API_URL + '?action=upload_rfid_csv', // (เรียก StudentController)
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'completed') {
                        const r = res.report;
                        let errorDetails = '';
                        if (r.errors && r.errors.length > 0) {
                            errorDetails = ` (${r.errors.slice(0, 5).join(', ')}...)`;
                        }
                        
                        // (!! KEV: อัปเดตข้อความรายงาน !!)
                        $resultDiv.html(
                            `<p class="text-green-600 font-bold">อัปโหลดสำเร็จ!</p>` +
                            `<ul>` +
                            `<li><i class="fas fa-plus text-green-500"></i> ลงทะเบียนใหม่: ${r.success} รายการ</li>` +
                            `<li><i class="fas fa-check text-blue-500"></i> อัปเดต (เขียนทับ): ${r.updated} รายการ</li>` +
                            `<li><i class="fas fa-times text-red-500"></i> ล้มเหลว (ซ้ำ/ผิดพลาด): ${r.failed} รายการ ${errorDetails}</li>` +
                            `<li><i class="fas fa-minus text-gray-400"></i> ข้าม (ข้อมูลไม่ครบ): ${r.skipped} รายการ</li>` +
                            `</ul>`
                        );
                        
                        refreshTables(); 
                    } else {
                        $resultDiv.html(`<p class="text-yellow-600">${res.message || 'ไฟล์ว่างเปล่า'}</p>`);
                    }
                    $uploadBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> อัปโหลด');
                    $('#csv_file_input').val(''); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let errorMsg = jqXHR.responseJSON ? jqXHR.responseJSON.error : 'การเชื่อมต่อล้มเหลว';
                    $resultDiv.html(`<p class="text-red-500"><strong>เกิดข้อผิดพลาด:</strong> ${errorMsg}</p>`);
                    $uploadBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> อัปโหลด');
                }
            });
        });

        //
        // !! KEV: แก้ไข Event Listener นี้ !!
        // (Event: ดาวน์โหลดเทมเพลตตามห้อง)
        //
        $('#downloadTemplateBtn').on('click', function() {
            // (1. ดึงค่าจากฟิลเตอร์ "CSV" ใหม่)
            const major = $('#csvFilterClass').val();
            const room = $('#csvFilterRoom').val();

            if (!major || !room) {
                Swal.fire('โปรดเลือก', 'กรุณาเลือก "ชั้น" และ "ห้อง" (สำหรับดาวน์โหลด)', 'info');
                return;
            }

            // (2. สร้าง URL ไปยัง StudentRfidController)
            const url = `${RFID_API_URL}?action=download_unregistered_csv&major=${encodeURIComponent(major)}&room=${encodeURIComponent(room)}`;
            
            // (3. สั่งดาวน์โหลด)
            window.location.href = url;
        });

        // --- 7. โหลดข้อมูลเมื่อเริ่มต้น ---
        loadDropdownFilters(); 
        setupRfidDataTable(); 
    });
</script>
</body>
</html>