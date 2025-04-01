<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

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

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
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
    <!-- Modal -->

<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="callout callout-success text-center">
                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                <h5 class="text-center text-lg">รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></h5>
                <h5 class="text-center text-lg">ปีการศึกษา <?=$pee?></h5>

                <div class="row justify-content-center">
                    <div id="showDataStudent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- การ์ดนักเรียนจะถูกแทรกที่นี่ -->
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Modal Structure for Viewing Student -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">ข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <!-- Student details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>


<?php require_once('script.php');?>

<script>
$(document).ready(function() {

// Function to handle printing
window.printPage = function() {
    let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');

    // Hide the export to Excel button
    $('#record_table_wrapper .dt-buttons').hide(); // Hides the export buttons

    // Hide the elements you want to exclude from the print
    elementsToHide.hide();
    $('thead').css('display', 'table-header-group'); // Ensure header shows

    setTimeout(() => {
        window.print();
        elementsToHide.show();
        $('#record_table_wrapper .dt-buttons').show();
    }, 100);
};

// Function to set up the print layout
function setupPrintLayout() {
    var style = '@page { size: A4 portrait; margin: 0.5in; }';
    var printStyle = document.createElement('style');
    printStyle.appendChild(document.createTextNode(style));
    document.head.appendChild(printStyle);
}

$('#stuid').on('input', function() {
    var stuid = $(this).val();
    if (stuid !== '') {
        $.ajax({
            type: 'POST',
            url: 'api/search_data_stu.php',
            data: { stuid: stuid },
            success: function(response) {
                $('#searchResults').html(response);
            }
        });
    } else {
        $('#searchResults').empty();
    }
});

function convertToThaiDate(dateString) {
    const months = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // Convert to Buddhist year
    return `${day} ${month} ${year}`;
}

async function loadStudentData() {
    try {
        var classValue = <?=$class?>;
        var roomValue = <?=$room?>;

        const response = await $.ajax({
            url: 'api/fetch_data_student.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue }
        });

        if (!response.success) {
            Swal.fire('🚨 ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

        const showDataStudent = $('#showDataStudent');
        showDataStudent.empty(); // เคลียร์ข้อมูลเก่า

        if (response.data.length === 0) {
            showDataStudent.html('<p class="text-center text-xl font-semibold text-gray-600">ไม่พบข้อมูลนักเรียน</p>');
        } else {
            response.data.forEach((item, index) => {
                const studentCard = `
                    <div class="card my-4 p-4 max-w-xs bg-white rounded-lg shadow-lg border border-gray-200 transition transform hover:scale-105">
                        <img class="card-img-top rounded-lg mb-4" src="https://student.phichai.ac.th/photo/${item.Stu_picture}" alt="Student Picture" style="height: 350px; object-fit: cover;">
                        <div class="card-body space-y-3">
                            <h5 class="card-title text-base font-bold text-gray-800">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur} </h5><br>
                            <p class="card-text text-gray-600 text-left">รหัสนักเรียน: <span class="font-semibold text-blue-600">${item.Stu_id}</span></p>
                            <p class="card-text text-gray-600 text-left">เลขที่: ${item.Stu_no}</p>
                            <p class="card-text text-gray-600 text-left">ชื่อเล่น: <span class="italic text-purple-500">${item.Stu_nick}</span></p>
                            <p class="card-text text-gray-600 text-left">เบอร์โทร: ${item.Stu_phone}</p>
                            <p class="card-text text-gray-600 text-left">เบอร์ผู้ปกครอง: ${item.Par_phone}</p>
                            <div class="flex space-x-2">
                                <button class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" data-id="${item.Stu_id}">👀 ดู</button>
                                <button class="btn btn-warning bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" data-id="${item.Stu_id}">✏️ แก้ไข</button>
                            </div>
                        </div>
                    </div>
                `;
                showDataStudent.append(studentCard); // แทรกการ์ดลงใน div
            });
        }

    } catch (error) {
        Swal.fire('🚨 ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
        console.error(error);
    }
}

// Add event listeners for the buttons
$('.btn-view').on('click', function() {
    var stuId = $(this).data('id');
    // Fetch student details and show in modal
    $.ajax({
        url: 'view_student.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            $('#studentModal .modal-body').html(response);
            $('#studentModal').modal('show');
        }
    });
});



$('#addBehaviorModal form').on('submit', function(event) {
    event.preventDefault(); // ป้องกันการ submit ฟอร์มปกติ

    var formData = new FormData(this); // เก็บข้อมูลทั้งหมดจากฟอร์ม


    $.ajax({
        url: 'api/insert_behavior.php',
        type: 'POST',
        data: formData,
        processData: false,  // ไม่ให้ jQuery จัดการกับข้อมูล
        contentType: false,  // ไม่กำหนด content-type ด้วยตัวเอง
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', response.message, 'success');
                $('#addBehaviorModal').modal('hide'); // ปิด modal หลังจากบันทึกข้อมูล
                loadTable(); // รีเฟรชข้อมูลในตาราง
            } else {
                Swal.fire('ข้อผิดพลาด', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
        }
    });
});

loadStudentData(); // Load data when page is loaded
});


</script>
</body>
</html>
