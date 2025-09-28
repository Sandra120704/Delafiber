<?= $this->extend('Layouts/header') ?>
<?= $this->section('content') ?>

<!-- Botón para nuevo lead -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoLead">
  <i class="ti-user"></i> Nuevo Lead
</button>

<!-- Modal de registro de lead -->
<?php include 'create.php'; ?>

<!-- Tabla de leads -->
<div class="card">
  <div class="card-header bg-light">
    <h5 class="mb-0"><i class="ti-target"></i> Lista de Leads</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Etapa</th>
            <th>Origen</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($leads)): ?>
            <?php foreach ($leads as $i => $lead): ?>
              <tr>
                <td><?= $i+1 ?></td>
                <td><?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?></td>
                <td><?= esc($lead['telefono']) ?></td>
                <td><span class="badge badge-info"><?= esc($lead['etapa']) ?></span></td>
                <td><?= esc($lead['origen']) ?></td>
                <td><?= esc($lead['usuario']) ?></td>
                <td><?= date('d/m/Y', strtotime($lead['fecha_registro'])) ?></td>
                <td>
                  <a href="<?= base_url('leads/view/'.$lead['idlead']) ?>" class="btn btn-sm btn-outline-primary" title="Ver"><i class="ti-eye"></i></a>
                  <a href="<?= base_url('leads/edit/'.$lead['idlead']) ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="ti-pencil"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">No hay leads registrados</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
