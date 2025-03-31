<?php
ob_start(); // Start output buffering
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("class/Utils.php");
$bs = new Bootstrap();

function redirectUser() {
    $roles = [
        'Teacher_login' => 'teacher/index.php',
        'Director_login' => 'director/index.php',
        'Group_leader_login' => 'groupleader/index.php',
        'Officer_login' => 'Officer/index.php',
        'Admin_login' => 'admin/index.php',
        'Student_login' => 'student/index.php'
    ];

    foreach ($roles as $sessionKey => $redirectPath) {
        if (isset($_SESSION[$sessionKey])) {
            header("Location: $redirectPath");
            exit(); // Prevent further execution
        }
    }
}

redirectUser(); // Ensure this is called before any HTML output
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

              <?php 

                include_once("config/Database.php");
                include_once("class/UserLogin.php");

                $studentDb = new Database("phichaia_student");
                $studentConn = $studentDb->getConnection();

                $user = new UserLogin($studentConn);
                $bs = new Bootstrap();

                if (isset($_POST['signin'])) {
                    $username = filter_input(INPUT_POST, 'txt_username_email', FILTER_SANITIZE_STRING);
                    $password = filter_input(INPUT_POST, 'txt_password', FILTER_SANITIZE_STRING);

                    $allowed_roles = ['Admin', 'Teacher', 'Officer'];
                    $role = filter_input(INPUT_POST, 'txt_role', FILTER_SANITIZE_STRING);
                    
                    if (!in_array($role, $allowed_roles)) {
                        $role = 'Teacher'; // หรือค่าที่ปลอดภัยอื่นๆ
                    }

                    $user->setUsername($username);
                    $user->setPassword($password);

                    if ($user->userNotExists()) {
                        $sw2 = new SweetAlert2(
                            'ไม่มีชื่อผู้ใช้นี้',
                            'error',
                            'login.php' // Redirect URL
                        );
                        $sw2->renderAlert();
                    } else {
                        if ($user->verifyPassword()) {
                            $userRole = $user->getUserRole();
                            $allowedUserRoles = [
                                'Teacher' => ['T', 'ADM', 'VP', 'OF', 'DIR'],
                                'Officer' => ['ADM', 'OF'],
                                'Admin' => ['ADM']
                            ];
                            
                            if (in_array($userRole, $allowedUserRoles[$role])) {
                                $_SESSION[$role . '_login'] = $_SESSION['user'];

                                $sw2 = new SweetAlert2(
                                    'ลงชื่อเข้าสู่ระบบเรียบร้อย',
                                    'success',
                                    strtolower($role) . '/index.php' // Redirect URL
                                );
                                $sw2->renderAlert();
                            } else {
                                $sw2 = new SweetAlert2(
                                    'บทบาทผู้ใช้ไม่ถูกต้อง',
                                    'error',
                                    'login.php' // Redirect URL
                                );
                                $sw2->renderAlert();
                            } 
                        } else {
                            $sw2 = new SweetAlert2(
                                'พาสเวิร์ดไม่ถูกต้อง',
                                'error',
                                'login.php' // Redirect URL
                            );
                            $sw2->renderAlert();
                        }
                    }
                }
                ?>


                          
                    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
                        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">เข้าสู่ระบบ</h2>

                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="space-y-4">
                            
                            <div>
                                <label class="block text-gray-600 mb-1">ชื่อผู้ใช้งาน</label>
                                <input type="text" name="txt_username_email" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="กรุณากรอกชื่อผู้ใช้งาน...">
                            </div>

                            <div>
                                <label class="block text-gray-600 mb-1">รหัสผ่าน</label>
                                <div class="relative">
                                    <input type="password" id="password" name="txt_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="กรุณากรอกรหัสผ่าน...">
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.837-.68 1.613-1.196 2.296M15.536 15.536A9.953 9.953 0 0112 17c-4.477 0-8.268-2.943-9.542-7a9.953 9.953 0 011.196-2.296M9.88 9.88a3 3 0 014.24 4.24" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 mb-1">ประเภทผู้ใช้</label>
                                <select name="txt_role" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-center">
                                    <option value="Teacher" selected>ครู</option>
                                    <option value="Officer">เจ้าหน้าที่</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>

                            <button type="submit" name="signin" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition duration-300">
                                เข้าสู่ระบบ
                            </button>
                        </form>
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
<?php ob_end_flush(); // Flush the output buffer ?>
</body>
</html>