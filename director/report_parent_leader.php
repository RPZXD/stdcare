<?php
include_once("../class/BoardParent.php");
$boardParent = new BoardParent($db);
$pee = $user->getPee();

$levels = [];
try {
    $sql = "SELECT DISTINCT parn_lev FROM tb_parnet WHERE parn_pee = :pee AND parn_lev IS NOT NULL AND parn_lev != '' ORDER BY parn_lev";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
    $stmt->execute();
    $levels = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $levels = [];
}

$selected_level = isset($_GET['level']) ? $_GET['level'] : '';
?>

<!-- Report: Parent Leaders -->
<div class="space-y-6">
    <!-- Header & Filter -->
    <div class="glass-effect rounded-2xl p-6 border border-white/50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">รายงานคณะกรรมการเครือข่ายผู้ปกครอง</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <select name="level" id="level" class="pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-purple-500/20 outline-none transition-all appearance-none cursor-pointer min-w-[160px]">
                        <option value="">ทุกระดับชั้น</option>
                        <?php foreach ($levels as $lev): ?>
                            <option value="<?= htmlspecialchars($lev) ?>">ม.<?= htmlspecialchars($lev) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                    </div>
                </div>
                <button type="button" onclick="printTable()" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
            </div>
        </div>
    </div>

    <!-- Print Header -->
    <div id="print-header" class="hidden text-center mb-4">
        <h3 class="text-xl font-black text-slate-800">รายงานคณะกรรมการเครือข่ายผู้ปกครอง</h3>
        <p id="print-pee" class="text-slate-600">ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
        <p id="print-level" class="text-slate-400"></p>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table id="parent-leader-table" class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="bg-purple-50/50 dark:bg-slate-800/50">
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center">ลำดับ</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ระดับชั้น/ห้อง</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ตำแหน่ง</th>
                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">เบอร์โทรศัพท์</th>
                </tr>
            </thead>
            <tbody id="parent-leader-tbody" class="font-bold text-slate-700 dark:text-slate-300">
                <tr><td colspan="5" class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลดข้อมูล...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden !important; }
    #parent-leader-table, #parent-leader-table *, #print-header, #print-header * { visibility: visible !important; }
    #parent-leader-table { position: absolute; left: 50%; transform: translateX(-50%); top: 80px; }
    #print-header { position: absolute; left: 0; right: 0; top: 20px; visibility: visible !important; display: block !important; }
}
</style>

<script>
function renderTable(data, level) {
    const tbody = document.getElementById('parent-leader-tbody');
    const printLevel = document.getElementById('print-level');
    printLevel.innerHTML = level ? `มัธยมศึกษาปีที่ ${level}` : 'ทุกระดับชั้น';
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-slate-400 italic"><i class="fas fa-users text-3xl mb-3 opacity-30 block"></i>ไม่พบข้อมูลประธานเครือข่ายผู้ปกครอง</td></tr>`;
        return;
    }
    
    let html = '';
    data.forEach((row, i) => {
        html += `<tr class="bg-white dark:bg-slate-800/50 hover:bg-purple-50 dark:hover:bg-slate-700/50 transition-all">
            <td class="px-4 py-3 text-center rounded-l-xl">${i + 1}</td>
            <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">${row.parn_name || '-'}</td>
            <td class="px-4 py-3 text-center"><span class="px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg text-xs font-bold">ม.${row.parn_lev || '-'}/${row.parn_room || '-'}</span></td>
            <td class="px-4 py-3 text-center"><span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full text-xs font-black">ประธาน</span></td>
            <td class="px-4 py-3 text-center rounded-r-xl">${row.parn_tel ? `<a href="tel:${row.parn_tel}" class="text-emerald-600 hover:underline font-bold">${row.parn_tel}</a>` : '-'}</td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

function fetchLeaders(level = '') {
    const params = new URLSearchParams();
    params.append('pee', '<?= htmlspecialchars($pee) ?>');
    if (level) params.append('level', level);
    
    fetch('api/api_parent_leader.php?' + params.toString())
        .then(res => res.json())
        .then(json => {
            renderTable(json.data, level);
        })
        .catch(() => {
            renderTable([], level);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    fetchLeaders(levelSelect.value);
    levelSelect.addEventListener('change', function() {
        fetchLeaders(this.value);
    });
});

function printTable() {
    window.print();
}
</script>
