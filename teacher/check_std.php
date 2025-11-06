<?php
// เรียกมาเฉพาะ class และ room ของครูผู้ใช้ปัจจุบัน
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

require_once("../class/Attendance.php");
$attendance = new Attendance($db);

// กำหนดวันที่ (วันนี้ หรือจาก GET)
function convertToBuddhistYear($date) {
    // ตรวจสอบว่ารูปแบบเป็น YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);

        // ถ้าเป็นปี ค.ศ. ให้บวก 543
        if ($year < 2500) {
            $year += 543;
        }

        return $year . '-' . $month . '-' . $day;
    }
    // ถ้า format ไม่ถูกต้อง คืนค่าเดิม
    return $date;
}

// ฟังก์ชันแปลงวันที่เป็น วัน เดือน ปี พ.ศ. ภาษาไทย
function thaiDate($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}

// ใช้งาน
date_default_timezone_set('Asia/Bangkok');
// ใช้ Gregorian date (ค.ศ.) สำหรับ database query
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// ดึงข้อมูลนักเรียนห้องของครู - ใช้ Gregorian date
$students = $attendance->getStudentsWithAttendance($date, $class, $room, $term, $pee);
$term = $user->getTerm();
$pee = $user->getPee();
?>

<!-- Header Card with Gradient -->
<div class="mb-6 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                <span class="text-3xl">📝</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold">เช็คชื่อนักเรียน</h2>
                <p class="text-blue-100 text-sm mt-1">
                    🏫 ชั้น ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?> 
                    📅 <?= htmlspecialchars(thaiDate(convertToBuddhistYear($date))) ?>
                </p>
            </div>
        </div>
        <form method="get" class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg p-3">
            <input type="hidden" name="tab" value="check">
            <label for="date" class="text-white font-medium flex items-center gap-2">
                📆 เลือกวันที่:
            </label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" 
                   class="border-0 rounded-lg px-3 py-2 text-gray-800 font-medium focus:ring-2 focus:ring-blue-300 transition">
            <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 font-medium transition shadow-md hover:shadow-lg flex items-center gap-2">
                <span>🔍</span> แสดง
            </button>
        </form>
    </div>
</div>

<!-- Info Banner -->
<div class="mb-6 bg-amber-50 border-l-4 border-amber-400 rounded-lg p-4 shadow-sm">
    <div class="flex items-start gap-3">
        <span class="text-2xl">💡</span>
        <div>
            <p class="text-amber-800 font-medium mb-1">คำแนะนำการใช้งาน</p>
            <p class="text-amber-700 text-sm">
                ✨ ระบบจะเช็ค <span class="font-semibold text-green-600">"มาเรียน"</span> เป็นค่าเริ่มต้นโดยอัตโนมัติ<br>
                📱 สำหรับมือถือ: คลิกปุ่ม <span class="inline-block bg-amber-500 text-white px-2 py-0.5 rounded text-xs font-bold">แก้ไข</span> เพื่อเปลี่ยนสถานะ
            </p>
        </div>
    </div>
</div>


