<?php 
session_start();


require_once("../config/Database.php");
require_once("../class/UserLogin.php");
require_once("../class/Student.php");
require_once("../class/Utils.php");

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
    <section class="content py-10 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <h4 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 animate-pulse">🔍 ค้นหาข้อมูล</h4>
                <h5 class="text-xl text-gray-600 mt-4">
                    (พิมพ์ <span class="font-semibold text-blue-600">เลขประจำตัว</span>, <span class="font-semibold text-green-600">ชื่อ</span>, หรือ <span class="font-semibold text-purple-600">นามสกุล</span> เพื่อค้นหา)
                </h5>
            </div>
           
            <div class="flex justify-center mt-8">
                <div class="w-full max-w-4xl">
                    <!-- Form ค้นหา -->
                    <form method="POST" id="searchForm" class="bg-white bg-opacity-90 backdrop-blur-lg p-8 rounded-2xl shadow-2xl space-y-6">
                        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                            <!-- Dropdown -->
                            <select name="type" id="type" class="block w-full md:w-1/3 px-6 py-4 text-lg text-gray-700 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-300 rounded-xl shadow-sm focus:ring-4 focus:ring-blue-400 focus:border-blue-500 transition-all duration-300">
                                <option value="teacher">👨‍🏫 ครู</option>
                                <option value="student">🎓 นักเรียน</option>
                            </select>
                            <!-- Search Input -->
                            <input type="search" name="search" id="search" class="block w-full px-6 py-4 text-lg text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl shadow-sm focus:ring-4 focus:ring-purple-400 focus:border-purple-500 transition-all duration-300" placeholder="กรุณากรอกชื่อ หรือ นามสกุลที่ต้องการค้นหา">
                            <!-- Search Button -->
                            <button type="submit" class="px-8 py-4 text-lg font-bold text-white bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl shadow-lg hover:shadow-xl hover:shadow-pink-500/50 hover:scale-105 transition-all duration-300 animate-bounce">
                                <i class="fa fa-search mr-2"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                    
                    <!-- พื้นที่สำหรับแสดงผลข้อมูล -->
                    <div id="resultContainer" class="w-full max-w-4xl"></div>
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
                        let linkprofile = '<?=htmlspecialchars($setting->getImgProfile())?>';
                        let linkprofileStudent = '<?=htmlspecialchars($setting->getImgProfileStudent())?>';

                        if (type === 'teacher') {
                            // แสดงข้อมูลครู
                            card = `
                                <div class="my-4 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white text-center py-6">
                                        <h2 class="text-3xl font-bold"><span class="animate-spin">👩‍🏫</span> ข้อมูลครู</h2>
                                    </div>
                                    <div class="p-8">
                                        <div class="text-center">
                                            <img class="rounded-full mx-auto h-40 w-40 object-cover border-4 border-white shadow-2xl hover:scale-110 transition-all duration-300" src="${linkprofile}${item.Teach_photo}" alt="${item.Teach_name}">
                                        </div>
                                        <h3 class="text-center text-2xl font-bold text-gray-800 mt-6">${item.Teach_name}</h3>
                                        <p class="text-center text-lg text-gray-600 mb-6">${item.Teach_major}</p>
                                        <ul class="space-y-4">
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-blue-700"><b>🆔 รหัสประจำตัวครู:</b></span><span class="text-blue-800 font-semibold">${item.Teach_id}</span></li>
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-green-700"><b>🚻 เพศ:</b></span><span class="text-green-800 font-semibold">${item.Teach_sex}</span></li>
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-purple-700"><b>🎂 วัน/เดือน/ปีเกิด:</b></span><span class="text-purple-800 font-semibold">${item.Teach_birth}</span></li>
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-yellow-700"><b>🏠 ที่อยู่:</b></span><span class="text-yellow-800 font-semibold">${item.Teach_addr}</span></li>
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-pink-50 to-pink-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-pink-700"><b>📞 เบอร์โทรศัพท์:</b></span><span class="text-pink-800 font-semibold">${item.Teach_phone}</span></li>
                                            <li class="flex justify-between items-center p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"><span class="font-bold text-indigo-700"><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span><span class="text-indigo-800 font-semibold">ม.${item.Teach_class}/${item.Teach_room}</span></li>
                                        </ul>
                                    </div>
                                </div>`;
                        } else if (type === 'student') {
                            // แสดงข้อมูลนักเรียน
                            card = `
                                <div class="my-4 bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 p-6">
                                    <img class="rounded-xl mb-6 shadow-lg hover:shadow-xl transition-all duration-300" src="${linkprofileStudent}${item.Stu_picture}" alt="Student Picture" style="height: 300px; object-fit: cover;">
                                    <div class="space-y-4">
                                        <h5 class="text-xl font-bold text-gray-800 text-center">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}</h5>
                                        <div class="space-y-2 text-gray-600">
                                            <p>รหัสนักเรียน: <span class="font-semibold text-blue-600">${item.Stu_id}</span></p>
                                            <p>เลขที่: <span class="font-semibold text-green-600">${item.Stu_no}</span></p>
                                            <p>ชื่อเล่น: <span class="italic text-purple-500 font-semibold">${item.Stu_nick}</span></p>
                                            <p>เบอร์โทร: <span class="font-semibold text-pink-600">${item.Stu_phone}</span></p>
                                            <p>เบอร์ผู้ปกครอง: <span class="font-semibold text-indigo-600">${item.Par_phone}</span></p>
                                        </div>
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
