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
                        <h5 class="text-center text-lg">ภาพกิจกรรมการประชุมผู้ปกครอง ระดับชั้นมัธยมศึกษาปีที่ <?= $class.'/'.$room; ?></h5>
                        <h5 class="text-center text-lg">ภาคเรียนที่ <?= $term; ?> ปีการศึกษา <?=$pee?></h5>
                        <h5 class="text-center text-lg" id="dateMeeting">วันเสาร์ที่ 14 ธันวาคม พ.ศ.2567</h5>
                        <h5 class="text-center text-lg" >โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์</h5>


                    <div class="text-left">

                    <button type="button" id="addButton" class="btn bg-cyan-500 text-white text-left mb-3 mt-2" data-toggle="modal" data-target="#addModal">
                    <i class="fas fa-plus"></i> เพิ่มรูปภาพการประชุม <i class="fas fa-plus"></i></button>
        
                    <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 mt-3 mb-3 mx-auto">
                            <div id="pictureGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
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

<!-- Modal สำหรับอัปโหลดรูปภาพ -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">อัปโหลดรูปภาพ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="uploadImage1">เลือกรูปภาพ 1</label>
                            <input type="file" class="form-control-file" id="uploadImage1" name="uploadImage[]" accept="image/*" onchange="previewImage(this, 'preview1')" required>
                            <img id="preview1" src="#" alt="Preview 1" class="mt-2 w-full h-auto hidden rounded shadow-md">
                        </div>
                        <div class="form-group">
                            <label for="uploadImage2">เลือกรูปภาพ 2</label>
                            <input type="file" class="form-control-file" id="uploadImage2" name="uploadImage[]" accept="image/*" onchange="previewImage(this, 'preview2')">
                            <img id="preview2" src="#" alt="Preview 2" class="mt-2 w-full h-auto hidden rounded shadow-md">
                        </div>
                        <div class="form-group">
                            <label for="uploadImage3">เลือกรูปภาพ 3</label>
                            <input type="file" class="form-control-file" id="uploadImage3" name="uploadImage[]" accept="image/*" onchange="previewImage(this, 'preview3')">
                            <img id="preview3" src="#" alt="Preview 3" class="mt-2 w-full h-auto hidden rounded shadow-md">
                        </div>
                        <div class="form-group">
                            <label for="uploadImage4">เลือกรูปภาพ 4</label>
                            <input type="file" class="form-control-file" id="uploadImage4" name="uploadImage[]" accept="image/*" onchange="previewImage(this, 'preview4')">
                            <img id="preview4" src="#" alt="Preview 4" class="mt-2 w-full h-auto hidden rounded shadow-md">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">อัปโหลด</button>
                </div>
            </form>
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

    function previewImage(input, previewId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block'; // Ensure the image is displayed
                preview.classList.remove('hidden'); // Remove hidden class if present
            };
            reader.readAsDataURL(file);
        } else {
            const preview = document.getElementById(previewId);
            preview.src = '#';
            preview.style.display = 'none'; // Hide the image if no file is selected
            preview.classList.add('hidden'); // Add hidden class if necessary
        }
    }

    // Attach the previewImage function to file inputs
    $('#uploadImage1').on('change', function() {
        previewImage(this, 'preview1');
    });
    $('#uploadImage2').on('change', function() {
        previewImage(this, 'preview2');
    });
    $('#uploadImage3').on('change', function() {
        previewImage(this, 'preview3');
    });
    $('#uploadImage4').on('change', function() {
        previewImage(this, 'preview4');
    });

    $.ajax({
        url: 'api/fetch_picture_meeting.php',
        method: 'GET',
        dataType: 'json',
        data: {
            class: classId,
            room: roomId,
            term: termValue,
            pee: PeeValue
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const pictureGrid = $('#pictureGrid');
                response.data.forEach(picture => {
                    const imgElement = `
                        <a href="${picture.url}" target="_blank" rel="noopener noreferrer">
                            <img src="${picture.url}" alt="${picture.alt}"
                                class="w-full max-w-[600px] h-auto max-h-[300px] rounded shadow-md border border-black object-cover mx-auto" />
                        </a>
                    `;
                    pictureGrid.append(imgElement);
                });
            } else {
                $('#pictureGrid').html('<p class="text-center text-gray-500">ไม่มีรูปภาพที่จะแสดง</p>');
            }
        },
        error: function() {
            $('#pictureGrid').html('<p class="text-center text-red-500">เกิดข้อผิดพลาดในการโหลดรูปภาพ</p>');
        }
    });

    // Handle form submission for uploading images
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('class', <?= $class ?>);
        formData.append('room', <?= $room ?>);
        formData.append('term', <?= $term ?>);
        formData.append('pee', <?= $pee ?>);

        $.ajax({
            url: 'api/insert_picture_meeting.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('อัปโหลดรูปภาพสำเร็จ');
                    location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');
            }
        });
    });

    // Function to handle printing
    window.printPage = function () {
        const printContents = document.querySelector('.card').cloneNode(true);
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
                        @media print {
                            button {
                                display: none !important;
                            }
                        }
                        body {
                            background: none !important;
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
    
});
</script>
</body>
</html>
