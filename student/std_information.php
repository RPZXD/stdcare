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
                        <h5 class="m-0">ข้อมูลนักเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid flex justify-center">
                <div class="max-w-xl w-full bg-white rounded-xl shadow-lg p-8 mt-8">
                    <div class="flex flex-col items-center">
                        <img src="<?php echo htmlspecialchars($setting->getImgProfileStudent().$student['Stu_picture']); ?>"
                            alt="User Avatar"
                            class="rounded-full w-60 h-60 object-cover shadow-lg mb-4 ring-4 ring-blue-300 transition-all duration-500 hover:scale-110 hover:shadow-2xl hover:rotate-3">
                        
                        <h2 class="text-2xl font-bold text-blue-700 mb-2"><?php echo htmlspecialchars($student['Stu_pre'].$student['Stu_name']." ".$student['Stu_sur']); ?></h2>
                        <p class="text-gray-500 mb-4">รหัสนักเรียน: <span class="font-semibold"><?php echo htmlspecialchars($student['Stu_id']); ?></span></p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-6">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🎂</span>
                            <span class="text-gray-700">วันเกิด:</span>
                            <span class="font-medium"><?php echo thai_date($student['Stu_birth']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧‍👦</span>
                            <span class="text-gray-700">ชั้น:</span>
                            <span class="font-medium"><?php echo htmlspecialchars('ม.'.$student['Stu_major'].'/'.$student['Stu_room']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🏠</span>
                            <span class="text-gray-700">ที่อยู่:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_addr']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📞</span>
                            <span class="text-gray-700">เบอร์โทร:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_phone']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧</span>
                            <span class="text-gray-700">ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_name']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📱</span>
                            <span class="text-gray-700">เบอร์ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_phone']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
