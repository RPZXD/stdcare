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
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">ยินดีต้อนรับ, <?php echo htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']); ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
    <div class="container mx-auto py-6 px-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Student Info Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">👨‍🎓 ข้อมูลนักเรียน</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>🆔 <span class="font-semibold">รหัสนักเรียน:</span> <?php echo htmlspecialchars($student['Stu_id']); ?></li>
                    <li>👤 <span class="font-semibold">ชื่อ-สกุล:</span> <?php echo htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']); ?></li>
                    <li>📚 <span class="font-semibold">ระดับชั้นมัธยมศึกษาปีที่:</span> <?php echo htmlspecialchars($student['Stu_major']); ?></li>
                    <li>🏫 <span class="font-semibold">ห้อง:</span> <?php echo htmlspecialchars($student['Stu_room']); ?></li>
                    <li>📞 <span class="font-semibold">เบอร์โทร:</span> <?php echo htmlspecialchars($student['Stu_phone']); ?></li>
                    <!-- ...ข้อมูลอื่นๆ... -->
                </ul>
            </div>
            <!-- Parent Info Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">👨‍👩‍👧‍👦 ข้อมูลผู้ปกครอง</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>👨 <span class="font-semibold">ชื่อผู้ปกครอง:</span> <?php echo htmlspecialchars($student['Par_name'] ?? '-'); ?></li>
                    <li>🏠 <span class="font-semibold">ที่อยู่:</span> <?php echo htmlspecialchars($student['Par_addr'] ?? '-'); ?></li>
                    <li>📞 <span class="font-semibold">เบอร์โทร:</span> <?php echo htmlspecialchars($student['Par_phone'] ?? '-'); ?></li>
                    <!-- ...ข้อมูลอื่นๆ... -->
                </ul>
            </div>
        </div>
        <!-- Score Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Attendance Score Card -->
            <div class="bg-blue-50 rounded-xl shadow p-6 flex flex-col items-center border border-blue-200">
                <h4 class="text-lg font-semibold mb-2 flex items-center gap-2">📅 การมาเรียน</h4>
                <div class="flex flex-wrap gap-3 justify-center">
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">✅</span>
                        <span class="text-sm text-gray-600">มาเรียน</span>
                        <span class="font-bold text-blue-700">0</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">❌</span>
                        <span class="text-sm text-gray-600">ขาดเรียน</span>
                        <span class="font-bold text-red-600">0</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">⏰</span>
                        <span class="text-sm text-gray-600">มาสาย</span>
                        <span class="font-bold text-yellow-600">0</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">🤒</span>
                        <span class="text-sm text-gray-600">ลาป่วย</span>
                        <span class="font-bold text-green-600">0</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">📝</span>
                        <span class="text-sm text-gray-600">ลากิจ</span>
                        <span class="font-bold text-green-600">0</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl">🎉</span>
                        <span class="text-sm text-gray-600">เข้าร่วมกิจกรรม</span>
                        <span class="font-bold text-purple-600">0</span>
                    </div>
                </div>
            </div>
            <!-- Behavior Score Card -->
            <div class="bg-green-50 rounded-xl shadow p-6 flex flex-col items-center border border-green-200 md:col-span-2">
                <h4 class="text-lg font-semibold mb-2 flex items-center gap-2">🌟 คะแนนพฤติกรรม</h4>
                <div class="flex flex-col items-center">
                    <span class="text-5xl font-bold text-green-700">0</span>
                    <span class="text-gray-600 mt-2">คะแนนสะสม</span>
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
