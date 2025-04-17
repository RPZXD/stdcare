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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));
// $count = $student->getStudyStatusCountClassRoom2($class, $room, Utils::convertToThaiDatePlusNum(date("Y-m-d")));
$countStdCome = $student->getStatusCountClassRoom($class, $room, [1] , $currentDate);
$countStdAbsent = $student->getStatusCountClassRoom($class, $room, [2, 4, 5] , $currentDate);
$countAll = $student->getCountClassRoom($class, $room);

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
    <!-- Modal -->

    <section class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="callout callout-success text-center">
                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                    <h4 class="text-lg font-semibold">การเช็คชื่อของนักเรียนชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                    <br>บันทึกเวลาเรียนประจำวันที่ <span id="textdate"></span></h4>
                

                  <form id="attendanceForm" method="POST" enctype="multipart/form-data">
                        
                        <!-- เพิ่ม input date ที่นี่ -->
                          <div class="my-3">
                              <label for="attendance_date" class="block text-base font-medium">เลือกวันที่:</label>
                              <input 
                                  type="date" 
                                  id="attendance_date" 
                                  name="attendance_date" 
                                  class="mt-1 w-1:3 rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-left text-base" 
                                  value="<?= date('Y-m-d'); ?>" 
                                  required
                              >
                          </div>
                        
                      <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="example2" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-indigo-500 text-white">
                                      <tr>
                                          <th class=" text-center">เลขที่</th>
                                          <th class=" text-center">เลขประจำตัว</th>
                                          <th class=" text-center">ชื่อ-นามสกุล</th>
                                          <th class=" text-center">เช็ค</th>
                                          <!-- Add more table column headers as needed -->
                                      </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>

                          <br>
                      <div class="form-group">
                          <input type="hidden" name="class" value="<?=$class?>">
                          <input type="hidden" name="room" value="<?=$room?>">
                          <input type="hidden" name="term" value="<?=$term?>">
                          <input type="hidden" name="pee" value="<?=$pee?>">
                          <input type="hidden" name="teacher_id" value="<?=$teacher_id?>">
                          <input type="hidden" name="teacher_name" value="<?=$userData['Teach_name']?>">
                          
                          <input type="submit" id="btn_submit" class="btn-lg btn-success" style="width: 100%;" value="บันทึกข้อมูล">
                      </div> <!-- Fixed closing tag for div -->
                </form> <!-- Fixed closing tag for form -->
            </div> <!-- Added closing div for col-md-12 -->
        </div> <!-- Added closing div for row -->

    </div><!-- /.container-fluid -->
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
      
      $('#stuid').on('input', function() {
        var stuid = $(this).val();

        if (stuid !== '') {
          $.ajax({
            type: 'POST',  // or 'GET'
            url: 'api/search_data_stu.php',  // Replace with the actual path to your server-side script
            data: { stuid: stuid },
            success: function(response) {
              $('#searchResults').html(response);
            }
          });
        } else {
          $('#searchResults').empty();
        }
      });

      $('#attendance_date').on('change', function() {
        var selectedDate = $(this).val();
        $('#textdate').text(convertToThaiDate(selectedDate));
        loadTable(selectedDate);
      });

      function loadTable(date) {
        $.ajax({
          url: 'api/fetch_check_student.php',
          method: 'GET',
          dataType: 'json',
          data: {
            date: date,
            class: <?=$class?>,
            room: <?=$room?>
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
              $('#example2 tbody').append('<tr><td colspan="4" class="text-center">ไม่พบข้อมูล</td></tr>');
            } else {
                $.each(data.data, function(index, item) {
                    const row = '<tr class="text-center">' +
                        '<td>' + item.Stu_no + '</td>' +
                        '<td><input type="hidden" name="stu_id[]" value="' + item.Stu_id + '" />' + item.Stu_id + '</td>' +
                        '<td class="text-left">' + (item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur) + '</td>' +
                        '<td>' +
                            '<select name="check[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center">' +
                                '<option value="1"' + (item.Study_status == 1 ? ' selected' : '') + ' class="text-success">มาเรียน</option>' +
                                '<option value="2"' + (item.Study_status == 2 ? ' selected' : '') + ' class="text-danger">ขาดเรียน</option>' +
                                '<option value="3"' + (item.Study_status == 3 ? ' selected' : '') + ' class="text-warning">มาสาย</option>' +
                                '<option value="4"' + (item.Study_status == 4 ? ' selected' : '') + ' class="text-info">ลาป่วย</option>' +
                                '<option value="5"' + (item.Study_status == 5 ? ' selected' : '') + ' class="text-info">ลากิจ</option>' +
                                '<option value="6"' + (item.Study_status == 6 ? ' selected' : '') + ' class="text-success">เข้าร่วมกิจกรรม</option>' +
                            '</select>' +
                        '</td>' +
                        '</tr>';
                    $('#example2 tbody').append(row);
                });

            }

            $('#example2').DataTable({
                "pageLength": 50,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true, // ปิด responsive mode
                "scrollX": false, // เลื่อนแนวนอนได้
                "language": {
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
                    "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 แถว",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ แถว)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
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

      $('#textdate').text(convertToThaiDate($('#attendance_date').val())); // แสดงวันที่เมื่อหน้าเพจโหลดเสร็จ
      loadTable($('#attendance_date').val()); // โหลดข้อมูลเมื่อหน้าเพจโหลดเสร็จ

      $('#btn_submit').on('click', function(e) {
        e.preventDefault();
        
        // แสดง Swal fire ก่อนเริ่มทำการบันทึกข้อมูล
        const swalWithProgress = Swal.fire({
          title: 'กำลังบันทึกข้อมูล...',
          html: 'โปรดรอ...',
          timer: 0, // No automatic closing
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        var formData = $('#attendanceForm').serialize();
        
        $.ajax({
          type: 'POST',
          url: 'api/insert_check_student.php',
          data: formData,
          success: function(response) {
            swalWithProgress.close(); // ปิดการแสดง Progress เมื่อสำเร็จ
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
              location.reload();
            });
          },
          error: function(xhr, status, error) {
            swalWithProgress.close(); // ปิดการแสดง Progress เมื่อเกิดข้อผิดพลาด
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
          }
        });
      });

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
</script>
</body>
</html>
