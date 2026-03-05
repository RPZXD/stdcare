<?php
/**
 * Screen 11 Report All - View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
ob_start();

// Prepare choice map for table rendering
$choiceMap = [
    'ปกติ' => ['ปกติ'],
    'เสี่ยง' => ['เสี่ยง'],
    'มีปัญหา' => ['มีปัญหา'],
    'มี' => ['มี'],
    'ไม่มี' => ['ไม่มี'],
];
?>

<!-- Page Header -->
<div class="mb-6 md:mb-8 no-print">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-violet-500/30">
                    <i class="fas fa-search-plus"></i>
                </div>
                สรุปสถิติคัดกรอง 11 ด้าน
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm md:text-base">
                ระดับชั้น ม.
                <?= $class ?>/
                <?= $room ?> ปีการศึกษา
                <?= $pee ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="screen11.php"
                class="px-4 py-2.5 bg-gradient-to-r from-slate-500 to-slate-600 text-white rounded-xl font-bold shadow-lg shadow-slate-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i>
                กลับหน้าคัดกรอง
            </a>
            <button onclick="printReport()"
                class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/25 hover:-translate-y-0.5 transition flex items-center gap-2 text-sm">
                <i class="fas fa-print"></i>
                พิมพ์รายงาน
            </button>
        </div>
    </div>
</div>

<!-- Summary Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">นักเรียนทั้งหมด</p>
                <p class="text-xl font-black text-slate-800 dark:text-white">
                    <?= $total_students ?>
                </p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">คัดกรองแล้ว</p>
                <p class="text-xl font-black text-emerald-600">
                    <?= $screened_count ?>
                </p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รอคัดกรอง</p>
                <p class="text-xl font-black text-amber-600">
                    <?= $total_students - $screened_count ?>
                </p>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-violet-100 dark:bg-violet-900/30 text-violet-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">สำเร็จ</p>
                <p class="text-xl font-black text-violet-600">
                    <?= $total_students > 0 ? round(($screened_count / $total_students) * 100) : 0 ?>%
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 no-print">
    <!-- Bar Chart -->
    <div class="glass-card rounded-[2rem] p-6 border border-white/50 dark:border-slate-700/50 shadow-xl">
        <h3 class="text-lg font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-violet-500"></i>
            กราฟแท่งสรุปการคัดกรอง
        </h3>
        <div class="h-72">
            <canvas id="screenBarChart"></canvas>
        </div>
    </div>
    <!-- Radar Chart -->
    <div class="glass-card rounded-[2rem] p-6 border border-white/50 dark:border-slate-700/50 shadow-xl">
        <h3 class="text-lg font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-spider text-purple-500"></i>
            กราฟเรดาร์ภาพรวม
        </h3>
        <div class="h-72">
            <canvas id="screenRadarChart"></canvas>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div
    class="glass-card rounded-[2rem] shadow-xl border border-white/50 dark:border-slate-700/50 overflow-hidden no-print">
    <!-- Card Header -->
    <div
        class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-violet-500 to-purple-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                    <i class="fas fa-table text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white">ตารางสรุปผลการคัดกรองนักเรียน 11 ด้าน</h2>
                    <p class="text-white/70 text-sm italic">ปีการศึกษา
                        <?= $pee ?> | ม.
                        <?= $class ?>/
                        <?= $room ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="screenTable">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest"
                        rowspan="2" style="min-width:260px;">การคัดกรอง</th>
                    <th class="px-2 py-2 text-center text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 rounded-tl-xl"
                        colspan="4">
                        <i class="fas fa-smile mr-1"></i>ปกติ
                    </th>
                    <th class="px-2 py-2 text-center text-[10px] font-black text-amber-600 uppercase tracking-widest bg-amber-50 dark:bg-amber-900/20"
                        colspan="4">
                        <i class="fas fa-exclamation-triangle mr-1"></i>เสี่ยง
                    </th>
                    <th class="px-2 py-2 text-center text-[10px] font-black text-rose-600 uppercase tracking-widest bg-rose-50 dark:bg-rose-900/20"
                        colspan="4">
                        <i class="fas fa-times-circle mr-1"></i>มีปัญหา
                    </th>
                    <th class="px-2 py-2 text-center text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/20"
                        colspan="4">
                        <i class="fas fa-check mr-1"></i>มี
                    </th>
                    <th class="px-2 py-2 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-100 dark:bg-slate-700/50 rounded-tr-xl"
                        colspan="4">
                        <i class="fas fa-minus-circle mr-1"></i>ไม่มี
                    </th>
                </tr>
                <tr class="bg-slate-50/50 dark:bg-slate-800/30">
                    <?php
                    $subHeaders = [
                        ['bg' => 'emerald', 'labels' => ['ชาย', 'หญิง', 'รวม', '%']],
                        ['bg' => 'amber', 'labels' => ['ชาย', 'หญิง', 'รวม', '%']],
                        ['bg' => 'rose', 'labels' => ['ชาย', 'หญิง', 'รวม', '%']],
                        ['bg' => 'blue', 'labels' => ['ชาย', 'หญิง', 'รวม', '%']],
                        ['bg' => 'slate', 'labels' => ['ชาย', 'หญิง', 'รวม', '%']],
                    ];
                    foreach ($subHeaders as $sh):
                        foreach ($sh['labels'] as $lb):
                            ?>
                            <th
                                class="px-2 py-2 text-center text-[9px] font-bold text-<?= $sh['bg'] ?>-500 uppercase tracking-wider">
                                <?= $lb ?>
                            </th>
                        <?php endforeach; endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                <?php foreach ($screenFields as $idx => $field):
                    $row = [];
                    foreach (['ปกติ', 'เสี่ยง', 'มีปัญหา', 'มี', 'ไม่มี'] as $group) {
                        $choice = $choiceMap[$group][0];
                        if (in_array($choice, $field['choices'])) {
                            $row[$group] = $summary[$field['key']][$choice];
                        } else {
                            $row[$group] = ['male' => '', 'female' => '', 'total' => '', 'percent' => ''];
                        }
                    }
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors fade-in-up"
                        style="animation-delay: <?= $idx * 0.05 ?>s">
                        <td class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                            <?= $field['label'] ?>
                        </td>
                        <?php
                        $groups = ['ปกติ', 'เสี่ยง', 'มีปัญหา', 'มี', 'ไม่มี'];
                        $groupColors = ['emerald', 'amber', 'rose', 'blue', 'slate'];
                        foreach ($groups as $gi => $group):
                            $d = $row[$group];
                            $gc = $groupColors[$gi];
                            $hasData = ($d['total'] !== '');
                            ?>
                            <td
                                class="px-2 py-3 text-center text-xs <?= $hasData ? "text-{$gc}-600 font-semibold" : 'text-slate-300' ?>">
                                <?= $hasData ? $d['male'] : '-' ?>
                            </td>
                            <td
                                class="px-2 py-3 text-center text-xs <?= $hasData ? "text-{$gc}-600 font-semibold" : 'text-slate-300' ?>">
                                <?= $hasData ? $d['female'] : '-' ?>
                            </td>
                            <td
                                class="px-2 py-3 text-center text-xs font-black <?= $hasData ? "text-{$gc}-700" : 'text-slate-300' ?>">
                                <?= $hasData ? $d['total'] : '-' ?>
                            </td>
                            <td class="px-2 py-3 text-center text-xs">
                                <?php if ($hasData && $d['percent'] > 0): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-<?= $gc ?>-100 dark:bg-<?= $gc ?>-900/30 text-<?= $gc ?>-600 text-[10px] font-bold rounded-full">
                                        <?= $d['percent'] ?>%
                                    </span>
                                <?php elseif ($hasData): ?>
                                    <span class="text-slate-400 text-[10px]">0%</span>
                                <?php else: ?>
                                    <span class="text-slate-300">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards (shown on small screens only) -->
<div class="md:hidden space-y-4 mt-6 no-print">
    <?php foreach ($screenFields as $idx => $field):
        $row = [];
        foreach (['ปกติ', 'เสี่ยง', 'มีปัญหา', 'มี', 'ไม่มี'] as $group) {
            $choice = $choiceMap[$group][0];
            if (in_array($choice, $field['choices'])) {
                $row[$group] = $summary[$field['key']][$choice];
            } else {
                $row[$group] = null;
            }
        }
        ?>
        <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 fade-in-up"
            style="animation-delay: <?= $idx * 0.05 ?>s">
            <h4 class="font-bold text-slate-800 dark:text-white text-sm mb-3">
                <?= $field['label'] ?>
            </h4>
            <div class="grid grid-cols-3 gap-2">
                <?php
                $mobileGroups = in_array('มี', $field['choices'])
                    ? [['มี', 'blue', 'fas fa-check'], ['ไม่มี', 'slate', 'fas fa-minus']]
                    : [['ปกติ', 'emerald', 'fas fa-smile'], ['เสี่ยง', 'amber', 'fas fa-exclamation-triangle'], ['มีปัญหา', 'rose', 'fas fa-times-circle']];
                foreach ($mobileGroups as $mg):
                    $d = $row[$mg[0]];
                    if ($d === null)
                        continue;
                    ?>
                    <div class="bg-<?= $mg[1] ?>-50 dark:bg-<?= $mg[1] ?>-900/20 rounded-xl p-2 text-center">
                        <p class="text-[10px] font-bold text-<?= $mg[1] ?>-600 mb-1"><i class="<?= $mg[2] ?> mr-1"></i>
                            <?= $mg[0] ?>
                        </p>
                        <p class="text-lg font-black text-<?= $mg[1] ?>-700">
                            <?= $d['total'] ?>
                        </p>
                        <p class="text-[10px] text-<?= $mg[1] ?>-500">
                            <?= $d['percent'] ?>%
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Print Header (Hidden on Screen) -->
<div id="printHeader" class="hidden print:block">
    <h2 style="font-size:14px; font-weight:bold; text-align:center; margin:0 0 2px 0;">สรุปสถิติการคัดกรองนักเรียน 11
        ด้าน โรงเรียนพิชัย</h2>
    <p style="text-align:center; font-size:11px; margin:0 0 2px 0;">
        ระดับชั้น ม.<?= $class ?>/<?= $room ?> ปีการศึกษา <?= $pee ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        ครูที่ปรึกษา
        <?php foreach ($roomTeachers as $t): ?>
            <?= $t['Teach_name'] ?>&nbsp;&nbsp;
        <?php endforeach; ?>
    </p>
    <p style="text-align:center; font-size:10px; margin:0 0 4px 0; color:#666;">
        นักเรียนทั้งหมด: <?= $total_students ?> คน &nbsp;|&nbsp;
        คัดกรองแล้ว: <?= $screened_count ?> คน &nbsp;|&nbsp;
        รอคัดกรอง: <?= $total_students - $screened_count ?> คน
    </p>
</div>

<!-- Print Table (Hidden on Screen) -->
<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse text-xs" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center" rowspan="2" style="width:30%;">การคัดกรอง</th>
                <th class="border border-slate-300 px-1 py-1 text-center bg-emerald-50" colspan="4">ปกติ</th>
                <th class="border border-slate-300 px-1 py-1 text-center bg-amber-50" colspan="4">เสี่ยง</th>
                <th class="border border-slate-300 px-1 py-1 text-center bg-rose-50" colspan="4">มีปัญหา</th>
                <th class="border border-slate-300 px-1 py-1 text-center bg-blue-50" colspan="4">มี</th>
                <th class="border border-slate-300 px-1 py-1 text-center bg-slate-50" colspan="4">ไม่มี</th>
            </tr>
            <tr class="bg-slate-50">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <th class="border border-slate-300 px-1 py-1 text-center text-[9px]">ช</th>
                    <th class="border border-slate-300 px-1 py-1 text-center text-[9px]">ญ</th>
                    <th class="border border-slate-300 px-1 py-1 text-center text-[9px]">รวม</th>
                    <th class="border border-slate-300 px-1 py-1 text-center text-[9px]">%</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($screenFields as $field):
                $row = [];
                foreach (['ปกติ', 'เสี่ยง', 'มีปัญหา', 'มี', 'ไม่มี'] as $group) {
                    $choice = $choiceMap[$group][0];
                    if (in_array($choice, $field['choices'])) {
                        $row[$group] = $summary[$field['key']][$choice];
                    } else {
                        $row[$group] = ['male' => '', 'female' => '', 'total' => '', 'percent' => ''];
                    }
                }
                ?>
                <tr>
                    <td class="border border-slate-300 px-2 py-1 text-left font-semibold">
                        <?= $field['label'] ?>
                    </td>
                    <?php foreach (['ปกติ', 'เสี่ยง', 'มีปัญหา', 'มี', 'ไม่มี'] as $group):
                        $d = $row[$group];
                        ?>
                        <td class="border border-slate-300 px-1 py-1 text-center">
                            <?= $d['male'] ?>
                        </td>
                        <td class="border border-slate-300 px-1 py-1 text-center">
                            <?= $d['female'] ?>
                        </td>
                        <td class="border border-slate-300 px-1 py-1 text-center font-bold">
                            <?= $d['total'] ?>
                        </td>
                        <td class="border border-slate-300 px-1 py-1 text-center">
                            <?= $d['percent'] !== '' ? $d['percent'] . '%' : '' ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Print Signature Section (Hidden on Screen) -->
<div id="printSignature" class="hidden print:block" style="margin-top:12px;">
    <div style="display:flex; justify-content:center; gap:80px;">
        <?php foreach ($roomTeachers as $t): ?>
            <div style="text-align:center;">
                <p style="margin-bottom:4px;">ลงชื่อ...........................................</p>
                <p style="font-weight:bold; margin:0;">(<?= $t['Teach_name'] ?>)</p>
                <p style="font-size:10px; color:#666; margin:0;">ครูที่ปรึกษา</p>
            </div>
        <?php endforeach; ?>
    </div>
    <p style="text-align:center; font-size:8px; color:#999; margin-top:6px;">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Print Styles */
    @media print {
        @page {
            size: A4 landscape;
            margin: 4mm;
        }

        body {
            background: white !important;
            font-family: 'Mali', sans-serif !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 9px;
        }

        .no-print,
        #sidebar,
        #sidebar-overlay,
        #navbar,
        #preloader,
        footer,
        nav {
            display: none !important;
        }

        .flex.min-h-screen {
            display: block !important;
        }

        .flex-1.flex.flex-col,
        .lg\:ml-64 {
            margin-left: 0 !important;
        }

        main {
            padding: 2mm !important;
            margin: 0 !important;
        }

        main>div {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        #printHeader,
        #printTable,
        #printSignature {
            display: block !important;
        }

        #printTableContent {
            font-size: 9px;
        }

        #printTableContent th,
        #printTableContent td {
            padding: 2px 3px;
            border: 1px solid #cbd5e1;
        }

        #printTableContent th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    /* Screen only - hide print elements */
    @media screen {

        #printHeader,
        #printTable,
        #printSignature {
            display: none !important;
        }
    }

    /* Hide mobile cards on desktop table view */
    @media (min-width: 768px) {
        .md\:hidden {
            display: none !important;
        }
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from PHP
    const chartData = {
        labels: [],
        normal: [],
        risk: [],
        problem: []
    };

    <?php
    // Prepare chart data for fields with ปกติ/เสี่ยง/มีปัญหา choices
    $chartLabels = [];
    $chartNormal = [];
    $chartRisk = [];
    $chartProblem = [];

    foreach ($screenFields as $field) {
        if (in_array('ปกติ', $field['choices'])) {
            $shortLabel = preg_replace('/^\d+\.\s*/', '', $field['label']);
            // Shorten long labels
            $shortLabel = mb_substr($shortLabel, 0, 15);
            $chartLabels[] = $shortLabel;
            $chartNormal[] = $summary[$field['key']]['ปกติ']['total'];
            $chartRisk[] = $summary[$field['key']]['เสี่ยง']['total'];
            $chartProblem[] = $summary[$field['key']]['มีปัญหา']['total'];
        }
    }
    ?>

    chartData.labels = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>;
    chartData.normal = <?= json_encode($chartNormal) ?>;
    chartData.risk = <?= json_encode($chartRisk) ?>;
    chartData.problem = <?= json_encode($chartProblem) ?>;

    document.addEventListener('DOMContentLoaded', function () {
        renderBarChart();
        renderRadarChart();
    });

    function renderBarChart() {
        const ctx = document.getElementById('screenBarChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'ปกติ',
                        data: chartData.normal,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'เสี่ยง',
                        data: chartData.risk,
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'มีปัญหา',
                        data: chartData.problem,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Mali', weight: 'bold', size: 11 },
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: { family: 'Mali', size: 9 },
                            maxRotation: 45,
                            minRotation: 30
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Mali' }
                        }
                    }
                }
            }
        });
    }

    function renderRadarChart() {
        const ctx = document.getElementById('screenRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'ปกติ',
                        data: chartData.normal,
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderColor: 'rgba(16, 185, 129, 0.8)',
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'เสี่ยง',
                        data: chartData.risk,
                        backgroundColor: 'rgba(245, 158, 11, 0.15)',
                        borderColor: 'rgba(245, 158, 11, 0.8)',
                        pointBackgroundColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'มีปัญหา',
                        data: chartData.problem,
                        backgroundColor: 'rgba(239, 68, 68, 0.15)',
                        borderColor: 'rgba(239, 68, 68, 0.8)',
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Mali', weight: 'bold', size: 11 },
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            font: { family: 'Mali', size: 9 },
                            backdropColor: 'transparent'
                        },
                        pointLabels: {
                            font: { family: 'Mali', size: 9, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

    function printReport() {
        window.print();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>