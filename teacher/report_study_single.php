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

// ฟังก์ชันแปลงสถานะ
function attendance_status_text($status) {
  switch ($status) {
      case '1': return ['text' => 'มาเรียน', 'color' => 'text-green-600', 'emoji' => '✅', 'icon' => '🟢'];
      case '2': return ['text' => 'ขาด', 'color' => 'text-red-600', 'emoji' => '❌', 'icon' => '🔴'];
      case '3': return ['text' => 'สาย', 'color' => 'text-yellow-600', 'emoji' => '🕒', 'icon' => '🟡'];
      case '4': return ['text' => 'ป่วย', 'color' => 'text-blue-600', 'emoji' => '🤒', 'icon' => '🔵'];
      case '5': return ['text' => 'กิจ', 'color' => 'text-purple-600', 'emoji' => '📝', 'icon' => '🟣'];
      case '6': return ['text' => 'กิจกรรม', 'color' => 'text-pink-600', 'emoji' => '🎉', 'icon' => '🟣'];
      default:  return ['text' => 'ไม่ระบุ', 'color' => 'text-gray-500', 'emoji' => '', 'icon' => '⚪'];
  }
}

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function thai_date($strDate) {
  $strYear = date("Y", strtotime($strDate)) ;
  $strMonth = date("n", strtotime($strDate));
  $strDay = date("j", strtotime($strDate));
  $thaiMonths = [
      "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
      "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
  ];
  $strMonthThai = $thaiMonths[$strMonth];
  return "$strDay $strMonthThai $strYear";
}


require_once('header.php');



