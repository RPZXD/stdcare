<?php
/**
 * Print Student List (Officer) - Advanced Version
 * Supports: Room, Level, School scopes, Dynamic Columns, Font Scaling, A4 Optimized
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../models/Student.php';

// Check login
if (!isset($_SESSION['Officer_login'])) {
    echo "กรุณาเข้าสู่ระบบก่อน";
    exit;
}

use App\DatabaseUsers;
use App\Models\Teacher;
use App\Models\Student;

$db = new DatabaseUsers();
$studentModel = new Student($db);
$teacherModel = new Teacher($db);

$scope = $_GET['scope'] ?? 'room'; // 'room', 'level', 'school'
$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$year = $_GET['year'] ?? 2569;

// Fetch data depending on scope
$students = [];
$titleText = "";
$advisors_text = "";

if ($scope === 'room') {
    if (empty($class) || empty($room)) {
        echo "กรุณาระบุชั้นและห้องสำหรับขอบเขตรายห้อง";
        exit;
    }
    $students = $studentModel->getAll(['class' => $class, 'room' => $room]);
    $titleText = "รายชื่อนักเรียนชั้นมัธยมศึกษาปีที่ {$class}/{$room}";
    
    // Get advisers
    $advisors_data = $teacherModel->getByClassAndRoom($class, $room);
    if (count($advisors_data) > 0) {
        $advisor_names = array_map(function($a) { return $a['Teach_name']; }, $advisors_data);
        $advisorList = [];
        foreach($advisor_names as $index => $name) {
             $advisorList[] = ($index + 1) . ". " . htmlspecialchars($name);
        }
        $advisors_text = "ครูที่ปรึกษา: " . implode("  ", $advisorList);
    } else {
        $advisors_text = "ครูที่ปรึกษา: (ไม่พบข้อมูลครูที่ปรึกษา)";
    }
    
    // Plan name
    function getPlanName($level, $room) {
        if ($level == 1) {
            if ($room == 1) return 'Enrichment Science Classroom (ESC)';
            if ($room == 2) return 'Enrichment Math Classroom (EMC)';
            if ($room == 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
            if ($room == 4) return 'วิทยาศาสตร์พลังสิบ';
            if ($room == 5) return 'ภาษาอังกฤษ';
            if ($room == 6) return 'ภาษาจีน';
            if ($room == 7) return 'ภาษาไทย';
            if ($room == 8) return 'สังคมศึกษา';
            if ($room == 9) return 'อุตสาหกรรม - พาณิชยกรรม';
            if ($room == 10) return 'เกษตรกรรม - คหกรรม';
            if ($room == 11) return 'ศิลปะ - ดนตรี';
            if ($room == 12) return 'กีฬา';
            return '';
        }
        if ($level >= 2 && $level <= 3) {
            if ($room == 1) return 'Enrichment Science Classroom (ESC)';
            if ($room == 2) return 'Enrichment Math Classroom (EMC)';
            if ($room == 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
            if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
            if ($room == 5) return 'ภาษาอังกฤษ';
            if ($room == 6) return 'ภาษาจีน';
            if ($room == 7) return 'ภาษาไทย';
            if ($room == 8) return 'สังคมศึกษา';
            if ($room == 9) return 'อุตสาหกรรม - พาณิชยกรรม';
            if ($room == 10) return 'เกษตรกรรม - คหกรรม';
            if ($room == 11) return 'ศิลปะ - ดนตรี';
            if ($room == 12) return 'กีฬา';
            return '';
        }
        if ($level == 4) {
            if ($room == 1) return 'Enrichment Science Classroom (ESC)';
            if ($room == 2) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
            if ($room == 3) return 'วิทยาศาสตร์พลังสิบ';
            if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
            if ($room == 5) return 'สังคมศาสตร์และภาษาไทย';
            if ($room == 6) return 'ภาษาศาสตร์';
            if ($room == 7) return 'บริหารอุตสาหกรรม';
            return '';
        }
        if ($level >= 5 && $level <= 6) {
            if ($room == 1) return 'Enrichment Science Classroom (ESC)';
            if ($room == 2) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
            if ($room == 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
            if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
            if ($room == 5) return 'แผนการเรียนศิลปศาสตร์ - สังคมศาสตร์';
            if ($room == 6) return 'ภาษาศาสตร์';
            if ($room == 7) return 'บริหารอุตสาหกรรม';
            return '';
        }
        return '';
    }
    $plan = getPlanName($class, $room);
    if (!empty($plan)) {
        $advisors_text .= "  |  แผนการเรียน: " . $plan;
    }
} elseif ($scope === 'level') {
    if (empty($class)) {
        echo "กรุณาระบุชั้นสำหรับขอบเขตระดับชั้น";
        exit;
    }
    $students = $studentModel->getAll(['class' => $class]);
    $titleText = "รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ {$class} ทั้งหมด";
    $advisors_text = "ระดับชั้น ม.{$class}  |  โรงเรียนพิชัย ปีการศึกษา {$year}";
} else {
    // School scope
    $students = $studentModel->getAll([]);
    $titleText = "รายชื่อนักเรียนทั้งหมด โรงเรียนพิชัย";
    $advisors_text = "โรงเรียนพิชัย ปีการศึกษา {$year}";
}

// Prepare data for JS
$studentsJson = json_encode(array_map(function($s, $idx) {
    return [
        'no' => $s['Stu_no'],
        'index' => $idx + 1,
        'id' => $s['Stu_id'],
        'prefix' => $s['Stu_pre'],
        'name' => $s['Stu_name'],
        'sur' => $s['Stu_sur'],
        'major' => $s['Stu_major'],
        'room' => $s['Stu_room'],
        'nick' => $s['Stu_nick'] ?: '',
        'sex' => ($s['Stu_pre'] === 'นาย' || $s['Stu_pre'] === 'เด็กชาย') ? 'ชาย' : 'หญิง',
        'phone' => $s['Stu_phone'] ?: '',
        'citizen_id' => $s['Stu_citizenid'] ?: '',
        'birth' => $s['Stu_birth'] ?: '',
        'blood' => $s['Stu_blood'] ?: '',
        'addr' => $s['Stu_addr'] ?: '',
        'parent_phone' => $s['Par_phone'] ?: '',
    ];
}, $students ?: [], array_keys($students ?: [])));

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titleText; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dist/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f3f4f6;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 5mm;
            }
            body { background-color: white; }
            .no-print { display: none !important; }
            .print-area { 
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid black !important; }
        }

        .paper {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 10mm;
            margin: 10px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8fafc;
            font-weight: bold;
        }

        td, th {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            line-height: 1.2;
        }

        .dynamic-font {
            font-size: 13px; /* default */
        }
        
        .control-panel {
            position: fixed;
            left: 20px;
            top: 20px;
            width: 300px;
            max-height: 90vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .control-panel::-webkit-scrollbar {
            width: 4px;
        }
        .control-panel::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .control-panel::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
    </style>
</head>
<body class="p-0 m-0">

    <!-- Control Panel -->
    <div class="control-panel no-print bg-white p-5 rounded-2xl shadow-2xl border border-slate-200">
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-cog text-violet-500"></i>
            ตั้งค่าการพิมพ์ (เจ้าหน้าที่)
        </h3>
        
        <!-- Font Size -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">ขนาดตัวอักษร: <span id="fontSizeDisplay">13px</span></label>
            <input type="range" id="fontSizeRange" min="8" max="24" value="13" 
                   class="w-full h-2 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
        </div>

        <!-- Columns Toggle -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">แสดงคอลัมน์หลัก:</label>
            <div class="space-y-1.5 border-b border-slate-100 pb-3 mb-3">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-id" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">เลขประจำตัว</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-major-room" <?php echo $scope !== 'room' ? 'checked' : ''; ?> class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ระดับชั้น/ห้อง</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-nick" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ชื่อเล่น</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-sex" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">เพศ</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-phone" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">เบอร์โทรศัพท์</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-citizen" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">เลขบัตรประชาชน</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-birth" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">วันเกิด</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-blood" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">กรุ๊ปเลือด</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-parent-phone" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">เบอร์ผู้ปกครอง</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-addr" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ที่อยู่</span>
                </label>
            </div>
        </div>

        <!-- Signature Toggle -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">แสดงส่วนลงชื่อ:</label>
            <div class="space-y-1.5 pb-3">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="show-signature" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ใช้ส่วนลงชื่อผู้ตรวจ/ครู</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="show-head-signature" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ใช้ส่วนลงชื่อหัวหน้าฝ่าย</span>
                </label>
            </div>
        </div>

        <!-- Custom Columns -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">เพิ่มคอลัมน์กำหนดเอง:</label>
            <p class="text-[10px] text-slate-400 mb-2">* พิมพ์หัวข้อตาราง แยกบรรทัดละ 1 หัวข้อ</p>
            <textarea id="customHeaders" rows="3" 
                      class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none" 
                      placeholder="เช็คชื่อ&#10;หมายเหตุ"></textarea>
        </div>

        <!-- Layout Options -->
        <div class="mb-6 space-y-3">
            <button onclick="window.print()" class="w-full bg-gradient-to-r from-violet-600 to-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-violet-200 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-print"></i> พิมพ์ / บันทึก PDF
            </button>
            <button onclick="exportExcel()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i> ส่งออก Excel
            </button>
            <button onclick="window.close()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>

    <!-- Paper Content -->
    <div class="paper print-area dynamic-font shadow-2xl rounded-sm" id="renderArea">
        <div class="text-center mb-3">
            <h1 class="text-base font-bold mb-1" id="printTitle"><?php echo $titleText; ?></h1>
            <p class="text-sm text-slate-600 mb-2"><?php echo htmlspecialchars($advisors_text); ?></p>
        </div>

        <table id="targetTable">
            <thead>
                <tr id="tableHeader">
                    <!-- Injected by JS -->
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Students will be injected here -->
            </tbody>
        </table>

        <!-- Summary & Stats -->
        <div class="mt-4 flex justify-between text-sm italic">
            <p id="statsSummary">รวมทั้งสิ้น 0 คน</p>
        </div>

        <!-- Dynamic Signature Area -->
        <div id="signature-section" class="mt-12 hidden" style="page-break-inside: avoid;">
            <div class="grid grid-cols-2 gap-y-12 gap-x-8 text-center text-sm">
                <div>
                    <p>ลงชื่อ..........................................................ผู้จัดทำ/ตรวจ</p>
                    <p class="mt-1">(..........................................................)</p>
                </div>
                
                <div id="head-signature-block" class="hidden">
                    <p>ลงชื่อ..........................................................หัวหน้างานทะเบียน</p>
                    <p class="mt-1">(..........................................................)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const students = <?php echo $studentsJson; ?>;
        const scope = "<?php echo $scope; ?>";
        
        // Control Elements
        const controls = {
            fontSizeRange: document.getElementById('fontSizeRange'),
            fontSizeDisplay: document.getElementById('fontSizeDisplay'),
            customHeaders: document.getElementById('customHeaders'),
            colId: document.getElementById('col-id'),
            colMajorRoom: document.getElementById('col-major-room'),
            colNick: document.getElementById('col-nick'),
            colSex: document.getElementById('col-sex'),
            colPhone: document.getElementById('col-phone'),
            colCitizen: document.getElementById('col-citizen'),
            colBirth: document.getElementById('col-birth'),
            colBlood: document.getElementById('col-blood'),
            colParentPhone: document.getElementById('col-parent-phone'),
            colAddr: document.getElementById('col-addr'),
            showSignature: document.getElementById('show-signature'),
            showHeadSignature: document.getElementById('show-head-signature'),
            renderArea: document.getElementById('renderArea'),
            tableHeader: document.getElementById('tableHeader'),
            tableBody: document.getElementById('tableBody'),
            signatureSection: document.getElementById('signature-section'),
            headSignatureBlock: document.getElementById('head-signature-block')
        };

        function updateTable() {
            const fSize = controls.fontSizeRange.value;
            controls.fontSizeDisplay.innerText = fSize + 'px';
            controls.renderArea.style.fontSize = fSize + 'px';
            
            const extraHeaders = controls.customHeaders.value.split('\n').map(h => h.trim()).filter(h => h !== '');

            // Signature Visibility
            controls.signatureSection.classList.toggle('hidden', !controls.showSignature.checked);
            controls.headSignatureBlock.classList.toggle('hidden', !controls.showHeadSignature.checked);

            // Header Logic
            let headerHtml = ``;
            if (scope === 'room') {
                headerHtml += `<th class="w-10 text-center">เลขที่</th>`;
            } else {
                headerHtml += `<th class="w-12 text-center">ลำดับ</th>`;
            }
            if (controls.colId.checked) headerHtml += `<th class="w-20 text-center">เลขประจำตัว</th>`;
            headerHtml += `<th class="text-center font-bold">ชื่อ-สกุล</th>`;
            if (controls.colMajorRoom && controls.colMajorRoom.checked) headerHtml += `<th class="w-20 text-center">ชั้น/ห้อง</th>`;
            if (controls.colNick.checked) headerHtml += `<th class="w-16 text-center">ชื่อเล่น</th>`;
            if (controls.colSex.checked) headerHtml += `<th class="w-12 text-center">เพศ</th>`;
            if (controls.colPhone.checked) headerHtml += `<th class="w-28 text-center">เบอร์โทร</th>`;
            if (controls.colCitizen.checked) headerHtml += `<th class="w-32 text-center">เลขบัตรฯ</th>`;
            if (controls.colBirth.checked) headerHtml += `<th class="w-24 text-center">วันเกิด</th>`;
            if (controls.colBlood.checked) headerHtml += `<th class="w-10 text-center">เลือด</th>`;
            if (controls.colParentPhone.checked) headerHtml += `<th class="w-28 text-center">เบอร์ผู้ปกครอง</th>`;
            if (controls.colAddr.checked) headerHtml += `<th class="text-left">ที่อยู่</th>`;
            
            extraHeaders.forEach(h => {
                headerHtml += `<th class="text-center border font-bold min-w-[40px]">${h}</th>`;
            });
            headerHtml += `<th class="w-20 text-center border font-bold">หมายเหตุ</th>`;
            controls.tableHeader.innerHTML = headerHtml;

            // Body Logic
            let bodyHtml = '';
            students.forEach(s => {
                const nameColorClass = s.sex === 'ชาย' ? 'text-blue-700' : 'text-pink-700';
                bodyHtml += `<tr>`;
                if (scope === 'room') {
                    bodyHtml += `<td class="text-center font-bold">${s.no}</td>`;
                } else {
                    bodyHtml += `<td class="text-center font-bold">${s.index}</td>`;
                }
                if (controls.colId.checked) bodyHtml += `<td class="text-center font-mono">${s.id}</td>`;
                bodyHtml += `<td class="text-left"><span class="${nameColorClass}">${s.prefix}${s.name} ${s.sur}</span></td>`;
                if (controls.colMajorRoom && controls.colMajorRoom.checked) bodyHtml += `<td class="text-center">ม.${s.major}/${s.room}</td>`;
                if (controls.colNick.checked) bodyHtml += `<td class="text-center">${s.nick}</td>`;
                if (controls.colSex.checked) bodyHtml += `<td class="text-center">${s.sex}</td>`;
                if (controls.colPhone.checked) bodyHtml += `<td class="text-center">${s.phone}</td>`;
                if (controls.colCitizen.checked) bodyHtml += `<td class="text-center font-mono text-[10px]">${s.citizen_id}</td>`;
                if (controls.colBirth.checked) bodyHtml += `<td class="text-center">${s.birth}</td>`;
                if (controls.colBlood.checked) bodyHtml += `<td class="text-center">${s.blood}</td>`;
                if (controls.colParentPhone.checked) bodyHtml += `<td class="text-center">${s.parent_phone}</td>`;
                if (controls.colAddr.checked) bodyHtml += `<td class="text-left text-[10px]">${s.addr}</td>`;
                
                extraHeaders.forEach(() => bodyHtml += `<td class="border"></td>`);
                bodyHtml += `<td class="border"></td>`;
                bodyHtml += `</tr>`;
            });
            controls.tableBody.innerHTML = bodyHtml;

            const male = students.filter(s => s.sex === 'ชาย').length;
            const female = students.length - male;
            document.getElementById('statsSummary').innerHTML = `รวมทั้งสิ้น ${students.length} คน (ชาย ${male} คน, หญิง ${female} คน)`;
        }

        // Add listeners to all checkboxes and inputs
        Object.values(controls).filter(c => c && (c.type === 'checkbox' || c.type === 'range' || c.tagName === 'TEXTAREA')).forEach(el => {
            el.addEventListener('change', updateTable);
            el.addEventListener('input', updateTable);
        });

        // Export Excel
        function exportExcel() {
            const data = [];
            const extraHeaders = controls.customHeaders.value.split('\n').map(h => h.trim()).filter(h => h !== '');

            const headers = [scope === 'room' ? 'เลขที่' : 'ลำดับ'];
            if (controls.colId.checked) headers.push('เลขประจำตัว');
            headers.push('ชื่อ-สกุล');
            if (controls.colMajorRoom && controls.colMajorRoom.checked) headers.push('ชั้น/ห้อง');
            if (controls.colNick.checked) headers.push('ชื่อเล่น');
            if (controls.colSex.checked) headers.push('เพศ');
            if (controls.colPhone.checked) headers.push('เบอร์โทร');
            if (controls.colCitizen.checked) headers.push('เลขบัตรฯ');
            if (controls.colBirth.checked) headers.push('วันเกิด');
            if (controls.colBlood.checked) headers.push('กรุ๊ปเลือด');
            if (controls.colParentPhone.checked) headers.push('เบอร์ผู้ปกครอง');
            if (controls.colAddr.checked) headers.push('ที่อยู่');
            
            extraHeaders.forEach(h => headers.push(h));
            headers.push('หมายเหตุ');
            
            data.push(headers);

            students.forEach(s => {
                const row = [scope === 'room' ? s.no : s.index];
                if (controls.colId.checked) row.push({ v: s.id || '', t: 's' });
                row.push(`${s.prefix}${s.name} ${s.sur}`);
                if (controls.colMajorRoom && controls.colMajorRoom.checked) row.push(`ม.${s.major}/${s.room}`);
                if (controls.colNick.checked) row.push(s.nick);
                if (controls.colSex.checked) row.push(s.sex);
                if (controls.colPhone.checked) row.push({ v: s.phone || '', t: 's' });
                if (controls.colCitizen.checked) row.push({ v: s.citizen_id || '', t: 's' });
                if (controls.colBirth.checked) row.push(s.birth);
                if (controls.colBlood.checked) row.push(s.blood);
                if (controls.colParentPhone.checked) row.push({ v: s.parent_phone || '', t: 's' });
                if (controls.colAddr.checked) row.push(s.addr);
                extraHeaders.forEach(() => row.push(''));
                row.push('');
                data.push(row);
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, "รายชื่อนักเรียน");
            
            let filename = `รายชื่อนักเรียน_ทั้งหมด`;
            if (scope === 'room') filename = `รายชื่อนักเรียน_ม${s.major}_${s.room}`;
            else if (scope === 'level') filename = `รายชื่อนักเรียน_ระดับชั้น_ม${students[0].major}`;
            
            XLSX.writeFile(wb, `${filename}.xlsx`);
        }

        updateTable();
    </script>
</body>
</html>
