<?php
session_start();


include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
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
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-lg font-semibold">⏳ รายงานการมาสาย-ขาดเรียนรายห้อง</h1>
            </div>
        </div>
    <section class="content py-8">
        <div class="container mx-auto px-4">
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow p-6 mb-6">
                <form id="filterForm" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block mb-1 font-semibold">ชั้น</label>
                        <select id="classSelect" name="class" class="border rounded px-3 py-2 w-32">
                            <option value="">-- เลือกชั้น --</option>
                            <!-- ดึงข้อมูลชั้นผ่าน API -->
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">ห้อง</label>
                        <select id="roomSelect" name="room" class="border rounded px-3 py-2 w-32" disabled>
                            <option value="">-- เลือกห้อง --</option>
                            <!-- ดึงข้อมูลห้องผ่าน API -->
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">วันที่</label>
                        <input type="date" id="dateInput" name="date" class="border rounded px-3 py-2 w-55" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">แสดงรายงาน</button>
                </form>
            </div>
            <!-- SDQ Result will be rendered here -->
            <div class="card bg-white border rounded-lg shadow-md p-4 mb-4">
                <div class="card-header">
                    <h3 class="card-title text-blue-500">
                        📊 รายงานการมาสาย-ขาดเรียนรายห้อง
                    </h3>
                </div>
                <div class="card-body mt-4">
                    <div id="reportlateContainer"></div>
                </div>
            </div>
        </div>
    </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // โหลดชั้นเรียนจาก API
    fetch('api/api_get_classes.php')
        .then(res => res.json())
        .then(data => {
            let classSelect = document.getElementById('classSelect');
            data.forEach(cls => {
                let opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = 'ม.' + cls.Stu_major;
                classSelect.appendChild(opt);
            });
        });

    // เมื่อเลือกชั้น ให้โหลดห้องจาก API
    document.getElementById('classSelect').addEventListener('change', function() {
        let classVal = this.value;
        let roomSelect = document.getElementById('roomSelect');
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        roomSelect.disabled = true;
        if (classVal) {
            fetch('api/api_get_rooms.php?class=' + classVal)
                .then(res => res.json())
                .then(data => {
                    roomSelect.disabled = false;
                    data.forEach(room => {
                        let opt = document.createElement('option');
                        opt.value = room.Stu_room;
                        opt.textContent = 'ห้อง ' + room.Stu_room;
                        roomSelect.appendChild(opt);
                    });
                });
        }
    });

    // เมื่อส่งฟอร์ม ให้แสดงรายงาน (mockup)
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let classVal = document.getElementById('classSelect').value;
        let roomVal = document.getElementById('roomSelect').value;
        let dateVal = document.getElementById('dateInput').value;
        let container = document.getElementById('reportlateContainer');
        if (!classVal || !roomVal) {
            container.innerHTML = '<div class="text-red-500">กรุณาเลือกชั้นและห้องเรียน</div>';
            return;
        }
        container.innerHTML = '<div class="text-gray-500">กำลังโหลดข้อมูล...</div>';
        fetch(`api/api_get_late_report.php?class=${classVal}&room=${roomVal}&date=${dateVal}`)
            .then(res => res.json())
            .then(data => {
                if (!data || !Array.isArray(data) || data.length === 0) {
                    container.innerHTML = '<div class="text-red-500">ไม่พบข้อมูลนักเรียนสำหรับวันที่เลือก</div>';
                    return;
                }
                let rows = data.map((stu, idx) => `
                    <tr>
                        <td class="border px-4 py-2 text-center">${stu.Stu_no}</td>
                        <td class="border px-4 py-2 text-center">${stu.Stu_id}</td>
                        <td class="border px-4 py-2">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</td>
                        <td class="border px-4 py-2 text-center">ม.${stu.Stu_major}/${stu.Stu_room}</td>
                        <td class="border px-4 py-2 text-center">${stu.attendance_status_info.emoji} <span class="${stu.attendance_status_info.color}">${stu.attendance_status_info.text}</span></td>
                        <td class="border px-4 py-2 text-center">${stu.parent_tel ?? '-'}</td>
                    </tr>
                `).join('');
                container.innerHTML = `
                    <div class="mb-2 font-semibold">รายงานห้อง ม.${classVal} ห้อง ${roomVal} วันที่ ${thaiDate(dateVal)}</div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded shadow">
                        <thead class="bg-indigo-500 text-white">
                            <tr>
                                <th class="px-4 py-2 border text-center">🔢 เลขที่</th>
                                <th class="px-4 py-2 border text-center">🆔 เลขประจำตัว</th>
                                <th class="px-4 py-2 border text-center">👨‍🎓 ชื่อนักเรียน</th>
                                <th class="px-4 py-2 border text-center">🏫 ห้อง</th>
                                <th class="px-4 py-2 border text-center">📏 สถานะ</th>
                                <th class="px-4 py-2 border text-center">📱 เบอร์ผู้ปกครอง</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows}
                        </tbody>
                    </table>
                    </div>
                `;
            })
            .catch(() => {
                container.innerHTML = '<div class="text-red-500">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
            });
    });

    // ฟังก์ชันแปลงวันที่เป็นภาษาไทย (เหมือนใน std_checktime.php)
    function thaiDate(strDate) {
        if (!strDate) return '-';
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
            "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        let d = new Date(strDate);
        let day = d.getDate();
        let month = months[d.getMonth() + 1];
        let year = d.getFullYear() + 543; // เพิ่ม 543 เพื่อแปลงเป็นปีไทย
        if (isNaN(day) || !month || isNaN(year)) return strDate;
        return `${day} ${month} ${year}`;
    }
});
</script>
</body>
</html>
