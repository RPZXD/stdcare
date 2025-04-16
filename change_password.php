<?php
require_once('header.php');
require_once('class/Utils.php');
require_once('config/Setting.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config/Database.php");
include_once("class/UserLogin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    // Validate password length, complexity, and no Thai characters
    if (strlen($newPassword) < 6 || !preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || preg_match('/[ก-๙]/u', $newPassword)) {
        $sw2 = new SweetAlert2(
            'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร ต้องประกอบด้วยตัวเลขและตัวอักษร และห้ามมีภาษาไทย',
            'error',
            'change_password.php' // Redirect URL
        );
        $sw2->renderAlert();
        exit();
    }

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $db = new Database("phichaia_student");
        $conn = $db->getConnection();
        $user = new UserLogin($conn);

        $query = "UPDATE teacher SET password = :password WHERE Teach_id = :user";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":user", $_SESSION['user']);
        $stmt->execute();

        $sw2 = new SweetAlert2(
            'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว',
            'success',
            'login.php' // Redirect URL
        );
        $sw2->renderAlert();
    } else {
        $sw2 = new SweetAlert2(
            'รหัสผ่านไม่ตรงกัน',
            'error',
            'change_password.php' // Redirect URL
        );
        $sw2->renderAlert();
    }
}
?>
<body class="hold-transition sidebar-mini layout-fixed">
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

        <div class="container-fluid">

            <div class="row flex items-center justify-center bg-gray-100">
                <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">กรุณาเปลี่ยนรหัสผ่าน <?= $_SESSION['user'] ?></h2>
                    <form method="POST" action="change_password.php" class="space-y-4">
                        <div>
                            <label for="new_password" class="block text-gray-600 mb-1">รหัสผ่านใหม่:</label>
                            <input type="password" name="new_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-gray-600 mb-1">ยืนยันรหัสผ่าน:</label>
                            <input type="password" name="confirm_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition duration-300">
                            เปลี่ยนรหัสผ่าน
                        </button>
                    </form>
                </div>
            </div>
      
      </div>
  </div><!-- /.container-fluid -->
  
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->


<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<script>
const passwordInput = document.getElementById('password');
const togglePasswordButton = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');

togglePasswordButton.addEventListener('click', () => {
  const isPassword = passwordInput.type === 'password';
  passwordInput.type = isPassword ? 'text' : 'password';
  eyeIcon.setAttribute('d', isPassword
      ? 'M12 4.5c-4.477 0-8.268 2.943-9.542 7 .274.837.68 1.613 1.196 2.296M15.536 15.536A9.953 9.953 0 0112 17c-4.477 0 8.268-2.943 9.542-7a9.953 9.953 0 01-1.196-2.296M9.88 9.88a3 3 0 014.24 4.24' // Eye open path
      : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.837-.68 1.613-1.196 2.296M15.536 15.536A9.953 9.953 0 0112 17c-4.477 0-8.268-2.943-9.542-7a9.953 9.953 0 011.196-2.296M9.88 9.88a3 3 0 014.24 4.24' // Eye closed path
  );
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>