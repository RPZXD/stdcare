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
include_once("../class/Behavior.php");

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

// ดึงคะแนนพฤติกรรมรวม
$behavior = new Behavior($studentConn);
$behaviors = $behavior->getBehaviorsByStudentId($student_id, $term, $pee);

// คำนวณคะแนนรวม (ถ้าไม่มีข้อมูลถือว่า 100)
$total_score = 100;
if ($behaviors && is_array($behaviors)) {
    $sum = 0;
    foreach ($behaviors as $b) {
        $sum += (int)$b['behavior_score'];
    }
    $total_score -= $sum; // สมมติว่า behavior_score เป็นค่าติดลบเมื่อโดนหัก
}

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
                        <h5 class="m-0">คะแนนพฤติกรรม</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- Card Score -->
                <div class="mb-6">
                    <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-700">คะแนนพฤติกรรมคงเหลือ</h4>
                        <span class="text-3xl font-bold text-blue-600"><?php echo $total_score; ?></span>
                        <span class="ml-2 text-gray-500">คะแนน</span>
                    </div>
                </div>
                <!-- ตารางรายละเอียดการหักคะแนน -->
                <div class="bg-white rounded-lg shadow mt-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                        <span class="text-xl">📋</span>
                        <h6 class="text-base font-semibold text-gray-700">รายละเอียดการหักคะแนน</h6>
                    </div>
                    <div class="p-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">📅 วันที่</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">📄 เรื่องที่ถูกหัก</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">📝 รายละเอียด</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-red-700 uppercase tracking-wider">🔻 คะแนน</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">👨‍🏫 ครูผู้หัก</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                <?php if ($behaviors && is_array($behaviors) && count($behaviors) > 0): ?>
                                    <?php foreach ($behaviors as $b): ?>
                                        <tr class="hover:bg-blue-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                <?php echo thai_date($b['behavior_date']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($b['behavior_type']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($b['behavior_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold <?php echo ((int)$b['behavior_score'] < 0) ? 'text-red-600' : 'text-green-600'; ?>">
                                                <?php echo htmlspecialchars($b['behavior_score']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($b['teacher_behavior']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">😃 ไม่มีข้อมูลการหักคะแนน</td>
                                    </tr>
                                <?php endif; ?>
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


<script>

</script>
</body>
</html>
