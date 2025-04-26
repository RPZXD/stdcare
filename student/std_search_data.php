<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    <h5 class="m-0">ค้นหาข้อมูล</h5>
                    </div>
                </div>
            </div>
        </div>
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
                                    <option value="student">🎓 นักเรียน</option>
                                    <option value="teacher">👨‍🏫 ครู</option>
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

    
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script>
        // ฟังก์ชันแปลงวันที่เป็นภาษาไทย
        function formatThaiDate(dateStr) {
            if (!dateStr) return '';
            const months = [
                '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            let [year, month, day] = parts;
            year = parseInt(year, 10) ;
            month = parseInt(month, 10);
            day = parseInt(day, 10);
            return `${day} ${months[month]} ${year}`;
        }

        $(document).ready(function() {
            $('#searchForm').on('submit', function(event) {
                event.preventDefault();
                const type = $('#type').val();
                const search = $('#search').val();
                $.ajax({
                    url: 'api/search_data.php',
                    method: 'POST',
                    data: { type: type, search: search },
                    dataType: 'json',
                    success: function(response) {
                        const resultContainer = $('#resultContainer');
                        resultContainer.empty();
                        if (response.length > 0) {
                            let linkprofileStudent = '<?=htmlspecialchars($setting->getImgProfileStudent())?>';
                            let linkprofile = '<?=htmlspecialchars($setting->getImgProfile())?>';
                            response.forEach(item => {
                                let card = '';
                                if (type === 'student') {
                                    card = `
                                        <div class="card my-2 mx-2 p-4 max-w-xs bg-white rounded-lg shadow-lg border border-gray-200 transition transform hover:scale-105">
                                            <img class="card-img-top rounded-lg mb-4" src="${linkprofileStudent}${item.Stu_picture}" alt="Student Picture" style="height: 350px; object-fit: cover;">
                                            <div class="card-body space-y-3">
                                                <h5 class="card-title text-base font-bold text-gray-800">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}</h5><br>
                                                <p class="card-text text-gray-600 text-left">รหัสนักเรียน: <span class="font-semibold text-blue-600">${item.Stu_id}</span></p>
                                                <p class="card-text text-gray-600 text-left">เลขที่: ${item.Stu_no}</p>
                                                <p class="card-text text-gray-600 text-left">ชื่อเล่น: <span class="italic text-purple-500">${item.Stu_nick}</span></p>
                                            </div>
                                        </div>`;
                                } else if (type === 'teacher') {
                                    // --- เปลี่ยนแปลงตรงนี้ ---
                                    const thaiBirth = formatThaiDate(item.Teach_birth);
                                    const phone = item.Teach_phone ? `<a href="tel:${item.Teach_phone}" class="text-blue-600 hover:underline">${item.Teach_phone}</a>` : '';
                                    card = `
                                        <div class="w-full sm:w-full">
                                            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                                                <div class="bg-gray-800 text-white text-center py-4">
                                                    <h2 class="text-2xl font-bold"><span>👩‍🏫</span> ข้อมูลครู</h2>
                                                </div>
                                                <div class="p-6">
                                                    <div class="text-center">
                                                        <img class="rounded-full mx-auto h-80 w-auto" src="${linkprofile}${item.Teach_photo}" alt="${item.Teach_name}">
                                                    </div>
                                                    <h3 class="text-center text-xl font-semibold mt-4">${item.Teach_name}</h3>
                                                    <p class="text-center text-gray-600">${item.Teach_major}</p>
                                                    <ul class="mt-4 space-y-2">
                                                        <li class="flex justify-between"><span><b>🚻 เพศ:</b></span><span>${item.Teach_sex}</span></li>
                                                        <li class="flex justify-between"><span><b>🎂 วัน/เดือน/ปีเกิด:</b></span><span>${thaiBirth}</span></li>
                                                        <li class="flex justify-between"><span><b>🏠 ที่อยู่:</b></span><span>${item.Teach_addr}</span></li>
                                                        <li class="flex justify-between"><span><b>📞 เบอร์โทรศัพท์:</b></span><span>${phone}</span></li>
                                                        <li class="flex justify-between"><span><b>📚 ครูที่ปรึกษาประจำชั้น:</b></span><span>ม.${item.Teach_class}/${item.Teach_room}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>`;
                                }
                                resultContainer.append(card);
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
                    var type = $('#type').val();
                    $.ajax({
                        url: 'api/search_autocomplete.php',
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            term: request.term,
                            type: type
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#search').val(ui.item.label);
                    return false;
                }
            });
        });
        </script>
</body>
</html>
