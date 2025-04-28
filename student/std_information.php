<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// เช็คสิทธิ์แก้ไขข้อมูล
$class = $student['Stu_major'];
$room = $student['Stu_room'];
$canEdit = false;
$jsonFile = __DIR__ . '/../teacher/api/student_edit_permission.json';
if (file_exists($jsonFile)) {
    $json = json_decode(file_get_contents($jsonFile), true);
    $key = $class . '-' . $room;
    if (isset($json['permissions'][$key]) && !empty($json['permissions'][$key]['allowEdit'])) {
        $canEdit = true;
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
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ข้อมูลนักเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content ">
            <div class="container-fluid flex justify-center">
                <div class="max-w-xl w-full bg-white rounded-xl shadow-lg p-8 mt-8">
                    <div class="flex flex-col items-center">
                        <img src="<?php echo htmlspecialchars($setting->getImgProfileStudent().$student['Stu_picture']); ?>"
                            alt="User Avatar"
                            class="rounded-full w-60 h-60 object-cover shadow-lg ring-4 ring-blue-300 transition-all duration-500 hover:scale-110 hover:shadow-2xl hover:rotate-3 mb-4">
                        <h2 class="text-2xl font-bold text-blue-700 mb-2"><?php echo htmlspecialchars($student['Stu_pre'].$student['Stu_name']." ".$student['Stu_sur']); ?></h2>
                        <p class="text-gray-500 mb-4">รหัสนักเรียน: <span class="font-semibold"><?php echo htmlspecialchars($student['Stu_id']); ?></span></p>
                        <?php if ($canEdit): ?>
                            <div class="flex gap-2 mb-4">
                                <button type="button" id="btnEditInfo" class="px-4 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition">
                                    ✏️ แก้ไขข้อมูล
                                </button>
                                <button type="button" id="btnEditProfilePic"
                                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 transition"
                                    title="แก้ไขรูปโปรไฟล์">
                                    <span class="">🖼️ แก้ไขรูปโปรไฟล์</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-6 ">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🎂</span>
                            <span class="text-gray-700">วันเกิด:</span>
                            <span class="font-medium"><?php echo thai_date($student['Stu_birth']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧‍👦</span>
                            <span class="text-gray-700">ชั้น:</span>
                            <span class="font-medium"><?php echo htmlspecialchars('ม.'.$student['Stu_major'].'/'.$student['Stu_room']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🏠</span>
                            <span class="text-gray-700">ที่อยู่:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_addr']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📞</span>
                            <span class="text-gray-700">เบอร์โทร:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_phone']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧</span>
                            <span class="text-gray-700">ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_name']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📱</span>
                            <span class="text-gray-700">เบอร์ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_phone']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>

<!-- Modal สำหรับแก้ไขข้อมูล -->
<div class="modal fade" id="editInfoModal" tabindex="-1" role="dialog" aria-labelledby="editInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-white rounded-lg shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="editInfoModalLabel">แก้ไขข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="editInfoModalBody">
        <!-- ฟอร์มจะแสดงที่นี่ -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" id="saveEditInfo">บันทึก</button>
      </div>
    </div>
  </div>
</div>

<?php require_once('script.php'); ?>
<script>
$(function() {
    $('#btnEditInfo').on('click', function() {
        // ปรับเป็น AJAX POST เพื่อความปลอดภัย (หรือ GET ก็ได้ถ้าไม่มีข้อมูลสำคัญ)
        $.ajax({
            url: 'std_information_edit_form.php',
            type: 'GET',
            dataType: 'html',
            success: function(html) {
                $('#editInfoModalBody').html(html);
                $('#editInfoModal').modal('show');
            },
            error: function() {
                $('#editInfoModalBody').html('<div class="text-red-500">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>');
                $('#editInfoModal').modal('show');
            }
        });
    });

    $('#saveEditInfo').on('click', function() {
        var form = $('#editStudentForm');
        if (form.length === 0) return;
        $.ajax({
            url: 'api/update_student_info.php',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#editInfoModal').modal('hide');
                    Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
            }
        });
    });

    $('#btnEditProfilePic').on('click', function() {
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
                $.ajax({
                    url: 'api/update_profile_pic.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('สำเร็จ', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success').then(() => {
                                location.reload();
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
});
</script>
</body>
</html>
