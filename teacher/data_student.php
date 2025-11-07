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
        '../login.php'
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<link rel="stylesheet" href="assets/css/student-management.css">

<style>
    /* เพิ่มใน assets/css/student-management.css หรือในส่วน <style> */
.cropper-container {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.cropper-canvas {
    cursor: grab;
    transition: transform 0.2s ease-out;
}

.cropper-canvas:active {
    cursor: grabbing;
}

/* ลดความไวในการลาก crop box */
.cropper-crop-box {
    cursor: move;
    transition: all 0.1s ease-out;
}

.cropper-drag-box {
    cursor: move;
    opacity: 0.1;
}

/* ปรับแต่ง handles ให้ใหญ่ขึ้นและง่ายต่อการจับ */
.cropper-point {
    width: 12px !important;
    height: 12px !important;
    background-color: #007bff;
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    opacity: 0.8;
}

.cropper-point:hover {
    opacity: 1;
    transform: scale(1.2);
}

/* ลดความไวในการลากขอบ crop box */
.cropper-line {
    background-color: #007bff;
    opacity: 0.6;
}

.cropper-line:hover {
    opacity: 1;
}

/* Hide scrollbars for the slide show container */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
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

    <section class="content py-8 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <!-- Header Card -->
                <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 text-white rounded-3xl shadow-2xl p-10 mb-8 transform hover:scale-105 transition-all duration-500">
                    <div class="text-center">
                        <div class="flex justify-center mb-6">
                            <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="rounded-full w-24 h-24 border-4 border-white shadow-xl animate-pulse">
                        </div>
                        <h2 class="text-4xl font-bold mb-4 animate-bounce">👨‍🎓 รายชื่อนักเรียน</h2>
                        <h4 class="text-2xl font-semibold opacity-90 mb-6">ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?> ปีการศึกษา <?=$pee?></h4>
                        
                        <!-- Enhanced Search Box -->
                        <div class="max-w-md mx-auto mb-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-purple-200 text-xl">🔍</span>
                                </div>
                                <input type="text" id="studentSearch" class="block w-full pl-12 pr-4 py-3 text-lg text-gray-900 bg-white bg-opacity-90 backdrop-blur-sm border border-purple-300 rounded-full shadow-lg focus:ring-4 focus:ring-purple-400 focus:border-purple-500 transition-all duration-300 placeholder-gray-500" placeholder="ค้นหาชื่อนักเรียน, รหัส, เลขที่ หรือชื่อเล่น...">
                            </div>
                        </div>

                        <!-- Enhanced Toggle Switch -->
                        <div class="flex items-center justify-center space-x-6 mb-6">
                            <span class="text-3xl animate-bounce">🙅‍♂️</span>
                            <div class="flex items-center space-x-4 bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-3">
                                <div class="toggle-switch" id="allowEditSwitch">
                                    <span class="toggle-emoji">🔒</span>
                                </div>
                                <span class="text-lg font-medium text-white" id="editStatusText">ปิดให้นักเรียนแก้ไขข้อมูล</span>
                            </div>
                            <span class="text-3xl animate-bounce">🙆‍♀️</span>
                        </div>

                        <!-- Print Button -->
                        <div class="text-center">
                            <button id="printStudentList" class="bg-gradient-to-r from-green-500 to-blue-500 text-white font-bold py-4 px-8 rounded-full shadow-xl hover:shadow-2xl hover:shadow-green-500/50 hover:scale-110 transition-all duration-300 animate-pulse">
                                <i class="fas fa-print mr-2"></i>🖨️ พิมพ์รายชื่อนักเรียน
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Student Cards Slide Show -->
                <div class="relative">
                    <div class="flex space-x-6 overflow-x-auto pb-6 snap-x snap-mandatory scrollbar-hide" id="showDataStudent" style="scroll-behavior: smooth;">
                        <!-- Student cards will be injected here -->
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 backdrop-blur-sm rounded-full p-3 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 text-purple-600 text-2xl z-10" id="prevBtn">
                        ◀️
                    </button>
                    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 backdrop-blur-sm rounded-full p-3 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 text-purple-600 text-2xl z-10" id="nextBtn">
                        ▶️
                    </button>
                </div>
            </div>
        </div>
    </section>
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<!-- Enhanced Modal Structure for Viewing Student -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-xl font-bold" id="studentModalLabel">📋 ข้อมูลนักเรียน</h5>
        <button type="button" class="close text-gray-900 hover:text-gray-200" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <!-- Student details will be loaded here -->
      </div>
      <div class="modal-footer bg-gray-50">
        <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Modal Structure for Editing Student -->
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-lg font-bold" id="editStudentModalLabel">✏️ แก้ไขข้อมูลนักเรียน</h5>
        <button type="button" class="close text-gray-900 hover:text-gray-200" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Edit form will be loaded here -->
      </div>
      <div class="modal-footer bg-gray-50">
        <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary btn-modern" id="saveChanges">💾 บันทึกการเปลี่ยนแปลง</button>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Image Cropper Modal -->
<div class="modal fade" id="imageCropModal" tabindex="-1" role="dialog" aria-labelledby="imageCropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold" id="imageCropModalLabel">🖼️ ตัดแต่งรูปภาพ</h5>
                <button type="button" class="close text-gray-900 hover:text-gray-200" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="crop-container">
                            <img id="cropImage" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="font-semibold mb-3">ตัวอย่าง</h6>
                            <div class="crop-preview" id="cropPreview"></div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-info btn-modern btn-sm" onclick="cropper.rotate(90)">↻ หมุนขวา</button>
                                <button type="button" class="btn btn-info btn-modern btn-sm" onclick="cropper.rotate(-90)">↺ หมุนซ้าย</button>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-warning btn-modern btn-sm" onclick="cropper.reset()">🔄 รีเซ็ต</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50">
                <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success btn-modern" id="cropAndUpload">✅ ตัดแต่งและอัปโหลด</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="text-center text-gray-900">
        <div class="loading-spinner"></div>
        <p class="mt-3">กำลังประมวลผล...</p>
    </div>
</div>


<?php require_once('script.php');?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let cropper;
let currentStudentId;

$(document).ready(function() {

// Enhanced Toggle Switch Logic
function updateSwitchUI(isAllowed, by, timestamp) {
    const switchElement = $('#allowEditSwitch');
    const emojiElement = $('.toggle-emoji');
    const statusText = $('#editStatusText');
    
    switchElement.toggleClass('active', isAllowed);
    emojiElement.text(isAllowed ? '🔓' : '🔒');
    statusText.html(
        isAllowed
            ? '✅ เปิดให้นักเรียนแก้ไขข้อมูล'
            : '❌ ปิดให้นักเรียนแก้ไขข้อมูล'
    );
    
    if (by && timestamp) {
        statusText.append(
            `<br><small class="text-gray-900">โดย ${by} (${timestamp})</small>`
        );
    }
}

function getRoomKey(cls, rm) {
    return cls + '-' + rm;
}

function fetchEditPermission() {
    const classValue = <?= json_encode($class) ?>;
    const roomValue = <?= json_encode($room) ?>;
    const roomKey = getRoomKey(classValue, roomValue);
    
    $.get('api/get_student_edit_permission.php', {
        room_key: roomKey
    }, function(res) {
        let isAllowed = false, by = '', timestamp = '';
        try {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            isAllowed = !!data.allowEdit;
            by = data.by || '';
            timestamp = data.timestamp || '';
        } catch(e) {}
        updateSwitchUI(isAllowed, by, timestamp);
    });
}

// Enhanced toggle switch click handler
$(document).on('click', '#allowEditSwitch', function() {
    const classValue = <?= json_encode($class) ?>;
    const roomValue = <?= json_encode($room) ?>;
    const roomKey = getRoomKey(classValue, roomValue);
    const isCurrentlyActive = $(this).hasClass('active');
    const newState = !isCurrentlyActive;
    
    showLoading();
    
    $.post('api/set_student_edit_permission.php', {
        room_key: roomKey,
        allowEdit: newState ? 1 : 0,
        by: <?= json_encode($teacher_name) ?>
    }, function(res) {
        hideLoading();
        fetchEditPermission();
          // Show success notification
        Swal.fire({
            icon: 'success',
            title: newState ? '🔓 เปิดการแก้ไข' : '🔒 ปิดการแก้ไข',
            text: newState ? 'นักเรียนสามารถแก้ไขข้อมูลได้แล้ว' : 'ปิดการแก้ไขข้อมูลเรียบร้อย',
            showConfirmButton: false,
            timer: 2000,
            toast: true,
            position: 'top-end'
        });
    }).fail(function() {
        hideLoading();
        Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
    });
});

fetchEditPermission();

// Enhanced student data loading with animations
async function loadStudentData() {
    try {
        showLoading();
        
        var classValue = <?=$class?>;
        var roomValue = <?=$room?>;

        const response = await $.ajax({
            url: 'api/fetch_data_student.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue }
        });

        hideLoading();

        if (!response.success) {
            Swal.fire('🚨 ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            return;
        }

        const showDataStudent = $('#showDataStudent');
        showDataStudent.empty();

        if (response.data.length === 0) {
            showDataStudent.html(`
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">📚</div>
                    <p class="text-xl font-semibold text-gray-900">ไม่พบข้อมูลนักเรียน</p>
                </div>
            `);
        } else {
            response.data.forEach((item, index) => {                const studentCard = `
                    <div class="student-card w-80 flex-shrink-0 snap-center bg-white bg-opacity-90 backdrop-blur-lg rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/50 hover:scale-105 transition-all duration-500 transform"
                        data-name="${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}"
                        data-id="${item.Stu_id}"
                        data-no="${item.Stu_no}"
                        data-nick="${item.Stu_nick}"
                        style="animation-delay: ${index * 0.1}s; opacity: 0;">
                        
                        <div class="relative">
                            <img class="w-full h-64 object-cover rounded-t-2xl" 
                                 src="../photo/${item.Stu_picture}" 
                                 alt="Student Picture"
                                 onerror="handleImageError(this, '${item.Stu_pre}${item.Stu_name}')">
                            <div class="absolute top-4 right-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full px-3 py-1 text-sm font-bold shadow-lg">
                                #${item.Stu_no}
                            </div>
                            <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                ${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="text-center mb-4">
                                <div class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                    รหัส: ${item.Stu_id}
                                </div>
                            </div>
                            
                            <div class="space-y-3 text-sm">
                                ${item.Stu_nick ? `<div class="flex justify-between items-center p-2 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                                    <span class="text-purple-700 font-medium">ชื่อเล่น:</span>
                                    <span class="font-semibold text-purple-800">${item.Stu_nick}</span>
                                </div>` : ''}
                                ${item.Stu_phone ? `<div class="flex justify-between items-center p-2 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                                    <span class="text-blue-700 font-medium">เบอร์:</span>
                                    <a href="tel:${item.Stu_phone}" class="text-blue-800 hover:text-blue-900 hover:underline font-semibold">
                                        📞 ${item.Stu_phone}
                                    </a>
                                </div>` : ''}
                                ${item.Par_phone ? `<div class="flex justify-between items-center p-2 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                                    <span class="text-green-700 font-medium">ผู้ปกครอง:</span>
                                    <a href="tel:${item.Par_phone}" class="text-green-800 hover:text-green-900 hover:underline font-semibold">
                                        👨‍👩‍👧‍👦 ${item.Par_phone}
                                    </a>
                                </div>` : ''}
                            </div>
                            
                            <div class="flex justify-center space-x-3 mt-6">
                                <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl hover:shadow-blue-500/50 hover:scale-110 transition-all duration-300" data-id="${item.Stu_id}" title="ดูข้อมูลทั้งหมด">
                                    <span class="text-lg">👀</span>
                                </button>
                                <button class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl hover:shadow-yellow-500/50 hover:scale-110 transition-all duration-300" data-id="${item.Stu_id}" title="แก้ไขข้อมูล">
                                    <span class="text-lg">✏️</span>
                                </button>
                                <button class="bg-gradient-to-r from-pink-500 to-pink-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl hover:shadow-pink-500/50 hover:scale-110 transition-all duration-300" data-id="${item.Stu_id}" title="เปลี่ยนรูปภาพ">
                                    <span class="text-lg">📷</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                showDataStudent.append(studentCard);
            });
            
            // Add entrance animation
            $('.student-card').each(function(index) {
                $(this).delay(index * 100).animate({'opacity': '1'}, 500);
            });
        }

    } catch (error) {
        hideLoading();
        Swal.fire('🚨 ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
        console.error(error);
    }
}

// Enhanced search functionality with debouncing
$('#studentSearch').on('input', function() {
    debouncedSearch($(this).val());
});

// Enhanced button handlers
$(document).on('click', '.btn-view', function() {
    var stuId = $(this).data('id');
    showLoading();
    
    $.ajax({
        url: 'api/view_student.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            hideLoading();
            $('#studentModal .modal-body').html(response);
            $('#studentModal').modal('show');
        },
        error: function() {
            hideLoading();
            Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
});

$(document).on('click', '.btn-edit', function() {
    var stuId = $(this).data('id');
    showLoading();
    
    $.ajax({
        url: 'api/edit_student_form.php',
        method: 'GET',
        data: { stu_id: stuId },
        success: function(response) {
            hideLoading();
            $('#editStudentModal .modal-body').html(response);
            $('#editStudentModal').modal('show');
        },
        error: function() {
            hideLoading();
            Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
});

// Enhanced photo upload with cropping
$(document).on('click', '.btn-photo', function() {
    currentStudentId = $(this).data('id');
    
    // Get student name and current photo for better UX
    const studentCard = $(this).closest('.student-card');
    const studentName = studentCard.attr('data-name');
    const currentPhoto = studentCard.find('.student-photo').attr('src');
    
    Swal.fire({
        title: `📷 จัดการรูปภาพ`,
        html: `
            <div class="text-center mb-4">
                <p class="text-gray-900">นักเรียน: <strong>${studentName}</strong></p>
            </div>
            
            <!-- Photo Management Options -->
            <div class="photo-options mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="option-card" id="useExistingPhoto" style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; margin-bottom: 15px;">
                            <div style="font-size: 2.5rem; margin-bottom: 10px;">🖼️</div>
                            <h6 style="margin-bottom: 8px; font-weight: bold;">ใช้ภาพเดิม</h6>
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 0;">ตัดแต่งภาพปัจจุบันใหม่</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="option-card" id="uploadNewPhoto" style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; margin-bottom: 15px;">
                            <div style="font-size: 2.5rem; margin-bottom: 10px;">📁</div>
                            <h6 style="margin-bottom: 8px; font-weight: bold;">อัปโหลดภาพใหม่</h6>
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 0;">เลือกไฟล์ภาพใหม่</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upload Section (Initially Hidden) -->
            <div id="uploadSection" style="display: none;">
                <div class="drop-zone" style="border: 2px dashed #cbd5e0; border-radius: 10px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s ease;" 
                     onclick="document.getElementById('photoInput').click()">
                    <div style="font-size: 3rem; margin-bottom: 10px;">📁</div>
                    <p>คลิกเพื่อเลือกรูปภาพ</p>
                    <p style="font-size: 0.8rem; color: #666;">หรือลากและวางรูปภาพที่นี่</p>
                </div>
                <input type="file" id="photoInput" accept="image/*" style="display: none;">
                <div class="mt-3">
                    <small class="text-gray-900">
                        รองรับไฟล์: JPG, PNG, GIF, WebP<br>
                        ขนาดไม่เกิน 5MB • จะมีเครื่องมือตัดแต่งรูปภาพ
                    </small>
                </div>
            </div>
            
            <!-- Current Photo Preview -->
            <div id="currentPhotoPreview" style="display: none; text-align: center; margin-top: 15px;">
                <img src="${currentPhoto}" style="max-width: 200px; max-height: 200px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" alt="ภาพปัจจุบัน">
                <p style="margin-top: 8px; font-size: 0.9rem; color: #666;">ภาพปัจจุบันของนักเรียน</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '📤 ต่อไป',
        cancelButtonText: '❌ ยกเลิก',
        confirmButtonColor: '#4facfe',
        cancelButtonColor: '#6c757d',
        width: '600px',
        didOpen: () => {
            let selectedOption = null;
            
            // Option selection handlers
            $('#useExistingPhoto').on('click', function() {
                selectedOption = 'existing';
                $('.option-card').css({
                    'border-color': '#e2e8f0',
                    'background-color': 'transparent'
                });
                $(this).css({
                    'border-color': '#4facfe',
                    'background-color': 'rgba(79, 172, 254, 0.1)'
                });
                $('#uploadSection').hide();
                $('#currentPhotoPreview').show();
                
                // Enable confirm button
                Swal.getConfirmButton().disabled = false;
                Swal.getConfirmButton().style.opacity = '1';
            });
            
            $('#uploadNewPhoto').on('click', function() {
                selectedOption = 'new';
                $('.option-card').css({
                    'border-color': '#e2e8f0',
                    'background-color': 'transparent'
                });
                $(this).css({
                    'border-color': '#4facfe',
                    'background-color': 'rgba(79, 172, 254, 0.1)'
                });
                $('#currentPhotoPreview').hide();
                $('#uploadSection').show();
                
                // Reset confirm button state
                Swal.getConfirmButton().disabled = true;
                Swal.getConfirmButton().style.opacity = '0.5';
            });
            
            // File input setup (same as before)
            const fileInput = document.getElementById('photoInput');
            const dropZone = document.querySelector('.drop-zone');
            
            if (fileInput && dropZone) {
                fileInput.onchange = function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        dropZone.innerHTML = `
                            <div style="font-size: 2rem; margin-bottom: 10px;">✅</div>
                            <p style="color: green;">เลือกไฟล์แล้ว: ${file.name}</p>
                            <p style="font-size: 0.8rem; color: #666;">คลิก "ต่อไป" เพื่อตัดแต่งรูปภาพ</p>
                        `;
                        
                        // Enable confirm button
                        Swal.getConfirmButton().disabled = false;
                        Swal.getConfirmButton().style.opacity = '1';
                    }
                };
                
                // Enhanced drag and drop functionality
                dropZone.ondragover = dropZone.ondragenter = function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#4facfe';
                    this.style.backgroundColor = 'rgba(79, 172, 254, 0.1)';
                };

                dropZone.ondragleave = function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#cbd5e0';
                    this.style.backgroundColor = 'transparent';
                };

                dropZone.ondrop = function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#cbd5e0';
                    this.style.backgroundColor = 'transparent';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        fileInput.onchange();
                    }
                };
            }
            
            // Initially disable confirm button
            Swal.getConfirmButton().disabled = true;
            Swal.getConfirmButton().style.opacity = '0.5';
            
            // Store selected option for preConfirm
            window.selectedPhotoOption = () => selectedOption;
        },
        preConfirm: () => {
            const option = window.selectedPhotoOption();
            
            if (!option) {
                Swal.showValidationMessage('กรุณาเลือกตัวเลือก');
                return false;
            }
            
            if (option === 'new') {
                const fileInput = document.getElementById('photoInput');
                const file = fileInput.files[0];
                
                if (!file) {
                    Swal.showValidationMessage('กรุณาเลือกรูปภาพ');
                    return false;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.showValidationMessage('ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน 5MB)');
                    return false;
                }
                
                // Validate file type
                if (!file.type.match('image.*')) {
                    Swal.showValidationMessage('กรุณาเลือกไฟล์รูปภาพ');
                    return false;
                }
                
                return { type: 'new', file: file };
            } else {
                return { type: 'existing', src: currentPhoto };
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            if (result.value.type === 'new') {
                initImageCropper(result.value.file);
            } else {
                initImageCropperFromURL(result.value.src);
            }
        }
    });
});

