<?php
/**
 * Print Student List with GPS Coordinates
 * Optimized for A4 Portrait/Landscape and custom groupings/color coding
 */

session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Teacher.php';

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacherClass = new Teacher($db);

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$term = $user->getTerm();
$pee = $user->getPee();

// Get class/room from query
$class = isset($_GET['class']) ? $_GET['class'] : ($userData['Teach_class'] ?? '');
$room = isset($_GET['room']) ? $_GET['room'] : ($userData['Teach_room'] ?? '');

// Fetch student GPS data for the class
$sql = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_no, s.Stu_addr,
               s.Stu_nick, s.Stu_phone, s.Par_phone, s.Stu_picture,
               g.latitude, g.longitude, g.updated_at
        FROM student s
        JOIN student_gps g ON s.Stu_id = g.Stu_id
        WHERE s.Stu_major = ? AND s.Stu_room = ?
        ORDER BY CAST(s.Stu_no AS UNSIGNED)";
$stmt = $db->prepare($sql);
$stmt->execute([$class, $room]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to extract village name from address
function getVillageGroup($addr) {
    if (empty($addr)) {
        return "ไม่ระบุหมู่บ้าน/ที่อยู่";
    }
    $addrClean = preg_replace('/([ก-๙]+)(อ\.|จ\.|อำเภอ|จังหวัด)/u', '$1 $2', $addr);
    $addrClean = preg_replace('/\s+/', ' ', $addrClean);
    
    $moo = '';
    if (preg_match('/(?:หมู่ที่|หมู่|ม\s*\.\s*|ม\s+)\s*(\d+)/u', $addrClean, $matches)) {
        $moo = $matches[1];
    }
    
    $subdistrict = '';
    if (preg_match('/(?:ตำบล|ต\s*\.\s*|ต\s+)\s*([\x{0e00}-\x{0e7f}]+)/u', $addrClean, $matches)) {
        $subdistrict = trim($matches[1]);
    }
    
    if (!$moo && !$subdistrict) {
        return "ไม่ระบุหมู่บ้าน/ที่อยู่";
    }
    
    $result = '';
    if ($moo) {
        $result .= "หมู่ " . $moo;
    }
    if ($subdistrict) {
        $result .= ($result ? " " : "") . "ต." . $subdistrict;
    }
    return $result;
}

// Helper function to extract subdistrict name from address
function getSubdistrictGroup($addr) {
    if (empty($addr)) {
        return "ไม่ระบุตำบล";
    }
    $addrClean = preg_replace('/([ก-๙]+)(อ\.|จ\.|อำเภอ|จังหวัด)/u', '$1 $2', $addr);
    $addrClean = preg_replace('/\s+/', ' ', $addrClean);
    
    if (preg_match('/(?:ตำบล|ต\s*\.\s*|ต\s+)\s*([\x{0e00}-\x{0e7f}]+)/u', $addrClean, $matches)) {
        return "ต." . trim($matches[1]);
    }
    return "ไม่ระบุตำบล";
}

// Add village and subdistrict keys to each student
foreach ($students as &$std) {
    $std['village'] = getVillageGroup($std['Stu_addr']);
    $std['subdistrict'] = getSubdistrictGroup($std['Stu_addr']);
    $std['sex'] = (strpos($std['Stu_pre'], 'นาย') !== false || strpos($std['Stu_pre'], 'เด็กชาย') !== false) ? 'ชาย' : 'หญิง';
}
unset($std);

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
$studentsJson = json_encode($students);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์รายชื่อและพิกัดนักเรียน ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dist/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f3f4f6;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
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
            tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
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
            padding: 6px 8px;
            line-height: 1.3;
        }

        .control-panel {
            position: fixed;
            left: 20px;
            top: 20px;
            width: 330px;
            max-height: 95vh;
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
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="fas fa-cog text-indigo-500"></i>
            ตั้งค่าพิมพ์รายชื่อพิกัด
        </h3>
        
        <!-- Font Size & Spacing -->
        <div class="mb-4 space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ขนาดตัวอักษร: <span id="fontSizeDisplay" class="text-indigo-600 font-extrabold">12px</span></label>
                <input type="range" id="fontSizeRange" min="8" max="20" value="11" 
                       class="w-full h-2 bg-indigo-100 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ความสูงแถวตาราง: <span id="rowHeightDisplay" class="text-indigo-600 font-extrabold">32px</span></label>
                <input type="range" id="rowHeightRange" min="20" max="60" value="32" 
                       class="w-full h-2 bg-indigo-100 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
        </div>

        <!-- Grouping & Sorting -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">จัดกลุ่มการพิมพ์:</label>
            <select id="groupType" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="normal">เรียงตามเลขที่ปกติ (ไม่จัดกลุ่ม)</option>
                <option value="subdistrict">จัดกลุ่มตามโซนตำบล</option>
                <option value="village">จัดกลุ่มตามโซนหมู่บ้าน</option>
            </select>
        </div>

        <!-- Color Coding Options -->
        <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" id="colorCodeSubdistricts" checked class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer">
            <label for="colorCodeSubdistricts" class="text-xs font-bold text-slate-600 cursor-pointer">แบ่งสีตามโซนตำบลให้เห็นชัดเจน</label>
        </div>

        <!-- Columns Toggle -->
        <div class="mb-4 border-t border-slate-100 pt-3">
            <label class="block text-xs font-bold text-slate-600 mb-2">แสดงคอลัมน์หลัก:</label>
            <div class="grid grid-cols-2 gap-2 text-xs text-slate-700">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-id" class="rounded text-indigo-600 border-slate-300">
                    <span>เลขประจำตัว</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-nick" checked class="rounded text-indigo-600 border-slate-300">
                    <span>ชื่อเล่น</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-phone" checked class="rounded text-indigo-600 border-slate-300">
                    <span>เบอร์โทรนักเรียน</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-parent" checked class="rounded text-indigo-600 border-slate-300">
                    <span>เบอร์ผู้ปกครอง</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-subdistrict" checked class="rounded text-indigo-600 border-slate-300">
                    <span>ตำบล (โซน)</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-village" class="rounded text-indigo-600 border-slate-300">
                    <span>หมู่บ้าน (ที่อยู่)</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-coords" checked class="rounded text-indigo-600 border-slate-300">
                    <span>พิกัด GPS</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="col-maplink" checked class="rounded text-indigo-600 border-slate-300">
                    <span>ลิงก์แผนที่</span>
                </label>
            </div>
        </div>

        <!-- Custom Columns -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">เพิ่มช่องประเมิน/เช็คชื่อ:</label>
            <p class="text-[9px] text-slate-400 mb-1">* ป้อนหัวข้อ แยกบรรทัดละ 1 คอลัมน์</p>
            <textarea id="customHeaders" rows="2" 
                      class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-400" 
                      placeholder="เช่น&#10;สถานะเยี่ยมบ้าน"></textarea>
        </div>

        <!-- Signature Toggle -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">แสดงส่วนลงชื่อท้ายกระดาษ:</label>
            <div class="space-y-1 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="show-signature" checked class="rounded text-indigo-600 border-slate-300">
                    <span>ครูที่ปรึกษา</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="show-head-signature" class="rounded text-indigo-600 border-slate-300">
                    <span>หัวหน้าระดับ</span>
                </label>
            </div>
        </div>

        <!-- Layout Options -->
        <div class="space-y-2 border-t border-slate-100 pt-3">
            <button onclick="window.print()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-2.5 rounded-xl shadow-lg hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-print"></i> พิมพ์ / บันทึก PDF
            </button>
            <button onclick="exportExcel()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-file-excel"></i> ส่งออก Excel
            </button>
            <button onclick="window.close()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition-all text-sm">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>

    <!-- Paper Content -->
    <div class="paper print-area shadow-2xl rounded-sm text-[11px]" id="renderArea">
        <div class="text-center mb-4">
            <h1 class="text-sm md:text-base font-bold mb-1">รายชื่อและพิกัดแผนที่บ้านนักเรียน ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?></h1>
            <p class="text-xs">ครูที่ปรึกษา: <?php echo htmlspecialchars($teacherNames); ?> | โรงเรียนพิชัย ปีการศึกษา <?php echo htmlspecialchars($pee); ?></p>
        </div>

        <table id="targetTable">
            <thead>
                <tr id="tableHeader">
                    <!-- Injected by JS -->
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Injected by JS -->
            </tbody>
        </table>

        <!-- Summary Statistics -->
        <div class="mt-4 flex justify-between items-center text-xs italic">
            <p id="statsSummary">รวมทั้งหมด <?php echo count($students); ?> คน</p>
        </div>

        <!-- Signatures Area -->
        <div id="signature-section" class="mt-12 hidden" style="page-break-inside: avoid;">
            <div class="grid grid-cols-2 gap-y-12 gap-x-8 text-center text-xs">
                <div id="advisor-sig-block">
                    <?php if ($teachers): ?>
                        <?php foreach ($teachers as $t): ?>
                        <div class="mb-4">
                            <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>
                            <p class="mt-1">( <?php echo htmlspecialchars($t['Teach_name']); ?> )</p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>
                            <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>
                            <p class="mt-1">( <?php echo htmlspecialchars($userData['Teach_name'] ?? ''); ?> )</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="head-signature-block" class="hidden">
                    <p>ลงชื่อ..........................................................หัวหน้าระดับ</p>
                    <p class="mt-1">(..........................................................)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const students = <?php echo $studentsJson; ?>;

        const pastelColors = [
            '#f0fdf4', // Soft green (emerald-50)
            '#eff6ff', // Soft blue (blue-50)
            '#faf5ff', // Soft purple (purple-50)
            '#fff7ed', // Soft orange (orange-50)
            '#f5f3ff', // Soft violet (violet-50)
            '#fff1f2', // Soft rose (rose-50)
            '#ecfdf5', // Soft mint (green-50)
            '#f0fdfa', // Soft teal (teal-50)
            '#fffbeb', // Soft amber (amber-50)
            '#fdf2f8', // Soft pink (pink-50)
        ];

        // Controls Map
        const controls = {
            fontSizeRange: document.getElementById('fontSizeRange'),
            fontSizeDisplay: document.getElementById('fontSizeDisplay'),
            rowHeightRange: document.getElementById('rowHeightRange'),
            rowHeightDisplay: document.getElementById('rowHeightDisplay'),
            groupType: document.getElementById('groupType'),
            colorCodeSubdistricts: document.getElementById('colorCodeSubdistricts'),
            colId: document.getElementById('col-id'),
            colNick: document.getElementById('col-nick'),
            colPhone: document.getElementById('col-phone'),
            colParent: document.getElementById('col-parent'),
            colSubdistrict: document.getElementById('col-subdistrict'),
            colVillage: document.getElementById('col-village'),
            colCoords: document.getElementById('col-coords'),
            colMaplink: document.getElementById('col-maplink'),
            customHeaders: document.getElementById('customHeaders'),
            showSignature: document.getElementById('show-signature'),
            showHeadSignature: document.getElementById('show-head-signature')
        };

        // Assign colors to unique subdistricts
        function getSubdistrictColorMap() {
            const map = {};
            let index = 0;
            students.forEach(s => {
                const sub = s.subdistrict || 'ไม่ระบุตำบล';
                if (!map[sub]) {
                    map[sub] = pastelColors[index % pastelColors.length];
                    index++;
                }
            });
            return map;
        }

        function updateTable() {
            const fontSize = controls.fontSizeRange.value;
            const rowHeight = controls.rowHeightRange.value;
            
            controls.fontSizeDisplay.innerText = fontSize + 'px';
            controls.rowHeightDisplay.innerText = rowHeight + 'px';
            document.getElementById('renderArea').style.fontSize = fontSize + 'px';
            
            // Signature state
            document.getElementById('signature-section').classList.toggle('hidden', !controls.showSignature.checked && !controls.showHeadSignature.checked);
            document.getElementById('advisor-sig-block').classList.toggle('hidden', !controls.showSignature.checked);
            document.getElementById('head-signature-block').classList.toggle('hidden', !controls.showHeadSignature.checked);

            // Construct Headers
            let headerHtml = `<th class="w-10 text-center font-bold">เลขที่</th>`;
            if (controls.colId.checked) headerHtml += `<th class="w-24 text-center font-bold">เลขประจำตัว</th>`;
            headerHtml += `<th class="text-left font-bold min-w-[120px]">ชื่อ - นามสกุล</th>`;
            if (controls.colNick.checked) headerHtml += `<th class="w-16 text-center font-bold">ชื่อเล่น</th>`;
            if (controls.colPhone.checked) headerHtml += `<th class="w-28 text-center font-bold">เบอร์โทร</th>`;
            if (controls.colParent.checked) headerHtml += `<th class="w-28 text-center font-bold">เบอร์ผู้ปกครอง</th>`;
            if (controls.colSubdistrict.checked) headerHtml += `<th class="text-left font-bold min-w-[80px]">ตำบล (โซน)</th>`;
            if (controls.colVillage.checked) headerHtml += `<th class="text-left font-bold min-w-[120px]">หมู่บ้าน (ที่อยู่)</th>`;
            if (controls.colCoords.checked) headerHtml += `<th class="w-36 text-center font-bold">พิกัด GPS</th>`;
            if (controls.colMaplink.checked) headerHtml += `<th class="w-16 text-center font-bold">แผนที่</th>`;
            
            const extraHeaders = controls.customHeaders.value.split('\n').map(h => h.trim()).filter(h => h !== '');
            extraHeaders.forEach(h => {
                headerHtml += `<th class="text-center font-bold min-w-[50px]">${h}</th>`;
            });
            headerHtml += `<th class="w-20 text-center font-bold">หมายเหตุ</th>`;
            document.getElementById('tableHeader').innerHTML = headerHtml;

            // Group and Render body
            const groupType = controls.groupType.value;
            const subdistrictColorMap = getSubdistrictColorMap();
            let bodyHtml = '';

            let displayStudents = [...students];

            if (groupType === 'normal') {
                // Flat render
                displayStudents.forEach(s => {
                    bodyHtml += renderStudentRow(s, rowHeight, subdistrictColorMap, extraHeaders);
                });
            } else if (groupType === 'subdistrict') {
                // Group by subdistrict
                const grouped = {};
                displayStudents.forEach(s => {
                    const key = s.subdistrict || 'ไม่ระบุตำบล';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(s);
                });

                Object.keys(grouped).sort().forEach(sub => {
                    const colCount = 3 + 
                        (controls.colId.checked ? 1 : 0) + 
                        (controls.colNick.checked ? 1 : 0) + 
                        (controls.colPhone.checked ? 1 : 0) + 
                        (controls.colParent.checked ? 1 : 0) + 
                        (controls.colSubdistrict.checked ? 1 : 0) + 
                        (controls.colVillage.checked ? 1 : 0) + 
                        (controls.colCoords.checked ? 1 : 0) + 
                        (controls.colMaplink.checked ? 1 : 0) + 
                        extraHeaders.length;

                    bodyHtml += `<tr class="bg-slate-100/80 font-bold border-t border-b border-slate-300">
                        <td colspan="${colCount}" class="text-left py-2 px-3 text-indigo-700 bg-slate-100 font-extrabold text-[12px]">
                            <i class="fas fa-building-user mr-1.5"></i> ตำบล ${sub.replace('ต.', '')} (รวม ${grouped[sub].length} คน)
                        </td>
                    </tr>`;

                    grouped[sub].forEach(s => {
                        bodyHtml += renderStudentRow(s, rowHeight, subdistrictColorMap, extraHeaders);
                    });
                });
            } else if (groupType === 'village') {
                // Group by village
                const grouped = {};
                displayStudents.forEach(s => {
                    const key = s.village || 'ไม่ระบุหมู่บ้าน';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(s);
                });

                Object.keys(grouped).sort().forEach(vil => {
                    const colCount = 3 + 
                        (controls.colId.checked ? 1 : 0) + 
                        (controls.colNick.checked ? 1 : 0) + 
                        (controls.colPhone.checked ? 1 : 0) + 
                        (controls.colParent.checked ? 1 : 0) + 
                        (controls.colSubdistrict.checked ? 1 : 0) + 
                        (controls.colVillage.checked ? 1 : 0) + 
                        (controls.colCoords.checked ? 1 : 0) + 
                        (controls.colMaplink.checked ? 1 : 0) + 
                        extraHeaders.length;

                    bodyHtml += `<tr class="bg-slate-100/80 font-bold border-t border-b border-slate-300">
                        <td colspan="${colCount}" class="text-left py-2 px-3 text-emerald-700 bg-slate-100 font-extrabold text-[12px]">
                            <i class="fas fa-folder mr-1.5"></i> ${vil} (รวม ${grouped[vil].length} คน)
                        </td>
                    </tr>`;

                    grouped[vil].forEach(s => {
                        bodyHtml += renderStudentRow(s, rowHeight, subdistrictColorMap, extraHeaders);
                    });
                });
            }

            document.getElementById('tableBody').innerHTML = bodyHtml;

            // Stats summary update
            const male = students.filter(s => s.sex === 'ชาย').length;
            const female = students.length - male;
            document.getElementById('statsSummary').innerHTML = `พบพิกัดทั้งหมด ${students.length} คน (ชาย ${male} คน, หญิง ${female} คน)`;
        }

        function renderStudentRow(s, rowHeight, subColorMap, extraHeaders) {
            const isColored = controls.colorCodeSubdistricts.checked;
            const bgStyle = isColored && s.subdistrict ? `style="background-color: ${subColorMap[s.subdistrict]};"` : '';
            const nameColorClass = s.sex === 'ชาย' ? 'text-blue-700 font-semibold' : 'text-pink-700 font-semibold';
            
            let rowHtml = `<tr ${bgStyle} style="height: ${rowHeight}px;">`;
            rowHtml += `<td class="text-center font-bold">${s.Stu_no}</td>`;
            if (controls.colId.checked) rowHtml += `<td class="text-center font-mono">${s.Stu_id}</td>`;
            rowHtml += `<td class="text-left font-bold"><span class="${nameColorClass}">${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}</span></td>`;
            if (controls.colNick.checked) rowHtml += `<td class="text-center font-bold text-indigo-700">${s.Stu_nick || '-'}</td>`;
            if (controls.colPhone.checked) rowHtml += `<td class="text-center font-mono">${s.Stu_phone || '-'}</td>`;
            if (controls.colParent.checked) rowHtml += `<td class="text-center font-mono">${s.Par_phone || '-'}</td>`;
            if (controls.colSubdistrict.checked) rowHtml += `<td class="text-left font-bold text-slate-700">${s.subdistrict || '-'}</td>`;
            if (controls.colVillage.checked) rowHtml += `<td class="text-left text-slate-500 truncate text-[10px]" title="${s.Stu_addr}">${s.village || '-'}</td>`;
            if (controls.colCoords.checked) rowHtml += `<td class="text-center font-mono text-[9px]">${s.latitude}, ${s.longitude}</td>`;
            if (controls.colMaplink.checked) {
                const link = `https://www.google.com/maps/search/?api=1&query=${s.latitude},${s.longitude}`;
                rowHtml += `<td class="text-center no-print">
                    <a href="${link}" target="_blank" class="px-2 py-0.5 bg-rose-50 text-rose-600 rounded border border-rose-100 hover:bg-rose-100 transition-colors text-[9px] font-bold">
                        <i class="fas fa-map-marker-alt"></i> แผนที่
                    </a>
                </td>`;
                // Printable version of maps cell
                rowHtml += `<td class="text-center print:table-cell hidden text-[8px] font-bold text-indigo-600 font-mono">LINK</td>`;
            }

            extraHeaders.forEach(() => {
                rowHtml += `<td></td>`;
            });
            rowHtml += `<td></td>`; // remarks
            rowHtml += `</tr>`;
            return rowHtml;
        }

        // Attach listeners
        Object.values(controls).forEach(el => {
            if (el) {
                el.addEventListener('change', updateTable);
                el.addEventListener('input', updateTable);
            }
        });

        // Export Excel Functionality
        function exportExcel() {
            const data = [];
            const groupType = controls.groupType.value;
            const extraHeaders = controls.customHeaders.value.split('\n').map(h => h.trim()).filter(h => h !== '');

            // Construct Headers row
            const headers = ['เลขที่'];
            if (controls.colId.checked) headers.push('เลขประจำตัว');
            headers.push('ชื่อ-นามสกุล');
            if (controls.colNick.checked) headers.push('ชื่อเล่น');
            if (controls.colPhone.checked) headers.push('เบอร์โทร');
            if (controls.colParent.checked) headers.push('เบอร์ผู้ปกครอง');
            if (controls.colSubdistrict.checked) headers.push('ตำบล');
            if (controls.colVillage.checked) headers.push('หมู่บ้าน');
            if (controls.colCoords.checked) headers.push('พิกัด GPS');
            if (controls.colMaplink.checked) headers.push('ลิงก์แผนที่');
            extraHeaders.forEach(h => headers.push(h));
            headers.push('หมายเหตุ');
            data.push(headers);

            // Populate rows
            if (groupType === 'normal') {
                students.forEach(s => data.push(buildExcelRow(s, extraHeaders)));
            } else if (groupType === 'subdistrict') {
                const grouped = {};
                students.forEach(s => {
                    const key = s.subdistrict || 'ไม่ระบุตำบล';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(s);
                });
                Object.keys(grouped).sort().forEach(sub => {
                    data.push([`ตำบล: ${sub} (รวม ${grouped[sub].length} คน)`]);
                    grouped[sub].forEach(s => data.push(buildExcelRow(s, extraHeaders)));
                });
            } else if (groupType === 'village') {
                const grouped = {};
                students.forEach(s => {
                    const key = s.village || 'ไม่ระบุหมู่บ้าน';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(s);
                });
                Object.keys(grouped).sort().forEach(vil => {
                    data.push([`${vil} (รวม ${grouped[vil].length} คน)`]);
                    grouped[vil].forEach(s => data.push(buildExcelRow(s, extraHeaders)));
                });
            }

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, "รายชื่อและพิกัด");
            XLSX.writeFile(wb, `รายชื่อพร้อมพิกัด_ม.${<?php echo json_encode($class); ?>}_${<?php echo json_encode($room); ?>}.xlsx`);
        }

        function buildExcelRow(s, extraHeaders) {
            const row = [s.Stu_no];
            if (controls.colId.checked) row.push(s.Stu_id);
            row.push(`${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`);
            if (controls.colNick.checked) row.push(s.Stu_nick);
            if (controls.colPhone.checked) row.push(s.Stu_phone);
            if (controls.colParent.checked) row.push(s.Par_phone);
            if (controls.colSubdistrict.checked) row.push(s.subdistrict);
            if (controls.colVillage.checked) row.push(s.village);
            if (controls.colCoords.checked) row.push(`${s.latitude}, ${s.longitude}`);
            if (controls.colMaplink.checked) row.push(`https://www.google.com/maps/search/?api=1&query=${s.latitude},${s.longitude}`);
            extraHeaders.forEach(() => row.push(''));
            row.push('');
            return row;
        }

        // Initialize table
        updateTable();
    </script>
</body>
</html>
