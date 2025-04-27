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
                        <div class="text-base font-bold text-center">
                            🏠 ผังโครงสร้างองค์กรห้องเรียนสีขาว ปีการศึกษา <?= htmlspecialchars($pee) ?>
                        </div>
                        <div class="text-center text-base">
                            ระดับชั้นมัธยมศึกษาปีที่ <?= htmlspecialchars($class)."/".htmlspecialchars($room) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?>
                        </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <!-- Advisors -->
                                        <thead class="bg-indigo-500 text-white">
                                            <tr>
                                                <th colspan="4" class="text-center">ครูที่ปรึกษา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <?php if(count($advisors) == 1): ?>
                                                    <td class="text-center">
                                                        <a href="<?= !empty($advisors[0]['Teach_photo']) ? $setting->getImgProfile().$advisors[0]['Teach_photo'] : '' ?>" target="_blank">
                                                            <img class="img-fluid img-rounded zoomable-img" src="<?= !empty($advisors[0]['Teach_photo']) ? $setting->getImgProfile().$advisors[0]['Teach_photo'] : '' ?>" alt="<?= htmlspecialchars($advisors[0]['Teach_photo']) ?>" style="height:70px;width:auto;">
                                                        </a>
                                                        <p class="text-center"><?= htmlspecialchars($advisors[0]['Teach_name']) ?></p>
                                                    </td>
                                                <?php else: ?>
                                                    <?php foreach($advisors as $row): ?>
                                                        <td colspan="<?= ceil(4/count($advisors)) ?>" class="text-center">
                                                            <a href="<?= !empty($row['Teach_photo']) ? $setting->getImgProfile().$row['Teach_photo'] : '' ?>" target="_blank">
                                                                <img class="img-fluid img-rounded zoomable-img" src="<?= !empty($row['Teach_photo']) ? $setting->getImgProfile().$row['Teach_photo'] : '' ?>" alt="<?= htmlspecialchars($row['Teach_photo']) ?>" style="height:70px;width:auto;">
                                                            </a>
                                                            <p class="text-center"><?= htmlspecialchars($row['Teach_name']) ?></p>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                        <!-- Head -->
                                        <thead class="bg-indigo-500 text-white">
                                            <tr>
                                                <th colspan="4" class="text-center"><?= $positions['1']['label'] ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <?php foreach($grouped['1'] ?? [] as $stu): ?>
                                                        <a href="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '' ?>" target="_blank">
                                                            <img class="img-fluid img-rounded zoomable-img" src="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '' ?>" style="height:70px;width:auto;">
                                                        </a>
                                                        <p class="text-center"><?= htmlspecialchars($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) ?></p>
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!-- Deputy Heads -->
                                        <thead class="bg-indigo-500 text-white">
                                            <tr>
                                                <th class="text-center"><?= $positions['5']['label'] ?></th>
                                                <th class="text-center"><?= $positions['2']['label'] ?></th>
                                                <th class="text-center"><?= $positions['3']['label'] ?></th>
                                                <th class="text-center"><?= $positions['4']['label'] ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <?php foreach(['5','2','3','4'] as $key): ?>
                                                    <td class="text-center">
                                                        <?php foreach($grouped[$key] ?? [] as $stu): ?>
                                                            <a href="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '../dist/img/student.png' ?>" target="_blank">
                                                                <img class="img-fluid img-rounded zoomable-img" src="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '../dist/img/student.png' ?>" style="height:70px;width:auto;">
                                                            </a>
                                                            <p class="text-center"><?= htmlspecialchars($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) ?></p>
                                                        <?php endforeach; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        </tbody>
                                        <!-- Leaders -->
                                        <thead class="bg-indigo-500 text-white">
                                            <tr>
                                                <th class="text-center"><?= $positions['6']['label'] ?></th>
                                                <th class="text-center"><?= $positions['7']['label'] ?></th>
                                                <th class="text-center"><?= $positions['8']['label'] ?></th>
                                                <th class="text-center"><?= $positions['9']['label'] ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // หาจำนวนแถวสูงสุดของกลุ่มแกนนำ
                                            $maxRows = max(
                                                count($grouped['6'] ?? []),
                                                count($grouped['7'] ?? []),
                                                count($grouped['8'] ?? []),
                                                count($grouped['9'] ?? [])
                                            );
                                            for($i=0; $i<$maxRows; $i++): ?>
                                            <tr>
                                                <?php foreach(['6','7','8','9'] as $key): ?>
                                                    <td class="text-center">
                                                        <?php if(!empty($grouped[$key][$i])): $stu = $grouped[$key][$i]; ?>
                                                            <a href="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '../dist/img/student.png' ?>" target="_blank">
                                                                <img class="img-fluid img-rounded zoomable-img" src="<?= !empty($stu['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.htmlspecialchars($stu['Stu_picture']) : '../dist/img/student.png' ?>" style="height:70px;width:auto;">
                                                            </a>
                                                            <p class="text-center"><?= htmlspecialchars($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) ?></p>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                        <!-- Maxim -->
                                        <tbody>
                                            <tr class="bg-indigo-500">
                                                <td colspan="4" class="bg-indigo-500 text-center">
                                                    <p class="text-white">
                                                        คติพจน์ห้องเรียนสีขาว : <?= nl2br(htmlspecialchars($maxim)) ?: '<span class="text-gray-400 italic">- ยังไม่ได้กรอก -</span>' ?>
                                                    </p>
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
<!-- ปรับรูปให้อยู่กึ่งกลางทุกตำแหน่ง -->
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
