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
                        <h5 class="text-center text-lg">รายงานการหักคะแนนพฤติกรรม<br>ของ<?= $teacher_name; ?></h5>
                        <h5 class="text-center text-lg">ภาคเรียนที่ <?=$term?> ปีการศึกษา <?=$pee?></h5>


                    <div class="text-left">

                    <button type="button" id="addButton" class="btn bg-red-500 text-white text-left mb-3 mt-2" data-toggle="modal" data-target="#addBehaviorModal">
                    <i class="fas fa-plus"></i> หักคะแนนนักเรียน <i class="fas fa-plus"></i></button>
                    <a href="behavior.php" ><button id="showBehavior" class="btn bg-blue-500 text-white text-left mb-3 mt-2"><i class="fa fa-search" aria-hidden="true"></i> แสดงข้อมูลคะแนนพฤติกรรมนักเรียนประจำชั้น <i class="fa fa-search" aria-hidden="true"></i></button></a>
                    <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="record_table" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-indigo-500 text-white">
                                <tr>
                                            <th  class="text-center">#</th>
                                            <th  class="text-center">เลขประจำตัว</th>
                                            <th  class="text-center">ชื่อ-นามสกุล</th>
                                            <th  class="text-center">วันที่</th>
                                            <th  class="text-center">ประเภทพฤติกรรม</th>
                                            <th  class="text-center">รายละเอียด</th>
                                            <th  class="text-center">คะแนน</th>
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

<!-- Modal  -->

<div class="modal fade" tabindex="-1" id="addBehaviorModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">หักคะแนนนักเรียน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="searchResults" class="text-center mb-3"></div>
                <form id="addBehaviorForm" method="POST" enctype="multipart/form-data" class="p-2" novalidate>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="stuid">เลขประจำตัวนักเรียน:</label>
                            <input type="text" name="stuid" id="stuid" class="form-control form-control-lg text-center" maxlength="5" required>
                            <small class="text-danger" id="stuidError"></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="date">วันที่:</label>
                            <input type="date" name="date" id="date" class="form-control form-control-lg text-center" required>
                            <small class="text-danger" id="dateError"></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="type">ประเภทพฤติกรรม:</label>
                            <select name="type" id="type" class="form-control form-control-lg text-center" required>
                                <option value="">-- โปรดเลือกพฤติกรรม --</option>
                                <?php 
                                    $behaviors = array(
                                        "หนีเรียนหรือออกนอกสถานศึกษา",
                                        "เล่นการพนัน",
                                        "มาโรงเรียนสาย",
                                        "แต่งกาย/ทรงผมผิดระเบียบ",
                                        "พกพาอาวุธหรือวัตถุระเบิด",
                                        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์",
                                        "สูบบุหรี่",
                                        "เสพยาเสพติด",
                                        "ลักทรัพย์ กรรโชกทรัพย์",
                                        "ก่อเหตุทะเลาะวิวาท",
                                        "แสดงพฤติกรรมทางชู้สาว",
                                        "จอดรถในที่ห้ามจอด",
                                        "แสดงพฤติกรรมก้าวร้าว",
                                        "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
                                    );

                                    foreach ($behaviors as $behavior) {
                                        echo '<option value="' . $behavior . '">' . $behavior . '</option>';
                                    }
                                ?>
                            </select>
                            <small class="text-danger" id="typeError"></small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="detail">รายละเอียด:</label>
                        <input class="form-control form-control-lg" type="text" name="detail" id="detail">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">ปิดหน้าต่าง</button>
                        <input type="hidden" name="term" value="<?=$term?>">
                        <input type="hidden" name="pee" value="<?=$pee?>">
                        <input type="hidden" name="teacherid" value="<?=$teacher_id?>">
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Car Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editBehaviorForm" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
                <div class="modal-header flex justify-between items-center border-b pb-4">
                    <h5 class="text-lg font-semibold" id="editModalLabel">แก้ไขข้อมูลคะแนนพฤติกรรม</h5>
                    <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body space-y-4">
                    <div id="searchResultsEdit" class="text-center mb-3"></div>
                    <input type="hidden" id="editBehaviorId" name="id">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editStuId">เลขประจำตัวนักเรียน</label>
                        <input type="text" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editStuId" name="StuId" placeholder="กรอกเลขประจำตัว" required>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorDate">วันที่</label>
                        <input type="date" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editBehaviorDate" name="BehaviorDate" placeholder="วันเดือนปีที่หัก(ค.ศ.)"  required>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorType">ประเภท</label>
                        <select name="BehaviorType" id="editBehaviorType" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- โปรดเลือกพฤติกรรม --</option>
                                <?php 
                                    $behaviors = array(
                                        "หนีเรียนหรือออกนอกสถานศึกษา",
                                        "เล่นการพนัน",
                                        "มาโรงเรียนสาย",
                                        "แต่งกาย/ทรงผมผิดระเบียบ",
                                        "พกพาอาวุธหรือวัตถุระเบิด",
                                        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์",
                                        "สูบบุหรี่",
                                        "เสพยาเสพติด",
                                        "ลักทรัพย์ กรรโชกทรัพย์",
                                        "ก่อเหตุทะเลาะวิวาท",
                                        "แสดงพฤติกรรมทางชู้สาว",
                                        "จอดรถในที่ห้ามจอด",
                                        "แสดงพฤติกรรมก้าวร้าว",
                                        "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
                                    );

                                    foreach ($behaviors as $behavior) {
                                        echo '<option value="' . $behavior . '">' . $behavior . '</option>';
                                    }
                                ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700" for="editBehaviorName">รายละเอียด</label>
                        <input type="text" class="form-control text-center mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="editBehaviorName" name="BehaviorName" placeholder="กรอกรายละเอียด" required>
                    </div>
                </div>
                <div class="modal-footer flex justify-end space-x-2 border-t pt-4">
                    <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('script.php');?>

