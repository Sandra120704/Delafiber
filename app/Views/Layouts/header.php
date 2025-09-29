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
  
  <!-- inject:css -->
  <?= link_tag('css/vertical-layout-light/style.css') ?>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
  
  <!-- CSRF Token para formularios -->
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
</head>

<body>
  <div class="container-scroller">
    <!-- Navbar -->
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
        
        <!-- Barra de búsqueda -->
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
          <!-- Notificaciones -->
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
          
          <!-- Perfil de usuario -->
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
                <i class="ti-user text-primary"></i>
                Mi Perfil
              </a>
              <a class="dropdown-item" href="<?= base_url('configuracion') ?>">
                <i class="ti-settings text-primary"></i>
                Configuración
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="cerrarSesion(); return false;">
                <i class="ti-power-off text-danger"></i>
                Cerrar sesión
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

      <!-- Sidebar -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <!-- Dashboard -->
          <li class="nav-item <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <!-- Leads con submenú -->
          <li class="nav-item <?= (strpos(uri_string(), 'leads') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#leads-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'leads') !== false) ? 'true' : 'false' ?>" 
               aria-controls="leads-menu">
              <i class="icon-target menu-icon"></i>
              <span class="menu-title">Leads</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'leads') !== false) ? 'show' : '' ?>" id="leads-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'leads' || uri_string() == 'leads/index') ? 'active' : '' ?>" 
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

          <!-- Campañas con submenú -->
          <li class="nav-item <?= (strpos(uri_string(), 'campanias') !== false || strpos(uri_string(), 'campania') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#campaigns-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'campanias') !== false) ? 'true' : 'false' ?>" 
               aria-controls="campaigns-menu">
              <i class="icon-layers menu-icon"></i>
              <span class="menu-title">Campañas</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'campanias') !== false) ? 'show' : '' ?>" id="campaigns-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'campanias' || uri_string() == 'campanias/index') ? 'active' : '' ?>" 
                     href="<?= base_url('campanias') ?>">Todas las Campañas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'campanias/create') ? 'active' : '' ?>" 
                     href="<?= base_url('campanias/create') ?>">Nueva Campaña</a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Tareas -->
          <li class="nav-item <?= (strpos(uri_string(), 'tareas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('tareas') ?>">
              <i class="icon-calendar menu-icon"></i>
              <span class="menu-title">Mis Tareas</span>
            </a>
          </li>

          <!-- Contactos con submenú -->
          <li class="nav-item <?= (strpos(uri_string(), 'personas') !== false) ? 'active' : '' ?>">
            <a class="nav-link" data-toggle="collapse" href="#contacts-menu" 
               aria-expanded="<?= (strpos(uri_string(), 'personas') !== false) ? 'true' : 'false' ?>" 
               aria-controls="contacts-menu">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">Contactos</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= (strpos(uri_string(), 'personas') !== false) ? 'show' : '' ?>" id="contacts-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'personas' || uri_string() == 'personas/index') ? 'active' : '' ?>" 
                     href="<?= base_url('personas') ?>">Todos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?= (uri_string() == 'personas/create' || uri_string() == 'personas/crear') ? 'active' : '' ?>" 
                     href="<?= base_url('personas/create') ?>">Nuevo</a>
                </li>
              </ul>
            </div>
          </li>
          
          <!-- Reportes -->
          <li class="nav-item <?= (strpos(uri_string(), 'reporte') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('reportes') ?>">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">Reportes</span>
            </a>
          </li>
          
          <?php if(session()->get('rol') == 'admin'): ?>
          <!-- Administración -->
          <li class="nav-item <?= (strpos(uri_string(), 'configuracion') !== false) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('configuracion') ?>">
              <i class="icon-settings menu-icon"></i>
              <span class="menu-title">Configuración</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
      
      <!-- Contenido principal -->
      <div class="main-panel">
        <div class="content-wrapper">
          <?= $this->renderSection('content') ?>
        </div>