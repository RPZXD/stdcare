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
// Database stores dates in Gregorian (A.D.) format

// <input type="date"> and date() in PHP use Gregorian (A.D.) format (e.g., 2024-10-21)
$report_date = $_GET['date'] ?? date('Y-m-d');

// Allow filtering by all classes/rooms
$report_class = $_GET['class'] ?? 'all'; // Default to 'all'
$report_room = $_GET['room'] ?? 'all';   // Default to 'all'


// --- Fetch Report Data ---
// Use Gregorian date for querying
$all_students = $attendance->getStudentsWithAttendance(
    $report_date, 
    ($report_class === 'all' ? null : $report_class), 
    ($report_room === 'all' ? null : $report_room), 
    $term, 
    $pee
);

// Helper for status labels
$temp_summary = new AttendanceSummary([], 0, 0, '', '', '');
$status_labels = $temp_summary->status_labels;

// Filter for absent students (status is NULL or NOT '1')
$absent_students = [];
foreach ($all_students as $student) {
    $status = $student['attendance_status'];
    // Not present (1) OR null (which defaults to absent '2')
    if ($status === null || $status != '1') {
        $status_key = $status ?? '2'; // Default null to '2' (Absent)
        $student['display_status'] = $status_labels[$status_key] ?? ['label' => 'ไม่ทราบ', 'emoji' => '❓'];
        $absent_students[] = $student;
    }
}


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
                            <h1 class="m-0">🚫 รายชื่อนักเรียนที่ไม่มาเรียน</h1>
                        </div></div></div></div>
            <section class="content py-8">
                <div class="container-fluid">

                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <h3 class="text-lg font-semibold mb-4">🔍 ค้นหาข้อมูล</h3>
                        <form method="GET" action="">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700">วันที่</label>
                                    <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="<?php echo htmlspecialchars($report_date); ?>">
                                </div>
                                <div>
                                    <label for="class" class="block text-sm font-medium text-gray-700">ระดับชั้น</label>
                                    <select name="class" id="class" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="all" <?php echo ($report_class === 'all') ? 'selected' : ''; ?>>ทั้งหมด</option>
                                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($report_class == $i) ? 'selected' : ''; ?>>ม.<?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="room" class="block text-sm font-medium text-gray-700">ห้อง</label>
                                    <select name="room" id="room" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                         <option value="all" <?php echo ($report_room === 'all') ? 'selected' : ''; ?>>ทั้งหมด</option>
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
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-4">
                            รายชื่อนักเรียนที่ไม่มาเรียน วันที่ <?php echo thaiDateShort($report_date); ?> 
                            (พบ <?php echo count($absent_students); ?> คน)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชั้น/ห้อง</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสนักเรียน</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ - สกุล</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เหตุผล</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($absent_students)) : ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลนักเรียนที่ไม่มาเรียน</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($absent_students as $student) :
                                            $status_info = $student['display_status'];
                                        ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_major'] . '/' . $student['Stu_room']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_no']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_id']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="font-semibold"><?php echo $status_info['emoji'] . ' ' . $status_info['label']; ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($student['reason'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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

</body>

</html>