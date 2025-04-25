<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

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

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));

require_once('header.php');
?>

<style>
    .form-check-input {
        transform: scale(2);
        margin-right: 30px;
    }
    @media print {
        #printButton, #chartSummaryContainer { display: none !important; }
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper. Contains page content -->
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
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body text-center">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3">
                        <h5 class="text-lg font-bold">
                            🏠 รายงานสถิติข้อมูล SDQ <br>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                        </h5>
                        <div class="text-left mt-4">
                            <button type="button" id="backButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 mb-3" onclick="window.location.href='sdq.php'">
                                🔙 กลับหน้าหลัก SDQ
                            </button>
                            <button type="button" id="printButton" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3 ml-2">
                                🖨️ พิมพ์รายงาน 🖨️
                            </button>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12 mt-3 mb-3 mx-auto">
                                <div class="table-responsive mx-auto">
                                    <table id="report_sdq_all" class="display table-bordered table-hover" style="width:100%">
                                        <thead class="thead-secondary bg-indigo-500 text-white">
                                            <tr>
                                                <th class="text-center">เลขที่</th>
                                                <th class="text-center">เลขประจำตัว</th>
                                                <th class="text-center">ชื่อ-นามสกุล</th>
                                                <th class="text-center">นักเรียน<br>(คะแนนรวม)</th>
                                                <th class="text-center">แปลผล</th>
                                                <th class="text-center">ครู<br>(คะแนนรวม)</th>
                                                <th class="text-center">แปลผล</th>
                                                <th class="text-center">ผู้ปกครอง<br>(คะแนนรวม)</th>
                                                <th class="text-center">แปลผล</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic content will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- กราฟสรุปผล -->
                        <div id="chartSummaryContainer" class="mt-6">
                            <h5 class="font-bold mb-2">สรุปผล SDQ (จำนวนคนในแต่ละระดับ)</h5>
                            <canvas id="sdqSummaryChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- /.content-wrapper -->

    <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // โหลดข้อมูลรายงาน SDQ ทั้งหมด
    let chartInstance = null;
    function loadReportSDQAll() {
        const classValue = <?= $class ?>;
        const roomValue = <?= $room ?>;
        const peeValue = <?= $pee ?>;
        const termValue = <?= $term ?>;

        $.ajax({
            url: 'api/fetch_sdq_report_all.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, pee: peeValue, term: termValue },
            success: function(response) {
                const table = $('#report_sdq_all').DataTable({
                    destroy: true,
                    pageLength: 50,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'asc']],
                    columnDefs: [
                        { targets: '_all', className: 'text-center' },
                        { targets: 2, className: 'text-left text-semibold' }
                    ],
                    autoWidth: false,
                    responsive: true,
                    searching: true
                });

                table.clear();

                // สำหรับสรุปผลกราฟ
                let summary = {
                    std: { normal: 0, borderline: 0, problem: 0 },
                    teach: { normal: 0, borderline: 0, problem: 0 },
                    par: { normal: 0, borderline: 0, problem: 0 }
                };

                function countResult(type, result) {
                    if (!result) return;
                    if (result.includes('ปกติ')) summary[type].normal++;
                    else if (result.includes('เสี่ยง')) summary[type].borderline++;
                    else if (result.includes('ปัญหา')) summary[type].problem++;
                }

                if (!response.success || response.data.length === 0) {
                    table.row.add([
                        '-', '-', '-', '-', '<td colspan="5" class="text-center">ไม่พบข้อมูล</td>'
                    ]);
                } else {
                    response.data.forEach((item, index) => {
                        table.row.add([
                            item.Stu_no,
                            item.Stu_id,
                            item.full_name,
                            item.std_score !== null ? item.std_score : '-',
                            item.std_result !== null ? item.std_result : '-',
                            item.teach_score !== null ? item.teach_score : '-',
                            item.teach_result !== null ? item.teach_result : '-',
                            item.par_score !== null ? item.par_score : '-',
                            item.par_result !== null ? item.par_result : '-'
                        ]);
                        countResult('std', item.std_result);
                        countResult('teach', item.teach_result);
                        countResult('par', item.par_result);
                    });
                }

                table.draw();

                // วาดกราฟ
                renderSummaryChart(summary);
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลรายงานได้', 'error');
            }
        });
    }

    function renderSummaryChart(summary) {
        const ctx = document.getElementById('sdqSummaryChart').getContext('2d');
        const labels = ['ปกติ', 'ภาวะเสี่ยง', 'มีปัญหา'];
        const bgColors = [
            'rgba(34,197,94,0.8)',    // green
            'rgba(234,179,8,0.8)',    // yellow
            'rgba(239,68,68,0.8)'     // red
        ];
        const borderColors = [
            'rgba(34,197,94,1)',
            'rgba(234,179,8,1)',
            'rgba(239,68,68,1)'
        ];
        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'นักเรียน',
                    data: [summary.std.normal, summary.std.borderline, summary.std.problem],
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1
                },
                {
                    label: 'ครู',
                    data: [summary.teach.normal, summary.teach.borderline, summary.teach.problem],
                    backgroundColor: bgColors.map(c => c.replace('0.8', '0.5')),
                    borderColor: borderColors,
                    borderWidth: 1
                },
                {
                    label: 'ผู้ปกครอง',
                    data: [summary.par.normal, summary.par.borderline, summary.par.problem],
                    backgroundColor: bgColors.map(c => c.replace('0.8', '0.3')),
                    borderColor: borderColors,
                    borderWidth: 1
                }
            ]
        };
        if (chartInstance) {
            chartInstance.destroy();
        }
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, precision: 0 }
                }
            }
        });
    }

    // ปุ่มพิมพ์
    $('#printButton').on('click', function() {
        $('#printButton').hide();
        $('#backButton').hide();
        $('#chartSummaryContainer').hide();
        setTimeout(() => {
            window.print();
            $('#printButton').show();
            $('#backButton').show();
            $('#chartSummaryContainer').show();
        }, 100);
    });

    loadReportSDQAll();
});
</script>
</body>
</html>
