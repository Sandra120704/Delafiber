<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-5" style="max-width: 400px;">
  <h3 class="mb-4 text-center">Iniciar Sesión</h3>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('/login_action') ?>" method="post">
    <div class="mb-3">
      <label for="nombreusuario" class="form-label">Usuario</label>
      <input type="text" name="nombreusuario" id="nombreusuario" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="claveacceso" class="form-label">Contraseña</label>
      <input type="password" name="claveacceso" id="claveacceso" class="form-control" required />
    </div>

    <button type="submit" class="btn btn-primary w-100">Entrar</button>
  </form>
</div>

</body>
</html>
