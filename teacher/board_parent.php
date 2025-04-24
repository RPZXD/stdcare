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
            <div class="card col-md-12">
                <div class="card-body text-center">
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

<div class="modal fade" tabindex="-1" id="addModal" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold" id="addModalLabel">เพิ่มข้อมูลคณะกรรมการเครือข่ายผู้ปกครอง</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-6 bg-gray-100 rounded-lg shadow-md">
                <form method="post" enctype="multipart/form-data" class="space-y-6" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stu_id" class="block text-base font-medium text-gray-700">เป็นผู้ปกครองของ:</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" id="stu_id" name="stu_id" required>
                                <option selected> -- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label for="image1" class="block text-base font-medium text-gray-700">รูปภาพ:</label>
                            <input type="file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="image1" id="image1" accept="image/*">
                            <img id="image-preview1" src="#" alt="Preview1" class="mt-2 max-w-xs rounded-md shadow-md hidden">
                        </div>
                    </div>
                    <div>
                        <label for="name" class="block text-base font-medium text-gray-700">ชื่อ-สกุลผู้ปกครอง:</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" required>
                    </div>
                    <div>
                        <label for="address" class="block text-base font-medium text-gray-700">ที่อยู่ผู้ปกครอง:</label>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="address" id="address" rows="3" required></textarea>
                    </div>
                    <div>
                        <label for="tel" class="block text-base font-medium text-gray-700">โทรศัพท์มือถือผู้ปกครอง:</label>
                        <input type="text" name="tel" id="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" maxlength="10" required>
                    </div>
                    <div>
                        <label for="pos" class="block text-base font-medium text-gray-700">ตำแหน่ง:</label>
                        <select name="pos" id="pos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" required>
                            <option value="" selected>--เลือก--</option>
                            <option value="1">ประธาน</option>
                            <option value="2">กรรมการ</option>
                            <option value="3">เลขานุการ</option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
                        <input type="hidden" name="major" value="<?=$class?>">
                        <input type="hidden" name="room" value="<?=$room?>">
                        <input type="hidden" name="teacherid" value="<?=$teacher_id?>">
                        <input type="hidden" name="term" value="<?=$term?>">
                        <input type="hidden" name="pee" value="<?=$pee?>">
                        <input type="submit" name="btn_submit" class="btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" value="บันทึกข้อมูล">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" tabindex="-1" id="editModal" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold" id="editModalLabel">แก้ไขข้อมูล</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-6 bg-gray-100 rounded-lg shadow-md">
                <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-6" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stu_id" class="block text-base font-medium text-gray-700">เป็นผู้ปกครองของ:</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center disabled" id="stu_id" name="stu_id" required disabled>
                                <option selected> -- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label for="image1" class="block text-base font-medium text-gray-700">รูปภาพ:</label>
                            <input type="file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="image1" id="image1" accept="image/*">
                            <img id="image-preview1" src="#" alt="Preview1" class="mt-2 max-w-xs rounded-md shadow-md hidden">
                        </div>
                    </div>
                    <div>
                        <label for="name" class="block text-base font-medium text-gray-700">ชื่อ-สกุลผู้ปกครอง:</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" required>
                    </div>
                    <div>
                        <label for="address" class="block text-base font-medium text-gray-700">ที่อยู่ผู้ปกครอง:</label>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" name="address" id="address" rows="3" required></textarea>
                    </div>
                    <div>
                        <label for="tel" class="block text-base font-medium text-gray-700">โทรศัพท์มือถือผู้ปกครอง:</label>
                        <input type="text" name="tel" id="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base" maxlength="10" required>
                    </div>
                    <div>
                        <label for="pos" class="block text-base font-medium text-gray-700">ตำแหน่ง:</label>
                        <select name="pos" id="pos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base text-center" required>
                            <option value="" selected>--เลือก--</option>
                            <option value="1">ประธาน</option>
                            <option value="2">กรรมการ</option>
                            <option value="3">เลขานุการ</option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
                        <input type="hidden" name="edit_id" id="edit_id">
                        <input type="hidden" name="pee" value="<?=$pee?>">
                        <input type="submit" name="btn_submit" class="btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" value="บันทึกการเปลี่ยนแปลง">
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
    const termValue = <?= $term ?>;
    const PeeValue = <?= $pee?>;

    const teachers = <?= json_encode($teacher->getTeachersByClassAndRoom($class, $room)); ?>;

    // Fetch student data for the dropdown in addModal
    $.ajax({
        url: 'api/fetch_student_classroom.php',
        method: 'GET',
        dataType: 'json',
        data: { class: classId, room: roomId },
        success: function(response) {
            if (response.success) {
                const studentDropdown = $('#addModal #stu_id');
                const studentDropdownEdit = $('#editModal #stu_id');
                response.data.forEach(student => {
                    studentDropdown.append(
                        `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}</option>`
                    );
                    studentDropdownEdit.append(
                        `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}</option>`
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
                data: { class: classId, room: roomId, pee: PeeValue },
            });

            if (!response.success) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                return;
            }

            const table = $('#record_table').DataTable({
                destroy: true,
                paging: false,
                searching: false,
                ordering: false,
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, className: 'text-center', width: '6%' },  // แถวที่ 1
                    { targets: 1, className: 'text-center', width: '20%' }, // แถวที่ 2
                    { targets: 2, className: 'text-left text-semibold' },   // แถวที่ 3 (ไม่กำหนดความกว้าง)
                    { targets: 3, className: 'text-center', width: '10%' }, // แถวที่ 4
                    { targets: 4, className: 'text-center', width: '10%' }, // แถวที่ 5
                    { targets: 5, className: 'text-center', width: '15%' }, // แถวที่ 6 (ไม่กำหนดความกว้าง)
                    { targets: 6, className: 'text-center', width: '10%' }  // แถวที่ 7 (ไม่กำหนดความกว้าง)
                ],
                info: false,
                lengthChange: true,
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
                    const position = item.parn_pos === '1' ? 'ประธาน' : item.parn_pos === '2' ? 'กรรมการ' : 'เลขานุการ';

                    table.row.add([
                        index + 1,
                        item.parn_name,
                        item.parn_addr,
                        item.parn_tel || 'ไม่มีข้อมูล',
                        position,
                        `<img src="https://std.phichai.ac.th/teacher/uploads/photopar/${item.parn_photo}" alt="Par Picture" class="rounded-full w-15 h-15">`,
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
            url: 'api/insert_boardparent.php',
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
                        window.location.reload(); // Reload the page to reflect changes
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
    window.printPage = function () {
        const cardElement = document.querySelector('.card');
        if (!cardElement) {
            alert('ไม่พบเนื้อหาที่จะพิมพ์');
            return;
        }
    
        const printContents = cardElement.cloneNode(true);
        const printWindow = window.open('', '', 'width=900,height=700');

        // สร้างส่วนลายเซ็นต์ครูที่ปรึกษา
        let teacherSignatures = '<div class="flex justify-end mt-8"><div class="text-center">';
        teachers.forEach(teacher => {
            teacherSignatures += '<p class="text-lg font-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ...............................................ครูที่ปรึกษา</p>';
            teacherSignatures += `<p class="text-lg">(${teacher.Teach_name})</p>`;
            teacherSignatures += '<br>';
        });
        teacherSignatures += '</div></div>';
    
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>พิมพ์รายงาน</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body {
                            font-family: "TH Sarabun New", sans-serif;
                            margin: 20px;
                            background: none;
                            color: black;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                        }
                        th {
                            background-color: #4CAF50 !important;
                            color: white !important;
                            text-align: center;
                        }
                        tr:nth-child(even) {
                            background-color: #f2f2f2 !important;
                        }
                        tr:hover {
                            background-color: #ddd !important;
                        }
                        @media print {
                            button {
                                display: none !important;
                            }
                            table th:nth-child(7), /* ซ่อนหัวคอลัมน์ "จัดการ" */
                            table td:nth-child(7) { /* ซ่อนข้อมูลในคอลัมน์ "จัดการ" */
                                display: none;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </head>
                <body class="p-4">
                    ${printContents.innerHTML}
                    <br>
                    ${teacherSignatures}
                </body>
            </html>
        `);
        printWindow.document.close();
    
        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    };

    // Function to set up the print layout
    function setupPrintLayout() {
        var style = '@page { size: A4 portrait; margin: 0.5in; }';
        var printStyle = document.createElement('style');
        printStyle.appendChild(document.createTextNode(style));
        document.head.appendChild(printStyle);
    }


    $(document).on('click', '.editBtn', function() {
        const studentId = $(this).data('id');

        // Fetch student data for editing
        $.ajax({
            url: 'api/fetch_boardparent_data.php',
            method: 'GET',
            data: { id: studentId, pee: <?= $pee ?> },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Populate modal fields with fetched data
                    $('#editModal #stu_id').val(response.data.Stu_id);
                    $('#editModal #name').val(response.data.parn_name);
                    $('#editModal #address').val(response.data.parn_addr);
                    $('#editModal #tel').val(response.data.parn_tel);
                    $('#editModal #pos').val(response.data.parn_pos);
                    if (response.data.parn_photo) {
                        $('#editModal #image-preview1')
                            .attr('src', `https://std.phichai.ac.th/teacher/uploads/photopar/${response.data.parn_photo}`)
                            .removeClass('hidden');
                    } else {
                        $('#editModal #image-preview1').addClass('hidden');
                    }
                    $('#editModal #edit_id').val(response.data.Stu_id);
                    $('#editModal').modal('show');
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
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
            url: 'api/update_boardparent_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // ← เพิ่มตรงนี้
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลสำเร็จ', 'success').then(() => {
                        $('#editModal').modal('hide');
                        loadTable(<?= $class ?>, <?= $room ?>);
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
                    url: 'api/delete_boardparent_data.php',
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
