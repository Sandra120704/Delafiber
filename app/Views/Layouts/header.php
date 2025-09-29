<?php helper('html'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= isset($title) ? $title : 'Delafiber - CRM' ?></title>
  
  <?= link_tag('assets/feather/feather.css') ?>
  <?= link_tag('assets/ti-icons/css/themify-icons.css') ?>
  <?= link_tag('assets/css/vendor.bundle.base.css') ?>
  <?= link_tag('assets/datatables.net-bs4/dataTables.bootstrap4.css') ?>
  <?= link_tag('css/vertical-layout-light/style.css') ?>
  
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  
  <style>
  /* Mejoras de UX para el menú */
  .nav-item {
    transition: all 0.3s ease;
  }
  
  .nav-item:hover {
    background-color: rgba(0,0,0,0.02);
  }
  
  .nav-link {
    cursor: pointer;
    position: relative;
    padding: 12px 20px;
  }
  
  .nav-item.active {
    background-color: rgba(103, 126, 234, 0.1);
  }
  
  .nav-item.active > .nav-link {
    color: #667eea;
    font-weight: 600;
  }
  
  /* Mejorar hover en tarjetas */
  .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  /* Tarjetas de métricas con hover */
  .border-left-primary { border-left: 4px solid #4e73df; }
  .border-left-success { border-left: 4px solid #1cc88a; }
  .border-left-info { border-left: 4px solid #36b9cc; }
  .border-left-warning { border-left: 4px solid #f6c23e; }
  .border-left-danger { border-left: 4px solid #e74a3b; }
  
  .border-left-primary:hover { box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3); }
  .border-left-success:hover { box-shadow: 0 2px 8px rgba(28, 200, 138, 0.3); }
  .border-left-warning:hover { box-shadow: 0 2px 8px rgba(246, 194, 62, 0.3); }
  .border-left-danger:hover { box-shadow: 0 2px 8px rgba(231, 74, 59, 0.3); }
  
  /* Botón flotante mejorado */
  .fab-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
  }
  
  .fab-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    text-decoration: none;
  }
  
  .fab-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
  }
  
  .fab-button i {
    font-size: 24px;
  }
  
  /* Animación para elementos interactivos */
  .quick-action {
    transition: all 0.2s ease;
  }
  
  .quick-action:hover {
    transform: scale(1.05);
  }
  
  /* Mejorar visibilidad de elementos clickeables */
  .lead-card, .task-item {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .lead-card:hover, .task-item:hover {
    background-color: rgba(102, 126, 234, 0.05);
    border-color: #667eea !important;
  }
  
  /* Welcome banner más atractivo */
  .welcome-banner {
    padding: 20px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 20px;
  }
  
  .welcome-banner h3 {
    color: white;
    margin-bottom: 8px;
  }
  
  .welcome-banner p {
    color: rgba(255,255,255,0.9);
    margin: 0;
  }
  </style>
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="<?= base_url('dashboard') ?>">
          <img src="<?= base_url('images/logo-delafiber.png') ?>" class="mr-2" alt="logo"/>
        </a>
        <a class="navbar-brand brand-logo-mini" href="<?= base_url('dashboard') ?>">
          <img src="<?= base_url('images/logo-mini.svg') ?>" alt="logo"/>
        </a>
      </div>
      
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" 
                     placeholder="Buscar contactos, leads..." 
                     aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" 
               href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"><?= isset($notification_count) ? $notification_count : '0' ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notificaciones</p>
              <a class="dropdown-item preview-item text-center">
                <p class="mb-0">No hay notificaciones nuevas</p>
              </a>
            </div>
          </li>
          
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown" id="profileDropdown">
              <span style="display:inline-flex;width:32px;height:32px;border-radius:50%;background:#667eea;align-items:center;justify-content:center;color:white;font-weight:bold;">
                <?= strtoupper(substr(session()->get('nombre_completo') ?? 'U', 0, 1)) ?>
              </span>
              <span class="d-none d-md-inline ml-2" style="font-weight:500; color:#444;">
                <?= session()->get('nombre_completo') ?? 'Usuario' ?>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <div class="dropdown-header">
                <h6 class="mb-0"><?= session()->get('nombre_completo') ?? 'Usuario' ?></h6>
                <small class="text-muted"><?= session()->get('correo') ?? '' ?></small>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                <i class="ti-user text-primary"></i> Mi Perfil
              </a>
              <a class="dropdown-item" href="<?= base_url('configuracion') ?>">
                <i class="ti-settings text-primary"></i> Configuración
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="cerrarSesion(); return false;">
                <i class="ti-power-off text-danger"></i> Cerrar sesión
              </a>
            </div>
          </li>
        </ul>
        
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
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

      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <li class="nav-item <?= (strpos(uri_string(), 'leads') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#leads-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'leads') !== false) ? 'true' : 'false' ?>">
              <i class="icon-target menu-icon"></i>
              <span class="menu-title">Leads</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'leads') !== false) ? 'show' : '' ?>" id="leads-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('leads') ?>">Todos los Leads</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('leads/create') ?>">Nuevo Lead</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('leads/pipeline') ?>">Pipeline</a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item <?= (strpos(uri_string(), 'campanias') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#campaigns-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'campanias') !== false) ? 'true' : 'false' ?>">
              <i class="icon-layers menu-icon"></i>
              <span class="menu-title">Campañas</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'campanias') !== false) ? 'show' : '' ?>" id="campaigns-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('campanias') ?>">Todas las Campañas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('campanias/create') ?>">Nueva Campaña</a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item <?= (strpos(uri_string(), 'tareas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('tareas') ?>">
              <i class="icon-calendar menu-icon"></i>
              <span class="menu-title">Mis Tareas</span>
            </a>
          </li>

          <li class="nav-item <?= (strpos(uri_string(), 'personas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#contacts-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'personas') !== false) ? 'true' : 'false' ?>">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">Contactos</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'personas') !== false) ? 'show' : '' ?>" id="contacts-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('personas') ?>">Todos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('personas/create') ?>">Nuevo</a>
                </li>
              </ul>
            </div>
          </li>
          
          <li class="nav-item <?= (strpos(uri_string(), 'reporte') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('reportes') ?>">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">Reportes</span>
            </a>
          </li>
          
          <?php if(session()->get('rol') == 'admin'): ?>
          <li class="nav-item <?= (strpos(uri_string(), 'configuracion') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('configuracion') ?>">
              <i class="icon-settings menu-icon"></i>
              <span class="menu-title">Configuración</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
      
      <div class="main-panel">
        <div class="content-wrapper">
          <?= $this->renderSection('content') ?>
        </div>
        
        