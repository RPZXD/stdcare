<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ครู
if (!isset($_SESSION['Teacher_login'])) {
    header("Location: ../login.php");
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);
$student = new Student($db);

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];
$teacherName = $userData['Teach_name'];
$term = $user->getTerm();
$pee = $user->getPee();

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
                            🏷️ เช็คชื่อด้วย RFID
                        </h1>
                        <div class="text-gray-600 mt-2">
                             ปีการศึกษา <?= htmlspecialchars($pee) ?> ภาคเรียนที่ <?= htmlspecialchars($term) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
        <div class="container mx-auto py-8 flex flex-col gap-6 max-w-3xl">
            <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                <div class="mb-2 font-semibold text-blue-700">สแกนบัตร RFID เพื่อเช็คชื่อ (เช้า/เย็น)</div>
                <div class="flex gap-2 items-center mb-4">
                    <input type="text" id="rfid_input" class="border border-blue-300 rounded px-3 py-2 text-lg font-mono w-72" placeholder="แตะบัตร RFID..." autocomplete="off" autofocus inputmode="latin">
                    <button id="btnClearRfid" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded text-gray-700">เคลียร์</button>
                </div>
                <div id="rfid_status" class="mt-2 text-sm text-gray-500"></div>
                <div id="student_info" class="mt-4"></div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 border border-blue-100">
                <div class="mb-2 font-semibold text-blue-700">ประวัติการเช็คชื่อวันนี้</div>
                <div class="overflow-x-auto">
                    <table id="attendanceTable" class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-indigo-500 text-white">
                                <th class="px-2 py-1">เวลา</th>
                                <th class="px-2 py-1">รหัส</th>
                                <th class="px-2 py-1">ชื่อ</th>
                                <th class="px-2 py-1">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS fill -->
                        </tbody>
                    </table>
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
    let lastStudent = null;

    // --- สแกน RFID ---
    $('#btnClearRfid').click(function() {
        $('#rfid_input').val('').focus();
        $('#rfid_status').text('');
        $('#student_info').html('');
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
        let val = $(this).val().replace(/[ก-๙]/gi, '');
        $(this).val(val);
        if (!val) {
            $('#rfid_status').text('');
            $('#student_info').html('');
            return;
        }
        // ดึงข้อมูลนักเรียนจาก RFID
        $.getJSON('../api/get_student_info.php?id=' + encodeURIComponent(val), function(stu) {
            if (!stu || !stu.Stu_id) {
                $('#rfid_status').html('<span class="text-red-600">ไม่พบข้อมูลนักเรียนสำหรับ RFID นี้</span>');
                $('#student_info').html('');
                return;
            }
            $('#rfid_status').html('<span class="text-green-600">พบข้อมูลนักเรียน</span>');
            $('#student_info').html(`
                <div class="flex gap-4 items-center mt-2">
                    <img src="../photo/${stu.Stu_picture || 'noimg.jpg'}" alt="student" class="w-20 h-20 rounded border object-cover bg-gray-100" onerror="this.src='../dist/img/logo-phicha.png'">
                    <div>
                        <div class="font-bold text-lg">${stu.Stu_id}</div>
                        <div class="text-md">${(stu.Stu_pre||'') + (stu.Stu_name||'') + ' ' + (stu.Stu_sur||'')}</div>
                        <div class="text-gray-600">ระดับชั้น: ม.${stu.Stu_major||'-'}/${stu.Stu_room||'-'}</div>
                    </div>
                </div>
            `);
            // ส่งข้อมูลไปเช็คชื่อ (API ต้องมี endpoint สำหรับบันทึกการเช็คชื่อ)
            $.post('api/checkin_rfid.php', {
                rfid_code: val,
                stu_id: stu.Stu_id,
                class: <?= json_encode($class) ?>,
                room: <?= json_encode($room) ?>,
                term: <?= json_encode($term) ?>,
                pee: <?= json_encode($pee) ?>
            }, function(res) {
                if (res.success) {
                    Swal.fire('เช็คชื่อสำเร็จ', '', 'success');
                    loadAttendanceTable();
                } else {
                    Swal.fire('ผิดพลาด', res.error || 'เกิดข้อผิดพลาด', 'error');
                }
            }, 'json');
        });
    });

    // --- โหลดประวัติการเช็คชื่อวันนี้ ---
    function loadAttendanceTable() {
        $.getJSON('api/attendance_today.php?class=<?= $class ?>&room=<?= $room ?>', function(data) {
            const $tbody = $('#attendanceTable tbody');
            if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                $('#attendanceTable').DataTable().destroy();
            }
            $tbody.empty();
            if (!data || data.length === 0) {
                $tbody.append('<tr><td colspan="4" class="text-center text-gray-400">ไม่มีข้อมูล</td></tr>');
                return;
            }
            data.forEach(row => {
                $tbody.append(`<tr>
                    <td class="px-2 py-1">${row.time || ''}</td>
                    <td class="px-2 py-1">${row.stu_id || ''}</td>
                    <td class="px-2 py-1">${row.stu_name || ''}</td>
                    <td class="px-2 py-1">${row.status || ''}</td>
                </tr>`);
            });
            $('#attendanceTable').DataTable({
                destroy: true,
                searching: false,
                paging: false,
                info: false,
                order: []
            });
        });
    }

    // --- โหลดข้อมูลเมื่อเริ่มต้น ---
    loadAttendanceTable();
    setTimeout(() => { $('#rfid_input').focus(); }, 500);
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>
