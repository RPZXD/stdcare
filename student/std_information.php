<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// เช็คสิทธิ์แก้ไขข้อมูล
$class = $student['Stu_major'];
$room = $student['Stu_room'];
$canEdit = false;
$jsonFile = __DIR__ . '/../teacher/api/student_edit_permission.json';
if (file_exists($jsonFile)) {
    $json = json_decode(file_get_contents($jsonFile), true);
    $key = $class . '-' . $room;
    if (isset($json['permissions'][$key]) && !empty($json['permissions'][$key]['allowEdit'])) {
        $canEdit = true;
    }
}

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function thai_date($strDate) {
    $strYear = date("Y", strtotime($strDate)) ;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $thaiMonths = [
        "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
    ];
    $strMonthThai = $thaiMonths[$strMonth];
    return "$strDay $strMonthThai $strYear";
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<style>
/* Cropper styles */
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

.cropper-crop-box {
    cursor: move;
    transition: all 0.1s ease-out;
}

.cropper-drag-box {
    cursor: move;
    opacity: 0.1;
}

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

.cropper-line {
    background-color: #007bff;
    opacity: 0.6;
}

.cropper-line:hover {
    opacity: 1;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ข้อมูลนักเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content ">
            <div class="container-fluid flex justify-center">
                <div class="max-w-xl w-full bg-white rounded-xl shadow-lg p-8 mt-8">
                    <div class="flex flex-col items-center">
                        <img src="<?php echo htmlspecialchars($setting->getImgProfileStudent().$student['Stu_picture']); ?>"
                            alt="User Avatar"
                            class="rounded-full w-60 h-60 object-cover shadow-lg ring-4 ring-blue-300 transition-all duration-500 hover:scale-110 hover:shadow-2xl hover:rotate-3 mb-4">
                        <h2 class="text-2xl font-bold text-blue-700 mb-2"><?php echo htmlspecialchars($student['Stu_pre'].$student['Stu_name']." ".$student['Stu_sur']); ?></h2>
                        <p class="text-gray-500 mb-4">รหัสนักเรียน: <span class="font-semibold"><?php echo htmlspecialchars($student['Stu_id']); ?></span></p>
                        <?php if ($canEdit): ?>
                            <div class="flex gap-2 mb-4">
                                <button type="button" id="btnEditInfo" class="px-4 py-2 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition">
                                    ✏️ แก้ไขข้อมูล
                                </button>
                                <button type="button" id="btnEditProfilePic"
                                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 transition"
                                    title="แก้ไขรูปโปรไฟล์">
                                    <span class="">🖼️ แก้ไขรูปโปรไฟล์</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-6 ">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🎂</span>
                            <span class="text-gray-700">วันเกิด:</span>
                            <span class="font-medium"><?php echo thai_date($student['Stu_birth']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧‍👦</span>
                            <span class="text-gray-700">ชั้น:</span>
                            <span class="font-medium"><?php echo htmlspecialchars('ม.'.$student['Stu_major'].'/'.$student['Stu_room']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🏠</span>
                            <span class="text-gray-700">ที่อยู่:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_addr']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📞</span>
                            <span class="text-gray-700">เบอร์โทร:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Stu_phone']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👨‍👩‍👧</span>
                            <span class="text-gray-700">ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_name']); ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📱</span>
                            <span class="text-gray-700">เบอร์ผู้ปกครอง:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($student['Par_phone']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>

<!-- Modal สำหรับแก้ไขข้อมูล -->
<div class="modal fade" id="editInfoModal" tabindex="-1" role="dialog" aria-labelledby="editInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-white rounded-lg shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="editInfoModalLabel">แก้ไขข้อมูลนักเรียน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="editInfoModalBody">
        <!-- ฟอร์มจะแสดงที่นี่ -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" id="saveEditInfo">บันทึก</button>
      </div>
    </div>
  </div>
</div>

<!-- Image Cropper Modal -->
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
                            <div class="crop-preview" id="cropPreview" style="width: 150px; height: 200px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;"></div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-info btn-sm" onclick="cropper.rotate(90)">↻ หมุนขวา</button>
                                <button type="button" class="btn btn-info btn-sm" onclick="cropper.rotate(-90)">↺ หมุนซ้าย</button>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-warning btn-sm" onclick="cropper.reset()">🔄 รีเซ็ต</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" id="cropAndUpload">✅ ตัดแต่งและอัปโหลด</button>
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

<?php require_once('script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let cropper;

$(function() {
    $('#btnEditInfo').on('click', function() {
        // ปรับเป็น AJAX POST เพื่อความปลอดภัย (หรือ GET ก็ได้ถ้าไม่มีข้อมูลสำคัญ)
        $.ajax({
            url: 'std_information_edit_form.php',
            type: 'GET',
            dataType: 'html',
            success: function(html) {
                $('#editInfoModalBody').html(html);
                $('#editInfoModal').modal('show');
            },
            error: function() {
                $('#editInfoModalBody').html('<div class="text-red-500">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>');
                $('#editInfoModal').modal('show');
            }
        });
    });

    $('#saveEditInfo').on('click', function() {
        var form = $('#editStudentForm');
        if (form.length === 0) return;
        $.ajax({
            url: 'api/update_student_info.php',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#editInfoModal').modal('hide');
                    Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
            }
        });
    });

    $('#btnEditProfilePic').on('click', function() {
        const currentPhoto = $('img[alt="User Avatar"]').attr('src');
        
        Swal.fire({
            title: `📷 จัดการรูปภาพโปรไฟล์`,
            html: `
                <div class="text-center mb-4">
                    <p class="text-gray-700">เลือกวิธีการอัปโหลดรูปภาพ</p>
                </div>
                
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
                
                <div id="uploadSection" style="display: none;">
                    <div class="drop-zone" style="border: 2px dashed #cbd5e0; border-radius: 10px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s ease;" 
                         onclick="document.getElementById('photoInput').click()">
                        <div style="font-size: 3rem; margin-bottom: 10px;">📁</div>
                        <p>คลิกเพื่อเลือกรูปภาพ</p>
                        <p style="font-size: 0.8rem; color: #666;">หรือลากและวางรูปภาพที่นี่</p>
                    </div>
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                    <div class="mt-3">
                        <small class="text-gray-600">
                            รองรับไฟล์: JPG, PNG, GIF, WebP<br>
                            ขนาดไม่เกิน 5MB • จะมีเครื่องมือตัดแต่งรูปภาพ
                        </small>
                    </div>
                </div>
                
                <div id="currentPhotoPreview" style="display: none; text-align: center; margin-top: 15px;">
                    <img src="${currentPhoto}" style="max-width: 200px; max-height: 200px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" alt="ภาพปัจจุบัน">
                    <p style="margin-top: 8px; font-size: 0.9rem; color: #666;">ภาพปัจจุบันของคุณ</p>
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
                    
                    Swal.getConfirmButton().disabled = true;
                    Swal.getConfirmButton().style.opacity = '0.5';
                });
                
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
                            
                            Swal.getConfirmButton().disabled = false;
                            Swal.getConfirmButton().style.opacity = '1';
                        }
                    };
                    
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
                
                Swal.getConfirmButton().disabled = true;
                Swal.getConfirmButton().style.opacity = '0.5';
                
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
                    
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.showValidationMessage('ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน 5MB)');
                        return false;
                    }
                    
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
});

function initImageCropper(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        setupCropper(e.target.result);
    };
    reader.readAsDataURL(file);
}

function initImageCropperFromURL(imageUrl) {
    let fullImageUrl = imageUrl;
    if (imageUrl.startsWith('../')) {
        const baseUrl = window.location.origin + window.location.pathname.replace('/student/std_information.php', '');
        fullImageUrl = baseUrl + imageUrl.substring(2);
    }
    
    const img = new Image();
    img.crossOrigin = 'anonymous';
    
    img.onload = function() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = this.naturalWidth;
        canvas.height = this.naturalHeight;
        ctx.drawImage(this, 0, 0);
        
        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        setupCropper(dataUrl);
    };
    
    img.onerror = function() {
        setupCropper(fullImageUrl);
    };
    
    img.src = fullImageUrl;
}

function setupCropper(imageSrc) {
    const cropImage = document.getElementById('cropImage');
    cropImage.src = imageSrc;
    
    $('#imageCropModal').off('shown.bs.modal hidden.bs.modal');
    $('#imageCropModal').modal('show');
    $('#cropAndUpload').prop('disabled', true).text('กำลังโหลด...');
    
    $('#imageCropModal').one('shown.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
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
    
    $('#imageCropModal').one('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
}

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

function showLoading() {
    $('#loadingOverlay').fadeIn(300);
}

function hideLoading() {
    $('#loadingOverlay').fadeOut(300);
}

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
        
        $.ajax({
            url: 'api/update_profile_pic.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                hideLoading();
                $('#imageCropModal').modal('hide');
                
                if (res.success) {
                    Swal.fire('สำเร็จ!', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
                }
            },
            error: function() {
                hideLoading();
                $('#imageCropModal').modal('hide');
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลด', 'error');
            }
        });
    }, 'image/jpeg', 0.8);
});

$('#imageCropModal').on('hidden.bs.modal', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    $('#cropImage').attr('src', '');
    $('#cropPreview').empty();
});
</script>
</body>
</html>
