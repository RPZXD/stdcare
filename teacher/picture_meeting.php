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
    
    /* Modern Card Styling */
    .meeting-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
        overflow: hidden;
    }
    
    .meeting-card-inner {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        margin: 4px;
    }
    
    /* Header Gradient */
    .header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        color: white;
        text-align: center;
    }
    
    .header-gradient h3, .header-gradient h4, .header-gradient h5 {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Button Styling */
    .btn-modern {
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: none;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    .btn-upload {
        background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
        color: white;
    }
    
    .btn-print {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .btn-date {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    /* Picture Card */
    .picture-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .picture-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .picture-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .picture-card:hover img {
        transform: scale(1.05);
    }
    
    .picture-card-actions {
        padding: 15px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    /* Date Display */
    .date-display {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .date-display:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.02);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 16px;
    }
    
    .empty-state-icon {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    /* Modal Styling */
    .modal-modern .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }
    
    .modal-modern .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    .modal-modern .modal-header .close {
        color: white;
        opacity: 1;
    }
    
    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 3px solid rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        border-top-color: #667eea;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Upload Preview Grid */
    .upload-preview-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .upload-preview-item {
        position: relative;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 10px;
        transition: all 0.3s ease;
    }
    
    .upload-preview-item:hover {
        border-color: #667eea;
    }
    
    .upload-preview-item img {
        border-radius: 8px;
        max-height: 120px;
        object-fit: cover;
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
          </div>
        </div>
      </div>
    </div>

  <section class="content">
        <div class="container-fluid">
            <div class="meeting-card col-md-12 p-0">
                <div class="meeting-card-inner">
                    <!-- Header Section -->
                    <div class="header-gradient">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" 
                             class="rounded-full mb-4 mx-auto shadow-lg" 
                             style="width: 80px; height: 80px; border: 3px solid white;">
                        
                        <h3 class="text-2xl font-bold mb-2">
                            <i class="fas fa-images mr-2"></i>
                            ภาพกิจกรรมการประชุมผู้ปกครอง
                        </h3>
                        <h4 class="text-xl mb-2">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= $class.'/'.$room; ?>
                        </h4>
                        <h5 class="text-lg mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            ภาคเรียนที่ <?= $term; ?> ปีการศึกษา <?=$pee?>
                        </h5>
                        
                        <!-- Editable Date Display -->
                        <div class="date-display" id="dateDisplayBtn" data-toggle="modal" data-target="#dateModal">
                            <i class="fas fa-calendar-day"></i>
                            <span id="dateMeetingText">วันที่ 8-9 พฤษภาคม พ.ศ.2568</span>
                            <i class="fas fa-edit" style="font-size: 12px;"></i>
                        </div>
                        
                        <h5 class="text-lg mt-3">
                            <i class="fas fa-school mr-2"></i>
                            โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์
                        </h5>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="p-4">
                        <div class="flex flex-wrap gap-3 justify-center mb-4">
                            <button type="button" id="addButton" class="btn btn-modern btn-upload" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> เพิ่มรูปภาพ
                            </button>
                            
                            <button type="button" class="btn btn-modern btn-date" data-toggle="modal" data-target="#dateModal">
                                <i class="fas fa-calendar-edit mr-2"></i> กำหนดวันที่
                            </button>
                            
                            <button class="btn btn-modern btn-print" id="printButton" onclick="printPage()">
                                <i class="fas fa-print mr-2"></i> พิมพ์รายงาน
                            </button>
                        </div>
                        
                        <!-- Picture Grid -->
                        <div id="pictureGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
                            <!-- Loading State -->
                            <div class="col-span-full text-center py-10" id="loadingState">
                                <div class="loading-spinner"></div>
                                <p class="text-gray-500 mt-4">กำลังโหลดรูปภาพ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  </div>
  
  <?php require_once('../footer.php');?>

</div>

<!-- Modal สำหรับกำหนดวันที่ -->
<div class="modal fade modal-modern" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateModalLabel">
                    <i class="fas fa-calendar-alt mr-2"></i> กำหนดวันที่ประชุมผู้ปกครอง
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-bold text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-1"></i> วันที่เริ่มต้น
                    </label>
                    <input type="date" class="form-control form-control-lg" id="dateStart" 
                           style="border-radius: 10px; border: 2px solid #e2e8f0;">
                </div>
                <div class="form-group mt-3">
                    <label class="font-bold text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-1"></i> วันที่สิ้นสุด (ถ้ามี)
                    </label>
                    <input type="date" class="form-control form-control-lg" id="dateEnd"
                           style="border-radius: 10px; border: 2px solid #e2e8f0;">
                </div>
                <p class="text-gray-500 text-sm mt-2">
                    <i class="fas fa-info-circle mr-1"></i> 
                    หากประชุมวันเดียว ไม่ต้องกรอกวันที่สิ้นสุด
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times mr-1"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" id="saveDateBtn" 
                        style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-save mr-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับอัปโหลดรูปภาพ -->
<div class="modal fade modal-modern" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> อัปโหลดรูปภาพการประชุม
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="upload-preview-grid">
                        <div class="upload-preview-item">
                            <label for="uploadImage1" class="font-bold text-gray-700 mb-2">
                                <i class="fas fa-image mr-1"></i> รูปภาพที่ 1 <span class="text-red-500">*</span>
                            </label>
                            <input type="file" class="form-control-file" id="uploadImage1" name="uploadImage[]" accept="image/*" required>
                            <img id="preview1" src="#" alt="Preview 1" class="mt-2 w-full hidden rounded shadow-md">
                        </div>
                        <div class="upload-preview-item">
                            <label for="uploadImage2" class="font-bold text-gray-700 mb-2">
                                <i class="fas fa-image mr-1"></i> รูปภาพที่ 2
                            </label>
                            <input type="file" class="form-control-file" id="uploadImage2" name="uploadImage[]" accept="image/*">
                            <img id="preview2" src="#" alt="Preview 2" class="mt-2 w-full hidden rounded shadow-md">
                        </div>
                        <div class="upload-preview-item">
                            <label for="uploadImage3" class="font-bold text-gray-700 mb-2">
                                <i class="fas fa-image mr-1"></i> รูปภาพที่ 3
                            </label>
                            <input type="file" class="form-control-file" id="uploadImage3" name="uploadImage[]" accept="image/*">
                            <img id="preview3" src="#" alt="Preview 3" class="mt-2 w-full hidden rounded shadow-md">
                        </div>
                        <div class="upload-preview-item">
                            <label for="uploadImage4" class="font-bold text-gray-700 mb-2">
                                <i class="fas fa-image mr-1"></i> รูปภาพที่ 4
                            </label>
                            <input type="file" class="form-control-file" id="uploadImage4" name="uploadImage[]" accept="image/*">
                            <img id="preview4" src="#" alt="Preview 4" class="mt-2 w-full hidden rounded shadow-md">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">
                        <i class="fas fa-times mr-1"></i> ปิด
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px; background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%); border: none;">
                        <i class="fas fa-upload mr-1"></i> อัปโหลด
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once('script.php');?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const classId = <?= $class ?>;
    const roomId = <?= $room ?>;
    const termValue = <?= $term ?>;
    const PeeValue = <?= $pee?>;
    const storageKey = `meetingDate_${classId}_${roomId}_${termValue}_${PeeValue}`;

    const teachers = <?= json_encode($teacher->getTeachersByClassAndRoom($class, $room)); ?>;

    // Convert date to Thai format
    function convertToThaiDate(dateString) {
        const months = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];
        const date = new Date(dateString);
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear() + 543;
        return `${day} ${month} พ.ศ.${year}`;
    }

    // Load saved date from localStorage
    function loadSavedDate() {
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            const data = JSON.parse(savedData);
            if (data.dateStart) {
                $('#dateStart').val(data.dateStart);
            }
            if (data.dateEnd) {
                $('#dateEnd').val(data.dateEnd);
            }
            updateDateDisplay(data.dateStart, data.dateEnd);
        }
    }

    // Update date display text
    function updateDateDisplay(startDate, endDate) {
        let displayText = '';
        if (startDate) {
            const startParts = startDate.split('-');
            const startDay = parseInt(startParts[2]);
            const startMonth = parseInt(startParts[1]) - 1;
            const startYear = parseInt(startParts[0]) + 543;
            
            const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                           'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
            
            if (endDate && endDate !== startDate) {
                const endParts = endDate.split('-');
                const endDay = parseInt(endParts[2]);
                const endMonth = parseInt(endParts[1]) - 1;
                const endYear = parseInt(endParts[0]) + 543;
                
                if (startMonth === endMonth && startYear === endYear) {
                    displayText = `วันที่ ${startDay}-${endDay} ${months[startMonth]} พ.ศ.${startYear}`;
                } else {
                    displayText = `วันที่ ${startDay} ${months[startMonth]} - ${endDay} ${months[endMonth]} พ.ศ.${endYear}`;
                }
            } else {
                displayText = `วันที่ ${startDay} ${months[startMonth]} พ.ศ.${startYear}`;
            }
        } else {
            displayText = 'วันที่ 8-9 พฤษภาคม พ.ศ.2568';
        }
        $('#dateMeetingText').text(displayText);
    }

    // Save date button handler
    $('#saveDateBtn').on('click', function() {
        const dateStart = $('#dateStart').val();
        const dateEnd = $('#dateEnd').val();
        
        if (!dateStart) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกวันที่เริ่มต้น',
                confirmButtonColor: '#667eea'
            });
            return;
        }
        
        const data = { dateStart, dateEnd };
        localStorage.setItem(storageKey, JSON.stringify(data));
        updateDateDisplay(dateStart, dateEnd);
        
        $('#dateModal').modal('hide');
        
        Swal.fire({
            icon: 'success',
            title: 'บันทึกวันที่สำเร็จ',
            showConfirmButton: false,
            timer: 1200
        });
    });

    // Load saved date on page load
    loadSavedDate();

    // Preview image function
    function previewImage(input, previewId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            const preview = document.getElementById(previewId);
            preview.src = '#';
            preview.style.display = 'none';
            preview.classList.add('hidden');
        }
    }

    // Attach the previewImage function to file inputs
    $('#uploadImage1').on('change', function() { previewImage(this, 'preview1'); });
    $('#uploadImage2').on('change', function() { previewImage(this, 'preview2'); });
    $('#uploadImage3').on('change', function() { previewImage(this, 'preview3'); });
    $('#uploadImage4').on('change', function() { previewImage(this, 'preview4'); });

    // Fetch pictures
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
            $('#loadingState').remove();
            
            if (response.success && response.data.length > 0) {
                const pictureGrid = $('#pictureGrid');
                response.data.forEach((picture, idx) => {
                    const imgElement = `
                        <div class="picture-card">
                            <a href="${picture.url}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden">
                                <img src="${picture.url}" alt="${picture.alt || 'ภาพการประชุม'}">
                            </a>
                            <div class="picture-card-actions text-center">
                                <button class="btn btn-danger btn-sm delete-picture-btn" data-id="${picture.id || idx}" style="border-radius: 8px;">
                                    <i class="fas fa-trash-alt mr-1"></i> ลบรูปภาพ
                                </button>
                            </div>
                        </div>
                    `;
                    pictureGrid.append(imgElement);
                });

                // Attach delete handler
                $('.delete-picture-btn').on('click', function() {
                    const pictureIdx = $(this).data('id');
                    Swal.fire({
                        title: 'ยืนยันการลบ',
                        text: 'คุณต้องการลบรูปภาพนี้ใช่หรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> ใช่, ลบเลย!',
                        cancelButtonText: '<i class="fas fa-times mr-1"></i> ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'api/delete_picture_meeting.php',
                                method: 'POST',
                                data: { id: pictureIdx, class: classId, room: roomId, term: termValue, pee: PeeValue },
                                dataType: 'json',
                                success: function(res) {
                                    if (res.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'ลบรูปภาพสำเร็จ',
                                            showConfirmButton: false,
                                            timer: 1200
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire('เกิดข้อผิดพลาด', res.message, 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('เกิดข้อผิดพลาด', 'เกิดข้อผิดพลาดในการลบรูปภาพ', 'error');
                                }
                            });
                        }
                    });
                });
            } else {
                $('#pictureGrid').html(`
                    <div class="col-span-full empty-state">
                        <div class="empty-state-icon">📷</div>
                        <h4 class="text-xl font-bold text-gray-600 mb-2">ยังไม่มีรูปภาพ</h4>
                        <p class="text-gray-500">คลิกปุ่ม "เพิ่มรูปภาพ" เพื่ออัปโหลดรูปภาพการประชุม</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#loadingState').remove();
            Swal.fire('เกิดข้อผิดพลาด', 'เกิดข้อผิดพลาดในการโหลดรูปภาพ', 'error');
            $('#pictureGrid').html(`
                <div class="col-span-full empty-state" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                    <div class="empty-state-icon">❌</div>
                    <h4 class="text-xl font-bold text-red-600 mb-2">เกิดข้อผิดพลาด</h4>
                    <p class="text-red-500">ไม่สามารถโหลดรูปภาพได้ กรุณาลองใหม่อีกครั้ง</p>
                </div>
            `);
        }
    });

    // Handle form submission for uploading images
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('class', classId);
        formData.append('room', roomId);
        formData.append('term', termValue);
        formData.append('pee', PeeValue);

        Swal.fire({
            title: 'กำลังอัปโหลด...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'api/insert_picture_meeting.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดรูปภาพสำเร็จ',
                        showConfirmButton: false,
                        timer: 1200
                    }).then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('เกิดข้อผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ', 'error');
            }
        });
    });

    // Function to handle printing
    window.printPage = function () {
        const dateText = $('#dateMeetingText').text();
        const printWindow = window.open('', '', 'width=900,height=700');

        // รวบรวมรูปภาพทั้งหมด
        let imagesHtml = '';
        $('#pictureGrid .picture-card img').each(function() {
            imagesHtml += `<div class="print-image-item"><img src="${$(this).attr('src')}" alt="ภาพการประชุม"></div>`;
        });

        // สร้างส่วนลายเซ็นต์ครูที่ปรึกษา
        let teacherSignatures = '<div class="signature-section">';
        teachers.forEach(teacher => {
            teacherSignatures += `
                <div class="signature-item">
                    <p class="signature-line">ลงชื่อ...............................................ครูที่ปรึกษา</p>
                    <p class="signature-name">(${teacher.Teach_name})</p>
                </div>
            `;
        });
        teacherSignatures += '</div>';

        printWindow.document.open();
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>ภาพกิจกรรมการประชุมผู้ปกครอง ม.${classId}/${roomId}</title>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                        
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        
                        body {
                            font-family: "Sarabun", "TH Sarabun New", sans-serif;
                            padding: 20px 40px;
                            background: white;
                            color: black;
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-bottom: 20px;
                            border-bottom: 2px solid #333;
                        }
                        
                        .print-logo {
                            width: 70px;
                            height: 70px;
                            margin-bottom: 15px;
                        }
                        
                        .print-title {
                            font-size: 22px;
                            font-weight: 700;
                            margin-bottom: 8px;
                        }
                        
                        .print-subtitle {
                            font-size: 18px;
                            font-weight: 600;
                            margin-bottom: 5px;
                        }
                        
                        .print-info {
                            font-size: 16px;
                            margin-bottom: 3px;
                        }
                        
                        .print-date {
                            font-size: 18px;
                            font-weight: 600;
                            margin-top: 10px;
                            padding: 8px 20px;
                            background: #f5f5f5;
                            display: inline-block;
                            border-radius: 5px;
                        }
                        
                        .print-images-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 15px;
                            margin: 20px 0;
                        }
                        
                        .print-image-item {
                            page-break-inside: avoid;
                        }
                        
                        .print-image-item img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            border: 1px solid #333;
                            border-radius: 5px;
                        }
                        
                        .signature-section {
                            margin-top: 40px;
                            text-align: right;
                            page-break-inside: avoid;
                        }
                        
                        .signature-item {
                            margin-bottom: 25px;
                        }
                        
                        .signature-line {
                            font-size: 16px;
                        }
                        
                        .signature-name {
                            font-size: 16px;
                            margin-top: 5px;
                        }
                        
                        @media print {
                            body {
                                padding: 10px 30px;
                            }
                            
                            .print-images-grid {
                                gap: 10px;
                            }
                            
                            .print-image-item img {
                                height: 160px;
                            }
                            
                            @page {
                                size: A4 portrait;
                                margin: 15mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <img src="../dist/img/logo-phicha.png" alt="Logo" class="print-logo">
                        <h1 class="print-title">ภาพกิจกรรมการประชุมผู้ปกครอง</h1>
                        <p class="print-subtitle">ระดับชั้นมัธยมศึกษาปีที่ ${classId}/${roomId}</p>
                        <p class="print-info">ภาคเรียนที่ ${termValue} ปีการศึกษา ${PeeValue}</p>
                        <p class="print-date">${dateText}</p>
                        <p class="print-info" style="margin-top: 10px;">โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์</p>
                    </div>
                    
                    <div class="print-images-grid">
                        ${imagesHtml}
                    </div>
                    
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
});
</script>
</body>
</html>
