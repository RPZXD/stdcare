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

        <div class="row">

        <div class="col-md-12">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">
                <h2 class="text-center">
                  การดำเนินงาน</h2>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                <div class="callout callout-danger">
                    <div class="col-12 form-group">
                        <a href="https://student.phichai.ac.th/kitjakarn-phichaicare.pdf" class="btn-lg btn-outline-primary"> <i class="fas fa-download"></i> ดาวน์โหลดคู่มือการดำเนินงานระบบการดูแลช่วยเหลือนักเรียน <i class="fas fa-download"></i></a>
                    </div>
                </div>
                <div class="callout callout-warning">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <img src="https://student.phichai.ac.th/phichai1.jpg" alt="slide" class="w-full">
                    </div>
                    <div class="col-md-6 mt-2">
                        <img src="https://student.phichai.ac.th/phichai2.jpg" alt="slide" class="w-full">
                    </div>
                    <div class="col-md-6 mt-2">
                        <img src="https://student.phichai.ac.th/phichai3.jpg" alt="slide" class="w-full">
                    </div>
                    <div class="col-md-6 mt-2">
                        <img src="https://student.phichai.ac.th/phichai4.jpg" alt="slide" class="w-full">
                    </div>

                </div>
                </div>
                




              </div>


              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->


          
          <!-- /.col -->
          <!-- /.col -->
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

<?php require_once('script.php');?>
</body>
</html>

