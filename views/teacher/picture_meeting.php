<?php
/**
 * Picture Meeting View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
$pageTitle = $title ?? 'ภาพกิจกรรมการประชุมผู้ปกครอง';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.85);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .image-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .image-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .slide-in {
        animation: slideIn 0.5s ease-out forwards;
    }
    /* Print Styles */
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        body { background: white !important; font-family: 'Mali', sans-serif !important; }
        .no-print, #sidebar, #navbar, footer { display: none !important; }
        #printHeader, #printContent, #printSignature { display: block !important; }
        .glass-card { display: none !important; }
    }
    @media screen {
        #printHeader, #printContent, #printSignature { display: none !important; }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">📷</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    ภาพกิจกรรมการประชุมผู้ปกครอง
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-violet-500 mr-1"></i>
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-violet-500 mr-1"></i>
                    ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-3 gap-2 md:gap-4 mb-4 md:mb-6 no-print">
    <div class="glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-violet-400 to-purple-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">🖼️</span>
        </div>
        <p class="text-xl md:text-2xl font-black text-violet-600" id="statTotal">-</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">รูปทั้งหมด</p>
    </div>
    <div class="glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">📅</span>
        </div>
        <p class="text-sm md:text-base font-black text-cyan-600" id="statDate">8-9 พ.ค.</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">วันประชุม</p>
    </div>
    <div class="glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-rose-400 to-pink-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">🏫</span>
        </div>
        <p class="text-sm md:text-base font-black text-rose-600">พิชัย</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">โรงเรียน</p>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <button type="button" onclick="openAddModal()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-plus-circle"></i>
        <span>📷 เพิ่มรูปภาพ</span>
    </button>
    <button onclick="window.print()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-print"></i>
        <span>🖨️ พิมพ์</span>
    </button>
</div>

<!-- Picture Gallery -->
<div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl no-print">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl flex items-center justify-center shadow">
            <i class="fas fa-images text-white"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white">🖼️ แกลเลอรี่รูปภาพ</h3>
    </div>
    
    <div id="pictureGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div id="loadingState" class="col-span-full text-center py-12">
            <div class="animate-spin w-10 h-10 border-4 border-violet-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-slate-500 font-semibold">กำลังโหลดรูปภาพ...</p>
        </div>
    </div>
</div>

<!-- Print Layout -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4 mb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600 font-bold">ภาพกิจกรรมการประชุมผู้ปกครอง</p>
    </div>
    <div class="flex justify-between items-end mb-4 text-sm font-bold">
        <div>ชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?></div>
        <div>ภาคเรียนที่ <?= $term ?> ปีการศึกษา <?= $pee ?></div>
    </div>
</div>

<div id="printContent" class="hidden print:block mb-8">
    <div id="printPictureGrid" class="grid grid-cols-2 gap-4"></div>
</div>

<div id="printSignature" class="hidden print:block mt-8">
    <div class="grid grid-cols-2 gap-8 px-8">
        <?php foreach ($roomTeachers as $t): ?>
        <div class="text-center mb-2">
            <p class="mb-12">ลงชื่อ..........................................</p>
            <p class="font-bold">(<?= $t['Teach_name'] ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-8 italic">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-violet-500 to-purple-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-camera"></i> อัปโหลดรูปภาพการประชุม
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
                    <div class="bg-violet-50 border-l-4 border-violet-400 p-3 rounded-r-xl">
                        <p class="text-sm text-violet-800 font-semibold">📷 คำแนะนำ: สามารถอัปโหลดได้สูงสุด 4 รูปต่อครั้ง</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Upload 1 -->
                        <div class="relative group">
                            <div id="dropzone1" class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-violet-400 transition-colors cursor-pointer">
                                <input type="file" id="uploadImage1" name="uploadImage[]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                <img id="preview1" src="#" alt="" class="hidden w-full h-32 object-cover rounded-lg mb-2">
                                <div id="placeholder1" class="space-y-2">
                                    <div class="w-12 h-12 mx-auto bg-violet-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-violet-500 text-xl"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">รูปที่ 1 *</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload 2 -->
                        <div class="relative group">
                            <div id="dropzone2" class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-violet-400 transition-colors cursor-pointer">
                                <input type="file" id="uploadImage2" name="uploadImage[]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <img id="preview2" src="#" alt="" class="hidden w-full h-32 object-cover rounded-lg mb-2">
                                <div id="placeholder2" class="space-y-2">
                                    <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-slate-400 text-xl"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">รูปที่ 2</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload 3 -->
                        <div class="relative group">
                            <div id="dropzone3" class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-violet-400 transition-colors cursor-pointer">
                                <input type="file" id="uploadImage3" name="uploadImage[]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <img id="preview3" src="#" alt="" class="hidden w-full h-32 object-cover rounded-lg mb-2">
                                <div id="placeholder3" class="space-y-2">
                                    <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-slate-400 text-xl"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">รูปที่ 3</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload 4 -->
                        <div class="relative group">
                            <div id="dropzone4" class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-violet-400 transition-colors cursor-pointer">
                                <input type="file" id="uploadImage4" name="uploadImage[]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <img id="preview4" src="#" alt="" class="hidden w-full h-32 object-cover rounded-lg mb-2">
                                <div id="placeholder4" class="space-y-2">
                                    <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-slate-400 text-xl"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">รูปที่ 4</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitUploadForm()" class="px-5 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-xl">
                    <i class="fas fa-upload mr-2"></i>อัปโหลด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 bg-black/90 rounded-3xl overflow-hidden">
            <div class="modal-header border-0 py-3 px-4">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <img id="lightboxImage" src="" alt="" class="max-w-full max-h-[70vh] rounded-xl mx-auto shadow-2xl">
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
(function() {
    const classId = <?= $class ?>;
    const roomId = <?= $room ?>;
    const termValue = <?= $term ?>;
    const peeValue = <?= $pee ?>;
    let allPictures = [];

    // Preview image function
    function previewImage(input, previewId, placeholderId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }

    // Attach preview handlers
    $('#uploadImage1').on('change', function() { previewImage(this, 'preview1', 'placeholder1'); });
    $('#uploadImage2').on('change', function() { previewImage(this, 'preview2', 'placeholder2'); });
    $('#uploadImage3').on('change', function() { previewImage(this, 'preview3', 'placeholder3'); });
    $('#uploadImage4').on('change', function() { previewImage(this, 'preview4', 'placeholder4'); });

    // Create picture card
    function createPictureCard(picture, index) {
        return `
            <div class="image-card bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-lg slide-in" style="animation-delay: ${index * 0.1}s">
                <div class="relative group cursor-pointer" onclick="openLightbox('${picture.url}')">
                    <img src="${picture.url}" alt="${picture.alt || 'ภาพประชุมผู้ปกครอง'}" 
                         class="w-full h-48 md:h-56 object-cover transition-transform group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-12 h-12 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <i class="fas fa-search-plus text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center text-sm font-bold text-violet-600">${index + 1}</span>
                        <span class="text-xs text-slate-500">${picture.alt || 'รูปภาพที่ ' + (index + 1)}</span>
                    </div>
                    <button onclick="deletePicture(${picture.id || index})" 
                            class="btn-action px-3 py-1.5 bg-gradient-to-r from-rose-400 to-red-500 text-white text-xs font-bold rounded-lg">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // Load pictures
    function loadPictures() {
        $.ajax({
            url: '../teacher/api/fetch_picture_meeting.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classId, room: roomId, term: termValue, pee: peeValue },
            success: function(response) {
                $('#loadingState').remove();
                
                if (response.success && response.data.length > 0) {
                    allPictures = response.data;
                    $('#statTotal').text(allPictures.length);
                    
                    let html = '';
                    let printHtml = '';
                    
                    allPictures.forEach((picture, index) => {
                        html += createPictureCard(picture, index);
                        printHtml += `<img src="${picture.url}" alt="" class="w-full h-auto rounded-lg border">`;
                    });
                    
                    $('#pictureGrid').html(html);
                    $('#printPictureGrid').html(printHtml);
                } else {
                    $('#statTotal').text('0');
                    $('#pictureGrid').html(`
                        <div class="col-span-full text-center py-12">
                            <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <span class="text-4xl">📷</span>
                            </div>
                            <h4 class="font-bold text-slate-600 mb-2">ยังไม่มีรูปภาพ</h4>
                            <p class="text-sm text-slate-400 mb-4">เริ่มต้นเพิ่มรูปภาพการประชุมผู้ปกครอง</p>
                            <button onclick="openAddModal()" class="btn-action px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold text-sm rounded-xl">
                                <i class="fas fa-plus mr-2"></i>เพิ่มรูปภาพ
                            </button>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#loadingState').html('<p class="text-red-500">เกิดข้อผิดพลาดในการโหลดรูปภาพ</p>');
            }
        });
    }

    window.openAddModal = function() {
        // Reset form and previews
        $('#uploadForm')[0].reset();
        ['preview1', 'preview2', 'preview3', 'preview4'].forEach((id, i) => {
            $(`#${id}`).addClass('hidden').attr('src', '#');
            $(`#placeholder${i + 1}`).removeClass('hidden');
        });
        new bootstrap.Modal(document.getElementById('addModal')).show();
    };

    window.submitUploadForm = function() {
        const formData = new FormData($('#uploadForm')[0]);
        formData.append('class', classId);
        formData.append('room', roomId);
        formData.append('term', termValue);
        formData.append('pee', peeValue);

        Swal.fire({ title: 'กำลังอัปโหลด...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: '../teacher/api/insert_picture_meeting.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'อัปโหลดสำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: 'เกิดข้อผิดพลาดในการอัปโหลด' });
            }
        });
    };

    window.openLightbox = function(url) {
        $('#lightboxImage').attr('src', url);
        new bootstrap.Modal(document.getElementById('lightboxModal')).show();
    };

    window.deletePicture = function(pictureId) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบรูปภาพนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../teacher/api/delete_picture_meeting.php',
                    method: 'POST',
                    data: { id: pictureId, class: classId, room: roomId, term: termValue, pee: peeValue },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', timer: 1500 }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: res.message });
                        }
                    }
                });
            }
        });
    };

    // Initial load
    loadPictures();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
