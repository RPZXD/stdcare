<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));


require_once('header.php');


?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Modal -->

<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="callout callout-success text-center">
                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                <h5 class="text-center text-lg">รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></h5>
                <h5 class="text-center text-lg">ปีการศึกษา <?=$pee?></h5>

                <!-- เพิ่มกล่องค้นหา -->
                <div class="row justify-content-center mb-4">
                    <div class="col-md-6">
                        <!-- <input type="text" id="studentSearch" class="form-control text-lg" placeholder="🔍 ค้นหาด้วยชื่อหรือรหัสนักเรียน..."> -->
                        <input type="text" id="studentSearch" class="form-control border border-gray-300 rounded px-3 py-2" placeholder="🔍 ค้นหาชื่อนักเรียน, รหัส, เลขที่ หรือชื่อเล่น...">
                    </div>
                </div>

                <!-- Slide Switch for Allowing Student Edit -->
                <div class="row justify-content-left mb-4">
                    <div class="col-md-6 flex items-center justify-center">
                        <span class="mr-3 text-lg">🙅‍♂️</span>
                        <label for="allowEditSwitch" class="inline-flex relative items-center cursor-pointer">
                            <input type="checkbox" id="allowEditSwitch" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-400 rounded-full peer peer-checked:bg-green-400 transition-all duration-300"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white border border-gray-300 h-6 w-6 rounded-full transition-all duration-300 peer-checked:translate-x-7 flex items-center justify-center text-xl">
                                <span id="switchEmoji" class="text-xs">🔒</span>
                            </div>
                        </label>
                        <span class="ml-3 text-lg">🙆‍♀️</span>
                        <span class="ml-4 text-base font-medium" id="editStatusText">ปิดให้นักเรียนแก้ไขข้อมูล</span>
                    </div>
                </div>
                <!-- End Slide Switch -->

                <div class="row justify-content-center">
                    <div id="showDataStudent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- การ์ดนักเรียนจะถูกแทรกที่นี่ -->
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Modal Structure for Viewing Student -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg " role="document"> <!-- Add 'modal-lg' for a larger modal -->
    <div class="modal-contentv bg-gray-100 rounded-lg shadow-md">
      <div class="modal-header">
        <h5 class="modal-title text-xl text-bold" id="studentModalLabel">ข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <!-- Student details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Structure for Editing Student -->
<div class="modal fade" id="editStudentModal"  tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document"> <!-- Add 'modal-lg' for a larger modal -->
    <div class="modal-content bg-purple-100 p-6 rounded-lg shadow-md">
      <div class="modal-header">
        <h5 class="modal-title text-lg text-bold" id="editStudentModalLabel">แก้ไขข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Edit form will be loaded here -->
      </div>
      <div class="flex justify-end">
                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-blue-400" data-dismiss="modal">ปิด</button>
                <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 ml-3" id="saveChanges">บันทึกการเปลี่ยนแปลง</button>
            </div>
    </div>
  </div>
</div>


<?php require_once('script.php');?>

