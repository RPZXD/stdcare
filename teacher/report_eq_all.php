<?php
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// DB connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$teacher = new Teacher($db);

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

        <!-- EQ Report Section -->
        <section class="content">
            <div class="container-fluid">
                <div class="card col-md-12">
                    <div class="card-body text-center">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3">
                        <h5 class="text-lg font-bold">
                            📊 รายงานสถิติข้อมูล EQ <br>
                            ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                        </h5>
                        <div class="text-left mt-4">
                        <button type="button" id="backButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 mb-3" onclick="window.location.href='eq.php'">
                                🔙 กลับหน้าหลัก EQ
                            </button>
                            <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3" id="printButton" onclick="printPage()">
                                🖨️ พิมพ์รายงาน 🖨️
                            </button>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12 mt-3 mb-3 mx-auto">
                                <div class="table-responsive mx-auto">
                                    <table id="eq_table" class="display table-bordered table-hover" style="width:100%">
                                        <thead class="thead-secondary bg-indigo-500 text-white">
                                            <tr>
                                                <th class="text-center">เลขที่</th>
                                                <th class="text-center">เลขประจำตัว</th>
                                                <th class="text-center">ชื่อ-นามสกุล</th>
                                                <th class="text-center">คะแนน EQ</th>
                                                <th class="text-center">ระดับ</th>
                                                <th class="text-center">รายละเอียด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic content will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                                <!-- กราฟสรุปผลรวมทั้งห้อง -->
                                <div class="mt-5">
                                    <h5 class="text-lg font-bold mb-3">สรุประดับ EQ รวมทั้งห้อง</h5>
                                    <canvas id="eqSummaryChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
  
    <?php require_once('../footer.php');?>

</div>
<!-- ./wrapper -->

<?php require_once('script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // พิมพ์เฉพาะตาราง (ไม่แสดงคอลัมน์ "รายละเอียด")
    window.printPage = function() {
        let elementsToHide = $('#backButton, #printButton, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info');
        elementsToHide.hide();
        // ซ่อนคอลัมน์ "รายละเอียด" (index 5)
        $('#eq_table th:nth-child(6), #eq_table td:nth-child(6)').hide();
        $('thead').css('display', 'table-header-group');
        setTimeout(() => {
            window.print();
            elementsToHide.show();
            $('#eq_table th:nth-child(6), #eq_table td:nth-child(6)').show();
        }, 100);
    };

    // โหลดข้อมูล EQ
    let eqChart = null;

    async function loadEQTable() {
        try {
            const classValue = <?= $class ?>;
            const roomValue = <?= $room ?>;
            const peeValue = <?= $pee ?>;
            const termValue = <?= $term ?>;

            const response = await $.ajax({
                url: 'api/fetch_eq_result.php',
                method: 'GET',
                dataType: 'json',
                data: { class: classValue, room: roomValue, pee: peeValue, term: termValue }
            });

            const table = $('#eq_table').DataTable({
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

            let eqLevelCount = {
                'EQ สูง': 0,
                'EQ ปานกลาง': 0,
                'EQ ต่ำ': 0
            };

            if (!response.success || response.data.length === 0) {
                table.row.add([
                    '-', '-', '-', '-', '-', '<td colspan="6" class="text-center">ไม่พบข้อมูล</td>'
                ]);
            } else {
                response.data.forEach((item, index) => {
                    // นับจำนวนแต่ละระดับ
                    if (item.eq_level && eqLevelCount.hasOwnProperty(item.eq_level)) {
                        eqLevelCount[item.eq_level]++;
                    }
                    table.row.add([
                        item.Stu_no,
                        item.Stu_id,
                        item.full_name,
                        item.eq_score ?? '-',
                        item.eq_level ?? '-',
                        `<button class="btn bg-purple-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-purple-600 btn-sm"
                            onclick="viewEQDetail('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')">
                            ดูรายละเอียด
                        </button>`
                    ]);
                });
            }

            table.draw();

            // วาดกราฟสรุประดับ EQ
            const ctx = document.getElementById('eqSummaryChart').getContext('2d');
            const labels = Object.keys(eqLevelCount);
            const data = Object.values(eqLevelCount);

            // ลบกราฟเดิมถ้ามี
            if (eqChart) {
                eqChart.destroy();
            }
            eqChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนนักเรียน',
                        data: data,
                        backgroundColor: [
                            '#22c55e', // EQ สูง - เขียว
                            '#facc15', // EQ ปานกลาง - เหลือง
                            '#ef4444'  // EQ ต่ำ - แดง
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'จำนวนนักเรียน' }
                        },
                        x: {
                            title: { display: true, text: 'ระดับ EQ' }
                        }
                    }
                }
            });

        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            console.error(error);
        }
    }

    // ฟังก์ชันดูรายละเอียด EQ (modal)
    window.viewEQDetail = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
        $.ajax({
            url: 'template_form/form_eq_result.php',
            method: 'GET',
            data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
            success: function(response) {
                const modalHtml = `
                    <div class="modal fade" id="eqResultModal" tabindex="-1" role="dialog" aria-labelledby="eqResultModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="modalContentToPrint">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="eqResultModalLabel">รายละเอียดผล EQ</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ${response}
                                </div>
                                <div class="modal-footer print-hide">
                                    <button type="button" class="btn btn-success" id="printModalBtn"><i class="fas fa-print"></i> พิมพ์</button>
                                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-600" data-dismiss="modal">ปิด</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);
                $('#eqResultModal').modal('show');

                // Print only modal content
                $('#printModalBtn').on('click', function() {
                    let printContents = document.getElementById('modalContentToPrint').innerHTML;
                    let printWindow = window.open('', '', 'height=800,width=900');
                    printWindow.document.write('<html><head><title>พิมพ์รายงาน EQ</title>');
                    $('link[rel=stylesheet], style').each(function() {
                        printWindow.document.write(this.outerHTML);
                    });
                    printWindow.document.write(`
                        <style>
                            @media print {
                                .print-hide, .modal-header .close { display: none !important; }
                                body { -webkit-print-color-adjust: exact; }
                            }
                            @page { size: A4 portrait; margin: 20mm 15mm 20mm 15mm; }
                            html, body { width: 210mm; height: 297mm; }
                            .modal-content { box-shadow: none !important; border: none !important; }
                        </style>
                    `);
                    printWindow.document.write('</head><body style="background:white;">');
                    printWindow.document.write('<div style="margin:0;">' + printContents + '</div>');
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    setTimeout(function() {
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();
                    }, 500);
                });

                $('#eqResultModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้', 'error');
            }
        });
    };

    // โหลดข้อมูลเมื่อเปิดหน้า
    loadEQTable();
});
</script>
</body>
</html>
