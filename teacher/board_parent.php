<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Student.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);
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
                        <h5 class="text-center text-lg">รายชื่อคณะกรรมการเครือข่ายผู้ปกครอง<br></h5>
                        <h5 class="text-center text-lg">ระดับชั้นมัธยมศึกษาปีที่ <?= $class.'/'.$room; ?></h5>
                        <h5 class="text-center text-lg">ปีการศึกษา <?=$pee?></h5>


                    <div class="text-left">

                    <button type="button" id="addButton" class="btn bg-teal-500 text-white text-left mb-3 mt-2" data-toggle="modal" data-target="#addModal">
                    <i class="fas fa-plus"></i> เพิ่มข้อมูลเครือข่ายผู้ปกครอง <i class="fas fa-plus"></i></button>
        
                    <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div class="table-responsive mx-auto">
                            <table id="record_table" class="display table-bordered table-hover" style="width:100%">
                            <thead class="thead-secondary bg-emerald-500 text-white">
                                <tr>
                                            <th  class="text-center">ลำดับที่</th>
                                            <th  class="text-center">ชื่อ-นามสกุล</th>
                                            <th  class="text-center">ที่อยู่</th>
                                            <th  class="text-center">เบอร์โทรศัพท์</th>
                                            <th  class="text-center">ตำแหน่ง</th>
                                            <th  class="text-center">รูปถ่าย</th>
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

<div class="modal fade" tabindex="-1" id="addModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-header">เพิ่มข้อมูลนักเรียนยากจน</h5>
            </div>
            <div class="modal-body p-6 bg-gray-100 rounded-lg shadow-md">
                <form method="post" id="addForm" enctype="multipart/form-data" class="space-y-6" novalidate>
                    <h5 class="text-lg font-semibold text-gray-700">คำชี้แจง กรุณาเลือกนักเรียนยากจนในชั้นเรียนของท่านจำนวน 10 ลำดับ เรียงจากยากจนมากที่สุดไปน้อยที่สุด</h5>
                    <hr class="border-gray-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="number" class="block text-base font-medium text-gray-700">ลำดับที่ความยากจน:</label>
                            <span class="text-base text-red-500">กรุณาเลือกลำดับความยากจนของนักเรียนจากมากที่สุดเป็นอับดับที่ 1</span>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" id="number" name="number" required>
                                <option selected> -- กรุณาเลือก --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div>
                            <label for="student" class="block text-base font-medium text-gray-700">ชื่อ-สกุล นักเรียน:</label>
                            <span class="text-base text-red-500"><br>กรุณาเลือกชื่อนักเรียน</span>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" id="student" name="student" required>
                                <option selected> -- กรุณาเลือก --</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="reason" class="block text-base font-medium text-gray-700">เหตุผลประกอบ:</label>
                        <span class="text-base text-red-500">กรุณาระบุเหตุผลประกอบที่แสดงให้เห็นว่านักเรียนมีความยากจนจริงๆ</span>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="reason" id="reason" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700">เคยได้รับทุนการศึกษา:</label>
                            <label class="block text-base font-medium text-gray-700">เคยได้รับทุนการศึกษา:</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <div class="flex items-center">
                                    <input type="radio" id="radioPrimary1" name="received" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="radioPrimary1" class="ml-2 block text-base text-gray-700">เคย</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="radioPrimary2" name="received" value="2" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="radioPrimary2" class="ml-2 block text-base text-gray-700">ไม่เคย</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="detail" class="block text-base font-medium text-gray-700">รายละเอียดทุนการศึกษา:</label>
                            <span class="text-base text-red-500">ระบุรายละเอียดทุนที่เคยได้รับ (ถ้าเคยได้รับล่าสุด)</span>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="detail" id="detail" rows="3" required></textarea>
                        </div>
                        <div class="flex justify-between items-center">
                            <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
                            <input type="hidden" name="teacherid" value="<?=$teacher_id?>">
                            <input type="submit" name="btn_submit" class="btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" value="บันทึกข้อมูล">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> 
</div>

<!-- Modal for Editing Visit -->
<div class="modal fade" id="VisitModal" tabindex="-1" aria-labelledby="VisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- เปลี่ยนจาก modal-lg เป็น modal-xl -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg text-bold" id="VisitModalLabel">ข้อมูลการเยี่ยมบ้าน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="VisitContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
                <div class="flex justify-end">
                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-blue-400" data-dismiss="modal">ปิด</button>
                
            </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal for Editing Student Data -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold" id="editModalLabel">แก้ไขข้อมูลนักเรียนยากจน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-6 bg-gray-100 rounded-lg shadow-md">
                <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-6" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="number" class="block text-base font-medium text-gray-700">ลำดับที่ความยากจน:</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" id="number" name="number" required>
                                <option selected> -- กรุณาเลือก --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div>
                            <label for="student" class="block text-base font-medium text-gray-700">ชื่อ-สกุล นักเรียน:</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" id="student" name="student" required>
                                <option selected> -- กรุณาเลือก --</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="reason" class="block text-base font-medium text-gray-700">เหตุผลประกอบ:</label>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="reason" id="reason" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700">เคยได้รับทุนการศึกษา:</label>
                        <div class="flex items-center space-x-4 mt-2">
                            <div class="flex items-center">
                                <input type="radio" id="radioPrimary1" name="received" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="radioPrimary1" class="ml-2 block text-base text-gray-700">เคย</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="radioPrimary2" name="received" value="2" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="radioPrimary2" class="ml-2 block text-base text-gray-700">ไม่เคย</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="detail" class="block text-base font-medium text-gray-700">รายละเอียดทุนการศึกษา:</label>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="detail" id="detail" rows="3" required></textarea>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
                        <input type="submit" class="btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" value="บันทึกการแก้ไข">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once('script.php');?>

