<?php
/**
 * Edit Visit Form Template
 * Modern UI with Tailwind CSS & Mobile Responsive
 */

$yearImg = $data['Pee'] - 543;
$fullName = $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur'];

// Questions configuration
$questions = [
    "1. บ้านที่อยู่อาศัย" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"],
    "2. ระยะทางระหว่างบ้านกับโรงเรียน" => ["1-5 กม.", "6-10 กม.", "11-15 กม.", "16-20 กม.", "20 กม.ขึ้นไป"],
    "3. การเดินทางไปโรงเรียน" => ["เดิน", "จักรยาน", "มอเตอร์ไซค์", "รถยนต์", "รถรับส่ง", "อื่นๆ"],
    "4. สภาพแวดล้อมของบ้าน" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"],
    "5. อาชีพของผู้ปกครอง" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"],
    "6. สถานที่ทำงานของบิดามารดา" => ["อำเภอเดียวกัน", "จังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"],
    "7. สถานภาพของบิดามารดา" => ["อยู่ด้วยกัน", "หย่าร้าง", "บิดาเสียชีวิต", "มารดาเสียชีวิต", "ทั้งคู่เสียชีวิต"],
    "8. วิธีการอบรมเลี้ยงดู" => ["เข้มงวด", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"],
    "9. โรคประจำตัวของนักเรียน" => ["ไม่มี", "มี"],
    "10. ความสัมพันธ์ในครอบครัว" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"],
    "11. หน้าที่รับผิดชอบในบ้าน" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"],
    "12. สนิทกับใครมากที่สุด" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
    "13. รายได้กับการใช้จ่าย" => ["เพียงพอ", "ไม่พอบางครั้ง", "ขัดสน"],
    "14. ลักษณะเพื่อนเล่นที่บ้าน" => ["รุ่นเดียวกัน", "รุ่นน้อง", "รุ่นพี่", "ทุกรุ่น"],
    "15. ความต้องการเมื่อจบการศึกษา" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"],
    "16. เมื่อมีปัญหาจะปรึกษาใคร" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
    "17. ความรู้สึกต่อครูที่มาเยี่ยม" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
    "18. ทัศนคติต่อโรงเรียน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
];

$images = [
    ["id" => "image1", "label" => "รูปที่ 1", "desc" => "ภาพตัวบ้านนักเรียน (ให้เห็นทั้งหลัง)", "icon" => "🏠", "picture" => $data['picture1']],
    ["id" => "image2", "label" => "รูปที่ 2", "desc" => "ภาพภายในบ้านนักเรียน", "icon" => "🛋️", "picture" => $data['picture2']],
    ["id" => "image3", "label" => "รูปที่ 3", "desc" => "ภาพครูเยี่ยมบ้านกับนักเรียนและผู้ปกครอง", "icon" => "👨‍👩‍👧", "picture" => $data['picture3']],
    ["id" => "image4", "label" => "รูปที่ 4", "desc" => "ภาพเพิ่มเติม (ถ้ามี)", "icon" => "📷", "picture" => $data['picture4']],
    ["id" => "image5", "label" => "รูปที่ 5", "desc" => "ภาพเพิ่มเติม (ถ้ามี)", "icon" => "📷", "picture" => $data['picture5']],
];
?>

<div class="space-y-6">
    <!-- Student Info Card -->
    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl">
                🏠
            </div>
            <div class="text-center md:text-left flex-1">
                <h3 class="text-lg md:text-xl font-bold"><?= htmlspecialchars($fullName) ?></h3>
                <div class="flex flex-wrap justify-center md:justify-start gap-x-4 gap-y-1 mt-2 text-sm text-white/80">
                    <span><i class="fas fa-id-card mr-1"></i> <?= $data['Stu_id'] ?></span>
                    <span><i class="fas fa-school mr-1"></i> ม.<?= $data['Stu_major'] ?>/<?= $data['Stu_room'] ?></span>
                </div>
            </div>
            <div class="bg-white/20 rounded-xl px-4 py-2 text-center">
                <p class="text-xs text-white/70">ภาคเรียน</p>
                <p class="text-lg font-bold"><?= $data['Term'] ?>/<?= $data['Pee'] ?></p>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="mt-4 pt-4 border-t border-white/20 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">📍</span>
                <span class="truncate"><?= htmlspecialchars($data['Stu_addr'] ?: 'ไม่ระบุที่อยู่') ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">📞</span>
                <span><?= htmlspecialchars($data['Stu_phone'] ?: 'ไม่ระบุเบอร์โทร') ?></span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="post" id="editVisitForm" enctype="multipart/form-data" class="space-y-4">
        <!-- Instructions -->
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl">
            <div class="flex items-start gap-3">
                <span class="text-2xl">📋</span>
                <div>
                    <h4 class="font-bold text-amber-800">แบบบันทึกการเยี่ยมบ้านนักเรียน</h4>
                    <p class="text-sm text-amber-700 mt-1">กรุณาเลือกคำตอบที่ตรงกับความเป็นจริงมากที่สุด</p>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="space-y-3">
            <?php
            $i = 1;
            foreach ($questions as $question => $options):
                $currentValue = isset($data['vh' . $i]) ? $data['vh' . $i] : null;
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                <h5 class="font-bold text-slate-800 dark:text-white text-sm mb-3"><?= $question ?></h5>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($options as $index => $option): 
                        $radioId = 'vh' . $i . '-' . $index;
                        $isChecked = $currentValue == ($index + 1);
                    ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="vh<?= $i ?>" value="<?= $index + 1 ?>" <?= $isChecked ? 'checked' : '' ?> required class="peer hidden">
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-xs md:text-sm font-semibold border-2 
                            peer-checked:bg-cyan-500 peer-checked:border-cyan-500 peer-checked:text-white
                            border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300
                            hover:border-cyan-300 transition-all">
                            <?= $option ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php 
            $i++;
            endforeach; 
            ?>
        </div>

        <!-- Images Section -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <h5 class="font-bold text-slate-800 dark:text-white text-sm mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center">📷</span>
                19. รูปถ่ายการเยี่ยมบ้าน (สูงสุด 5 รูป)
            </h5>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($images as $image): 
                    $imagePath = $image['picture'];
                ?>
                <div class="bg-slate-50 dark:bg-slate-700 rounded-xl p-3 border-2 border-dashed border-slate-200 dark:border-slate-600">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl"><?= $image['icon'] ?></span>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white"><?= $image['label'] ?></p>
                            <p class="text-[10px] text-slate-500"><?= $image['desc'] ?></p>
                        </div>
                    </div>
                    
                    <div id="preview-<?= $image['id'] ?>" class="mb-2 rounded-lg overflow-hidden relative">
                        <?php if ($imagePath): ?>
                            <img src="uploads/visithome<?= $yearImg ?>/<?= $imagePath ?>?t=<?= time() ?>" 
                                 alt="<?= $image['label'] ?>" 
                                 class="w-full h-24 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="h-24 bg-slate-200 dark:bg-slate-600 rounded-lg flex items-center justify-center">
                                <span class="text-slate-400 text-xs">ยังไม่มีรูปภาพ</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="remove_<?= $image['id'] ?>" id="remove_<?= $image['id'] ?>" value="0">
                    <input type="file" 
                           name="<?= $image['id'] ?>" 
                           id="<?= $image['id'] ?>" 
                           accept="image/*,.heic,.heif"
                           onchange="handleImagePreview(this, 'preview-<?= $image['id'] ?>')"
                           class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200 transition">
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Problems/Assistance -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <h5 class="font-bold text-slate-800 dark:text-white text-sm mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-rose-100 dark:bg-rose-900/30 rounded-lg flex items-center justify-center">💬</span>
                20. ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ
            </h5>
            <textarea name="vh20" id="vh20" rows="4" 
                      class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 resize-none"
                      placeholder="ระบุปัญหา อุปสรรค หรือความต้องการความช่วยเหลือ..."><?= isset($data['vh20']) ? htmlspecialchars($data['vh20']) : '' ?></textarea>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="stuId" value="<?= $data['Stu_id'] ?>">
        <input type="hidden" name="term" value="<?= $data['Term'] ?>">
        <input type="hidden" name="pee" value="<?= $data['Pee'] ?>">

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                <i class="fas fa-save"></i>
                บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>

<script>
if (typeof window.handleImagePreview === 'undefined') {
    // Dynamically load heic2any if not present
    if (typeof heic2any === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
        document.head.appendChild(script);
    }
    
    window.uploadedWebpBlobs = window.uploadedWebpBlobs || {};
    
    window.processImageToWebp = async function(file) {
        let imageFile = file;
        const fileName = file.name.toLowerCase();
        const isHeic = fileName.endsWith('.heic') || fileName.endsWith('.heif') || file.type === 'image/heic' || file.type === 'image/heif';

        if (isHeic) {
            if (typeof heic2any === 'undefined') {
                throw new Error('กำลังโหลดไลบรารี HEIC กรุณาลองใหม่อีกครั้ง');
            }
            try {
                const convertedBlob = await heic2any({
                    blob: file,
                    toType: 'image/jpeg',
                    quality: 0.8
                });
                imageFile = Array.isArray(convertedBlob) ? convertedBlob[0] : convertedBlob;
            } catch (error) {
                console.error('HEIC conversion failed:', error);
                throw new Error('ไม่สามารถแปลงไฟล์ HEIC ได้: ' + error.message);
            }
        }

        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 800;
                    const maxHeight = 600;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height);
                        width = Math.round(width * ratio);
                        height = Math.round(height * ratio);
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {
                        if (blob) {
                            resolve(blob);
                        } else {
                            reject(new Error('การแปลงภาพเป็น WebP ล้มเหลว'));
                        }
                    }, 'image/webp', 0.85);
                };
                img.onerror = function() {
                    reject(new Error('ไม่สามารถโหลดภาพเพื่อปรับขนาดได้'));
                };
                img.src = event.target.result;
            };
            reader.onerror = function() {
                reject(new Error('ไม่สามารถอ่านไฟล์ภาพได้'));
            };
            reader.readAsDataURL(imageFile);
        });
    };

    window.handleImagePreview = async function(input, previewId) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const container = document.getElementById(previewId);
        if (!container) return;

        const originalContent = container.innerHTML;
        container.innerHTML = `
            <div class="h-full w-full flex flex-col items-center justify-center p-4 text-center bg-slate-100 dark:bg-slate-700 animate-pulse relative z-20">
                <div class="w-8 h-8 rounded-full border-4 border-blue-500 border-t-transparent animate-spin mb-2"></div>
                <p class="text-[10px] text-blue-600 dark:text-blue-400 font-bold leading-tight">กำลังแปลงไฟล์และลดขนาด...</p>
            </div>
        `;

        try {
            const webpBlob = await window.processImageToWebp(file);
            
            const form = input.closest('form');
            const formId = form ? form.id : 'unknownForm';
            
            window.uploadedWebpBlobs[formId] = window.uploadedWebpBlobs[formId] || {};
            window.uploadedWebpBlobs[formId][input.name] = webpBlob;

            const objectUrl = URL.createObjectURL(webpBlob);
            container.innerHTML = `
                <img src="${objectUrl}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <span class="px-4 py-2 bg-white text-slate-800 rounded-xl font-bold text-sm">เปลี่ยนรูปภาพ</span>
                </div>
            `;

            const removeId = 'remove_' + input.id;
            const removeInput = document.getElementById(removeId);
            if (removeInput) {
                removeInput.value = "0";
            }
        } catch (error) {
            console.error('Image preprocessing error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message || 'ไม่สามารถประมวลผลรูปภาพได้'
                });
            } else {
                alert(error.message || 'ไม่สามารถประมวลผลรูปภาพได้');
            }
            container.innerHTML = originalContent;
            input.value = '';
        }
    };
}