?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h2 class="m-0">📝 รายงานเวลาเรียนรายบุคคล</h2>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content py-8">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
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
                    <label class="block mb-1 font-semibold">นักเรียน</label>
                    <select id="studentSelect" name="student" class="border rounded px-3 py-2 w-80" disabled>
                        <option value="">-- เลือกนักเรียน --</option>
                        <!-- ดึงรายชื่อนักเรียนผ่าน API -->
                    </select>
                </div>
            </form>
        </div>

        <div id="studentSummary" class="hidden">
            <!-- Card -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-green-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-green-700 text-lg">มาเรียน</span>
                    <span id="term-present" class="text-2xl font-bold">0</span>
                </div>
                <div class="bg-red-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-red-700 text-lg">ขาด</span>
                    <span id="term-absent" class="text-2xl font-bold">0</span>
                </div>
                <div class="bg-yellow-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-yellow-700 text-lg">สาย</span>
                    <span id="term-late" class="text-2xl font-bold">0</span>
                </div>
                <div class="bg-blue-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-blue-700 text-lg">ป่วย</span>
                    <span id="term-sick" class="text-2xl font-bold">0</span>
                </div>
                <div class="bg-purple-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-purple-700 text-lg">กิจ</span>
                    <span id="term-activity" class="text-2xl font-bold">0</span>
                </div>
                <div class="bg-pink-100 rounded-lg p-4 flex flex-col items-center">
                    <span class="font-bold text-pink-700 text-lg">กิจกรรม</span>
                    <span id="term-event" class="text-2xl font-bold">0</span>
                </div>
            </div>
            <!-- Chart -->
            <div class="bg-white rounded-lg shadow p-4 mb-6 items-center flex flex-col">
                <canvas id="attendanceChart" width="320" height="320"></canvas>
            </div>
            <!-- Table -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="font-semibold mb-2">ประวัติการมาเรียน</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="bg-purple-50 text-gray-700">
                                <th class="px-3 py-2 border-b text-center">วันที่</th>
                                <th class="px-3 py-2 border-b text-center">สถานะ</th>
                                <th class="px-3 py-2 border-b text-center">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <!-- Data will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->

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
        let studentSelect = document.getElementById('studentSelect');
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        studentSelect.disabled = true;
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
        } else {
            roomSelect.disabled = true;
        }
    });

    // เมื่อเลือกห้อง ให้โหลดรายชื่อนักเรียนจาก API
    document.getElementById('roomSelect').addEventListener('change', function() {
        let classVal = document.getElementById('classSelect').value;
        let roomVal = this.value;
        let studentSelect = document.getElementById('studentSelect');
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        if (classVal && roomVal) {
            fetch('api/api_get_students.php?class=' + classVal + '&room=' + roomVal)
                .then(res => res.json())
                .then(data => {
                    studentSelect.disabled = false;
                    data.forEach(stu => {
                        let opt = document.createElement('option');
                        opt.value = stu.Stu_id;
                        opt.textContent = stu.Stu_no + ' ' + stu.Stu_pre + stu.Stu_name + ' ' + stu.Stu_sur;
                        studentSelect.appendChild(opt);
                    });
                });
        } else {
            studentSelect.disabled = true;
        }
    });

    // เมื่อเลือกนักเรียน
    document.getElementById('studentSelect').addEventListener('change', function() {
        let stuId = this.value;
        if (stuId) {
            fetch('api/ajax_get_student_attendance.php?stu_id=' + stuId)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('studentSummary').classList.remove('hidden');
                    // Card
                    document.getElementById('term-present').textContent = data.summary.present;
                    document.getElementById('term-absent').textContent = data.summary.absent;
                    document.getElementById('term-late').textContent = data.summary.late;
                    document.getElementById('term-sick').textContent = data.summary.sick;
                    document.getElementById('term-activity').textContent = data.summary.activity;
                    document.getElementById('term-event').textContent = data.summary.event;
                    // Table
                    let tbody = document.getElementById('attendanceTableBody');
                    tbody.innerHTML = '';
                    if (data.records.length > 0) {
                        data.records.forEach((row, i) => {
                            let rowBg = i % 2 === 0 ? 'bg-white' : 'bg-blue-50';
                            let date = row.attendance_date ? thaiDate(row.attendance_date) : '-';
                            let status = row.status_text ? `<span class="${row.status_color} font-bold">${row.status_emoji} ${row.status_text}</span>` : '-';
                            let reason = row.reason ? row.reason : '-';
                            let tr = document.createElement('tr');
                            tr.className = rowBg + " hover:bg-blue-100 transition-colors duration-150 text-[15px]";
                            tr.innerHTML = `<td class="px-3 py-2 border-b text-center">${date}</td>
                                <td class="px-3 py-2 border-b text-center">${status}</td>
                                <td class="px-3 py-2 border-b text-center">${reason}</td>`;
                            tbody.appendChild(tr);
                        });
                    } else {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `<td colspan="3" class="text-center text-gray-400 py-6 bg-white rounded-b-xl">ไม่พบข้อมูลการมาเรียน</td>`;
                        tbody.appendChild(tr);
                    }
                    // Chart
                    if (window.attChart) window.attChart.destroy();
                    let ctx = document.getElementById('attendanceChart').getContext('2d');
                    document.getElementById('attendanceChart').width = 320;
                    document.getElementById('attendanceChart').height = 320;
                    window.attChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['มาเรียน', 'ขาด', 'สาย', 'ป่วย', 'กิจ', 'กิจกรรม'],
                            datasets: [{
                                data: [
                                    data.summary.present,
                                    data.summary.absent,
                                    data.summary.late,
                                    data.summary.sick,
                                    data.summary.activity,
                                    data.summary.event
                                ],
                                backgroundColor: [
                                    '#22c55e', // green
                                    '#ef4444', // red
                                    '#eab308', // yellow
                                    '#3b82f6', // blue
                                    '#a21caf', // purple
                                    '#ec4899'  // pink
                                ]
                            }]
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'bottom' }
                            }
                        }
                    });
                });
        } else {
            document.getElementById('studentSummary').classList.add('hidden');
        }
    });

    // ฟังก์ชันแปลงวันที่เป็นภาษาไทย (เหมือนใน std_checktime.php)
    function thaiDate(strDate) {
        if (!strDate) return '-';
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
            "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        let d = new Date(strDate);
        let day = d.getDate();
        let month = months[d.getMonth() + 1];
        let year = d.getFullYear();
        if (isNaN(day) || !month || isNaN(year)) return strDate;
        return `${day} ${month} ${year}`;
    }
});
</script>
<!-- ต้องสร้างไฟล์ api_get_classes.php, api_get_rooms.php, api_get_students.php, ajax_get_student_attendance.php เพิ่มเติม -->
</body>
</html>
