<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Homeroom.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database_User();
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);
$homeroom = new Homeroom($db);
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
                        <h5 class="text-center text-lg">รายงานกิจกรรมโฮมรูม<br>ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></h5>
                        <h5 class="text-center text-lg">ภาคเรียนที่ <?=$term?> ปีการศึกษา <?=$pee?></h5>

                       
                    <div class="text-left">

                    <button type="button" class="btn-lg btn-primary mb-4" data-toggle="modal" data-target="#addhomeModal">
                            เพิ่มกิจกรรมโฮมรูม
                        </button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="example2" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-green-400 text-white">
                                <tr >
                                    <th  class=" text-center">#</th>
                                    <th  class=" text-center">วันที่</th>
                                    <th  class=" text-center">ประเภท</th>
                                    <th  class=" text-center">หัวข้อเรื่อง</th>
                                    <th  class=" text-center">รายละเอียดกิจกรรม</th>
                                    <th  class=" text-center">ผลที่คาดว่าจะได้รับ</th>
                                    <th  class=" text-center" style="width:18%;">จัดการ</th>
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
    </section>

  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Modal for Editing Student Information -->

<?php require_once('script.php');?>

<script>
    $(document).ready(function() {
      
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
                            <button class="btn bg-blue-400 text-white my-1 btn-view" data-id="${item.h_id}">ดู</button>
                            <button class="btn bg-yellow-400 text-white my-1 btn-edit" data-id="${item.h_id}">แก้ไข</button>
                            <button class="btn bg-red-400 text-white my-1 btn-delete" data-id="${item.h_id}">ลบ</button>
                        </td>
                    </tr>
                `;
                
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
                "responsive": true,
                "scrollX": false,
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
                        $('#editImagePreview1').attr('src', 'uploads/homeroom/' + homeroom.h_pic1).show();
                        $('#editImagePreview2').attr('src', 'uploads/homeroom/' + homeroom.h_pic2).show();
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

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถย้อนกลับได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
