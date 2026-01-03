<?php
/**
 * Report Wroom 2 View - Organization Chart
 * Beautiful Print-Ready Design for Board Display
 */
$pageTitle = $title ?? 'ผังโครงสร้างห้องเรียนสีขาว';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .btn-action {
        transition: all 0.3s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    /* Organization Chart Styles */
    .org-chart {
        position: relative;
    }
    .org-box {
        transition: all 0.3s ease;
        position: relative;
    }
    .org-box:hover {
        transform: scale(1.03);
        z-index: 10;
    }
    .connector-v {
        width: 3px;
        background: linear-gradient(180deg, #6366f1, #8b5cf6);
    }
    .connector-h {
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }
    .member-photo {
        transition: all 0.3s ease;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .member-photo:hover {
        transform: scale(1.2);
        z-index: 20;
    }
    
    /* Print Styles - Optimized for A4 Board Display */
    @media print {
        @page { 
            size: A4 portrait; 
            margin: 8mm 6mm 8mm 6mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html, body {
            width: 210mm;
            height: 297mm;
        }
        body { 
            background: white !important; 
            font-family: 'Mali', 'TH Sarabun New', sans-serif !important;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .no-print { 
            display: none !important; 
        }
        #printableChart {
            display: block !important;
            width: 100%;
            max-width: 195mm;
            margin: 0 auto;
            padding: 0;
        }
        .glass-card {
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
        .org-box {
            border: 2px solid #374151 !important;
            background: white !important;
            box-shadow: none !important;
        }
        .org-box-leader {
            background: linear-gradient(135deg, #f43f5e, #ec4899) !important;
            color: white !important;
        }
        .connector-v, .connector-h {
            background: #374151 !important;
        }
        .member-photo {
            border: 2px solid #374151 !important;
        }
    }
    @media screen {
        #printableChart {
            display: block;
        }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">👥</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800">ผังโครงสร้างห้องเรียนสีขาว</h1>
                <p class="text-slate-500 font-semibold text-sm mt-1">
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?> • ปีการศึกษา <?= htmlspecialchars($pee) ?>
                </p>
            </div>
            <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80 hidden md:block">
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <button onclick="exportToPDF()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-file-pdf"></i> 📄 Export PDF
    </button>
    <button onclick="exportToImage()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-image"></i> 🖼️ Export รูปภาพ
    </button>
    <button onclick="location.href='report_wroom.php'" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-list"></i> รายชื่อ
    </button>
    <button onclick="location.href='wroom.php'" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-slate-500 to-gray-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-arrow-left"></i> กลับ
    </button>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
        <div class="animate-spin w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="font-bold text-slate-700">กำลังสร้างไฟล์...</p>
        <p class="text-sm text-slate-500 mt-1">กรุณารอสักครู่</p>
    </div>
</div>

<!-- Printable Organization Chart -->
<div id="printableChart" class="glass-card rounded-2xl p-4 md:p-8 border border-slate-200 shadow-2xl org-chart">
    
    <!-- Header with School Logo -->
    <div class="text-center mb-6 pb-4 border-b-4 border-indigo-500">
        <div class="flex items-center justify-center gap-4 mb-3">
            <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16">
            <div class="text-left">
                <h1 class="text-2xl md:text-3xl font-black text-indigo-700">ผังโครงสร้างห้องเรียนสีขาว</h1>
                <p class="text-sm text-slate-600 font-bold">โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์</p>
            </div>
        </div>
        <div class="inline-block bg-indigo-100 px-6 py-2 rounded-full">
            <span class="font-bold text-indigo-800">ชั้น ม.<?= $class ?>/<?= $room ?> &nbsp;•&nbsp; ปีการศึกษา <?= $pee ?></span>
        </div>
    </div>

    <!-- Advisors Section -->
    <div class="text-center mb-4">
        <div class="org-box inline-block bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl px-6 py-4 shadow-xl">
            <p class="text-white font-black text-sm mb-3 tracking-wide">👨‍🏫 ครูที่ปรึกษา</p>
            <div class="flex justify-center gap-4 flex-wrap">
                <?php foreach ($roomTeachers as $t): ?>
                <div class="text-center">
                    <?php if (!empty($t['Teach_photo'])): ?>
                    <img src="https://std.phichai.ac.th/teacher/uploads/phototeach/<?= htmlspecialchars($t['Teach_photo']) ?>" 
                         class="member-photo w-14 h-14 rounded-full object-cover mx-auto mb-1">
                    <?php else: ?>
                    <div class="w-14 h-14 bg-white/30 rounded-full flex items-center justify-center mx-auto mb-1">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                    <?php endif; ?>
                    <p class="text-white text-xs font-bold"><?= htmlspecialchars($t['Teach_name']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Connector -->
    <div class="flex justify-center mb-4"><div class="connector-v h-8 rounded-full"></div></div>

    <!-- Leader (Head of Class) -->
    <div class="text-center mb-4">
        <div class="org-box org-box-leader inline-block bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl px-8 py-4 shadow-2xl min-w-[180px]">
            <span class="text-3xl block mb-2">👤</span>
            <p class="text-white font-black text-base mb-2">หัวหน้าห้อง</p>
            <?php if (!empty($grouped['1'])): ?>
                <?php foreach ($grouped['1'] as $s): ?>
                <div class="text-center">
                    <?php if (!empty($s['Stu_picture'])): ?>
                    <img src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($s['Stu_picture']) ?>" 
                         class="member-photo w-16 h-16 rounded-full object-cover mx-auto mb-2">
                    <?php endif; ?>
                    <p class="text-white font-bold text-sm"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-white/70 text-sm italic">- ยังไม่ได้เลือก -</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Connector with branches -->
    <div class="flex justify-center mb-2"><div class="connector-v h-6 rounded-full"></div></div>
    <div class="flex justify-center mb-4">
        <div class="connector-h w-3/4 max-w-xl rounded-full"></div>
    </div>

    <!-- Vice Leaders Row -->
    <div class="grid grid-cols-4 gap-2 md:gap-4 mb-4 max-w-4xl mx-auto">
        <?php 
        $vicePositions = [
            '5' => ['emoji' => '🚨', 'label' => 'ฝ่ายสารวัตร', 'gradient' => 'from-red-500 to-rose-600', 'bg' => 'red'],
            '2' => ['emoji' => '📘', 'label' => 'ฝ่ายการเรียน', 'gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'blue'],
            '3' => ['emoji' => '🛠️', 'label' => 'ฝ่ายการงาน', 'gradient' => 'from-orange-500 to-amber-600', 'bg' => 'orange'],
            '4' => ['emoji' => '🎉', 'label' => 'ฝ่ายกิจกรรม', 'gradient' => 'from-purple-500 to-violet-600', 'bg' => 'purple'],
        ];
        foreach ($vicePositions as $key => $pos): ?>
        <div class="text-center">
            <div class="flex justify-center mb-1"><div class="connector-v h-4 rounded-full"></div></div>
            <div class="org-box bg-gradient-to-br <?= $pos['gradient'] ?> rounded-xl p-2 md:p-3 shadow-lg">
                <span class="text-xl md:text-2xl block"><?= $pos['emoji'] ?></span>
                <p class="text-white font-bold text-[10px] md:text-xs mb-1">รองฯ <?= $pos['label'] ?></p>
                <?php if (!empty($grouped[$key])): ?>
                    <?php foreach ($grouped[$key] as $s): ?>
                    <div class="text-center">
                        <?php if (!empty($s['Stu_picture'])): ?>
                        <img src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($s['Stu_picture']) ?>" 
                             class="member-photo w-10 h-10 md:w-12 md:h-12 rounded-full object-cover mx-auto mb-1">
                        <?php endif; ?>
                        <p class="text-white text-[8px] md:text-[10px] font-medium"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white/70 text-[8px] italic">-</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Core Members Row -->
    <div class="grid grid-cols-4 gap-2 md:gap-4 mb-4 max-w-4xl mx-auto">
        <?php 
        $corePositions = [
            '9' => ['emoji' => '🛡️', 'label' => 'ฝ่ายสารวัตร', 'gradient' => 'from-pink-400 to-rose-500', 'light' => 'bg-pink-50 border-pink-200'],
            '6' => ['emoji' => '📚', 'label' => 'ฝ่ายการเรียน', 'gradient' => 'from-sky-400 to-blue-500', 'light' => 'bg-sky-50 border-sky-200'],
            '7' => ['emoji' => '🔧', 'label' => 'ฝ่ายการงาน', 'gradient' => 'from-amber-400 to-orange-500', 'light' => 'bg-amber-50 border-amber-200'],
            '8' => ['emoji' => '🎭', 'label' => 'ฝ่ายกิจกรรม', 'gradient' => 'from-violet-400 to-purple-500', 'light' => 'bg-violet-50 border-violet-200'],
        ];
        foreach ($corePositions as $key => $pos): ?>
        <div class="text-center">
            <div class="flex justify-center mb-1"><div class="connector-v h-4 rounded-full"></div></div>
            <div class="org-box <?= $pos['light'] ?> border-2 rounded-xl p-2 md:p-3">
                <span class="text-lg md:text-xl block"><?= $pos['emoji'] ?></span>
                <p class="font-bold text-[9px] md:text-[10px] text-slate-700 mb-1">แกนนำ<?= $pos['label'] ?></p>
                <?php if (!empty($grouped[$key])): ?>
                    <div class="space-y-1">
                    <?php foreach ($grouped[$key] as $s): ?>
                    <div class="flex items-center gap-1 justify-center">
                        <?php if (!empty($s['Stu_picture'])): ?>
                        <img src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($s['Stu_picture']) ?>" 
                             class="w-6 h-6 rounded-full object-cover border border-slate-300">
                        <?php endif; ?>
                        <span class="text-[7px] md:text-[8px] text-slate-600"><?= htmlspecialchars($s['Stu_name'] . ' ' . $s['Stu_sur']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-slate-400 text-[8px] italic">-</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Connector -->
    <div class="flex justify-center mb-4">
        <div class="connector-h w-1/3 rounded-full"></div>
    </div>

    <!-- Secretary Row -->
    <div class="flex justify-center gap-4 mb-6">
        <!-- เลขานุการ -->
        <div class="text-center">
            <div class="flex justify-center mb-1"><div class="connector-v h-4 rounded-full"></div></div>
            <div class="org-box bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl px-4 py-3 shadow-lg min-w-[120px]">
                <span class="text-xl block">📝</span>
                <p class="text-white font-bold text-xs mb-1">เลขานุการ</p>
                <?php if (!empty($grouped['10'])): ?>
                    <?php foreach ($grouped['10'] as $s): ?>
                    <div class="text-center">
                        <?php if (!empty($s['Stu_picture'])): ?>
                        <img src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($s['Stu_picture']) ?>" 
                             class="member-photo w-10 h-10 rounded-full object-cover mx-auto mb-1">
                        <?php endif; ?>
                        <p class="text-white text-[9px] font-medium"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white/70 text-[8px] italic">-</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ผู้ช่วยเลขานุการ -->
        <div class="text-center">
            <div class="flex justify-center mb-1"><div class="connector-v h-4 rounded-full"></div></div>
            <div class="org-box bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl px-4 py-3 shadow-lg min-w-[120px]">
                <span class="text-xl block">🗂️</span>
                <p class="text-white font-bold text-xs mb-1">ผู้ช่วยเลขาฯ</p>
                <?php if (!empty($grouped['11'])): ?>
                    <?php foreach ($grouped['11'] as $s): ?>
                    <div class="text-center">
                        <?php if (!empty($s['Stu_picture'])): ?>
                        <img src="https://std.phichai.ac.th/photo/<?= htmlspecialchars($s['Stu_picture']) ?>" 
                             class="member-photo w-10 h-10 rounded-full object-cover mx-auto mb-1">
                        <?php endif; ?>
                        <p class="text-white text-[9px] font-medium"><?= htmlspecialchars($s['Stu_pre'] . $s['Stu_name'] . ' ' . $s['Stu_sur']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white/70 text-[8px] italic">-</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Members Section -->
    <div class="bg-slate-100 rounded-2xl p-4 mb-4 text-center border-2 border-slate-300">
        <div class="flex items-center justify-center gap-2 mb-2">
            <span class="text-2xl">👥</span>
            <p class="font-black text-slate-700 text-lg">สมาชิกห้องเรียนสีขาว</p>
        </div>
        <p class="text-slate-500 text-sm">นักเรียนทุกคนในห้องเรียน ม.<?= $class ?>/<?= $room ?></p>
    </div>

    <!-- Maxim Banner -->
    <div class="bg-gradient-to-r from-amber-400 via-orange-400 to-amber-400 rounded-2xl p-4 text-center shadow-lg border-4 border-amber-500">
        <p class="font-black text-amber-900 text-sm mb-1">✍️ คติพจน์ห้องเรียนสีขาว</p>
        <?php if ($maxim): ?>
        <p class="text-xl md:text-2xl font-black text-amber-900 italic">"<?= htmlspecialchars($maxim) ?>"</p>
        <?php else: ?>
        <p class="text-amber-700 italic">- ยังไม่ได้กรอกคติพจน์ -</p>
        <?php endif; ?>
    </div>

    <!-- Footer for Print -->
    <div class="mt-4 pt-4 border-t-2 border-slate-200 text-center">
        <p class="text-[10px] text-slate-400">ระบบดูแลช่วยเหลือนักเรียน โรงเรียนพิชัย • พิมพ์เมื่อ <?= date('d/m/Y') ?></p>
    </div>
</div>

<!-- html2canvas & jsPDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// Convert external images to base64 via proxy
async function convertImagesToBase64(element) {
    const images = element.querySelectorAll('img');
    const promises = [];
    
    for (const img of images) {
        const src = img.src;
        
        // Skip if already base64 or local
        if (src.startsWith('data:') || src.includes('localhost') || src.includes('/dist/')) {
            continue;
        }
        
        // Convert external images via proxy
        const promise = fetch('../teacher/api/image_proxy.php?url=' + encodeURIComponent(src))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    img.src = data.data;
                }
            })
            .catch(err => {
                console.warn('Failed to convert image:', src, err);
            });
        
        promises.push(promise);
    }
    
    await Promise.all(promises);
    
    // Wait a bit for images to render
    await new Promise(resolve => setTimeout(resolve, 500));
}

// Export to PDF Function
async function exportToPDF() {
    const element = document.getElementById('printableChart');
    const overlay = document.getElementById('loadingOverlay');
    const filename = 'ผังห้องเรียนสีขาว_ม<?= $class ?>-<?= $room ?>_<?= $pee ?>.pdf';
    
    overlay.classList.remove('hidden');
    
    try {
        // Convert external images to base64
        await convertImagesToBase64(element);
        
        // Create canvas with high quality
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            width: element.scrollWidth,
            height: element.scrollHeight
        });
        
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const { jsPDF } = window.jspdf;
        
        // A4 size in mm
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        const pageWidth = 210;
        const pageHeight = 297;
        const margin = 5;
        const contentWidth = pageWidth - (margin * 2);
        
        // Calculate height to maintain aspect ratio
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;
        const ratio = contentWidth / imgWidth;
        const contentHeight = imgHeight * ratio;
        
        // Add image to PDF
        pdf.addImage(imgData, 'JPEG', margin, margin, contentWidth, contentHeight);
        
        // Save PDF
        pdf.save(filename);
        
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: 'ดาวน์โหลด PDF เรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('PDF Export Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้าง PDF ได้ กรุณาลองใหม่อีกครั้ง'
        });
    } finally {
        overlay.classList.add('hidden');
        // Reload page to restore original image sources
        // location.reload();
    }
}

// Export to Image Function
async function exportToImage() {
    const element = document.getElementById('printableChart');
    const overlay = document.getElementById('loadingOverlay');
    const filename = 'ผังห้องเรียนสีขาว_ม<?= $class ?>-<?= $room ?>_<?= $pee ?>.png';
    
    overlay.classList.remove('hidden');
    
    try {
        // Convert external images to base64
        await convertImagesToBase64(element);
        
        const canvas = await html2canvas(element, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false
        });
        
        // Create download link
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
        
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ!',
            text: 'ดาวน์โหลดรูปภาพเรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('Image Export Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างรูปภาพได้ กรุณาลองใหม่อีกครั้ง'
        });
    } finally {
        overlay.classList.add('hidden');
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
