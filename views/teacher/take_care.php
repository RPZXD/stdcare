<?php
/**
 * Teacher Take Care View - MVC Pattern
 * Modern UI for Student Care System with Tailwind CSS
 */
ob_start();
?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="mb-10 text-center fade-in-up">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold text-sm mb-3">
            <i class="fas fa-project-diagram"></i>
            <span>PHICHAI STUDENT CARE SYSTEM</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-slate-800 dark:text-white mb-4">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:to-indigo-400">
                ระบบดูแลช่วยเหลือนักเรียน 5 ขั้นตอน
            </span>
        </h1>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed">
            โรงเรียนพิชัยได้ดำเนินการตามระบบการดูแลช่วยเหลือนักเรียนโดยยึดหลัก <span class="font-bold text-indigo-600 dark:text-indigo-400">5 ใจ 1-G MODEL</span> เพื่อการพัฒนาศักยภาพนักเรียนอย่างรอบด้าน
        </p>
    </div>

    <!-- 5 ใจ 1-G MODEL Showcase Card -->
    <div class="mb-12 bg-white dark:bg-slate-800 rounded-[2.5rem] p-6 md:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 fade-in-up" style="animation-delay: 0.1s">
        <div class="flex flex-col lg:flex-row items-center gap-8">
            <!-- Image with zoom overlay -->
            <div class="w-full lg:w-3/5 flex flex-col items-center">
                <div class="relative group cursor-pointer overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-md transition-all duration-300 hover:shadow-xl w-full flex justify-center bg-slate-50 dark:bg-slate-900/50" onclick="openModelModal()">
                    <img src="../dist/img/5jai-1g-model.png" alt="5 ใจ 1-G MODEL โรงเรียนพิชัย" class="w-full max-w-2xl h-auto object-contain transition-transform duration-500 group-hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="px-4 py-2 bg-white/90 dark:bg-slate-800/90 backdrop-blur text-slate-800 dark:text-white rounded-full text-sm font-semibold shadow flex items-center gap-2">
                            <i class="fas fa-search-plus text-indigo-600"></i> คลิกเพื่อดูภาพขยาย
                        </span>
                    </div>
                </div>
                <button type="button" onclick="openModelModal()" class="mt-3 text-xs md:text-sm text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1.5 font-medium">
                    <i class="fas fa-expand-alt"></i> เปิดดูรูปภาพ 5 ใจ 1-G MODEL ขนาดเต็ม
                </button>
            </div>

            <!-- Description / Highlights of the Model -->
            <div class="w-full lg:w-2/5 flex flex-col justify-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-3 w-fit">
                    <i class="fas fa-sitemap"></i> โมเดลการขับเคลื่อนคุณภาพ
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-4">
                    5 ใจ 1-G MODEL
                </h2>
                <p class="text-sm md:text-base text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                    กรอบแนวทางการดำเนินงานระบบการดูแลช่วยเหลือนักเรียนของโรงเรียนพิชัยที่เชื่อมโยงนโยบายสู่การปฏิบัติอย่างเป็นรูปธรรม
                </p>

                <div class="space-y-3.5">
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900/30">
                        <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            IN
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wide">INPUT &bull; ปัจจัยนำเข้า</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">นโยบาย สพฐ. / นโยบาย สพม. พิษณุโลก อุตรดิตถ์ / นโยบาย วิสัยทัศน์ พันธกิจ คุณธรรมอัตลักษณ์ รร.พิชัย</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            5 ใจ
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wide">PROCESS &bull; กระบวนการ 5 ใจ</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">ใส่ใจ &bull; เข้าใจ &bull; พร้อมใจ &bull; เชื่อใจ &bull; มั่นใจ</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/30">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-sm">
                            1-G
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wide">OUTPUT &bull; ผลลัพธ์</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5"><span class="font-bold text-emerald-700 dark:text-emerald-400">GOOD STUDENT</span> พัฒนาผู้เรียนให้เป็นคนดี มีคุณธรรม และศักยภาพรอบด้าน</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Step 1: ใส่ใจ -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-red-200 dark:hover:border-red-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-red-50 dark:bg-red-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-heart text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">ขั้นตอนที่ 1 ใส่ใจ</h3>
                <p class="text-sm font-medium text-red-500 uppercase tracking-widest mb-6">รู้รอบกรอบบุคคล</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="data_student.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-user"></i></span>
                            <span class="font-semibold text-sm">ข้อมูลนักเรียนรายบุคคล</span>
                        </a>
                    </li>
                    <li>
                        <a href="visithome.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-home"></i></span>
                            <span class="font-semibold text-sm">ข้อมูลการเยี่ยมบ้านนักเรียน</span>
                        </a>
                    </li>
                    <li>
                        <a href="poor.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-hand-holding-usd"></i></span>
                            <span class="font-semibold text-sm">ข้อมูลนักเรียนยากจน</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://student.phichai.ac.th/teacher/stucare14.pdf" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-file-pdf"></i></span>
                            <span class="font-semibold text-sm">โหลดแบบเยี่ยมบ้านนักเรียน</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 2: เข้าใจ -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.2s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-brain text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">ขั้นตอนที่ 2 เข้าใจ</h3>
                <p class="text-sm font-medium text-blue-500 uppercase tracking-widest mb-6">กรองกมลบูรณาการ</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="sdq.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-chart-bar"></i></span>
                            <span class="font-semibold text-sm">แบบประเมิน SDQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="eq.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-smile"></i></span>
                            <span class="font-semibold text-sm">แบบประเมิน EQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="screen11.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-search"></i></span>
                            <span class="font-semibold text-sm">คัดกรอง 11 ด้าน</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-eye"></i></span>
                            <span class="font-semibold text-sm">คัดกรองเชิงประจักษ์</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 3: พร้อมใจ -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-amber-200 dark:hover:border-amber-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.3s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-amber-50 dark:bg-amber-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">ขั้นตอนที่ 3 พร้อมใจ</h3>
                <p class="text-sm font-medium text-amber-600 uppercase tracking-widest mb-6">ประสานเสริมให้พัฒนา</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="home_room.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-school"></i></span>
                            <span class="font-semibold text-sm">กิจกรรมโฮมรูมประจำวัน</span>
                        </a>
                    </li>
                    <li>
                        <a href="board_parent.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-users-cog"></i></span>
                            <span class="font-semibold text-sm">เครือข่ายผู้ปกครอง</span>
                        </a>
                    </li>
                    <li>
                        <a href="wroom.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-door-open"></i></span>
                            <span class="font-semibold text-sm">ห้องเรียนสีขาว</span>
                        </a>
                    </li>
                    <li>
                        <a href="picture_meeting.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-camera"></i></span>
                            <span class="font-semibold text-sm">ภาพประชุมผู้ปกครอง</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 4: เชื่อใจ -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-purple-200 dark:hover:border-purple-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.4s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-purple-50 dark:bg-purple-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-fuchsia-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">ขั้นตอนที่ 4 เชื่อใจ</h3>
                <p class="text-sm font-medium text-purple-500 uppercase tracking-widest mb-6">คลายปัญหาเป็นระบบ</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="https://student.phichai.ac.th/teacher/stucare41.pdf" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-file-alt"></i></span>
                            <span class="font-semibold text-sm">แบบบันทึกการดูแลรายบุคคล</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://student.phichai.ac.th/teacher/stucare42.pdf" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-file-contract"></i></span>
                            <span class="font-semibold text-sm">แบบสรุปผลการป้องกัน/แก้ไข</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 5: มั่นใจ -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-emerald-200 dark:hover:border-emerald-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.5s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">ขั้นตอนที่ 5 มั่นใจ</h3>
                <p class="text-sm font-medium text-emerald-500 uppercase tracking-widest mb-6">เมื่อพานพบรีบส่งต่อ</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="https://student.phichai.ac.th/teacher/stucare51.pdf" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-share-square"></i></span>
                            <span class="font-semibold text-sm">แบบบันทึกการส่งต่อนักเรียน</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://student.phichai.ac.th/teacher/stucare52.pdf" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-clipboard-check"></i></span>
                            <span class="font-semibold text-sm">แบบสรุปผลการส่งต่อ</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Behavior Stats -->
        <div class="group relative bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-900/30 transition-all duration-500 hover:-translate-y-2 fade-in-up" style="animation-delay: 0.6s">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full flex items-center justify-center -z-0 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">คะแนนพฤติกรรม</h3>
                <p class="text-sm font-medium text-indigo-500 uppercase tracking-widest mb-6">การส่งเสริมระเบียบวินัย</p>
                
                <ul class="space-y-4">
                    <li>
                        <a href="behavior.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-exclamation-triangle"></i></span>
                            <span class="font-semibold text-sm">บันทึกคะแนนความผิด</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-slate-700 dark:text-slate-300 transition-colors group/link">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center group-hover/link:scale-110 transition-transform"><i class="fas fa-thumbs-up"></i></span>
                            <span class="font-semibold text-sm">บันทึกคะแนนความดี</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Detailed Guidelines Section: 5 ใจ 1-G MODEL -->
    <div class="mt-16 space-y-8 fade-in-up" style="animation-delay: 0.7s">
        <!-- Section Header & Management Structure -->
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700">
            <div class="max-w-4xl mx-auto text-center mb-8">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-3">
                    <i class="fas fa-clipboard-list"></i> แนวทางการดำเนินงาน
                </span>
                <h2 class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white mb-4">
                    แนวทางการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-base md:text-lg leading-relaxed text-justify md:text-center">
                    โรงเรียนพิชัยมีการแต่งตั้งคณะกรรมการอำนวยการ (ทีมนำ) คณะกรรมการประสานงาน (ทีมประสาน) คณะกรรมการดำเนินการ (ทีมทำ) ในการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน มีปฏิทินปฏิบัติงานระบบการดูแลช่วยเหลือนักเรียนแนวทางการดำเนินงาน ครูและบุคลากรมีความรู้ความเข้าใจในวิธีการกระบวนการและขั้นตอนดำเนินงานระบบการดูแลช่วยเหลือนักเรียนในสถานศึกษาทั้ง 5 ขั้นตอนภายใต้หลัก <span class="font-bold text-indigo-600 dark:text-indigo-400">5 ใจ - 1 G โมเดล</span> โดยมีการดำเนินงานสอดคล้องเป็นระบบ ดังนี้
                </p>
            </div>

            <!-- Team Roles (ทีมนำ / ทีมประสาน / ทีมทำ) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-5xl mx-auto pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                        <i class="fas fa-chess-king"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">บทบาทที่ 1</div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมนำ</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการอำนวยการ กำหนดนโยบาย ทิศทาง และสนับสนุนการดำเนินงาน</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">บทบาทที่ 2</div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมประสาน</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการประสานงาน เชื่อมโยงข้อมูล บุคลากร ฝ่ายต่างๆ และหน่วยงานภายนอก</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/50 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 font-black text-lg">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">บทบาทที่ 3</div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">ทีมทำ</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">คณะกรรมการดำเนินการ ระดับสายชั้น (หัวหน้าระดับ, รองหัวหน้าระดับ, เลขานุการ, ครูที่ปรึกษา)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 Steps Detailed Explanation -->
        <div class="space-y-6">
            <!-- Step 1: การรู้จักนักเรียนเป็นรายบุคคล (ใส่ใจ) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-lg shadow-slate-200/40 dark:shadow-none border-l-8 border-l-red-500 border border-slate-100 dark:border-slate-700">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center font-black">
                            1
                        </span>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white">1. การรู้จักนักเรียนเป็นรายบุคคล (ใส่ใจ)</h3>
                            <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">รู้รอบกรอบบุคคล</span>
                        </div>
                    </div>
                    <a href="https://std.phichai.ac.th/" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 hover:bg-red-100 text-xs font-bold transition-colors w-fit">
                        <i class="fas fa-globe"></i> https://std.phichai.ac.th/
                    </a>
                </div>
                <div class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed pl-0 md:pl-13 text-justify">
                    <p>
                        โรงเรียนได้สร้างระบบโปรแกรมออนไลน์ STDCARE <a href="https://std.phichai.ac.th/" target="_blank" class="text-red-600 dark:text-red-400 font-semibold underline underline-offset-2">https://std.phichai.ac.th/</a> เพื่อเก็บข้อมูลนักเรียนเป็นรายบุคคลข้อมูลพื้นฐานของนักเรียน สามารถนำข้อมูลมาวิเคราะห์ เพื่อการคัดกรองนักเรียน เป็นประโยชน์ในการส่งเสริม การป้องกันและแก้ไขปัญหานักเรียนได้อย่างทั่วถึง ถ้าหากนักเรียนมีปัญหาจะได้ดำเนินการช่วยเหลือได้ทันเวลา
                    </p>
                </div>
            </div>

            <!-- Step 2: การคัดกรองนักเรียน (เข้าใจ) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-lg shadow-slate-200/40 dark:shadow-none border-l-8 border-l-blue-500 border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center font-black">
                        2
                    </span>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">2. การคัดกรองนักเรียน (เข้าใจ)</h3>
                        <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">กรองกมลบูรณาการ 11 ด้าน</span>
                    </div>
                </div>

                <div class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed space-y-4 text-justify">
                    <p>
                        การคัดกรองนักเรียนด้วยการบูรณาการ (เข้าใจ) มีการคัดกรองนักเรียน ด้วยโปรแกรมระบบการดูแลช่วยเหลือนักเรียน STDCARE มีข้อมูลในการวางแผนดูแลช่วยเหลือนักเรียนทั้งสิ้น 11 ด้าน การคัดกรองนักเรียนเป็นการพิจารณาข้อมูลที่เกี่ยวกับตัวนักเรียนเพื่อการจัดกลุ่มนักเรียน อาจนิยามกลุ่มได้ 4 กลุ่ม คือ
                    </p>

                    <!-- 4 Groups Definition Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40">
                            <div class="flex items-center gap-2 font-bold text-emerald-800 dark:text-emerald-300 mb-1.5 text-sm">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                กลุ่มปกติ
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                คือ นักเรียนที่ได้รับการวิเคราะห์ข้อมูลต่าง ๆ ตามเกณฑ์การคัดกรองของโรงเรียนแล้วอยู่ในเกณฑ์ของกลุ่มปกติ ซึ่งควรได้รับการสร้างเสริมภูมิคุ้มกันและการส่งเสริมพัฒนา
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40">
                            <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300 mb-1.5 text-sm">
                                <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                                กลุ่มเสี่ยง
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                คือ นักเรียนที่จัดอยู่ในเกณฑ์ของกลุ่มเสี่ยงตามเกณฑ์การคัดกรองของโรงเรียนซึ่งโรงเรียนต้องให้การป้องกันหรือแก้ไขปัญหาตามแต่กรณี
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40">
                            <div class="flex items-center gap-2 font-bold text-rose-800 dark:text-rose-300 mb-1.5 text-sm">
                                <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                                กลุ่มมีปัญหา
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                คือ นักเรียนที่จัดอยู่ในเกณฑ์ของกลุ่มมีปัญหาตามเกณฑ์การคัดกรองของโรงเรียนซึ่งโรงเรียนต้องช่วยเหลือและแก้ปัญหาโดยเร่งด่วน
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40">
                            <div class="flex items-center gap-2 font-bold text-purple-800 dark:text-purple-300 mb-1.5 text-sm">
                                <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                                กลุ่มพิเศษ
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                คือ นักเรียนที่มีความสามารถพิเศษ มีความเป็นอัจฉริยะ แสดงออกซึ่งความสามารถอันโดดเด่นด้านใดด้านหนึ่งหรือหลายด้าน อย่างเป็นที่ประจักษ์เมื่อเทียบกับผู้มีอายุในระดับเดียวกันภายใต้สภาพแวดล้อมเดียวกัน ซึ่งโรงเรียนต้องให้การส่งเสริมนักเรียนได้พัฒนาศักยภาพความสามารถพิเศษนั้นจนถึงขั้นสูงสุด
                            </p>
                        </div>
                    </div>

                    <p class="pt-2 text-xs md:text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                        <i class="fas fa-info-circle text-blue-500 mr-1.5"></i>
                        <strong>ประโยชน์ของการจัดกลุ่ม:</strong> การจัดกลุ่มนักเรียนนี้มีประโยชน์ต่อครูที่ปรึกษาในการหาวิธีการเพื่อดูแลช่วยเหลือนักเรียนได้อย่างถูกต้อง โดยเฉพาะการแก้ไขปัญหาให้ตรงกับปัญหาของนักเรียนยิ่งขึ้น และมีความรวดเร็วในการแก้ไขปัญหาเพราะมีข้อมูลของนักเรียนในด้านต่าง ๆ ซึ่งหากครูที่ปรึกษาไม่ได้คัดกรองนักเรียนเพื่อการจัดกลุ่มแล้ว ความชัดเจนในเป้าหมายเพื่อการแก้ไขปัญหาของนักเรียนจะมีน้อยลง มีผลต่อความรวดเร็วในการช่วยเหลือ ซึ่งบางกรณีจำเป็นต้องแก้ไขโดยเร่งด่วน
                    </p>
                </div>
            </div>

            <!-- Step 3: การส่งเสริม และพัฒนานักเรียน / ป้องกันและแก้ไขปัญหา (พร้อมใจ) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-lg shadow-slate-200/40 dark:shadow-none border-l-8 border-l-amber-500 border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center font-black">
                        3
                    </span>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">3. การป้องกันและแก้ไขปัญหา (พร้อมใจ)</h3>
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">ประสานเสริมให้พัฒนา</span>
                    </div>
                </div>

                <div class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed space-y-3.5 text-justify">
                    <p>
                        การส่งเสริม และพัฒนานักเรียน (พร้อมใจ) นำข้อมูลในการวางแผนการจัดกิจกรรมที่ตอบสนองต่อความต้องการดูแลช่วยเหลือได้อย่างเหมาะสมกับกลุ่มปกติ กลุ่มเสี่ยง และกลุ่มมีปัญหา โดยในกลุ่มปกติ สามารถนำข้อมูลวางแนวทางจัดกิจกรรมที่จะส่งเสริมคุณลักษณะด้านต่างๆ ตามความถนัดและความสนใจของนักเรียน
                    </p>
                    <p>
                        ในการดูแลช่วยเหลือนักเรียน ครูควรให้ความเอาใจใส่กับนักเรียนทุกคนอย่างเท่าเทียมกันแต่สำหรับนักเรียนกลุ่มเสี่ยง/มีปัญหานั้น จำเป็นอย่างมากที่ต้องให้ความดูแลเอาใจใส่อย่างใกล้ชิดและหาวิธีการช่วยเหลือทั้งการป้องกันและการแก้ไขปัญหา โดยไม่ปล่อยปละละเลยนักเรียนจนกลายเป็นปัญหาของสังคม การสร้างภูมิคุ้มกัน การป้องกันและแก้ไขปัญหาของนักเรียน จึงเป็นภาระงานที่ยิ่งใหญ่และมีคุณค่าอย่างมากในการพัฒนาให้นักเรียนเติบโตเป็นบุคคลที่มีคุณภาพของสังคมต่อไป
                    </p>
                    
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-800/40">
                        <h4 class="font-bold text-amber-800 dark:text-amber-300 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> สิ่งที่ครูประจำชั้น/ครูที่ปรึกษาจำเป็นต้องดำเนินการอย่างน้อย 2 ประการ:
                        </h4>
                        <ul class="space-y-1.5 text-xs md:text-sm text-slate-700 dark:text-slate-300 pl-4 list-disc">
                            <li><strong>1. การให้คำปรึกษาเบื้องต้น</strong></li>
                            <li><strong>2. การจัดกิจกรรมเพื่อป้องกันและแก้ไขปัญหา</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4: การป้องกันและแก้ไขปัญหาอย่างเป็นระบบ (เชื่อใจ) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-lg shadow-slate-200/40 dark:shadow-none border-l-8 border-l-purple-500 border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center font-black">
                        4
                    </span>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">4. การป้องกันและแก้ไขปัญหาอย่างเป็นระบบ (เชื่อใจ)</h3>
                        <span class="text-xs font-semibold text-purple-500 uppercase tracking-wider">คลายปัญหาเป็นระบบ</span>
                    </div>
                </div>

                <div class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed text-justify">
                    <p>
                        มีคำสั่งแต่งตั้งคณะทำงานเกี่ยวกับระบบการดูแลช่วยเหลือนักเรียน การเสริมสร้างทักษะชีวิตและการคุ้มครองนักเรียนในแต่ละปีการศึกษา แต่งตั้งครูและบุคลากรในตำแหน่งต่างๆ แบ่งการทำงานเป็นระดับสายชั้น ประกอบด้วยหัวหน้าระดับ รองหัวหน้าระดับ เลขานุการ และครูที่ปรึกษา โดยมีทีมนำ ทีมประสานและทีมทำ เพื่อให้มีการขับเคลื่อนงานระบบการดูแลช่วยเหลือนักเรียนได้อย่างมีประสิทธิภาพ มีความทั่วถึงและต่อเนื่อง โรงเรียนมีการดำเนินการดูแลช่วยเหลือนักเรียนในด้านต่างๆ อย่างเป็นระบบ
                    </p>
                </div>
            </div>

            <!-- Step 5: การส่งต่อนักเรียนอย่างมีคุณภาพ (มั่นใจ) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-lg shadow-slate-200/40 dark:shadow-none border-l-8 border-l-emerald-500 border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center font-black">
                        5
                    </span>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">5. การส่งต่อนักเรียนอย่างมีคุณภาพ (มั่นใจ)</h3>
                        <span class="text-xs font-semibold text-emerald-500 uppercase tracking-wider">เมื่อพานพบรีบส่งต่อ</span>
                    </div>
                </div>

                <div class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed space-y-4 text-justify">
                    <p>
                        มีการดำเนินการส่งต่อให้ผู้เชี่ยวชาญเฉพาะด้าน โดยการส่งต่อนักเรียนแบ่งเป็น 2 กรณี คือ:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-700/50">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1.5 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center text-xs"><i class="fas fa-arrow-right"></i></span>
                                การส่งต่อภายใน
                            </h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                ครูที่ปรึกษาส่งต่อไปยังครูที่สามารถให้การช่วยเหลือนักเรียนได้ ทั้งนี้ขึ้นอยู่กับลักษณะปัญหา
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-700/50">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1.5 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center text-xs"><i class="fas fa-external-link-alt"></i></span>
                                การส่งต่อภายนอก
                            </h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                ครูแนะแนวหรือฝ่ายกิจการนักเรียนเป็นผู้ดำเนินการส่งต่อไปยังผู้เชี่ยวชาญภายนอกในการส่งต่อนักเรียนส่วนใหญ่ของโรงเรียนพิชัย
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- การพัฒนาและส่งเสริมผู้เรียน & กิจกรรมหลัก 4 กิจกรรม -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800 dark:to-slate-800/80 rounded-[2.5rem] p-8 md:p-10 shadow-lg shadow-indigo-100/50 dark:shadow-none border border-indigo-100/80 dark:border-slate-700">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-500/30">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div>
                        <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white">การพัฒนาและส่งเสริมผู้เรียน</h3>
                        <p class="text-xs md:text-sm text-indigo-600 dark:text-indigo-400 font-medium">การสนับสนุนผู้เรียนให้พัฒนาเต็มศักยภาพอย่างต่อเนื่อง</p>
                    </div>
                </div>

                <p class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed text-justify mb-6">
                    การพัฒนาและส่งเสริมนักเรียนเป็นการสนับสนุนให้นักเรียนทุกคน ไม่ว่าจะเป็นนักเรียนกลุ่มปกติหรือกลุ่มเสี่ยง/มีปัญหา กลุ่มความสามารถพิเศษ ให้มีคุณภาพมากขึ้น ได้พัฒนาเต็มศักยภาพมีความภาคภูมิใจในตนเองในด้านต่าง ๆ ซึ่งจะช่วยป้องกันมิให้นักเรียนที่อยู่ในกลุ่มปกติและกลุ่มพิเศษกลายเป็นนักเรียนกลุ่มเสี่ยง/มีปัญหา และเป็นการช่วยให้นักเรียนกลุ่มเสี่ยง/มีปัญหากลับมาเป็นนักเรียนกลุ่มปกติและมีคุณภาพตามมาตรฐานที่โรงเรียนหรือชุมชนคาดหวังต่อไป
                </p>

                <div class="bg-white dark:bg-slate-900/50 rounded-2xl p-5 md:p-6 border border-indigo-100 dark:border-slate-700/60 mb-8">
                    <h4 class="font-bold text-slate-800 dark:text-white text-sm md:text-base mb-4 flex items-center gap-2">
                        <i class="fas fa-tasks text-indigo-600 dark:text-indigo-400"></i>
                        กิจกรรมหลักสำคัญที่โรงเรียนต้องดำเนินการ (4 กิจกรรม):
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">1</span>
                            <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">การจัดกิจกรรมโฮมรูม</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">2</span>
                            <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">การเยี่ยมบ้าน</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">3</span>
                            <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">การจัดประชุมผู้ปกครองชั้นเรียน (Classroom Meeting)</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">4</span>
                            <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">การจัดกิจกรรมเสริมสร้างทักษะการดำรงชีวิตและกิจกรรมพัฒนาผู้เรียน</span>
                        </div>
                    </div>
                </div>

                <!-- Good Student (1G) Result Card -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-emerald-600/20">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="w-20 h-20 md:w-24 md:h-24 bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center border-2 border-white/30 flex-shrink-0">
                            <i class="fas fa-heart text-4xl md:text-5xl text-white"></i>
                        </div>
                        <div class="text-center md:text-left flex-1">
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white font-bold text-xs uppercase tracking-wider mb-2 inline-block">
                                OUTPUT &bull; ผลผลิตของโมเดล
                            </span>
                            <h3 class="text-2xl md:text-3xl font-black mb-3">
                                GOOD STUDENT (1G)
                            </h3>
                            <p class="text-emerald-50 text-sm md:text-base leading-relaxed mb-4">
                                จากการดำเนินงานทำให้ผู้เรียนมี Good Student (1G) ได้แก่:
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs md:text-sm text-emerald-100">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> มีสุขภาพกาย สุขภาพจิต และสุขลักษณะนิสัยที่ดี
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> มีทักษะในการหลีกเลี่ยง ป้องกันภัยอันตราย และพฤติกรรมที่ไม่พึงประสงค์
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> รักและเห็นคุณค่าในตนเองและผู้อื่น
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> สามารถจัดการกับปัญหาและอารมณ์ของตนเองได้
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> เป็นสมาชิกที่ดีของครอบครัว โรงเรียน ชุมชน และสังคม
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-white"></i> มีเจตคติที่ดี และมีทักษะพื้นฐานในการประกอบอาชีพสุจริต
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Lightbox for 5 ใจ 1-G MODEL Image -->
    <div id="modelImageModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300" onclick="if(event.target === this) closeModelModal()">
        <div class="relative max-w-5xl w-full bg-white dark:bg-slate-800 rounded-3xl p-4 md:p-6 shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class="fas fa-project-diagram text-sm"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-bold text-slate-800 dark:text-white">5 ใจ 1-G MODEL - แผนผังระบบดูแลช่วยเหลือนักเรียน</h3>
                </div>
                <button type="button" onclick="closeModelModal()" class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-2 max-h-[78vh] overflow-auto">
                <img src="../dist/img/5jai-1g-model.png" alt="5 ใจ 1-G MODEL" class="w-full h-auto max-h-[72vh] object-contain rounded-xl shadow-sm">
            </div>
            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span>กรอบการดำเนินงาน: INPUT (3 นโยบาย) &rarr; PROCESS (5 ใจ) &rarr; OUTPUT (GOOD STUDENT)</span>
                <a href="../dist/img/5jai-1g-model.png" target="_blank" download="5jai-1g-model.png" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 font-semibold transition-colors flex items-center gap-1.5">
                    <i class="fas fa-download"></i> ดาวน์โหลดภาพ
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openModelModal() {
    const modal = document.getElementById('modelImageModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeModelModal() {
    const modal = document.getElementById('modelImageModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModelModal();
    }
});
</script>

<style>
.fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
