<?php
/**
 * Base Layout
 * Centralized layout template for all roles
 */
$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'] ?? ['nameschool' => 'โรงเรียน'];
$themeColor = $themeColor ?? 'indigo';
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'ระบบ'; ?> | <?php echo $global['nameschool']; ?></title>
    
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

    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/dist/css/style.css">
    
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo $basePath ?? '..'; ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $basePath ?? '..'; ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * { font-family: 'Mali', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        .glass-effect { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .dark .glass-effect { background: rgba(15, 23, 42, 0.7); }
        
        .sidebar-item { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-item:hover { transform: translateX(5px); }
        
        .no-print { @media print { display: none !important; } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out; }

        /* Bootstrap Modal Override for Tailwind compatibility */
        .modal { z-index: 9999 !important; }
        .modal-backdrop { z-index: 9998 !important; }
        .modal-dialog { z-index: 10000 !important; }
        .modal-content { font-family: 'Mali', sans-serif; border-radius: 1.5rem; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-header { border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1.5rem; }
        .modal-footer { border-top: 1px solid rgba(0,0,0,0.05); padding: 1.25rem 1.5rem; }

        .admin-table-shell { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .app-content-shell,
        .app-main-shell,
        .app-page-shell {
            min-width: 0;
            width: 100%;
        }
        .app-main-shell { overflow-x: hidden; }
        .admin-page-header h2,
        .admin-page-header p { overflow-wrap: anywhere; }
        .admin-header-actions button span {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .admin-responsive-table td { word-break: break-word; }
        .dataTables_wrapper .row { margin-left: 0; margin-right: 0; row-gap: .75rem; }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { color: inherit !important; }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            padding: .5rem .75rem;
            background: rgba(248, 250, 252, .9);
            color: #334155;
            outline: none;
        }
        .dark .dataTables_wrapper .dataTables_filter input,
        .dark .dataTables_wrapper .dataTables_length select {
            border-color: #334155;
            background: rgba(15, 23, 42, .75);
            color: #e2e8f0;
        }

        @media (max-width: 767.98px) {
            body { overflow-x: hidden; }
            main { padding: 1rem .75rem 1.5rem !important; }
            footer { padding-bottom: 1.5rem; }
            .glass-effect { backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
            .admin-navbar-inner { padding: .75rem 1rem .75rem 4rem !important; }
            .admin-page-header { margin-top: .25rem; }
            .admin-header-actions { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .admin-header-actions button { width: 100%; min-height: 2.75rem; padding-left: .75rem !important; padding-right: .75rem !important; }
            .admin-header-actions button span { white-space: normal; line-height: 1.15; }
            .glass-effect > .flex.flex-wrap { align-items: stretch !important; }
            .glass-effect > .flex.flex-wrap > .flex-1 { flex: 0 0 100% !important; width: 100%; }
            .glass-effect select,
            .glass-effect input[type="text"],
            .glass-effect input[type="date"],
            .glass-effect input[type="time"],
            .glass-effect input[type="number"],
            .glass-effect input[type="file"],
            .glass-effect button { max-width: 100%; }
            .modal-dialog { margin: .5rem !important; max-width: calc(100% - 1rem) !important; }
            .modal-content { border-radius: 1rem !important; max-height: calc(100vh - 1rem); }
            .modal-header, .modal-footer { padding: 1rem !important; }
            .modal-body { max-height: calc(100vh - 10rem) !important; }
            .dataTables_wrapper .row > [class*="col-"] { flex: 0 0 100%; max-width: 100%; padding-left: 0; padding-right: 0; }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { text-align: left !important; }
            .dataTables_wrapper .dataTables_filter label,
            .dataTables_wrapper .dataTables_length label { width: 100%; font-size: .8rem; font-weight: 700; color: #64748b; }
            .dataTables_wrapper .dataTables_filter input { width: 100%; margin: .35rem 0 0 0 !important; }
            .dataTables_wrapper .dataTables_length select { margin: 0 .35rem; }
            .dataTables_wrapper .dataTables_paginate { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .75rem !important; }
            .dataTables_wrapper .dataTables_paginate .paginate_button { margin: 0 !important; padding: .45rem .65rem !important; border-radius: .65rem !important; }

            .admin-table-shell { overflow: visible; }
            table.admin-responsive-table { display: block; width: 100% !important; border-collapse: separate !important; border-spacing: 0 !important; }
            table.admin-responsive-table thead { display: none; }
            table.admin-responsive-table tbody { display: block; width: 100%; }
            table.admin-responsive-table tbody tr {
                display: block;
                width: 100%;
                margin: 0 0 .85rem 0;
                padding: .8rem;
                border: 1px solid rgba(226, 232, 240, .9);
                border-radius: 1rem;
                background: rgba(255, 255, 255, .86);
                box-shadow: 0 12px 24px -18px rgba(15, 23, 42, .45);
            }
            .dark table.admin-responsive-table tbody tr {
                border-color: rgba(51, 65, 85, .9);
                background: rgba(15, 23, 42, .72);
            }
            table.admin-responsive-table tbody td {
                display: grid !important;
                grid-template-columns: minmax(5.5rem, 38%) minmax(0, 1fr);
                align-items: center;
                gap: .75rem;
                width: 100% !important;
                padding: .55rem 0 !important;
                border: 0 !important;
                border-bottom: 1px solid rgba(226, 232, 240, .72) !important;
                text-align: right !important;
                white-space: normal !important;
            }
            .dark table.admin-responsive-table tbody td { border-bottom-color: rgba(51, 65, 85, .82) !important; }
            table.admin-responsive-table tbody td:last-child { border-bottom: 0 !important; padding-bottom: 0 !important; }
            table.admin-responsive-table tbody td::before {
                content: attr(data-label);
                min-width: 0;
                text-align: left;
                font-size: .65rem;
                line-height: 1.15;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #94a3b8;
            }
            table.admin-responsive-table tbody td:empty::after { content: "-"; color: #94a3b8; }
            table.admin-responsive-table .action-buttons,
            table.admin-responsive-table td:last-child > div { justify-content: flex-end !important; flex-wrap: wrap !important; gap: .35rem !important; }
        }
    </style>
    
    <link rel="icon" type="image/png" href="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>">
</head>

<body class="bg-<?php echo $themeColor; ?>-50/30 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-500">
    
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-[100] flex items-center justify-center bg-white dark:bg-slate-950 transition-opacity duration-700">
        <div class="relative">
            <div class="w-24 h-24 border-4 border-<?php echo $themeColor; ?>-500/10 border-t-<?php echo $themeColor; ?>-600 rounded-full animate-spin"></div>
            <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="Logo" class="absolute inset-0 m-auto w-12 h-12 animate-pulse">
        </div>
    </div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../components/' . $role . '_sidebar.php'; ?>
        
        <!-- Content Area -->
        <div class="app-content-shell flex-1 min-w-0 w-full flex flex-col lg:ml-64">
            <!-- Navbar -->
            <?php include __DIR__ . '/../components/' . $role . '_navbar.php'; ?>
            
            <!-- Page Content -->
            <main class="app-main-shell flex-1 min-w-0 p-3 md:p-8 lg:p-10">
                <div class="app-page-shell max-w-[1600px] min-w-0 w-full mx-auto animate-slide-up">
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
            if (preloader) {
                preloader.classList.add('opacity-0');
                setTimeout(() => preloader.style.display = 'none', 700);
            }
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
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 1024) toggleSidebar();
        }

        function applyAdminResponsiveTableLabels(table) {
            const $table = window.jQuery ? jQuery(table) : null;
            if (!$table || !$table.length || !$table.hasClass('admin-responsive-table')) return;
            const labels = [];
            $table.find('thead th').each(function() {
                labels.push(jQuery(this).text().replace(/\s+/g, ' ').trim());
            });
            $table.find('tbody tr').each(function() {
                jQuery(this).children('td').each(function(index) {
                    if (!this.getAttribute('data-label')) {
                        this.setAttribute('data-label', labels[index] || '');
                    }
                });
            });
        }

        if (window.jQuery) {
            jQuery(function($) {
                $('table.admin-responsive-table').each(function() { applyAdminResponsiveTableLabels(this); });
                $(document).on('draw.dt', function(e, settings) {
                    if (settings && settings.nTable) applyAdminResponsiveTableLabels(settings.nTable);
                });
            });
        }
    </script>
</body>
</html>
