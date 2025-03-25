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
    async function fetchStudentInfo(device = '') {
        try {
            const response = await fetch(`api/get_realtime_attendance.php?device=${device}`); // Replace with your API endpoint
            const data = await response.json();
            
            // Update StudentProfile
            document.getElementById('StudentProfile').src = `https://student.phichai.ac.th/photo/${data.Stu_picture}`;
            
            // Update StudentDetails
            document.getElementById('StudentDetails').innerHTML = `
                ชื่อ: ${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}<br>
                รหัสประจำตัว: ${data.Stu_id}<br>
                ห้อง: ม.${data.Stu_major}/${data.Stu_room}<br>
                สถานะ: ${data.Study_status} <br>
                วันเวลา: ${new Date(data.create_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' })}
            `;
        } catch (error) {
            console.error('Error fetching student info:', error);
        }
    }

    // Fetch student info every 5 seconds
    setInterval(() => {
        const selectedDevice = document.getElementById('deviceSelect').value;
        fetchStudentInfo(selectedDevice);
    }, 1000);

    $(document).ready(function() {
        const table = $('#recordTable').DataTable({
            "pageLength": 10,
            "order": [[4, "desc"]],
            "ajax": {
                "url": "api/get_realtime_attendance_records.php",
                "dataSrc": ""
            },
            "columns": [
                { 
                    "data": null,
                    "render": function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { "data": "Stu_id" },
                { 
                    "data": null,
                    "render": function (data, type, row) {
                        return `${row.Stu_pre} ${row.Stu_name} ${row.Stu_sur}`;
                    }
                },
                { 
                    "data": null,
                    "render": function (data, type, row) {
                        return `ม.${row.Stu_major}/${row.Stu_room}`;
                    }
                },
                { 
                    "data": "create_at",
                    "render": function (data, type, row) {
                        const date = new Date(data);
                        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
                        return date.toLocaleDateString('th-TH', options);
                    }
                }
            ]
        });

        // Fetch device names and populate the dropdown
        async function fetchDeviceNames() {
            try {
                const response = await fetch('api/get_device_names.php'); // Replace with your API endpoint
                const devices = await response.json();
                const deviceSelect = document.getElementById('deviceSelect');
                devices.forEach(device => {
                    const option = document.createElement('option');
                    option.value = device.device;
                    option.textContent = device.device;
                    deviceSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching device names:', error);
            }
        }

        fetchDeviceNames();

        // Filter student info based on selected device
        $('#deviceSelect').on('change', function() {
            const selectedDevice = $(this).val();
            fetchStudentInfo(selectedDevice);
        });

        setInterval(function() {
            table.ajax.reload(null, false); // user paging is not reset on reload
        }, 1000);
    });
</script>

<?php require_once('script.php');?>
</body>
</html>