// Intercept form submit for direct submissions (e.g. from poor.php)
$(document).ready(function() {
    $('#editVisitForm').on('submit', function(e) {
        // If we are in visithome.php, it has its own ajax submit handler and will override/bypass standard submit.
        // But in poor.php, it will submit normally. We intercept here to do client-side WebP uploads.
        const formId = this.id;
        if (window.uploadedWebpBlobs && window.uploadedWebpBlobs[formId]) {
            e.preventDefault();
            const formElement = this;
            const formData = new FormData(formElement);
            
            for (let i = 1; i <= 5; i++) {
                const fileKey = 'image' + i;
                const blob = window.uploadedWebpBlobs[formId][fileKey];
                if (blob) {
                    formData.delete(fileKey);
                    formData.append(fileKey, blob, fileKey + '.webp');
                }
            }
            
            const targetUrl = '../teacher/api/update_visit_data.php';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'กำลังบันทึก...', didOpen: () => Swal.showLoading() });
            }
            
            $.ajax({
                url: targetUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('สำเร็จ', 'อัปเดตข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                                    // Bootstrap dismiss modal
                                    const modalElement = $('#visitModal');
                                    if (modalElement.length) modalElement.modal('hide');
                                    if (typeof loadTable === 'function') loadTable();
                                });
                            } else {
                                alert('อัปเดตข้อมูลเรียบร้อยแล้ว');
                                const modalElement = $('#visitModal');
                                if (modalElement.length) modalElement.modal('hide');
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('ล้มเหลว', res.message, 'error');
                            } else {
                                alert('ล้มเหลว: ' + res.message);
                            }
                        }
                    } catch(err) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('สำเร็จ', 'อัปเดตข้อมูลเรียบร้อยแล้ว', 'success').then(() => {
                                const modalElement = $('#visitModal');
                                if (modalElement.length) modalElement.modal('hide');
                                if (typeof loadTable === 'function') loadTable();
                            });
                        } else {
                            alert('อัปเดตข้อมูลเรียบร้อยแล้ว');
                            const modalElement = $('#visitModal');
                            if (modalElement.length) modalElement.modal('hide');
                        }
                    }
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('ล้มเหลว', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    } else {
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    }
                }
            });
        }
    });
});
</script>