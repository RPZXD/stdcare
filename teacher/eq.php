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
                                    🏠 แบบฟอร์มบันทึกคะแนน EQ <br>
                                    ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                                </h5>
                        
                        <div class="text-left mt-4">
                            <button type="button" id="addButton" class="bg-rose-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-rose-600 mb-3" onclick="window.location.href='report_eq_all.php'">
                            📊 รายงานสถิติข้อมูล EQ 📊
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
                                                <th class="text-center">แบบประเมิน EQ</th>
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
        const classValue = <?= $class ?>;
        const roomValue = <?= $room ?>;
        const peeValue = <?= $pee ?>;
        const termValue = <?= $term ?>;

        const response = await $.ajax({
            url: 'api/fetch_eq_classroom.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, pee: peeValue, term: termValue }
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
                { targets: '_all', className: 'text-center' },
                { targets: 2, className: 'text-left text-semibold' }
            ],
            autoWidth: false,
            responsive: true,
            searching: true
        });

        table.clear();

        function createActionButton(isHave, item) {
            const color = isHave ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-500 hover:bg-blue-600';
            const icon = isHave ? 'fa-edit' : 'fa-save';
            const text = isHave ? 'แก้ไข' : 'บันทึก';
            const statusIcon = isHave ? '✅' : '❌';
            const method = isHave ? 'openEditModal' : 'openAddModal';

            return `
                <span class="${isHave ? 'text-success' : 'text-danger'}">${statusIcon}</span>
                <button class="btn ${color} text-white px-4 py-2 rounded-lg shadow-md btn-sm"
                    onclick="${method}('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')">
                    <i class="fas ${icon}"></i> ${text}
                </button>`;
        }

        function createResultButton(isHave, item) {
            if (!isHave) {
                return `<span class="text-danger">❌ ยังไม่ประเมิน</span>`;
            }

            return `
                <button class="btn bg-purple-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-purple-600 btn-sm"
                    onclick="openResultModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${<?=$class?>}', '${<?=$room?>}', '${<?=$term?>}', '${<?=$pee?>}')">
                    💻 แปลผล
                </button>`;
        }


        if (response.data.length === 0) {
            table.row.add([
                '-', '-', '-', '-', '<td colspan="5" class="text-center">ไม่พบข้อมูล</td>'
            ]);
        } else {
            response.data.forEach((item, index) => {
                table.row.add([
                    item.Stu_no,
                    item.Stu_id,
                    item.full_name,
                    createActionButton(item.eq_ishave === 1, item),
                    createResultButton(item.eq_ishave === 1, item)
                ]);
            });
        }

        table.draw();
    } catch (error) {
        Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
        console.error(error);
    }
}


// Call the loadTable function when the page is loaded
loadTable();
});

</script>
</body>
</html>