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

    <section class="content">
      <div class="container mx-auto py-6">
        <div class="flex flex-wrap justify-center">
          <div class="w-full lg:w-1/3 md:w-2/3 sm:w-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
              <div class="bg-gray-800 text-white text-center py-4">
                <h2 class="text-2xl font-bold">
                  <span>👩‍🏫</span> ข้อมูลครู
                </h2>
              </div>
              <div class="p-6">
                <div class="text-center">
                  <img class="rounded-full mx-auto h-80 w-auto"
                       src="<?php echo $setting->getImgProfile().$userData['Teach_photo'];?>"
                       alt="<?php echo $userData['Teach_name'];?>">
                </div>
                <h3 class="text-center text-xl font-semibold mt-4"><?php echo $userData['Teach_name'];?></h3>
                <p class="text-center text-gray-600"><?php echo $userData['Teach_major'];?></p>
                <ul class="mt-4 space-y-2">
                  <li class="flex justify-between">
                    <span><b>🆔 รหัสประจำตัวครู:</b></span>
                    <span><?php echo $userData['Teach_id'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🚻 เพศ:</b></span>
                    <span><?php echo $userData['Teach_sex'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🎂 วัน/เดือน/ปีเกิด:</b></span>
                    <span><?php echo Utils::convertToThaiDate($userData['Teach_birth']);?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>🏠 ที่อยู่:</b></span>
                    <span><?php echo $userData['Teach_addr'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>📞 เบอร์โทรศัพท์:</b></span>
                    <span><?php echo $userData['Teach_phone'];?></span>
                  </li>
                  <li class="flex justify-between">
                    <span><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span>
                    <span><?php echo "ม.".$userData['Teach_class']."/".$userData['Teach_room'];?></span>
                  </li>
                </ul>
                <button type="button" class="form-control block mt-6 bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600" id="editBtn">
                  ✏️ แก้ไขข้อมูล
                </button>
              </div>
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
<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTeacherModalLabel">✏️ แก้ไขข้อมูลครู</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editTeacherForm" enctype="multipart/form-data">
                    <!-- รูปภาพ -->
                    <div class="form-group text-center">
                             <img id="image-preview1" class="rounded-full mx-auto h-80 w-auto"
                                src="<?php echo $setting->getImgProfile().$userData['Teach_photo'];?>"
                                alt="<?php echo $userData['Teach_name'];?>">
                        <label for="image1" class="mt-3">ภาพประจำตัว:</label>
                        <input type="file" class="form-control" name="image1" id="image1" accept="image/*">
                    </div>

                    <!-- ชื่อ-นามสกุล -->
                    <div class="form-group">
                        <label for="Teach_name">👤 ชื่อ-นามสกุล</label>
                        <input type="text" class="form-control" id="Teach_name" name="Teach_name" value="<?php echo $userData['Teach_name']; ?>" required>
                    </div>

                    <!-- เพศ -->
                    <div class="form-group">
                        <label for="Teach_sex">🚻 เพศ</label>
                        <select name="Teach_sex" id="Teach_sex" class="form-control" required>
                            <option value="<?php echo $userData['Teach_sex']; ?>"><?php echo $userData['Teach_sex']; ?></option>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                        </select>
                    </div>

                    <!-- วัน/เดือน/ปีเกิด -->
                    <div class="form-group">
                        <label for="Teach_birth">🎂 วัน/เดือน/ปีเกิด</label>
                        <input type="date" class="form-control" id="Teach_birth" name="Teach_birth" value="<?php echo $userData['Teach_birth']; ?>" required>
                    </div>

                    <!-- ที่อยู่ -->
                    <div class="form-group">
                        <label for="Teach_addr">🏠 ที่อยู่</label>
                        <input type="text" class="form-control" id="Teach_addr" name="Teach_addr" value="<?php echo $userData['Teach_addr']; ?>" required>
                    </div>

                    <!-- กลุ่มสาระ -->
                    <div class="form-group">
                        <label for="Teach_major">🏠 กลุ่มสาระ</label>
                        <select name="Teach_major" id="Teach_major" class="form-control">
                            <option value="<?php echo $userData['Teach_major']; ?>"><?php echo $userData['Teach_major']; ?></option>
                            <option value="ภาษาไทย">ภาษาไทย</option>
                            <option value="คณิตศาสตร์">คณิตศาสตร์</option>
                            <option value="วิทยาศาสตร์">วิทยาศาสตร์และเทคโนโลยี</option>
                            <option value="สังคมศึกษาฯ">สังคมศึกษาฯ</option>
                            <option value="สุขศึกษาและพลศึกษา">สุขศึกษาและพลศึกษา</option>
                            <option value="ศิลปะ">ศิลปะ</option>
                            <option value="การงานอาชีพฯ">การงานอาชีพฯ</option>
                            <option value="ภาษาต่างประเทศ">ภาษาต่างประเทศ</option>
                            <option value="พัฒนาผู้เรียน">พัฒนาผู้เรียน</option>
                        </select>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="form-group">
                        <label for="Teach_phone">📞 เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="Teach_phone" name="Teach_phone" value="<?php echo $userData['Teach_phone']; ?>" pattern="\d{10}" maxlength="10" required>
                    </div>

                    <!-- ครูที่ปรึกษาประจำชั้น -->
                    <div class="form-group">
                      <label>👨‍🏫 ครูที่ปรึกษาประจำชั้น</label>
                      <div class="row">
                          <div class="col-auto">
                              <label for="Teach_class">ม.</label>
                              <input type="text" class="form-control" value="<?php echo $userData['Teach_class']; ?>" readonly>
                          </div>
                          <div class="col-auto">
                              <label for="Teach_room">ห้อง</label>
                              <input type="text" class="form-control" value="<?php echo $userData['Teach_room']; ?>" readonly>
                          </div>
                      </div>
                  </div>
                    <input type="hidden" name="Teach_class" id="Teach_class" value="<?php echo $userData['Teach_class']; ?>">
                    <input type="hidden" name="Teach_room" id="Teach_room" value="<?php echo $userData['Teach_room']; ?>">
                    <input type="hidden" name="Teach_id" id="Teach_id" value="<?php echo $userData['Teach_id']; ?>">
                    <input type="hidden" name="Teach_photo" id="Teach_photo" value="<?php echo $userData['Teach_photo']; ?>">
                </form>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="saveChanges">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </div>
    </div>
</div>


<?php require_once('script.php'); ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // เปิด modal เมื่อคลิกปุ่ม editBtn
    document.getElementById('editBtn').addEventListener('click', function() {
        $('#editTeacherModal').modal('show'); // ใช้ Bootstrap modal
    });

    // บันทึกการเปลี่ยนแปลง
    document.getElementById('saveChanges').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('editTeacherForm'));

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        fetch('api/update_teacher.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                    location.reload(); // โหลดหน้าใหม่เพื่อแสดงข้อมูลที่อัปเดต
                });
            } else {
                Swal.fire('ข้อผิดพลาด', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            console.error(error);
        });
    });
});
</script>
</body>
</html>
