<?= $header ?>
<h4>Agregar persona</h4>
<form method="post" action="/personas/create">
  <div class="mb-2">
    <label>Nombres</label>
    <input type="text" name="nombres" class="form-control" required>
  </div>
  <div class="mb-2">
    <label>Apellidos</label>
    <input type="text" name="apellidos" class="form-control" required>
  </div>
  <div class="mb-2">
    <label>DNI</label>
    <input type="text" name="dni" class="form-control">
  </div>
  <div class="mb-2">
    <label>Correo</label>
    <input type="email" name="correo" class="form-control">
  </div>
  <div class="mb-2">
    <label>Teléfono</label>
    <input type="text" name="telefono" class="form-control">
  </div>
  <div class="mb-2">
    <label>Dirección</label>
    <input type="text" name="direccion" class="form-control">
  </div>
  <button type="submit" class="btn btn-success">Guardar</button>
  <a href="/personas" class="btn btn-secondary">Cancelar</a>
</form>
<?= $footer ?>
