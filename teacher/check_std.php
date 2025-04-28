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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateC = convertToBuddhistYear($date);

// ดึงข้อมูลนักเรียนห้องของครู
$students = $attendance->getStudentsWithAttendance($dateC, $class, $room);
$term = $user->getTerm();
$pee = $user->getPee();
?>

<div class="mb-4 flex flex-wrap gap-4 items-center">
    <div class="text-blue-700 font-semibold">
        เช็คชื่อนักเรียน ชั้น ม.<?= htmlspecialchars($class) ?> ห้อง <?= htmlspecialchars($room) ?> ของวันที่ <?= htmlspecialchars(thaiDate($dateC)) ?>
    </div>
    <form method="get" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="check">
        <label for="date" class="text-gray-700">เลือกวันที่:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border rounded px-2 py-1">
        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">แสดง</button>
    </form>
</div>

<div class="overflow-x-auto">
    <style>
        /* เพิ่มลูกเล่น hover และ effect ให้ radio */
        .attendance-radio label {
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .attendance-radio label:active {
            transform: scale(0.97);
        }
        .attendance-radio input:focus + span {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }
        .attendance-radio span {
            display: inline-block;
            min-width: 70px;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: background 0.2s, color 0.2s;
        }
        /* เพิ่ม effect เล็กน้อยเมื่อเลือก */
        .attendance-radio input:checked + span {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        /* เพิ่ม CSS ให้แน่ใจว่าฟอร์มแก้ไขถูกซ่อนเสมอตอนเริ่มต้น */
        .edit-attendance-form {
            display: none !important;
        }
    </style>
    <form method="post" action="api/check_std_action.php">
        <?php
        // แปลงปีใน $date ให้เป็น พ.ศ.
        $date_thai = $date;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            $date_thai = ($m[1] + 543) . '-' . $m[2] . '-' . $m[3];
        }
        ?>
        <input type="hidden" name="date" value="<?= htmlspecialchars($date_thai) ?>">
        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
        <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-blue-100">
                <tr>
                    <th class="px-3 py-2 border text-center">เลขที่</th>
                    <th class="px-3 py-2 border text-center">รหัสนักเรียน</th>
                    <th class="px-3 py-2 border text-center">ชื่อ-สกุล</th>
                    <th class="px-3 py-2 border text-center">สถานะ</th>
                    <th class="px-3 py-2 border text-center">การเช็คชื่อ</th>
                    <th class="px-3 py-2 border text-center">สาเหตุ</th>
                    <th class="px-3 py-2 border text-center">เช็คจาก</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $idx => $std): ?>
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-3 py-2 border text-center"><?= htmlspecialchars($std['Stu_no']) ?></td>
                            <td class="px-3 py-2 border"><?= htmlspecialchars($std['Stu_id']) ?></td>
                            <td class="px-3 py-2 border"><?= htmlspecialchars($std['Stu_pre'] . $std['Stu_name'] . ' ' . $std['Stu_sur']) ?></td>
                            <td class="px-3 py-2 border text-center">
                                <?php
                                if (!empty($std['attendance_status'])) {
                                    switch ($std['attendance_status']) {
                                        case '1':
                                            echo '<span class="inline-block px-2 py-1 bg-green-200 rounded text-green-700">มาเรียน</span>';
                                            break;
                                        case '2':
                                            echo '<span class="inline-block px-2 py-1 bg-red-200 rounded text-red-700">ขาดเรียน</span>';
                                            break;
                                        case '3':
                                            echo '<span class="inline-block px-2 py-1 bg-yellow-200 rounded text-yellow-700">มาสาย</span>';
                                            break;
                                        case '4':
                                            echo '<span class="inline-block px-2 py-1 bg-blue-200 rounded text-blue-700">ลาป่วย</span>';
                                            break;
                                        case '5':
                                            echo '<span class="inline-block px-2 py-1 bg-purple-200 rounded text-purple-700">ลากิจ</span>';
                                            break;
                                        case '6':
                                            echo '<span class="inline-block px-2 py-1 bg-pink-200 rounded text-pink-700">เข้าร่วมกิจกรรม</span>';
                                            break;
                                        default:
                                            echo '<span class="inline-block px-2 py-1 bg-gray-200 rounded text-gray-700">-</span>';
                                    }
                                } else {
                                    echo '<span class="inline-block px-2 py-1 bg-gray-200 rounded text-gray-700">-</span>';
                                }
                                ?>
                            </td>
                            <td class="px-3 py-2 border text-center">
                                <?php
                                if (!empty($std['attendance_status'])) {
                                    // --- เพิ่มปุ่มแก้ไข ---
                                    ?>
                                    <div>
                                        <?= !empty($std['attendance_date']) ? htmlspecialchars($std['attendance_date']) : '-' ?>
                                        <button type="button" class="btn bg-amber-500 text-white px-3 py-1 rounded hover:bg-amber-600 ml-2 text-white  text-sm edit-attendance-btn" data-stu-id="<?= htmlspecialchars($std['Stu_id']) ?>">แก้ไข</button>
                                    </div>
                                    <!-- ฟอร์มแก้ไข (ซ่อนอยู่) - Removed inline style -->
                                    <form method="post" action="api/check_std_action.php" class="edit-attendance-form mt-2 hidden" id="edit-form-<?= htmlspecialchars($std['Stu_id']) ?>">
                                        <input type="hidden" name="edit_mode" value="1">
                                        <input type="hidden" name="Stu_id[]" value="<?= htmlspecialchars($std['Stu_id']) ?>">
                                        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
                                        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
                                        <input type="hidden" name="date" value="<?= htmlspecialchars($date_thai) ?>">
                                        <input type="hidden" name="teach_id[<?= htmlspecialchars($std['Stu_id']) ?>]" value="<?= htmlspecialchars($_SESSION['Teacher_login'] ?? '') ?>">
                                        <div class="flex flex-wrap gap-2 mb-1 justify-center attendance-radio">
                                            <?php
                                            $status_options = [
                                                '1' => ['✅ มา', 'bg-green-100 text-green-700 peer-checked:bg-green-500 peer-checked:text-white shadow'],
                                                '2' => ['❌ ขาด', 'bg-red-100 text-red-700 peer-checked:bg-red-500 peer-checked:text-white shadow'],
                                                '3' => ['🕒 สาย', 'bg-yellow-100 text-yellow-700 peer-checked:bg-yellow-500 peer-checked:text-white shadow'],
                                                '4' => ['🤒 ป่วย', 'bg-blue-100 text-blue-700 peer-checked:bg-blue-500 peer-checked:text-white shadow'],
                                                '5' => ['📝 กิจ', 'bg-purple-100 text-purple-700 peer-checked:bg-purple-500 peer-checked:text-white shadow'],
                                                '6' => ['🎉 กิจกรรม', 'bg-pink-100 text-pink-700 peer-checked:bg-pink-500 peer-checked:text-white shadow'],
                                            ];
                                            foreach ($status_options as $val => [$label, $cls]) {
                                                ?>
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                        name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]"
                                                        value="<?= $val ?>"
                                                        class="hidden peer"
                                                        <?= $std['attendance_status'] == $val ? 'checked' : '' ?>>
                                                    <span class="px-2 py-1 rounded <?= $cls ?>">
                                                        <?= $label ?>
                                                    </span>
                                                </label>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <input type="text" name="reason[<?= htmlspecialchars($std['Stu_id']) ?>]" placeholder="สาเหตุ (ถ้ามี)" class="border rounded px-2 py-1 mb-1" value="<?= htmlspecialchars($std['reason'] ?? '') ?>" />
                                        <!-- สำหรับบันทึก behavior กรณีมาสาย -->
                                        <input type="hidden" name="behavior_type[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                        <input type="hidden" name="behavior_name[<?= htmlspecialchars($std['Stu_id']) ?>]" value="มาโรงเรียนสาย">
                                        <input type="hidden" name="behavior_score[<?= htmlspecialchars($std['Stu_id']) ?>]" value="5">
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 ml-2">บันทึก</button>
                                        <button type="button" class="cancel-edit-btn btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2">ยกเลิก</button>
                                    </form>
                                    <?php
                                } else {
                                    // radio group: name="attendance_status[Stu_id]"
                                    ?>
                                    <div class="flex flex-wrap gap-2 mb-1 justify-center attendance-radio">
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
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-700 peer-checked:bg-green-500 peer-checked:text-white shadow">
                                            ✅ มา
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="2" 
                                            class="hidden peer">
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-700 peer-checked:bg-red-500 peer-checked:text-white shadow">
                                            ❌ ขาด
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="3" 
                                            class="hidden peer">
                                        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 peer-checked:bg-yellow-500 peer-checked:text-white shadow">
                                            🕒 สาย
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="4" 
                                            class="hidden peer">
                                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 peer-checked:bg-blue-500 peer-checked:text-white shadow">
                                            🤒 ป่วย
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="5" 
                                            class="hidden peer">
                                        <span class="px-2 py-1 rounded bg-purple-100 text-purple-700 peer-checked:bg-purple-500 peer-checked:text-white shadow">
                                            📝 กิจ
                                        </span>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                            name="attendance_status[<?= htmlspecialchars($std['Stu_id']) ?>]" 
                                            value="6" 
                                            class="hidden peer">
                                        <span class="px-2 py-1 rounded bg-pink-100 text-pink-700 peer-checked:bg-pink-500 peer-checked:text-white shadow">
                                            🎉 กิจกรรม
                                        </span>
                                    </label>
                                </div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="px-3 py-2 border text-center">
                                <?php
                                // สาเหตุ
                                if (!empty($std['attendance_status'])) {
                                    echo !empty($std['reason']) ? htmlspecialchars($std['reason']) : '-';
                                } else {
                                    ?>
                                    <input type="text" name="reason[<?= htmlspecialchars($std['Stu_id']) ?>]" placeholder="สาเหตุ (ถ้ามี)" class="border rounded px-2 py-1 mb-1" />
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="px-3 py-2 border text-center">
                                <?php
                                // เช็คจาก
                                if (!empty($std['checked_by'])) {
                                    if ($std['checked_by'] === 'system') {
                                        echo '<span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded">ครูที่ปรึกษา</span>';
                                    } elseif ($std['checked_by'] === 'rfid') {
                                        $time = !empty($std['attendance_time']) ? date('H:i', strtotime($std['attendance_time'])) : null;
                                        echo '<span class="inline-block px-2 py-1 bg-amber-100 text-gray-700 rounded">scan card';
                                        if ($time !== null) {
                                            echo ' <span class="text-xs text-gray-700 text-bold">เวลา: ' . htmlspecialchars($time) . ' น.</span>';
                                        }
                                        echo '</span>';
                                    } else {
                                        echo htmlspecialchars($std['checked_by']);
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">ไม่พบนักเรียน</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if (!empty($students)): ?>
            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">บันทึกการเช็คชื่อทั้งห้อง</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSS rule `.edit-attendance-form { display: none !important; }` should handle initial hiding.
    // No need for explicit JS hiding here if the CSS rule is effective.

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
});
</script>
