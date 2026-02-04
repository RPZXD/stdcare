<?php
/**
 * Show Behavior View - MVC Pattern
 * Teacher's behavior record list with modern Tailwind CSS UI
 */
ob_start();
?>

<!-- Custom Styles -->
<style>
    /* Glass morphism effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .dark .glass-card {
        background: rgba(30, 41, 59, 0.95);
    }

    /* Table hover effect */
    .behavior-row:hover {
        transform: translateX(4px);
        transition: all 0.2s ease;
    }

    /* Skeleton loading */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Fade in animation */
    .fade-in-up {
        animation: fadeInUp 0.4s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile table responsive */
    @media (max-width: 768px) {
        .mobile-card {
            display: block !important;
        }

        .desktop-table {
            display: none !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-card {
            display: none !important;
        }

        .desktop-table {
            display: block !important;
        }
    }
</style>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                รายงานการหักคะแนน
            </h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">
                ครู <?php echo htmlspecialchars($teacher_name); ?> | ภาคเรียน <?php echo $term; ?>/<?php echo $pee; ?>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 md:gap-3">
            <button onclick="openAddModal()"
                class="btn-action px-4 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:shadow-rose-500/30 transition flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span class="hidden sm:inline">หักคะแนน</span>
            </button>
            <a href="behavior.php"
                class="btn-action px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-emerald-500/30 transition flex items-center gap-2 no-underline">
                <i class="fas fa-chart-bar"></i>
                <span class="hidden sm:inline">คะแนนชั้นเรียน</span>
            </a>
            <button onclick="printReport()"
                class="btn-action px-4 py-2.5 bg-gradient-to-r from-purple-500 to-violet-600 text-white rounded-xl font-bold shadow-lg hover:shadow-purple-500/30 transition flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span class="hidden sm:inline">พิมพ์</span>
            </button>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div
            class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-file-alt text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600"
            id="totalRecords">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">รายการทั้งหมด</p>
    </div>

    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div
            class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-rose-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-red-600"
            id="totalStudents">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">นักเรียน</p>
    </div>

    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div
            class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-amber-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-minus-circle text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600"
            id="totalDeducted">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">รวมหัก</p>
    </div>

    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div
            class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-violet-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-calendar-alt text-white text-lg"></i>
        </div>
        <p class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-600"
            id="latestDate">-</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ล่าสุด</p>
    </div>
</div>

<!-- Search Bar -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input type="text" id="searchInput"
                class="w-full pl-11 pr-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 transition"
                placeholder="ค้นหา รหัส, ชื่อ, ประเภทพฤติกรรม...">
        </div>
        <select id="filterType"
            class="px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-indigo-400 transition">
            <option value="">ทุกประเภท</option>
            <?php foreach ($behaviorTypes as $type => $score): ?>
                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Behavior Records List -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-xl overflow-hidden">
    <!-- Header -->
    <div
        class="p-4 md:p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-500 to-purple-600">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fas fa-list-alt"></i>
            รายการหักคะแนน
        </h2>
    </div>

    <!-- Desktop Table -->
    <div class="desktop-table overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th
                        class="px-4 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        #</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        รหัส</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        ชื่อ-สกุล</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        วันที่</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        ประเภท</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        คะแนน</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        จัดการ</th>
                </tr>
            </thead>
            <tbody id="behaviorTableBody" class="divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Loading Skeletons -->
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <tr class="skeleton-row">
                        <td class="px-4 py-4">
                            <div class="skeleton h-4 w-8 rounded"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-4 w-16 rounded"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-4 w-32 rounded"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-4 w-24 rounded mx-auto"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-4 w-40 rounded"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-8 w-12 rounded-full mx-auto"></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="skeleton h-8 w-24 rounded mx-auto"></div>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div id="mobileCards" class="mobile-card p-4 space-y-3">
        <!-- Will be populated by JS -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden p-8 text-center">
        <div
            class="w-20 h-20 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
            <i class="fas fa-inbox text-3xl text-slate-400"></i>
        </div>
        <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-1">ยังไม่มีรายการหักคะแนน</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">คลิกปุ่ม "หักคะแนน" เพื่อเพิ่มรายการใหม่</p>
    </div>

    <!-- Pagination -->
    <div
        class="p-4 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-slate-600 dark:text-slate-400" id="paginationInfo">แสดง 0 รายการ</p>
        <div class="flex gap-2" id="paginationButtons">
            <!-- Will be populated by JS -->
        </div>
    </div>
</div>

<!-- Score Guidelines -->
<div class="mt-6 glass-card rounded-2xl border border-amber-200 dark:border-amber-800 shadow-lg overflow-hidden">
    <div
        class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 border-b border-amber-200 dark:border-amber-800">
        <h3 class="font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
            <i class="fas fa-lightbulb"></i>
            เกณฑ์การดำเนินการ
        </h3>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-circle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-red-700 dark:text-red-300">กลุ่มที่ 1: < 50 คะแนน</p>
                        <p class="text-sm text-red-600 dark:text-red-400">เข้าค่ายปรับพฤติกรรม</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-orange-700 dark:text-orange-300">กลุ่มที่ 2: 50-70 คะแนน</p>
                <p class="text-sm text-orange-600 dark:text-orange-400">บำเพ็ญ 20 ชม. (หัวหน้าระดับ)</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div>
                <p class="font-bold text-yellow-700 dark:text-yellow-300">กลุ่มที่ 3: 71-99 คะแนน</p>
                <p class="text-sm text-yellow-600 dark:text-yellow-400">บำเพ็ญ 10 ชม. (ครูที่ปรึกษา)</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Behavior Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black/50" onclick="closeAddModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all fade-in-up my-8">
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
                        <!-- Student Search -->
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
                                    class="w-full pl-11 pr-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 transition"
                                    placeholder="พิมพ์ชื่อ นามสกุล หรือเลขประจำตัว...">
                                <div id="searchLoading" class="absolute inset-y-0 right-0 pr-4 items-center hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-rose-500"></div>
                                </div>
                            </div>
                            <div id="searchDropdown"
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-slate-700 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-600 max-h-64 overflow-y-auto hidden">
                            </div>
                        </div>

                        <!-- Selected Student Preview -->
                        <div id="selectedStudent" class="hidden">
                            <div
                                class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-xl border-2 border-emerald-200 dark:border-emerald-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div
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
                                        class="p-2 text-slate-400 hover:text-red-500 rounded-lg transition">
                                        <i class="fas fa-times-circle text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="addStuId" name="addStu_id">
                        </div>

                        <div id="noStudentWarning"
                            class="p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-xl text-amber-700 dark:text-amber-300 text-sm flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            กรุณาค้นหาและเลือกนักเรียนก่อน
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-rose-500"></i> วันที่
                            </label>
                            <input type="date" id="addDate" name="addBehavior_date" value="<?php echo $currentDate; ?>"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 transition">
                        </div>

                        <!-- Behavior Type -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-exclamation-triangle mr-1 text-rose-500"></i> ประเภทพฤติกรรม
                            </label>
                            <select id="addType" name="addBehavior_type"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 transition">
                                <option value="">-- เลือกประเภท --</option>
                                <?php foreach ($behaviorTypes as $type => $score): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>"
                                        data-score="<?php echo $score; ?>">
                                        <?php echo htmlspecialchars($type); ?> (-<?php echo $score; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Detail -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-edit mr-1 text-rose-500"></i> รายละเอียด (ไม่บังคับ)
                            </label>
                            <textarea id="addDetail" name="addBehavior_name" rows="2"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-rose-400 transition resize-none"
                                placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                        </div>

                        <!-- Score Display -->
                        <div
                            class="p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 rounded-xl border-2 border-red-200 dark:border-red-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-red-700 dark:text-red-300">
                                    <i class="fas fa-minus-circle mr-1"></i> คะแนนที่หัก
                                </span>
                                <span id="addScoreDisplay"
                                    class="text-3xl font-black text-red-600 dark:text-red-400">0</span>
                            </div>
                            <input type="hidden" id="addScore" name="addBehavior_score" value="0">
                        </div>

                        <input type="hidden" name="term" value="<?php echo $term; ?>">
                        <input type="hidden" name="pee" value="<?php echo $pee; ?>">
                        <input type="hidden" name="teacherid" value="<?php echo $teacher_id; ?>">
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3">
                <button onclick="closeAddModal()"
                    class="flex-1 py-3.5 px-6 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="submitBehavior()"
                    class="flex-1 py-3.5 px-6 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:shadow-rose-500/30 transition">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black/50" onclick="closeEditModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all fade-in-up my-8">
            <div
                class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-amber-500 to-orange-600 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        แก้ไขรายการ
                    </h3>
                    <button onclick="closeEditModal()" class="text-white/80 hover:text-white text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 max-h-[50vh] overflow-y-auto">
                <div id="editStudentPreview" class="mb-4"></div>
                <form id="editBehaviorForm">
                    <input type="hidden" id="editId" name="editId">
                    <input type="hidden" id="editStuId" name="editStu_id">

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">วันที่</label>
                            <input type="date" id="editDate" name="editBehavior_date"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">ประเภท</label>
                            <select id="editType" name="editBehavior_type"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white">
                                <?php foreach ($behaviorTypes as $type => $score): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>"
                                        data-score="<?php echo $score; ?>"><?php echo htmlspecialchars($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">รายละเอียด</label>
                            <textarea id="editDetail" name="editBehavior_name" rows="2"
                                class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white resize-none"></textarea>
                        </div>

                        <div
                            class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 rounded-xl border-2 border-amber-200 dark:border-amber-700">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-700 dark:text-amber-300">คะแนนที่หัก</span>
                                <span id="editScoreDisplay"
                                    class="text-2xl font-black text-amber-600 dark:text-amber-400">0</span>
                            </div>
                            <input type="hidden" id="editScore" name="editBehavior_score" value="0">
                        </div>
                    </div>
                </form>
            </div>

            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3">
                <button onclick="closeEditModal()"
                    class="flex-1 py-3.5 px-6 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="updateBehavior()"
                    class="flex-1 py-3.5 px-6 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    const teacherId = <?php echo $teacher_id; ?>;
    const termValue = <?php echo $term; ?>;
    const peeValue = <?php echo $pee; ?>;

    let allBehaviors = [];
    let filteredBehaviors = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let selectedStudentId = null;
    let searchTimeout = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        loadBehaviors();

        // Search input
        document.getElementById('searchInput').addEventListener('input', function (e) {
            filterBehaviors();
        });

        // Filter type
        document.getElementById('filterType').addEventListener('change', function () {
            filterBehaviors();
        });

        // Student search in modal
        document.getElementById('studentSearchInput').addEventListener('input', function (e) {
            const query = e.target.value.trim();
            if (searchTimeout) clearTimeout(searchTimeout);

            if (query.length < 2) {
                document.getElementById('searchDropdown').classList.add('hidden');
                return;
            }

            document.getElementById('searchLoading').classList.remove('hidden');
            document.getElementById('searchLoading').classList.add('flex');

            searchTimeout = setTimeout(() => searchStudentsLive(query), 300);
        });

        // Behavior type change - auto score
        document.getElementById('addType').addEventListener('change', function () {
            const score = this.options[this.selectedIndex].dataset.score || 0;
            document.getElementById('addScore').value = score;
            document.getElementById('addScoreDisplay').textContent = score;
        });

        document.getElementById('editType').addEventListener('change', function () {
            const score = this.options[this.selectedIndex].dataset.score || 0;
            document.getElementById('editScore').value = score;
            document.getElementById('editScoreDisplay').textContent = score;
        });
    });

    async function loadBehaviors() {
        try {
            const response = await fetch(`../controllers/BehaviorController.php?action=teacher_behaviors&teacher_id=${teacherId}`);
            const result = await response.json();

            if (result.success) {
                allBehaviors = result.data || [];
                filteredBehaviors = [...allBehaviors];
                renderBehaviors();
                updateStats();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function filterBehaviors() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const filterType = document.getElementById('filterType').value;

        filteredBehaviors = allBehaviors.filter(b => {
            const matchSearch = !search ||
                b.stu_id.includes(search) ||
                (b.Stu_name && b.Stu_name.toLowerCase().includes(search)) ||
                (b.Stu_sur && b.Stu_sur.toLowerCase().includes(search)) ||
                (b.behavior_type && b.behavior_type.toLowerCase().includes(search));

            const matchType = !filterType || b.behavior_type === filterType;

            return matchSearch && matchType;
        });

        currentPage = 1;
        renderBehaviors();
    }

    function renderBehaviors() {
        const tbody = document.getElementById('behaviorTableBody');
        const mobileCards = document.getElementById('mobileCards');
        const emptyState = document.getElementById('emptyState');

        // Remove skeletons
        document.querySelectorAll('.skeleton-row').forEach(el => el.remove());

        if (filteredBehaviors.length === 0) {
            tbody.innerHTML = '';
            mobileCards.innerHTML = '';
            emptyState.classList.remove('hidden');
            document.getElementById('paginationInfo').textContent = 'ไม่พบข้อมูล';
            return;
        }

        emptyState.classList.add('hidden');

        // Pagination
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginated = filteredBehaviors.slice(start, end);

        // Desktop table
        let tableHtml = '';
        paginated.forEach((b, i) => {
            const thaiDate = formatThaiDate(b.behavior_date);
            tableHtml += `
        <tr class="behavior-row hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-300">${start + i + 1}</td>
            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">${b.stu_id}</td>
            <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">${b.Stu_pre || ''}${b.Stu_name} ${b.Stu_sur}</td>
            <td class="px-4 py-3 text-center text-slate-600 dark:text-slate-400">${thaiDate}</td>
            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">${b.behavior_type}</td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                    -${b.behavior_score}
                </span>
            </td>
            <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="editBehavior(${b.id})" class="p-2 text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteBehavior(${b.id})" class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition" title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
        });
        tbody.innerHTML = tableHtml;

        // Mobile cards
        let cardsHtml = '';
        paginated.forEach((b, i) => {
            const thaiDate = formatThaiDate(b.behavior_date);
            cardsHtml += `
        <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 fade-in-up" style="animation-delay: ${i * 0.05}s">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white">${b.Stu_pre || ''}${b.Stu_name} ${b.Stu_sur}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">รหัส: ${b.stu_id}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                    -${b.behavior_score}
                </span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">${b.behavior_type}</p>
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span><i class="fas fa-calendar mr-1"></i>${thaiDate}</span>
                <div class="flex gap-2">
                    <button onclick="editBehavior(${b.id})" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteBehavior(${b.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
        });
        mobileCards.innerHTML = cardsHtml;

        // Pagination info
        document.getElementById('paginationInfo').textContent = `แสดง ${start + 1}-${Math.min(end, filteredBehaviors.length)} จาก ${filteredBehaviors.length} รายการ`;

        // Pagination buttons
        const totalPages = Math.ceil(filteredBehaviors.length / itemsPerPage);
        let paginationHtml = '';

        if (totalPages > 1) {
            paginationHtml += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 disabled:opacity-50"><i class="fas fa-chevron-left"></i></button>`;

            for (let p = 1; p <= totalPages; p++) {
                if (p === currentPage) {
                    paginationHtml += `<button class="px-3 py-2 rounded-lg bg-indigo-500 text-white font-bold">${p}</button>`;
                } else {
                    paginationHtml += `<button onclick="changePage(${p})" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700">${p}</button>`;
                }
            }

            paginationHtml += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 disabled:opacity-50"><i class="fas fa-chevron-right"></i></button>`;
        }
        document.getElementById('paginationButtons').innerHTML = paginationHtml;
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredBehaviors.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderBehaviors();
        }
    }

    function updateStats() {
        document.getElementById('totalRecords').textContent = allBehaviors.length;

        const uniqueStudents = new Set(allBehaviors.map(b => b.stu_id));
        document.getElementById('totalStudents').textContent = uniqueStudents.size;

        const totalDeducted = allBehaviors.reduce((sum, b) => sum + parseInt(b.behavior_score || 0), 0);
        document.getElementById('totalDeducted').textContent = totalDeducted;

        if (allBehaviors.length > 0) {
            const latest = allBehaviors.reduce((a, b) => new Date(a.behavior_date) > new Date(b.behavior_date) ? a : b);
            document.getElementById('latestDate').textContent = formatThaiDateShort(latest.behavior_date);
        }
    }

    function formatThaiDate(dateStr) {
        const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        const d = new Date(dateStr);
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
    }

    function formatThaiDateShort(dateStr) {
        const d = new Date(dateStr);
        return `${d.getDate()}/${d.getMonth() + 1}`;
    }

    // Modal functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('addBehaviorForm').reset();
        document.getElementById('addScoreDisplay').textContent = '0';
        document.getElementById('studentSearchInput').value = '';
        document.getElementById('searchDropdown').classList.add('hidden');
        document.getElementById('selectedStudent').classList.add('hidden');
        document.getElementById('noStudentWarning').classList.remove('hidden');
        selectedStudentId = null;
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    async function searchStudentsLive(query) {
        const dropdown = document.getElementById('searchDropdown');
        const loading = document.getElementById('searchLoading');

        try {
            const response = await fetch(`../controllers/BehaviorController.php?action=search_students&q=${encodeURIComponent(query)}&limit=10`);
            const students = await response.json();

            loading.classList.add('hidden');

            if (students.length === 0) {
                dropdown.innerHTML = '<div class="p-4 text-center text-slate-500">ไม่พบนักเรียน</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            let html = '';
            students.forEach(s => {
                html += `
            <div onclick="selectStudent('${s.Stu_id}', '${s.Stu_pre || ''}${s.Stu_name}', '${s.Stu_sur}', '${s.Stu_major}', '${s.Stu_room}')" 
                 class="flex items-center gap-3 p-3 hover:bg-slate-100 dark:hover:bg-slate-600 cursor-pointer border-b border-slate-100 dark:border-slate-600 last:border-0">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-slate-800 dark:text-white">${s.Stu_pre || ''}${s.Stu_name} ${s.Stu_sur}</p>
                    <p class="text-xs text-slate-500">รหัส: ${s.Stu_id} | ม.${s.Stu_major}/${s.Stu_room}</p>
                </div>
            </div>
            `;
            });

            dropdown.innerHTML = html;
            dropdown.classList.remove('hidden');
        } catch (e) {
            loading.classList.add('hidden');
        }
    }

    function selectStudent(stuId, name, surname, major, room) {
        selectedStudentId = stuId;
        document.getElementById('addStuId').value = stuId;
        document.getElementById('selectedStudentName').textContent = `${name} ${surname}`;
        document.getElementById('selectedStudentInfo').textContent = `รหัส: ${stuId} | ม.${major}/${room}`;
        document.getElementById('selectedStudent').classList.remove('hidden');
        document.getElementById('noStudentWarning').classList.add('hidden');
        document.getElementById('searchDropdown').classList.add('hidden');
        document.getElementById('studentSearchInput').value = '';
    }

    function clearSelectedStudent() {
        selectedStudentId = null;
        document.getElementById('addStuId').value = '';
        document.getElementById('selectedStudent').classList.add('hidden');
        document.getElementById('noStudentWarning').classList.remove('hidden');
    }

    async function submitBehavior() {
        if (!selectedStudentId) {
            Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกนักเรียน', 'error');
            return;
        }

        const type = document.getElementById('addType').value;
        if (!type) {
            Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกประเภทพฤติกรรม', 'error');
            return;
        }

        const formData = new FormData(document.getElementById('addBehaviorForm'));

        try {
            const response = await fetch('../controllers/BehaviorController.php?action=create', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success');
                closeAddModal();
                loadBehaviors();
            } else {
                Swal.fire('ข้อผิดพลาด', result.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        } catch (e) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
        }
    }

    async function editBehavior(id) {
        try {
            const response = await fetch(`../controllers/BehaviorController.php?action=get&id=${id}`);
            const data = await response.json();

            if (data) {
                document.getElementById('editId').value = data.id;
                document.getElementById('editStuId').value = data.stu_id;
                document.getElementById('editDate').value = data.behavior_date;
                document.getElementById('editType').value = data.behavior_type;
                document.getElementById('editDetail').value = data.behavior_name || '';
                document.getElementById('editScore').value = data.behavior_score;
                document.getElementById('editScoreDisplay').textContent = data.behavior_score;

                // Load student preview
                const stuRes = await fetch(`../controllers/BehaviorController.php?action=search_student&id=${data.stu_id}`);
                const stu = await stuRes.json();
                if (stu) {
                    document.getElementById('editStudentPreview').innerHTML = `
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white">${stu.Stu_pre || ''}${stu.Stu_name} ${stu.Stu_sur}</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">รหัส: ${stu.Stu_id} | ม.${stu.Stu_major}/${stu.Stu_room}</p>
                        </div>
                    </div>
                </div>
                `;
                }

                document.getElementById('editModal').classList.remove('hidden');
            }
        } catch (e) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูล', 'error');
        }
    }

    async function updateBehavior() {
        const formData = new FormData(document.getElementById('editBehaviorForm'));

        try {
            const response = await fetch('../controllers/BehaviorController.php?action=update', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                Swal.fire('สำเร็จ', 'อัปเดตข้อมูลเรียบร้อย', 'success');
                closeEditModal();
                loadBehaviors();
            } else {
                Swal.fire('ข้อผิดพลาด', result.message || 'ไม่สามารถอัปเดตได้', 'error');
            }
        } catch (e) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
        }
    }

    async function deleteBehavior(id) {
        const result = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบรายการนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch('../controllers/BehaviorController.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const res = await response.json();

                if (res.success) {
                    Swal.fire('ลบแล้ว!', 'ข้อมูลถูกลบเรียบร้อย', 'success');
                    loadBehaviors();
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                }
            } catch (e) {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาด', 'error');
            }
        }
    }

    function printReport() {
        const teacherName = '<?php echo htmlspecialchars($teacher_name); ?>';
        const termPee = 'ภาคเรียนที่ <?php echo $term; ?>/<?php echo $pee; ?>';
        const currentDate = new Date().toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Summary stats
        const totalRecords = allBehaviors.length;
        const uniqueStudents = new Set(allBehaviors.map(b => b.stu_id)).size;
        const totalDeducted = allBehaviors.reduce((sum, b) => sum + parseInt(b.behavior_score || 0), 0);

        // Build table rows
        let tableRows = '';
        allBehaviors.forEach((b, index) => {
            const thaiDate = formatThaiDate(b.behavior_date);
            tableRows += `
            <tr style="background: ${index % 2 === 0 ? '#f8fafc' : '#ffffff'};">
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center;">${index + 1}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; font-family: monospace;">${b.stu_id}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0;">${b.Stu_pre || ''}${b.Stu_name} ${b.Stu_sur}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center;">${thaiDate}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0;">${b.behavior_type}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0;">${b.behavior_name || '-'}</td>
                <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; color: #dc2626; font-weight: bold;">-${b.behavior_score}</td>
            </tr>
        `;
        });

        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>รายงานการหักคะแนน - ${teacherName}</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Sarabun', sans-serif; padding: 20px; font-size: 11pt; color: #1e293b; }
                .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #4f46e5; }
                .header img { width: 60px; height: 60px; margin-bottom: 10px; }
                .header h1 { font-size: 18pt; color: #1e40af; margin-bottom: 5px; }
                .header h2 { font-size: 14pt; color: #374151; margin-bottom: 3px; }
                .header p { font-size: 11pt; color: #64748b; }
                .info-box { display: flex; justify-content: center; gap: 30px; margin-bottom: 20px; background: linear-gradient(135deg, #f0f9ff, #e0e7ff); padding: 15px; border-radius: 8px; }
                .info-box div { text-align: center; }
                .info-box .label { font-size: 10pt; color: #64748b; }
                .info-box .value { font-size: 14pt; font-weight: bold; }
                .info-box .value.red { color: #dc2626; }
                .info-box .value.blue { color: #2563eb; }
                .info-box .value.purple { color: #7c3aed; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
                thead th { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 10px 8px; border: 1px solid #4f46e5; font-weight: 600; }
                .footer { margin-top: 30px; text-align: right; font-size: 10pt; color: #64748b; }
                .signature { margin-top: 50px; display: flex; justify-content: flex-end; }
                .signature-box { text-align: center; min-width: 200px; }
                .signature-line { border-top: 1px solid #1e293b; margin-top: 60px; padding-top: 8px; font-size: 11pt; }
                @media print {
                    body { padding: 10px; }
                    @page { size: A4 portrait; margin: 1cm; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="../dist/img/logo-phicha.png" alt="Logo">
                <h1>📋 รายงานการหักคะแนนพฤติกรรม</h1>
                <h2>ครู ${teacherName}</h2>
                <p>${termPee} | วันที่พิมพ์: ${currentDate}</p>
            </div>
            
            <div class="info-box">
                <div>
                    <div class="label">รายการทั้งหมด</div>
                    <div class="value blue">${totalRecords} รายการ</div>
                </div>
                <div>
                    <div class="label">นักเรียน</div>
                    <div class="value purple">${uniqueStudents} คน</div>
                </div>
                <div>
                    <div class="label">รวมคะแนนหัก</div>
                    <div class="value red">-${totalDeducted} คะแนน</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">ที่</th>
                        <th style="width: 70px;">รหัส</th>
                        <th>ชื่อ-สกุล</th>
                        <th style="width: 90px;">วันที่</th>
                        <th style="width: 150px;">ประเภท</th>
                        <th>รายละเอียด</th>
                        <th style="width: 60px;">คะแนน</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows || '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">ไม่พบข้อมูล</td></tr>'}
                </tbody>
            </table>
            
            <div class="signature">
                <div class="signature-box">
                    <div class="signature-line">
                        (${teacherName})<br>
                        ครูผู้บันทึก
                    </div>
                </div>
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