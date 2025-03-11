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
            <div class="col-lg-12 col-sm-12 col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">มัธยมศึกษาตอนต้น</h3>
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
                    <canvas id="barChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">มัธยมศึกษาตอนปลาย</h3>
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
                    <canvas id="barChart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
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

function fetchChartData(chartId, apiUrl) {
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

fetchChartData('barChart1', 'api/fetch_chartstu.php?level=1-3');
fetchChartData('barChart2', 'api/fetch_chartstu.php?level=4-6');
</script>
<?php require_once('script.php');?>
</body>
</html>
