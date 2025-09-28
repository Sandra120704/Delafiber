<?= $this->extend('Layouts/header') ?>

<?= $this->section('content') ?>

<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoLead">
  <i class="ti-plus"></i> Nuevo Lead
</button>

<div class="modal fade" id="modalNuevoLead" tabindex="-1" aria-labelledby="modalNuevoLeadLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalNuevoLeadLabel"><i class="ti-user"></i> Registro de Prospecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="<?= base_url('leads/store') ?>" method="post" autocomplete="off">
        <div class="modal-body">

          <!-- Datos del prospecto -->
          <div class="card mb-4 border-0">
            <div class="card-header bg-light">
              <h6 class="mb-0 text-primary"><i class="ti-id-badge"></i> Datos del Prospecto</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="dni"><i class="ti-id-badge"></i> DNI</label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="dni" id="dni" maxlength="8" minlength="8" pattern="\d{8}" required autocomplete="off">
                    <button class="btn btn-outline-success" type="button" id="buscar-dni">Buscar</button>
                  </div>
                  <small class="d-none" id="searching">Buscando datos...</small>
                </div>

                <div class="col-md-4 mb-3">
                  <label for="nombres"><i class="ti-user"></i> Nombres</label>
                  <input type="text" class="form-control" name="nombres" id="nombres" required readonly>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="apellidos"><i class="ti-user"></i> Apellidos</label>
                  <input type="text" class="form-control" name="apellidos" id="apellidos" required readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="telefono"><i class="ti-mobile"></i> Teléfono</label>
                  <input type="text" class="form-control" name="telefono" id="telefono" maxlength="9" pattern="\d{9}" required autocomplete="off">
                </div>
                <div class="col-md-8 mb-3">
                  <label for="direccion"><i class="ti-location-pin"></i> Dirección</label>
                  <input type="text" class="form-control" name="direccion" id="direccion">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="distrito"><i class="ti-map-alt"></i> Distrito</label>
                  <select class="form-control" name="distrito" required>
                    <option value="">-- Seleccione --</option>
                    <?php if (!empty($distritos)): ?>
                      <?php foreach ($distritos as $distrito): ?>
                        <option value="<?= esc($distrito['iddistrito']) ?>">
                          <?= esc($distrito['nombre'] ?? 'Sin nombre') ?>
                        </option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No hay distritos disponibles</option>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="referencias"><i class="ti-info-alt"></i> Referencia</label>
                  <input type="text" class="form-control" name="referencias" id="referencias">
                </div>
              </div>
            </div>
          </div>

          <!-- Información del Lead -->
          <div class="card mb-4 border-0">
            <div class="card-header bg-light">
              <h6 class="mb-0 text-warning"><i class="ti-target"></i> Información del Lead</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="origen"><i class="ti-direction-alt"></i> Origen</label>
                  <select class="form-control" name="origen" required>
                    <?php if (!empty($origenes)): ?>
                      <?php foreach ($origenes as $origen): ?>
                        <option value="<?= esc($origen['idorigen']) ?>"><?= esc($origen['nombre']) ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No hay orígenes disponibles</option>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="campania"><i class="ti-announcement"></i> Campaña</label>
                  <select class="form-control" name="campania">
                    <?php if (!empty($campanias)): ?>
                      <?php foreach ($campanias as $campania): ?>
                        <option value="<?= esc($campania['idcampania']) ?>"><?= esc($campania['nombre']) ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No hay campañas disponibles</option>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="etapa"><i class="ti-layers-alt"></i> Etapa</label>
                  <select class="form-control" name="etapa">
                    <?php if (!empty($etapas)): ?>
                      <?php foreach ($etapas as $etapa): ?>
                        <option value="<?= esc($etapa['idetapa']) ?>"><?= esc($etapa['nombre']) ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No hay etapas disponibles</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label for="observaciones"><i class="ti-comment-alt"></i> Observaciones</label>
                <textarea class="form-control" name="observaciones"></textarea>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="ti-check"></i> Guardar Lead</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<script>
  const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
  const API_BUSCAR_DNI = BASE_URL + "personas/buscardni";
</script>
<script src="<?= base_url('js/persona-manager.js') ?>"></script>
