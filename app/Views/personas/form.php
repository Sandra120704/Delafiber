<form action="<?= isset($persona) ? base_url('personas/update/' . $persona['idpersona']) : base_url('personas/store') ?>" method="post">
  <?= csrf_field() ?>
  <?php if(isset($persona)): ?>
    <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
  <?php endif; ?>

  <div class="modal-header">
    <h5 class="modal-title"><?= isset($persona) ? 'Editar Persona' : 'Registrar Persona' ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>

  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <label>Apellidos *</label>
        <input type="text" name="apellidos" class="form-control" required value="<?= isset($persona) ? esc($persona['apellidos']) : '' ?>">
      </div>
      <div class="col-md-6">
        <label>Nombres *</label>
        <input type="text" name="nombres" class="form-control" required value="<?= isset($persona) ? esc($persona['nombres']) : '' ?>">
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-6">
        <label>Teléfono Primario *</label>
        <input type="text" name="telprimario" class="form-control" required value="<?= isset($persona) ? esc($persona['telprimario']) : '' ?>">
      </div>
      <div class="col-md-6">
        <label>Teléfono Alternativo</label>
        <input type="text" name="telalternativo" class="form-control" value="<?= isset($persona) ? esc($persona['telalternativo']) : '' ?>">
      </div>
    </div>

    <div class="mt-3">
      <label>Email *</label>
      <input type="email" name="email" class="form-control" required value="<?= isset($persona) ? esc($persona['email']) : '' ?>">
    </div>

    <div class="mt-3">
      <label>Dirección *</label>
      <input type="text" name="direccion" class="form-control" required value="<?= isset($persona) ? esc($persona['direccion']) : '' ?>">
    </div>

    <div class="mt-3">
      <label>Referencia *</label>
      <input type="text" name="referencia" class="form-control" required value="<?= isset($persona) ? esc($persona['referencia']) : '' ?>">
    </div>

    <div class="mt-3">
      <label>Distrito *</label>
      <select name="iddistrito" class="form-select" required>
        <option value="">Seleccione un distrito</option>
        <?php foreach($distritos as $distrito): ?>
          <option value="<?= $distrito['iddistrito'] ?>" <?= (isset($persona) && $persona['iddistrito']==$distrito['iddistrito']) ? 'selected' : '' ?>>
            <?= esc($distrito['distrito']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="modal-footer">
    <button type="submit" class="btn btn-success"><?= isset($persona) ? 'Actualizar' : 'Registrar' ?></button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  </div>
</form>
