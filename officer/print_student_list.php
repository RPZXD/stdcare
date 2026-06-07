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

if ($scope === 'room') {
    if (empty($class) || empty($room)) {
        echo "กรุณาระบุชั้นและห้องสำหรับขอบเขตรายห้อง";
        exit;
    }
    $students = $studentModel->getAll(['class' => $class, 'room' => $room]);
} elseif ($scope === 'level') {
    if (empty($class)) {
        echo "กรุณาระบุชั้นสำหรับขอบเขตระดับชั้น";
        exit;
    }
    $students = $studentModel->getAll(['class' => $class]);
} else {
    // School scope
    $students = $studentModel->getAll([]);
}

// Fetch all active advisors to map them by room
$advisors = $teacherModel->getAll(false);
$advisorMap = [];
foreach ($advisors as $t) {
    $cKey = $t['Teach_class'];
    $rKey = $t['Teach_room'];
    if ($cKey !== null && $rKey !== null) {
        $key = $cKey . "_" . $rKey;
        if (!isset($advisorMap[$key])) {
            $advisorMap[$key] = [];
        }
        $advisorMap[$key][] = $t['Teach_name'];
    }
}
$advisorMapJson = json_encode($advisorMap);

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
    <title>พิมพ์รายชื่อนักเรียน</title>
    
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
            body { background-color: white; }
            .no-print { display: none !important; }
            .print-area { 
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .paper {
                page-break-after: always;
                border: none !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 10mm 5mm !important;
                min-height: auto !important;
            }
            .paper:last-child {
                page-break-after: avoid !important;
            }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid black !important; }
        }

        .paper {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 10mm;
            margin: 15px auto;
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
            font-size: var(--th-font-size, inherit);
        }

        td, th {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            line-height: 1.2;
            word-break: break-word;
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

        <!-- Header Font Size -->
        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-600 mb-2">ขนาดตัวอักษรหัวตาราง: <span id="headerFontSizeDisplay">13px</span></label>
            <input type="range" id="headerFontSizeRange" min="8" max="24" value="13" 
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
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="col-remarks" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-sm text-slate-700 group-hover:text-violet-600 transition">หมายเหตุ</span>
                </label>
            </div>
        </div>

        <!-- Column Width Settings -->
        <div class="mb-5">
            <details class="group border border-slate-200 rounded-xl p-3 bg-slate-50 [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex items-center justify-between font-bold text-sm text-slate-700 cursor-pointer select-none">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-arrows-alt-h text-violet-500"></i>
                        ปรับความกว้างคอลัมน์ (px)
                    </span>
                    <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                </summary>
                <div class="space-y-3 mt-3 max-h-60 overflow-y-auto pr-1" id="widthControlsContainer">
                    <div id="width-no-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เลขที่</span>
                            <span id="w-no-val">40px</span>
                        </label>
                        <input type="range" id="w-no" min="20" max="150" value="40" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-id-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เลขประจำตัว</span>
                            <span id="w-id-val">80px</span>
                        </label>
                        <input type="range" id="w-id" min="40" max="250" value="80" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-name-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>ชื่อ-สกุล</span>
                            <span id="w-name-val">150px</span>
                        </label>
                        <input type="range" id="w-name" min="80" max="400" value="150" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-majorRoom-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>ชั้น/ห้อง</span>
                            <span id="w-majorRoom-val">80px</span>
                        </label>
                        <input type="range" id="w-major-room" min="40" max="200" value="80" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-nick-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>ชื่อเล่น</span>
                            <span id="w-nick-val">60px</span>
                        </label>
                        <input type="range" id="w-nick" min="30" max="150" value="60" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-sex-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เพศ</span>
                            <span id="w-sex-val">48px</span>
                        </label>
                        <input type="range" id="w-sex" min="25" max="120" value="48" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-phone-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เบอร์โทร</span>
                            <span id="w-phone-val">110px</span>
                        </label>
                        <input type="range" id="w-phone" min="60" max="250" value="110" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-citizen-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เลขบัตรฯ</span>
                            <span id="w-citizen-val">120px</span>
                        </label>
                        <input type="range" id="w-citizen" min="80" max="300" value="120" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-birth-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>วันเกิด</span>
                            <span id="w-birth-val">90px</span>
                        </label>
                        <input type="range" id="w-birth" min="50" max="250" value="90" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-blood-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เลือด</span>
                            <span id="w-blood-val">40px</span>
                        </label>
                        <input type="range" id="w-blood" min="25" max="100" value="40" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-parentPhone-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>เบอร์ผู้ปกครอง</span>
                            <span id="w-parentPhone-val">110px</span>
                        </label>
                        <input type="range" id="w-parentPhone" min="60" max="250" value="110" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-addr-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>ที่อยู่</span>
                            <span id="w-addr-val">180px</span>
                        </label>
                        <input type="range" id="w-addr" min="80" max="400" value="180" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-custom-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>คอลัมน์กำหนดเอง</span>
                            <span id="w-custom-val">80px</span>
                        </label>
                        <input type="range" id="w-custom" min="40" max="250" value="80" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                    <div id="width-remarks-ctrl">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1 flex justify-between">
                            <span>หมายเหตุ</span>
                            <span id="w-remarks-val">80px</span>
                        </label>
                        <input type="range" id="w-remarks" min="40" max="250" value="80" class="w-full h-1 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
                    </div>
                </div>
            </details>
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

    <!-- Papers Container -->
    <div id="papersContainer" class="print-area dynamic-font">
        <!-- Injected by JS -->
    </div>

    <script>
        const students = <?php echo $studentsJson; ?>;
        const scope = "<?php echo $scope; ?>";
        const year = "<?php echo $year; ?>";
        const advisorMap = <?php echo $advisorMapJson; ?>;

        // Control Elements
        const controls = {
            fontSizeRange: document.getElementById('fontSizeRange'),
            fontSizeDisplay: document.getElementById('fontSizeDisplay'),
            headerFontSizeRange: document.getElementById('headerFontSizeRange'),
            headerFontSizeDisplay: document.getElementById('headerFontSizeDisplay'),
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
            colRemarks: document.getElementById('col-remarks'),
            showSignature: document.getElementById('show-signature'),
            showHeadSignature: document.getElementById('show-head-signature'),
            papersContainer: document.getElementById('papersContainer'),
            
            // Widths
            wNo: document.getElementById('w-no'),
            wNoVal: document.getElementById('w-no-val'),
            wId: document.getElementById('w-id'),
            wIdVal: document.getElementById('w-id-val'),
            wName: document.getElementById('w-name'),
            wNameVal: document.getElementById('w-name-val'),
            wMajorRoom: document.getElementById('w-major-room'),
            wMajorRoomVal: document.getElementById('w-majorRoom-val'),
            wNick: document.getElementById('w-nick'),
            wNickVal: document.getElementById('w-nick-val'),
            wSex: document.getElementById('w-sex'),
            wSexVal: document.getElementById('w-sex-val'),
            wPhone: document.getElementById('w-phone'),
            wPhoneVal: document.getElementById('w-phone-val'),
            wCitizen: document.getElementById('w-citizen'),
            wCitizenVal: document.getElementById('w-citizen-val'),
            wBirth: document.getElementById('w-birth'),
            wBirthVal: document.getElementById('w-birth-val'),
            wBlood: document.getElementById('w-blood'),
            wBloodVal: document.getElementById('w-blood-val'),
            wParentPhone: document.getElementById('w-parentPhone'),
            wParentPhoneVal: document.getElementById('w-parentPhone-val'),
            wAddr: document.getElementById('w-addr'),
            wAddrVal: document.getElementById('w-addr-val'),
            wCustom: document.getElementById('w-custom'),
            wCustomVal: document.getElementById('w-custom-val'),
            wRemarks: document.getElementById('w-remarks'),
            wRemarksVal: document.getElementById('w-remarks-val'),
        };

        // JS getPlanName function equivalent
        function getPlanName(level, room) {
            level = parseInt(level);
            room = parseInt(room);
            if (level === 1) {
                if (room === 1) return 'Enrichment Science Classroom (ESC)';
                if (room === 2) return 'Enrichment Math Classroom (EMC)';
                if (room === 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
                if (room === 4) return 'วิทยาศาสตร์พลังสิบ';
                if (room === 5) return 'ภาษาอังกฤษ';
                if (room === 6) return 'ภาษาจีน';
                if (room === 7) return 'ภาษาไทย';
                if (room === 8) return 'สังคมศึกษา';
                if (room === 9) return 'อุตสาหกรรม - พาณิชยกรรม';
                if (room === 10) return 'เกษตรกรรม - คหกรรม';
                if (room === 11) return 'ศิลปะ - ดนตรี';
                if (room === 12) return 'กีฬา';
                return '';
            }
            if (level >= 2 && level <= 3) {
                if (room === 1) return 'Enrichment Science Classroom (ESC)';
                if (room === 2) return 'Enrichment Math Classroom (EMC)';
                if (room === 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
                if (room === 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
                if (room === 5) return 'ภาษาอังกฤษ';
                if (room === 6) return 'ภาษาจีน';
                if (room === 7) return 'ภาษาไทย';
                if (room === 8) return 'สังคมศึกษา';
                if (room === 9) return 'อุตสาหกรรม - พาณิชยกรรม';
                if (room === 10) return 'เกษตรกรรม - คหกรรม';
                if (room === 11) return 'ศิลปะ - ดนตรี';
                if (room === 12) return 'กีฬา';
                return '';
            }
            if (level === 4) {
                if (room === 1) return 'Enrichment Science Classroom (ESC)';
                if (room === 2) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
                if (room === 3) return 'วิทยาศาสตร์พลังสิบ';
                if (room === 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
                if (room === 5) return 'สังคมศาสตร์และภาษาไทย';
                if (room === 6) return 'ภาษาศาสตร์';
                if (room === 7) return 'บริหารอุตสาหกรรม';
                return '';
            }
            if (level >= 5 && level <= 6) {
                if (room === 1) return 'Enrichment Science Classroom (ESC)';
                if (room === 2) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
                if (room === 3) return 'วิทยาศาสตร์ คณิตศาสตร์ และเทคโนโลยี (Coding)';
                if (room === 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
                if (room === 5) return 'แผนการเรียนศิลปศาสตร์ - สังคมศาสตร์';
                if (room === 6) return 'ภาษาศาสตร์';
                if (room === 7) return 'บริหารอุตสาหกรรม';
                return '';
            }
            return '';
        }

        function updateTable() {
            const fSize = controls.fontSizeRange.value;
            controls.fontSizeDisplay.innerText = fSize + 'px';
            controls.papersContainer.style.fontSize = fSize + 'px';
            
            // Header font size
            const hFontSize = controls.headerFontSizeRange.value;
            controls.headerFontSizeDisplay.innerText = hFontSize + 'px';
            controls.papersContainer.style.setProperty('--th-font-size', hFontSize + 'px');
            
            // Show/hide width controls depending on active columns
            document.getElementById('width-id-ctrl').style.display = controls.colId.checked ? 'block' : 'none';
            if (controls.colMajorRoom) {
                document.getElementById('width-majorRoom-ctrl').style.display = controls.colMajorRoom.checked ? 'block' : 'none';
            }
            document.getElementById('width-nick-ctrl').style.display = controls.colNick.checked ? 'block' : 'none';
            document.getElementById('width-sex-ctrl').style.display = controls.colSex.checked ? 'block' : 'none';
            document.getElementById('width-phone-ctrl').style.display = controls.colPhone.checked ? 'block' : 'none';
            document.getElementById('width-citizen-ctrl').style.display = controls.colCitizen.checked ? 'block' : 'none';
            document.getElementById('width-birth-ctrl').style.display = controls.colBirth.checked ? 'block' : 'none';
            document.getElementById('width-blood-ctrl').style.display = controls.colBlood.checked ? 'block' : 'none';
            document.getElementById('width-parentPhone-ctrl').style.display = controls.colParentPhone.checked ? 'block' : 'none';
            document.getElementById('width-addr-ctrl').style.display = controls.colAddr.checked ? 'block' : 'none';
            document.getElementById('width-remarks-ctrl').style.display = controls.colRemarks.checked ? 'block' : 'none';

            const extraHeaders = controls.customHeaders.value.split('\n').map(h => h.trim()).filter(h => h !== '');
            document.getElementById('width-custom-ctrl').style.display = extraHeaders.length > 0 ? 'block' : 'none';

            // Get widths
            const widths = {
                no: controls.wNo.value,
                id: controls.wId.value,
                name: controls.wName.value,
                majorRoom: controls.wMajorRoom ? controls.wMajorRoom.value : 80,
                nick: controls.wNick.value,
                sex: controls.wSex.value,
                phone: controls.wPhone.value,
                citizen: controls.wCitizen.value,
                birth: controls.wBirth.value,
                blood: controls.wBlood.value,
                parentPhone: controls.wParentPhone.value,
                addr: controls.wAddr.value,
                custom: controls.wCustom.value,
                remarks: controls.wRemarks.value
            };

            // Update displays
            controls.wNoVal.innerText = widths.no + 'px';
            controls.wIdVal.innerText = widths.id + 'px';
            controls.wNameVal.innerText = widths.name + 'px';
            if (controls.wMajorRoomVal) controls.wMajorRoomVal.innerText = widths.majorRoom + 'px';
            controls.wNickVal.innerText = widths.nick + 'px';
            controls.wSexVal.innerText = widths.sex + 'px';
            controls.wPhoneVal.innerText = widths.phone + 'px';
            controls.wCitizenVal.innerText = widths.citizen + 'px';
            controls.wBirthVal.innerText = widths.birth + 'px';
            controls.wBloodVal.innerText = widths.blood + 'px';
            controls.wParentPhoneVal.innerText = widths.parentPhone + 'px';
            controls.wAddrVal.innerText = widths.addr + 'px';
            controls.wCustomVal.innerText = widths.custom + 'px';
            controls.wRemarksVal.innerText = widths.remarks + 'px';

            // Group students by major and room
            const groupedStudents = {};
            students.forEach(s => {
                const key = `${s.major}_${s.room}`;
                if (!groupedStudents[key]) {
                    groupedStudents[key] = [];
                }
                groupedStudents[key].push(s);
            });

            // Sort rooms logically
            const sortedKeys = Object.keys(groupedStudents).sort((a, b) => {
                const [aMajor, aRoom] = a.split('_').map(Number);
                const [bMajor, bRoom] = b.split('_').map(Number);
                if (aMajor !== bMajor) return aMajor - bMajor;
                return aRoom - bRoom;
            });

            let containerHtml = '';

            sortedKeys.forEach(key => {
                const [major, room] = key.split('_');
                const roomStudents = groupedStudents[key];
                
                // Get advisers
                const advisorsList = advisorMap[key] || [];
                let advText = '';
                if (advisorsList.length > 0) {
                    advText = "ครูที่ปรึกษา: " + advisorsList.map((name, i) => `${i + 1}. ${name}`).join("  ");
                } else {
                    advText = "ครูที่ปรึกษา: (ไม่พบข้อมูลครูที่ปรึกษา)";
                }
                
                // Plan
                const planName = getPlanName(major, room);
                if (planName) {
                    advText += "  |  แผนการเรียน: " + planName;
                }

                // Table Headers
                let headerHtml = `<th style="width: ${widths.no}px; min-width: ${widths.no}px; max-width: ${widths.no}px;" class="text-center font-bold">เลขที่</th>`;
                if (controls.colId.checked) {
                    headerHtml += `<th style="width: ${widths.id}px; min-width: ${widths.id}px; max-width: ${widths.id}px;" class="text-center font-bold">เลขประจำตัว</th>`;
                }
                headerHtml += `<th style="width: ${widths.name}px; min-width: ${widths.name}px; max-width: ${widths.name}px;" class="text-center font-bold">ชื่อ-สกุล</th>`;
                if (controls.colMajorRoom && controls.colMajorRoom.checked) {
                    headerHtml += `<th style="width: ${widths.majorRoom}px; min-width: ${widths.majorRoom}px; max-width: ${widths.majorRoom}px;" class="text-center font-bold">ชั้น/ห้อง</th>`;
                }
                if (controls.colNick.checked) {
                    headerHtml += `<th style="width: ${widths.nick}px; min-width: ${widths.nick}px; max-width: ${widths.nick}px;" class="text-center font-bold">ชื่อเล่น</th>`;
                }
                if (controls.colSex.checked) {
                    headerHtml += `<th style="width: ${widths.sex}px; min-width: ${widths.sex}px; max-width: ${widths.sex}px;" class="text-center font-bold">เพศ</th>`;
                }
                if (controls.colPhone.checked) {
                    headerHtml += `<th style="width: ${widths.phone}px; min-width: ${widths.phone}px; max-width: ${widths.phone}px;" class="text-center font-bold">เบอร์โทร</th>`;
                }
                if (controls.colCitizen.checked) {
                    headerHtml += `<th style="width: ${widths.citizen}px; min-width: ${widths.citizen}px; max-width: ${widths.citizen}px;" class="text-center font-bold">เลขบัตรฯ</th>`;
                }
                if (controls.colBirth.checked) {
                    headerHtml += `<th style="width: ${widths.birth}px; min-width: ${widths.birth}px; max-width: ${widths.birth}px;" class="text-center font-bold">วันเกิด</th>`;
                }
                if (controls.colBlood.checked) {
                    headerHtml += `<th style="width: ${widths.blood}px; min-width: ${widths.blood}px; max-width: ${widths.blood}px;" class="text-center font-bold">เลือด</th>`;
                }
                if (controls.colParentPhone.checked) {
                    headerHtml += `<th style="width: ${widths.parentPhone}px; min-width: ${widths.parentPhone}px; max-width: ${widths.parentPhone}px;" class="text-center font-bold">เบอร์ผู้ปกครอง</th>`;
                }
                if (controls.colAddr.checked) {
                    headerHtml += `<th style="width: ${widths.addr}px; min-width: ${widths.addr}px; max-width: ${widths.addr}px;" class="text-left font-bold">ที่อยู่</th>`;
                }
                
                extraHeaders.forEach(h => {
                    headerHtml += `<th style="width: ${widths.custom}px; min-width: ${widths.custom}px; max-width: ${widths.custom}px;" class="text-center border font-bold">${h}</th>`;
                });
                if (controls.colRemarks.checked) {
                    headerHtml += `<th style="width: ${widths.remarks}px; min-width: ${widths.remarks}px; max-width: ${widths.remarks}px;" class="text-center border font-bold">หมายเหตุ</th>`;
                }

                // Table Rows
                let bodyHtml = '';
                roomStudents.forEach(s => {
                    const nameColorClass = s.sex === 'ชาย' ? 'text-blue-700' : 'text-pink-700';
                    bodyHtml += `<tr>`;
                    bodyHtml += `<td style="width: ${widths.no}px; min-width: ${widths.no}px; max-width: ${widths.no}px;" class="text-center font-bold">${s.no}</td>`;
                    if (controls.colId.checked) {
                        bodyHtml += `<td style="width: ${widths.id}px; min-width: ${widths.id}px; max-width: ${widths.id}px;" class="text-center font-mono">${s.id}</td>`;
                    }
                    bodyHtml += `<td style="width: ${widths.name}px; min-width: ${widths.name}px; max-width: ${widths.name}px;" class="text-left"><span class="${nameColorClass}">${s.prefix}${s.name} ${s.sur}</span></td>`;
                    if (controls.colMajorRoom && controls.colMajorRoom.checked) {
                        bodyHtml += `<td style="width: ${widths.majorRoom}px; min-width: ${widths.majorRoom}px; max-width: ${widths.majorRoom}px;" class="text-center">ม.${s.major}/${s.room}</td>`;
                    }
                    if (controls.colNick.checked) {
                        bodyHtml += `<td style="width: ${widths.nick}px; min-width: ${widths.nick}px; max-width: ${widths.nick}px;" class="text-center">${s.nick}</td>`;
                    }
                    if (controls.colSex.checked) {
                        bodyHtml += `<td style="width: ${widths.sex}px; min-width: ${widths.sex}px; max-width: ${widths.sex}px;" class="text-center">${s.sex}</td>`;
                    }
                    if (controls.colPhone.checked) {
                        bodyHtml += `<td style="width: ${widths.phone}px; min-width: ${widths.phone}px; max-width: ${widths.phone}px;" class="text-center">${s.phone}</td>`;
                    }
                    if (controls.colCitizen.checked) {
                        bodyHtml += `<td style="width: ${widths.citizen}px; min-width: ${widths.citizen}px; max-width: ${widths.citizen}px;" class="text-center font-mono text-[10px]">${s.citizen_id}</td>`;
                    }
                    if (controls.colBirth.checked) {
                        bodyHtml += `<td style="width: ${widths.birth}px; min-width: ${widths.birth}px; max-width: ${widths.birth}px;" class="text-center">${s.birth}</td>`;
                    }
                    if (controls.colBlood.checked) {
                        bodyHtml += `<td style="width: ${widths.blood}px; min-width: ${widths.blood}px; max-width: ${widths.blood}px;" class="text-center">${s.blood}</td>`;
                    }
                    if (controls.colParentPhone.checked) {
                        bodyHtml += `<td style="width: ${widths.parentPhone}px; min-width: ${widths.parentPhone}px; max-width: ${widths.parentPhone}px;" class="text-center">${s.parent_phone}</td>`;
                    }
                    if (controls.colAddr.checked) {
                        bodyHtml += `<td style="width: ${widths.addr}px; min-width: ${widths.addr}px; max-width: ${widths.addr}px;" class="text-left text-[10px]">${s.addr}</td>`;
                    }
                    
                    extraHeaders.forEach(() => {
                        bodyHtml += `<td style="width: ${widths.custom}px; min-width: ${widths.custom}px; max-width: ${widths.custom}px;" class="border"></td>`;
                    });
                    if (controls.colRemarks.checked) {
                        bodyHtml += `<td style="width: ${widths.remarks}px; min-width: ${widths.remarks}px; max-width: ${widths.remarks}px;" class="border"></td>`;
                    }
                    bodyHtml += `</tr>`;
                });

                const male = roomStudents.filter(s => s.sex === 'ชาย').length;
                const female = roomStudents.length - male;
                const statsHtml = `รวมทั้งสิ้น ${roomStudents.length} คน (ชาย ${male} คน, หญิง ${female} คน)`;

                const showSig = controls.showSignature.checked ? '' : 'hidden';
                const showHeadSig = controls.showHeadSignature.checked ? '' : 'hidden';

                containerHtml += `
                    <div class="paper shadow-2xl rounded-sm">
                        <div class="text-center mb-3">
                            <h1 class="text-base font-bold mb-1">รายชื่อนักเรียนชั้นมัธยมศึกษาปีที่ ${major}/${room} โรงเรียนพิชัย ปีการศึกษา ${year}</h1>
                            <p class="text-xs text-slate-600 mb-2">${advText}</p>
                        </div>

                        <table>
                            <thead>
                                <tr>${headerHtml}</tr>
                            </thead>
                            <tbody>
                                ${bodyHtml}
                            </tbody>
                        </table>

                        <!-- Summary & Stats -->
                        <div class="mt-4 flex justify-between text-sm italic">
                            <p>${statsHtml}</p>
                        </div>

                        <!-- Dynamic Signature Area -->
                        <div class="mt-12 ${showSig}" style="page-break-inside: avoid;">
                            <div class="grid grid-cols-2 gap-y-12 gap-x-8 text-center text-sm">
                                <div>
                                    <p>ลงชื่อ..........................................................ผู้จัดทำ/ตรวจ</p>
                                    <p class="mt-1">(..........................................................)</p>
                                </div>
                                
                                <div class="${showHeadSig}">
                                    <p>ลงชื่อ..........................................................หัวหน้างานทะเบียน</p>
                                    <p class="mt-1">(..........................................................)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            controls.papersContainer.innerHTML = containerHtml;
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

            const headers = ['ลำดับ', 'เลขที่'];
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
            if (controls.colRemarks.checked) headers.push('หมายเหตุ');
            
            data.push(headers);

            students.forEach(s => {
                const row = [s.index, s.no];
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
                if (controls.colRemarks.checked) row.push('');
                data.push(row);
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Calculate column widths in Excel (approximate character widths)
            const colWidths = [
                { wch: 6 }, // ลำดับ
                { wch: 6 }, // เลขที่
            ];
            if (controls.colId.checked) colWidths.push({ wch: 15 });
            colWidths.push({ wch: 25 }); // ชื่อ-สกุล
            if (controls.colMajorRoom && controls.colMajorRoom.checked) colWidths.push({ wch: 10 });
            if (controls.colNick.checked) colWidths.push({ wch: 10 });
            if (controls.colSex.checked) colWidths.push({ wch: 6 });
            if (controls.colPhone.checked) colWidths.push({ wch: 15 });
            if (controls.colCitizen.checked) colWidths.push({ wch: 20 });
            if (controls.colBirth.checked) colWidths.push({ wch: 12 });
            if (controls.colBlood.checked) colWidths.push({ wch: 8 });
            if (controls.colParentPhone.checked) colWidths.push({ wch: 15 });
            if (controls.colAddr.checked) colWidths.push({ wch: 35 });
            extraHeaders.forEach(() => colWidths.push({ wch: 12 }));
            if (controls.colRemarks.checked) colWidths.push({ wch: 15 });
            ws['!cols'] = colWidths;

            XLSX.utils.book_append_sheet(wb, ws, "รายชื่อนักเรียน");
            
            let filename = `รายชื่อนักเรียน_ทั้งหมด`;
            if (scope === 'room') filename = `รายชื่อนักเรียน_ม${students[0].major}_${students[0].room}`;
            else if (scope === 'level') filename = `รายชื่อนักเรียน_ระดับชั้น_ม${students[0].major}`;
            
            XLSX.writeFile(wb, `${filename}.xlsx`);
        }

        updateTable();
    </script>
</body>
</html>
