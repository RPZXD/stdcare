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
<style>
.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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

    <section class="content py-8">
      <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
          <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-10">
              <h2 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 animate-bounce">
                👩‍🏫 ข้อมูลครู
              </h2>
            </div>
            <div class="flex flex-col lg:flex-row items-center lg:items-start gap-10">
              <div class="flex-shrink-0">
                <div class="relative group">
                  <img class="rounded-full h-96 w-96 object-cover border-8 border-white shadow-2xl hover:shadow-3xl hover:shadow-purple-500/50 hover:scale-105 transition-all duration-500"
                       src="<?php echo $setting->getImgProfile().$userData['Teach_photo'];?>"
                       alt="<?php echo $userData['Teach_name'];?>">
                  <div class="absolute inset-0 rounded-full bg-gradient-to-t from-purple-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white bg-opacity-90 px-4 py-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="text-sm font-semibold text-gray-700">ครู <?php echo $userData['Teach_name'];?></span>
                  </div>
                </div>
              </div>
              <div class="flex-1 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl p-8 shadow-xl">
                <h3 class="text-center text-4xl font-bold text-gray-800 mb-4"><?php echo $userData['Teach_name'];?></h3>
                <p class="text-center text-xl text-gray-600 mb-8 font-medium"><?php echo $userData['Teach_major'];?></p>
                <ul class="space-y-6">
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-blue-700 text-lg animate-pulse"><b>🆔 รหัสประจำตัวครู:</b></span>
                    <span class="text-blue-800 font-semibold"><?php echo $userData['Teach_id'];?></span>
                  </li>
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-green-50 to-green-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-green-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-green-700 text-lg animate-pulse"><b>🚻 เพศ:</b></span>
                    <span class="text-green-800 font-semibold"><?php echo $userData['Teach_sex'];?></span>
                  </li>
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-purple-700 text-lg animate-pulse"><b>🎂 วัน/เดือน/ปีเกิด:</b></span>
                    <span class="text-purple-800 font-semibold"><?php echo Utils::convertToThaiDate($userData['Teach_birth']);?></span>
                  </li>
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-yellow-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-yellow-700 text-lg animate-pulse"><b>🏠 ที่อยู่:</b></span>
                    <span class="text-yellow-800 font-semibold"><?php echo $userData['Teach_addr'];?></span>
                  </li>
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-pink-50 to-pink-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-pink-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-pink-700 text-lg animate-pulse"><b>📞 เบอร์โทรศัพท์:</b></span>
                    <span class="text-pink-800 font-semibold"><?php echo $userData['Teach_phone'];?></span>
                  </li>
                  <li class="flex justify-between items-center p-5 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl shadow-sm hover:shadow-lg hover:shadow-indigo-500/50 hover:scale-105 transition-all duration-300 animate-fadeInUp">
                    <span class="font-bold text-indigo-700 text-lg animate-pulse"><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span>
                    <span class="text-indigo-800 font-semibold"><?php echo "ม.".$userData['Teach_class']."/".$userData['Teach_room'];?></span>
                  </li>
                </ul>
                <div class="text-center mt-10">
                  <button type="button" class="bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 text-white font-bold py-4 px-10 rounded-full shadow-xl hover:shadow-2xl hover:shadow-pink-500/70 hover:scale-110 transition-all duration-300 animate-pulse" id="editBtn">
                    ✏️ แก้ไขข้อมูล
                  </button>
                </div>
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
