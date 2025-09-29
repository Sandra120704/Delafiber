<?php helper('html'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= isset($title) ? $title : 'Delafiber - CRM' ?></title>
  
  <!-- plugins:css -->
  <?= link_tag('assets/feather/feather.css') ?>
  <?= link_tag('assets/ti-icons/css/themify-icons.css') ?>
  <?= link_tag('assets/css/vendor.bundle.base.css') ?>
  
  <!-- Plugin css for this page -->
  <?= link_tag('assets/datatables.net-bs4/dataTables.bootstrap4.css') ?>
  <?= link_tag('js/select.dataTables.min.css') ?>
  
  <!-- inject:css -->
  <?= link_tag('css/vertical-layout-light/style.css') ?>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
  
  <!-- CSRF Token para formularios -->
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="<?= base_url() ?>">
          <img src="<?= base_url('images/logo-delafiber.png') ?>" class="mr-2" alt="logo"/>
        </a>
        <a class="navbar-brand brand-logo-mini" href="<?= base_url() ?>">
          <img src="<?= base_url('images/logo-mini.svg') ?>" alt="logo"/>
        </a>
      </div>
      
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        
        <!-- Barra de búsqueda mejorada -->
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" 
                     placeholder="Buscar contactos, leads, empresas..." 
                     aria-label="search" aria-describedby="search"
                     data-url="<?= base_url('api/search') ?>">
            </div>
          </li>
        </ul>
        
        <ul class="navbar-nav navbar-nav-right">
          <!-- Notificaciones mejoradas -->
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" 
               href="#" data-toggle="dropdown" data-url="<?= base_url('api/notifications') ?>">
              <i class="icon-bell mx-0"></i>
              <span class="count" id="notification-count"><?= isset($notification_count) ? $notification_count : '0' ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notificaciones</p>
              <div id="notification-list">
                <!-- Las notificaciones se cargan dinámicamente -->
                <?php if(isset($notifications) && !empty($notifications)): ?>
                  <?php foreach($notifications as $notification): ?>
                    <a class="dropdown-item preview-item" href="<?= base_url($notification['url']) ?>">
                      <div class="preview-thumbnail">
                        <div class="preview-icon bg-<?= $notification['type'] ?>">
                          <i class="<?= $notification['icon'] ?> mx-0"></i>
                        </div>
                      </div>
                      <div class="preview-item-content">
                        <h6 class="preview-subject font-weight-normal"><?= $notification['title'] ?></h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          <?= time_elapsed_string($notification['created_at']) ?>
                        </p>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <a class="dropdown-item preview-item text-center">
                    <p class="mb-0">No hay notificaciones</p>
                  </a>
                <?php endif; ?>
              </div>
              <a class="dropdown-item text-center" href="<?= base_url('notifications/all') ?>">
                Ver todas las notificaciones
              </a>
            </div>
          </li>
          
          <!-- Perfil de usuario mejorado -->
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown" id="profileDropdown">
              <?php if (isset($user_avatar) && !empty($user_avatar)): ?>
                <img src="<?= base_url($user_avatar) ?>" alt="<?= isset($user_name) ? $user_name : 'Usuario' ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"/>
              <?php else: ?>
                <span style="display:inline-flex;width:32px;height:32px;border-radius:50%;background:#e0e0e0;align-items:center;justify-content:center;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="#888"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                </span>
              <?php endif; ?>
              <span class="d-none d-md-inline ml-2" style="font-weight:500; color:#444;">
                <?= isset($user_name) ? $user_name : 'Usuario' ?>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <div class="dropdown-header">
                <h6 class="mb-0"><?= isset($user_name) ? $user_name : 'Usuario' ?></h6>
                <small class="text-muted"><?= isset($user_email) ? $user_email : '' ?></small>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?= base_url('profile') ?>">
                <i class="ti-user text-primary"></i>
                Mi Perfil
              </a>
              <a class="dropdown-item" href="<?= base_url('settings') ?>">
                <i class="ti-settings text-primary"></i>
                Configuración
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="cerrarSesion()">
                <i class="ti-power-off text-danger"></i>
                Cerrar sesión
              </a>
              <script>
              function cerrarSesion() {
                  if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                      window.location.href = '<?= base_url('auth/logout') ?>';
                  }
              }
              </script>
            </div>
          </li>
        </ul>
        
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
      <!-- Panel de configuración de tema -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">APARIENCIA SIDEBAR</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border mr-3"></div>Claro
          </div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border mr-3"></div>Oscuro
          </div>
          <p class="settings-heading mt-2">COLORES DE HEADER</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>

      <!-- Sidebar mejorado -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <!-- 1. Dashboard -->
          <li class="nav-item <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <!-- 2. Leads (Prospectos) -->
          <li class="nav-item <?= (strpos(uri_string(), 'leads') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#leads-menu" role="button" aria-expanded="<?= (strpos(uri_string(), 'leads') !== false) ? 'true' : 'false' ?>" aria-controls="leads-menu">
              <i class="icon-target menu-icon"></i>
              <span class="menu-title">Leads</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'leads') !== false) ? 'show' : '' ?>" id="leads-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'leads') ? 'active' : '' ?>" 
                     href="<?= base_url('leads') ?>">Todos los Leads</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'leads/create') ? 'active' : '' ?>" 
                     href="<?= base_url('leads/create') ?>">Nuevo Lead</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'leads/pipeline') ? 'active' : '' ?>" 
                     href="<?= base_url('leads/pipeline') ?>">Pipeline</a>
                </li>
              </ul>
            </div>
          </li>

          <!-- 3. Contactos (Clientes) -->
          <li class="nav-item <?= (strpos(uri_string(), 'contacts') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#contacts-menu" 
               aria-expanded="false"
               aria-controls="contacts-menu">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">Contactos</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="contacts-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('contacts') ?>">Todos los Contactos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('contacts/create') ?>">Nuevo Contacto</a>
                </li>
              </ul>
            </div>
          </li>

          <!-- 4. Procesos (Campañas y Flujos) -->
          <li class="nav-item <?= (strpos(uri_string(), 'processes') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#processes-menu"
               aria-expanded="false"
               aria-controls="processes-menu">
              <i class="icon-layout menu-icon"></i>
              <span class="menu-title">Procesos</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="processes-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('campaigns') ?>">Campañas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('workflows') ?>">Flujos de trabajo</a>
                </li>
              </ul>
            </div>
          </li>
          
          <!-- 5. Reportes -->
          <li class="nav-item <?= (strpos(uri_string(), 'reports') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#reports-menu"
               aria-expanded="false"
               aria-controls="reports-menu">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">Reportes</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reports-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('reports/sales') ?>">Ventas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('reports/activities') ?>">Actividades</a>
                </li>
              </ul>
            </div>
          </li>
          
          <!-- 6. Documentación -->
          <li class="nav-item <?= (uri_string() == 'documentation') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('documentation') ?>">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Documentación</span>
            </a>
          </li>
          
          <?php if(isset($user_role) && $user_role == 'admin'): ?>
          <!-- 7. Administración (solo para admins) -->
          <li class="nav-item <?= (strpos(uri_string(), 'admin') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#admin-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'admin') !== false) ? 'true' : 'false' ?>" 
               aria-controls="admin-menu">
              <i class="icon-lock menu-icon"></i>
              <span class="menu-title">Administración</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'admin') !== false) ? 'show' : '' ?>" id="admin-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('admin/users') ?>">Usuarios</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('admin/settings') ?>">Configuración del Sistema</a>
                </li>
              </ul>
            </div>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
      <div class="main-panel">
        <div class="content-wrapper">
          <?= $this->renderSection('content') ?>
        </div>
      </div>
    </div>

    <!-- Scripts necesarios para DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Bootstrap JS (al final del body) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </div>
</body>

</html>
