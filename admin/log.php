<?php
// เปลี่ยนไปใช้คลาสเชื่อมต่อฐานข้อมูลตัวใหม่
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;

include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection (ใช้คลาสใหม่)
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary': '#3B82F6',
                    'secondary': '#6B7280',
                    'success': '#10B981',
                    'warning': '#F59E0B',
                    'danger': '#EF4444',
                    'info': '#06B6D4'
                }
            }
        }
    }
</script>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper">
        <section class="content-header bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 py-8 shadow-lg">
            <div class="container-fluid">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full backdrop-blur-sm">
                            <i class="fas fa-history text-3xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">📊 ประวัติการใช้งาน (Logs)</h1>
                            <p class="text-blue-100">ติดตามและตรวจสอบกิจกรรมของระบบ</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-blue-100 text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content py-8">
            <div class="container-fluid space-y-6">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform hover:scale-[1.02] transition-all duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-filter mr-3"></i>
                            🎯 ตัวกรองข้อมูล
                        </h3>
                        <p class="text-blue-100 mt-1">ค้นหาและกรองข้อมูลประวัติการใช้งาน</p>
                    </div>
                    <div class="p-6">
                        <form id="logFilterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="user_id" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-user mr-2 text-blue-500"></i>
                                    User ID
                                </label>
                                <input type="text" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-gray-50 hover:bg-white" id="user_id" placeholder="🔍 รหัสผู้ใช้">
                            </div>
                            <div class="space-y-2">
                                <label for="role" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-user-tag mr-2 text-purple-500"></i>
                                    Role
                                </label>
                                <input type="text" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-gray-50 hover:bg-white" id="role" placeholder="👤 บทบาท">
                            </div>
                            <div class="space-y-2">
                                <label for="action" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                                    Action
                                </label>
                                <select id="action" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-200 bg-gray-50 hover:bg-white">
                                    <option value="">🎪 ทั้งหมด</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                    Status
                                </label>
                                <select id="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-gray-50 hover:bg-white">
                                    <option value="">📊 ทั้งหมด</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="date_from" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                                    จากวันที่
                                </label>
                                <input type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 bg-gray-50 hover:bg-white" id="date_from">
                            </div>
                            <div class="space-y-2">
                                <label for="date_to" class="block text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-calendar-check mr-2 text-pink-500"></i>
                                    ถึงวันที่
                                </label>
                                <input type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-100 transition-all duration-200 bg-gray-50 hover:bg-white" id="date_to">
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 flex justify-center pt-4">
                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center space-x-3">
                                    <i class="fas fa-search"></i>
                                    <span>🔍 ค้นหา</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform hover:scale-[1.01] transition-all duration-300">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-table mr-3"></i>
                            📋 ข้อมูลประวัติการใช้งาน
                        </h3>
                        <p class="text-green-100 mt-1">แสดงรายการกิจกรรมทั้งหมดในระบบ</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table id="logTable" class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-gray-800"></th>
                                        <th class="px-6 py-4 font-bold text-gray-800">
                                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                                            วันที่-เวลา
                                        </th>
                                        <th class="px-6 py-4 font-bold text-gray-800">
                                            <i class="fas fa-user mr-2 text-purple-500"></i>
                                            ผู้ใช้งาน
                                        </th>
                                        <th class="px-6 py-4 font-bold text-gray-800">
                                            <i class="fas fa-user-tag mr-2 text-indigo-500"></i>
                                            บทบาท
                                        </th>
                                        <th class="px-6 py-4 font-bold text-gray-800">
                                            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                                            การกระทำ
                                        </th>
                                        <th class="px-6 py-4 font-bold text-gray-800">
                                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                            สถานะ
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    
    // โหลดค่าฟิลเตอร์จาก API
    function loadFilters() {
        $.getJSON('api/log_filters.php')
            .done(function(data) {
                if (data.success) {
                    // เพิ่มตัวเลือก Action
                    const actionSelect = $('#action');
                    actionSelect.empty();
                    actionSelect.append('<option value="">🎪 ทั้งหมด</option>');
                    data.actions.forEach(function(action) {
                        actionSelect.append(`<option value="${action.value}">${action.label}</option>`);
                    });

                    // เพิ่มตัวเลือก Status
                    const statusSelect = $('#status');
                    statusSelect.empty();
                    statusSelect.append('<option value="">📊 ทั้งหมด</option>');
                    data.statuses.forEach(function(status) {
                        statusSelect.append(`<option value="${status.value}">${status.label}</option>`);
                    });
                }
            })
            .fail(function() {
                console.error('Failed to load filters');
            });
    }

    // โหลดฟิลเตอร์เมื่อหน้าโหลดเสร็จ
    loadFilters();
    
    // ฟังก์ชันสำหรับสร้าง Child Row (รายละเอียดที่ซ่อนไว้)
    function formatDetails(d) {
        return '<div class="bg-gradient-to-r from-gray-50 to-blue-50 p-6 rounded-xl border-l-4 border-blue-500 shadow-inner">' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">' +
                '<div class="space-y-3">' +
                    '<div class="flex items-center space-x-3">' +
                        '<i class="fas fa-comment text-blue-500"></i>' +
                        '<span class="font-semibold text-gray-700">Message:</span>' +
                    '</div>' +
                    '<p class="text-gray-600 bg-white p-3 rounded-lg border">' + (d.message || '📝 ไม่มีข้อมูล') + '</p>' +
                '</div>' +
                '<div class="space-y-3">' +
                    '<div class="flex items-center space-x-3">' +
                        '<i class="fas fa-globe text-green-500"></i>' +
                        '<span class="font-semibold text-gray-700">IP Address:</span>' +
                    '</div>' +
                    '<p class="text-gray-600 bg-white p-3 rounded-lg border font-mono">' + (d.ip || '🌐 ไม่มีข้อมูล') + '</p>' +
                '</div>' +
                '<div class="space-y-3">' +
                    '<div class="flex items-center space-x-3">' +
                        '<i class="fas fa-mobile-alt text-purple-500"></i>' +
                        '<span class="font-semibold text-gray-700">User Agent:</span>' +
                    '</div>' +
                    '<p class="text-gray-600 bg-white p-3 rounded-lg border text-xs break-all">' + (d.user_agent || '📱 ไม่มีข้อมูล') + '</p>' +
                '</div>' +
                '<div class="space-y-3">' +
                    '<div class="flex items-center space-x-3">' +
                        '<i class="fas fa-link text-indigo-500"></i>' +
                        '<span class="font-semibold text-gray-700">URL:</span>' +
                    '</div>' +
                    '<p class="text-gray-600 bg-white p-3 rounded-lg border text-xs break-all">' + (d.url || '🔗 ไม่มีข้อมูล') + '</p>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    var logTable = $('#logTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "api/log_data.php",
            "type": "GET",
            "data": function(d) {
                d.user_id = $('#user_id').val();
                d.role = $('#role').val();
                d.action = $('#action').val();
                d.status = $('#status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        "columns": [
            {
                "className": 'text-center',
                "orderable": false,
                "data": null,
                "defaultContent": '<button class="bg-gradient-to-r from-blue-400 to-blue-600 hover:from-blue-500 hover:to-blue-700 text-white p-2 rounded-lg shadow-md transform hover:scale-110 transition-all duration-200"><i class="fas fa-plus-circle"></i></button>',
                "width": "5%"
            },
            { 
                "data": "datetime", 
                "width": "20%",
                "className": "px-6 py-4 text-gray-800 font-medium"
            },
            { 
                "data": "userId",
                "className": "px-6 py-4 text-gray-700"
            },
            { 
                "data": "role",
                "className": "px-6 py-4 text-gray-700"
            },
            { 
                "data": "action",
                "className": "px-6 py-4",
                "orderable": false
            },
            { 
                "data": "status",
                "className": "px-6 py-4",
                "orderable": false
            }
        ],
        "order": [[1, 'desc']],
        "language": {
            "zeroRecords": "🔍 ไม่พบข้อมูล log ที่ตรงกับเงื่อนไข",
            "info": "📄 แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "📭 ไม่มีข้อมูล",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
            "lengthMenu": "📏 แสดง _MENU_ รายการ",
            "search": "🔎 ค้นหาด่วน:",
            "processing": '<div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div><span class="ml-2">⏳ กำลังประมวลผล...</span></div>',
            "paginate": {
                "first": "⏪ หน้าแรก",
                "last": "⏩ หน้าสุดท้าย",
                "next": "➡️ ถัดไป",
                "previous": "⬅️ ก่อนหน้า"
            }
        },
        "initComplete": function() {
            // เพิ่ม animation ให้กับแถว
            $('#logTable tbody tr').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({opacity: 1}, 300);
            });
        },
        "drawCallback": function() {
            // เพิ่ม animation ให้กับแถวใหม่หลังจากโหลดข้อมูล
            $('#logTable tbody tr').each(function(index) {
                $(this).css('opacity', '0').delay(index * 50).animate({opacity: 1}, 200);
            });
        }
    });

    // Event listener สำหรับฟอร์มฟิลเตอร์
    $('#logFilterForm').on('submit', function(e) {
        e.preventDefault();
        // เพิ่ม animation เมื่อค้นหา
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin mr-2"></i><span>🔍 กำลังค้นหา...</span>');
        setTimeout(() => {
            logTable.ajax.reload(function() {
                $(this).find('button[type="submit"]').html('<i class="fas fa-search mr-2"></i><span>🔍 ค้นหา</span>');
            });
        }, 500);
    });

    // Event listener สำหรับปุ่ม + / -
    $('#logTable tbody').on('click', 'td.text-center button', function() {
        var tr = $(this).closest('tr');
        var row = logTable.row(tr);
        var icon = $(this).find('i');

        if (row.child.isShown()) {
            // แถวนี้เปิดอยู่ -> ปิด
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            $(this).removeClass('from-red-400 to-red-600').addClass('from-blue-400 to-blue-600');
        } else {
            // แถวนี้ปิดอยู่ -> เปิด
            row.child(formatDetails(row.data())).show();
            tr.addClass('shown');
            icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            $(this).removeClass('from-blue-400 to-blue-600').addClass('from-red-400 to-red-600');
            
            // เพิ่ม animation ให้ child row
            row.child().find('div').css('opacity', '0').animate({opacity: 1}, 400);
        }
    });

    // เพิ่ม hover effects
    $('#logTable tbody').on('mouseenter', 'tr', function() {
        $(this).addClass('bg-blue-50 transition-colors duration-200');
    }).on('mouseleave', 'tr', function() {
        $(this).removeClass('bg-blue-50');
    });
});

// เพิ่ม CSS animations สำหรับ loading states
const style = document.createElement('style');
style.textContent = `
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

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-bounce {
    animation: bounce 1s infinite;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #3b82f6, #8b5cf6);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #2563eb, #7c3aed);
}

/* Gradient text effects */
.gradient-text {
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Glass morphism effect */
.glass {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Hover glow effect */
.hover-glow:hover {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}
`;
document.head.appendChild(style);
</script>
</body>
</html>