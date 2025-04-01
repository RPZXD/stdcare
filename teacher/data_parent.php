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
                        <h5 class="text-center text-lg">ข้อมูลผู้ปกครอง</h5>
                        <h5 class="text-center text-lg"><?=$term?> ปีการศึกษา <?=$pee?></h5>


                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="record_table" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-indigo-500 text-white">
                                <tr>
                                            <th  class="text-center">เลขที่</th>
                                            <th  class="text-center">เลขประจำตัว</th>
                                            <th  class="text-center">ชื่อ-นามสกุล</th>
                                            <th  class="text-center">ชื่อผู้ปกครอง</th>
                                            <th  class="text-center">เบอร์โทรผู้ปกครอง</th>
                                            <th  class="text-center">จัดการ</th>
                                    <!-- Add more table column headers as needed -->
                                </tr>
                            </thead>
                            <tbody> 
                            </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div><!-- /.container-fluid -->
        </div>
    </section>

  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Modal Structure for Viewing Student -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg " role="document"> <!-- Add 'modal-lg' for a larger modal -->
    <div class="modal-contentv bg-gray-100 rounded-lg shadow-md">
      <div class="modal-header">
        <h5 class="modal-title text-xl text-bold" id="studentModalLabel">ข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <!-- Student details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Structure for Editing Student -->
<div class="modal fade" id="editStudentModal"  tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document"> <!-- Add 'modal-lg' for a larger modal -->
    <div class="modal-content bg-purple-100 p-6 rounded-lg shadow-md">
      <div class="modal-header">
        <h5 class="modal-title text-lg text-bold" id="editStudentModalLabel">แก้ไขข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Edit form will be loaded here -->
      </div>
      <div class="flex justify-end">
                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-blue-400" data-dismiss="modal">ปิด</button>
                <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 ml-3" id="saveChanges">บันทึกการเปลี่ยนแปลง</button>
            </div>
    </div>
  </div>
</div>
<?php require_once('script.php');?>

<script>
$(document).ready(function() {
    loadTable(); // Load data on page load
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

async function loadTable() {
        try {
            var TeacherId = <?=$teacher_id?>;

            const response = await $.ajax({
                url: 'api/fetch_data_student.php',
                method: 'GET',
                dataType: 'json',
                data: { 
                    class: <?= $class ?>,
                    room: <?= $room ?> 
                }
            });

            if (!response.success) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                return;
            }

            const table = $('#record_table').DataTable({
                destroy: true,
                pageLength: 50,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, className: 'text-center' },
                    { targets: 1, className: 'text-center' },
                    { targets: 2, className: 'text-left text-semibold' },
                    { targets: 3, className: 'text-left text-semibold' },
                    { targets: 4, className: 'text-center' },
                    { targets: 5, className: 'text-center' }, // จัดกลางสำหรับปุ่ม
                ],
                scrollX: true,
                autoWidth: false,
                info: true,
                lengthChange: true,
                ordering: true,
                responsive: true,
                paging: true,
                searching: true
            });

            // Clear old data
            table.clear();

            if (response.data.length === 0) {
                table.row.add([
                    '<td colspan="6" class="text-center">ไม่พบข้อมูล</td>'
                ]);
            } else {
                response.data.forEach((item, index) => {
                    const thaiDate = convertToThaiDate(item.behavior_date);

                    // ปุ่มแก้ไขและลบ
                    const actionButtons = `
                        <button class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 btn-view" data-id="${item.Stu_id}">👀 ดู</button>
                        <button class="btn btn-warning bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 btn-edit" data-id="${item.Stu_id}">✏️ แก้ไข</button>
                    `;

                    table.row.add([
                        (index + 1),
                        item.Stu_id,
                        item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur,
                        item.Par_name,
                        item.Par_phone,
                        actionButtons
                    ]);
                });
            }

            // Re-draw table
            table.draw();

        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            console.error(error);
        }
    }

// Use event delegation to handle dynamically added elements
$(document).on('click', '.btn-view', function() {
    var stuId = $(this).data('id'); // ดึงค่า Stu_id จาก data-id
    // Fetch student details and show in modal
    $.ajax({
        url: 'api/view_student.php', // ไฟล์ PHP ที่ใช้ดึงข้อมูล
        method: 'GET',
        data: { stu_id: stuId }, // ส่งค่า Stu_id ไปยัง server
        success: function(response) {
            // แสดงข้อมูลใน modal
            $('#studentModal .modal-body').html(response);
            $('#studentModal').modal('show'); // เปิด modal
        },
        error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            console.error(error);
        }
    });
});

$(document).on('click', '.btn-edit', function() {
    var stuId = $(this).data('id');
    // Fetch student details and show in modal
    $.ajax({
        url: 'api/edit_student_form.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            $('#editStudentModal .modal-body').html(response);
            $('#editStudentModal').modal('show');
        }
    });
});

$('#saveChanges').on('click', function() {
    var formData = $('#editStudentForm').serialize();
    $.ajax({
        url: 'api/update_student.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            $('#editStudentModal').modal('hide');
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                location.reload();
            });
        },
        error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
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
