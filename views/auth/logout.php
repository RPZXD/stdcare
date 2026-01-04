<?php
/**
 * StdCare System - Logout View
 * Refactored to premium Tailwind CSS design
 */
$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'];
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚪 กำลังออกจากระบบ - <?php echo $global['nameschool']; ?></title>
    
    <!-- Google Font: Mali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'mali': ['Mali', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'spin-slow': 'spin 3s linear infinite',
                        'glow': 'glow 2s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        glow: {
                            '0%, 100%': { opacity: '0.5', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.1)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Mali', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .progress-bar-glow {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4 overflow-hidden relative">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-1/2 -right-24 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-24 left-1/4 w-72 h-72 bg-pink-500/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s"></div>
    </div>

    <!-- Main Card -->
    <div class="w-full max-w-sm relative z-10 transition-all duration-500 transform hover:scale-[1.02]">
        <div class="glass rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden group">
            <!-- Logo Section -->
            <div class="flex justify-center mb-6">
                <img src="dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="School Logo" class="h-16 w-auto object-contain drop-shadow-md">
            </div>

            <!-- Icon Section -->
            <div class="flex justify-center mb-8 relative">
                <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-2xl animate-glow group-hover:bg-blue-500/30 transition-all"></div>
                <div class="relative w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center shadow-2xl rotate-3 group-hover:rotate-6 transition-transform duration-500">
                    <i class="fas fa-sign-out-alt text-4xl text-white animate-bounce-slow"></i>
                </div>
            </div>

            <!-- Text Content -->
            <div class="text-center space-y-3 mb-10">
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                    ออกจากระบบ
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    ขอบคุณที่ใช้บริการ <br>
                    <span class="text-blue-600 dark:text-blue-400 font-bold">StdCare System</span>
                </p>
            </div>

            <!-- Progress Redirect Section -->
            <div class="space-y-6">
                <!-- Status Box -->
                <div class="bg-white/50 dark:bg-slate-800/50 rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 flex items-center justify-center gap-3">
                    <?php if (isset($logoutSuccess) && $logoutSuccess): ?>
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">ดำเนินการสำเร็จ</span>
                    <?php else: ?>
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="fas fa-info text-sm"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">ไม่พบคำขอที่ใช้งานอยู่</span>
                    <?php endif; ?>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-2">
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-1">
                        <span>REDIRECTING</span>
                        <span id="percentText">0%</span>
                    </div>
                    <div class="h-3 w-full bg-slate-200 dark:bg-slate-700/50 rounded-full p-0.5 overflow-hidden border border-white/20">
                        <div id="progressBar" class="h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-full w-0 transition-all duration-100 progress-bar-glow"></div>
                    </div>
                    <p class="text-center text-xs text-slate-400 dark:text-slate-500 font-medium" id="countdownMsg">
                        กำลังพาท่านไปที่หน้าเข้าสู่ระบบ...
                    </p>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-10 pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[10px] font-bold tracking-wider uppercase border border-white/20">
                    <i class="fas fa-shield-halved text-blue-500"></i>
                    <span>StdCare Security v2.0</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Nav -->
        <div class="text-center mt-8">
            <a href="login.php" class="text-white/60 hover:text-white transition-colors text-sm font-medium flex items-center justify-center gap-2 group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                เข้าสู่ระบบทันที
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark Mode Class Support
            if (localStorage.getItem('darkMode') === 'true' || 
                (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }

            const progressBar = document.getElementById('progressBar');
            const percentText = document.getElementById('percentText');
            const countdownMsg = document.getElementById('countdownMsg');
            
            let progress = 0;
            const duration = 2500; // 2.5 seconds
            const interval = 50; 
            const step = (100 / (duration / interval));

            const countdown = setInterval(() => {
                progress += step;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(countdown);
                    
                    // Final transition
                    document.body.style.opacity = '0';
                    document.body.style.transition = 'opacity 0.6s ease-out';
                    
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 600);
                }
                
                progressBar.style.width = `${progress}%`;
                percentText.innerText = `${Math.round(progress)}%`;
                
                const secs = Math.ceil((100 - progress) / step * interval / 1000);
                if (secs > 0) {
                    countdownMsg.innerHTML = `<span class="animate-pulse">กรุณารอสักครู่...</span> (${secs}s)`;
                } else {
                    countdownMsg.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> ความเรียบร้อย...`;
                }
            }, interval);
        });
    </script>
</body>
</html>
