<?php
/**
 * StdCare - Public Student List Print Page
 * Features:
 * - Publicly accessible (no session required)
 * - Restricted data visibility (Roll No, Student ID, Full Name only)
 * - Dynamic dependent dropdowns for Class & Room based on database records
 * - Real-time custom control panel (Font size, Row height, Column count, Margin, Landscape/Portrait)
 * - Editable School Year, Learning Plan, and Advisors (saved to localStorage per room)
 * - Perfect printing layout using custom @media print rules
 * - SheetJS Excel export functionality
 */

require_once __DIR__ . '/classes/DatabaseUsers.php';
require_once __DIR__ . '/class/UserLogin.php';

use App\DatabaseUsers;

try {
    $database = new DatabaseUsers();
    $db = $database->getPDO();
    $userLogin = new UserLogin($db);
} catch (\Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// 1. AJAX Action to retrieve students and advisors
if (isset($_GET['action']) && $_GET['action'] === 'get_students') {
    header('Content-Type: application/json');
    $class = isset($_GET['class']) ? trim($_GET['class']) : '';
    $room = isset($_GET['room']) ? trim($_GET['room']) : '';
    
    if (empty($class) || empty($room)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาระบุระดับชั้นและห้องเรียน']);
        exit;
    }
    
    try {
        // Fetch students (Restricted to: Stu_no, Stu_id, prefix + name + sur)
        $sql = "SELECT Stu_no, Stu_id, Stu_pre, Stu_name, Stu_sur 
                FROM student 
                WHERE Stu_major = :class 
                  AND Stu_room = :room 
                  AND Stu_status = '1'
                ORDER BY CAST(Stu_no AS UNSIGNED) ASC, Stu_id ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['class' => $class, 'room' => $room]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch advisors
        $advisorSql = "SELECT Teach_name 
                       FROM teacher 
                       WHERE Teach_class = :class 
                         AND Teach_room = :room 
                         AND Teach_status = '1'
                       ORDER BY Teach_name ASC";
        $advisorStmt = $db->prepare($advisorSql);
        $advisorStmt->execute(['class' => $class, 'room' => $room]);
        $advisors = $advisorStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Fetch Default School Year
        $pee = $userLogin->getPee() ?: (date('Y') + 543);
        
        echo json_encode([
            'success' => true,
            'students' => $students,
            'advisors' => $advisors,
            'year' => $pee
        ]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()]);
    }
    exit;
}

