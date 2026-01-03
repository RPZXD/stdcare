<?php
/**
 * View: Student Information
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();

$imgPath = isset($student['Stu_picture']) && $student['Stu_picture'] 
    ? $profileImgPath 
    : '../dist/img/default-avatar.svg';
?>

<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<style>
/* Cropper styles */
.cropper-container { user-select: none; }
.cropper-point {
    width: 12px !important; height: 12px !important;
    background-color: #3b82f6; border: 3px solid #fff;
    border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.4);
}
.cropper-line { background-color: #3b82f6; opacity: 0.6; }
</style>

<div class="space-y-6 md:space-y-8">
    
    <!-- Profile Hero Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 shadow-2xl">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-10">
                <!-- Profile Image -->
                <div class="relative group">
                    <div class="w-36 h-36 md:w-48 md:h-48 rounded-[1.5rem] md:rounded-[2rem] overflow-hidden border-4 border-white/30 shadow-2xl bg-white/20 backdrop-blur-sm group-hover:scale-105 transition-transform duration-300">
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="Avatar" class="w-full h-full object-cover" id="mainProfileImg" onerror="this.src='../dist/img/default-avatar.svg'">
                    </div>
                    <?php if ($canEdit): ?>
                    <button onclick="openPhotoOptions()" class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-lg border-2 border-white hover:scale-110 transition-transform">
                        <i class="fas fa-camera text-lg"></i>
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Profile Info -->
                <div class="text-center md:text-left flex-1">
                    <p class="text-blue-200 text-sm font-bold uppercase tracking-widest mb-2">
                        <i class="fas fa-user-graduate mr-1"></i> ข้อมูลนักเรียน
                    </p>
                    <h1 class="text-2xl md:text-4xl font-black text-white mb-3 leading-tight">
                        <?= htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']) ?>
                    </h1>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold text-white">
                            <i class="fas fa-id-badge mr-1"></i> <?= htmlspecialchars($student['Stu_id']) ?>
                        </span>
                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold text-white">
                            <i class="fas fa-school mr-1"></i> ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?>
                        </span>
                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold text-white">
                            <i class="fas fa-list-ol mr-1"></i> เลขที่ <?= htmlspecialchars($student['Stu_no'] ?? '-') ?>
                        </span>
                    </div>
                    
                    <?php if ($canEdit): ?>
                    <button onclick="openEditModal()" class="px-6 py-3 bg-white text-indigo-600 rounded-xl font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition-all flex items-center gap-2 mx-auto md:mx-0">
                        <i class="fas fa-edit"></i>
                        <span>แก้ไขข้อมูล</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Personal Info Card -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-user text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">ข้อมูลส่วนตัว</h3>
                    <p class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Personal Information</p>
                </div>
            </div>
            
            <!-- Desktop: Table -->
            <div class="hidden md:block">
                <table class="w-full">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 w-1/3"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัสนักเรียน</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_id']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">วันเกิด</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= thai_date($student['Stu_birth']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขประจำตัวประชาชน</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_peopleid'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ชั้น/ห้อง</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white">ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขที่</span></td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Stu_no'] ?? '-') ?></td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</span></td>
                            <td class="px-6 py-4 font-bold text-blue-600"><?= htmlspecialchars($student['Stu_phone'] ?? '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile: Cards -->
            <div class="md:hidden p-4 space-y-3">
                <?php
                $personalItems = [
                    ['icon' => 'fa-id-card', 'color' => 'blue', 'label' => 'รหัสนักเรียน', 'value' => $student['Stu_id']],
                    ['icon' => 'fa-user', 'color' => 'indigo', 'label' => 'ชื่อ-สกุล', 'value' => $student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']],
                    ['icon' => 'fa-birthday-cake', 'color' => 'pink', 'label' => 'วันเกิด', 'value' => thai_date($student['Stu_birth'])],
                    ['icon' => 'fa-address-card', 'color' => 'purple', 'label' => 'เลขประจำตัวประชาชน', 'value' => $student['Stu_peopleid'] ?? '-'],
                    ['icon' => 'fa-school', 'color' => 'cyan', 'label' => 'ชั้น/ห้อง', 'value' => 'ม.' . $student['Stu_major'] . '/' . $student['Stu_room']],
                    ['icon' => 'fa-list-ol', 'color' => 'amber', 'label' => 'เลขที่', 'value' => $student['Stu_no'] ?? '-'],
                    ['icon' => 'fa-phone', 'color' => 'green', 'label' => 'เบอร์โทร', 'value' => $student['Stu_phone'] ?? '-'],
                ];
                foreach ($personalItems as $item): ?>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-<?= $item['color'] ?>-100 dark:bg-<?= $item['color'] ?>-900/30 text-<?= $item['color'] ?>-600 rounded-xl flex items-center justify-center">
                        <i class="fas <?= $item['icon'] ?>"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?= $item['label'] ?></p>
                        <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($item['value']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact & Address Card -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-map-marker-alt text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">ที่อยู่และการติดต่อ</h3>
                    <p class="text-[10px] font-bold text-teal-200 uppercase tracking-widest">Contact Information</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Address -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">ที่อยู่</p>
                            <p class="font-bold text-slate-700 dark:text-white text-sm"><?= htmlspecialchars($student['Stu_addr'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เบอร์นักเรียน</p>
                                <p class="font-bold text-green-600"><?= htmlspecialchars($student['Stu_phone'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fab fa-line"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Line ID</p>
                                <p class="font-bold text-blue-600"><?= htmlspecialchars($student['Stu_line'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Info Card -->
        <div class="lg:col-span-2 glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-5 flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-users text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">ข้อมูลผู้ปกครอง</h3>
                    <p class="text-[10px] font-bold text-amber-200 uppercase tracking-widest">Parent Information</p>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ชื่อผู้ปกครอง</p>
                                <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_name'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ความสัมพันธ์</p>
                                <p class="font-bold text-slate-700 dark:text-white"><?= htmlspecialchars($student['Par_relation'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เบอร์ผู้ปกครอง</p>
                                <p class="font-bold text-green-600"><?= htmlspecialchars($student['Par_phone'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-home"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ที่อยู่ผู้ปกครอง</p>
                                <p class="font-bold text-slate-700 dark:text-white text-sm"><?= htmlspecialchars($student['Par_addr'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Info Modal -->
<div id="editModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeEditModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white flex items-center gap-2">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลนักเรียน
                </h3>
                <button onclick="closeEditModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="editModalBody">
                <!-- Form will be loaded here -->
                <div class="text-center py-8">
                    <div class="w-12 h-12 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">กำลังโหลด...</p>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex gap-3">
                <button onclick="closeEditModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="saveStudentInfo()" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Cropper Modal -->
<div id="cropModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closeCropModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white flex items-center gap-2">
                    <i class="fas fa-crop-alt"></i> ตัดแต่งรูปภาพ
                </h3>
                <button onclick="closeCropModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="bg-slate-100 dark:bg-slate-700 rounded-xl p-4" style="max-height: 400px; overflow: hidden;">
                            <img id="cropImage" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                    <div class="text-center">
                        <h6 class="text-sm font-bold text-slate-600 dark:text-slate-300 mb-3">ตัวอย่าง</h6>
                        <div id="cropPreview" class="w-32 h-40 mx-auto bg-slate-200 dark:bg-slate-600 rounded-xl overflow-hidden mb-4"></div>
                        <div class="flex flex-wrap justify-center gap-2">
                            <button onclick="cropper.rotate(90)" class="px-3 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg font-bold text-sm hover:bg-blue-200 transition">
                                <i class="fas fa-redo"></i> หมุนขวา
                            </button>
                            <button onclick="cropper.rotate(-90)" class="px-3 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg font-bold text-sm hover:bg-blue-200 transition">
                                <i class="fas fa-undo"></i> หมุนซ้าย
                            </button>
                            <button onclick="cropper.reset()" class="px-3 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-lg font-bold text-sm hover:bg-amber-200 transition">
                                <i class="fas fa-sync"></i> รีเซ็ต
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex gap-3">
                <button onclick="closeCropModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="uploadCroppedImage()" id="cropUploadBtn" class="flex-1 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-upload mr-2"></i>อัปโหลด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cropper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<script>
let cropper;
const currentPhoto = '<?= htmlspecialchars($imgPath) ?>';

// Edit Modal
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
    fetch('std_information_edit_form.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('editModalBody').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('editModalBody').innerHTML = '<div class="text-center text-red-500 py-8">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>';
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function saveStudentInfo() {
    const form = document.getElementById('editStudentForm');
    if (!form) return;
    
    const formData = new FormData(form);
    
    fetch('api/update_student_info.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                confirmButtonColor: '#3b82f6'
            }).then(() => location.reload());
        } else {
            Swal.fire('ผิดพลาด', data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    })
    .catch(() => {
        Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
    });
}

// Photo Options
function openPhotoOptions() {
    Swal.fire({
        title: '📷 จัดการรูปโปรไฟล์',
        html: `
            <div class="grid grid-cols-2 gap-4 mt-4">
                <button onclick="Swal.close(); useExistingPhoto()" class="p-6 bg-blue-50 hover:bg-blue-100 rounded-2xl transition text-center group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-blue-500 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                    <p class="font-bold text-slate-700">ใช้ภาพเดิม</p>
                    <p class="text-xs text-slate-500">ตัดแต่งภาพปัจจุบัน</p>
                </button>
                <button onclick="Swal.close(); uploadNewPhoto()" class="p-6 bg-green-50 hover:bg-green-100 rounded-2xl transition text-center group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-green-500 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition">
                        <i class="fas fa-upload text-2xl"></i>
                    </div>
                    <p class="font-bold text-slate-700">อัปโหลดใหม่</p>
                    <p class="text-xs text-slate-500">เลือกไฟล์ใหม่</p>
                </button>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        width: '400px'
    });
}

function useExistingPhoto() {
    initCropperFromURL(currentPhoto);
}

function uploadNewPhoto() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('ผิดพลาด', 'ไฟล์ใหญ่เกินไป (ไม่เกิน 5MB)', 'error');
                return;
            }
            const reader = new FileReader();
            reader.onload = e => setupCropper(e.target.result);
            reader.readAsDataURL(file);
        }
    };
    input.click();
}

function initCropperFromURL(imageUrl) {
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = this.naturalWidth;
        canvas.height = this.naturalHeight;
        canvas.getContext('2d').drawImage(this, 0, 0);
        setupCropper(canvas.toDataURL('image/jpeg', 0.9));
    };
    img.onerror = () => setupCropper(imageUrl);
    img.src = imageUrl;
}

function setupCropper(imageSrc) {
    document.getElementById('cropImage').src = imageSrc;
    document.getElementById('cropModal').classList.remove('hidden');
    
    setTimeout(() => {
        if (cropper) cropper.destroy();
        cropper = new Cropper(document.getElementById('cropImage'), {
            aspectRatio: 3 / 4,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 0.8,
            preview: '#cropPreview',
            movable: true,
            scalable: true,
            zoomable: true,
            rotatable: true,
            responsive: true
        });
    }, 300);
}

function closeCropModal() {
    document.getElementById('cropModal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

function uploadCroppedImage() {
    if (!cropper) return;
    
    const btn = document.getElementById('cropUploadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังอัปโหลด...';
    
    cropper.getCroppedCanvas({
        width: 400,
        height: 533,
        imageSmoothingQuality: 'high'
    }).toBlob(blob => {
        const formData = new FormData();
        formData.append('profile_pic', blob, 'profile.jpg');
        
        fetch('api/update_profile_pic.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeCropModal();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'อัปโหลดรูปโปรไฟล์เรียบร้อย',
                    confirmButtonColor: '#3b82f6'
                }).then(() => location.reload());
            } else {
                Swal.fire('ผิดพลาด', data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        })
        .catch(() => {
            closeCropModal();
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลด', 'error');
        });
    }, 'image/jpeg', 0.85);
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
