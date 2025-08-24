<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Persona</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

  <h2>Registrar Nueva Persona</h2>

  <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form action="" method="post">
    <?=csrf_field()?>
    <div class="row">
      <div class="col-md-6">
        <label for="apellidos">Apellidos *</label>
        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
      </div>
      <div class="col-md-6">
        <label for="nombres">Nombres *</label>
        <input type="text" class="form-control" id="nombres" name="nombres" required>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-md-6">
        <label>Teléfono Primario *</label>
        <input type="text" name="telprimario" class="form-control" required>
      </div>
    <div class="col-md-6">
        <label>Teléfono Alternativo</label>
        <input type="text" name="telalternativo" class="form-control">
      </div>
    </div>

    <div class="mt-3">
      <label>Email *</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mt-3">
      <label>Referencia *</label>
      <input type="text" name="referencia" class="form-control" required>
    </div>

    <div class="mt-3">
      <label>Distrito *</label>
          <select name="iddistrito" class="form-select" required>
            <option value="">Seleccione un distrito</option>
              <?php foreach ($distritos as $distrito): ?>
                  <option value="<?= $distrito['iddistrito'] ?>"><?= $distrito['distrito'] ?></option>
              <?php endforeach; ?>
            </select>
        </div>
      <div class="mt-4">
        <button type="submit" class="btn btn-success">Registrar</button>
        <a href="<?= base_url('personas') ?>" class="btn btn-secondary">Cancelar</a>
      </div>
  </form>
</div>
</body>
</html>