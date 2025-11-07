<?php

require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php"); // (ยังใช้ UserLogin ตัวเก่า)
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)


// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();


if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ข้อมูลครู 👨‍🏫</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <!-- Search Filter -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" id="teacherSearch" placeholder="ค้นหาครู... 🔍" class="w-full px-4 py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-md transition-all duration-200 text-center">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-lg">🔍</span>
                                </div>
                            </div>
                        </div>
                        <div id="loading" class="text-center py-8 text-lg font-semibold text-gray-600">
                            กำลังโหลดข้อมูลครู... ⏳
                        </div>
                        <div id="teacherContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
                    </div>
                </div>
            </div>
        </section>


<style>
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
.animate-fade-in {
    animation: fadeInUp 0.6s ease-out forwards;
}
.teacher-card {
    transition: all 0.3s ease;
}
.teacher-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
.zoomable-avatar {
    cursor: pointer;
    transition: transform 0.18s ease;
}
.zoomable-avatar:hover {
    transform: scale(1.04);
}
</style>

<script>
        // API URL for reading teacher data only
        const API_URL = '../controllers/TeacherController.php';

        let allTeachers = [];

        function handleImageError(img, fallbackText) {
            img.style.display = 'none';
            const container = img.parentElement;
            container.innerHTML = '👨‍🏫';
            container.classList.add('flex', 'items-center', 'justify-center');
        }

        function renderTeachers(data, filterText = '') {
            $('#teacherContainer').empty();
            $('#loading').hide();
            
            let filteredData = data;
            if (filterText) {
                filteredData = data.filter(teacher => {
                    let roleText = '';
                    switch(teacher.role_std) {
                        case 'T': roleText = 'ครู'; break;
                        case 'OF': roleText = 'เจ้าหน้าที่'; break;
                        case 'VP': roleText = 'รองผู้อำนวยการ'; break;
                        case 'DIR': roleText = 'ผู้อำนวยการ'; break;
                        case 'ADM': roleText = 'Admin'; break;
                        default: roleText = teacher.role_std;
                    }
                    return teacher.Teach_name.toLowerCase().includes(filterText.toLowerCase()) ||
                           teacher.Teach_major.toLowerCase().includes(filterText.toLowerCase()) ||
                           roleText.toLowerCase().includes(filterText.toLowerCase());
                });
            }
            
            filteredData.forEach((teacher, index) => {
                let statusBadge = teacher.Teach_status == '1' ? 
                    '<span class="inline-block bg-gradient-to-r from-green-400 to-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">ปกติ ✅</span>' : 
                    '<span class="inline-block bg-gradient-to-r from-red-400 to-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">ไม่ใช้งาน ❌</span>';
                
                let roleText = '';
                let roleEmoji = '';
                switch(teacher.role_std) {
                    case 'T': roleText = 'ครู'; roleEmoji = '👨‍🏫'; break;
                    case 'OF': roleText = 'เจ้าหน้าที่'; roleEmoji = '👔'; break;
                    case 'VP': roleText = 'รองผู้อำนวยการ'; roleEmoji = '👨‍💼'; break;
                    case 'DIR': roleText = 'ผู้อำนวยการ'; roleEmoji = '🏫'; break;
                    case 'ADM': roleText = 'Admin'; roleEmoji = '⚙️'; break;
                    default: roleText = teacher.role_std; roleEmoji = '👤';
                }
                
                let classRoom = teacher.Teach_class && teacher.Teach_room ? `ม.${teacher.Teach_class}/${teacher.Teach_room}` : 'ไม่มี';
                
                let photoUrl = teacher.Teach_photo ? `https://std.phichai.ac.th/teacher/uploads/phototeach/${teacher.Teach_photo}` : '';
                
                let card = `
                    <div class="teacher-card bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6 rounded-2xl shadow-lg border border-gray-200 hover:border-indigo-300 transition-all duration-300 animate-fade-in" style="animation-delay: ${index * 0.1}s;">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-2xl mr-4 shadow-lg ring-4 ring-indigo-200 relative overflow-hidden">
                                ${photoUrl ? `<img src="${photoUrl}" alt="${teacher.Teach_name}" class="w-full h-full rounded-full object-cover zoomable-avatar" onerror="handleImageError(this, '${teacher.Teach_name}')" data-fullsrc="${photoUrl}">` : roleEmoji}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-1">${teacher.Teach_name}</h3>
                                <p class="text-sm text-indigo-600 font-medium">${teacher.Teach_major} 📚</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="font-semibold text-gray-700 mr-2">🏫 ครูที่ปรึกษา:</span>
                                <span class="text-gray-600">${classRoom}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="font-semibold text-gray-700 mr-2">📊 สถานะ:</span>
                                ${statusBadge}
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="font-semibold text-gray-700 mr-2">🎭 บทบาท:</span>
                                <span class="text-gray-600">${roleText} ${roleEmoji}</span>
                            </div>
                        </div>
                    </div>
                `;
                $('#teacherContainer').append(card);
            });
            
            if (filteredData.length === 0 && filterText) {
                $('#teacherContainer').html('<div class="col-span-full text-center py-8 text-gray-500 text-lg">ไม่พบครูที่ตรงกับการค้นหา �</div>');
            }
        }

        function loadTeachers() {
            $('#loading').show();
            $('#teacherContainer').empty();
            $.ajax({
                url: API_URL + "?action=list",
                method: 'GET',
                success: function(data) {
                    allTeachers = data;
                    renderTeachers(data);
                },
                error: function() {
                    $('#loading').html('<p class="text-red-500">เกิดข้อผิดพลาดในการโหลดข้อมูล 😞</p>');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadTeachers();

            // Search functionality
            $('#teacherSearch').on('input', function() {
                const searchText = $(this).val();
                renderTeachers(allTeachers, searchText);
            });

            // Avatar click -> open modal with large image
            $(document).on('click', '.zoomable-avatar', function(e) {
                e.preventDefault();
                var src = $(this).data('fullsrc') || $(this).attr('src');
                var alt = $(this).attr('alt') || '';

                // remove any existing modal placeholder
                $('#avatarModal').remove();

                var modalHtml = `
                    <div id="avatarModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content bg-transparent border-0">
                                <div class="modal-body text-center p-0">
                                    <button type="button" class="close modal-close p-2" data-dismiss="modal" aria-label="Close" style="position:absolute; right:8px; top:8px; z-index:1052; background: rgba(255,255,255,0.8); border-radius:50%;">&times;</button>
                                    <img src="${src}" alt="${alt}" style="max-width:90vw; max-height:90vh; border-radius:8px; box-shadow:0 18px 40px rgba(0,0,0,0.45);">
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                var $modal = $(modalHtml);
                $modal.appendTo('body');
                $modal.modal('show');
                $modal.on('hidden.bs.modal', function() { $(this).remove(); });
            });
        });
</script>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>