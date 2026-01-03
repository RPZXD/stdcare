<?php
$pageTitle = $title ?? 'เข้าสู่ระบบ';

// Get saved cookies
$savedUsername = $_COOKIE['stdcare_username'] ?? '';
$savedRole = $_COOKIE['stdcare_role'] ?? 'Teacher';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .login-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .login-card {
        background: rgba(30, 41, 59, 0.85);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .gradient-text {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .input-glow:focus {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }
    .btn-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
        background-size: 200% 200%;
        animation: gradient-shift 3s ease infinite;
    }
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        50% { opacity: 0.1; transform: scale(1.3); }
    }
</style>

<div class="min-h-[85vh] flex items-center justify-center py-6 md:py-12 px-4">
    <div class="w-full max-w-md">
        
        <!-- Logo & Header -->
        <div class="text-center mb-6 md:mb-8">
            <div class="relative inline-block mb-4">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl blur-lg opacity-50 pulse-ring"></div>
                <div class="relative w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl floating-icon overflow-hidden">
                    <img src="dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 md:w-20 md:h-20 object-contain">
                </div>
            </div>
            <h1 class="text-2xl md:text-4xl font-black gradient-text tracking-tight mb-2">
                เข้าสู่ระบบ
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm md:text-base font-medium">
                ระบบดูแลช่วยเหลือนักเรียน โรงเรียนพิชัย
            </p>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/30 dark:border-slate-700/50 shadow-2xl relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-400/20 rounded-full blur-3xl"></div>
            
            <?php if (!empty($error)): ?>
            <!-- Error Message -->
            <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-rose-700 dark:text-rose-300 text-sm">เข้าสู่ระบบไม่สำเร็จ</p>
                        <p class="text-rose-600 dark:text-rose-400 text-xs mt-0.5"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5 relative z-10">
                <!-- Username Field -->
                <div class="group">
                    <label class="block text-slate-700 dark:text-slate-300 mb-2 font-bold text-sm flex items-center gap-2">
                        <span class="text-lg">👤</span>
                        <span>ชื่อผู้ใช้งาน</span>
                    </label>
                    <input 
                        type="text" 
                        name="txt_username_email" 
                        value="<?php echo htmlspecialchars($savedUsername); ?>"
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-0 transition-all font-medium input-glow" 
                        placeholder="กรุณากรอกชื่อผู้ใช้งาน..."
                        required
                        autofocus
                    >
                </div>

                <!-- Password Field -->
                <div class="group">
                    <label class="block text-slate-700 dark:text-slate-300 mb-2 font-bold text-sm flex items-center gap-2">
                        <span class="text-lg">🔑</span>
                        <span>รหัสผ่าน</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="txt_password" 
                            class="w-full px-4 py-3.5 pr-12 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-0 transition-all font-medium input-glow" 
                            placeholder="กรุณากรอกรหัสผ่าน..."
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="group">
                    <label class="block text-slate-700 dark:text-slate-300 mb-2 font-bold text-sm flex items-center gap-2">
                        <span class="text-lg">🎭</span>
                        <span>ประเภทผู้ใช้</span>
                    </label>
                    <div class="relative">
                        <select 
                            name="txt_role" 
                            id="roleSelect"
                            class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-0 transition-all font-medium appearance-none cursor-pointer input-glow"
                        >
                            <option value="Teacher" <?php echo ($savedRole == 'Teacher') ? 'selected' : ''; ?>>👨‍🏫 ครู</option>
                            <option value="Student" <?php echo ($savedRole == 'Student') ? 'selected' : ''; ?>>🎓 นักเรียน</option>
                            <option value="Officer" <?php echo ($savedRole == 'Officer') ? 'selected' : ''; ?>>👨‍💼 เจ้าหน้าที่</option>
                            <option value="Director" <?php echo ($savedRole == 'Director') ? 'selected' : ''; ?>>🏛️ ผู้อำนวยการ</option>
                            <option value="Admin" <?php echo ($savedRole == 'Admin') ? 'selected' : ''; ?>>🛡️ Admin</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-slate-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox" 
                            name="remember_me" 
                            <?php echo !empty($savedUsername) ? 'checked' : ''; ?>
                            class="w-5 h-5 text-blue-500 bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500 cursor-pointer"
                        >
                        <span class="ml-3 text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-blue-500 transition-colors">
                            🔒 จดจำข้อมูลการเข้าสู่ระบบ
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    name="signin" 
                    class="w-full btn-gradient text-white py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl hover:shadow-blue-500/30 transform hover:-translate-y-1 active:translate-y-0 active:scale-[0.98] transition-all flex items-center justify-center gap-3"
                >
                    <span class="text-xl">🚀</span>
                    <span>เข้าสู่ระบบ</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700 text-center relative z-10">
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">
                    © <?php echo date('Y') + 543; ?> โรงเรียนพิชัย | ระบบดูแลช่วยเหลือนักเรียน
                </p>
                <p class="text-slate-300 dark:text-slate-600 text-xs mt-1">
                    ✨ StdCare System v2.0
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="index.php" class="inline-flex items-center text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors font-medium text-sm group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                <span>กลับหน้าหลัก</span>
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Success/Error SweetAlert handling
<?php if (!empty($success)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: '🎉 สำเร็จ!',
        html: '<div class="text-lg"><?php echo $successMessage ?? "เข้าสู่ระบบสำเร็จ"; ?></div><div class="mt-4 text-sm text-gray-500">กำลังนำทางไปยังหน้าหลัก...</div>',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        allowOutsideClick: false
    }).then(() => {
        window.location.href = '<?php echo $redirect ?? "index.php"; ?>';
    });
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
