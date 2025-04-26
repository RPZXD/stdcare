<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Student.php";
require_once "../class/Utils.php";
require_once "../class/BoardParent.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacher = new Teacher($db);
$student = new Student($db);
$boardParent = new BoardParent($db);

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
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

$term = $user->getTerm();
$pee = $user->getPee();

require_once('header.php');
?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"></h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-80 mb-3 w-12 h-12 mx-auto">
                        <h5 class="text-center text-lg mb-4">รายงานคณะกรรมการเครือข่ายผู้ปกครอง<br>
                        ปีการศึกษา <?=$pee?></h5>
                        <div class="text-left">
                            <button class="btn bg-green-500 text-white text-left mb-3 mt-2" id="printButton" onclick="printPage()"> <i class="fa fa-print" aria-hidden="true"></i> พิมพ์รายงาน  <i class="fa fa-print" aria-hidden="true"></i></button>
                        </div>
                        <div class="row mb-4" id="selector_class">
                            <div class="col-md-4 mx-auto">
                                <label for="class_select" class="block text-base font-medium text-gray-700">เลือกระดับชั้น:</label>
                                <select id="class_select" class="form-control text-center ">
                                    <option value="">-- เลือกระดับชั้น --</option>
                                    <?php
                                    // ดึงระดับชั้นที่มีข้อมูลใน tb_parnet
                                    $stmt = $db->query("SELECT DISTINCT parn_lev
                                                        FROM tb_parnet
                                                        WHERE parn_lev IS NOT NULL
                                                        AND parn_lev != ''
                                                        AND parn_lev != '0'
                                                        ORDER BY parn_lev ASC;
                                                        ");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['parn_lev'].'">ม.' . $row['parn_lev'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="report_table" class="display table-bordered table-hover" style="width:100%">
                                <thead class="thead-secondary bg-emerald-500 text-white">
                                    <tr>
                                        <th class="text-center">ลำดับที่</th>
                                        <th class="text-center">ห้อง</th>
                                        <th class="text-center">ชื่อ-นามสกุล</th>
                                        <th class="text-center">ที่อยู่</th>
                                        <th class="text-center">เบอร์โทรศัพท์</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ข้อมูลจะถูกเติมโดย JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Section Content -->

    </div>
    <?php require_once('../footer.php');?>
</div>
<?php require_once('script.php');?>

<script>
$(document).ready(function() {
    var table = $('#report_table').DataTable({
        destroy: true,
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        lengthChange: false,
        columnDefs: [
            { targets: 0, className: 'text-center', width: '6%' },
            { targets: 1, className: 'text-center', width: '8%' },
            { targets: 2, className: 'text-center', width: '20%' },
            { targets: 3, className: 'text-left' },
            { targets: 4, className: 'text-center', width: '12%' }
        ]
    });

    $('#class_select').on('change', function() {
        var classVal = $(this).val();
        table.clear().draw();
        if (!classVal) return;

        $.ajax({
            url: 'api/fetch_boardparent_by_class.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classVal, pee: <?= json_encode($pee) ?> },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(item, idx) {
                        table.row.add([
                            idx + 1,
                            `ม.${item.parn_lev}/${item.parn_room}`,
                            item.parn_name,
                            item.parn_addr,
                            item.parn_tel || 'ไม่มีข้อมูล'
                        ]);
                    });
                } else {
                    table.row.add([
                        '', '', 'ไม่พบข้อมูล', '', ''
                    ]);
                }
                table.draw();
            },
            error: function() {
                table.row.add([
                    '', '', 'เกิดข้อผิดพลาดในการโหลดข้อมูล', '', ''
                ]).draw();
            }
        });
    });

    window.printPage = function() {
        let elementsToHide = $('#addButton, #selector_class, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, .btn-warning, .btn-primary');

        // Hide the export to Excel button
        $('#record_table_wrapper .dt-buttons').hide(); // Hides the export buttons

        // Hide the elements you want to exclude from the print
        elementsToHide.hide();
        $('thead').css('display', 'table-header-group'); // Ensure header shows

        setTimeout(() => {
            window.print();
            elementsToHide.show();
            $('#record_table_wrapper .dt-buttons').show();
        }, 100);
    };

    // Function to set up the print layout
    function setupPrintLayout() {
        var style = '@page { size: A4 portrait; margin: 0.5in; }';
        var printStyle = document.createElement('style');
        printStyle.appendChild(document.createTextNode(style));
        document.head.appendChild(printStyle);
    }
});
</script>
</body>
</html>
