<?php
/**
 * Teacher Profile View - MVC Pattern
 * Modern UI with Tailwind CSS and Glassmorphism
 */
ob_start();
?>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }
    .profile-gradient {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
    }
    .profile-image-container::after {
        content: '';
        position: absolute;
        inset: -8px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 0.4; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }
    .fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb or Header -->
    <div class="mb-8 fade-in-up">
        <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-id-card text-xl"></i>
            </div>
            โปรไฟล์ของฉัน
        </h1>
        <p class="mt-2 text-slate-600 dark:text-slate-400">จัดการข้อมูลส่วนตัวและรูปภาพโปรไฟล์ของคุณ</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-3xl overflow-hidden shadow-xl border border-white/40 dark:border-slate-700/40 fade-in-up" style="animation-delay: 0.1s">
                <div class="h-32 profile-gradient relative"></div>
                <div class="px-6 pb-8 -mt-16 text-center relative z-10">
                    <div class="inline-block relative">
                        <div class="profile-image-container relative">
                            <img src="<?php echo $setting->getImgProfile() . $userData['Teach_photo']; ?>" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-2xl mx-auto"
                                 alt="<?php echo $userData['Teach_name']; ?>">
                        </div>
                        <label for="imageUpload" class="absolute bottom-1 right-1 w-9 h-9 bg-white dark:bg-slate-700 rounded-full shadow-lg flex items-center justify-center cursor-pointer hover:bg-indigo-50 dark:hover:bg-slate-600 transition-colors border border-slate-100 dark:border-slate-600">
                            <i class="fas fa-camera text-indigo-500 dark:text-indigo-400 text-sm"></i>
                            <input type="file" id="imageUpload" class="hidden" accept="image/*" onchange="handleImageChange(this)">
                        </label>
                    </div>
                    <h2 class="mt-4 text-2xl font-bold text-slate-800 dark:text-white"><?php echo $userData['Teach_name']; ?></h2>
                    <p class="text-indigo-600 dark:text-indigo-400 font-medium"><?php echo $userData['Teach_major']; ?></p>
                    
                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold ring-1 ring-indigo-200 dark:ring-indigo-800/50">
                            ครูประจำชั้น ม.<?php echo $userData['Teach_class']; ?>/<?php echo $userData['Teach_room']; ?>
                        </span>
                        <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-bold ring-1 ring-emerald-200 dark:ring-emerald-800/50">
                            ID: <?php echo $userData['Teach_id']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card rounded-3xl p-6 shadow-lg border border-white/40 dark:border-slate-700/40 fade-in-up" style="animation-delay: 0.2s">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">การจัดการ</h3>
                <div class="grid grid-cols-1 gap-3">
                    <button onclick="openEditModal()" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold shadow-indigo-500/20 shadow-lg hover:shadow-indigo-500/40 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i> แก้ไขข้อมูลส่วนตัว
                    </button>
                    <button onclick="window.history.back()" class="w-full py-3 px-4 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i> ย้อนกลับ
                    </button>
                </div>
            </div>
        </div>

        <!-- Details Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-3xl shadow-xl border border-white/40 dark:border-slate-700/40 overflow-hidden fade-in-up" style="animation-delay: 0.3s">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-info-circle text-indigo-500"></i> ข้อมูลโดยละเอียด
                    </h3>
                </div>
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">ชื่อ-นามสกุล</p>
                            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors"><?php echo $userData['Teach_name']; ?></p>
                        </div>
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">กลุ่มสาระ</p>
                            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors"><?php echo $userData['Teach_major']; ?></p>
                        </div>
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">เพศ</p>
                            <div class="flex items-center gap-2">
                                <?php if($userData['Teach_sex'] == 'ชาย'): ?>
                                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-mars"></i></span>
                                <?php else: ?>
                                    <span class="w-8 h-8 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center"><i class="fas fa-venus"></i></span>
                                <?php endif; ?>
                                <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors"><?php echo $userData['Teach_sex']; ?></p>
                            </div>
                        </div>
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">วัน/เดือน/ปีเกิด</p>
                            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors"><?php echo Utils::convertToThaiDate($userData['Teach_birth']); ?></p>
                        </div>
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">เบอร์โทรศัพท์</p>
                            <a href="tel:<?php echo $userData['Teach_phone']; ?>" class="text-lg font-bold text-indigo-600 hover:text-indigo-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-phone-alt animate-pulse text-sm"></i> <?php echo $userData['Teach_phone']; ?>
                            </a>
                        </div>
                        <div class="group">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">ที่อยู่</p>
                            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 transition-colors"><?php echo $userData['Teach_addr']; ?></p>
                        </div>
                        <div class="group md:col-span-2">
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">ชั้นเรียนที่รับผิดชอบ</p>
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-700 group-hover:border-indigo-200 transition-all flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-amber-600 flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-slate-800 dark:text-white">มัธยมศึกษาปีที่ <?php echo $userData['Teach_class']; ?>/<?php echo $userData['Teach_room']; ?></p>
                                    <p class="text-sm text-slate-500">ครูที่ปรึกษาประจำชั้นเรียน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        
        <div class="relative bg-white dark:bg-slate-800 w-full max-w-2xl rounded-[2.5rem] shadow-2xl transform transition-all overflow-hidden border border-white/20 dark:border-slate-700/50">
            <!-- Modal Header -->
            <div class="px-8 py-6 profile-gradient relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center justify-between">
                    <h3 class="text-2xl font-black text-white flex items-center gap-3">
                        <i class="fas fa-user-edit"></i> แก้ไขข้อมูลโปรไฟล์
                    </h3>
                    <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-all transform hover:rotate-90">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="editForm" class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <input type="hidden" name="Teach_id" value="<?php echo $userData['Teach_id']; ?>">
                <input type="hidden" name="Teach_photo" value="<?php echo $userData['Teach_photo']; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">ชื่อ-นามสกุล</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="Teach_name" value="<?php echo $userData['Teach_name']; ?>" 
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all dark:text-white" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">กลุ่มสาระ</label>
                        <div class="relative">
                            <i class="fas fa-swatchbook absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select name="Teach_major" class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 outline-none transition-all dark:text-white appearance-none">
                                <?php 
                                $majors = ["ภาษาไทย", "คณิตศาสตร์", "วิทยาศาสตร์และเทคโนโลยี", "คอมพิวเตอร์", "สังคมศึกษาฯ", "สุขศึกษาและพลศึกษา", "ศิลปะ", "การงานอาชีพฯ", "ภาษาต่างประเทศ", "พัฒนาผู้เรียน"];
                                
                                // Ensure current major is in the list
                                if ($userData['Teach_major'] && !in_array($userData['Teach_major'], $majors)) {
                                    $majors[] = $userData['Teach_major'];
                                }

                                foreach($majors as $major): 
                                    $selected = ($userData['Teach_major'] == $major) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $major; ?>" <?php echo $selected; ?>><?php echo $major; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">เพศ</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="Teach_sex" value="ชาย" <?php echo ($userData['Teach_sex'] == 'ชาย') ? 'checked' : ''; ?> class="peer hidden">
                                <div class="px-4 py-3 text-center rounded-2xl border-2 border-slate-100 dark:border-slate-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 transition-all">
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400">ชาย</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="Teach_sex" value="หญิง" <?php echo ($userData['Teach_sex'] == 'หญิง') ? 'checked' : ''; ?> class="peer hidden">
                                <div class="px-4 py-3 text-center rounded-2xl border-2 border-slate-100 dark:border-slate-700 peer-checked:border-pink-500 peer-checked:bg-pink-50 dark:peer-checked:bg-pink-900/20 transition-all">
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400 peer-checked:text-pink-600 dark:peer-checked:text-pink-400">หญิง</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">วัน/เดือน/ปีเกิด</label>
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" name="Teach_birth" value="<?php echo $userData['Teach_birth']; ?>" 
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 outline-none transition-all dark:text-white" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">เบอร์โทรศัพท์</label>
                        <div class="relative">
                            <i class="fas fa-phone-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="tel" name="Teach_phone" value="<?php echo $userData['Teach_phone']; ?>" pattern="\d{10}" maxlength="10"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 outline-none transition-all dark:text-white" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">ครูประจำชั้น (ม./ห้อง)</label>
                        <div class="flex gap-2">
                            <input type="text" value="<?php echo $userData['Teach_class']; ?>" readonly class="w-1/2 px-4 py-3 bg-slate-100 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-slate-500 dark:text-slate-400 outline-none">
                            <input type="text" value="<?php echo $userData['Teach_room']; ?>" readonly class="w-1/2 px-4 py-3 bg-slate-100 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-slate-500 dark:text-slate-400 outline-none">
                            <input type="hidden" name="Teach_class" value="<?php echo $userData['Teach_class']; ?>">
                            <input type="hidden" name="Teach_room" value="<?php echo $userData['Teach_room']; ?>">
                        </div>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">ที่อยู่</label>
                        <div class="relative">
                            <i class="fas fa-home absolute left-4 top-4 text-slate-400"></i>
                            <textarea name="Teach_addr" rows="3" class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 outline-none transition-all dark:text-white"><?php echo $userData['Teach_addr']; ?></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-3">
                <button onclick="closeEditModal()" class="flex-1 py-4 px-6 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl font-bold border-2 border-slate-100 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all active:scale-95">
                    ยกเลิก
                </button>
                <button onclick="saveProfile()" class="flex-[2] py-4 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl font-bold shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function handleImageChange(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image1', input.files[0]);
        formData.append('Teach_id', '<?php echo $userData['Teach_id']; ?>');
        formData.append('Teach_name', '<?php echo $userData['Teach_name']; ?>');
        formData.append('Teach_sex', '<?php echo $userData['Teach_sex']; ?>');
        formData.append('Teach_birth', '<?php echo $userData['Teach_birth']; ?>');
        formData.append('Teach_addr', '<?php echo $userData['Teach_addr']; ?>');
        formData.append('Teach_major', '<?php echo $userData['Teach_major']; ?>');
        formData.append('Teach_phone', '<?php echo $userData['Teach_phone']; ?>');
        formData.append('Teach_class', '<?php echo $userData['Teach_class']; ?>');
        formData.append('Teach_room', '<?php echo $userData['Teach_room']; ?>');
        formData.append('Teach_photo', '<?php echo $userData['Teach_photo']; ?>');

        Swal.fire({
            title: 'กำลังอัปโหลด...',
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('api/update_teacher.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('สำเร็จ', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', data.message, 'error');
            }
        });
    }
}

function saveProfile() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    Swal.fire({
        title: 'กำลังบันทึก...',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('api/update_teacher.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('ข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
