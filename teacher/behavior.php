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
    <!-- Modal -->

    <section class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="callout callout-success text-center">
                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                        <h5 class="text-center text-lg">รายงานคะแนนพฤติกรรมของนักเรียน<br>ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></h5>
                        <h5 class="text-center text-lg">ภาคเรียนที่ <?=$term?> ปีการศึกษา <?=$pee?></h5>


                    <div class="text-left">

                    <button type="button" id="addButton" class="btn bg-red-500 text-white text-left mb-3 mt-2" data-toggle="modal" data-target="#addBehaviorModal">
                    <i class="fas fa-plus"></i> หักคะแนนนักเรียน <i class="fas fa-plus"></i></button>
                    <a href="show_behavior.php" ><button id="showBehavior" class="btn bg-blue-500 text-white text-left mb-3 mt-2"><i class="fa fa-search" aria-hidden="true"></i> แสดงข้อมูลการหักคะแนนของครู <i class="fa fa-search" aria-hidden="true"></i></button></a>
                    <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="record_table" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-indigo-500 text-white">
                                <tr >
                                    <th  class=" text-center">เลขที่</th>
                                    <th  class=" text-center">เลขประจำตัว</th>
                                    <th  class=" text-center">ชื่อ-นามสกุล</th>
                                    <th  class=" text-center">ถูกหัก</th>
                                    <th  class=" text-center">ถูกหัก (%)</th>
                                    <th  class=" text-center">ถูกหักโดย</th>
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
                <div class="flex justify-center my-4">
                    <div class="w-full max-w-3xl p-6 bg-red-100 border-l-4 border-red-500 rounded-lg shadow-md">
                        <h4 class="text-xl font-bold text-red-600 flex items-center">
                            <span class="text-2xl">⚠️</span>
                            <span class="ml-3">คำแนะนำ</span>
                        </h4>
                        <div class="mt-3 text-gray-700">
                            <p class="flex items-center">
                                <span class="text-lg">📖</span>
                                <span class="ml-3">คลิกปุ่มหักคะแนนนักเรียน เพื่อเลือกนักเรียนที่ต้องการบันทึกคะแนน</span>
                            </p>
                            <p class="flex items-center mt-2">
                                <span class="text-lg">📖</span>
                                <span class="ml-3">ป้อนข้อมูลให้ครบทุกช่องจากนั้นคลิกปุ่ม "บันทึกข้อมูล" หรือ "ปิดหน้าต่าง" เมื่อต้องการล้างข้อมูล</span>
                            </p>
                            <p class="flex items-center mt-2">
                                <span class="text-lg">📖</span>
                                <span class="ml-3">คะแนนพฤติกรรม 100 คะแนน</span>
                            </p>
                            <div class="mt-3 text-gray-600">
                                <p class="ml-6">✅ <strong>กลุ่มที่ 1:</strong> คะแนนต่ำกว่า 50 → เข้าค่ายปรับพฤติกรรม (โดยกลุ่มบริหารงานกิจการนักเรียน)</p>
                                <p class="ml-6">✅ <strong>กลุ่มที่ 2:</strong> คะแนนระหว่าง 50 - 70 → บำเพ็ญประโยชน์ 20 ชั่วโมง (โดยหัวหน้าระดับ)</p>
                                <p class="ml-6">✅ <strong>กลุ่มที่ 3:</strong> คะแนนระหว่าง 71 - 99 → บำเพ็ญประโยชน์ 10 ชั่วโมง (โดยครูที่ปรึกษา)</p>
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

async function loadTable() {
    try {
        var classValue = <?=$class?>;
        var roomValue = <?=$room?>;

        const response = await $.ajax({
            url: 'api/fet_behavior_class.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue }
        });

        if (!response.success) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

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
                { targets: 4, className: 'text-center' }, // Center align fifth column
                { targets: 5, className: 'text-left' } // Left align sixth column
            ],
            fixedHeader: true,
            fixedColumns: true,
            // scrollY: '400px',
            // scrollCollapse: true,
            scrollX: true,
            autoWidth: false,
            info: true,
            lengthChange: true,
            ordering: true,
            responsive: true,
            paging: true,
            searching: true,
            ordering: true
            
        });

        // Clear old data without destroying DataTable
        table.clear();

        if (response.data.length === 0) {
            table.row.add([
                '<td colspan="8" class="text-center">ไม่พบข้อมูล</td>'
            ]);
        } else {
            response.data.forEach((item, index) => {
                const thaiDate = convertToThaiDate(item.behavior_date);
                const teacherNames = item.teacher_names || ' - ';
                const score = item.total_behavior_score || 0;
                const maxScore = 100;
                const progress = (score / maxScore) * 100;

                table.row.add([
                    (index + 1),
                    item.Stu_id,
                    item.Stu_pre + item.Stu_name + ' ' + item.Stu_sur,
                    `<span class="text-danger">${item.total_behavior_score}</span>`,
                    `<div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: ${progress}%" aria-valuenow="${score}" aria-valuemin="0" aria-valuemax="${maxScore}">
                            ${score}/${maxScore}
                        </div>
                    </div>`,
                    `<span class="text-primary">${teacherNames}</span>`
                ]);
            });
        }

        // Re-draw table after data is updated
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
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
        }
    });
});

loadTable(); // Load data when page is loaded
});


</script>
</body>
</html>
