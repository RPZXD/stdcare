<?php
// เปลี่ยน path ให้ถูกต้อง (จาก ../../ เป็น ../)
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

$group = $_GET['group'] ?? '';
$type = $_GET['type'] ?? 'all';
$term = $_GET['term'] ?? '1';
$pee = $_GET['pee'] ?? '2567';
$level = $_GET['level'] ?? '';
$class = $_GET['class'] ?? '';


$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);

function buildTableRows($students) {
    $html = '';
    $i = 1;
    foreach ($students as $stu) {

        $score = intval($stu['behavior_count']);
        $barColor = 'bg-green-500';
        if ($score > 50) $barColor = 'bg-red-500';
        else if ($score >= 30 && $score <= 50) $barColor = 'bg-yellow-400';
        $html .= '<tr class="border-b hover:bg-pink-50 transition">';
        $html .= '<td class="py-2 px-4 text-center">'.$i++.'</td>';
        $html .= '<td class="py-2 px-4 text-center">'.$stu['Stu_id'].'</td>';
        $html .= '<td class="py-2 px-4">'.$stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur'].'</td>';
        $html .= '<td class="py-2 px-4 text-center">ม.'.$stu['Stu_major'].'/'.$stu['Stu_room'].'</td>';
        $html .= '<td class="py-2 px-4 text-center">'.$stu['Stu_no'].'</td>';
        $html .= '<td class="py-2 px-4 text-center text-red-600 font-semibold">'.$stu['behavior_count'].' ✂️</td>';
        $html .= '<td class="py-2 px-4"><div class="w-32 bg-gray-200 rounded-full h-4 overflow-hidden"><div class="'.$barColor.' h-4 rounded-full transition-all" style="width: '.$score.'%;"></div></div><div class="text-xs text-gray-600 mt-1 text-center">'.$score.' / 100</div></td>';
        $html .= '</tr>';
    }
    return $html;
}

$html = '';
if ($type === 'all') {
    // รวมข้อมูลทุกกลุ่ม (1,2,3)
    $allStudents = [];
    for ($g = 1; $g <= 3; $g++) {
        $students = $behavior->getScoreBehaviorsGroup($g, $term, $pee);

        if ($students && is_array($students)) {
            $allStudents = array_merge($allStudents, $students);
        }
    }
    // sort by ชั้น/เลขที่
    usort($allStudents, function($a, $b) {
        if ($a['Stu_major'] != $b['Stu_major']) return $a['Stu_major'] - $b['Stu_major'];
        if ($a['Stu_room'] != $b['Stu_room']) return $a['Stu_room'] - $b['Stu_room'];
        return $a['Stu_no'] - $b['Stu_no'];
    });

    if (empty($allStudents)) {
        $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
    }
} elseif ($type === 'level') {
    $students = $behavior->getScoreBehaviorsGroup($group, $term, $pee);

    if (!$students) $students = [];
    // filter เฉพาะช่วงชั้นที่เลือก
    if ($level === 'lower') {
        $students = array_filter($students, fn($s) => intval($s['Stu_major']) >= 1 && intval($s['Stu_major']) <= 3);

        $html = buildTableRows($students);
        if (empty($students)) {
            $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
        }
    } else if ($level === 'upper') {
        $students = array_filter($students, fn($s) => intval($s['Stu_major']) >= 4 && intval($s['Stu_major']) <= 6);

        $html = buildTableRows($students);
        if (empty($students)) {
            $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
        }
    } else {
        // เดิม: แสดงทั้ง lower/upper
        $lower = array_filter($students, fn($s) => intval($s['Stu_major']) >= 1 && intval($s['Stu_major']) <= 3);
        $upper = array_filter($students, fn($s) => intval($s['Stu_major']) >= 4 && intval($s['Stu_major']) <= 6);
        $html .= '<tr><td colspan="7" class="bg-blue-50 font-bold text-blue-700 text-center">ช่วงชั้น ม.ต้น</td></tr>';
        $html .= buildTableRows($lower);
        $html .= '<tr><td colspan="7" class="bg-purple-50 font-bold text-purple-700 text-center">ช่วงชั้น ม.ปลาย</td></tr>';
        $html .= buildTableRows($upper);
        if (empty($lower) && empty($upper)) {
            $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
        }
    }
} elseif ($type === 'class') {
    $students = $behavior->getScoreBehaviorsGroup($group, $term, $pee);

    if (!$students) $students = [];
    if ($class) {
        $classStudents = array_filter($students, fn($s) => intval($s['Stu_major']) === intval($class));

        if (empty($classStudents)) {
            $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
        }
    } else {
        // เดิม: แสดงทุกชั้น
        $found = false;
        for ($m = 1; $m <= 6; $m++) {
            $classArr = array_filter($students, fn($s) => intval($s['Stu_major']) === $m);
            $html .= '<tr><td colspan="7" class="bg-green-50 font-bold text-green-700 text-center">ระดับชั้น ม.'.$m.'</td></tr>';
            $html .= buildTableRows($classArr);
            if (!empty($classArr)) $found = true;
        }
        if (!$found) {
            $html = '<tr><td colspan="7" class="py-4 text-center text-gray-500">ไม่พบข้อมูล</td></tr>';
        }
    }
}
echo json_encode(['success'=>true, 'html'=>$html]);
