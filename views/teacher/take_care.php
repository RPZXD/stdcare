<?php
/**
 * Teacher Take Care View - MVC Pattern
 * Modern UI for Student Care System with Tailwind CSS
 */
ob_start();
?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="mb-12 text-center fade-in-up">
        <h1 class="text-4xl md:text-5xl font-black text-slate-800 dark:text-white mb-4">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">
                ระบบดูแลช่วยเหลือนักเรียน 5 ขั้นตอน
            </span>
        </h1>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed">
            โรงเรียนพิชัยได้ดำเนินการตามระบบการดูแลช่วยเหลือนักเรียนโดยยึดหลัก <span class="font-bold text-indigo-600 dark:text-indigo-400">5 ใจ 1 G</span> เพื่อการพัฒนาศักยภาพนักเรียนอย่างรอบด้าน
        </p>
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

    <!-- Additional Info Section -->
    <div class="mt-16 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-[3rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-500/30 fade-in-up" style="animation-delay: 0.7s">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="w-32 h-32 md:w-48 md:h-48 bg-white/10 backdrop-blur-xl rounded-full flex items-center justify-center border border-white/20">
                <i class="fas fa-info-circle text-6xl md:text-8xl text-white/80"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-black mb-4">ข้อมูลเพิ่มเติมเกี่ยวกับการดูแลนักเรียน</h2>
                <p class="text-indigo-100 text-lg mb-8 leading-relaxed">
                    ระบบการดูแลช่วยเหลือนักเรียนเป็นกระบวนการดำเนินงานที่มีขั้นตอนชัดเจน มีเครื่องมือและวิธีการที่เหมาะสม โดยความร่วมมือระหว่างครู ผู้ปกครอง และบุคลากรที่เกี่ยวข้อง เพื่อเสริมสร้างทักษะชีวิตและป้องกันปัญหาที่อาจเกิดขึ้นกับนักเรียน
                </p>
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <button class="px-8 py-3 bg-white text-indigo-700 font-bold rounded-2xl hover:bg-indigo-50 transition-colors flex items-center gap-2">
                        <i class="fas fa-book-open"></i> อ่านคู่มือการใช้งาน
                    </button>
                    <button class="px-8 py-3 bg-indigo-500/30 text-white font-bold rounded-2xl border border-white/20 hover:bg-indigo-500/50 transition-colors flex items-center gap-2">
                        <i class="fas fa-question-circle"></i> สอบถามการใช้งาน
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
