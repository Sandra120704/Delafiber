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

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <!-- Formulario de registro -->
  <form action="<?= base_url('/personas/store') ?>" method="post" class="mb-4">
    <div class="row">
      <div class="col">
        <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" required>
      </div>
      <div class="col">
        <input type="text" name="nombres" class="form-control" placeholder="Nombres" required>
      </div>
      <div class="col">
        <input type="text" name="telprimario" class="form-control" placeholder="Teléfono" required>
      </div>
      <div class="col">
        <select name="iddistrito" class="form-control" required>
          <option value="">Seleccione distrito</option>
          <?php foreach ($distritos as $d): ?>
            <option value="<?= $d['iddistrito'] ?>"><?= $d['distrito'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col">
        <button type="submit" class="btn btn-primary">Registrar</button>
      </div>
    </div>
  </form>

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
              <a href="<?= base_url('/personas/edit/'.$p['idpersona']) ?>" class="btn btn-sm btn-warning">Editar</a>
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
</body>
</html>