<script>
$(document).ready(function() {
    const classId = <?= $class ?>;
    const roomId = <?= $room ?>;
    const pee = <?= $pee ?>;

    // Fetch student data for the dropdown
    $.ajax({
        url: 'api/fetch_student_classroom.php',
        method: 'GET',
        dataType: 'json',
        data: { class: classId, room: roomId },
        success: function(response) {
            if (response.success) {
                const studentDropdown = $('#addModal #student');
                const studentDropdown2 = $('#editModal #student');
                response.data.forEach(student => {
                    studentDropdown.append(
                        `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name}  ${student.Stu_sur}</option>`
                    );
                    studentDropdown2.append(
                        `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name}  ${student.Stu_sur}</option>`
                    );
                });
            } else {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดรายชื่อนักเรียนได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูลนักเรียน', 'error');
        }
    });

    loadTable(classId, roomId); // Load data on page load

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

    async function loadTable(classId, roomId, Pee) {
        try {
            const response = await $.ajax({
                url: 'api/fetch_boardparent_classroom.php',
                method: 'GET',
                dataType: 'json',
                data: { class: classId, room: roomId, pee: Pee },
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
                    { targets: 5, className: 'text-center' },
                    { targets: 6, className: 'text-center' }
                ],
                fixHeader: true,
                fixedHeader: {
                    header: true,
                    footer: true
                },
                scrollX: true,
                autoWidth: true,
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
                    '', // Empty for column 1
                    '', // Empty for column 2
                    '', // Empty for column 3
                    '', // Empty for column 4
                    '', // Empty for column 5
                    '', // Empty for column 6
                    'ไม่พบข้อมูล' // Merged message
                ]).draw();
            } else {
                response.data.forEach((item, index) => {
                    const thaiDate = convertToThaiDate(item.create_at);

                    // Action buttons
                    const actionButtons = `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${item.Stu_id}">
                            <i class="fas fa-edit"></i> แก้ไข
                        </button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${item.Stu_id}">
                            <i class="fas fa-trash-alt"></i> ลบ
                        </button>
                    `;
                    const visithomeButton = `
                        <button class="btn btn-info btn-sm visitBtn"  onclick="Visit(1, \'' + ${item.Stu_id} + '\')">
                            <i class="fas fa-home"></i> เยี่ยมบ้าน
                        </button>
                    `;

                    table.row.add([
                        item.poor_no,
                        `${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}`,
                        item.poor_reason,
                        item.poor_schol || 'ไม่มีข้อมูล',
                        visithomeButton,
                        `<img src="https://student.phichai.ac.th/photo/${item.Stu_picture}" alt="Student Picture" class="rounded-full w-30 h-30">`,
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

    // Handle form submission
    $('form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);

        $.ajax({
            url: 'api/insert_poor.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ', 'บันทึกข้อมูลสำเร็จ', 'success').then(() => {
               
                        // ซ่อน modal และโหลดตารางใหม่
                        $('#addModal').modal('hide');
                        loadTable(classId, roomId);
                    });

                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            }
        });
    });

    // Function to handle printing
    window.printPage = function() {
        let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');

        // Ensure the last column (จัดการ) is visible
        $('#record_table th:last-child, #record_table td:last-child').show();

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

    window.Visit = function(term, stuId) {
        const pee = <?= $pee ?>;

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
                $('#VisitContent').html(response);
                $('#VisitModal').modal('show');
            },
            error: function(xhr, status, error) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้: ' + error, 'error');
            }
        });
    };

    $(document).on('click', '.editBtn', function() {
        const studentId = $(this).data('id');

        // Fetch student data for editing
        $.ajax({
            url: 'api/fetch_poor_data.php',
            method: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Populate modal fields with fetched data
                    console.log(response.data);
                    $('#editModal #number').val(response.data.poor_no);
                    $('#editModal #student').val(response.data.Stu_id);
                    $('#editModal #reason').val(response.data.poor_reason);
                    $('#editModal #detail').val(response.data.poor_schol);

                    // Set the correct radio button for "received"
                    if (response.data.poor_even === "1") {
                        $('#editModal #radioPrimary1').prop('checked', true);
                    } else if (response.data.poor_even === "2") {
                        $('#editModal #radioPrimary2').prop('checked', true);
                    }

                    $('#editModal').modal('show');
                } else {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
    
        // Update student data
        $.ajax({
            url: 'api/update_poor_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลสำเร็จ', 'success').then(() => {
                        $('#editModal').modal('hide');
                        loadTable(classId, roomId);
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล', 'error');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        const studentId = $(this).data('id');

        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // Delete student data
                $.ajax({
                    url: 'api/delete_poor_data.php',
                    method: 'POST',
                    data: { id: studentId },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('สำเร็จ', 'ลบข้อมูลสำเร็จ', 'success').then(() => {
                                loadTable(classId, roomId);
                            });
                        } else {
                            Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
