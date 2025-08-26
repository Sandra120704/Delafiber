<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard CRM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { min-height: 100vh; }
    .sidebar { width: 220px; min-height: 100vh; }
  </style>
</head>
<body>

<div class="d-flex">

  <!-- Sidebar -->
  <nav class="bg-dark text-white p-3 sidebar">
    <h4>DELAFIBER CRM</h4>
    <ul class="nav flex-column mt-4">
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('personas') ?>">Personas</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('leads') ?>">Leads</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('tareas') ?>">Tareas</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('reportes') ?>">Reportes</a></li>
    </ul>
  </nav>

  <!-- Contenido principal -->
  <div class="container-fluid p-4" style="margin-left: 220px;">

    <h2>Dashboard</h2>

    <!-- KPIs -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white p-3">
          <h5>Total Personas</h5>
          <h2 id="totalPersonas">12</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white p-3">
          <h5>Total Leads</h5>
          <h2 id="totalLeads">8</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-white p-3">
          <h5>Leads Nuevos</h5>
          <h2 id="leadsNuevos">5</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-danger text-white p-3">
          <h5>Leads Cerrados</h5>
          <h2 id="leadsCerrados">3</h2>
        </div>
      </div>
    </div>

    <!-- Gráfico de Personas -->
    <div class="row mb-4">
      <div class="col-md-6">
        <canvas id="graficoPersonas"></canvas>
      </div>
      <div class="col-md-6">
        <canvas id="graficoLeads"></canvas>
      </div>
    </div>

    <!-- Personas -->
    <h4>Personas</h4>
    <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#modalCrearPersona">Agregar Persona</button>
    <table class="table table-bordered">
      <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr></thead>
      <tbody id="tablaPersonas">
        <tr>
          <td>1</td>
          <td>Juan Perez</td>
          <td>juan@mail.com</td>
          <td>
            <button class="btn btn-sm btn-warning btn-editar" data-id="1" data-nombres="Juan" data-apellidos="Perez" data-email="juan@mail.com">Editar</button>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>Maria Lopez</td>
          <td>maria@mail.com</td>
          <td>
            <button class="btn btn-sm btn-warning btn-editar" data-id="2" data-nombres="Maria" data-apellidos="Lopez" data-email="maria@mail.com">Editar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Leads -->
    <h4>Leads</h4>
    <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#modalCrearLead">Agregar Lead</button>
    <table class="table table-bordered">
      <thead><tr><th>ID</th><th>Nombre</th><th>Persona</th><th>Estado</th></tr></thead>
      <tbody>
        <tr><td>1</td><td>Lead A</td><td>Juan Perez</td><td>Nuevo</td></tr>
        <tr><td>2</td><td>Lead B</td><td>Maria Lopez</td><td>En seguimiento</td></tr>
      </tbody>
    </table>

  </div>
</div>

<!-- Modal Crear Persona -->
<div class="modal fade" id="modalCrearPersona" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCrearPersona">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="nombres" placeholder="Nombres" class="form-control mb-2">
          <input type="text" name="apellidos" placeholder="Apellidos" class="form-control mb-2">
          <input type="email" name="email" placeholder="Email" class="form-control mb-2">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Persona -->
<div class="modal fade" id="modalEditarPersona" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditarPersona">
        <div class="modal-header">
          <h5 class="modal-title">Editar Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="idpersona">
          <input type="text" name="nombres" placeholder="Nombres" class="form-control mb-2">
          <input type="text" name="apellidos" placeholder="Apellidos" class="form-control mb-2">
          <input type="email" name="email" placeholder="Email" class="form-control mb-2">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Crear Lead -->
<div class="modal fade" id="modalCrearLead" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Agregar Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" placeholder="Nombre del Lead" class="form-control mb-2">
          <select class="form-select mb-2">
            <option selected>Selecciona Persona</option>
            <option>Juan Perez</option>
            <option>Maria Lopez</option>
          </select>
          <select class="form-select mb-2">
            <option>Nuevo</option>
            <option>En seguimiento</option>
            <option>Cerrado</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Gráficos -->
<script>
const ctxPersonas = document.getElementById('graficoPersonas').getContext('2d');
new Chart(ctxPersonas, {
    type: 'bar',
    data: {
        labels: ['Enero','Febrero','Marzo','Abril','Mayo'],
        datasets: [{label:'Personas Registradas', data:[5,10,7,12,8], backgroundColor:'rgba(54,162,235,0.6)'}]
    }
});

const ctxLeads = document.getElementById('graficoLeads').getContext('2d');
new Chart(ctxLeads, {
    type: 'line',
    data: {
        labels: ['Enero','Febrero','Marzo','Abril','Mayo'],
        datasets: [{label:'Leads', data:[2,5,3,7,4], backgroundColor:'rgba(75,192,192,0.4)', borderColor:'rgba(75,192,192,1)', fill:true}]
    }
});

// Abrir modal Editar Persona y rellenar campos
document.querySelectorAll('.btn-editar').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const modal = new bootstrap.Modal(document.getElementById('modalEditarPersona'));
        const form = document.getElementById('formEditarPersona');
        form.idpersona.value = btn.dataset.id;
        form.nombres.value = btn.dataset.nombres;
        form.apellidos.value = btn.dataset.apellidos;
        form.email.value = btn.dataset.email;
        modal.show();
    });
});
</script>

</body>
</html>
