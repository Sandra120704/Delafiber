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

  <!-- Custom js for this page-->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/Chart.roundedBarCharts.js') ?>"></script>

  <!-- Script para funcionalidad del menú -->
  <script>
  $(document).ready(function() {
    // Inicializar colapsos de Bootstrap para los menús
    $('.nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
      e.preventDefault();
      const target = $(this).data('bs-target');
      $(target).collapse('toggle');
    });

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
    
    // Función para cerrar sesión con SweetAlert2
    window.cerrarSesion = function() {
      Swal.fire({
        title: '¿Cerrar sesión?',
        text: '¿Estás seguro que deseas salir del sistema?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="ti-power-off"></i> Sí, salir',
        cancelButtonText: '<i class="ti-close"></i> Cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Cerrando sesión...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          setTimeout(() => {
            window.location.href = '<?= base_url('auth/logout') ?>';
          }, 500);
        }
      });
    };

    // Configurar Toast global
    window.Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    // Función helper para mostrar toast
    window.showToast = function(icon, title) {
      Toast.fire({ icon: icon, title: title });
    };

    // Función global para confirmar eliminación
    window.confirmarEliminacion = function(titulo, texto, url) {
      Swal.fire({
        title: titulo || '¿Estás seguro?',
        html: texto || 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="ti-trash"></i> Sí, eliminar',
        cancelButtonText: '<i class="ti-close"></i> Cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Eliminando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          window.location.href = url;
        }
      });
    };

    // Búsqueda global
    $('#searchInput').on('keypress', function(e) {
      if (e.key === 'Enter') {
        const query = $(this).val().trim();
        if (query.length >= 3) {
          window.location.href = '<?= base_url('buscar?q=') ?>' + encodeURIComponent(query);
        } else {
          showToast('info', 'Ingresa al menos 3 caracteres para buscar');
        }
      }
    });

    // Atajo de teclado Ctrl+K para búsqueda
    $(document).on('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        $('#searchInput').focus();
      }
    });

    // Mostrar mensajes flash con SweetAlert2
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

  <!-- Scripts específicos de cada página -->
  <?= $this->renderSection('scripts') ?>
</body>
</html>