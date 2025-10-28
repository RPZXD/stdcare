<?php
// เปลี่ยนไปใช้คลาสเชื่อมต่อฐานข้อมูลตัวใหม่
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;

include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection (ใช้คลาสใหม่)
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();

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

require_once('header.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-history"></i> ประวัติการใช้งาน (Logs)</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active">Logs</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> ตัวกรองข้อมูล</h3>
                    </div>
                    <div class="card-body">
                        <form id="logFilterForm" class="row g-3">
                            <div class="col-md-2">
                                <label for="user_id" class="form-label">User ID</label>
                                <input type="text" class="form-control" id="user_id" placeholder="รหัสผู้ใช้">
                            </div>
                            <div class="col-md-2">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" placeholder="บทบาท">
                            </div>
                            <div class="col-md-2">
                                <label for="action" class="form-label">Action</label>
                                <select id="action" class="form-control">
                                    <option value="">ทั้งหมด</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" class="form-control">
                                    <option value="">ทั้งหมด</option>
                                    <option value="success">Success</option>
                                    <option value="fail">Fail</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">จากวันที่</label>
                                <input type="date" class="form-control" id="date_from">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">ถึงวันที่</label>
                                <input type="date" class="form-control" id="date_to">
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> ค้นหา</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <table id="logTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th> <th>วันที่-เวลา</th>
                                    <th>ผู้ใช้งาน</th>
                                    <th>บทบาท</th>
                                    <th>การกระทำ</th>
                                    <th>สถานะ</th>
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
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    
    // !! KEV: ฟังก์ชันสำหรับสร้าง Child Row (รายละเอียดที่ซ่อนไว้)
    function formatDetails(d) {
        // 'd' คือ object ข้อมูลสำหรับแถวนั้น (มาจาก log_data.php)
        return '<table class="table table-sm table-borderless" style="background-color: #f9f9f9;">' +
            '<tr>' +
                '<td style="width:15%; font-weight:bold; text-align:right; padding-right:10px;">Message:</td>' +
                '<td>' + (d.message || '-') + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td style="width:15%; font-weight:bold; text-align:right; padding-right:10px;">IP Address:</td>' +
                '<td>' + (d.ip || '-') + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td style="width:15%; font-weight:bold; text-align:right; padding-right:10px;">User Agent:</td>' +
                '<td>' + (d.user_agent || '-') + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td style="width:15%; font-weight:bold; text-align:right; padding-right:10px;">URL:</td>' +
                '<td>' + (d.url || '-') + '</td>' +
            '</tr>' +
        '</table>';
    }

    var logTable = $('#logTable').DataTable({
        "processing": true,
        "serverSide": true, // !! KEV: เปิด Server-side processing
        "ajax": {
            "url": "api/log_data.php", // !! KEV: ชี้ไปที่ไฟล์ใหม่
            "type": "GET",
            "data": function(d) {
                // ส่งค่าจากฟอร์มฟิลเตอร์ไปด้วย
                d.user_id = $('#user_id').val();
                d.role = $('#role').val();
                d.action = $('#action').val();
                d.status = $('#status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        "columns": [
            // !! KEV: คอลัมน์ที่ 0 สำหรับปุ่ม +
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '<i class="fas fa-plus-circle text-green-500 cursor-pointer"></i>',
                "width": "5%"
            },
            // คอลัมน์ที่เหลือ
            { "data": "datetime", "width": "20%" },
            { "data": "userId" },
            { "data": "role" },
            { "data": "action" },
            { "data": "status" }
        ],
        "order": [[1, 'desc']], // !! KEV: เรียงลำดับตามวันที่-เวลา (คอลัมน์ที่ 1) ล่าสุดก่อน
        "language": {
            "zeroRecords": "ไม่พบข้อมูล log",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "ไม่มีข้อมูล",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "search": "ค้นหาด่วน:",
            "processing": "กำลังประมวลผล...",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            }
        }
    });

    // !! KEV: Event listener สำหรับฟอร์มฟิลเตอร์
    $('#logFilterForm').on('submit', function(e) {
        e.preventDefault();
        logTable.ajax.reload(); // สั่งให้ DataTables โหลดข้อมูลใหม่ (โดยใช้ค่าฟิลเตอร์)
    });

    // !! KEV: Event listener สำหรับปุ่ม + / -
    $('#logTable tbody').on('click', 'td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = logTable.row(tr);
        var icon = $(this).find('i');

        if (row.child.isShown()) {
            // แถวนี้เปิดอยู่ -> ปิด
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-minus-circle text-red-500').addClass('fa-plus-circle text-green-500');
        } else {
            // แถวนี้ปิดอยู่ -> เปิด
            // (ข้อมูล 'message', 'ip' ฯลฯ มาจาก 'row.data()' ซึ่งถูกส่งมาจาก log_data.php)
            row.child(formatDetails(row.data())).show();
            tr.addClass('shown');
            icon.removeClass('fa-plus-circle text-green-500').addClass('fa-minus-circle text-red-500');
        }
    });
});
</script>
</body>
</html>