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
      </div>
    </div>   
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="<?= base_url('assets/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/chart.js/Chart.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables/js/dataTables.bootstrap4.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/template.js') ?>"></script>
  <script src="<?= base_url('assets/js/settings.js') ?>"></script>
  <script src="<?= base_url('assets/js/todolist.js') ?>"></script>
  <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
  
  <script>
  function cerrarSesion() {
      if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
          window.location.href = '<?= base_url('auth/logout') ?>';
      }
  }
  </script>
  
  <?= $this->renderSection('scripts') ?>
</body>
</html>