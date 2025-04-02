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
        '../login.php' // Redirect URL
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
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content justify-center items-center flex flex-col">

    <!-- /.content-header -->

    <section class="content mt-4 mb-4">
        <div class="container mx-auto px-4">
            <div class="col-md-12">
                <div class="bg-white border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
                    <div class="text-center">
                        <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="mx-auto w-16 h-16 mb-3">
                        <h5 class="text-base font-bold">
                            <p>🏠 รายงานสถิติการเยี่ยมบ้านนักเรียนจำแนกตามห้องเรียน</p>
                            <p>รายงานสถิติการเยี่ยมบ้านนักเรียนจำแนกตามห้องเรียน ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?></p>
                            <p>ครูที่ปรึกษา <?php
                                        // Fetch teacher names using the function from the Teacher class
                                        $teachers = $teacher->getTeachersByClassAndRoom($class, $room);

                                        if ($teachers) {
                                            foreach ($teachers as $row) {
                                                echo $row['Teach_name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
                                            }
                                        } else {
                                            echo "ไม่พบข้อมูลครูที่ปรึกษา";
                                        }
                                        ?></p>
                        </h5>
                    </div>
                    <div class="text-left mt-4">
                        <button type="button" id="addButton" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-yellow-600 mb-3" onclick="window.location.href='visithome.php'">
                            🔙 กลับไปหน้าข้อมูลการเยี่ยมบ้าน 🔙
                        </button>
                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3" id="printButton" onclick="printPage()">
                            🖨️ พิมพ์รายงาน 🖨️
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="select_term">ภาคเรียน</label>
                                    </div>
                                    <select class="custom-select text-center" id="select_term">
                                        <option value="">โปรดเลือกภาคเรียน...</option>
                                        <option value="1">ภาคเรียนที่ 1</option>
                                        <option value="2">ภาคเรียนที่ 2</option>
                                        <!-- Add your room options here -->
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 justify-center items-center text-center">
                            <button id="filter" class="btn btn-sm btn-outline-info">ค้นหา</button>
                            <button id="reset" class="btn btn-sm btn-outline-warning">ล้างการค้นหา</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto mt-6">
                        <table id="record_table" class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="bg-indigo-500 text-white">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center">รายการ</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">คำตอบ</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">จำนวนนักเรียน(คน)</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">ร้อยละ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="bg-gray-100 border-l-4 border-gray-500 p-6 rounded-lg shadow-md">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">กราฟแสดงคำจำนวนคำตอบของนักเรียนในแต่ละข้อ</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
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
    <?php require_once('../footer.php'); ?>
</div>
<!-- ./wrapper -->

<!-- Modal for Editing Visit -->


<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {

  // Function to handle printing
  window.printPage = function() {
      let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, .btn-warning, .btn-primary');

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


function convertToThaiDate(dateString) {
    const months = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // Convert to Buddhist year
    return `${day} ${month} ${year}`;
}

// Function to load table data based on selected term
async function loadTableByTerm(term) {
    try {
        var classValue = <?= $class ?>;
        var roomValue = <?= $room ?>;
        var peeValue = <?= $pee ?>;

        console.log(classValue, roomValue, peeValue, term); // Debugging line

        // Make an AJAX request to fetch data based on the selected term
        const response = await $.ajax({
            url: 'api/fetch_visithomeclass.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classValue, room: roomValue, pee: peeValue, term: term }
        });

        console.log('Response:', response); // Debugging line

        // Determine if the data is directly in the response or in response.data
        let dataArray;
        if (Array.isArray(response)) {
            dataArray = response;
        } else if (response && response.data && Array.isArray(response.data)) {
            dataArray = response.data;
        } else {
            dataArray = [];
        }

        // Check if we have data
        if (dataArray && dataArray.length > 0) {
            console.log('Processing data entries:', dataArray.length);
            
            // Group data by item_type
            const groupedByType = {};
            dataArray.forEach(item => {
                if (!groupedByType[item.item_type]) {
                    groupedByType[item.item_type] = [];
                }
                groupedByType[item.item_type].push(item);
            });
            
            // Destroy any existing DataTable
            if ($.fn.DataTable.isDataTable('#record_table')) {
                $('#record_table').DataTable().destroy();
            }
            
            // Clear the table content
            $('#record_table').html(`
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">รายการ</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">คำตอบ</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">จำนวนนักเรียน(คน)</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">ร้อยละ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);
            
            // Build table with standard HTML structure
            Object.keys(groupedByType).forEach(type => {
                const items = groupedByType[type];
                if (items.length > 0) {
                    // Create a row for each answer in the group
                    items.forEach((item, index) => {
                        const row = $('<tr>');
                        
                        // Only add the question cell with rowspan for the first row in each group
                        if (index === 0) {
                            row.append(`<td class="border border-gray-300 px-4 py-2 text-center" rowspan="${items.length}">${type}</td>`);
                        }
                        
                        // Add other data cells
                        row.append(`<td class="border border-gray-300 px-4 py-2">${item.item_list}</td>`);
                        row.append(`<td class="border border-gray-300 px-4 py-2 text-center">${item.Stu_total}</td>`);
                        
                        const percentCell = `
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="bg-gray-200" style="width: 100%; height: 20px;">
                                    <div style="width: ${item.Persent}%; height: 100%; background-color: ${item.bg_color};"></div>
                                </div>
                                ${item.Persent}%
                            </td>
                        `;
                        row.append(percentCell);
                        
                        $('#record_table tbody').append(row);
                    });
                }
            });
            
            // Now initialize pagination and search without DataTables
            initializeTablePagination();
            
            // Update the chart with the data
            updateChart(dataArray);
        } else {
            // Display no data message
            $('#record_table').html(`
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">รายการ</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">คำตอบ</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">จำนวนนักเรียน(คน)</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">ร้อยละ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">ไม่พบข้อมูล</td>
                    </tr>
                </tbody>
            `);
        }
        
    } catch (error) {
        console.error('Error in loadTableByTerm:', error);
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error.message,
            icon: 'error'
        });
        
        // Display error message
        $('#record_table').html(`
            <thead class="bg-indigo-500 text-white">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-center">รายการ</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">คำตอบ</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">จำนวนนักเรียน(คน)</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">ร้อยละ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</td>
                </tr>
            </tbody>
        `);
    }
}

// Custom table pagination function
function initializeTablePagination() {
    // Add table search functionality
    $('#table-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#record_table tbody tr').each(function() {
            // Skip rows that don't have direct text content (e.g. rows missing the first cell due to rowspan)
            let rowVisible = false;
            let rowHasText = false;
            
            $(this).find('td').each(function() {
                const cellText = $(this).text().toLowerCase();
                if (cellText) {
                    rowHasText = true;
                    if (cellText.includes(searchTerm)) {
                        rowVisible = true;
                    }
                }
            });
            
            if (rowHasText) {
                $(this).toggle(rowVisible || searchTerm === '');
            }
        });
    });

}

// Function to update the chart with data
function updateChart(data) {
    if (!data || !Array.isArray(data) || data.length === 0) {
        console.log('No chart data available');
        return;
    }
    
    console.log('Updating chart with data:', data.length, 'entries');
    
    // Group data by item_type to better visualize in chart
    let chartData = {};
    data.forEach(item => {
        if (!chartData[item.item_type]) {
            chartData[item.item_type] = [];
        }
        chartData[item.item_type].push(item);
    });
    
    // Get unique item_type values for chart sections
    const itemTypes = Object.keys(chartData).filter(type => type !== '');
    const labels = itemTypes.map(type => type.replace(/^\d+\.\s+/, '')); // Remove numbering
    const values = itemTypes.map(type => {
        return chartData[type].reduce((sum, item) => sum + parseInt(item.Stu_total), 0);
    });
    const bgColors = itemTypes.map((type, index) => {
        const item = chartData[type][0];
        return item.bg_color || getRandomColor();
    });
    
    // Get the canvas element
    const ctx = document.getElementById('donutChart').getContext('2d');
    
    // Destroy previous chart if exists
    if (window.myDonutChart) {
        window.myDonutChart.destroy();
    }
    
    console.log('Creating chart with:', { labels, values, bgColors });
    
    // Create new chart
    window.myDonutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: bgColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} คน (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Generate random color for chart segments
function getRandomColor() {
    const r = Math.floor(Math.random() * 255);
    const g = Math.floor(Math.random() * 255);
    const b = Math.floor(Math.random() * 255);
    return `rgba(${r}, ${g}, ${b}, 0.7)`; 
}

// Function to load initial table data
function loadTable() {
    // Default to term 1 if none selected
    const defaultTerm = $('#select_term').val() || '1';
    $('#select_term').val(defaultTerm); // Pre-select the default term
    loadTableByTerm(defaultTerm);
}

// Event listener for the filter button
$('#filter').on('click', function() {
    const selectedTerm = $('#select_term').val();
    if (selectedTerm) {
        loadTableByTerm(selectedTerm);
    } else {
        Swal.fire('ข้อผิดพลาด', 'โปรดเลือกภาคเรียนก่อนค้นหา', 'warning');
    }
});

// Event listener for the reset button
$('#reset').on('click', function() {
    $('#select_term').val('');
    loadTable();
});

// Event listener for the select_term dropdown
$('#select_term').on('change', function() {
    const selectedTerm = $(this).val();
    if (selectedTerm) {
        loadTableByTerm(selectedTerm);
    }
});

// Call the loadTable function when the page is loaded
loadTable();
});

</script>
</body>
</html>
