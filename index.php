<?php 

require_once('header.php');
require_once('config/Setting.php');
require_once('class/Utils.php');
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

      <div class="container-fluid">
        <h3 class="text-dark">ยอดนักเรียนแต่ละระดับชั้น</h3>
      <div class="row">

          <div class="col-lg-4 col-sm-12 col-md-12">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>1365</h3>

                <p>นักเรียนมัธยมศึกษาตอนต้น</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-sm-12 col-md-12">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>781<sup style="font-size: 20px"></sup></h3>

                <p>นักเรียนมัธยมศึกษาตอนปลาย</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-sm-12 col-md-12">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>2146<sup style="font-size: 20px"></sup></h3>

                <p>นักเรียนทั้งหมด</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          
          <!-- ./col -->
          
        </div>
        <h4>สรุปการมาเรียนของนักเรียนประจำวันที่ <?=Utils::convertToThaiDatePlus(date("Y-m-d"));?> </h4>
        <div class="row">
            <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">ม.1</h3>
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
                    <canvas id="donutChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
                <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">ม.2</h3>
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
                    <canvas id="donutChart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
                <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">ม.3</h3>
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
                    <canvas id="donutChart3" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
                <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">ม.4</h3>
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
                    <canvas id="donutChart4" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
                <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">ม.5</h3>
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
                    <canvas id="donutChart5" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
                <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">ม.6</h3>
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
                    <canvas id="donutChart6" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        </div>


        
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$.widget.bridge('uibutton', $.ui.button)
  // Make an AJAX request to fetch data from fet_comestu.php
// Function to fetch data and create donut chart for class values from 1 to 6
// Function to fetch data and create donut chart for class values from 1 to 6
// Loop through classes 1 to 6
// Loop through classes 1 to 6
for (var i = 1; i <= 6; i++) {
    // Create a closure to capture the value of 'i'
    (function(classNumber) {
        // Make AJAX call for each class
        $.ajax({
            url: 'api/fetch_comestu.php',
            type: 'GET',
            data: { class: classNumber }, // Change the class parameter for each iteration
            dataType: 'json',
            success: function(data) {
                // Assuming 'data' is an array of objects containing the fetched data
                // Process the fetched data to populate the donutData object
                var donutData = {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: []
                    }]
                };

                // Populate labels, data, and backgroundColor arrays in donutData
                data.forEach(function(item) {
                    donutData.labels.push(item.label); // Assuming 'label' is the key for label data
                    donutData.datasets[0].data.push(item.value); // Assuming 'value' is the key for data value
                    donutData.datasets[0].backgroundColor.push(item.color); // Assuming 'color' is the key for background color
                });

                // Call the function to create the Donut chart with the populated data
                createDonutChart(donutData, 'donutChart' + classNumber); // Pass chart ID dynamically
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(xhr.responseText);
            }
        });
    })(i); // Pass the value of 'i' to the closure
}

// Function to create the Donut chart
function createDonutChart(data, chartId) {
    var donutChartCanvas = $('#' + chartId).get(0).getContext('2d');
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
    };
    // Create doughnut chart
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: data,
        options: donutOptions
    });
}


</script>
<?php require_once('script.php');?>
</body>
</html>