<script>
$(document).ready(function() {

    loadTable(); // Load data on page load


    function convertToThaiDate(dateString) {
        const months = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];
        const date = new Date(dateString);
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear(); // Convert to Buddhist year
        return `${day} ${month} ${year}`;
    }
    function convertToEngDate(thaiDate) {   
        const dateParts = thaiDate.split('-'); // แยกวันที่ด้วย "-"
        const year = parseInt(dateParts[0]) - 543;// แปลงปี พ.ศ. เป็น ค.ศ.
        return `${year}-${dateParts[1]}-${dateParts[2]}`; // คืนค่าผลลัพธ์เป็นปี ค.ศ.
    }

    async function loadTable() {
        try {
            var TeacherId = <?=$teacher_id?>;

            const response = await $.ajax({
                url: 'api/fet_behavior_teacherid.php',
                method: 'GET',
                dataType: 'json',
                data: { id: TeacherId }
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
                    { targets: 3, className: 'text-center' },
                    { targets: 4, className: 'text-center' },
                    { targets: 5, className: 'text-left' },
                    { targets: 6, className: 'text-center' }, // จัดกลางสำหรับปุ่ม
                ],
                fixedHeader: true,
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
                    '<td colspan="8" class="text-center">ไม่พบข้อมูล</td>'
                ]);
            } else {
                response.data.forEach((item, index) => {
                    const thaiDate = convertToThaiDate(item.behavior_date);

                    // ปุ่มแก้ไขและลบ
                    const actionButtons = `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${item.id}">
                            <i class="fas fa-edit"></i> แก้ไข
                        </button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${item.id}">
                            <i class="fas fa-trash-alt"></i> ลบ
                        </button>
                    `;

                    table.row.add([
                        (index + 1),
                        item.stu_id,
                        item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur,
                        thaiDate,
                        item.behavior_type,
                        item.behavior_name,
                        `<div class="text-red-500">${item.behavior_score}<div>`,
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



    $('#addBehaviorModal form').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการ submit ฟอร์มปกติ

        var formData = new FormData(this); // เก็บข้อมูลทั้งหมดจากฟอร์ม

        // แสดงข้อมูลใน FormData
        // for (var pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }

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
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' + error, 'error');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(`api/get_behavior.php?id=${id}`, function (data) {
            // แสดงข้อมูลใน modal
            $('#editBehaviorId').val(data.id);
            $('#editStuId').val(data.stu_id);
            const Engdate = convertToEngDate(data.behavior_date);
            $('#editBehaviorDate').val(Engdate);
            $('#editBehaviorType').val(data.behavior_type);
            $('#editBehaviorName').val(data.behavior_name);

            // ถ้ามี stu_id ให้ทำการค้นหาข้อมูล
            if (data.stu_id !== '') {
                $.ajax({
                    type: 'POST',
                    url: 'api/search_data_stu.php',
                    data: { stuid: data.stu_id },  // ใช้ data.stu_id แทน stuid
                    success: function(response) {
                        $('#searchResultsEdit').html(response);
                    }
                });
            } else {
                $('#searchResultsEdit').empty();
            }
            
            // เปิด modal
            $('#editModal').modal('show');
        }).fail(() => showToast('danger', 'ไม่สามารถโหลดข้อมูลได้'));
    });

    $('#editBehaviorForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: 'api/update_behavior.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $('#editModal').modal('hide');
                Swal.fire('สำเร็จ', 'ข้อมูลถูกอัพเดทเรียบร้อยแล้ว', 'success');
                loadTable();
            },
            error: function () {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถอัพเดทข้อมูลได้', 'error');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        const BehaviorId = $(this).data('id');

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ใช่, ลบเลย!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/delete_behavior.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: BehaviorId }),
                    success: function () {
                        Swal.fire('ลบแล้ว!', 'ข้อมูลของคุณถูกลบแล้ว.', 'success');
                        loadTable();
                    },
                    error: function () {
                        Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                    }
                });
            }
        });
    });

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
    $('#editStuId').on('input', function() {
        var stuid = $(this).val();
        if (stuid !== '') {
            $.ajax({
                type: 'POST',
                url: 'api/search_data_stu.php',
                data: { stuid: stuid },
                success: function(response) {
                    $('#searchResultsEdit').html(response);
                }
            });
        } else {
            $('#searchResultsEdit').empty();
        }
    });


});


</script>
</body>
</html>
