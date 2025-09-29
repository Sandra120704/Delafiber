<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Gestión de Leads</h4>
                    <div>
                        <a href="<?= base_url('leads/exportar') ?>" class="btn btn-outline-success">
                            <i class="icon-download"></i> Exportar
                        </a>
                        <a href="<?= base_url('leads/create') ?>" class="btn btn-primary">
                            <i class="icon-plus"></i> Nuevo Lead
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <form action="<?= base_url('leads') ?>" method="GET" id="filtrosForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Buscar</label>
                                        <input type="text" class="form-control" name="busqueda" 
                                               placeholder="Nombre, DNI, teléfono..." 
                                               value="<?= $filtros_aplicados['busqueda'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Etapa</label>
                                        <select class="form-control" name="etapa">
                                            <option value="">Todas</option>
                                            <?php foreach ($etapas as $etapa): ?>
                                            <option value="<?= $etapa['idetapa'] ?>" 
                                                    <?= (isset($filtros_aplicados['idetapa']) && $filtros_aplicados['idetapa'] == $etapa['idetapa']) ? 'selected' : '' ?>>
                                                <?= esc($etapa['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Campaña</label>
                                        <select class="form-control" name="campania">
                                            <option value="">Todas</option>
                                            <?php foreach ($campanias as $campania): ?>
                                            <option value="<?= $campania['idcampania'] ?>"
                                                    <?= (isset($filtros_aplicados['idcampania']) && $filtros_aplicados['idcampania'] == $campania['idcampania']) ? 'selected' : '' ?>>
                                                <?= esc($campania['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control" name="estado">
                                            <option value="">Activos</option>
                                            <option value="Convertido" <?= (isset($filtros_aplicados['estado']) && $filtros_aplicados['estado'] == 'Convertido') ? 'selected' : '' ?>>Convertidos</option>
                                            <option value="Descartado" <?= (isset($filtros_aplicados['estado']) && $filtros_aplicados['estado'] == 'Descartado') ? 'selected' : '' ?>>Descartados</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?= base_url('leads') ?>" class="btn btn-light">Limpiar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Leads -->
                <div class="table-responsive">
                    <table class="table table-hover" id="tableLeads">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Etapa</th>
                                <th>Origen</th>
                                <th>Campaña</th>
                                <th>Vendedor</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leads)): ?>
                                <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:40px;height:40px;">
                                                <?= strtoupper(substr($lead['cliente'], 0, 2)) ?>
                                            </div>
                                            <div>
                                                <strong><?= esc($lead['cliente']) ?></strong><br>
                                                <small class="text-muted">DNI: <?= esc($lead['dni']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="icon-phone text-primary"></i> <?= esc($lead['telefono']) ?><br>
                                        <?php if ($lead['correo']): ?>
                                        <small class="text-muted"><i class="icon-envelope"></i> <?= esc($lead['correo']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= esc($lead['etapa_actual']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($lead['origen']) ?></td>
                                    <td>
                                        <?php if ($lead['campania']): ?>
                                            <span class="badge badge-secondary"><?= esc($lead['campania']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($lead['vendedor_asignado']) ?></td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($lead['fecha_registro'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url('leads/view/' . $lead['idlead']) ?>" 
                                               class="btn btn-sm btn-info" title="Ver detalle">
                                                <i class="icon-eye"></i>
                                            </a>
                                            <a href="<?= base_url('leads/edit/' . $lead['idlead']) ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmarEliminar(<?= $lead['idlead'] ?>)"
                                                    title="Eliminar">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="icon-info text-muted" style="font-size: 48px;"></i>
                                        <p class="text-muted mt-2">No se encontraron leads</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if (!empty($pager)): ?>
                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este lead?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminar(idlead) {
    const formEliminar = document.getElementById('formEliminar');
    formEliminar.action = '<?= base_url('leads/delete/') ?>' + idlead;
    $('#modalEliminar').modal('show');
}
</script>

<?= $this->endsection() ?>