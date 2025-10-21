<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$student_obj = new Student($db); // Renamed

// Fetch terms and pee
$current_term = $user->getTerm();
$current_buddhist_year = $user->getPee(); // *** FIXED: Get B.E. year (พ.ศ.) ***

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
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

// --- Get Filter Data ---
$report_term = $_GET['term'] ?? $current_term;
$report_year = $_GET['year'] ?? $current_buddhist_year; // *** FIXED: Default to B.E. year ***
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// --- Fetch Report Data ---
$students = $student_obj->fetchFilteredStudents2($report_class, $report_room);
$summary_map = [];

if (!empty($students)) {
    // Fetch aggregate attendance data
    // *** FIXED: Query uses B.E. year (พ.ศ.) ***
    $query = "SELECT
                sa.student_id,
                sa.attendance_status,
                COUNT(sa.id) AS status_count
              FROM student_attendance sa
              JOIN student s ON sa.student_id = s.Stu_id
              WHERE s.Stu_major = :class AND s.Stu_room = :room
                AND sa.term = :term AND sa.year = :year
              GROUP BY sa.student_id, sa.attendance_status";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':class' => $report_class,
        ':room' => $report_room,
        ':term' => $report_term,
        ':year' => $report_year // B.E. year
    ]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Re-organize data
    foreach ($records as $record) {
        $summary_map[$record['student_id']][$record['attendance_status']] = $record['status_count'];
    }
}

// Helper for status labels
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
    '4.1' => ['label' => 'ลาป่วย', 'emoji' => '🤒'], // สมมติรหัสลาป่วย
    '4.2' => ['label' => 'ลากิจ', 'emoji' => '📝'], // สมมติรหัสลากิจ
    '5' => ['label' => 'กิจกรรม', 'emoji' => '🎉'], // สมมติรหัสกิจกรรม
];
// *** NOTE: Use the exact keys from AttendanceSummary.php for consistency ***
$status_labels = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉'],
];


require_once('header.php');
?>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
    <div class="wrapper">

        <?php require_once('wrapper.php'); ?>

        <div class="content-wrapper">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">📚 เวลาเรียนประจำภาคเรียน/ปีการศึกษา</h1>
                        </div></div></div></div>
            <section class="content py-8">
                <div class="container-fluid">

                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <h3 class="text-lg font-semibold mb-4">🔍 ค้นหาข้อมูล</h3>
                        <form method="GET" action="">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="term" class="block text-sm font-medium text-gray-700">ภาคเรียน</label>
                                    <select name="term" id="term" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="1" <?php echo ($report_term == '1') ? 'selected' : ''; ?>>1</option>
                                        <option value="2" <?php echo ($report_term == '2') ? 'selected' : ''; ?>>2</option>
                                    </select>
                                </div>
                                 <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700">ปีการศึกษา (พ.ศ.)</label>
                                    <input type="number" name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="<?php echo htmlspecialchars($report_year); ?>">
                                </div>
                                <div>
                                    <label for="class" class="block text-sm font-medium text-gray-700">ระดับชั้น</label>
                                    <select name="class" id="class" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($report_class == $i) ? 'selected' : ''; ?>>ม.<?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="room" class="block text-sm font-medium text-gray-700">ห้อง</label>
                                    <select name="room" id="room" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($report_room == $i) ? 'selected' : ''; ?>>ห้อง <?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="self-end">
                                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                        ค้นหา
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php if (!empty($students)) : ?>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4">
                                สรุปการมาเรียน ม.<?php echo $report_class . "/" . $report_room; ?> 
                                ภาคเรียนที่ <?php echo $report_term; ?> ปีการศึกษา <?php echo $report_year; ?>
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ - สกุล</th>
                                            <?php foreach ($status_labels as $info) : ?>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $info['emoji'] . ' ' . $info['label']; ?></th>
                                            <?php endforeach; ?>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">รวม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($students as $student) : 
                                            $stu_id = $student['Stu_id'];
                                            $total = 0;
                                        ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_no']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']); ?></td>
                                                
                                                <?php foreach ($status_labels as $key => $info) : 
                                                    $count = $summary_map[$stu_id][$key] ?? 0;
                                                    $total += $count;
                                                ?>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center font-semibold"><?php echo $count; ?></td>
                                                <?php endforeach; ?>

                                                <td class="px-4 py-4 whitespace-nowrap text-center font-bold text-blue-600"><?php echo $total; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php else : ?>
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow" role="alert">
                            <p>ไม่พบข้อมูลนักเรียนสำหรับชั้นเรียนที่เลือก</p>
                        </div>
                    <?php endif; ?>

                </div>
            </section>
            </div>
        <?php require_once('../footer.php'); ?>
    </div>
    <?php require_once('script.php'); ?>

</body>

</html>