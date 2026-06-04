<?php
/**
 * View: Teacher GPS Visit Home
 * Displays a map with all student locations
 */
ob_start();

// Group students by village
$groupedStudents = [];
foreach ($studentGpsList as $std) {
    $villageName = $std['village'] ?? "ไม่ระบุหมู่บ้าน/ที่อยู่";
    $groupedStudents[$villageName][] = $std;
}

// Sort villages by name (so หมู่ 1, หมู่ 2, etc. are ordered)
uksort($groupedStudents, function($a, $b) {
    $aEmpty = (strpos($a, 'ไม่ระบุ') !== false);
    $bEmpty = (strpos($b, 'ไม่ระบุ') !== false);
    if ($aEmpty && !$bEmpty) return 1;
    if (!$aEmpty && $bEmpty) return -1;
    
    preg_match('/\d+/', $a, $aNum);
    preg_match('/\d+/', $b, $bNum);
    if (isset($aNum[0]) && isset($bNum[0])) {
        if ($aNum[0] != $bNum[0]) {
            return $aNum[0] - $bNum[0];
        }
    }
    return strcasecmp($a, $b);
});

// Group students by subdistrict
$groupedSubdistricts = [];
foreach ($studentGpsList as $std) {
    $subdistrictName = $std['subdistrict'] ?? "ไม่ระบุตำบล";
    $groupedSubdistricts[$subdistrictName][] = $std;
}

// Sort subdistricts by name
uksort($groupedSubdistricts, function($a, $b) {
    $aEmpty = (strpos($a, 'ไม่ระบุ') !== false);
    $bEmpty = (strpos($b, 'ไม่ระบุ') !== false);
    if ($aEmpty && !$bEmpty) return 1;
    if (!$aEmpty && $bEmpty) return -1;
    return strcasecmp($a, $b);
});
?>

