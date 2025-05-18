<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

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


require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">รายงานสรุป</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-4">
                <?php
                // รับค่า tab จาก query string
                $tab = $_GET['tab'] ?? 'late';
                // สร้าง array สำหรับ mapping tab => ไฟล์
                $tabFiles = [
                    'late' => 'report_late.php',
                    'homevisit' => 'report_homevisit.php',
                    'deduct-room' => 'report_deduct_room.php',
                    'deduct-group' => 'report_deduct_group.php',
                    'parent-leader' => 'report_parent_leader.php',
                    'sdq_room' => 'report_sdq_room.php',
                    'sdq_class' => 'report_sdq_class.php',
                    'sdq_school' => 'report_sdq_school.php',
                    'eq' => 'report_eq.php',
                    'screen11' => 'report_screen11.php',
                    'whiteclass' => 'report_whiteclass.php'
                ];
                // กำหนดกลุ่ม tab หลัก/เพิ่มเติม
                $mainTabs = [
                    ['late', '⏰ ข้อมูลมาสาย', 'blue'],
                    ['homevisit', '🏠 การเยี่ยมบ้าน', 'green'],
                    ['deduct-room', '🏫 การหักคะแนน(รายห้อง)', 'yellow'],
                    ['deduct-group', '📊 การหักคะแนน(แบ่งตามกลุ่ม)', 'pink'],
                    
                ];
                $moreTabs = [
                    ['parent-leader', '👨‍👩‍👧‍👦 รายชื่อประธานเครือข่ายผู้ปกครอง', 'purple'],
                    ['sdq_room', '🧠 ข้อมูล SDQ (รายห้อง)', 'red'],
                    ['sdq_class', '🧠 ข้อมูล SDQ (รายชั้น)', 'indigo'],
                    ['sdq_school', '🧠 ข้อมูล SDQ (โรงเรียน)', 'cyan'],
                    ['eq', '💡 ข้อมูล EQ', 'orange'],
                    ['screen11', '🔬 คัดกรอง 11 ด้าน', 'teal'],
                    ['whiteclass', '⚪ ห้องเรียนสีขาว', 'gray'],
                ];
                ?>
                <!-- Tabs -->
                <div class="flex flex-wrap border-b mb-6 gap-2 animate-fade-in-down">
                    <?php foreach ($mainTabs as [$key, $label, $color]): ?>
                        <a href="?tab=<?= $key ?>"
                           class="px-4 py-2 -mb-px font-semibold border-b-2 transition-all duration-200 rounded-t-lg
                           <?= $tab === $key ? "border-{$color}-500 text-{$color}-700 bg-{$color}-50 shadow animate-bounce-in" : "border-transparent text-gray-500 hover:text-{$color}-600 hover:bg-{$color}-50 hover:shadow" ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                    <!-- Dropdown สำหรับ tab เพิ่มเติม -->
                    <div class="relative group" tabindex="0">
                        <button class="px-4 py-2 -mb-px font-semibold border-b-2 border-transparent text-gray-500 hover:text-indigo-600 flex items-center gap-1 focus:outline-none transition-all duration-200 rounded-t-lg hover:bg-indigo-50 hover:shadow">
                            <span class="animate-pulse">เพิ่มเติม</span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded shadow-lg z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 transition-all duration-150
                            border border-indigo-100 animate-fade-in"
                             onmouseover="this.classList.add('visible','opacity-100')" onmouseout="this.classList.remove('visible','opacity-100')">
                            <?php foreach ($moreTabs as [$key, $label, $color]): ?>
                                <a href="?tab=<?= $key ?>"
                                   class="block px-4 py-2 text-sm font-semibold transition-all duration-150 rounded hover:scale-105
                                   <?= $tab === $key ? "text-{$color}-700 bg-{$color}-50 font-bold animate-bounce-in" : "text-gray-600 hover:text-{$color}-600 hover:bg-{$color}-50" ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 mt-4 animate-fade-in">
                    <?php
                    // include ไฟล์ตาม tab ที่เลือก
                    if (isset($tabFiles[$tab]) && file_exists($tabFiles[$tab])) {
                        include($tabFiles[$tab]);
                    } else {
                        echo '<div class="text-gray-600">ไม่พบรายงานที่เลือก</div>';
                    }
                    ?>
                </div>
                <style>
                @layer utilities {
                    .animate-fade-in { animation: fadeIn 0.7s; }
                    .animate-fade-in-down { animation: fadeInDown 0.7s; }
                    .animate-bounce-in { animation: bounceIn 0.7s; }
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
                /* Responsive dropdown for mobile */
                @media (max-width: 640px) {
                    .group:hover .group-hover\:block,
                    .group:focus-within .group-focus\:block {
                        display: block;
                    }
                }
                /* Fix dropdown hover/focus for desktop */
                .group:focus-within > div,
                .group:hover > div {
                    visibility: visible !important;
                    opacity: 1 !important;
                }
                .group > div {
                    visibility: hidden;
                    opacity: 0;
                    transition: opacity 0.15s;
                }
                </style>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
