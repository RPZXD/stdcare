<?php
/**
 * Sub-View: Screening 11 Overall School Report (Officer)
 * Modern UI with Tailwind CSS & Responsive Design
 * Included in officer/report.php
 */
include_once("../config/Database.php");
include_once("../class/Screeningdata.php");
require_once("../class/Utils.php");
require_once("../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-filter"></i>
                </span>
                สรุปผลการคัดกรอง <span class="text-indigo-600 italic">11 ด้าน</span> (ภาพรวมโรงเรียน)
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">School-wide Student Screening Summary • <?= htmlspecialchars($pee) ?></p>
        </div>
        
        <div class="flex gap-2 no-print">
            <button onclick="loadScreenSchoolTable()" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> รีเฟรชข้อมูล
            </button>
        </div>
    </div>

    <!-- Content Container -->
    <div id="screen-table-container" class="space-y-10">
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-500 italic mt-4">กำลังประมวลผลสถิติภาพรวม...</p>
        </div>
    </div>
</div>

<script>
function loadScreenSchoolTable() {
    const $container = $('#screen-table-container');
    $container.html(`
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-500 italic mt-4">กำลังประมวลผลสถิติภาพรวม...</p>
        </div>
    `);
    
    fetch('api/ajax_screen_school_table.php')
        .then(res => res.text())
        .then(html => {
            $container.hide().html(html).fadeIn(500);
            if (typeof updateMobileLabels === 'function') updateMobileLabels();
        });
}

$(document).ready(function() {
    loadScreenSchoolTable();
});
</script>
