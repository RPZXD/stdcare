<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Homeroom.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
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

                    <button type="button" id="addButton" class="btn bg-blue-500 text-white text-left mb-3 mt-2" data-toggle="modal" data-target="#addhomeModal">
                    <i class="fas fa-plus"></i> เพิ่มกิจกรรมโฮมรูม <i class="fas fa-plus"></i></button>
                    <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="example2" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-purple-400 text-white">
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

<!-- Modal  -->

    <div class="modal fade" tabindex="-1" id="addhomeModal">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-header">เพิ่มข้อมูลโฮมรูม</h5>
                    </div>
                    <div class="modal-body">
                        <form id="homeroomForm" enctype="multipart/form-data" class="p-2" novalidate>
                            <div class="row mb-3 gx-3">
                                <div class="col"><label for="type">ประเภทเรื่องในการโฮมรูม : <br><span class="text-danger">กรุณาเลือกประเภททุกครั้ง</span></label>
                                </div>
                                <div class="col">
                                <select class="form-control form-control-lg text-center" id="type" name="type" required>
                                <option selected> -- กรุณาเลือก --</option>
                                <?php
                                    $types = $homeroom->fetchHomeroomTypes();
                                    foreach ($types as $row) {
                                    echo '<option value="'.$row['th_id'].'">'.$row['th_name'].'</option>';
                                    }
                                    ?>
                                </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="title">หัวข้อเรื่อง : <span class="text-danger">ควรเป็นหัวข้อเรื่องสั้นๆ ที่กระชับ หากมีรายละเอียดเพิ่มเติมกรุณากรอกในช่องรายละเอียดกิจกรรม</span></label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg" required>
                            </div>
                            <div class="mb-3">
                                <label for="detail">รายละเอียดกิจกรรม : </label>
                                <textarea class="form-control form-control-lg " name="detail" id="detail" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="result">ผลที่คาดว่าจะได้รับจากการจัดกิจกรรม : </label>
                                <textarea class="form-control form-control-lg " name="result" id="result" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                    <label for="image1" >ภาพประกอบ 1 : </label>
                                    <input type="file" class="form-control" name="image1" id="image1" accept="image/*">
                                    <img id="image-preview1" src="#" alt="Preview1" style="display: none; max-width: 400px; margin-top: 10px;">
                            </div>

                            <div class="form-group">
                                    <label for="image2" >ภาพประกอบ 2 : </label>
                                    <input type="file" class="form-control" name="image2" id="image2" accept="image/*">
                                    <img id="image-preview2" src="#" alt="Preview2" style="display: none; max-width: 400px; margin-top: 10px;">
                            </div>

                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">ปิดหน้าต่าง</button>
                                <input type="hidden" name="class" value="<?=$class?>">
                                <input type="hidden" name="room" value="<?=$room?>">
                                <input type="hidden" name="term" value="<?=$term?>">
                                <input type="hidden" name="pee" value="<?=$pee?>">
                                <input type="submit" name="btn_submit" class="btn btn-primary" value="บันทึกข้อมูล">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="modal fade" tabindex="-1" id="viewHomeModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดโฮมรูม </h5>
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col"><strong>โฮมรูมของวันที่:</strong></div>
                        <div class="col" id="viewDate"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>ประเภทเรื่อง:</strong></div>
                        <div class="col" id="viewType"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>หัวข้อเรื่อง:</strong></div>
                        <div class="col" id="viewTitle"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>รายละเอียดกิจกรรม:</strong></div>
                        <div class="col" id="viewDetail"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>ผลที่คาดว่าจะได้รับ:</strong></div>
                        <div class="col" id="viewResult"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>ภาพประกอบ 1:</strong></div>
                        <div class="col"><img id="viewImage1" src="#" alt="Image 1" style="max-width: 100%;"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><strong>ภาพประกอบ 2:</strong></div>
                        <div class="col"><img id="viewImage2" src="#" alt="Image 2" style="max-width: 100%;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="editHomeModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลโฮมรูม</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editHomeroomForm" enctype="multipart/form-data" class="p-2" novalidate>
                        <div class="row mb-3 gx-3">
                            <div class="col"><label for="editType">ประเภทเรื่องในการโฮมรูม : <br><span class="text-danger">กรุณาเลือกประเภททุกครั้ง</span></label>
                            </div>
                            <div class="col">
                                <select class="form-control form-control-lg text-center" id="editType" name="type" required>
                                    <option selected> -- กรุณาเลือก --</option>
                                    <?php
                                    $types = $homeroom->fetchHomeroomTypes();
                                    foreach ($types as $row) {
                                        echo '<option value="'.$row['th_id'].'">'.$row['th_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTitle">หัวข้อเรื่อง : <span class="text-danger">ควรเป็นหัวข้อเรื่องสั้นๆ ที่กระชับ หากมีรายละเอียดเพิ่มเติมกรุณากรอกในช่องรายละเอียดกิจกรรม</span></label>
                            <input type="text" name="title" id="editTitle" class="form-control form-control-lg" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDetail">รายละเอียดกิจกรรม : </label>
                            <textarea class="form-control form-control-lg " name="detail" id="editDetail" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editResult">ผลที่คาดว่าจะได้รับจากการจัดกิจกรรม : </label>
                            <textarea class="form-control form-control-lg " name="result" id="editResult" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="editImage1" >ภาพประกอบ 1 : </label>
                            <input type="file" class="form-control" name="image1" id="editImage1" accept="image/*">
                            <img id="editImagePreview1" src="#" alt="Preview1" style="display: none; max-width: 400px; margin-top: 10px;">
                        </div>

                        <div class="form-group">
                            <label for="editImage2" >ภาพประกอบ 2 : </label>
                            <input type="file" class="form-control" name="image2" id="editImage2" accept="image/*">
                            <img id="editImagePreview2" src="#" alt="Preview2" style="display: none; max-width: 400px; margin-top: 10px;">
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">ปิดหน้าต่าง</button>
                            <input type="hidden" name="id" id="editHomeroomId">
                            <input type="submit" name="btn_submit" class="btn btn-primary" value="บันทึกข้อมูล">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once('script.php');?>

<script>
    $(document).ready(function() {

          // Function to handle printing
        window.printPage = function() {
            let elementsToHide = $('#addButton, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');

            // Hide the export to Excel button
            $('#record_table_wrapper .dt-buttons').hide(); // Hides the export buttons

            // Hide the elements you want to exclude from the print
            elementsToHide.hide();
            $('thead').css('display', 'table-header-group'); // Ensure header shows

            // Hide the last column
            $('table tr').each(function() {
                $(this).find('td:last-child, th:last-child').hide();
            });
            

            setTimeout(() => {
                window.print();
                elementsToHide.show();

                // After printing, restore the last column
                $('table tr').each(function() {
                    $(this).find('td:last-child, th:last-child').show();
                });
                $('#record_table_wrapper .dt-buttons').show();
            }, 100);
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
                            <button class="btn bg-blue-400 text-white my-1 btn-view" data-id="${item.h_id}"><i class="fas fa-search"></i></button>
                            <button class="btn bg-yellow-400 text-white my-1 btn-edit" data-id="${item.h_id}"><i class="fas fa-pen"></i></button>
                            <button class="btn bg-red-400 text-white my-1 btn-delete" data-id="${item.h_id}"><i class="fas fa-trash"></i></button>
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
