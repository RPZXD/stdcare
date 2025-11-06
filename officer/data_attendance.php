<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../config/Setting.php");
include_once("../class/Utils.php");
// Officer สามารถดูข้อมูลทุกชั้นและห้อง โดยใช้ filter
require_once("../class/Attendance.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);


if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
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
$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);
$setting = new Setting();
$attendance = new Attendance($db);

// กำหนดวันที่ (วันนี้ หรือจาก GET)
function convertToBuddhistYear($date) {
    // ตรวจสอบว่ารูปแบบเป็น YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);

        // ถ้าเป็นปี ค.ศ. ให้บวก 543
        if ($year < 2500) {
            $year += 543;
        }

        return $year . '-' . $month . '-' . $day;
    }
    // ถ้า format ไม่ถูกต้อง คืนค่าเดิม
    return $date;
}

// ฟังก์ชันแปลงวันที่เป็น วัน เดือน ปี พ.ศ. ภาษาไทย
function thaiDate($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}

// ใช้งาน
date_default_timezone_set('Asia/Bangkok');
// ใช้ Gregorian date (ค.ศ.) สำหรับ database query
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Officer สามารถเลือก ชั้น และ ห้อง ได้จาก filter
$class = isset($_GET['class']) ? $_GET['class'] : null;
$room = isset($_GET['room']) ? $_GET['room'] : null;

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงข้อมูลนักเรียนตาม filter - ใช้ AJAX แทน
// $students = $attendance->getStudentsWithAttendance($date, $class, $room, $term, $pee);


