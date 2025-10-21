<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Attendance.php"); // Added
include_once("../class/AttendanceSummary.php"); // Added
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$student = new Student($db);
$attendance = new Attendance($db); // Added

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

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

// <input type="date"> and date() in PHP use Gregorian (A.D.) format (e.g., 2024-10-21)
$report_gregorian_date = $_GET['date'] ?? date('Y-m-d');

// We must convert this A.D. date to a B.E. date string (e.g., 2567-10-21) for the SQL query
$date_parts = explode('-', $report_gregorian_date);
$gregorian_year = (int)$date_parts[0];
$month = $date_parts[1];
$day = $date_parts[2];
$buddhist_year = $gregorian_year + 543;

// This B.E. date is for the database query
$report_buddhist_date = $buddhist_year . '-' . $month . '-' . $day; 

// Get class/room filters
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// --- Fetch Report Data ---

// Use the B.E. date ($report_buddhist_date) for querying the database
$students = $attendance->getStudentsWithAttendance($report_buddhist_date, $report_class, $report_room, $term, $pee);

// Use the B.E. date for the summary
$summary = new AttendanceSummary($students, $report_class, $report_room, $report_buddhist_date, $term, $pee);

// NOTE: The $report_gregorian_date is still used in the <input> value, which is correct.


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
                            <h1 class="m-0">📅 เวลาเรียนประจำวัน</h1>
                        </div></div></div></div>
            <section class="content py-8">
                <div class="container-fluid">

                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <h3 class="text-lg font-semibold mb-4">🔍 ค้นหาข้อมูล</h3>
                        <form method="GET" action="">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700">วันที่</label>
                                    <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="<?php echo htmlspecialchars($report_gregorian_date); ?>">
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
                        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                             <h3 class="text-lg font-semibold mb-4">สรุปการมาเรียน ม.<?php echo $report_class . "/" . $report_room; ?> วันที่ <?php echo thaiDateShort($report_buddhist_date); ?> (นักเรียนทั้งหมด <?php echo count($students); ?> คน)</h3>
                             <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                <?php foreach($summary->status_labels as $key => $info): 
                                    $count = $summary->status_count[$key];
                                    $percent = count($students) > 0 ? round($count * 100 / count($students), 1) : 0;
                                ?>
                                <div class="bg-gray-50 p-3 rounded-lg border">
                                    <div class="text-2xl"><?php echo $info['emoji']; ?></div>
                                    <div class="font-semibold"><?php echo $info['label']; ?></div>
                                    <div class="text-xl font-bold"><?php echo $count; ?> คน</div>
                                    <div class="text-sm text-gray-500">(<?php echo $percent; ?>%)</div>
                                </div>
                                <?php endforeach; ?>
                             </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4">📋 รายชื่อนักเรียน</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสนักเรียน</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ - สกุล</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เหตุผล</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($students as $student) :
                                            $status_key = $student['attendance_status'] ?? '2'; // Default to 'ขาดเรียน' if null
                                            $status_info = $summary->status_labels[$status_key] ?? ['label' => 'ไม่ทราบ', 'emoji' => '❓'];
                                        ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_no']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_id']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="font-semibold"><?php echo $status_info['emoji'] . ' ' . $status_info['label']; ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['reason'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php else : ?>
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow" role="alert">
                            <p class="font-bold">ไม่พบข้อมูล</p>
                            <p>ไม่พบข้อมูลนักเรียนสำหรับชั้นเรียนที่เลือก หรือยังไม่มีการเช็คชื่อในวันนี้</p>
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