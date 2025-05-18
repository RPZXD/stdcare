<?php
ob_start(); // Start output buffering
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("class/Utils.php");
include_once("class/Logger.php"); // Include Logger class
$bs = new Bootstrap();

function redirectUser() {
    $roles = [
        'Teacher_login' => 'teacher/index.php',
        'Director_login' => 'director/index.php',
        'Group_leader_login' => 'groupleader/index.php',
        'Officer_login' => 'officer/index.php',
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
<body class="hold-transition sidebar-mini layout-fixed bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen">
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

            <div class="row flex items-center justify-center min-h-[70vh] bg-transparent">

              <?php 

                include_once("config/Database.php");
                include_once("class/UserLogin.php");

                $studentDb = new Database("phichaia_student");
                $studentConn = $studentDb->getConnection();

                $user = new UserLogin($studentConn);
                $bs = new Bootstrap();

                if (isset($_POST['signin'])) {
                    $logger = new Logger("logs/login.json"); // Initialize logger
                    $username = filter_input(INPUT_POST, 'txt_username_email', FILTER_SANITIZE_STRING);
                    $password = filter_input(INPUT_POST, 'txt_password', FILTER_SANITIZE_STRING);

                    // Collect additional data for logging
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $sessionId = session_id();
                    $accessTime = date("c"); // ISO 8601 format

                    $allowed_roles = ['Admin', 'Teacher', 'Officer', 'Director', 'Parent', 'Student'];
                    $role = filter_input(INPUT_POST, 'txt_role', FILTER_SANITIZE_STRING);
                    
                    if (!in_array($role, $allowed_roles)) {
                        $role = 'Teacher'; // หรือค่าที่ปลอดภัยอื่นๆ
                    }

                    $user->setUsername($username);
                    $user->setPassword($password);

                    if ($role === 'Student') {
                        // Student login logic
                        if ($user->studentNotExists()) {
                            $logger->log([
                                "user_id" => null,
                                "role" => $role,
                                "ip_address" => $ipAddress,
                                "user_agent" => $userAgent,
                                "access_time" => $accessTime,
                                "url" => $_SERVER['REQUEST_URI'],
                                "method" => $_SERVER['REQUEST_METHOD'],
                                "status_code" => 401,
                                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                "action_type" => "login_attempt",
                                "session_id" => $sessionId,
                                "message" => "Student does not exist"
                            ]);
                            $sw2 = new SweetAlert2(
                                'ไม่มีชื่อนักเรียนนี้',
                                'error',
                                'login.php'
                            );
                            $sw2->renderAlert();
                        } else {
                            if ($user->verifyStudentPassword()) {
                                $stuStatus = $user->getUserRoleStudent();
                                if ($stuStatus == 1) { // เฉพาะนักเรียนสถานะปกติ
                                    $_SESSION['user'] = $username;
                                    $_SESSION['Student_login'] = $_SESSION['user'];
                                    $logger->log([
                                        "user_id" => $username,
                                        "role" => $role,
                                        "ip_address" => $ipAddress,
                                        "user_agent" => $userAgent,
                                        "access_time" => $accessTime,
                                        "url" => $_SERVER['REQUEST_URI'],
                                        "method" => $_SERVER['REQUEST_METHOD'],
                                        "status_code" => 200,
                                        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                        "action_type" => "login_success",
                                        "session_id" => $sessionId,
                                        "message" => "Student login successful"
                                    ]);
                                    $sw2 = new SweetAlert2(
                                        'ลงชื่อเข้าสู่ระบบเรียบร้อย',
                                        'success',
                                        'student/index.php'
                                    );
                                    $sw2->renderAlert();
                                } else {
                                    $logger->log([
                                        "user_id" => $username,
                                        "role" => $role,
                                        "ip_address" => $ipAddress,
                                        "user_agent" => $userAgent,
                                        "access_time" => $accessTime,
                                        "url" => $_SERVER['REQUEST_URI'],
                                        "method" => $_SERVER['REQUEST_METHOD'],
                                        "status_code" => 403,
                                        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                        "action_type" => "login_attempt",
                                        "session_id" => $sessionId,
                                        "message" => "Student not active"
                                    ]);
                                    $sw2 = new SweetAlert2(
                                        'นักเรียนนี้ไม่มีสถานะปกติ',
                                        'error',
                                        'login.php'
                                    );
                                    $sw2->renderAlert();
                                }
                            } else {
                                $logger->log([
                                    "user_id" => $username,
                                    "role" => $role,
                                    "ip_address" => $ipAddress,
                                    "user_agent" => $userAgent,
                                    "access_time" => $accessTime,
                                    "url" => $_SERVER['REQUEST_URI'],
                                    "method" => $_SERVER['REQUEST_METHOD'],
                                    "status_code" => 401,
                                    "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                    "action_type" => "login_attempt",
                                    "session_id" => $sessionId,
                                    "message" => "Incorrect student password"
                                ]);
                                $sw2 = new SweetAlert2(
                                    'พาสเวิร์ดไม่ถูกต้อง',
                                    'error',
                                    'login.php'
                                );
                                $sw2->renderAlert();
                            }
                        }
                    } else {
                        if ($user->userNotExists()) {
                            $logger->log([
                                "user_id" => null,
                                "role" => $role,
                                "ip_address" => $ipAddress,
                                "user_agent" => $userAgent,
                                "access_time" => $accessTime,
                                "url" => $_SERVER['REQUEST_URI'],
                                "method" => $_SERVER['REQUEST_METHOD'],
                                "status_code" => 401,
                                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                "action_type" => "login_attempt",
                                "session_id" => $sessionId,
                                "message" => "User does not exist"
                            ]);
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
                                    'Director' => ['VP', 'DIR', 'ADM'],
                                    'Admin' => ['ADM']
                                ];
                                
                                if (in_array($userRole, $allowedUserRoles[$role])) {
                                    $_SESSION['user'] = $username; // Ensure $_SESSION['user'] is set
                                    $_SESSION[$role . '_login'] = $_SESSION['user'];
                                    $logger->log([
                                        "user_id" => $username,
                                        "role" => $role,
                                        "ip_address" => $ipAddress,
                                        "user_agent" => $userAgent,
                                        "access_time" => $accessTime,
                                        "url" => $_SERVER['REQUEST_URI'],
                                        "method" => $_SERVER['REQUEST_METHOD'],
                                        "status_code" => 200,
                                        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                        "action_type" => "login_success",
                                        "session_id" => $sessionId,
                                        "message" => "Login successful"
                                    ]);
                                    $sw2 = new SweetAlert2(
                                        'ลงชื่อเข้าสู่ระบบเรียบร้อย',
                                        'success',
                                        strtolower($role) . '/index.php' // Redirect URL
                                    );
                                    $sw2->renderAlert();
                                } else {
                                    $logger->log([
                                        "user_id" => $username,
                                        "role" => $role,
                                        "ip_address" => $ipAddress,
                                        "user_agent" => $userAgent,
                                        "access_time" => $accessTime,
                                        "url" => $_SERVER['REQUEST_URI'],
                                        "method" => $_SERVER['REQUEST_METHOD'],
                                        "status_code" => 403,
                                        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                        "action_type" => "login_attempt",
                                        "session_id" => $sessionId,
                                        "message" => "Invalid role"
                                    ]);
                                    $sw2 = new SweetAlert2(
                                        'บทบาทผู้ใช้ไม่ถูกต้อง',
                                        'error',
                                        'login.php' // Redirect URL
                                    );
                                    $sw2->renderAlert();
                                } 
                            } else {
                                $logger->log([
                                    "user_id" => $username,
                                    "role" => $role,
                                    "ip_address" => $ipAddress,
                                    "user_agent" => $userAgent,
                                    "access_time" => $accessTime,
                                    "url" => $_SERVER['REQUEST_URI'],
                                    "method" => $_SERVER['REQUEST_METHOD'],
                                    "status_code" => 401,
                                    "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                                    "action_type" => "login_attempt",
                                    "session_id" => $sessionId,
                                    "message" => "Incorrect password"
                                ]);
                                $sw2 = new SweetAlert2(
                                    'พาสเวิร์ดไม่ถูกต้อง',
                                    'error',
                                    'login.php' // Redirect URL
                                );
                                $sw2->renderAlert();
                            }
                        }
                    }
                }
                ?>


                          
                    <div class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8 border border-blue-200 animate-fade-in">
                        <h2 class="text-3xl font-extrabold text-center text-blue-700 mb-6 flex items-center justify-center gap-2 animate-fade-in-down">
                            <span class="text-4xl">🔐</span> เข้าสู่ระบบ
                        </h2>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="space-y-5">
                            <div>
                                <label class="block text-gray-600 mb-1 font-semibold flex items-center gap-1">👤 ชื่อผู้ใช้งาน</label>
                                <input type="text" name="txt_username_email" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200 shadow-sm" placeholder="กรุณากรอกชื่อผู้ใช้งาน...">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1 font-semibold flex items-center gap-1">🔑 รหัสผ่าน</label>
                                <div class="relative">
                                    <input type="password" id="password" name="txt_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-200 shadow-sm" placeholder="กรุณากรอกรหัสผ่าน...">
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.837-.68 1.613-1.196 2.296M15.536 15.536A9.953 9.953 0 0112 17c-4.477 0-8.268-2.943-9.542-7a9.953 9.953 0 011.196-2.296M9.88 9.88a3 3 0 014.24 4.24" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1 font-semibold flex items-center gap-1">🧑‍💼 ประเภทผู้ใช้</label>
                                <select name="txt_role" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none text-center transition-all duration-200 shadow-sm">
                                    <option value="Teacher" selected>👨‍🏫 ครู</option>
                                    <option value="Student">🎓 นักเรียน</option>
                                    <!-- <option value="Parent">👪 ผู้ปกครอง</option> -->
                                    <option value="Officer">🧑‍💻 เจ้าหน้าที่</option>
                                    <option value="Director">👔 ผู้อำนวยการ</option>
                                    <option value="Admin">🛡️ Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="signin" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-lg font-bold text-lg flex items-center justify-center gap-2 transition-all duration-200 animate-bounce-in">
                                🚀 เข้าสู่ระบบ
                            </button>
                        </form>
                        <div class="mt-6 text-center text-gray-400 text-xs animate-fade-in">
                            © <?= date('Y') ?> โรงเรียนพิชัย | ระบบดูแลช่วยเหลือนักเรียน
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
        // Optional: animate icon
        eyeIcon.classList.add('animate-pulse');
        setTimeout(() => eyeIcon.classList.remove('animate-pulse'), 300);
    });
</script>
<style>
@layer utilities {
    .animate-fade-in { animation: fadeIn 0.7s; }
    .animate-fade-in-down { animation: fadeInDown 0.7s; }
    .animate-bounce-in { animation: bounceIn 0.8s; }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px);}
    to { opacity: 1; transform: translateY(0);}
}
@keyframes bounceIn {
    0% { transform: scale(0.9); opacity: 0.7;}
    60% { transform: scale(1.05);}
    80% { transform: scale(0.98);}
    100% { transform: scale(1); opacity: 1;}
}
}
</style>
<?php require_once('script.php'); ?>
<?php ob_end_flush(); // Flush the output buffer ?>
</body>
</html>