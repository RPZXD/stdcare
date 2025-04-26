<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";
require_once "../class/Wroom.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacher = new Teacher($db);

$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// ดึงรายชื่อครูที่ปรึกษา
$advisors = $teacher->getTeachersByClassAndRoom($class, $room);

// ดึงข้อมูลคณะกรรมการห้องเรียนสีขาวและ maxim ผ่านคลาส Wroom
$wroomObj = new Wroom($db);
$wroom = $wroomObj->getWroomStudents($class, $room, $pee);
$maxim = $wroomObj->getMaxim($class, $room, $pee);

// ตำแหน่งและอิโมจิ
$positions = [
    "advisors" => ["emoji" => "👨‍🏫", "label" => "ครูที่ปรึกษา", "limit" => null],
    "1" => ["emoji" => "👤", "label" => "หัวหน้าห้อง", "limit" => 1],
    "2" => ["emoji" => "📘", "label" => "รองหัวหน้าฝ่ายการเรียน", "limit" => 1],
    "3" => ["emoji" => "🛠️", "label" => "รองหัวหน้าฝ่ายการงาน", "limit" => 1],
    "4" => ["emoji" => "🎉", "label" => "รองหัวหน้าฝ่ายกิจกรรม", "limit" => 1],
    "5" => ["emoji" => "🚨", "label" => "รองหัวหน้าฝ่ายสารวัตรนักเรียน", "limit" => 1],
    "10" => ["emoji" => "📝", "label" => "เลขานุการ", "limit" => 1],
    "11" => ["emoji" => "🗂️", "label" => "ผู้ช่วยเลขานุการ", "limit" => 1],
    "6" => ["emoji" => "📚", "label" => "นักเรียนแกนนำฝ่ายการเรียน", "limit" => 4],
    "7" => ["emoji" => "🔧", "label" => "นักเรียนแกนนำฝ่ายการงาน", "limit" => 4],
    "8" => ["emoji" => "🎭", "label" => "นักเรียนแกนนำฝ่ายกิจกรรม", "limit" => 4],
    "9" => ["emoji" => "🛡️", "label" => "นักเรียนแกนนำฝ่ายสารวัตรนักเรียน", "limit" => 4],
];

// จัดกลุ่มนักเรียนตามตำแหน่ง
$grouped = [];
foreach ($wroom as $stu) {
    $pos = $stu['wposit'];
    if (!isset($grouped[$pos])) $grouped[$pos] = [];
    $grouped[$pos][] = $stu;
}

require_once('header.php');
require_once('wrapper.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <!-- ...existing code for header/wrapper... -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"></h1>
                </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid flex flex-col items-center">
                <div class="card col-md-10 mx-auto mt-6 shadow-lg rounded-xl" id="printCard">
                    <div class="card-body p-8">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-20 h-20 mb-4 d-block">
                        <div class="text-base font-bold text-center mb-2">
                            🏠 รายชื่อคณะกรรมการห้องเรียนสีขาว ปีการศึกษา <?= htmlspecialchars($pee) ?>
                        </div>
                        <div class="text-center text-base mb-2">
                            โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์<br>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= htmlspecialchars($class)."/".htmlspecialchars($room) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?>
                        </div>
                        <div class="mb-4">
                            <div class="font-semibold text-base mb-1"><?= $positions['advisors']['emoji'] ?> <?= $positions['advisors']['label'] ?>:</div>
                            <ul class="list-disc list-inside ml-6">
                                <?php foreach($advisors as $row): ?>
                                    <li><?= htmlspecialchars($row['Teach_name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php
                        foreach ($positions as $key => $pos) {
                            if ($key === "advisors") continue;
                            $members = $grouped[$key] ?? [];
                            ?>
                            <div class="mb-2">
                                <div class="font-semibold text-base mb-1"><?= $pos['emoji'] ?> <?= $pos['label'] ?>
                                    <?php if ($pos['limit']): ?>
                                        <span class="text-gray-500">(<?= count($members) ?>/<?= $pos['limit'] ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (count($members) > 0): ?>
                                    <ul class="list-decimal list-inside ml-6">
                                        <?php foreach($members as $stu): ?>
                                            <li><?= htmlspecialchars($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-gray-400 italic ml-6">- ไม่มี -</div>
                                <?php endif; ?>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="mt-6 p-4 bg-gray-100 border border-gray-300 rounded-xl">
                            <div class="font-semibold text-base mb-2">✍️ คติพจน์ห้องเรียนสีขาว</div>
                            <div class="text-gray-800"><?= nl2br(htmlspecialchars($maxim)) ?: '<span class="text-gray-400 italic">- ยังไม่ได้กรอก -</span>' ?></div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4 justify-center mt-6 no-print mb-4">
                    <button onclick="printPage()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center">
                        <i class="fa fa-print mr-2"></i> พิมพ์รายชื่อ
                    </button>
                    <button onclick="location.href='report_wroom2.php'" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center">
                        <i class="fa fa-clipboard mr-2"></i> ดูผังโครงสร้างองค์กรห้องเรียนสีขาว
                    </button>
                    <button onclick="location.href='wroom.php'" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center">
                        <i class="fa fa-home mr-2"></i> กลับหน้าหลัก
                    </button>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script>
$(document).ready(function() {

    // Function to handle printing
    window.printPage = function () {
        const printContents = document.getElementById('printCard').cloneNode(true);
        // สร้างหน้าต่างใหม่สำหรับพิมพ์
        const printWindow = window.open('', '', 'width=900,height=700');
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>พิมพ์รายงาน</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body {
                            font-family: "TH Sarabun New", sans-serif;
                            margin: 20px;
                            background: none;
                            color: black;
                        }
                        @media print {
                            @page { size: A4 portrait; margin: 0.5in; }
                            .no-print, button { display: none !important; }
                        }
                        /* ป้องกัน Tailwind บาง utility หายไป */
                        .grid { display: grid; }
                        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
                        .md\\:grid-cols-2 { }
                        @media (min-width: 768px) {
                            .md\\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                        }
                    </style>
                </head>
                <body class="p-4 bg-white">
                    ${printContents.outerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();

        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    };

    // Function to set up the print layout
    function setupPrintLayout() {
        var style = '@page { size: A4 portrait; margin: 0.5in; }';
        var printStyle = document.createElement('style');
        printStyle.appendChild(document.createTextNode(style));
        document.head.appendChild(printStyle);
    }
    
});
</script>
</body>
</html>
