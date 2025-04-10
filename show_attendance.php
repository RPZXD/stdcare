<?php 

require_once('config/Setting.php');
require_once('class/Utils.php');
require_once('config/Database.php');
require_once('class/Student.php');
require_once('header.php');
?>
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
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="flex flex-wrap mt-2">
                        <div class="w-full text-center">
                            <label for="deviceSelect">เลือกอุปกรณ์:</label>
                            <select id="deviceSelect" class="form-control mb-3 text-center">
                                <option value="">ทั้งหมด</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
    <div class="container-fluid">
        <div class="row">
            <div class="w-full">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 text-center">
                        <h4 class="text-lg font-semibold">หน้าแสดงการสแกนบัตรนักเรียน <?=$setting->getPageTitle()?></h4>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-12">
                <div class="flex flex-wrap mt-4">
                    <div class="w-full md:w-2/5 px-2 mb-4">
                    <!-- small box -->
                        <div class="bg-red-500 text-white p-4 rounded-lg shadow callout text-center">
                            <div class="flex justify-center items-center">
                                <img src="" alt="" id="StudentProfile" class="user-avatar rounded-lg shadow w-28 h-28 mx-auto" style="width:250px;height:300px;">
                            </div>
                            <div class="flex justify-center items-center mt-3">
                            <h3 class="text-lg text-left text-bold" id="StudentDetails"></h3>
                        </div>
                        </div>
                    </div>
                    <!-- ./col -->

                    <div class="w-full md:w-3/5 px-2 mb-4">
                    <!-- small box -->
                    <div class="bg-blue-500 p-4 rounded-lg shadow text-center">
                        <div class="flex justify-center items-center callout">
                            <div class="table-responsive mx-auto">
                                
                                <table id="recordTable" class="display table-bordered table-hover" style="width:100%">
                                <thead class="thead-secondary bg-blue-500 text-white ">
                                    <tr >
                                        <th  style="width:5%" class=" text-center">#</th>
                                        <th  style="width:20%" class=" text-center">รหัสประจำตัวนักเรียน</th>
                                        <th  class=" text-center">ชื่อ - สกุล</th>
                                        <th  style="width:10%" class=" text-center">ชั้น</th>
                                        <th  style="width:30%" class=" text-center">เวลา</th>
                                        <!-- Add more table column headers as needed -->
                                    </tr>
                                </thead>
                                <tbody> 
                                </tbody>
                                </table>
                                </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>

    </div><!-- /.container-fluid -->

</section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"></script>

<script>
    let cachedStudentInfo = {};
    const cacheDuration = 30000; // Cache duration in milliseconds

    async function fetchStudentInfo(device = '') {
        const now = Date.now();
        if (cachedStudentInfo[device] && (now - cachedStudentInfo[device].timestamp < cacheDuration)) {
            updateStudentDetails(cachedStudentInfo[device].data);
            return;
        }

        try {
            const response = await fetch(`api/get_realtime_attendance.php?device=${device}`);
            const data = await response.json();
            cachedStudentInfo[device] = { data, timestamp: now }; // Cache the result
            updateStudentDetails(data);
        } catch (error) {
            console.error('Error fetching student info:', error);
        }
    }

    async function fetchAllData(device = '') {
        try {
            const response = await fetch(`api/get_combined_data.php?device=${device}`);
            const data = await response.json();
            updateStudentDetails(data.studentInfo);
            updateTable(data.attendanceRecords);
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function getStatusWithEmoji(status) {
        switch (status) {
            case '1':
                return 'มาเรียน ✅';
            case '2':
                return 'ขาดเรียน ❌';
            case '3':
                return 'มาสาย ⏰';
            case '4':
                return 'ลาป่วย 🤒';
            case '5':
                return 'ลากิจ 📝';
            case '6':
                return 'เข้าร่วมกิจกรรม 🎉';
            default:
                return 'สถานะไม่ทราบ';
        }
    }

    function updateStudentDetails(data) {
        document.getElementById('StudentProfile').src = `https://student.phichai.ac.th/photo/${data.Stu_picture}`;
        document.getElementById('StudentDetails').innerHTML = `
            ชื่อ: ${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}<br>
            รหัสประจำตัว: ${data.Stu_id}<br>
            ห้อง: ม.${data.Stu_major}/${data.Stu_room}<br>
            สถานะ: ${data.Study_status} <br>
            วันเวลา: ${new Date(data.create_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' })}
        `;

        const table = $('#recordTable').DataTable();
        table.clear().rows.add(data.attendanceRecords).draw();
    }

    // Reduce request frequency to every 5 seconds
    setInterval(() => {
        const selectedDevice = document.getElementById('deviceSelect').value;
        fetchStudentInfo(selectedDevice);
    }, 5000);

    $(document).ready(function() {
        const table = $('#recordTable').DataTable({
            "pageLength": 10,
            "serverSide": true,
            "ajax": {
                "url": "api/get_realtime_attendance_records.php",
                "type": "POST",
                "data": function(d) {
                    d.device = $('#deviceSelect').val();
                }
            },
            "columns": [
                { 
                    "data": null, 
                    "render": function (data, type, row, meta) {
                        return meta.row + 1; // Generate index dynamically
                    },
                    "className": "text-center"
                },
                { "data": "Stu_id", "className": "text-center" },
                { "data": "full_name", "className": "text-center" },
                { 
                    "data": null, 
                    "render": function (data, type, row) {
                        return `ม.${row.Stu_major}/${row.Stu_room}`; // Combine Stu_major and Stu_room
                    },
                    "className": "text-center"
                },
                { "data": "create_at", "className": "text-center" }
            ]
        });

        // Populate the dropdown with predefined devices
        const devices = ["raspberry_pi_01", "raspberry_pi_02", "raspberry_pi_03", "raspberry_pi_04", "raspberry_pi_05", "raspberry_pi_06", "raspberry_pi_07", "raspberry_pi_08", "raspberry_pi_09", "raspberry_pi_10"];
        const deviceSelect = document.getElementById('deviceSelect');
        devices.forEach(device => {
            const option = document.createElement('option');
            option.value = device;
            option.textContent = device;
            deviceSelect.appendChild(option);
        });

        // Filter student info based on selected device
        $('#deviceSelect').on('change', function() {
            const selectedDevice = $(this).val();
            fetchStudentInfo(selectedDevice);
        });

        // Reload table periodically
        setInterval(function() {
            table.ajax.reload(null, false);
        }, 10000); // Reload every 10 seconds
    });
</script>

<?php require_once('script.php');?>
</body>
</html>
