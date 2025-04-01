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
    <section class="content">
        <div class="container mx-auto px-4">
            <div class="col-md-12">
                <div class="bg-white border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
                    <div class="text-center">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3">
                        <h5 class="text-lg font-bold">
                            🏠 แบบฟอร์มบันทึกการเยี่ยมบ้านนักเรียน<br>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                        </h5>
                    </div>
                    <div class="text-left mt-4">
                        <button type="button" id="addButton" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-600 mb-3">
                            ➕ รายงานสถิติข้อมูลการเยี่ยมบ้าน ➕
                        </button>
                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3" id="printButton" onclick="printPage()">
                            🖨️ พิมพ์รายงาน 🖨️
                        </button>
                    </div>
                    <div class="overflow-x-auto mt-6">
                        <table id="record_table" class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="bg-indigo-500 text-white">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center">เลขที่</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">เลขประจำตัว</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">ชื่อ-นามสกุล</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">เยี่ยมบ้านครั้งที่ 1 (100%)</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">เยี่ยมบ้านครั้งที่ 2 (ออนไลน์ 75%,บ้าน 25%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-red-100 border-l-4 border-red-500 p-6 rounded-lg shadow-md">
                        <h4 class="text-xl font-bold text-red-600 flex items-center">
                            ⚠️ <span class="ml-3">คำแนะนำ</span>
                        </h4>
                        <div class="mt-3 text-gray-700 space-y-2">
                            <p class="flex items-center">
                                📖 <span class="ml-3">เมื่อต้องการดูสรุปสถิติข้อมูลการเยี่ยมบ้านนักเรียนให้คลิกที่ "สรุปสถิติข้อมูลการเยี่ยมบ้าน"</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">เมื่อต้องการบันทึกข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "บันทึก"</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">เมื่อต้องการแก้ไขข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "แก้ไข"</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">เมื่อต้องการดูรายละเอียดข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "ดู"</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">เยี่ยมบ้านครั้งที่ 1 ให้ดำเนินการเยี่ยมบ้าน และกรอกข้อมูลนักเรียนให้ครบทุกคน</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">เยี่ยมบ้านครั้งที่ 2 ให้ดำเนินการเยี่ยมบ้าน เฉพาะนักเรียนกลุ่มเสี่ยง หรือกลุ่มที่มีความต้องการพิเศษ ร้อยละ 25</span>
                            </p>
                            <p class="flex items-center">
                                📖 <span class="ml-3">โดยกรอกข้อมูลของนักเรียนเฉพาะรายบุคคลเท่านั้น</span>
                            </p>
                        </div>
                    </div>
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

<!-- Modal for Editing Visit -->
<div class="modal fade" id="editVisitModal" tabindex="-1" aria-labelledby="editVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- เปลี่ยนจาก modal-lg เป็น modal-xl -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg text-bold" id="editVisitModalLabel">แก้ไขข้อมูลการเยี่ยมบ้าน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="editVisitContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        // Make an AJAX request to the API
        const response = await $.ajax({
            url: 'api/fetch_visit_class.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, pee: peeValue }
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
                    index + 1, // Row number
                    item.Stu_id, // Student ID
                    item.FullName, // Full name
                    item.visit_status1 === 1
                        ? '<span class="text-success">✅</span> <button class="btn btn-warning btn-sm" onclick="editVisit(1, \'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn btn-primary btn-sm" onclick="saveVisit(1, \'' + item.Stu_id + '\')"><i class="fas fa-save"></i> บันทึก</button>', // Visit status for Term 1
                    item.visit_status2 === 1
                        ? '<span class="text-success">✅</span> <button class="btn btn-warning btn-sm" onclick="editVisit(2, \'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn btn-primary btn-sm" onclick="saveVisit(2, \'' + item.Stu_id + '\')">i class="fas fa-save"></i> บันทึก</button>' // Visit status for Term 2
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

// Define the editVisit function globally
window.editVisit = function(term, stuId) {
    var pee = <?= $pee ?>;

    // Make an AJAX request to fetch student data
    $.ajax({
        url: 'api/get_visit_data.php',
        method: 'GET',
        data: { term: term, pee: pee, stuId: stuId },
        dataType: 'html',
        success: function(response) {
            if (response.trim() === '') {
                Swal.fire('ข้อผิดพลาด', 'ไม่พบข้อมูลการเยี่ยมบ้าน', 'error');
                return;
            }
            $('#editVisitContent').html(response);
            $('#editVisitModal').modal('show');
        },
        error: function(xhr, status, error) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้: ' + error, 'error');
        }
    });
};

// Handle save button click
$('#saveEditVisit').on('click', function () {
    const formData = $('#editVisitForm').serialize(); // Serialize form data

    // Make an AJAX request to save the edited data
    $.ajax({
        url: 'api/update_visit_data.php', // API สำหรับบันทึกข้อมูลการเยี่ยมบ้าน
        method: 'POST',
        data: formData,
        success: function (response) {
            if (response.success) {
                Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
                $('#editVisitModal').modal('hide'); // Close the modal
                loadTable(); // Reload the table
            } else {
                Swal.fire('ข้อผิดพลาด', response.message, 'error');
            }
        },
        error: function () {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
        }
    });
});

// Call the loadTable function when the page is loaded
loadTable();
});

</script>
</body>
</html>