<script>
$(document).ready(function() {

// --- Slide Switch Logic (Backend sync, per room) ---
function updateSwitchUI(isAllowed, by, timestamp) {
    $('#allowEditSwitch').prop('checked', isAllowed);
    $('#switchEmoji').text(isAllowed ? '🔓' : '🔒');
    $('#editStatusText').text(
        isAllowed
            ? 'เปิดให้นักเรียนแก้ไขข้อมูล'
            : 'ปิดให้นักเรียนแก้ไขข้อมูล'
    );
    if (by && timestamp) {
        $('#editStatusText').append(
            `<br><span class="text-xs text-gray-500">โดย ${by} (${timestamp})</span>`
        );
    }
}

function getRoomKey(cls, rm) {
    return cls + '-' + rm;
}

function fetchEditPermission() {
    const classValue = <?= json_encode($class) ?>;
    const roomValue = <?= json_encode($room) ?>;
    const roomKey = getRoomKey(classValue, roomValue);
    $.get('api/get_student_edit_permission.php', {
        room_key: roomKey
    }, function(res) {
        // ป้องกันการลบข้อมูลห้องอื่น: ต้องให้ backend อัปเดตเฉพาะ key ที่ส่งมา ไม่เขียนทับทั้งไฟล์
        let isAllowed = false, by = '', timestamp = '';
        try {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            isAllowed = !!data.allowEdit;
            by = data.by || '';
            timestamp = data.timestamp || '';
        } catch(e) {}
        updateSwitchUI(isAllowed, by, timestamp);
    });
}

$('#allowEditSwitch').on('change', function() {
    const classValue = <?= json_encode($class) ?>;
    const roomValue = <?= json_encode($room) ?>;
    const roomKey = getRoomKey(classValue, roomValue);
    const isAllowed = $(this).is(':checked') ? 1 : 0;
    $.post('api/set_student_edit_permission.php', {
        room_key: roomKey,
        allowEdit: isAllowed,
        by: <?= json_encode($teacher_name) ?>
    }, function(res) {
        // ป้องกันการลบข้อมูลห้องอื่น: ต้องให้ backend อัปเดตเฉพาะ key ที่ส่งมา ไม่เขียนทับทั้งไฟล์
        fetchEditPermission();
    });
});

fetchEditPermission();
// --- End Slide Switch Logic (Backend sync, per room) ---

async function loadStudentData() {
    try {
        var classValue = <?=$class?>;
        var roomValue = <?=$room?>;

        const response = await $.ajax({
            url: 'api/fetch_data_student.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue }
        });

        if (!response.success) {
            Swal.fire('🚨 ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

        const showDataStudent = $('#showDataStudent');
        showDataStudent.empty();

        if (response.data.length === 0) {
            showDataStudent.html('<p class="text-center text-xl font-semibold text-gray-600">ไม่พบข้อมูลนักเรียน</p>');
        } else {
            response.data.forEach((item, index) => {
                const studentCard = `
                    <div class="card my-4 p-4 max-w-xs bg-white rounded-lg shadow-lg border border-gray-200 transition transform hover:scale-105 student-card"
                        data-name="${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}"
                        data-id="${item.Stu_id}"
                        data-no="${item.Stu_no}"
                        data-nick="${item.Stu_nick}">
                        <img class="card-img-top rounded-lg mb-4" src="https://std.phichai.ac.th/photo/${item.Stu_picture}" alt="Student Picture" style="height: 350px; object-fit: cover;">
                        <div class="card-body space-y-3">
                            <h5 class="card-title text-base font-bold text-gray-800">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur} </h5><br>
                            <p class="card-text text-gray-600 text-left">รหัสนักเรียน: <span class="font-semibold text-blue-600">${item.Stu_id}</span></p>
                            <p class="card-text text-gray-600 text-left">เลขที่: ${item.Stu_no}</p>
                            <p class="card-text text-gray-600 text-left">ชื่อเล่น: <span class="italic text-purple-500">${item.Stu_nick}</span></p>
                            <p class="card-text text-gray-600 text-left">
                                เบอร์โทร: 
                                <a href="tel:${item.Stu_phone}" class="text-blue-500 hover:underline">${item.Stu_phone}</a>
                            </p>
                            <p class="card-text text-gray-600 text-left">
                                เบอร์ผู้ปกครอง: 
                                <a href="tel:${item.Par_phone}" class="text-blue-500 hover:underline">${item.Par_phone}</a>
                            </p>
                            <div class="flex space-x-2">
                                <button class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 btn-view" data-id="${item.Stu_id}">👀 ดู</button>
                                <button class="btn btn-warning bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 btn-edit" data-id="${item.Stu_id}">✏️ แก้ไข</button>
                                <button class="btn btn-info bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 btn-edit-profile-pic" data-id="${item.Stu_id}">🖼️ รูป</button>
                            </div>
                        </div>
                    </div>
                `;
                showDataStudent.append(studentCard);
            });
        }

    } catch (error) {
        Swal.fire('🚨 ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
        console.error(error);
    }
}

// ฟิลเตอร์การ์ดนักเรียนแบบ client-side
$('#studentSearch').on('input', function() {
    const val = $(this).val().trim().toLowerCase();
    $('.student-card').each(function() {
        const name = ($(this).attr('data-name') || '').toLowerCase();
        const id = ($(this).attr('data-id') || '').toLowerCase();
        const no = ($(this).attr('data-no') || '').toLowerCase();
        const nick = ($(this).attr('data-nick') || '').toLowerCase();
        if (
            name.includes(val) ||
            id.includes(val) ||
            no.includes(val) ||
            nick.includes(val)
        ) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Use event delegation to handle dynamically added elements
$(document).on('click', '.btn-view', function() {
    var stuId = $(this).data('id');
    // Fetch student details and show in modal
    $.ajax({
        url: 'api/view_student.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            $('#studentModal .modal-body').html(response);
            $('#studentModal').modal('show');
        }
    });
});

$(document).on('click', '.btn-edit', function() {
    var stuId = $(this).data('id');
    // Fetch student details and show in modal
    $.ajax({
        url: 'api/edit_student_form.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            $('#editStudentModal .modal-body').html(response);
            $('#editStudentModal').modal('show');
        }
    });
});

// เพิ่ม event สำหรับปุ่มแก้ไขรูปโปรไฟล์
$(document).on('click', '.btn-edit-profile-pic', function() {
    var stuId = $(this).data('id');
    Swal.fire({
        title: 'เปลี่ยนรูปโปรไฟล์',
        html: '<input type="file" id="profilePicInput" accept="image/*" class="swal2-input">',
        showCancelButton: true,
        confirmButtonText: 'อัปโหลด',
        cancelButtonText: 'ยกเลิก',
        preConfirm: () => {
            const fileInput = Swal.getPopup().querySelector('#profilePicInput');
            if (!fileInput.files[0]) {
                Swal.showValidationMessage('กรุณาเลือกรูปภาพ');
                return false;
            }
            return fileInput.files[0];
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            var formData = new FormData();
            formData.append('profile_pic', result.value);
            formData.append('Stu_id', stuId);
            $.ajax({
                url: 'api/update_profile_pic_std.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success').then(() => {
                            loadStudentData();
                        });
                    } else {
                        Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
                }
            });
        }
    });
});

$('#saveChanges').on('click', function() {
    var formData = $('#editStudentForm').serialize();
    $.ajax({
        url: 'api/update_student.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            $('#editStudentModal').modal('hide');
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
            loadStudentData(); // โหลดข้อมูลใหม่ ไม่ต้อง reload ทั้งหน้า
        },
        error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        }
    });
});

// เรียก loadStudentData() แค่ครั้งเดียว
loadStudentData(); // Load data when page is loaded
});
</script>
</body>
</html>
