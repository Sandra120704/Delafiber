<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Personas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Listado de Personas</h2>

     <!-- Botón para abrir modal -->
  <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearPersona">
    Registrar Persona
  </button>

  <!-- Tabla -->
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Apellidos</th>
        <th>Nombres</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Distrito</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($personas)): ?>
      <?php foreach ($personas as $p): ?>
        <tr>
          <td><?= $p['idpersona'] ?></td>
          <td><?= $p['apellidos'] ?></td>
          <td><?= $p['nombres'] ?></td>
          <td><?= $p['telprimario'] ?></td>
          <td><?= $p['email'] ?></td>
          <td><?= $p['distrito'] ?? '—' ?></td>
          <td>
            <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $p['idpersona'] ?>">Editar</button>
            <a href="<?= base_url('/personas/delete/'.$p['idpersona']) ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('¿Eliminar persona?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" class="text-center">No hay personas registradas</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->include('personas/create'); ?>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Modal para editar persona -->
<div class="modal fade" id="modalEditarPersona" tabindex="-1" aria-labelledby="modalEditarPersonaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="modalEditarPersonaContent">
      <!-- Aquí se cargará el formulario de edición vía AJAX -->
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');

      // Cargar formulario de edición vía AJAX
      fetch('<?= base_url("personas/edit/") ?>' + id)
        .then(response => response.text())
        .then(html => {
          document.getElementById('modalEditarPersonaContent').innerHTML = html;
          new bootstrap.Modal(document.getElementById('modalEditarPersona')).show();
        })
        .catch(err => console.error('Error al cargar el formulario:', err));
    });
  });
});
</script>


</html>

