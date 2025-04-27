<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Director_login'])) {
    $userid = $_SESSION['Director_login'];
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
                        <h5 class="m-0">ข้อมูลครู</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card container mx-auto px-4 py-6 ">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">ข้อมูลครู</h2>
                    <!-- <button id="btnAddTeacher" class="btn btn-primary">+ เพิ่มครู</button> -->
                </div>
                <div class="overflow-x-auto">
                    <table id="teacherTable" class="min-w-full divide-y divide-gray-200 table-auto " style="width:100%">
                        <thead class="bg-indigo-500">
                            <tr>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">รหัสครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ชื่อครู</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">กลุ่ม</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ชั้น</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">ห้อง</th>
                                <th class="px-4 py-2 text-center  font-medium text-white uppercase tracking-wider border-b">Role</th>
                            </tr>
                        </thead>
                        <tbody id="teacherTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </section>



<script>
        let teacherTable;
        // ใส่ token key ที่นี่ (ต้องตรงกับใน api_teacher.php)
        const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
        // Initial load
        $(document).ready(function() {
            // สร้าง DataTable ครั้งเดียว
            teacherTable = $('#teacherTable').DataTable({
                columnDefs: [
                    { className: 'text-center', width: '8%', targets: 0 },  // ✅ ถูกต้อง
                    { className: 'text-left', width: '20%', targets: 1 },
                    { className: 'text-left', width: '20%', targets: 2 },
                    { className: 'text-center', targets: 3 },
                    { className: 'text-center', targets: 4 },
                    { className: 'text-center', targets: 5 }
                ],
                autoWidth: false,
                order: [[0, 'asc']], // Default sort by first column (รหัสครู)
                pageLength: 10, // Default number of rows per page
                lengthMenu: [10, 25, 50, 100], // Options for number of rows per page
                pagingType: 'full_numbers', // Full pagination controls
                searching: true, // Enable search box
                
            });
            loadTeachers();


            
        });

        // เพิ่มฟังก์ชันนี้ก่อน fetch('api/fet_major.php')
function populateSelectElement(selectId, data) {
    const select = document.getElementById(selectId);
    if (!select) return;
    // ลบ option เดิม ยกเว้นตัวแรก
    while (select.options.length > 1) {
        select.remove(1);
    }
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.Teach_major || '';
        option.text = item.Teach_major || '';
        select.appendChild(option);
    });
}

// Fetch majors and populate the selects
fetch('api/fet_major.php')
    .then(response => response.json())
    .then(data => {
        populateSelectElement('addTeach_major', data);
        populateSelectElement('editTeach_major', data);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
});

        // Fetch and render teacher data
        async function loadTeachers() {
            const res = await fetch('api/api_teacher.php?action=list&token=' + encodeURIComponent(API_TOKEN_KEY));
            const data = await res.json();
            teacherTable.clear();
            data.forEach(teacher => {
                // Map role_std code to Thai description
                let roleDisplay = '';
                switch (teacher.role_std) {
                    case 'T':
                        roleDisplay = 'ครู';
                        break;
                    case 'DIR':
                        roleDisplay = 'ผู้อำนวยการ';
                        break;
                    case 'VP':
                        roleDisplay = 'รองผู้อำนวยการ';
                        break;
                    case 'OF':
                        roleDisplay = 'เจ้าหน้าที่';
                        break;
                    case 'ADM':
                        roleDisplay = 'Administrator';
                        break;
                    default:
                        roleDisplay = teacher.role_std || '';
                }
                teacherTable.row.add([
                    teacher.Teach_id,
                    teacher.Teach_name,
                    teacher.Teach_major || '',
                    teacher.Teach_class || '',
                    teacher.Teach_room || '',
                    roleDisplay
                ]);
            });
            teacherTable.draw();
            // Attach event listeners
            // เปลี่ยนจาก document.querySelectorAll เป็น event delegation
            // document.querySelectorAll('.editBtn').forEach(btn => {
            //     btn.onclick = () => openEditModal(btn.dataset.id);
            // });
            // document.querySelectorAll('.deleteBtn').forEach(btn => {
            //     btn.onclick = () => deleteTeacher(btn.dataset.id);
            // });
        }

        // ใช้ event delegation สำหรับปุ่ม edit
        $(document).on('click', '.editBtn', function() {
            const id = $(this).data('id');
            openEditModal(id);
        });
</script>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
        <!-- Bootstrap 5, jQuery, DataTables scripts -->

</body>
</html>
