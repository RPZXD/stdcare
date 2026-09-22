<?php
/**
 * Soft Power View - StdCare System
 * รูปแบบการบริหารการขับเคลื่อน Soft Power เพื่อพัฒนาอัตลักษณ์ของนักเรียนโรงเรียนพิชัย
 * ดร. รสสุคนธ์ อินชัยเขา ผู้อำนวยการโรงเรียนพิชัย
 */
ob_start();

// Check if embedded in subfolder
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isSubfolder = strpos($currentPath, '/teacher/') !== false 
            || strpos($currentPath, '/admin/') !== false 
            || strpos($currentPath, '/director/') !== false
            || strpos($currentPath, '/student/') !== false
            || strpos($currentPath, '/officer/') !== false;
$prefix = $isSubfolder ? '../' : '';
$docPath = $prefix . 'dist/doc/soft-power-manual.pdf';
$coverImg = $prefix . 'dist/img/soft-power-cover.jpg';
$modelImg = $prefix . 'dist/img/soft-power-model.jpg';
?>

<div class="space-y-8 max-w-7xl mx-auto w-full min-w-0">
    <!-- Header Hero Card -->
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 md:p-10 shadow-xl border border-white/20 dark:border-slate-700/50 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-72 h-72 bg-gradient-to-br from-indigo-500/20 via-sky-400/15 to-transparent rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-72 h-72 bg-gradient-to-tr from-amber-500/20 via-orange-400/15 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/50 border border-blue-200 dark:border-blue-800/50 text-blue-700 dark:text-blue-300 text-xs font-bold tracking-wide uppercase">
                    <i class="fas fa-book-open text-indigo-500 animate-pulse"></i>
                    <span>คู่มือการใช้รูปแบบการบริหารสถานศึกษา</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight text-gray-900 dark:text-white">
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 bg-clip-text text-transparent">รูปแบบการบริหารการขับเคลื่อน Soft Power</span>
                </h1>
                <p class="text-lg md:text-xl font-bold text-gray-700 dark:text-gray-200">
                    เพื่อพัฒนาอัตลักษณ์ของนักเรียนโรงเรียนพิชัย สพม.พิษณุโลก อุตรดิตถ์
                </p>
                <div class="flex items-center gap-3 pt-1 text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 font-medium">
                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                        ดร. รสสุคนธ์ อินชัยเขา (ผู้อำนวยการโรงเรียนพิชัย)
                    </span>
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-medium border border-emerald-200/50 dark:border-emerald-800/50">
                        <i class="fas fa-check-circle"></i> นวัตกรรมการบริหาร
                    </span>
                </div>
            </div>

            <!-- Action / Download / Print Buttons -->
            <div class="flex flex-wrap items-center gap-3 no-print">
                <a href="<?php echo $docPath; ?>" download="คู่มือการใช้รูปแบบ_ผอ_รสสุคนธ์_อินชัยเขา.pdf" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white font-semibold hover:from-blue-700 hover:to-indigo-800 transition shadow-lg shadow-indigo-500/25 active:scale-95 group">
                    <i class="fas fa-file-pdf text-rose-300 group-hover:animate-bounce"></i>
                    <span>ดาวน์โหลดคู่มือฉบับเต็ม (PDF)</span>
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 font-medium transition shadow active:scale-95">
                    <i class="fas fa-print"></i>
                    <span>พิมพ์</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Cover & Key Summary Showcase -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch w-full min-w-0">
        <!-- Cover & Preview -->
        <div class="w-full min-w-0 flex flex-col gap-4">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-5 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col items-center">
                <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-slate-700 shadow-md group relative w-full flex items-center justify-center bg-slate-900/5 dark:bg-slate-950/40 p-2">
                    <img src="<?php echo $coverImg; ?>" alt="หน้าปกคู่มือ Soft Power" class="w-full max-w-full h-auto max-h-[520px] object-contain transition-transform duration-500 group-hover:scale-[1.02]">
                    <div class="absolute bottom-4 right-4 bg-black/70 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-medium">
                        <i class="fas fa-eye mr-1"></i> ปกเอกสารคู่มือ
                    </div>
                </div>
                <div class="w-full mt-4 p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 text-center">
                    <p class="text-sm font-bold text-blue-900 dark:text-blue-200">
                        “สืบสานวัฒนธรรม สร้างสรรค์นวัตกรรม ก้าวสู่สากล”
                    </p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        โรงเรียนพิชัย สร้างคนดี มีความรู้ สู่สังคม
                    </p>
                </div>
            </div>

            <!-- Download Direct Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-6 text-white shadow-xl flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-cloud-arrow-down text-sky-200"></i>
                        เอกสารคู่มือฉบับสมบูรณ์
                    </h4>
                    <p class="text-xs text-blue-100 mt-1">
                        เนื้อหา 4 ส่วนหลัก รวม 38 หน้า พร้อมแบบประเมิน
                    </p>
                </div>
                <a href="<?php echo $docPath; ?>" target="_blank" class="px-4 py-2.5 rounded-xl bg-white text-blue-700 font-bold text-sm shadow hover:bg-blue-50 transition active:scale-95 flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-file-download"></i> เปิดไฟล์ PDF
                </a>
            </div>
        </div>

        <!-- Overview & Model Components -->
        <div class="w-full min-w-0 space-y-6">
            <!-- About the Model -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 md:p-8 shadow-xl border border-white/20 dark:border-slate-700/50">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fas fa-lightbulb"></i>
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">ความเป็นมาและเป้าหมาย</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Soft Power เพื่อพัฒนาอัตลักษณ์ของนักเรียน</p>
                    </div>
                </div>
                <div class="prose dark:prose-invert text-gray-700 dark:text-gray-300 text-justify leading-relaxed text-sm md:text-base space-y-3">
                    <p>
                        <strong>โรงเรียนพิชัย</strong> ได้พัฒนารูปแบบการบริหารการขับเคลื่อน <strong>Soft Power</strong> เพื่อพัฒนาอัตลักษณ์ของนักเรียน โดยเชื่อมโยง <em>“ทุนทางประวัติศาสตร์และวัฒนธรรม”</em> อันโดดเด่นของท้องถิ่น โดยเฉพาะเรื่องราวความกล้าหาญของ <strong>พระยาพิชัยดาบหัก</strong> ภูมิปัญญาท้องถิ่น และหลักปรัชญาของเศรษฐกิจพอเพียง
                    </p>
                    <p>
                        กระบวนการเรียนรู้มุ่งเน้นให้นักเรียน <strong>“รู้คุณค่า – เกิดความภาคภูมิใจ – ลงมือปฏิบัติ – สร้างสรรค์ – ถ่ายทอด”</strong> ก้าวจากการเป็นเพียงผู้รับความรู้ สู่การเป็น <strong>ผู้สร้างสรรค์ (Creator)</strong> และ <strong>ผู้ถ่ายทอด (Communicator)</strong> นำทุนทางวัฒนธรรมไปต่อยอดเป็นผลงานและนวัตกรรมที่มีคุณค่า
                    </p>
                </div>

                <!-- 4 Core Identity Attributes (อัตลักษณ์ลูกพิชัย) -->
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <i class="fas fa-id-badge text-indigo-500"></i>
                        อัตลักษณ์ 4 ด้านของ “ลูกพิชัย” (40 พฤติกรรมบ่งชี้)
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/40 text-center">
                            <div class="w-9 h-9 mx-auto rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow">
                                1
                            </div>
                            <h4 class="font-bold text-sm text-amber-900 dark:text-amber-200">ความพอเพียง</h4>
                            <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-1">ประหยัด คุ้มค่า มีเหตุผล พึ่งพาตนเอง</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-900/40 text-center">
                            <div class="w-9 h-9 mx-auto rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow">
                                2
                            </div>
                            <h4 class="font-bold text-sm text-rose-900 dark:text-rose-200">ความกล้าหาญ</h4>
                            <p class="text-[11px] text-rose-700 dark:text-rose-300 mt-1">กล้าคิด กล้าทำสิ่งที่ถูกต้อง รับผิดชอบ</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-900/40 text-center">
                            <div class="w-9 h-9 mx-auto rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow">
                                3
                            </div>
                            <h4 class="font-bold text-sm text-emerald-900 dark:text-emerald-200">ความเสียสละ</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-300 mt-1">จิตสาธารณะ ช่วยเหลือ อุทิศเพื่อส่วนรวม</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200/60 dark:border-sky-900/40 text-center">
                            <div class="w-9 h-9 mx-auto rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow">
                                4
                            </div>
                            <h4 class="font-bold text-sm text-sky-900 dark:text-sky-200">ความกตัญญู</h4>
                            <p class="text-[11px] text-sky-700 dark:text-sky-300 mt-1">รู้คุณ ดูแล ตอบแทนผู้มีพระคุณและแผ่นดิน</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Structure Diagram Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 md:p-8 shadow-xl border border-white/20 dark:border-slate-700/50">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">
                            <i class="fas fa-sitemap"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">แผนภาพรูปแบบการบริหาร Soft Power</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">องค์ประกอบ 5 ด้าน และกระบวนการขับเคลื่อน</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/40 p-2 flex justify-center">
                    <img src="<?php echo $modelImg; ?>" alt="แผนภาพรูปแบบ Soft Power โรงเรียนพิชัย" class="w-full max-w-full h-auto max-h-[460px] object-contain rounded-xl hover:scale-[1.01] transition-transform duration-300">
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Elements of Management (องค์ประกอบสำคัญของรูปแบบ) -->
    <div>
        <div class="flex items-center gap-3 mb-6 px-1">
            <span class="w-2.5 h-8 rounded-full bg-gradient-to-b from-blue-600 to-indigo-600 inline-block"></span>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">5 องค์ประกอบสำคัญของรูปแบบการบริหาร</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">โครงสร้างหลักตามคู่มือการใช้รูปแบบการขับเคลื่อน Soft Power</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 1. Principles -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-blue-500/30">
                            1
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 border border-blue-200/50">
                            Principles
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">หลักการของรูปแบบ</h3>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mb-3">กรอบแนวคิดและปรัชญาพื้นฐาน</p>
                    <ul class="space-y-2 text-xs md:text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-0.5"></i>
                            <span>หลักการบริหารแบบมีส่วนร่วมบนฐานทุนทางวัฒนธรรม</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-0.5"></i>
                            <span>การพัฒนาผู้เรียนแบบองค์รวมผ่านการจัดการเรียนรู้เชิงนวัตกรรม</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-0.5"></i>
                            <span>การบริหารคุณภาพเพื่อการพัฒนาอย่างต่อเนื่องและยั่งยืน</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 2. Objectives -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-emerald-500/30">
                            2
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 border border-emerald-200/50">
                            Objectives
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">จุดมุ่งหมายของรูปแบบ</h3>
                    <p class="text-xs text-teal-600 dark:text-teal-400 font-semibold mb-3">เป้าหมายครอบคลุม K-P-A</p>
                    <ul class="space-y-2 text-xs md:text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-bullseye text-emerald-500 mt-0.5"></i>
                            <span>พัฒนาคุณธรรมอัตลักษณ์ด้านความพอเพียง</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-bullseye text-emerald-500 mt-0.5"></i>
                            <span>พัฒนาคุณธรรมอัตลักษณ์ด้านความกล้าหาญ</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-bullseye text-emerald-500 mt-0.5"></i>
                            <span>พัฒนาคุณธรรมอัตลักษณ์ด้านความเสียสละ</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-bullseye text-emerald-500 mt-0.5"></i>
                            <span>พัฒนาคุณธรรมอัตลักษณ์ด้านความกตัญญู</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 3. Content -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-purple-500/30">
                            3
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 border border-purple-200/50">
                            Content
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">เนื้อหาของรูปแบบ</h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold mb-3">5 ด้านการขับเคลื่อน</p>
                    <ul class="space-y-2 text-xs md:text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-cube text-purple-500 mt-0.5"></i>
                            <span><strong>3.1 ด้านการบริหารสถานศึกษา:</strong> นโยบาย ทรัพยากร ระบบกำกับติดตาม</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-cube text-purple-500 mt-0.5"></i>
                            <span><strong>3.2 ด้านคุณภาพครู:</strong> การอบรม PLC นวัตกรรมและสื่อดิจิทัล</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-cube text-purple-500 mt-0.5"></i>
                            <span><strong>3.3 ด้านคุณภาพการจัดการเรียนรู้:</strong> Active Learning ฐานชุมชน</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-cube text-purple-500 mt-0.5"></i>
                            <span><strong>3.4 ด้านคุณภาพผู้เรียน:</strong> สมรรถนะและการสร้างสรรค์ผลงาน</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-cube text-purple-500 mt-0.5"></i>
                            <span><strong>3.5 ด้านเครือข่ายความร่วมมือ:</strong> ชุมชน ผู้ปกครอง ปราชญ์ท้องถิ่น</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4. Process (PECEC) -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 md:col-span-2 lg:col-span-2">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-pink-500/30">
                            4
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-300 border border-pink-200/50">
                            Management Process (PECEC)
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">กระบวนการบริหารรูปแบบ 5 ขั้นตอน</h3>
                    <p class="text-xs text-pink-600 dark:text-pink-400 font-semibold mb-3">วงจรคุณภาพเพื่อการพัฒนาอย่างต่อเนื่อง (Continuous Feedback Loop)</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 mt-4">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs inline-flex items-center justify-center mb-1">P</span>
                            <h4 class="font-bold text-xs text-gray-800 dark:text-gray-200">Planning</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">วางแผน กำหนดเป้าหมาย และสำรวจทุนวัฒนธรรม</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs inline-flex items-center justify-center mb-1">E</span>
                            <h4 class="font-bold text-xs text-gray-800 dark:text-gray-200">Execution</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">ลงมือปฏิบัติ บูรณาการหลักสูตร และผลิตสื่อ</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="w-7 h-7 rounded-full bg-amber-600 text-white font-bold text-xs inline-flex items-center justify-center mb-1">C</span>
                            <h4 class="font-bold text-xs text-gray-800 dark:text-gray-200">Control</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">กำกับติดตาม นิเทศภายในแบบกัลยาณมิตร</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="w-7 h-7 rounded-full bg-purple-600 text-white font-bold text-xs inline-flex items-center justify-center mb-1">E</span>
                            <h4 class="font-bold text-xs text-gray-800 dark:text-gray-200">Evaluation</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">ประเมิน 4 ระดับ (กระบวนการ/ผลผลิต/ผลลัพธ์/ผลกระทบ)</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="w-7 h-7 rounded-full bg-rose-600 text-white font-bold text-xs inline-flex items-center justify-center mb-1">C</span>
                            <h4 class="font-bold text-xs text-gray-800 dark:text-gray-200">Continuous</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">ถอดบทเรียน Best Practice สู่วัฒนธรรมองค์กร</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Outputs & Outcomes -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-amber-500/30">
                            5
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 border border-amber-200/50">
                            Outcomes
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">ผลลัพธ์จากการบริหาร</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mb-3">การเปลี่ยนแปลงเชิงคุณภาพ</p>
                    <ul class="space-y-2 text-xs md:text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-trophy text-amber-500 mt-0.5"></i>
                            <span>ผู้เรียนมีอัตลักษณ์ 4 ด้าน ชัดเจน มีความภาคภูมิใจในท้องถิ่น</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-trophy text-amber-500 mt-0.5"></i>
                            <span>ครูมีนวัตกรรมการจัดการเรียนรู้เชิงรุก (Active Learning)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-trophy text-amber-500 mt-0.5"></i>
                            <span>โรงเรียนเป็นศูนย์กลางและต้นแบบการขับเคลื่อน Soft Power</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Instruments & Assessment Section (ส่วนที่ 4 สื่อและเครื่องมือ) -->
    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 top-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold tracking-wider uppercase border border-blue-400/20 mb-2 inline-block">
                        ส่วนที่ 4 ของคู่มือ
                    </span>
                    <h3 class="text-2xl md:text-3xl font-bold">สื่อและเครื่องมือประกอบการพัฒนา</h3>
                    <p class="text-gray-300 text-sm mt-1">เครื่องมือวัดและประเมินผลมาตรฐานตามรูปแบบการบริหาร</p>
                </div>
                <a href="<?php echo $docPath; ?>" download="คู่มือการใช้รูปแบบ_ผอ_รสสุคนธ์_อินชัยเขา.pdf" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-slate-900 font-bold text-sm hover:bg-slate-100 transition shadow-lg active:scale-95 whitespace-nowrap">
                    <i class="fas fa-download text-indigo-600"></i> ดาวน์โหลดแบบประเมินในคู่มือ
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tool 1 -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-300 text-lg">
                            <i class="fas fa-clipboard-user"></i>
                        </div>
                        <h4 class="font-bold text-lg">1. แบบประเมินอัตลักษณ์ของนักเรียน (40 พฤติกรรมบ่งชี้)</h4>
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed mb-4">
                        เครื่องมือประเมินระดับพฤติกรรมของนักเรียน 5 ระดับ (ดีเยี่ยม - ดีมาก - ดี - พอใช้ - ปรับปรุง) ครอบคลุมความพอเพียง 10 ข้อ, ความกล้าหาญ 10 ข้อ, ความเสียสละ 10 ข้อ และความกตัญญู 10 ข้อ
                    </p>
                    <div class="flex items-center gap-2 text-xs text-blue-200">
                        <i class="fas fa-check-circle"></i> ใช้สำหรับการประเมินก่อน-หลัง และสะท้อนผลการเรียนรู้
                    </div>
                </div>

                <!-- Tool 2 -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-300 text-lg">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                        <h4 class="font-bold text-lg">2. แบบประเมินการจัดกิจกรรมการเรียนรู้ (20 รายการประเมิน)</h4>
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed mb-4">
                        เครื่องมือนิเทศและประเมินคุณภาพการจัดการเรียนรู้ของครูผู้สอน การออกแบบแผนการเรียนรู้บูรณาการ Soft Power, การจัดแบบ Active Learning และการประเมินตามสภาพจริง
                    </p>
                    <div class="flex items-center gap-2 text-xs text-indigo-200">
                        <i class="fas fa-check-circle"></i> ใช้ประกอบกระบวนการนิเทศภายในและชุมชน PLC
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$layoutPath = $isSubfolder ? __DIR__ . '/../layouts/teacher_app.php' : __DIR__ . '/../layouts/app.php';
include $layoutPath;
?>