// 2. Fetch active Class Levels and Rooms to populate filters dynamically
try {
    $filtersSql = "SELECT DISTINCT Stu_major, Stu_room 
                   FROM student 
                   WHERE Stu_status = '1' 
                     AND Stu_major IS NOT NULL AND Stu_major != ''
                     AND Stu_room IS NOT NULL AND Stu_room != ''
                   ORDER BY CAST(Stu_major AS UNSIGNED) ASC, CAST(Stu_room AS UNSIGNED) ASC";
    $filtersStmt = $db->query($filtersSql);
    $activeGroups = $filtersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $classesData = [];
    foreach ($activeGroups as $group) {
        $c = $group['Stu_major'];
        $r = $group['Stu_room'];
        if (!isset($classesData[$c])) {
            $classesData[$c] = [];
        }
        $classesData[$c][] = $r;
    }
    
    // Default fallback if no data in database
    if (empty($classesData)) {
        $classesData = [
            "1" => ["1", "2", "3", "4", "5"],
            "2" => ["1", "2", "3", "4", "5"],
            "3" => ["1", "2", "3", "4", "5"],
            "4" => ["1", "2", "3", "4", "5"],
            "5" => ["1", "2", "3", "4", "5"],
            "6" => ["1", "2", "3", "4", "5"]
        ];
    }
    
    $defaultPee = $userLogin->getPee() ?: (date('Y') + 543);
} catch (\Exception $e) {
    $classesData = [
        "1" => ["1", "2", "3", "4", "5"],
        "2" => ["1", "2", "3", "4", "5"],
        "3" => ["1", "2", "3", "4", "5"],
        "4" => ["1", "2", "3", "4", "5"],
        "5" => ["1", "2", "3", "4", "5"],
        "6" => ["1", "2", "3", "4", "5"]
    ];
    $defaultPee = date('Y') + 543;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์รายชื่อนักเรียนแบบตารางตรวจสอบ - โรงเรียนพิชัย</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- xlsx-js-style for Beautiful styled Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>

    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --accent: #d946ef;
            --sidebar-bg: #0f172a;
            --sidebar-card: #1e293b;
            --body-bg: #f8fafc;
            --border-color: #cbd5e1;
            --text-primary: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--body-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styling */
        .sidebar {
            width: 340px;
            background-color: var(--sidebar-bg);
            color: #f8fafc;
            padding: 24px;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: #475569;
            border-radius: 4px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #334155;
            padding-bottom: 16px;
            margin-bottom: 6px;
        }

        .sidebar-header i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .sidebar-header h2 {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
            font-family: 'Inter', 'Sarabun', sans-serif;
            letter-spacing: -0.025em;
        }

        .card {
            background-color: var(--sidebar-card);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #334155;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .card-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: 0.05em;
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background-color: #0f172a;
            border: 1px solid #475569;
            border-radius: 8px;
            color: #f8fafc;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        /* Ranges */
        .range-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .range-container input[type="range"] {
            flex: 1;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .range-val {
            font-size: 0.85rem;
            font-weight: 700;
            color: #94a3b8;
            min-width: 42px;
            text-align: right;
        }

        /* Checkbox label */
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        /* Buttons styling */
        .btn-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-print {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-print:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-excel {
            background-color: #10b981;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-excel:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        .btn-home {
            background-color: #475569;
            color: white;
        }

        .btn-home:hover {
            background-color: #334155;
        }

        /* Main Preview Container */
        .main-container {
            flex: 1;
            margin-left: 340px;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-x: auto;
        }

        /* Paper Renders */
        .paper {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05), 0 5px 15px rgba(0, 0, 0, 0.02);
            padding: 10mm;
            display: flex;
            flex-direction: column;
            position: relative;
            box-sizing: border-box;
            transition: width 0.3s, min-height 0.3s;
        }

        /* Landscape modification */
        .paper.landscape {
            width: 297mm;
            min-height: 210mm;
        }

        /* Headings & Document Header */
        .doc-header {
            text-align: center;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .doc-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #000000;
        }

        .doc-subrow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            margin-top: 4px;
            padding: 0 4px;
            color: #000000;
        }

        .doc-advisors {
            text-align: left;
            font-size: 0.9rem;
            margin-top: 3px;
            padding-left: 4px;
            color: #000000;
        }

        /* Table design */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            color: #000000;
        }

        .print-table th, .print-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
            font-size: 12px;
            text-align: center;
            vertical-align: middle;
        }

        .print-table th {
            font-weight: 700;
            background-color: #f8fafc; /* Subtle header color for screen only */
        }

        .print-table td.text-left {
            text-align: left;
        }

        /* Dynamic classes loaded by script */
        .th-no { width: 8%; }
        .th-id { width: 14%; }
        .th-name { width: 35%; }
        .th-blank { width: auto; }

        /* Signatures block */
        .doc-signatures {
            margin-top: auto;
            padding-top: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            page-break-inside: avoid;
        }

        .sig-block {
            text-align: center;
            font-size: 0.85rem;
            line-height: 1.8;
            color: #000000;
        }

        /* Empty/Info screens */
        .no-data {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            text-align: center;
            color: var(--text-muted);
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            margin: auto;
            max-width: 500px;
        }

        .no-data i {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .no-data h3 {
            font-size: 1.2rem;
            margin: 0 0 8px 0;
            color: var(--text-primary);
        }

        /* Print Media Setup */
        @media print {
            body {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .sidebar {
                display: none !important;
            }

            .main-container {
                margin-left: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                width: 100% !important;
            }

            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
                background: white !important;
            }

            .print-table th {
                background-color: transparent !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    
    <!-- Dynamic custom print orientation style sheet -->
    <style id="printOrientationStyle"></style>
</head>
<body>

    <!-- CONTROL PANEL SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-print"></i>
            <h2>ระบบพิมพ์รายชื่อ</h2>
        </div>

        <!-- 1. Selection Card -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-filter"></i> ตัวกรองข้อมูล
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="filterClass">ระดับชั้น</label>
                    <select id="filterClass" class="form-control">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filterRoom">ห้องเรียน</label>
                    <select id="filterRoom" class="form-control" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Configuration Card -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-sliders-h"></i> ตั้งค่าหน้ากระดาษ
            </div>

            <!-- Page Title Custom -->
            <div class="form-group">
                <label for="customTitle">หัวข้อกระดาษ</label>
                <input type="text" id="customTitle" class="form-control" value="รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่">
            </div>

            <!-- Learning Plan Custom -->
            <div class="form-group">
                <label for="learningPlan">แผนการเรียน / ห้องเรียน</label>
                <input type="text" id="learningPlan" class="form-control" placeholder="เช่น วิทย์ - คณิต, ESC, ทั่วไป">
            </div>

            <!-- Advisors Edit -->
            <div class="form-group">
                <label for="advisorNames">ครูที่ปรึกษา</label>
                <input type="text" id="advisorNames" class="form-control" placeholder="ครูที่ปรึกษา 1, ครูที่ปรึกษา 2">
            </div>

            <!-- Academic Year -->
            <div class="form-group">
                <label for="schoolYear">ปีการศึกษา</label>
                <input type="text" id="schoolYear" class="form-control" value="<?php echo htmlspecialchars($defaultPee); ?>">
            </div>

            <!-- Orientation -->
            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="pageOrientation">แนวตั้ง / แนวนอน</label>
                    <select id="pageOrientation" class="form-control">
                        <option value="portrait">แนวตั้ง (A4 Portrait)</option>
                        <option value="landscape">แนวนอน (A4 Landscape)</option>
                    </select>
                </div>
            </div>

            <!-- Column count -->
            <div class="form-group">
                <label>จำนวนช่องว่างเช็คชื่อ</label>
                <div class="range-container">
                    <input type="range" id="blankColsRange" min="1" max="20" value="12">
                    <span class="range-val" id="blankColsVal">12 ช่อง</span>
                </div>
            </div>

            <!-- Font size -->
            <div class="form-group">
                <label>ขนาดตัวอักษร</label>
                <div class="range-container">
                    <input type="range" id="fontSizeRange" min="10" max="22" value="10">
                    <span class="range-val" id="fontSizeVal">10px</span>
                </div>
            </div>

            <!-- Row Height -->
            <div class="form-group">
                <label>ความสูงแถวตาราง</label>
                <div class="range-container">
                    <input type="range" id="rowHeightRange" min="20" max="55" value="20">
                    <span class="range-val" id="rowHeightVal">20px</span>
                </div>
            </div>

            <!-- Margins -->
            <div class="form-group">
                <label for="pageMargin">ระยะขอบกระดาษ</label>
                <select id="pageMargin" class="form-control">
                    <option value="5mm">แคบ (5 มม.)</option>
                    <option value="8mm" selected>มาตรฐาน (8 มม.)</option>
                    <option value="10mm">กว้าง (10 มม.)</option>
                    <option value="15mm">กว้างพิเศษ (15 มม.)</option>
                </select>
            </div>
        </div>

        <!-- 3. Layout Options -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-th-list"></i> ตัวเลือกการแสดงผล
            </div>
            
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="showAdvisorsHeader" checked>
                    <span>แสดงรายชื่อครูในหัวข้อด้านบน</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" id="showAdvisorsFooter">
                    <span>แสดงส่วนลงชื่อครูที่ปรึกษา</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" id="showSupervisorFooter">
                    <span>แสดงส่วนลงชื่อหัวหน้าระดับ</span>
                </label>
            </div>
        </div>

        <!-- Action Buttons Stack -->
        <div class="btn-stack">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> พิมพ์รายงาน / PDF
            </button>
            <button onclick="exportToExcel()" class="btn btn-excel">
                <i class="fas fa-file-excel"></i> ส่งออก Excel
            </button>
            <a href="index.php" class="btn btn-home">
                <i class="fas fa-arrow-left"></i> กลับไปหน้าหลัก
            </a>
        </div>
    </div>

    <!-- MAIN PAPERS CONTAINER -->
    <div class="main-container">
        <!-- Render paper simulation -->
        <div class="paper" id="paperArea">
            <div id="paperContent">
                <!-- Fallback empty display before selecting a room -->
                <div class="no-data" id="emptyDisplay">
                    <i class="fas fa-info-circle"></i>
                    <h3>กรุณาเลือก ระดับชั้น และ ห้องเรียน</h3>
                    <p>ระบบจะดึงรายชื่อและจัดหน้ากระดาษแบบตารางเช็คชื่อให้อย่างเหมาะสมแบบพร้อมพิมพ์ทันที</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENT SCRIPT LOGIC -->
    <script>
        // Database dynamic classes and rooms data loaded from PHP
        const classesData = <?php echo json_encode($classesData); ?>;
        let loadedStudents = [];
        let loadedAdvisors = [];

        // Elements
        const filterClass = document.getElementById('filterClass');
        const filterRoom = document.getElementById('filterRoom');
        const customTitle = document.getElementById('customTitle');
        const learningPlan = document.getElementById('learningPlan');
        const advisorNames = document.getElementById('advisorNames');
        const schoolYear = document.getElementById('schoolYear');
        const pageOrientation = document.getElementById('pageOrientation');
        const pageMargin = document.getElementById('pageMargin');
        
        // Ranges
        const blankColsRange = document.getElementById('blankColsRange');
        const fontSizeRange = document.getElementById('fontSizeRange');
        const rowHeightRange = document.getElementById('rowHeightRange');
        
        // Value labels
        const blankColsVal = document.getElementById('blankColsVal');
        const fontSizeVal = document.getElementById('fontSizeVal');
        const rowHeightVal = document.getElementById('rowHeightVal');

        // Checkboxes
        const showAdvisorsHeader = document.getElementById('showAdvisorsHeader');
        const showAdvisorsFooter = document.getElementById('showAdvisorsFooter');
        const showSupervisorFooter = document.getElementById('showSupervisorFooter');

        // Display
        const paperArea = document.getElementById('paperArea');
        const paperContent = document.getElementById('paperContent');
        const printOrientationStyle = document.getElementById('printOrientationStyle');

        // Initialize Filter dropdowns
        function initFilters() {
            // Sort keys numerically
            const sortedClasses = Object.keys(classesData).sort((a, b) => parseInt(a) - parseInt(b));
            
            sortedClasses.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = `มัธยมศึกษาปีที่ ${c}`;
                filterClass.appendChild(opt);
            });
        }

        // On Class change, update rooms
        filterClass.addEventListener('change', function() {
            const selectedClass = this.value;
            filterRoom.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            
            if (selectedClass && classesData[selectedClass]) {
                filterRoom.disabled = false;
                // Sort rooms numerically
                const rooms = classesData[selectedClass].sort((a, b) => parseInt(a) - parseInt(b));
                rooms.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r;
                    opt.textContent = `ห้อง ${r}`;
                    filterRoom.appendChild(opt);
                });
            } else {
                filterRoom.disabled = true;
            }
            
            clearPaper();
        });

        // Clear paper
        function clearPaper() {
            loadedStudents = [];
            loadedAdvisors = [];
            paperContent.innerHTML = `
                <div class="no-data" id="emptyDisplay">
                    <i class="fas fa-info-circle"></i>
                    <h3>กรุณาเลือก ระดับชั้น และ ห้องเรียน</h3>
                    <p>ระบบจะดึงรายชื่อและจัดหน้ากระดาษแบบตารางเช็คชื่อให้อย่างเหมาะสมแบบพร้อมพิมพ์ทันที</p>
                </div>
            `;
        }

        // On Room change, fetch data via AJAX
        filterRoom.addEventListener('change', function() {
            const classVal = filterClass.value;
            const roomVal = this.value;
            
            if (!classVal || !roomVal) {
                clearPaper();
                return;
            }

            // Show loading inside paper
            paperContent.innerHTML = `
                <div class="no-data">
                    <i class="fas fa-spinner fa-spin" style="color: var(--primary);"></i>
                    <h3>กำลังโหลดข้อมูล...</h3>
                    <p>กำลังดึงข้อมูลนักเรียนจากระบบฐานข้อมูล</p>
                </div>
            `;

            fetch(`print_student.php?action=get_students&class=${classVal}&room=${roomVal}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadedStudents = data.students;
                        loadedAdvisors = data.advisors;
                        
                        // Load customized settings from localStorage if available
                        const localPlanKey = `plan_${classVal}_${roomVal}`;
                        const localAdvisorsKey = `adv_${classVal}_${roomVal}`;
                        
                        if (localStorage.getItem(localPlanKey)) {
                            learningPlan.value = localStorage.getItem(localPlanKey);
                        } else {
                            // Default value
                            learningPlan.value = "แผนการเรียนทั่วไป";
                        }
                        
                        if (localStorage.getItem(localAdvisorsKey)) {
                            advisorNames.value = localStorage.getItem(localAdvisorsKey);
                        } else {
                            // Convert database array to string listing
                            if (loadedAdvisors.length > 0) {
                                advisorNames.value = loadedAdvisors.map((name, i) => `${i+1}.${name}`).join(' ');
                            } else {
                                advisorNames.value = '';
                            }
                        }

                        schoolYear.value = data.year;
                        
                        renderPaper();
                    } else {
                        paperContent.innerHTML = `
                            <div class="no-data" style="border-color: #ef4444;">
                                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                                <h3>เกิดข้อผิดพลาด</h3>
                                <p>${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error(err);
                    paperContent.innerHTML = `
                        <div class="no-data" style="border-color: #ef4444;">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                            <h3>เกิดข้อผิดพลาดการเชื่อมต่อ</h3>
                            <p>โปรดตรวจสอบสัญญาณอินเทอร์เน็ตหรือติดต่อผู้ดูแลระบบ</p>
                        </div>
                    `;
                });
        });

        // Store custom edits in local storage
        learningPlan.addEventListener('input', function() {
            const classVal = filterClass.value;
            const roomVal = filterRoom.value;
            if (classVal && roomVal) {
                localStorage.setItem(`plan_${classVal}_${roomVal}`, this.value);
            }
            renderPaper();
        });

        advisorNames.addEventListener('input', function() {
            const classVal = filterClass.value;
            const roomVal = filterRoom.value;
            if (classVal && roomVal) {
                localStorage.setItem(`adv_${classVal}_${roomVal}`, this.value);
            }
            renderPaper();
        });

        // Set up rendering updates for all controls
        [customTitle, schoolYear, pageOrientation, pageMargin, showAdvisorsHeader, showAdvisorsFooter, showSupervisorFooter].forEach(el => {
            el.addEventListener('change', renderPaper);
            el.addEventListener('input', renderPaper);
        });

        // Set up sliders
        blankColsRange.addEventListener('input', function() {
            blankColsVal.textContent = `${this.value} ช่อง`;
            renderPaper();
        });
        fontSizeRange.addEventListener('input', function() {
            fontSizeVal.textContent = `${this.value}px`;
            renderPaper();
        });
        rowHeightRange.addEventListener('input', function() {
            rowHeightVal.textContent = `${this.value}px`;
            renderPaper();
        });

        // Render functions
        function renderPaper() {
            if (loadedStudents.length === 0) return;

            const classVal = filterClass.value;
            const roomVal = filterRoom.value;
            const titleText = customTitle.value;
            const planText = learningPlan.value;
            const advisorsText = advisorNames.value;
            const yearVal = schoolYear.value;
            const orientation = pageOrientation.value;
            const marginVal = pageMargin.value;
            
            const blankCols = parseInt(blankColsRange.value);
            const fontSize = fontSizeRange.value + 'px';
            const rowHeight = rowHeightRange.value + 'px';
            
            const showAdvHead = showAdvisorsHeader.checked;
            const showAdvFoot = showAdvisorsFooter.checked;
            const showSupFoot = showSupervisorFooter.checked;

            // Apply orientation styles dynamically to preview screen and paper printing config
            if (orientation === 'landscape') {
                paperArea.classList.add('landscape');
                printOrientationStyle.innerHTML = `
                    @media print {
                        @page {
                            size: A4 landscape;
                            margin: ${marginVal};
                        }
                    }
                `;
            } else {
                paperArea.classList.remove('landscape');
                printOrientationStyle.innerHTML = `
                    @media print {
                        @page {
                            size: A4 portrait;
                            margin: ${marginVal};
                        }
                    }
                `;
            }

            // Margin configuration (Preview)
            paperArea.style.padding = marginVal;
            paperArea.style.fontSize = fontSize;

            // Build HTML
            let html = '';

            // Header Section
            html += `<div class="doc-header">`;
            html += `    <div class="doc-title">${titleText} ${classVal}/${roomVal} โรงเรียนพิชัย ปีการศึกษา ${yearVal}</div>`;
            
            html += `    <div class="doc-subrow">`;
            html += `        <div>แผนการเรียน: ${planText ? planText : '-'}</div>`;
            html += `        <div>จำนวนนักเรียน: ${loadedStudents.length} คน</div>`;
            html += `    </div>`;

            if (showAdvHead && advisorsText) {
                html += `    <div class="doc-advisors">ครูที่ปรึกษา: ${advisorsText}</div>`;
            }
            html += `</div>`;

            // Table Section
            html += `<table class="print-table" id="rosterTable">`;
            html += `    <thead>`;
            html += `        <tr>`;
            html += `            <th class="th-no" style="font-size: ${fontSize};">เลขที่</th>`;
            html += `            <th class="th-id" style="font-size: ${fontSize};">เลขประจำตัว</th>`;
            html += `            <th class="th-name" style="font-size: ${fontSize};">ชื่อ - สกุล</th>`;
            
            // Render blank column headers
            for (let i = 1; i <= blankCols; i++) {
                html += `            <th class="th-blank" style="font-size: ${fontSize};"></th>`;
            }
            
            html += `            <th style="width: 10%; font-size: ${fontSize};">หมายเหตุ</th>`;
            html += `        </tr>`;
            html += `    </thead>`;
            html += `    <tbody>`;

            // Roster rows
            loadedStudents.forEach((student, index) => {
                const fullName = `${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}`;
                html += `        <tr style="height: ${rowHeight};">`;
                html += `            <td style="font-size: ${fontSize}; font-weight: bold;">${student.Stu_no}</td>`;
                html += `            <td style="font-size: ${fontSize}; font-family: 'Inter', sans-serif;">${student.Stu_id}</td>`;
                html += `            <td class="text-left" style="font-size: ${fontSize};">${fullName}</td>`;
                
                // Blank cells
                for (let i = 1; i <= blankCols; i++) {
                    html += `            <td></td>`;
                }
                
                html += `            <td></td>`;
                html += `        </tr>`;
            });

            html += `    </tbody>`;
            html += `</table>`;

            // Footers / Signatures
            if (showAdvFoot || showSupFoot) {
                html += `<div class="doc-signatures">`;
                
                if (showAdvFoot) {
                    // Try to split advisor names if they were separated by numbers
                    const splitAdvisors = advisorsText.split(/\s+\d+\./).map(name => name.replace(/^\d+\./, '').trim()).filter(name => name !== '');
                    
                    if (splitAdvisors.length > 0) {
                        splitAdvisors.forEach(name => {
                            html += `    <div class="sig-block">`;
                            html += `        <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>`;
                            html += `        <p>( ${name} )</p>`;
                            html += `    </div>`;
                        });
                    } else {
                        // Render standard two signature blocks as placeholders
                        html += `    <div class="sig-block">`;
                        html += `        <p>ลงชื่อ..........................................................ครูที่ปรึกษา</p>`;
                        html += `        <p>( .......................................................... )</p>`;
                        html += `    </div>`;
                    }
                }
                
                if (showSupFoot) {
                    html += `    <div class="sig-block">`;
                    html += `        <p>ลงชื่อ..........................................................หัวหน้าระดับ</p>`;
                    html += `        <p>( .......................................................... )</p>`;
                    html += `    </div>`;
                }
                
                html += `</div>`;
            }

            paperContent.innerHTML = html;
        }

        // Excel Export
        function exportToExcel() {
            if (loadedStudents.length === 0) {
                alert("กรุณาเลือกและโหลดข้อมูลระดับชั้นก่อนส่งออก Excel");
                return;
            }

            const classVal = filterClass.value;
            const roomVal = filterRoom.value;
            const planText = learningPlan.value;
            const advisorsText = advisorNames.value;
            const yearVal = schoolYear.value;
            const blankCols = parseInt(blankColsRange.value);

            // Construct array of rows
            const excelRows = [];

            // Title headers for visual context
            excelRows.push([`รายชื่อนักเรียน ชั้น ม.${classVal}/${roomVal} ปีการศึกษา ${yearVal} (โรงเรียนพิชัย)`]);
            excelRows.push([`แผนการเรียน: ${planText ? planText : '-'} | ครูที่ปรึกษา: ${advisorsText ? advisorsText : '-'}`]);
            excelRows.push([]); // blank spacing

            // Headers
            const headers = ['เลขที่', 'เลขประจำตัว', 'ชื่อ-นามสกุล'];
            for (let i = 1; i <= blankCols; i++) {
                headers.push('');
            }
            headers.push('หมายเหตุ');
            excelRows.push(headers);

            // Add students
            loadedStudents.forEach(student => {
                const fullName = `${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}`;
                const row = [parseInt(student.Stu_no) || student.Stu_no, student.Stu_id, fullName];
                
                // Add empty blank values
                for (let i = 0; i < blankCols; i++) {
                    row.push('');
                }
                row.push('');
                excelRows.push(row);
            });

            // SheetJS operations
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(excelRows);

            // Style definitions
            const titleStyle = {
                font: { name: 'Tahoma', sz: 14, bold: true, color: { rgb: "1E293B" } },
                alignment: { horizontal: "center", vertical: "center" }
            };

            const subtitleStyle = {
                font: { name: 'Tahoma', sz: 10, color: { rgb: "475569" } },
                alignment: { horizontal: "center", vertical: "center" }
            };

            const headerStyle = {
                font: { name: 'Tahoma', sz: 10, bold: true, color: { rgb: "1E293B" } },
                fill: { fgColor: { rgb: "F1F5F9" } },
                alignment: { horizontal: "center", vertical: "center" },
                border: {
                    top: { style: "thin", color: { rgb: "94A3B8" } },
                    bottom: { style: "medium", color: { rgb: "1E293B" } },
                    left: { style: "thin", color: { rgb: "CBD5E1" } },
                    right: { style: "thin", color: { rgb: "CBD5E1" } }
                }
            };

            const dataStyleCenter = {
                font: { name: 'Tahoma', sz: 10 },
                alignment: { horizontal: "center", vertical: "center" },
                border: {
                    top: { style: "thin", color: { rgb: "E2E8F0" } },
                    bottom: { style: "thin", color: { rgb: "E2E8F0" } },
                    left: { style: "thin", color: { rgb: "E2E8F0" } },
                    right: { style: "thin", color: { rgb: "E2E8F0" } }
                }
            };

            const dataStyleLeft = {
                font: { name: 'Tahoma', sz: 10 },
                alignment: { horizontal: "left", vertical: "center" },
                border: {
                    top: { style: "thin", color: { rgb: "E2E8F0" } },
                    bottom: { style: "thin", color: { rgb: "E2E8F0" } },
                    left: { style: "thin", color: { rgb: "E2E8F0" } },
                    right: { style: "thin", color: { rgb: "E2E8F0" } }
                }
            };

            // Apply styles to cells
            const totalCols = 3 + blankCols + 1;
            for (let key in ws) {
                if (key.indexOf('!') === 0) continue; // Skip metadata
                
                const colLetter = key.match(/[A-Z]+/)[0];
                const rowNum = parseInt(key.match(/\d+/)[0]);
                
                let colIndex = 0;
                for (let i = 0; i < colLetter.length; i++) {
                    colIndex = colIndex * 26 + (colLetter.charCodeAt(i) - 64);
                }
                colIndex = colIndex - 1; // 0-indexed
                
                const cell = ws[key];
                
                if (rowNum === 1) {
                    cell.s = titleStyle;
                } else if (rowNum === 2) {
                    cell.s = subtitleStyle;
                } else if (rowNum === 4) {
                    cell.s = headerStyle;
                } else if (rowNum > 4) {
                    // Data rows
                    if (colIndex === 2) { // Column C: Name
                        cell.s = dataStyleLeft;
                    } else { // Other columns: Center align
                        cell.s = dataStyleCenter;
                    }
                }
            }

            // 1. Column Widths
            const wscols = [
                { wch: 8 },  // Col A: เลขที่
                { wch: 15 }, // Col B: เลขประจำตัว
                { wch: 30 }  // Col C: ชื่อ-นามสกุล
            ];
            for (let i = 1; i <= blankCols; i++) {
                wscols.push({ wch: 6 }); // Narrow blank check columns
            }
            wscols.push({ wch: 15 }); // Col: หมายเหตุ
            ws['!cols'] = wscols;

            // 2. Row Heights
            const wsrows = [
                { hpt: 28 }, // Row 1 (Title)
                { hpt: 20 }, // Row 2 (Subtitle)
                { hpt: 12 }  // Row 3 (Blank gap)
            ];
            wsrows.push({ hpt: 24 }); // Row 4 (Table Headers)
            for (let i = 0; i < loadedStudents.length; i++) {
                wsrows.push({ hpt: 20 }); // Student Data Rows
            }
            ws['!rows'] = wsrows;

            // 3. Merge Cells (Header titles)
            ws['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: totalCols - 1 } }, // Merge Title Row
                { s: { r: 1, c: 0 }, e: { r: 1, c: totalCols - 1 } }  // Merge Subtitle Row
            ];

            XLSX.utils.book_append_sheet(wb, ws, "Roster Listing");
            XLSX.writeFile(wb, `รายชื่อนักเรียน_ม.${classVal}_${roomVal}_ปี_${yearVal}.xlsx`);
        }

        // Initialize dynamic filters when body loads
        window.addEventListener('DOMContentLoaded', () => {
            initFilters();
            
            // Check for pre-populated URL params
            const urlParams = new URLSearchParams(window.location.search);
            const classParam = urlParams.get('class');
            const roomParam = urlParams.get('room');
            
            if (classParam) {
                filterClass.value = classParam;
                // Trigger change event manually to rebuild rooms dropdown
                filterClass.dispatchEvent(new Event('change'));
                
                if (roomParam) {
                    filterRoom.value = roomParam;
                    // Trigger change event to fetch students
                    filterRoom.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
</body>
</html>
