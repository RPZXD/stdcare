<?php
// ตัวอย่าง: ดึงข้อมูล SDQ ของนักเรียน (สมมติว่ามีตารางชื่อ sdq_results)
include_once("../config/Database.php");
include_once("../class/SDQ.php");
require_once("../class/Utils.php");
require_once("../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงชั้นเรียนทั้งหมด
$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ดึงห้องเรียน (ถ้ามีการเลือกชั้น)
$class = $_GET['class'] ?? ($classList[0] ?? '');
$room = $_GET['room'] ?? '';
// $pee, $term มาจากระบบ

// ห้องเรียนอัตโนมัติ
$roomOptions = [];
if ($class) {
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->bindParam(':class', $class);
    $stmt->execute();
    $roomOptions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$room && count($roomOptions) > 0) {
        $room = $roomOptions[0];
    }
}



?>
<div class="mb-6">
    <h2 class="text-xl font-bold text-red-600 flex items-center gap-2 mb-4">
        🧠 รายงานผลการคัดกรอง 11 ด้าน (รายระดับชั้น)
    </h2>
    <form method="get" id="screen-filter-form" class="mb-4 flex flex-wrap gap-2 items-end" onsubmit="return false;">
        <div>
            <label class="block text-sm text-gray-600">ชั้น</label>
            <select name="class" id="class-select" class="border rounded px-2 py-1 min-w-[80px]">
                <?php foreach ($classList as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $c == $class ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <div class="mb-4 flex justify-end">
        <button onclick="printScreenClassTable()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow font-semibold print:hidden">
            🖨️ พิมพ์รายงาน
        </button>
    </div>
    <div id="screen-table-container">
        <div class="text-center text-gray-400 py-6">กรุณาเลือกชั้น</div>
    </div>
    <script>
    function loadScreenTable() {
        var classValue = document.getElementById('class-select').value;
        if (classValue) {
            document.getElementById('screen-table-container').innerHTML = '<div class="text-center text-gray-400 py-6">กำลังโหลด...</div>';
            fetch('api/ajax_screen_class_table.php?class=' + encodeURIComponent(classValue))
                .then(response => response.text())
                .then(html => {
                    // เพิ่มหัวกระดาษตอน print
                    let header = `
                        <div id="print-header" class="mb-4 text-center">
                            <div class="font-bold text-xl">รายงานผลการคัดกรอง 11 ด้าน (รายชั้น)</div>
                            <div class="text-lg">
                                ชั้นมัธยมศึกษาปีที่ ${classValue}
                            </div>
                            <div class="text-lg">
                                ปีการศึกษา <?= htmlspecialchars($pee) ?> ภาคเรียนที่ <?= htmlspecialchars($term) ?>
                            </div>
                        </div>
                    `;
                    document.getElementById('screen-table-container').innerHTML = `<div id="print-area">${header}${html}</div>`;
                });
        } else {
            document.getElementById('screen-table-container').innerHTML = '<div class="text-center text-gray-400 py-6">กรุณาเลือกชั้น</div>';
        }
    }

    document.getElementById('class-select').addEventListener('change', function() {
        loadScreenTable();
    });
    document.addEventListener('DOMContentLoaded', function() {
        loadScreenTable();
    });

    function printScreenClassTable() {
        let printContents = document.getElementById('print-area').innerHTML;
        let win = window.open('', '', 'width=900,height=700');
        win.document.write('<html><head><title>รายงานผลการคัดกรอง 11 ด้าน (รายชั้น)</title>');
        win.document.write('<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">');
        win.document.write('<style>@media print{.print\\:hidden{display:none !important;} body{background:#fff !important;}}</style>');
        win.document.write('</head><body onload="window.print();setTimeout(function(){window.close()},100);">');
        win.document.write(printContents);
        win.document.write('</body></html>');
        win.document.close();
    }
    </script>
    <style>
    @media print {
        .print\:hidden { display: none !important; }
        body { background: #fff !important; }
    }
    </style>
</div>
