<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

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


require_once('header.php');

// 11 ด้านและ key
$screenFields = [
    ['label' => '1. ความสามารถพิเศษ', 'key' => 'special_ability', 'choices' => ['ไม่มี', 'มี']],
    ['label' => '2. ด้านการเรียน', 'key' => 'study_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '3. ด้านสุขภาพ', 'key' => 'health_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '4. ด้านเศรษฐกิจ', 'key' => 'economic_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'key' => 'welfare_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'key' => 'drug_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'key' => 'violence_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '8. ด้านพฤติกรรมทางเพศ', 'key' => 'sex_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '9. ด้านการติดเกม', 'key' => 'game_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '10. นักเรียนที่มีความต้องการพิเศษ', 'key' => 'special_need_status', 'choices' => ['ไม่มี', 'มี']],
    ['label' => '11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์', 'key' => 'it_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
];

// ดึงข้อมูลนักเรียนทั้งหมดในห้องนี้
$students = [];
$stmt = $db->prepare("SELECT Stu_id, Stu_sex FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1");
$stmt->bindParam(':class', $class);
$stmt->bindParam(':room', $room);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $students[$row['Stu_id']] = $row['Stu_sex'];
}
$total_students = count($students);

// ดึงข้อมูลคัดกรองล่าสุดของแต่ละคน
$screenData = [];
if ($total_students > 0) {
    $ids = array_keys($students);
    $in = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "SELECT * FROM student_screening WHERE student_id IN ($in) AND pee = ? AND created_at IN (
        SELECT MAX(created_at) FROM student_screening WHERE student_id IN ($in) AND pee = ? GROUP BY student_id
    )";
    $params = array_merge($ids, [$pee], $ids, [$pee]);
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $screenData[$row['student_id']] = $row;
    }
}

// เตรียมสรุปผล
$summary = [];
foreach ($screenFields as $field) {
    $summary[$field['key']] = [];
    foreach ($field['choices'] as $choice) {
        $summary[$field['key']][$choice] = ['male' => 0, 'female' => 0, 'total' => 0];
    }
}

// นับจำนวนแต่ละช้อย
foreach ($students as $stu_id => $sex) {
    $data = $screenData[$stu_id] ?? [];
    foreach ($screenFields as $field) {
        $val = $data[$field['key']] ?? null;
        foreach ($field['choices'] as $choice) {
            if ($val === $choice) {
                $gender = ($sex == 'ช' || $sex == 'ชาย' || $sex == '1') ? 'male' : 'female';
                $summary[$field['key']][$choice][$gender]++;
                $summary[$field['key']][$choice]['total']++;
            }
        }
    }
}

// คำนวณร้อยละ
foreach ($screenFields as $field) {
    foreach ($field['choices'] as $choice) {
        $summary[$field['key']][$choice]['percent'] = $total_students > 0
            ? round(($summary[$field['key']][$choice]['total'] / $total_students) * 100, 2)
            : 0;
    }
}

