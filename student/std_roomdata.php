<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function thai_date($strDate) {
    $strYear = date("Y", strtotime($strDate)) ;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $thaiMonths = [
        "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
    ];
    $strMonthThai = $thaiMonths[$strMonth];
    return "$strDay $strMonthThai $strYear";
}
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ข้อมูลห้องเรียน (ม.<?= $student['Stu_major'].'/'.$student['Stu_room']?>)</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="callout callout-success text-center">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                        <h5 class="text-center text-lg">รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?= $student['Stu_major'].'/'.$student['Stu_room']; ?></h5>
                        <h5 class="text-center text-lg">ปีการศึกษา <?= date('Y')+543; ?></h5>
                        <!-- Search box -->
                        <div class="flex justify-center my-4">
                            <input type="text" id="studentSearch" class="form-control w-80 border border-gray-300 rounded px-3 py-2" placeholder="ค้นหาชื่อนักเรียน, รหัส, เลขที่ หรือชื่อเล่น...">
                        </div>
                        <div class="row justify-content-center">
                            <div id="showDataStudent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <?php
                                // ดึงรายชื่อนักเรียนในห้องเดียวกัน
                                $query = "SELECT * FROM student WHERE Stu_major = :major AND Stu_room = :room AND Stu_status = 1 ORDER BY Stu_no ASC";
                                $stmt = $studentConn->prepare($query);
                                $stmt->bindParam(":major", $student['Stu_major']);
                                $stmt->bindParam(":room", $student['Stu_room']);
                                $stmt->execute();
                                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($students) === 0) {
                                    echo '<p class="text-center text-xl font-semibold text-gray-600">ไม่พบข้อมูลนักเรียน</p>';
                                } else {
                                    foreach ($students as $item) {
                                        ?>
                                        <div class="card my-4 p-4 max-w-xs bg-white rounded-lg shadow-lg border border-gray-200 transition transform hover:scale-105 student-card"
                                            data-name="<?= htmlspecialchars($item['Stu_pre'].$item['Stu_name'].' '.$item['Stu_sur']) ?>"
                                            data-id="<?= htmlspecialchars($item['Stu_id']) ?>"
                                            data-no="<?= htmlspecialchars($item['Stu_no']) ?>"
                                            data-nick="<?= htmlspecialchars($item['Stu_nick']) ?>">
                                            <img class="card-img-top rounded-lg mb-4" src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($item['Stu_picture']) ?>" alt="Student Picture" style="height: 350px; object-fit: cover;">
                                            <div class="card-body space-y-3">
                                                <h5 class="card-title text-base font-bold text-gray-800"><?= htmlspecialchars($item['Stu_pre'].$item['Stu_name'].' '.$item['Stu_sur']) ?></h5><br>
                                                <p class="card-text text-gray-600 text-left">รหัสนักเรียน: <span class="font-semibold text-blue-600"><?= htmlspecialchars($item['Stu_id']) ?></span></p>
                                                <p class="card-text text-gray-600 text-left">เลขที่: <?= htmlspecialchars($item['Stu_no']) ?></p>
                                                <p class="card-text text-gray-600 text-left">ชื่อเล่น: <span class="italic text-purple-500"><?= htmlspecialchars($item['Stu_nick']) ?></span></p>
                                                <p class="card-text text-gray-600 text-left">
                                                    เบอร์โทร: 
                                                    <a href="tel:<?= htmlspecialchars($item['Stu_phone']) ?>" class="text-blue-500 hover:underline"><?= htmlspecialchars($item['Stu_phone']) ?></a>
                                                </p>
                                                <!-- ไม่แสดงเบอร์ผู้ปกครอง และไม่มีปุ่มดู/แก้ไข -->
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<!-- Search filter script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const cards = document.querySelectorAll('.student-card');
    searchInput.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const id = card.getAttribute('data-id').toLowerCase();
            const no = card.getAttribute('data-no').toLowerCase();
            const nick = card.getAttribute('data-nick').toLowerCase();
            if (
                name.includes(val) ||
                id.includes(val) ||
                no.includes(val) ||
                nick.includes(val)
            ) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>
