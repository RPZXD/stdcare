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
// เพิ่ม Behavior class
include_once("../class/Behavior.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงคะแนนพฤติกรรมรวม
// ดึงเทอมและปีการศึกษา
$term = null;
$pee = null;
if (method_exists($user, 'getTerm')) {
    $term = $user->getTerm();
}
if (method_exists($user, 'getPee')) {
    $pee = $user->getPee();
}
$behavior_score = 100;
if ($term && $pee) {
    $behavior = new Behavior($studentConn);
    $behaviors = $behavior->getBehaviorsByStudentId($student_id, $term, $pee);
    if ($behaviors && is_array($behaviors)) {
        $sum = 0;
        foreach ($behaviors as $b) {
            $sum += (int)$b['behavior_score'];
        }
        $behavior_score -= $sum;
    }
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
                    <?php
                    // กำหนด class สีตัวเลข
                    if ($behavior_score < 50) {
                        $score_class = "text-red-600";
                    } elseif ($behavior_score >= 50 && $behavior_score <= 70) {
                        $score_class = "text-yellow-700";
                    } elseif ($behavior_score >= 71 && $behavior_score <= 99) {
                        $score_class = "text-blue-700";
                    } else {
                        $score_class = "text-green-700";
                    }
                    ?>
                    <span class="text-5xl font-bold <?php echo $score_class; ?>"><?php echo $behavior_score; ?></span>
                    <span class="text-gray-600 mt-2">คะแนนสะสม</span>
                    <?php
                    // แสดงกลุ่มและสี
                    if ($behavior_score < 50) {
                        echo '<div class="mt-4 px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold text-center">
                            กลุ่มที่ 1: คะแนนต่ำกว่า 50<br>เข้าค่ายปรับพฤติกรรม (โดยกลุ่มบริหารงานกิจการนักเรียน)
                        </div>';
                    } elseif ($behavior_score >= 50 && $behavior_score <= 70) {
                        echo '<div class="mt-4 px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 font-semibold text-center">
                            กลุ่มที่ 2: คะแนนระหว่าง 50 - 70<br>บำเพ็ญประโยชน์ 20 ชั่วโมง (โดยหัวหน้าระดับ)
                        </div>';
                    } elseif ($behavior_score >= 71 && $behavior_score <= 99) {
                        echo '<div class="mt-4 px-4 py-2 rounded-lg bg-blue-100 text-blue-800 font-semibold text-center">
                            กลุ่มที่ 3: คะแนนระหว่าง 71 - 99<br>บำเพ็ญประโยชน์ 10 ชั่วโมง (โดยครูที่ปรึกษา)
                        </div>';
                    }
                    ?>
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
