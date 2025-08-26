<!-- Modal: Registrar Nueva Persona -->
<div class="modal fade" id="modalCrearPersona" tabindex="-1" aria-labelledby="modalCrearPersonaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <form action="<?= base_url('personas/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="modal-header">
          <h5 class="modal-title" id="modalCrearPersonaLabel">Registrar Nueva Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

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
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
  </div>
</div>
