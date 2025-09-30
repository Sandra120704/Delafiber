<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
            <h3 class="mb-0">Gestión de Campañas</h3>
            <a href="<?= base_url('campanias/create') ?>" class="btn btn-primary">
                <i class="icon-plus"></i> Nueva Campaña
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($campanias)): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaCampanias">
                        <thead class="thead-light">
                            <tr>
                                <th>Campaña</th>
                                <th>Estado</th>
                                <th>Periodo</th>
                                <th>Presupuesto</th>
                                <th>Leads</th>
                                <th>ROI</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($campanias as $campania): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($campania['nombre']) ?></strong><br>
                                    <small class="text-muted"><?= esc($campania['descripcion'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?= ($campania['estado'] ?? 'Inactiva') == 'Activa' ? 'success' : 'secondary' ?>">
                                        <?= esc($campania['estado'] ?? 'Inactiva') ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($campania['fecha_inicio'])) ?><br>
                                    <small class="text-muted">
                                        <?= $campania['fecha_fin'] ? 'hasta ' . date('d/m/Y', strtotime($campania['fecha_fin'])) : 'Sin fecha fin' ?>
                                    </small>
                                </td>
                                <td>S/ <?= number_format($campania['presupuesto'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="badge badge-info badge-pill">
                                        <?= $campania['total_leads'] ?? 0 ?> leads
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $presupuesto = $campania['presupuesto'] ?? 0;
                                    $leads = $campania['total_leads'] ?? 0;
                                    $cpl = $leads > 0 ? $presupuesto / $leads : 0;
                                    ?>
                                    <small>S/ <?= number_format($cpl, 2) ?> / lead</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('campanias/view/' . $campania['idcampania']) ?>" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="<?= base_url('campanias/edit/' . $campania['idcampania']) ?>" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <button onclick="confirmarEliminacion(<?= $campania['idcampania'] ?>)" 
                                                class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="icon-layers" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No hay campañas registradas</p>
                    <a href="<?= base_url('campanias/create') ?>" class="btn btn-primary mt-2">
                        <i class="icon-plus"></i> Crear primera campaña
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar DataTable solo si jQuery está disponible
if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $(document).ready(function() {
        $('#tablaCampanias').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[3, 'desc']]
        });
    });
} else {
    // Si no hay jQuery/DataTable, no inicializar nada
    // Opcional: puedes mostrar un mensaje en consola si lo deseas
    // console.warn('jQuery/DataTable no está disponible');
}

function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de eliminar esta campaña? Esta acción no se puede deshacer.')) {
        window.location.href = '<?= base_url('campanias/delete/') ?>' + id;
    }
}
</script>

<?= $this->endSection() ?>