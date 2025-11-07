<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../controllers/HomeroomController.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);
$homeroomController = new HomeroomController($db);
// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));


require_once('header.php');


?>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
    <style>
        /* Improve select option visibility and wrapping inside modals */
        select.form-control.form-control-lg {
            white-space: normal !important;
            word-break: break-word !important;
            min-height: 48px !important;
            line-height: 1.3 !important;
            padding-top: .6rem !important;
            padding-bottom: .6rem !important;
            overflow: visible !important;
        }

        select.form-control.form-control-lg option {
            white-space: normal !important;
            word-break: break-word !important;
        }

        /* For Firefox/IE fallback */
        select.form-control.form-control-lg::-ms-expand { display: none; }
        /* File input styling and preview */
        .file-input-wrapper {
            display: block;
            width: 100%;
            background: #fff;
            border-radius: .75rem;
            padding: .25rem .5rem;
            border: 2px solid #e5e7eb; /* gray-200 */
        }

        input[type="file"].form-control {
            display: inline-block;
            width: 100%;
            padding: .5rem .75rem;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .image-preview {
            display: none;
            max-height: 260px;
            object-fit: cover;
            border-radius: .75rem;
        }

        .image-preview.visible { display: block; }
    </style>
<div class="wrapper">

    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper. Contains page content -->
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
        <!-- /.content-header -->

        <!-- EQ Report Section -->
        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12 bg-gradient-to-br from-purple-50 to-blue-50 shadow-xl border-0 rounded-2xl overflow-hidden">
                    <div class="card-body text-center p-8">
                        <!-- Logo and Title Section -->
                        <div class="mb-6">
                            <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-4 w-16 h-16 mx-auto shadow-lg">
                            <h5 class="text-center text-2xl font-bold text-gray-800 mb-2">
                                📚 รายงานกิจกรรมโฮมรูม
                            </h5>
                            <h6 class="text-center text-lg text-gray-600">
                                ระดับชั้นมัธยมศึกษาปีที่ <span class="font-semibold text-purple-600"><?=$class."/".$room?></span>
                            </h6>
                            <h6 class="text-center text-lg text-gray-600">
                                ภาคเรียนที่ <span class="font-semibold text-blue-600"><?=$term?></span> ปีการศึกษา <span class="font-semibold text-green-600"><?=$pee?></span>
                            </h6>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap justify-center gap-4 mb-8">
                            <button type="button" id="addButton" class="btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" data-toggle="modal" data-target="#addhomeModal">
                                ➕ เพิ่มกิจกรรมโฮมรูม
                            </button>
                            <button class="btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" id="printButton" onclick="printPage()">
                               🖨️ พิมพ์รายงาน
                            </button>
                        </div>

                        <!-- Table Section -->
                        <div class="row justify-content-center">
                            <div class="col-md-12 mt-3 mb-3 mx-auto">
                                <div class="table-responsive mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                                    <table id="example2" class="display table table-hover" style="width:100%">
                                        <thead class="bg-gradient-to-r from-purple-500 to-purple-600 text-white">
                                            <tr>
                                                <th class="text-center py-4 font-semibold">#</th>
                                                <th class="text-center py-4 font-semibold">📅 วันที่</th>
                                                <th class="text-center py-4 font-semibold">🏷️ ประเภท</th>
                                                <th class="text-center py-4 font-semibold">📝 หัวข้อเรื่อง</th>
                                                <th class="text-center py-4 font-semibold">📋 รายละเอียดกิจกรรม</th>
                                                <th class="text-center py-4 font-semibold">🎯 ผลที่คาดว่าจะได้รับ</th>
                                                <th class="text-center py-4 font-semibold" style="width:18%;">⚙️ จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
          <!-- /.content -->
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->

<!-- Modal  -->

    <div class="modal fade" tabindex="-1" id="addhomeModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-gradient-to-br from-blue-50 to-indigo-50 border-0 rounded-2xl shadow-2xl">
                <div class="modal-header bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-2xl">
                    <h5 class="modal-title font-bold text-xl">
                        ➕ เพิ่มข้อมูลโฮมรูม
                    </h5>
                    <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-8">
                    <form id="homeroomForm" enctype="multipart/form-data" class="p-2" novalidate>
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="type" class="block text-lg font-semibold text-gray-700 mb-3">
                                    🏷️ ประเภทเรื่องในการโฮมรูม :
                                    <span class="text-red-500 text-sm">กรุณาเลือกประเภททุกครั้ง</span>
                                </label>
                                <select class="form-control form-control-lg text-left bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" id="type" name="type" required>
                                    <option selected> -- กรุณาเลือก --</option>
                                    <?php
                                        $types = $homeroomController->getTypes();
                                        foreach ($types as $row) {
                                            echo '<option value="'.$row['th_id'].'">'.$row['th_name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="title" class="block text-lg font-semibold text-gray-700 mb-3">
                                📝 หัวข้อเรื่อง :
                                <span class="text-red-500 text-sm">ควรเป็นหัวข้อเรื่องสั้นๆ ที่กระชับ หากมีรายละเอียดเพิ่มเติมกรุณากรอกในช่องรายละเอียดกิจกรรม</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" required>
                        </div>

                        <div class="mb-6">
                            <label for="detail" class="block text-lg font-semibold text-gray-700 mb-3">
                                📋 รายละเอียดกิจกรรม :
                            </label>
                            <textarea class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" name="detail" id="detail" rows="4" required></textarea>
                        </div>

                        <div class="mb-6">
                            <label for="result" class="block text-lg font-semibold text-gray-700 mb-3">
                                🎯 ผลที่คาดว่าจะได้รับจากการจัดกิจกรรม :
                            </label>
                            <textarea class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" name="result" id="result" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label for="image1" class="block text-lg font-semibold text-gray-700 mb-3">
                                    📸 ภาพประกอบ 1 :
                                </label>
                                <input type="file" class="form-control bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" name="image1" id="image1" accept="image/*">
                                <img id="image-preview1" src="#" alt="Preview1" class="hidden max-w-full mt-4 rounded-xl shadow-lg">
                            </div>

                            <div class="col-md-6 mb-6">
                                <label for="image2" class="block text-lg font-semibold text-gray-700 mb-3">
                                    📸 ภาพประกอบ 2 :
                                </label>
                                <input type="file" class="form-control bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" name="image2" id="image2" accept="image/*">
                                <img id="image-preview2" src="#" alt="Preview2" class="hidden max-w-full mt-4 rounded-xl shadow-lg">
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between bg-gray-50 rounded-b-2xl border-t-0 pt-6">
                            <button type="button" class="btn bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" data-dismiss="modal">
                                ❌ ปิดหน้าต่าง
                            </button>
                            <input type="hidden" name="class" value="<?=$class?>">
                            <input type="hidden" name="room" value="<?=$room?>">
                            <input type="hidden" name="term" value="<?=$term?>">
                            <input type="hidden" name="pee" value="<?=$pee?>">
                            <input type="submit" name="btn_submit" class="btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" value="💾 บันทึกข้อมูล">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    <div class="modal fade" tabindex="-1" id="viewHomeModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-gradient-to-br from-green-50 to-emerald-50 border-0 rounded-2xl shadow-2xl">
                <div class="modal-header bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-t-2xl">
                    <h5 class="modal-title font-bold text-xl">
                        👁️ รายละเอียดโฮมรูม
                    </h5>
                    <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-8">
                    <div class="space-y-6">
                        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border-l-4 border-blue-500">
                            <div class="flex-shrink-0">
                                
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">📅 โฮมรูมของวันที่</p>
                                <p class="text-lg font-semibold text-gray-900" id="viewDate"></p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border-l-4 border-purple-500">
                            <div class="flex-shrink-0">
                                
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">🏷️ ประเภทเรื่อง</p>
                                <p class="text-lg font-semibold text-gray-900" id="viewType"></p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border-l-4 border-indigo-500">
                            <div class="flex-shrink-0">
                                
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">📝 หัวข้อเรื่อง</p>
                                <p class="text-lg font-semibold text-gray-900" id="viewTitle"></p>
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-green-500">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-500 mb-2">📋 รายละเอียดกิจกรรม</p>
                                    <p class="text-gray-900 whitespace-pre-wrap" id="viewDetail"></p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-yellow-500">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-500 mb-2">🎯 ผลที่คาดว่าจะได้รับ</p>
                                    <p class="text-gray-900 whitespace-pre-wrap" id="viewResult"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-pink-500">
                                <p class="text-sm font-medium text-gray-500 mb-3">📸 ภาพประกอบ 1</p>
                                <img id="viewImage1" src="#" alt="Image 1" class="w-full rounded-xl shadow-lg border-2 border-gray-200">
                            </div>
                            <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-cyan-500">
                                <p class="text-sm font-medium text-gray-500 mb-3">📸 ภาพประกอบ 2</p>
                                <img id="viewImage2" src="#" alt="Image 2" class="w-full rounded-xl shadow-lg border-2 border-gray-200">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-2xl border-t-0 pt-6">
                    <button type="button" class="btn bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" data-dismiss="modal">
                        ❌ ปิดหน้าต่าง
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="editHomeModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-gradient-to-br from-yellow-50 to-orange-50 border-0 rounded-2xl shadow-2xl">
                <div class="modal-header bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-t-2xl">
                    <h5 class="modal-title font-bold text-xl">
                        ✏️ แก้ไขข้อมูลโฮมรูม
                    </h5>
                    <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-8">
                    <form id="editHomeroomForm" enctype="multipart/form-data" class="p-2" novalidate>
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="editType" class="block text-lg font-semibold text-gray-700 mb-3">
                                    🏷️ ประเภทเรื่องในการโฮมรูม :
                                    <span class="text-red-500 text-sm">กรุณาเลือกประเภททุกครั้ง</span>
                                </label>
                                <select class="form-control form-control-lg text-left bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" id="editType" name="type" required>
                                    <option selected> -- กรุณาเลือก --</option>
                                    <?php
                                        $types = $homeroomController->getTypes();
                                        foreach ($types as $row) {
                                            echo '<option value="'.$row['th_id'].'">'.$row['th_name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="editTitle" class="block text-lg font-semibold text-gray-700 mb-3">
                                📝 หัวข้อเรื่อง :
                                <span class="text-red-500 text-sm">ควรเป็นหัวข้อเรื่องสั้นๆ ที่กระชับ หากมีรายละเอียดเพิ่มเติมกรุณากรอกในช่องรายละเอียดกิจกรรม</span>
                            </label>
                            <input type="text" name="title" id="editTitle" class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" required>
                        </div>

                        <div class="mb-6">
                            <label for="editDetail" class="block text-lg font-semibold text-gray-700 mb-3">
                                📋 รายละเอียดกิจกรรม :
                            </label>
                            <textarea class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" name="detail" id="editDetail" rows="4" required></textarea>
                        </div>

                        <div class="mb-6">
                            <label for="editResult" class="block text-lg font-semibold text-gray-700 mb-3">
                                🎯 ผลที่คาดว่าจะได้รับจากการจัดกิจกรรม :
                            </label>
                            <textarea class="form-control form-control-lg bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" name="result" id="editResult" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label for="editImage1" class="block text-lg font-semibold text-gray-700 mb-3">
                                    📸 ภาพประกอบ 1 :
                                </label>
                                <input type="file" class="form-control bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" name="image1" id="editImage1" accept="image/*">
                                <img id="editImagePreview1" src="#" alt="Preview1" class="hidden max-w-full mt-4 rounded-xl shadow-lg border-2 border-gray-200">
                            </div>

                            <div class="col-md-6 mb-6">
                                <label for="editImage2" class="block text-lg font-semibold text-gray-700 mb-3">
                                    📸 ภาพประกอบ 2 :
                                </label>
                                <input type="file" class="form-control bg-white border-2 border-gray-300 rounded-xl py-3 px-4 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200" name="image2" id="editImage2" accept="image/*">
                                <img id="editImagePreview2" src="#" alt="Preview2" class="hidden max-w-full mt-4 rounded-xl shadow-lg border-2 border-gray-200">
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between bg-gray-50 rounded-b-2xl border-t-0 pt-6">
                            <button type="button" class="btn bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" data-dismiss="modal">
                               ❌ ปิดหน้าต่าง
                            </button>
                            <input type="hidden" name="id" id="editHomeroomId">
                            <input type="submit" name="btn_submit" class="btn bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" value="💾 อัปเดตข้อมูล">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once('script.php');?>

<script>
    $(document).ready(function() {
        const classId = <?= $class ?>;
        const roomId = <?= $room ?>;
        const termValue = <?= $term ?>;
        const PeeValue = <?= $pee?>;

        const teachers = <?= json_encode($teacher->getTeachersByClassAndRoom($class, $room)); ?>;
          // Function to handle printing
    window.printPage = function () {
        const printContents = document.querySelector('.card').cloneNode(true);
        const printWindow = window.open('', '', 'width=900,height=700');

        // สร้างส่วนลายเซ็นต์ครูที่ปรึกษา
        let teacherSignatures = '<div class="flex justify-end mt-8"><div class="text-center">';
        teachers.forEach(teacher => {
            teacherSignatures += '<p class="text-lg font-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ...............................................ครูที่ปรึกษา</p>';
            teacherSignatures += `<p class="text-lg">(${teacher.Teach_name})</p>`;
            teacherSignatures += '<br>';
        });
        teacherSignatures += '</div></div>';

        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>พิมพ์รายงาน</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body {
                            background: none !important;
                        }
                            body {
                            font-family: "TH Sarabun New", sans-serif;
                            margin: 20px;
                            background: none;
                            color: black;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                        }
                        th {
                            background-color:rgb(192, 132, 252) !important;
                            color: white !important;
                            text-align: center;
                        }
                        tr:nth-child(even) {
                            background-color: #f2f2f2 !important;
                        }
                        tr:hover {
                            background-color: #ddd !important;
                        }
                        @media print {
                            button,
                            .dataTables_length,
                            .dataTables_filter,
                            .dataTables_info,
                            .dataTables_paginate,
                            th:last-child,
                            td:last-child {
                                display: none !important;
                            }
                            button {
                                display: none !important;
                            }
                            table th:nth-child(6), /* ซ่อนหัวคอลัมน์ "จัดการ" */
                            table td:nth-child(6) { /* ซ่อนข้อมูลในคอลัมน์ "จัดการ" */
                                display: none;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </head>
                <body class="p-4">
                    ${printContents.innerHTML}
                    ${teacherSignatures}
                </body>
            </html>
        `);
        printWindow.document.close();

        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    };



        // Function to set up the print layout
        function setupPrintLayout() {
            // Set page properties for landscape A4 with 0.5 inch margins
            var style = '@page { size: A4 portrait; margin: 0.5in; }';
            var printStyle = document.createElement('style');
            printStyle.appendChild(document.createTextNode(style));
            document.head.appendChild(printStyle);
        }

      
      function loadTable() {
        $.ajax({
          url: 'api/fetch_homeroom.php',
          method: 'GET',
          dataType: 'json',
          data: {
            class: <?= $class ?>,
            room: <?= $room ?>,
            term: <?= $term ?>,
            pee: <?= $pee ?>
          },
          success: function(data) {
            if (!data.success) {
              Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
              return;
            }

            if ($.fn.dataTable.isDataTable('#example2')) {
              $('#example2').DataTable().clear().destroy();
            }

            $('#example2 tbody').empty();

            if (data.data.length === 0) {
              $('#example2 tbody').append('<tr><td colspan="7" class="text-center">ไม่พบข้อมูล</td></tr>');
            } else {
              $.each(data.data, function(index, item) {
                var date = convertToThaiDate(item.h_date);
                const row = `
                    <tr class="text-center">
                        <td>${index + 1}</td>
                        <td>${date}</td>
                        <td>${item.th_name}</td>
                        <td>${item.h_topic}</td>
                        <td>${item.h_detail}</td>
                        <td>${item.h_result}</td>
                        <td>
                            <div class="flex justify-center space-x-2">
                                <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transform hover:scale-105 transition-all duration-200 btn-view" data-id="${item.h_id}" title="ดูรายละเอียด">
                                     👁️
                                </button>
                                <button class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transform hover:scale-105 transition-all duration-200 btn-edit" data-id="${item.h_id}" title="แก้ไข">
                                    ✏️
                                </button>
                                <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transform hover:scale-105 transition-all duration-200 btn-delete" data-id="${item.h_id}" title="ลบ">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                
                $('#example2 tbody').append(row);
              });
            }

            $('#example2').DataTable({
                "pageLength": 10,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
                "language": {
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจาก _MAX_ ทั้งหมด)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "แรก",
                        "last": "สุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

          },
          error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
          }
        });
      }

      $('#homeroomForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
          url: 'api/insert_homeroom.php',
          method: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            $('#addhomeModal').modal('hide');
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
            loadTable();
          },
          error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
          }
        });
      });

      $(document).on('click', '.btn-view', function() {
          var id = $(this).data('id');
          $.ajax({
              url: 'api/fetch_single_homeroom.php',
              method: 'GET',
              dataType: 'json',
              data: { id: id },
              success: function(data) {
                  if (data.success) {
                      var homeroom = data.data[0];
                      var date = convertToThaiDate(homeroom.h_date);
                      $('#viewDate').text(date);
                      $('#viewType').text(homeroom.th_name);
                      $('#viewTitle').text(homeroom.h_topic);
                      $('#viewDetail').text(homeroom.h_detail);
                      $('#viewResult').text(homeroom.h_result);
                      $('#viewImage1').attr('src', 'uploads/homeroom/' + homeroom.h_pic1).show();
                      $('#viewImage2').attr('src', 'uploads/homeroom/' + homeroom.h_pic2).show();
                      $('#viewHomeModal').modal('show');
                  } else {
                      Swal.fire('ข้อผิดพลาด', data.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                  }
              },
              error: function(xhr, status, error) {
                  Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error, 'error');
              }
          });
      });

        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.ajax({
                url: 'api/fetch_single_homeroom.php',
                method: 'GET',
                dataType: 'json',
                data: { id: id },
                success: function(data) {
                    if (data.success) {
                        var homeroom = data.data[0];
                        $('#editType').val(homeroom.th_id);
                        $('#editTitle').val(homeroom.h_topic);
                        $('#editDetail').val(homeroom.h_detail);
                        $('#editResult').val(homeroom.h_result);
                        $('#editImagePreview1').attr('src', 'uploads/homeroom/' + homeroom.h_pic1).removeClass('hidden').addClass('image-preview visible');
                        $('#editImagePreview2').attr('src', 'uploads/homeroom/' + homeroom.h_pic2).removeClass('hidden').addClass('image-preview visible');
                        $('#editHomeroomId').val(homeroom.h_id);
                        $('#editHomeModal').modal('show');
                    } else {
                        Swal.fire('ข้อผิดพลาด', data.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error, 'error');
                }
            });
        });

        $('#editHomeroomForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'api/update_homeroom.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editHomeModal').modal('hide');
                    Swal.fire('สำเร็จ', 'อัปเดตข้อมูลเรียบร้อยแล้ว', 'success');
                    loadTable();
                },
                error: function(xhr, status, error) {
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล', 'error');
                }
            });
        });

        // Image preview helper for add/edit file inputs
        function previewFile(inputEl, $imgEl) {
            if (!inputEl || !inputEl.files) return;
            const file = inputEl.files[0];
            if (!file) {
                $imgEl.attr('src', '#').removeClass('image-preview visible').addClass('hidden');
                return;
            }
            // Only preview images
            if (!file.type.startsWith('image/')) {
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                $imgEl.attr('src', e.target.result).removeClass('hidden').addClass('image-preview visible');
            }
            reader.readAsDataURL(file);
        }

        // Bind change handlers for add modal
        $('#image1').on('change', function() { previewFile(this, $('#image-preview1')); });
        $('#image2').on('change', function() { previewFile(this, $('#image-preview2')); });

        // Bind change handlers for edit modal
        $('#editImage1').on('change', function() { previewFile(this, $('#editImagePreview1')); });
        $('#editImage2').on('change', function() { previewFile(this, $('#editImagePreview2')); });

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถย้อนกลับได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ใช่, ลบเลย!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/del_homeroom.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('ลบแล้ว!', 'ข้อมูลของคุณถูกลบแล้ว.', 'success');
                                loadTable();
                            } else {
                                Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' + error, 'error');
                        }
                    });
                }
            });
        });

      loadTable();
    });

    function convertToThaiDate(dateString) {
        const months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
        const [year, month, day] = dateString.split('-');
        return `${parseInt(day)} ${months[parseInt(month) - 1]} ${parseInt(year) + 543}`;
    }
</script>
</body>
</html>
