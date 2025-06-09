<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));

require_once('header.php');

?>

<!-- Add Tailwind CSS CDN and custom CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<!-- jQuery CDN (required for the functionality) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Custom animations and styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Prompt', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    .animate-slide-up {
        animation: slideUp 0.8s ease-out;
    }
    
    .animate-bounce-in {
        animation: bounceIn 1s ease-out;
    }
    
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
    
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .gradient-text {
        background: linear-gradient(45deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .btn-modern {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
    }
    
    .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .btn-modern:hover::before {
        left: 100%;
    }
    
    .floating-animation {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .chart-container {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
      @media print {
        body { 
            background: white !important; 
            font-size: 12px;
        }
        .glass-effect { 
            background: white !important; 
            backdrop-filter: none !important; 
            border: 1px solid #ccc !important;
        }
        .no-print { 
            display: none !important; 
        }
        .btn-modern {
            display: none !important;
        }
        .gradient-text {
            color: #1f2937 !important;
            -webkit-text-fill-color: initial !important;
        }
        .bg-gradient-to-r {
            background: #1f2937 !important;
            color: white !important;
        }
        .animate-fade-in,
        .animate-slide-up,
        .animate-bounce-in {
            animation: none !important;
        }
        .floating-animation {
            animation: none !important;
        }
        .chart-container {
            page-break-inside: avoid;
        }
        h1, h2, h3, h4, h5 {
            page-break-after: avoid;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .print-bw * {
            color: black !important;
            background: white !important;
        }
    }
    
    /* Additional responsive styles */
    @media (max-width: 768px) {
        .glass-effect {
            padding: 1rem !important;
        }
        .text-4xl {
            font-size: 2rem !important;
        }
        .grid-cols-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .btn-modern {
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
        }
        .chart-container {
            padding: 1rem !important;
        }
    }
    
    @media (max-width: 640px) {
        .grid-cols-4,
        .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        .flex-wrap {
            flex-direction: column !important;
        }
        .space-x-4 > * {
            margin-left: 0 !important;
            margin-bottom: 0.5rem !important;
        }
    }
    
    /* Search highlighting */
    .highlight-search {
        background: linear-gradient(90deg, rgba(255, 235, 59, 0.3), rgba(255, 193, 7, 0.3)) !important;
        animation: highlight-pulse 2s infinite;
    }
    
    @keyframes highlight-pulse {
        0%, 100% { background-opacity: 0.3; }
        50% { background-opacity: 0.6; }
    }
    
    /* Loading states */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<body class="min-h-screen">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper with enhanced styling -->
    <div class="content-wrapper bg-transparent">
        <!-- Header Section -->
        <div class="content-header py-6">
            <div class="container-fluid">
                <div class="animate-fade-in">
                    <!-- Modern Header Card -->
                    <div class="glass-effect rounded-3xl p-8 mb-6 card-hover">
                        <div class="text-center">
                            <!-- Logo with floating animation -->
                            <div class="floating-animation mb-6">
                                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-2xl">
                                    <i class="fas fa-home text-white text-4xl"></i>
                                </div>
                            </div>
                            
                            <!-- Title with gradient text -->
                            <h1 class="text-4xl font-bold gradient-text mb-4 animate-bounce-in">
                                📊 รายงานสถิติการเยี่ยมบ้านนักเรียน
                            </h1>
                            
                            <!-- Subtitle with modern styling -->
                            <div class="bg-white bg-opacity-80 rounded-2xl p-6 shadow-lg animate-slide-up">
                                <p class="text-xl font-semibold text-gray-700 mb-2">
                                    ระดับชั้นมัธยมศึกษาปีที่ <span class="text-blue-600 font-bold"><?= $class."/".$room; ?></span>
                                </p>
                                
                                <!-- Teacher info with modern design -->
                                <div class="flex items-center justify-center space-x-2 mt-4">
                                    <i class="fas fa-user-tie text-purple-600 text-lg"></i>
                                    <span class="text-lg font-medium text-gray-600">ครูที่ปรึกษา:</span>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <?php
                                        $teachers = $teacher->getTeachersByClassAndRoom($class, $room);
                                        if ($teachers) {
                                            foreach ($teachers as $row) {
                                                echo '<span class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-medium shadow-md">' . $row['Teach_name'] . '</span>';
                                            }
                                        } else {
                                            echo '<span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">ไม่พบข้อมูลครูที่ปรึกษา</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <!-- Date display -->
                                <div class="flex items-center justify-center space-x-2 mt-3">
                                    <i class="fas fa-calendar-alt text-green-600"></i>
                                    <span class="text-sm text-gray-600"><?= $currentDate2; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <section class="content">
            <div class="container-fluid px-6">
                <!-- Control Panel -->
                <div class="glass-effect rounded-2xl p-6 mb-6 animate-slide-up">
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 mb-6 no-print">
                        <button type="button" id="addButton" class="btn-modern flex items-center space-x-2" onclick="window.location.href='visithome.php'">
                            <i class="fas fa-arrow-left"></i>
                            <span>กลับไปหน้าข้อมูลการเยี่ยมบ้าน</span>
                        </button>
                        <button class="btn-modern flex items-center space-x-2" id="printButton" onclick="printPage()">
                            <i class="fas fa-print"></i>
                            <span>พิมพ์รายงาน</span>
                        </button>
                        <button class="btn-modern flex items-center space-x-2" id="exportBtn" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i>
                            <span>ส่งออก Excel</span>
                        </button>
                        <button class="btn-modern flex items-center space-x-2" id="refreshBtn" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                            <span>รีเฟรชข้อมูล</span>
                        </button>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="bg-white bg-opacity-60 rounded-xl p-6 shadow-inner">
                        <div class="flex flex-col md:flex-row items-center gap-4">
                            <!-- Term Selection -->
                            <div class="flex-1 w-full md:w-auto">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-graduation-cap mr-2"></i>เลือกภาคเรียน
                                </label>
                                <select class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-300" id="select_term">
                                    <option value="">โปรดเลือกภาคเรียน...</option>
                                    <option value="1">ภาคเรียนที่ 1</option>
                                    <option value="2">ภาคเรียนที่ 2</option>
                                </select>
                            </div>
                            
                            <!-- Filter Buttons -->
                            <div class="flex gap-3">
                                <button id="filter" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2">
                                    <i class="fas fa-search"></i>
                                    <span>ค้นหา</span>
                                </button>
                                <button id="reset" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2">
                                    <i class="fas fa-undo-alt"></i>
                                    <span>ล้างการค้นหา</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 animate-slide-up" id="summaryCards">
                    <!-- Summary cards will be loaded dynamically -->
                </div>

                <!-- Data Table Section -->
                <div class="glass-effect rounded-2xl p-6 mb-6 animate-slide-up">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-table mr-3 text-blue-600"></i>
                            ตารางสถิติรายละเอียด
                        </h3>
                        <div class="flex items-center space-x-4 no-print">
                            <!-- Search Box -->
                            <div class="relative">
                                <input type="text" id="table-search" placeholder="ค้นหาในตาราง..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <!-- Loading indicator -->
                            <div id="loadingIndicator" class="hidden">
                                <i class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto bg-white rounded-xl shadow-inner">
                        <table id="record_table" class="w-full border-collapse">
                            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                                <tr>
                                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">รายการ</th>
                                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">คำตอบ</th>
                                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">จำนวนนักเรียน(คน)</th>
                                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">ร้อยละ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-8">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-spinner fa-spin text-blue-600 text-3xl mb-4"></i>
                                            <span class="text-gray-600">กำลังโหลดข้อมูล...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="glass-effect rounded-2xl p-6 animate-slide-up">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie mr-3 text-purple-600"></i>
                            กราฟแสดงผลทางสถิติ
                        </h3>
                        <div class="flex space-x-2 no-print">
                            <button onclick="toggleChartType('doughnut')" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-all duration-300">
                                <i class="fas fa-chart-pie mr-1"></i>Pie Chart
                            </button>
                            <button onclick="toggleChartType('bar')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-all duration-300">
                                <i class="fas fa-chart-bar mr-1"></i>Bar Chart
                            </button>
                        </div>
                    </div>
                    
                    <div id="chartsContainer" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="chart-container flex items-center justify-center min-h-64">
                            <div class="text-center">
                                <i class="fas fa-chart-pie text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-600">รอการโหลดข้อมูลกราฟ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->

<!-- Modal for Editing Visit -->


<?php require_once('script.php'); ?>

<!-- Enhanced JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize page with animations
    initializeAnimations();
    setupEventListeners();
    
    // Global variables for charts
    let currentChartType = 'doughnut';
    let chartsData = {};
    let chartInstances = {};
    
    // Enhanced loading states
    function showLoading() {
        $('#loadingIndicator').removeClass('hidden');
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    function hideLoading() {
        $('#loadingIndicator').addClass('hidden');
        Swal.close();
    }

    // Initialize animations and effects
    function initializeAnimations() {
        // Add stagger animation to elements
        $('.animate-slide-up').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
        
        // Add intersection observer for scroll animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            });
            
            document.querySelectorAll('.chart-container').forEach(el => {
                observer.observe(el);
            });
        }
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Enhanced print function
        window.printPage = function() {
            showPrintPreview();
        };
        
        // Export to Excel function
        window.exportToExcel = function() {
            exportTableToExcel();
        };
        
        // Refresh data function
        window.refreshData = function() {
            refreshAllData();
        };
        
        // Chart type toggle
        window.toggleChartType = function(type) {
            currentChartType = type;
            updateAllCharts();
        };
        
        // Table search with debounce
        let searchTimeout;
        $('#table-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterTable($(this).val());
            }, 300);
        });
    }

    // Enhanced print functionality
    function showPrintPreview() {
        Swal.fire({
            title: 'พิมพ์รายงาน',
            text: 'เลือกรูปแบบการพิมพ์',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'พิมพ์แบบสี',
            denyButtonText: 'พิมพ์ขาว-ดำ',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#3b82f6',
            denyButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed || result.isDenied) {
                const colorMode = result.isConfirmed;
                printReport(colorMode);
            }
        });
    }
    
    function printReport(colorMode = true) {
        const elementsToHide = $('.no-print');
        elementsToHide.hide();
        
        if (!colorMode) {
            $('body').addClass('print-bw');
        }
        
        setTimeout(() => {
            window.print();
            elementsToHide.show();
            $('body').removeClass('print-bw');
        }, 500);
    }

    // Export to Excel functionality
    function exportTableToExcel() {
        try {
            const table = document.getElementById('record_table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "รายงานการเยี่ยมบ้าน"});
            const filename = `รายงานการเยี่ยมบ้าน_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, filename);
            
            Swal.fire({
                icon: 'success',
                title: 'ส่งออกสำเร็จ!',
                text: 'ไฟล์ถูกดาวน์โหลดแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถส่งออกไฟล์ได้'
            });
        }
    }

    // Refresh data functionality
    function refreshAllData() {
        const selectedTerm = $('#select_term').val();
        if (selectedTerm) {
            showLoading();
            loadTableByTerm(selectedTerm);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกภาคเรียน',
                text: 'โปรดเลือกภาคเรียนก่อนรีเฟรชข้อมูล'
            });
        }
    }

    // Enhanced table filtering
    function filterTable(searchTerm) {
        const rows = $('#record_table tbody tr');
        searchTerm = searchTerm.toLowerCase();
        
        rows.each(function() {
            const rowText = $(this).text().toLowerCase();
            const isVisible = rowText.includes(searchTerm) || searchTerm === '';
            $(this).toggle(isVisible);
            
            if (isVisible && searchTerm) {
                $(this).addClass('highlight-search');
            } else {
                $(this).removeClass('highlight-search');
            }
        });
        
        // Update row count
        const visibleRows = rows.filter(':visible').length;
        updateTableFooter(visibleRows, rows.length);
    }
    
    function updateTableFooter(visible, total) {
        let footerText = `แสดง ${visible} จาก ${total} รายการ`;
        if (visible !== total) {
            footerText += ` (กรองแล้ว)`;
        }
        
        $('#tableFooter').remove();
        $('#record_table').after(`
            <div id="tableFooter" class="text-center text-sm text-gray-600 mt-2 p-2 bg-gray-50 rounded">
                ${footerText}
            </div>
        `);
    }


    // Enhanced data loading with better error handling and animations
    async function loadTableByTerm(term) {
        try {
            showLoading();
            
            const classValue = <?= $class ?>;
            const roomValue = <?= $room ?>;
            const peeValue = <?= $pee ?>;

            console.log('Loading data for:', { classValue, roomValue, peeValue, term });

            const response = await $.ajax({
                url: 'api/fetch_visithomeclass.php',
                method: 'GET',
                dataType: 'json',
                data: { class: classValue, room: roomValue, pee: peeValue, term: term }
            });

            let dataArray = Array.isArray(response) ? response : 
                           (response?.data && Array.isArray(response.data)) ? response.data : [];

            if (dataArray && dataArray.length > 0) {
                console.log('Processing data entries:', dataArray.length);
                
                // Update summary cards
                updateSummaryCards(dataArray);
                
                // Group data by item_type
                const groupedByType = {};
                dataArray.forEach(item => {
                    if (!groupedByType[item.item_type]) {
                        groupedByType[item.item_type] = [];
                    }
                    groupedByType[item.item_type].push(item);
                });
                
                // Build enhanced table
                buildEnhancedTable(groupedByType);
                
                // Update charts with animation
                updateChartsWithAnimation(dataArray);
                
                // Store data for exports
                chartsData = dataArray;
                
                hideLoading();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'โหลดข้อมูลสำเร็จ!',
                    text: `พบข้อมูล ${dataArray.length} รายการ`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
            } else {
                displayNoDataMessage();
                hideLoading();
            }
            
        } catch (error) {
            console.error('Error in loadTableByTerm:', error);
            hideLoading();
            
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                confirmButtonColor: '#3b82f6'
            });
            
            displayErrorMessage();
        }
    }
    
    // Create summary statistics cards
    function updateSummaryCards(data) {
        // Get actual total students from class and room
        const classValue = <?= $class ?>;
        const roomValue = <?= $room ?>;
        
        // Fetch total students count
        $.ajax({
            url: 'api/get_class_student_count.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue },
            success: function(response) {
                const totalStudents = response.total || 0;
                renderSummaryCards(data, totalStudents);
            },
            error: function() {
                // Fallback to estimated count if API fails
                const totalStudents = <?php 
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_major = ? AND Stu_room = ? AND Stu_status = 1");
                    $stmt->execute([$class, $room]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo $result['total'];
                ?>;
                renderSummaryCards(data, totalStudents);
            }
        });
    }
    
    function renderSummaryCards(data, totalStudents) {
        const totalCategories = new Set(data.map(item => item.item_type)).size;
        const avgPercentage = data.reduce((sum, item) => sum + parseFloat(item.Persent || 0), 0) / data.length;
        const highestCategory = data.reduce((max, item) => 
            parseInt(item.Stu_total || 0) > parseInt(max.Stu_total || 0) ? item : max, data[0]);

        const summaryCards = `
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover animate-slide-up">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">จำนวนนักเรียนทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900">${totalStudents}</p>
                        <p class="text-xs text-gray-500">ม.${<?= $class ?>}/${<?= $room ?>}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover animate-slide-up">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-list text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">หมวดหมู่ทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900">${totalCategories}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover animate-slide-up">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-percentage text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">เปอร์เซ็นต์เฉลี่ย</p>
                        <p class="text-2xl font-bold text-gray-900">${avgPercentage.toFixed(1)}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-lg card-hover animate-slide-up">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">หมวดสูงสุด</p>
                        <p class="text-lg font-bold text-gray-900">${highestCategory.item_type}</p>
                        <p class="text-sm text-gray-500">${highestCategory.Stu_total} คน</p>
                    </div>
                </div>
            </div>
        `;
        
        $('#summaryCards').html(summaryCards);
    }
    
    // Build enhanced table with modern styling
    function buildEnhancedTable(groupedByType) {
        const tableHeader = `
            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <tr>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">รายการ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">คำตอบ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">จำนวนนักเรียน(คน)</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">ร้อยละ</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        let tableRows = '';
        
        Object.keys(groupedByType).forEach(type => {
            const items = groupedByType[type];
            if (items.length > 0) {
                items.forEach((item, index) => {
                    const rowClass = 'table-row-hover border-b border-gray-200 transition-all duration-200';
                    
                    let row = `<tr class="${rowClass}">`;
                    
                    // Add question cell with rowspan for first row
                    if (index === 0) {
                        row += `<td class="border border-gray-200 px-6 py-4 text-center font-medium bg-gray-50" rowspan="${items.length}">${type}</td>`;
                    }
                    
                    // Add other cells with enhanced styling
                    row += `
                        <td class="border border-gray-200 px-6 py-3">${item.item_list}</td>
                        <td class="border border-gray-200 px-6 py-3 text-center font-semibold text-blue-600">${item.Stu_total}</td>
                        <td class="border border-gray-200 px-6 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-4 relative overflow-hidden">
                                    <div class="progress-bar h-full rounded-full" style="width: ${item.Persent}%; background: ${item.bg_color || 'linear-gradient(90deg, #667eea, #764ba2)'}"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 min-w-12">${item.Persent}%</span>
                            </div>
                        </td>
                    `;
                    
                    row += '</tr>';
                    tableRows += row;
                });
            }
        });
        
        $('#record_table').html(tableHeader + tableRows + '</tbody>');
        
        // Add table footer
        updateTableFooter(Object.keys(groupedByType).length, Object.keys(groupedByType).length);
    }
    
    // Enhanced chart updates with animations
    function updateChartsWithAnimation(data) {
        if (!data || !Array.isArray(data) || data.length === 0) {
            displayNoChartsMessage();
            return;
        }

        const chartData = {};
        data.forEach(item => {
            if (!chartData[item.item_type]) {
                chartData[item.item_type] = [];
            }
            chartData[item.item_type].push(item);
        });

        // Clear existing charts
        Object.values(chartInstances).forEach(chart => chart.destroy());
        chartInstances = {};
        
        const chartsContainer = document.getElementById('chartsContainer');
        chartsContainer.innerHTML = '';

        // Modern color palette
        const modernColors = [
            'rgba(99, 102, 241, 0.8)',   // Indigo
            'rgba(16, 185, 129, 0.8)',   // Emerald
            'rgba(245, 101, 101, 0.8)',  // Red
            'rgba(251, 191, 36, 0.8)',   // Amber
            'rgba(139, 92, 246, 0.8)',   // Violet
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(6, 182, 212, 0.8)',    // Cyan
            'rgba(34, 197, 94, 0.8)',    // Green
        ];

        // Create enhanced charts
        Object.keys(chartData).forEach((itemType, typeIndex) => {
            const items = chartData[itemType];
            const labels = items.map(item => item.item_list);
            const values = items.map(item => parseInt(item.Stu_total));
            
            const bgColors = items.map((_, index) => modernColors[index % modernColors.length]);

            // Create chart container
            const chartContainer = document.createElement('div');
            chartContainer.className = 'chart-container animate-slide-up';
            chartContainer.style.animationDelay = `${typeIndex * 0.1}s`;

            const chartTitle = document.createElement('h4');
            chartTitle.className = 'text-lg font-bold text-center mb-4 text-gray-800';
            chartTitle.innerHTML = `
                <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                    <span>${itemType}</span>
                </div>
            `;
            chartContainer.appendChild(chartTitle);

            const canvas = document.createElement('canvas');
            canvas.id = `chart-${itemType}-${typeIndex}`;
            canvas.style.maxHeight = '300px';
            chartContainer.appendChild(canvas);

            chartsContainer.appendChild(chartContainer);

            // Create chart with enhanced options
            const ctx = canvas.getContext('2d');
            const chartInstance = new Chart(ctx, {
                type: currentChartType,
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    family: 'Prompt'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#667eea',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} คน (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            chartInstances[`${itemType}-${typeIndex}`] = chartInstance;
        });
    }
    
    // Update all charts when type changes
    function updateAllCharts() {
        if (chartsData && chartsData.length > 0) {
            updateChartsWithAnimation(chartsData);
        }
    }
    
    // Display no data message
    function displayNoDataMessage() {
        $('#record_table').html(`
            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <tr>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">รายการ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">คำตอบ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">จำนวนนักเรียน(คน)</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">ร้อยละ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">ไม่พบข้อมูล</h3>
                            <p class="text-gray-500">ไม่มีข้อมูลการเยี่ยมบ้านในภาคเรียนที่เลือก</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        `);
        
        $('#summaryCards').html(`
            <div class="col-span-4 text-center py-8">
                <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">ไม่มีข้อมูลสำหรับแสดงสถิติ</p>
            </div>
        `);
        
        displayNoChartsMessage();
    }
    
    function displayNoChartsMessage() {
        document.getElementById('chartsContainer').innerHTML = `
            <div class="col-span-full chart-container flex items-center justify-center min-h-64">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">ไม่มีข้อมูลกราฟ</h3>
                    <p class="text-gray-500">กรุณาเลือกภาคเรียนเพื่อแสดงกราฟ</p>
                </div>
            </div>
        `;
    }
      function displayErrorMessage() {
        $('#record_table').html(`
            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <tr>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">รายการ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">คำตอบ</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">จำนวนนักเรียน(คน)</th>
                    <th class="border border-gray-200 px-6 py-4 text-center font-semibold">ร้อยละ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-red-600 mb-2">เกิดข้อผิดพลาด</h3>
                            <p class="text-red-500">ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        `);
    }
    
    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printPage();
        }
        
        // Ctrl+S for export
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            exportToExcel();
        }
        
        // F5 for refresh
        if (e.key === 'F5') {
            e.preventDefault();
            refreshData();
        }
        
        // Escape to close search
        if (e.key === 'Escape') {
            $('#table-search').val('').trigger('input');
            $('#table-search').blur();
        }
    });
    
    // Auto-refresh data every 5 minutes (optional)
    setInterval(function() {
        const selectedTerm = $('#select_term').val();
        if (selectedTerm && document.visibilityState === 'visible') {
            console.log('Auto-refreshing data...');
            loadTableByTerm(selectedTerm);
        }
    }, 300000); // 5 minutes
    
    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Page became visible, refresh if data is stale
            const lastUpdate = sessionStorage.getItem('lastDataUpdate');
            const now = Date.now();
            if (lastUpdate && (now - parseInt(lastUpdate)) > 300000) { // 5 minutes
                const selectedTerm = $('#select_term').val();
                if (selectedTerm) {
                    refreshData();
                }
            }
        }
    });
    
    // Store timestamp when data is loaded
    function updateLastDataTimestamp() {
        sessionStorage.setItem('lastDataUpdate', Date.now().toString());
    }
    
    // Enhanced data loading with timestamp tracking
    const originalLoadTableByTerm = loadTableByTerm;
    loadTableByTerm = function(term) {
        originalLoadTableByTerm(term).then(() => {
            updateLastDataTimestamp();
        }).catch(() => {
            // Error handling already done in original function
        });
    };
    
    // Responsive design improvements
    function adjustForMobile() {
        if (window.innerWidth < 768) {
            // Mobile optimizations
            $('.glass-effect').removeClass('p-8').addClass('p-4');
            $('.btn-modern').removeClass('px-6').addClass('px-4');
            $('.text-4xl').removeClass('text-4xl').addClass('text-2xl');
        }
    }
    
    // Call on resize
    window.addEventListener('resize', adjustForMobile);
    adjustForMobile(); // Call initially
      // Event listeners for enhanced functionality
    $('#filter').on('click', function() {
        const selectedTerm = $('#select_term').val();
        if (selectedTerm) {
            loadTableByTerm(selectedTerm);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกภาคเรียน',
                text: 'โปรดเลือกภาคเรียนก่อนค้นหาข้อมูล',
                confirmButtonColor: '#3b82f6'
            });
        }
    });

    $('#reset').on('click', function() {
        $('#select_term').val('');
        $('#table-search').val('');
        displayNoDataMessage();
    });

    $('#select_term').on('change', function() {
        const selectedTerm = $(this).val();
        if (selectedTerm) {
            loadTableByTerm(selectedTerm);
        } else {
            displayNoDataMessage();
        }
    });

    // Initialize page with default term
    function initializePage() {
        const defaultTerm = '1';
        $('#select_term').val(defaultTerm);
        loadTableByTerm(defaultTerm);
    }    // Call initialization
    initializePage();
});

</script>

<?php require_once('script.php'); ?>
<?php require_once('footer.php'); ?>
</body>
</html>
