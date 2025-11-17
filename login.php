<?php
ob_start(); // Start output buffering
date_default_timezone_set('Asia/Bangkok');
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include_once("class/Utils.php");
// include_once("class/Logger.php"); // ปิดตัวเก่า
require_once(__DIR__ . "/controllers/DatabaseLogger.php"); // เรียกใช้ Controller ใหม่
require_once(__DIR__ . "/classes/DatabaseUsers.php"); // เรียกใช้ Database Class ใหม่
use App\DatabaseUsers; // อย่าลืม use namespace

// $bs = new Bootstrap();

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

                // ใช้ MVC Pattern: Controllers และ Models
                require_once(__DIR__ . "/controllers/LoginController.php");
                require_once(__DIR__ . "/controllers/DatabaseLogger.php");
                require_once(__DIR__ . "/classes/DatabaseUsers.php");

                // สร้าง Database Connection
                $studentDb = new App\DatabaseUsers();
                $studentConn = $studentDb->getPDO();

                // สร้าง Logger และ LoginController
                $logger = new DatabaseLogger($studentConn);
                $loginController = new LoginController($logger);
                
                // $bs = new Bootstrap();

                if (isset($_POST['signin'])) {
                    // รับข้อมูลจากฟอร์ม
                    $username = filter_input(INPUT_POST, 'txt_username_email', FILTER_SANITIZE_STRING);
                    $password = filter_input(INPUT_POST, 'txt_password', FILTER_SANITIZE_STRING);
                    $remember = isset($_POST['remember_me']) ? true : false;
                    
                    // ตรวจสอบ role
                    $allowed_roles = ['Admin', 'Teacher', 'Officer', 'Director', 'Parent', 'Student'];
                    $role = filter_input(INPUT_POST, 'txt_role', FILTER_SANITIZE_STRING);
                    
                    if (!in_array($role, $allowed_roles)) {
                        $role = 'Teacher'; // ค่า default
                    }

                    // เรียกใช้ LoginController
                    $result = $loginController->login($username, $password, $role);
                    
                    // ถ้า login สำเร็จและเลือก remember me
                    if ($result['success'] && $remember) {
                        // เก็บ cookie 30 วัน
                        setcookie('stdcare_username', $username, time() + (86400 * 30), "/");
                        setcookie('stdcare_role', $role, time() + (86400 * 30), "/");
                    } elseif ($result['success']) {
                        // ลบ cookie ถ้าไม่ tick remember me
                        setcookie('stdcare_username', '', time() - 3600, "/");
                        setcookie('stdcare_role', '', time() - 3600, "/");
                    }
                    
                    // แสดงผลตาม result พร้อม auto redirect
                    if ($result['success']) {
                        // กรณีสำเร็จ - แสดงแบบสวยงามและ redirect อัตโนมัติ
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    title: "🎉 สำเร็จ!",
                                    html: "<div class=\"text-lg\">' . $result['message'] . '</div><div class=\"mt-4 text-sm text-gray-500\">กำลังนำทางไปยังหน้าหลัก...</div>",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    background: "#fff",
                                    backdrop: "rgba(0,0,0,0.4)",
                                    customClass: {
                                        popup: "animated-popup",
                                        title: "text-gradient",
                                        htmlContainer: "text-container"
                                    },
                                    didOpen: (popup) => {
                                        // Add confetti effect
                                        const canvas = document.createElement("canvas");
                                        canvas.style.position = "fixed";
                                        canvas.style.top = "0";
                                        canvas.style.left = "0";
                                        canvas.style.width = "100%";
                                        canvas.style.height = "100%";
                                        canvas.style.pointerEvents = "none";
                                        canvas.style.zIndex = "9999";
                                        document.body.appendChild(canvas);
                                        
                                        const ctx = canvas.getContext("2d");
                                        canvas.width = window.innerWidth;
                                        canvas.height = window.innerHeight;
                                        
                                        const particles = [];
                                        const colors = ["#60a5fa", "#a78bfa", "#ec4899", "#10b981", "#f59e0b"];
                                        
                                        for (let i = 0; i < 50; i++) {
                                            particles.push({
                                                x: Math.random() * canvas.width,
                                                y: -10,
                                                vx: (Math.random() - 0.5) * 4,
                                                vy: Math.random() * 3 + 2,
                                                color: colors[Math.floor(Math.random() * colors.length)],
                                                size: Math.random() * 5 + 3
                                            });
                                        }
                                        
                                        function animate() {
                                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                                            
                                            particles.forEach((p, index) => {
                                                p.y += p.vy;
                                                p.x += p.vx;
                                                p.vy += 0.1;
                                                
                                                ctx.fillStyle = p.color;
                                                ctx.beginPath();
                                                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                                                ctx.fill();
                                                
                                                if (p.y > canvas.height) {
                                                    particles.splice(index, 1);
                                                }
                                            });
                                            
                                            if (particles.length > 0) {
                                                requestAnimationFrame(animate);
                                            } else {
                                                canvas.remove();
                                            }
                                        }
                                        
                                        animate();
                                    }
                                }).then(() => {
                                    window.location.href = "' . $result['redirect'] . '";
                                });
                            });
                        </script>';
                    } else {
                        // กรณีผิดพลาด - แสดงแบบสวยงามและกลับไป login
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    title: "❌ เกิดข้อผิดพลาด",
                                    html: "<div class=\"text-lg font-medium\">' . $result['message'] . '</div><div class=\"mt-4 text-sm text-gray-500\">กรุณาลองใหม่อีกครั้ง...</div>",
                                    icon: "error",
                                    showConfirmButton: true,
                                    confirmButtonText: "ลองใหม่อีกครั้ง",
                                    confirmButtonColor: "#ef4444",
                                    timer: 3000,
                                    timerProgressBar: true,
                                    allowOutsideClick: false,
                                    background: "#fff",
                                    backdrop: "rgba(0,0,0,0.4)",
                                    customClass: {
                                        popup: "animated-popup shake-animation",
                                        title: "text-red-600",
                                        confirmButton: "custom-confirm-btn"
                                    },
                                    didOpen: (popup) => {
                                        // Add shake effect
                                        popup.classList.add("animate-shake-error");
                                        setTimeout(() => {
                                            popup.classList.remove("animate-shake-error");
                                        }, 500);
                                    }
                                }).then(() => {
                                    window.location.href = "' . $result['redirect'] . '";
                                });
                            });
                        </script>
                        <style>
                            .animate-shake-error {
                                animation: shake-error 0.5s;
                            }
                            @keyframes shake-error {
                                0%, 100% { transform: translateX(0); }
                                10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
                                20%, 40%, 60%, 80% { transform: translateX(10px); }
                            }
                        </style>';
                    }
                    
                    // เพิ่ม custom styles สำหรับ SweetAlert2
                    echo '<style>
                        .animated-popup {
                            border-radius: 20px !important;
                            padding: 2rem !important;
                            box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
                            animation: popup-in 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important;
                        }
                        
                        @keyframes popup-in {
                            0% {
                                transform: scale(0.5) rotate(-5deg);
                                opacity: 0;
                            }
                            100% {
                                transform: scale(1) rotate(0deg);
                                opacity: 1;
                            }
                        }
                        
                        .text-gradient {
                            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            font-weight: 800 !important;
                            font-size: 1.8rem !important;
                        }
                        
                        .text-container {
                            color: #4b5563 !important;
                            margin-top: 1rem !important;
                        }
                        
                        .swal2-timer-progress-bar {
                            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899) !important;
                        }
                        
                        .swal2-icon.swal2-success {
                            border-color: #10b981 !important;
                        }
                        
                        .swal2-icon.swal2-success .swal2-success-ring {
                            border-color: rgba(16, 185, 129, 0.3) !important;
                        }
                        
                        .swal2-icon.swal2-success [class^="swal2-success-line"] {
                            background-color: #10b981 !important;
                        }
                        
                        .swal2-icon.swal2-error {
                            border-color: #ef4444 !important;
                        }
                        
                        .swal2-icon.swal2-error [class^="swal2-x-mark-line"] {
                            background-color: #ef4444 !important;
                        }
                        
                        .custom-confirm-btn {
                            border-radius: 12px !important;
                            padding: 12px 32px !important;
                            font-weight: 600 !important;
                            font-size: 1rem !important;
                            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
                            transition: all 0.3s ease !important;
                        }
                        
                        .custom-confirm-btn:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4) !important;
                        }
                        
                        .swal2-popup.shake-animation {
                            animation: shake 0.5s;
                        }
                        
                        @keyframes shake {
                            0%, 100% { transform: translateX(0); }
                            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
                            20%, 40%, 60%, 80% { transform: translateX(8px); }
                        }
                    </style>';
                }
                
                // ดึงค่าจาก cookie ถ้ามี
                $savedUsername = isset($_COOKIE['stdcare_username']) ? $_COOKIE['stdcare_username'] : '';
                $savedRole = isset($_COOKIE['stdcare_role']) ? $_COOKIE['stdcare_role'] : 'Teacher';
                ?>


                          
                    <div class="w-full max-w-xl bg-white/95 backdrop-blur-xl shadow-2xl rounded-3xl p-10 border-2 border-gradient animate-fade-in-up relative overflow-hidden">
                        <!-- Decorative floating circles -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400/20 rounded-full blur-3xl animate-pulse"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                        
                        <!-- Header with animated gradient -->
                        <div class="text-center mb-8 relative z-10">
                            <div class="inline-block p-2 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-full mb-2 animate-bounce-slow shadow-lg">
                                <span class="text-5xl filter drop-shadow-lg"><img src="dist/img/logo-phicha.png" alt="logo" style="width: 100px; height: auto;"></span>
                            </div>
                            <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 mb-2 animate-gradient">
                                เข้าสู่ระบบ
                            </h2>
                            <p class="text-gray-500 text-sm">ระบบดูแลช่วยเหลือนักเรียน โรงเรียนพิชัย</p>
                        </div>

                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="space-y-6 relative z-10">
                            <!-- Username Field -->
                            <div class="group">
                                <label class="block text-gray-700 mb-2 font-bold flex items-center gap-2 text-sm uppercase tracking-wide">
                                    <span class="text-xl">👤</span> 
                                    <span>ชื่อผู้ใช้งาน</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="txt_username_email" 
                                        value="<?php echo htmlspecialchars($savedUsername); ?>"
                                        class="w-full p-4 pl-12 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 focus:outline-none transition-all duration-300 shadow-sm hover:shadow-md group-hover:border-blue-400" 
                                        placeholder="กรุณากรอกชื่อผู้ใช้งาน..."
                                        required
                                    >

                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="group">
                                <label class="block text-gray-700 mb-2 font-bold flex items-center gap-2 text-sm uppercase tracking-wide">
                                    <span class="text-xl">🔑</span> 
                                    <span>รหัสผ่าน</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="txt_password" 
                                        class="w-full p-4 pl-12 pr-12 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 focus:outline-none transition-all duration-300 shadow-sm hover:shadow-md group-hover:border-blue-400" 
                                        placeholder="กรุณากรอกรหัสผ่าน..."
                                        required
                                    >
                                    <button 
                                        type="button" 
                                        id="togglePassword" 
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600 transition-all duration-200 hover:scale-110 active:scale-95"
                                    >
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Role Selection -->
                            <div class="group">
                                <label class="block text-gray-700 mb-2 font-bold flex items-center gap-2 text-sm uppercase tracking-wide">
                                    <span class="text-xl">🧑</span> 
                                    <span>ประเภทผู้ใช้</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        name="txt_role" 
                                        id="roleSelect"
                                        class="w-full p-4 pl-12 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 focus:outline-none transition-all duration-300 shadow-sm hover:shadow-md appearance-none cursor-pointer group-hover:border-blue-400 bg-white"
                                    >
                                        <option value="Teacher" <?php echo ($savedRole == 'Teacher') ? 'selected' : ''; ?>>👨‍🏫 ครู</option>
                                        <option value="Student" <?php echo ($savedRole == 'Student') ? 'selected' : ''; ?>>🎓 นักเรียน</option>
                                        <option value="Officer" <?php echo ($savedRole == 'Officer') ? 'selected' : ''; ?>>🧑‍ เจ้าหน้าที่</option>
                                        <option value="Director" <?php echo ($savedRole == 'Director') ? 'selected' : ''; ?>>🧑‍💼 ผู้อำนวยการ</option>
                                        <option value="Admin" <?php echo ($savedRole == 'Admin') ? 'selected' : ''; ?>>🛡️ Admin</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer group/check">
                                    <input 
                                        type="checkbox" 
                                        name="remember_me" 
                                        id="rememberMe"
                                        <?php echo !empty($savedUsername) ? 'checked' : ''; ?>
                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer transition-all"
                                    >
                                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover/check:text-blue-600 transition-colors">
                                        🔒 จดจำข้อมูลการเข้าสู่ระบบ
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                name="signin" 
                                class="w-full relative overflow-hidden bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white p-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-2xl transform hover:-translate-y-1 active:translate-y-0 transition-all duration-300 group/btn"
                            >
                                <span class="relative z-10 flex items-center justify-center gap-3">
                                    <span class="text-2xl group-hover/btn:animate-bounce">🚀</span>
                                    <span>เข้าสู่ระบบ</span>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                            </button>
                        </form>

                        <!-- Footer -->
                        <div class="mt-8 text-center relative z-10">
                            <div class="flex items-center justify-center gap-2 text-gray-400 text-xs mb-2">
                                <span class="inline-block w-12 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></span>
                                <span>🏫</span>
                                <span class="inline-block w-12 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></span>
                            </div>
                            <p class="text-gray-500 text-xs font-medium">
                                © <?= date('Y') ?> โรงเรียนพิชัย | ระบบดูแลช่วยเหลือนักเรียน
                            </p>
                            <p class="text-gray-400 text-xs mt-1">
                                ✨ Powered by StdCare System v2.0
                            </p>
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
    // Load saved theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light-mode';
        document.body.classList.remove('light-mode', 'dark-mode');
        document.body.classList.add(savedTheme);
    });

    // Toggle Password Visibility
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePasswordButton.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        
        // Animate icon transition
        eyeIcon.classList.add('animate-spin');
        setTimeout(() => eyeIcon.classList.remove('animate-spin'), 200);
        
        // Change icon
        if (isPassword) {
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    });

    // Dynamic role icon update
    const roleSelect = document.getElementById('roleSelect');
    const roleIcon = document.getElementById('roleIcon');
    const roleEmojis = {
        'Teacher': '👨‍🏫',
        'Student': '🎓',
        'Officer': '🧑‍💻',
        'Director': '👔',
        'Admin': '🛡️'
    };

    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        roleIcon.textContent = roleEmojis[selectedRole] || '👤';
        
        // Add animation
        roleIcon.classList.add('animate-bounce');
        setTimeout(() => roleIcon.classList.remove('animate-bounce'), 500);
    });

    // Add floating particles effect
    function createFloatingParticle() {
        const particle = document.createElement('div');
        particle.className = 'floating-particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
        particle.style.opacity = Math.random() * 0.5 + 0.3;
        document.querySelector('.content-wrapper').appendChild(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 5000);
    }

    // Create particles periodically
    setInterval(createFloatingParticle, 300);

    // Add ripple effect to button
    const submitBtn = document.querySelector('button[name="signin"]');
    submitBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        this.appendChild(ripple);
        
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Input focus effects
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-102');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-102');
        });
    });

    // Form validation with visual feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const username = document.querySelector('input[name="txt_username_email"]');
        const password = document.querySelector('input[name="txt_password"]');
        
        if (!username.value.trim() || !password.value.trim()) {
            e.preventDefault();
            
            // Shake animation
            form.classList.add('animate-shake');
            setTimeout(() => form.classList.remove('animate-shake'), 500);
        }
    });
