<?php
/**
 * Sub-View: Screening 11 Report by Class Level (Officer)
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

// ดึงชั้นเรียนทั้งหมด
$stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
$stmt->execute();
$classList = $stmt->fetchAll(PDO::FETCH_COLUMN);

$class = $_GET['class'] ?? ($classList[0] ?? '');
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg text-lg">
                    <i class="fas fa-filter"></i>
                </span>
                รายงานผลการคัดกรอง <span class="text-indigo-600 italic">11 ด้าน</span> รายระดับชั้น
            </h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic pl-13">Student Screening (11 Aspects) • By Grade Level</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-6 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">เลือกระดับชั้น</label>
                <div class="relative">
                    <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                    <select id="class-select" class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-bold text-slate-700 dark:text-white text-sm appearance-none">
                        <?php foreach ($classList as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $c == $class ? 'selected' : '' ?>>มัธยมศึกษาปีที่ <?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="md:col-span-3 md:col-start-10">
                <button onclick="window.printReport ? window.printReport() : window.print()" class="w-full py-3.5 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2 no-print">
                    <i class="fas fa-print"></i> พิมพ์รายงาน
                </button>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div id="screen-table-container" class="space-y-8">
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-500 italic mt-4">กำลังโหลดข้อมูลการคัดกรอง...</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const $classSelect = $('#class-select');
    const $container = $('#screen-table-container');

    function loadScreenTable() {
        const classVal = $classSelect.val();
        
        if (classVal) {
            $container.html(`
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-slate-500 italic mt-4">กำลังโหลดข้อมูลการคัดกรอง...</p>
                </div>
            `);
            
            fetch('api/ajax_screen_class_table.php?class=' + encodeURIComponent(classVal))
                .then(res => res.text())
                .then(html => {
                    $container.hide().html(html).fadeIn(300);
                    if (typeof updateMobileLabels === 'function') updateMobileLabels();
                });
        }
    }

    $classSelect.on('change', loadScreenTable);
    loadScreenTable();
});
</script>
