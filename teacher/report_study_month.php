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
$student_obj = new Student($db); // Renamed to avoid conflict

// Fetch terms and pee
$term = $user->getTerm();
$current_buddhist_year = $user->getPee(); // ใช้ปี พ.ศ. จาก DB

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
$report_year = $_GET['year'] ?? $current_buddhist_year;
$report_month = $_GET['month'] ?? date('m');
$report_class = $_GET['class'] ?? $userData['Teach_major'] ?? '1';
$report_room = $_GET['room'] ?? $userData['Teach_room'] ?? '1';

// Days in month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $report_month, ($report_year - 543)); // cal_days_in_month needs A.D. year

// --- Fetch Report Data ---
$students = $student_obj->fetchFilteredStudents2($report_class, $report_room);
$attendance_map = [];
$summary_map = [];

if (!empty($students)) {
    // ... (โค้ดดึงข้อมูล Database เหมือนเดิม) ...
    $query = "SELECT student_id, attendance_date, attendance_status
              FROM student_attendance sa
              JOIN student s ON sa.student_id = s.Stu_id
              WHERE s.Stu_major = :class AND s.Stu_room = :room
                AND YEAR(sa.attendance_date) = :year 
                AND MONTH(sa.attendance_date) = :month";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':class' => $report_class,
        ':room' => $report_room,
        ':year' => $report_year, // B.E. year
        ':month' => $report_month
    ]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Re-organize data for easy lookup
    foreach ($records as $record) {
        $day = (int)date('j', strtotime($record['attendance_date']));
        $stu_id = $record['student_id'];
        $status = $record['attendance_status'];
        
        $attendance_map[$stu_id][$day] = $status;

        if (!isset($summary_map[$stu_id][$status])) {
            $summary_map[$stu_id][$status] = 0;
        }
        $summary_map[$stu_id][$status]++;
    }
}

// Helper for status symbols (for the grid)
$status_symbols = [
    '1' => '✅', '2' => '❌', '3' => '🕒',
    '4' => '🤒', '5' => '📝', '6' => '🎉',
];

// Helper for Legend (full labels)
$status_labels_legend = [
    '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
    '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
    '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
    '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒'],
    '5' => ['label' => 'ลากิจ', 'emoji' => '📝'],
    '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉'],
];

// Helper for Thai Months
$thai_months = [
    '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
    '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
    '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
    '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
];


require_once('header.php');
?>

