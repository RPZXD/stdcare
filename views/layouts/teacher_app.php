<?php
/**
 * Teacher Layout
 * MVC Pattern - Main layout template for teacher pages
 * Uses same design as main app.php but with teacher-specific sidebar
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check login - allow if Teacher_login exists
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'];

// Ensure teacher_data is in session (fallback if not already set)
if (!isset($_SESSION['teacher_data']) || empty($_SESSION['teacher_data']['Teach_name'])) {
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../class/UserLogin.php';
    $connectDB = new \Database("phichaia_student");
    $db = $connectDB->getConnection();
    $userLogin = new \UserLogin($db);
    $_SESSION['teacher_data'] = $userLogin->userData($_SESSION['Teacher_login']);
}
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'ครู'; ?> | <?php echo $global['nameschool']; ?></title>
    
    <!-- Google Font: Mali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    
    <!-- Font Awesome 6 (CDN) -->
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
                        accent: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'slide-in-left': 'slideInLeft 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        }
                    },
                }
            }
        }
    </script>
    
    <!-- jQuery (Local) -->
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/jquery/jquery.min.js"></script>
    
    <!-- SweetAlert2 (Local) -->
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    
    <!-- DataTables (Local) -->
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    
    <!-- Bootstrap 5.3.3 (Cloudflare CDN for better stability in TH) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    
    <style>
        * {
            font-family: 'Mali', sans-serif;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6 0%, #6366f1 100%);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #2563eb 0%, #4f46e5 100%);
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .dark .glass {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Sidebar animation */
        .sidebar-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-item:hover {
            transform: translateX(8px);
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Loading spinner */
        .loader {
            border: 3px solid rgba(59, 130, 246, 0.2);
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Bootstrap Modal Override for Tailwind compatibility */
        .modal {
            z-index: 9999 !important;
        }
        .modal-backdrop {
            z-index: 9998 !important;
        }
        .modal-dialog {
            z-index: 10000 !important;
        }
        .modal-content {
            font-family: 'Mali', sans-serif;
        }
    </style>
    
    <link rel="icon" type="image/png" href="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>">
</head>

<body class="font-mali bg-gradient-to-br from-blue-50 via-indigo-50 to-violet-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen transition-colors duration-500">
    
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-slate-900 transition-opacity duration-500">
        <div class="text-center">
            <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="Logo" class="w-24 h-24 mx-auto animate-bounce-slow">
            <div class="loader mx-auto mt-6"></div>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 animate-pulse">ระบบดูแลช่วยเหลือนักเรียน</p>
        </div>
    </div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../components/teacher_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <?php include __DIR__ . '/../components/teacher_navbar.php'; ?>
            
            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto animate-fade-in">
                    <?php echo $content ?? ''; ?>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="py-4 px-6 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 no-print">
                <p>&copy; <?php echo date('Y') + 543; ?> <?php echo $global['nameschool']; ?> - All rights reserved.</p>
                <p class="mt-1"><?php echo $global['footerCredit'] ?? ''; ?></p>
            </footer>
        </div>
    </div>
    
    <script>
        // Preloader
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            setTimeout(() => {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }, 800);
        });
        
        // Dark mode toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }
        
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
        
        // Close sidebar on mobile when clicking a link
        function closeSidebarOnMobile() {
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
        
        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    </script>
</body>
</html>