</script>
<style>
@layer utilities {
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    .animate-fade-in-down { animation: fadeInDown 0.8s ease-out; }
    .animate-fade-in-up { animation: fadeInUp 0.8s ease-out; }
    .animate-bounce-in { animation: bounceIn 1s ease-out; }
    .animate-bounce-slow { animation: bounce 2s infinite; }
    .animate-gradient { animation: gradient 3s ease infinite; }
    .animate-shake { animation: shake 0.5s; }
    .scale-102 { transform: scale(1.02); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInDown {
    from { 
        opacity: 0; 
        transform: translateY(-30px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% { 
        transform: scale(0.8); 
        opacity: 0.5;
    }
    60% { 
        transform: scale(1.05);
        opacity: 1;
    }
    80% { 
        transform: scale(0.98);
    }
    100% { 
        transform: scale(1); 
        opacity: 1;
    }
}

@keyframes gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
    20%, 40%, 60%, 80% { transform: translateX(10px); }
}

/* Floating particles */
.floating-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: linear-gradient(45deg, #60a5fa, #a78bfa, #ec4899);
    border-radius: 50%;
    pointer-events: none;
    animation: float-up linear forwards;
    z-index: 1;
}

@keyframes float-up {
    from {
        bottom: -10px;
        opacity: 1;
    }
    to {
        bottom: 100%;
        opacity: 0;
    }
}

/* Ripple effect */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s ease-out;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Gradient border animation */
.border-gradient {
    border-image: linear-gradient(45deg, #60a5fa, #a78bfa, #ec4899) 1;
    animation: border-gradient-rotate 3s linear infinite;
}

@keyframes border-gradient-rotate {
    0% { border-image-source: linear-gradient(45deg, #60a5fa, #a78bfa, #ec4899); }
    50% { border-image-source: linear-gradient(45deg, #ec4899, #60a5fa, #a78bfa); }
    100% { border-image-source: linear-gradient(45deg, #60a5fa, #a78bfa, #ec4899); }
}

/* Enhanced focus effects */
input:focus, select:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
}

/* Checkbox custom style */
input[type="checkbox"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}

/* Background animation */
.content-wrapper {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
    background-size: 400% 400%;
    animation: gradient-shift 20s ease infinite;
}

/* Dark mode support - Manual Toggle */
body.dark-mode .content-wrapper {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #0f3460 50%, #533483 75%, #1a1a2e 100%) !important;
}

body.dark-mode .w-full.max-w-xl {
    background: rgba(30, 30, 30, 0.95) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

body.dark-mode .text-gray-700,
body.dark-mode label {
    color: #e5e7eb !important;
}

body.dark-mode .text-gray-500,
body.dark-mode p {
    color: #9ca3af !important;
}

body.dark-mode .border-gray-300 {
    border-color: rgba(255, 255, 255, 0.2) !important;
}

body.dark-mode input, 
body.dark-mode select {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: #e5e7eb !important;
}

body.dark-mode input::placeholder {
    color: #6b7280 !important;
}

/* Dark mode support - Auto Detection */
@media (prefers-color-scheme: dark) {
    body:not(.light-mode) .content-wrapper {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #0f3460 50%, #533483 75%, #1a1a2e 100%);
    }
    
    body:not(.light-mode) .w-full.max-w-xl {
        background: rgba(30, 30, 30, 0.95) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    body:not(.light-mode) .text-gray-700,
    body:not(.light-mode) label {
        color: #e5e7eb !important;
    }
    
    body:not(.light-mode) .text-gray-500,
    body:not(.light-mode) p {
        color: #9ca3af !important;
    }
    
    body:not(.light-mode) .border-gray-300 {
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    
    body:not(.light-mode) input, 
    body:not(.light-mode) select {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #e5e7eb !important;
    }
    
    body:not(.light-mode) input::placeholder {
        color: #6b7280 !important;
    }
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Glow effect on hover */
.group:hover input,
.group:hover select {
    box-shadow: 0 0 20px rgba(96, 165, 250, 0.3);
}

/* Animated gradient text */
.bg-clip-text {
    background-size: 200% 200%;
}
</style>
<?php require_once('script.php'); ?>
<?php ob_end_flush(); // Flush the output buffer ?>
</body>
</html>