require_once('wrapper.php');
?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
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
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3 d-block">
                        <h5 class="text-lg font-bold text-center mb-4">
                            🏠 สรุปสถิติคัดกรองนักเรียน 11 ด้าน ปีการศึกษา <?= $pee ?> <br>
                            โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์<br>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?> ปีการศึกษา <?= $pee ?><br>
                            ครูที่ปรึกษา <?php
                         
                         $teachers = $teacher->getTeachersByClassAndRoom($class, $room);

                                foreach ($teachers as $row) {
                                    echo $row['Teach_name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                  
                         ?>
                        </h5>
                        <div class="text-left mt-4">
                            <button type="button" id="backButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 mb-3" onclick="window.location.href='screen11.php'">
                                🔙 กลับหน้าหลัก การคัดกรองนักเรียนรายบุคคล
                            </button>
                            <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3" id="printButton" onclick="printPage()">
                                🖨️ พิมพ์รายงาน 🖨️
                            </button>
                        </div>
                        <div class="table-responsive">
                        <table id="example2" class="display responsive nowrap table-bordered" style="width:100%">
                            <thead class="text-center">
                                <tr class="text-center">
                                    <th class="table-blue-500" rowspan="2" style="width:35%;">การคัดกรอง</th>
                                    <th class="table-success" colspan="4">ปกติ</th>
                                    <th class="table-warning" colspan="4">เสี่ยง</th>
                                    <th class="table-danger" colspan="4">มีปัญหา</th>
                                    <th class="table-success" colspan="4">มี</th>
                                    <th class="table-primary" colspan="4">ไม่มี</th>
                                </tr>
                                <tr>
                                    <th class="table-success">ชาย</th>
                                    <th class="table-success">หญิง</th>
                                    <th class="table-success">รวม</th>
                                    <th class="table-success">ร้อยละ</th>
                                    <th class="table-warning">ชาย</th>
                                    <th class="table-warning">หญิง</th>
                                    <th class="table-warning">รวม</th>
                                    <th class="table-warning">ร้อยละ</th>
                                    <th class="table-danger">ชาย</th>
                                    <th class="table-danger">หญิง</th>
                                    <th class="table-danger">รวม</th>
                                    <th class="table-danger">ร้อยละ</th>
                                    <th class="table-success">ชาย</th>
                                    <th class="table-success">หญิง</th>
                                    <th class="table-success">รวม</th>
                                    <th class="table-success">ร้อยละ</th>
                                    <th class="table-primary">ชาย</th>
                                    <th class="table-primary">หญิง</th>
                                    <th class="table-primary">รวม</th>
                                    <th class="table-primary">ร้อยละ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Map ช้อยแต่ละกลุ่มให้ตรงกับแต่ละ field
                                $choiceMap = [
                                    'ปกติ' => ['ปกติ'],
                                    'เสี่ยง' => ['เสี่ยง'],
                                    'มีปัญหา' => ['มีปัญหา'],
                                    'มี' => ['มี'],
                                    'ไม่มี' => ['ไม่มี'],
                                ];
                                foreach ($screenFields as $field):
                                    // เตรียมข้อมูลแต่ละกลุ่ม
                                    $row = [];
                                    foreach (['ปกติ','เสี่ยง','มีปัญหา','มี','ไม่มี'] as $group) {
                                        $choice = $choiceMap[$group][0];
                                        if (in_array($choice, $field['choices'])) {
                                            $row[$group] = $summary[$field['key']][$choice];
                                        } else {
                                            $row[$group] = ['male'=>'','female'=>'','total'=>'','percent'=>''];
                                        }
                                    }
                                ?>
                                <tr>
                                    <td class="text-left"><?= $field['label'] ?></td>
                                    <td><?= $row['ปกติ']['male'] ?></td>
                                    <td><?= $row['ปกติ']['female'] ?></td>
                                    <td><?= $row['ปกติ']['total'] ?></td>
                                    <td><?= $row['ปกติ']['percent'] ?></td>
                                    <td><?= $row['เสี่ยง']['male'] ?></td>
                                    <td><?= $row['เสี่ยง']['female'] ?></td>
                                    <td><?= $row['เสี่ยง']['total'] ?></td>
                                    <td><?= $row['เสี่ยง']['percent'] ?></td>
                                    <td><?= $row['มีปัญหา']['male'] ?></td>
                                    <td><?= $row['มีปัญหา']['female'] ?></td>
                                    <td><?= $row['มีปัญหา']['total'] ?></td>
                                    <td><?= $row['มีปัญหา']['percent'] ?></td>
                                    <td><?= $row['มี']['male'] ?></td>
                                    <td><?= $row['มี']['female'] ?></td>
                                    <td><?= $row['มี']['total'] ?></td>
                                    <td><?= $row['มี']['percent'] ?></td>
                                    <td><?= $row['ไม่มี']['male'] ?></td>
                                    <td><?= $row['ไม่มี']['female'] ?></td>
                                    <td><?= $row['ไม่มี']['total'] ?></td>
                                    <td><?= $row['ไม่มี']['percent'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    window.printPage = function() {
        let elementsToHide = $('#backButton, #printButton, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');
        elementsToHide.hide();
        $('thead').css('display', 'table-header-group');
        setTimeout(() => {
            window.print();
            elementsToHide.show();
        }, 100);
    };
});
</script>
</body>
</html>
