<div class="modal fade" id="modalPersona" tabindex="-1" aria-labelledby="modalPersonaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('personas/update/' . $persona['idpersona']) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="modalPersonaLabel">Editar Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <label for="apellidos">Apellidos *</label>
              <input type="text" class="form-control" id="apellidos" name="apellidos" required value="<?= esc($persona['apellidos']) ?>">
            </div>
            <div class="col-md-6">
              <label for="nombres">Nombres *</label>
              <input type="text" class="form-control" id="nombres" name="nombres" required value="<?= esc($persona['nombres']) ?>">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-6">
              <label>Teléfono Primario *</label>
              <input type="text" name="telprimario" class="form-control" required value="<?= esc($persona['telprimario']) ?>">
            </div>
            <div class="col-md-6">
              <label>Teléfono Alternativo</label>
              <input type="text" name="telalternativo" class="form-control" value="<?= esc($persona['telalternativo']) ?>">
            </div>
          </div>

          <div class="mt-3">
            <label>Email *</label>
            <input type="email" name="email" class="form-control" required value="<?= esc($persona['email']) ?>">
          </div>

          <div class="mt-3">
            <label>Referencia *</label>
            <input type="text" name="referencia" class="form-control" required value="<?= esc($persona['referencia']) ?>">
          </div>

          <div class="mt-3">
            <label>Distrito *</label>
            <select name="iddistrito" class="form-select" required>
              <option value="">Seleccione un distrito</option>
              <?php foreach ($distritos as $distrito): ?>
                <option value="<?= $distrito['iddistrito'] ?>" <?= $persona['iddistrito'] == $distrito['iddistrito'] ? 'selected' : '' ?>>
                  <?= esc($distrito['distrito']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
