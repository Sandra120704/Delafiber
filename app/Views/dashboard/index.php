<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard CRM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
      <div class="sidebar-sticky p-3">
        <h4 class="text-white">CRM</h4>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link text-white" href="<?= base_url('personas'); ?>">👤 Personas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="<?= base_url('leads'); ?>">📋 Leads</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="<?= base_url('campanas'); ?>">🎯 Campañas</a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Main content -->
    <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
      </div>

      <!-- Tarjetas de métricas -->
      <div class="row">
        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h5 class="card-title">Personas registradas</h5>
              <p class="card-text fs-3"><?= $totalPersonas ?? 0 ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h5 class="card-title">Leads activos</h5>
              <p class="card-text fs-3"><?= $totalLeads ?? 0 ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h5 class="card-title">Campañas</h5>
              <p class="card-text fs-3"><?= $totalCampanas ?? 0 ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfica (placeholder) -->
      <div class="card shadow-sm mt-3">
        <div class="card-body">
          <h5 class="card-title">Leads por estado</h5>
          <canvas id="graficoLeads"></canvas>
        </div>
      </div>

    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('graficoLeads');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Nuevo', 'Contactado', 'Interesado', 'No interesado', 'Perdido'],
      datasets: [{
        label: 'Cantidad',
        data: <?= json_encode($leadsPorEstado ?? [0,0,0,0,0]); ?>,
        borderWidth: 1
      }]
    }
  });
</script>
</body>
</html>
