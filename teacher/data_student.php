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
            <div class="callout callout-success text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 20px; padding: 40px;">
                <div class="logo-container mb-4">
                    <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-90 mb-3" style="width: 80px; height: 80px; border: 4px solid rgba(255,255,255,0.3);">
                </div>
                <h2 class="text-center text-2xl font-bold mb-2">รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></h2>
                <h4 class="text-center text-lg opacity-90">ปีการศึกษา <?=$pee?></h4>

                <!-- Enhanced Search Box -->
                <div class="search-container">
                    <div class="search-icon">🔍</div>
                    <input type="text" id="studentSearch" class="search-input" placeholder="ค้นหาชื่อนักเรียน, รหัส, เลขที่ หรือชื่อเล่น...">
                </div>

                <!-- Enhanced Toggle Switch -->
                <div class="toggle-container">
                    <div class="flex items-center justify-center space-x-4">
                        <span class="text-2xl">🙅‍♂️</span>
                        <div class="flex items-center space-x-3">
                            <div class="toggle-switch" id="allowEditSwitch">
                                <span class="toggle-emoji">🔒</span>
                            </div>
                            <span class="text-lg font-medium" id="editStatusText">ปิดให้นักเรียนแก้ไขข้อมูล</span>
                        </div>
                        <span class="text-2xl">🙆‍♀️</span>
                    </div>
                </div>

                <!-- Enhanced Grid Container -->
                <div id="showDataStudent" class="student-grid">
                    <!-- Student cards will be injected here -->
                </div>
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
        <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
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
        <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="close text-white hover:text-gray-200" data-dismiss="modal" aria-label="Close">
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
    <div class="text-center text-white">
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
            `<br><small class="text-white">โดย ${by} (${timestamp})</small>`
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
                    <p class="text-xl font-semibold text-white">ไม่พบข้อมูลนักเรียน</p>
                </div>
            `);
        } else {
            response.data.forEach((item, index) => {                const studentCard = `
                    <div class="student-card hover-lift"
                        data-name="${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}"
                        data-id="${item.Stu_id}"
                        data-no="${item.Stu_no}"
                        data-nick="${item.Stu_nick}"
                        style="animation-delay: ${index * 0.1}s; opacity: 0;">
                        
                        <div class="student-photo-container" style="position: relative;">
                            <img class="student-photo w-full h-128 object-cover" 
                                 src="../photo/${item.Stu_picture}" 
                                 alt="Student Picture"
                                 onerror="handleImageError(this, '${item.Stu_pre}${item.Stu_name}')"
                                 onload="this.classList.add('animate-fadeInUp')">
                            <div class="absolute top-2 right-2 bg-white bg-opacity-80 rounded-full px-2 py-1 text-xs font-bold text-gray-700">
                                #${item.Stu_no}
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h5 class="text-lg font-bold text-lime-300 mb-2">
                                    ${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}
                                </h5>
                                <div class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    รหัส: ${item.Stu_id}
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                ${item.Stu_nick ? `<div class="flex justify-between">
                                    <span class="text-white">ชื่อเล่น:</span>
                                    <span class="font-semibold text-purple-600">${item.Stu_nick}</span>
                                </div>` : ''}
                                ${item.Stu_phone ? `<div class="flex justify-between">
                                    <span class="text-white">เบอร์:</span>
                                    <a href="tel:${item.Stu_phone}" class="text-white hover:underline flex items-center">
                                        📞 ${item.Stu_phone}
                                    </a>
                                </div>` : ''}
                                ${item.Par_phone ? `<div class="flex justify-between">
                                    <span class="text-white">ผู้ปกครอง:</span>
                                    <a href="tel:${item.Par_phone}" class="text-white hover:underline flex items-center">
                                        👨‍👩‍👧‍👦 ${item.Par_phone}
                                    </a>
                                </div>` : ''}
                            </div>
                            
                            <div class="flex justify-center space-x-2 mt-6">
                                <button class="btn-modern btn-view btn-sm hover-lift" data-id="${item.Stu_id}" title="ดูข้อมูลทั้งหมด">
                                    <span class="text-lg">👀</span>
                                </button>
                                <button class="btn-modern btn-edit btn-sm hover-lift" data-id="${item.Stu_id}" title="แก้ไขข้อมูล">
                                    <span class="text-lg">✏️</span>
                                </button>
                                <button class="btn-modern btn-photo btn-sm hover-lift" data-id="${item.Stu_id}" title="เปลี่ยนรูปภาพ">
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
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(50px)'
                }).delay(index * 100).animate({
                    'opacity': '1'
                }, 500).css('transform', 'translateY(0)');
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
    
    // Get student name for better UX
    const studentCard = $(this).closest('.student-card');
    const studentName = studentCard.attr('data-name');
    
    Swal.fire({
        title: `📷 เปลี่ยนรูปภาพ`,
        html: `
            <div class="text-center mb-4">
                <p class="text-white">นักเรียน: <strong>${studentName}</strong></p>
            </div>
            <div class="drop-zone" style="border: 2px dashed #cbd5e0; border-radius: 10px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s ease;" 
                 onclick="document.getElementById('photoInput').click()">
                <div style="font-size: 3rem; margin-bottom: 10px;">📁</div>
                <p>คลิกเพื่อเลือกรูปภาพ</p>
                <p style="font-size: 0.8rem; color: #666;">หรือลากและวางรูปภาพที่นี่</p>
            </div>
            <input type="file" id="photoInput" accept="image/*" style="display: none;">
            <div class="mt-3">
                <small class="text-white">
                    รองรับไฟล์: JPG, PNG, GIF, WebP<br>
                    ขนาดไม่เกิน 5MB • จะมีเครื่องมือตัดแต่งรูปภาพ
                </small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '📤 ต่อไป',
        cancelButtonText: '❌ ยกเลิก',
        confirmButtonColor: '#4facfe',
        cancelButtonColor: '#6c757d',
        width: '500px',
        preConfirm: () => {
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
            
            return file;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            initImageCropper(result.value);
        }
    });

    // Enhanced drag and drop functionality
    $(document).off('click', '.drop-zone').on('click', '.drop-zone', function() {
        document.getElementById('photoInput').click();
    });

    $(document).off('dragover dragenter', '.drop-zone').on('dragover dragenter', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': '#4facfe',
            'background-color': 'rgba(79, 172, 254, 0.1)'
        });
    });

    $(document).off('dragleave', '.drop-zone').on('dragleave', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': '#cbd5e0',
            'background-color': 'transparent'
        });
    });

    $(document).off('drop', '.drop-zone').on('drop', '.drop-zone', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': '#cbd5e0',
            'background-color': 'transparent'
        });
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('photoInput').files = files;
            Swal.clickConfirm();
        }
    });

    // File input change handler
    $(document).off('change', '#photoInput').on('change', '#photoInput', function() {
        if (this.files.length > 0) {
            $('.drop-zone').html(`
                <div style="font-size: 2rem; margin-bottom: 10px;">✅</div>
                <p style="color: green;">เลือกไฟล์แล้ว: ${this.files[0].name}</p>
                <p style="font-size: 0.8rem; color: #666;">คลิก "ต่อไป" เพื่อตัดแต่งรูปภาพ</p>
            `);
        }
    });
});

// Enhanced image cropper with better preview
function initImageCropper(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const cropImage = document.getElementById('cropImage');
        cropImage.src = e.target.result;
        
        // Show modal with loading state
        $('#imageCropModal').modal('show');
        $('#cropAndUpload').prop('disabled', true).text('กำลังโหลด...');
        
        // Initialize cropper after modal is shown
        $('#imageCropModal').on('shown.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
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
                ready: function() {
                    $('#cropAndUpload').prop('disabled', false).text('✅ ตัดแต่งและอัปโหลด');
                },
                crop: function(event) {
                    // Update preview info
                    const canvas = cropper.getCroppedCanvas({
                        width: 150,
                        height: 150
                    });
                    
                    if (canvas) {
                        const preview = document.getElementById('cropPreview');
                        preview.innerHTML = '';
                        preview.appendChild(canvas);
                        canvas.style.width = '100%';
                        canvas.style.height = '100%';
                        canvas.style.borderRadius = '50%';
                    }
                }
            });
        });
    };
    reader.readAsDataURL(file);
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

});
</script>
</body>
</html>
