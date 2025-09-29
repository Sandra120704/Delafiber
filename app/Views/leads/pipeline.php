<?= $this->extend('Layouts/header') ?>
<?= $this->section('content') ?>

<h3 class="mb-4"><i class="ti-layout"></i> Pipeline de Leads</h3>

<div class="pipeline-scroll" style="overflow-x:auto;">
  <div class="d-flex flex-row gap-3">
    <?php foreach ($pipeline as $col): ?>
      <div class="card flex-shrink-0" style="min-width:300px;">
        <div class="card-header bg-light">
          <strong><?= esc($col['etapa_nombre']) ?></strong>
          <span class="badge bg-primary float-end"><?= count($col['leads']) ?></span>
        </div>
        <div class="card-body p-2">
          <?php if (!empty($col['leads'])): ?>
            <?php foreach ($col['leads'] as $lead): ?>
              <div class="card mb-2 shadow-sm">
                <div class="card-body p-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?></strong><br>
                      <small><i class="ti-mobile"></i> <?= esc($lead['telefono']) ?></small>
                    </div>
                    <div class="btn-group">
                      <!-- Acciones rápidas -->
                      <a href="tel:<?= esc($lead['telefono']) ?>" class="btn btn-sm btn-outline-success" title="Llamar">
                        <i class="ti-headphone-alt"></i>
                      </a>
                      <a href="https://wa.me/51<?= esc($lead['telefono']) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp">
                        <i class="ti-comment"></i>
                      </a>
                      <a href="<?= base_url('leads/view/'.$lead['idlead']) ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                        <i class="ti-eye"></i>
                      </a>
                      <?php if ($col['etapa_nombre'] !== 'VENTA'): ?>
                        <button class="btn btn-sm btn-outline-info" 
                                onclick="avanzarEtapa(<?= $lead['idlead'] ?>, <?= $col['etapa_id'] ?>)" 
                                title="Avanzar etapa">
                          <i class="ti-arrow-right"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted">Sin leads en esta etapa</div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function avanzarEtapa(idlead, etapaActual) {
  fetch('<?= base_url('leads/updateEtapa') ?>', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'idlead=' + idlead + '&idetapa=' + (etapaActual + 1)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) location.reload();
    else alert('No se pudo avanzar la etapa');
  });
}
</script>

<style>
.pipeline-scroll { padding-bottom: 1rem; }
.card.flex-shrink-0 { min-width: 300px; max-width: 350px; }
@media (max-width: 768px) {
  .card.flex-shrink-0 { min-width: 90vw; }
}
</style>

<?= $this->endSection() ?>
<?= $this->include('Layouts/footer') ?>