// Enhanced image cropper with better preview.
function initImageCropper(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        setupCropper(e.target.result);
    };
    reader.readAsDataURL(file);
}

// New function to initialize cropper from existing image URL
function initImageCropperFromURL(imageUrl) {
    // Handle relative URLs
    let fullImageUrl = imageUrl;
    if (imageUrl.startsWith('../')) {
        // Convert relative URL to absolute URL
        const baseUrl = window.location.origin + window.location.pathname.replace('/teacher/data_student.php', '');
        fullImageUrl = baseUrl + imageUrl.substring(2); // Remove '../'
    }
    
    // Load image with CORS handling
    const img = new Image();
    img.crossOrigin = 'anonymous';
    
    img.onload = function() {
        // Convert loaded image to canvas then to blob
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = this.naturalWidth;
        canvas.height = this.naturalHeight;
        ctx.drawImage(this, 0, 0);
        
        // Convert canvas to data URL
        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        setupCropper(dataUrl);
    };
    
    img.onerror = function() {
        // Fallback: try to load image directly
        setupCropper(fullImageUrl);
    };
    
    img.src = fullImageUrl;
}

// Refactored common cropper setup function
function setupCropper(imageSrc) {
    const cropImage = document.getElementById('cropImage');
    cropImage.src = imageSrc;
    
    // Remove any existing event handlers to prevent conflicts
    $('#imageCropModal').off('shown.bs.modal hidden.bs.modal');
    
    // Show modal
    $('#imageCropModal').modal('show');
    $('#cropAndUpload').prop('disabled', true).text('กำลังโหลด...');
    
    // Initialize cropper after a short delay to ensure modal is fully rendered
    $('#imageCropModal').one('shown.bs.modal', function() {
        // Destroy existing cropper if any
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        // Wait for modal animation to complete
        setTimeout(function() {
            try {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 3 / 4,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    preview: '#cropPreview',
                    movable: true,
                    scalable: true,
                    zoomable: true,
                    rotatable: true,
                    checkOrientation: false,
                    wheelZoomRatio: 0.05,
                    minContainerWidth: 300,
                    minContainerHeight: 300,
                    modal: true,
                    background: true,
                    responsive: true,
                    checkCrossOrigin: false,
                    built: function() {
                        const canvas = this.cropper.getCanvasData();
                        this.cropper.setCanvasData({
                            ...canvas,
                            naturalWidth: canvas.naturalWidth,
                            naturalHeight: canvas.naturalHeight
                        });
                    },
                    ready: function() {
                        $('#cropAndUpload').prop('disabled', false).text('✅ ตัดแต่งและอัปโหลด');
                        updateCropPreview();
                    },
                    crop: function(event) {
                        clearTimeout(this.cropTimeout);
                        this.cropTimeout = setTimeout(() => {
                            updateCropPreview();
                        }, 100); 
                    }
                });
            } catch (error) {
                console.error('Error initializing cropper:', error);
                $('#cropAndUpload').prop('disabled', false).text('✅ ตัดแต่งและอัปโหลด');
            }
        }, 300);
    });
    
    // Clean up when modal is hidden
    $('#imageCropModal').one('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
}

