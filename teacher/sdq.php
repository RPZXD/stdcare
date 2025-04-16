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
                            <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3">
                                <h5 class="text-lg font-bold">
                                    🏠 แบบฟอร์มบันทึกคะแนน SDQ <br>
                                    ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                                </h5>
                        
                        <div class="text-left mt-4">
                            <button type="button" id="addButton" class="bg-rose-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-rose-600 mb-3" onclick="window.location.href='report_sdq_all.php'">
                            📊 รายงานสถิติข้อมูล SDQ 📊
                            </button>
                            <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3" id="printButton" onclick="printPage()">
                                🖨️ พิมพ์รายงาน 🖨️
                            </button>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12 mt-3 mb-3 mx-auto">
                                <div class="table-responsive mx-auto">
                                    <table id="record_table" class="display table-bordered table-hover" style="width:100%">
                                        <thead class="thead-secondary bg-indigo-500 text-white">
                                            <tr >
                                                <th class="text-center">เลขที่</th>
                                                <th class="text-center">เลขประจำตัว</th>
                                                <th class="text-center">ชื่อ-นามสกุล</th>
                                                <th class="text-center">นักเรียน</th>
                                                <th class="text-center">ครู</th>
                                                <th class="text-center">ผู้ปกครอง</th>
                                                <th class="text-center">แปลผล</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic content will be loaded here -->
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


<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {

  // Function to handle printing
  window.printPage = function() {
      let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, .btn-warning, .btn-primary');

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
        // Get class, room, and pee values from PHP variables
        var classValue = <?= $class ?>;
        var roomValue = <?= $room ?>;
        var peeValue = <?= $pee ?>;
        var termValue = <?= $term ?>;

        // Make an AJAX request to the API
        const response = await $.ajax({
            url: 'api/fetch_sdq_classroom.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, pee: peeValue, term: termValue }
        });

        // Check if the API response is successful
        if (!response.success) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

        // Initialize or destroy the DataTable instance
        const table = $('#record_table').DataTable({
            destroy: true, // Destroy the previous instance of DataTable
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'asc']], // Sort by the first column (index 0)
            columnDefs: [
                { targets: 0, className: 'text-center' }, // Center align first column
                { targets: 1, className: 'text-center' }, // Center align second column
                { targets: 2, className: 'text-left text-semibold' }, // Left align third column
                { targets: 3, className: 'text-center' }, // Center align fourth column
                { targets: 4, className: 'text-center' } // Center align fifth column
            ],
            autoWidth: false,
            info: true,
            lengthChange: true,
            ordering: true,
            responsive: true,
            paging: true,
            searching: true
        });

        // Clear old data without destroying DataTable
        table.clear();

        // Check if there is data in the response
        if (response.data.length === 0) {
            table.row.add([
                '<td colspan="5" class="text-center">ไม่พบข้อมูล</td>'
            ]);
        } else {
            // Populate the table with data
            response.data.forEach((item, index) => {
                table.row.add([
                    item.Stu_no, // Row number
                    item.Stu_id, // Student ID
                    item.full_name, // Full name
                    item.self_ishave === 1
                        ? '<span class="text-success">✅</span> <button class="btn bg-amber-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-amber-600 btn-sm" onclick="editSDQstd(\'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 btn-sm" onclick="addSDQstd(\'' + item.Stu_id + '\', \'' + item.full_name + '\', \'' + item.Stu_no + '\', \'' + <?=$class ?> + '\', \'' + <?=$room ?> + '\', \'' + <?=$term ?> + '\', \'' + <?=$pee ?> + '\')"><i class="fas fa-save"></i> บันทึก</button>', 
                    item.par_ishave === 1
                        ? '<span class="text-success">✅</span> <button class="btn bg-amber-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-amber-600 btn-sm" onclick="editSDQpar(\'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 btn-sm" onclick="addSDQpar(\'' + item.Stu_id + '\')"><i class="fas fa-save"></i> บันทึก</button>', 
                    item.teach_ishave === 1
                        ? '<span class="text-success">✅</span> <button class="btn bg-amber-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-amber-600 btn-sm" onclick="editSDQteach(\'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 btn-sm" onclick="addSDQteach(\'' + item.Stu_id + '\')"><i class="fas fa-save"></i> บันทึก</button>', 
                    item.self_ishave === 1 && item.par_ishave === 1 && item.teach_ishave === 1
                        ? '<button class="btn bg-purple-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-purple-600 btn-sm" onclick="resultSDQ(\'' + item.Stu_id + '\')">💻 แปลผล</button>'
                        : '<span class="text-danger">❌ โปรดประเมินให้ครบ</span>'

                ]);
            });
        }

        // Re-draw the table after data is updated
        table.draw();
    } catch (error) {
        Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
        console.error(error);
    }
}

// Function to handle addSDQstd
window.addSDQstd = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
    $.ajax({
        url: 'template_form/form_sdq_self.php',
        method: 'GET',
        data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
        success: function(response) {
            // Create and display the modal
            const modalHtml = `
                <div class="modal fade" id="sdqModal" tabindex="-1" role="dialog" aria-labelledby="sdqModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sdqModalLabel">แบบฟอร์มแก้ไขข้อมูลแบบประเมินตนเอง (SDQ) (ฉบับนักเรียนประเมินตนเอง)</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ${response}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                <button type="button" class="btn btn-primary" id="saveSDQ">บันทึก</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            $('#sdqModal').modal('show');

            // Handle save button click
            $('#saveSDQ').on('click', function() {
                const formData = $('#sdqForm').serialize(); // Assuming the form has id="sdqForm"

                // แสดงหน้าต่างโหลด
                Swal.fire({
                    title: 'กำลังบันทึกข้อมูล...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // แสดงวงกลมโหลด
                    }
                });

                $.ajax({
                    url: 'api/save_sdq_self.php',
                    method: 'POST',
                    data: formData,
                    success: function(saveResponse) {
                        Swal.fire({
                            title: 'สำเร็จ',
                            text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#sdqModal').modal('hide');
                            $('#sdqModal').remove();
                            window.location.reload(); // รีโหลดหน้า
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'ข้อผิดพลาด',
                            text: 'ไม่สามารถบันทึกข้อมูลได้',
                            icon: 'error'
                        });
                    }
                });
            });



            // Remove modal from DOM after hiding
            $('#sdqModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้', 'error');
        }
    });
};

// Call the loadTable function when the page is loaded
loadTable();
});

</script>
</body>
</html>
