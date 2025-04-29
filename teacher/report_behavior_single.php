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
          <h2 class="m-0">📝 รายงานคะแนนพฤติกรรมรายบุคคล</h2>
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
        <!-- Behavior Result will be rendered here -->
        <div class="card bg-white border rounded-lg shadow-md p-4 mb-4">
            <div class="card-header">
                <h3 class="card-title text-blue-500">
                    📊 รายงานคะแนนพฤติกรรมรายบุคคล
                </h3>
            </div>
            <div class="card-body mt-4">
                <div id="behaviorResultContainer"></div>
            </div>
        </div>
        <!-- ตารางรายละเอียดการหักคะแนน -->
        <div id="behaviorDetailTable"></div>
        <!-- /ตารางรายละเอียดการหักคะแนน -->

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
        let behaviorResultContainer = document.getElementById('behaviorResultContainer');
        let behaviorDetailTable = document.getElementById('behaviorDetailTable');
        if (stuId) {
            // ดึงข้อมูล Behavior result จาก API
            fetch('api/ajax_get_behavior_self_result.php?stu_id=' + stuId)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        behaviorResultContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-4 rounded">ไม่พบข้อมูลพฤติกรรม</div>';
                        behaviorDetailTable.innerHTML = '';
                        return;
                    }
                    // แสดงคะแนนพฤติกรรมคงเหลือ
                    let total_score = 100;
                    if (data.behaviorList && Array.isArray(data.behaviorList)) {
                        let sum = 0;
                        data.behaviorList.forEach(b => {
                            sum += parseInt(b.behavior_score);
                        });
                        total_score -= sum;
                    }
                    let html = '';
                    html += `<div class="mb-6">
                        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-700">คะแนนพฤติกรรมคงเหลือ</h4>
                            <span class="text-3xl font-bold text-blue-600">${total_score}</span>
                            <span class="ml-2 text-gray-500">คะแนน</span>
                        </div>
                    </div>`;
                    behaviorResultContainer.innerHTML = html;

                    // ตารางรายละเอียดการหักคะแนน
                    let tableHtml = `
                    <div class="bg-white rounded-lg shadow mt-8">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                            <span class="text-xl">📋</span>
                            <h6 class="text-base font-semibold text-gray-700">รายละเอียดการหักคะแนน</h6>
                        </div>
                        <div class="p-0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">📅 วันที่</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">📄 เรื่องที่ถูกหัก</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">📝 รายละเอียด</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-red-700 uppercase tracking-wider">🔻 คะแนน</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">👨‍🏫 ครูผู้หัก</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">`;
                    if (data.behaviorList && Array.isArray(data.behaviorList) && data.behaviorList.length > 0) {
                        data.behaviorList.forEach(b => {
                            tableHtml += `
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${thaiDate(b.behavior_date)}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${b.behavior_type}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${b.behavior_name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold ${parseInt(b.behavior_score) < 0 ? 'text-red-600' : 'text-green-600'}">${b.behavior_score}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${b.teacher_behavior || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tableHtml += `<tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-400">😃 ไม่มีข้อมูลการหักคะแนน</td>
                        </tr>`;
                    }
                    tableHtml += `</tbody></table></div></div></div>`;
                    behaviorDetailTable.innerHTML = tableHtml;
                });
        } else {
            behaviorResultContainer.innerHTML = '';
            behaviorDetailTable.innerHTML = '';
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

</body>
</html>
