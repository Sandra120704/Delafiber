<!-- app/Views/leads/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Lead</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Registrar Nuevo Lead</h2>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form action="/leads/store" method="post">
    <div class="mb-3">
      <label for="iddifusion" class="form-label">ID Difusión</label>
      <input type="number" name="iddifusion" id="iddifusion" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="idpersona" class="form-label">ID Persona</label>
      <input type="number" name="idpersona" id="idpersona" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="idusuarioregistro" class="form-label">ID Usuario Registro</label>
      <input type="number" name="idusuarioregistro" id="idusuarioregistro" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="idusuarioresponsable" class="form-label">ID Usuario Responsable</label>
      <input type="number" name="idusuarioresponsable" id="idusuarioresponsable" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="fechasignacion" class="form-label">Fecha de Asignación</label>
      <input type="date" name="fechasignacion" id="fechasignacion" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
  </form>
</div>
</body>
</html>
