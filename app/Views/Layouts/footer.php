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
  <script src="<?= base_url('assets/chart.js/Chart.min.js') ?>"></script>
  <script src="<?= base_url('assets/datatables.net/jquery.dataTables.js') ?>"></script>
  <script src="<?= base_url('assets/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <!-- Componente Select Buscador JS -->
  <script src="<?= base_url('js/components/select-buscador.js') ?>"></script>

  <!-- Custom js for this page-->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/Chart.roundedBarCharts.js') ?>"></script>

  <!-- Configuración global -->
  <script src="<?= base_url('js/config/datatables-config.js') ?>"></script>
  <script src="<?= base_url('js/config/sweetalert-config.js') ?>"></script>
  <script src="<?= base_url('js/layout/sidebar.js') ?>"></script>

  <!-- Mensajes Flash con SweetAlert2 -->
  <script>
  $(document).ready(function() {
    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
      icon: 'success',
      title: '¡Éxito!',
      text: '<?= addslashes(session()->getFlashdata('success')) ?>',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Aceptar',
      timer: 3000,
      timerProgressBar: true
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: '<?= addslashes(session()->getFlashdata('error')) ?>',
      confirmButtonColor: '#d33',
      confirmButtonText: 'Aceptar'
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: '<?= addslashes(session()->getFlashdata('warning')) ?>',
      confirmButtonColor: '#f39c12',
      confirmButtonText: 'Aceptar'
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
    Swal.fire({
      icon: 'info',
      title: 'Información',
      text: '<?= addslashes(session()->getFlashdata('info')) ?>',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Aceptar'
    });
    <?php endif; ?>
  });
  </script>

  <!-- Sistema de Notificaciones en Tiempo Real -->
  <script src="<?= base_url('js/notificaciones/notificaciones-sistema.js?v=' . time()) ?>"></script>

  <!-- Scripts específicos de cada página -->
  <?= $this->renderSection('scripts') ?>
  
  <!-- Estilos CSS para Notificaciones -->
  <style>
    /* Notificaciones */
    .notificacion-item {
      padding: 12px 16px;
      transition: background-color 0.2s;
      border-left: 3px solid transparent;
    }

    .notificacion-item:hover {
      background-color: #f8f9fa;
    }

    .notificacion-item.no-leida {
      background-color: #e3f2fd;
      border-left-color: #2196F3;
    }

    .notificacion-item.leida {
      opacity: 0.7;
    }

    .notificacion-nueva {
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateX(-100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* Toast de notificación */
    .toast-notificacion {
      position: fixed;
      top: 80px;
      right: 20px;
      width: 350px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 9999;
      opacity: 0;
      transform: translateX(400px);
      transition: all 0.3s ease-out;
    }

    .toast-notificacion.show {
      opacity: 1;
      transform: translateX(0);
    }

    .toast-header {
      padding: 12px 16px;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      align-items: center;
      background-color: #f8f9fa;
      border-radius: 8px 8px 0 0;
    }

    .toast-body {
      padding: 12px 16px;
    }
  </style>
</body>
</html>