<div class="overflow-x-auto">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <!-- DataTables JS + jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <style>
        /* Modern Gradient Background */

        
        /* เพิ่มลูกเล่น hover และ effect ให้ radio */
        .attendance-radio label {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .attendance-radio label:hover {
            transform: translateY(-2px);
        }
        .attendance-radio label:active {
            transform: scale(0.97);
        }
        .attendance-radio input:focus + span {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .attendance-radio span {
            display: inline-block;
            min-width: 75px;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        /* เพิ่ม effect เมื่อเลือก */
        .attendance-radio input:checked + span {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transform: scale(1.05);
            border-color: currentColor;
        }
        
        /* Table Styling */
        #attendance-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
            border: none;
        }
        
        #attendance-table tbody tr {
            transition: all 0.2s ease;
            background: white;
        }
        
        #attendance-table tbody tr:hover {
            background: linear-gradient(90deg, #EBF4FF 0%, #E0F2FE 100%);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        #attendance-table tbody td {
            border-left: none;
            border-right: none;
            border-top: 1px solid #E5E7EB;
            padding: 0.875rem 0.75rem;
        }
        
        /* Sticky columns for mobile - ตึงคอลัมน์เลขที่และชื่อ */
        .table-wrapper {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Remove custom sticky - let DataTables handle it */
        
        /* Mobile optimization */
        @media (max-width: 768px) {
            #attendance-table {
                font-size: 0.8rem;
            }
            
            #attendance-table thead th,
            #attendance-table tbody td {
                padding: 0.625rem 0.375rem;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.625rem !important;
            }
            
            .attendance-radio span {
                min-width: 55px;
                font-size: 0.7rem;
                padding: 0.375rem 0.625rem;
            }
            
            /* ปรับ header ให้กระชับ */
            #attendance-table thead th {
                font-size: 0.7rem;
                padding: 0.5rem 0.25rem;
            }
        }
        
        /* Edit Form Animation */
        .edit-attendance-form {
            display: none !important;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Button Styling */
        .edit-attendance-btn {
            transition: all 0.2s ease;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .edit-attendance-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .save-edit-btn, .cancel-edit-btn {
            transition: all 0.2s ease;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .save-edit-btn:hover, .cancel-edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Status Badge Animations */
        .status-badge {
            display: inline-block;
            animation: fadeIn 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #E5E7EB;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #E5E7EB;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
        
        /* Fixed Columns Styling */
        table.dataTable.DTFC_Cloned thead th,
        table.dataTable.DTFC_Cloned tbody td {
            background-color: white !important;
        }
        
        table.dataTable.DTFC_Cloned thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        div.DTFC_LeftWrapper table.dataTable tbody tr:hover td {
            background: linear-gradient(90deg, #EBF4FF 0%, #E0F2FE 100%) !important;
        }
        
        div.DTFC_LeftBodyLiner {
            overflow: hidden !important;
        }
        
        /* ปรับ z-index ให้ fixed columns อยู่เหนือ */
        div.DTFC_LeftWrapper {
            z-index: 15 !important;
        }
        
        div.DTFC_LeftHeadWrapper {
            z-index: 16 !important;
        }
        
        /* Smooth scroll for table */
        .dataTables_scrollBody {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        /* ปรับ edit button ให้เล็กลงในมือถือ */
        @media (max-width: 768px) {
            .edit-attendance-btn {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.75rem;
            }
            
            .edit-attendance-form {
                width: 90vw;
                max-width: 400px;
            }
        }
    </style>
    <form id="attendance-form" method="post">
        <?php
        // ส่งวันที่แบบ Gregorian (ค.ศ.) ไปยัง API
        ?>
        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
        <div class="table-wrapper overflow-x-auto">
        <table id="attendance-table" class="min-w-[1000px] border-0 rounded-xl shadow-xl overflow-hidden bg-white w-full">
            <thead class="bg-blue-100">
                <tr>
                    <th class="px-3 py-3 border-0 text-center">🔢<br><span class="text-xs">เลขที่</span></th>
                    <th class="px-3 py-3 border-0 text-center">🆔<br><span class="text-xs">รหัสนักเรียน</span></th>
                    <th class="px-4 py-3 border-0 text-left">👤 ชื่อ-สกุล</th>
                    <th class="px-4 py-3 border-0 text-center whitespace-nowrap">✅ การเช็คชื่อ</th>
                    <th class="px-4 py-3 border-0 text-center">📊 สถานะ</th>
                    <th class="px-3 py-3 border-0 text-center">📝<br><span class="text-xs">สาเหตุ</span></th>
                    <th class="px-3 py-3 border-0 text-center">👨‍🏫<br><span class="text-xs">เช็คโดย</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $idx => $std): ?>
                        <tr data-stu-id="<?= htmlspecialchars($std['Stu_id']) ?>" class="hover:bg-blue-50 transition-colors border-b border-gray-100">
                            <td class="px-3 py-3 text-center font-bold text-lg text-indigo-600"><?= htmlspecialchars($std['Stu_no']) ?></td>
                            <td class="px-3 py-3 font-mono text-sm text-gray-600"><?= htmlspecialchars($std['Stu_id']) ?></td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full flex-shrink-0"></span>
                                    <span class="truncate"><?= htmlspecialchars($std['Stu_pre'] . $std['Stu_name'] . ' ' . $std['Stu_sur']) ?></span>
                                </div>
                            </td>
                            
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap flex-row gap-1 justify-center items-center whitespace-nowrap">
                                <?php
                                if (!empty($std['attendance_status'])) {
                                    // --- เพิ่มปุ่มแก้ไข ---
                                    ?>
                                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-2 rounded-lg shadow-sm">
                                        
                                        <button type="button" class="bg-gradient-to-r from-amber-400 to-orange-500 text-white px-3 py-1.5 rounded-lg hover:from-amber-500 hover:to-orange-600 text-sm edit-attendance-btn transition-all" data-stu-id="<?= htmlspecialchars($std['Stu_id']) ?>">
                                            ✏️ แก้ไข
                                        </button>

                                        <span class="text-sm font-medium text-gray-700">📅 <?= !empty($std['attendance_date']) ? htmlspecialchars($std['attendance_date']) : '-' ?></span>
                                    </div>
                                    <div class="edit-attendance-form mt-2 hidden bg-white p-4 rounded-xl shadow-xl border-2 border-indigo-200" id="edit-form-<?= htmlspecialchars($std['Stu_id']) ?>">
                                    <!-- ฟอร์มแก้ไข (ซ่อนอยู่) - use a div instead of nested form -->
                                        <input type="hidden" name="edit_mode" value="1">
                                        <input type="hidden" name="Stu_id[]" value="<?= htmlspecialchars($std['Stu_id']) ?>">
                                        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
                                        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
                                        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
                                        <input type="hidden" name="teach_id[<?= htmlspecialchars($std['Stu_id']) ?>]" value="<?= htmlspecialchars($_SESSION['Teacher_login'] ?? '') ?>">
                                        <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                            <span>📊</span> เลือกสถานะการเข้าเรียน
                                        </p>
                                        <div class="flex flex-wrap gap-2 mb-3 justify-center attendance-radio">
                                            <?php
                                            $status_options = [
                                                '1' => ['✅ มา', 'bg-green-50 text-green-700 peer-checked:bg-gradient-to-r peer-checked:from-green-400 peer-checked:to-emerald-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                                '2' => ['❌ ขาด', 'bg-red-50 text-red-700 peer-checked:bg-gradient-to-r peer-checked:from-red-400 peer-checked:to-rose-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                                '3' => ['🕒 สาย', 'bg-yellow-50 text-yellow-700 peer-checked:bg-gradient-to-r peer-checked:from-yellow-400 peer-checked:to-orange-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                                '4' => ['🤒 ป่วย', 'bg-blue-50 text-blue-700 peer-checked:bg-gradient-to-r peer-checked:from-blue-400 peer-checked:to-cyan-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                                '5' => ['📝 กิจ', 'bg-purple-50 text-purple-700 peer-checked:bg-gradient-to-r peer-checked:from-purple-400 peer-checked:to-indigo-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                                '6' => ['🎉 กิจกรรม', 'bg-pink-50 text-pink-700 peer-checked:bg-gradient-to-r peer-checked:from-pink-400 peer-checked:to-fuchsia-500 peer-checked:text-white shadow-sm hover:shadow-md'],
                                            ];
                                            foreach ($status_options as $val => [$label, $cls]) {
                                                ?>
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                        name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]"
                                                        value="<?= $val ?>"
                                                        class="hidden peer"
                                                        <?= $std['attendance_status'] == $val ? 'checked' : '' ?>>
                                                    <span class="px-3 py-2 rounded-lg <?= $cls ?>">
                                                        <?= $label ?>
                                                    </span>
                                                </label>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <input type="text" name="reason[<?= htmlspecialchars($std['Stu_id']) ?>]" placeholder="💬 ระบุสาเหตุ (ถ้ามี)" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 mb-3 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition" value="<?= htmlspecialchars($std['reason'] ?? '') ?>" />
                                        <!-- สำหรับบันทึก behavior กรณีมาสาย -->
                                        <input type="hidden" name="behavior_type[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                        <input type="hidden" name="behavior_name[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                        <input type="hidden" name="behavior_score[<?= htmlspecialchars($std['Stu_id']) ?>]" value="5">
                                        <div class="flex gap-2 justify-center">
                                            <button type="button" class="save-edit-btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 font-medium flex items-center gap-2">
                                                💾 บันทึก
                                            </button>
                                            <button type="button" class="cancel-edit-btn bg-gradient-to-r from-red-500 to-rose-600 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-rose-700 font-medium flex items-center gap-2">
                                                ❌ ยกเลิก
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    // radio group: name="attendance_status[Stu_id]"
                                    ?>
                                    <div class="flex flex-row flex-wrap gap-2 justify-center items-center whitespace-nowrap attendance-radio">
                                    <input type="hidden" name="Stu_id[]" value="<?= htmlspecialchars($std['Stu_id']) ?>">
                                    <!-- สำหรับบันทึก behavior กรณีมาสาย -->
                                    <input type="hidden" name="behavior_type[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                    <input type="hidden" name="behavior_name[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                    <input type="hidden" name="behavior_score[<?= htmlspecialchars($std['Stu_id']) ?>]" value="5">
                                    <input type="hidden" name="teach_id[<?= htmlspecialchars($std['Stu_id']) ?>]" value="<?= htmlspecialchars($_SESSION['Teacher_login'] ?? '') ?>">
                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="1" 
                                            class="hidden peer" 
                                            checked>
                                        <span class="px-3 py-2 rounded-lg bg-green-50 text-green-700 peer-checked:bg-gradient-to-r peer-checked:from-green-400 peer-checked:to-emerald-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            ✅ มา
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="2" 
                                            class="hidden peer">
                                        <span class="px-3 py-2 rounded-lg bg-red-50 text-red-700 peer-checked:bg-gradient-to-r peer-checked:from-red-400 peer-checked:to-rose-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            ❌ ขาด
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="3" 
                                            class="hidden peer">
                                        <span class="px-3 py-2 rounded-lg bg-yellow-50 text-yellow-700 peer-checked:bg-gradient-to-r peer-checked:from-yellow-400 peer-checked:to-orange-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            🕒 สาย
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="4" 
                                            class="hidden peer">
                                        <span class="px-3 py-2 rounded-lg bg-blue-50 text-blue-700 peer-checked:bg-gradient-to-r peer-checked:from-blue-400 peer-checked:to-cyan-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            🤒 ป่วย
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="5" 
                                            class="hidden peer">
                                        <span class="px-3 py-2 rounded-lg bg-purple-50 text-purple-700 peer-checked:bg-gradient-to-r peer-checked:from-purple-400 peer-checked:to-indigo-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            📝 กิจ
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="6" 
                                            class="hidden peer">
                                        <span class="px-3 py-2 rounded-lg bg-pink-50 text-pink-700 peer-checked:bg-gradient-to-r peer-checked:from-pink-400 peer-checked:to-fuchsia-500 peer-checked:text-white shadow-sm hover:shadow-md">
                                            🎉 กิจกรรม
                                        </span>
                                    </label>
                                </div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 text-center"><?php
                                if (!empty($std['attendance_status'])) {
                                    switch ($std['attendance_status']) {
                                        case '1':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full text-white shadow-md">✅ มาเรียน</span>';
                                            break;
                                        case '2':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-red-400 to-rose-500 rounded-full text-white shadow-md">❌ ขาดเรียน</span>';
                                            break;
                                        case '3':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full text-white shadow-md">🕒 มาสาย</span>';
                                            break;
                                        case '4':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full text-white shadow-md">🤒 ลาป่วย</span>';
                                            break;
                                        case '5':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full text-white shadow-md">📝 ลากิจ</span>';
                                            break;
                                        case '6':
                                            echo '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-pink-400 to-fuchsia-500 rounded-full text-white shadow-md">🎉 เข้าร่วมกิจกรรม</span>';
                                            break;
                                        default:
                                            echo '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                    }
                                } else {
                                    echo '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                }
                                ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php
                                // สาเหตุ
                                if (!empty($std['attendance_status'])) {
                                    echo !empty($std['reason']) ? '<span class="text-gray-700 font-medium">' . htmlspecialchars($std['reason']) . '</span>' : '<span class="text-gray-400">➖</span>';
                                } else {
                                    ?>
                                    <input type="text" name="reason[<?= htmlspecialchars($std['Stu_id']) ?>]" placeholder="💬 สาเหตุ (ถ้ามี)" class="border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition w-full max-w-xs" />
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php
                                // เช็คจาก
                                if (!empty($std['checked_by'])) {
                                    if ($std['checked_by'] === 'system' || $std['checked_by'] === 'teacher') {
                                        echo '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full text-white text-sm font-medium shadow-md">👨‍🏫 ครูที่ปรึกษา</span>';
                                    } elseif ($std['checked_by'] === 'rfid' || $std['checked_by'] === 'RFID') {
                                        $time = !empty($std['attendance_time']) ? date('H:i', strtotime($std['attendance_time'])) : null;
                                        echo '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full text-white text-sm font-medium shadow-md">💳 สแกนบัตร';
                                        if ($time !== null) {
                                            echo ' <span class="text-xs opacity-90">(' . htmlspecialchars($time) . ')</span>';
                                        }
                                        echo '</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-200 rounded-full text-gray-700 text-sm font-medium">' . htmlspecialchars($std['checked_by']) . '</span>';
                                    }
                                } else {
                                    echo '<span class="text-gray-400">➖</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-6xl">📭</span>
                                <p class="text-lg font-medium">ไม่พบนักเรียนในห้องนี้</p>
                                <p class="text-sm text-gray-400">กรุณาตรวจสอบข้อมูลหรือเลือกวันที่อื่น</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
        <?php if (!empty($students)): ?>
            <div class="flex justify-end mt-6">
                <button id="btn-save-bulk" type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-3 rounded-xl hover:from-green-600 hover:to-emerald-700 font-bold text-lg shadow-lg hover:shadow-xl transition-all flex items-center gap-3 transform hover:-translate-y-1">
                    <span class="text-2xl">💾</span>
                    <span>บันทึกการเช็คชื่อทั้งห้อง</span>
                    <span class="text-2xl">✨</span>
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // small helper to escape HTML when injecting text
    function escapeHtml(str){
        if (!str && str !== 0) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    // DataTables initialization
    var table = $('#attendance-table').DataTable({
        responsive: false,
        autoWidth: false,
        lengthChange: false,
        pageLength: 50,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2  // ตึงคอลัมน์แรก 2 คอลัมน์ (เลขที่ + รหัส)
        },
        columnDefs: [
            { className: "text-center font-bold", targets: [0], width: "70px", orderable: true },
            { className: "text-center", targets: [1], width: "100px" },
            { className: "text-left font-semibold", targets: [2], width: "220px", orderable: true },
            { className: "text-center", targets: [3], width: "140px" },
            { className: "text-center whitespace-nowrap", targets: [4], width: "280px", orderable: false },
            { className: "text-center", targets: [5], width: "140px" },
            { className: "text-center", targets: [6], width: "130px" },
        ],
        order: [[0, 'asc']], // เรียงตามเลขที่
        language: {
            search: "🔍 ค้นหา:",
            searchPlaceholder: "ค้นหาชื่อนักเรียน...",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ คน",
            infoEmpty: "ไม่พบข้อมูล",
            infoFiltered: "(กรองจากทั้งหมด _MAX_ คน)",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // เมื่อกดปุ่ม "แก้ไข"
    document.querySelectorAll('.edit-attendance-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // ปิดฟอร์มอื่นๆ ก่อน (Set display to none, CSS rule will take over)
            document.querySelectorAll('.edit-attendance-form').forEach(function(f) {
                f.style.display = 'none';
            });
            // เปิดฟอร์มของแถวนี้
            var tr = btn.closest('tr');
            if (tr) {
                var form = tr.querySelector('.edit-attendance-form');
                if (form) {
                    // Use setProperty to override the CSS !important rule
                    form.style.setProperty('display', 'block', 'important');
                }
            }
        });
    });
    // เมื่อกดปุ่ม "ยกเลิก"
    document.querySelectorAll('.cancel-edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var form = btn.closest('.edit-attendance-form');
            if (form) {
                // Set display to none, the CSS rule will ensure it stays hidden
                form.style.display = 'none';
            }
        });
    });

    // เมื่อกดปุ่ม "บันทึก" ในแผงแก้ไข ให้ส่งข้อมูลด้วย fetch (ป้องกัน nested form)
    document.querySelectorAll('.save-edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var panel = btn.closest('.edit-attendance-form');
            if (!panel) return;

            var formData = new FormData();
            // collect all inputs and radios inside the panel
            panel.querySelectorAll('input').forEach(function(input) {
                if (!input.name) return;
                if (input.type === 'radio') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    }
                } else if (input.type === 'checkbox') {
                    if (input.checked) formData.append(input.name, input.value);
                } else {
                    formData.append(input.name, input.value);
                }
            });

            // send to controller endpoint (use save_bulk which accepts the same form payload)
            // disable button to avoid duplicate clicks
            if (btn.dataset.busy === '1') return;
            btn.dataset.busy = '1';
            var origBtnText = btn.innerHTML;
            btn.innerHTML = 'กำลังบันทึก...';
            btn.disabled = true;

            fetch('../controllers/AttendanceController.php?action=save_bulk', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(resp) {
                return resp.json();
            }).then(function(json) {
                if (json && json.success) {
                    // update rows in-place if results provided
                    if (json.results) {
                        Object.keys(json.results).forEach(function(stuId){
                            var info = json.results[stuId];
                            var tr = document.querySelector('tr[data-stu-id="' + stuId + '"]');
                            if (!tr) return;
                            // status cell index 3
                            var statusCell = tr.cells[3];
                            var reasonCell = tr.cells[5];
                            var checkedCell = tr.cells[6];
                            // render status badge
                            function renderStatusBadge(code){
                                if (!code) return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                switch (String(code)){
                                    case '1': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full text-white shadow-md">✅ มาเรียน</span>';
                                    case '2': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-red-400 to-rose-500 rounded-full text-white shadow-md">❌ ขาดเรียน</span>';
                                    case '3': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full text-white shadow-md">🕒 มาสาย</span>';
                                    case '4': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full text-white shadow-md">🤒 ลาป่วย</span>';
                                    case '5': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full text-white shadow-md">📝 ลากิจ</span>';
                                    case '6': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-pink-400 to-fuchsia-500 rounded-full text-white shadow-md">🎉 เข้าร่วมกิจกรรม</span>';
                                    default: return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                }
                            }

                            statusCell.innerHTML = renderStatusBadge(info && info.attendance_status ? info.attendance_status : null);
                            reasonCell.innerHTML = info && info.reason ? '<span class="text-gray-700 font-medium">' + escapeHtml(info.reason) + '</span>' : '<span class="text-gray-400">➖</span>';
                            // checked_by rendering
                            var cb = info && info.checked_by ? info.checked_by : null;
                            if (cb === 'system' || cb === 'teacher') {
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full text-white text-sm font-medium shadow-md">👨‍🏫 ครูที่ปรึกษา</span>';
                            } else if (cb === 'rfid' || cb === 'RFID') {
                                var t = info.attendance_time ? (' <span class="text-xs opacity-90">(' + info.attendance_time.substring(0,5) + ')</span>') : '';
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full text-white text-sm font-medium shadow-md">💳 สแกนบัตร' + t + '</span>';
                            } else if (cb) {
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-200 rounded-full text-gray-700 text-sm font-medium">' + escapeHtml(cb) + '</span>';
                            } else {
                                checkedCell.innerHTML = '<span class="text-gray-400">➖</span>';
                            }

                            // hide edit form if any
                            var editForm = tr.querySelector('.edit-attendance-form');
                            if (editForm) editForm.style.display = 'none';
                        });
                    }

                    // show toast if available
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: (json.saved ? (json.saved + ' รายการ') : 'สำเร็จ'),
                            timer: 1500,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                        // reload to ensure UI (edit buttons/forms) render correctly
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        alert('บันทึกสำเร็จ');
                        setTimeout(function(){ location.reload(); }, 500);
                    }
                } else {
                    alert('บันทึกล้มเหลว: ' + (json && json.error ? json.error : 'Unknown error'));
                    btn.disabled = false;
                    btn.dataset.busy = '0';
                    btn.innerHTML = origBtnText;
                }
            }).catch(function(err) {
                console.error(err);
                alert('เกิดข้อผิดพลาดในการบันทึก');
                btn.disabled = false;
                btn.dataset.busy = '0';
                btn.innerHTML = origBtnText;
            });
        });
    });

    // Intercept bulk save to use AJAX and prevent duplicate submissions
    const attendanceForm = document.getElementById('attendance-form');
    const saveBtn = document.getElementById('btn-save-bulk');
    if (attendanceForm && saveBtn) {
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveBtn.dataset.busy === '1') return; // already submitting
            saveBtn.dataset.busy = '1';
            const origText = saveBtn.innerHTML;
            saveBtn.innerHTML = 'กำลังบันทึก...';
            saveBtn.disabled = true;

            const formData = new FormData(attendanceForm);

            fetch('../controllers/AttendanceController.php?action=save_bulk', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(resp){
                return resp.json();
            }).then(function(json){
                if (json && json.success) {
                    if (json.results) {
                        Object.keys(json.results).forEach(function(stuId){
                            var info = json.results[stuId];
                            var tr = document.querySelector('tr[data-stu-id="' + stuId + '"]');
                            if (!tr) return;
                            var statusCell = tr.cells[3];
                            var reasonCell = tr.cells[5];
                            var checkedCell = tr.cells[6];
                            function renderStatusBadge(code){
                                if (!code) return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                switch (String(code)){
                                    case '1': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full text-white shadow-md">✅ มาเรียน</span>';
                                    case '2': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-red-400 to-rose-500 rounded-full text-white shadow-md">❌ ขาดเรียน</span>';
                                    case '3': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full text-white shadow-md">🕒 มาสาย</span>';
                                    case '4': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full text-white shadow-md">🤒 ลาป่วย</span>';
                                    case '5': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full text-white shadow-md">📝 ลากิจ</span>';
                                    case '6': return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-pink-400 to-fuchsia-500 rounded-full text-white shadow-md">🎉 เข้าร่วมกิจกรรม</span>';
                                    default: return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
                                }
                            }
                            statusCell.innerHTML = renderStatusBadge(info && info.attendance_status ? info.attendance_status : null);
                            reasonCell.innerHTML = info && info.reason ? '<span class="text-gray-700 font-medium">' + escapeHtml(info.reason) + '</span>' : '<span class="text-gray-400">➖</span>';
                            var cb = info && info.checked_by ? info.checked_by : null;
                            if (cb === 'system' || cb === 'teacher') {
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full text-white text-sm font-medium shadow-md">👨‍🏫 ครูที่ปรึกษา</span>';
                            } else if (cb === 'rfid' || cb === 'RFID') {
                                var t = info.attendance_time ? (' <span class="text-xs opacity-90">(' + info.attendance_time.substring(0,5) + ')</span>') : '';
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full text-white text-sm font-medium shadow-md">💳 สแกนบัตร' + t + '</span>';
                            } else if (cb) {
                                checkedCell.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-200 rounded-full text-gray-700 text-sm font-medium">' + escapeHtml(cb) + '</span>';
                            } else {
                                checkedCell.innerHTML = '<span class="text-gray-400">➖</span>';
                            }
                            var editForm = tr.querySelector('.edit-attendance-form');
                            if (editForm) editForm.style.display = 'none';
                        });
                    }
                    if (window.Swal) {
                        Swal.fire({icon:'success', title:'บันทึกสำเร็จ', text:(json.saved?json.saved+' รายการ':'สำเร็จ'), toast:true, position:'top-end', timer:1500, showConfirmButton:false});
                        // refresh so edit buttons are in the expected state
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        alert('บันทึกสำเร็จ');
                        setTimeout(function(){ location.reload(); }, 500);
                    }
                } else {
                    alert('บันทึกล้มเหลว: ' + (json.error || 'Unknown error'));
                    saveBtn.disabled = false; saveBtn.dataset.busy = '0'; saveBtn.innerHTML = origText;
                }
            }).catch(function(err){
                console.error(err);
                alert('เกิดข้อผิดพลาดในการส่งข้อมูล โปรดลองใหม่');
                saveBtn.disabled = false; saveBtn.dataset.busy = '0'; saveBtn.innerHTML = origText;
            });
        });
    }
});
</script>
