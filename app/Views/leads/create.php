<form action="<?= base_url('leads/store') ?>" method="post" autocomplete="off">
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Datos del Prospecto</h5>
    </div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="nombres">Nombres</label>
          <input type="text" class="form-control" name="nombres" required>
        </div>
        <div class="form-group col-md-6">
          <label for="apellidos">Apellidos</label>
          <input type="text" class="form-control" name="apellidos" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="dni">DNI</label>
          <input type="text" class="form-control" name="dni">
        </div>
        <div class="form-group col-md-4">
          <label for="telefono">Teléfono</label>
          <input type="text" class="form-control" name="telefono" required>
        </div>
        <div class="form-group col-md-4">
          <label for="direccion">Dirección</label>
          <input type="text" class="form-control" name="direccion">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="distrito">Distrito</label>
          <select class="form-control" name="distrito">
            <!-- Opciones de distritos -->
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Información del Lead</h5>
    </div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="origen">Origen</label>
          <select class="form-control" name="origen">
            <!-- Opciones de origen -->
          </select>
        </div>
        <div class="form-group col-md-4">
          <label for="campania">Campaña</label>
          <select class="form-control" name="campania">
            <!-- Opciones de campaña -->
          </select>
        </div>
        <div class="form-group col-md-4">
          <label for="etapa">Etapa</label>
          <select class="form-control" name="etapa">
            <!-- Opciones de etapa (CAPTACION, CONTACTO, etc) -->
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="observaciones">Observaciones</label>
        <textarea class="form-control" name="observaciones"></textarea>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-success btn-block">Guardar Lead</button>
</form>
