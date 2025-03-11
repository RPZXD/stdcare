<script>
  document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const navbar = document.querySelector('.main-header.navbar');

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light-mode';
    body.classList.remove('light-mode', 'dark-mode');
    body.classList.add(savedTheme);
    navbar.classList.toggle('navbar-light', savedTheme === 'light-mode');
    navbar.classList.toggle('navbar-dark', savedTheme === 'dark-mode');
    themeToggle.checked = savedTheme === 'dark-mode';

    // Add event listener for the switch
    themeToggle.addEventListener('change', function() {
      const isDarkMode = themeToggle.checked;
      body.classList.toggle('light-mode', !isDarkMode);
      body.classList.toggle('dark-mode', isDarkMode);
      navbar.classList.toggle('navbar-light', !isDarkMode);
      navbar.classList.toggle('navbar-dark', isDarkMode);
      localStorage.setItem('theme', isDarkMode ? 'dark-mode' : 'light-mode');
    });
  });
</script>
<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI after jQuery -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