// Separate function for updating crop preview
function updateCropPreview() {
    if (!cropper) return;
    
    try {
        const canvas = cropper.getCroppedCanvas({
            width: 150,
            height: 200,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        if (canvas) {
            const preview = document.getElementById('cropPreview');
            preview.innerHTML = '';
            preview.appendChild(canvas);
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.borderRadius = '8px';
            canvas.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        }
    } catch (error) {
        console.error('Error updating preview:', error);
    }
}

// Crop and upload handler
$('#cropAndUpload').on('click', function() {
    if (!cropper) return;
    
    showLoading();
    
    cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    }).toBlob(function(blob) {
        const formData = new FormData();
        formData.append('profile_pic', blob, 'profile.jpg');
        formData.append('Stu_id', currentStudentId);
        
        $.ajax({
            url: 'api/update_profile_pic_std.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                hideLoading();
                $('#imageCropModal').modal('hide');
                  if (res.success) {
                    showNotification('success', 'สำเร็จ!', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว');
                    
                    // Update the specific student card image without full reload
                    const studentCard = $(`.student-card[data-id="${currentStudentId}"]`);
                    const newImageUrl = `../photo/${res.filename}?v=${Date.now()}`;
                    studentCard.find('.student-photo').attr('src', newImageUrl);
                    
                    // Add success glow effect
                    studentCard.addClass('success-glow');
                    setTimeout(() => {
                        studentCard.removeClass('success-glow');
                    }, 2000);
                } else {
                    showNotification('error', 'ผิดพลาด', res.message || 'เกิดข้อผิดพลาด');
                }
            },
            error: function() {
                hideLoading();
                $('#imageCropModal').modal('hide');
                Swal.fire('❌ ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลด', 'error');
            }
        });
    }, 'image/jpeg', 0.8);
});

