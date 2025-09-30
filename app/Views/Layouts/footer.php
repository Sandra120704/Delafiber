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
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller ends -->

  <!-- jQuery primero -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap 5 Bundle (incluye Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- plugins:js -->
  <script src="<?= base_url('assets/js/vendor.bundle.base.js') ?>"></script>

  <!-- Plugin js for this page -->
  <script src="<?= base_url('assets/chart.js/Chart.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/dataTables.bootstrap4.min.js') ?>"></script>

  <!-- inject:js -->
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/template.js') ?>"></script>
  <script src="<?= base_url('assets/js/settings.js') ?>"></script>
  <script src="<?= base_url('assets/js/todolist.js') ?>"></script>

  <!-- Custom js for this page-->

  <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
  <script src="<?= base_url('assets/js/Chart.roundedBarCharts.js') ?>"></script>

  <!-- Script para funcionalidad del menú -->
  <script>
  $(document).ready(function() {
    // Toggle sidebar en desktop (minimizar/expandir)
    $('#sidebarToggle').on('click', function(e) {
      e.preventDefault();
      $('body').toggleClass('sidebar-icon-only');
    });
    
    // Toggle sidebar en mobile (mostrar/ocultar)
    $('#mobileMenuToggle').on('click', function(e) {
      e.preventDefault();
      $('.sidebar-offcanvas').toggleClass('active');
    });
    
    // Cerrar sidebar mobile al hacer click fuera
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.sidebar-offcanvas, #mobileMenuToggle').length) {
        $('.sidebar-offcanvas').removeClass('active');
      }
    });
    
    // Función para cerrar sesión
    window.cerrarSesion = function() {
      if(confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = '<?= base_url('auth/logout') ?>';
      }
    };
  });
  </script>
</body>
</html>