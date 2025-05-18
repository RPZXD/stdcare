<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">Officer Dashboard</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
<?php
// ดึงข้อมูลจำนวนนักเรียน
$studentCount = $db->query("SELECT COUNT(*) as total FROM student WHERE Stu_status=1")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูลจำนวนบุคลากร
$teacherCount = $db->query("SELECT COUNT(*) as total FROM teacher WHERE Teach_status=1")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูลจำนวนพฤติกรรมทั้งหมด
$behaviorCount = $db->query("SELECT COUNT(*) as total FROM behavior WHERE behavior_term = $term AND behavior_pee = $pee")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูล score พฤติกรรมรวม
$behaviorScore = $db->query("SELECT COALESCE(SUM(behavior_score),0) as total FROM behavior WHERE behavior_term = $term AND behavior_pee = $pee")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// ดึงคะแนนรวมของแต่ละ Stu_id ก่อน แล้วค่อยจัดกลุ่ม
$scoreGroups = [
    'เข้าค่ายปรับพฤติกรรม (<50)' => 0,
    'บำเพ็ญประโยชน์ 20 ชม. (50-70)' => 0,
    'บำเพ็ญประโยชน์ 10 ชม. (71-99)' => 0,
    'ไม่มีคะแนน' => 0
];
$totalStudentsForGraph = 0;
$scoreStmt = $db->query("
    SELECT s.Stu_id, COALESCE(SUM(b.behavior_score),0) AS total_score
    FROM student s
    LEFT JOIN behavior b ON s.Stu_id = b.stu_id
    WHERE s.Stu_status=1 AND b.behavior_term = $term AND b.behavior_pee = $pee
    GROUP BY s.Stu_id
");
while ($row = $scoreStmt->fetch(PDO::FETCH_ASSOC)) {
    $score = (int)($row['total_score'] ?? 0);
    if ($score === 0) {
        $scoreGroups['ไม่มีคะแนน']++;
    } elseif ($score < 50) {
        $scoreGroups['เข้าค่ายปรับพฤติกรรม (<50)']++;
    } elseif ($score <= 70) {
        $scoreGroups['บำเพ็ญประโยชน์ 20 ชม. (50-70)']++;
    } elseif ($score <= 99) {
        $scoreGroups['บำเพ็ญประโยชน์ 10 ชม. (71-99)']++;
    }
    $totalStudentsForGraph++;
}
// ปรับยอดรวมในกราฟให้เท่ากับจำนวนนักเรียน (กันกรณีข้อมูลผิดพลาด)
if ($totalStudentsForGraph != $studentCount) {
    // กรณีมีนักเรียนที่ไม่มีในผล query (เช่น ไม่มี behavior เลย)
    $diff = $studentCount - $totalStudentsForGraph;
    if ($diff > 0) {
        $scoreGroups['ไม่มีคะแนน'] += $diff;
    }
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mx-auto py-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-4xl mb-2">👨‍🎓</span>
            <div class="text-2xl font-bold"><?= $studentCount ?></div>
            <div class="text-gray-600">นักเรียน</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-4xl mb-2">👩‍🏫</span>
            <div class="text-2xl font-bold"><?= $teacherCount ?></div>
            <div class="text-gray-600">บุคลากร</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-4xl mb-2">📋</span>
            <div class="text-2xl font-bold"><?= $behaviorCount ?></div>
            <div class="text-gray-600">จำนวนพฤติกรรม</div>
        </div>

        <!-- กราฟ -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-3">
            <h2 class="text-xl font-semibold mb-4 flex items-center">📊 <span class="ml-2">สรุปกลุ่มคะแนนพฤติกรรม</span></h2>
            <canvas id="scoreChart" height="100"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('scoreChart').getContext('2d');
const scoreChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($scoreGroups)) ?>,
        datasets: [{
            label: 'จำนวนนักเรียน',
            data: <?= json_encode(array_values($scoreGroups)) ?>,
            backgroundColor: [
                '#f87171',  // red
                '#fbbf24', // yellow
                '#60a5fa', // blue
                '#34d399' // green
            ],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
