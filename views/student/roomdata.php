<?php
/**
 * View: Student Roomdata
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design - Cards for classmates
 */
ob_start();
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-teal-600 via-cyan-600 to-blue-600 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-users text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">ข้อมูลห้องเรียน</h1>
                        <p class="text-cyan-200 font-bold">ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?> • ปี <?= $pee ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-user-friends text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black text-blue-600"><?= $totalStudents ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">ทั้งหมด</p>
        </div>
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-mars text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black text-sky-600"><?= $maleCount ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">ชาย</p>
        </div>
        <div class="glass-effect rounded-2xl p-4 md:p-5 border border-white/50 shadow-lg text-center group hover:scale-105 transition-all">
            <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-2 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-venus text-lg md:text-xl"></i>
            </div>
            <p class="text-2xl md:text-3xl font-black text-pink-600"><?= $femaleCount ?></p>
            <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">หญิง</p>
        </div>
    </div>

    <!-- Search Box -->
    <div class="glass-effect rounded-2xl p-4 border border-white/50 shadow-lg">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input type="text" id="studentSearch" 
                   class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-base dark:text-white focus:border-teal-400 focus:ring-4 focus:ring-teal-400/20 transition-all" 
                   placeholder="ค้นหาชื่อ, รหัส, เลขที่ หรือชื่อเล่น...">
        </div>
    </div>

    <!-- Students List -->
    <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-list-ol text-xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">รายชื่อนักเรียน</h3>
                    <p class="text-[10px] font-bold text-teal-200 uppercase tracking-widest"><?= $totalStudents ?> คน</p>
                </div>
            </div>
        </div>

        <?php if ($totalStudents > 0): ?>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">เลขที่</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">รูป</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัส</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อเล่น</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="desktopList">
                    <?php foreach ($classmates as $item): 
                        $isMale = in_array($item['Stu_pre'], ['นาย', 'เด็กชาย']);
                        $genderColor = $isMale ? 'sky' : 'pink';
                        $imgUrl = 'https://std.phichai.ac.th/photo/' . $item['Stu_picture'];
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors student-row"
                        data-name="<?= htmlspecialchars(strtolower($item['Stu_pre'] . $item['Stu_name'] . ' ' . $item['Stu_sur'])) ?>"
                        data-id="<?= htmlspecialchars($item['Stu_id']) ?>"
                        data-no="<?= htmlspecialchars($item['Stu_no']) ?>"
                        data-nick="<?= htmlspecialchars(strtolower($item['Stu_nick'] ?? '')) ?>">
                        <td class="px-4 py-3 text-center">
                            <span class="w-10 h-10 inline-flex items-center justify-center rounded-xl font-black text-white text-sm" style="background: linear-gradient(135deg, <?= $isMale ? '#0ea5e9' : '#ec4899' ?>, <?= $isMale ? '#3b82f6' : '#f43f5e' ?>);">
                                <?= htmlspecialchars($item['Stu_no']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <img src="<?= $imgUrl ?>" alt="" class="w-14 h-14 rounded-xl object-cover mx-auto border-2" style="border-color: <?= $isMale ? '#0ea5e9' : '#ec4899' ?>;" onerror="this.src='../dist/img/default-avatar.svg'">
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($item['Stu_pre'] . $item['Stu_name'] . ' ' . $item['Stu_sur']) ?></p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300"><?= htmlspecialchars($item['Stu_id']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold" style="background: <?= $isMale ? '#e0f2fe' : '#fce7f3' ?>; color: <?= $isMale ? '#0369a1' : '#be185d' ?>;">
                                <?= htmlspecialchars($item['Stu_nick'] ?? '-') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if (!empty($item['Stu_phone'])): ?>
                            <a href="tel:<?= htmlspecialchars($item['Stu_phone']) ?>" class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-xs font-bold hover:bg-emerald-200 transition">
                                <i class="fas fa-phone mr-1"></i><?= htmlspecialchars($item['Stu_phone']) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden p-3 space-y-3" id="mobileList">
            <?php foreach ($classmates as $item): 
                $isMale = in_array($item['Stu_pre'], ['นาย', 'เด็กชาย']);
                $gradientBg = $isMale ? 'from-sky-500 to-blue-600' : 'from-pink-500 to-rose-600';
                $borderColor = $isMale ? '#0ea5e9' : '#ec4899';
                $lightBg = $isMale ? '#e0f2fe' : '#fce7f3';
                $textColor = $isMale ? '#0369a1' : '#be185d';
                $imgUrl = 'https://std.phichai.ac.th/photo/' . $item['Stu_picture'];
            ?>
            <div class="student-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border-l-4 transition-all hover:shadow-xl" style="border-color: <?= $borderColor ?>;"
                 data-name="<?= htmlspecialchars(strtolower($item['Stu_pre'] . $item['Stu_name'] . ' ' . $item['Stu_sur'])) ?>"
                 data-id="<?= htmlspecialchars($item['Stu_id']) ?>"
                 data-no="<?= htmlspecialchars($item['Stu_no']) ?>"
                 data-nick="<?= htmlspecialchars(strtolower($item['Stu_nick'] ?? '')) ?>">
                <div class="flex p-4 gap-4">
                    <!-- Avatar & Number -->
                    <div class="relative flex-shrink-0">
                        <img src="<?= $imgUrl ?>" alt="" class="w-20 h-20 rounded-xl object-cover border-2" style="border-color: <?= $borderColor ?>;" onerror="this.src='../dist/img/default-avatar.svg'">
                        <span class="absolute -top-2 -left-2 w-7 h-7 flex items-center justify-center rounded-lg text-white text-xs font-black shadow-lg bg-gradient-to-br <?= $gradientBg ?>">
                            <?= htmlspecialchars($item['Stu_no']) ?>
                        </span>
                    </div>
                    
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-black text-slate-800 dark:text-white text-base mb-1 truncate">
                            <?= htmlspecialchars($item['Stu_pre'] . $item['Stu_name'] . ' ' . $item['Stu_sur']) ?>
                        </h4>
                        
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($item['Stu_id']) ?>
                            </span>
                            <?php if (!empty($item['Stu_nick'])): ?>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold" style="background: <?= $lightBg ?>; color: <?= $textColor ?>;">
                                "<?= htmlspecialchars($item['Stu_nick']) ?>"
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($item['Stu_phone'])): ?>
                        <a href="tel:<?= htmlspecialchars($item['Stu_phone']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-xs font-bold">
                            <i class="fas fa-phone"></i>
                            <?= htmlspecialchars($item['Stu_phone']) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
            </div>
            <p class="text-slate-500 font-bold text-lg">ไม่พบข้อมูลนักเรียน</p>
            <p class="text-slate-400 text-sm mt-2">ยังไม่มีข้อมูลนักเรียนในห้องนี้</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const desktopRows = document.querySelectorAll('.student-row');
    const mobileCards = document.querySelectorAll('.student-card');
    
    searchInput.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        
        // Filter desktop rows
        desktopRows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const id = row.getAttribute('data-id') || '';
            const no = row.getAttribute('data-no') || '';
            const nick = row.getAttribute('data-nick') || '';
            
            if (name.includes(val) || id.includes(val) || no.includes(val) || nick.includes(val)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Filter mobile cards
        mobileCards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const id = card.getAttribute('data-id') || '';
            const no = card.getAttribute('data-no') || '';
            const nick = card.getAttribute('data-nick') || '';
            
            if (name.includes(val) || id.includes(val) || no.includes(val) || nick.includes(val)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