<!-- Leaflet Assets (External) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4 animate-fadeIn">
        <div class="flex items-center gap-4">
            <a href="visithome.php" class="w-12 h-12 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-md transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-map-location-dot text-blue-600"></i> แผนที่บ้านนักเรียน
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">ชั้นมัธยมศึกษาปีที่ <?= htmlspecialchars($class . "/" . $room); ?> • พบพิกัด <?= count($studentGpsList) ?> คน</p>
            </div>
        </div>
        <!-- Header Actions -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <a href="print_student_gps.php?class=<?= urlencode($class) ?>&room=<?= urlencode($room) ?>" target="_blank" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-print"></i> พิมพ์รายชื่อและพิกัด (พร้อมพิมพ์)
            </a>
        </div>
    </div>

    <!-- Map and List Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-auto lg:h-[800px] lg:max-h-[80vh]">
        <!-- Student List Sidebar -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden flex flex-col h-[500px] lg:h-full">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 relative z-20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> รายชื่อ (มีพิกัด)</h3>
                    <button id="toggleRouteMode" class="text-xs font-bold px-3 py-1.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 transition-colors flex items-center gap-1.5 border border-blue-200 dark:border-blue-800">
                        <i class="fas fa-route"></i> จัดเส้นทาง
                    </button>
                </div>
                
                <div id="routeToolbar" class="hidden flex-col gap-3 pt-2 border-t border-slate-200 dark:border-slate-700 mt-2">
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300 font-bold select-none hover:text-blue-600 transition-colors">
                            <input type="checkbox" id="selectAllStudents" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 transition-all">
                            เลือกทั้งหมด
                        </label>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">เลือก <span id="selectedCount" class="text-blue-600">0</span> คน</span>
                    </div>
                    <button id="calcRouteBtn" onclick="generateGoogleRoute()" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all opacity-50 cursor-not-allowed flex items-center justify-center gap-2" disabled>
                        <i class="fas fa-location-arrow"></i> นำทางกลุ่มที่เลือก
                    </button>
                    <p class="text-[10px] text-slate-400 text-center leading-tight">ระบบจะสร้างเส้นทาง Google Maps โดยแวะตามจุดที่เลือก (สูงสุด 9-10 จุดเพื่อความแม่นยำ)</p>
                </div>
            </div>
            
            <!-- List Mode Tabs Toggle -->
            <div class="px-3 pb-3 flex border-b border-slate-100 dark:border-slate-700 gap-1.5 relative z-20">
                <button id="showFlatList" class="flex-1 py-2 text-[11px] font-bold rounded-xl transition-all border bg-blue-600 text-white border-blue-600 shadow-md flex items-center justify-center gap-1 active:scale-[0.98]">
                    <i class="fas fa-list"></i> รายชื่อทั้งหมด
                </button>
                <button id="showSubdistrictList" class="flex-1 py-2 text-[11px] font-bold rounded-xl transition-all border bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center gap-1 active:scale-[0.98]">
                    <i class="fas fa-building-user"></i> จัดกลุ่มตำบล
                </button>
                <button id="showGroupedList" class="flex-1 py-2 text-[11px] font-bold rounded-xl transition-all border bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center gap-1 active:scale-[0.98]">
                    <i class="fas fa-folder"></i> จัดกลุ่มหมู่บ้าน
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-slate-50/30 dark:bg-slate-900/20">
                <!-- Flat List Container -->
                <div id="flatStudentList" class="space-y-2">
                    <?php if (empty($studentGpsList)): ?>
                        <div class="text-center py-8 text-slate-400">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-map-marker-alt text-2xl opacity-50"></i>
                            </div>
                            <p class="font-medium text-sm">ยังไม่มีนักเรียนบันทึกพิกัด</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($studentGpsList as $std): ?>
                            <div class="student-item-container relative group">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 z-10 hidden checkbox-wrapper">
                                    <input type="checkbox" class="student-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-5 h-5 cursor-pointer shadow-sm transition-all" value="<?= $std['Stu_id'] ?>" data-lat="<?= $std['latitude'] ?>" data-lng="<?= $std['longitude'] ?>" data-village="<?= htmlspecialchars($std['village'] ?? '') ?>" data-subdistrict="<?= htmlspecialchars($std['subdistrict'] ?? '') ?>">
                                </div>
                                
                                <button onclick="focusMap(<?= $std['latitude'] ?>, <?= $std['longitude'] ?>, '<?= $std['Stu_id'] ?>')" class="student-btn w-full text-left p-3.5 pl-4 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-750 transition-all bg-white dark:bg-slate-800 shadow-sm active:scale-[0.98] flex items-center gap-3 relative">
                                    <?php 
                                        $isFemale = (strpos($std['Stu_pre'], 'หญิง') !== false || strpos($std['Stu_pre'], 'นางสาว') !== false || strpos($std['Stu_pre'], 'ด.ญ.') !== false || strpos($std['Stu_pre'], 'น.ส.') !== false);
                                        $bgGradient = $isFemale ? 'from-rose-400 to-pink-500 dark:from-rose-500/80 dark:to-pink-600/80' : 'from-blue-400 to-indigo-500 dark:from-blue-500/80 dark:to-indigo-600/80';
                                        $initials = mb_substr($std['Stu_name'], 0, 1, 'UTF-8');
                                        $photoUrl = !empty($std['Stu_picture']) ? "../photo/" . $std['Stu_picture'] : "https://std.phichai.ac.th/photo/" . $std['Stu_id'] . ".jpg";
                                    ?>
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr <?= $bgGradient ?> text-white flex items-center justify-center font-black text-sm shadow-inner shrink-0 overflow-hidden relative student-avatar transition-transform duration-300">
                                        <span class="absolute inset-0 flex items-center justify-center"><?= $initials ?></span>
                                        <img src="<?= $photoUrl ?>" class="w-full h-full object-cover relative z-10" onerror="this.style.display='none'">
                                    </div>

                                    <div class="student-info-content transition-transform duration-300 flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 dark:text-white text-xs group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                            <?= $std['Stu_pre'] . $std['Stu_name'] . " " . $std['Stu_sur'] ?>
                                        </p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                            <span>เลขที่: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_no'] ?></strong></span>
                                            <span class="text-slate-300">|</span>
                                            <span>รหัส: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_id'] ?></strong></span>
                                        </p>
                                        <?php if (!empty($std['Stu_nick'])): ?>
                                            <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold mt-0.5">
                                                ชื่อเล่น: <?= htmlspecialchars($std['Stu_nick']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 truncate italic">
                                            <i class="fas fa-map-marker-alt text-rose-500/80 mr-0.5"></i> <?= htmlspecialchars($std['village'] ?? '') ?>
                                        </p>
                                    </div>
                                    <i class="fas fa-location-crosshairs text-slate-300 group-hover:text-blue-500 transition-colors text-base flex-shrink-0 ml-auto mr-1"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Subdistrict List Container -->
                <div id="subdistrictStudentList" class="hidden space-y-3">
                    <?php if (empty($groupedSubdistricts)): ?>
                        <div class="text-center py-8 text-slate-400">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-map-marker-alt text-2xl opacity-50"></i>
                            </div>
                            <p class="font-medium text-sm">ยังไม่มีนักเรียนบันทึกพิกัด</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($groupedSubdistricts as $subdistrict => $students): ?>
                            <div class="subdistrict-group border border-slate-100 dark:border-slate-700/80 rounded-2xl overflow-hidden bg-white dark:bg-slate-800/40 shadow-sm transition-all duration-200">
                                <!-- Subdistrict Header -->
                                <div class="subdistrict-header p-3 bg-slate-50/80 dark:bg-slate-900/40 flex items-center justify-between cursor-pointer select-none border-b border-slate-100 dark:border-slate-800 hover:bg-slate-100/50 dark:hover:bg-slate-900/60 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 chevron-icon"></i>
                                        <span class="font-bold text-xs text-slate-700 dark:text-slate-200 truncate">
                                            <?= htmlspecialchars($subdistrict) ?>
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-slate-200/80 dark:bg-slate-700/80 text-slate-600 dark:text-slate-400 rounded-full">
                                            <?= count($students) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0" onclick="event.stopPropagation()">
                                        <!-- Focus Subdistrict Bounds Button -->
                                        <button onclick="focusSubdistrict('<?= htmlspecialchars($subdistrict) ?>')" class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors" title="ดูแผนที่ตำบลนี้">
                                            <i class="fas fa-map-marked-alt text-[11px]"></i>
                                        </button>
                                        <!-- Select All in Subdistrict Checkbox (Visible only in Route Mode) -->
                                        <div class="subdistrict-checkbox-wrapper hidden flex items-center justify-center">
                                            <input type="checkbox" class="subdistrict-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-4 h-4 cursor-pointer transition-all" data-subdistrict="<?= htmlspecialchars($subdistrict) ?>" title="เลือกทั้งหมดในตำบลนี้">
                                        </div>
                                    </div>
                                </div>
                                <!-- Subdistrict Students Container -->
                                <div class="subdistrict-content p-2 space-y-2 bg-slate-50/10 dark:bg-slate-900/10">
                                    <?php foreach ($students as $std): ?>
                                        <div class="student-item-container relative group">
                                            <div class="absolute left-3 top-1/2 -translate-y-1/2 z-10 hidden checkbox-wrapper">
                                                <input type="checkbox" class="student-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-5 h-5 cursor-pointer shadow-sm transition-all" value="<?= $std['Stu_id'] ?>" data-lat="<?= $std['latitude'] ?>" data-lng="<?= $std['longitude'] ?>" data-village="<?= htmlspecialchars($std['village'] ?? '') ?>" data-subdistrict="<?= htmlspecialchars($subdistrict) ?>">
                                            </div>
                                            
                                            <button onclick="focusMap(<?= $std['latitude'] ?>, <?= $std['longitude'] ?>, '<?= $std['Stu_id'] ?>')" class="student-btn w-full text-left p-3.5 pl-4 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-750 transition-all bg-white dark:bg-slate-800 shadow-sm active:scale-[0.98] flex items-center gap-3 relative">
                                                <?php 
                                                    $isFemale = (strpos($std['Stu_pre'], 'หญิง') !== false || strpos($std['Stu_pre'], 'นางสาว') !== false || strpos($std['Stu_pre'], 'ด.ญ.') !== false || strpos($std['Stu_pre'], 'น.ส.') !== false);
                                                    $bgGradient = $isFemale ? 'from-rose-400 to-pink-500 dark:from-rose-500/80 dark:to-pink-600/80' : 'from-blue-400 to-indigo-500 dark:from-blue-500/80 dark:to-indigo-600/80';
                                                    $initials = mb_substr($std['Stu_name'], 0, 1, 'UTF-8');
                                                    $photoUrl = !empty($std['Stu_picture']) ? "../photo/" . $std['Stu_picture'] : "https://std.phichai.ac.th/photo/" . $std['Stu_id'] . ".jpg";
                                                ?>
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr <?= $bgGradient ?> text-white flex items-center justify-center font-black text-sm shadow-inner shrink-0 overflow-hidden relative student-avatar transition-transform duration-300">
                                                    <span class="absolute inset-0 flex items-center justify-center"><?= $initials ?></span>
                                                    <img src="<?= $photoUrl ?>" class="w-full h-full object-cover relative z-10" onerror="this.style.display='none'">
                                                </div>

                                                <div class="student-info-content transition-transform duration-300 flex-1 min-w-0">
                                                    <p class="font-bold text-slate-800 dark:text-white text-xs group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                                        <?= $std['Stu_pre'] . $std['Stu_name'] . " " . $std['Stu_sur'] ?>
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                                        <span>เลขที่: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_no'] ?></strong></span>
                                                        <span class="text-slate-300">|</span>
                                                        <span>รหัส: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_id'] ?></strong></span>
                                                    </p>
                                                    <?php if (!empty($std['Stu_nick'])): ?>
                                                        <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold mt-0.5">
                                                            ชื่อเล่น: <?= htmlspecialchars($std['Stu_nick']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 truncate italic">
                                                        <i class="fas fa-map-marker-alt text-rose-500/80 mr-0.5"></i> <?= htmlspecialchars($std['village'] ?? '') ?>
                                                    </p>
                                                </div>
                                                <i class="fas fa-location-crosshairs text-slate-300 group-hover:text-blue-500 transition-colors text-base flex-shrink-0 ml-auto mr-1"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Grouped List Container -->
                <div id="groupedStudentList" class="hidden space-y-3">
                    <?php if (empty($groupedStudents)): ?>
                        <div class="text-center py-8 text-slate-400">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-map-marker-alt text-2xl opacity-50"></i>
                            </div>
                            <p class="font-medium text-sm">ยังไม่มีนักเรียนบันทึกพิกัด</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($groupedStudents as $village => $students): ?>
                            <div class="village-group border border-slate-100 dark:border-slate-700/80 rounded-2xl overflow-hidden bg-white dark:bg-slate-800/40 shadow-sm transition-all duration-200">
                                <!-- Village Header -->
                                <div class="village-header p-3 bg-slate-50/80 dark:bg-slate-900/40 flex items-center justify-between cursor-pointer select-none border-b border-slate-100 dark:border-slate-800 hover:bg-slate-100/50 dark:hover:bg-slate-900/60 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 chevron-icon"></i>
                                        <span class="font-bold text-xs text-slate-700 dark:text-slate-200 truncate">
                                            <?= htmlspecialchars($village) ?>
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-slate-200/80 dark:bg-slate-700/80 text-slate-600 dark:text-slate-400 rounded-full">
                                            <?= count($students) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0" onclick="event.stopPropagation()">
                                        <!-- Focus Village Bounds Button -->
                                        <button onclick="focusVillage('<?= htmlspecialchars($village) ?>')" class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors" title="ดูแผนที่หมู่บ้านนี้">
                                            <i class="fas fa-map-marked-alt text-[11px]"></i>
                                        </button>
                                        <!-- Select All in Village Checkbox (Visible only in Route Mode) -->
                                        <div class="village-checkbox-wrapper hidden flex items-center justify-center">
                                            <input type="checkbox" class="village-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-4 h-4 cursor-pointer transition-all" data-village="<?= htmlspecialchars($village) ?>" title="เลือกทั้งหมดในหมู่บ้านนี้">
                                        </div>
                                    </div>
                                </div>
                                <!-- Village Students Container -->
                                <div class="village-content p-2 space-y-2 bg-slate-50/10 dark:bg-slate-900/10">
                                    <?php foreach ($students as $std): ?>
                                        <div class="student-item-container relative group">
                                            <div class="absolute left-3 top-1/2 -translate-y-1/2 z-10 hidden checkbox-wrapper">
                                                <input type="checkbox" class="student-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-5 h-5 cursor-pointer shadow-sm transition-all" value="<?= $std['Stu_id'] ?>" data-lat="<?= $std['latitude'] ?>" data-lng="<?= $std['longitude'] ?>" data-village="<?= htmlspecialchars($village) ?>" data-subdistrict="<?= htmlspecialchars($std['subdistrict'] ?? '') ?>">
                                            </div>
                                            
                                            <button onclick="focusMap(<?= $std['latitude'] ?>, <?= $std['longitude'] ?>, '<?= $std['Stu_id'] ?>')" class="student-btn w-full text-left p-3.5 pl-4 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-750 transition-all bg-white dark:bg-slate-800 shadow-sm active:scale-[0.98] flex items-center gap-3 relative">
                                                <?php 
                                                    $isFemale = (strpos($std['Stu_pre'], 'หญิง') !== false || strpos($std['Stu_pre'], 'นางสาว') !== false || strpos($std['Stu_pre'], 'ด.ญ.') !== false || strpos($std['Stu_pre'], 'น.ส.') !== false);
                                                    $bgGradient = $isFemale ? 'from-rose-400 to-pink-500 dark:from-rose-500/80 dark:to-pink-600/80' : 'from-blue-400 to-indigo-500 dark:from-blue-500/80 dark:to-indigo-600/80';
                                                    $initials = mb_substr($std['Stu_name'], 0, 1, 'UTF-8');
                                                    $photoUrl = !empty($std['Stu_picture']) ? "../photo/" . $std['Stu_picture'] : "https://std.phichai.ac.th/photo/" . $std['Stu_id'] . ".jpg";
                                                ?>
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr <?= $bgGradient ?> text-white flex items-center justify-center font-black text-sm shadow-inner shrink-0 overflow-hidden relative student-avatar transition-transform duration-300">
                                                    <span class="absolute inset-0 flex items-center justify-center"><?= $initials ?></span>
                                                    <img src="<?= $photoUrl ?>" class="w-full h-full object-cover relative z-10" onerror="this.style.display='none'">
                                                </div>

                                                <div class="student-info-content transition-transform duration-300 flex-1 min-w-0">
                                                    <p class="font-bold text-slate-800 dark:text-white text-xs group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                                        <?= $std['Stu_pre'] . $std['Stu_name'] . " " . $std['Stu_sur'] ?>
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                                        <span>เลขที่: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_no'] ?></strong></span>
                                                        <span class="text-slate-300">|</span>
                                                        <span>รหัส: <strong class="text-slate-600 dark:text-slate-300"><?= $std['Stu_id'] ?></strong></span>
                                                    </p>
                                                    <?php if (!empty($std['Stu_nick'])): ?>
                                                        <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold mt-0.5">
                                                            ชื่อเล่น: <?= htmlspecialchars($std['Stu_nick']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium mt-0.5 truncate italic">
                                                        <i class="fas fa-map-marker-alt text-rose-500/80 mr-0.5"></i> <?= htmlspecialchars($village) ?>
                                                    </p>
                                                </div>
                                                <i class="fas fa-location-crosshairs text-slate-300 group-hover:text-blue-500 transition-colors text-base flex-shrink-0 ml-auto mr-1"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden relative h-[500px] lg:h-full">
            <div id="visithomeMap" class="w-full h-full relative z-10" style="min-height: 500px;"></div>
            
            <!-- Floating Map Legend -->
            <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
                <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">คำอธิบาย</p>
                <div class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                    <div class="flex items-center gap-2">
                        <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" class="h-6 w-auto" alt="marker">
                        <span>ตำแหน่งบ้านนักเรียน</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let markers = {};

    function initMap() {
        const studentData = <?= json_encode($studentGpsList) ?>;
        
        if (studentData.length === 0) {
            // Default center to Thailand if no data
            map = L.map('visithomeMap').setView([15.8700, 100.9925], 6);
        } else {
            // Center to the first student initially
            map = L.map('visithomeMap').setView([studentData[0].latitude, studentData[0].longitude], 12);
        }

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const bounds = [];

        studentData.forEach(std => {
            const lat = parseFloat(std.latitude);
            const lng = parseFloat(std.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                bounds.push([lat, lng]);
                
                const popupContent = `
                    <div class="text-center p-2">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-2 text-lg font-bold">
                            ${std.Stu_no}
                        </div>
                        <h4 class="font-bold text-sm text-slate-800 mb-1">${std.Stu_pre}${std.Stu_name} ${std.Stu_sur}</h4>
                        <p class="text-xs text-slate-500 mb-3">รหัส: ${std.Stu_id}</p>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors shadow-md">
                            <i class="fas fa-location-arrow"></i> นำทาง
                        </a>
                    </div>
                `;

                // Default blue marker
                const defaultIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                const marker = L.marker([lat, lng], {icon: defaultIcon}).bindPopup(popupContent).addTo(map);
                markers[std.Stu_id] = marker;
            }
        });

        // Fit map to show all markers
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    function focusMap(lat, lng, stuId) {
        if (map) {
            map.setView([lat, lng], 18, { animate: true, duration: 1 });
            if (markers[stuId]) {
                setTimeout(() => {
                    markers[stuId].openPopup();
                }, 500);
            }
        }
    }

    function focusVillage(villageName) {
        const studentData = <?= json_encode($studentGpsList) ?>;
        const bounds = [];
        studentData.forEach(std => {
            if (std.village === villageName) {
                const lat = parseFloat(std.latitude);
                const lng = parseFloat(std.longitude);
                if (!isNaN(lat) && !isNaN(lng)) {
                    bounds.push([lat, lng]);
                }
            }
        });
        
        if (bounds.length > 0 && map) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    function focusSubdistrict(subdistrictName) {
        const studentData = <?= json_encode($studentGpsList) ?>;
        const bounds = [];
        studentData.forEach(std => {
            if (std.subdistrict === subdistrictName) {
                const lat = parseFloat(std.latitude);
                const lng = parseFloat(std.longitude);
                if (!isNaN(lat) && !isNaN(lng)) {
                    bounds.push([lat, lng]);
                }
            }
        });
        
        if (bounds.length > 0 && map) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    // --- Route Mode Logic ---
    let isRouteMode = false;
    
    function updateRouteUI() {
        const checkedBoxes = $('.student-checkbox:checked');
        const count = checkedBoxes.length;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#calcRouteBtn').removeClass('opacity-50 cursor-not-allowed').removeAttr('disabled');
        } else {
            $('#calcRouteBtn').addClass('opacity-50 cursor-not-allowed').attr('disabled', true);
        }

        // Highlight markers
        const greenIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        const blueIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Reset all to blue first
        Object.keys(markers).forEach(id => {
            markers[id].setIcon(blueIcon);
            markers[id].setOpacity(isRouteMode ? 0.4 : 1); // Dim unselected in route mode
        });

        // Set checked to green
        if (isRouteMode) {
            checkedBoxes.each(function() {
                const id = $(this).val();
                if (markers[id]) {
                    markers[id].setIcon(greenIcon);
                    markers[id].setOpacity(1);
                }
            });
        }
    }

    function generateGoogleRoute() {
        const checkedBoxes = $('#flatStudentList .student-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const points = [];
        checkedBoxes.each(function() {
            points.push({
                lat: $(this).data('lat'),
                lng: $(this).data('lng')
            });
        });

        // Google Maps URL logic: Origin (current location ideally, but we'll leave it blank so Maps asks user), 
        // Destination (last point), Waypoints (middle points)
        const destination = points[points.length - 1];
        
        let url = `https://www.google.com/maps/dir/?api=1&destination=${destination.lat},${destination.lng}`;
        
        if (points.length > 1) {
            // Add waypoints
            const waypoints = points.slice(0, -1).map(p => `${p.lat},${p.lng}`).join('|');
            url += `&waypoints=${waypoints}`;
        }
        
        window.open(url, '_blank');
    }

    function updateVillageCheckboxes() {
        $('.village-checkbox').each(function() {
            const village = $(this).data('village');
            const total = $(`.student-checkbox[data-village="${village}"]`).length;
            const checked = $(`.student-checkbox[data-village="${village}"]:checked`).length;
            $(this).prop('checked', total > 0 && checked === total);
        });
    }

    function updateSubdistrictCheckboxes() {
        $('.subdistrict-checkbox').each(function() {
            const subdistrict = $(this).data('subdistrict');
            const total = $(`.student-checkbox[data-subdistrict="${subdistrict}"]`).length;
            const checked = $(`.student-checkbox[data-subdistrict="${subdistrict}"]:checked`).length;
            $(this).prop('checked', total > 0 && checked === total);
        });
    }

    function updateAllGroupCheckboxes() {
        updateVillageCheckboxes();
        updateSubdistrictCheckboxes();
    }

    $(document).ready(function() {
        initMap();
        
        // Tab Toggles
        $('#showFlatList').on('click', function() {
            // Activate Flat button
            $(this).removeClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                   .addClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Deactivate others
            $('#showSubdistrictList, #showGroupedList').addClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                                                     .removeClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Show list
            $('#flatStudentList').removeClass('hidden');
            $('#subdistrictStudentList, #groupedStudentList').addClass('hidden');
        });

        $('#showSubdistrictList').on('click', function() {
            // Activate Subdistrict button
            $(this).removeClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                   .addClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Deactivate others
            $('#showFlatList, #showGroupedList').addClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                                               .removeClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Show list
            $('#subdistrictStudentList').removeClass('hidden');
            $('#flatStudentList, #groupedStudentList').addClass('hidden');
        });

        $('#showGroupedList').on('click', function() {
            // Activate Grouped button
            $(this).removeClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                   .addClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Deactivate others
            $('#showFlatList, #showSubdistrictList').addClass('bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700')
                                               .removeClass('bg-blue-600 text-white border-blue-600 shadow-md');
            
            // Show list
            $('#groupedStudentList').removeClass('hidden');
            $('#flatStudentList, #subdistrictStudentList').addClass('hidden');
        });

        // Toggle Village Accordion
        $(document).on('click', '.village-header', function() {
            const content = $(this).next('.village-content');
            const icon = $(this).find('.chevron-icon');
            
            content.slideToggle(200);
            icon.toggleClass('rotate-[-90deg]');
        });

        // Toggle Subdistrict Accordion
        $(document).on('click', '.subdistrict-header', function() {
            const content = $(this).next('.subdistrict-content');
            const icon = $(this).find('.chevron-icon');
            
            content.slideToggle(200);
            icon.toggleClass('rotate-[-90deg]');
        });

        // Toggle Route Mode
        $('#toggleRouteMode').on('click', function() {
            isRouteMode = !isRouteMode;
            
            if (isRouteMode) {
                $(this).removeClass('bg-blue-100 text-blue-700').addClass('bg-emerald-600 text-white border-emerald-600');
                $('#routeToolbar').removeClass('hidden').addClass('flex');
                $('.checkbox-wrapper').removeClass('hidden');
                $('.village-checkbox-wrapper, .subdistrict-checkbox-wrapper').removeClass('hidden');
                $('.student-btn').addClass('pl-11');
            } else {
                $(this).addClass('bg-blue-100 text-blue-700').removeClass('bg-emerald-600 text-white border-emerald-600');
                $('#routeToolbar').addClass('hidden').removeClass('flex');
                $('.checkbox-wrapper').addClass('hidden');
                $('.village-checkbox-wrapper, .subdistrict-checkbox-wrapper').addClass('hidden');
                $('.student-btn').removeClass('pl-11');
                
                // Uncheck all
                $('.student-checkbox').prop('checked', false);
                $('.village-checkbox').prop('checked', false);
                $('.subdistrict-checkbox').prop('checked', false);
                $('#selectAllStudents').prop('checked', false);
            }
            
            updateRouteUI();
        });

        // Checkbox events
        $(document).on('change', '.student-checkbox', function() {
            const stuId = $(this).val();
            const isChecked = $(this).prop('checked');
            // Sync with other instances of the student checkbox
            $(`.student-checkbox[value="${stuId}"]`).not(this).prop('checked', isChecked);
            
            updateRouteUI();
            
            // Check if all are checked
            const total = $('#flatStudentList .student-checkbox').length;
            const checked = $('#flatStudentList .student-checkbox:checked').length;
            $('#selectAllStudents').prop('checked', total > 0 && checked === total);

            // Update group checkbox states
            updateAllGroupCheckboxes();
        });

        // Select All event
        $('#selectAllStudents').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.student-checkbox').prop('checked', isChecked);
            updateRouteUI();
            updateAllGroupCheckboxes();
        });

        // Village Checkbox event
        $(document).on('change', '.village-checkbox', function() {
            const village = $(this).data('village');
            const isChecked = $(this).prop('checked');
            
            $(`.student-checkbox[data-village="${village}"]`).prop('checked', isChecked).trigger('change');
        });

        // Subdistrict Checkbox event
        $(document).on('change', '.subdistrict-checkbox', function() {
            const subdistrict = $(this).data('subdistrict');
            const isChecked = $(this).prop('checked');
            
            $(`.student-checkbox[data-subdistrict="${subdistrict}"]`).prop('checked', isChecked).trigger('change');
        });
        
        // Ensure map resizes correctly
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 300);
    });
</script>

<style>
    /* Fix Tailwind CSS conflict with Leaflet Images */
    .leaflet-container img {
        max-width: none !important;
        max-height: none !important;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        border: 1px solid #f1f5f9;
    }
    .leaflet-popup-content {
        margin: 0;
        min-width: 200px;
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
