<?php helper('html'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title><?= isset($title) ? $title : 'Delafiber - CRM' ?></title>

  <!-- CSS Required -->
  <?= link_tag('assets/feather/feather.css') ?>
  <?= link_tag('assets/ti-icons/css/themify-icons.css') ?>
  <?= link_tag('assets/css/vendor.bundle.base.css') ?>
  <?= link_tag('css/vertical-layout-light/style.css') ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  
  <!-- Componente Select Buscador CSS -->
  <link rel="stylesheet" href="<?= base_url('css/components/select-buscador.css') ?>">

  <!-- Layout CSS -->
  <link rel="stylesheet" href="<?= base_url('css/dashboard/dashboard.css') ?>">

  <!-- CSS específico de cada página -->
  <?= $this->renderSection('styles') ?>

  <!-- Favicon y CSRF -->
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="base-url" content="<?= base_url() ?>">
  
  <!-- Variable global baseUrl -->
  <script>
    // Declarar baseUrl globalmente ANTES de cargar cualquier otro script
    var baseUrl = '<?= rtrim(base_url(), '/') ?>';
  </script>
</head>
<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo me-3" href="<?= base_url('dashboard') ?>">
          <img src="<?= base_url('images/logo-delafiber.png') ?>" alt="Delafiber Logo" style="height: 36px;"/>
        </a>
        <a class="navbar-brand brand-logo-mini" href="<?= base_url('dashboard') ?>">
          <img src="<?= base_url('images/logo-mini.svg') ?>" alt="Logo Mini" style="height: 36px;"/>
        </a>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <!-- Botón minimizar sidebar -->
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" id="sidebarToggle">
          <span class="icon-menu"></span>
        </button>

        <!-- Barra de búsqueda -->
        <ul class="navbar-nav me-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Buscar contactos, leads..." id="searchInput">
              <button class="btn btn-outline-light" type="button"><i class="ti-search"></i></button>
            </div>
          </li>
        </ul>

        <!-- Notificaciones y Perfil -->
        <ul class="navbar-nav navbar-nav-right">
          <!-- Notificaciones en Tiempo Real -->
          <li class="nav-item dropdown">
            <a class="nav-link position-relative" href="#" id="notificacionesDropdown" 
               data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ti-bell" style="font-size: 20px;"></i>
              <span class="badge bg-danger position-absolute" id="notificaciones-badge" 
                    style="display: none; top: 5px; right: 5px; font-size: 10px; padding: 2px 5px;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 380px; max-height: 500px; overflow-y: auto;">
              <!-- Header -->
              <div class="dropdown-header d-flex justify-content-between align-items-center bg-light" style="padding: 12px 16px;">
                <strong><i class="ti-bell me-2"></i>Notificaciones</strong>
                <button class="btn btn-sm btn-link text-primary p-0" id="btn-marcar-todas-leidas" 
                        style="text-decoration: none; font-size: 12px;">
                  Marcar todas como leídas
                </button>
              </div>
              <div class="dropdown-divider m-0"></div>
              
              <!-- Lista de notificaciones (se llena con JavaScript) -->
              <div id="notificaciones-lista" style="max-height: 400px; overflow-y: auto;">
                <div class="text-center py-4 text-muted">
                  <i class="ti-bell" style="font-size: 48px; opacity: 0.3;"></i>
                  <p class="mb-0 mt-2">Cargando notificaciones...</p>
                </div>
              </div>
              
              <div class="dropdown-divider m-0"></div>
              <a href="<?= base_url('notificaciones') ?>" class="dropdown-item text-center text-primary py-2">
                <small><strong>Ver todas las notificaciones</strong></small>
              </a>
            </div>
          </li>

          <!-- Perfil -->
          <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" href="#" id="profileDropdown" 
               data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-avatar d-flex align-items-center justify-content-center bg-primary text-white">
                <?= strtoupper(substr(session()->get('nombre_completo') ?? session()->get('usuario') ?? 'U', 0, 1)) ?>
              </div>
              <span class="d-none d-md-inline ms-2"><?= session()->get('nombre_completo') ?? session()->get('usuario') ?? 'Usuario' ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li>
                <div class="dropdown-header">
                  <h6 class="mb-0"><?= session()->get('nombre_completo') ?? session()->get('usuario') ?? 'Usuario' ?></h6>
                  <small class="text-muted"><?= session()->get('correo') ?? session()->get('email') ?? '' ?></small>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= base_url('perfil') ?>"><i class="ti-user me-2"></i>Mi Perfil</a></li>
              <li><a class="dropdown-item" href="<?= base_url('configuracion') ?>"><i class="ti-settings me-2"></i>Configuración</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); cerrarSesion();">
                  <i class="ti-power-off text-danger me-2"></i>Cerrar sesión
                </a>
              </li>
            </ul>
          </li>
        </ul>

        <!-- Botón menú mobile -->
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" 
                type="button" id="mobileMenuToggle">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <!-- Container -->
    <div class="container-fluid page-body-wrapper">
      <!-- Sidebar -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <!-- Dashboard -->
          <li class="nav-item <?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">
              <i class="ti-home menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

          <li class="nav-item nav-category">
            <span class="nav-link">VENTAS</span>
          </li>

          <!-- Leads -->
          <li class="nav-item <?= (strpos(uri_string(), 'leads') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#leads-menu" role="button"
               aria-expanded="<?= (strpos(uri_string(), 'leads') !== false) ? 'true' : 'false' ?>">
              <i class="ti-target menu-icon"></i>
              <span class="menu-title">Leads</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'leads') !== false) ? 'show' : '' ?>" id="leads-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('leads') ?>">Todos los Leads</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('leads/create') ?>">Nuevo Lead</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('leads/pipeline') ?>">Pipeline</a></li>
              </ul>
            </div>
          </li>

          <!-- Campañas -->
          <li class="nav-item <?= (strpos(uri_string(), 'campanias') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#campaigns-menu" role="button"
               aria-expanded="<?= (strpos(uri_string(), 'campanias') !== false) ? 'true' : 'false' ?>">
              <i class="ti-layers menu-icon"></i>
              <span class="menu-title">Campañas</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'campanias') !== false) ? 'show' : '' ?>" id="campaigns-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('campanias') ?>">Todas las Campañas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('campanias/create') ?>">Nueva Campaña</a></li>
              </ul>
            </div>
          </li>

          <!-- Tareas -->
          <li class="nav-item <?= (strpos(uri_string(), 'tareas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#tareas-menu" role="button"
               aria-expanded="<?= (strpos(uri_string(), 'tareas') !== false) ? 'true' : 'false' ?>">
              <i class="ti-calendar menu-icon"></i>
              <span class="menu-title">Mis Tareas</span>
              <?php if(isset($tareas_pendientes_count) && $tareas_pendientes_count > 0): ?>
                <span class="badge badge-danger ms-auto"><?= $tareas_pendientes_count ?></span>
              <?php endif; ?>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'tareas') !== false) ? 'show' : '' ?>" id="tareas-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('tareas') ?>">Lista de Tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('tareas/calendario') ?>">Calendario</a></li>
              </ul>
            </div>
          </li>

          <!-- Cotizaciones -->
          <li class="nav-item <?= (strpos(uri_string(), 'cotizaciones') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#cotizaciones-menu" role="button"
               aria-expanded="<?= (strpos(uri_string(), 'cotizaciones') !== false) ? 'true' : 'false' ?>">
              <i class="ti-receipt menu-icon"></i>
              <span class="menu-title">Cotizaciones</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'cotizaciones') !== false) ? 'show' : '' ?>" id="cotizaciones-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('cotizaciones') ?>">Todas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('cotizaciones/create') ?>">Nueva</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('servicios') ?>">Servicios</a></li>
              </ul>
            </div>
          </li>

          <li class="nav-item nav-category">
            <span class="nav-link">ANÁLISIS</span>
          </li>

          <!-- Reportes -->
          <li class="nav-item <?= (strpos(uri_string(), 'reportes') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('reportes') ?>">
              <i class="ti-bar-chart menu-icon"></i>
              <span class="menu-title">Reportes</span>
            </a>
          </li>

          <!-- Mapa -->
          <li class="nav-item <?= (strpos(uri_string(), 'mapa') !== false || strpos(uri_string(), 'crm-campanas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('mapa') ?>">
              <i class="ti-map-alt menu-icon"></i>
              <span class="menu-title">Mapa Territorial</span>
            </a>
          </li>

          <!-- WhatsApp (Próximamente) -->
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="Swal.fire('Próximamente', 'Módulo de WhatsApp en desarrollo', 'info'); return false;">
              <i class="ti-comments menu-icon text-success"></i>
              <span class="menu-title">WhatsApp</span>
              <span class="badge badge-warning badge-pill ms-auto">Pronto</span>
            </a>
          </li>

          <li class="nav-item nav-category">
            <span class="nav-link">GESTIÓN</span>
          </li>

          <!-- Contactos -->
          <li class="nav-item <?= (strpos(uri_string(), 'personas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('personas') ?>">
              <i class="ti-id-badge menu-icon"></i>
              <span class="menu-title">Contactos</span>
            </a>
          </li>

          <!-- Usuarios (solo admin) -->
          <?php if(session()->get('nombreRol') == 'Administrador' || session()->get('idrol') == 1): ?>
          <li class="nav-item <?= (strpos(uri_string(), 'usuarios') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('usuarios') ?>">
              <i class="ti-user menu-icon"></i>
              <span class="menu-title">Usuarios</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>

      <div class="main-panel">
        <div class="content-wrapper">