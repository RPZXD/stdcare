<?php
/**
 * Teacher Behavior View
 * MVC Pattern - Premium Modern UI with Tailwind CSS
 * Mobile-First Design
 */

$pageTitle = $pageTitle ?? 'บันทึกพฤติกรรม';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }

        50% {
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.6);
        }
    }

    .pulse-danger {
        animation: pulse-glow 2s ease-in-out infinite;
    }

    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .btn-action:active {
        transform: scale(0.98);
    }

    .student-card {
        transition: all 0.3s ease;
    }

    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #94a3b8, #64748b);
        border-radius: 10px;
    }

    /* Progress Bar */
    .progress-bar {
        transition: width 0.5s ease-in-out;
    }

    /* Modal */
    .modal-overlay {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden fade-in-up">
    <div
        class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-8 border border-white/40 dark:border-slate-700/50 shadow-2xl relative">
        <!-- Background Orbs -->
        <div
            class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-br from-rose-400/30 to-pink-500/30 rounded-full blur-3xl -z-10">
        </div>
        <div
            class="absolute bottom-0 left-0 w-24 md:w-48 h-24 md:h-48 bg-gradient-to-tr from-red-400/30 to-orange-500/30 rounded-full blur-3xl -z-10">
        </div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <!-- Title -->
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl blur-xl opacity-60">
                    </div>
                    <div
                        class="relative w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-clipboard-list text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-1">
                        📋 บันทึกพฤติกรรม
                    </h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-rose-100 to-pink-100 dark:from-rose-900/40 dark:to-pink-900/40 text-rose-700 dark:text-rose-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-chalkboard-teacher"></i>
                            ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-orange-100 to-amber-100 dark:from-orange-900/40 dark:to-amber-900/40 text-orange-700 dark:text-orange-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-calendar-alt"></i>
                            ภาคเรียน <?php echo htmlspecialchars($term); ?>/<?php echo htmlspecialchars($pee); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                <button onclick="openAddModal()"
                    class="btn-action bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-rose-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-minus-circle"></i>
                    <span>หักคะแนน</span>
                </button>
                <a href="show_behavior.php"
                    class="btn-action bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-blue-500/30 flex items-center justify-center gap-2 no-underline">
                    <i class="fas fa-history"></i>
                    <span>ประวัติการหัก</span>
                </a>
                <button onclick="printReport()"
                    class="btn-action bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-emerald-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4 mb-6">
    <div
        class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div
            class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-users text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600"
            id="totalStudents">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">นักเรียนทั้งหมด
        </p>
    </div>

    <div
        class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div
            class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-rose-400 to-red-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-minus-circle text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-red-600"
            id="totalDeduction">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">รวมหัก</p>
    </div>

    <div
        class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div
            class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-teal-400 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-hand-holding-heart text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-cyan-600"
            id="totalBonus">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">จิตอาสา</p>
    </div>

    <div
        class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div
            class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-amber-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600"
            id="studentWithDeduct">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">ถูกหักคะแนน</p>
    </div>

    <div
        class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center group">
        <div
            class="w-14 h-14 mx-auto mb-3 bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-check-circle text-white text-xl"></i>
        </div>
        <p class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-green-600"
            id="studentGood">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2">ไม่ถูกหัก</p>
    </div>
</div>

<!-- Student Behavior List -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-xl overflow-hidden mb-6">
    <div
        class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800 dark:to-slate-900">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-list-ol text-rose-500"></i>
                รายชื่อนักเรียน
            </h2>
            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" id="searchStudent" placeholder="ค้นหานักเรียน..."
                    class="w-full pl-9 pr-3 py-2 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:bg-slate-700 dark:text-white focus:border-rose-400 transition">
            </div>
        </div>
    </div>

    <!-- Student Cards Grid -->
    <div id="studentGrid" class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Loading Skeleton -->
        <div
            class="skeleton-card glass-card rounded-xl p-4 border border-slate-200 dark:border-slate-700 animate-pulse">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-slate-200 dark:bg-slate-600 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-600 rounded w-1/2"></div>
                </div>
            </div>
        </div>
        <div
            class="skeleton-card glass-card rounded-xl p-4 border border-slate-200 dark:border-slate-700 animate-pulse">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-slate-200 dark:bg-slate-600 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-600 rounded w-1/2"></div>
                </div>
            </div>
        </div>
        <div
            class="skeleton-card glass-card rounded-xl p-4 border border-slate-200 dark:border-slate-700 animate-pulse">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-slate-200 dark:bg-slate-600 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-600 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden p-12 text-center">
        <div
            class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center">
            <i class="fas fa-users text-slate-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-600 dark:text-slate-400 mb-2">ไม่พบข้อมูลนักเรียน</h3>
        <p class="text-sm text-slate-500">ไม่มีนักเรียนในห้องนี้</p>
    </div>
</div>

<!-- Score Explanation Card -->
<div
    class="glass-card rounded-2xl border border-amber-200 dark:border-amber-800/50 shadow-xl overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20">
    <div class="p-5 border-b border-amber-200 dark:border-amber-800/50">
        <h3 class="text-lg font-bold text-amber-700 dark:text-amber-300 flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            เกณฑ์การดำเนินการ
        </h3>
    </div>
    <div class="p-5 space-y-3">
        <div
            class="flex items-center gap-3 p-3 bg-red-100 dark:bg-red-900/30 rounded-xl border border-red-200 dark:border-red-800">
            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-red-700 dark:text-red-300">กลุ่มที่ 1: คะแนนต่ำกว่า 50</p>
                <p class="text-sm text-red-600 dark:text-red-400">เข้าค่ายปรับพฤติกรรม (กลุ่มบริหารงานกิจการนักเรียน)
                </p>
            </div>
        </div>
        <div
            class="flex items-center gap-3 p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl border border-orange-200 dark:border-orange-800">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-orange-700 dark:text-orange-300">กลุ่มที่ 2: คะแนน 50-70</p>
                <p class="text-sm text-orange-600 dark:text-orange-400">บำเพ็ญประโยชน์ 20 ชั่วโมง (หัวหน้าระดับ)</p>
            </div>
        </div>
        <div
            class="flex items-center gap-3 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl border border-yellow-200 dark:border-yellow-800">
            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-yellow-700 dark:text-yellow-300">กลุ่มที่ 3: คะแนน 71-99</p>
                <p class="text-sm text-yellow-600 dark:text-yellow-400">บำเพ็ญประโยชน์ 10 ชั่วโมง (ครูที่ปรึกษา)</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Behavior Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeAddModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all my-8">
            <!-- Modal Header -->
            <div
                class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-rose-500 to-red-600 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-minus-circle"></i>
                        หักคะแนนนักเรียน
                    </h3>
                    <button onclick="closeAddModal()" class="text-white/80 hover:text-white text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[50vh] overflow-y-auto">
                <form id="addBehaviorForm">
                    <div class="space-y-5">

                        <!-- Student Search Section -->
                        <div class="relative">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-search mr-1 text-rose-500"></i>
                                ค้นหานักเรียน
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-user-graduate text-slate-400"></i>
                                </div>
                                <input type="text" id="studentSearchInput" autocomplete="off"
                                    class="w-full pl-11 pr-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition"
                                    placeholder="พิมพ์ชื่อ นามสกุล หรือเลขประจำตัว...">
                                <div id="searchLoading" class="absolute inset-y-0 right-0 pr-4 items-center hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-rose-500"></div>
                                </div>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div id="searchResults"
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-slate-700 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-600 max-h-64 overflow-y-auto hidden">
                                <!-- Results will be populated here -->
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา
                            </p>
                        </div>

                        <!-- Selected Student Preview -->
                        <div id="selectedStudent" class="hidden">
                            <div
                                class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-xl border-2 border-emerald-200 dark:border-emerald-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div id="selectedStudentAvatar"
                                            class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-xl shadow-lg">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <h4 id="selectedStudentName"
                                                class="font-bold text-slate-800 dark:text-white text-lg"></h4>
                                            <p id="selectedStudentInfo"
                                                class="text-sm text-slate-600 dark:text-slate-400"></p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearSelectedStudent()"
                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition">
                                        <i class="fas fa-times-circle text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="addStuId" name="addStu_id">
                        </div>

                        <!-- No Student Warning -->
                        <div id="noStudentWarning"
                            class="p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-xl text-amber-700 dark:text-amber-300 text-sm flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            กรุณาค้นหาและเลือกนักเรียนก่อน
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-rose-500"></i>
                                วันที่
                            </label>
                            <input type="date" id="addDate" name="addBehavior_date" value="<?php echo $currentDate; ?>"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition">
                        </div>

                        <!-- Behavior Type -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-exclamation-triangle mr-1 text-rose-500"></i>
                                ประเภทพฤติกรรม
                            </label>
                            <select id="addType" name="addBehavior_type"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition cursor-pointer">
                                <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                <?php foreach ($behaviorTypes as $type => $score): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>"
                                        data-score="<?php echo $score; ?>">
                                        <?php echo htmlspecialchars($type); ?> (-<?php echo $score; ?> คะแนน)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Detail -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-edit mr-1 text-rose-500"></i>
                                รายละเอียดเพิ่มเติม (ไม่บังคับ)
                            </label>
                            <textarea id="addDetail" name="addBehavior_name" rows="2"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition resize-none"
                                placeholder="ระบุรายละเอียดเพิ่มเติม..."></textarea>
                        </div>

                        <!-- Score Display -->
                        <div
                            class="p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 rounded-xl border-2 border-red-200 dark:border-red-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-red-700 dark:text-red-300">
                                    <i class="fas fa-minus-circle mr-1"></i>
                                    คะแนนที่หัก
                                </span>
                                <span id="addScoreDisplay"
                                    class="text-3xl font-black text-red-600 dark:text-red-400">0</span>
                            </div>
                            <input type="hidden" id="addScore" name="addBehavior_score" value="0">
                        </div>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="term" value="<?php echo $term; ?>">
                        <input type="hidden" name="pee" value="<?php echo $pee; ?>">
                        <input type="hidden" name="teacherid" value="<?php echo $teacher_id; ?>">
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3">
                <button onclick="closeAddModal()"
                    class="flex-1 btn-action py-3.5 px-6 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-500 transition">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="submitBehavior()" id="submitBtn"
                    class="flex-1 btn-action py-3.5 px-6 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:shadow-rose-500/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>บันทึกการหักคะแนน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeViewModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all my-8">
            <!-- Modal Header -->
            <div
                class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-clipboard-list"></i>
                        รายละเอียดการถูกหักคะแนน
                    </h3>
                    <button onclick="closeViewModal()" class="text-white/80 hover:text-white text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[50vh] overflow-y-auto">
                <div id="viewStudentInfo" class="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <!-- Student Info -->
                </div>
                <div id="viewBehaviorDetails" class="space-y-4">
                    <!-- Behavior Details -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <button onclick="closeViewModal()"
                    class="w-full btn-action py-3 px-6 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    const classValue = <?php echo $class; ?>;
    const roomValue = <?php echo $room; ?>;
    const termValue = <?php echo $term; ?>;
    const peeValue = <?php echo $pee; ?>;

    let allStudents = [];
    let bonusData = {}; // เก็บคะแนนจิตอาสาของแต่ละนักเรียน
    let selectedStudentId = null; // เก็บ ID นักเรียนที่เลือก
    let searchTimeout = null; // สำหรับ debounce

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        loadStudents();

        // Search functionality for main page
        document.getElementById('searchStudent').addEventListener('input', function (e) {
            filterStudents(e.target.value);
        });

        // Student search in modal - with debounce
        document.getElementById('studentSearchInput').addEventListener('input', function (e) {
            const query = e.target.value.trim();

            if (searchTimeout) clearTimeout(searchTimeout);

            if (query.length < 2) {
                document.getElementById('searchResults').classList.add('hidden');
                document.getElementById('searchLoading').classList.add('hidden');
                return;
            }

            document.getElementById('searchLoading').classList.remove('hidden');
            document.getElementById('searchLoading').classList.add('flex');

            searchTimeout = setTimeout(() => {
                searchStudentsLive(query);
            }, 300);
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const searchContainer = document.getElementById('studentSearchInput')?.parentElement?.parentElement;
            if (searchContainer && !searchContainer.contains(e.target)) {
                document.getElementById('searchResults').classList.add('hidden');
            }
        });

        // Behavior type change - auto fill score
        document.getElementById('addType').addEventListener('change', function (e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const score = selectedOption.dataset.score || 0;
            document.getElementById('addScore').value = score;
            document.getElementById('addScoreDisplay').textContent = score;
        });
    });

    async function loadStudents() {
        try {
            // Load students with behavior scores
            const response = await fetch(`../controllers/BehaviorController.php?action=class_list&class=${classValue}&room=${roomValue}&term=${termValue}&pee=${peeValue}`);
            const data = await response.json();

            // Load volunteer bonus points
            const bonusResponse = await fetch(`../controllers/BehaviorController.php?action=volunteer_bonus&class=${classValue}&room=${roomValue}`);
            const bonusResult = await bonusResponse.json();

            if (bonusResult.success) {
                bonusData = bonusResult.data || {};
            }

            if (data.success) {
                allStudents = data.data || [];
                renderStudents(allStudents);
                updateStats(allStudents);
            }
        } catch (error) {
            console.error('Error loading students:', error);
        }
    }

    function renderStudents(students) {
        const grid = document.getElementById('studentGrid');
        const emptyState = document.getElementById('emptyState');

        // Remove skeletons
        grid.querySelectorAll('.skeleton-card').forEach(el => el.remove());

        if (students.length === 0) {
            grid.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        let html = '';
        students.forEach((student, index) => {
            const deduct = parseInt(student.total_behavior_score) || 0;
            const bonus = parseInt(bonusData[student.Stu_id]) || 0;
            const netScore = Math.max(0, Math.min(100, 100 - deduct + bonus)); // คะแนนสุทธิ
            const progress = Math.min(100, deduct); // แท่งแสดงการหัก

            let statusColor = 'emerald';
            let statusText = 'ดี';
            if (netScore < 50) {
                statusColor = 'red';
                statusText = 'วิกฤต';
            } else if (netScore < 70) {
                statusColor = 'orange';
                statusText = 'เฝ้าระวัง';
            } else if (netScore < 100) {
                statusColor = 'amber';
                statusText = 'ปานกลาง';
            }

            html += `
        <div class="student-card glass-card rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden fade-in-up" style="animation-delay: ${index * 0.03}s">
            <div class="p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-slate-600 to-slate-800 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        ${student.Stu_no || index + 1}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 dark:text-white truncate">
                            ${student.Stu_pre || ''}${student.Stu_name} ${student.Stu_sur}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">รหัส: ${student.Stu_id}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-${statusColor}-100 text-${statusColor}-700 dark:bg-${statusColor}-900/40 dark:text-${statusColor}-300">
                        ${statusText}
                    </span>
                </div>
                
                <!-- Net Score -->
                <div class="mb-3 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-600 dark:text-slate-400">คะแนนสุทธิ</span>
                        <span class="font-bold text-${statusColor}-600">${netScore}/100</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 dark:bg-slate-600 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-${statusColor}-400 to-${statusColor}-600 rounded-full transition-all duration-500" style="width: ${netScore}%"></div>
                    </div>
                </div>
                
                <!-- Score Details -->
                <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                    <div class="flex items-center justify-between p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <span class="text-red-600 dark:text-red-400"><i class="fas fa-minus-circle mr-1"></i>หัก</span>
                        <span class="font-bold text-red-600 dark:text-red-400">-${deduct}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                        <span class="text-teal-600 dark:text-teal-400"><i class="fas fa-hand-holding-heart mr-1"></i>จิตอาสา</span>
                        <span class="font-bold text-teal-600 dark:text-teal-400">+${bonus}</span>
                    </div>
                </div>
                
                <button onclick="showDetails('${student.Stu_id}', '${student.Stu_pre || ''}${student.Stu_name} ${student.Stu_sur}')" 
                        class="w-full py-2 px-4 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-lg font-medium text-sm hover:bg-blue-200 transition">
                    <i class="fas fa-eye mr-1"></i> ดูรายละเอียด
                </button>
            </div>
        </div>
        `;
        });

        grid.innerHTML = html;
    }

    function filterStudents(keyword) {
        if (!keyword) {
            renderStudents(allStudents);
            return;
        }

        const filtered = allStudents.filter(s =>
            s.Stu_name.toLowerCase().includes(keyword.toLowerCase()) ||
            s.Stu_sur.toLowerCase().includes(keyword.toLowerCase()) ||
            s.Stu_id.includes(keyword)
        );
        renderStudents(filtered);
    }

    function updateStats(students) {
        const total = students.length;
        const withDeduct = students.filter(s => s.total_behavior_score > 0).length;
        const good = total - withDeduct;
        const totalDeduction = students.reduce((sum, s) => sum + (parseInt(s.total_behavior_score) || 0), 0);
        const totalBonus = Object.values(bonusData).reduce((sum, v) => sum + (parseInt(v) || 0), 0);

        animateNumber('totalStudents', total);
        animateNumber('studentWithDeduct', withDeduct);
        animateNumber('studentGood', good);
        animateNumber('totalDeduction', totalDeduction);
        animateNumber('totalBonus', totalBonus);
    }

    function animateNumber(elementId, target) {
        const element = document.getElementById(elementId);
        const duration = 800;
        const start = parseInt(element.textContent) || 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(start + (target - start) * easeProgress);
            element.textContent = current;

            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    // Modal functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addBehaviorForm').reset();
        document.getElementById('addScore').value = 0;
        document.getElementById('addScoreDisplay').textContent = '0';

        // Reset search UI
        document.getElementById('studentSearchInput').value = '';
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('selectedStudent').classList.add('hidden');
        document.getElementById('noStudentWarning').classList.remove('hidden');
        selectedStudentId = null;
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
    }

    // Live search for students
    async function searchStudentsLive(query) {
        const resultsContainer = document.getElementById('searchResults');
        const loadingEl = document.getElementById('searchLoading');

        try {
            const response = await fetch(`../controllers/BehaviorController.php?action=search_students&q=${encodeURIComponent(query)}&limit=10`);
            const students = await response.json();

            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');

            if (students.length === 0) {
                resultsContainer.innerHTML = `
                <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p>ไม่พบนักเรียนที่ค้นหา</p>
                </div>
            `;
                resultsContainer.classList.remove('hidden');
                return;
            }

            let html = '';
            students.forEach(student => {
                html += `
                <div onclick="selectStudent('${student.Stu_id}', '${student.Stu_pre || ''}${student.Stu_name}', '${student.Stu_sur}', '${student.Stu_major}', '${student.Stu_room}')" 
                     class="flex items-center gap-3 p-3 hover:bg-slate-100 dark:hover:bg-slate-600 cursor-pointer transition border-b border-slate-100 dark:border-slate-600 last:border-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow">
                        ${student.Stu_picture ?
                        `<img src="https://std.phichai.ac.th/photo/${student.Stu_picture}" class="w-10 h-10 rounded-full object-cover">` :
                        `<i class="fas fa-user"></i>`
                    }
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 dark:text-white truncate">${student.Stu_pre || ''}${student.Stu_name} ${student.Stu_sur}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">รหัส: ${student.Stu_id} | ม.${student.Stu_major}/${student.Stu_room}</p>
                    </div>
                    <i class="fas fa-chevron-right text-slate-400"></i>
                </div>
            `;
            });

            resultsContainer.innerHTML = html;
            resultsContainer.classList.remove('hidden');

        } catch (error) {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
            resultsContainer.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                <p>เกิดข้อผิดพลาด</p>
            </div>
        `;
            resultsContainer.classList.remove('hidden');
        }
    }

    // Select student from search results
    function selectStudent(stuId, name, surname, major, room) {
        selectedStudentId = stuId;

        // Update hidden field
        document.getElementById('addStuId').value = stuId;

        // Update UI
        document.getElementById('selectedStudentName').textContent = `${name} ${surname}`;
        document.getElementById('selectedStudentInfo').textContent = `รหัส: ${stuId} | ม.${major}/${room}`;

        // Show selected, hide warning and search results
        document.getElementById('selectedStudent').classList.remove('hidden');
        document.getElementById('noStudentWarning').classList.add('hidden');
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('studentSearchInput').value = '';
    }

    // Clear selected student
    function clearSelectedStudent() {
        selectedStudentId = null;
        document.getElementById('addStuId').value = '';
        document.getElementById('selectedStudent').classList.add('hidden');
        document.getElementById('noStudentWarning').classList.remove('hidden');
        document.getElementById('studentSearchInput').focus();
    }

    async function submitBehavior() {
        const form = document.getElementById('addBehaviorForm');
        const formData = new FormData(form);

        // Validate
        const stuId = formData.get('addStu_id');
        const type = formData.get('addBehavior_type');

        if (!stuId || !selectedStudentId) {
            Swal.fire('ข้อผิดพลาด', 'กรุณาค้นหาและเลือกนักเรียน', 'error');
            return;
        }

        if (!type) {
            Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกประเภทพฤติกรรม', 'error');
            return;
        }

        try {
            const response = await fetch('../controllers/BehaviorController.php?action=create', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success');
                closeAddModal();
                loadStudents();
            } else {
                Swal.fire('ข้อผิดพลาด', result.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
        }
    }

    async function showDetails(stuId, studentName) {
        document.getElementById('viewModal').classList.remove('hidden');

        document.getElementById('viewStudentInfo').innerHTML = `
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 dark:text-white text-lg">${studentName}</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">รหัส: ${stuId}</p>
            </div>
        </div>
    `;

        document.getElementById('viewBehaviorDetails').innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-3 text-slate-600">กำลังโหลด...</p>
        </div>
    `;

        try {
            const response = await fetch(`../controllers/BehaviorController.php?action=student_details&stu_id=${stuId}`);
            const result = await response.json();

            if (result.success && result.data && result.data.length > 0) {
                let html = '';
                let totalScore = 0;

                result.data.forEach((item, idx) => {
                    totalScore += parseInt(item.behavior_score);
                    const date = new Date(item.behavior_date).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

                    html += `
                <div class="p-4 bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-1 text-xs font-bold bg-red-100 text-red-700 rounded">#${idx + 1}</span>
                        <span class="px-3 py-1 text-sm font-bold bg-red-500 text-white rounded-full">-${item.behavior_score}</span>
                    </div>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-1">${item.behavior_type}</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-1"><i class="fas fa-calendar mr-1"></i>${date}</p>
                    ${item.behavior_name ? `<p class="text-sm text-slate-600 dark:text-slate-400"><i class="fas fa-info-circle mr-1"></i>${item.behavior_name}</p>` : ''}
                    <p class="text-sm text-blue-600 dark:text-blue-400 mt-2"><i class="fas fa-user-tie mr-1"></i>${item.Teach_name || 'ไม่ระบุ'}</p>
                </div>
                `;
                });

                html += `
            <div class="p-4 bg-gradient-to-r from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30 rounded-xl border border-red-200 dark:border-red-800">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-red-800 dark:text-red-300">รวมถูกหัก:</span>
                    <span class="text-2xl font-black text-red-600 dark:text-red-400">${totalScore} คะแนน</span>
                </div>
            </div>
            `;

                document.getElementById('viewBehaviorDetails').innerHTML = html;
            } else {
                document.getElementById('viewBehaviorDetails').innerHTML = `
            <div class="text-center py-8">
                <div class="text-5xl mb-4">✅</div>
                <h4 class="font-bold text-emerald-700 dark:text-emerald-300">ไม่มีประวัติถูกหักคะแนน</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">นักเรียนคนนี้มีพฤติกรรมดี</p>
            </div>
            `;
            }
        } catch (error) {
            document.getElementById('viewBehaviorDetails').innerHTML = `
        <div class="text-center py-8 text-red-500">
            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
            <p>เกิดข้อผิดพลาดในการโหลดข้อมูล</p>
        </div>
        `;
        }
    }

    function printReport() {
        const classRoom = '<?php echo $class . "/" . $room; ?>';
        const termPee = 'ภาคเรียนที่ <?php echo $term; ?>/<?php echo $pee; ?>';
        const teacherName = '<?php echo htmlspecialchars($teacher_name); ?>';
        const currentDate = new Date().toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Calculate totals
        const totalStudents = allStudents.length;
        const totalDeduction = allStudents.reduce((sum, s) => sum + (parseInt(s.total_behavior_score) || 0), 0);
        const totalBonus = Object.values(bonusData).reduce((sum, v) => sum + (parseInt(v) || 0), 0);
        const studentsWithDeduct = allStudents.filter(s => s.total_behavior_score > 0).length;

        // Build table rows
        let tableRows = '';
        allStudents.forEach((student, index) => {
            const deduct = parseInt(student.total_behavior_score) || 0;
            const bonus = parseInt(bonusData[student.Stu_id]) || 0;
            const netScore = Math.max(0, Math.min(100, 100 - deduct + bonus));

            let statusText = 'ดี';
            let statusColor = '#10b981';
            if (netScore < 50) {
                statusText = 'วิกฤต';
                statusColor = '#ef4444';
            } else if (netScore < 70) {
                statusText = 'เฝ้าระวัง';
                statusColor = '#f97316';
            } else if (netScore < 100) {
                statusText = 'ปานกลาง';
                statusColor = '#f59e0b';
            }

            tableRows += `
            <tr style="background: ${index % 2 === 0 ? '#f8fafc' : '#ffffff'};">
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">${student.Stu_no || index + 1}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center;">${student.Stu_id}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0;">${student.Stu_pre || ''}${student.Stu_name} ${student.Stu_sur}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; color: #ef4444; font-weight: bold;">${deduct > 0 ? `-${deduct}` : '0'}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; color: #14b8a6; font-weight: bold;">${bonus > 0 ? `+${bonus}` : '0'}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">${netScore}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; color: ${statusColor}; font-weight: bold;">${statusText}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0;"></td>
            </tr>
        `;
        });

        // Collect unique teacher names who recorded deductions
        const allTeacherNames = new Set();
        allStudents.forEach(student => {
            if (student.teacher_names) {
                student.teacher_names.split(',').forEach(name => {
                    const trimmed = name.trim();
                    if (trimmed) allTeacherNames.add(trimmed);
                });
            }
        });

        // Build signature boxes for all teachers
        let signatureBoxes = '';
        // First add homeroom teacher
        signatureBoxes += `
        <div class="signature-box">
            <div class="signature-line">
                (${teacherName})<br>
                ครูที่ปรึกษา
            </div>
        </div>
    `;
        // Then add all teachers who recorded deductions
        allTeacherNames.forEach(tName => {
            if (tName !== teacherName) { // Avoid duplicate if homeroom teacher also recorded
                signatureBoxes += `
                <div class="signature-box">
                    <div class="signature-line">
                        (${tName})<br>
                        ครูผู้บันทึก
                    </div>
                </div>
            `;
            }
        });

        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>รายงานคะแนนพฤติกรรม ม.${classRoom}</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
                * { font-family: 'Sarabun', 'Mali', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
                body { padding: 20px; font-size: 12pt; color: #1e293b; }
                .header { text-align: center; margin-bottom: 20px; }
                .header img { width: 60px; height: 60px; margin-bottom: 10px; }
                .header h1 { font-size: 18pt; font-weight: bold; margin: 5px 0; }
                .header h2 { font-size: 14pt; font-weight: 500; margin: 3px 0; }
                .header p { font-size: 10pt; color: #64748b; margin: 2px 0; }
                .info-box { display: flex; justify-content: space-between; margin: 15px 0; padding: 10px; background: #f1f5f9; border-radius: 8px; }
                .info-box div { text-align: center; }
                .info-box .label { font-size: 10pt; color: #64748b; }
                .info-box .value { font-size: 14pt; font-weight: bold; }
                .info-box .value.red { color: #ef4444; }
                .info-box .value.teal { color: #14b8a6; }
                .info-box .value.blue { color: #3b82f6; }
                .info-box .value.amber { color: #f59e0b; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
                thead th { background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; padding: 10px 8px; border: 1px solid #3b82f6; font-weight: bold; }
                .footer { margin-top: 20px; text-align: right; font-size: 10pt; color: #64748b; }
                .signature { margin-top: 40px; display: flex; justify-content: center; flex-wrap: wrap; gap: 30px; }
                .signature-box { text-align: center; min-width: 150px; }
                .signature-line { border-top: 1px solid #1e293b; margin-top: 50px; padding-top: 5px; font-size: 10pt; }
                @media print {
                    body { padding: 10px; }
                    @page { size: A4 portrait; margin: 1cm; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="../dist/img/logo-phicha.png" alt="Logo">
                <h1>📊 รายงานคะแนนพฤติกรรมนักเรียน</h1>
                <h2>ระดับชั้นมัธยมศึกษาปีที่ ${classRoom}</h2>
                <p>${termPee} | ครูที่ปรึกษา: ${teacherName}</p>
                <p>วันที่พิมพ์: ${currentDate}</p>
            </div>
            
            <div class="info-box">
                <div>
                    <div class="label">นักเรียนทั้งหมด</div>
                    <div class="value blue">${totalStudents} คน</div>
                </div>
                <div>
                    <div class="label">ถูกหักคะแนน</div>
                    <div class="value amber">${studentsWithDeduct} คน</div>
                </div>
                <div>
                    <div class="label">รวมคะแนนหัก</div>
                    <div class="value red">-${totalDeduction} คะแนน</div>
                </div>
                <div>
                    <div class="label">รวมจิตอาสา</div>
                    <div class="value teal">+${totalBonus} คะแนน</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">ที่</th>
                        <th style="width: 70px;">รหัส</th>
                        <th>ชื่อ-สกุล</th>
                        <th style="width: 50px;">หัก</th>
                        <th style="width: 55px;">จิตอาสา</th>
                        <th style="width: 50px;">สุทธิ</th>
                        <th style="width: 100px;">สถานะ</th>
                        <th style="width: 100px;">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
            
            <div class="signature">
                ${signatureBoxes}
            </div>
            
            <div class="footer">
                <p>ระบบดูแลช่วยเหลือนักเรียน - โรงเรียนพิชัย</p>
            </div>
            
            <scr` + `ipt>
                window.onload = function() {
                    window.print();
                };
            </scr` + `ipt>
        </body>
        </html>
    `;

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(printContent);
        printWindow.document.close();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>