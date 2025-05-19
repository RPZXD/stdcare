<?php
// รายงานสรุปภาพรวมห้องเรียนสีขาว (สำหรับเจ้าหน้าที่)
require_once("../config/Database.php");
require_once("../class/Wroom.php");
require_once("../class/Teacher.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$wroomObj = new Wroom($db);
$teacherObj = new Teacher($db);

// ดึงปีการศึกษาล่าสุด
$pee = date('Y') + 543;

// ดึงรายชื่อห้องทั้งหมด
$rooms = [];
$stmt = $db->query("SELECT Stu_major, Stu_room FROM student WHERE Stu_status=1 GROUP BY Stu_major, Stu_room ORDER BY Stu_major, Stu_room");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rooms[] = $row;
}

echo '<h2 class="text-lg font-bold mb-4">รายงานสรุปห้องเรียนสีขาว (ภาพรวม)</h2>';
echo '<div class="overflow-x-auto">';
echo '<table class="min-w-full border text-sm">';
echo '<thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1">#</th>
            <th class="border px-2 py-1">ห้อง</th>
            <th class="border px-2 py-1">ครูที่ปรึกษา</th>
            <th class="border px-2 py-1">จำนวนกรรมการ</th>
            <th class="border px-2 py-1">คติพจน์</th>
            <th class="border px-2 py-1">สถานะ</th>
        </tr>
      </thead><tbody>';
$i = 1;
foreach($rooms as $r) {
    $class = $r['Stu_major'];
    $room = $r['Stu_room'];
    $advisors = $teacherObj->getTeachersByClassAndRoom($class, $room);
    $advisorsStr = implode(', ', array_map(fn($a) => $a['Teach_name'], $advisors));
    $wroom = $wroomObj->getWroomStudents($class, $room, $pee);
    $maxim = $wroomObj->getMaxim($class, $room, $pee);
    $committeeCount = count(array_filter($wroom, fn($w) => $w['wposit'] != ''));
    $status = ($committeeCount >= 18 && $maxim) ? '<span class="text-green-600">ครบถ้วน</span>' : '<span class="text-red-600">ไม่ครบ</span>';
    echo "<tr>
        <td class='border px-2 py-1 text-center'>{$i}</td>
        <td class='border px-2 py-1 text-center'>ม.{$class}/{$room}</td>
        <td class='border px-2 py-1'>{$advisorsStr}</td>
        <td class='border px-2 py-1 text-center'>{$committeeCount}</td>
        <td class='border px-2 py-1'>".($maxim ? '✔️' : '-')."</td>
        <td class='border px-2 py-1 text-center'>{$status}</td>
    </tr>";
    $i++;
}
echo '</tbody></table></div>';
echo '<div class="mt-4 text-gray-500">* หมายเหตุ: ครบถ้วน = มีกรรมการครบ 18 คน และกรอกคติพจน์</div>';
?>
