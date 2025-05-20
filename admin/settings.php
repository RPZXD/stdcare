<?php
include_once("../config/Database.php");
include_once("../config/Setting.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);


// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">การตั้งค่า</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <!-- ตั้งค่าระบบ -->
            <div class="max-w-6xl mx-auto mt-6">
                <h2 class="text-2xl font-bold mb-4">ตั้งค่าระบบ</h2>
                <!-- Tabs -->
                <div>
                    <ul class="flex border-b mb-4" id="settingsTabs">
                        <li class="-mb-px mr-1">
                            <button class="bg-white inline-block py-2 px-4 font-semibold border-l border-t border-r rounded-t text-blue-700 border-blue-700" onclick="showTab('termTab')">ปีการศึกษา</button>
                        </li>
                        <li class="mr-1">
                            <button class="bg-white inline-block py-2 px-4 font-semibold text-gray-500 hover:text-blue-700" onclick="showTab('promoteTab')">เลื่อนชั้นปี</button>
                        </li>
                        <li>
                            <button class="bg-white inline-block py-2 px-4 font-semibold text-gray-500 hover:text-blue-700" onclick="showTab('advisorTab')">ครูที่ปรึกษา</button>
                        </li>
                        <li>
                            <button class="bg-white inline-block py-2 px-4 font-semibold text-gray-500 hover:text-blue-700" onclick="showTab('importStudentTab')">นำเข้ารายชื่อนักเรียนใหม่</button>
                        </li>
                        <li>
                            <button class="bg-white inline-block py-2 px-4 font-semibold text-gray-500 hover:text-blue-700" onclick="showTab('updateStudentTab')">อัพเดทข้อมูลนักเรียน</button>
                        </li>
                    </ul>
                </div>
                <!-- Tab Contents -->
                <div id="termTab" class="tab-content">
                    <form id="SetPeeForm" class="bg-white shadow rounded p-6 space-y-4" method="post" action="#" autocomplete="off">
                        <div>
                            <label class="block mb-1 font-medium" for="academic_year">
                                ปีการศึกษา <span class="text-red-500">*</span>
                            </label>
                            <select id="academic_year" name="academic_year" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                                <?php
                                    $currentYear = date('Y') + 543;
                                    $selectedYear = $user->getPee(); // ปีที่ต้องการให้เลือกไว้
                                    for ($y = $currentYear + 2; $y >= $currentYear - 2; $y--) {
                                        $selected = ($y == $selectedYear) ? 'selected' : '';
                                        echo "<option value=\"$y\" $selected>$y</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium" for="term">
                                ภาคเรียนที่ <span class="text-red-500">*</span>
                            </label>
                            <select id="term" name="term" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                                <?php
                                    $selectedTerm = $user->getTerm();
                                ?>
                                <option value="1" <?php echo ($selectedTerm == '1') ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?php echo ($selectedTerm == '2') ? 'selected' : ''; ?>>2</option>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">บันทึกการตั้งค่า</button>
                        </div>
                    </form>
                    <div id="setPeeMsg" class="mt-2 text-sm"></div>
                </div>
                <div id="promoteTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" id="setUpdateClassForm"  autocomplete="off">
                        <div>
                            <p class="mb-2 font-medium">เลื่อนชั้นปีของนักเรียน</p>
                            <p class="text-gray-600 text-sm mb-4">ระบบจะเลื่อนชั้นปีของนักเรียนทุกคนโดยอัตโนมัติ และ ม.3, ม.6 จะถูกบันทึกเป็น "จบปีการศึกษา"</p>
                        </div>
                        <div class="text-right">
                            <!-- ลบ onclick ออก ไม่ต้องมี confirm() -->
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">เลื่อนชั้นปี</button>
                        </div>
                    </form>
                </div>
                <div id="advisorTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" id="advisorForm" enctype="multipart/form-data" autocomplete="off">
                        <div>
                            <label class="block mb-1 font-medium" for="advisor_excel">อัปโหลดไฟล์ Excel ตั้งค่าครูที่ปรึกษา <span class="text-red-500">*</span></label>
                            <input type="file" id="advisor_excel" name="advisor_excel" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer focus:outline-none focus:ring focus:border-blue-300 py-2 px-3">
                            <p class="text-gray-500  mt-1">รองรับไฟล์ .xlsx, .xls เท่านั้น</p>
                            <div class="mt-2">
                                <a href="api/advisor_sample.php" class="btn bg-green-500 text-white hover:bg-green-600 transition" download>ดาวน์โหลดไฟล์ตัวอย่าง (ข้อมูลปัจจุบัน)</a>
                            </div>
                            <p class="text-gray-700 text-lg mt-1">
                                <strong>หมายเหตุ:</strong> แถวแรกของไฟล์ต้องประกอบด้วยหัวข้อ <strong class="text-rose-500">รหัสครู</strong>, <strong class="text-rose-500">ชั้นปี</strong>, <strong class="text-rose-500">ห้อง</strong> ตามลำดับ<br>
                                ระบบจะอัปเดตข้อมูลครูที่ปรึกษาตาม <strong class="text-rose-500">รหัสครู</strong> ที่ระบุในไฟล์
                            </p>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded shadow hover:bg-purple-700 transition">บันทึกการตั้งค่า</button>
                        </div>
                    </form>
                </div>
                <div id="importStudentTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" id="importStudentForm" enctype="multipart/form-data" autocomplete="off">
                        <div>
                            <label class="block mb-1 font-medium" for="student_excel">อัปโหลดไฟล์ Excel รายชื่อนักเรียนใหม่ <span class="text-red-500">*</span></label>
                            <input type="file" id="student_excel" name="student_excel" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer focus:outline-none focus:ring focus:border-blue-300 py-2 px-3">
                            <p class="text-gray-500 mt-1">รองรับไฟล์ .xlsx, .xls เท่านั้น</p>
                            <div class="mt-2">
                                <a href="api/student_sample.php" class="btn bg-green-500 text-white hover:bg-green-600 transition" download>ดาวน์โหลดไฟล์ตัวอย่าง (ข้อมูลที่ต้องกรอก)</a>
                            </div>
                            <p class="text-gray-700 text-lg mt-1">
                                <strong>หมายเหตุ:</strong> แถวแรกของไฟล์ต้องประกอบด้วยหัวข้อ <strong class="text-rose-500">เลขประจำตัว</strong>, <strong class="text-rose-500">คำนำหน้า</strong>, <strong class="text-rose-500">ชื่อ</strong>, <strong class="text-rose-500">สกุล</strong>, <strong class="text-rose-500">ชั้นปี</strong>, <strong class="text-rose-500">ห้อง</strong>, <strong class="text-rose-500">เลขที่</strong> ตามลำดับ<br>
                                ใช้สำหรับนำเข้านักเรียนใหม่ ม.1 หรือ ม.4 (หรือ ม.3 เดิมที่ขึ้น ม.4)
                            </p>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">นำเข้าข้อมูล</button>
                        </div>
                    </form>
                </div>
                <div id="updateStudentTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" id="updateDataStudentForm" enctype="multipart/form-data" autocomplete="off">
                        <div>
                            <label class="block mb-1 font-medium" for="number_excel">อัปโหลดไฟล์ Excel อัพเดทข้อมูลนักเรียน <span class="text-red-500">*</span></label>
                            <input type="file" id="number_excel" name="number_excel" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer focus:outline-none focus:ring focus:border-blue-300 py-2 px-3">
                            <p class="text-gray-500 mt-1">รองรับไฟล์ .xlsx, .xls เท่านั้น</p>
                            <!-- เพิ่ม dropdown เลือกชั้น/ห้อง สำหรับดาวน์โหลดไฟล์ตัวอย่าง -->
                                                     <div class="text-right">
                            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded shadow hover:bg-yellow-700 transition">อัพเดทข้อมูลนักเรียน</button>
                        </div>
                    </form>
                            <div class="mt-2">
                                <form method="get" action="api/update_datastudent_sample_dynamic.php" target="_blank" class="flex flex-wrap gap-2 items-center" onsubmit="return validateUpdateStudentSampleForm(this);">
                                    <label for="sample_pe2" class="font-medium">เลือกชั้น:</label>
                                    <select id="sample_pe2" name="pe" required class="border rounded px-2 py-1">
                                        <option value="">-เลือกชั้น-</option>
                                        <?php for($i=1;$i<=6;$i++): ?>
                                            <option value="<?php echo $i; ?>">ม.<?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <label for="sample_room2" class="font-medium">เลือกห้อง:</label>
                                    <select id="sample_room2" name="room" required class="border rounded px-2 py-1">
                                        <option value="">-เลือกห้อง-</option>
                                        <?php for($i=1;$i<=12;$i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button type="submit" class="btn bg-green-500 text-white hover:bg-green-600 transition px-3 py-1 rounded">ดาวน์โหลดไฟล์ตัวอย่าง (ข้อมูลปัจจุบัน)</button>
                                </form>
                            </div>
                            <p class="text-gray-700 text-lg mt-1">
                                <strong>หมายเหตุ:</strong> แถวแรกของไฟล์ต้องประกอบด้วยหัวข้อ <strong class="text-rose-500">เลขประจำตัว</strong>, <strong class="text-rose-500">ชั้นปี</strong>, <strong class="text-rose-500">ห้อง</strong>, <strong class="text-rose-500">เลขที่ใหม่</strong> ตามลำดับ<br>
                                ใช้สำหรับอัพเดทข้อมูลนักเรียนในแต่ละห้อง
                            </p>
                        </div>

                </div>
            </div>
            <script>
                function showTab(tabId) {
                    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                    document.getElementById(tabId).classList.remove('hidden');
                    // Tab button active style
                    const btns = document.querySelectorAll('#settingsTabs button');
                    btns.forEach(btn => btn.classList.remove('text-blue-700', 'border-blue-700', 'border-l', 'border-t', 'border-r', 'rounded-t'));
                    if(tabId === 'termTab') btns[0].classList.add('text-blue-700','border-blue-700','border-l','border-t','border-r','rounded-t');
                    if(tabId === 'promoteTab') btns[1].classList.add('text-blue-700','border-blue-700','border-l','border-t','border-r','rounded-t');
                    if(tabId === 'advisorTab') btns[2].classList.add('text-blue-700','border-blue-700','border-l','border-t','border-r','rounded-t');
                    if(tabId === 'importStudentTab') btns[3].classList.add('text-blue-700','border-blue-700','border-l','border-t','border-r','rounded-t');
                    if(tabId === 'updateStudentTab') btns[4].classList.add('text-blue-700','border-blue-700','border-l','border-t','border-r','rounded-t');
                }
                // Default tab
                showTab('termTab');

                // --- เพิ่ม JavaScript สำหรับ SetPeeForm ---
                document.addEventListener('DOMContentLoaded', function() {
                    const setPeeForm = document.getElementById('SetPeeForm');
                    if(setPeeForm) {
                        setPeeForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(setPeeForm);
                            fetch('api/update_pee_term.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(typeof Swal !== "undefined") {
                                    if(data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'สำเร็จ',
                                            text: 'บันทึกสำเร็จ'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาด',
                                            text: data.message || 'เกิดข้อผิดพลาด'
                                        });
                                    }
                                } else {
                                    // fallback
                                    alert(data.success ? 'บันทึกสำเร็จ' : (data.message || 'เกิดข้อผิดพลาด'));
                                }
                            })
                            .catch(() => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                                    });
                                } else {
                                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                                }
                            });
                        });
                    }

                    // --- เพิ่ม JavaScript สำหรับ setUpdateClassForm ---
                    const setUpdateClassForm = document.getElementById('setUpdateClassForm');
                    if(setUpdateClassForm) {
                        setUpdateClassForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'ยืนยันการเลื่อนชั้นปี?',
                                text: 'ระบบจะเลื่อนชั้นปีของนักเรียนทุกคนโดยอัตโนมัติ และ ม.3, ม.6 จะถูกบันทึกเป็น "จบปีการศึกษา"',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'ยืนยัน',
                                cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    const formData = new FormData(setUpdateClassForm);
                                    fetch('api/promote_students.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if(data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'สำเร็จ',
                                                text: data.message || 'เลื่อนชั้นปีสำเร็จ'
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'เกิดข้อผิดพลาด',
                                                text: data.message || 'เกิดข้อผิดพลาด'
                                            });
                                        }
                                    })
                                    .catch(() => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาด',
                                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                                        });
                                    });
                                }
                            });
                        });
                    }

                    // --- เพิ่ม JavaScript สำหรับ advisorForm ---
                    const advisorForm = document.getElementById('advisorForm');
                    if(advisorForm) {
                        advisorForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(advisorForm);
                            fetch('api/advisor_advisor_upload.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: data.success ? 'success' : 'error',
                                        title: data.success ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                                        text: data.message
                                    });
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(() => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                                    });
                                } else {
                                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                                }
                            });
                        });
                    }

                    // --- เพิ่ม JavaScript สำหรับ importStudentForm ---
                    const importStudentForm = document.getElementById('importStudentForm');
                    if(importStudentForm) {
                        importStudentForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(importStudentForm);
                            fetch('api/import_student_upload.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: data.success ? 'success' : 'error',
                                        title: data.success ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                                        text: data.message
                                    });
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(() => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                                    });
                                } else {
                                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                                }
                            });
                        });
                    }

                    // --- เพิ่ม JavaScript สำหรับ updateDataStudentForm ---
                    const updateDataStudentForm = document.getElementById('updateDataStudentForm');
                    if(updateDataStudentForm) {
                        updateDataStudentForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(updateDataStudentForm);
                            fetch('api/update_data_student_upload.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: data.success ? 'success' : 'error',
                                        title: data.success ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                                        text: data.message
                                    });
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(() => {
                                if(typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                                    });
                                } else {
                                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                                }
                            });
                        });
                    }
                });
            </script>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