require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    
<section class="content">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ข้อมูลการเข้าเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card shadow-2xl border-0 overflow-hidden">
            <!-- Modern Header with Enhanced Gradient -->
                <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8 text-white overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
                        <div class="absolute top-16 right-16 w-24 h-24 bg-white rounded-full opacity-50"></div>
                        <div class="absolute bottom-8 left-1/4 w-16 h-16 bg-white rounded-full opacity-30"></div>
                    </div>

                    <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="bg-white/20 backdrop-blur-lg rounded-2xl p-4 shadow-xl border border-white/20">
                                <span class="text-4xl">📊</span>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold mb-2 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                                    ข้อมูลการเช็คชื่อนักเรียน
                                </h1>
                                <div class="flex items-center gap-4 text-indigo-100">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">📅</span>
                                        <span class="font-medium"><?= htmlspecialchars(thaiDate(convertToBuddhistYear($date))) ?></span>
                                    </div>
                                    <?php if ($class): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">🏫</span>
                                            <span class="font-medium">ม.<?= htmlspecialchars($class) ?><?= $room ? '/' . htmlspecialchars($room) : '' ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Filter Form -->
                        <form method="get" class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20 min-w-[400px]">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="space-y-2">
                                    <label for="date" class="text-sm font-semibold text-indigo-100 flex items-center gap-2">
                                        <span>📆</span> เลือกวันที่
                                    </label>
                                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>"
                                        class="w-full px-3 py-2 bg-white/90 text-gray-800 rounded-lg border-0 focus:ring-2 focus:ring-white focus:bg-white transition-all duration-200 font-medium">
                                </div>

                                <div class="space-y-2">
                                    <label for="class" class="text-sm font-semibold text-indigo-100 flex items-center gap-2">
                                        <span>🎓</span> ชั้น
                                    </label>
                                    <select id="class" name="class" class="w-full px-3 py-2 bg-white/90 text-gray-800 rounded-lg border-0 focus:ring-2 focus:ring-white focus:bg-white transition-all duration-200 font-medium">
                                        <option value="">ทุกชั้น</option>
                                        <?php for($i = 1; $i <= 6; $i++): ?>
                                            <option value="<?= $i ?>" <?= $class == $i ? 'selected' : '' ?>>ม.<?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="room" class="text-sm font-semibold text-indigo-100 flex items-center gap-2">
                                        <span>🚪</span> ห้อง
                                    </label>
                                    <select id="room" name="room" class="w-full px-3 py-2 bg-white/90 text-gray-800 rounded-lg border-0 focus:ring-2 focus:ring-white focus:bg-white transition-all duration-200 font-medium">
                                        <option value="">ทุกห้อง</option>
                                        <?php for($i = 1; $i <= 15; $i++): ?>
                                            <option value="<?= $i ?>" <?= $room == $i ? 'selected' : '' ?>>ห้อง <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                                        <span>🔍</span>
                                        <span>ค้นหา</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Enhanced Info Banner -->
                <div class="mx-8 -mt-4 relative z-20">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-md">
                                <span class="text-2xl text-white">💡</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">ข้อมูลการเช็คชื่อ</h3>
                                <div class="grid md:grid-cols-2 gap-3 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span>รวมข้อมูลจาก RFID, ครูที่ปรึกษา และเจ้าหน้าที่</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <span>รองรับการ Export Excel และ CSV</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                        <span>ค้นหาด้วยชื่อหรือรหัสนักเรียนได้ทันที</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                        <span>โหลดเร็วด้วยเทคโนโลยีการแบ่งหน้า</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="overflow-x-auto">
                    <!-- DataTables CSS -->
                    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
                    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
                    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
                    <!-- DataTables JS + jQuery -->
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
                    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
                    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
                    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
                    <style>
                        /* Enhanced Modern Styling */

                        /* Table Container */
                        .table-container {
                            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                            border-radius: 1.5rem;
                            padding: 2rem;
                            margin: 2rem 0;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                        }

                        /* Enhanced Table Styling */
                        #attendance-table {
                            border-radius: 1rem;
                            overflow: hidden;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                            background: white;
                        }

                        #attendance-table thead th {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            font-weight: 700;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            font-size: 0.8rem;
                            padding: 1.5rem 0.75rem;
                            border: none;
                            position: relative;
                        }

                        #attendance-table thead th::after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 60%;
                            height: 2px;
                            background: rgba(255,255,255,0.3);
                            border-radius: 1px;
                        }

                        #attendance-table tbody tr {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            background: white;
                            border-bottom: 1px solid #f1f5f9;
                        }

                        #attendance-table tbody tr:hover {
                            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                            transform: translateX(8px) scale(1.01);
                            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
                            border-radius: 0.5rem;
                        }

                        #attendance-table tbody td {
                            border: none;
                            padding: 1rem 0.75rem;
                            vertical-align: middle;
                        }

                        /* Enhanced Status Badges */
                        .status-badge {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.375rem;
                            padding: 0.5rem 1rem;
                            border-radius: 2rem;
                            font-weight: 600;
                            font-size: 0.8rem;
                            letter-spacing: 0.025em;
                            text-transform: uppercase;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            transition: all 0.3s ease;
                            animation: badgeFadeIn 0.5s ease-out;
                        }

                        .status-badge:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
                        }

                        @keyframes badgeFadeIn {
                            from {
                                opacity: 0;
                                transform: translateY(10px) scale(0.9);
                            }
                            to {
                                opacity: 1;
                                transform: translateY(0) scale(1);
                            }
                        }

                        /* Enhanced DataTables Styling */
                        .dataTables_wrapper {
                            background: white;
                            border-radius: 1rem;
                            overflow: hidden;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                        }

                        .dataTables_wrapper .dataTables_filter input {
                            border: 2px solid #e2e8f0;
                            border-radius: 0.75rem;
                            padding: 0.75rem 1.25rem;
                            font-size: 0.9rem;
                            transition: all 0.3s ease;
                            background: #f8fafc;
                        }

                        .dataTables_wrapper .dataTables_filter input:focus {
                            border-color: #667eea;
                            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
                            background: white;
                            outline: none;
                        }

                        .dataTables_wrapper .dataTables_length select {
                            border: 2px solid #e2e8f0;
                            border-radius: 0.75rem;
                            padding: 0.5rem 1rem;
                            background: #f8fafc;
                            font-weight: 500;
                        }

                        .dataTables_wrapper .dataTables_info {
                            color: #64748b;
                            font-weight: 500;
                            padding: 1rem;
                        }

                        .dataTables_wrapper .dataTables_paginate .paginate_button {
                            padding: 0.5rem 1rem;
                            margin: 0 0.125rem;
                            border-radius: 0.5rem;
                            border: 2px solid #e2e8f0;
                            background: white;
                            color: #64748b;
                            font-weight: 500;
                            transition: all 0.3s ease;
                        }

                        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                            background: #667eea;
                            color: white;
                            border-color: #667eea;
                            transform: translateY(-2px);
                        }

                        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                            background: #667eea;
                            color: white;
                            border-color: #667eea;
                            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                        }

                        /* Enhanced Loading State */
                        .dataTables_processing {
                            background: rgba(255, 255, 255, 0.95);
                            border-radius: 1rem;
                            padding: 2rem;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                            backdrop-filter: blur(10px);
                        }

                        /* Enhanced Mobile Responsiveness */
                        @media (max-width: 768px) {
                            .table-container {
                                padding: 1rem;
                                margin: 1rem 0;
                            }

                            #attendance-table {
                                font-size: 0.75rem;
                            }

                            #attendance-table thead th,
                            #attendance-table tbody td {
                                padding: 0.5rem 0.375rem;
                            }

                            .status-badge {
                                font-size: 0.7rem;
                                padding: 0.375rem 0.75rem;
                            }

                            .dataTables_wrapper .dataTables_filter input {
                                padding: 0.5rem 1rem;
                                font-size: 0.8rem;
                            }
                        }

                        /* Enhanced Fixed Columns */
                        .DTFC_LeftBodyWrapper table.dataTable tbody tr:hover td {
                            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
                        }

                        /* Custom Scrollbar */
                        .dataTables_scrollBody::-webkit-scrollbar {
                            width: 8px;
                            height: 8px;
                        }

                        .dataTables_scrollBody::-webkit-scrollbar-track {
                            background: #f1f5f9;
                            border-radius: 4px;
                        }

                        .dataTables_scrollBody::-webkit-scrollbar-thumb {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border-radius: 4px;
                        }

                        .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
                            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
                        }

                        /* Enhanced Export Buttons */
                        .dt-buttons {
                            margin-bottom: 1rem;
                        }

                        .dt-buttons .dt-button {
                            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                            color: white;
                            border: none;
                            padding: 0.75rem 1.5rem;
                            border-radius: 0.75rem;
                            font-weight: 600;
                            margin-right: 0.5rem;
                            transition: all 0.3s ease;
                            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                        }

                        .dt-buttons .dt-button:hover {
                            background: linear-gradient(135deg, #059669 0%, #047857 100%);
                            transform: translateY(-2px);
                            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
                        }

                        /* Student Name Styling */
                        .student-name {
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                        }

                        .student-avatar {
                            width: 2rem;
                            height: 2rem;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-weight: bold;
                            font-size: 0.8rem;
                            flex-shrink: 0;
                        }

                        /* Time Display Enhancement */
                        .time-display {
                            display: flex;
                            flex-direction: column;
                            gap: 0.25rem;
                        }

                        .time-badge {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.25rem;
                            padding: 0.25rem 0.5rem;
                            border-radius: 0.5rem;
                            font-size: 0.7rem;
                            font-weight: 600;
                        }

                        .time-badge.arrival {
                            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                            color: white;
                        }

                        .time-badge.departure {
                            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                            color: white;
                        }
                    </style>
                    <form id="attendance-form" method="post">
                        <?php
                        // ส่งวันที่แบบ Gregorian (ค.ศ.) ไปยัง API
                        ?>
                        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
                        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
                        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
                        <div class="table-wrapper overflow-x-auto">
                        <table id="attendance-table" class="min-w-[1000px] border-0 rounded-xl overflow-hidden bg-white w-full">
                            <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl">🔢</span>
                                            <span class="text-xs font-medium">เลขที่</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl">🆔</span>
                                            <span class="text-xs font-medium">รหัสนักเรียน</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-6 border-0 text-left font-bold">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">👤</span>
                                            <span>ชื่อ-สกุล</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl">✅</span>
                                            <span class="text-xs font-medium">สถานะ</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl"></span>
                                            <span class="text-xs font-medium">เวลาสแกน</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl">💬</span>
                                            <span class="text-xs font-medium">สาเหตุ</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-6 border-0 text-center font-bold">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-2xl">👨‍🏫</span>
                                            <span class="text-xs font-medium">เช็คโดย</span>
                                        </div>
                                    </th>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                        <!-- Bulk save functionality removed for performance optimization -->
                        <!-- Use individual edit pages for attendance modifications -->
                    </form>
                </div>    

            </div>
        </div>
    </div>


</section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // small helper to escape HTML when injecting text
    function escapeHtml(str){
        if (!str && str !== 0) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Helper functions for rendering
    function getStatusBadge(status) {
        if (!status) {
            return '<span class="status-badge unknown">⚠️ ยังไม่เช็ค</span>';
        }
        
        switch (String(status)) {
            case '1':
                return '<span class="status-badge present">✅ มาเรียน</span>';
            case '2':
                return '<span class="status-badge absent">❌ ขาดเรียน</span>';
            case '3':
                return '<span class="status-badge late">⏰ มาสาย</span>';
            case '4':
                return '<span class="status-badge leave">🏥 ลาป่วย</span>';
            case '5':
                return '<span class="status-badge leave">📄 ลากิจ</span>';
            case '6':
                return '<span class="status-badge activity">🎯 กิจกรรมโรงเรียน</span>';
            default:
                return '<span class="status-badge unknown">➖</span>';
        }
    }

    function getScanTimes(arrival, leave) {
        let html = '';
        if (arrival || leave) {
            html += '<div class="time-display">';
            if (arrival) {
                html += '<span class="time-badge arrival">🔵 เข้า: ' + escapeHtml(arrival.substring(0, 5)) + '</span>';
            }
            if (leave) {
                html += '<span class="time-badge departure">🔴 ออก: ' + escapeHtml(leave.substring(0, 5)) + '</span>';
            }
            html += '</div>';
        } else {
            html = '<span class="text-gray-400 text-sm">➖</span>';
        }
        return html;
    }

    function getCheckedByBadge(row) {
        if (row.checked_by) {
            if (row.checked_by === 'system' || row.checked_by === 'teacher') {
                return '<span class="checked-by-badge teacher">👨‍🏫 ครูที่ปรึกษา</span>';
            } else if (row.checked_by === 'officer') {
                return '<span class="checked-by-badge officer">👮 เจ้าหน้าที่</span>';
            } else if (row.checked_by === 'rfid' || row.checked_by === 'RFID') {
                let timeStr = '';
                if (row.attendance_time) {
                    timeStr = ' <span class="text-xs opacity-90">(' + escapeHtml(row.attendance_time.substring(0, 5)) + ')</span>';
                }
                return '<span class="checked-by-badge rfid">💳 สแกนบัตร' + timeStr + '</span>';
            } else {
                return '<span class="checked-by-badge manual">' + escapeHtml(row.checked_by) + '</span>';
            }
        } else {
            return '<span class="text-gray-400">➖</span>';
        }
    }
    // Handle filter form submission
    document.querySelector('form[method="get"]').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = window.location.pathname + '?' + params.toString();
    });

    // DataTables initialization with server-side processing
    var table = $('#attendance-table').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: 'api/api_attendance_data.php',
            type: 'GET',
            data: function(d) {
                // Add filter parameters
                d.date = '<?= htmlspecialchars($date) ?>';
                d.class = '<?= htmlspecialchars($class ?? '') ?>';
                d.room = '<?= htmlspecialchars($room ?? '') ?>';
                d.term = '<?= htmlspecialchars($term ?? '') ?>';
                d.pee = '<?= htmlspecialchars($pee ?? '') ?>';
            }
        },
        responsive: false,
        autoWidth: false,
        lengthChange: true,
        pageLength: 50,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollCollapse: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'ข้อมูลการเช็คชื่อ - <?= thaiDate(convertToBuddhistYear($date)) ?>',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> Export CSV',
                className: 'btn btn-info btn-sm',
                title: 'ข้อมูลการเช็คชื่อ',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        fixedColumns: {
            leftColumns: 2  // ตึงคอลัมน์แรก 2 คอลัมน์ (เลขที่ + รหัส)
        },
        columns: [
            {
                data: 'stu_no',
                className: "text-center font-bold",
                width: "70px",
                orderable: true,
                render: function(data, type, row) {
                    return '<td class="px-3 py-3 text-center font-bold text-lg text-indigo-600">' + escapeHtml(data) + '</td>';
                }
            },
            {
                data: 'stu_id',
                className: "text-center",
                width: "100px",
                render: function(data, type, row) {
                    return '<td class="px-3 py-3 font-mono text-sm text-gray-600">' + escapeHtml(data) + '</td>';
                }
            },
            {
                data: 'stu_name',
                className: "text-left font-semibold",
                width: "220px",
                orderable: true,
                render: function(data, type, row) {
                    const firstName = data.split(' ')[0] || '';
                    const initial = firstName.charAt(0).toUpperCase();
                    return '<td class="px-6 py-4 font-semibold text-gray-800"><div class="student-name"><div class="student-avatar">' + escapeHtml(initial) + '</div><span class="truncate">' + escapeHtml(data) + '</span></div></td>';
                }
            },
            {
                data: 'attendance_status',
                className: "text-center",
                width: "100px",
                render: function(data, type, row) {
                    return '<td class="px-4 py-3 text-center">' + getStatusBadge(data) + '</td>';
                }
            },
            {
                data: null,
                className: "text-center",
                width: "120px",
                orderable: false,
                render: function(data, type, row) {
                    return '<td class="px-4 py-3 text-center">' + getScanTimes(row.arrival_time, row.leave_time) + '</td>';
                }
            },
            {
                data: 'reason',
                className: "text-center",
                width: "150px",
                orderable: false,
                render: function(data, type, row) {
                    if (row.has_attendance) {
                        return '<td class="px-4 py-3 text-center">' + (data ? '<span class="text-gray-700 font-medium">' + escapeHtml(data) + '</span>' : '<span class="text-gray-400">➖</span>') + '</td>';
                    } else {
                        return '<td class="px-4 py-3 text-center"><input type="text" name="reason[' + escapeHtml(row.stu_id) + ']" placeholder="💬 สาเหตุ (ถ้ามี)" class="border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition w-full max-w-xs" /></td>';
                    }
                }
            },
            {
                data: null,
                className: "text-center",
                width: "130px",
                orderable: false,
                render: function(data, type, row) {
                    return '<td class="px-4 py-3 text-center">' + getCheckedByBadge(row) + '</td>';
                }
            }
        ],
        order: [[0, 'asc']], // เรียงตามเลขที่
        language: {
            processing: '<div class="flex items-center justify-center p-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><span class="ml-2">กำลังโหลด...</span></div>',
            search: "🔍 ค้นหา:",
            searchPlaceholder: "ค้นหาชื่อนักเรียน...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ คน",
            infoEmpty: "ไม่พบข้อมูล",
            infoFiltered: "(กรองจากทั้งหมด _MAX_ คน)",
            zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // เมื่อกดปุ่ม "แก้ไข"
    document.querySelectorAll('.edit-attendance-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // ปิดฟอร์มอื่นๆ ก่อน (Set display to none, CSS rule will take over)
            document.querySelectorAll('.edit-attendance-form').forEach(function(f) {
                f.style.display = 'none';
            });
            // เปิดฟอร์มของแถวนี้
            var tr = btn.closest('tr');
            if (tr) {
                var form = tr.querySelector('.edit-attendance-form');
                if (form) {
                    // Use setProperty to override the CSS !important rule
                    form.style.setProperty('display', 'block', 'important');
                }
            }
        });
    });
    // เมื่อกดปุ่ม "ยกเลิก"
    document.querySelectorAll('.cancel-edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var form = btn.closest('.edit-attendance-form');
            if (form) {
                // Set display to none, the CSS rule will ensure it stays hidden
                form.style.display = 'none';
            }
        });
    });

    // เมื่อกดปุ่ม "บันทึก" ในแผงแก้ไข ให้ส่งข้อมูลด้วย fetch (ป้องกัน nested form)
    document.querySelectorAll('.save-edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var panel = btn.closest('.edit-attendance-form');
            if (!panel) return;

            var formData = new FormData();
            // collect all inputs and radios inside the panel
            panel.querySelectorAll('input').forEach(function(input) {
                if (!input.name) return;
                if (input.type === 'radio') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    }
                } else if (input.type === 'checkbox') {
                    if (input.checked) formData.append(input.name, input.value);
                } else {
                    formData.append(input.name, input.value);
                }
            });

            // send to controller endpoint (use save_bulk which accepts the same form payload)
            // disable button to avoid duplicate clicks
            if (btn.dataset.busy === '1') return;
            btn.dataset.busy = '1';
            var origBtnText = btn.innerHTML;
            btn.innerHTML = 'กำลังบันทึก...';
            btn.disabled = true;

            fetch('../controllers/AttendanceController.php?action=save_bulk', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(resp) {
                return resp.json();
            }).then(function(json) {
                if (json && json.success) {
                    // update rows in-place if results provided
                    if (json.results) {
                        Object.keys(json.results).forEach(function(stuId){
                            var info = json.results[stuId];
                            var tr = document.querySelector('tr[data-stu-id="' + stuId + '"]');
                            if (!tr) return;
                            // Update cell indices: status=3, scanTime=4, reason=5, checked=6
                            var statusCell = tr.cells[3];
                            var reasonCell = tr.cells[5];
                            var checkedCell = tr.cells[6];
                            // render status badge
                            function renderStatusBadge(code){
                                if (!code) return '<span class="status-badge unknown">➖</span>';
                                switch (String(code)){
                                    case '1': return '<span class="status-badge present">✅ มาเรียน</span>';
                                    case '2': return '<span class="status-badge absent">❌ ขาดเรียน</span>';
                                    case '3': return '<span class="status-badge late">🕒 มาสาย</span>';
                                    case '4': return '<span class="status-badge leave">🤒 ลาป่วย</span>';
                                    case '5': return '<span class="status-badge leave">📝 ลากิจ</span>';
                                    case '6': return '<span class="status-badge activity">🎉 เข้าร่วมกิจกรรม</span>';
                                    default: return '<span class="status-badge unknown">➖</span>';
                                }
                            }

                            statusCell.innerHTML = renderStatusBadge(info && info.attendance_status ? info.attendance_status : null);
                            reasonCell.innerHTML = info && info.reason ? '<span class="text-gray-700 font-medium">' + escapeHtml(info.reason) + '</span>' : '<span class="text-gray-400">➖</span>';
                            // checked_by rendering
                            var cb = info && info.checked_by ? info.checked_by : null;
                            if (cb === 'system' || cb === 'teacher') {
                                checkedCell.innerHTML = '<span class="checked-by-badge teacher">👨‍🏫 ครูที่ปรึกษา</span>';
                            } else if (cb === 'officer') {
                                checkedCell.innerHTML = '<span class="checked-by-badge officer">👮 เจ้าหน้าที่</span>';
                            } else if (cb === 'rfid' || cb === 'RFID') {
                                var t = info.attendance_time ? (' <span class="text-xs opacity-90">(' + info.attendance_time.substring(0,5) + ')</span>') : '';
                                checkedCell.innerHTML = '<span class="checked-by-badge rfid">💳 สแกนบัตร' + t + '</span>';
                            } else if (cb) {
                                checkedCell.innerHTML = '<span class="checked-by-badge manual">' + escapeHtml(cb) + '</span>';
                            } else {
                                checkedCell.innerHTML = '<span class="text-gray-400">➖</span>';
                            }

                            // hide edit form if any
                            var editForm = tr.querySelector('.edit-attendance-form');
                            if (editForm) editForm.style.display = 'none';
                        });
                    }

                    // show toast if available
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: (json.saved ? (json.saved + ' รายการ') : 'สำเร็จ'),
                            timer: 1500,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                        // reload to ensure UI (edit buttons/forms) render correctly
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        alert('บันทึกสำเร็จ');
                        setTimeout(function(){ location.reload(); }, 500);
                    }
                } else {
                    alert('บันทึกล้มเหลว: ' + (json && json.error ? json.error : 'Unknown error'));
                    btn.disabled = false;
                    btn.dataset.busy = '0';
                    btn.innerHTML = origBtnText;
                }
            }).catch(function(err) {
                console.error(err);
                alert('เกิดข้อผิดพลาดในการบันทึก');
                btn.disabled = false;
                btn.dataset.busy = '0';
                btn.innerHTML = origBtnText;
            });
        });
    });

    // Intercept bulk save to use AJAX and prevent duplicate submissions
    const attendanceForm = document.getElementById('attendance-form');
    const saveBtn = document.getElementById('btn-save-bulk');
    if (attendanceForm && saveBtn) {
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveBtn.dataset.busy === '1') return; // already submitting
            saveBtn.dataset.busy = '1';
            const origText = saveBtn.innerHTML;
            saveBtn.innerHTML = 'กำลังบันทึก...';
            saveBtn.disabled = true;

            // Only include students without existing attendance (no edit button)
            const formData = new FormData();
            
            // Add date, term, pee from form
            formData.append('date', attendanceForm.querySelector('input[name="date"]').value);
            formData.append('term', attendanceForm.querySelector('input[name="term"]').value);
            formData.append('pee', attendanceForm.querySelector('input[name="pee"]').value);
            
            // Only collect data from rows that DON'T have the edit button (no existing attendance)
            const allRows = document.querySelectorAll('tr[data-stu-id]');
            allRows.forEach(function(row) {
                const hasEditBtn = row.querySelector('.edit-attendance-btn');
                if (hasEditBtn) {
                    // Skip - this student already has attendance record
                    return;
                }
                
                // Collect data for new attendance records only
                const stuId = row.dataset.stuId;
                const radioChecked = row.querySelector('input[name="attendance_status[' + stuId + ']"]:checked');
                const reasonInput = row.querySelector('input[name="reason[' + stuId + ']"]');
                const teachIdInput = row.querySelector('input[name="teach_id[' + stuId + ']"]');
                const behaviorTypeInput = row.querySelector('input[name="behavior_type[' + stuId + ']"]');
                const behaviorNameInput = row.querySelector('input[name="behavior_name[' + stuId + ']"]');
                const behaviorScoreInput = row.querySelector('input[name="behavior_score[' + stuId + ']"]');
                
                if (radioChecked) {
                    formData.append('Stu_id[]', stuId);
                    formData.append('attendance_status[' + stuId + ']', radioChecked.value);
                    if (reasonInput && reasonInput.value) {
                        formData.append('reason[' + stuId + ']', reasonInput.value);
                    }
                    if (teachIdInput) {
                        formData.append('teach_id[' + stuId + ']', teachIdInput.value);
                    }
                    if (behaviorTypeInput) {
                        formData.append('behavior_type[' + stuId + ']', behaviorTypeInput.value);
                    }
                    if (behaviorNameInput) {
                        formData.append('behavior_name[' + stuId + ']', behaviorNameInput.value);
                    }
                    if (behaviorScoreInput) {
                        formData.append('behavior_score[' + stuId + ']', behaviorScoreInput.value);
                    }
                }
            });

            fetch('../controllers/AttendanceController.php?action=save_bulk', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(resp){
                return resp.json();
            }).then(function(json){
                if (json && json.success) {
                    if (json.results) {
                        Object.keys(json.results).forEach(function(stuId){
                            var info = json.results[stuId];
                            var tr = document.querySelector('tr[data-stu-id="' + stuId + '"]');
                            if (!tr) return;
                            // Update cell indices: status=3, scanTime=4, reason=5, checked=6
                            var statusCell = tr.cells[3];
                            var reasonCell = tr.cells[5];
                            var checkedCell = tr.cells[6];
                            function renderStatusBadge(code){
                                if (!code) return '<span class="status-badge unknown">➖</span>';
                                switch (String(code)){
                                    case '1': return '<span class="status-badge present">✅ มาเรียน</span>';
                                    case '2': return '<span class="status-badge absent">❌ ขาดเรียน</span>';
                                    case '3': return '<span class="status-badge late">🕒 มาสาย</span>';
                                    case '4': return '<span class="status-badge leave">🤒 ลาป่วย</span>';
                                    case '5': return '<span class="status-badge leave">📝 ลากิจ</span>';
                                    case '6': return '<span class="status-badge activity">🎉 เข้าร่วมกิจกรรม</span>';
                                    default: return '<span class="status-badge unknown">➖</span>';
                                }
                            }
                            statusCell.innerHTML = renderStatusBadge(info && info.attendance_status ? info.attendance_status : null);
                            reasonCell.innerHTML = info && info.reason ? '<span class="text-gray-700 font-medium">' + escapeHtml(info.reason) + '</span>' : '<span class="text-gray-400">➖</span>';
                            var cb = info && info.checked_by ? info.checked_by : null;
                            if (cb === 'system' || cb === 'teacher') {
                                checkedCell.innerHTML = '<span class="checked-by-badge teacher">👨‍🏫 ครูที่ปรึกษา</span>';
                            } else if (cb === 'officer') {
                                checkedCell.innerHTML = '<span class="checked-by-badge officer">👮 เจ้าหน้าที่</span>';
                            } else if (cb === 'rfid' || cb === 'RFID') {
                                var t = info.attendance_time ? (' <span class="text-xs opacity-90">(' + info.attendance_time.substring(0,5) + ')</span>') : '';
                                checkedCell.innerHTML = '<span class="checked-by-badge rfid">💳 สแกนบัตร' + t + '</span>';
                            } else if (cb) {
                                checkedCell.innerHTML = '<span class="checked-by-badge manual">' + escapeHtml(cb) + '</span>';
                            } else {
                                checkedCell.innerHTML = '<span class="text-gray-400">➖</span>';
                            }
                            var editForm = tr.querySelector('.edit-attendance-form');
                            if (editForm) editForm.style.display = 'none';
                        });
                    }
                    if (window.Swal) {
                        Swal.fire({icon:'success', title:'บันทึกสำเร็จ', text:(json.saved?json.saved+' รายการ':'สำเร็จ'), toast:true, position:'top-end', timer:1500, showConfirmButton:false});
                        // refresh so edit buttons are in the expected state
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        alert('บันทึกสำเร็จ');
                        setTimeout(function(){ location.reload(); }, 500);
                    }
                } else {
                    alert('บันทึกล้มเหลว: ' + (json.error || 'Unknown error'));
                    saveBtn.disabled = false; saveBtn.dataset.busy = '0'; saveBtn.innerHTML = origText;
                }
            }).catch(function(err){
                console.error(err);
                alert('เกิดข้อผิดพลาดในการส่งข้อมูล โปรดลองใหม่');
                saveBtn.disabled = false; saveBtn.dataset.busy = '0'; saveBtn.innerHTML = origText;
            });
        });
    }
});
</script>

</body>
</html>
