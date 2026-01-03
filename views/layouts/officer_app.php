<?php
/**
 * Officer Layout
 * MVC Pattern - Main layout template for officer pages
 * Premium Design with Tailwind CSS & Responsive Components
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check login
if (!isset($_SESSION['Officer_login'])) {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'] ?? ['nameschool' => 'โรงเรียน'];

// Ensure officer data is in session
if (!isset($_SESSION['officer_data']) || empty($_SESSION['officer_data']['Teach_name'])) {
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../class/UserLogin.php';
    $connectDB = new \Database("phichaia_student");
    $db = $connectDB->getConnection();
    $userLogin = new \UserLogin($db);
    $_SESSION['officer_data'] = $userLogin->userData($_SESSION['Officer_login']);
}
$userData = $_SESSION['officer_data'];
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'ระบบเจ้าหน้าที่'; ?> | <?php echo $global['nameschool']; ?></title>
    
    <!-- Google Font: Mali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jQuery first -->
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/jquery/jquery.min.js"></script>
    
    <!-- Bootstrap 4 (Compatibility) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 'mali': ['Mali', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    
    <style>
        * { font-family: 'Mali', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .dark .glass { background: rgba(15, 23, 42, 0.7); }
        
        .sidebar-item { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-item:hover { transform: translateX(5px); }
        
        .no-print { @media print { display: none !important; } }

        /* Bootstrap Modal Override for Tailwind compatibility */
        .modal { z-index: 9999 !important; }
        .modal-backdrop { z-index: 9998 !important; }
        .modal-dialog { z-index: 10000 !important; }
        .modal-content { font-family: 'Mali', sans-serif; border-radius: 1.5rem; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-header { border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1.5rem; }
        .modal-footer { border-top: 1px solid rgba(0,0,0,0.05); padding: 1.25rem 1.5rem; }
    </style>
    
    <link rel="icon" type="image/png" href="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>">
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-500">
    
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-[100] flex items-center justify-center bg-white dark:bg-slate-950 transition-opacity duration-700">
        <div class="relative">
            <div class="w-24 h-24 border-4 border-blue-500/10 border-t-blue-600 rounded-full animate-spin"></div>
            <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="Logo" class="absolute inset-0 m-auto w-12 h-12 animate-pulse">
        </div>
    </div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../components/officer_sidebar.php'; ?>
        
        <!-- Content Area -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <?php include __DIR__ . '/../components/officer_navbar.php'; ?>
            
            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-8 lg:p-10">
                <div class="max-w-[1600px] mx-auto animate-slide-up">
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
        // Preloader Logic
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            preloader.classList.add('opacity-0');
            setTimeout(() => preloader.style.display = 'none', 700);
        });

        // Theme Toggle Logic
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }
        
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        
        // Responsive Sidebar Logic
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 1024) toggleSidebar();
        }
    </script>
</body>
</html>
