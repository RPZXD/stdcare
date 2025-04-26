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
            <div class="max-w-3xl mx-auto mt-6">
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
                    </ul>
                </div>
                <!-- Tab Contents -->
                <div id="termTab" class="tab-content">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" action="#" autocomplete="off">
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
                                เทอม <span class="text-red-500">*</span>
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
                </div>
                <div id="promoteTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" action="#" autocomplete="off">
                        <div>
                            <p class="mb-2 font-medium">เลื่อนชั้นปีของนักเรียน</p>
                            <p class="text-gray-600 text-sm mb-4">ระบบจะเลื่อนชั้นปีของนักเรียนทุกคนโดยอัตโนมัติ และ ม.3, ม.6 จะถูกบันทึกเป็น "จบปีการศึกษา"</p>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition" onclick="return confirm('ยืนยันการเลื่อนชั้นปี?')">เลื่อนชั้นปี</button>
                        </div>
                    </form>
                </div>
                <div id="advisorTab" class="tab-content hidden">
                    <form class="bg-white shadow rounded p-6 space-y-4" method="post" action="#" enctype="multipart/form-data" autocomplete="off">
                        <div>
                            <label class="block mb-1 font-medium" for="advisor_excel">อัปโหลดไฟล์ Excel ตั้งค่าครูที่ปรึกษา <span class="text-red-500">*</span></label>
                            <input type="file" id="advisor_excel" name="advisor_excel" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer focus:outline-none focus:ring focus:border-blue-300 py-2 px-3">
                            <p class="text-gray-500 text-xs mt-1">รองรับไฟล์ .xlsx, .xls เท่านั้น</p>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded shadow hover:bg-purple-700 transition">บันทึกการตั้งค่า</button>
                        </div>
                    </form>
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
                }
                // Default tab
                showTab('termTab');
            </script>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
