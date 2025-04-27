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
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">Log</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto py-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Login & Logout Log</h2>
                    <!-- Filter Form -->
                    <form id="logFilterForm" method="get" class="mb-4 flex flex-wrap gap-2 items-end">
                        <input type="text" name="user_id" placeholder="User ID" value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>" class="border rounded px-2 py-1" />
                        <input type="text" name="role" placeholder="Role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>" class="border rounded px-2 py-1" />
                        <select name="action" class="border rounded px-2 py-1">
                            <option value="">Action</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="login_attempt">Login Attempt</option>
                        </select>
                        <select name="status" class="border rounded px-2 py-1">
                            <option value="">Status</option>
                            <option value="success">Success</option>
                            <option value="fail">Fail</option>
                        </select>
                        <input type="date" name="date_from" class="border rounded px-2 py-1" />
                        <span class="mx-1">-</span>
                        <input type="date" name="date_to" class="border rounded px-2 py-1" />
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Filter</button>
                        <a href="log.php" class="ml-2 text-sm text-gray-500 underline">Clear</a>
                    </form>
                    <div class="overflow-x-auto">
                        <table id="logTable" class="min-w-full divide-y divide-gray-200 border display">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border text-left">วันที่เวลา</th>
                                    <th class="px-4 py-2 border text-left">User ID</th>
                                    <th class="px-4 py-2 border text-left">Role</th>
                                    <th class="px-4 py-2 border text-left">IP</th>
                                    <th class="px-4 py-2 border text-left">Action</th>
                                    <th class="px-4 py-2 border text-left">Status</th>
                                    <th class="px-4 py-2 border text-left">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will load data here -->
                            </tbody>
                        </table>
                    </div>
                    <!-- jQuery & DataTables JS -->
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
                    <script>
                    let logTable;
                    function getFilterParams() {
                        const form = document.getElementById('logFilterForm');
                        return Object.fromEntries(new FormData(form).entries());
                    }
                    $(document).ready(function() {
                        logTable = $('#logTable').DataTable({
                            processing: true,
                            serverSide: true,
                            searching: false,
                            ajax: {
                                url: 'log_data.php',
                                type: 'GET',
                                data: function(d) {
                                    // Merge DataTables params with filter params
                                    return Object.assign({}, d, getFilterParams());
                                }
                            },
                            order: [[0, 'desc']],
                            columns: [
                                { data: 'datetime' },
                                { data: 'userId' },
                                { data: 'role' },
                                { data: 'ip' },
                                { data: 'action' },
                                { data: 'status' },
                                { data: 'message' }
                            ],
                            language: {
                                "zeroRecords": "ไม่พบข้อมูล log",
                                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                                "infoEmpty": "ไม่มีข้อมูล",
                                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                                "lengthMenu": "แสดง _MENU_ รายการ",
                                "paginate": {
                                    "first": "หน้าแรก",
                                    "last": "หน้าสุดท้าย",
                                    "next": "ถัดไป",
                                    "previous": "ก่อนหน้า"
                                }
                            }
                        });

                        $('#logFilterForm').on('submit', function(e) {
                            e.preventDefault();
                            logTable.ajax.reload();
                        });
                    });
                    </script>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