// Clean up cropper when modal is hidden
$('#imageCropModal').on('hidden.bs.modal', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    // Clear the image src to free memory
    $('#cropImage').attr('src', '');
    $('#cropPreview').empty();
});

// Enhanced save changes handler
$('#saveChanges').on('click', function() {
    const form = $('#editStudentForm');
    
    if (form.length === 0) {
        Swal.fire('❌ ข้อผิดพลาด', 'ไม่พบฟอร์มแก้ไข', 'error');
        return;
    }
    
    showLoading();
    
    const formData = form.serialize();
    $.ajax({
        url: 'api/update_student.php',
        method: 'POST',
        data: formData,
        success: function(response) {            hideLoading();
            $('#editStudentModal').modal('hide');
            
            showNotification('success', 'บันทึกสำเร็จ!', 'อัปเดตข้อมูลนักเรียนเรียบร้อยแล้ว');
            
            loadStudentData();
        },
        error: function(xhr, status, error) {
            hideLoading();
            Swal.fire('❌ ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        }
    });
});

// Utility functions
function showLoading() {
    $('#loadingOverlay').fadeIn(300);
}

function hideLoading() {
    $('#loadingOverlay').fadeOut(300);
}

// Enhanced image error handler with SVG fallback
function handleImageError(img, studentName) {
    // First try the default SVG avatar
    if (img.src.indexOf('default-avatar.svg') === -1) {
        img.src = '../dist/img/default-avatar.svg';
        img.onerror = function() {
            // If SVG fails, generate dynamic avatar
            generateDynamicAvatar(img, studentName);
        };
        return;
    }
    
    // Generate dynamic avatar if SVG fails
    generateDynamicAvatar(img, studentName);
}

function generateDynamicAvatar(img, studentName) {
    const initials = studentName.split(' ').map(name => name.charAt(0)).join('').substring(0, 2);
    const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    
    const canvas = document.createElement('canvas');
    canvas.width = 400;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');
    
    // Create gradient background
    const gradient = ctx.createLinearGradient(0, 0, 400, 400);
    gradient.addColorStop(0, randomColor);
    gradient.addColorStop(1, adjustBrightness(randomColor, -20));
    
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, 400, 400);
    
    // Add initials
    ctx.fillStyle = 'white';
    ctx.font = 'bold 120px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(initials, 200, 200);
    
    // Add subtle pattern
    ctx.globalAlpha = 0.1;
    for (let i = 0; i < 10; i++) {
        ctx.beginPath();
        ctx.arc(Math.random() * 400, Math.random() * 400, Math.random() * 50, 0, 2 * Math.PI);
        ctx.fill();
    }
    
    img.src = canvas.toDataURL();
    img.onerror = null; // Prevent infinite loop
}

// Color adjustment utility
function adjustBrightness(hex, percent) {
    const num = parseInt(hex.replace("#", ""), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = (num >> 8 & 0x00FF) + amt;
    const B = (num & 0x0000FF) + amt;
    return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
}

// Enhanced notification system
function showNotification(type, title, message, timer = 3000) {
    const icons = {
        success: '🎉',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    Swal.fire({
        icon: type,
        title: `${icons[type]} ${title}`,
        text: message,
        showConfirmButton: false,
        timer: timer,
        toast: true,
        position: 'top-end',
        background: type === 'success' ? '#f0fff4' : type === 'error' ? '#fef5e7' : '#f7fafc',
        color: type === 'success' ? '#22543d' : type === 'error' ? '#742a2a' : '#2d3748'
    });
}

// Debounced search function for better performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Enhanced search with debouncing
const debouncedSearch = debounce(function(searchValue) {
    const val = searchValue.trim().toLowerCase();
    
    $('.student-card').each(function() {
        const name = ($(this).attr('data-name') || '').toLowerCase();
        const id = ($(this).attr('data-id') || '').toLowerCase();
        const no = ($(this).attr('data-no') || '').toLowerCase();
        const nick = ($(this).attr('data-nick') || '').toLowerCase();
        
        const isMatch = name.includes(val) || id.includes(val) || no.includes(val) || nick.includes(val);
        
        if (isMatch) {
            $(this).removeClass('hidden').addClass('animate-fadeInUp');
        } else {
            $(this).addClass('hidden').removeClass('animate-fadeInUp');
        }
    });
    
    // Show search results summary (ตกแต่งด้วย Tailwind)
    const visibleCards = $('.student-card:not(.hidden)').length;
    const totalCards = $('.student-card').length;
    
    if (val) {
        $('#searchSummary').remove();
        $('#showDataStudent').prepend(`
            <div id="searchSummary" class="col-span-full flex justify-center py-4 mb-4">
                <div class="bg-white border border-blue-200 rounded-xl px-8 py-4 shadow-lg flex items-center space-x-4 transition-all duration-300">
                    <span class="text-blue-500 text-3xl drop-shadow">🔎</span>
                    <p class="text-blue-800 font-semibold text-lg m-0 tracking-wide">
                        ${visibleCards > 0 
                            ? `พบ <span class="font-bold text-blue-600">${visibleCards}</span> รายการ จากทั้งหมด <span class="font-bold text-blue-600">${totalCards}</span> คน`
                            : '<span class="text-red-500 font-bold">ไม่พบผลการค้นหา</span>'
                        }
                    </p>
                </div>
            </div>
        `);
    } else {
        $('#searchSummary').remove();
    }
}, 300);

// Load initial data
loadStudentData();

// Make functions globally available
window.handleImageError = handleImageError;
window.showNotification = showNotification;

// Print functionality
$('#printStudentList').on('click', function() {
    showLoading();
    
    const classValue = <?=$class?>;
    const roomValue = <?=$room?>;
    
    $.ajax({
        url: 'api/print_student_list.php',
        method: 'GET',
        data: { 
            class: classValue, 
            room: roomValue,
            format: 'table'
        },
        success: function(response) {
            hideLoading();
            
            // Create print window
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>รายชื่อนักเรียน ม.${classValue}/${roomValue}</title>
                    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        @page {
                            size: A4;
                            margin: 0.5in;
                        }
                        
                        body {
                            font-family: 'Sarabun', Arial, sans-serif;
                            font-size: 12px;
                            line-height: 1.4;
                            color: #000;
                            margin: 0;
                            padding: 20px;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 25px;
                            border-bottom: 2px solid #000;
                            padding-bottom: 15px;
                        }
                        
                        .print-header h1 {
                            margin: 0;
                            font-size: 18px;
                            font-weight: bold;
                        }
                        
                        .print-header h2 {
                            margin: 5px 0;
                            font-size: 16px;
                            font-weight: 600;
                        }
                        
                        .print-header p {
                            margin: 5px 0;
                            font-size: 14px;
                        }
                        
                        .print-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 15px;
                            font-size: 11px;
                        }
                        
                        .print-table th,
                        .print-table td {
                            border: 1px solid #000;
                            padding: 6px 4px;
                            text-align: left;
                            vertical-align: middle;
                        }
                        
                        .print-table th {
                            background-color: #f0f0f0;
                            font-weight: bold;
                            text-align: center;
                            font-size: 11px;
                        }
                        
                        .print-table tbody tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }
                        
                        .print-table .col-no {
                            width: 8%;
                            text-align: center;
                        }
                        
                        .print-table .col-id {
                            width: 12%;
                            text-align: center;
                        }
                        
                        .print-table .col-name {
                            width: 25%;
                        }
                        
                        .print-table .col-nick {
                            width: 12%;
                            text-align: center;
                        }
                        
                        .print-table .col-phone {
                            width: 15%;
                            text-align: center;
                        }
                        
                        .print-table .col-parent {
                            width: 15%;
                            text-align: center;
                        }
                        
                        .print-table .col-note {
                            width: 13%;
                        }
                        
                        .print-photo {
                            width: 35px;
                            height: 45px;
                            object-fit: cover;
                            border: 1px solid #ccc;
                            border-radius: 2px;
                        }
                        
                        .print-footer {
                            margin-top: 30px;
                            font-size: 11px;
                        }
                        
                        .signature-section {
                            margin-top: 40px;
                            display: flex;
                            justify-content: space-between;
                            page-break-inside: avoid;
                        }
                        
                        .signature-box {
                            text-align: center;
                            width: 200px;
                        }
                        
                        .signature-line {
                            border-bottom: 1px solid #000;
                            margin-bottom: 5px;
                            height: 60px;
                        }
                        
                        .page-break {
                            page-break-before: always;
                        }
                        
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    ${response}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            
            // Wait for content to load then print
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            };
        },
        error: function() {
            hideLoading();
            Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถสร้างรายการพิมพ์ได้', 'error');
        }
    });
});

});
</script>

<!-- Print Styles -->
<style media="print">
    @page {
        size: A4;
        margin: 0.5in;
    }
    
    .print-only {
        display: block !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    body {
        font-family: 'Sarabun', Arial, sans-serif;
        font-size: 12px;
        line-height: 1.3;
        color: #000;
    }
    
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    
    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    
    .print-table th,
    .print-table td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
        vertical-align: top;
    }
    
    .print-table th {
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }
    
    .print-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .print-photo {
        width: 40px;
        height: 50px;
        object-fit: cover;
        border: 1px solid #ccc;
    }
    
    .print-footer {
        margin-top: 30px;
        text-align: right;
        font-size: 11px;
    }
    
    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }
    
    .signature-box {
        text-align: center;
        width: 200px;
    }
    
    .signature-line {
        border-bottom: 1px solid #000;
        margin-bottom: 5px;
        height: 50px;
    }
</style>
