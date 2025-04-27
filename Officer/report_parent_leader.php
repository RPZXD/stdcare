<?php
include_once("../class/BoardParent.php");
// ใช้ $db, $pee จาก context หลัก (report.php)
$boardParent = new BoardParent($db);
$pee = $user->getPee();
// ดึงระดับชั้นทั้งหมด
$levels = [];
try {
    $sql = "SELECT DISTINCT parn_lev
            FROM tb_parnet
            WHERE parn_pee = :pee
            AND parn_lev IS NOT NULL
            AND parn_lev != ''
            ORDER BY parn_lev
            ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
    $stmt->execute();
    $levels = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $levels = [];
}

// รับค่าระดับชั้นที่เลือก
$selected_level = isset($_GET['level']) ? $_GET['level'] : '';
?>
<div>
    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2 justify-center print:block print:text-center print:mb-2" style="margin-bottom:0;">
        รายงานคณะกรรมการเครือผู้ปกครอง
    </h2>
    <div class="flex flex-col items-center gap-2 mb-2 print:block print:text-center print:mb-2" style="margin-bottom:0;">
        <div class="text-base print:text-base" id="print-pee">ปีการศึกษา 2567</div>
        <div id="print-level" class="text-base print:text-base"></div>
    </div>
    <form method="get" class="flex items-center gap-2 mb-2 print:hidden" id="filterForm" onsubmit="return false;">
        <input type="hidden" name="tab" value="parent-leader">
        <label for="level" class="font-semibold">เลือกระดับชั้น:</label>
        <select name="level" id="level" class="border rounded px-2 py-1">
            <option value="">-- ทุกระดับชั้น --</option>
            <?php foreach ($levels as $lev): ?>
                <option value="<?= htmlspecialchars($lev) ?>">
                    <?= htmlspecialchars($lev) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="printTable()" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 ml-2">พิมพ์</button>
    </form>
    <div class="overflow-x-auto">
        <table id="parent-leader-table" class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead>
                <tr class="bg-purple-100 text-gray-700">
                    <th class="py-2 px-3 border-b text-center">ลำดับ</th>
                    <th class="py-2 px-3 border-b text-center">ชื่อ-นามสกุล</th>
                    <th class="py-2 px-3 border-b text-center">ระดับชั้น/ห้อง</th>
                    <th class="py-2 px-3 border-b text-center">ตำแหน่ง</th>
                    <th class="py-2 px-3 border-b text-center">เบอร์โทรศัพท์</th>
                </tr>
            </thead>
            <tbody id="parent-leader-tbody">
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">กำลังโหลดข้อมูล...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden !important; }
    #parent-leader-table, #parent-leader-table * { visibility: visible !important; }
    h2, #print-level, #print-pee { visibility: visible !important; display: block !important; }
    #parent-leader-table { margin-top: 20px; }
    .print\:block { display: block !important; }
    .print\:hidden { display: none !important; }
}
</style>
<script>
function renderTable(data, level) {
    const tbody = document.getElementById('parent-leader-tbody');
    const printLevel = document.getElementById('print-level');
    printLevel.innerHTML = level ? `ของมัธยมศึกษาปีที่ ${level}` : '';
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="5" class="py-4 text-center text-gray-500">ไม่พบข้อมูลประธานเครือข่ายผู้ปกครอง</td>
        </tr>`;
        return;
    }
    let html = '';
    data.forEach((row, i) => {
        html += `<tr class="${i % 2 === 0 ? 'bg-gray-50' : ''}">
            <td class="py-2 px-3 border-b text-center">${i + 1}</td>
            <td class="py-2 px-3 border-b text-left">${row.parn_name ? row.parn_name : ''}</td>
            <td class="py-2 px-3 border-b text-center">ม.${row.parn_lev ? row.parn_lev : ''}/${row.parn_room ? row.parn_room : ''}</td>
            <td class="py-2 px-3 border-b text-center">ประธาน</td>
            <td class="py-2 px-3 border-b text-center">${row.parn_tel ? row.parn_tel : ''}</td>
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
