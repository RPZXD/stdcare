<?php
/**
 * Get Visit Data API
 * Returns visit home data for a student
 */
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

$term = $_GET['term'] ?? '';
$pee = $_GET['pee'] ?? '';
$stuId = $_GET['stuId'] ?? '';

if (empty($term) || empty($pee) || empty($stuId)) {
    echo '
    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-6 text-center">
        <span class="text-4xl mb-4 block">⚠️</span>
        <h4 class="font-bold text-amber-800 mb-2">ข้อมูลไม่ครบถ้วน</h4>
        <p class="text-sm text-amber-700">กรุณาระบุข้อมูลให้ครบถ้วน</p>
    </div>';
    exit;
}

$data = $visitHome->getVisitData($stuId, $term, $pee);

if ($data) {
    include 'edit_visit_form.php';
} else {
    echo '
    <div class="bg-slate-50 rounded-2xl p-8 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
            <span class="text-4xl">🏠</span>
        </div>
        <h4 class="font-bold text-slate-800 text-lg mb-2">ไม่พบข้อมูลการเยี่ยมบ้าน</h4>
        <p class="text-sm text-slate-500 mb-4">ยังไม่มีข้อมูลการเยี่ยมบ้านของนักเรียนคนนี้ในภาคเรียนนี้</p>
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-100 text-cyan-700 text-sm font-semibold rounded-lg">
            <i class="fas fa-info-circle"></i>
            กรุณาเพิ่มข้อมูลการเยี่ยมบ้านก่อน
        </div>
    </div>';
}
?>