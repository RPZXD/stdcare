<?php
/**
 * View: Student Search Data
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-600 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-search text-4xl text-white"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-white mb-2">ค้นหาข้อมูล</h1>
                <p class="text-purple-200 font-medium text-sm md:text-base">
                    พิมพ์ <span class="font-bold text-white">เลขประจำตัว</span>, <span class="font-bold text-white">ชื่อ</span> หรือ <span class="font-bold text-white">นามสกุล</span> เพื่อค้นหา
                </p>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="glass-effect rounded-[2rem] p-5 md:p-8 border border-white/50 shadow-xl">
        <form id="searchForm" class="space-y-4">
            <!-- Type Select & Search Input -->
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Type Dropdown -->
                <div class="w-full md:w-48">
                    <select name="type" id="type" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-base font-bold dark:text-white focus:border-purple-400 focus:ring-4 focus:ring-purple-400/20 transition-all">
                        <option value="student">🎓 นักเรียน</option>
                        <option value="teacher">👨‍🏫 ครู</option>
                    </select>
                </div>
                
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="search" name="search" id="search" 
                           class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-base dark:text-white focus:border-purple-400 focus:ring-4 focus:ring-purple-400/20 transition-all" 
                           placeholder="กรอกชื่อ หรือนามสกุลที่ต้องการค้นหา...">
                </div>
                
                <!-- Search Button -->
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>ค้นหา</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Container -->
    <div id="resultContainer" class="min-h-[200px]">
        <!-- Initial State -->
        <div id="initialState" class="glass-effect rounded-[2rem] p-10 border border-white/50 shadow-xl text-center">
            <div class="w-24 h-24 mx-auto mb-4 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-4xl text-purple-400"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">เริ่มค้นหา</p>
            <p class="text-slate-400 text-sm mt-2">พิมพ์คำค้นหาด้านบนเพื่อดูผลลัพธ์</p>
        </div>

        <!-- Results will be populated here -->
        <div id="resultsGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        </div>

        <!-- No Results State -->
        <div id="noResults" class="hidden glass-effect rounded-[2rem] p-10 border border-white/50 shadow-xl text-center">
            <div class="w-24 h-24 mx-auto mb-4 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-4xl text-amber-400"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">ไม่พบข้อมูล</p>
            <p class="text-slate-400 text-sm mt-2">ลองค้นหาด้วยคำอื่น</p>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden glass-effect rounded-[2rem] p-10 border border-white/50 shadow-xl text-center">
            <div class="w-16 h-16 mx-auto mb-4 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
            <p class="text-slate-500 dark:text-slate-400 font-bold">กำลังค้นหา...</p>
        </div>
    </div>
</div>

<script>
const imgProfileStudent = '<?= htmlspecialchars($imgProfileStudent) ?>';
const imgProfile = '<?= htmlspecialchars($imgProfile) ?>';

// Thai date formatter
function formatThaiDate(dateStr) {
    if (!dateStr) return '-';
    const months = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    let [year, month, day] = parts;
    return `${parseInt(day)} ${months[parseInt(month)]} ${parseInt(year)}`;
}

$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        const type = $('#type').val();
        const search = $('#search').val().trim();
        
        if (!search) {
            Swal.fire('กรุณากรอกคำค้นหา', '', 'warning');
            return;
        }
        
        // Show loading
        $('#initialState, #resultsGrid, #noResults').addClass('hidden');
        $('#loadingState').removeClass('hidden');
        
        $.ajax({
            url: 'api/search_data.php',
            method: 'POST',
            data: { type, search },
            dataType: 'json',
            success: function(response) {
                $('#loadingState').addClass('hidden');
                
                if (response && response.length > 0) {
                    let html = '';
                    
                    response.forEach(item => {
                        if (type === 'student') {
                            const isMale = ['นาย', 'เด็กชาย'].includes(item.Stu_pre);
                            const borderColor = isMale ? '#0ea5e9' : '#ec4899';
                            const gradientBg = isMale ? 'from-sky-500 to-blue-600' : 'from-pink-500 to-rose-600';
                            
                            html += `
                                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border-t-4 hover:shadow-xl transition-all" style="border-color: ${borderColor};">
                                    <div class="p-4">
                                        <div class="flex items-center gap-4 mb-4">
                                            <img src="${imgProfileStudent}${item.Stu_picture}" 
                                                 class="w-20 h-20 rounded-xl object-cover border-2" 
                                                 style="border-color: ${borderColor};"
                                                 onerror="this.src='../dist/img/default-avatar.svg'"
                                                 alt="">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-black text-slate-800 dark:text-white truncate">
                                                    ${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}
                                                </h4>
                                                <p class="text-sm text-slate-500">${item.Stu_id}</p>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                                                        ม.${item.Stu_major}/${item.Stu_room}
                                                    </span>
                                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 rounded text-xs font-bold text-purple-600">
                                                        เลขที่ ${item.Stu_no}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        ${item.Stu_nick ? `
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-2">
                                            <i class="fas fa-user-tag text-purple-400"></i>
                                            <span>ชื่อเล่น: <b class="text-purple-600">${item.Stu_nick}</b></span>
                                        </div>` : ''}
                                        ${item.Stu_phone ? `
                                        <a href="tel:${item.Stu_phone}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-xs font-bold">
                                            <i class="fas fa-phone"></i> ${item.Stu_phone}
                                        </a>` : ''}
                                    </div>
                                </div>`;
                        } else if (type === 'teacher') {
                            const thaiBirth = formatThaiDate(item.Teach_birth);
                            
                            html += `
                                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all">
                                    <div class="bg-gradient-to-r from-slate-700 to-slate-800 p-4 text-center">
                                        <span class="text-2xl">👩‍🏫</span>
                                        <p class="text-white font-bold text-sm mt-1">ข้อมูลครู</p>
                                    </div>
                                    <div class="p-4">
                                        <div class="text-center mb-4">
                                            <img src="${imgProfile}${item.Teach_photo}" 
                                                 class="w-24 h-24 rounded-xl object-cover mx-auto border-2 border-slate-300"
                                                 onerror="this.src='../dist/img/default-avatar.svg'"
                                                 alt="">
                                            <h4 class="font-black text-slate-800 dark:text-white mt-3">${item.Teach_name}</h4>
                                            <span class="inline-block px-3 py-1 mt-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-full text-xs font-bold">
                                                ${item.Teach_major || '-'}
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                                <span class="w-6 text-center">🚻</span>
                                                <span class="text-slate-600 dark:text-slate-300">${item.Teach_sex || '-'}</span>
                                            </div>
                                            <div class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                                <span class="w-6 text-center">🎂</span>
                                                <span class="text-slate-600 dark:text-slate-300">${thaiBirth}</span>
                                            </div>
                                            <div class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                                <span class="w-6 text-center">📚</span>
                                                <span class="text-slate-600 dark:text-slate-300">ม.${item.Teach_class || '-'}/${item.Teach_room || '-'}</span>
                                            </div>
                                            ${item.Teach_phone ? `
                                            <a href="tel:${item.Teach_phone}" class="flex items-center gap-2 p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-600 font-bold">
                                                <span class="w-6 text-center">📞</span>
                                                <span>${item.Teach_phone}</span>
                                            </a>` : ''}
                                        </div>
                                    </div>
                                </div>`;
                        }
                    });
                    
                    $('#resultsGrid').html(html).removeClass('hidden').addClass('grid');
                } else {
                    $('#noResults').removeClass('hidden');
                }
            },
            error: function() {
                $('#loadingState').addClass('hidden');
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถค้นหาข้อมูลได้', 'error');
            }
        });
    });

    // Autocomplete
    $('#search').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'api/search_autocomplete.php',
                method: 'GET',
                dataType: 'json',
                data: { term: request.term, type: $('#type').val() },
                success: function(data) { response(data); }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('#search').val(ui.item.label);
            return false;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
