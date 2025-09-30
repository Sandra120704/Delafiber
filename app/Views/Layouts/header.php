<?php helper('html'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= isset($title) ? $title : 'Delafiber - CRM' ?></title>

  <!-- CSS Required -->
  <?= link_tag('assets/feather/feather.css') ?>
  <?= link_tag('assets/ti-icons/css/themify-icons.css') ?>
  <?= link_tag('assets/css/vendor.bundle.base.css') ?>
  <?= link_tag('assets/datatables.net-bs4/dataTables.bootstrap4.css') ?>
  <?= link_tag('css/vertical-layout-light/style.css') ?>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Favicon y CSRF -->
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
  <meta name="csrf-token" content="<?= csrf_hash() ?>">

  <style>
    .nav-item { transition: all 0.3s ease; }
    .nav-item:hover { background-color: rgba(0,0,0,0.02); }
    .nav-link { cursor: pointer; padding: 12px 20px; }
    .nav-item.active { background-color: rgba(103, 126, 234, 0.1); }
    .nav-item.active > .nav-link { color: #667eea; font-weight: 600; }
    .user-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; background: #e0e0e0; }
    .notification-badge { 
      position: absolute; top: 5px; right: 5px; 
      background: #ff4757; color: white; border-radius: 50%; 
      padding: 2px 6px; font-size: 10px; min-width: 18px; text-align: center;
    }
    
    /* Sidebar collapse */
    .sidebar-icon-only .sidebar { width: 70px; }
    .sidebar-icon-only .sidebar .nav .nav-item .nav-link { padding: 12px 20px; text-align: center; }
    .sidebar-icon-only .sidebar .nav .nav-item .nav-link .menu-title { display: none; }
    .sidebar-icon-only .sidebar .nav .nav-item .nav-link .menu-arrow { display: none; }
    .sidebar-icon-only .main-panel { width: calc(100% - 70px); }
    
    /* Mobile sidebar */
    @media (max-width: 991px) {
      .sidebar-offcanvas { 
        position: fixed; max-height: 100vh; 
        top: 0; bottom: 0; overflow: auto; 
        left: -260px; transition: all 0.25s ease-out; z-index: 1050;
      }
      .sidebar-offcanvas.active { left: 0; box-shadow: 0 0 20px rgba(0,0,0,0.3); }
    }
    
    /* Botón flotante WhatsApp */
    .fab-whatsapp { 
      position: fixed; bottom: 30px; right: 30px; z-index: 999;
      display: flex; align-items: center; justify-content: center;
      width: 60px; height: 60px; 
      background: #25D366; color: white; 
      border-radius: 50%; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
      text-decoration: none; transition: all 0.3s ease;
    }
    .fab-whatsapp:hover { 
      transform: scale(1.1); 
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6); 
      color: white; 
    }
  </style>
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
          <!-- Notificaciones -->
          <li class="nav-item dropdown">
            <a class="nav-link position-relative" href="#" id="notificationDropdown" 
               data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ti-bell"></i>
              <span class="notification-badge"><?= isset($notification_count) ? $notification_count : '0' ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
              <li><h6 class="dropdown-header">Notificaciones</h6></li>
              <?php if(isset($notifications) && !empty($notifications)): ?>
                <?php foreach($notifications as $notification): ?>
                  <li>
                    <a class="dropdown-item" href="<?= base_url($notification['url']) ?>">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <i class="<?= $notification['icon'] ?> text-<?= $notification['type'] ?>"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-0"><?= $notification['title'] ?></h6>
                          <small class="text-muted"><?= $notification['time'] ?></small>
                        </div>
                      </div>
                    </a>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li><a class="dropdown-item text-center">No hay notificaciones</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-center" href="<?= base_url('notificaciones') ?>">Ver todas</a></li>
            </ul>
          </li>

          <!-- Perfil -->
          <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" href="#" id="profileDropdown" 
               data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-avatar d-flex align-items-center justify-content-center bg-primary text-white">
                <?= strtoupper(substr(session()->get('nombre_completo') ?? 'U', 0, 1)) ?>
              </div>
              <span class="d-none d-md-inline ms-2"><?= session()->get('nombre_completo') ?? 'Usuario' ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li>
                <div class="dropdown-header">
                  <h6 class="mb-0"><?= session()->get('nombre_completo') ?? 'Usuario' ?></h6>
                  <small class="text-muted"><?= session()->get('correo') ?? '' ?></small>
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
            <a class="nav-link" href="<?= base_url('tareas') ?>">
              <i class="ti-calendar menu-icon"></i>
              <span class="menu-title">Mis Tareas</span>
              <?php if(isset($tareas_pendientes_count) && $tareas_pendientes_count > 0): ?>
                <span class="badge badge-danger ms-auto"><?= $tareas_pendientes_count ?></span>
              <?php endif; ?>
            </a>
          </li>

          <!-- Reportes -->
          <li class="nav-item <?= (strpos(uri_string(), 'reportes') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('reportes') ?>">
              <i class="ti-bar-chart menu-icon"></i>
              <span class="menu-title">Reportes</span>
            </a>
          </li>

          <!-- Contactos -->
          <li class="nav-item <?= (strpos(uri_string(), 'personas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#contacts-menu" role="button"
               aria-expanded="<?= (strpos(uri_string(), 'personas') !== false) ? 'true' : 'false' ?>">
              <i class="ti-id-badge menu-icon"></i>
              <span class="menu-title">Contactos</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'personas') !== false) ? 'show' : '' ?>" id="contacts-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('personas') ?>">Todos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('personas/create') ?>">Nuevo</a></li>
              </ul>
            </div>
          </li>

          <!-- Usuarios (solo admin) -->
          <?php if(session()->get('rol') == 'admin'): ?>
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