<style>
    /* แนะนำให้ browser พิมพ์แนวนอน */
    @page {
        size: A4 landscape;
        margin: 0.5in;
    }

    @media print {
        /* ซ่อน element ที่ไม่ต้องการพิมพ์ */
        .main-sidebar, .main-header, .content-header, .main-footer, .no-print, .filter-card {
            display: none !important;
        }
        /* ปรับ layout ของ content ให้เต็มหน้า */
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        /* ปรับ container ของรายงาน */
        #report-container {
            box-shadow: none;
            border: none;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: white !important;
        }

        /* --- ส่วนแก้ไขสำหรับตารางล้น --- */
        /* บังคับให้ div ที่มี scrollbar แสดงผลเนื้อหาที่ล้นออกมา */
        #table-container {
            overflow-x: visible !important;
            width: 100% !important;
        }
        /* ยกเลิกการ 'sticky' (ปักหมุด) คอลัมน์ชื่อ */
        .sticky {
            position: static !important;
        }
        /* บังคับให้ตารางใช้ความกว้าง 100% ของกระดาษ */
        #report-table {
            width: 100% !important;
            table-layout: auto; /* ให้ browser จัดการความกว้างคอลัมน์เอง */
        }
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
    <div class="wrapper">

        <?php require_once('wrapper.php'); ?>

        <div class="content-wrapper">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">📆 เวลาเรียนประจำเดือน</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content py-8">
                <div class="container-fluid">

                    <div class="bg-white p-4 rounded-lg shadow-md mb-6 filter-card no-print">
                        <h3 class="text-lg font-semibold mb-4">🔍 ค้นหาข้อมูล</h3>
                        <form method="GET" action="">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="month" class="block text-sm font-medium text-gray-700">เดือน</label>
                                    <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <?php foreach ($thai_months as $month_val => $month_name) : ?>
                                            <option value="<?php echo $month_val; ?>" <?php echo ($report_month == $month_val) ? 'selected' : ''; ?>><?php echo $month_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                 <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700">ปี (พ.ศ.)</label>
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
                    
                        <div class="bg-white p-4 rounded-lg shadow-md mb-6 no-print">
                            <h3 class="text-lg font-semibold mb-4">🖨️ พิมพ์/ส่งออก</h3>
                            <div class="flex flex-wrap gap-4">
                                <button onclick="printReport()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                                    พิมพ์
                                </button>
                                <button onclick="exportToExcel('report-table', 'report_month.xls')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                                    ดาวน์โหลด Excel
                                </button>
                            </div>
                        </div>


                        <div class="bg-white p-4 rounded-lg shadow-md" id="report-container">
                            <h3 class="text-lg font-semibold mb-4">ตารางการมาเรียน ม.<?php echo $report_class . "/" . $report_room; ?> เดือน <?php echo $thai_months[$report_month] . " " . $report_year; ?></h3>
                            
                            <div class="overflow-x-auto" id="table-container">
                                <table class="min-w-full divide-y divide-gray-200 border" id="report-table">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">ชื่อ - สกุล</th>
                                            <?php for ($day = 1; $day <= $days_in_month; $day++) : ?>
                                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 40px;"><?php echo $day; ?></th>
                                            <?php endfor; ?>
                                            <?php foreach ($status_symbols as $symbol): ?>
                                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100" style="min-width: 50px;"><?php echo $symbol; ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($students as $student) : $stu_id = $student['Stu_id']; ?>
                                            <tr>
                                                <td class="px-2 py-4 whitespace-nowrap text-sm sticky left-0 bg-white z-10">
                                                    (<?php echo htmlspecialchars($student['Stu_no']); ?>) <?php echo htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']); ?>
                                                </td>
                                                <?php for ($day = 1; $day <= $days_in_month; $day++) : 
                                                    $status = $attendance_map[$stu_id][$day] ?? null;
                                                    $symbol = $status ? ($status_symbols[$status] ?? '❓') : '-';
                                                ?>
                                                    <td class="px-2 py-4 whitespace-nowrap text-center text-sm"><?php echo $symbol; ?></td>
                                                <?php endfor; ?>
                                                <?php foreach ($status_symbols as $key => $symbol): 
                                                    $count = $summary_map[$stu_id][$key] ?? 0;
                                                ?>
                                                <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-bold bg-gray-50"><?php echo $count; ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div> <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="font-semibold mb-2 text-gray-700">คำอธิบายสัญลักษณ์:</h4>
                                <div class="flex flex-wrap gap-x-4 gap-y-2">
                                    <?php foreach ($status_labels_legend as $info) : ?>
                                        <span class="text-sm text-gray-600">
                                            <?php echo $info['emoji']; ?> = <?php echo htmlspecialchars($info['label']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <span class="text-sm text-gray-600">- = ยังไม่เช็คชื่อ</span>
                                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // 2. Print Function
        function printReport() {
            window.print();
        }

        // 3. (UPDATED) PDF Function
        function exportToPDF(elementId, filename) {
            const element = document.getElementById(elementId);
            const tableContainer = document.getElementById('table-container'); // 1. หา div ที่มี scroll
            const stickyCells = element.querySelectorAll('.sticky'); // 2. หา cell ที่ 'sticky'

            // --- 3. เก็บ CSS เดิมไว้ ---
            const oldContainerOverflow = tableContainer.style.overflowX;
            const oldStickyStyles = [];
            stickyCells.forEach(cell => {
                oldStickyStyles.push({ el: cell, position: cell.style.position, zIndex: cell.style.zIndex });
            });

            // --- 4. แก้ไข CSS ชั่วคราว ---
            // บังคับให้ div แสดงผลเต็มความกว้าง (ไม่เลื่อน)
            tableContainer.style.overflowX = 'visible';
            // ปลด 'sticky' ออก (เพราะ html2canvas ไม่รองรับ)
            stickyCells.forEach(cell => {
                cell.style.position = 'static';
                cell.style.zIndex = 'auto';
            });
            // --- จบการแก้ไขชั่วคราว ---

            const opt = {
                margin:       0.5,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 2, 
                    useCORS: true, 
                    // บังคับให้ html2canvas จับภาพตามความกว้างจริงของเนื้อหา (สำคัญมาก)
                    windowWidth: element.scrollWidth 
                },
                jsPDF:        { unit: 'in', format: 'A4', orientation: 'landscape' }
            };

            // 5. สร้าง PDF
            html2pdf().from(element).set(opt).save().then(() => {
                // --- 6. คืนค่า CSS เดิม ---
                // (จะทำงานหลังจาก PDF ถูกสร้างแล้ว)
                tableContainer.style.overflowX = oldContainerOverflow;
                oldStickyStyles.forEach(item => {
                    item.el.style.position = item.position;
                    item.el.style.zIndex = item.zIndex;
                });
                // --- จบการคืนค่า ---
            });
        }


        // 4. Excel Function (HTML Table Hack)
        function exportToExcel(tableId, filename) {
            const table = document.getElementById(tableId);
            let tableHTML = table.outerHTML;
            
            // สร้าง template ของ Excel (HTML)
            // เพิ่ม <meta charset="UTF-8"> เพื่อให้ Excel อ่านภาษาไทยถูก
            const template = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:x="urn:schemas-microsoft-com:office:excel" 
                      xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="UTF-8">
                    </head>
                <body>
                    ${tableHTML}
                </body>
                </html>`;

            // สร้าง data URI
            const data_type = 'data:application/vnd.ms-excel';
            const encoded_template = encodeURIComponent(template);
            
            // สร้าง link ชั่วคราวเพื่อดาวน์โหลด
            const a = document.createElement('a');
            a.href = data_type + ', ' + encoded_template;
            a.download = filename;
            a.click();
        }
    </script>
    <?php require_once('script.php'); ?>

</body>

</html>