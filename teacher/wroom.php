<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacher = new Teacher($db);

$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// เพิ่มตัวแปรสำหรับ JS
echo "<script>
    const stu_major = '".addslashes($class)."';
    const stu_room = '".addslashes($room)."';
    const pee = '".addslashes($pee)."';
</script>";

require_once('header.php');
require_once('wrapper.php');
?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <!-- ...existing code for header/wrapper... -->

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"></h1>
                </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3 d-block">
                        <div class="text-base font-bold text-center mb-4">
                            🏠 แบบฟอร์มบันทึกรายชื่อคณะกรรมการห้องเรียนสีขาว ปีการศึกษา <?= $pee ?>
                            <p>
                                โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์
                            </p>
                            <p>
                                ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?> ปีการศึกษา <?= $pee ?>
                            </p>
                            <p>
                            ครูที่ปรึกษา <?php
                         
                                    $teachers = $teacher->getTeachersByClassAndRoom($class, $room);

                                            foreach ($teachers as $row) {
                                                echo $row['Teach_name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
                                            }
                            
                                    ?>
                            </p></div>
                            <div class="bg-gray-100 border border-gray-300 rounded-xl p-6 text-gray-800">
                                <h2 class="text-lg font-semibold mb-4">📌 คำชี้แจง</h2>
                                <p class="mb-4">
                                    โปรดเลือกตำแหน่งของนักเรียนในการเป็น <span class="font-medium text-blue-600">คณะกรรมการดำเนินงานห้องเรียนสีขาว</span> 
                                    โดยพิจารณาตามรายการตำแหน่งด้านล่างนี้:
                                </p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>👤 <strong>หัวหน้าห้อง</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📘 <strong>รองหัวหน้าฝ่ายการเรียน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🛠️ <strong>รองหัวหน้าฝ่ายการงาน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🎉 <strong>รองหัวหน้าฝ่ายกิจกรรม</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🚨 <strong>รองหัวหน้าฝ่ายสารวัตรนักเรียน</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📝 <strong>เลขานุการ</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>🗂️ <strong>ผู้ช่วยเลขานุการ</strong> จำนวน <span class="text-red-600 font-semibold">1 คน</span></li>
                                    <li>📚 <strong>นักเรียนแกนนำฝ่ายการเรียน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🔧 <strong>นักเรียนแกนนำฝ่ายการงาน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🎭 <strong>นักเรียนแกนนำฝ่ายกิจกรรม</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                    <li>🛡️ <strong>นักเรียนแกนนำฝ่ายสารวัตรนักเรียน</strong> จำนวน <span class="text-red-600 font-semibold">4 คน</span></li>
                                </ul>
                                <p class="mt-4">
                                    👥 นักเรียนที่ <span class="underline">ไม่ได้รับเลือก</span> ในตำแหน่งใด ๆ จะถือว่าเป็น <span class="font-medium text-blue-600">สมาชิกทั่วไป</span> 
                                    ของคณะกรรมการห้องเรียนสีขาว
                                </p>
                                <p class="mt-4">
                                    ✍️ <strong>โปรดกรอก "คติพจน์ของห้องเรียนสีขาว"</strong> ให้เรียบร้อยก่อนกดปุ่มบันทึก
                                </p>
                            </div>

                        
                            <div class="flex w-full mt-4">
                                <button type="button"
                                    class="w-[calc(50%-0.25rem)] mr-2 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600"
                                    onclick="location.href='report_wroom.php'">
                                    <i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;ดูรายชื่อคณะกรรมดำเนินงานห้องเรียนสีขาว
                                </button>

                                <button type="button"
                                    class="w-[calc(50%-0.25rem)] bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600"
                                    onclick="location.href='report_wroom2.php'">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>&nbsp;&nbsp;ดูผังโครงสร้างองค์กรห้องเรียนสีขาว
                                </button>
                            </div>

                        <!-- ตารางนักเรียนและตำแหน่ง (render ด้วย JS) -->
                        <div class="table-responsive mt-6">
                            <form id="wroomForm" class="space-y-4">
                                <table id="studentTable" class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden shadow bg-white">
                                    <thead class="bg-teal-500">
                                        <tr>
                                            <th class="px-4 py-2 text-center font-medium text-white uppercase">#</th>
                                            <th class="px-4 py-2 text-center font-medium text-white uppercase">เลขประจำตัว</th>
                                            <th class="px-4 py-2 text-center font-medium text-white uppercase">ชื่อ-นามสกุล</th>
                                            <th class="px-4 py-2 text-center font-medium text-white uppercase">ตำแหน่ง</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- JS will render rows here -->
                                    </tbody>
                                </table>
                                <br>
                                <label for="maxim" class="block font-semibold mb-2">คติพจน์ห้องเรียนสีขาว :</label>
                                <textarea class="form-control border border-gray-300 rounded-lg p-2 w-full" name="maxim" id="maxim" cols="30" rows="3"></textarea>
                                <div class="form-group">
                                    <input type="hidden" name="major" value="<?= htmlspecialchars($class) ?>">
                                    <input type="hidden" name="room" value="<?= htmlspecialchars($room) ?>">
                                    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
                                    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
                                    <button type="submit" class="btn-lg bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded w-full mt-4">บันทึกข้อมูล</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ตำแหน่ง
const positions = [
    { key: "", value: "สมาชิก" },
    { key: "1", value: "หัวหน้าห้อง" },
    { key: "2", value: "รองหัวหน้าฝ่ายการเรียน" },
    { key: "3", value: "รองหัวหน้าฝ่ายการงาน" },
    { key: "4", value: "รองหัวหน้าฝ่ายกิจกรรม" },
    { key: "5", value: "รองหัวหน้าฝ่ายสารวัตรนักเรียน" },
    { key: "6", value: "นักเรียนแกนนำฝ่ายการเรียน" },
    { key: "7", value: "นักเรียนแกนนำฝ่ายการงาน" },
    { key: "8", value: "นักเรียนแกนนำฝ่ายกิจกรรม" },
    { key: "9", value: "นักเรียนแกนนำฝ่ายสารวัตรนักเรียน" },
    { key: "10", value: "เลขานุการ" },
    { key: "11", value: "ผู้ช่วยเลขานุการ" }
];

// กำหนดจำนวนสูงสุดแต่ละตำแหน่ง
const positionLimits = {
    "1": 1,  // หัวหน้าห้อง
    "2": 1,  // รองหัวหน้าฝ่ายการเรียน
    "3": 1,  // รองหัวหน้าฝ่ายการงาน
    "4": 1,  // รองหัวหน้าฝ่ายกิจกรรม
    "5": 1,  // รองหัวหน้าฝ่ายสารวัตรนักเรียน
    "10": 1, // เลขานุการ
    "11": 1, // ผู้ช่วยเลขานุการ
    "6": 4,  // นักเรียนแกนนำฝ่ายการเรียน
    "7": 4,  // นักเรียนแกนนำฝ่ายการงาน
    "8": 4,  // นักเรียนแกนนำฝ่ายกิจกรรม
    "9": 4   // นักเรียนแกนนำฝ่ายสารวัตรนักเรียน
};

// ดึงข้อมูลนักเรียนและตำแหน่งจาก API
async function fetchWroomData() {
    const res = await fetch('api/api_wroom.php?major=' + encodeURIComponent(stu_major) + '&room=' + encodeURIComponent(stu_room) + '&pee=' + encodeURIComponent(pee));
    const data = await res.json();
    return data;
}

function renderTable(students) {
    const tbody = document.getElementById('studentTableBody');
    tbody.innerHTML = '';
    students.forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.className = idx % 2 === 0 ? 'bg-white' : 'bg-gray-50';
        tr.innerHTML = `
            <td class="text-center px-4 py-2">${row.Stu_no}</td>
            <td class="text-center px-4 py-2">${row.Stu_id}</td>
            <td class="text-left px-4 py-2">${row.Stu_pre}${row.Stu_name} ${row.Stu_sur}</td>
            <td class="text-center px-4 py-2">
                <select name="position[]" class="form-control border border-gray-300 rounded px-2 py-1 text-center">
                    ${positions.map(pos => `<option value="${pos.key}" ${row.wposit == pos.key ? 'selected' : ''}>${pos.value}</option>`).join('')}
                </select>
                <input type="hidden" name="stdid[]" value="${row.Stu_id}">
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ดึง maxim
async function fetchMaxim() {
    const res = await fetch('api/api_wroom_maxim.php?major=' + encodeURIComponent(stu_major) + '&room=' + encodeURIComponent(stu_room) + '&pee=' + encodeURIComponent(pee));
    const data = await res.json();
    document.getElementById('maxim').value = data.maxim || '';
}

// โหลดข้อมูลเมื่อหน้าเพจโหลดเสร็จ
document.addEventListener('DOMContentLoaded', async () => {
    const students = await fetchWroomData();
    renderTable(students);
    await fetchMaxim();
});

// ส่งข้อมูลฟอร์มด้วย fetch
document.getElementById('wroomForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    // ตรวจสอบจำนวนแต่ละตำแหน่ง
    const selects = document.querySelectorAll('select[name="position[]"]');
    const count = {};
    selects.forEach(sel => {
        const val = sel.value;
        if (val && positionLimits[val]) {
            count[val] = (count[val] || 0) + 1;
        }
    });
    let over = [];
    for (const key in positionLimits) {
        if ((count[key] || 0) > positionLimits[key]) {
            over.push(positions.find(p => p.key === key).value + " (" + count[key] + "/" + positionLimits[key] + ")");
        }
    }
    if (over.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'เลือกตำแหน่งเกินจำนวนที่กำหนด',
            html: 'ตำแหน่งต่อไปนี้เกินจำนวนที่กำหนด:<br><b>' + over.join('<br>') + '</b>',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    // ...existing code for fetch submit...
    const form = e.target;
    const formData = new FormData(form);
    const res = await fetch('api/api_wroom_save.php', {
        method: 'POST',
        body: formData
    });
    const result = await res.json();
    if(result.success) {
        Swal.fire({
            icon: 'success',
            title: 'บันทึกข้อมูลสำเร็จ',
            confirmButtonText: 'ตกลง'
        }).then(() => location.reload());
    } else {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: result.message || '',
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
</body>
</html>
