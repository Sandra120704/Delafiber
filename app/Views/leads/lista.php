<!-- Este archivo solo debe contener la tabla, sin llamadas a $this->section() ni $this->endSection() -->
<!-- Inclúyelo en la vista principal (index.php) con: <?php include 'lista.php'; ?> -->

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
  </tbody>
</table>
