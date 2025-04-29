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
          <h2 class="m-0">📝 รายงาน SDQ รายบุคคล</h2>
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
        <!-- SDQ Result will be rendered here -->
        <div class="card bg-white border rounded-lg shadow-md p-4 mb-4">
            <div class="card-header">
                <h3 class="card-title text-blue-500">
                    📊 รายงานคะแนน SDQ
                </h3>
            </div>
            <div class="card-body mt-4">
                <div id="sdqResultContainer"></div>
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
        let sdqResultContainer = document.getElementById('sdqResultContainer');
        if (stuId) {
            // ดึงข้อมูล SDQ
            fetch('api/ajax_get_sdq_self_result.php?stu_id=' + stuId)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        sdqResultContainer.innerHTML = '<div class="bg-red-100 text-red-700 p-4 rounded">ไม่พบข้อมูล SDQ</div>';
                        return;
                    }
                    // Render SDQ result (HTML structure based on form_sdq_result_self.php)
                    let html = '';
                    html += `<div class="flex flex-wrap -mx-4 mb-4">
                        <div class="w-full md:w-1/2 px-4">
                            <div class="card bg-emerald-500 border  rounded-lg shadow-sm p-4 mb-4">
                                <div class="card-body">
                                    <h2 class="text-lg font-semibold text-white">🎓 ข้อมูลนักเรียน</h2>
                                    <p class="text-white">
                                        ชื่อ: ${data.student_name}
                                        เลขที่: ${data.student_no}
                                        ชั้น: ม.${data.student_class}/${data.student_room}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 px-4">
                            <div class="card border rounded-lg shadow-sm p-4 mb-4">
                                <div class="card-body">
                                    <p class="text-center text-uppercase text-gray-900 mb-0">คะแนนรวม</p>
                                    <p class="text-center text-4xl text-gray-900 font-bold">${data.totalProblemScore}</p>
                                    <p class="text-center text-2xl font-bold ${
                                        data.totalProblemScore >= 20 ? 'text-red-500' : (data.totalProblemScore >= 14 ? 'text-yellow-500' : 'text-green-500')
                                    }">
                                        ${
                                            data.totalProblemScore >= 20 ? 'มีปัญหา 😥' :
                                            (data.totalProblemScore >= 14 ? 'ภาวะเสี่ยง 😐' : 'ปกติ 😄')
                                        }
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    // Category bars
                    Object.entries(data.categoryScores).forEach(([label, score]) => {
                        let percent = Math.min(100, Math.round((score / 10) * 100));
                        let status = data.categoryLevels[label];
                        let color = status.includes('ปกติ') ? 'bg-green-500' : (status.includes('เสี่ยง') ? 'bg-yellow-500' : 'bg-red-500');
                        html += `<div class="mb-4">
                            <p class="mb-0 font-semibold">${label}</p>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="${color} text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="width: ${percent}%; min-width: 120px; white-space: nowrap;">
                                    ${score} คะแนน = ${status}
                                </div>
                            </div>
                        </div>`;
                    });
                    // Memo
                    html += `<div class="bg-white border rounded-lg shadow-md p-4 mb-4">
                        <blockquote class="blockquote">
                            <p class="mb-0 text-gray-700 font-medium"><i class="fas fa-comment-dots"></i> ความคิดเห็นเพิ่มเติม</p>
                            <footer class="blockquote-footer text-gray-500">${data.memo || '-'}</footer>
                        </blockquote>
                    </div>`;
                    // Impact
                    html += `<div class="bg-white border rounded-lg shadow-md p-4">
                        <p class="mb-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> โดยรวม
                        <span class="inline-block ${data.totalProblemScore >= 20 ? 'bg-red-500' : (data.totalProblemScore >= 15 ? 'bg-yellow-500' : 'bg-green-500')} text-white text-sm font-semibold px-2 py-1 rounded">
                            ${data.totalProblemScore > 20 ? 'มีปัญหา' : (data.totalProblemScore >= 15 ? 'ภาวะเสี่ยง' : 'ไม่มีปัญหา')}
                        </span>
                        ในด้านใดด้านหนึ่ง 
                            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านอารมณ์ </span>
                            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านสมาธิ </span>
                            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านพฤติกรรม </span>
                            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ความสามารถเข้ากับผู้อื่น </span>
                        </p>
                        <p class="mb-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> ปัญหานี้รบกวนชีวิตประจำวันในด้านต่างๆ ต่อไปนี้</p>
                        <ul class="list-disc pl-5 text-gray-700">
                            <li>ความเป็นอยู่ที่บ้าน: <span class="${data.impactColors.home} text-white text-sm font-semibold px-2 py-1 rounded">${data.impactTexts.home}</span></li>
                            <li>กิจกรรมยามว่าง: <span class="${data.impactColors.leisure} text-white text-sm font-semibold px-2 py-1 rounded">${data.impactTexts.leisure}</span></li>
                            <li>การคบเพื่อน: <span class="${data.impactColors.friend} text-white text-sm font-semibold px-2 py-1 rounded">${data.impactTexts.friend}</span></li>
                            <li>การเรียนในห้องเรียน: <span class="${data.impactColors.classroom} text-white text-sm font-semibold px-2 py-1 rounded">${data.impactTexts.classroom}</span></li>
                        </ul>
                        <p class="mt-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> ปัญหานี้</p>
                        <span class="${data.impactColors.burden} text-white text-sm font-semibold px-2 py-1 rounded">
                            ${data.impactTexts.burden}
                        </span> ทำให้ตัวเองหรือครอบครัวเกิดความยุ่งยาก
                    </div>`;
                    sdqResultContainer.innerHTML = html;
                });
        } else {
            sdqResultContainer.innerHTML = '';
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
