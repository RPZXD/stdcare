<?php
date_default_timezone_set('Asia/Bangkok');

// ใช้ MVC Pattern: Controllers และ Models
require_once(__DIR__ . "/controllers/LoginController.php");
require_once(__DIR__ . "/controllers/DatabaseLogger.php");
require_once(__DIR__ . "/classes/DatabaseUsers.php");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// สร้าง Database Connection
$studentDb = new App\DatabaseUsers();
$studentConn = $studentDb->getPDO();

// สร้าง Logger และ LoginController
$logger = new DatabaseLogger($studentConn);
$loginController = new LoginController($logger);

// ทำการ logout ผ่าน controller
$logoutSuccess = false;
if (isset($_SESSION['user'])) {
    $result = $loginController->logout();
    $logoutSuccess = $result['success'];
} else {
    // Log กรณีไม่มี session
    $logger->log([
        "user_id" => null,
        "role" => null,
        "ip_address" => $_SERVER['REMOTE_ADDR'],
        "user_agent" => $_SERVER['HTTP_USER_AGENT'],
        "access_time" => date("c"),
        "url" => $_SERVER['REQUEST_URI'],
        "method" => $_SERVER['REQUEST_METHOD'],
        "status_code" => 400,
        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
        "action_type" => "logout_attempt",
        "session_id" => session_id(),
        "message" => "Logout attempted without an active session"
    ]);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚪 กำลังออกจากระบบ - StdCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#3B82F6',
                        'secondary': '#6B7280',
                        'success': '#10B981',
                        'warning': '#F59E0B',
                        'danger': '#EF4444',
                        'info': '#06B6D4'
                    },
                    animation: {
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'spin-slow': 'spin 2s linear infinite',
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.6s ease-out'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen animated-bg flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Particle effects -->
    <div class="particle" style="top: 10%; left: 10%;"></div>
    <div class="particle" style="top: 20%; right: 15%;"></div>
    <div class="particle" style="bottom: 15%; left: 20%;"></div>
    <div class="particle" style="top: 60%; right: 10%;"></div>
    <div class="particle" style="bottom: 30%; right: 25%;"></div>
    <div class="particle" style="top: 40%; left: 60%;"></div>
    <div class="particle" style="bottom: 50%; left: 70%;"></div>
    <div class="particle" style="top: 80%; right: 40%;"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Main Logout Card -->
        <div class="bg-white/90 glass rounded-3xl shadow-2xl border border-white/20 overflow-hidden transform animate-scale-in hover:shadow-3xl transition-all duration-500 animate-pulse-glow">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 p-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                        <i class="fas fa-sign-out-alt text-3xl text-white animate-bounce-slow"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">🚪 กำลังออกจากระบบ</h1>
                    <p class="text-blue-100">กรุณารอสักครู่...</p>
                </div>

                <!-- Animated background elements -->
                <div class="absolute top-4 left-4 w-8 h-8 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-8 right-8 w-6 h-6 bg-white/10 rounded-full animate-pulse-slow" style="animation-delay: 0.5s;"></div>
                <div class="absolute bottom-6 left-6 w-4 h-4 bg-white/10 rounded-full animate-pulse-slow" style="animation-delay: 1s;"></div>
            </div>

            <!-- Content -->
            <div class="p-8 text-center">
                <?php if ($logoutSuccess): ?>
                    <!-- Success State -->
                    <div class="space-y-6">
                        <div class="flex justify-center">
                            <div class="relative">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center animate-pulse-slow">
                                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center animate-bounce">
                                    <i class="fas fa-star text-xs text-white"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl font-bold text-gray-800">✅ ออกจากระบบสำเร็จ!</h2>
                            <p class="text-gray-600">ขอบคุณที่ใช้บริการ StdCare</p>
                        </div>

                        <!-- Progress bar -->
                        <div class="space-y-2">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full animate-pulse" id="progressBar" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-500" id="countdownText">กำลังนำทางไปยังหน้าเข้าสู่ระบบ...</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Error State -->
                    <div class="space-y-6">
                        <div class="flex justify-center">
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center animate-pulse-slow">
                                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl font-bold text-gray-800">⚠️ ไม่พบเซสชัน</h2>
                            <p class="text-gray-600">คุณไม่ได้เข้าสู่ระบบอยู่</p>
                        </div>

                        <!-- Progress bar -->
                        <div class="space-y-2">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full animate-pulse" id="progressBar" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-500" id="countdownText">กำลังนำทางไปยังหน้าเข้าสู่ระบบ...</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-shield-alt text-blue-500"></i>
                    <span>ระบบรักษาความปลอดภัย StdCare</span>
                </div>
            </div>
        </div>

        <!-- Floating elements -->
        <div class="fixed top-10 left-10 animate-float opacity-20">
            <i class="fas fa-lock text-4xl text-blue-500"></i>
        </div>
        <div class="fixed top-20 right-16 animate-float opacity-15" style="animation-delay: 1s;">
            <i class="fas fa-key text-3xl text-purple-500"></i>
        </div>
        <div class="fixed bottom-16 left-20 animate-float opacity-10" style="animation-delay: 2s;">
            <i class="fas fa-user-shield text-5xl text-indigo-500"></i>
        </div>
        <div class="fixed top-1/2 left-8 animate-rotate opacity-5" style="animation-delay: 0.5s;">
            <i class="fas fa-shield-alt text-2xl text-green-500"></i>
        </div>
        <div class="fixed top-1/3 right-12 animate-float opacity-8" style="animation-delay: 1.5s;">
            <i class="fas fa-star text-xl text-yellow-500"></i>
        </div>
        <div class="fixed bottom-1/3 right-8 animate-rotate opacity-6" style="animation-delay: 2.5s;">
            <i class="fas fa-heart text-lg text-pink-500"></i>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            }
            50% {
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.6), 0 0 40px rgba(139, 92, 246, 0.4);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.6s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-rotate {
            animation: rotate 2s linear infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        /* Particle effect */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            pointer-events: none;
            animation: float 4s ease-in-out infinite;
        }

        .particle:nth-child(2n) {
            background: linear-gradient(45deg, #ec4899, #f59e0b);
            animation-delay: 1s;
        }

        .particle:nth-child(3n) {
            background: linear-gradient(45deg, #10b981, #06b6d4);
            animation-delay: 2s;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #2563eb, #7c3aed);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Background animation */
        .animated-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }

        /* Dark mode support - Manual Toggle */
        body.dark-mode .animated-bg {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #0f3460 50%, #533483 75%, #1a1a2e 100%) !important;
        }
        
        body.dark-mode .bg-white\/90 {
            background: rgba(30, 30, 30, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .text-gray-800 {
            color: #e5e7eb !important;
        }
        
        body.dark-mode .text-gray-600 {
            color: #9ca3af !important;
        }
        
        body.dark-mode .text-gray-500 {
            color: #6b7280 !important;
        }
        
        body.dark-mode .bg-gray-50 {
            background-color: rgba(20, 20, 20, 0.5) !important;
        }

        /* Dark mode support - Auto Detection */
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .animated-bg {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #0f3460 50%, #533483 75%, #1a1a2e 100%);
            }
            
            body:not(.light-mode) .bg-white\/90 {
                background: rgba(30, 30, 30, 0.95) !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
            }
            
            body:not(.light-mode) .text-gray-800 {
                color: #e5e7eb !important;
            }
            
            body:not(.light-mode) .text-gray-600 {
                color: #9ca3af !important;
            }
            
            body:not(.light-mode) .text-gray-500 {
                color: #6b7280 !important;
            }
            
            body:not(.light-mode) .bg-gray-50 {
                background-color: rgba(20, 20, 20, 0.5) !important;
            }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.1); }
            50% { transform: scale(1.2); }
            75% { transform: scale(1.1); }
        }

        .animate-heartbeat {
            animation: heartbeat 1.5s ease-in-out infinite;
        }
    </style>

    <script>
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light-mode';
            document.body.classList.remove('light-mode', 'dark-mode');
            document.body.classList.add(savedTheme);
            
            // Progress bar and countdown logic
            const progressBar = document.getElementById('progressBar');
            const countdownText = document.getElementById('countdownText');
            let progress = 0;
            const duration = 3000; // 3 seconds
            const interval = 50; // Update every 50ms
            const steps = duration / interval;
            const increment = 100 / steps;

            // Create confetti effect for successful logout
            <?php if ($logoutSuccess): ?>
            function createConfetti() {
                const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b', '#06b6d4'];
                for (let i = 0; i < 50; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 3 + 's';
                    document.body.appendChild(confetti);

                    setTimeout(() => {
                        confetti.remove();
                    }, 4000);
                }
            }

            // Add confetti styles
            const confettiStyle = document.createElement('style');
            confettiStyle.textContent = `
                .confetti {
                    position: fixed;
                    top: -10px;
                    width: 10px;
                    height: 10px;
                    animation: fall 4s linear forwards;
                    z-index: 1000;
                }

                @keyframes fall {
                    to {
                        transform: translateY(100vh) rotate(720deg);
                    }
                }
            `;
            document.head.appendChild(confettiStyle);

            // Trigger confetti after a short delay
            setTimeout(createConfetti, 500);
            <?php endif; ?>

            const countdown = setInterval(() => {
                progress += increment;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(countdown);

                    // Add fade out effect before redirect
                    document.body.style.transition = 'opacity 0.8s ease-out';
                    document.body.style.opacity = '0';

                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 800);
                }

                progressBar.style.width = progress + '%';

                // Update countdown text with emoji
                const remaining = Math.ceil((100 - progress) / (100 / 3));
                if (remaining > 0) {
                    countdownText.innerHTML = `<i class="fas fa-clock mr-1"></i>กำลังนำทางไปยังหน้าเข้าสู่ระบบ... (${remaining})`;
                } else {
                    countdownText.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i>กำลังนำทาง...`;
                }
            }, interval);

            // Add entrance animations with stagger
            const elements = document.querySelectorAll('.animate-scale-in, .animate-slide-up, .animate-fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Enhanced hover effects
            const card = document.querySelector('.bg-white\\/90');
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.03) translateY(-8px) rotate(1deg)';
                this.style.boxShadow = '0 30px 60px -12px rgba(0, 0, 0, 0.25), 0 0 40px rgba(59, 130, 246, 0.3)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) translateY(0) rotate(0deg)';
                this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.15)';
            });

            // Add click effect to progress bar
            progressBar.addEventListener('click', function() {
                this.style.transform = 'scaleY(1.5)';
                setTimeout(() => {
                    this.style.transform = 'scaleY(1)';
                }, 200);
            });

            // Add heartbeat animation to success icon
            <?php if ($logoutSuccess): ?>
            const successIcon = document.querySelector('.fa-check-circle');
            if (successIcon) {
                successIcon.style.animation = 'heartbeat 1.5s ease-in-out infinite';
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>

<?php
// Don't call $user->logOut() here since we're handling the redirect with JavaScript
// $user->logOut();
?>