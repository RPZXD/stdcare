<?php

require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php"); // (ยังใช้ UserLogin ตัวเก่า)
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)


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
                        <h5 class="m-0">จัดการข้อมูลครู</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header with-border">
                        <h3 class="card-title">สรุปข้อมูลครู 📊</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="chart-container" style="height:300px;">
                                    <canvas id="teacherStatusChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <h5 class="text-center">รวมครูทั้งหมด: <span id="totalTeachers" class="font-bold text-blue-600">0</span></h5>
                                    <p class="text-muted text-center">สถานะและบทบาทสรุปโดยรวม</p>
                                </div>

                                <!-- Summary Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200">
                                        <h6 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                                            <span class="mr-2">📚</span> กลุ่มสาระ
                                        </h6>
                                        <div class="chart-container" style="height:150px;">
                                            <canvas id="majorChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200">
                                        <h6 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                                            <span class="mr-2">👥</span> บทบาท
                                        </h6>
                                        <div class="chart-container" style="height:150px;">
                                            <canvas id="roleChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200">
                                        <h6 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                                            <span class="mr-2">🏫</span> ชั้นที่ปรึกษา
                                        </h6>
                                        <div class="chart-container" style="height:150px;">
                                            <canvas id="classChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addTeacherModal"><i class="fas fa-user-plus"></i> เพิ่มข้อมูลครู</button>
                        <style>
                            .avatar-thumb { width:48px; height:48px; object-fit:cover; border-radius:50%; border:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
                            .avatar-emoji { width:48px; height:48px; display:inline-flex; align-items:center; justify-content:center; font-size:20px; border-radius:50%; background:linear-gradient(135deg,#6c757d,#343a40); color:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
                            .btn-emoji { font-weight:600 }
                        </style>
                        <table id="teacherTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>รูป 👩‍🏫</th>
                                    <th>รหัสครู</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th>ชั้น/ห้อง 🏫</th>
                                    <th>กลุ่มสาระ</th>
                                    <th>สถานะ</th>
                                    <th>บทบาท</th>
                                    <th>จัดการ ⚙️</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">เพิ่มข้อมูลครู</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addTeacherForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>รหัสครู</label>
                                <input type="text" class="form-control" name="addTeach_id" required>
                            </div>
                            <div class="form-group">
                                <label>ชื่อ-สกุล</label>
                                <input type="text" class="form-control" name="addTeach_name" required>
                            </div>
                            <div class="form-group">
                                <label>กลุ่มสาระ</label>
                                <select class="form-control text-center" name="addTeach_major" id="addTeach_major">
                                    <option value="">-- โปรดเลือกกลุ่มสาระ --</option>
                                    <option value="ผู้อำนวยการ">ผู้อำนวยการ</option>
                                    <option value="รองผู้อำนวยการ">รองผู้อำนวยการ</option>
                                    <option value="วิทยาศาสตร์">วิทยาศาสตร์</option>
                                    <option value="ภาษาไทย">ภาษาไทย</option>
                                    <option value="ภาษาต่างประเทศ">ภาษาต่างประเทศ</option>
                                    <option value="คณิตศาสตร์">คณิตศาสตร์</option>
                                    <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                                    <option value="การงานอาชีพ">การงานอาชีพ</option>
                                    <option value="ศิลปะ">ศิลปะ</option>
                                    <option value="สุขศึกษาและพลศึกษา">สุขศึกษาและพลศึกษา</option>
                                    <option value="สังคมศึกษา ศาสนา และวัฒนธรรม">สังคมศึกษา ศาสนา และวัฒนธรรม</option>
                                    <option value="กิจกรรมพัฒนาผู้เรียน">กิจกรรมพัฒนาผู้เรียน</option>
                                    <option value="เจ้าหน้าที่ธุรการ">เจ้าหน้าที่ธุรการ</option>
                                    <option value="เจ้าหน้าที่งานการเงิน">เจ้าหน้าที่งานการเงิน</option>
                                    <option value="เจ้าหน้าที่ห้องพยาบาล">เจ้าหน้าที่ห้องพยาบาล</option>
                                    <option value="เจ้าหน้าที่โสตทัศนศึกษา">เจ้าหน้าที่โสตทัศนศึกษา</option>
                                    <option value="เจ้าหน้าที่บริหารงานทั่วไป">เจ้าหน้าที่บริหารงานทั่วไป</option>
                                    <option value="นักการภารโรง">นักการภารโรง</option>
                                    <option value="แม่บ้าน">แม่บ้าน</option>
                                    <option value="พนักงานขับรถ">พนักงานขับรถ</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>ชั้น (ระดับ)</label>
                                    <select class="form-control text-center" name="addTeach_class">
                                        <option value="">-- ระดับชั้น --</option>
                                        <option value="1">ม.1</option>
                                        <option value="2">ม.2</option>
                                        <option value="3">ม.3</option>
                                        <option value="4">ม.4</option>
                                        <option value="5">ม.5</option>
                                        <option value="6">ม.6</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ห้อง</label>
                                    <input type="text" class="form-control" name="addTeach_room" placeholder="เช่น 1, 2, A">
                                </div>
                            </div>

                            <!-- Teach_photo managed externally (filename in DB). ไม่อนุญาตให้อัปเดตรูปที่นี่ -->
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select class="form-control text-center" name="addTeach_status">
                                    <option value="1">ปกติ</option>
                                    <option value="2">ย้าย</option>
                                    <option value="3">เกษียณ</option>
                                    <option value="4">ลาออก</option>
                                    <option value="9">เสียชีวิต</option>
                                </select>
                            </div>
                             <div class="form-group">
                                <label>บทบาท</label>
                            <select class="form-control text-center" name="addrole_std" id="addrole_std">
                                <option value="">-- โปรดเลือกบทบาท --</option>
                                <option value="T">ครู</option>
                                <option value="OF">เจ้าหน้าที่</option>
                                <option value="VP">รองผู้อำนวยการ</option>
                                <option value="DIR">ผู้อำนวยการ</option>
                                <option value="ADM">Admin</option>
                            </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขข้อมูลครู</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editTeacherForm">
                        <div class="modal-body">
                            <input type="hidden" name="editTeach_id_old">
                            <div class="form-group">
                                <label>รหัสครู</label>
                                <input type="text" class="form-control" name="editTeach_id" required readonly>
                            </div>
                            <div class="form-group">
                                <label>ชื่อ-สกุล</label>
                                <input type="text" class="form-control" name="editTeach_name" required>
                            </div>
                            <div class="form-group">
                                <label>กลุ่มสาระ</label>
                                <select class="form-control text-center" name="editTeach_major" id="editTeach_major">
                                    <option value="">-- โปรดเลือกกลุ่มสาระ --</option>
                                    <option value="ผู้อำนวยการ">ผู้อำนวยการ</option>
                                    <option value="รองผู้อำนวยการ">รองผู้อำนวยการ</option>
                                    <option value="วิทยาศาสตร์">วิทยาศาสตร์</option>
                                    <option value="ภาษาไทย">ภาษาไทย</option>    
                                    <option value="ภาษาต่างประเทศ">ภาษาต่างประเทศ</option>
                                    <option value="คณิตศาสตร์">คณิตศาสตร์</option>
                                    <option value="คอมพิวเตอร์">คอมพิวเตอร์</option>
                                    <option value="การงานอาชีพ">การงานอาชีพ</option>
                                    <option value="ศิลปะ">ศิลปะ</option>
                                    <option value="สุขศึกษาและพลศึกษา">สุขศึกษาและพลศึกษา</option>
                                    <option value="สังคมศึกษา ศาสนา และวัฒนธรรม">สังคมศึกษา ศาสนา และวัฒนธรรม</option>
                                    <option value="กิจกรรมพัฒนาผู้เรียน">กิจกรรมพัฒนาผู้เรียน</option>
                                    <option value="เจ้าหน้าที่ธุรการ">เจ้าหน้าที่ธุรการ</option>
                                    <option value="เจ้าหน้าที่งานการเงิน">เจ้าหน้าที่งานการเงิน</option>
                                    <option value="เจ้าหน้าที่ห้องพยาบาล">เจ้าหน้าที่ห้องพยาบาล</option>
                                    <option value="เจ้าหน้าที่โสตทัศนศึกษา">เจ้าหน้าที่โสตทัศนศึกษา</option>
                                    <option value="เจ้าหน้าที่บริหารงานทั่วไป">เจ้าหน้าที่บริหารงานทั่วไป</option>
                                    <option value="นักการภารโรง">นักการภารโรง</option>
                                    <option value="แม่บ้าน">แม่บ้าน</option>
                                    <option value="พนักงานขับรถ">พนักงานขับรถ</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>ชั้น (ระดับ)</label>
                                    <select class="form-control text-center" name="editTeach_class">
                                        <option value="">-- ระดับชั้น --</option>
                                        <option value="1">ม.1</option>
                                        <option value="2">ม.2</option>
                                        <option value="3">ม.3</option>
                                        <option value="4">ม.4</option>
                                        <option value="5">ม.5</option>
                                        <option value="6">ม.6</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ห้อง</label>
                                    <input type="text" class="form-control" name="editTeach_room" placeholder="เช่น 1, 2, A">
                                </div>
                            </div>

                            <!-- Teach_photo managed externally (filename in DB). ไม่อนุญาตให้อัปเดตรูปที่นี่ -->
                            <div class="form-group">
                                <label>สถานะ</label>
                                <select class="form-control text-center" name="editTeach_status">
                                    <option value="1">ปกติ</option>
                                    <option value="2">ย้าย</option>
                                    <option value="3">เกษียณ</option>
                                    <option value="4">ลาออก</option>
                                    <option value="9">เสียชีวิต</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>บทบาท</label>
                                <select class="form-control text-center" name="editrole_std" id="editrole_std">
                                    <option value="">-- โปรดเลือกบทบาท --</option>
                                    <option value="T">ครู</option>
                                    <option value="OF">เจ้าหน้าที่</option>
                                    <option value="VP">รองผู้อำนวยการ</option>
                                    <option value="DIR">ผู้อำนวยการ</option>
                                    <option value="ADM">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<script>
        // Load Tailwind CSS
        const tailwindLink = document.createElement('link');
        tailwindLink.rel = 'stylesheet';
        tailwindLink.href = 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';
        document.head.appendChild(tailwindLink);

        let teacherTable;
    // (URL ใหม่ ชี้ไปที่ Controller)
    const API_URL = '../controllers/TeacherController.php';
    // Base URL สำหรับรูปครู (DB เก็บเฉพาะชื่อไฟล์)
    const PHOTO_BASE_URL = 'https://std.phichai.ac.th/teacher/uploads/phototeach/';

        document.addEventListener('DOMContentLoaded', function() {
            teacherTable = $('#teacherTable').DataTable({
                "processing": true,
                "serverSide": false, // (เราจะใช้ Client-side สำหรับ list (ตามโค้ดเดิม))
                "ajax": {
                    "url": API_URL + "?action=list", // (เรียก list)
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "Teach_photo", "render": function(data){
                        if (data) {
                            const src = PHOTO_BASE_URL + data;
                            return `<img src="${src}" class="avatar-thumb img-thumb">`;
                        }
                        // no file -> show emoji avatar
                        return `<div class="avatar-emoji" title="ไม่มีรูป">👩‍🏫</div>`;
                    }, "orderable": false },
                    { "data": "Teach_id" },
                    { "data": "Teach_name" },
                    { "data": null, "render": function(row){
                        const cls = row.Teach_class || '-';
                        const room = row.Teach_room || '-';
                        return `<span>📚 ${cls} / ${room}</span>`;
                    }},
                    { "data": "Teach_major" },
                    { 
                        "data": "Teach_status",
                        "render": function(data) {
                            switch(String(data)) {
                                case '1': return '<span class="badge badge-success">✅ ปกติ</span>';
                                case '2': return '<span class="badge badge-info">🔁 ย้าย</span>';
                                case '3': return '<span class="badge badge-secondary">🎖️ เกษียณ</span>';
                                case '4': return '<span class="badge badge-warning">⚠️ ลาออก</span>';
                                case '9': return '<span class="badge badge-dark">⚰️ เสียชีวิต</span>';
                                case '0': return '<span class="badge badge-danger">⛔ ไม่ใช้งาน</span>';
                                default: return '<span class="badge badge-light">'+(data||'-')+'</span>';
                            }
                        }
                    },
                    { "data": "role_std",
                        "render": function(data) {
                            let roleText = '';
                            switch(data) {
                                case 'T': roleText = '👩‍🏫 ครู'; break;
                                case 'OF': roleText = '🏢 เจ้าหน้าที่'; break;
                                case 'VP': roleText = '🧑‍💼 รองผอ.'; break;
                                case 'DIR': roleText = '👨‍💼 ผอ.'; break;
                                case 'ADM': roleText = '🛠️ Admin'; break;
                                default: roleText = data; 
                            }
                            return roleText;
                        }
                    },
                    { 
                        "data": "Teach_id",
                        "render": function(data) {
                            return `
                                <button title="แก้ไข ✏️" class="btn btn-warning btn-sm editTeacherBtn btn-emoji" data-id="${data}"><i class="fas fa-edit"></i></button>
                                <button title="ปิดการใช้งาน 🗑️" class="btn btn-danger btn-sm deleteTeacherBtn btn-emoji" data-id="${data}"><i class="fas fa-trash"></i></button>
                                <button title="รีเซ็ตรหัสผ่าน 🔑" class="btn btn-secondary btn-sm resetTeacherPwdBtn btn-emoji" data-id="${data}"><i class="fas fa-key"></i></button>
                            `;
                        },
                        "orderable": false
                    }
                ],
                "language": {
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    // ... (ภาษาไทยอื่นๆ) ...
                }
            });

            // Photo modal
            $('body').append(`
                <div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                                            <div class="modal-body text-center p-3">
                                                <img id="photoModalImg" src="" style="max-width:100%; height:auto; border-radius:8px; display:block; margin:0 auto;"> 
                                            </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                      </div>
                    </div>
                  </div>
                </div>
            `);

            // Click thumbnail to open modal
            $('#teacherTable').on('click', 'img.avatar-thumb', function(){
                const src = $(this).attr('src');
                $('#photoModalImg').attr('src', src);
                $('#photoModal').modal('show');
            });

            // Fetch data for chart
            async function loadChart() {
                try {
                    const res = await fetch(API_URL + '?action=list');
                    const data = await res.json();
                    const total = data.length || 0;
                    $('#totalTeachers').text(total);
                    // count status
                    const statusCounts = { '1':0, '2':0, '3':0, '4':0, '9':0, '0':0 };
                    data.forEach(r => { const s = String(r.Teach_status || '0'); statusCounts[s] = (statusCounts[s]||0) + 1; });
                    // build additional summaries: major, role, advisory class
                    const majorCounts = {};
                    const roleCounts = {};
                    const classCounts = {};
                    data.forEach(r => {
                        // major
                        const maj = (r.Teach_major && String(r.Teach_major).trim()) ? r.Teach_major : 'ไม่ระบุ';
                        majorCounts[maj] = (majorCounts[maj]||0) + 1;
                        // role
                        const role = (r.role_std && String(r.role_std).trim()) ? r.role_std : 'UNK';
                        roleCounts[role] = (roleCounts[role]||0) + 1;
                        // class (advisory level)
                        const cls = (r.Teach_class || r.Teach_class === 0) ? String(r.Teach_class) : 'ไม่ระบุ';
                        classCounts[cls] = (classCounts[cls]||0) + 1;
                    });
                    // render chart (Chart.js) showing all defined statuses
                    if (typeof Chart === 'undefined') {
                        // load Chart.js from CDN
                        await new Promise((resolve) => {
                            const s = document.createElement('script');
                            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
                            s.onload = resolve; document.head.appendChild(s);
                        });
                    }
                    const ctx = document.getElementById('teacherStatusChart').getContext('2d');
                    if (window.teacherStatusChartObj) window.teacherStatusChartObj.destroy();
                    window.teacherStatusChartObj = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['ปกติ','ย้าย','เกษียณ','ลาออก','เสียชีวิต','ไม่ใช้งาน'],
                            datasets: [{
                                data: [
                                    statusCounts['1']||0,
                                    statusCounts['2']||0,
                                    statusCounts['3']||0,
                                    statusCounts['4']||0,
                                    statusCounts['9']||0,
                                    statusCounts['0']||0
                                ],
                                backgroundColor: ['#28a745','#17a2b8','#6c757d','#ffc107','#343a40','#dc3545']
                            }]
                        },
                        options: { responsive:true, maintainAspectRatio:false }
                    });

                    // render summary charts
                    function renderSummaryChart(canvasId, countsObj, formatter, color) {
                        const ctx = document.getElementById(canvasId).getContext('2d');
                        const entries = Object.entries(countsObj).sort((a,b)=>b[1]-a[1]).slice(0, 5); // top 5
                        const labels = entries.map(([k]) => formatter ? formatter(k) : k);
                        const data = entries.map(([,v]) => v);
                        if (window[canvasId + 'Chart']) window[canvasId + 'Chart'].destroy();
                        window[canvasId + 'Chart'] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: data,
                                    backgroundColor: color,
                                    borderColor: color.replace('0.8', '1'),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { beginAtZero: true, ticks: { precision: 0 } },
                                    y: { ticks: { font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    renderSummaryChart('majorChart', majorCounts, (k)=>k, 'rgba(54, 162, 235, 0.8)');
                    renderSummaryChart('roleChart', roleCounts, (k)=>{
                        const map = { 'T':'ครู', 'OF':'เจ้าหน้าที่', 'VP':'รองผอ.', 'DIR':'ผอ.', 'ADM':'Admin', 'UNK':'อื่นๆ' };
                        return map[k]||k;
                    }, 'rgba(255, 99, 132, 0.8)');
                    renderSummaryChart('classChart', classCounts, (k)=>{
                        if (k === 'ไม่ระบุ') return 'ไม่ระบุ';
                        if (/^\d+$/.test(k)) return `ม.${k}`;
                        return k;
                    }, 'rgba(75, 192, 192, 0.8)');
                } catch (e) { console.error('Chart load error', e); }
            }
            // initial load
            loadChart();

            // refresh chart after reload (also reloads table)
            window.loadTeachers = function() {
                teacherTable.ajax.reload(null, false);
                // reload chart too
                setTimeout(loadChart, 500);
            }

            // (Event: Add Teacher)
            document.getElementById('addTeacherForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const res = await fetch(API_URL + "?action=create", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#addTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'เพิ่มข้อมูลครูเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถเพิ่มข้อมูลได้', 'error');
                }
            });

            // (Event: Show Edit Modal)
            $('#teacherTable').on('click', '.editTeacherBtn', async function() {
                const id = $(this).data('id');
                const res = await fetch(API_URL + "?action=get&id=" + id);
                const data = await res.json();
                
                if (data && data.Teach_id) {
                    $('[name="editTeach_id_old"]').val(data.Teach_id);
                    $('[name="editTeach_id"]').val(data.Teach_id);
                    $('[name="editTeach_name"]').val(data.Teach_name);
                    $('[name="editTeach_major"]').val(data.Teach_major);
                    $('[name="editTeach_class"]').val(data.Teach_class);
                    $('[name="editTeach_room"]').val(data.Teach_room);
                    $('[name="editTeach_status"]').val(data.Teach_status);
                    $('[name="editrole_std"]').val(data.role_std);
                    $('#editTeacherModal').modal('show');
                }
            });

            // (Event: Edit Teacher)
            document.getElementById('editTeacherForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // (Controller ใหม่รับ Teach_id ใน body ไม่ใช่ใน URL)
                const res = await fetch(API_URL + "?action=update", {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editTeacherModal').modal('hide');
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', result.message || 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                }
            });

            // (Event: Delete Teacher)
            $('#teacherTable').on('click', '.deleteTeacherBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'ยืนยันการลบข้อมูลครูนี้?',
                    text: "ข้อมูลจะถูกตั้งค่าเป็น 'ไม่ใช้งาน'",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย',
                    cancelButtonText: 'ยกเลิก'
                });
                if (!result.isConfirmed) return;
                
                const res = await fetch(API_URL + "?action=delete", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                });
                const response = await res.json();
                if (response.success) {
                    loadTeachers();
                    Swal.fire('สำเร็จ', 'ลบข้อมูลสำเร็จ', 'success');
                } else {
                    Swal.fire('ล้มเหลว', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                }
            });

            // (Event: Reset Password)
             $('#teacherTable').on('click', '.resetTeacherPwdBtn', async function() {
                const id = $(this).data('id');
                const result = await Swal.fire({
                    title: 'รีเซ็ตรหัสผ่าน?',
                    text: `รหัสผ่านของ ${id} จะถูกตั้งค่าเป็น ${id}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, รีเซ็ต',
                    cancelButtonText: 'ยกเลิก'
                });
                if (!result.isConfirmed) return;
                
                const res = await fetch(API_URL + "?action=resetpwd", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                });
                const response = await res.json();
                if (response.success) {
                    Swal.fire('สำเร็จ', 'รีเซ็ตรหัสผ่านเรียบร้อย', 'success');
                } else {
                    Swal.fire('ล้มเหลว', response.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้', 'error');
                }
            });

            // When opening Add modal reset form (no photo upload here)
            $('#addTeacherModal').on('show.bs.modal', function(){
                $('#addTeacherForm')[0].reset();
            });
        });
</script>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>