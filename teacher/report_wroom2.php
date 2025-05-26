<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";
require_once "../class/Wroom.php";
// ...existing code for DB and session...

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

$advisors = $teacher->getTeachersByClassAndRoom($class, $room);

$wroomObj = new Wroom($db);
$wroom = $wroomObj->getWroomStudents($class, $room, $pee);
$maxim = $wroomObj->getMaxim($class, $room, $pee);

$positions = [
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
                <div class="card col-md-11 mx-auto mt-6 shadow-lg rounded-xl" >
                    <div class="card-body p-8" id="printCard">
                        <h2 class="text-2xl font-bold text-indigo-700 mb-6 text-center tracking-wide drop-shadow">👥 โครงสร้างห้องเรียนสีขาว</h2>
                        <div class="mb-6 text-center">
                            <span class="inline-block px-4 py-2 bg-indigo-50 rounded-full text-indigo-700 font-semibold shadow-sm">
                                ห้อง ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?>
                            </span>
                        </div>
                        <div class="mb-4 text-center">
                            <span class="font-semibold text-gray-700">ครูที่ปรึกษา:</span>
                            <span class="text-indigo-700 font-medium">
                                <?php if ($advisors && count($advisors)): ?>
                                    <?php foreach ($advisors as $a): ?>
                                        <div class="inline-block mx-2 align-top text-center">
                                            <?php if (!empty($a['Teach_photo'])): ?>
                                                <a href="<?= 'https://std.phichai.ac.th/teacher/uploads/phototeach/' . htmlspecialchars($a['Teach_photo']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                    <img src="<?= 'https://std.phichai.ac.th/teacher/uploads/phototeach/' . htmlspecialchars($a['Teach_photo']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                </a><br>
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($a['Teach_name']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="overflow-x-auto flex justify-center">
                            <table class="min-w-full mx-auto border border-indigo-200 bg-white rounded-xl shadow-lg">
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="bg-indigo-100 text-center font-bold py-3 border-b border-indigo-200 text-lg tracking-wide shadow-inner">
                                            👤 หัวหน้าห้อง
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <?php if (!empty($grouped['1'])): ?>
                                                <?php foreach ($grouped['1'] as $s): ?>
                                                    <?php if (!empty($s['Stu_picture'])): ?>
                                                        <a href="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                            <img src="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                        </a><br>
                                                    <?php endif; ?>
                                                    <span class="font-semibold text-gray-800"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">- ไม่มี -</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🚨 รองฯฝ่ายสารวัตร</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📘 รองฯฝ่ายการเรียน</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🛠️ รองฯฝ่ายการงาน</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🎉 รองฯฝ่ายกิจกรรม</td>
                                    </tr>
                                    <tr>
                                        <?php foreach(['5','2','3','4'] as $key): ?>
                                            <td class="text-center py-2">
                                                <?php if (!empty($grouped[$key])): ?>
                                                    <?php foreach ($grouped[$key] as $s): ?>
                                                        <?php if (!empty($s['Stu_picture'])): ?>
                                                            <a href="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                                <img src="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                            </a><br>
                                                        <?php endif; ?>
                                                        <span class="font-xs"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🛡️ กรรมการฝ่ายสารวัตร</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📚 กรรมการฝ่ายการเรียน</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🔧 กรรมการฝ่ายการงาน</td>
                                        <td class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🎭 กรรมการฝ่ายกิจกรรม</td>
                                    </tr>
                                    <tr>
                                        <?php foreach(['9','6','7','8'] as $key): ?>
                                            <td class="text-center">
                                                <?php if (!empty($grouped[$key])): ?>
                                                    <?php foreach ($grouped[$key] as $s): ?>
                                                        <div class="flex flex-col items-center">
                                                            <?php if (!empty($s['Stu_picture'])): ?>
                                                                <a href="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                                    <img src="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                                </a>
                                                            <?php endif; ?>
                                                            <span class="font-xs block"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">📝 เลขานุการ</td>
                                        <td colspan="2" class="bg-indigo-50 text-center font-semibold py-2 border-b border-indigo-100">🗂️ ผู้ช่วยเลขานุการ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center py-2">
                                            <?php if (!empty($grouped['10'])): ?>
                                                <?php foreach ($grouped['10'] as $s): ?>
                                                    <?php if (!empty($s['Stu_picture'])): ?>
                                                        <a href="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                            <img src="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                        </a><br>
                                                    <?php endif; ?>
                                                    <span class="font-medium"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td colspan="2" class="text-center py-2">
                                            <?php if (!empty($grouped['11'])): ?>
                                                <?php foreach ($grouped['11'] as $s): ?>
                                                    <?php if (!empty($s['Stu_picture'])): ?>
                                                        <a href="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" target="_blank" class="group inline-block transition-transform hover:scale-125 duration-200">
                                                            <img src="<?= 'https://std.phichai.ac.th/photo/' . htmlspecialchars($s['Stu_picture']) ?>" class="inline-block rounded-full shadow-lg ring-2 ring-indigo-200 mx-auto mb-1 transition-all duration-200 group-hover:ring-indigo-400" style="height:54px;width:54px;object-fit:cover;">
                                                        </a><br>
                                                    <?php endif; ?>
                                                    <span class="font-medium"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="bg-indigo-100 text-center font-semibold py-3 border-t border-indigo-200 text-lg">✍️ คติพจน์ห้องเรียนสีขาว</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <?php if ($maxim): ?>
                                                <span class="text-indigo-700 font-bold text-lg"><?= htmlspecialchars($maxim) ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-400 italic">- ยังไม่ได้กรอก -</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4 justify-center mt-6 no-print mb-4">
                    <button onclick="printPage()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center">
                        <i class="fa fa-print mr-2"></i> พิมพ์ผังโครงสร้าง
                    </button>
                    <button onclick="location.href='report_wroom.php'" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center justify-center">
                        <i class="fa fa-list mr-2"></i> ดูรายชื่อคณะกรรมการ
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
    window.printPage = function () {
        const printContents = document.getElementById('printCard').cloneNode(true);
        const printWindow = window.open('', '', 'width=900,height=700');
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>พิมพ์ผังโครงสร้าง</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        @page {
                            size: A4 portrait;
                            margin: 18mm 10mm 18mm 10mm;
                        }
                        html, body {
                            width: 210mm;
                            min-height: 297mm;
                            background: none !important;
                            font-family: "TH Sarabun New", sans-serif;
                            color: black;
                        }
                        body {
                            margin: 0;
                            background: none;
                        }
                        #printCard {
                            width: 100%;
                            max-width: 165mm;
                            margin: 0 auto;
                            padding: 4mm 0 4mm 0;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                            font-size: 0.95rem;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 2px 1px;
                            vertical-align: middle !important;
                            text-align: center;
                        }
                        img.img-fluid.img-rounded {
                            display: block;
                            margin-left: auto !important;
                            margin-right: auto !important;
                            margin-top: 0.2em;
                            margin-bottom: 0.2em;
                            max-width: 48px;
                            height: 30px;
                            object-fit: cover;
                        }
                        @media print {
                            @page {
                                size: A4;
                                margin: 0.1cm;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                            }
                            html, body {
                                width: 210mm;
                                min-height: 297mm;
                            }
                            #printCard {
                                width: 100%;
                                max-width: 165mm;
                                margin: 0 auto;
                                padding: 0;
                            }
                            table {
                                font-size: 0.95rem;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .no-print {
                                display: none !important;
                            }
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
});
</script>
<style>
    .table td.text-center, .table th.text-center {
        vertical-align: middle !important;
    }
    .table img.img-fluid.img-rounded {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .zoomable-img {
        transition: transform 0.2s cubic-bezier(.4,2,.6,1), box-shadow 0.2s;
        cursor: zoom-in;
        z-index: 1;
    }
    .zoomable-img:hover {
        transform: scale(6) translateY(-10px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        z-index: 10;
        position: relative;
    }
</style>
</body>
</html>
