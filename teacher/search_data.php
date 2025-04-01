<?php 
session_start();


include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

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


require_once('header.php');


?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content py-10 bg-gray-100">
        <div class="container mx-auto">
            <h4 class="text-center text-3xl font-bold text-gray-800">🔍 ค้นหาข้อมูล</h4>
            <h5 class="text-center text-lg text-gray-600 mt-2">
                (พิมพ์ <span class="font-semibold">เลขประจำตัว</span>, <span class="font-semibold">ชื่อ</span>, หรือ <span class="font-semibold">นามสกุล</span> เพื่อค้นหา)
            </h5>
            <div class="flex justify-center mt-8">
                <div class="w-full max-w-2xl">
                    <!-- Form ค้นหา -->
                    <form method="POST" id="searchForm" class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <!-- Dropdown -->
                            <select name="type" id="type" class="block w-1/3 px-4 py-3 text-lg text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="teacher">👨‍🏫 ครู</option>
                                <option value="student">🎓 นักเรียน</option>
                            </select>
                            <!-- Search Input -->
                            <input type="search" name="search" id="search" class="block w-full px-4 py-3 text-lg text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="กรุณากรอกชื่อ หรือ นามสกุลที่ต้องการค้นหา">
                            <!-- Search Button -->
                            <button type="submit" class="px-6 py-3 text-lg font-semibold text-white bg-blue-500 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <i class="fa fa-search"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                    
                    <!-- พื้นที่สำหรับแสดงผลข้อมูล -->
                    <div id="resultContainer" class="mt-8 flex flex-wrap justify-center space-y-4 mb-4"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->


<?php require_once('script.php'); ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('#searchForm').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการ reload หน้า

        const type = $('#type').val(); // ประเภท (teacher หรือ student)
        const search = $('#search').val(); // คำค้นหา

        // ส่งคำขอ AJAX
        $.ajax({
            url: 'api/search_data.php', // ไฟล์ PHP สำหรับค้นหา
            method: 'POST',
            data: { type: type, search: search },
            dataType: 'json',
            success: function(response) {
                const resultContainer = $('#resultContainer');
                resultContainer.empty(); // ล้างข้อมูลเก่า

                if (response.length > 0) {
                    response.forEach(item => {
                        let card = '';

                        if (type === 'teacher') {
                            // แสดงข้อมูลครู
                            card = `
                                <div class="w-full sm:w-full">
                                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                                        <div class="bg-gray-800 text-white text-center py-4">
                                            <h2 class="text-2xl font-bold"><span>👩‍🏫</span> ข้อมูลครู</h2>
                                        </div>
                                        <div class="p-6">
                                            <div class="text-center">
                                                <img class="rounded-full mx-auto h-80 w-auto" src="uploads/phototeach/${item.Teach_photo}" alt="${item.Teach_name}">
                                            </div>
                                            <h3 class="text-center text-xl font-semibold mt-4">${item.Teach_name}</h3>
                                            <p class="text-center text-gray-600">${item.Teach_major}</p>
                                            <ul class="mt-4 space-y-2">
                                                <li class="flex justify-between"><span><b>🆔 รหัสประจำตัวครู:</b></span><span>${item.Teach_id}</span></li>
                                                <li class="flex justify-between"><span><b>🚻 เพศ:</b></span><span>${item.Teach_sex}</span></li>
                                                <li class="flex justify-between"><span><b>🎂 วัน/เดือน/ปีเกิด:</b></span><span>${item.Teach_birth}</span></li>
                                                <li class="flex justify-between"><span><b>🏠 ที่อยู่:</b></span><span>${item.Teach_addr}</span></li>
                                                <li class="flex justify-between"><span><b>📞 เบอร์โทรศัพท์:</b></span><span>${item.Teach_phone}</span></li>
                                                <li class="flex justify-between"><span><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span><span>ม.${item.Teach_class}/${item.Teach_room}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>`;
                        } else if (type === 'student') {
                            // แสดงข้อมูลนักเรียน
                            card = `
                                <div class="card my-4 p-4 max-w-xs bg-white rounded-lg shadow-lg border border-gray-200 transition transform hover:scale-105">
                                    <img class="card-img-top rounded-lg mb-4" src="https://student.phichai.ac.th/photo/${item.Stu_picture}" alt="Student Picture" style="height: 350px; object-fit: cover;">
                                    <div class="card-body space-y-3">
                                        <h5 class="card-title text-base font-bold text-gray-800">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}</h5><br>
                                        <p class="card-text text-gray-600 text-left">รหัสนักเรียน: <span class="font-semibold text-blue-600">${item.Stu_id}</span></p>
                                        <p class="card-text text-gray-600 text-left">เลขที่: ${item.Stu_no}</p>
                                        <p class="card-text text-gray-600 text-left">ชื่อเล่น: <span class="italic text-purple-500">${item.Stu_nick}</span></p>
                                        <p class="card-text text-gray-600 text-left">เบอร์โทร: ${item.Stu_phone}</p>
                                        <p class="card-text text-gray-600 text-left">เบอร์ผู้ปกครอง: ${item.Par_phone}</p>
                                    </div>
                                </div>`;
                        }

                        resultContainer.append(card); // เพิ่มการ์ดลงใน container
                    });
                } else {
                    resultContainer.html('<p class="text-center text-gray-600">ไม่พบข้อมูล</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#search').autocomplete({
        source: function(request, response) {
            // ดึงค่าของ type (teacher หรือ student)
            var type = $('#type').val();

            // ส่งคำขอ AJAX ไปยังเซิร์ฟเวอร์
            $.ajax({
                url: 'api/search_autocomplete.php', // ไฟล์ PHP สำหรับค้นหา
                method: 'GET',
                dataType: 'json',
                data: {
                    term: request.term, // คำที่พิมพ์ในช่องค้นหา
                    type: type // ประเภท (teacher หรือ student)
                },
                success: function(data) {
                    response(data); // ส่งข้อมูลกลับไปยัง Autocomplete
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        },
        minLength: 2, // เริ่มแสดงผลลัพธ์เมื่อพิมพ์อย่างน้อย 2 ตัวอักษร
        select: function(event, ui) {
            // เมื่อเลือกผลลัพธ์
            $('#search').val(ui.item.label); // กรอกค่าที่เลือกลงในช่องค้นหา
            return false;
        }
    });
});
</script>
</body>
</html>
