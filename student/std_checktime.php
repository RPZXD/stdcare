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

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

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
                        <h5 class="m-0">เวลาเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto px-4 py-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-center mb-6 flex items-center justify-center gap-2">
                        ⏰ ตารางเวลาเรียนของฉัน
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">วัน</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">เวลาเข้าเรียน</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">เวลาออก</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <!-- ตัวอย่างข้อมูล สามารถแทนที่ด้วยข้อมูลจริงได้ -->
                                <tr>
                                    <td class="px-4 py-2">1</td>
                                    <td class="px-4 py-2">จันทร์</td>
                                    <td class="px-4 py-2">08:00 🟢</td>
                                    <td class="px-4 py-2">15:30 🏁</td>
                                    <td class="px-4 py-2"><span class="text-green-600 font-bold">มาเรียน</span> 😃</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">2</td>
                                    <td class="px-4 py-2">อังคาร</td>
                                    <td class="px-4 py-2">08:05 🟡</td>
                                    <td class="px-4 py-2">15:30 🏁</td>
                                    <td class="px-4 py-2"><span class="text-yellow-600 font-bold">สาย</span> 😅</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">3</td>
                                    <td class="px-4 py-2">พุธ</td>
                                    <td class="px-4 py-2">-</td>
                                    <td class="px-4 py-2">-</td>
                                    <td class="px-4 py-2"><span class="text-red-600 font-bold">ขาด</span> 😢</td>
                                </tr>
                                <!-- ...สามารถเพิ่มแถวเพิ่มเติม... -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>


<script>

</script>
</body>
</html>
