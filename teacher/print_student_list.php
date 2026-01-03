<?php
/**
 * Print Student List - Advanced Version
 * Supports: Multiple Teachers, Dynamic Columns, Font Scaling, A4 Optimized
 */

session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Teacher.php';

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentClass = new Student($db);
$teacherClass = new Teacher($db);

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$term = $user->getTerm();
$pee = $user->getPee();

// Get class/room from query or session
$class = isset($_GET['class']) ? $_GET['class'] : ($userData['Teach_class'] ?? '');
$room = isset($_GET['room']) ? $_GET['room'] : ($userData['Teach_room'] ?? '');

// Get students
$students = $studentClass->getStudentsByMajorRoom($class, $room);

// Get all teachers for this room
$teachers = $teacherClass->getTeachersByClassAndRoomDuo($class, $room);
$teacherList = [];
if ($teachers) {
    foreach ($teachers as $idx => $t) {
        $teacherList[] = ($idx + 1) . "." . $t['Teach_name'];
    }
} else {
    $teacherList[] = "1." . ($userData['Teach_name'] ?? '');
}

$teacherNames = implode(" ", $teacherList);

// Prepare data for JS
$studentsJson = json_encode(array_map(function($s) {
    return [
        'no' => $s['Stu_no'],
        'id' => $s['Stu_id'],
        'prefix' => $s['Stu_pre'],
        'name' => $s['Stu_name'],
        'sur' => $s['Stu_sur'],
        'nick' => $s['Stu_nick'] ?: '',
        'sex' => ($s['Stu_pre'] === 'นาย' || $s['Stu_pre'] === 'เด็กชาย') ? 'ชาย' : 'หญิง',
        'phone' => $s['Stu_phone'] ?: '',
        'citizen_id' => $s['Stu_citizenid'] ?: '',
        'birth' => $s['Stu_birth'] ?: '',
        'blood' => $s['Stu_blood'] ?: '',
        'addr' => $s['Stu_addr'] ?: '',
        'parent_phone' => $s['Par_phone'] ?: '',
    ];
}, $students ?? []));

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบรายชื่อนักเรียน ม.<?php echo $class; ?>/<?php echo $room; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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

        .blank-col {
            width: 8mm;
        }

        /* Dynamic Font Size Class */
        .dynamic-font {
            font-size: 14px; /* default */
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

        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Custom scrollbar for control panel */
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
            ตั้งค่าการพิมพ์
        </h3>
        
        <!-- Font Size -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">ขนาดตัวอักษร: <span id="fontSizeDisplay">14px</span></label>
            <input type="range" id="fontSizeRange" min="8" max="24" value="12" 
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
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ใช้ส่วนลงชื่อครูที่ปรึกษา</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="show-head-signature" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">ใช้ส่วนลงชื่อหัวหน้าระดับ</span>
                </label>
            </div>
        </div>

        <!-- Custom Columns -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">เพิ่มคอลัมน์กำหนดเอง:</label>
            <p class="text-[10px] text-slate-400 mb-2">* พิมพ์หัวข้อตาราง แยกบรรทัดละ 1 หัวข้อ</p>
            <textarea id="customHeaders" rows="4" 
                      class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none" 
                      placeholder="เช็คชื่อ&#10;คะแนนเก็บ"></textarea>
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
            <h1 class="text-base font-bold mb-1">รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?php echo $class; ?>/<?php echo $room; ?> โรงเรียนพิชัย ปีการศึกษา <?php echo $pee; ?></h1>
            <p class="text-base">ครูที่ปรึกษา <?php echo htmlspecialchars($teacherNames); ?></p>
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

        <!-- Summary & Signatures -->
        <div class="mt-4 flex justify-between text-sm italic">
            <p id="statsSummary">รวมทั้งสิ้น <?php echo count($students); ?> คน</p>
        </div>

        <!-- Dynamic Signature Area -->
        <div id="signature-section" class="mt-16 hidden" style="page-break-inside: avoid;">
            <div class="grid grid-cols-2 gap-y-12 gap-x-8 text-center">
                <?php if ($teachers): ?>
                    <?php foreach ($teachers as $t): ?>
                    <div>
                        <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>
                        <p class="mt-1">( <?php echo htmlspecialchars($t['Teach_name']); ?> )</p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>
                        <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>
                        <p class="mt-1">( <?php echo htmlspecialchars($userData['Teach_name']); ?> )</p>
                    </div>
                <?php endif; ?>
                
                <div id="head-signature-block" class="hidden">
                    <p>ลงชื่อ..........................................................หัวหน้าระดับ</p>
                    <p class="mt-1">(..........................................................)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const students = <?php echo $studentsJson; ?>;
        
        // Control Elements
        const controls = {
            fontSizeRange: document.getElementById('fontSizeRange'),
            fontSizeDisplay: document.getElementById('fontSizeDisplay'),
            customHeaders: document.getElementById('customHeaders'),
            colId: document.getElementById('col-id'),
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
            let headerHtml = `<th class="w-10 text-center">เลขที่</th>`;
            if (controls.colId.checked) headerHtml += `<th class="w-24 text-center">เลขประจำตัว</th>`;
            headerHtml += `<th class="text-center font-bold">ชื่อ-สกุล</th>`;
            if (controls.colNick.checked) headerHtml += `<th class="w-20 text-center">ชื่อเล่น</th>`;
            if (controls.colSex.checked) headerHtml += `<th class="w-16 text-center">เพศ</th>`;
            if (controls.colPhone.checked) headerHtml += `<th class="w-32 text-center">เบอร์โทร</th>`;
            if (controls.colCitizen.checked) headerHtml += `<th class="w-36 text-center">เลขบัตรฯ</th>`;
            if (controls.colBirth.checked) headerHtml += `<th class="w-24 text-center">วันเกิด</th>`;
            if (controls.colBlood.checked) headerHtml += `<th class="w-12 text-center">เลือด</th>`;
            if (controls.colParentPhone.checked) headerHtml += `<th class="w-32 text-center">เบอร์ผู้ปกครอง</th>`;
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
                bodyHtml += `<td class="text-center font-bold">${s.no}</td>`;
                if (controls.colId.checked) bodyHtml += `<td class="text-center font-mono">${s.id}</td>`;
                bodyHtml += `<td class="text-left"><span class="${nameColorClass}">${s.prefix}${s.name} ${s.sur}</span></td>`;
                if (controls.colNick.checked) bodyHtml += `<td class="text-center">${s.nick}</td>`;
                if (controls.colSex.checked) bodyHtml += `<td class="text-center font-bold">${s.sex}</td>`;
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

            const headers = ['เลขที่'];
            if (controls.colId.checked) headers.push('เลขประจำตัว');
            headers.push('ชื่อ-สกุล');
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
                const row = [s.no];
                if (controls.colId.checked) row.push(s.id);
                row.push(`${s.prefix}${s.name} ${s.sur}`);
                if (controls.colNick.checked) row.push(s.nick);
                if (controls.colSex.checked) row.push(s.sex);
                if (controls.colPhone.checked) row.push(s.phone);
                if (controls.colCitizen.checked) row.push(s.citizen_id);
                if (controls.colBirth.checked) row.push(s.birth);
                if (controls.colBlood.checked) row.push(s.blood);
                if (controls.colParentPhone.checked) row.push(s.parent_phone);
                if (controls.colAddr.checked) row.push(s.addr);
                extraHeaders.forEach(() => row.push(''));
                row.push('');
                data.push(row);
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, "รายชื่อนักเรียน");
            XLSX.writeFile(wb, `รายชื่อนักเรียน_ม${<?php echo json_encode($class); ?>}_${<?php echo json_encode($room); ?>}.xlsx`);
        }

        updateTable();
    </script>
</body>
</html></body>
</html>
