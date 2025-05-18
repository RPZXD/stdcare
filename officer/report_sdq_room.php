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
        🧠 รายงานผล SDQ (รายห้อง)
    </h2>
    <form method="get" id="sdq-filter-form" class="mb-4 flex flex-wrap gap-2 items-end" onsubmit="return false;">
        <div>
            <label class="block text-sm text-gray-600">ชั้น</label>
            <select name="class" id="class-select" class="border rounded px-2 py-1 min-w-[80px]">
                <?php foreach ($classList as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $c == $class ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600">ห้อง</label>
            <select name="room" id="room-select" class="border rounded px-2 py-1 min-w-[80px]">
                <?php foreach ($roomOptions as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>" <?= $r == $room ? 'selected' : '' ?>><?= htmlspecialchars($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <div class="mb-4 flex justify-end">
        <button onclick="printSDQRoomTable()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow font-semibold print:hidden">
            🖨️ พิมพ์รายงาน
        </button>
    </div>
    <div id="sdq-table-container">
        <div class="text-center text-gray-400 py-6">กรุณาเลือกชั้นและห้อง</div>
    </div>
    <script>
    function loadSDQTable() {
        var classValue = document.getElementById('class-select').value;
        var roomValue = document.getElementById('room-select').value;
        if (classValue && roomValue) {
            document.getElementById('sdq-table-container').innerHTML = '<div class="text-center text-gray-400 py-6">กำลังโหลด...</div>';
            fetch('api/ajax_sdq_room_table.php?class=' + encodeURIComponent(classValue) + '&room=' + encodeURIComponent(roomValue))
                .then(response => response.text())
                .then(html => {
                    // เพิ่มหัวกระดาษตอน print
                    let header = `
                        <div id="print-header" class="mb-4 text-center">
                            <div class="font-bold text-xl">รายงานผล SDQ (รายห้อง)</div>
                            <div class="text-lg">
                                ชั้นมัธยมศึกษาปีที่ ${classValue}${roomValue ? '/' + roomValue : ''}
                            </div>
                            <div class="text-lg">
                                ปีการศึกษา <?= htmlspecialchars($pee) ?> ภาคเรียนที่ <?= htmlspecialchars($term) ?>
                            </div>
                        </div>
                    `;
                    document.getElementById('sdq-table-container').innerHTML = `<div id="print-area">${header}${html}</div>`;
                });
        } else {
            document.getElementById('sdq-table-container').innerHTML = '<div class="text-center text-gray-400 py-6">กรุณาเลือกชั้นและห้อง</div>';
        }
    }
    document.getElementById('class-select').addEventListener('change', function() {
        var classValue = this.value;
        var roomSelect = document.getElementById('room-select');
        roomSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
        fetch('api/ajax_get_rooms.php?class=' + encodeURIComponent(classValue))
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.forEach(function(room) {
                    html += '<option value="' + room + '">' + room + '</option>';
                });
                roomSelect.innerHTML = html;
                loadSDQTable();
            });
    });
    document.getElementById('room-select').addEventListener('change', function() {
        loadSDQTable();
    });
    document.addEventListener('DOMContentLoaded', function() {
        loadSDQTable();
    });

    function printSDQRoomTable() {
        let printContents = document.getElementById('print-area').innerHTML;
        let win = window.open('', '', 'width=900,height=700');
        win.document.write('<html><head><title>รายงานผล SDQ (รายห้อง)</title>');
        // เพิ่มลิงก์ Tailwind CDN
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
