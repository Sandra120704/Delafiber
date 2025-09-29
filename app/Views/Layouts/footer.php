<!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              Copyright © <?= date('Y') ?> - Todos los derechos reservados
            </span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
              Delafiber Perú <i class="ti-heart text-danger ml-1"></i>
            </span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- jQuery primero -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Plugins:js -->
  <script src="<?= base_url('assets/js/vendor.bundle.base.js') ?>"></script>
  
  <!-- Plugin js for this page -->
  <script src="<?= base_url('assets/chart.js/Chart.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/dataTables.bootstrap4.min.js') ?>"></script>
  
  <!-- Inject:js -->
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/template.js') ?>"></script>
  <script src="<?= base_url('assets/js/settings.js') ?>"></script>
  <script src="<?= base_url('assets/js/todolist.js') ?>"></script>
  
  <!-- Custom js for this page-->
  <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
  <script src="<?= base_url('assets/js/Chart.roundedBarCharts.js') ?>"></script>
  
  <!-- Script de cerrar sesión -->
  <script>
  function cerrarSesion() {
      if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
          window.location.href = '<?= base_url('auth/logout') ?>';
      }
  }

  // Prevenir comportamiento por defecto en menús colapsables
  $(document).ready(function() {
      $('.nav-link[data-toggle="collapse"]').on('click', function(e) {
          // Solo prevenir si se hace clic directamente en el enlace
          if ($(e.target).closest('.menu-arrow').length === 0) {
              e.preventDefault();
          }
      });
  });
  </script>
</body>
</html>